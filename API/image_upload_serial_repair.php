<?php
$my = date("Y-M");
$path = "../repair_img/".$my;
if (!is_dir($path)) {
	mkdir($path, 0777, 'R');
}
$json = $_POST["ImageUpload"];
if (get_magic_quotes_gpc()) {
	$json = stripslashes($json);
}
//Decode JSON into an Array
$data = json_decode($json);
$a = array();
for ($i = 0; $i < count($data); $i++) {	
$b = array();

	$job_no = $data[$i]->job_no;
	$base = $data[$i]->part_encodedString;
	// Get file name posted from Android App
	$filename = $data[$i]->part_fileName;

	// Decode Image
	if ($base != "") {
		$binary = base64_decode($base);
	} else {
		$binary = null;
	}
	header('Content-Type: bitmap; charset=utf-8');

	$file_nm = $path.'/'.$filename;

	// Images will be saved under 'www/imgupload/uplodedimages' folder
	// Create File
	if ($binary != null && !file_exists($file_nm)) {
		$file = fopen($file_nm, 'wb');
		fwrite($file, $binary);
		fclose($file);
		if (file_exists($file_nm)) {
			$b['file'] = "0";
		} else {
			$b['file'] = "1";
		}
	}else {$b['file'] = "0";}
			array_push($a, $b);
} ///close for loop
echo json_encode($a);
?>
