<?php
include_once './db_functions.php'; 
$db = new DB_Functions(); 
$json = $_POST["ImageUpload"];
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}

//Decode JSON into an Array 
$data = json_decode($json);
$temp=0;


for($i=0; $i<count($data) ; $i++) {
$temp++; // get  no of jobs for which images are uploaded 
        $base=$data[$i]->encodedString;

   
 // Get file name posted from Android App
        $filename = $data[$i]->fileName;

    // Decode Image//
  	 if($base!=null){
    	   $binary=base64_decode($base);}
  	  else
    	    {
   	 $binary=null;
   	     }

	
    header('Content-Type: bitmap; charset=utf-8');
	$file_nm='uploadproimages/'.$filename;
	
    // Images will be saved under 'www/imgupload/uplodedimages' folder   
	$file = fopen($file_nm, 'wb');

    // Create File
	if($binary!=null){
    		fwrite($file, $binary);
		}
	fclose($file);

}
echo 'Image upload complete, Please check your php file directory and count is : '.$temp;  

    
  
?>