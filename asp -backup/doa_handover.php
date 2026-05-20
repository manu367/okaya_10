<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
if($job_row['call_for']!='Workshop'){$page_link='job_list';}
else { $page_link='job_list_asp';}
//print_r($arrstate);
@extract($_POST);
////// case 2. if we want to Add new user
if($_POST){
	if ($_POST['hando']=='Handover' && $docid!=''){
		//// initialize transaction parameters
		$flag = true;
		mysqli_autocommit($link1, false);
		$error_msg = "";
		////// update in jobsheet data
    	$sql_update = "UPDATE jobsheet_data set status ='9', sub_status ='94', hand_date ='".$today."',hand_time='".$currtime."',recipient_name='".$recipient_name."',recipient_contact='".$recipient_no."' ,doa_bag = '".$doa_bag."' where job_no ='".$docid."' ";
    	$res_update=mysqli_query($link1,$sql_update);
		//// check if query is not executed
		if (!$res_update) {
			 $flag = false;
			  $error_msg = "Error details1: " . mysqli_error($link1) . ".";
			
		}
		

		$res_replcedata = mysqli_query($link1,"INSERT INTO repair_detail set job_id='".$job_id."', job_no ='".$docid."', repair_location='".$_SESSION['asc_code']."', location_code='".$_SESSION['asc_code']."', model_id='".$model_id."' , status='9', fault_code='F0164', rep_lvl='1', part_repl='N', repair_code='R0049',handover_date ='".$today."'");
			//// check if query is not executed
			if (!$res_replcedata) {
				 $flag = false;
				  $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			
			}
			
			$max_rep=mysqli_query($link1,"insert into job_claim_appr set job_no='".$docid."',brand_id='".$_POST['brand']."',action_by='".$_SESSION['asc_code']."',rep_lvl ='1',cat='".$_POST['model_type']."',hand_date='".$today."'");
			//// check if query is not executed
		if (!$max_rep) {
			 $flag = false;
			  $error_msg = "Error details3: " . mysqli_error($link1) . ".";
		
		}
		 ///// Doa Issue and DOA collect ASP inventory plus and faulty crrate //
		if($_POST['doa_issue']=='asp' && $_POST['doa_collect']=='asp')
		{
			 ///// Job Partcode stock in  client inventory //
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$_POST['partcode']."' and location_code='".$_SESSION['asc_code']."' and okqty>0"))>0 ){
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set okqty=okqty -'1' ,faulty=faulty+1, updatedate='".$datetime."' where partcode='".$_POST['partcode']."' and location_code='".$_SESSION['asc_code']."'");
		
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
                $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			   
           }
			  $flag = stockLedger($docid,$today,$_POST['partcode'],$_SESSION['asc_code'],$job_row['customer_name'],"OUT","OK","DOA","DOA Done","1","0.00",$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
			  
			   $flag = stockLedger($docid,$today,$_POST['partcode'],$_SESSION['asc_code'],$job_row['customer_name'],"IN","Faulty","DOA","DOA Done","1","0.00",$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
	}	
	else
	{
	 $flag = false;
      $error_msg = "Stock is not available" . mysqli_error($link1) . ".";
	}
		}//////both ASP Select Case
		
		///// Doa Issue and DOA collect ASP inventory plus and faulty crrate //
		if($_POST['doa_issue']=='delaer' && $_POST['doa_collect']=='asp')
		{
			 ///// Job Partcode stock in  client inventory //
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$_POST['partcode']."' and location_code='".$_SESSION['asc_code']."'"))>0 ){
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set faulty=faulty+1 updatedate='".$datetime."' where partcode='".$_POST['partcode']."' and location_code='".$_SESSION['asc_code']."'");
		
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
                $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			   
           }
	}	
	else
	{
	 $result=mysqli_query($link1,"insert into client_inventory set faulty=faulty+1 ,updatedate='".$datetime."',partcode='".$_POST['partcode']."', location_code='".$_SESSION['asc_code']."'");
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
                $error_msg = "Error details21: " . mysqli_error($link1) . ".";
			   
           }
	}
			$flag = stockLedger($docid,$today,$_POST['partcode'],$_SESSION['asc_code'],$job_row['customer_name'],"IN","Faulty","DOA","DOA Done","1","0.00",$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
		}//////both ASP Select Case
		
		
		///// entry in call/job  history
		$flag = callHistory($docid,$_SESSION['asc_code'],"9","DOA Handover","Pending For DOA Certificate Print",$_SESSION['userid'],$job_row['warranty_status'],"","","",$ip,$link1,$flag);
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$docid,"DOA","Pending For DOA Certificate Print",$ip,$link1,$flag);
		///// check if all query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$cflag = "success";
			$cmsg = "Success";
			$msg="Job <strong>".$docid."</strong> is successfully handover to customer.";
		} else {
			mysqli_rollback($link1);
			$cflag = "danger";
			$cmsg = "Failed";
			$msg = "Request could not be processed. Please try again. ".$error_msg;
		} 
		mysqli_close($link1);
	}else{
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
	}
   	///// move to parent page
    header("location:$page_link.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <script>
$(document).ready(function(){
        $("#frm1").validate();
});
</script>
<script src="../js/frmvalidate.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script></head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-handshake-o"></i> DOA Handover</h2>
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
              <td><?=$job_row['model']?>  </td>
              <td><label class="control-label">Accessory Present</label></td>
              <td><?php echo $job_row['acc_rec'];?></td>
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
              <td><?=dt_format($job_row['dop'])?></td>
              <td><label class="control-label">Activation Date</label></td>
              <td><?=dt_format($job_row['activation'])?></td>
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
              <td><?=$job_row['voc1']?></td>
              <td><?=$job_row['voc2']?></td>
              <td><?=$job_row['voc3']?></td>
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
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-handshake-o fa-lg"></i>&nbsp;&nbsp;Job Handover</div>
      <div class="panel-body">
		<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Recipient Name <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="recipient_name" class="required form-control" id="recipient_name" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Recipient Contact No. <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="recipient_no" class="required number form-control" id="recipient_no" required/>
				    <input type="hidden" name="partcode" class="required form-control" id="partcode" value="<?php echo $job_row['partcode']?>" readonly/>
              </div>
            </div>
          </div>
			<div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">DOA Issue <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="doa_issue" id="doa_issue" class="form-control required">
				  <option value="">--Select DOA issue----</option>
					   <option value="delaer">Dealer</option>
					   <option value="asp">ASP</option>
				  </select>
              </div>
            </div>
          </div>
			<div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">DOA collect <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="doa_collect" id="doa_collect" class="form-control required">
				  <option value="">--Select DOA Collect----</option>
					   <option value="delaer">Dealer</option>
					   <option value="asp">ASP</option>
				  </select>
              </div>
            </div>
          </div>
           <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">DOA Polybag number <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="doa_bag" class="required form-control" id="doa_bag" required/>
                 <input type="hidden" name="model_id" class="required form-control" id="model_id"  value=<?=$job_row['model_id']?> required/>
                 <input type="hidden" name="job_id" class="required form-control" id="job_id"  value=<?=$job_row['job_id']?> required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <div class="col-md-6">
                
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="hando" id="hando" value="Handover" title="Handover to customer" <?php if($_POST['hando']=='Handover'){?>disabled<?php }?>>&nbsp;&nbsp;&nbsp;
                  <?php $f_type=getAnyDetails($job_row["model_id"],"feature_type","model_id","model_master",$link1);?> <input type="hidden" name="model_type"  id="model_type"  value="<?=$f_type?>"/>
               <input type="hidden" name="brand" class="required number form-control" id="brand" value="<?=$job_row["brand_id"]?>" required/>
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($docid)?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='<?=$page_link?>.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'">
            </div>
          </div>
    </form>
      </div><!--close panel body-->
    </div><!--close panel-->

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