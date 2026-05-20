<?php

include_once './db_functions.php'; 
$db = new DB_Functions(); 
$json = $_POST["ImageUpload"];
//echo $json;
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}

//Decode JSON into an Array 
$data = json_decode($json);

$ei = $_REQUEST[eid];

$temp=0;
$a=array();
    $b=array();        
	$b["status"] = 'yes';
    $base=$data->food_expns_file;
	$base1=$data->other_expns_file;
	$base2=$data->courier_expns_file;
	$base3=$data->local_expns_file;
	$base4=$data->mobile_expns_file; 

 // Get file name posted from Android App
    $filename = $data->food_expns_Img;
	$filename1 = $data->other_expns_Img;
	$filename2 = $data->courier_expns_Img;
	$filename3 = $data->local_expns_Img;
	$filename4 = $data->mobile_expns_Img;
	

    // Decode Image
  	 if($base!=null){
    	   $binary=base64_decode($base);}
  	  else
    	    {
   	 $binary=null;
   	     }

 	if($base1!=null){
     	  $binary1=base64_decode($base1);}
    	else
    	    {
    	$binary1=null;
    	    }

	if($base2!=null){
	       $binary2=base64_decode($base2);}
    	else
     	   {
   	 $binary2=null;
    	    }

	if($base3!=null){
     	  $binary3=base64_decode($base3);}
 	   else
      	  {
    	$binary3=null;
    	    }

    if($base4!=null){
     	  $binary4=base64_decode($base4);}
 	   else
      	  {
    	$binary4=null;
    	    }
			
	 if($base5!=null){
     	  $binary5=base64_decode($base5);}
 	   else
      	  {
    	$binary5=null;
    	    }
	
    header('Content-Type: bitmap; charset=utf-8');
	$file_nm='expenseimage/'.$filename;
	$file_nm1='expenseimage/'.$filename1;
	$file_nm2='expenseimage/'.$filename2;
	$file_nm3='expenseimage/'.$filename3;
	$file_nm4='expenseimage/'.$filename4;
	$file_nm5='expenseimage/'.$filename5;

    // Images will be saved under 'www/imgupload/uplodedimages' folder
    // Create File
	if($binary!=null){
		$file = fopen($file_nm, 'wb');
    	fwrite($file, $binary);
		fclose($file);
	}
	if($binary1!=null){
		$file1 = fopen($file_nm1, 'wb');
    	fwrite($file1, $binary1);
		fclose($file1);
	}
	if($binary2!=null){
		$file2 = fopen($file_nm2, 'wb');
    	fwrite($file2, $binary2);
		fclose($file2);
	}
	if($binary3!=null){
		$file3 = fopen($file_nm3, 'wb');
    	fwrite($file3, $binary3);
		fclose($file3);
	}
   if($binary4!=null){
	   $file4 = fopen($file_nm4, 'wb');
	   fwrite($file4, $binary4);
	   fclose($file4);
	}
    if($binary5!=null){	
		$file5 = fopen($file_nm5, 'wb');
    	fwrite($file5, $binary5);
		fclose($file5);
	}
array_push($a,$b); 
    
 echo json_encode($a); 
?>