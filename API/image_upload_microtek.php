<?php
include_once 'db_functions.php'; 
$db = new DB_Functions();
$my = date("Y-M");
$path = "../app_image/".$my;
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
	$base = $data[$i]->encodedString;
	$base1 = $data[$i]->encodedString1;
	$base2 = $data[$i]->encodedString2;
	$base3 = $data[$i]->encodedString3;
	$base4 = $data[$i]->encodedString4;
	$base5 = $data[$i]->encodedString_serial;
	$base6 = $data[$i]->encodedString5;
	
	// Get file name posted from Android App
	$filename = $data[$i]->fileName;
	$filename1 = $data[$i]->fileName1;
	$filename2 = $data[$i]->fileName2;
	$filename3 = $data[$i]->fileName3;
	$filename4 = $data[$i]->fileName4;
	$filename5 = $data[$i]->fileName_serial;
	$filename6 = $data[$i]->fileName5;

	// Decode Image
	if ($base != "") {
		$binary = base64_decode($base);
	} else {
		$binary = null;
	}
	if ($base1 != "") {
		$binary1 = base64_decode($base1);
	} else {
		$binary1 = null;
	}
	if ($base2 != "") {
		$binary2 = base64_decode($base2);
	} else {
		$binary2 = null;
	}
	if ($base3 != "") {
		$binary3 = base64_decode($base3);
	} else {
		$binary3 = null;
	}
	if ($base4 != "") {
		$binary4 = base64_decode($base4);
	} else {
		$binary4 = null;
	}
	if ($base5 != "") {
		$binary5 = base64_decode($base5);
	} else {
		$binary5 = null;
	}
	if ($base6 != "") {
		$binary6 = base64_decode($base6);
	} else {
		$binary6 = null;
	}
	header('Content-Type: bitmap; charset=utf-8');
	
	$file_nm = "";
	$file_nm1 = "";
	$file_nm2 = "";
	$file_nm3 = "";
	$file_nm4 = "";
	$file_nm5 = "";
	$file_nm6 = "";

	$file_nm = $path.'/'.$filename;
	$file_nm1 = $path.'/'.$filename1;
	$file_nm2 = $path.'/'.$filename2;
	$file_nm3 = $path.'/'.$filename3;
	$file_nm4 = $path.'/'.$filename4;
	$file_nm5 = $path.'/'.$data[$i]->job_no."_".$filename5;
	$file_nm6 = $path.'/'.$filename6;

	// Images will be saved under 'www/imgupload/uplodedimages' folder
	// Create File
	if ($binary != null && !file_exists($file_nm)) {
		$file = fopen($file_nm, 'wb');
		fwrite($file, $binary);
		fclose($file);
		if (file_exists($file_nm)) {
			$b['file1'] = "0";
		} else {
			$b['file1'] = "1";
		}
	}else {$b['file1'] = "0";
	}

	if ($binary1 != null && !file_exists($file_nm1)) {
		$file1 = fopen($file_nm1, 'wb');
		fwrite($file1, $binary1);
		fclose($file1);
		if (file_exists($file_nm1)) {
			$b['file2'] = "0";
		} else {
			$b['file2'] = "1";
		}
	}else{$b['file2'] = "0";}

	if ($binary2 != null && !file_exists($file_nm2)) {
		$file2 = fopen($file_nm2, 'wb');
		fwrite($file2, $binary2);
		fclose($file2);
		if (file_exists($file_nm2)) {
			$b['file3'] = "0";
		} else {
			$b['file3'] = "1";
		}
	}else{$b['file3'] = "0";}

	if ($binary3 != null && !file_exists($file_nm3)) {
		$file3 = fopen($file_nm3, 'wb');
		fwrite($file3, $binary3);
		fclose($file3);
		if (file_exists($file_nm3)) {
			$b['file4'] = "0";
		} else {
			$b['file4'] = "1";
		}
	}else{$b['file4'] = "0";}
	if ($binary4 != null && !file_exists($file_nm4)) {
		$file4 = fopen($file_nm4, 'wb');
		fwrite($file4, $binary4);
		fclose($file4);
		if (file_exists($file_nm4)) {
			$b['file5'] = "0";
		} else {
			$b['file5'] = "1";
		}				
	}else{$b['file5'] = "0";}
	
	if ($binary5 != null && !file_exists($file_nm5)) {
		$file5 = fopen($file_nm5, 'wb');
		fwrite($file5, $binary5);
		fclose($file5);
		if (file_exists($file_nm5)) {
			$b['file6'] = "0";
		} else {
			$b['file6'] = "1";
		}				
	}else{$b['file6'] = "0";}

	if ($binary6 != null && !file_exists($file_nm6)) {
		$file6 = fopen($file_nm6, 'wb');
		fwrite($file6, $binary6);
		fclose($file6);
		if (file_exists($file_nm6)) {
			$b['file7'] = "0";
		} else {
			$b['file7'] = "1";
		}				
	}else{$b['file7'] = "0";}
	
	$image_up = $db->storeImageJob2($data[$i]->job_no,$file_nm,$file_nm1,$file_nm2,$file_nm3,$file_nm4,$file_nm5,$file_nm6);
	array_push($a, $b);
} ///close for loop
echo json_encode($a);
?>
