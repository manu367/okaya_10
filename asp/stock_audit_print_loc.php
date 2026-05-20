<?php
require_once("../includes/config.php");
$brandarray = getBrandArray($link1);
$productarray = getProductArray($link1);
$docid=base64_decode($_REQUEST['refno']);
//// stock audit details
$sa_sql = "SELECT * FROM stock_audit_master WHERE ref_no = '".$docid."'";
$sa_res = mysqli_query($link1,$sa_sql)or die (mysqli_error($link1));
$sa_row = mysqli_fetch_assoc($sa_res);
///////
$location_info = getLocationDispAddress($sa_row['location_code'],$link1);
?>
<!DOCTYPE>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Stock Audit Print</title>
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
        <div align="center" class="lable"><u><strong>Stock Audit</strong></u></div>
        <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong>Reference No.</strong></td>
                <td width="35%" colspan="2"><?=$docid?></td>
                <td width="15%" colspan="2"><strong>Stock Taken Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($sa_row['audit_date'])?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;ENTRY DETAIL</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong>Audit Location</strong></td>
                <td colspan="2"><?=getAnyDetails($sa_row["location_code"],"locationname","location_code","location_master",$link1)." (".$sa_row["location_code"].")";?></td>
				<td colspan="2"><strong>Entry Date</strong></td>
                <td colspan="2"><?=dt_format($sa_row['entry_date'])." ".$sa_row['entry_time'];?></td>                
              </tr>
              <tr>
			  <td colspan="2"><strong>Entry By</strong></td>
                <td colspan="2"><?php echo $entby = getAnyDetails($sa_row["entry_by"],"name","username","admin_users",$link1); if($entby==""){ echo $entby = getAnyDetails($sa_row["entry_by"],"locationname","location_code","location_master",$link1);};?></td>
                <td colspan="2">&nbsp;</td>
                <td colspan="2">&nbsp;</td>				
              </tr>              
              <tr>
                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>
              </tr>
			  </tbody>
        </table>
		<table class="table" border="1" style="margin-bottom: 0px;">
          <thead>
          	<tr>
              <td width="5%" rowspan="2">#</td>
              <td width="10%" rowspan="2"><strong>Partcode</strong></td>
              <td width="25%" rowspan="2"><strong>Part Description</strong></td>
              <td width="10%" rowspan="2"><strong>Product</strong></td>
              <td width="10%" rowspan="2"><strong>Brand</strong></td>
              <td colspan="2" align="center"><strong>CRM</strong></td>
       	      <td colspan="2" align="center"><strong>Physical</strong></td>
       	    </tr>
          	<tr>
              <td width="10%"><strong>OK Qty</strong></td>
              <td width="10%"><strong>Faulty Qty</strong></td>
              <td width="10%"><strong>OK Qty</strong></td>
              <td width="10%"><strong>Faulty Qty</strong></td>
          	</tr>
		  </thead>
          <tbody>
            <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			$res_invt = mysqli_query($link1,"SELECT * FROM stock_audit WHERE ref_no='".$docid."'")or die (mysqli_error($link1));
            while($row_invt = mysqli_fetch_assoc($res_invt)){
			?>
            <tr>
                <td><?=$i;?></td>
                <td><?=$row_invt["partcode"];?></td>
                <td><?=getAnyDetails($row_invt["partcode"],"part_name","partcode","partcode_master",$link1);?></td>
                <td><?=$productarray[$row_invt["product_id"]];?></td>
                <td><?=$brandarray[$row_invt["brand_id"]];?></td>
                <td align="right"><?=$row_invt["crm_okqty"];?></td>
                <td align="right"><?=$row_invt["crm_faultyqty"];?></td>
                <td align="right"><?=$row_invt["audit_okqty"];?></td>
                <td align="right"><?=$row_invt["audit_faultyqty"];?></td>     
            </tr>
            <?php
				$tot_crmok += $row_invt["crm_okqty"];
				$tot_crmflt += $row_invt["crm_faultyqty"];
				$tot_phyok += $row_invt["audit_okqty"];
				$tot_phyflt += $row_invt["audit_faultyqty"];
				$i++;
			}
			?>  
            <tr>
                <td colspan="5" align="right"><strong>Total Qty</strong></td>
                <td align="right"><strong><?php echo  $tot_crmok;?></strong></td>
                <td align="right"><strong><?php echo  $tot_crmflt;?></strong></td>
                <td align="right"><strong><?php echo  $tot_phyok;?></strong></td>
                <td align="right"><strong><?php echo  $tot_phyflt;?></strong></td>
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