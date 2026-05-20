<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getJobStatus($link1);
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
        <div align="center" class="lable"><u><strong>STORE COPY</strong></u></div>
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
                <td><strong>Invoice No.</strong></td>
                <td><?=$job_row['inv_no']?></td>
                <td><strong>Dealer Name</strong></td>
                <td><?=$job_row['dname']?></td>
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
                <td><strong>Defect reported</strong></td>
                <td colspan="3"><?php 
				
				 $voc_name1 = explode("~",getAnyDetails($job_row['cust_problem'],"voc_desc","voc_code","voc_master",$link1));
				   
				   
					
				if($job_row['cust_problem2']!=''){
					$voc_name2 = explode("~",getAnyDetails($job_row['cust_problem2'],"voc_desc","voc_code","voc_master",$link1));
					$voc2=" / ".$voc_name1[2];
				}else{
					$voc2="";
				}
				if($job_row['cust_problem3']!=''){
					 $voc_name3 = explode("~",getAnyDetails($job_row['cust_problem3'],"voc_desc","voc_code","voc_master",$link1));
					$voc3=" / ".$voc_name3[0];
				}else{
					$voc3="";
				}
				echo $voc_name1[0]."".$voc2."".$voc3;
				?></td>
              </tr>
              <tr>
                <td><strong>Remark</strong></td>
                <td colspan="3"><?=$job_row['remark']?></td>
              </tr>
              <tr>
                <td colspan="4" align="left"><i class="fa fa-wrench fa-lg"></i><strong style="font-size:14px"> REPAIR STATUS</strong>&nbsp;&nbsp;&nbsp;<?=$arrstatus[$job_row["sub_status"]][$job_row['status']]?></td>
              </tr>
           </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
            	<td width="25%"><strong>Fault Code</strong></td>
                <td width="25%"><strong>Repair Code</strong></td>
                <td width="40%"><strong>Part Consume</strong></td>
                <td width="10%"><strong>Qty</strong></td>
            </tr>
          </thead>
          <tbody>
          <?php
		  $res_repair = mysqli_query($link1,"SELECT * FROM repair_detail where job_no = '".$docid."'");
		  while($row_repair = mysqli_fetch_assoc($res_repair)){
		  ?>
          	<tr>
                <td><?=getAnyDetails($row_repair['fault_code'],"symp_desc","symp_code","symptom_master",$link1)." - ".$row_repair['fault_code']?></td>
                <td><?=getAnyDetails($row_repair['repair_code'],"rep_desc","rep_code","repaircode_master",$link1)." - ".$row_repair['repair_code']?></td>
                <td><?=getAnyDetails($row_repair['partcode'],"part_name","partcode","partcode_master",$link1)." - ".$row_repair['partcode']?></td>
                <td><?=$row_repair['part_qty']?></td>
            </tr>
          <?php
		  }
		  ?>   
          </tbody>
        </table>
		<table class="table" border="1">
           <tbody> 
           	  <tr>
                <td colspan="4" align="left"><strong>Delivery Remark</strong>:</td>
              </tr> 
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