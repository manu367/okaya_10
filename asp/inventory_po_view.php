<?php
require_once("../includes/config.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM po_master where po_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);

$locinfo= mysqli_fetch_array(mysqli_query($link1,"select * from location_master where location_code='".$job_row['to_code']."' "));
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i> PO View</h2>
      <h4 align="center">PO No.- <?=$docid?></h4>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;PO Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Location Name</label></td>
                <td width="30%"><?php echo $locinfo['locationname'];?></td>
                <td width="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?php echo $locinfo['locationaddress'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">PO No.</label></td>
                <td><?php echo $job_row['po_no'];?></td>
                <td><label class="control-label">PO  Date.</label></td>
                <td><?php echo $job_row['po_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Status</label></td>
                <td><?php echo getdispatchstatus($job_row['status']);?></td>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($job_row["to_state"],"state","stateid","state_master",$link1);?></td>
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
              <tr>
                <th width="5%" >#</th>
                <th width="15%" >Product</th>
                <th width="15%" >Brand</th>
                <th width="13%" >Model</th>
                <th width="20%" >Partcode</th>
                <th width="7%" >Qty</th>
				<th width="10%" >Status</th>
				<th width="15%" >Processed Qty</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM po_items where po_no='".$job_row['po_no']."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getAnyDetails($podata_row['product_id'],"product_name","product_id","product_master",$link1));
				$brand=explode("~",getAnyDetails($podata_row['brand_id'],"brand","brand_id","brand_master",$link1));
				$model=explode("~",getAnyDetails($podata_row['model_id'],"model","model_id","model_master",$link1));
				$part=explode("~",getAnyDetails($podata_row['partcode'],"part_name","partcode","partcode_master",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0]?></td>
                <td><?=$brand[0]?></td>
                <td><?=$model[0]?></td>
                <td><?=$part[0]." (".$podata_row['partcode'].")"?></td>
                <td><?=$podata_row['qty']?></td>    
				<td><?=getdispatchstatus($podata_row['status'])?></td> 
				<td><?=$podata_row['processed_qty']?></td>       
                </tr>
            <?php
			$i++;
			}
			?>
			 <tr>
                <td width="100%" align="center" colspan="8">
                   <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='inventory_po.php?<?=$pagenav?>'">
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