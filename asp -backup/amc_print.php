<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// job details
$amc_sql = "SELECT * FROM amc where amcid='".$docid."'";
$amc_res = mysqli_query($link1,$amc_sql);
$amc_row = mysqli_fetch_assoc($amc_res);
////// get location details
//$location_info = explode("~",getAnyDetails($job_row['location_code'],"locationname,locationaddress","location_code","location_master",$link1));
$location_info = getLocationDispAddress($amc_row['location_code'],$link1);


   
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Print Jobsheet</title>
<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/printcss.css" rel="stylesheet">
<script src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery-barcode.js"></script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$("#barcodeprint").barcode(
		"<?=$docid?>", // Value barcode (dependent on the type of barcode)
		"code128" // type (string)
/* Types
codabar
code11 (code 11)
code39 (code 39)
code93 (code 93)
code128 (code 128)
ean8 (ean 8)
ean13 (ean 13)
std25 (standard 2 of 5 - industrial 2 of 5)
int25 (interleaved 2 of 5)
msi
datamatrix (ASCII + extended)
*/
/* Setting
barWidth: 1,
barHeight: 50,
moduleSize: 5,
showHRI: true,
addQuietZone: true,
marginHRI: 5,
bgColor: "#FFFFFF",
color: "#000000",
fontSize: 10,
output: "css",
posX: 0,
posY: 0
*/
	);
});
</script>
</head>

<body>
<!--	<page size="A4" layout="portrait"></page>-->
	<page size="A4">
		<table class="table" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="20%"><img src="../images/blogo.png"/></td>
                <td width="30%" align="center"><div id="barcodeprint"></div></td>
                <td width="50%">
                	<div class="pull-left"><strong>Location Details :</strong></div>
  					<div class="pull-right"><?=$location_info?></div>                   
                </td>
              </tr>
            </tbody>
    	</table>
        <div align="center" class="lable"><u><strong>AMC CERTIFICATE</strong></u></div>
        <table class="table" border="1">
            <tbody>
              <tr>
                <td width="15%"><strong>AMC No.</strong></td>
                <td width="35%"><?=$docid?></td>
                <td width="15%"><strong>Create Date</strong></td>
                <td width="35%"><?=dt_format($amc_row['purchase_date'])." ".$amc_row['open_time']?></td>
              </tr>
              <tr>
                <td colspan="4" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px"> CUSTOMER DETAIL</strong></td>
              </tr>
              <tr>
                <td><strong>Customer Name</strong></td>
                <td><?=$amc_row['customer_name']?></td>
                <td><strong>Contact No.</strong></td>
                <td><?=$amc_row['contract_no']?></td>
              </tr>
              <tr>
                <td><strong>Address.</strong></td>
                <td><?=$amc_row['addrs']?></td>
                <td><strong>Email</strong></td>
                <td><?=$amc_row['email']?></td>
              </tr>
             
              <tr>
                <td colspan="4" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
              <tr>
                <td><strong>Product</strong></td>
                <td><?php echo getAnyDetails($amc_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
                <td><strong>Brand</strong></td>
                <td><?php echo getAnyDetails($amc_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
              </tr>
              <tr>
                <td><strong>Model</strong></td>
                <td><?php echo getAnyDetails($amc_row["model_id"],"model","model_id","model_master",$link1);?></td>
                <td><strong></strong></td>
                <td></td>
              </tr>
           
              <tr>
                <td><strong>AMC Type</strong></td>
                <td><font color="#FF0000"><?=$amc_row['amc_type']?></font></td>
                <td><strong><?php echo SERIALNO ?></strong></td>
                <td>&nbsp;<?=$amc_row['serial_no']?></td>
              </tr>
              <tr>
                <td><strong>AMC Start Date</strong></td>
                <td><?=dt_format($amc_row['amc_start_date'])?></td>
                <td><strong>AMC End Date</strong></td>
                <td><?=dt_format($amc_row['amc_end_date'])?></td>
              </tr>
              <tr>
                <td><strong>Purchase Date</strong></td>
                <td><?=dt_format($amc_row['purchase_date'])?></td>
                <td><strong>AMC Duration(in Days)</strong></td>
                <td><?=$amc_row['amc_duration']?></td>
              </tr>
              <tr>
                <td><strong>Entity Name</strong></td>
               
                <td><?php echo getAnyDetails($amc_row['entity_type'],"name","id","entity_type",$link1);?></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td colspan="4" align="left"><i class="fa fa-pencil-square-o fa-lg"></i><strong style="font-size:14px"> Payment Details</strong></td>
              </tr>
               <tr>
                <td><strong>AMC Amount</strong></td>
               
                <td><?=$amc_row['amc_amount'];?></td>
                <td>Payment Mode</td>
                <td><?=$amc_row['mode_of_payment'];?></td>
              </tr>
               <tr>
                <td><strong>Cheque number</strong></td>
               
                <td><?=$amc_row['cheque_no'];?></td>
                <td>Cheque Date</td>
                <td><?=$amc_row['cheque_date'];?></td>
              </tr> <tr>
                <td><strong>CR/Transaction Number</strong></td>
               <td><?=$amc_row['cr_no'];?></td>
                <td></td>
                <td></td>
              </tr>
              
            

              <!--<tr>
                <td colspan="4" align="left">I have read and understood the aforesaid note and terms &amp; conditions as mentioned below.</td>
              </tr>-->
              <tr>
                <td colspan="2" height="50" style="vertical-align:bottom;border-bottom:none"><?php  echo "____________________________"?></td>
                <td colspan="2" align="right" style="vertical-align:bottom;border-bottom:none"><?php  echo "____________________________"?></td>
              </tr>
              <tr>
                <td colspan="2" style="border-top:none">(Customer signature with full name)</td>
                <td colspan="2" style="border-top:none" align="right">(Location signature)</td>
              </tr>
              <tr>
                <td style="border-right:none"><strong>Date & Time</strong></td>
                <td colspan="3" style="vertical-align:bottom;border-left:none"><?php  echo "____________________________"?></td>
              </tr>
              <!--<tr>
                <td colspan="4" align="left"><strong>Terms & Conditions:</strong>
                <p style="font-size:9px">&nbsp;</p>
                </td>
              </tr>-->
            </tbody>
    	</table>
    </page>
</body>
</html>