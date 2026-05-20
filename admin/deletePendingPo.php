<?php
require_once("../includes/config.php");
///////////////////   function for update status in po_master/////////////////////////////////////////////
function getPOSt($val,$sn,$link1){
	$flag1=0; $i=0; $j=0; $k=0; $l=0; $m=0;
	$res2=mysqli_query($link1 ,"select id,status from po_items where po_no = '".$val."'  and id !='".$sn."'");
 	$count =  mysqli_num_rows($res2);
	if($count>0){
	while($row = mysqli_fetch_array($res2)){
		if($row['status']==2){ $i++;}
		if($row['status']==6){ $j++;}
		if($row['status']==5){ $k++;}
		if($row['status']==1){ $l++;}
	}}
	$m=$i+$j+$l;
	if($count==0){
		$flag1 = 5;
	}else if($m==0 && $k>0){
		$flag1 = 5;
	}else{
		$flag1 = '';
	}

	return $flag1;
}
///////////////////////////// function end////////////////////////////////////////////////////////////////////////////


	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg = "";
		
	 ///// cancel po in po_items table////
	   $query2=("UPDATE po_items set status='5',cancel_by='".$_SESSION['userid']."',cancel_date='".$today."',cancel_rmk ='Manually' where id='".$_REQUEST['sno']."' ");	   
	   $result1 = mysqli_query($link1,$query2)or die ("ER1".mysqli_error());
	  
	  //// check if query is not executed
	  if (!$result1) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      }
	  
	  ///////// call  function for status update in po_master //////////////////////////////////////////////////
	  $po_stts=getPOSt($_REQUEST['challan'],$_REQUEST['sno'],$link1);
	  ///// cancel po in po_master ///////////
	  if($po_stts=='5'){
	   	$query1="UPDATE po_master set status='5',cancel_by='".$_SESSION['userid']."',cancel_date='".$today."',cancel_rmk='".$remark."' where po_no='".$_REQUEST['challan']."'";
	  	$result = mysqli_query($link1,$query1);
	  
	 	 //// check if query is not executed
	 	 	if (!$result) {
	     		$flag = false;
         		echo "Error details: " . mysqli_error($link1) . ".";
      	 	}
	  	}else{
		}


	///// check if all query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = "PO is cancel successfully.";
		echo "<BODY onLoad='window.close(); window.opener.location.reload(true);'></BODY>";
	} else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	mysqli_close($link1);

?>
