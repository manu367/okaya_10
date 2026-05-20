<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getJobStatus($link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql = "SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res = mysqli_query($link1,$job_sql);
$job_row = mysqli_fetch_assoc($job_res);
///////////////// estimate detail from master table //////////////////
$estimate = "SELECT * FROM estimate_master where job_no='".$docid."'";
$estimate_res = mysqli_query($link1,$estimate);
$estimate_row = mysqli_fetch_assoc($estimate_res);

////// get location details
//$location_info = explode("~",getAnyDetails($job_row['location_code'],"locationname,locationaddress","location_code","location_master",$link1));
$location_info = getLocationDispAddress($job_row['current_location'],$link1);
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
        <div align="center" class="lable"><u><strong>ESTIMATE COPY</strong></u></div>
        <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%"><strong>Job No.</strong></td>
                <td width="35%"><?=$docid?></td>
                <td width="15%"><strong>Create Date</strong></td>
                <td width="35%"><?=dt_format($job_row['open_date'])." ".$job_row['open_time']?></td>
              </tr>
              <tr>
                <td colspan="4" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px"> CUSTOMER DETAIL</strong></td>
              </tr>
              <tr>
                <td><strong>Customer Name</strong></td>
                <td><?=$job_row['customer_name']?></td>
                <td><strong>Contact No.</strong></td>
                <td><?=$job_row['contact_no']?></td>
              </tr>
              <tr>
                <td><strong>Alternate No.</strong></td>
                <td><?=$job_row['alternate_no']?></td>
                <td><strong>Email</strong></td>
                <td><?=$job_row['email']?></td>
              </tr>
              <tr>
                <td><strong>Address</strong></td>
                <td><?=$job_row['address']?></td>
                <td><strong>Pincode</strong></td>
                <td><?=$job_row['pincode']?></td>
              </tr>
              <tr>
                <td colspan="4" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> ESTIMATE DETAIL</strong></td>
              </tr>
              <tr>
                <td><strong>Estimate No.</strong></td>
                <td><?=$estimate_row['estimate_no'];?></td>
                <td><strong>Estimate Date</strong></td>
                <td><?=dt_format($estimate_row['estimate_date']);?></td>
              </tr>
              <tr>
                <td colspan="4" align="left"><i class="fa fa-wrench fa-lg"></i><strong style="font-size:14px"> Estimate Item Details</strong>&nbsp;&nbsp;&nbsp;</td>
              </tr>
           </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
            	<td width="25%"><strong>Part Name</strong></td>
                <td width="25%"><strong>Part Code</strong></td>
                <td width="10%"><strong>Hsncode</strong></td>
                <td width="10%"><strong>Basic Amt</strong></td>
				<td width="10%"><strong>Tax %</strong></td>
				<td width="10%"><strong>Tax Amt</strong></td>
				<td width="10%"><strong>Total Amt</strong></td>
            </tr>
          </thead>
          <tbody>
          <?php
		  $res_repair = mysqli_query($link1,"SELECT * FROM estimate_items where job_no = '".$docid."'");
		  while($row_repair = mysqli_fetch_assoc($res_repair)){
			  
			  $part_vender=explode("~",getAnyDetails($row_repair['partcode'],"vendor_partcode","partcode","partcode_master",$link1));
		  ?>
          	<tr>
                <td><?=$row_repair['part_name']."(".$part_vender[0].")";?></td>
                <td><?=$row_repair['partcode'];?></td>
                <td><?=$row_repair['hsn_code'];?></td>
                <td><?=$row_repair['basic_amount'];?></td>
				<td><?=$row_repair['tax_per'];?></td>
				<td><?=$row_repair['tax_amt'];?></td>
				<td><?=$row_repair['total_amount'];?></td>
            </tr>
          <?php
		  }
		  ?>   
          </tbody>
        </table>
		<table class="table" border="1">
           <tbody>  
              <tr>
                <td colspan="4" align="left"><strong>Billing (applicable for chargeable job only) Collected</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-inr fa-lg" aria-hidden="true"></i></td>
              </tr>
              <tr>
                <td width="50%" colspan="2" height="50" style="vertical-align:bottom;border-bottom:none"><?php  echo "____________________________"?></td>
                <td width="50%" colspan="2" align="right" style="vertical-align:bottom;border-bottom:none"><?php  echo "____________________________"?></td>
              </tr>
              <tr>
                <td colspan="2" style="border-top:none">(Customer signature with full name)</td>
                <td colspan="2" style="border-top:none" align="right">(Location signature)</td>
              </tr>
              <tr>
                <td colspan="4" align="left"><strong>Unit has been repaired to my satisfaction</strong></td>
              </tr>
              <tr>
                <td style="border-right:none"><strong>Receipt Date & Time</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo "____________________________"?></td>
                <td colspan="3" style="vertical-align:bottom;border-left:none">&nbsp;</td>
              </tr>
            </tbody>
    	</table>
    </page>
</body>
</html>