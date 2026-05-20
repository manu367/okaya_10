<?php
include_once 'db_functions.php'; 
$db = new DB_Functions(); 
 $json = $_REQUEST["ImageUpload"];
if (get_magic_quotes_gpc()){ 
	$json = stripslashes($json); 
}
//Decode JSON into an Array 
 $data = json_decode($json);
//print_r($data);
$ei = $_REQUEST['eid'];
$temp=0;
$a = array();     
$b = array();

//$sel=mysql_fetch_array(mysql_query("SELECT engg_assign, job_no FROM complaints_master where job_no='$job_no'"));
for($i=0; $i<count($data) ; $i++) {
	

		$temp++; // get  no of jobs for which images are uploaded 
    
		$base1=$data[$i]->encodedStrDigital;
	
		//$result = mysql_query("update complaints_master set img_path='$base' where job_no='MJ003'")or die(mysql_error());

 		// Get file name posted from Android App
    
		$filename1 = $data[$i]->fileNameDigital;
	
    	// Decode Image
  	
 		if($base1!=""){
     	  	$binary1=base64_decode($base1);}
    	else{
    		$binary1=null;
		}
	
		header('Content-Type: bitmap; charset=utf-8');
		
		$file_nm1='../app_image/'.$filename1;
	

    	// Images will be saved under 'www/imgupload/uplodedimages' folder		
    	// Create File

		
		if($binary1!=null){
			$file1 = fopen($file_nm1, 'wb');
    		fwrite($file1, $binary1);
			fclose($file1);
			if(file_exists($file_nm1)){
				$b[$filename1] = "0";
				array_push($a,$b);
			}else{
				$b[$filename1] = "1";
				array_push($a,$b);
			}
		}
		
	

		
		$image_up = $db->storedigitalImageJob($data[$i]->job_no,$data[$i]->fileNameDigital);
		if($image_up){
		$b["id"] = $data[$i]->job_no;         
$b["status"] = 1;
$b["msg"] = "sucess";
array_push($a,$b);
}else{
		$b["id"] = $data[$i]->job_no;         
$b["status"] = 0;  
$b["msg"] = "Fail";
array_push($a,$b);
	}


}///close for loop
echo json_encode($a);
?>