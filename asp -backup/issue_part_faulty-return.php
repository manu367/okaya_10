<?php
require_once("../includes/config.php");
//////////////// decode challan number////////////////////////////////////////////////////////


///// after hitting receive button ///
if($_POST){
 if ($_POST['save']=='Receive'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	
	if($_POST['partrow']){}else{

 $flag = false;
  $error_msg = "Select Atleast 1 part.";

}
print_r($_POST['partrow']);
$id = $_POST['partrow'];
$cnt = count($id);
for ($i =0 ; $i<$cnt ; $i++){ 
	////// fetching data from data table//////////////////////////////////////////////////////////////////////////////////////////
 $sql_po_data="select * from part_to_credit where  eng_status='1' and sno= '".$_POST['partrow'][$i]."'";
    $res_poData=mysqli_query($link1,$sql_po_data)or die("error1".mysqli_error());
    $row_poData=mysqli_fetch_assoc($res_poData);
		################### Insert in imei detail table By Vikas ########################
	echo 	$stock_type="stock_type".$_POST['partrow'][$i];
		
	
		  ///// initialize posted variables
		  
		
		  ///// update stock in  client inventory  Ok Receive//
		  if($_POST[$stock_type]=='OK'){
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['from_location']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set faulty=faulty+'1',updatedate='".$datetime."' where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['from_location']."'");
		  }		
		  else{			
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into client_inventory set location_code='".$row_poData['from_location']."',partcode='".$row_poData['partcode']."',faulty='1',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg = "Error details1: " . mysqli_error($link1) . ".";
           }
		     $flag=stockLedgerO($po_no,$today,$row_poData['partcode'],$row_poData['eng_id'],$row_poData['from_location'],"IN","Faulty","Faulty Part Receive from eng","",$_POST[$okqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag,$row_poData['from_location']);
			 
		  }
		  
		  
	////////////////////////////////Damage Receove///////////////////////////
	
	
	  ///// update stock in  client inventory  Ok Receive//
		  if($_POST[$stock_type]=='Damage'){
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['from_location']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set broken=broken+'1',updatedate='".$datetime."' where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['from_location']."'");
		  }		
		  else{			
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into client_inventory set location_code='".$row_poData['from_location']."',partcode='".$row_poData['partcode']."',broken='1',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg = "Error details1: " . mysqli_error($link1) . ".";
           }
		     $flag=stockLedgerO($po_no,$today,$row_poData['partcode'],$row_poData['eng_id'],$row_poData['from_location'],"IN","Damage","Faulty Part Receive from eng","",$_POST[$okqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag,$row_poData['from_location']);
			
		  }	  
		  	  ///// update stock in  client inventory  Ok Receive//
		  if($_POST[$stock_type]=='Missing'){
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['from_location']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set missing=missing+'1',updatedate='".$datetime."' where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['from_location']."'");
		  }		
		  else{			
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into client_inventory set location_code='".$row_poData['from_location']."',partcode='".$row_poData['partcode']."',missing='1',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg = "Error details1: " . mysqli_error($link1) . ".";
           }
		     $flag=stockLedgerO($po_no,$today,$row_poData['partcode'],$row_poData['eng_id'],$row_poData['from_location'],"IN","Missing","Faulty Part Receive from eng","",$_POST[$okqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag,$row_poData['from_location']);
			  
		  }	
		  
		$result1=mysqli_query($link1,"update part_to_credit set type='P2C',status='1',eng_status='4',stock_type='".$_POST[$stock_type]."',eng_rec_date='".$today."' ,remark='".$rcv_rmk."' where sno='".$row_poData['sno']."'");
		
		
							$res_faulty_user = mysqli_query($link1,"UPDATE user_inventory set faulty = faulty-'1' where location_code='".$_SESSION['asc_code']."' and partcode='".$row_poData['partcode']."' and 	locationuser_code ='".$row_poData['eng_id']."'");
				//// check if query is not executed
				if (!$res_faulty_user) {
					 $flag = false;
					 $error_msg = "Error detailsuserfauty: " . mysqli_error($link1) . ".";
				}
				
	}//// close while loop
	//// Update status in  master table
  
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$docid,"Faulty Part Receive From Eng","RECEIVE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$msg="Successfully Faulty Part Received";
		$cflag="success";
		$cmsg="Success";
    } else {
		mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
		
	} 
    mysqli_close($link1);
	///// move to parent page
header("location:faulty_part_eng.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  exit;
 }
 }
 
?>
