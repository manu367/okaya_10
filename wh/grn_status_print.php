<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po details from master table

$po_sql="select * from grn_master where grn_no='".$docid."'";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Print GRN</title>
<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/printcss.css" rel="stylesheet">
<script src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/jquery-barcode.js"></script>
<script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$("#barcodeprint").barcode(
		'<?=$docid?>', // Value barcode (dependent on the type of barcode)
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
                	                  
                </td>
              </tr>
            </tbody>
    	</table>
        <div align="center" class="lable"><u><strong>GRN</strong></u></div>
        <table class="table" border="1">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong>GRN No.</strong></td>
                <td width="35%" colspan="2"><?=$docid?></td>
                <td width="15%" colspan="2"><strong>GRN Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($po_row['receive_date'])?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">SUPPLIER DETAIL</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong>Supplier Name</strong></td>
                <td colspan="2"><?= getAnyDetails($po_row['party_code'],"name","id","vendor_master",$link1);?></td>
				<td colspan="2"><strong>Supplier Address</strong></td>
                <td colspan="2"><?= getAnyDetails($po_row['party_code'],"address","id","vendor_master",$link1);?></td>                
              </tr>
                <tr>
                <td colspan="2"><strong>GST No</strong></td>
                <td colspan="2"><?= getAnyDetails($po_row['party_code'],"gst_no","id","vendor_master",$link1);?></td>
				               
              </tr>
                <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px"> DELIVERY DETAIL</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong> Name</strong></td>
                <td colspan="2"><?php echo getAnyDetails($po_row["location_code"],"locationname","location_code","location_master",$link1)."(".$po_row['location_code'].")";?></td>
				<td colspan="2"><strong> Address</strong></td>
                <td colspan="2"><?php echo getAnyDetails($po_row["location_code"],"locationaddress","location_code","location_master",$link1)."(".$po_row['location_code'].")";?></td>                
              </tr>
               <tr>
                <td colspan="2"><strong>GST No</strong></td>
                <td colspan="2"><?=getAnyDetails($po_row["location_code"],"gstno","location_code","location_master",$link1)."(".$po_row['location_code'].")";?></td>
				               
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
			  </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
			  <tr>
                <td width="5%">#</td>
                <td width="20%">Product</td>
                <td width="20%">Brand</td>
                <td width="20%">Model</td>
                <td width="15%">Part</td>
                <td width="5%" >Qty</td>
                 <td width="5%">Price</td>
                  <td width="5%" >Cost</td>
				  <td width="5%" >TAX Name</td>
				  <?php if($po_row['total_igst_amt'] == '0.00'){?>
				<td width="5%" >CGST %</td>
				<td width="5%" >CGST Amt</td>
				<td width="5%" >SGST %</td>
				<td width="5%" >SGST Amt</td>
				<?php } else {?>
				<td width="5%" >IGST %</td>
				<td width="5%" >IGST Amt</td>
				<?php }?>
				<td width="5%" >Total Cost</td>
                </tr>
				</thead>
          <tbody>
            <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			$podata_sql="select * from grn_data where grn_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getAnyDetails($podata_row['product_id'],"product_name","product_id","product_master",$link1));
				$brand=explode("~",getAnyDetails($podata_row['brand_id'],"brand","brand_id","brand_master",$link1));
				$model=explode("~",getAnyDetails($podata_row['model_id'],"model","model_id","model_master",$link1));
				$part=explode("~",getAnyDetails($podata_row['partcode'],"part_name","partcode","partcode_master",$link1));
				$part_vender=explode("~",getAnyDetails($podata_row['partcode'],"vendor_partcode","partcode","partcode_master",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet['0'];?></td>
                <td><?=$brand['0'];?></td>
                <td><?=$model['0']?></td>
                 <td><?=$part['0']."  | ".$part_vender['0']." | ".$podata_row['partcode']?></td>
                <td><?=$podata_row['shipped_qty']?></td>             
                      <td><?=$podata_row['price']?></td>  
                      <td ><?php echo $podata_row['sub_total']?></td>  
					   <td ><?php echo $podata_row['tax_name']?></td>
					    <?php if($po_row['total_igst_amt'] == '0.00'){?>
					    <td ><?php echo $podata_row['cgst_per']?></td>
						 <td ><?php echo $podata_row['cgst_amt']?></td>
						 <td ><?php echo $podata_row['sgst_per']?></td>
						 <td ><?php echo $podata_row['sgst_amt']?></td>
						 <?php } else {?>
						 <td ><?php echo $podata_row['igst_per']?></td>
						 <td ><?php echo $podata_row['igst_amt']?></td>
						 <?php }?>
				 <td ><?php echo $podata_row['amount']?></td>
                </tr>
            <?php
			$total+= $podata_row['amount'];
			$total_row+=$podata_row['shipped_qty'];
			$i++;
			}
			?>   
            
            <tr><td colspan="5" align="right">Total</td> 
            <td><?=$total_row;?></td> 
        <?php if($po_row['total_igst_amt'] == '0.00'){?> <td colspan="7"></td>
             <td ><?=$total;?></td> <?php } else {?><td colspan="5"></td>
             <td ><?=$total;?></td> <?php }?></tr>
			 </tbody>
        </table>
		<table class="table" border="1">
           <tbody>  
	     
              <tr>          
                <td colspan="8" align="right" style="vertical-align:bottom;border-bottom:none"><BR><BR><BR><?php  echo "____________________________"?></td>
              </tr>
              <tr>        
                <td colspan="8" style="border-top:none" align="right">(Authorized signature)</td>
              </tr>
              <tr>
                <td style="border-right:none"><strong>Date & Time</strong></td>
                <td colspan="8" style="vertical-align:bottom;border-left:none"><?php  echo "____________________________"?></td>
              </tr>              
            </tbody>
    	</table>
    </page>
</body>
</html>