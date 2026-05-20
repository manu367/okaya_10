<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po details from master table
$po_sql = "SELECT * FROM grn_master where grn_no='" . $docid . "' and location_code='".$_SESSION['asc_code']."'";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);
$bill_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM billing_master where challan_no='".$docid."'"));
////// get location details
$location_info_from = explode("~",getAnyDetails($po_row['party_code'],"name,vendor_orign","id","vendor_master",$link1));
$location_info_to = getAnyDetails($po_row['location_code'],"locationname","location_code","location_master",$link1);
$location_info = getLocationDispAddress($po_row['location_code'],$link1);
//////// get  state details///////////////////
$state_from = explode("~",getAnyDetails($bill_det['from_stateid'],"state","stateid","state_master",$link1));
$state_to = explode("~",getAnyDetails($bill_det['to_stateid'],"state","stateid","state_master",$link1));
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Print Local Purchase</title>
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
        <div align="center" class="lable"><u><strong><?php echo "Local Purchase";$str = "GRN";?></strong></u></div>
      <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong><?=$str?> No.</strong></td>
                <td width="35%" colspan="2"><?=$docid?></td>
                <td width="15%" colspan="2"><strong><?=$str?> Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($po_row['receive_date'])?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;<?=strtoupper($str)?> DETAILS</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong><?=$str?> From</strong></td>
                <td colspan="2"><?=$location_info_from['0']." (".$po_row['party_code'].")";?></td>
				<td colspan="2"><strong><?=$str?> To</strong></td>
                <td colspan="2"><?=$location_info_to;?></td>                
              </tr>
              <tr>
			  <td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=$bill_det['from_addrs']?></td>
                <td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=$bill_det['deliv_addrs']?></td>				
              </tr>
              <tr>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=$state_from['0'];?></td>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=$state_to['0'];?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>GST No.</strong></td>
                <td colspan="2"><?=$bill_det['from_gst_no']?></td>
                <td colspan="2"><strong>GST No.</strong></td>
                <td colspan="2"><?=$bill_det['to_gst_no']?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>Status</strong></td>
                <td colspan="2"><?=getdispatchstatus($po_row['status']);?></td>
                <td colspan="2"><strong></strong></td>
                <td colspan="2"></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
	    </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
              <td width="4%">#</td>
              <td width="15%"><strong>Product</strong></td>
              <td width="15%"><strong>Brand</strong></td>
              <td width="15%"><strong>Model</strong></td>
              <td width="25%"><strong>Part Name</strong></td>
              <td width="8%"><strong>Qty</strong></td>
			  <td width="8%"><strong>Price</strong></td>
			  <td width="10%"><strong>Amount</strong></td>
              </tr>
				</thead>
          <tbody>
            <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			$podata_sql="SELECT * FROM grn_data where grn_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){ 
				$proddet=explode("~",getAnyDetails($podata_row['product_id'],"product_name","product_id","product_master",$link1));
				$brand=explode("~",getAnyDetails($podata_row['brand_id'],"brand","brand_id","brand_master",$link1));
				$model=explode("~",getAnyDetails($podata_row['model_id'],"model","model_id","model_master",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0];?></td>
                <td><?=$brand[0];?></td>
                <td><?=$model[0];?></td>
                <td><?=$podata_row['part_name']." (".$podata_row['partcode'].")";?></td>
                <td align="right"><?=$podata_row['okqty']?></td>
                <td align="right"><?=currencyFormat($podata_row['price'])?></td>
                <td align="right"><?=currencyFormat($podata_row['amount'])?></td>   
              </tr>
            <?php
			$totqty+=$podata_row['okqty'];
			$totamt+=$podata_row['amount'];                                                
			$i++;
			}
			?>   
            	<tr>
                	<td colspan="5" align="right"><strong>Total</strong></td>
                	<td align="right"><?=$totqty?></td>
                	<td align="right">&nbsp;</td>
                	<td align="right"><?=currencyFormat($totamt)?></td>
                </tr>
                <tr>
                  <td colspan="8"><strong>Amount in Words: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-inr" aria-hidden="true"></i>&nbsp;&nbsp;<?php echo number_to_words($totamt) . " Only"; ?></td>
                </tr>
                <tr>
                  <td colspan="8"><strong>Remark: </strong>&nbsp;&nbsp;&nbsp;&nbsp;<?=$po_row['remark']?></td>
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