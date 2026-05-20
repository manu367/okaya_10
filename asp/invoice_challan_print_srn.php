<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['id']);
//// po details from master table
$po_sql = "SELECT * FROM billing_master where challan_no='" . $docid . "' and from_location='".$_SESSION['asc_code']."'";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);
////// get location details
$location_info_from = explode("~",getAnyDetails($po_row['from_location'],"locationname,locationaddress","location_code","location_master",$link1));
$location_info = getLocationDispAddress($po_row['from_location'],$link1);
$location_info_to = explode("~",getAnyDetails($po_row['to_location'],"locationname,locationaddress","location_code","location_master",$link1));
//////// get  state details///////////////////
$state_from = explode("~",getAnyDetails($po_row['from_stateid'],"state","stateid","state_master",$link1));
$state_to = explode("~",getAnyDetails($po_row['to_stateid'],"state","stateid","state_master",$link1));
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
        <div align="center" class="lable"><u><strong><?php if($po_row['document_type']=="DC"){ echo "Delivery Challan"; $str = "Challan";}else{ echo "Invoice";$str = "Invoice";}?></strong></u></div>
      <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong><?=$str?> No.</strong></td>
                <td width="35%" colspan="2"><?=$docid?></td>
                <td width="15%" colspan="2"><strong><?=$str?> Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($po_row['sale_date'])?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;<?=strtoupper($str)?> DETAILS</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong><?=$str?> From</strong></td>
                <td colspan="2"><?=$location_info_from['0']." (".$po_row['from_location'].")";?></td>
				<td colspan="2"><strong><?=$str?> To</strong></td>
                <td colspan="2"><?=$location_info_to['0']." (".$po_row['to_location'].")";?></td>                
              </tr>
              <tr>
			  <td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=$po_row['from_addrs'];?></td>
                <td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=$po_row['to_addrs'];?></td>				
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
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
	    </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
              <td width="3%">#</td>
              <td width="17%"><strong>Description Of Goods</strong></td>
              <td width="10%"><strong>HSN Code</strong></td>
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
			$podata_sql="SELECT * FROM billing_product_items where challan_no='".$docid."' and from_location='".$_SESSION['asc_code']."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$val = $podata_row['qty']*$podata_row['price'];
                $taxable = $val-$podata_row['discount_amt']*$podata_row['qty'];
				$product_name = explode("~", getAnyDetails($podata_row['partcode'],"part_name,product_id,brand_id,hsn_code,vendor_partcode","partcode","partcode_master",$link1)); 
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$product_name[0].' | '.$podata_row['partcode'].' | '.$product_name[4];?></td>
                <td><?=$product_name[3];?></td>
                <td><?=$podata_row['qty']?></td>
                <td><?=currencyFormat($podata_row['price'])?></td>
                <td><?=currencyFormat($podata_row['discount_amt'])?></td>    
				<td><?=currencyFormat($taxable)?></td>
                <?php if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
				<td><?=$podata_row['sgst_per']?></td>
				<td><?=currencyFormat($podata_row['sgst_amt'])?></td>
				<td><?=$podata_row['cgst_per']?></td>
				<td><?=currencyFormat($podata_row['cgst_amt'])?></td>
                <?php }else{?>
				<td><?=$podata_row['igst_per']?></td>
				<td><?=currencyFormat($podata_row['igst_amt'])?></td>
                <?php }?>
				<td><?=currencyFormat($podata_row['item_total'])?></td>       
                </tr>
            <?php
			$total+=$podata_row['qty'];
			$price+=$podata_row['price'];
			$value+=$podata_row['totalvalue'];                                                
			$discount = $podata_row['discount'];
			$i++;
			}
			if($po_row['to_stateid']==$po_row['from_stateid']){ $colspn=11; }else{ $colspn=9;}
			?>   
            	<tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Sub Total</strong></td>
                    <td><?php echo currencyFormat($value); ?></td>
                </tr>
                <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Round Off</strong></td>
                    <td><?php echo currencyFormat($po_row['round_off']); ?></td>
                </tr>
                <tr>
                	<td colspan="<?=$colspn?>" align="right"><strong>Total Amount</strong></td>
                    <td><?php echo currencyFormat($po_row['total_cost']); ?></td>
                </tr>
                <tr>
                  <td colspan="<?=$colspn+1?>"><strong>Amount in Words: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-inr" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo number_to_words($po_row['total_cost']) . " Only"; ?></td>
                </tr>
                <tr>
                  <td colspan="<?=$colspn+1?>"><strong>Remark: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<?=$po_row['billing_rmk']?></td>
                </tr>
			 </tbody>
        </table>
		<table class="table" border="1">
           <tbody>         
              <tr>          
                <td colspan="8" align="right" style="vertical-align:bottom;border-bottom:none" height="50"><?php  echo "____________________________"?></td>
              </tr>
              <tr>        
                <td colspan="8" style="border-top:none" align="right">(Location signature)</td>
              </tr>
              <tr>
                <td style="border-right:none"><strong>Date & Time</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php  echo "____________________________"?></td>
                <td colspan="7" style="vertical-align:bottom;border-left:none">&nbsp;</td>
              </tr>              
          </tbody>
   	  </table>
    </page>
</body>
</html>