<?php

require_once("../includes/config.php");

//$docid=$_REQUEST['refid'];

//// po details from master table

$po_sql = "SELECT * FROM supplier_po_master where system_ref_no='".$docid."'";

$po_res = mysqli_query($link1,$po_sql);

$po_row = mysqli_fetch_assoc($po_res);

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

        <div align="center" class="lable"><u><strong>PO to Vendor</strong></u></div>

        <table class="table" border="1" style="margin-bottom:0px;">

            <tbody>

              <tr>

                <td width="15%" colspan="2"><strong>PO No.</strong></td>

                <td width="35%" colspan="2"><?=$docid?></td>

                <td width="15%" colspan="2"><strong>PO Date</strong></td>

                <td width="35%" colspan="2"><?=dt_format($po_row['entry_date'])?></td>

              </tr>

              <tr>

                <td colspan="8" align="left"><i class="fa fa-id-card fa-lg"></i><strong style="font-size:14px">&nbsp;PO DETAIL</strong></td>

              </tr>

              <tr>

                <td colspan="2"><strong>Supplier Name</strong></td>

                <td colspan="2"><?= getAnyDetails($po_row['party_name'],"name","id","vendor_master",$link1);?></td>

				<td colspan="2"><strong>Generate From</strong></td>

                <td colspan="2"><?= getAnyDetails($po_row['location_code'],"locationname","location_code","location_master",$link1);?></td>                

              </tr>

              <tr>

			  <td colspan="2"><strong>Bill To</strong></td>

                <td colspan="2"><?= getAnyDetails($po_row['bill_to'],"locationname","location_code","location_master",$link1);?></td>

                <td colspan="2"><strong>Delivery Address</strong></td>

                <td colspan="2"><?=$po_row['bill_address'];?></td>				

              </tr>  
               <tr>

			  <td colspan="2"><strong>Ship To</strong></td>

                <td colspan="2"><?= getAnyDetails($po_row['comp_code'],"locationname","location_code","location_master",$link1);?></td>

                <td colspan="2"><strong>Shiping Address</strong></td>

                <td colspan="2"><?=$po_row['ship_address2'];?></td>				

              </tr>  

                 

              

              <tr>

                <td colspan="8" align="left"><i class="fa fa-desktop fa-lg"></i><strong style="font-size:14px"> PRODUCT DETAIL</strong></td>

              </tr>

			  </tbody>

        </table>

		<table class="table" border="1" style="margin-bottom: 0px;">

          <thead>

          	<tr>
              <td width="3%"><strong>#</strong></td>
              <td width="10%"><strong>Product</strong></td>
              <td width="8%"><strong>Brand</strong></td>
              <td width="10%"><strong>Model</strong></td>
              <td width="15%"><strong>Partcode</strong></td>
              <td width="8%"><strong>Qty</strong></td>
              <td width="8%"><strong>Price</strong></td>
              <td width="10%"><strong>SubTotal</strong></td>
              <td width="10%"><strong>Tax</strong></td>
              <td width="8%"><strong>Tax Amt</strong></td>
              <td width="10%"><strong>Total Amt</strong></td>

			

              </tr>

				</thead>

          <tbody>

            <?php

			$i=1;

			/////////////////////////// fetching data from data table /////////////////////////////////////////////////////////////////////////

			$podata_sql="SELECT * FROM supplier_po_data where system_ref_no='".$docid."'";

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
                <td><?=$part['0']." (".$part_vender['0'].")"?></td>
                <td align="right"><?=$podata_row['qty']?></td>
                <td align="right"><?=$podata_row['price']?></td>
                <td align="right"><?=$podata_row['cost']?></td>
                <td align="left"><?=$podata_row['tax_name']." ".$podata_row['item_tax'];?></td>
                <td align="right"><?=$podata_row['tax_cost']?></td>
                <td align="right"><?=$podata_row['total_cost']?></td>    
                </tr>
            <?php
			$tot_qty+=$podata_row['qty'];
			$tot_sub+=$podata_row['cost'];
			$tot_tax+=$podata_row['tax_cost'];
			$tot_grand+=$podata_row['total_cost'];
			$i++;
			}
			?>
            <tr align="right">
                <td colspan="5"><strong>Total</strong></td>
                <td align="right"><strong><?=$tot_qty?></strong></td>
                <td align="right">&nbsp;</td>
                <td align="right"><strong><?=currencyFormat($tot_sub)?></strong></td>
                <td align="right">&nbsp;</td>
                <td align="right"><strong><?=currencyFormat($tot_tax)?></strong></td>
                <td align="right"><strong><?=currencyFormat($tot_grand)?></strong></td>
              </tr>   

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