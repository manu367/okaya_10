<?php
session_start();
 $_SESSION['userid']=$_REQUEST['eng_id'];

require_once("../includes/config.php");
$docid=$_REQUEST['job_no'];
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
if($job_row['call_for']!='Workshop'){$page_link='job_list';}
else { $page_link='job_list_asp';}
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

	
	
	$cust_det = explode("~",getAnyDetails($job_row['customer_id'],"customer_id,landmark,email,phone,dob_date,mrg_date,alt_mobile ","customer_id","customer_master",$link1));
	$product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where job_no='".$job_row['job_no']."'"));
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-ticket"></i> Complaint Details</h2>
      <h4 align="center">Job No.- <?=$docid?></h4>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Customer Name</label></td>
                <td width="30%"><?php echo $job_row['customer_name'];?> <input name="customer_name" type="hidden" class=" form-control" id="customer_name" value="<?=$job_row['customer_name'];?>" ></td>
            <td wid    th="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?php echo $job_row['address'];?></td>
              </tr>              <tr>
                <td><label class="control-label">Contact No.</label><br/><span class="red_small">(For SMS Update)</span></td>

                <td><?php echo $job_row['contact_no'];?>  <input name="contact_no" type="hidden" class=" form-control" id="contact_no" value="<?=$job_row['contact_no'];?>" ></td>
                <td><label class="control-label">Alternate Contact No.</label></td>
                <td><?php echo $cust_det[6];?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($job_row["state_id"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Email</label></td>
                <td><?php echo $cust_det[2];?></td>
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
                <td><?php echo $cust_det[3];?></td>
              </tr>
			   <tr>
                <td><label class="control-label">Landmarks</label></td>
                <td><?php echo $cust_det[1];?></td>
                <td><label class="control-label"></label></td>
                <td><?php ?></td>
              </tr>
			  	   <tr>
                <td><label class="control-label">Date Of Birth</label></td>
                <td><?php echo $cust_det[4];?></td>
                <td><label class="control-label">Marriage Date</label></td>
                <td><?php  echo $cust_det[5]; ?></td>
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
              <td><?=dt_format($job_row['installation_date'])?></td>
            </tr>
            <tr>
              <td><label class="control-label"><?php echo SERIALNO ?></label></td>
              <td><?=$job_row['imei']?></td>
              <td><label class="control-label">Call Source</label></td>
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
			  <td><label class="control-label">Invoice No</label></td>
              <td><?=$job_row['inv_no']?></td>
              <td><label class="control-label">Purchase From</label></td>
              <td ><?php if( $job_row['entity_type']=='Others') { echo "Others" ;} else {echo getAnyDetails($job_row['entity_type'],"name","id","entity_type",$link1); }?></td>
              
            </tr>
			 <tr>
              <td><label class="control-label">Dealer Name</label></td>
              <td><?=$job_row['dname']?></td>
              <!--<td><label class="control-label">Invoice No</label></td>
              <td><?=$job_row['inv_no']?></td>-->
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
              <td ><?=$job_row['remark']?></td>
			  <?php if($_SESSION['id_type']=='CC'){?>
			    <td><label class="control-label">Happy Code </label></td>
              <td ><?=$job_row['h_code']?></td><?php } else {?>
			    <td><label class="control-label">&nbsp; </label></td>
              <td >&nbsp;</td> <?php }?>
            </tr>
			 <tr>
              <td><label class="control-label">Confirm By</label></td>
              <td><?=$job_row['recipient_name']?></td>
              <td><label class="control-label">Contact No</label></td>
              <td><?=$job_row['recipient_contact']?></td>
            </tr>
			 <tr>
              <td><label class="control-label">Service Remark</label></td>
              <td><?=$job_row['service_rmak']?></td>
              <td><label class="control-label">Rating</label></td>
              <td><?=$job_row['rating']?></td>
            </tr> <tr>
              <td><label class="control-label">Service Charge</label></td>
              <td><?php echo "";?></td>
              <td><label class="control-label"></label></td>
              <td></td>
            </tr>
				 <tr>
              <td><label class="control-label">Document Type</label></td>
              <td><?=$job_row['doc_type']?></td>
              <td><label class="control-label">Customer Ready to Pay</label></td>
              <td><?=$job_row['customer_satif']?></td>
            </tr> <tr>
              <td><label class="control-label">Manufecturing Date</label></td>
             <td><?=$job_row['manufacter_date']?></td>
              <td><label class="control-label">Warranty Card</label></td>
            <td><?=$job_row['warranty_card']?></td>
            </tr>
			<tr>
             <td><label class="control-label">Invoice No at the Close Job</label></td>
            <td><?=$job_row['invoice_no']?></td>
              <td><label class="control-label">&nbsp;</label></td>
            <td>&nbsp;</td>
            </tr>
			<?php 
			$image_det = mysqli_query($link1,"SELECT * FROM image_upload_details  where job_no='".$job_row['job_no']."'");
			 while($row_image=mysqli_fetch_array($image_det)){?>  <tr>
              <td><label class="control-label"><?=$row_image['activity']?></label></td>
              <td colspan="3"  ><?php if ($row_image['img_url']!=""){?><span> <img src="<?=$row_image['img_url']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php } if($row_image['img_url1']!="") {?><span> <img src="<?=$row_image['img_url1']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span> <?php } if($row_image['img_url2']!="") {?><span> <img src="<?=$row_image['img_url2']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php } if($row_image['img_url3']!="") {?><span> <img src="<?=$row_image['img_url3']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php } if($row_image['img_url4']!="") {?><span> <img src="<?=$row_image['img_url4']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php }?></td>
            </tr><?php }?>
            <tr>
              <td><label class="control-label">Eng Name</label></td>
              <td><?php $eng_det= explode("~",getAnyDetails($job_row['eng_id'],"locusername,contactmo","userloginid","locationuser_master",$link1));
			  echo $eng_det['0'];?></td>
              <td><label class="control-label">Eng Phone No</label></td>
              <td><?=$eng_det['1'];?></td>
            </tr>
            <tr>
            <td><label class="control-label">Pending  Reason</label></td>
            <td><?=$job_row[reason]?></td>
            <td><label class="control-label">Close  Reason</label></td> 
            <td><?=$job_row[close_rmk]?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
	 </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;History</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="15%"><strong>Location</strong></td>
                    <td width="10%"><strong>Activity</strong></td>
                    <td width="15%" colspan="2"><strong>Outcome</strong></td>
                    <td width="10%"><strong>Travel(KM)</strong></td>
                   
                    <td width="10%"><strong>Update By</strong></td>
                    <td width="10%"><strong>Remark</strong></td>
					<td width="10%"><strong>Priority</strong></td>
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
                    <td colspan="2"><?=$row_jobhistory['outcome']?></td>
                    <td><?=$row_jobhistory['travel_km']?></td>
                   
                    <td><?=$row_jobhistory['updated_by']?></td>
                    <td><?=$row_jobhistory['remark']?></td>
					 <td><?php if($row_jobhistory['priority'] == '1') {echo "Low";} elseif($row_jobhistory['priority'] == '2') {echo "Normal";} else {echo "High" ;}?></td>
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
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;Check  In &  OUT Details </div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
                    <td width="15%"><strong>ENG ID</strong></td>
                    <td width="10%"><strong>Address</strong></td>
                    <td width="15%" colspan="2"><strong>Date</strong></td>
                    <td width="10%"><strong>Time</strong></td>
                  </tr>
                </thead>
                <tbody>
                <?php
				$res_jobhistory1 = mysqli_query($link1,"SELECT * FROM job_punch_details where job_no='".$docid."'");
				while($row_jobhistory1 = mysqli_fetch_assoc($res_jobhistory1)){
				?>
                  <tr>
                    <td><?=$row_jobhistory1['eng_id']?></td>
                    <td><?=$row_jobhistory1['punch_address']?></td>
                    <td colspan="2"><?=$row_jobhistory1['punch_date']?></td>
                    <td><?=$row_jobhistory1['punch_time']?></td>
                  </tr>
                  <?php
				}
				  ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    
	<?php $repair_history = mysqli_query($link1,"SELECT * FROM repair_detail where job_no='".$docid."'"); if(mysqli_num_rows($repair_history)>0){?>	
	    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-history fa-lg"></i>&nbsp;&nbsp;Repair Detail</div>
      <div class="panel-body">
  <table class="table table-bordered" width="100%">
         <thead>	
                  <tr>
               	 
                    <td width="15%"><strong>Repair Location</strong></td>
					 <td width="15%"><strong>Defect Name</strong></td>
                    <td width="10%"><strong>Repair Code Name</strong></td>
                    <td width="10%"><strong>Partcode</strong></td>               
                    <td width="10%"><strong>Engineer Name</strong></td>
					 <td width="10%"><strong>Replace <?php echo SERIALNO ?> </strong></td>
                  
					 <td width="10%"><strong>Remark</strong></td>
					  <td width="10%"><strong>Update Date</strong></td>
                  </tr>
                </thead>
                <tbody>
               <?php
				
				while($repair_info = mysqli_fetch_assoc($repair_history)){
				?>
                  <tr>
      
                   
                    <td><?=getAnyDetails($repair_info['repair_location'],"locationname","location_code","location_master",$link1);?></td>
					 <td><?=getAnyDetails($repair_info['fault_code'],"defect_desc","defect_code","defect_master",$link1);?></td>
                    <td><?=getAnyDetails($repair_info['repair_code'],"rep_desc","rep_code","repaircode_master",$link1);?></td>
                    <td><?=getAnyDetails($repair_info['partcode'],"part_name","partcode","partcode_master",$link1);?></td>                  
                    <td><?=getAnyDetails($repair_info['eng_id'],"locusername","userloginid","locationuser_master",$link1);?></td>
					<td><?=$repair_info['replace_imei1']?></td>
				
                    <td><?=$repair_info['remark']?></td>
					 <td><?=$repair_info['update_date']?></td>
                  </tr>
                  <?php
				}
				  ?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->


<?php }?>
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
                    <td><?=getAnyDetails($po_items['process_challan'],"sale_date","challan_no","billing_master",$link1);?></td>
                    <td><?=getAnyDetails($po_items['process_challan'],"challan_no","challan_no","billing_master",$link1);?></td>                  
                    <td><?=getAnyDetails($po_items['process_challan'],"docket_no","challan_no","billing_master",$link1);?></td>
					<td><?=getAnyDetails($po_items['process_challan'],"courier","challan_no","billing_master",$link1);?></td>
                  </tr>
                  <?php
				}
				  ?>	
             </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
 
  <?php } ?> 
  
 
	</form>

</div><!--close container-fluid-->
</body>
</html>