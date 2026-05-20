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
				if($status=='92'){
					$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set sub_status  = '".$status."' ,doa_remark = '".$remark."',doa_approval= 'Y',doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."'  where job_no='".$docid."' ");
					
				$flag = callHistory($docid,$job_row['location_code'],$status,"DOA Approval","DOA Approved",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
				}else{
					$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set status  = '1',sub_status  = '1' ,doa_remark = '".$remark."',doa_approval= 'N' ,doa_ar_by='".$_SESSION['userid']."',doa_ar_dt='".$today."',doa_rej_rmk='".$remark."' where job_no='".$docid."' ");
				$flag = callHistory($docid,$job_row['location_code'],$status,"DOA Rejected - call Re-open","DOA Rejected",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
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
        header("location:job_list_doa.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
                <td><label class="control-label">Contact No. <span class="small">(For SMS Update)</span></label></td>
                <td><?php echo $job_row['contact_no'];?></td>
                <td><label class="control-label">Alternate Contact No.</label></td>
                <td><?php echo $job_row['alternate_no'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($job_row["state_id"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Email</label></td>
                <td><?php echo $job_row['email'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">City</label></td>
                <td><?php echo getAnyDetails($job_row["city_id"],"city","cityid","city_master",$link1);?></td>
                <td><label class="control-label">Pincode</label></td>
                <td><?php echo $job_row['pincode'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Customer Type</label></td>
                <td><?php echo $job_row['customer_type'];?></td>
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
              <td><label class="control-label">Accessory Present</label></td>
              <td><?php $part= explode(",",$job_row['acc_rec']); 
			           $partpresent   = count($part);
					   for($i=0 ; $i<$partpresent; $i++){
			 			 echo getAnyDetails($part[$i],"part_desc","partcode","partcode_master",$link1 ).",";
			 }
			  ?></td>
            </tr>
            <tr>
              <td><label class="control-label">IMEI 1/Serial No. 1</label></td>
              <td><?=$job_row['imei']?></td>
              <td><label class="control-label">IMEI 2/Serial No. 2</label></td>
              <td><?=$job_row['sec_imei']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Job Type</label></td>
              <td><?=$job_row['call_type']?></td>
              <td><label class="control-label">Job For</label></td>
              <td><?=$job_row['call_for']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Purchase Date</label></td>
              <td><?=$job_row['dop']?></td>
              <td><label class="control-label">Activation Date</label></td>
              <td><?=$job_row['activation']?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
     <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;DOA Conditions</div>
              <div class="panel-body">
                  <div class="form-group">
                    <div class="col-md-12"><label class="col-md-10 custom_label">Q1. Is the purchase proof available with IMEI No ' s? <span class="red_small">*</span></label>
                      <div class="col-md-2">
							<?php if($doa_row['q1']=="Y"){?> <i class="fa fa-check"></i> <?php }else{?> <i class="fa fa-close"></i> <?php }?>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-12"><label class="col-md-10 custom_label">Q2. Are IMEI and Serial No on the mobile same as that on the packing box? <span class="red_small">*</span></label>
                      <div class="col-md-2">
							<?php if($doa_row['q2']=="Y"){?> <i class="fa fa-check"></i> <?php }else{?> <i class="fa fa-close"></i> <?php }?>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-12"><label class="col-md-10 custom_label">Q3. The problem reported is not related to software which can be solved by upgrade software version.<span class="red_small">*</span></label>
                      <div class="col-md-2">
							<?php if($doa_row['q3']=="Y"){?> <i class="fa fa-check"></i> <?php }else{?> <i class="fa fa-close"></i> <?php }?>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-12"><label class="col-md-10 custom_label">Q4. The problem reported is not related to accessories. <span class="red_small">*</span></label>
                      <div class="col-md-2">
							<?php if($doa_row['q4']=="Y"){?> <i class="fa fa-check"></i> <?php }else{?> <i class="fa fa-close"></i> <?php }?>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-12"><label class="col-md-10 custom_label">Q5. Unit does not have any physical damage, water damage/Water Liquid (Water detection label) or tampering on Handset. <span class="red_small">*</span></label>
                      <div class="col-md-2">
							<?php if($doa_row['q5']=="Y"){?> <i class="fa fa-check"></i> <?php }else{?> <i class="fa fa-close"></i> <?php }?>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-12"><label class="col-md-10 custom_label">Q6. Is not a Cosmetic reject (E.g. : Scratches on phone, lens, dent, etc.).<span class="red_small">*</span></label>
                      <div class="col-md-2">
							<?php if($doa_row['q6']=="Y"){?> <i class="fa fa-check"></i> <?php }else{?> <i class="fa fa-close"></i> <?php }?>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-12"><label class="col-md-10 custom_label">Q7. Is the unit complete sales package as mention in content of the box label sticker (Gift box, handset, user manual, hands free, charger, battery, software CD, data cable and memory card)?<span class="red_small">*</span></label>
                      <div class="col-md-2">
							<?php if($doa_row['q7']=="Y"){?> <i class="fa fa-check"></i> <?php }else{?> <i class="fa fa-close"></i> <?php }?>
                      </div>
                    </div>
                  </div>
    			</div>
              </div>
            </div>
    
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Initial Symptom</label></td>
                <td width="30%"><?php echo $job_row['symp_code'];?></td>
                <td width="20%"><label class="control-label">Physical Condition</label></td>
                <td width="30%"><?php echo $job_row['phy_cond'];?></td>
              </tr>
            <tr>
              <td><label class="control-label">ELS Status</label></td>
              <td><?=$job_row['els_status']?></td>
              <td><label class="control-label">Warranty Status</label></td>
              <td><?php echo $job_row['warranty_status'];?></td>
            </tr>
            <tr>
              <td><label class="control-label">Dealer Name</label></td>
              <td><?=$job_row['dname']?></td>
              <td><label class="control-label">Invoice No</label></td>
              <td><?=$job_row['inv_no']?></td>
            </tr>
            <tr>
              <td><label class="control-label">VOC</label></td>
               <td><?=getAnyDetails($job_row['cust_problem'],"voc_desc","voc_code","voc_master",$link1);?></td>
              <td><?=getAnyDetails($job_row['cust_problem2'],"voc_desc","voc_code","voc_master",$link1);?></td>
              <td><?=getAnyDetails($job_row['cust_problem3'],"voc_desc","voc_code","voc_master",$link1);?></td>
            </tr>
            <tr>
              <td><label class="control-label">Remark </label></td>
              <td colspan="3"><?=$job_row['remark']?></td>
            </tr>
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
	<?php  if($job_row['status'] == '9' && $job_row['sub_status'] == '91') { ?>
	<form   id="frm1" name="frm1" method="post" >
<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;DOA Approval</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Status</label></td>
                <td width="30%"><select   id="status" name="status" class="form-control" required ><option value="">Please Select</option>
                <option value="92" >Approved</option><option value="93" >Rejected</option></select></td>
                <td width="20%"><label class="control-label">Remark</label></td>
                <td width="30%"><textarea id="remark" name="remark" class="form-control" required></textarea></td>
              </tr> 
			   <tr>
                 <td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_doa.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" id="update" name="update" value="Update" class="btn<?=$btncolor?>"></td>
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