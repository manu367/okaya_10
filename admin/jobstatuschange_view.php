<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
?>
<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
  <?=siteTitle?>
  </title>
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
  <script>
 $(document).ready(function() {
    if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });
});
$(window).on("popstate", function() {
    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
    $("a[href='" + anchor + "']").tab("show");
	if(location.hash=="#menu1"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
	}
	else if(location.hash=="#menu2"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="";
		document.getElementById("menu3").style.display="none";
	}
	else if(location.hash=="#menu3"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="";
	}
	else{
		document.getElementById("home").style.display="";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
		document.getElementById("menu3").style.display="none";
	}
});
  ///////apply loading message after hitting save button
 /*$('#savejob').on('click', function() {
    var $this = $(this);
  	$this.button('loading');
    setTimeout(function() {
       $this.button('reset');
   }, 8000);
 }); */
$('#savejob').on('click', function() {
  $('#frm1').submit(function(){
    $("input[type='submit']", this)
      .val("Please Wait...")
      .attr('disabled', 'disabled');
    return true;
  });
});
  </script>
  <script type="text/javascript" src="../js/jquery.validate.js"></script>
  <body>
  <div class="container-fluid">
    <div class="row content">
      <?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="<?=$screenwidth?>">
        <h2 align="center"><i class="fa fa-list-alt"></i> Job Status Change</h2>
        <h4 align="center">Job No.-
          <?=$docid?>
        </h4>
        <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-id-card"></i> Customer Details</a></li>
            <li><a data-toggle="tab" href="#menu1"><i class="fa fa-desktop"></i> Product Details</a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-pencil-square-o"></i> Observation</a></li>
            <li><a data-toggle="tab" href="#menu3"><i class="fa fa-gear"></i> History</a></li>
            <li><a data-toggle="tab" href="#menu4"><i class="fa fa-list-alt"></i> Job Status Change</a></li>
          </ul>
          <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
              <table class="table table-bordered" width="100%">
                <tbody>
                  <tr>
                    <td width="20%"><label class="control-label">Customer Name</label></td>
                    <td width="30%"><?php echo $job_row['customer_name'];?></td>
                    <td width="20%"><label class="control-label">Address</label></td>
                    <td width="30%"><?php echo $job_row['address'];?></td>
                  </tr>
                  <tr>
                    <td><label class="control-label">Contact No.</label></td>
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
                <td><label class="control-label">Recepient Name</label></td>
                <td><?php echo $job_row['recipient_name'];?></td>
              </tr>
			  <tr>
                <td><label class="control-label">Recepient Contact No.</label></td>
                <td><?php echo $job_row['recipient_contact'];?></td>
                <td><label class="control-label"></label></td>
                <td></td>
              </tr>
                  <tr>
                    <td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='admin_jobstatus_change.php?<?=$pagenav?>'">
                      &nbsp;
                      <button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu1'">Next >></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="menu1" class="tab-pane fade">
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
                    <td><?=$$job_row['activation']?></td>
                  </tr>
                  <tr>
                    <td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='admin_jobstatus_change.php?<?=$pagenav?>'">
                      &nbsp;
                      <button title="Previous" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#home'"><< Previous</button>
                      &nbsp;
                      <button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu2'">Next >></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="menu2" class="tab-pane fade">
              <table class="table table-bordered" width="100%">
                <tbody>
                  <tr>
                    <td width="20%" colspan="2"><label class="control-label">Initial Symptom</label></td>
                    <td width="30%" colspan="2"><?php echo $job_row['symp_code'];?></td>
                    <td width="20%" colspan="2"><label class="control-label">Physical Condition</label></td>
                    <td width="30%" colspan="2"><?php echo $job_row['phy_cond'];?></td>
                  </tr>
                  <tr>
                    <td colspan="2"><label class="control-label">Dealer Name</label></td>
                    <td colspan="2"><?=$job_row['dname']?></td>
                    <td colspan="2"><label class="control-label">Invoice No</label></td>
                    <td colspan="2"><?=$job_row['inv_no']?></td>
                  </tr>
                  <tr>
                   <td colspan="2"><label class="control-label">VOC</label></td>
                   <td colspan="2"><?=getAnyDetails($job_row['cust_problem'],"voc_desc","voc_code","voc_master",$link1);?></td>
             		<td colspan="2"><?=getAnyDetails($job_row['cust_problem2'],"voc_desc","voc_code","voc_master",$link1);?></td>
              		<td colspan="2"><?=getAnyDetails($job_row['cust_problem3'],"voc_desc","voc_code","voc_master",$link1);?></td>
                  </tr>
                 <tr>
              <td colspan="2"><label class="control-label">Close Date</label></td>
              <td colspan="2"><?=dt_format($job_row['close_date'])?></td>
			  <td colspan="2"><label class="control-label">Handover Date</label></td>
              <td colspan="2"><?=dt_format($job_row['hand_date'])?></td>
            </tr>
            <tr>
              <td colspan="2"><label class="control-label">Remark </label></td>
              <td colspan="2"><?=$job_row['remark']?></td>
			  <td colspan="2"><label class="control-label"></label></td>
              <td colspan="2"></td>
            </tr>
			<?php 	$repair_history = mysqli_query($link1,"SELECT * FROM repair_detail where job_no='".$docid."'");
					if(mysqli_num_rows($repair_history)>0){  ?>
			<tr><td colspan="8" align="center"><strong>Repair Details</strong></td></tr>
                  <tr>
                    <td width="15%"><strong>Repair Location</strong></td>
					 <td width="15%"><strong>Fault Code Name</strong></td>
                    <td width="10%"><strong>Repair Code Name</strong></td>
                    <td width="10%"><strong>Partcode</strong></td>               
                    <td width="10%"><strong>Engineer Name</strong></td>
					 <td width="10%"><strong>Replace Imei 1</strong></td>
                    <td width="10%"><strong>Replace Imei 2</strong></td>
					 <td width="10%"><strong>Remark</strong></td>
                  </tr>
                <?php			
				while($repair_info = mysqli_fetch_assoc($repair_history)){
				?>
                  <tr>
                    <td><?=getAnyDetails($repair_info['repair_location'],"locationname","location_code","location_master",$link1);?></td>
					 <td><?=getAnyDetails($repair_info['fault_code'],"symp_desc","symp_code","symptom_master",$link1);?></td>
                    <td><?=getAnyDetails($repair_info['repair_code'],"rep_desc","rep_code","repaircode_master",$link1);?></td>
                    <td><?=getAnyDetails($repair_info['partcode'],"name","partcode","partcode_master",$link1);?></td>                  
                    <td><?=getAnyDetails($repair_info['eng_id'],"locusername","userloginid","locationuser_master",$link1);?></td>
					<td><?=$repair_info['replace_imei1']?></td>
					<td><?=$repair_info['replace_imei2']?></td>
                    <td><?=$repair_info['remark']?></td>
                  </tr>
                  <?php
				}}
				  ?>	
				  <?php $asc_part=mysqli_query($link1,"Select * from sfr_transaction where job_no = '".$docid."' ");
	 			if(mysqli_num_rows($asc_part)>0){?>	
			<tr><td colspan="8" align="center"><strong>SFR Details</strong></td></tr>
			 <tr>
                    <td width="15%"><strong>From Party</strong></td>
					 <td width="15%"><strong>To Party</strong></td>
                    <td width="10%"><strong>Dispatch Date</strong></td>
                    <td width="10%"><strong>Challan No.</strong></td>               
                    <td width="10%" colspan="2"><strong>Docket No.</strong></td>
					 <td width="10%" colspan="2"><strong>Courier Name</strong></td>                  
                  </tr>
				  <?php
						
				while($ascpart_info = mysqli_fetch_assoc($asc_part)){
				?>
                  <tr>
                    <td><?=getAnyDetails($ascpart_info['from_location'],"locationname","location_code","location_master",$link1);?></td>
					 <td><?=getAnyDetails($ascpart_info['to_location'],"locationname","location_code","location_master",$link1);?></td>
                    <td><?=dt_format($ascpart_info['challan_date']);?></td>
                    <td><?=$ascpart_info['challan_no'];?></td>                  
                    <td colspan="2"><?=getAnyDetails($ascpart_info['challan_no'],"docket_no","challan_no","sfr_challan",$link1);?></td>
					<td colspan="2"><?=getAnyDetails($ascpart_info['challan_no'],"courier","challan_no","sfr_challan",$link1);?></td>
                  </tr>
                  <?php
				}}
				  ?>	
				   <?php $pna_info=mysqli_query($link1,"Select * from auto_part_request where job_no ='".$docid."' ");
	 if(mysqli_num_rows($pna_info)>0){?>	
				  <tr><td colspan="8" align="center"><strong>PNA  Details</strong></td></tr>
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
				   <?php	
				$pna_info=mysqli_query($link1,"Select * from auto_part_request where job_no ='".$docid."' ");		
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
				}}
				  ?>										         			
                  <tr>
                    <td colspan="8" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='admin_jobstatus_change.php?<?=$pagenav?>'">
                      &nbsp;
                      <button title="Previous" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu1'"><< Previous</button>
                      &nbsp;
                      <button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu3'">Next >></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="menu3" class="tab-pane fade">
            <table class="table table-bordered" width="100%">
                <thead>	
                  <tr>
                    <td width="15%"><strong>Location</strong></td>
                    <td width="15%"><strong>Activity</strong></td>
                    <td width="15%"><strong>Outcome</strong></td>
                    <td width="10%"><strong>Warranty</strong></td>
                    <td width="10%"><strong>Update By</strong></td>
                    <td width="15%"><strong>Remark</strong></td>
                    <td width="10%"><strong>Update on</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				$res_jobhistory = mysqli_query($link1,"SELECT * FROM call_history where job_no='".$docid."'");
				while($row_jobhistory = mysqli_fetch_assoc($res_jobhistory)){
				?>
                  <tr>
                    <td><?=getAnyDetails($row_jobhistory['location_code'],"locationname","location_code","location_master",$link1);?></td>
                    <td><?=$row_jobhistory['activity']?></td>
                    <td><?=$row_jobhistory['outcome']?></td>
                    <td><?=$row_jobhistory['warranty_status']?></td>
                    <td><?=getAnyDetails($row_jobhistory['updated_by'],"locationname","location_code","location_master",$link1);?></td>
                    <td><?=$row_jobhistory['remark']?></td>
                    <td><?=$row_jobhistory['update_date']?></td>
                  </tr>
                <?php
				}
				?>
                  <tr>
                    <td colspan="8" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='admin_jobstatus_change.php?<?=$pagenav?>'">
                      &nbsp;
                      <button title="Previous" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu2'"><< Previous</button>
                      &nbsp;<button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu4'">Next >></button></td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="menu4" class="tab-pane fade">
		<form  name="frm1" id="frm1" class="form-horizontal" action="job_status_change.php" method="post"><br>
			<?php if($job_row['status'] == '8' ||  $job_row['status'] == '9' || $job_row['status'] == '10'  || $job_row['status']  == '11' || $job_row['status'] == '12'  || $job_row['status'] == '13' || $job_row['status'] == '6') { 
			echo "<strong>" ."Sorry , You can't change status of JOB" ."</strong>";
			?>		
			<?php }
			else{ ?>
              <div class="form-group">
                <div class="col-md-6"><label class="col-md-5">Job Status<span class="red_small">*</span></label>
                  <div class="col-md-6">
                    <select name="jobstatus" id="jobstatus"  class="required form-control" style="width:250px;" required>
                      <option value="" selected>--Select Status--</option>
                      <?php
							 $res_jobstatus = mysqli_query($link1,"select status_id, display_status from jobstatus_master where status_id=main_status_id and system_status in ('Open','Handover','Cancel') order by display_status");
							 while($row_jobstatus = mysqli_fetch_assoc($res_jobstatus)){
							?>
                      <option value="<?=$row_jobstatus['status_id']?>" <?php if($job_result['status'] == $row_jobstatus['status_id']){echo 'selected';}?>>
                      <?=$row_jobstatus['display_status']?>
                      </option>
                      <?php
							 }
							 ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6">Remark<span class="red_small">*</span></label>
                  <div class="col-md-6">
                    <textarea name="rep_remark" id="rep_remark" class="required form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:none"></textarea>
                  </div>
                </div>
              </div><!--close form group-->
			   <div class="form-group">
                <div class="col-md-12" align="center">
				<br/>
                  <input name="savejob" id="savejob" type="submit" class="btn btn-success" value="Save" title="Save">
                  <input name="postjobno" type="hidden" class="form-control" id="postjobno" value="<?=base64_encode($job_row['job_no'])?>"/>
				  
                  <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               	  <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                  <br/><br/>
			  
			  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='admin_jobstatus_change.php?<?=$pagenav?>'">
                      &nbsp;
                      <button title="Previous" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu3'"><< Previous</button>
                      &nbsp; 
        </div>
              </div>
              </form>
            </div>
			<?php }?>
          </div>
        </div>
      </div>
      <!--End form group--> 
    </div>
    <!--End col-sm-9--> 
  </div>
  <!--End row content-->
  </div>
  <!--End container fluid-->
  <?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>