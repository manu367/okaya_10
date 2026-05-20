<?php

require_once("../includes/config.php");
////get access ASP details
$docid=base64_decode($_REQUEST['refid']);
////////////// update by jitender on dec 11 for repair and bounce type call for claim process ////////////////////////////////////////
/*if($_REQUEST['mobileno']){
$srch_criteria = "mobile = '".$_REQUEST['mobileno']."' ";
}else if($_REQUEST['email_id']){
$srch_criteria = "email = '".$_REQUEST['email_id']."'";
}else if($_REQUEST['customer_id']){
$srch_criteria = "customer_id = '".$_REQUEST['customer_id']."'";
}else{
$srch_criteria="";
}*/

$srch_criteria = "where customer_id = '".$docid."'";
$sql_cust	= mysqli_query($link1, "select  *  from customer_master ".$srch_criteria."   order by id desc");

$row_customer=mysqli_fetch_array($sql_cust);

 

////// final submit form ////
if($_POST['addotheraddr']=='Add New Address'){
    @extract($_POST);
    $flag = true;
	mysqli_autocommit($link1, false);
	$error_msg = "";
$usr_add="insert into customer_other_address set  customer_id='".$row_customer['customer_id']."', customer_name='".$othercustomer_name."', address1='".$otheraddress."', pincode='".$otherpincode."', cityid='".$otherlocationcity."', stateid='".$otherlocationstate."',phone='".$otherres_no."',alt_mobile='".$otherphone2."', update_date='".$today."', update_by='".$_SESSION['asc_code']."',landmark='".$otherlandmark."',reg_name='".$otherreg_name."'";
     $msg="Other Customer Address has been registered successfully";
    $res_add=mysqli_query($link1,$usr_add);
if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
    if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = $msg;
	} else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
 header("location:customer_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&mobileno=".$phone1."&customer_id=".$customer_id."&email_id=".$email."&status=1");
}
if($_POST['savejob']=='Save'){
	@extract($_POST);
   $flag = true;
	mysqli_autocommit($link1, false);
	$error_msg = "";
    /////////12-Feb-2020 Comment By Ravi Sir
    /*if($email!=''){
    $usr_srch="select email from customer_master where email='".$email."'";
$result_usr=mysqli_query($link1,$usr_srch);
$arr_usr=mysqli_fetch_array($result_usr);	
if ((mysqli_num_rows($result_usr)==0) ){
    $usr_email="update customer_master set email='".$email."',update_date='".$today."', update_by='".$_SESSION['asc_code']."' where customer_id='".$customer_id."'";
    $email_update=mysqli_query($link1,$usr_email);
     $flag = dailyActivity($_SESSION['userid'],$row_customer['email']."-".$email,"Customer Email","Customer Update",$_SERVER['REMOTE_ADDR'],$link1,$flag);
    $msg='Update Customer Email Information successfully';
        }
    }*/
$usr_srch="select mobile from customer_master where mobile='".$phone1."'";
$result_usr=mysqli_query($link1,$usr_srch);
$arr_usr=mysqli_fetch_array($result_usr);	
if ((mysqli_num_rows($result_usr)==0) ){
    $usr_mobile="update customer_master set mobile='".$phone1."',update_date='".$today."', update_by='".$_SESSION['asc_code']."' where customer_id='".$customer_id."'";
$mobile_update=mysqli_query($link1,$usr_mobile);
      $msg='Update Customer mobile No. Information successfully';
    $flag = dailyActivity($_SESSION['userid'],$row_customer['mobile']."-".$phone1,"Customer Mobile","Customer Update",$_SERVER['REMOTE_ADDR'],$link1,$flag);
}
		//////////////////////////////customer details//////////////////////////////////////////
	$usr_add="update customer_master set   customer_name='".$customer_name."', address1='".$address."', pincode='".$pincode."', cityid='".$locationcity."', stateid='".$locationstate."',phone='".$res_no."',email='".$email."', alt_mobile='".$phone2."', update_date='".$today."', update_by='".$_SESSION['asc_code']."',landmark='".$landmark."',reg_name='".$reg_name."',gst_no='".$gst_no."' where customer_id='".$customer_id."'";
    $res_add=mysqli_query($link1,$usr_add); 
    $row_update=mysqli_affected_rows($link1); 
    if($row_update > 0){
         $msg="Update Customer Information successfully"; 
        $flag = dailyActivity($_SESSION['userid'],"Customer Update","Customer Update","Customer Update",$_SERVER['REMOTE_ADDR'],$link1,$flag);
    }
if ($flag) {
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
		$msg = $msg;
	} else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
 header("location:customer_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&mobileno=".$phone1."&customer_id=".$customer_id."&email_id=".$email."&status=1");
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


function show_add(){
    $("#add_new_address").css("display", "");
    $("#hide").css("display", "");

}
     function hide_add(){
    $("#add_new_address").css("display", "none");
    $("#show").css("display", ""); 
           $("#hide").css("display", "none");
}
     function otherget_citydiv(){

	  var name=$('#otherlocationstate').val();

	  $.ajax({

	    type:'post',


		url:'../includes/getAzaxFields.php',

		data:{oterstate:name},

		success:function(data){

	    $('#othercitydiv').html(data);

	    }

	  });

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
      <h2 align="center"><i class="fa fa-id-badge"></i> Enter Complaint Details</h2>
		<form  name="frm1" id="frm1" class="form-horizontal" enctype="multipart/form-data"  action="" method="post">
        <div class="panel-group">
            <div class="panel panel-info">
              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
              <div class="panel-body">
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Name <span class="red_small">*</span></label>
                      <div class="col-md-6">
                      	<input name="customer_name" id="customer_name" type="text" value="<?=$row_customer['customer_name'];?>" class="form-control required" <?php if($row_customer['customer_name']!=''){?>  <?php }else{}?>/>
					 <input name="customer_id" id="customer_id" value="<?=$row_customer['customer_id'];?>" type="hidden"/>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical" ><?=$row_customer['address1'];?></textarea>
                      </div>
                    </div>
                  </div>
				                    <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Landmark  <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        	<input name="landmark" id="landmark" type="text" class="form-control  required" value="<?=$row_customer['landmark'];?>"  /> 
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Pincode  <span class="red_small">*</span></label>
                      <div class="col-md-6">
                              <input name="pincode" type="text" class="digits form-control required" id="pincode" value="<?=$row_customer['pincode']?>" >
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No. <!--<span class="small">(For SMS Update)</span>--> <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();"   value="<?php if($row_customer['mobile']!=''){ echo $row_customer['mobile'];}else{ echo $_REQUEST['mobileno'];}?>">
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No. <span class="red_small">*</span></label>
                      <div class="col-md-6">
                      <input name="phone2" type="text" class="digits form-control required" id="phone2" maxlength="10" value="<?=$row_customer['alt_mobile'];?>">
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

                          <input name="email" type="email" class="email form-control" id="email" value="<?=$row_customer['email'];?>"  >

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

                        <input name="res_no" type="text" class="digits form-control" id="res_no" value="<?=$row_customer['phone']?>"  >

                      </div>

                    </div>

                  </div>
				  
<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">GST No.</label>

                        <div class="col-md-6" id="citydiv">

                         <input name="gst_no" type="text" class=" form-control" id="gst_no" value="<?=$row_customer['gst_no']?>" >
                      </div>

                    </div>

                   <div class="col-md-6"><label class="col-md-6 custom_label">Registration Name.</label>

                      <div class="col-md-6">

                        <input name="reg_name" type="text" class=" form-control" id="reg_name" value="<?=$row_customer['reg_name']?>"  >

                      </div>

                    </div>

                  </div>
              </div>

            </div>
		  <!------------- End Image Uploder --------------->
                  <div class="form-group">

                    <div class="col-md-12" align="center">

                      <span id="errmsg" class="red_small"></span>

                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='customer_list.php?<?=$pagenav?>&customer_id=<?=$_REQUEST['customer_id']?>'">&nbsp;
						<input name="wsd" id="wsd" value="<?=$_REQUEST['p_wsd'];?>" type="hidden"/>
                     
                   

                      <input type="submit" class="btn<?=$btncolor?>" name="savejob" id="savejob" value="Save" title="Save Job Details" <?php if($_POST['savejob']=='Save'){?>disabled<?php }?>>&nbsp;

                    </div>

                  </div> 

            </div>

        </form>
        <?php
        	$sql_cust_other	= mysqli_query($link1, "select  *  from customer_other_address  where customer_id='".$row_customer['customer_id']."'  order by id desc");
        if(mysqli_num_rows($sql_cust_other) > 0){
        ?>
        <div class="panel panel-info">

              <div class="panel-heading" align="center"> Other Address Details</div>

              <div class="panel-body">
			 <table class="table table-bordered" width="100%">

                    	

                        <thead>

                        	<tr>

                            	<td><strong>S.No.</strong></td>

                                <td><strong> ID</strong></td>

				                 <td><strong>Name</strong></td>
								 <td><strong>Address</strong></td>
                                <td><strong>Landmark</strong></td>
                                <td><strong>State</strong></td>
                                 <td><strong>City</strong></td>
							
								  <td><strong>Mobile No.</strong></td>

                                <td><strong>Residence No</strong></td>
                                 <td><strong>Residence Name</strong></td>
								<td><strong>Edit</strong></td>
								
                            </tr>

                        </thead>

                        <tbody>
						

		
		 			<?php 
					

				
					
while($row_customer_other=mysqli_fetch_array($sql_cust_other)){

						?> <tr> 
                            	<td><?=$k+1;?></td>

                                <td><?=$row_customer_other['customer_id']?></td>

                                <td><?=$row_customer_other['customer_name']?></td>

                                <td><?=$row_customer_other['address1']?></td>
<td><?=$row_customer_other['landmark']?></td>
                                 <td><?php echo getAnyDetails($row_customer_other["stateid"],"state","stateid","state_master",$link1);?></td>
								<td><?php echo getAnyDetails($row_customer_other["cityid"],"city","cityid","city_master",$link1);?></td>

							
								 <td><?=$row_customer_other['alt_mobile']?></td>
								  <td><?=$row_customer_other['phone']?></td>
                            <td><?=$row_customer_other['reg_name']?></td>

  <td><div align="center"><a href="customer_list.php?id=<?=$row_customer_other['id']?>"".$pagenav." title='view'><i class='fa fa-eye fa-lg faicon' title='view job details'></i></a></div></td>
							
							
							</tr>

						<?php
 
						}

                        ?>

                        </tbody>

                    </table> 
			  
			  </div>
			   </div>
			<?php } ?>  
<!--<div class="col-lg-2" id="show"><button onclick="show_add();" type="button" class="btn btn-success">Add New Address</button></div>-->
        <div class="col-lg-2" id="hide" style="display:none;"><button onclick="hide_add();" type="button" class="btn btn-danger">No Required Other Address</button></div>
    </div><!--End col-sm-9-->
<div class="<?=$screenwidth?>" id="add_new_address" style="display:none;">
      <h2 align="center"><i class="fa fa-id-badge"></i> Add New  Address Details</h2>
		<form  name="frm1" id="frm1" class="form-horizontal" enctype="multipart/form-data"  action="" method="post">
        <div class="panel-group">
            <div class="panel panel-info">
              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Address Details</div>
              <div class="panel-body">
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Name <span class="red_small">*</span></label>
                      <div class="col-md-6">
                      	<input name="othercustomer_name" id="othercustomer_name" type="text" value="" class="form-control required" required />
						<input name="othercusto_id" id="othercusto_id" type="hidden" value="<?=$row_customer['customer_id'];?>" class="form-control required"/>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <textarea name="otheraddress" id="otheraddress" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical" required ></textarea>
                      </div>
                    </div>
                  </div>
				                    <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Landmark  <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        	<input name="otherlandmark" id="otherlandmark" type="text" class="form-control  required" required value=""  /> 
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Pincode  <span class="red_small">*</span></label>
                      <div class="col-md-6">
                              <input name="otherpincode" type="text" class="digits form-control required" id="otherpincode" required value="" >
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No. <!--<span class="small">(For SMS Update)</span>--> <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" readonly  maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?php if($row_customer['mobile']!=''){ echo $row_customer['mobile'];}else{ echo $_REQUEST['mobileno'];}?>">
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No. <span class="red_small"></span></label>
                      <div class="col-md-6">
                      <input name="otherphone2" type="text" class="digits form-control" id="otherphone2" maxlength="10" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 custom_label">State <span class="red_small">*</span></label>
                      <div class="col-md-6">
                         <select name="otherlocationstate" id="otherlocationstate" class="form-control required"  onchange="otherget_citydiv();" required >
                          <option value=''>--Please Select--</option>
                          <?php 
						 $state_query="select stateid, state from state_master where countryid='1' order by state";
						 $state_res=mysqli_query($link1,$state_query);
						 while($row_res = mysqli_fetch_array($state_res)){?>
						   <option value="<?=$row_res['stateid']?>"><?=$row_res['state']?></option>

						 <?php }?> 	

                        </select>               

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">City <span class="red_small">*</span></label>

                        <div class="col-md-6" id="othercitydiv">

                       <select name="otherlocationcity" id="otherlocationcity" class="form-control required" required >

                       <option value=''>--Please Select-</option>

                       <?php 

					  

						 $city_query="SELECT cityid, city FROM city_master where stateid='".$row_customer['stateid']."' and cityid='".$row_customer['cityid']."'";

						 $city_res=mysqli_query($link1,$city_query);

						 while($row_city = mysqli_fetch_array($city_res)){

						?>

						<option value="<?=$row_city['cityid']?>"><?=$row_city['city']?></option>

						<?php }

					

						?>

                       </select>

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                

                   <div class="col-md-6"><label class="col-md-6 custom_label">Residence No.</label>

                      <div class="col-md-6">

                        <input name="otherres_no" type="text" class="digits form-control" id="otherres_no" value=""  >

                      </div>

                    </div>
   <div class="col-md-6"><label class="col-md-6 custom_label">Registration Name.</label>

                      <div class="col-md-6">

                        <input name="otherreg_name" type="text" class=" form-control" id="otherreg_name" value=""  >

                      </div>

                    </div>
                  </div>
              </div>

            </div>
		  <!------------- End Image Uploder --------------->
                  <div class="form-group">

                    <div class="col-md-12" align="center">

                      <span id="othererrmsg" class="red_small"></span>

                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='customer_list.php?<?=$pagenav?>&customer_id=<?=$_REQUEST['customer_id']?>'">&nbsp;
						<input name="otherwsd" id="otherwsd" value="<?=$_REQUEST['p_wsd'];?>" type="hidden"/>
                      <input name="othercustomer_id" id="othcustomer_id" value="<?=$_REQUEST['customer_id'];?>" type="hidden"/>
                   

                      <input type="submit" class="btn<?=$btncolor?>" name="addotheraddr" id="addotheraddr" value="Add New Address" title="Add New Address">&nbsp;

                    </div>

                  </div> 

            </div>

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