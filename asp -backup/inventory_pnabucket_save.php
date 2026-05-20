<?php
require_once("../includes/config.php");
$parentcode = $_POST['location_code'];
$id = $_POST['pnarow'];
$cnt = count($id);
mysqli_autocommit($link1, false);
$flag = true;
$err_msg = "";
$error= 0;
if($_POST['pnarow']){}else{

$error=1;

}

if($error==1){

	$msg = "Request could not be processed. No part has been selected. ";
		$cflag = "danger";
		$cmsg = "Failed";
		mysqli_close($link1);	   
 header("location:inventory_pna_bucket.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
exit;
}
//// Make System generated PNA no.//////
	$res_po=mysqli_query($link1,"select max(po_id) as no from po_master where from_code='".$_SESSION['asc_code']."'");
	$row_po=mysqli_fetch_array($res_po);
	$c_nos=$row_po['no']+1;
$po_no=$_SESSION['asc_code']."".$todayt."PNA".$c_nos; 
	//$po_no=$_SESSION['asc_code']."PNA".$c_nos; 
	///////////////////
	$fromaddress = explode ("~" ,getAnyDetails($_SESSION['userid'],"locationaddress,stateid","location_code","location_master",$link1));
	$toaddress = explode ("~" ,getAnyDetails($parentcode,"locationaddress,stateid","location_code","location_master",$link1));
  	////////////////////
    $usr_add="INSERT INTO po_master set 	po_no='".$po_no."', po_date='".$today."' , to_code ='".$parentcode."' , to_address='".$toaddress[0]."' ,to_state='".$toaddress[1]."', update_date='".$today."',entry_by='".$_SESSION['userid']."' ,entry_ip ='".$_SERVER['REMOTE_ADDR']."' ,status='1' ,from_code= '".$_SESSION['userid']."', from_address = '".$fromaddress[0]."' ,  	from_state = '".$fromaddress[1]."'  , potype = 'PNA', po_id='".$c_nos."'";
  $result3=mysqli_query($link1,$usr_add);
	//// check if query is not executed
	if (!$result3) {
	     $flag = false;
         $err_msg = "Error details1: " . mysqli_error($link1) . ".";
    }
	
	foreach($_POST['pnarow'] as $tmp=>$value){
	
	$part_code="partcode".$value;
	
$job_id="job_no".$value;
$model_code="model".$value;	
$product="product".$value;
$brand="brand".$value;

	$result = mysqli_query($link1,"update auto_part_request set status = '1'  where id = '".$value."' ");
//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Error details2: " . mysqli_error($link1) . ".";
    }	
	echo " insert into po_items set po_no = '".$po_no."' ,job_no ='".$_POST[$job_id]."' , status = '1' , partcode = '".$_POST[$part_code]."' ,type = 'PNA' , update_date = '".$today."' , qty = '1' ,product_id ='".$_POST[$product]."', brand_id='".$_POST[$brand]."', model_id= '".$_POST[$model_code]."' ";
	$result1 = mysqli_query($link1," insert into po_items set po_no = '".$po_no."' ,job_no ='".$_POST[$job_id]."' , status = '1' , partcode = '".$_POST[$part_code]."' ,type = 'PNA' , update_date = '".$today."' , qty = '1' ,product_id ='".$_POST[$product]."', brand_id='".$_POST[$brand]."', model_id= '".$_POST[$model_code]."' ");
	//// check if query is not executed
	if (!$result1) {
	     $flag = false;
        $err_msg = "Error details3: " . mysqli_error($link1) . ".";
    }
	//// update substatus of jobsheet data 
	$result2 = mysqli_query($link1," update jobsheet_data set sub_status = '31'  where job_no ='".$_POST[$job_id]."'");
	//// check if query is not executed
	if (!$result2) {
	    $flag = false;
        $err_msg = "Error details3.1: " . mysqli_error($link1) . ".";
    }
	///// entry in call/job  history
	$flag = callHistory($_POST[$job_id],$_SESSION['asc_code'],"31","PNA PO Raised","Part Requested",$_SESSION['userid'],"","","","",$ip,$link1,$flag);	
}
if ($flag) {
        mysqli_commit($link1);
        $msg = "PO is successfully placed  against selected PNA parts";
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
 header("location:inventory_pna_bucket.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
exit;
?>
