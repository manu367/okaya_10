<?php
$path = "../../charger_pdf";
$json = $_POST["DocUpload"];
//print_r($json);
//exit;
//Decode JSON into an Array
$data = json_decode($json);

$a = array();
for ($i = 0; $i < count($data); $i++) {
	$b = array();
	$base = $data[$i]->uploadedfile;
	
	// Get file name posted from Android App
	$filename = $data[$i]->name;

	// Decode Image
	if ($base != "") {
		$binary = base64_decode($base);
	} else {
		$binary = null;
	}

	header('Content-Type: bitmap; charset=utf-8');

	 $file_nm = $path.'/'.$filename;

	// Create File
	if ($binary != null && !file_exists($filename)) {
		$file = fopen($file_nm, 'wb');
		fwrite($file, $binary);
		fclose($file);
		if (file_exists($file_nm)) {
			$b['status'] = "0";
		} else {
			$b['status'] = "1";
		}
	}else {$b['status'] = "0";
	}

	array_push($a, $b);
} ///close for loop
echo json_encode($a);
?>
