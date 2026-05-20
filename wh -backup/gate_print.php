<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po details from master table
$po_sql = "SELECT * FROM gate_entry_detail where po_no='".$docid."'";
$po_res = mysqli_query($link1,$po_sql);
$po_row = mysqli_fetch_assoc($po_res);

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
                	                  
                </td>
              </tr>
            </tbody>
    	</table>
        <div align="center" class="lable"><u><strong>Gate Entry Details</strong></u></div>
        <table class="table" border="1">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong>PO No.</strong></td>
                <td width="35%" colspan="2"><?=$docid?></td>
                <td width="15%" colspan="2"><strong>Request No</strong></td>
                <td width="35%" colspan="2"><?=$po_row['request_no']?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;DETAILS</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong>WH</strong></td>
                <td colspan="2"><?=getAnyDetails($po_row['location_code'],"locationname","location_code","location_master",$link1);?></td>
				<td colspan="2"><strong>Supplier Name</strong></td>
                <td colspan="2"><?=getAnyDetails($po_row['from_party_name'],"name","id","vendor_master",$link1);?></td>                
              </tr>
              <tr>
			  <td colspan="2"><strong>Vehcile No.</strong></td>
                <td colspan="2"><?=$po_row['vehicle_no'];?></td>
                <td colspan="2"><strong>Contact Person</strong></td>
                <td colspan="2"><?=$po_row['contact_person'];?></td>				
              </tr>
              <tr>
                <td colspan="2"><strong>Status</strong></td>
                <td colspan="2"><?=getdispatchstatus($po_row['entry_status']);?></td>
                <td colspan="2"><strong>Entry Date</strong></td>
                <td colspan="2"><?=dt_format($po_row['entry_date']);?></td>
              </tr>
              <tr>
                <td colspan="2"><strong>No. of boxes</strong></td>
                <td colspan="2"><?=$po_row['box_no'];?></td>
                <td colspan="2"><strong>No. of Invoice</strong></td>
                <td colspan="2"><?=$po_row['inv_nos'];?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> Shipment Detail</strong></td>
              </tr>
			  </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
			  <tr>
                <td width="5%">#</td>
                <td width="20%" align="center">Qty</td>
                <td width="20%" align="center">UOM</td>            
                </tr>
				</thead>
          <tbody>
            <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			$podata_sql="SELECT * FROM gate_entry_oth where  gen_id='".$po_row['sno']."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td align="center"><?=$podata_row['ship_qty'];?></td>
                <td align="center"><?=$podata_row['uom'];?></td>             
                </tr>
            <?php
			$i++;
			}
			?>   
			 </tbody>
        </table>
		<table class="table" border="1">
           <tbody>         
              <tr>          
                <td colspan="8" align="right" style="vertical-align:bottom;border-bottom:none"><?php  echo "____________________________"?></td>
              </tr>
              <tr>        
                <td colspan="8" style="border-top:none" align="right">(Location signature)</td>
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