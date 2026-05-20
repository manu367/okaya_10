<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql = "SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res = mysqli_query($link1,$job_sql);
$job_row = mysqli_fetch_assoc($job_res);
////// get location details
//$location_info = explode("~",getAnyDetails($job_row['location_code'],"locationname,locationaddress","location_code","location_master",$link1));
$location_info = getLocationDispAddress($job_row['location_code'],$link1);
   
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
        <div align="center" class="lable"><u><strong>DOA Duplicate CERTIFICATE</strong></u></div>
        <table class="table" border="1">
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
                <td><strong>Recipient Name</strong></td>
                <td><?php ?></td>
                <td><strong>Contact No.</strong></td>
                <td><?php ?></td>
              </tr>
              <tr>
                <td colspan="4" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
              <tr>
                <td><strong>Product</strong></td>
                <td><?php echo getAnyDetails($job_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
                <td><strong>Brand</strong></td>
                <td><?php echo getAnyDetails($job_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
              </tr>
              <tr>
                <td><strong>Model</strong></td>
                <td><?=$job_row['model']?></td>
                <td><strong>Accessory</strong></td>
                <td><?php echo $job_row['acc_rec'];?></td>
              </tr>
              <tr>
                <td><strong>IMEI/Serial No.1</strong></td>
                <td><?=$job_row['imei']?></td>
                <td><strong>IMEI/Serial No.2</strong></td>
                <td><?=$job_row['sec_imei']?></td>
              </tr>
              <tr>
                <td><strong>Job Type</strong></td>
                <td><font color="#FF0000"><?=$job_row['call_type']?></font></td>
                <td>&nbsp;<strong>DOA Polybag number </strong></td>
                <td>&nbsp;<?=$job_row['doa_bag']?></td>
              </tr>
              <tr>
                <td><strong>Purchase Date</strong></td>
                <td><?=$job_row['dop']?></td>
                <td><strong>Activation Date</strong></td>
                <td><?=$job_row['activation']?></td>
              </tr>
              <tr>
                <td><strong>ELS Status</strong></td>
                <td><?=$job_row['els_status']?></td>
                <td><strong>Warranty Status</strong></td>
                <td><?=$job_row['warranty_status']?></td>
              </tr>
              <tr>
                <td><strong>Initial Symptom</strong></td>
               
                <td><?php  $symp_name = explode("~",getAnyDetails($job_row['symp_code'],"symp_desc","symp_code","symptom_master",$link1));
				
				echo $symp_name[0];
				?></td>
                <td><strong>Physical Condition</strong></td>
                <td><?=$job_row['phy_cond']?></td>
              </tr>
              <tr>
                <td colspan="4" align="left"><i class="fa fa-pencil-square-o fa-lg"></i><strong style="font-size:14px"> PROBLEM REPORTED</strong></td>
              </tr>
              <tr>
                <td><strong>Defect Reported (As per Customer)</strong></td>
                <td colspan="3">
				<?php
				  $voc_name1 = explode("~",getAnyDetails($job_row['cust_problem'],"voc_desc","voc_code","voc_master",$link1));
				   $voc_name2 = explode("~",getAnyDetails($job_row['cust_problem2'],"voc_desc","voc_code","voc_master",$link1));
				    $voc_name3 = explode("~",getAnyDetails($job_row['cust_problem3'],"voc_desc","voc_code","voc_master",$link1));
				
				
				?>
				<?php echo $voc_name1[0]."&nbsp;&nbsp;/&nbsp;&nbsp;".$voc_name2[0]."&nbsp;&nbsp;/&nbsp;&nbsp;".$voc_name3[0]?></td>
              </tr>
              <tr>
                <td><strong>Remark</strong></td>
                <td colspan="3"><?=$job_row['remark']?></td>
              </tr>
              <tr>

                <td colspan="4" align="left"><strong>Terms & Conditions:</strong>

                <p style="font-size:9px">I hereby acknowledge that,<br><br>
1.	The Device submitted is complete in all respected Anee DOA policy.<br>
2.	The mentioned IMEI number device purchased by customer within 14 days from the date of invoice.<br>
3.	The device IMEI matches with Box IMEI.<br>
4.	The Proof of purchase received with GST number.<br><br> 
       
The Above Certificate is Valid for replacement if accompanied along with a Valid Service DOA Bag, issued by Anee Authorized Service Centre.

                
                
                </p></td></tr>
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