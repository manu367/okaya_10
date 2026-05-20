<?php
require_once("../includes/config.php");
$today=date("Y-m-d");
$jobno = base64_decode($_REQUEST['refid']);
//// pNA details
$job_sql="SELECT * FROM po_items where job_no='".$jobno."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
$po_master  = mysqli_fetch_array(mysqli_query($link1,"select * from po_master where po_no = '".$job_row['po_no']."' "));
@extract($_POST);
if($_POST){
  if($_POST[Submit]=='Cancel'){

	mysqli_autocommit($link1, false);
	$flag = true;
	//////////////////////////////////////////////// update status in auto part request//////////////////////////////////
	$result = mysqli_query($link1,"update auto_part_request set  cancel_date  = '".$today."' ,remark ='Cancelled'   where  	job_no = '".$jobno."' ");
//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }	
		//////////////////////////////////////////////// update status in  po_master//////////////////////////////////
	$result1 = mysqli_query($link1,"update po_master set  status = '5' ,cancel_date  = '".$today."' ,cancel_rmk ='".$remark."' ,cancel_by = '".$_SESSION['userid']. "'   where  po_no = '".$job_row['po_no']."' ");
//// check if query is not executed
	if (!$result1) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }
	
	/////////////////// update status in po_items///////////////////////////////////////////////////
	$result2 = mysqli_query($link1,"update po_items set  status = '5' ,cancel_date  = '".$today."' ,cancel_rmk ='".$remark."'  ,cancel_by = '".$_SESSION['userid']."' where  po_no = '".$job_row['po_no']."' ");
//// check if query is not executed
	if (!$result2) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
    }
	
	
}	
if ($flag) {
        mysqli_commit($link1);
        $msg = "Successfully PNA Cancelled";
    } else {
		mysqli_rollback($link1);
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	   ///// move to parent page
 header("location:inventory_pna_bucket.php?msg=".$msg."".$pagenav);
 exit;
 }
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
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Cancel PNA</h2>
      <h4 align="center">PNA No.- <?=$job_row['po_no']?></h4>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">  
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;PNA Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Location Name</label></td>
                <td width="30%"><?=getAnyDetails($po_master["from_code"],"locationname","location_code","location_master",$link1);?></td>
                <td width="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?= $po_master['from_address'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">PNA No.</label></td>
                <td><?php echo $job_row['po_no'];?></td>
                <td><label class="control-label">PNA  Date.</label></td>
                <td><?php echo $po_master['po_date'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Status</label></td>
                <td><?php echo $job_row['status'];?></td>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($po_master["from_state"],"state","stateid","state_master",$link1);?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->

  <div class="panel panel-info table-responsive">
     <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;PNA Items Details</div>
         <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
              <tr>
                <th width="5%" style="text-align:center">#</th>
                <th width="20%" style="text-align:center">Product</th>
                <th width="20%" style="text-align:center">Brand</th>
                <th width="15%" style="text-align:center">Model</th>
                <th width="15%" style="text-align:center">Partcode</th>
                <th width="7%" style="text-align:center">Qty</th>
				<th width="12%" style="text-align:center">Status</th>
                </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$podata_sql="SELECT * FROM po_items where po_no='".$job_row[po_no]."'";
			$podata_res=mysqli_query($link1,$podata_sql);
			while($podata_row=mysqli_fetch_assoc($podata_res)){
				$proddet=explode("~",getAnyDetails($podata_row[product_id],"product_name","product_id","product_master",$link1));
				$brand=explode("~",getAnyDetails($podata_row[brand_id],"brand","brand_id","brand_master",$link1));
				$model=explode("~",getAnyDetails($podata_row[model_id],"model","model_id","model_master",$link1));
				$part=explode("~",getAnyDetails($podata_row[partcode],"part_name","partcode","partcode_master",$link1));
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=$proddet[0];?></td>
                <td><?=$brand[0];?></td>
                <td><?=$model[0];?></td>
                <td><?=$part[0];?></td>
                <td><?=$podata_row['qty'];?></td>    
				<td><?=$podata_row['status'];?></td>       
                </tr>
            <?php
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	<div class="panel panel-info table-responsive">
       <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Cancel Reason</div>
      <div class="panel-body">
       
        <table class="table table-bordered" width="100%">
            <tbody>
              
              <tr>
                <td><label class="control-label">Cancel Remark <span class="red_small">*</span></label></td>
                <td><textarea name="remark" id="remark" required class="required form-control" onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;width:300px;"></textarea></td>
              </tr>
              <tr>
                <td colspan="2" align="center">
                  <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Cancel" title="" <?php if($_POST['Submit']=='Cancel'){?>disabled<?php }?>>&nbsp;
                  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='inventory_pna_bucket.php?<?=$pagenav?>'">
                  </td>
                </tr>
            </tbody>
          </table>
         
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
	</form>
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>