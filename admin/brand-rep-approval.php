<?php
require_once("../includes/config.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
$doa_sql="SELECT * FROM doa_data where job_no='".$docid."'";
$doa_res=mysqli_query($link1,$doa_sql);
$doa_row=mysqli_fetch_assoc($doa_res);

@extract($_POST);
////// if we hit process button
if ($_POST) {
    if ($_POST['update'] == 'Update') {
		mysqli_autocommit($link1, false);
            $flag = true;
            $err_msg = "";
			/////update jobsheet data  table
				if($status=='51'){
					$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set sub_status  = '".$status."' ,doa_remark = '".$remark."',doa_approval= 'Y',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."'  where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"Request Approved","Request Approved",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
				}else{
					$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set status  = '1',sub_status  = '1' ,doa_remark = '".$remark."',doa_approval= 'N' ,doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."' where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"Request Reject - call Re-open","Request Reject",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
					}
			/// check if query is execute or not//
					if(!$jobsheet_upd){
						$flag = false;
						$err_msg = "Error1". mysqli_error($link1) . ".";
					}	
	
		///////////////////////// entry in call history table ///////////////////////////////////////	
		
				
					
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		////// insert in activity table////
       $flag = dailyActivity($_SESSION['userid'], $docid, $status,$remark,$_SERVER['REMOTE_ADDR'], $link1, $flag);
		
		  ///// check both master and data query are successfully executed
                    if ($flag) {
                        mysqli_commit($link1);
                        $msg = "Successfully done with ref. no. " . $docid;
						$cflag = "success";
						$cmsg = "Success";
                    } else {
                        mysqli_rollback($link1);
                        $msg = "Request could not be processed " . $err_msg . ". Please try again.";
						$cflag = "danger";
						$cmsg = "Failed";
                    }
                    mysqli_close($link1);
					 ///// move to parent page
        header("location:job_brand_appr.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
       exit;
	}	               
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
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-list-alt"></i> Job View</h2>
      <h4 align="center">Job No.- <?=$docid?></h4>
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
                <td width="30%"><?php echo $job_row['address'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Contact No.</label><br/><span class="red_small">(For SMS Update)</span></td>
                <td><?php echo $job_row['contact_no'];?></td>
                <td><label class="control-label">Alternate Contact No.</label></td>
                <td><?php echo $job_row['alternate_no'];?></td>
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
                <td><?php echo $job_row['pincode'];?></td>
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
              <td><?=$job_row['model']?></td>
              <td><label class="control-label">Date Of Installation</label></td>
              <td><?=dt_format($product_det['installation_date'])?></td>
            </tr>
            <tr>
              <td><label class="control-label"><?php echo SERIALNO ?></label></td>
              <td><?=$job_row['imei']?></td>
              <td><label class="control-label">Escalations From</label></td>
              <td><?=$job_row['call_type']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Warranty Status</label></td>
              <td><?=$job_row['warranty_status']?></td>
              <td><label class="control-label">Job For</label></td>
              <td><?=$job_row['call_for']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Purchase Date</label></td>
              <td><?=dt_format($job_row['dop'])?></td>
              <td><label class="control-label">Warranty End Date</label></td>
              <td><?=dt_format($product_det['warranty_end_date'])?></td>
            </tr>
			 <tr>
			  <td><label class="control-label">AMC Number</label></td>
              <td><?=$product_det['amc_no']?></td>
              <td><label class="control-label">AMC Expiry Date </label></td>
              <td ><?=dt_format($product_det['amc_end_date'])?></td>
              
            </tr>
			 <tr>
			  <td><label class="control-label">Date Of Installation</label></td>
              <td><?=dt_format($product_det['installation_date'])?></td>
              <td><label class="control-label">Entity Name</label></td>
              <td ><?php echo getAnyDetails($job_row['entity_type'],"name","id","entity_type",$link1);?></td>
              
            </tr>
			 <tr>
              <td><label class="control-label">Dealer Name</label></td>
              <td><?=$job_row['dname']?></td>
              <td><label class="control-label">Invoice No</label></td>
              <td><?=$job_row['inv_no']?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    
    
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>
        
                <tr>
              <td width="26%"><label class="control-label">Assign Location</label></td>
              <td  colspan="3"><?php echo getAnyDetails($job_row["current_location"],"locationname","location_code","location_master",$link1);?></td>
           
            </tr>
            <tr>
              <td><label class="control-label">VOC</label></td>
              <td><?php echo getAnyDetails($job_row["cust_problem"],"voc_desc","voc_code","voc_master",$link1);?></td>
              <td><?php 	$voc= explode(",",$job_row['cust_problem2']); 
			           $vocpresent   = count($voc);
					   if($vocpresent == '1'){
					   $name = getAnyDetails($voc[0],"voc_desc","voc_code","voc_master",$link1 );
					   }
					   else if($vocpresent >1){
					     $name ='';
					   for($i=0 ; $i<$vocpresent; $i++){					 
			 			$name.=  getAnyDetails($voc[$i],"voc_desc","voc_code","voc_master",$link1 ).",";
			 			}} echo $name;?></td>
              <td><?=$job_row['cust_problem3']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Remark </label></td>
              <td colspan="3"><?=$job_row['remark']?></td>
            </tr>
		<?php 
			$image_det = mysqli_query($link1,"SELECT * FROM image_upload_details  where job_no='".$job_row['job_no']."'");
			 while($row_image=mysqli_fetch_array($image_det)){?>  <tr>
              <td><label class="control-label"><?=$row_image['activity']?></label></td>
              <td colspan="3"  ><?php if ($row_image['img_url']!=""){?><span> <img src="<?=$row_image['img_url']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" height="100" width="100"></span><?php } if($row_image['img_url1']!="") {?><span> <img src="<?=$row_image['img_url1']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" height="100" width="100"></span> <?php } if($row_image['img_url2']!="") {?><span> <img src="<?=$row_image['img_url2']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)"  height="100" width="100"></span><?php } if($row_image['img_url3']!="") {?><span> <img src="<?=$row_image['img_url3']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" height="100" width="100"></span><?php } if($row_image['img_url4']!="") {?><span> <img src="<?=$row_image['img_url4']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)"  height="100" width="100"></span><?php }?></td>
            </tr><?php }?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;History</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="15%"><strong>Location</strong></td>
                    <td width="10%"><strong>Activity</strong></td>
                    <td width="15%"><strong>Outcome</strong></td>
                    <td width="10%"><strong>Warranty</strong></td>
                    <td width="10%"><strong>Status</strong></td>
                    <td width="10%"><strong>Update By</strong></td>
                    <td width="25%"><strong>Remark</strong></td>
                    <td width="15%"><strong>Update on</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				$res_jobhistory = mysqli_query($link1,"SELECT * FROM call_history where job_no='".$docid."'");
				while($row_jobhistory = mysqli_fetch_assoc($res_jobhistory)){
				?>
                  <tr>
                    <td><?=$row_jobhistory['location_code']?></td>
                    <td><?=$row_jobhistory['activity']?></td>
                    <td><?=$row_jobhistory['outcome']?></td>
                    <td><?=$row_jobhistory['warranty_status']?></td>
                    <td><?=$row_jobhistory['status']?></td>
                    <td><?=$row_jobhistory['updated_by']?></td>
                    <td><?=$row_jobhistory['remark']?></td>
                    <td><?=$row_jobhistory['update_date']?></td>
                  </tr>
                  <?php
				}
				  ?>
				  <?php   if($job_row['status'] == '9' && $job_row['sub_status'] != '91')  {?>
            <tr>
                 <td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_doa.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"></td>
               </tr>
			   <?php  } ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	
	<!--approval for EP-->
	<?php  if($job_row['status'] == '50' && $job_row['sub_status'] == '53') { ?>
	<form   id="frm1" name="frm1" method="post" >
<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Replacement Approval</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Status</label></td>
                <td width="30%"><select   id="status" name="status" class="form-control" required ><option value="">Please Select</option>
                <option value="51" >Approved</option><option value="52" >Rejected</option></select></td>
                <td width="20%"><label class="control-label">Remark</label></td>
                <td width="30%"><textarea id="remark" name="remark" class="form-control" required></textarea></td>
              </tr> 
			   <tr>
                 <td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_repl.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" id="update" name="update" value="Update" class="btn<?=$btncolor?>"></td>
               </tr>     
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	</form>
<?php  }?>
  </div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>