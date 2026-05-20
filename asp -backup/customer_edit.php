<?php

require_once("../includes/config.php");
////get access ASP details


////////////// update by jitender on dec 11 for repair and bounce type call for claim process ////////////////////////////////////////
if($_REQUEST['mobileno']){
$srch_criteria = "mobile = '".$_REQUEST['mobileno']."' ";
}else if($_REQUEST['email_id']){
$srch_criteria = "email = '".$_REQUEST['email_id']."'";
}else if($_REQUEST['customer_id']){
$srch_criteria = "customer_id = '".$_REQUEST['customer_id']."'";
}else{
$srch_criteria="";
}

$sql_cust	= mysqli_query($link1, "select  *  from customer_master  where ".$srch_criteria."   order by id desc");

$row_customer=mysqli_fetch_array($sql_cust);



////// final submit form ////

if($_POST['savejob']=='Save'){


	@extract($_POST);


	
		//////////////////////////////customer details//////////////////////////////////////////


	$usr_add="update customer_master set   customer_name='".ucwords($customer_name)."', address1='".ucwords($address)."', pincode='".$pincode."', cityid='".$locationcity."', stateid='".$locationstate."', email='".$email."',  phone='".$res_no."', mobile='".$phone1."', alt_mobile='".$phone2."', update_date='".$today."', update_by='".$_SESSION['asc_code']."',landmark='".ucwords($landmark)."',reg_name='".ucwords($reg_name)."',gst_no='".strtoupper($gst_no)."' ,mrg_date='".$mrg_date."',dob_date='".$dob_date."' where customer_id='".$customer_id."'";
$res_add=mysqli_query($link1,$usr_add); 




	


	////// insert in activity table////

	$flag = dailyActivity($_SESSION['userid'],"Customer Update","Customer Update","Customer Update",$_SERVER['REMOTE_ADDR'],$link1,$flag);

	///// check both master and data query are successfully executed



   ///// move to parent page

 header("location:complaint_create.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&mobileno=".$phone1."&customer_id=".$customer_id."&email_id=".$email."&status=1");
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
	$(document).ready(function(){

        $("#frm1").validate();

    });

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


$(document).ready(function () {

		$('#dob_date').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
	$('#mrg_date').datepicker({
		format: "yyyy-mm-dd",
		endDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});


});
		 function getmapinstate(){
	
	  var pincode=$('#pincode').val();
	
	
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{Locpinstate:pincode},
		success:function(data){
	
	    $('#loc_pincodestate').html(data);
	    }
	  });
	
	};
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

      <h2 align="center"><i class="fa fa-id-badge"></i> Update Customer Details</h2>

   

		<form  name="frm1" id="frm1" class="form-horizontal" enctype="multipart/form-data"  action="" method="post">

        <div class="panel-group">

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>

              <div class="panel-body">

              	

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Name <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	<input name="customer_name" id="customer_name" type="text" value="<?=$row_customer['customer_name'];?>" class="form-control required" <?php if($row_customer['customer_name']!=''){?> readonly <?php }else{}?>/>
						<input name="custo_id" id="custo_id" type="hidden" value="<?=$row_customer['customer_id'];?>" class="form-control required"/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical" ><?=$row_customer['address1'];?></textarea>

                      </div>

                    </div>

                  </div>
				                    <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Landmark </label>

                      <div class="col-md-6">

                        	<input name="landmark" id="landmark" type="text" class="form-control  " value="<?=$row_customer['landmark'];?>"  /> 

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Pincode  <span class="red_small">*</span></label>

                      <div class="col-md-6">

                              <input name="pincode" type="text" class="digits form-control required" id="pincode" maxlength="6"  minlength="6" value="<?=$row_customer['pincode']?>"  required>

                      </div>

                    </div>

                  </div>

                   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No. <!--<span class="small">(For SMS Update)</span>--> <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?php if($row_customer['mobile']!=''){ echo $row_customer['mobile'];}else{ echo $_REQUEST['mobileno'];}?>"  <?php if($row_customer['mobile']!=''){?> readonly <?php }else{}?> >

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No.</label>

                      <div class="col-md-6">

                      <input name="phone2" type="text" class="digits form-control " id="phone2" maxlength="10" value="<?=$row_customer['alt_mobile'];?>">

                      </div>

                    </div>

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">State <span class="red_small">*</span></label>

                      <div class="col-md-6"  id="loc_pincodestate">

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

                    <div class="col-md-6"><label class="col-md-6 custom_label">Date Of Birth. <!--<span class="small">(For SMS Update)</span>--></label>

                      <div class="col-md-6">

                     <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="dob_date"  id="dob_date" style="width:150px;" value="<?php if( $row_customer['dob_date']!='0000-00-00'  ){ echo $row_customer['dob_date'];?>  <?php }else{ echo "";}?>"   ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Marriage Date.</label>

                      <div class="col-md-6">

                    <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="mrg_date"  id="mrg_date" style="width:150px;" value="<?php if($row_customer['mrg_date']!='0000-00-00'){ echo $row_customer['mrg_date'];?>  <?php }else{ echo "";}?>"   ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

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

                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='complaint_create.php?<?=$pagenav?>&customer_id=<?=$_REQUEST['customer_id']?>'">&nbsp;
						<input name="wsd" id="wsd" value="<?=$_REQUEST['p_wsd'];?>" type="hidden"/>
                      <input name="customer_id" id="customer_id" value="<?=$_REQUEST['customer_id'];?>" type="hidden"/>
                   

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