<?php
require_once("../includes/config.php");

////get access ASP details
$docid=base64_decode($_REQUEST['refid']);
$access_asp = getAccessASP($_SESSION['asc_code'],$link1);
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);

////////////// update by jitender on dec 11 for repair and bounce type call for claim process ////////////////////////////////////////
$srch_criteria = "where customer_id = '".$job_row['customer_id']."'";
$sql_cust	= mysqli_query($link1, "select  *  from customer_master   ".$srch_criteria."   order by id desc");
$row_customer=mysqli_fetch_array($sql_cust);


////// final submit form ////
if($_POST['savejob']=='Update')
{
	barCheck($link1);
	@extract($_POST);
	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg="";

	############################################
	### image processor by Hemant - feb'2024 ###
	if(is_uploaded_file($_FILES['handset_img']['tmp_name']))
	{
		$ext = strtolower(pathinfo($_FILES["handset_img"]["name"], PATHINFO_EXTENSION));
		$allowed = array("png","jpg","jpeg","pdf");
		if(!in_array($ext, $allowed))
		{
			$flag = false;
			$error_msg = "Please upload a valid png, jpg or pdf file!";
		}
		else if($_FILES["handset_img"]["size"] > 3145728)
		{
			$flag = false;
			$error_msg = "Image/Pdf file size should be under 3 MB!";
		}
		else
		{
			$my = "../uploads/job/images/".date("Y-M");
			if(!is_dir($my)){
				$s = mkdir($my, 0777, 'R');
			}
			$file_name =$_FILES['handset_img']['name'];
			$file_tmp =$_FILES['handset_img']['tmp_name'];
			$targetPath = $my."/".time()."_".cleanFileName($file_name, 60);
			$img_upload = move_uploaded_file($file_tmp,$targetPath);
			if($img_upload)
			{
				$img_res = mysqli_query($link1,"SELECT sno,img_url FROM image_upload_details WHERE job_no='".$docid."' ORDER BY sno DESC LIMIT 1");
				if(mysqli_num_rows($img_res) > 0)
				{
					$img_row = mysqli_fetch_assoc($img_res);
					$result = mysqli_query($link1,"UPDATE image_upload_details SET img_url='".$targetPath."', upload_date='".$today."' WHERE sno='".$img_row['sno']."'");
				}
				else
				{
					$result = mysqli_query($link1,"INSERT INTO image_upload_details SET job_no ='".$docid."', img_url='".$targetPath."', upload_date='".$today."'");
				}
				mysqli_query($link1, "INSERT INTO upload_history SET doc_no='".$docid."', process='JOB DETAILS EDIT', file_url='".$targetPath."', create_by='".$_SESSION['userid']."', create_dt='".$datetime."', create_ip='".$ip."'");
			}
		}
	}
	############################################
	### video processor by Hemant - feb'2024 ###
	if(is_uploaded_file($_FILES['att_video']['tmp_name']))
	{			 
		$ext = strtolower(pathinfo($_FILES["att_video"]["name"], PATHINFO_EXTENSION));
		$allowed = array("mp4");
		if(!in_array($ext, $allowed))
		{
			$flag = false;
			$error_msg = "Please upload a valid mp4 file!";
		}
		else if($_FILES["att_video"]["size"] > 10485760)
		{
			$flag = false;
			$error_msg = "Video file size should be under 10 MB!";
		}
		else
		{
			$my = "../uploads/job/videos/".date("Y-M");
			if(!is_dir($my)){
				$s = mkdir($my, 0777, 'R');
			}
			$file_name =$_FILES['att_video']['name'];
			$file_tmp =$_FILES['att_video']['tmp_name'];
			$targetPath = $my."/".time()."_".cleanFileName($file_name, 60);
			$vid_upload = move_uploaded_file($file_tmp,$targetPath);
			if($vid_upload)
			{
				$vid_res = mysqli_query($link1,"SELECT sno,vid_url FROM image_upload_details where job_no='".$docid."' ORDER BY sno DESC LIMIT 1");
				if(mysqli_num_rows($vid_res) > 0)
				{
					$vid_row = mysqli_fetch_assoc($vid_res);
					$result = mysqli_query($link1,"UPDATE image_upload_details SET vid_url='".$targetPath."', upload_date='".$today."' WHERE sno='".$vid_row['sno']."'");
				}
				else
				{
					$result = mysqli_query($link1,"INSERT INTO image_upload_details SET job_no ='".$docid."', vid_url='".$targetPath."', upload_date='".$today."'");
				}
				mysqli_query($link1, "INSERT INTO upload_history SET doc_no='".$docid."', process='JOB DETAILS EDIT', file_url='".$targetPath."', create_by='".$_SESSION['userid']."', create_dt='".$datetime."', create_ip='".$ip."'");
			}
		}
	}
	############################################
	
	if($flag)
	{
		$usr_add="update customer_master set  customer_name='".$customer_name."', address1='".$address."', pincode='".$pincode."', cityid='".$locationcity."', stateid='".$locationstate."', email='".$email."',  phone='".$res_no."', mobile='".$phone1."', alt_mobile='".$phone2."', update_date='".$today."', update_by='".$_SESSION['asc_code']."',max_id='".$code_id."',landmark='".$landmark."',type='".$customer_type."',reg_name='".$reg_name."',gst_no='".$gst_no."' where customer_id='".$custo_id."'";
		$res_add=mysqli_query($link1,$usr_add); 
	}
	
	///// entry in job sheet data
	$modelsplit = explode("~",$modelid);
	if(is_array($voc2)){
		$array_voc2 = implode(",", $voc2);
	}
	///// model details
	if(is_array($acc_present)){
		$array_accprest = implode(",", $acc_present);
	}
	
	//$st="status='1', sub_status='1'";

	$sql_inst = "update jobsheet_data set   location_code='".$_SESSION['asc_code']."', city_id='".$locationcity."', state_id='".$locationstate."', pincode='".$pincode."', product_id='".$product_name."', brand_id='".$brand."', customer_type='".$customer_type."', model_id='".$modelsplit[0]."', partcode='".$modelpartcode['partcode']."', model='".$modelsplit[1]."', imei='".$imei_serial1."', warranty_status='".$warranty_status."',warranty_days='".$wsd."', dop='".$pop_date."', dname='".$dealer_name."', inv_no='".$invoice_no."',  call_type='".$call_type."', call_for='".$call_for."', customer_name='".$customer_name."',  contact_no='".$phone1."', alternate_no='".$phone2."', email='".$email."', address='".$address."', cust_problem='".$voc1."', cust_problem2='".$array_voc2."', cust_problem3='".$voc3."', phy_cond='".$physical_cond."', created_by='".$_SESSION['userid']."', remark='".$remark."' ,ip='".$ip."',current_location='".$rep_location."',entity_type='".$entity_type."',acc_rec='".$array_accprest."' where job_no='".$docid."'";

	$res_inst = mysqli_query($link1,$sql_inst);

	//// check if query is not executed

	if (!$res_inst) {

		$flag = false;

		$error_msg = "Error jobsheet : " . mysqli_error($link1) . ".";

	}

	$usr_add3="update product_registered set  product_id='".$product_name."', model_id='".$modelsplit[0]."', purchase_date='".$pop_date."', installation_date ='".$install_date."', warranty_end_date='".$warraty_date."', status='1',mobile_no='".$phone1."',brand_id='".$brand."',amc_no='".$amc_no."',amc_end_date='".$amc_exp_date."' where serial_no='".$pro_id."'";
	$res_add3=mysqli_query($link1,$usr_add3);



	///// entry in call/job  history

	$flag = callHistory($docid,$_SESSION['asc_code'],"1","Complaint Update","Infromation update",$_SESSION['userid'],$warranty_status,$remark,"","",$ip,$link1,$flag);

	////// insert in activity table////

	$flag = dailyActivity($_SESSION['userid'],$docid,"Complaint","Update",$_SERVER['REMOTE_ADDR'],$link1,$flag);

	///// check both master and data query are successfully executed

	if ($flag) {

		mysqli_commit($link1);

		////// return message

		$msg="You have successfully created a Job like ".$docid;

		$cflag="success";

		$cmsg="Success";

	} else {

		mysqli_rollback($link1);

		$cflag="danger";

		$cmsg="Failed";

		$msg = "Request could not be processed. Please try again. ".$error_msg;

	} 

	mysqli_close($link1);

	///// move to parent page
	if($phone1!=''){
		//$sms_msg="Dear ".$customer_name.", thanks for visiting our store. Job  No. ".$jobno." Phonup.";
	}



	header("location:job_list_edit.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&smsmsg=".$sms_msg."&mobileno=".$phone1."&customer_id=".$customer_id."&imei_serial=".$imei_serial1."&email_id=".$email."&status=1");
	//exit;

}

////// get model details if post model id from previous page

if($_REQUEST['p_modelcode'] || $_REQUEST['modelid']){

	if($_REQUEST['modelid']){

		$modelexpl = explode("~",$_REQUEST['modelid']);

		$model_code = $modelexpl[0];

		$model_name = $modelexpl[1];
		$model_wp = $modelexpl[2];

		$model_det = explode("~",getAnyDetails($modelexpl[0],"product_id,brand_id,model,make_doa,doa_days,out_warranty,wp","model_id","model_master",$link1));

	}else{



	}

}
//echo "SELECT * FROM product_registered  where serial_no='".$_REQUEST['imei_serial']."'";

$product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where serial_no='".$job_row['imei']."'"));






////// make voc array

$voc_arr = array();

$res_voc = mysqli_query($link1,"select voc_code, voc_desc from voc_master where 1");

while($row_voc = mysqli_fetch_assoc($res_voc)){

	$voc_arr[$row_voc['voc_code']] = $row_voc['voc_desc'];

}

//// calculate warranty
$model_det3 = explode("~",getAnyDetails($product_det['model_id'],"product_id,brand_id,model,make_doa,doa_days,out_warranty,wp","model_id","model_master",$link1));

if($product_det['purchase_date']!='' && $product_det['purchase_date']!='0000-00-00'){

	$days_diff = daysDifference($today,$product_det['purchase_date']);

	if($days_diff <= $model_det3[6]){

		$ws = "IN";

	}else{

		$ws = "OUT";

	}

}else{
	$ws = "";
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

	<?php if($_REQUEST['p_dop']!='' && $_REQUEST['p_dop']!='0000-00-00'){?>

    $(document).ready(function () {

	  $('#pop_date').attr('readonly', true);

	});

	<?php }else{?>

	$(document).ready(function () {

		$('#pop_date').datepicker({

			format: "yyyy-mm-dd",

			endDate: "<?=$today?>",

			todayHighlight: true,

			autoclose: true,

		}).on('changeDate', function(ev){

    		

			getWarranty();

		});
		
		$('#install_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
		$('#amc_exp_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});

	});

	<?php }?>

 </script>

 <script language="javascript" type="text/javascript">

 /////////// function to get city on the basis of state

 function get_citydiv(){

	  var name=$('#locationstate').val();

	  $.ajax({

	    type:'post',


		url:'../includes/getAzaxFields.php',

		data:{state:name},

		success:function(data){

	    $('#citydiv').html(data);

	    }

	  });

 }

 /////////////

 $(document).ready(function() {

	$('#example-multiple-selected1').multiselect({

			includeSelectAllOption: true,

			enableFiltering: true,

			buttonWidth:"200"

            //enableFiltering: true

	});

	$('#example-multiple-selected2').multiselect({

			includeSelectAllOption: true,

			enableFiltering: true,

			buttonWidth:"200"

            //enableFiltering: true

	});

 });

 /////////// function to get model on the basis of brand

  $(document).ready(function(){

	$('#brand').change(function(){

	  var brandid=$('#brand').val();
	 
	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{brand:brandid},

		success:function(data){

	    $('#modeldiv').html(data);

	    }

	  });

    });

  });
function getdate4() {


var start_date = new Date($('#pop_date').val());

 var model_wp =  document.getElementById('modelid').value;
 
 var modelsplit=model_wp.split("~");

  var end_date = new Date(start_date);


  end_date.setDate(start_date.getDate() +  parseInt(modelsplit[2]));  
   
  $datecc=end_date.getFullYear() + '-' + ("0" + (end_date.getMonth() + 1)).slice(-2) + '-' + ("0" + end_date.getDate()).slice(-2);
                     
  $("#warraty_date").val($datecc);
          // $('#warranty_date').val(date);
   // document.getElementById('warranty_date').value = someFormattedDate;
   getWarranty();
}
  /////////// function to check DOA is eligible or not

  

  /////////// function to get model on the basis of brand

  function getAccessory(){

	//$('#modelid').change(function(){

	  var model_id=$('#modelid').val();

	  var modelcode = model_id.split("~");
	  
	 

	//  var calltype=$('#call_type').val();
	  
	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{model:modelcode[0]},

		success:function(data){
		

	    $('#accdiv').html(data);

		$('#example-multiple-selected2').multiselect({

			includeSelectAllOption: true,

			enableFiltering: true,

			buttonWidth:"200"

	    });

	    }

	  });

    //});

  }

//// check warrantty on the basis of els status and pop selection

function getWarranty(){

	var sel_pop = $('#pop_date').val();

 var model_wp =  document.getElementById('modelid').value;
 
 var post_wsd=model_wp.split("~");

	//alert(post_wsd);

	////// check out warranty flag of this model

	if("<?=$model_det[5]?>" == "Y"){

		document.getElementById("warranty_status").value = "OUT";

		document.getElementById("errmsg").innerHTML = "You are making a job for OUT warranty model. <br/>";

	}else{

		/////calculate days

		var diffDays = date_difference("<?=$today?>", sel_pop);

		///// calculate warranty
	//alert(diffDays+""+post_wsd);
		if(diffDays <= post_wsd[2]){

			document.getElementById("warranty_status").value = "IN";

		
		}else{

			document.getElementById("warranty_status").value = "OUT";

		}

	}

}


	 function validateVid(nam,ind)
	 {
		 var err_msg="";
		 document.getElementById("errmsg"+ind).innerHTML = err_msg;
		 var img1=document.getElementById(nam).value;
		 var file=document.getElementById(nam).files[0];
		 var t = file.type.split('/').pop().toLowerCase();
		 if(t!="mp4")
		 {
			 err_msg = "<strong>Please select a valid mp4 file!<br/></strong>";
			 document.getElementById("errmsg"+ind).innerHTML = err_msg;
			 document.getElementById(nam).value = '';
			 return false;
		 }
		 else if(file.size > 10240000) //10 MB
		 {
			 err_msg = "<strong>File size should be under 10 MB.<br/></strong>";
			 document.getElementById("errmsg"+ind).innerHTML = err_msg;
			 document.getElementById(nam).value = '';
			 return false;
		 }
		 return true;
	 }
	 function validateImage(nam,ind)
	 {
		 var err_msg="";
		 document.getElementById("errmsg"+ind).innerHTML = err_msg;
		 var img1=document.getElementById("handset_img").value;
		 var file = document.getElementById(nam).files[0];
		 var t = file.type.split('/').pop().toLowerCase();
		 if(t != "jpeg" && t != "jpg" && t != "png" && t != "pdf")
		 {
			 err_msg = "<strong>Please select a valid jpg, png or pdf file!<br/></strong>";
			 document.getElementById("errmsg"+ind).innerHTML = err_msg;
			 document.getElementById(nam).value = '';
			 return false;
		 }
		 else if(file.size > 3072000)
		 {
			 err_msg = "<strong>File size should be under 3 MB.<br/></strong>";
			 document.getElementById("errmsg"+ind).innerHTML = err_msg;
			 document.getElementById(nam).value = '';
			 return false;
		 }
		 return true;
	 }

$(document).ready(function () {
	$('#pop_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
	

	


});
//// date difference

function date_difference(enddate,startdate){

	var end_date = (enddate).split("-");

	var start_date = (startdate).split("-");	

	var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds

	var firstDate = new Date(start_date[0], start_date[1], start_date[2]);

	var secondDate = new Date(end_date[0], end_date[1], end_date[2]);

	/////calculate days

	var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));

	return diffDays;

}

  </script>

 <script type="text/javascript" src="../js/jquery.validate.js"></script>

 <script type="text/javascript" src="../js/common_js.js"></script>

  <!-- Include Date Picker -->

 <link rel="stylesheet" href="../css/datepicker.css">

 <script src="../js/bootstrap-datepicker.js"></script>

 <!-- Include multiselect -->

 <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>

 <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>

 <style type="text/css">

 .custom_label {

	 text-align:left;

	 vertical-align:middle

 }

 </style>

<body >

<div class="container-fluid">

  <div class="row content">

	<?php 

   include("../includes/leftnav2.php");

    ?>

	  <div class="<?=$screenwidth?>">
		  <h2 align="center" style="border-bottom:1px solid #aaa8a8;padding:25px 0px;margin:0px;"><i class="fa fa-id-badge"></i> Enter Complaint Details - <?=$docid;?></h2>
		  <form  name="frm1" id="frm1" class="form-horizontal" enctype="multipart/form-data"  action="" method="post">

        <div class="panel-group">

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>

              <div class="panel-body">

              	  <div class="form-group">
              	    <div class="col-md-6">
              	      <label class="col-md-6 custom_label">Customer Category <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <select name="customer_type" id="customer_type" class="form-control required" required>
                         
                          <?php



				$cus_query="SELECT * FROM customer_type where status = '1' order by customer_type";



				$check_cust=mysqli_query($link1,$cus_query);

?>
 <option value="">--Please Select--</option>
			<?php	while($br_cust = mysqli_fetch_array($check_cust)){



				?>
                          <option value="<?=$br_cust['customer_type']?>"<?php if($row_customer['type']==$br_cust['customer_type']){ echo "selected";}?>><?php echo $br_cust['customer_type']?></option>
                          <?php }?>
                        </select>
                      </div>
           	        </div>
              	    <div class="col-md-6"><label class="col-md-6 custom_label">Assign Location <span class="red_small">*</span></label>

                      <div class="col-md-6">
 <select name="rep_location" id="rep_location" class="form-control required" required>
 
 
                        <option value="">--Please Select--</option>
                        <?php
                        $lctype_query="select location_code,locationname  from location_master where statusid='1'  order by locationname";
                        $check_lctype=mysqli_query($link1,$lctype_query);
                        while($br_lctype = mysqli_fetch_array($check_lctype)){
                        ?>
                        <option value="<?=$br_lctype['location_code']?>" <?php if($job_row["current_location"]==$br_lctype['location_code']){ echo "selected";}?>><?php echo $br_lctype['locationname']?></option>
                        <?php } ?>
						
						
						
                      </select>
                      </div>

                    </div>

                </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Name <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	<input name="customer_name" id="customer_name" type="text" value="<?=$row_customer['customer_name'];?>" class="form-control required" />
						<input name="custo_id" id="custo_id" type="hidden" value="<?=$row_customer['customer_id'];?>" class="form-control required"/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$row_customer['address1'];?></textarea>

                      </div>

                    </div>

                  </div>
				                    <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Landmark </label>

                      <div class="col-md-6">

                        	<input name="landmark" id="landmark" type="text" class="form-control " value="<?=$row_customer['landmark'];?>" /> 

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Pincode  <span class="red_small">*</span></label>

                      <div class="col-md-6">

                              <input name="pincode" type="text" class="digits form-control required"  maxlength="6" id="pincode" value="<?=$row_customer['pincode']?>" >

                      </div>

                    </div>

                  </div>

                   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No. <!--<span class="small">(For SMS Update)</span>--> <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?php if($row_customer['mobile']!=''){ echo $row_customer['mobile'];}else{ echo $_REQUEST['mobileno'];}?>" >

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No.</label>

                      <div class="col-md-6">

                      <input name="phone2" type="text" class="digits form-control " id="phone2" maxlength="10" value="<?=$row_customer['alt_mobile'];?>" >

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">State <span class="red_small">*</span></label>

                      <div class="col-md-6">

                         <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();" required >

                          <option value=''>--Please Select--</option>

                          <?php 

						 $state_query="select stateid, state from state_master where countryid='1' order by state";

						 $state_res=mysqli_query($link1,$state_query);

						 while($row_res = mysqli_fetch_array($state_res)){?>

						   <option value="<?=$row_res['stateid']?>"<?php if($row_customer['stateid']==$row_res['stateid']){ echo "selected";}?>><?=$row_res['state']?></option>

						 <?php }?> 	

                        </select>               

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Email</label>

                      <div class="col-md-6">

                          <input name="email" type="email" class="email form-control" id="email" value="<?=$row_customer['email'];?>" >

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">City <span class="red_small">*</span></label>

                        <div class="col-md-6" id="citydiv">

                       <select name="locationcity" id="locationcity" class="form-control required" required >

                       <option value=''>--Please Select-</option>

                       <?php 

					  

						 $city_query="SELECT cityid, city FROM city_master where stateid='".$row_customer['stateid']."' and cityid='".$row_customer['cityid']."'";

						 $city_res=mysqli_query($link1,$city_query);

						 while($row_city = mysqli_fetch_array($city_res)){

						?>

						<option value="<?=$row_city['cityid']?>"<?php if($row_customer['cityid']==$row_city['cityid']){ echo "selected";}?>><?=$row_city['city']?></option>

						<?php }

					

						?>

                       </select>

                      </div>

                    </div>

                   <div class="col-md-6"><label class="col-md-6 custom_label">Residence No.</label>

                      <div class="col-md-6">

                        <input name="res_no" type="text" class="digits form-control" id="res_no" value="<?=$row_customer['phone']?>" >

                      </div>

                    </div>

                  </div>
				  
 <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">GST No.</label>

                        <div class="col-md-6" id="citydiv">

                         <input name="gst_no" type="text" class=" form-control" id="gst_no" value="<?=$row_customer['gst_no']?>"  >
                      </div>

                    </div>

                   <div class="col-md-6"><label class="col-md-6 custom_label">Registration Name.</label>

                      <div class="col-md-6">

                        <input name="reg_name" type="text" class=" form-control" id="reg_name" value="<?=$row_customer['reg_name']?>" >

                      </div>

                    </div>

                  </div>
              </div>

            </div>

        

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>

              <div class="panel-body">

              	<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Product <span class="red_small">*</span></label>

                      <div class="col-md-6">

                         <select name="product_name" id="product_name" class="form-control required" required>
                          <option value=''>--Select Product--</option>
                        <?php $dept_query="SELECT * FROM product_master where status = '1'  order by product_name";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>
                                    
						  <option value="<?=$br_dept['product_id']?>"<?php if($product_det['product_id'] == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>

						<?php }?>
                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Brand <span class="red_small">*</span></label>

                      <div class="col-md-6">

                       <select name="brand" id="brand" class="form-control required" required>

                        
						    <option value=''>--Select Brand--</option>
                          <?php

						  	

							$dept_query="SELECT * FROM brand_master where status = '1'  order by brand";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>

						  <option value="<?=$br_dept['brand_id']?>"<?php if($product_det['brand_id'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>

						<?php }?>

                        </select>

                      </div>

                    </div>

                  </div>

              	<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Model <span class="red_small">*</span></label>

                      <div class="col-md-6" id="modeldiv">

                        <select name="modelid" id="modelid" class="form-control required" required>

                          <?php

						  	if($product_det['model_id']!=""){
							$model_det2 = explode("~",getAnyDetails($product_det['model_id'],"product_id,brand_id,model,make_doa,doa_days,out_warranty,wp","model_id","model_master",$link1));

						  ?>

                          <option value="<?=$product_det['model_id']."~".$model_det2['2'];?>"><?=$model_det2['2']?></option>

						  <?php

							}else if($_REQUEST['p_modelcode'] || $_REQUEST['modelid']){

						  ?>
                                        <option value=''>--Select Model--</option>
                          <option value="<?=$model_code."~".$model_name;?>"><?=$model_det[2]?></option>

                          <?php }else{?>

                          <option value=''>--Select Model--</option>

                          <?php } ?>

                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label"><?php echo SERIALNO ?> <span class="red_small">*</span></label>

                      <div class="col-md-6" >

	<input name="imei_serial1" id="imei_serial1" type="text" value="<?=$job_row['imei']?>" class="form-control required " required />
                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Bill Purchase Date</label>

                      <div class="col-md-6">

                      <div style="display:inline-block;float:left;"><input type="text" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="<?php if($product_det['purchase_date']!=''){ echo $product_det['purchase_date'];?>  <?php }else{ echo "";}?>"   onChange="getdate4();"></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Warranty End Date</label>

                      <div class="col-md-6">

                       <input name="warraty_date" id="warraty_date" type="text" value="<?=$product_det['warranty_end_date']?>"  class="form-control" readonly/>

                      </div>

                    </div>

                  </div>


                  <div class="form-group">
						<div class="col-md-6"><label class="col-md-6 custom_label">Date Of Installation</label>

                      <div class="col-md-6">

            
  <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="install_date"  id="install_date" style="width:150px;" value="<?=$product_det['installation_date'];?>"  <?php if($product_det['installation_date']!=''){?> readonly <?php }else{}?> ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                      </div>
                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Call Type <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <select name="call_for" id="call_for" class="form-control required" required>

                          <option value='' <?php if($job_row['call_for'] == ""){ echo "selected";}?>>--Select Call Type--</option>	
						   <option value='Repair'<?php if($job_row['call_for'] == "Repair"){ echo "selected";}?>>Repair</option>
                          <option value='Installation' <?php if($job_row['call_for'] =="Installation"){ echo "selected";}?>>Installation</option>
						  <option value='Demo' <?php if($job_row['call_for'] == "Demo"){ echo "selected";}?>>Demo</option>
						  <option value='Workshop' <?php if($job_row['call_for'] == "Workshop"){ echo "selected";}?>>Workshop</option>

                        </select>

                      </div>

                    </div>

                  </div>

                 <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">AMC Number</label>

                      <div class="col-md-6">
						 <input name="amc_no" id="amc_no" type="text" value="<?=$product_det['amc_no']?>"  class="form-control"  <?php if($product_det['amc_no']!=''){?> readonly <?php }else{}?>/>

                      </div>

                    </div>


                    <div class="col-md-6"><label class="col-md-6 custom_label">AMC Expiry Date </label>

                      <div class="col-md-6">

                      
					     <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="amc_exp_date"  id="amc_exp_date" style="width:150px;" value="<?=$product_det['amc_end_date'];?>"  <?php if($product_det['amc_end_date']!=''){?> readonly <?php }else{}?> ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                      </div>

                    </div>

                  </div> 
				  
				                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Warranty status<span class="red_small">*</span></label>

                      <div class="col-md-6">
						 <input name="warranty_status" id="warranty_status" type="text" value="<?=$ws?>" class="form-control " readonly/>
                      </div>

                    </div>


                    <div class="col-md-6"><label class="col-md-6 custom_label">Dealer Name </label>

                      <div class="col-md-6">

                         	<input name="dealer_name" id="dealer_name" type="text" value="<?=$job_row['dname']?>" class="form-control"/>

                      </div>

                    </div>

                  </div> 
				   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Invoice No </label>

                      <div class="col-md-6">

                      <input name="invoice_no" id="invoice_no" type="text" value="<?=$job_row['inv_no']?>" class="form-control"/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Escalations From</label>

                      <div class="col-md-6">

                       <select name="call_type" id="call_type" class="form-control required" required>

                          <option value='' <?php if($job_row['call_type'] == ""){ echo "selected";}?>>--Select --</option>	
						   <option value='Presidential' <?php if($job_row['call_type'] == "Presidential"){ echo "selected";}?>>Presidential</option>
                          <option value='Social Media' <?php if($job_row['call_type'] == "Social Media"){ echo "selected";}?>>Social Media</option>
						  <option value='Web' <?php if($job_row['call_type'] == "Web"){ echo "selected";}?>>Web</option>
						  <option value='Call Center' <?php if($job_row['call_type'] == "Call Center"){ echo "selected";}?>>Call Center</option>
						  <option value='SMS Feedback'  <?php if($job_row['call_type'] == "SMS Feedback"){ echo "selected";}?>>SMS Feedback</option>

                        </select>

                      </div>

                    </div>

                  </div> 
				  
				  	   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Entity Name </label>

                      <div class="col-md-6">

                        <select name="entity_type" id="entity_type" class="form-control required" required>
                          <option value="">--Please Select--</option>
                          <?php



				$enty_query="SELECT * FROM entity_type where status_id = '1' order by name";



				$check_enty=mysqli_query($link1,$enty_query);



				while($br_entity = mysqli_fetch_array($check_enty)){



				?>
                          <option value="<?=$br_entity['id']?>"<?php if($job_row['entity_type']==$br_entity['id']){ echo "selected";}?>><?php echo $br_entity['name']?></option>
                          <?php }?>
                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Accessory Change Required</label>

                      <div class="col-md-6" id="accdiv">

                       <select name="acc_present[]" id="example-multiple-selected2" multiple="multiple" class="form-control">
						<?php 
$acc= explode(",",$job_row['acc_rec']); 
			           $vocpresent   = count($acc);
					   if($vocpresent == '1'){
					   $name = getAnyDetails($acc[0],"part_name","partcode","partcode_master",$link1 );?>
					   	<option value="<?=$br_acc['part_name']?>"><?php echo $name?></option>
					   <?php }
					   else if($vocpresent >1){
					     $name ='';
					   for($i=0 ; $i<$vocpresent; $i++){					 
			 			$name=  getAnyDetails($acc[$i],"part_name","partcode","partcode_master",$link1 ).",";
						?>
						<option value="<?=$br_acc['part_name']?>"><?php echo $name?></option>
			 		<?php	}}?>
							
                        </select>
                      </div>

                    </div>

                  </div> 

              </div>

            </div>
            

            
            
            
            
            
            

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>

              <div class="panel-body">



				  <div class="form-group">
					  <div class="col-md-12">
						  <div class="col-md-3">
							  <label class="custom_label">VOC <span class="red_small">*</span></label>
						  </div>
						  <div class="col-md-9">
							  <div class="col-md-4" style="padding:0px !important;">
								  <select name="voc1" id="voc1" class="form-control required" required>
									  <option value=''>--Select VOC 1--</option>
									  <?php
									  foreach($voc_arr as $key => $value)
									  {
									  ?>
									  <option value="<?=$key?>"<?php if($job_row['cust_problem']==$key){ echo "selected";}?>><?=$value?></option>
									  <?php
										}
									  ?>
								  </select>
							  </div>
							  <div class="col-md-4">
								  <select name="voc2[]" id="example-multiple-selected1" multiple="multiple" class="form-control">
									  <?php
									  foreach($voc_arr as $key => $value){
									  ?>
									  <option value="<?=$key?>"><?=$value?></option>
									  <?php
										  }
									  ?>
								  </select>
							  </div>
							  <div class="col-md-4" style="padding:0px !important;">
								  <input name="voc3" id="voc3" type="text"  class="form-control" value="<?=$job_row['cust_problem3']?>"  placeholder="Enter Other VOC"/>
							  </div>
						  </div>
					  </div>
				  </div>
				  
				  <div class="form-group">
					  <div class="col-md-12">
						  <div class="col-md-3">
							  <label class="custom_label">Remark</label>
						  </div>
						  <div class="col-md-9">
							  <textarea name="remark" id="remark"  class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:none"><?=$job_row['remark']?></textarea>
						  </div>
					  </div>
				  </div>
				  <?php
					$image_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT img_url, vid_url FROM image_upload_details WHERE job_no='".$job_row['job_no']."'"));
				  ?>
				  <!------------- Start Image Uploder --------------->
				  <div class="form-group">
					  <div class="col-md-12">
						  <div class="col-md-3">
							  <label class="custom_label">Image Attachement<br><span style="color:#ff5f5f;font-weight:normal;">( png, jpg, pdf | max: 3 MB )</span></label>
						  </div>
						  <div class="col-md-5" style="background:#f0f0f0;padding:15px;margin:0px 15px;">
							  
							  <input type="file" name="handset_img" id="handset_img" onChange="return validateImage('handset_img','0');" class="form-control "  accept=".png,.jpg,.jpeg" style="margin-bottom:10px;">
							  <span id="errmsg0" class="red_small"></span>
							  <?php
							  if($image_det['img_url'] != "")
							  {
							  ?>
							  <hr style="border:1px solid #cecece;">
							  <div style="max-width:100%;margin:0px auto;">
								  <img src="<?=$image_det['img_url']?>" alt="Smiley face" style="display:block;max-width:inherit;margin:0px auto">
								  <button type="button" class="btn btn-primary" name="" id="" onclick= "window.open('<?=$image_det['img_url']?>', '_blank');" title="View" style="width:100%;margin-top:5px;background:#33b767;border-color:#149b49;"><i class="fa fa-external-link" style="color:#fff;" aria-hidden="true"></i> View</button>
							  </div>
							  <?php
								 }
							  ?>
						  </div>
					  </div>
				  </div>
				  <div class="form-group">
					  <div class="col-md-12">
						  <div class="col-md-3">
							  <label class="custom_label">Video Attachement <br><span style="color:#ff5f5f;font-weight:normal;">( mp4 | max: 10MB )</span></label>
						  </div>
						  <div class="col-md-5" style="background:#f0f0f0;padding:15px;margin:0px 15px;">
							  <input type="file" name="att_video" id="att_video" onChange="return validateVid('att_video','AV');" class="form-control" accept=".mp4" style="margin-bottom:10px;">
							  <span id="errmsgAV" class="red_small"></span>
							  <?php
							  if($image_det['vid_url'] != "")
							  {
							  ?>
							  <hr style="border:1px solid #cecece;">
							  <video width="720" height="240" controls style="max-width:100%;margin:0px auto;background:#000;">
								  <source src="<?=$image_det['vid_url']?>" type="video/mp4">
								  Your browser does not support the video tag.
							  </video>
							  <button type="button" class="btn btn-primary" name="" id="" onclick= "window.open('<?=$image_det['vid_url']?>', '_blank');" title="View" style="width:100%;background:#33b767;border-color:#149b49;"><i class="fa fa-external-link" style="color:#fff;" aria-hidden="true"></i> View</button>
							  <?php
								  }
							  ?>
						  </div>
					  </div>
				  </div>
				  <!------------- End Image Uploder --------------->
				  <div class="form-group">
					  <div class="col-md-12" align="center">
						  <span id="errmsg" class="red_small"></span>
						  <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list_edit.php?<?=$pagenav?>'">&nbsp;
						  <input name="wsd" id="wsd" value="<?=$_REQUEST['p_wsd'];?>" type="hidden"/>
						  <input name="pro_id" id="pro_id" value="<?=$product_det['id'];?>" type="hidden"/>
						  <input name="day_diff" id="day_diff" value="<?=$days_diference;?>" type="hidden"/>
						  <input name="symptom" id="symptom" value="<?=$count['symp_code']?>" type="hidden"/>
						  <input type="submit" class="btn<?=$btncolor?>" name="savejob" id="savejob" value="Update" title="Save Job Details" <?php if($_POST['savejob']=='Update'){?>disabled<?php }?>>&nbsp;
					  </div>
				  </div> 

              </div>

            </div><!-- end panal-->

        </div><!-- end panal group-->

        </form>

    </div><!--End col-sm-9-->

  </div><!--End row content-->

</div><!--End container fluid-->

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>