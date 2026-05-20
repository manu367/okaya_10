<?php
require_once("../includes/config.php");
/////get status//
error_reporting(0);
$today=date("Y-m-d",$time_zone);
$docid=base64_decode($_REQUEST['refid']);
echo $job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);


///error_reporting(E_ALL);
@extract($_POST);
////// case 1. if we want to update details

////// case 2. if we want to Add new user
if($_POST){
	//// initialize transaction parameters

    if ($_POST['upd']=='Update'){
    /////////  checking  sfr Transaction////////////////////////////
		$flag2=true;
		mysqli_autocommit($link1, false);
		$error_msg=""; 

   $up_job=mysqli_query($link1,"update jobsheet_data set status='18',els_rmk='$_POST[remarks]',els_date='".$today."' ".$st." where job_no='".$_POST['job_no']."'" );

		if (!$up_job) { 
			 	$flag2 = false;
				$error_msg = "Error details2:Job update " . mysqli_error($link1) . ".";
			}
      $flag = callHistory($_POST[job_no],$_SESSION[asc_code],"","Device Received at ASP","ASP Device Received",$_SESSION[asc_code],$ws,$_POST['remarks'],"","",$ip,$link1,$flag2);
		 
		  if (!$flag) { 
			 	$flag2 = false;
				$error_msg = "Error details2:Call history 1 " . mysqli_error($link1) . ".";
			}
		
	
   
	
		}////part model check for BOX
		else {
		$flag2 = false;
		$error_msg = "Something Wrong Happened";
		}
   if ($flag2) {
                        mysqli_commit($link1);
                        $msg = "Successfully done with ref. no. " . $_POST['job_no'];
						$cflag = "success";
						$cmsg = "Success";
                    }
		else {
		mysqli_rollback($link1);

		$cflag = "danger";

		$cmsg = "Failed";

		$msg = "Request could not be processed. Please try again. ".$error_msg;
		}
 header("location:job_list_pickup.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

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
$(document).ready(function () {
	$('#release_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});
	 function change_els(val){
		 
	let datediff = "<?php echo $days_diff; ?>";
		/// console.log(datediff);
	let make_doa = "<?php echo $make_doa?>";
		 	// console.log(make_doa);
	let doa_days = "<?php echo $doa_days?>";
		// console.log(doa_days);
	if(val=='Pass' && make_doa=="Y" && doa_days >= datediff ){
		 
		document.getElementById("repair_status").style.display = "";
		
		
	}
	else {
	
	document.getElementById("repair_status").style.display = "none";
	 
	}
}
function change_stat(val){
	
	if(val=='9'){
		document.getElementById("doa_policy").style.display="none";
		//document.getElementById("SFR").style.display="none";
	}
	else {
	document.getElementById("doa_policy").style.display="none";
	}
}
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
		$cust_det = explode("~",getAnyDetails($job_row['customer_id'],"customer_id,landmark,email,phone","customer_id","customer_master",$link1));
    include("../includes/leftnavemp2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-list-alt"></i>Device Deposit Confrim</h2>
      <h4 align="center">Job No.- <?=$_REQUEST['job_no']?></h4>
   <div class="panel-group">
     <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
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
                <td><label class="control-label">Contact No.</label><br/></td>
                <td><?php echo $job_row['contact_no'];?></td>
                <td><label class="control-label">Alternate Contact No.</label></td>
                <td><?php echo $job_row['alternate_no'];?></td>
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
              <td><label class="control-label">Warranty Status</label></td>
              <td><?php echo $job_row['warranty_status'];?>   <input type="hidden" name="warranty_status"  id="warranty_status" value="<?=$job_row['warranty_status']?>" /></td>
            </tr>
            <tr>
              <td><label class="control-label"><?php echo SERIALNO ?></label></td>
              <td><?=$job_row['imei']?></td>
              <td><label class="control-label">VOC</label></td>
              <td><?php echo getAnyDetails($job_row["cust_problem"],"voc_desc","voc_code","voc_master",$link1);?><br/><?php 	$voc= explode(",",$job_row['cust_problem2']); 
			           $vocpresent   = count($voc);
					   if($vocpresent == '1'){
					   $name = getAnyDetails($voc[0],"voc_desc","voc_code","voc_master",$link1 );
					   }
					   else if($vocpresent >1){
					     $name ='';
					   for($i=0 ; $i<$vocpresent; $i++){					 
			 			$name.=  getAnyDetails($voc[$i],"voc_desc","voc_code","voc_master",$link1 ).",";
			 			}} echo $name;?><br/><?=$job_row['cust_problem3']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Job Type</label></td>
              <td><?=$job_row['call_type']?></td>
              <td bgcolor="#CCCC00"><label class="control-label">Job For</label></td>
              <td bgcolor="#CCCC00"><?=$job_row['call_for']?></td>
            </tr>
            	<?php 
			$image_det = mysqli_query($link1,"SELECT * FROM image_upload_details  where job_no='".$docid."'");
			 while($row_image=mysqli_fetch_array($image_det)){?>  <tr>
              <td><label class="control-label"><?=$row_image['activity']?></label></td>
              <td colspan="3"  ><?php if ($row_image['img_url']!=""){?><span> <img src="<?=$row_image['img_url']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php } if($row_image['img_url1']!="") {?><span> <img src="<?=$row_image['img_url1']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span> <?php } if($row_image['img_url2']!="") {?><span> <img src="<?=$row_image['img_url2']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php } if($row_image['img_url3']!="") {?><span> <img src="<?=$row_image['img_url3']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php } if($row_image['img_url4']!="") {?><span> <img src="<?=$row_image['img_url4']?>" onMouseOver="bigImg(this)" onMouseOut="normalImg(this)" alt="Smiley face" height="100" width="100"></span><?php }?></td>
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
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Acknowledegment</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
              <td width="49%"><label class="control-label"> Status </label></td>
              <td width="51%"> <select   id="status" name="status"  class="form-control" required >
           <option value="Received" >Received</option>
				  </select></td>
		   
            </tr>
			<tr>
			  <td width="49%"><label class="control-label">Remarks </label></td>
			  <td width="51%"> <input type="text" name="remarks" id="remarks" class="form-control" /></td>
			  
			  </tr>  </tbody></table>
			<div id="repair_status" ></div><table class="table table-bordered" width="100%">
            <tbody>
				
            <tr>
                 <td align="center" colspan="2"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_pickup.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="update els">  <input type="text" name="job_no"  id="job_no" value="<?=$docid?>" /></td>
               </tr>
            </tbody>
          </table>
		  
      </div><!--close panel body-->
    </div><!--close panel-->
 </form>
  </div><!--close panel group-->
 </div><!--close col-sm-9-->

  </div><!--close panel group-->
 </div><!--close col-sm-9-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>