<?php
require_once("../includes/config.php");
////get access ASP details
////// final submit form ////

if($_POST['savejob']=='Save'){


	@extract($_POST);

	//// initialize transaction parameters

	$flag = true;

	mysqli_autocommit($link1, false);

	$error_msg="";
	
	
	$query_code="select MAX(req_no) as qa from asc_appo_request where 1";
	$result_code=mysqli_query($link1,$query_code);
	$arr_result2=mysqli_fetch_array($result_code);
	$code_id=$arr_result2[0]+1;
	$pad="ASPA".$todayt.$code_id;
	
	$sql="INSERT INTO asc_appo_request set name='".$_POST['dis_name']."', pre_asc='".$_POST['asc_name']."', assc_ten='".$_POST['ass_ten']."', 	req_no='".$code_id."', request_no='".$pad."', brand='".$_POST['brand']."', dis_distric='".$_POST['district']."', state='".$_POST['state']."',city='".$_POST['city']."', qty='".$_POST['ytm_qty']."', m1='".$_POST['m1']."',m2='".$_POST['m2']."', m3='".$_POST['m3']."',tm='".$_POST['tm']."', asc_state='".$_POST['asc_state']."', asc_city='".$_POST['asc_city']."', asc_dis='".$_POST['asc_dis']."',ec='".$_POST['ec']."', ec3='".$_POST['ec3']."', nd='".$_POST['nd']."', pma='".$_POST['pma']."', status='7', type='".$_SESSION['id_type']."',asc_type='".$_POST['asc_type']."',asc_remark='".$_POST['ascremark']."',update_by='".$_SESSION['userid']."',requestdate='".$today."'";

mysqli_query($link1,$sql)or die("error in insertion1".mysqli_error($link1));

$sql1="INSERT INTO remark_master set req_id='".$pad."',module='APPO_REQ',remark='ASP Appointment Request', status='pending', type='".$_SESSION['id_type']."',req_by='".$_SESSION['userid']."',outcome='ASP Appointment Request'";
mysqli_query($link1,$sql1)or die("error in insertion2".mysqli_error($link1));
	
	
 $email=mysqli_query($link1,"select email from email_user where (type='admin' or type='HO' )");
 //$email_to=mysql_fetch_array($email);
 $cn=mysqli_num_rows($email);

$toemail="";
while($row=mysqli_fetch_array($email)){
	if($toemail==""){
	    $toemail.=$row[email];
	}else{
		$toemail.=",".$row[email];
	}
}
 $toemail;

$message = "Dear Sir ,<br />";
$message.="<br>Below ticket has beeen raised for appointment of new ASC .<br />";
$message.="<br>Request No  :".$pad."<br />";
$message.="<br>Request Date: ".$today."<br />";
$message.="<br>Kindly check your CRM id for approve the same<br />";
// Always set content-type when sending HTML email
$headers1 = "MIME-Version: 1.0\r\n";
$headers1 .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers1 .= "From:doNotReply@cancrm.in". "\r\n";
$subject = "ASP Appointment Request";
mail($toemail,$subject,$message ,$headers1);

	////// insert in activity table////

	$flag = dailyActivity($_SESSION['userid'],$pad,"ASP Appointment Request","ASP Appointment Request",$_SERVER['REMOTE_ADDR'],$link1,$flag);

	///// check both master and data query are successfully executed

	if ($flag) {

		mysqli_commit($link1);
		////// return message
		$msg="Request ".$pad." has been Sent!";
		$cflag="success";
		$cmsg="Success";

	} else {

		mysqli_rollback($link1);

		$cflag="danger";

		$cmsg="Failed";

		$msg = "Request could not be processed. Please try again. ".$error_msg;

	} 


	

   

 header("location:asp_appo_detail.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."");
	//exit;

}

//echo "SELECT * FROM asc_appo_request  where sno='".$_REQUEST['sno']."'";

$req_dels = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM asc_appo_request  where sno='".$_REQUEST['sno']."'"));




?>

<!DOCTYPE html>

<html>

<head>

 <meta charset="utf-8">

 <meta name="viewport" content="width=device-width, initial-scale=1">

 <title><?=siteTitle?></title>

 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">

 <script src="../js/jquery-1.10.2.js"></script>

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

 <script language="javascript" type="text/javascript">
 /////////// function to get city on the basis of state







	

	
	
function get_cityASP() {
	
	  var pincode=$('#asc_city').val();

	
	
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{locbycity:pincode},
		success:function(data){
	
	//alert(data);
		if(data!=""){
	    $('#ascdivasc').html(data);
			
		}
	    }
	  });
		
};



 function get_dist_details(val){

	  var disname=val;

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{distdetails:disname},

		success:function(data){
	console.log(data);
	    if(data!=""){
	repval=data.split("~");
			
	document.getElementById("state").value=repval[0];
	document.getElementById("city").value=repval[1];
 document.getElementById("ass_ten").value=repval[2];

		}

	    }

	  });
	 

 }
function qty_calc()
{
	var abc=document.getElementById("ytm_qty").value;
	//alert (abc);
	abc1=(abc *1)/100;
		document.getElementById("ec").value=Math.round(abc1);
//Math.round(2.5);
}
function qty3_calc()
{
	var abc2=document.getElementById("tm").value;
	var abc7=document.getElementById("ytm_qty").value;
	 add_qu=parseFloat(abc2)+parseFloat(abc7);
	//alert (abc2);
	abc3=(add_qu *1)/100;
		document.getElementById("ec3").value=Math.round(abc3);
		//Math.round(2.5);
}
function business_quant()
{
	var abcA=document.getElementById("m1").value;
	var abcB=document.getElementById("m2").value;
	var abcC=document.getElementById("m3").value;
	business=parseFloat(abcA)+parseFloat(abcB)+parseFloat(abcC);
	//alert (abc2);
	//abc3=(add_qu *1)/100;
	document.getElementById("tm").value=Math.round(business);
		
		
		//Math.round(2.5);

}


 function get_citydiv(){

	  var name=$('#asc_state').val();

	  $.ajax({

	    type:'post',


		url:'../includes/getAzaxFields.php',

		data:{stateascapp:name},

		success:function(data){
	
	    $('#citydiv').html(data);
		//	$('#pincode').val("");

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

<body>


<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnav2.php");

    ?>

    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa fa-id-badge"></i> ASC Appointment Request</h2>



		<form  name="frm1" id="frm1" class="form-horizontal" enctype="multipart/form-data"   autocomplete="off"  action="" method="post">

        <div class="panel-group">

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Party Details</div>

              <div class="panel-body">

              	  <div class="form-group">
              	    <div class="col-md-6">
              	      <label class="col-md-6 custom_label">Brand <span class="red_small">*</span></label>
                      <div class="col-md-6">
                         <select name="brand" id="brand" class="form-control required" required>

               
						    <option value=''>--Select Brand--</option>
                          <?php

						  	

							$dept_query="SELECT * FROM brand_master where status = '1'  order by brand";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>

						  <option value="<?=$br_dept['brand_id']?>"<?php if($req_dels['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>

						<?php }?>

                        </select>
                      </div>
           	        </div>
              	    <div class="col-md-6"><label class="col-md-6 custom_label">Distributor Name<span class="red_small"></span></label>

                      <div class="col-md-6">
	   <select name="dis_name" id="dis_name" class="form-control required"  onchange="return get_dist_details(this.value);" >
                        <option value="">Please Select</option>
                        <?php
                       $lctype_query="select location_code,locationname  from location_master where  locationtype='DIST' and statusid='1' order by locationname";
                        $check_lctype=mysqli_query($link1,$lctype_query);
                        while($br_lctype = mysqli_fetch_array($check_lctype)){
                        ?>
                        <option value="<?=$br_lctype['location_code']?>"<?php if($req_dels['name']==$br_lctype['location_code']){ echo "selected";}?>><?php echo $br_lctype['locationname']?></option>
                        <?php } ?>
                      </select>
  

                      </div>

                    </div>

                </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Current Business Qty(No of Unit sold till date)&nbsp; <span class="red_small">*</span></label>

                      <div class="col-md-6">

                     <input type="text" name="ytm_qty" id="ytm_qty" class="form-control required" required   maxlength="7"  onKeyPress="return onlyNumbers(this);"  onblur="return qty_calc(this),qty3_calc(this)" value="<?=$req_dels['qty']?>">

                      </div>

                    </div>

                                      <div class="col-md-6"><label class="col-md-6 custom_label">Association Tenure <span class="red_small">*</span></label>

                      <div class="col-md-6" >
<input type="text" class="form-control "    name="ass_ten"  id="ass_ten"  readonly="readonly" value="<?=$req_dels['assc_ten']?>" >
                   

                      </div>

                    </div>

                  </div>
				                    <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">M+1(Sales Quantity)&nbsp;  </label>

                      <div class="col-md-6">

                   <input type="text" name="m1" id="m1" class="form-control required" required  maxlength="4" onKeyPress="return onlyNumbers(this);" value=" <?=$req_dels['m1']?>"  onblur="return business_quant(),qty3_calc(this)">

                      </div>

                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">state <span class="red_small">*</span></label>

                        <div class="col-md-6" >

                  <input type="text" name="state" id="state" class="form-control required" readonly="readonly" value="<?=$req_dels['state']?>" >

                   

                      </div>

                    </div>


                  </div>

                   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">M+2(Sales Quantity) <span class="red_small">*</span></label>

                      <div class="col-md-6">

                   <input type="text" name="m2" id="m2" class="form-control required" maxlength="4" onKeyPress="return onlyNumbers(this);"  onblur="return business_quant(),qty3_calc(this)" value="<?=$req_dels['m2']?>">

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">City<span class="red_small"></span></label>

                      <div class="col-md-6" >

                 <input type="text" name="city" id="city" class="form-control required" readonly="readonly" value="<?=$req_dels['city']?>">

                      </div>

                    </div>

                  </div>

                
                  <div class="form-group">


					
					  <div class="col-md-6"><label class="col-md-6 custom_label">M+3(Sales Quantity)&nbsp;  <span class="red_small">*</span></label>

                      <div class="col-md-6">

                     <input type="text" name="m3" id="m3" class="form-control required"  maxlength="4" value="<?=$req_dels['m3']?>" onKeyPress="return onlyNumbers(this);"  onblur="return business_quant(),qty3_calc(this)">

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Next 3 Month Planned Business Qty(No of Unit to be sold)</label>

                      <div class="col-md-6">

                        <input type="text" name="tm" id="tm" class="form-control required" maxlength="7" onKeyPress="return onlyNumbers(this);"   onblur="return qty3_calc(this)" readonly="readonly" value="<?=$req_dels['tm']?>">
                      </div>

                    </div>

                  </div>

              
				  
				
            </div>
			    </div>


        

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;ASC Required Location</div>

              <div class="panel-body">

              	<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">state <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <select name="asc_state" id="asc_state" class="form-control required"   onchange="get_citydiv();" required >

                          <option value=''>--Please Select--</option>

                          <?php 

						 $state_query="select stateid, state from state_master where countryid='1' order by state";

						 $state_res=mysqli_query($link1,$state_query);

						 while($row_res = mysqli_fetch_array($state_res)){?>

						   <option value="<?=$row_res['stateid']?>"<?php if($req_dels['asc_state']==$row_res['stateid']){ echo "selected";}?>><?=$row_res['state']?></option>

						 <?php }?> 	

                        </select>               

                      </div>

                    </div>

  <div class="col-md-6"><label class="col-md-6 custom_label">ASP City </label>

                      <div class="col-md-6" id="citydiv">

	    <select name="asc_city" class="form-control required"  id="asc_city">
                   
					   <?php 

					  

						 $city_query="SELECT cityid, city FROM city_master where stateid='".$req_dels['asc_state']."' and cityid='".$req_dels['asc_city']."'";

						 $city_res=mysqli_query($link1,$city_query);

						 while($row_city = mysqli_fetch_array($city_res)){

						?>
 <option value=""<?php if($req_dels['cityid']==""){ echo "selected";}?>>Select City</option>
						<option value="<?=$row_city['cityid']?>"<?php if($req_dels['asc_city']==$row_city['cityid']){ echo "selected";}?>><?=$row_city['city']?></option>

						<?php }

					

						?>

                       </select>
                  </select>
                      </div>

                    </div>
                  </div>

              	<div class="form-group">
				
				
                    <div class="col-md-6"><label class="col-md-6 custom_label">Nearest ASC Name <span class="red_small">*</span></label>

                      <div class="col-md-6" id="ascdivasc" >

                       
                  <select name="asc_name" class="form-control required" id="asc_name">
                         <?php
                       $lctype_query="select location_code,locationname  from location_master where location_code='".$req_dels['pre_asc']."' order by locationname";
                        $check_lctype=mysqli_query($link1,$lctype_query);
                        while($br_lctype = mysqli_fetch_array($check_lctype)){
                        ?>
                        <option value="<?=$br_lctype['location_code']?>"<?php if($req_dels['pre_asc']==$job_det_t['current_location']){ echo "selected";}?>><?php echo $br_lctype['locationname']?></option>
                        <?php } ?>
                      </select>
                  </select>
             

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Nearest ASC Repair Load <span class="red_small">*</span></label>

                      <div class="col-md-6" id="modeldiv">

                 <input name="pma" type="text" id="pma" value=" <?=$req_dels['pma']?>"   maxlength="4" class="form-control required"   onKeyPress="return onlyNumbers(this);">

                      </div>

                    </div>

                  

                  </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Distance of Nearest ASC from Proposed ASC City <span class="red_small">* IN KM</span></label>

                      <div class="col-md-6">
<input type="text" name="nd" id="nd" class="form-control required"  maxlength="4"  value=" <?=$req_dels['nd']?>"    onKeyPress="return onlyNumbers(this);">

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">ASC Type&nbsp;</label>

                      <div class="col-md-6">

             <select name="asc_type" id="asc_type" class="form-control required">
                  <option value=""<?php if($req_dels['asc_type']==''){ echo "selected";}?>> Select ASC type</option>
                  <option value="new_asc"<?php if($req_dels['asc_type']=='new_asc'){ echo "selected";}?>>New ASC</option>
                  <option value="repl_asc"<?php if($req_dels['asc_type']=='repl_asc'){ echo "selected";}?>>Replacement ASC</option>
                </select>

                      </div>

                    </div>

                  </div>


                  <div class="form-group">
						<div class="col-md-6"><label class="col-md-6 custom_label">Expt. Repair Load (Begining) p.m&nbsp;</label>

                      <div class="col-md-6">

            
<input type="text" name="ec"  class="form-control required"  value=" <?=$req_dels['ec']?>"  onKeyPress="return onlyNumbers(this);" id="ec" readonly="readonly" value="0">
                      </div>
                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Expt. Repair Load (After 3 Month) p.m&nbsp; <span class="red_small">*</span></label>

                      <div class="col-md-6">

                   <input name="ec3" type="text"   value=" <?=$req_dels['ec3']?>"  class="form-control required" id="ec3" onKeyPress="return onlyNumbers(this);" readonly="readonly" value="0">

                      </div>

                    </div>

                  </div>

             
				  
				                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Justification&nbsp <span class="red_small">*</span></label>

                      <div class="col-md-6">
					<input type="text" name="ascremark" id="ascremark" value=" <?=$req_dels['asc_remark']?>"   class="form-control required">
                      </div>

                    </div>


                    <div class="col-md-6"><label class="col-md-6 custom_label"></label>

                      <div class="col-md-6">

                         	

                      </div>

                    </div>

                  </div> 
				 
   <div class="form-group">

                    <div class="col-md-12" align="center">

                      <span id="errmsg" class="red_small"></span>

                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_appo_detail.php?<?=$pagenav?>'">&nbsp;
					
                      
                   

                      <input type="submit" class="btn<?=$btncolor?>" name="savejob" id="savejob" value="Save" title="Save Job Details" <?php if($_POST['savejob']=='Save'){?>disabled<?php }?>>&nbsp;

                    </div>

                  </div> 

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