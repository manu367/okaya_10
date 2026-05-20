<?php
require_once("../includes/config.php");
///// delete particular imei no.
$result= mysqli_query($link1,"DELETE FROM temp_barcode_tnx WHERE id ='".$_REQUEST['rid']."'");
if($result){
	$po_no = base64_decode($_REQUEST['refid']);
	$partcode = base64_decode($_REQUEST['partcode']);
	$qty = base64_decode($_REQUEST['pqty']);
	$productdet = base64_decode($_REQUEST['partname']);
	header("Location:local_enter_imei.php?refid=".base64_encode($po_no)."&partcode=".base64_encode($partcode)."&pqty=".base64_encode($qty)."&partname=".base64_encode($productdet)."&grn_type=".$_REQUEST['grn_type']."".$pagenav);
}
?>