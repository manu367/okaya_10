<?php

require_once("../includes/config.php");
////get access ASP details

$access_asp = getAccessASP($_SESSION['asc_code'],$link1);
	
////////////// update by jitender on dec 11 for repair and bounce type call for claim process ////////////////////////////////////////
if($_REQUEST['mobileno']){
$srch_criteria = "where mobile = '".$_REQUEST['mobileno']."' ";
}else if($_REQUEST['email_id']){
$srch_criteria = "where email = '".$_REQUEST['email_id']."'";
}else if($_REQUEST['customer_id']){
$srch_criteria = "where customer_id = '".$_REQUEST['customer_id']."'";
}else{
$srch_criteria="";
}
//echo "select  *  from customer_master   ".$srch_criteria."   order by id desc";
$sql_cust	= mysqli_query($link1, "select  *  from customer_master   ".$srch_criteria."   order by id desc");

$row_customer=mysqli_fetch_array($sql_cust);



////// final submit form ////

if($_POST['savejob']=='Save'){


	@extract($_POST);

	//// initialize transaction parameters

	$flag = true;

	mysqli_autocommit($link1, false);

	$error_msg="";
	
		//////////////////////////////customer details//////////////////////////////////////////
$usr_srch="select mobile from customer_master where mobile='".$phone1."'";
$result_usr=mysqli_query($link1,$usr_srch);
$arr_usr=mysqli_fetch_array($result_usr);	
if ((mysqli_num_rows($result_usr)==0) ){	
// also save customer details \\ 	
$sel_uid="select max(max_id) from customer_master";
$res_uid=mysqli_query($link1,$sel_uid);
$arr_result2=mysqli_fetch_array($res_uid);
$code_id=$arr_result2[0]+1;
$pad=str_pad($code_id,5,"0",STR_PAD_LEFT);
$customer_id="C".$stCode.$pad;

	$usr_add="insert into customer_master set  customer_id='".$customer_id."', customer_name='".$customer_name."', address1='".$address."', pincode='".$pincode."', cityid='".$locationcity."', stateid='".$locationstate."', email='".$email."',  phone='".$res_no."', mobile='".$phone1."', alt_mobile='".$phone2."', update_date='".$today."', update_by='".$_SESSION['asc_code']."',max_id='".$code_id."',landmark='".$landmark."',customer_type='".$customer_type."'";
$res_add=mysqli_query($link1,$usr_add); 

$cust_id=$customer_id;
}else{
	$cust_id=$custo_id;
	}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//// pick max count of AMC
$res_amc = mysqli_query($link1,"SELECT amc_count from job_counter where location_code='".$_SESSION['asc_code']."'");

	$row_amc = mysqli_fetch_assoc($res_amc);

	///// make job sequence

	$nextamc = $row_amc['amc_count'] + 1;

	$amcno = $_SESSION['userid']."A".str_pad($nextamc,4,0,STR_PAD_LEFT);
		//// first update the job count

	$res_upd = mysqli_query($link1,"UPDATE job_counter set amc_count='".$nextamc."' where location_code='".$_SESSION['asc_code']."'");

	//// check if query is not executed

	if (!$res_upd) {

		 $flag = false;

		 $error_msg = "Error details1 in amc counter: " . mysqli_error($link1) . ".";

	}
	$modelsplit = explode("~",$modelid);

	///// entry in AMC sheet data
	
		 $sql_inst = "INSERT INTO amc set amcid='".$amcno."',product_id='".$product_name."',brand_id='".$brand."',model_id='".$modelsplit[0]."',amc_type='".$amc_type."',mode_of_payment='".$payment_mode."',amc_start_date='".$amc_eff_date."',amc_end_date='".$amc_exp_date."',customer_name='".$customer_name."',contract_no='".$phone1."',addrs='".$address."',country_id='1',state_id='".$locationstate."',city_id='".$locationcity."',amc_duration ='".$amc_duration."',purchase_date='".$pop_date."',landmark='".$landmark."',remarks='".$remark."',serial_no='".$imei_serial1."',email='".$email."',update_date='".$today."',cheque_no='".$check_no."',cheque_date='".$chequedate."',bank_name='".$bank."',payee_name ='".$payee."',location_code='".$_SESSION['asc_code']."',amc_amount='".$amc_amount."', open_time='".$currtime."',status='1',customer_type='".$customer_type."',quotetype='".$quotetype."',cr_no='".$cr_no."',entity_type='".$entity_type."', alt_mobile='".$phone2."',customer_id='".$cust_id."'";

	$res_inst = mysqli_query($link1,$sql_inst);
	
	//// check if query is not executed

	if (!$res_inst) {

		 $flag = false;

		 $error_msg = "Error amc : " . mysqli_error($link1) . ".";

	}


	///// model details

	
	//// Product Register \\\\\
		//echo "select * from product_registered where serial_no='$serial_no'<br />";
		$usr_product="select serial_no from product_registered where serial_no='".$imei_serial1."'";
		$result_product=mysqli_query($link1,$usr_product);
		///// if found \\\\\
		if (mysqli_num_rows($result_product)==0){
		
	 $usr_add3="INSERT INTO product_registered set serial_no='".$imei_serial1."', customer_id='".$cust_id."', product_id='".$product_name."', model_id='".$modelsplit[0]."', purchase_date='".$pop_date."', warranty_end_date='".$warraty_date."', status='1',mobile_no='".$phone1."',brand_id='".$brand."'";
		$res_add3=mysqli_query($link1,$usr_add3);
		
		}
    


	////// insert in activity table////

	$flag = dailyActivity($_SESSION['userid'],$amcno,"AMC","CREATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);

	///// check both master and data query are successfully executed

	if ($flag) {

		mysqli_commit($link1);

		////// return message

		$msg="You have successfully created a AMC like ".$amcno;

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
    


 header("location:amc_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&smsmsg=".$sms_msg."&mobileno=".$phone1."&customer_id=".$customer_id."&imei_serial=".$imei_serial1."&email_id=".$email."&status=1");
	//exit;

}




//echo "SELECT * FROM product_registered  where serial_no='".$_REQUEST['imei_serial']."'";
if($_REQUEST['imei_serial']){
$product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where serial_no='".$_REQUEST['imei_serial']."'"));
}





////// make voc array

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

		$('#amc_eff_date').datepicker({

			format: "yyyy-mm-dd",
startDate: "<?=$today?>",
			//endDate: "<?=$today?>",

			todayHighlight: true,

			autoclose: true,

		}).on('changeDate', function(ev){

    		

			

		})

	});

	

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
	   var product_name=document.getElementById("product_name").value;
	  // alert(product_name);
	 
	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{brandModel:brandid,product_id:product_name},

		success:function(data){

	    $('#modeldiv').html(data);

	    }

	  });

    });

  });
function getdate4() {


var start_date = new Date($('#amc_eff_date').val());

 var exr_day =  document.getElementById('amc_duration').value;
 

  var end_date = new Date(start_date);


  end_date.setDate(start_date.getDate() +  parseInt(exr_day));  
   
  $datecc=end_date.getFullYear() + '-' + ("0" + (end_date.getMonth() + 1)).slice(-2) + '-' + ("0" + end_date.getDate()).slice(-2);
                     
  $("#amc_exp_date").val($datecc);
          // $('#warranty_date').val(date);
   // document.getElementById('warranty_date').value = someFormattedDate;
 
}
  /////////// function to check DOA is eligible or not

  






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
$(document).ready(function () {
	$('#pop_date').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
	

	
$('#chequedate').datepicker({
		format: "yyyy-mm-dd",
		startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});

});

function changemode(){


	
	var payment_mode = $('#payment_mode').val();



	if(payment_mode == "Cheque"){

		document.getElementById("checkdetail1").style.display = "";
		document.getElementById("checkdetail2").style.display = "";
	document.getElementById("cashdetail").style.display = "none";
		

	}else{

	document.getElementById("checkdetail1").style.display = 'none';
	document.getElementById("checkdetail2").style.display = 'none';
		document.getElementById("cashdetail").style.display = '';
	}

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

<body >

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnavemp2.php");

    ?>

    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa fa-id-badge"></i> Enter AMC Details</h2>



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
                          <option value="">--Please Select--</option>
                          <?php



				$cus_query="SELECT * FROM customer_type where status = '1' order by customer_type";



				$check_cust=mysqli_query($link1,$cus_query);



				while($br_cust = mysqli_fetch_array($check_cust)){



				?>
                          <option value="<?=$br_cust['customer_type']?>"<?php if($_REQUEST['customer_type']==$br_cust['customer_type']){ echo "selected";}?>><?php echo $br_cust['customer_type']?></option>
                          <?php }?>
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

                      	<input name="customer_name" id="customer_name" type="text" value="<?=$row_customer['customer_name'];?>" class="form-control required" <?php if($row_customer['customer_name']!=''){?> readonly <?php }else{}?>/>
						<input name="custo_id" id="custo_id" type="hidden" value="<?=$row_customer['customer_id'];?>" class="form-control required"/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical" <?php if($row_customer['address1']!=''){?> readonly <?php }else{}?>><?=$row_customer['address1'];?></textarea>

                      </div>

                    </div>

                  </div>
				                    <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Landmark </label>

                      <div class="col-md-6">

                        	<input name="landmark" id="landmark" type="text" class="form-control " value="<?=$row_customer['landmark'];?>" <?php if($row_customer['customer_name']!=''){?> readonly <?php }else{}?> /> 

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Pincode  <span class="red_small">*</span></label>

                      <div class="col-md-6">

                              <input name="pincode" type="text" class="digits form-control required" id="pincode" value="<?=$row_customer['pincode']?>" <?php if($row_customer['pincode']!=''){?> readonly <?php }else{}?>>

                      </div>

                    </div>

                  </div>

                   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No. <!--<span class="small">(For SMS Update)</span>--> <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?php if($row_customer['mobile']!=''){ echo $row_customer['mobile'];}else{ echo $_REQUEST['mobileno'];}?>" <?php if($row_customer['mobile']!=''){?> readonly <?php }else{}?>>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No.</label>

                      <div class="col-md-6">

                      <input name="phone2" type="text" class="digits form-control" id="phone2" maxlength="10" value="<?=$row_customer['alt_mobile'];?>" <?php if($row_customer['alt_mobile']!=''){?> readonly <?php }else{}?>>

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">State <span class="red_small">*</span></label>

                      <div class="col-md-6">

                         <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();" required <?php if($row_customer['stateid']!=''){?> readonly <?php }else{}?>>

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

                          <input name="email" type="email" class="email form-control" id="email" value="<?=$row_customer['email'];?>"  <?php if($row_customer['email']!=''){?> readonly <?php }else{}?>>

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">City <span class="red_small">*</span></label>

                        <div class="col-md-6" id="citydiv">

                       <select name="locationcity" id="locationcity" class="form-control required" required <?php if($row_customer['cityid']!=''){?> readonly <?php }else{}?>>

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

                        <input name="res_no" type="text" class="digits form-control" id="res_no" value="<?=$row_customer['phone']?>"  <?php if($row_customer['cityid']!=''){?> readonly <?php }else{}?>>

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

                        

                          <?php
						  if($product_det['product_id']==''){?>
						    <option value=''>--Select Product--</option>

							<?php $dept_query="SELECT * FROM product_master where status = '1'  order by product_name";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>

						  <option value="<?=$br_dept['product_id']?>"<?php if($sel_product == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>

						<?php }} else {?>	
                              <option value='<?=$product_det['product_id']?>'><?=getAnyDetails($product_det['product_id'],"product_name","product_id","product_master",$link1);?></option>
							  <?php }?>
                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Brand <span class="red_small">*</span></label>

                      <div class="col-md-6">

                       <select name="brand" id="brand" class="form-control required" required>

                        <?php  if($product_det['brand_id']==''){?>
						    <option value=''>--Select Product--</option>
                          <?php

						  	

							$dept_query="SELECT * FROM brand_master where status = '1'  order by brand";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>

						  <option value="<?=$br_dept['brand_id']?>"<?php if($sel_brand == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>

						<?php }} else {?>	
                              <option value='<?=$product_det['brand_id']?>'><?=getAnyDetails($product_det['brand_id'],"brand","brand_id","brand_master",$link1);?></option>
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

                          <option value="<?=$model_code."~".$model_name;?>"><?=$model_det[2]?></option>

                          <?php }else{?>

                          <option value=''>--Select Model--</option>

                          <?php } ?>

                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label"><?php echo SERIALNO ?> <span class="red_small">*</span></label>

                      <div class="col-md-6" >

	<input name="imei_serial1" id="imei_serial1" type="text" value="<?=$_REQUEST['imei_serial']?>" class="form-control required " required />
                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">AMC Duration.<span class="red_small">(In Days)</span></label>

                      <div class="col-md-6">

                       <input name="amc_duration" type="text" class="digits required form-control" maxlength="3" required id="amc_duration">

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">AMC Start Date <span class="red_small">*</span></label>

                      <div class="col-md-6">
 <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="amc_eff_date"  id="amc_eff_date" style="width:150px;" required  onChange="getdate4();"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>

                      </div>

                    </div>

                  </div>


                  <div class="form-group">
						<div class="col-md-6"><label class="col-md-6 custom_label">AMC Expiry Date </label>

                      <div class="col-md-6">
     <input name="amc_exp_date" id="amc_exp_date" type="text" value="<?=$_REQUEST['p_activation']?>" class="form-control" readonly/>
	 

                      </div>
                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">AMC  Type <span class="red_small">*</span></label>

                      <div class="col-md-6">

                     	 <select name="amc_type" id="amc_type" class="form-control required">
                    <option value="">--Please Select--</option>
                    <option value="Comprehansive" >Comprehansive</option>
                    <option value="Non Comprehansive">Non Comprehansive</option>
                   
                 </select>

                      </div>

                    </div>

                  </div>

                 <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">AMC Amount<span class="red_small">*</span></label>

                      <div class="col-md-6">
						  <input name="amc_amount" type="text" class="digits required form-control" maxlength="8" required id="amc_amount">
                      </div>

                    </div>


                    <div class="col-md-6"><label class="col-md-6 custom_label">Bill Purchase Date</label>

                      <div class="col-md-6">
<div style="display:inline-block;float:left;"><input type="text" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="<?php if($product_det['purchase_date']!=''){ echo $product_det['purchase_date'];?>  <?php }else{ echo "";}?>"  ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                   
                      </div>

                    </div>

                  </div> 
				  
				            
				  
				  

              </div>

            </div>
            

            
            
            
            
            
            

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Payment</div>

              <div class="panel-body">



                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Payment Mode <span class="red_small">*</span></label>

                      <div class="col-md-6">

                       <select name="payment_mode" id="payment_mode"  class="form-control required" required onChange="changemode();">
                    <option value="">--Please Select--</option>
                    <option value="Cash">Cash</option>
					  <option value="Online">Online</option>
                    <option value="Cheque" >Cheque</option>
                    
                 </select>   

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Entity Name <span class="red_small">*</span></label>

                    	<div class="col-md-6">

                        <select name="entity_type" id="entity_type" class="form-control required" required>
                          <option value="Others">Others</option>
                          <?php



				$enty_query="SELECT * FROM entity_type where status_id = '1' order by name";



				$check_enty=mysqli_query($link1,$enty_query);



				while($br_entity = mysqli_fetch_array($check_enty)){



				?>
                          <option value="<?=$br_entity['id']?>"<?php if($_REQUEST['entity_type']==$br_entity['id']){ echo "selected";}?>><?php echo $br_entity['name']?></option>
                          <?php }?>
                        </select>


                        </div>

                      	<div class="col-md-6">

                            
                      	</div>

                    </div>

                  </div>
				    <div class="form-group"  id="cashdetail" style="display:none">
            <div class="col-md-6"><label class="col-md-6 custom_label">CR/Transaction Number   <span class="red_small">*</span></label>
                <div class="col-md-6">
               	 <input type="text" name="cr_no" class=" form-control required" id="cr_no" />
              </div>
            </div>
          <div class="col-md-6">
    <div class="col-md-6" ></div>
            </div>
          </div>
		    </div>
   <div class="form-group"  id="checkdetail1" style="display:none">
            <div class="col-md-6"><label class="col-md-6 custom_label">Cheque Number   <span class="red_small">*</span></label>
                <div class="col-md-6">
               	 <input type="text" name="check_no" class=" form-control required" id="check_no" />
              </div>
            </div>
          <div class="col-md-6"><label class="col-md-6 custom_label">Cheque Date <span class="red_small">*</span></label>
    <div class="col-md-6" ><div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="chequedate"  id="chequedate" style="width:150px;" required value=""></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		    </div>
				    <div class="form-group"  id="checkdetail2" style="display:none">
            <div class="col-md-6"><label class="col-md-6 custom_label">Bank Name   <span class="red_small">*</span></label>
                <div class="col-md-6">
               	 <input type="text" name="bank" class=" form-control required" id="bank" />
              </div>
            </div>
          <div class="col-md-6"><label class="col-md-6 custom_label">Payee Name   <span class="red_small">*</span></label>
    <div class="col-md-6" > <input type="text" name="payee" class=" form-control required" id="payee" />
            </div>
          </div>
		    </div>
                 

		  <!------------- End Image Uploder --------------->
                  <div class="form-group">

                    <div class="col-md-12" align="center">

                      <span id="errmsg" class="red_small"></span>

                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='complaint_create.php?<?=$pagenav?>'">&nbsp;
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