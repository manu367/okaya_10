<?php

require_once("../includes/config.php");

$docid=base64_decode($_REQUEST['refid']);

//// job details

$job_sql = "SELECT * FROM jobsheet_data where job_no='".$docid."'";

$job_res = mysqli_query($link1,$job_sql);

$job_row = mysqli_fetch_assoc($job_res);

////// get location details

//$location_info = explode("~",getAnyDetails($job_row['location_code'],"locationname,locationaddress","location_code","location_master",$link1));

$location_info = getLocationDispAddress($job_row['current_location'],$link1);
$image=getAnyDetails($row["brand_id"],"brand_logo","brand_id","brand_master",$link1);

?>

<!DOCTYPE>

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

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

		<table class="table" >

            <tbody>

              <tr>

                <td width="20%"><div style="padding-top:20px;padding-left:20px;"><img style="height:70px; width:auto;" src="../images/blogo.png"/></div></td>

                <td width="30%" align="center"><div id="barcodeprint"></div></td>

                <td width="30%">

                	

  					<div class="pull-right"><?=$location_info?> </br> Working time :- 10:00 AM TO 7:00 PM</div>                   

                </td>
				 <td width="20%"><img src="<?=$image?>"/></td>

              </tr>

            </tbody>

    	</table>

        <div align="center" class="lable"><u><strong>CUSTOMER COPY</strong></u></div>

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
                <td><strong><?php echo SERIALNO ?></strong></td>
                <td><?=$job_row['imei']?></td>
                <td><strong>&nbsp;</strong></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong>Job Type</strong></td>
                <td><?=$job_row['call_type']?></td>
                <td><strong>Job For</strong></td>
                <td><?=$job_row['call_for']?></td>
              </tr>
              <tr>
                <td><strong>Purchase Date</strong></td>
                <td><?=$job_row['dop']?></td>
              <td><strong>Warranty Status</strong></td>
                <td><?=$job_row['warranty_status']?></td>
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

                <p style="font-size:9px">

             1. The repair estimate will be provided on requestand the charges will be 25% of labour charge,If the estimate is not approved. <br/>
      2.  An advance of 50% of the approved estimate shall be collected before undertaking the repairs. 
      <br/>
      3.All repairs (except for imported, tampered, mishandled products) are guaranteed for labor for one month from date of delivery.
      <br/>
      4.Imported products are accepted for repairs subject to availability of spare parts. <br/>
      5. Defective components for out warranty jobs shall be returned along with the repaired equipment. 
      <br/>
      6.Reasonable care will be taken to the equipment given for repairs. However, we are not liable for any loss or damage arising from accident, fire, theft or any other cause beyond our control. 
      <br/>
      7.  Equipment's remaining uncollected for more than 30 days from the date of intimation for collection shall be disposed at the customer's risk. 
      <br/>
      8.Equipment will be delivered only against this receipt. 9.I agree to receive SMS notifications on my mobile related to the given Equipment.
      <br/>


                </p></td>

              </tr>

              <tr>

                <td colspan="2" height="50" style="vertical-align:bottom;border-bottom:none"><?php  echo "____________________________"?></td>

                <td colspan="2" align="right" style="vertical-align:bottom;border-bottom:none"><?php  echo "____________________________"?></td>

              </tr>

              <tr>

                <td colspan="2" style="border-top:none">(Customer signature with full name)</td>

                <td colspan="2" style="border-top:none" align="right">(AVSC signature)</td>

              </tr>

              <tr>

                <td style="border-right:none"><strong>Date & Time</strong></td>

                <td colspan="3" style="vertical-align:bottom;border-left:none"><?php  echo "____________________________"?></td>

              </tr>

              <tr>

                <td colspan="4" align="left">

                </td>

              </tr>

            </tbody>

    	</table>

        

        </page>

        



        <page size="A4">

        <table class="table" >

        <tbody>

           <tr>

                <td width="20%"><div style="padding-top:20px;padding-left:20px;"><img style="height:70px; width:auto;" src="../images/blogo.png"/></div></td>

                <td width="30%" align="center"><div id="barcodeprint"></div></td>

                <td width="30%">

                	

  					<div class="pull-right"><?=$location_info?> </br> Working time :- 10:00 AM TO 7:00 PM</div>                   

                </td>
				 <td width="20%"><img src="<?=$image?>"/></td>

              </tr>


            </tbody>

    	</table>

        <div align="center" class="lable"><u><strong>SERVICE CENTRE COPY</strong></u></div>

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
                <td><strong><?php echo SERIALNO ?></strong></td>
                <td><?=$job_row['imei']?></td>
                <td><strong>&nbsp;</strong></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td><strong>Job Type</strong></td>
                <td><?=$job_row['call_type']?></td>
                <td><strong>Job For</strong></td>
                <td><?=$job_row['call_for']?></td>
              </tr>
              <tr>
                <td><strong>Purchase Date</strong></td>
                <td><?=$job_row['dop']?></td>
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

              

            </tr>

          </thead>

          <tbody>

          <?php

		  $res_repair = mysqli_query($link1,"SELECT * FROM repair_detail where job_no = '".$docid."'");

		  while($row_repair = mysqli_fetch_assoc($res_repair)){

		  ?>

          	<tr>

                <td><?=getAnyDetails($row_repair['fault_code'],"defect_desc","defect_code","defect_master",$link1)." - ".$row_repair['fault_code']?></td>

                <td><?=getAnyDetails($row_repair['repair_code'],"rep_desc","rep_code","repaircode_master",$link1)." - ".$row_repair['repair_code']?></td>

                <td><?=getAnyDetails($row_repair['partcode'],"part_name","partcode","partcode_master",$link1)." - ".$row_repair['partcode']?></td>

               

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