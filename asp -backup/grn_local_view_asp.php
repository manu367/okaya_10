<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// po to vendor details
$job_sql="SELECT * FROM grn_master where grn_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
$bill_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM billing_master where challan_no='".$docid."'"));
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
      <h2 align="center"><i class="fa fa-car"></i> Local GRN Details</h2>
      <h4 align="center">GRN No.- <?=$docid?></h4>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Party Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Supplier Name</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row['party_name'],"name","id","vendor_master",$link1);?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php  if($job_row['status'] == '4') { echo "Received";} else{ echo $job_row['status'];}?></td>
              </tr>
			   <tr>
                <td><label class="control-label">Bill To</label></td>
                <td><?php echo getAnyDetails($job_row['location_code'],"locationname","location_code","location_master",$link1);?></td>
                 <td width="20%"><label class="control-label">Ship To</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row['comp_code'],"locationname","location_code","location_master",$link1);?></td>
              </tr>
              <tr>
                <td><label class="control-label">GRN No.</label></td>
                <td><?php echo $job_row['grn_no'];?></td>
                <td><label class="control-label">GRN Date</label></td>
                <td><?php echo dt_format($job_row['receive_date']);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Supplier Address</label></td>
                <td><?=$bill_det['from_addrs']?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?=$job_row['remark']?></td>
              </tr>
              <tr>
                <td><label class="control-label">Billing Address</label></td>
                <td><?=$bill_det['to_addrs']?></td>
                <td><label class="control-label">Shipping Address</label></td>
                <td><?=$bill_det['deliv_addrs']?></td>
              </tr>
             
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

  <div class="panel panel-info table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;GRN Items Details</div>
         <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th width="4%" style="text-align:center">#</th>
                <th width="15%" style="text-align:center">Product</th>
                <th width="15%" style="text-align:center">Brand</th>
                <th width="15%" style="text-align:center">Model</th>
                <th width="25%" style="text-align:center">Part Name</th>
                <th width="8%" style="text-align:center">Qty</th>
                <th width="8%" style="text-align:center">Price</th>
                <th width="10%" style="text-align:center">Amount</th>
			
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$totqty = 0;
			$totamt = 0.00;
			$podata_sql="SELECT * FROM grn_data where grn_no='".$job_row['grn_no']."'";
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
                <td width="100%" align="center" colspan="10">
                   <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='grn_local_asp.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>