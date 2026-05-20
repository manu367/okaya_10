<?php
require_once("../includes/config.php");

$docid=base64_decode($_REQUEST['refid']);
//// job details
 $job_sql="SELECT * FROM amc where amcid='".$docid."'";;
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
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
<script>
function bigImg(x) {
  x.style.height = "300px";
  x.style.width = "300px";
}

function normalImg(x) {
  x.style.height = "100px";
  x.style.width = "100px";
}
</script>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
<div class="row content">
<?php 
    include("../includes/leftnavemp2.php");
	$cust_det = explode("~",getAnyDetails($job_row['customer_id'],"landmark,email,phone,alt_mobile,pincode","customer_id","customer_master",$link1));
	$product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where serial_no='".$job_row['serial_no']."'"));
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-ticket"></i> AMC Details</h2>
      <h4 align="center">AMC No.- <?=$docid?></h4>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Customer Name</label></td>
                <td width="30%"><?php echo $job_row['customer_name'];?></td>
                <td width="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?php echo $job_row['addrs'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Contact No.</label><br/><span class="red_small">(For SMS Update)</span></td>
                <td><?php echo $job_row['contract_no'];?></td>
                <td><label class="control-label">Alternate Contact No.</label></td>
                <td><?php echo $cust_det[3];?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($job_row["state_id"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Email</label></td>
                <td><?php echo $cust_det[1];?></td>
              </tr>
              <tr>
                <td><label class="control-label">City</label></td>
                <td><?php echo getAnyDetails($job_row["city_id"],"city","cityid","city_master",$link1);?></td>
                <td><label class="control-label">Pincode</label></td>
                <td><?php echo $cust_det['4'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Customer Category</label></td>
                <td><?php echo $job_row['customer_type'];?></td>
                <td><label class="control-label">Residence No</label></td>
                <td><?php echo $cust_det[2];?></td>
              </tr>
			   <tr>
                <td><label class="control-label">Landmarks</label></td>
                <td><?php echo $cust_det[0];?></td>
                <td><label class="control-label"></label></td>
                <td><?php ?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
          <tbody>
            <tr>
              <td width="20%"><label class="control-label">Product</label></td>
              <td width="30%"><?php echo getAnyDetails($job_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
              <td width="20%"><label class="control-label">Brand</label></td>
              <td width="30%"><?php echo getAnyDetails($job_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
            </tr>
            <tr>
              <td><label class="control-label">Model</label></td>
              <td><?php echo getAnyDetails($job_row["model_id"],"model","model_id","model_master",$link1);?></td>
              <td><label class="control-label">AMC Type</label></td>
              <td><?=$job_row['amc_type']?></td>
            </tr>
            <tr>
              <td><label class="control-label"><?php echo SERIALNO ?></label></td>
              <td><?=$job_row['serial_no']?></td>
              <td><label class="control-label">&nbsp;</label></td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td width="20%"><label class="control-label">AMC Start Date</label></td>
              <td width="30%"><?=dt_format($job_row['amc_start_date']);?></td>
              <td><label class="control-label">AMC End Date</label></td>
              <td width="30%"><?=dt_format($job_row['amc_end_date']);?></td>
            </tr>
            <tr>
              <td><label class="control-label">Purchase Date</label></td>
              <td><?=dt_format($job_row['purchase_date'])?></td>
               <td><label class="control-label">AMC Duration(in Days)</label></td>
              <td><?=$job_row['amc_duration']?></td>
            </tr>
          
            <tr>
			 <td><label class="control-label">Entity Name</label></td>
              <td ><?php echo getAnyDetails($job_row['entity_type'],"name","id","entity_type",$link1);?></td>
              <td><label class="control-label">&nbsp;</label></td>
              <td>&nbsp;</td>
             
            </tr>
           
          </tbody>
        </table>
      </div>
      <!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Payment</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
        
                <tr>
          <td><label class="control-label">AMC Amount</label></td>
              <td><?php echo $job_row['amc_amount'];?></td>
			   <td><label class="control-label">Payment Mode</label></td>
              <td><?=$job_row['mode_of_payment']?></td>
           
            </tr>
			 <tr>
              <td><label class="control-label">Cheque number</label></td>
              <td><?=$job_row['cheque_no']?></td>
              <td><label class="control-label">Cheque Date</label></td>
              <td><?=dt_format($job_row['cheque_date'])?></td>
            </tr>
            <tr>
              <td><label class="control-label">CR/Transaction Number </label></td>
              <td><?=$job_row['cr_no']?></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          

            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	 </div><!--close panel-->
   
	

 
  <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;</div>
      <div class="panel-body">
     
		   <div class="form-group">
                    <div class="col-md-12" align="center">
					
					     <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='amc_list.php?<?=$pagenav?>&mobileno=<?=$job_row['contact_no']?>&customer_id=<?=$job_row['customer_id']?>&email_id=<?=$job_row['email']?>&imei_serial=<?=$job_row[' 	imei']?>'">
					  
					  <input type="hidden" id="locationcode" name="locationcode" value ="<?=$job_row['location_code']?>" >
					  <input type="hidden" id="status" name="status" value ="<?=$job_row['status']?>" >
					   <input type="hidden" id="warranty" name="warranty" value ="<?=$job_row['warranty_status']?>" >
                     <!--  <input type="submit" class="btn<?=$btncolor?>" name="savermk" id="savermk" value="Save" title="Save Remark Details" <?php if($_POST['savermk']=='Save'){?>disabled<?php }?>>-->&nbsp;
                    </div>
                  </div> 
      </div><!--close panel body-->
    </div><!--close panel-->
	</form>
 </div><!--close col-sm-9-->
</div><!--close container-fluid-->
<?php
include("../includes/connection_close.php");
?>
</body>
</html>