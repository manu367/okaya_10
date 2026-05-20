<?php 
require_once("../includes/config.php");
$sel_usr="select partcode from partcode_master where partcode='".$_REQUEST['part']."'";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
	echo $sel_result['partcode'];
	?>