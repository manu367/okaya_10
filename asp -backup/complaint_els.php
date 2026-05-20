<?php
require_once("../includes/config.php");
/////get status//
error_reporting(0);
$job_sql="SELECT * FROM jobsheet_data where job_no='".$_REQUEST['job_no']."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
if($job_row['open_date']!='0000-00-00' && $job_row['dop']!='0000-00-00'){
 $days_diff = daysDifference($job_row['open_date'],$job_row['dop']);
}
if($job_row['brand_id']!=''){
  $brand_sql="SELECT make_doa,doa_days FROM brand_master where brand_id='".$job_row['brand_id']."'";
$brand_res=mysqli_query($link1,$brand_sql);
$brand_row=mysqli_fetch_assoc($brand_res);
}
 $make_doa=$brand_row['make_doa'];
 $doa_days=$brand_row['doa_days'];
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
		


   //////////////////////////////// Insert call  history//////////////////////////////////////
	  
	  /////////////////////////////////////Job Sheet data////////////////////////////////////////////////////////
		if($_POST['jobstatus'] == "9"){
		 $part_sql="SELECT partcode FROM partcode_master WHERE model_id='".$job_row['model_id']."' and part_category='BOX'";
		  	$part_res=mysqli_query($link1,$part_sql);
		  	$part_row=mysqli_fetch_assoc($part_res);
		 $part=	$part_row['partcode'];
		}
		else {
			if($part_row['partcode']!=''){
		$part=$part_row['partcode'];
			}
			else {
			$part="1";
			}
		}
		$status=$_REQUEST['status'];
		if($part!=''){
	  if($status=='Pass'){
		  /////Box model data find 
		if($_POST['jobstatus'] == "9"){ 
	 $st=",partcode='".$part_row['partcode']."',status='".$_POST['jobstatus']."',sub_status='91'";
		}
	
		
		
	
   $up_job=mysqli_query($link1,"update jobsheet_data set els_status='".$status."',els_rmk='$_POST[remarks]',els_eng_name='".$eng_name."',els_date='".$today."' ".$st." where job_no='".$_POST['job_no']."'" );

		if (!$up_job) { 
			 	$flag2 = false;
				$error_msg = "Error details2:Job update " . mysqli_error($link1) . ".";
			}
      $flag = callHistory($_POST[job_no],$_SESSION[asc_code],"","ELS","Pass By ASC",$_SESSION[asc_code],$ws,$_POST['remarks'],"","",$ip,$link1,$flag2);
		 
		  if (!$flag) { 
			 	$flag2 = false;
				$error_msg = "Error details2:Call history 1 " . mysqli_error($link1) . ".";
			}
		  if($_POST['jobstatus'] == "9"){

		$doa_inst = "INSERT INTO doa_data set job_no='".$_POST['job_no']."', location_code='".$_SESSION['asc_code']."', q1='".$_POST['q1']."', q2='".$_POST['q2']."', q3='".$_POST['q3']."', q4='".$_POST['q4']."', q5='".$_POST['q5']."', q6='".$_POST['q6']."', q7='".$_POST['q7']."'";
		$res_inst = mysqli_query($link1,$doa_inst);

			//// check if query is not executed
			if (!$res_inst) { 
			 	$flag2 = false;
				$error_msg = "Error details2:res_inst " . mysqli_error($link1) . ".";
			}
			$res_repdoa_data = mysqli_query($link1,"INSERT INTO repair_detail set job_id='".$job_row['job_id']."', job_no ='".$jobno."',imei='".$job_row['imei']."',sec_imei='".$job_row['sec_imei']."', repair_location='".$job_row['location_code']."', location_code='".$job_row['location_code']."', model_id='".$job_row['model_id']."', eng_id ='".$job_row['location_code']."', status='9', remark='".$_POST['rep_remark']."', fault_code='".$_POST['fault_code_doa']."', rep_lvl='0.00', part_repl='N', repair_code='".$_POST['repair_code_doa']."',browser='".$_SERVER['HTTP_USER_AGENT']."'");

			//// check if query is not executed
			if (!$res_repdoa_data) { 
			 	$flag2 = false;
				$error_msg = "Error details2:res_repdoa_data " . mysqli_error($link1) . ".";
			}
			///// entry in call/job  history
			if($flag2){  
		$flag2 = callHistory($_POST['job_no'],$_SESSION['asc_code'],'9',"DOA Request","DOA Request",$_SESSION['userid'],$job_row['warranty_status'],$_POST['rep_remark'],"","",$ip,$link1,$flag2);	
		
		   if (!$flag2) { 
			 	$flag2 = false;
				$error_msg = "Error details2:Call history 2" . mysqli_error($link1) . ".";
			}

		////// insert in activity table////
		$flag2 = dailyActivity($_SESSION['userid'],$_POST['job_no'],"DOA Request by ".$_SESSION['asc_code'],"DOA Request",$ip,$link1,$flag2);
		
			   if (!$flag2) { 
			 	$flag2 = false;
				$error_msg = "Error details2:dailyActivity 1" . mysqli_error($link1) . ".";
			}
			}
	}

	########## End DOA CASE
		  if($_POST['jobstatus'] == "17"){

			///// entry in call/job  history
		$flag = callHistory($jobno,$_SESSION['asc_code'],$_POST['jobstatus'],"DOA Decline","DOA Decline",$_SESSION['userid'],$job_row['warranty_status'],$_POST['rep_remark'],"","",$ip,$link1,$flag2);
		 if (!$flag) { 
			 	$flag2 = false;
				$error_msg = "Error details2:Call history 4" . mysqli_error($link1) . ".";
			}


		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$jobno,"DOA Decline by ".$_SESSION['asc_code'],"DOA Decline",$ip,$link1,$flag2);
			   if (!$flag) { 
			 	$flag2 = false;
				$error_msg = "Error details2:dailyActivity 5" . mysqli_error($link1) . ".";
			}

	}
   }
		else{
    $up_job=mysqli_query($link1,"update jobsheet_data set warranty_status='OUT', partcode='".$part_row['partcode']."',els_status='".$status."',els_rmk='$_POST[remarks]',els_eng_name='".$eng_name."',els_date='".$today."' where job_no='".$_POST['job_no']."'" );
			
   
    if (!$up_job) { 
			 	$flag2 = false;
				$error_msg = "Error details2:Update update else" . mysqli_error($link1) . ".";
			}
			
			  $flag = callHistory($_POST['job_no'],$_SESSION['asc_code'],"Fail","ELS","Fail By ASC",$_SESSION['asc_code'],$ws,$_POST['remarks'],"","",$ip,$link1,$flag2);
			 if (!$flag) { 
			 	$flag2 = false;
				$error_msg = "Error details2:dailyActivity y" . mysqli_error($link1) . ".";
			}

   }
		}////part model check for BOX
		else {
		$flag2 = false;
		$error_msg = "Complete Box partcode is not available  Part Master";
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
 header("location:job_list_asp.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);



 
 


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
      <h2 align="center"><i class="fa fa-list-alt"></i> Job ELS </h2>
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
                <td><label class="control-label">Contact No.</label><br/><span class="red_small">(For SMS Update)</span></td>
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
              <td><label class="control-label">Job For</label></td>
              <td><?=$job_row['call_for']?></td>
            </tr>
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
              <td width="49%"><label class="control-label">Els Status </label></td>
              <td width="51%"> <select   id="status" name="status" onChange="return change_els(this.value);" class="form-control" required ><option value="">Please Select</option>
           <option value="Pass" >Pass</option>
				  <option value="Fail" >Fail</option></select></td>
		   
            </tr>
				 <tr>
              <td width="49%"><label class="control-label">Els Engg </label></td>
              <td width="51%"> <select   id="eng_name" name="eng_name" class="form-control" required ><?php
				$qry_usr="Select * from locationuser_master where location_code = '".$_SESSION['asc_code']."' and statusid='1'";
$result_usr=mysqli_query($link1,$qry_usr);
		  while($arr_usr=mysqli_fetch_array($result_usr)){
		  ?>
                <option value="<?=$arr_usr['userloginid']?>" <?php if(!empty($_POST['eng_name'])==$arr_usr['userloginid']) echo " selected"?>>
                  <?=$arr_usr['locusername']?>
                  </option>
                <?php
		  }
		  ?>
				  </select></td>
            </tr>
			<tr>
              <td width="49%"><label class="control-label">Remarks </label></td>
              <td width="51%"> <input type="text" name="remarks" id="remarks" class="form-control" /></td>
		   
            </tr>  </tbody></table>
			<div id="repair_status" style="display:none;">
			<table class="table table-bordered" width="100%" >
            <tbody>
				  <tr >
				  
              <td width="49%"><label class="control-label">Repair Status </label></td>
              <td width="51%">
			  <select name="jobstatus" id="jobstatus" onChange="return change_stat(this.value);" class="form-control" style="width:250px;" >

                      <option value="" selected>--Select Repair Result--</option>

                      <?php
						    $select_st="'DOA','DOA Decline'";
							 $res_jobstatus = mysqli_query($link1,"select status_id,display_status from jobstatus_master where status_id=main_status_id and system_status in ('DOA','DOA Decline') order by display_status");

							 while($row_jobstatus = mysqli_fetch_assoc($res_jobstatus)){

							?>

                      <option value="<?=$row_jobstatus['status_id']?>" <?php if($job_result['status'] == $row_jobstatus['status_id']){echo 'selected';}?>>

                      <?=$row_jobstatus['display_status']?>

                      </option>

                      <?php

							 }

							 ?>

                    </select>
</td>
		   
            </tr>
				  </tbody></table></div><table class="table table-bordered" width="100%">
            <tbody>
				  <tr>
				  <td colspan="2">
					 
 <div class="form-group" id="doa_policy" style="display:none">

                



					  <div class="col-md-12"><label class="col-md-10 custom_label">Q1. Is the purchase proof available with IMEI No ' s? <span class="red_small">*</span></label>



                      <div class="col-md-2">

							<label for="radiobutton"><input name="q1" type="radio" value="Y" id="q1" class="required"/>

                            Yes

                              <input name="q1" type="radio" value="N" id="q1" class="required"  />

                              No </label>

                      </div>



                    </div>

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q2. Are IMEI and Serial No on the mobile same as that on the packing box? <span class="red_small">*</span></label>



                      <div class="col-md-2">

						 <label for="radiobutton"><input name="q2" type="radio" value="Y" id="q2" class="required" />

                           Yes

                              <input name="q2" type="radio" value="N" id="q2" class="required"/>

                              No</label>

                      </div>



                    </div>

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q3. The problem reported is not related to software which can be solved by upgrade software version. <span class="red_small">*</span></label>



                      <div class="col-md-2">

						<label for="radiobutton"><input name="q3" type="radio" value="Y" id="q3" class="required"/>

                            Yes

                              <input name="q3" type="radio" value="N" id="q3" class="required"/>

                              No </label>

                      </div>



                    </div>

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q4. The problem reported is not related to accessories. <span class="red_small">*</span></label>



                      <div class="col-md-2">

						<label for="radiobutton"><input name="q4" type="radio" value="Y" id="q4" class="required"/>

                            Yes

                              <input name="q4" type="radio" value="N" id="q4" class="required"/>

                              No </label>

                      </div>



                    </div>

                  <div class="col-md-12"><label class="col-md-10 custom_label">Q5. Unit does not have any physical damage, water damage/Water Liquid (Water detection label) or tampering on Handset. <span class="red_small">*</span></label>



                      <div class="col-md-2">

						<label for="radiobutton"><input name="q5" type="radio" value="Y" id="q5" class="required"/>

                            Yes

                              <input name="q5" type="radio" value="N" id="q5" class="required"/>

                              No </label>

                      </div>



                    </div>

                   <div class="col-md-12"><label class="col-md-10 custom_label">Q6. Is not a Cosmetic reject (E.g. : Scratches on phone, lens, dent, etc.).<span class="red_small">*</span></label>



                      <div class="col-md-2">

						<label for="radiobutton"><input name="q6" type="radio" value="Y" id="q6" class="required"/>

                            Yes

                              <input name="q6" type="radio" value="N" id="q6" class="required" />

                              No </label>

                      </div>



                    </div>

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q7. Is the unit complete sales package as mention in content of the box label sticker (Gift box, handset, user manual, hands free, charger, battery, software CD, data cable and memory card)?<span class="red_small">*</span></label>



                      <div class="col-md-2">

						<label for="radiobutton"><input name="q7" type="radio" value="Y" id="q7" class="required" />

                            Yes

                              <input name="q7" type="radio" value="N" id="q7" class="required" />

                              No </label>

                      </div>



                    </div>

                  

					  </div>

                

              

              

              <!--close form group--></td>
				  </tr>
            <tr>
                 <td align="center" colspan="2"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_asp.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="update els">  <input type="hidden" name="job_no"  id="job_no" value="<?=$job_row['job_no']?>" /></td>
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