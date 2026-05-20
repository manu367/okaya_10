<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
if($docid!="")
$imei_sql="SELECT * FROM imei_data_temp where id='".$docid."'";
$imei_res=mysqli_query($link1,$imei_sql);
$imei_row=mysqli_fetch_assoc($imei_res);
if($_REQUEST['op']!=""){
	mysqli_autocommit($link1, false);
	$flag = true;
	if($_REQUEST['op']=='Y'){
	 //inserting query into data base
      	$sql = "INSERT INTO imei_data_import (imei1,imei2,import_date,model_id,model)VALUES('".$imei_row['imei1']."','".$imei_row['imei2']."','".$imei_row['import_date']."','".$imei_row['model_id']."','".$imei_row['model']."')";
		$result =	mysqli_query($link1,$sql);
				  //// check if query is not executed
	    if (!$result) {
           $flag = false;
              echo "Error details: " . mysqli_error($link1) . ".";
           }
	   				///////// update imei_data_temp
		$upd = mysqli_query($link1 , "update imei_data_temp set status='2',approve_date='".$today."',approve_by='".$_SESSION['userid']."' where id='".$docid."' " );
				 //// check if query is not executed
 	    if (!$upd) {
     	   $flag = false;
       	   echo "Error details: " . mysqli_error($link1) . ".";
   	    }
		
	}
	else if($_REQUEST['op']=='N'){
		$upd = mysqli_query($link1 , "update imei_data_temp set status='3',approve_date='".$today."',approve_by='".$_SESSION['userid']."' where id='".$docid."' " );
				 //// check if query is not executed
 	    if (!$upd) {
     	   $flag = false;
       	   echo "Error details: " . mysqli_error($link1) . ".";
   	    }
	}else{
	}
	 ///// check  master  query are successfully executed
 	if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "IMEI Successfully Updated.";
   	} else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
		$cflag = "danger";
		$cmsg = "Failed";
	} 
   	mysqli_close($link1);
}
header("Location:appr_import_imei.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
?>
