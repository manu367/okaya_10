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
	
 $job_details=$db->getAnyDetails($data[$i]->job_no,"job_no,eng_id","job_no","jobsheet_data");
 $job_row=explode("~",$job_details);
	if($data[$i]->job_no==$job_row[0] &&  $ei==$job_row[1]){
		$temp++; // get  no of jobs for which images are uploaded 
    
		$base1=$data[$i]->encodedString1;
		$base2=$data[$i]->encodedString2;
		$base3=$data[$i]->encodedString3;
		$base4=$data[$i]->encodedString4;
		//$result = mysql_query("update complaints_master set img_path='$base' where job_no='MJ003'")or die(mysql_error());

 		// Get file name posted from Android App
    
		$filename1 = $data[$i]->fileName1;
		$filename2 = $data[$i]->fileName2;
		$filename3 = $data[$i]->fileName3;
	   $filename4 = $data[$i]->fileName4;

    	// Decode Image
  	
 		if($base1!=""){
     	  	$binary1=base64_decode($base1);}
    	else{
    		$binary1=null;
		}
		if($base2!=""){
	       	$binary2=base64_decode($base2);}
    	else{
   	 		$binary2=null;
		}
		if($base3!=""){
     	  	$binary3=base64_decode($base3);}
 	   else{
    		$binary3=null;
	   }
	   
	   	if($base4!=""){
     	  	$binary4=base64_decode($base4);}
 	   else{
    		$binary4=null;
	   }
		header('Content-Type: bitmap; charset=utf-8');
		
		$file_nm1='../app_image/'.$filename1;
		$file_nm2='../app_image/'.$filename2;
		$file_nm3='../app_image/'.$filename3;
			$file_nm4='../app_image/'.$filename4;

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
		
		if($binary2!=null){
			$file2 = fopen($file_nm2, 'wb');
    		fwrite($file2, $binary2);
			fclose($file2);
			if(file_exists($file_nm2)){
				$b[$filename2] = "0";
				array_push($a,$b);
			}else{
				$b[$filename2] = "1";
				array_push($a,$b);
			}
		}
		
		if($binary3!=null){
			$file3 = fopen($file_nm3, 'wb');
    		fwrite($file3, $binary3);
			fclose($file3);
			if(file_exists($file_nm3)){
				$b[$filename3] = "0";
				array_push($a,$b);
			}else{
				$b[$filename3] = "1";
				array_push($a,$b);
			}
		}
		
			if($binary4!=null){
			$file4 = fopen($file_nm4, 'wb');
    		fwrite($file4, $binary4);
			fclose($file4);
			if(file_exists($file_nm4)){
				$b[$filename4] = "0";
				array_push($a,$b);
			}else{
				$b[$filename4] = "1";
				array_push($a,$b);
			}
		}
		
		$image_up = $db->storeImageJob($data[$i]->job_no,$data[$i]->fileName1,$data[$i]->fileName2,$data[$i]->fileName3,$data[$i]->fileName4);
		if($image_up){
		$b["id"] = $data[$i]->job_no;         
$b["status"] = 1;
array_push($a,$b);
}else{
		$b["id"] = $data[$i]->job_no;         
$b["status"] = 0;  
array_push($a,$b);
	}
	}

}///close for loop
echo json_encode($a);
?>