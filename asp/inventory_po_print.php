<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po details from master table
$po_sql = "SELECT * FROM po_master where po_no='".$docid."'";
$po_res = mysqli_query($link1,$po_sql);
$po_row = mysqli_fetch_assoc($po_res);
////// get location details
$location_info_from = explode("~",getAnyDetails($po_row['from_code'],"locationname,locationaddress","location_code","location_master",$link1));
$location_info_to = explode("~",getAnyDetails($po_row['to_code'],"locationname,locationaddress","location_code","location_master",$link1));
$location_info = getLocationDispAddress($po_row['from_code'],$link1);
//////// get  state details///////////////////
$state_from = explode("~",getAnyDetails($po_row['from_state'],"state","stateid","state_master",$link1));
$state_to = explode("~",getAnyDetails($po_row['to_state'],"state","stateid","state_master",$link1));
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Purchase Order</title>
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
        <div align="center" class="lable"><u><strong>Purchase Order</strong></u></div>
        <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong>PO No.</strong></td>
                <td width="35%" colspan="2"><?=$docid?></td>
                <td width="15%" colspan="2"><strong>PO Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format2($po_row['po_date'])?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;PO DETAIL</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong>PO From</strong></td>
                <td colspan="2"><?=$location_info_from['0']." (".$po_row['from_code'].")";?></td>
				<td colspan="2"><strong>PO To</strong></td>
                <td colspan="2"><?=$location_info_to['0']." (".$po_row['to_code'].")";?></td>                
              </tr>
              <tr>
			  <td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=$location_info_from['1'];?></td>
                <td colspan="2"><strong>Address</strong></td>
                <td colspan="2"><?=$location_info_to['1'];?></td>				
              </tr>
              <tr>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=$state_from['0'];?></td>
                <td colspan="2"><strong>State</strong></td>
                <td colspan="2"><?=$state_to['0'];?></td>
              </tr>
              
              <tr>
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
			  </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
              <td width="5%">#</td>
              <td width="15%"><strong>Product</strong></td>
              <td width="15%"><strong>Brand</strong></td>
              <td width="20%"><strong>Model</strong></td>
              <td width="15%"><strong>Partcode</strong></td>
              <td width="10%"><strong>Qty</strong></td>
              <td width="15%"><strong>Price</strong></td>
              <td width="15%"><strong>Cost</strong></td>
			 
              </tr>
				</thead>
          <tbody>
            <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			$podata_sql="SELECT * FROM po_items where po_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			$tot_amt=0;
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getAnyDetails($podata_row['product_id'],"product_name","product_id","product_master",$link1));
				$brand=explode("~",getAnyDetails($podata_row['brand_id'],"brand","brand_id","brand_master",$link1));
				$model=explode("~",getAnyDetails($podata_row['model_id'],"model","model_id","model_master",$link1));
				$part=explode("~",getAnyDetails($podata_row['partcode'],"part_name,location_price","partcode","partcode_master",$link1));
				$part_vender=explode("~",getAnyDetails($podata_row['partcode'],"vendor_partcode","partcode","partcode_master",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet['0'];?></td>
                <td><?=$brand['0'];?></td>
                <td><?=$model['0']?></td>
                <td><?=$part['0']." (".$part_vender['0'].")"?></td>
                <td><?=$podata_row['qty']?></td> 
                <td><?=$part['1']?></td>  
                <td><?=$cost=$part['1']*$podata_row['qty'];
				$tot_amt+=$cost;?></td>     
				
                </tr>
            <?php
			$i++;
			}
			?>  
            <tr>
            <td colspan="7" align="right">Total Amount:</td>
            <td><?php echo  $tot_amt;?></td>
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