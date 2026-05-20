<?php

require_once("../includes/config.php");
if($_POST['makejob']=='Make Repair Job'){	
////////////// update by priya on july 2 for repair and bounce type call for claim process ////////////////////////////////////////
$sql	= mysqli_query($link1, "select  hand_date,symp_code from jobsheet_data  where ((imei = '".$_REQUEST['imei_serial']."'  and sec_imei = '".$_REQUEST['imei_serial2']."' )  or (imei = '".$_REQUEST['imei_serial2']."'  and sec_imei = '".$_REQUEST['imei_serial']."' )) order by job_id desc");
	if(mysqli_num_rows($sql) >0){
	$count	= mysqli_fetch_array($sql);
		$days_diference = daysDifference($today,$count['hand_date']);

	}
	$call_type_val="Normal";
	
}else if($_POST['makedoa']=='Make DOA Job'){
	$call_type_val="DOA";
}else if($_POST['makerepl']=='Make Replacement Job'){
	$call_type_val="Replacement";
}else{
	$call_type_val="Normal";
}

////// final submit form ////

if($_POST['savejob']=='Save'){


	@extract($_POST);

	//// initialize transaction parameters

	$flag = true;

	mysqli_autocommit($link1, false);

	$error_msg="";
	
	///////////////   update by priya on 2 july for repeat and repair ///////////////////////////////////////////
	if($_POST['day_diff'] != '' && $_POST['day_diff'] <=90 && $_POST['symptom'] == $_POST['initial_symp'])
		{
	
			$call_detail="Repair Repeat";
		}
		else if ($_POST['day_diff'] != '' && $_POST['day_diff']  <= 90 && $_POST['symptom'] != $_POST['initial_symp']) {
			$call_detail="Bounce";			
			}
			else{
			$call_detail=$_POST['call_type'];				
				}		
		
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//// pick max count of job

	$res_jobcount = mysqli_query($link1,"SELECT job_count from job_counter where location_code='".$_SESSION['asc_code']."'");

	$row_jobcount = mysqli_fetch_assoc($res_jobcount);

	///// make job sequence

	$nextjobno = $row_jobcount['job_count'] + 1;

	$jobno = $_SESSION['userid']."".str_pad($nextjobno,4,0,STR_PAD_LEFT);

	//// first update the job count

	$res_upd = mysqli_query($link1,"UPDATE job_counter set job_count='".$nextjobno."' where location_code='".$_SESSION['asc_code']."'");

	//// check if query is not executed

	if (!$res_upd) {

		 $flag = false;

		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";

	}

	///// entry in job sheet data

	$modelsplit = explode("~",$modelid);
if(is_array($voc2)){
	$array_voc2 = implode(",", $voc2);
}
if(is_array($acc_present)){
	$array_accprest = implode(",", $acc_present);
}	///// model details


	//// image upload//////////
	//$folder1="imei_image";
	$folder1="handset_image";
	if(($_FILES['handset_img']["name"]!='') && ($_FILES["handset_img"]["size"] < 2000000)){
		 $file_name =$_FILES['handset_img']['name'];
		 $file_tmp =$_FILES['handset_img']['tmp_name'];
		  $file_path="../".$folder1."/".time()."INV".$file_name;
		 $img_upld1 = move_uploaded_file($file_tmp,$file_path);
		 
		 if($img_upld1 != ""){
		//echo "INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'";
		 	$result = mysqli_query($link1,"INSERT INTO image_upload_details set job_no ='".$jobno."', activity='JOB CREATE',img_url='".$file_path."', upload_date='".$today."',location_code='".$_SESSION['asc_code']."'");
	  		//// check if query is not executed
		 	if (!$result) {
				$flag = false;
				$error_msg = "Image Upload Problem: " . mysqli_error($link1) . ".";
			}
		 }else{
		 	$flag = false;
			$error_msg = "Invoice Image can not upload on server due to size or image type issue " . mysqli_error($link1) . ".";
		 }
	}

	$modelpartcode = mysqli_fetch_assoc(mysqli_query($link1,"select partcode from partcode_master where model_id='".$modelsplit[0]."' and part_category='UNIT'"));
    if($call_type=="DOA"){
		$st="status='9', sub_status='91'";
		$doa_inst = "INSERT INTO doa_data set job_no='".$jobno."', location_code='".$_SESSION['asc_code']."', q1='".$q1."', q2='".$q2."', q3='".$q3."', q4='".$q4."', q5='".$q5."', q6='".$q6."', q7='".$q7."'";

		$res_inst = mysqli_query($link1,$doa_inst);
	}else{
		$st="status='1', sub_status='1'";
	}
	   $sql_inst = "INSERT INTO jobsheet_data set job_no='".$jobno."', system_date='".$today."', location_code='".$_SESSION['asc_code']."', city_id='".$locationcity."', state_id='".$locationstate."', pincode='".$pincode."', product_id='".$product_name."', brand_id='".$brand."', customer_type='".$customer_type."', model_id='".$modelsplit[0]."', partcode='".$modelpartcode['partcode']."', model='".$modelsplit[1]."', imei='".$imei_serial1."', sec_imei='".$imei_serial2."', th_imei='', sno='', open_date='".$today."', open_time='".$currtime."', warranty_status='".$warranty_status."',warranty_days='".$wsd."', dop='".$pop_date."', activation='".$activation_date."', dname='".$dealer_name."', inv_no='".$invoice_no."',  call_type='".$call_detail."', call_for='".$call_for."', customer_name='".$customer_name."',  contact_no='".$phone1."', alternate_no='".$phone2."', email='".$email."', address='".$address."', cust_problem='".$voc1."', cust_problem2='".$array_voc2."', cust_problem3='".$voc3."', phy_cond='".$physical_cond."',  symp_code ='".$initial_symp."', acc_rec='".$array_accprest."', els_status ='".$els_status."', created_by='".$_SESSION['userid']."', remark='".$remark."', ".$st." ,ip='".$ip."',ticket_no='".base64_decode($ticketno)."',current_location='".$_SESSION['asc_code']."'";

	$res_inst = mysqli_query($link1,$sql_inst);
	
	//// check if query is not executed

	if (!$res_inst) {

		 $flag = false;

		 $error_msg = "Error jobsheet : " . mysqli_error($link1) . ".";

	}

	//// if job is being created against ticket

	if(base64_decode($ticketno)!=''){

		//// first update the job no. in ticket master

		$res_upd = mysqli_query($link1,"UPDATE ticket_master set job_no='".$jobno."' where ticket_no='".base64_decode($ticketno)."'");

		//// check if query is not executed

		if (!$res_upd) {

			 $flag = false;

			 $error_msg = "Error details3: " . mysqli_error($link1) . ".";

		}

	}

	///// entry in call/job  history

	$flag = callHistory($jobno,$_SESSION['asc_code'],"1","Job Create","Job Create",$_SESSION['userid'],$warranty_status,$remark,$ip,$link1,$flag);

	////// insert in activity table////

	$flag = dailyActivity($_SESSION['userid'],$jobno,"JOB","CREATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);

	///// check both master and data query are successfully executed

	if ($flag) {

		mysqli_commit($link1);

		////// return message

		$msg="You have successfully created a Job like ".$jobno;

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
      $sms_msg="Dear ".$customer_name.", thanks for contacting us. Your Service Request no. is ".$jobno." Thanks Team CRM.";
}
  header("location:job_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&smsmsg=".$sms_msg."&to=".$phone1."&status=1");
	//exit;

}

////// get model details if post model id from previous page

if($_REQUEST['p_modelcode'] || $_REQUEST['modelid']){

	if($_REQUEST['modelid']){

		$modelexpl = explode("~",$_REQUEST['modelid']);

		$model_code = $modelexpl[0];

		$model_name = $modelexpl[1];

		$model_det = explode("~",getAnyDetails($modelexpl[0],"product_id,brand_id,model,make_doa,doa_days,out_warranty,wp","model_id","model_master",$link1));

	}else{

		$model_code = $_REQUEST['p_modelcode'];

		$model_det = explode("~",getAnyDetails($_REQUEST['p_modelcode'],"product_id,brand_id,model,make_doa,doa_days,out_warranty,wp","model_id","model_master",$link1));

		$model_name = $model_det[2];

	}

}

/////// check if request raised from ticket page

if(base64_decode($_REQUEST['ticket_no'])){

	$ticket_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM ticket_master where ticket_no='".base64_decode($_REQUEST['ticket_no'])."'"));

}else{

}

if($ticket_det['product_id']!="" && $ticket_det['product_id']!=0){ $sel_product=$ticket_det['product_id'];}else{$sel_product=$model_det[0];}

if($ticket_det['brand_id']!="" && $ticket_det['brand_id']!=0){ $sel_brand=$ticket_det['brand_id'];}else{$sel_brand=$model_det[1];}

////// make voc array

$voc_arr = array();

$res_voc = mysqli_query($link1,"select voc_code, voc_desc from voc_master where  product_id='".$sel_product."'");

while($row_voc = mysqli_fetch_assoc($res_voc)){

	$voc_arr[$row_voc['voc_code']] = $row_voc['voc_desc'];

}

//// calculate warranty

if($_REQUEST['p_activation']!='' && $_REQUEST['p_activation']!='0000-00-00'){

	$days_diff = daysDifference($today,$_REQUEST['p_activation']);

	if($days_diff <= $_REQUEST['p_wsd']){

		$ws = "IN";

	}else{

		$ws = "OUT";

	}

}else{
	$ws = $_REQUEST['job_warr'];
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

    		checkJobType();

			getWarranty();

		})

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

  /////////// function to check DOA is eligible or not

  function checkJobType(){

	  var calltype = $('#call_type').val();

	  if(calltype == "DOA"){
		document.getElementById("doa_policy").style.display=""; 
		  ////// check DOA is eligible for this model or not

		  if("<?=$model_det[3]?>" == "Y"){

			  ///// check DOA warranty days

			  var sel_pop = $('#pop_date').val();

			  var diffDays = date_difference("<?=$today?>", sel_pop);

			  var doaDays = "<?=$model_det[4]?>";

			  if(parseInt(diffDays) <= parseInt(doaDays)){

			  	document.getElementById("errmsg").innerHTML = "";

			  	document.getElementById("savejob").style.display="";

			  }else{
				
				document.getElementById("errmsg").innerHTML = "DOA warranty days are exceeding for this model. <br/>";

			    document.getElementById("savejob").style.display="none";  

			  }

		  }else{

			  document.getElementById("errmsg").innerHTML = "This model is not eligible for DOA. <br/>";

			  document.getElementById("savejob").style.display="none";

		  }

	  }else{
		document.getElementById("doa_policy").style.display="none"; 
		  document.getElementById("errmsg").innerHTML = "";

		  document.getElementById("savejob").style.display="";

	  }

  }

  /////////// function to get model on the basis of brand

  function getAccessory(){

	//$('#modelid').change(function(){

	  var model_id=$('#modelid').val();

	  var modelcode = model_id.split("~");

	  var calltype=$('#call_type').val();
	  
	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{model:modelcode[0],call_typ:calltype},

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

	var sel_elstatus = $('#els_status').val();

	var post_wsd = "<?=$model_det[6]?>";
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
		if(diffDays <= post_wsd){

			document.getElementById("warranty_status").value = "IN";

			if(sel_elstatus != "OK"){

				document.getElementById("warranty_status").value = "VOID";

			}

		}else{

			document.getElementById("warranty_status").value = "OUT";

		}

	}

}


 function validateImage(nam,ind) {
	var err_msg="";
	var img1=document.getElementById("handset_img").value;
	
    var file = document.getElementById(nam).files[0];
    var t = file.type.split('/').pop().toLowerCase();
	
    if(t != "jpeg" && t != "jpg" && t != "png" && t != "bmp" && t != "gif") {
		err_msg = "<strong>Please select a valid file. <br/></strong>";
		document.getElementById("errmsg"+ind).innerHTML = err_msg;
		document.getElementById(nam).value = '';
        return false;
    }else if(file.size > 2048000){  /**** 204800 ***/
		err_msg = "<strong>Max file size can be 2 MB.<br/></strong>";
		document.getElementById("errmsg"+ind).innerHTML = err_msg;
		document.getElementById(nam).value = '';
        return false;
    }else{
		document.getElementById("errmsg"+ind).innerHTML ="";
	}
    
	return true;
}

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

<body <?php if($_REQUEST['p_modelcode'] || $_REQUEST['modelid'] || $ticket_det['ticket_no']!=''){ ?> onLoad="getAccessory();"<?php }?>>

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnavemp2.php");

    ?>

    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa fa-id-badge"></i> Enter Job Details</h2>

      <?php if($model_det[5]=="Y"){ ?><h4 align="center" style="color:#F00">You are making a job for OUT warranty model .</h4> <?php } ?>

		<form  name="frm1" id="frm1" class="form-horizontal" enctype="multipart/form-data"  action="" method="post">

        <div class="panel-group">

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>

              <div class="panel-body">

              	  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Type <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	<select name="customer_type" id="customer_type" class="required form-control">

                          <option value='Walk-in Customer' selected> Walk-in Customer</option>

                          <option value='Dealer'>Dealer</option>

                          <option value='Distributor'>Distributor</option>	

                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label"></label>

                      <div class="col-md-6">

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Name <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	<input name="customer_name" id="customer_name" type="text" value="<?=$ticket_det['customer_name'];?>" class="form-control required"/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$ticket_det['address'];?></textarea>

                      </div>

                    </div>

                  </div>

                   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No. <!--<span class="small">(For SMS Update)</span>--> <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?php if($ticket_det['contact_no']!=''){ echo $ticket_det['contact_no'];}else{ echo $_REQUEST['contact_no'];}?>">

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No.</label>

                      <div class="col-md-6">

                      <input name="phone2" type="text" class="digits form-control" id="phone2" maxlength="10" value="<?=$ticket_det['alternate_no'];?>">

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">State <span class="red_small">*</span></label>

                      <div class="col-md-6">

                         <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();" required>

                          <option value=''>--Please Select--</option>

                          <?php 

						 $state_query="select stateid, state from state_master where countryid='1' order by state";

						 $state_res=mysqli_query($link1,$state_query);

						 while($row_res = mysqli_fetch_array($state_res)){?>

						   <option value="<?=$row_res['stateid']?>"<?php if($ticket_det['state_id']==$row_res['stateid']){ echo "selected";}?>><?=$row_res['state']?></option>

						 <?php }?> 	

                        </select>               

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Email</label>

                      <div class="col-md-6">

                          <input name="email" type="email" class="email form-control" id="email" value="<?=$ticket_det['email'];?>">

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">City <span class="red_small">*</span></label>

                        <div class="col-md-6" id="citydiv">

                       <select name="locationcity" id="locationcity" class="form-control required" required>

                       <option value=''>--Please Select-</option>

                       <?php 

					   if($ticket_det['city_id']!='' && $ticket_det['city_id']!=0){

						 $city_query="SELECT cityid, city FROM city_master where stateid='".$ticket_det['state_id']."' and cityid='".$ticket_det['city_id']."'";

						 $city_res=mysqli_query($link1,$city_query);

						 while($row_city = mysqli_fetch_array($city_res)){

						?>

						<option value="<?=$row_city['cityid']?>"<?php if($ticket_det['city_id']==$row_city['cityid']){ echo "selected";}?>><?=$row_city['city']?></option>

						<?php }

					   }

						?>

                       </select>

                      </div>

                    </div>

                   <div class="col-md-6"><label class="col-md-6 custom_label">Pincode</label>

                      <div class="col-md-6">

                        <input name="pincode" type="text" class="digits form-control" id="pincode" value="<?=$ticket_det['pincode']?>">

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

                          <?php

							$dept_query="SELECT * FROM product_master where status = '1' and product_id='".$sel_product."' order by product_name";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>

						  <option value="<?=$br_dept['product_id']?>"<?php if($sel_product == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>

						<?php }?>	

                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Brand <span class="red_small">*</span></label>

                      <div class="col-md-6">

                       <select name="brand" id="brand" class="form-control required" required>

                          <option value=''>--Select Brand--</option>

                          <?php

						  	

							$dept_query="SELECT * FROM brand_master where status = '1' and brand_id='".$sel_brand."' order by brand";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>

						  <option value="<?=$br_dept['brand_id']?>"<?php if($sel_brand == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>

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

						  	if($ticket_det['model_id']!=""){

						  ?>

                          <option value="<?=$ticket_det['model_id']."~".$ticket_det['model'];?>"><?=$ticket_det['model']?></option>

						  <?php

							}else if($_REQUEST['p_modelcode'] || $_REQUEST['modelid']){

						  ?>

                          <option value="<?=$model_code."~".$model_name;?>"><?=$model_det[2]?></option>

                          <?php }else{?>

                          <option value=''>--Select Model--</option>

                          <?php } ?>

                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Accessory Present <?php if($call_type_val=="DOA"){?><span class="red_small">*</span><?php }?></label>

                      <div class="col-md-6" id="accdiv">

                        <select name="acc_present[]" id="example-multiple-selected2" multiple="multiple" class="form-control<?php if($call_type_val=="DOA"){?> required<?php }?>">
						<?php $acc_part = mysqli_fetch_assoc(mysqli_query($link1,"select partcode,part_name from partcode_master where model_id='".$model_code."' and part_category='ACCESSORY' and status='1'"));

							while($br_acc = mysqli_fetch_array($acc_part)){

						  ?>
<option value="<?=$br_acc['part_name']?>"><?php echo $br_acc['part_name']?></option>

						<?php }?>
                        </select>
                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">IMEI 1/Serial No. 1 <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	<input name="imei_serial1" id="imei_serial1" type="text" value="<?=$_REQUEST['imei_serial']?>" class="form-control required" readonly/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">IMEI 2/Serial No. 2</label>

                      <div class="col-md-6">

                       <input name="imei_serial2" id="imei_serial2" type="text" value="<?=$_REQUEST['imei_serial2']?>" <?php if($_REQUEST['imei_serial2']!=''){?> readonly <?php }else{}?> class="form-control"/>

                      </div>

                    </div>

                  </div>


                  <div class="form-group">
						<div class="col-md-6"><label class="col-md-6 custom_label">Purchase Date</label>

                      <div class="col-md-6">

                        <div style="display:inline-block;float:left;"><!--<input type="text" class="form-control span2 required" name="pop_date"  id="pop_date" style="width:150px;" required value="<?php if($_REQUEST['p_dop']!='' && $_REQUEST['p_dop']!='0000-00-00'){ echo $_REQUEST['p_dop'];}else{ }?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>-->
<input name="pop_date" id="pop_date" type="text" value="<?php if($_REQUEST['p_dop']!='' && $_REQUEST['p_dop']!='0000-00-00'){ echo $_REQUEST['p_dop'];}else{ }?>" readonly  class="required form-control"/>
                  		</div>

                      </div>
                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Job For <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <select name="call_for" id="call_for" class="form-control required" required>

                          <option value=''>--Select Call For--</option>	

                          <option value='Unit'>Unit/Handset</option>

                          <option value='Accessory'>Accessory</option>

                        </select>

                      </div>

                    </div>

                  </div>

                 <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Job Type <span class="red_small">*</span></label>

                      <div class="col-md-6">
						<input name="call_type" id="call_type" type="text" value="<?=$call_type_val?>" readonly  class="required form-control"/>
                        <!--<select name="call_type" id="call_type" class="form-control required" required onChange="checkJobType();">

                          <option value=''>--Select Call Type--</option>

                          <option value='Normal'>Normal</option>

                          <?php if($model_det[5]!="Y"){ ?>

                          <option value='DOA'>DOA</option>

                          <option value='Replacement'>Replacement</option>	

                          <?php } ?>

                        </select>-->

                      </div>

                    </div>


                    <div class="col-md-6"><label class="col-md-6 custom_label">Activation Date</label>

                      <div class="col-md-6">

                       <input name="activation_date" id="activation_date" type="text" value="<?=$_REQUEST['p_activation']?>" class="form-control" readonly/>

                      </div>

                    </div>

                  </div> 

              </div>

            </div>
            
            
            
            
             <div id="doa_policy" <?php if($call_type_val!="DOA"){?> style="display:none"<?php }else{}?> class="panel panel-info"><!-- Start DOA panal group-->

              <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;DOA Conditions</div>

              <div class="panel-body">

                  <div class="form-group">

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q1. Is the purchase proof available with IMEI No ' s? <span class="red_small">*</span></label>

                      <div class="col-md-2">
							<label for="radiobutton"><input name="q1" type="radio" value="Y" id="q1" class="required"/>
                            Yes
                              <input name="q1" type="radio" value="N" id="q1" class="required"  />
                              No </label>
                      </div>

                    </div>
                  
                  </div>
                  
 				  <div class="form-group">

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q2. Are IMEI and Serial No on the mobile same as that on the packing box? <span class="red_small">*</span></label>

                      <div class="col-md-2">
						 <label for="radiobutton"><input name="q2" type="radio" value="Y" id="q2" class="required" />
                           Yes
                              <input name="q2" type="radio" value="N" id="q2" class="required"/>
                              No</label>
                      </div>

                    </div>
                  
                  </div>
                  
                   <div class="form-group">

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q3. The problem reported is not related to software which can be solved by upgrade software version. <span class="red_small">*</span></label>

                      <div class="col-md-2">
						<label for="radiobutton"><input name="q3" type="radio" value="Y" id="q3" class="required"/>
                            Yes
                              <input name="q3" type="radio" value="N" id="q3" class="required"/>
                              No </label>
                      </div>

                    </div>
                  
                  </div>
                  
                   <div class="form-group">

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q4. The problem reported is not related to accessories. <span class="red_small">*</span></label>

                      <div class="col-md-2">
						<label for="radiobutton"><input name="q4" type="radio" value="Y" id="q4" class="required"/>
                            Yes
                              <input name="q4" type="radio" value="N" id="q4" class="required"/>
                              No </label>
                      </div>

                    </div>
                  
                  </div>


					 <div class="form-group">

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q5. Unit does not have any physical damage, water damage/Water Liquid (Water detection label) or tampering on Handset. <span class="red_small">*</span></label>

                      <div class="col-md-2">
						<label for="radiobutton"><input name="q5" type="radio" value="Y" id="q5" class="required"/>
                            Yes
                              <input name="q5" type="radio" value="N" id="q5" class="required"/>
                              No </label>
                      </div>

                    </div>
                  
                  </div>

				 <div class="form-group">

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q6. Is not a Cosmetic reject (E.g. : Scratches on phone, lens, dent, etc.).<span class="red_small">*</span></label>

                      <div class="col-md-2">
						<label for="radiobutton"><input name="q6" type="radio" value="Y" id="q6" class="required"/>
                            Yes
                              <input name="q6" type="radio" value="N" id="q6" class="required" />
                              No </label>
                      </div>

                    </div>
                  
                  </div>


 					<div class="form-group">

                    <div class="col-md-12"><label class="col-md-10 custom_label">Q7. Is the unit complete sales package as mention in content of the box label sticker (Gift box, handset, user manual, hands free, charger, battery, software CD, data cable and memory card)?<span class="red_small">*</span></label>

                      <div class="col-md-2">
						<label for="radiobutton"><input name="q7" type="radio" value="Y" id="q7" class="required" />
                            Yes
                              <input name="q7" type="radio" value="N" id="q7" class="required" />
                              No </label>
                      </div>

                    </div>
                  
                  </div>


                 

              </div>

            </div><!-- end panal-->

        </div><!-- end DOA panal group-->
            
            
            
            
            
            

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>

              <div class="panel-body">

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Initial Symptom <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <select name="initial_symp" id="initial_symp" class="form-control required" required>

                          <option value=''>--Select Symptom--</option>

                          <?php

                          $res_symp = mysqli_query($link1,"select symp_code, symp_desc from symptom_master where (brand_id='".$sel_brand."' or brand_id='0' ) and product_id='".$sel_product."' and status='1'");

						  while($row_symp = mysqli_fetch_assoc($res_symp)){

						  ?>

						  <option value="<?=$row_symp['symp_code']?>"><?=$row_symp['symp_desc']?></option>

						  <?php }?>

                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Physical Condition <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <select name="physical_cond" id="physical_cond" class="form-control required" required>

                          <option value=''>--Select Physical Condition--</option>

                          <option value="Good">Good</option>

						  <option value="Scratched">Scratched</option>

                          <option value="Used" selected="selected">Used</option>	

                        </select>

                      </div>

                    </div>

                  </div>

                   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">ELS Status <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	 <select name="els_status" id="els_status" class="form-control required" required onChange="getWarranty();">

                          <option value=''>--Select ELS--</option>	

                          <option value='OK'>OK</option>

                          <?php if($call_type_val!="DOA"){?><option value="Physical damaged">Physical damaged</option><?php }?>

                          <?php if($call_type_val!="DOA"){?><option value="Tempered">Tempered</option><?php }?>

                         <?php if($call_type_val!="DOA"){?><option value="Liquid damaged">Liquid damaged</option><?php }?>

                          <?php if($call_type_val!="DOA"){?><option value="Electrical malfunctioning">Electrical malfunctioning</option><?php }?>

                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Warranty Status <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <input name="warranty_status" id="warranty_status" type="text" value="<?=$ws?>" class="form-control required" readonly/>

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Dealer Name <?php if($call_type_val=="DOA"){?><span class="red_small">*</span><?php }?></label>

                      <div class="col-md-6">

                      	<input name="dealer_name" id="dealer_name" type="text" value="" class="form-control <?php if($call_type_val=="DOA"){?>required<?php }?>"/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Invoice No.<?php if($call_type_val=="DOA"){?><span class="red_small">*</span><?php }?></label>

                      <div class="col-md-6">

                        <input name="invoice_no" id="invoice_no" type="text" value="" class="form-control <?php if($call_type_val=="DOA"){?>required<?php }?>"/>

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">VOC <span class="red_small">*</span></label>

                      <div class="col-md-6">

                       <select name="voc1" id="voc1" class="form-control required" required>

                          <option value=''>--Select VOC 1--</option>

                          <?php

						  foreach($voc_arr as $key => $value){

						  ?>

                          <option value="<?=$key?>"<?php if($ticket_det['cust_problem']==$key){ echo "selected";}?>><?=$value?></option>

						  <?php

                          }

						  ?>	

                        </select>               

                      </div>

                    </div>

                    <div class="col-md-6">

                    	<div class="col-md-6">

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

                      	<div class="col-md-6">

                            <input name="voc3" id="voc3" type="text" value="" class="form-control" placeholder="Enter Other VOC"/>

                      	</div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-12"><label class="col-md-3 custom_label">Remark <span class="red_small">*</span></label>

                      <div class="col-md-9">

                      <textarea name="remark" id="remark" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:none"><?=$ticket_det['remark']?></textarea>

                      </div>

                    </div>

                  </div>
	  <!------------- Start Image Uploder --------------->
		  <div class="form-group">
			<label class="col-md-3 custom_label">Device Image <span class="red_small">*</span></label>
			  <div class="col-md-5">
				  <input type="file"  name="handset_img" id="handset_img" onChange="return validateImage('handset_img','0');" class="form-control required" required accept=".png,.jpg,.jpeg,.gif" /><br>
				  <span id="errmsg0" class="red_small"></span>
			  </div>
		  </div>
		  <div class="form-group">
		
			  <div class="col-md-5">
			
			  </div>
		  </div>
		  <!------------- End Image Uploder --------------->
                  <div class="form-group">

                    <div class="col-md-12" align="center">

                      <span id="errmsg" class="red_small"></span>

                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_create.php?<?=$pagenav?>'">&nbsp;
						<input name="wsd" id="wsd" value="<?=$_REQUEST['p_wsd'];?>" type="hidden"/>
                      <input name="ticketno" id="ticketno" value="<?=base64_encode($ticket_det['ticket_no']);?>" type="hidden"/>
                      <input name="day_diff" id="day_diff" value="<?=$days_diference;?>" type="hidden"/>
                      
                      <input name="symptom" id="symptom" value="<?=$count['symp_code']?>" type="hidden"/>

                      <input type="submit" class="btn<?=$btncolor?>" name="savejob" id="savejob" value="Save" title="Save Job Details" <?php if($_POST['savejob']=='Save'){?>disabled<?php }?>>&nbsp;

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