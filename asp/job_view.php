<?php
require_once("../includes/config.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);

@extract($_POST);
////// if we hit process button
if ($_POST) {
	echo $_POST['update'];
	
    if ($_POST['update'] == 'Update') {
		mysqli_autocommit($link1, false);
            $flag = true;
            $err_msg = "";
			/////update estimate master table
			$res_upd = mysqli_query($link1,"UPDATE estimate_master set status  = '".$status."' ,appr_remark= '".$remark."' where job_no='".$docid."' ");
			/// check if query is execute or not//
					if(!$res_upd){
						$flag = false;
						$err_msg = "Error1". mysqli_error($link1) . ".";
					}
				
				/////update jobsheet data  table
				if($status=='51'){
					
			$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set sub_status  = '".$status."' ,remark= '".$remark."',estimate_approval= 'Y'  where job_no='".$docid."' ");
					
				 $flag = callHistory($docid,$_SESSION['asc_code'],$status,"EP Approval","EP Approve by Customer",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
			
				}else{
					
						$jobsheet_upd = mysqli_query($link1,"UPDATE jobsheet_data set sub_status  = '".$status."' ,remark= '".$remark."',estimate_approval= 'N'  where job_no='".$docid."' ");
				$flag = callHistory($docid,$_SESSION['asc_code'],$status,"EP Rejected","EP Reject by Customer",$_SESSION['userid'],"",$remark,"","",$ip,$link1,$flag);
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
       header("location:job_list_asp.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
    include("../includes/leftnavemp2.php");
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
              <td><?=$job_row['dop']?></td>
              <td><label class="control-label">Activation Date</label></td>
              <td><?=$job_row['activation']?></td>
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
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Vistor Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Vist Date</label></td>
                <td width="30%"><?php echo $job_row['vistor_date'];?></td>
                <td width="20%"><label class="control-label">Vist Time</label></td>
                <td width="30%"><?php echo $job_row['vistor_time'];?></td>
              </tr>
            <tr>
              <td><label class="control-label">Vist Approved By</label></td>
              <td><?=$job_row['vistor_app_by']?></td>
              <td><label class="control-label"></label></td>
              <td></td>
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
				 <!-- <?php   if($job_row['status'] != '5')  {?>
            <tr>
                 <td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"></td>
               </tr>
			   <?php  } ?>-->
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	
	<?php $repair_history = mysqli_query($link1,"SELECT * FROM repair_detail where job_no='".$docid."'"); if(mysqli_num_rows($repair_history)>0){?>	
	 <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;Repair Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
				     <td width="15%"><strong>Condition</strong></td>
					 <td width="15%"><strong>Symptom</strong></td>
                    <td width="10%"><strong>Section</strong></td>
                    <td width="15%"><strong>Repair Location</strong></td>
					 <td width="15%"><strong>Defect Name</strong></td>
                    <td width="10%"><strong>Repair Code Name</strong></td>
                    <td width="10%"><strong>Partcode</strong></td>               
                    <td width="10%"><strong>Engineer Name</strong></td>
					 <td width="10%"><strong>Replace Imei 1</strong></td>
                    <td width="10%"><strong>Replace Imei 2</strong></td>
					 <td width="10%"><strong>Remark</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				
				while($repair_info = mysqli_fetch_assoc($repair_history)){
				?>
                  <tr>
				 <td><?=getAnyDetails($repair_info['condition_code'],"condition_desc","condition_code","condition_master",$link1);?></td>
					 <td><?=getAnyDetails($repair_info['symptom_code'],"symp_desc","symp_code","symptom_master",$link1);?></td>
					  <td><?=getAnyDetails($repair_info['section_code'],"section_desc","section_code","section_master",$link1);?></td>
                   
                    <td><?=getAnyDetails($repair_info['repair_location'],"locationname","location_code","location_master",$link1);?></td>
					 <td><?=getAnyDetails($repair_info['fault_code'],"defect_desc","defect_code","defect_master",$link1);?></td>
                    <td><?=getAnyDetails($repair_info['repair_code'],"rep_desc","rep_code","repaircode_master",$link1);?></td>
                    <td><?=getAnyDetails($repair_info['partcode'],"name","partcode","partcode_master",$link1);?></td>                  
                    <td><?=getAnyDetails($repair_info['eng_id'],"locusername","userloginid","locationuser_master",$link1);?></td>
					<td><?=$repair_info['replace_imei1']?></td>
					<td><?=$repair_info['replace_imei2']?></td>
                    <td><?=$repair_info['remark']?></td>
                  </tr>
                  <?php
				}
				  ?>				
           
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	
	
  </div><!--close panel group-->
  <?php } ?> 
 
  <?php $asc_part=mysqli_query($link1,"Select * from sfr_transaction where job_no = '".$docid."' ");
	 if(mysqli_num_rows($asc_part)>0){?>	
	 <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;SFR Dispatch Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="15%"><strong>From Party</strong></td>
					 <td width="15%"><strong>To Party</strong></td>
                    <td width="10%"><strong>Dispatch Date</strong></td>
                    <td width="10%"><strong>Challan No.</strong></td>               
                    <td width="10%"><strong>Docket No.</strong></td>
					 <td width="10%"><strong>Courier Name</strong></td>
                   
                  </tr>
                </thead>
                <tbody>
                <?php			
				while($ascpart_info = mysqli_fetch_assoc($asc_part)){
				?>
                  <tr>
                    <td><?=getAnyDetails($ascpart_info['from_location'],"locationname","location_code","location_master",$link1);?></td>
					 <td><?=getAnyDetails($ascpart_info['to_location'],"locationname","location_code","location_master",$link1);?></td>
                    <td><?=dt_format($ascpart_info['challan_date']);?></td>
                    <td><?=$ascpart_info['challan_no'];?></td>                  
                    <td><?=getAnyDetails($ascpart_info['challan_no'],"docket_no","challan_no","sfr_challan",$link1);?></td>
					<td><?=getAnyDetails($ascpart_info['challan_no'],"courier","challan_no","sfr_challan",$link1);?></td>
                  </tr>
                  <?php
				}
				  ?>				
           
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
  <?php } ?> 
  
  
  <?php $pna_info=mysqli_query($link1,"Select * from auto_part_request where job_no ='".$docid."' ");
	 if(mysqli_num_rows($pna_info)>0){?>	
	 <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;PNA Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="15%"><strong>Part Detail</strong></td>
					 <td width="10%"><strong>PNA Request Date</strong></td>
                    <td width="10%"><strong>Request No.</strong></td>
					<td width="10%"><strong>Request Generate Date</strong></td>
                    <td width="10%"><strong>PO Dispatch Date</strong></td>               
                    <td width="10%"><strong>Challan No.</strong></td>
					 <td width="10%"><strong>Docket No.</strong></td>
					 <td width="10%"><strong>Courier</strong></td>                 
                  </tr>
                </thead>
                <tbody>
                <?php			
				while($pna_detail = mysqli_fetch_assoc($pna_info)){
				 $po_items =  mysqli_fetch_array(mysqli_query($link1,"select * from po_items where job_no = '".$pna_detail['job_no']."' "));
				?>
                  <tr>
                    <td><?=getAnyDetails($pna_detail['partcode'],"part_desc","partcode","partcode_master",$link1);?></td>
					<td><?=dt_format($pna_detail['request_date']);?></td>
					<td><?=$po_items['po_no'];?></td>
					<td><?=dt_format($po_items['update_date']);?></td>					
                    <td><?=getAnyDetails($po_items['po_no'],"sale_date","po_no","billing_master",$link1);?></td>
                    <td><?=getAnyDetails($po_items['po_no'],"challan_no","po_no","billing_master",$link1);?></td>                  
                    <td><?=getAnyDetails($po_items['po_no'],"docket_no","po_no","billing_master",$link1);?></td>
					<td><?=getAnyDetails($po_items['po_no'],"courier","po_no","billing_master",$link1);?></td>
                  </tr>
                  <?php
				}
				  ?>				
           
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
  </div><!--close panel group-->
  <?php } ?> 
  
    <div class="panel panel-info table-responsive">
     
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
    <tr>
                 <td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_asp.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"></td>
               </tr>
           
          </table>
     
    </div><!--close panel-->
      </div><!--close panel-->
	
	
  </div><!--close panel group-->
	
	<!--approval for EP-->
	<?php  if($job_row['status'] == '5') { ?>
	<form   id="frm1" name="frm1" method="post" >
<div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Estimate Approval</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Status</label></td>
                <td width="30%"><select   id="status" name="status" class="form-control" required ><option value="">Please Select</option><option value="51" <?php if($_REQUEST['status'] == "51") { echo 'selected'; }?>>Approve by Customer</option><option value="52" <?php if($_REQUEST['status'] == "52") { echo 'selected'; }?>>Reject  by Customer</option></select></td>
                <td width="20%"><label class="control-label">Remark</label></td>
                <td width="30%"><textarea id="remark" name="remark" class="form-control" required></textarea></td>
              </tr> 
			   <tr>
                 <td width="100%" align="center" colspan="8"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_asp.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" id="update" name="update" value="Update" class="btn<?=$btncolor?>"></td>
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