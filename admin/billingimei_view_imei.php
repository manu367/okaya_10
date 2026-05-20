<?php
require_once("../includes/config.php");
$docid=$_REQUEST['refid'];
	$todayDate=date("Y-m-d");
	$todayTime=date("H:i:s");
//// job details
$job_sql="SELECT * FROM billing_master where challan_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);

$status = getdispatchstatus($job_row[status]);
////// final submit form ////

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
    include("../includes/leftnav2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-arrows-alt"></i> IMEI Details</h2>
      <h4 align="center">Challan No.- <?=$docid?></h4>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
	  <?php if(mysqli_num_rows($job_res) >=1)
{ ?>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Location Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">From Location Name</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["from_location"],"locationname","location_code","location_master",$link1);?></td>
                <td width="20%"><label class="control-label">To Location Name</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["to_location"],"locationname","location_code","location_master",$link1);?></td>
              </tr>
              <tr>
                <td><label class="control-label">From Address</label></td>
                <td><?php echo $job_row['from_addrs'];?></td>
                <td><label class="control-label">To Address</label></td>
                <td><?php echo $job_row['to_addrs'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">From Gst No.</label></td>
                <td><?php echo  $job_row['from_gst_no'];?></td>
                <td><label class="control-label">To Gst No.</label></td>
                <td><?php echo  $job_row['to_gst_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Type</label></td>
                <td><?php echo $job_row['po_type'];?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $status;?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th width="4%" style="text-align:center">#</th>
                <th width="15%" style="text-align:center">Product</th>
                <th width="15%" style="text-align:center">Brand</th>
                <th width="15%" style="text-align:center">Model</th>
                <th width="20%" style="text-align:center">Part Name</th>
                <th width="8%" style="text-align:center">Qty</th>
                <th width="10%" style="text-align:center">Price</th>
                <th width="10%" style="text-align:center">Amount</th>
			
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$totqty = 0;
			$totamt = 0.00;
			$podata_sql="SELECT * FROM billing_product_items where challan_no='".$job_row['challan_no']."'";
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
                <td align="right"><?=$podata_row['qty']?></td>
                <td align="right"><?=currencyFormat($podata_row['price'])?></td>
                <td align="right"><?=currencyFormat($podata_row['value'])?></td>    
                </tr>
            <?php
			$totqty+=$podata_row['okqty'];
			$totamt+=$podata_row['amount'];
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
	   <div align="center">
	  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='inventory_imei_details.php?<?=$pagenav?>'">
	  </div>
    </div><!--close panel-->
 
  </div><!--close panel group-->
  <?php  }  else {   
  //// grn details
  //// job details
$job_grn="SELECT * FROM  grn_master where grn_no='".$docid."'";
$grn_res=mysqli_query($link1,$job_grn);
$grn_detail=mysqli_fetch_assoc($grn_res);
$status_grn = getdispatchstatus($grn_detail[status]);  ?>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Location Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label"> Location Name</label></td>
                <td width="30%"><?php echo getAnyDetails($grn_detail["location_code"],"locationname","location_code","location_master",$link1);?></td>
                <td width="20%"><label class="control-label">Party Name</label></td>
                <td width="30%"><?php echo getAnyDetails($grn_detail["party_code"],"name","id","vendor_master",$link1);?></td>
              </tr>
              <tr>
                <td><label class="control-label">GRN Type</label></td>
                <td><?php echo $grn_detail['grn_type'];?></td>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $status_grn;?></td>
              </tr>
              <tr>
                <td><label class="control-label">Cost</label></td>
                <td><?php echo  $grn_detail['cost'];?></td>
                <td><label class="control-label">Remark</label></td>
                <td><?php echo  $grn_detail['remark'];?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th width="4%" style="text-align:center">#</th>
                <th width="15%" style="text-align:center">Product</th>
                <th width="15%" style="text-align:center">Brand</th>
                <th width="15%" style="text-align:center">Model</th>
                <th width="20%" style="text-align:center">Part Name</th>
                <th width="8%" style="text-align:center">Qty</th>
                <th width="10%" style="text-align:center">Price</th>
          
			
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$totqty = 0;
			$totamt = 0.00;
			$podata_sql="SELECT * FROM grn_data where grn_no='".$grn_detail['grn_no']."'";
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
             
                </tr>
            <?php
			
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
	   <div align="center">
	  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='inventory_imei_details.php?<?=$pagenav?>'">
	  </div>
    </div><!--close panel-->
 
  </div><!--close panel group-->
  
  
  
  <?php  }?>
	</form>
 </div><!--close col-sm-9-->
</div><!--close container-fluid-->
<?php
include("../includes/connection_close.php");
?>
</body>
</html>