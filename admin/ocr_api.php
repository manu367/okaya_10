<?php
//require_once("../includes/config.php");
header('Content-type: application/json');
// Path of Teseract-OCR
define('TESSERACT_PATH',    '"C:\\Program Files\\Tesseract-OCR\\tesseract.exe"');
define('UPLOAD_BASE_FOLDER', 'C:\xampp1\htdocs\okaya\upload_pi');   // root save folder
define('OCR_LANG',           'eng');                     // English
define('MAX_FILE_SIZE_MB',   20);                            // max upload size
// Parse API
define('PARSE_API_URL', 'http://164.52.210.225:80/test_api/parse');
define('PARSE_API_KEY', 'candour@cspl');
$ALLOWED_TYPES = [
        'image/jpeg', 'image/jpg', 'image/png',
        'image/webp', 'application/pdf',
];
$response = [
        'success'        => false,
        'message'        => '',
        'file_path'      => '',
        'ocr_success'    => false,
        'extracted_text' => '',
        'ocr_error'      => '',
];
//if (isset($_POST['submit']))
if (
    isset($_POST['submit']) &&
    isset($_REQUEST['api-key']) &&
    !empty($_REQUEST['api-key']) &&
    $_REQUEST['api-key'] === "hello@cspl"
)
{
    $date=$_REQUEST['date_range']??'';
    $pi_number=$_REQUEST['pi_number']??'';
    $file      = $_FILES['file_upload'];
    $fileError = $file['error'];

    if ($fileError !== UPLOAD_ERR_OK) {
        $response['message'] = 'File upload error code: ' . $fileError;
        finalize($response);
    }

    $maxBytes = MAX_FILE_SIZE_MB * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        $response['message'] = 'The file is too large. Maximum allowed size: ' . MAX_FILE_SIZE_MB . ' MB.';
        finalize($response);
    }

    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $ALLOWED_TYPES, true)) {
        $response['message'] = 'Allowed file types: JPG, PNG, WEBP, PDF. and Your type: ' . $mimeType;
        finalize($response);
    }

    $year  = date('Y');
    $month = date('m');
    $saveDir = rtrim(UPLOAD_BASE_FOLDER, '/\\') . DIRECTORY_SEPARATOR
            . $year . DIRECTORY_SEPARATOR
            . $month;

    if (!is_dir($saveDir) && !mkdir($saveDir, 0755, true)) {
        $response['message'] = 'Failed to create the directory:' . $saveDir;
        finalize($response);
    }

    // Unique filename: timestamp + random + original extension
    $origExt      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safeExt      = in_array($origExt, ['jpg','jpeg','png','webp','pdf']) ? $origExt : 'bin';
    $uniqueName   = date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $safeExt;
    $savedFilePath = $saveDir . DIRECTORY_SEPARATOR . $uniqueName;

    // ── 5. Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $savedFilePath)) {
        $response['message'] = 'An error occurred while saving the file.';
        finalize($response);
    }

    $response['success']   = true;
    $response['message']   = 'File uploaded successfully';
    $response['file_path'] = relativePath($savedFilePath);  // relative for display


    // 6. Run Tesseract OCR
    if ($mimeType === 'application/pdf') {
        ocrPdf($savedFilePath, $response);
    } else {
        ocrImage($savedFilePath, $response);
    }

    // 7. OCR success hone par Parse API call
    if ($response['ocr_success'] && !empty($response['extracted_text'])) {
        if (stripos($response['extracted_text'], $pi_number) !== false) {
            $response['pi_valid'] = true;
            $response['pi_message'] = 'PI Number matched successfully';
        }
        else {
            $response['pi_valid'] = false;
            $response['pi_message'] = 'Invalid PI Number';
        }
//        var_dump($response);exit();
        callParseApi($response['extracted_text'], $response);
    }
}
/**
 * OCR a single image file with Tesseract
 */
function ocrImage($imagePath, &$response)
{
    $outBase = $imagePath . '_ocr';   // tesseract adds .txt automatically
    $lang    = OCR_LANG;
    $tess    = TESSERACT_PATH;

    // Build command — output to text file, then read it
    // --oem 1  = LSTM engine   --psm 3 = auto page segmentation
    $cmd = $tess . ' '
            . escapeshellarg($imagePath) . ' '
            . escapeshellarg($outBase)
            . ' -l ' . $lang
            . ' --oem 1 --psm 3 2>&1';
    exec($cmd, $output, $returnCode);
    $txtFile = $outBase . '.txt';

    if ($returnCode === 0 && file_exists($txtFile)) {
        $text = file_get_contents($txtFile);
        @unlink($txtFile);                        // temp OCR output delete
        $response['ocr_success']    = true;
        $response['extracted_text'] = trim($text);
    } else {
        $response['ocr_success'] = false;
        $response['ocr_error']   = implode("\n", $output) ?: 'Tesseract command failed (code ' . $returnCode . ')';
    }
}
/**
 * OCR a PDF:  Ghostscript se pages → PNG, phir har page OCR
 */
function ocrPdf($pdfPath, $response=[]){
    $tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ocr_' . uniqid();
    @mkdir($tmpDir, 0755, true);

    $pages = [];

    // STEP 1: pdftoppm (Poppler)
    // Windows: C:\Program Files\poppler-xx\bin\pdftoppm.exe
    // Install: https://github.com/oschwartz10612/poppler-windows/releases
    $pdftoppm = '"C:\poppler-26.02.0\Library\bin\pdftoppm.exe"';
    $outPrefix = $tmpDir . DIRECTORY_SEPARATOR . 'page';

    $cmd1 = $pdftoppm
            . ' -png -r 300 '
            . escapeshellarg($pdfPath) . ' '
            . escapeshellarg($outPrefix)
            . ' 2>&1';

    exec($cmd1, $out1, $code1);
    $pages = glob($tmpDir . DIRECTORY_SEPARATOR . 'page-*.png');
    if (empty($pages)) {
        $pages = glob($tmpDir . DIRECTORY_SEPARATOR . 'page*.png'); // some versions differ
    }

    // STEP 2: ImageMagick (fallback)
    if (empty($pages)) {
        $magick  = 'magick';   // ImageMagick 7.x; older version mein 'convert' likhein
        $outMask = $tmpDir . DIRECTORY_SEPARATOR . 'page_%04d.png';

        $cmd2 = $magick . ' -density 300 '
                . escapeshellarg($pdfPath) . ' '
                . escapeshellarg($outMask)
                . ' 2>&1';

        exec($cmd2, $out2, $code2);
        $pages = glob($tmpDir . DIRECTORY_SEPARATOR . 'page_*.png');
    }

    // Pages mil gayi — Tesseract se OCR karo
    if (!empty($pages)) {
        sort($pages);
        $allText = '';

        foreach ($pages as $idx => $pagePng) {
            $pr = ['ocr_success' => false, 'extracted_text' => '', 'ocr_error' => ''];
            ocrImage($pagePng, $pr);
            if ($pr['ocr_success']) {
                $allText .= "\n\n--- Page " . ($idx + 1) . " ---\n\n"
                        . $pr['extracted_text'];
            }
            @unlink($pagePng);
        }

        cleanDir($tmpDir);
        if ($allText !== '') {
            $response['ocr_success']    = true;
            $response['extracted_text'] = trim($allText);
            return;
        }
    }

    cleanDir($tmpDir);

    //  STEP 3: pdftotext (sirf text-based / selectable PDF ke liye)
    // Yahan text-only extract hoga — scanned PDF ke liye kaam nahi karega
    $pdftotext = 'pdftotext';  // Poppler ka hi tool hai
    $txtOut    = $tmpDir . '_extracted.txt';

    $cmd3 = $pdftotext . ' -layout '
            . escapeshellarg($pdfPath) . ' '
            . escapeshellarg($txtOut)
            . ' 2>&1';

    exec($cmd3, $out3, $code3);

    if ($code3 === 0 && file_exists($txtOut)) {
        $text = trim(file_get_contents($txtOut));
        @unlink($txtOut);

        if ($text !== '') {
            $response['ocr_success']    = true;
            $response['extracted_text'] = $text;
            return;
        }
    }

    // Sab fail — error report
    $response['ocr_success'] = false;
    $response['ocr_error']   =
            "PDF se text extract nahi ho saka.\n\n"
            . "Troubleshooting:\n"
            . "1. Poppler install karein: https://github.com/oschwartz10612/poppler-windows/releases\n"
            . "   Phir bin/ folder ko System PATH mein add karein.\n"
            . "2. Ya ImageMagick install karein: https://imagemagick.org/script/download.php\n\n"
            . "pdftoppm output: " . implode(' | ', array_slice($out1 ?? [], 0, 3)) . "\n"
            . "magick output:   " . implode(' | ', array_slice($out2 ?? [], 0, 3));
}
/**
 * OCR text ko Parse API pe POST karo aur response $response mein store karo
 */
function callParseApi($extractedText,&$response){
    $payload = json_encode([
            'key'  => PARSE_API_KEY,
            'data' => $extractedText,
    ]);

    $ch = curl_init(PARSE_API_URL);
    curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($payload),
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
    ]);
    $rawResponse = curl_exec($ch);
    $curlError   = curl_error($ch);
    $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($rawResponse === false || !empty($curlError)) {
        $response['parse_api_success'] = false;
        $response['parse_api_error']   = 'cURL Error: ' . $curlError;
        return;
    }
    $decoded = json_decode($rawResponse, true);
    $response['parse_api_success']  = ($httpCode >= 200 && $httpCode < 300);
    $response['parse_api_raw']      = $rawResponse;          // raw JSON string
    $response['parse_api_data']     = $decoded;              // decoded array
    $response['parse_api_httpcode'] = $httpCode;
    if (!$response['parse_api_success']) {
        $response['parse_api_error'] = 'HTTP ' . $httpCode . ' response mila.';
    }
    echo json_encode($response['parse_api_data']);exit();
}
/**
 * Temporary directory clean
 */
function cleanDir($dir)
{
    if (!is_dir($dir)) return;
    foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $f) {
        @unlink($f);
    }
    @rmdir($dir);
}
function relativePath($absPath){
    $base = realpath(__DIR__ . '/..');
    $rel  = str_replace($base, '', $absPath);
    return ltrim(str_replace('\\', '/', $rel), '/');
}
function finalize(array &$response){
    return;
}