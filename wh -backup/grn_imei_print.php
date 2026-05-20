<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po details from master table
 $po_sql = "SELECT * FROM grn_master where grn_no='" . $docid . "' ";
$po_res = mysqli_query($link1, $po_sql);
$po_row = mysqli_fetch_assoc($po_res);

////// get location details
$location_info_from = explode("~",getAnyDetails($po_row['party_code'],"name","id","vendor_master",$link1));
$location_info_to = explode("~",getAnyDetails($po_row['location_code'],"locationname,locationaddress","location_code","location_master",$link1));
$location_info = getLocationDispAddress($po_row['location_code'],$link1);

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

        <div align="center" class="lable"><u><strong>Imei Details</strong></u></div>

      <table class="table" border="1" style="margin-bottom: 0px;">
            <tbody>
              <tr>
                <td width="15%" colspan="2"><strong>Grn  No.</strong></td>
                <td width="35%" colspan="2"><?=$docid?></td>
                <td width="15%" colspan="2"><strong>Grn  Date</strong></td>
                <td width="35%" colspan="2"><?=dt_format($po_row['receive_date'])?></td>
              </tr>
              <tr>
                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;<?=strtoupper($str)?> DETAILS</strong></td>
              </tr>
              <tr>
                <td colspan="2"><strong><?=$str?> From</strong></td>
                <td colspan="2"><?=$location_info_from['0']." (".$po_row['party_code'].")";?></td>
				<td colspan="2"><strong><?=$str?> To</strong></td>
                <td colspan="2"><?=$location_info_to['0']." (".$po_row['location_code'].")";?></td>                
              </tr>
              <tr>
			  <td colspan="2"><strong>Status</strong></td>
                <td colspan="2"><?=getDispatchStatus($po_row['status']);?></td>
                <td colspan="2"><strong>Remark</strong></td>
                <td colspan="2"><?=$po_row['remark'];?></td>				
              </tr> 
			  <tr>
			  <td colspan="2"><strong>Grn Type</strong></td>
                <td colspan="2"><?=$po_row['grn_type'];?></td>
                <td colspan="2"></td>
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
              <td width="3%">#</td>
              <td width="17%"><strong>Description Of Goods</strong></td>
                <td width="17%"><strong>IMEI 1</strong></td>
                <td width="17%"><strong>IMEI 2</strong></td>
              </tr>
				</thead>
          <tbody>
            <?php
			$i=1;
			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////
			$podata_sql="SELECT * FROM imei_details where  grn_no='".$docid."'";
			$podata_res=mysqli_query($link1,$podata_sql);

			while($podata_row=mysqli_fetch_assoc($podata_res)){
					$product_name = explode("~", getAnyDetails($podata_row['partcode'],"part_name,product_id,brand_id,hsn_code,vendor_partcode","partcode","partcode_master",$link1)); 				
				?>
              <tr>
               <td><?=$i?></td>
                <td><?=$product_name[0].' | '.$podata_row['partcode'].' | '.$product_name[4];?></td>
                <td><?=$podata_row['imei1'];?></td>
               <td><?=$podata_row['imei2'];?></td>
                </tr>
            <?php
			$total+=$podata_row['qty'];
			$price+=$podata_row['price'];
			$value+=$podata_row['totalvalue'];                                                
			$discount = $podata_row['discount'];
			$i++;
			}
			if($po_row['to_stateid']==$po_row['from_stateid']){ $colspn=13; }else{ $colspn=9;}
			?>   
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