<?php
require_once("../includes/config.php");

mysqli_autocommit($link1, false);
$flag = true;
$err_msg = "";

 	 $claim_sql = "SELECT * FROM job_claim_appr  where app_status = '' ";
	$claim_res = mysqli_query($link1,$claim_sql);
	while($claimdata_row = mysqli_fetch_assoc($claim_res)){
		
		
		 $aprstatus="appr".$claimdata_row['id'];
		 $remark="remark".$claimdata_row['id'];
		 $travel="trvl".$claimdata_row['id'];
	$result = mysqli_query($link1,"update job_claim_appr set app_status = '".$_POST[$aprstatus]."',remark='".$_POST[$remark]."',travel_km='".$_POST[$travel]."',app_date='".$today."'  where id = '".$claimdata_row['id']."' ");	
//// check if query is not executed
	 if (!$result) {
	      $flag = false;
        $err_msg = "Error details2: " . mysqli_error($link1) . ".";
     }	
}
if ($flag) {
     mysqli_commit($link1);
        $msg = "Call Claim Approved";
		$cflag = "success";
		$cmsg = "Success";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again. ".$err_msg;
		$cflag = "danger";
		$cmsg = "Failed";
	} 
    mysqli_close($link1);
	   ///// move to parent page
  header("location:claim_approval.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
exit;
?>
