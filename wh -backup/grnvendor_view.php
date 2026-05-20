<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po to vendor details
$job_sql="SELECT * FROM supplier_po_master where system_ref_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);

?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-ship"></i> Purchase Order</h2>
      <h4 align="center">PO No.- <?=$docid?></h4>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;PO Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Supplier Name</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row['party_name'],"name","id","vendor_master",$link1);?></td>
                <td width="20%"><label class="control-label">Bill From</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row['location_code'],"locationname","location_code","location_master",$link1);?></td>
              </tr>
			   <tr>
                <td><label class="control-label">Bill To</label></td>
                <td><?php echo getAnyDetails($job_row['bill_to'],"locationname","location_code","location_master",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php  echo getdispatchstatus($job_row["status"]); ?></td>
              </tr>
              <tr>
                <td><label class="control-label">PO No.</label></td>
                <td><?php echo $job_row['system_ref_no'];?></td>
                <td><label class="control-label">PO  Date.</label></td>
                <td><?php echo dt_format($job_row['entry_date']);?></td>
              </tr>
             
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

  <div class="panel panel-info table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;PO Items Details</div>
         <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th width="3%" style="text-align:center">#</th>
                <th width="8%" style="text-align:center">Product</th>
                <th width="8%" style="text-align:center">Brand</th>
                <th width="10%" style="text-align:center">Model</th>
                <th width="14%" style="text-align:center">Partcode</th>
                <th width="7%" style="text-align:center">Qty</th>
                <th width="8%" style="text-align:center">Price</th>
                <th width="10%" style="text-align:center">SubTotal</th>
                <th width="8%" style="text-align:center">Tax</th>
				<?php if($job_row['total_igst_amt'] == '0.00'){?>
                <th width="6%" style="text-align:center">CGST %</th>
				<th width="6%" style="text-align:center">CGST Amt</th>
				<th width="6%" style="text-align:center">SGST %</th>
				<th width="6%" style="text-align:center">SGST Amt</th>
				<?php  } else {?>
				<th width="6%" style="text-align:center">IGST %</th>
				<th width="6%" style="text-align:center">IGST Amt</th>
				<?php }?>
                <th width="10%" style="text-align:center">Total Amt</th>
			
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM supplier_po_data where system_ref_no='".$job_row['system_ref_no']."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getAnyDetails($podata_row['product_id'],"product_name","product_id","product_master",$link1));
				$brand=explode("~",getAnyDetails($podata_row['brand_id'],"brand","brand_id","brand_master",$link1));
				$model=explode("~",getAnyDetails($podata_row['model_id'],"model","model_id","model_master",$link1));
				$part=explode("~",getAnyDetails($podata_row['partcode'],"part_name","partcode","partcode_master",$link1));
			?>
              <tr align="left">
                <td><?=$i?></td>
                <td><?=$proddet[0];?></td>
                <td><?=$brand[0];?></td>
                <td><?=$model[0];?></td>
                <td><?=$part[0];?></td>
                <td align="right"><?=$podata_row['qty']?></td>
                <td align="right"><?=$podata_row['price']?></td>
                <td align="right"><?=$podata_row['cost']?></td>
                <td><?=$podata_row['tax_name']?></td>
				<?php if($job_row['total_igst_amt'] == '0.00'){?>
                <td align="right"><?=$podata_row['cgst_per']?>%</td>
                <td align="right"><?=$podata_row['cgst_amt']?></td>
				 <td align="right"><?=$podata_row['sgst_per']?>%</td>
                <td align="right"><?=$podata_row['sgst_amt']?></td>
				<?php } else {?>
				 <td align="right"><?=$podata_row['igst_per']?>%</td>
                <td align="right"><?=$podata_row['igst_amt']?></td>
				<?php }?>
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
				<?php if($job_row['total_igst_amt'] == 0.00){?>
                <td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right"><strong><?php echo currencyFormat($job_row['total_cgst_amt'])?></strong></td>
                <td align="right">&nbsp;</td>
				<td align="right"><strong><?php echo currencyFormat($job_row['total_sgst_amt'])?></strong></td>
				<?php } else {?>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="right"><strong><?php echo currencyFormat($job_row['total_igst_amt'])?></strong></td>
				
				<?php }?>
                <td align="right"><strong><?=currencyFormat($tot_grand)?></strong></td>
              </tr>
			 <tr>
                <td align="center" colspan="14">
                   <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='grn_vendor.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>