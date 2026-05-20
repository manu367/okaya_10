<?php
require_once("../includes/config.php");

$docid=base64_decode($_REQUEST['refid']); 
//$docid="PND00173/25/0001";

//// po details from master table
$po_sql = "SELECT * FROM billing_master where challan_no='" . $docid . "'";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);

//// po details from master table
$job_sql = "SELECT job_no, open_date, imei, repl_appr_no, repl_appr_date, current_location, del_dc_no, pick_dc_no, partner_id, partner_type, m_job_date, model_id FROM jobsheet_data where job_no='" . $po_row['job_no'] . "'";
$job_res = mysqli_query($link1, $job_sql);
$job_row = mysqli_fetch_assoc($job_res);

////// get location details
//$location_info_from = explode("~",getAnyDetails($po_row['from_location'],"locationname,locationaddress,zipcode,contactno1","location_code","location_master",$link1));
$location_info_from = explode("~",getAnyDetails($po_row['from_location'],"locationname,locationaddress,zipcode,contactno1","location_code","location_master",$link1));
$location_info = getLocationDispAddress($po_row['from_location'],$link1);

$location_info_to = explode("~", getAnyDetails($po_row['customer_id'], "customer_name,address1,cityid,stateid,pincode,email,gst_no,mobile", "customer_id", "customer_master", $link1));

//////// get  state details///////////////////
$state_from = explode("~",getAnyDetails($po_row['from_stateid'],"state,stateid","stateid","state_master",$link1));
//print_r('dddddd');exit;

$state_to = explode("~",getAnyDetails($po_row['to_stateid'],"state,stateid","stateid","state_master",$link1));
//print_r('dddddd');exit;
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Invoice</title>
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
<page size="A4" layout="portrait">
	<!---<page size="A4">--->

  <div align="center" class="lable" style="padding: 15px; font-size: 25px;"><u><strong><?php if($po_row['document_type']=="DC"){ echo "Delivery Challan"; $str = "Challan";}else if($po_row['document_type']=="INV"){ echo "Invoice";$str = "Invoice";}else{
		echo $po_row['document_type'] ;$str = $po_row['document_type'];}?></strong></u></div>

    <!--------------------------------------------------------- portrait start --------------------------------------------------->

    <table class="table" border="1" style="margin-bottom: 5px;">
      <tbody>
        <tr>
          <td width="40%" colspan="2"><img src="../images/blogo.png"/></td>
          <td width="20%" rowspan="2">
            <div class="pull-left"><strong>BILL TO :</strong></div>
  					<div class="pull-left">
              <?php
               /* $rdm_sql = "SELECT * FROM retailer_distibuter_master where sap_id='".$job_row['partner_id']."'";
                $rdm_res = mysqli_query($link1, $rdm_sql);
                $rdm_row = mysqli_fetch_assoc($rdm_res);

                echo $rdm_row['name']." (".$rdm_row['sap_id'].")";
                echo "<br>";
                echo $rdm_row['street'].", ".$rdm_row['district'].", ".$rdm_row['state'].", ".$rdm_row['pincode'];
                echo "<br><br>";
                
                echo "<strong>Mobile : </strong>".$rdm_row['mobile'];*/
						echo $po_row['bill_from'];
                echo "<br>";
                echo $po_row['from_addrs'].", ".$po_row['from_city'].", ".$po_row['from_state'].", ".$po_row['from_pincode'];
                echo "<br><br>";
                
                echo "<strong>Mobile : </strong>".$po_row['from_phone'];
                
              ?>
            </div>   
          </td>
          <td width="20%" rowspan="2">
            <div class="pull-left"><strong>SHIP TO :</strong></div>
  					<div class="pull-left">
              <?php
             
                echo $po_row['bill_to'];
                echo "<br>";
                echo $po_row['deliv_addrs'].", ".$po_row['to_city'].", ".$po_row['to_state'].", ".$po_row['to_pincode'];
                echo "<br><br>";
                
                echo "<strong>Mobile : </strong>".$po_row['to_phone'];
              ?>
            </div>                  
          </td>
          <td width="15%" style="text-align:center;" rowspan="2">
            <strong>Challan No.: </strong><?=$docid?>      
            <br>
            <strong>Date: </strong><?=dt_format($po_row['sale_date'])?> 
            <br>
            <br>
            <div id="barcodeprint"></div>   
          </td>
        </tr>
        <tr>
          <td width="20%">
            <strong>Principal Address :</strong> <?=$location_info?>
            <br>
            <br>
          </td>
          <td width="25%">
            <strong>Billing Location :</strong> 
            <?=$po_row['bill_from']?>
            <br>
            <?=$po_row['disp_addrs'].", ".$po_row['from_city'].", ".$po_row['from_state'].", ".$po_row['from_pincode'];?>
            <br>
            <br>
            <?php
              $state_from1 = explode("~",getAnyDetails($po_row['from_stateid'],"state,stateid","stateid","state_master",$link1));
            ?>
            <strong>GSTIN:<?=$po_row['ship_from_gst']." | ".$state_from1[0];?></strong> 
          </td>
        </tr>

      </tbody>
    </table>

    <table class="table" border="1" style="margin-bottom: 5px;">
      <tbody>
        <tr>
          <td width="15%"><strong>Pick Up By</strong></td>
          <td width="15%"><strong>Mode of dispatch</strong></td>
          <td width="15%"><strong>Contact Person Name</strong></td>
          <td width="15%"><strong>Contact Person No</strong></td>
          <td width="15%"><strong>Vehicle No</strong></td>
          <td width="15%"><strong>Date of disptch</strong></td>
        </tr>
        <tr>
          <td width="15%"><?=$po_row['pick_up_by']?></td>
          <td width="15%"><?=$po_row['transport_name']?></td>
          <td width="15%"><?=$po_row['person_name']?></td>
          <td width="15%"><?=$po_row['person_no']?></td>
          <td width="15%"><?=$po_row['vehicle_no']?></td>
          <td width="15%"><?=dt_format($job_row['m_job_date'])?></td>
        </tr>
      </tbody>
    </table>

    <table class="table" border="1" style="margin-bottom: 5px;">
      <tbody>
        <tr>
          <td colspan="9" width="95%" style="text-align: center; background-color: gainsboro;"><strong style="font-size:14px"> Defective Battery Details </strong></td>
        </tr>
        <tr>
          <td><strong>Material Code</strong></td>
          <td><strong>Description</strong></td>
          <td><strong>Product S.No</strong></td>
          <td><strong>Service Request No</strong></td>
          <td><strong>Service Request date</strong></td>
          <td><strong>FSE Name</strong></td>
          <td><strong>FSE Contact No</strong></td>
          <td><strong>Replacement Approval No</strong></td>
          <td><strong>Approval Date</strong></td>
        </tr>
        <?php
          $podata_sql="SELECT * FROM billing_product_items where challan_no='".$job_row['del_dc_no']."'";
          $podata_res=mysqli_query($link1,$podata_sql);
          $podata_row=mysqli_fetch_assoc($podata_res);

          //$product_name = explode("~", getAnyDetails($podata_row['partcode'],"part_name,product_id,brand_id,hsn_code,vendor_partcode","partcode","partcode_master",$link1)); 
          $model_name = explode("~", getAnyDetails($job_row['model_id'],"model,model_id","model_id","model_master",$link1));
        ?>
        <?php
          $loc = explode("~",getAnyDetails($job_row['current_location'],"locationname,contactno1","location_code","location_master",$link1));
		  //print_r('dddddddd');exit;

        ?>
        <tr>
          <td><?=$model_name[1]?></td>
          <td><?=$model_name[0]." (".$model_name[1].")"?></td>
          <td><?=$job_row["imei"]?></td>
          <td><?=$job_row["job_no"]?></td>
          <td><?=dt_format($job_row["open_date"])?></td>
          <td><?=$loc[0]." (".$job_row["current_location"].")";?></td>
          <td><?=$loc[1]?></td>
          <td><?=$job_row["repl_appr_no"]?></td>
          <td><?=dt_format($job_row["repl_appr_date"])?></td>
        </tr>
      </tbody>
    </table>

    <table class="table" border="1" style="margin-bottom: 5px;">
      <tbody>

        <!---<tr>
          <td colspan="13" width="95%" style="text-align: center; background-color: gainsboro;"><strong style="font-size:14px"> Replaced Battery Details </strong></td>
        </tr>--->

        <tr>
          <td colspan="13" width="95%" style="text-align: center; background-color: gainsboro;"><strong style="font-size:14px"> Replaced by Dealer or distributor </strong></td>
        </tr>

        <tr>
          <td colspan="2"><strong>Material Code</strong></td>
          <td colspan="3"><strong>Description</strong></td>
          <td colspan="3"><strong>Product S.No</strong></td>
          <td colspan="3"><strong>Distributor Name</strong></td>
          <td colspan="2"><strong>Distributor Code</strong></td>          
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
          <td colspan="3">&nbsp;</td>
          <td colspan="3">&nbsp;</td>
          <td colspan="3">&nbsp;</td>
          <td colspan="2">&nbsp;</td>          
        </tr>

        <tr>
          <td colspan="13" width="95%" style="text-align: center; background-color: gainsboro;"><strong style="font-size:14px"> Replaced from the Service Center </strong></td>
        </tr>
        
        <tr>
          <td><strong>Material Code</strong></td>
          <td><strong>Description</strong></td>
          <td><strong>Product S.No</strong></td>
          <td><strong>Service Request No</strong></td>
          <td><strong>Service Request date</strong></td>
          <td><strong>Replacement Approval No</strong></td>
          <td><strong>Approval date</strong></td>
          <td><strong>Qty</strong></td>
          <td><strong>UOM</strong></td> 
          <td><strong>Basic Price</strong></td>
          <td><strong>GST %</strong></td>
          <td><strong>GST Value</strong></td>     
          <td><strong>Total Price</strong></td>          
        </tr>
        <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			$podata_sql="SELECT * FROM billing_product_items where challan_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);

			while($podata_row=mysqli_fetch_assoc($podata_res)){

				$val = $podata_row['qty']*$podata_row['price'];
        $taxable = $val-$podata_row['discount_amt']*$podata_row['qty'];

        $product_name = explode("~", getAnyDetails($podata_row['partcode'],"part_name,product_id,brand_id,hsn_code,vendor_partcode","partcode","partcode_master",$link1)); 

        $model_name = explode("~", getAnyDetails($podata_row['model_id'],"model,model_id","model_id","model_master",$link1)); 
				$total_price = currencyFormat($podata_row['price']);
				$per = $total_price*18/100;

			?>
        <tr>
          <td><?=$model_name[1]?></td>
          <td><?=$model_name[0]?></td>
          <td><?=$job_row["imei"]?></td>
          <td><?=$po_row["job_no"]?></td>
          <td><?=dt_format($job_row["open_date"])?></td>
          <td><?=$job_row["repl_appr_no"]?></td>
          <td><?=dt_format($job_row["repl_appr_date"])?></td>
          <td>1</td>
          <td>NOS</td>
          <td><?=currencyFormat($podata_row['price'])?></td>
          <td>18</td>
          <td><?=$per?></td>
          <td><?=currencyFormat($podata_row['item_total'])?></td>
        </tr>
        <?php 
          $total+=$podata_row['qty'];
          $price+=$podata_row['price'];
          $value+=$podata_row['item_total'];                                                
          $discount = $podata_row['discount'];
          $i++;
          }
         ?>
      </tbody>
    </table>    

    <table class="table" border="1" style="margin-bottom: 5px;">
      <tbody>
        <tr>
          <td width="50%" rowspan="2">
            <strong> Declaration: </strong>&nbsp;&nbsp;We certify that, This is a replacement Delivery Challan. The defective battery approved in warranty claim is being replaced to customer through this Delivery challan hence there is no commercial transaction involved.
          </td>
          <td width="20%"><strong>Total Value of invoice(in words) :</strong> <?php echo number_to_words($value+$po_row['round_off']) . " Only"; ?></td>
          <td width="25%">
              <div> 
                <div class="pull-left"><strong>Total Value :</strong></div>
                <div class="pull-right"><?php echo currencyFormat($value+$po_row['round_off']); ?></div>
                <br><br>
              </div>
              <div> 
                <div class="pull-left"><strong>Other Charges Less Disc. & Rebate Total Tax Value :</strong></div>
                <div class="pull-right"><?php echo currencyFormat("0.00"); ?></div>
                <br><br>
              </div>
          </td>
        </tr>
        <tr>
          <td width="20%">
            <br><br><br>
            <strong>Reciever Signature with Seal and Date</strong>
          </td>
          <td width="25%">
            <div><strong>For SU-KAM INT. PVT. LTD.</strong></div>  
            <br><br>
            <div><strong>Authorised Signatory</strong></div>    
          </td>
        </tr>
      </tbody>
    </table>


    <!--------------------------------------------------------- portrait end ----------------------------------------------------->

    <?php /************************************************************************************* ?>

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
        <div align="center" class="lable"><u><strong><?php if($po_row['document_type']=="DC"){ echo "Delivery Note"; $str = "Challan";}else if($po_row['document_type']=="INV"){ echo "Invoice";$str = "Invoice";}else{
		echo $po_row['document_type'] ;$str = $po_row['document_type'];}?></strong></u></div>

      <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-users fa-lg"></i><strong style="font-size:14px">&nbsp;PARTY DETAILS</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong><?=$str?> From</strong></td>
                <td colspan="2"><?=$location_info_from['0']." (".$po_row['from_location'].")";?></td>
				<td colspan="2"><strong><?=$str?> To</strong></td>
                <td colspan="2"><?=$location_info_to['0']." (".$po_row['customer_id'].")";?></td>                
              </tr>
              <tr>
			  <td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=$po_row['from_addrs'];?></td>
                <td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=$po_row['deliv_addrs'];?></td>				
              </tr>
              <tr>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=$state_from['0'];?></td>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=$state_to['0'];?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>GST No.</strong></td>
                <td colspan="2"><?=$po_row['from_gst_no']?></td>
                <td colspan="2"><strong>GST No.</strong></td>
                <td colspan="2"><?=$po_row['to_gst_no']?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>State Code</strong></td>
                <td colspan="2"><?=$state_from[1]?></td>
                <td colspan="2"><strong>State Code</strong></td>
                <td colspan="2"><?=$state_to[1]?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>Pincode</strong></td>
                <td colspan="2"><?=$location_info_from[2]?></td>
                <td colspan="2"><strong>Pincode</strong></td>
                <td colspan="2"><?=$location_info_to[4]?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>Contact No.</strong></td>
                <td colspan="2"><?=$location_info_from[3]?></td>
                <td colspan="2"><strong>Contact No.</strong></td>
                <td colspan="2"><?=$location_info_to[7]?></td>
              </tr>

              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;<?=strtoupper($str)?> DETAILS</strong></td>
              </tr>

              <tr>
                <td width="15%" colspan="2"><strong><?=$str?> No.</strong></td>
                <td width="35%" colspan="2"><?=$docid?></td>
                <td width="15%" colspan="2"><strong><?=$str?> Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($po_row['sale_date'])?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>Complaint No</strong></td>
                <td colspan="2"><?=$po_row["job_no"]?></td>
                <td colspan="2"><strong>Complaint date</strong></td>
                <td colspan="2"><?=dt_format($job_row["open_date"])?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>Repl. Approval No</strong></td>
                <td colspan="2"><?=$job_row["repl_appr_no"]?></td>
                <td colspan="2"><strong>Repl. Approval Date</strong></td>
                <td colspan="2"><?=dt_format($job_row["repl_appr_date"])?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>FSE Name</strong></td>
                <td colspan="2">
                  <?php
                    $loc = explode("~",getAnyDetails($job_row['current_location'],"locationname,contactno1","location_code","location_master",$link1));
                    echo $loc[0]." (".$job_row["current_location"].")";
                  ?>
                </td>
                <td colspan="2"><strong>FSE Contact No</strong></td>
                <td colspan="2"><?=$loc[1]?></td>
              </tr>
              
              <tr>
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> DEFECTIVE BATTERY DETAIL</strong></td>
              </tr>

              <tr>
                <td colspan="2"><strong>Defective S.No</strong></td>
                <td colspan="2"><?=$job_row["imei"]?></td>
                <td colspan="2"><strong>Stock Type</strong></td>
                <td colspan="2"><?php echo "FAULTY"; ?></td>
              </tr>

              <?php
                	$podata_sql="SELECT * FROM billing_product_items where challan_no='".$job_row['pick_dc_no']."'";
                  $podata_res=mysqli_query($link1,$podata_sql);
                  $podata_row=mysqli_fetch_assoc($podata_res);

                  $product_name = explode("~", getAnyDetails($podata_row['partcode'],"part_name,product_id,brand_id,hsn_code,vendor_partcode","partcode","partcode_master",$link1)); 

                  $model_name = explode("~", getAnyDetails($podata_row['model_id'],"model,partcode,model_id","model_id","model_master",$link1));

                  $repl_podata_sql="SELECT * FROM billing_product_items where challan_no='".$job_row['del_dc_no']."'";
                  $repl_podata_res=mysqli_query($link1,$repl_podata_sql);
                  $repl_podata_row=mysqli_fetch_assoc($repl_podata_res);
                  
                 //$repl_product_name = explode("~", getAnyDetails($repl_podata_row['partcode'],"part_name,product_id,brand_id,hsn_code,vendor_partcode","partcode","partcode_master",$link1)); 

                  //$repl_model_name = explode("~", getAnyDetails($repl_podata_row['model_id'],"model,partcode,model_id","model_id","model_master",$link1));
              ?>

              <tr>
                <td colspan="2"><strong>Description Of Part</strong></td>
                <td colspan="2"><?=$product_name[0].' | '.$podata_row['partcode'];?></td>
                <td colspan="2"><strong>Description Of Model</strong></td>
                <td colspan="2"><?=$model_name[0].' | '.$model_name[2].' | '.$model_name[1];?></td>
              </tr>

              <tr>
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> REPLACED BATTERY DETAIL</strong></td>
              </tr>

              <tr>
                <td colspan="2"><strong>Replaced S.No</strong></td>
                <td colspan="2"><?=$po_row["job_repl_serial_no"]?></td>
                <td colspan="2"><strong>Stock Type</strong></td>
                <td colspan="2"><?php echo "FRESH"; ?></td>
              </tr>
              <!---
              <tr>
                <td colspan="2"><strong>Description Of Part</strong></td>
                <td colspan="2"><?=$repl_product_name[0].' | '.$repl_podata_row['partcode'];?></td>
                <td colspan="2"><strong>Description Of Model</strong></td>
                <td colspan="2"><?=$repl_model_name[0].' | '.$repl_model_name[2].' | '.$repl_model_name[1];?></td>
              </tr>----->

              <tr>
                <td colspan="2"><strong>Partner Type</strong></td>
                <td colspan="2"><?php 
                  if($job_row['partner_type']=="1"){
                    echo "Distributor";
                  } else if($job_row['partner_type']=="2"){
                    echo "Direct dealer";
                  }else if($job_row['partner_type']=="3"){
                    echo "Retailer";
                  }else{
                    echo "";
                  }
                ?></td>
                <td colspan="2"><strong>Partner Name</strong></td>
                <td colspan="2"><?php 
                $rdm_sql = "SELECT * FROM retailer_distibuter_master where sap_id='".$job_row['partner_id']."'";
                $rdm_res = mysqli_query($link1, $rdm_sql);
                $rdm_row = mysqli_fetch_assoc($rdm_res);
                echo cleanData($rdm_row['name'])." (".$job_row['partner_id'].")";
                ?></td>
              </tr>

              <tr>
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> REPLACED ITEM LIST</strong></td>
              </tr>

	    </tbody>
        </table>

        
        <table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
              <td width="3%">#</td>
              <td width="17%"><strong>Description Of Part</strong></td>
              <td width="10%"><strong>Description Of Model</strong></td>
              <td width="10%"><strong>Qty</strong></td>
              <td width="10%"><strong>Price</strong></td>
              <td width="10%"><strong>Discount</strong></td>
			  <td width="10%"><strong>Taxable</strong></td>
              <?php if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
			  <td width="5%"><strong>SGST %</strong></td>
			  <td width="5%"><strong>SGST Amount</strong></td>
			  <td width="5%"><strong>CGST %</strong></td>
			  <td width="5%"><strong>CGST Amount</strong></td>
              <?php }else{?>
			  <td width="10%"><strong>IGST %</strong></td>
			  <td width="10%"><strong>IGST Amount</strong></td>
              <?php }?>
			  <td width="10%"><strong>Amount</strong></td>
              </tr>
				</thead>
          <tbody>
            <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			$repl_podata_sql="SELECT * FROM billing_product_items where challan_no='".$job_row['del_dc_no']."'";
			$repl_podata_res=mysqli_query($link1,$repl_podata_sql);
			while($repl_podata_row=mysqli_fetch_assoc($repl_podata_res)){
				$val = $repl_podata_row['qty']*$repl_podata_row['price'];
                $taxable = $val-$repl_podata_row['discount_amt']*$repl_podata_row['qty'];

				        $repl_product_name = explode("~", getAnyDetails($repl_podata_row['partcode'],"part_name,product_id,brand_id,hsn_code,vendor_partcode","partcode","partcode_master",$link1)); 

                $repl_model_name = explode("~", getAnyDetails($repl_podata_row['model_id'],"model,partcode,model_id","model_id","model_master",$link1)); 
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$repl_product_name[0].' | '.$repl_podata_row['partcode'];?></td>
                <td><?=$repl_model_name[0].' | '.$repl_model_name[2].' | '.$repl_model_name[1];?></td>
                <td align="right"><?=$repl_podata_row['qty']?></td>
                <td align="right"><?=currencyFormat($repl_podata_row['price'])?></td>
                <td align="right"><?=currencyFormat($repl_podata_row['discount_amt'])?></td>    
				<td align="right"><?=currencyFormat($taxable)?></td>
                <?php if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
				<td align="right"><?=$repl_podata_row['sgst_per']?></td>
				<td align="right"><?=currencyFormat($repl_podata_row['sgst_amt'])?></td>
				<td align="right"><?=$repl_podata_row['cgst_per']?></td>
				<td align="right"><?=currencyFormat($repl_podata_row['cgst_amt'])?></td>
                <?php }else{?>
				<td align="right"><?=$repl_podata_row['igst_per']?></td>
				<td align="right"><?=currencyFormat($repl_podata_row['igst_amt'])?></td>
                <?php }?>
				<td align="right"><?=currencyFormat($repl_podata_row['item_total'])?></td>       
            </tr>
            <?php
			$total+=$repl_podata_row['qty'];
			$price+=$repl_podata_row['price'];
			$value+=$repl_podata_row['item_total'];                                                
			$discount = $repl_podata_row['discount'];
			$i++;
			}
			if($po_row['to_stateid']==$po_row['from_stateid']){ $colspn=11; }else{ $colspn=9;}
			?>   
            	<tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Sub Total</strong></td>
                    <td align="right"><?php echo currencyFormat($value); ?></td>
                </tr>
                <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Round Off</strong></td>
                    <td align="right"><?php echo currencyFormat($po_row['round_off']); ?></td>
                </tr>
                <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Total Amount</strong></td>
                    <td align="right"><?php echo currencyFormat($value+$po_row['round_off']); ?></td>
                </tr>
                <tr>
                  <td colspan="<?=$colspn+1?>"><strong>Amount in Words: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-inr" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo number_to_words($value+$po_row['round_off']) . " Only"; ?></td>
                </tr>
                <tr>
                  <td colspan="<?=$colspn+1?>"><strong> Billing Remark: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<?=$po_row['billing_rmk']?></td>
                  
                </tr> 
                <!----
                <tr>
                   <td colspan="<?=$colspn+1?>"><strong> Receive  Remark: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<?=$po_row['rcv_rmk']?></td>
                </tr>---->
			 </tbody>
        </table>




        
		<table class="table">
           <tbody>         
              <tr>          
                <td colspan="4" align="center" style="vertical-align:bottom;border-bottom:none;border-right:none" height="100"><?php  echo "____________________________"?></td>
                <!---<td colspan="3" align="center" style="vertical-align:bottom;border-bottom:none;border-right:none;border-left:none" height="50"><?php  echo "____________________________"?></td>--->
                <td colspan="4" align="center" style="vertical-align:bottom;border-bottom:none;border-left:none" height="100"><?php  echo "____________________________"?></td>
              </tr>
              <tr>        
                <td colspan="4" style="border-top:none;border-right:none" align="center">(Sender's Singature with Seal and Date)</td>
                <!---<td colspan="3" style="border-top:none;border-right:none;border-left:none" align="center">(Authorized By)</td>--->
                <td colspan="4" style="border-top:none;border-left:none;" align="center">(Receiver's Singature with Seal and Date)</td>
              </tr>
              <!----
              <tr>
                <td style="border-right:none"><strong>Date & Time</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo "____________________________"?></td>
                <td colspan="7" style="vertical-align:bottom;border-left:none">&nbsp;</td>
              </tr> ---->  
                     
          </tbody>
   	  </table>

       <table class="table" border="1">
           <tbody>         
              <tr>
                  <td colspan="8" height="30" style="background-color: gainsboro;" ><strong> Declaration: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<strong>We certify that, This is a replacement Delivery Challan. The defective battery approved in warranty claim is being eplaced to customer through this Delivery challan hence there is no commercial transaction involved.</strong></td>
                  
              </tr>           
          </tbody>
   	  </table>

       <?php *************************************************************************************/ ?>

    </page>
</body>
</html>