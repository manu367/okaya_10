<?php
require_once("../includes/config.php");
/////get status//


@extract($_POST);
////// case 1. if we want to update details

////// case 2. if we want to Add new user
if($_POST){

if($_POST['add']=='ADD'){
	//// initialize transaction parameters

	$flag = true;

	mysqli_autocommit($link1, false);

	$error_msg="";
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
	if($gq=="Y"){
	$quotetype="Y";
	$status="3";
	$st="Q";
	
	$flag = dailyActivity($_SESSION['userid'],$amcno,"AMC","Generate Quotation",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	
	}else{
		$quotetype="";
	$status="1";
	$st="Y";
	$flag = dailyActivity($_SESSION['userid'],$amcno,"AMC","CREATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	}
	
	 $sql_inst = "INSERT INTO amc set amcid='".$amcno."',product_id='".$product_id."',brand_id='".$brand_id."',model_id='".$model_id."',amc_type='".$amc_type."',mode_of_payment='".$payment_mode."',amc_start_date='".$amc_eff_date."',amc_end_date='".$amc_exp_date."',customer_name='".$customer_name."',contract_no='".$mobile_no."',addrs='".$address."',country_id='".$country."',state_id='".$locationstate."',city_id='".$locationcity."',amc_duration ='".$amc_duration."',purchase_date='".$today."',landmark='".$landmark."',remarks='".$remark."',serial_no='".$serail_no."',email='".$email."',update_date='".$today."',cheque_no='".$check_no."',cheque_date='".$chequedate."',bank_name='".$bank."',payee_name ='".$payee."',location_code='".$_SESSION['asc_code']."',amc_amount='".$amc_amount."', open_time='".$currtime."',status='".$status."',customer_type='".$customer_type."',quotetype='".$quotetype."'";

	$res_inst = mysqli_query($link1,$sql_inst);
	
	//// check if query is not executed

	if (!$res_inst) {

		 $flag = false;

		 $error_msg = "Error amc : " . mysqli_error($link1) . ".";

	}
	
	
		$res_import = mysqli_query($link1,"UPDATE imei_data_import set amc='".$st."', amc_end_date='".$amc_exp_date."'  where (imei1 = '".$serail_no."'  or  imei2 = '".$serail_no."' )");

	//// check if query is not executed

	if (!$res_import) {

		 $flag = false;

		 $error_msg = "Error details import: " . mysqli_error($link1) . ".";

	}
	
	
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
	 header("location:amc_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
}

	//exit;

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
	
	$('#amc_eff_date').datepicker({
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
	$('#chequedate').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});

$(document).ready(function(){



	$('#country').change(function(){



	  var countryid=$('#country').val();



	  $.ajax({



	    type:'post',



		url:'../includes/getAzaxFields.php',



		data:{cntryid:countryid},



		success:function(data){



	    $('#statediv').html(data);



	    }



	  });



    });



  });



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
  $(document).ready(function() {

	 /////// if user enter imei or serial no. then contact no. field should be disabled

	 $("#imei_serial").keyup(function() {

		if($("#imei_serial").val()!=""){ 

        	$("#contact_no").attr("disabled",true);

			$("#Submit").attr("disabled",false);

		}else{

			$("#contact_no").attr("disabled",false);

			$("#Submit").attr("disabled",true);

		}

    });

    /////// if user enter contact no. then imei or serial no. field should be disabled

	 $("#contact_no").keyup(function() {

		 if($("#contact_no").val()!=""){ 

        	$("#imei_serial").attr("disabled",true);

			$("#Submit").attr("disabled",false);

		 }else{

			 $("#imei_serial").attr("disabled",false);

			 $("#Submit").attr("disabled",true);

		 }

    });

 });
 
 
 //// Check payment mode collection

function changemode(){


	
	var payment_mode = $('#payment_mode').val();



	if(payment_mode == "Cheque"){

		document.getElementById("checkdetail1").style.display = "";
		document.getElementById("checkdetail2").style.display = "";

		

	}else{

	document.getElementById("checkdetail1").style.display = 'none';
	document.getElementById("checkdetail2").style.display = 'none';
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
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-cube"></i> <?=$_REQUEST['op']?> AMC</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
	  
	  
	  <form id="frm13" name="frm13" class="form-horizontal" action="" method="post">
     <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">Enter IMEI/Serial No.<span class="red_small">*</span></label>

                  <div class="col-md-6">

                     <input type="text" name="imei_serial" class="form-control required" maxlength="15" required id="imei_serial" value="<?=$_REQUEST['imei_serial']?>" placeholder="Enter only IMEI/Serial No."/>

                  </div>

                </div>

              </div>

             <!-- <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">OR</label>

                  <div class="col-md-6">

                    

                  </div>

                </div>

              </div>-->

              <!--<div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label">Contact No.</label>

                  <div class="col-md-6">

                     <input type="text" name="contact_no" class="digits form-control" id="contact_no" value="<?=$_REQUEST['contact_no']?>" placeholder="Enter only Contact No."/>

                  </div>

                </div>

              </div>-->

               <div class="form-group">

                <div class="col-md-10"><label class="col-md-4 control-label"></label>

                  <div class="col-md-6">

                     <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="Submit" value="Search" title="Search" disabled>

                  </div>

                </div>

              </div>

          	</form>
	    <?php if($_POST['Submit']=="Search" && ($_POST['imei_serial']!='' || $_POST['contact_no']!='') ){

				$sql_job	= mysqli_query($link1, "select  * from jobsheet_data  where (imei = '".$_REQUEST['imei_serial']."'  or sec_imei = '".$_REQUEST['imei_serial']."' )   order by job_id desc");	
				
				$sql_imp	= mysqli_query($link1, "select  *  from imei_data_import  where (imei1 = '".$_REQUEST['imei_serial']."'  or  imei2 = '".$_REQUEST['imei_serial']."' )   order by id desc");
				
				
				
			if(mysqli_num_rows($sql_job)>0 || mysqli_num_rows($sql_imp)>0 ){
			
				$row_import=mysqli_fetch_array($sql_imp);	
				$row_job=mysqli_fetch_array($sql_job);
				
				$model_detail = explode("~", getAnyDetails($row_import['model_id'],"product_id,brand_id,model","model_id","model_master",$link1)); 	
				$product_detail =  getAnyDetails($model_detail[0],"product_name","product_id","product_master",$link1);
				$brand_detail =  getAnyDetails($model_detail[1],"brand","brand_id","brand_master",$link1);	
						

			?> 
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Product Name </label>
                <div class="col-md-6">
             	 <input type="text" name="product_name" class=" form-control" id="product_name"  value="<?=$product_detail?>" readonly/> 
				  <input type="hidden" name="product_id" class="form-control" id="product_id"  value="<?=$model_detail[0]?>" readonly/>  	
				  <input type="hidden" name="serail_no" class=" form-control" id="serail_no"  value="<?=$_REQUEST['imei_serial']?>" readonly/> 
				  
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Brand </label>
              <div class="col-md-6">
                 <input type="text" name="brand_name" class="form-control" id="brand_name"  value="<?=$brand_detail?>" readonly/> 
				   <input type="hidden" name="brand_id" class="form-control" id="brand_id"  value="<?=$model_detail[1]?>" readonly/> 
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">AMC Effective Date</label>
              <div class="col-md-6">
                 <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="amc_eff_date"  id="amc_eff_date" style="width:150px;" required value="<?=$today?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                  </div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Model. </label>
              <div class="col-md-6">
                 <input type="text" name="model" class=" form-control" id="model"  value="<?=$model_detail[2]?>"  readonly />
				  <input type="hidden" name="model_id" class="form-control" id="model_id"  value="<?=$row_import['model_id']?>" readonly/> 
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">AMC Type  <span class="red_small">*</span> </label>
                <div class="col-md-6">
               	 <select name="amc_type" id="amc_type" class="form-control">
                    <option value="">--Please Select--</option>
                    <option value="Comprehansive" >Comprehansive</option>
                    <option value="Non Comprehansive">Non Comprehansive</option>
                   
                 </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Payment Mode  <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="payment_mode" id="payment_mode"  class="form-control required" required onChange="changemode();">
                    <option value="">--Please Select--</option>
                    <option value="Cash">Cash</option>
                    <option value="Cheque" >Cheque</option>
                    
                 </select>
              </div>
            </div>
          </div>
		
		    <div class="form-group"  id="checkdetail1" style="display:none">
            <div class="col-md-6"><label class="col-md-6 control-label">Cheque Number   <span class="red_small">*</span></label>
                <div class="col-md-6">
               	 <input type="text" name="check_no" class=" form-control required" id="check_no" />
              </div>
            </div>
          <div class="col-md-6"><label class="col-md-6 control-label">cheque Date.  <span class="red_small">*</span></label>
    <div class="col-md-6" ><div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="chequedate"  id="chequedate" style="width:150px;" required value="<?=$today?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		    </div>
				    <div class="form-group"  id="checkdetail2" style="display:none">
            <div class="col-md-6"><label class="col-md-6 control-label">Bank Name   <span class="red_small">*</span></label>
                <div class="col-md-6">
               	 <input type="text" name="bank" class=" form-control required" id="bank" />
              </div>
            </div>
          <div class="col-md-6"><label class="col-md-6 control-label">payee Name.   <span class="red_small">*</span></label>
    <div class="col-md-6" > <input type="text" name="payee" class=" form-control required" id="payee" />
            </div>
          </div>
		    </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Customer Name <span class="red_small">*</span></label>
                <div class="col-md-6">
               	 <input type="text" name="customer_name" class=" required form-control" id="customer_name" value="<?=$row_job['customer_name']?>" required/>
              </div>
            </div>
			   <div class="col-md-6"><label class="col-md-6 control-label">Mobile No. <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="mobile_no" class="digits required form-control" id="mobile_no" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();"value="<?=$row_job['contact_no']?>" required/>
              </div>
            </div>
           
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">
AMC Expiry Date <span class="red_small">*</span></label>
               <div class="col-md-6" ><div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="amc_exp_date"  id="amc_exp_date" style="width:150px;" required value="<?=$today?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
            </div>
            </div>
          <div class="col-md-6"><label class="col-md-6 control-label">Address   <span class="red_small">*</span> </label>
              <div class="col-md-6">
                  <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$row_job['address']?></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Country <span class="red_small">*</span></label>
              <div class="col-md-6">
             
                 <select name="country" id="country" class="form-control required" required>



                  <option value="">--Please Select--</option>



                  <?php



				$country_query="SELECT * FROM country_master where status = 'A' order by countryname";



				$check_country=mysqli_query($link1,$country_query);



				while($br_country = mysqli_fetch_array($check_country)){



				?>



                <option value="<?=$br_country['countryid']?>"<?php if($_REQUEST['country']==$br_country['countryid']){ echo "selected";}?>><?php echo $br_country['countryname']?></option>



                <?php }?>



                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">State  <span class="red_small">*</span> </label>
                 <div class="col-md-6" id="statediv">



                 <select name="locationstate" id="locationstate" class="form-control required" required>



                  <option value=''>--Please Select--</option>



                



                </select>               



              </div>

            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">City <span class="red_small">*</span></label>
              <div class="col-md-6" id="citydiv">



               <select name="locationcity" id="locationcity" class="form-control required" required>



               <option value=''>--Please Select-</option>



               </select>



              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Landmark</label>
              <div class="col-md-6">
                 <input type="text" name="landmark" class="form-control" id="landmark" />
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Customar type <span class="red_small">*</span></label>
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
            <div class="col-md-6"><label class="col-md-6 control-label">Email Id </label>
              <div class="col-md-6">
                   <input name="email" type="email" class="email required form-control" id="email" required onBlur="return checkEmail(this.value,'email');">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">AMC Duration. <span class="red_small">*</span></label>
              <div class="col-md-6">
            <input name="amc_duration" type="text" class="digits required form-control" maxlength="2" required id="amc_duration">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Remarks </label>
              <div class="col-md-6">
                    <textarea name="remark" id="remark"  class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
              </div>
            </div>
          </div>
		  
		          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">AMC Amount. <span class="red_small">*</span></label>
              <div class="col-md-6">
            <input name="amc_amount" type="text" class="digits required form-control" maxlength="8" required id="amc_amount">
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Generate Quatation </label>
              <div class="col-md-6">
                   <input type="checkbox" name="gq" id="gq" value="Y">
              </div>
            </div>
          </div>

          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New AMC">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Model Details">
              <?php }?>
           
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='amc_list.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
            </div>
          </div>
    </form>
	
	 <?php } else {
	 echo "No Record Found";
	 
	 
	 }
	 
	 
	 
	  } ?>
      </div>

    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>