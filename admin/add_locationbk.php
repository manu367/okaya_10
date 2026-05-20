<?php
require_once("../includes/config.php");
////// final submit form ////
@extract($_POST);
if($_POST['Submit']=='Save'){
    // insert all details of location //
    	$sql="INSERT INTO location_master set erpid='".$erp_id."', othid='".$oth_id."',locationname='".ucwords($party_name)."',locationtype='".$party_type."', partner_type='".$propritortype."', contact_person='".ucwords($contact_person)."',landlineno='".$helpline_no."',emailid='".$email."',contactno1='".$phone1."',contactno2='".$phone2."',locationaddress='".ucwords($address)."',dispatchaddress='".ucwords($address)."',deliveryaddress='".ucwords($ship_address)."',districtid='".$locationdistrict."',cityid='".$locationcity."',stateid='".$locationstate."',countryid='".$country."',zipcode='".$pincode."',statusid='1',loginstatus='1',gstno='".$gst_no."',panno='".$pan_no."',cin='".$cin."',oth_taxr_no='".$othtaxr_no."',oth_tax_name='".$othtax_name."',createby='".$_SESSION['userid']."',createdate='".$datetime."',price_lvl='".$part_price."',entity_type='".$entity_code."',zone='".$zone."', balance_limit = '".$balance_limit."' ";
		
	   mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));
	
	   $insid = mysqli_insert_id($link1);
	   /// make 4 digit padding
	   $pad=str_pad($insid,4,"0",STR_PAD_LEFT);
	   //// make logic of employee code
	   $newlocationcode="RVS".$party_type.$pad;

	   //////// update system genrated code in location
	   mysqli_query($link1,"UPDATE location_master set location_code='".$newlocationcode."', pwd='".$newlocationcode."' where locationid='".$insid."'")or die("ER2".						mysqli_error($link1));
   
   /////////// mail send //////
   if($insid != ""){
   		$to_location_info = mysqli_fetch_array(mysqli_query($link1, "select location_code, pwd, locationname, emailid from location_master where locationid='".$insid."' "));
   		if($to_location_info['emailid']!=""){
		
			$toemail =$to_location_info['emailid'].",".'serviceonsite2018@gmail.com'."";
			//$toemail =$to_location_info['emailid'].",".'crmcare@candoursoft.com'.",".'jitugupta20121989@gmail.com'."";
			
			$urll = "http://rv.cancrm.in/beta/";
			
			$message="<table>";
			$message.="<tr><td>To ".$to_location_info['locationname'].",</td></tr>";
			$message.="<tr><td>Dear Sir/Mam,</td></tr><tr><td> </td></tr>";
			$message.="<tr><td>You are now connected with our CRM.</td></tr><tr><td> </td></tr>";
			$message.="<tr><td>We hereby inform you that your login credentials are given below.</td></tr>";
			$message.="<tr><td> URL : ".$urll." </td></tr>";
			$message.="<tr><td> USER ID : ".$to_location_info['location_code']." </td></tr>";
			$message.="<tr><td> PASSWORD : ".$to_location_info['pwd']." </td></tr><tr><td> </td></tr>";
			$message.="<tr><td>With Best Regards,</td></tr>";
			$message.="<tr><td>RV Solutions Pvt Ltd</td></tr>";
			$message.="</table>";
				
			
			// Always set content-type when sending HTML email
			$headers1 = "MIME-Version: 1.0\r\n";
			$headers1 .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers1 .= "From:doNotReply@rvsolutions.com". "\r\n";
			$subject = "CRM login credentials";
			$data = mail($toemail,$subject,$message ,$headers1);
			if($data){	
				mysqli_query($link1, "update location_master set mailsend = 'Y' where location_code = '".$to_location_info['location_code']."' ");
			} 
			
		}
   }
   ////////////////////////////

   ///// entry in job counter 
   $sql_jobcount="INSERT INTO job_counter set location_code='".$newlocationcode."', job_count='0',job_series='VS".$insid."' ";
   
   mysqli_query($link1,$sql_jobcount)or die("ER2".mysqli_error($link1));

   ///// entry in invoice counter 
	
$yr=0;
$yr1=0;
if(date('m')<'04'){
	$yr=date('y');
	//$yr1=date('y');
}else{
	$yr=date('y')+1;
	//$yr1=date('y')+1;
}
$cyr=$yr;	
	
   $sql_invcount="INSERT INTO invoice_counter set location_code='".$newlocationcode."',fy='".$cyr."/',inv_series='I".$pad."/', inv_counter='0', stn_series='DC".$pad."/',stn_counter='0'";
   
   mysqli_query($link1,$sql_invcount)or die("ER2.1".mysqli_error($link1));

   ///// entry in current cr status
   $sql_crlimit="INSERT INTO current_cr_status set location_code='".$newlocationcode."',  credit_bal='0.00',   credit_limit='0.00',   total_credit_limit='0.00'";
   
   mysqli_query($link1,$sql_crlimit)or die("ER3".mysqli_error($link1));

    ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$newlocationcode,"LOCATION","ADD",$ip,$link1,"");
	$msg="You have successfully created a new location with ref. no. ".$newlocationcode;
	
	////// return message

	///// move to parent page
    header("Location:location_master.php?msg=".$msg."".$pagenav);
	exit;
}

?>



<!DOCTYPE html>

<html>

<head>

 <meta charset="utf-8">

 <meta name="viewport" content="width=device-width, initial-scale=1">

 <title><?=siteTitle?></title>

 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">

 <script src="../js/jquery.min.js"></script>

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

 <script src="../js/frmvalidate.js"></script>

 <script type="text/javascript" src="../js/jquery.validate.js"></script>

 <script type="text/javascript" src="../js/common_js.js"></script>

 <script language="javascript" type="text/javascript">

  /////////// function to get state on the basis of circle

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

 /////////// function to get district on the basis of state

 function get_distdiv(){

	  var name=$('#locationstate').val();

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{state2:name},

		success:function(data){

	    $('#distctdiv').html(data);

	    }

	  });

   

 } 

  </script>

<body>

<div class="container-fluid">

  <div class="row content">

	<?php 

    include("../includes/leftnav2.php");

    ?>

    <div class="<?=$screenwidth?>">

      <h2 align="center"><i class="fa fa-id-badge"></i> Add New <?=$locationstr?></h2><br/><br/>

      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >

          <form  name="frm1" id="frm1" class="form-horizontal"  autocomplete="off"  action="" method="post">

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

            <div class="col-md-6"><label class="col-md-6 control-label">Party Type <span class="red_small">*</span></label>

              <div class="col-md-6">

               <select name="party_type" id="party_type" class="form-control required" required>

                  <option value="">--Please Select--</option>

                <?php

				$lctype_query="SELECT * FROM  location_type_master order by displayname";

				$check_lctype=mysqli_query($link1,$lctype_query);

				while($br_lctype = mysqli_fetch_array($check_lctype)){

				?>

                <option value="<?=$br_lctype['usedname']?>"<?php if($_REQUEST['party_type']==$br_lctype['usedname']){ echo "selected";}?>><?php echo $br_lctype['displayname']?></option>

                <?php }?>

                </select>

              </div>

            </div>

          </div>

          <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">State <span class="red_small">*</span></label>

              <div class="col-md-6" id="statediv">

                 <select name="locationstate" id="locationstate" class="form-control required" required>

                  <option value=''>--Please Select--</option>

                

                </select>               

              </div>

            </div>

            <div class="col-md-6"><label class="col-md-6 control-label">Party Name <span class="red_small">*</span></label>

              <div class="col-md-6"> 

               <input name="party_name" type="text" class="form-control required" required id="party_name" style="background-color:#FFFFCC">

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

            <div class="col-md-6"><label class="col-md-6 control-label">Contact Person <span class="red_small">*</span></label>

              <div class="col-md-6">

               <input name="contact_person" type="text" class="form-control required" required id="contact_person">

              </div>

            </div>

          </div>

          <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">District </label>

                <div class="col-md-6" id="distctdiv">

               <select name="locationdistrict" id="locationdistrict" class="form-control " >

               <option value=''>--Please Select-</option>

               </select>

              </div>

            </div>

            <div class="col-md-6"><label class="col-md-6 control-label">Email <span class="red_small">*</span></label>

              <div class="col-md-6">

                  <input name="email" type="email" class="email required form-control" id="email" required onBlur="return checkEmail(this.value,'email');">

              </div>

            </div>

          </div>		 


		  <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">Pincode <span class="red_small">*</span></label>

              <div class="col-md-6">

                <input name="pincode" type="text" class="required form-control" id="pincode"  maxlength="6" minlength="6" required>

              </div>

            </div>

            <div class="col-md-6"><label class="col-md-6 control-label">Phone Number1 <span class="red_small">*</span></label>

              <div class="col-md-6">

              <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">

              </div>

            </div>

          </div>

		  <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">Address <span class="red_small">*</span></label>

              <div class="col-md-6">

                <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>

              </div>

            </div>

           	<div class="col-md-6"><label class="col-md-6 control-label">Phone Number2</label>

              <div class="col-md-6">

              <input name="phone2" type="text" class="digits form-control"  id="phone2" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();">

              </div>

            </div>

          </div>

          <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">Firm Type <span class="red_small">*</span></label>

              <div class="col-md-6">

                <select name="propritortype" id="propritortype" class="form-control required"  required>
                  <option value="">Select Type--</option>
                  <option value="Area Franchisee">Area Franchisee</option>
                   <option value="Unit Franchisee">Unit Franchisee</option>
                  <option value="owned">Owned</option>
                  <option value="partner">Partnership</option>
                </select>

              </div>

            </div>

           	<div class="col-md-6"><label class="col-md-6 control-label">Helpline No.</label>

              <div class="col-md-6">

              <input name="helpline_no" type="text" class="form-control" id="helpline_no">

              </div>

            </div>

          </div>

          <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">PAN No. <span class="red_small">*</span></label>

              <div class="col-md-6">

                <input name="pan_no" type="text" class="form-control required" required id="pan_no">

              </div>

            </div>

           	<div class="col-md-6"><label class="col-md-6 control-label">GST No. </label>

              <div class="col-md-6">

              <input name="gst_no" type="text" class="form-control" id="gst_no">

              </div>

            </div>

          </div>
          
                  <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">CIN</label>
                    <div class="col-md-6">
                      <input name="cin" type="text" class="form-control "  id="cin" value="<?=$row_locdet['cin']?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label"></label>
                    <div class="col-md-6">
                     
                    </div>
                  </div>
                </div>
                

          <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">Other Tax Reg. No.</label>

              <div class="col-md-6">

                <input name="othtaxr_no" type="text" class="form-control" id="othtaxr_no">

              </div>

            </div>

           	<div class="col-md-6"><label class="col-md-6 control-label">Other Tax Name</label>

              <div class="col-md-6">

              <input name="othtax_name" type="text" class="form-control" id="othtax_name">

              </div>

            </div>

          </div>

          <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">ERP/SAP Code</label>

              <div class="col-md-6">

                <input name="erp_id" type="text" class="form-control" id="erp_id">

              </div>

            </div>

           	<div class="col-md-6"><label class="col-md-6 control-label">Other Code</label>

              <div class="col-md-6">

              <input name="oth_id" type="text" class="form-control" id="oth_id">

              </div>

            </div>

          </div>
          
           <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">Purchase From  </label>

              <div class="col-md-6">

               <select name="entity_code" id="entity_code" class="form-control " >
                          <option value="">--Please Select--</option>
                          <?php

				$enty_query="SELECT * FROM entity_type where status_id = '1' order by name";

				$check_enty=mysqli_query($link1,$enty_query);

				while($br_entity = mysqli_fetch_array($check_enty)){

				?>
                          <option value="<?=$br_entity['id']?>"<?php if($_REQUEST['entity_type']==$br_entity['id']){ echo "selected";}?>><?php echo $br_entity['name']?></option>
                          <?php }?>
                        </select>

              </div>

            </div>

           	<div class="col-md-6"><label class="col-md-6 control-label">Zone<span class="red_small">*</span></label>

              <div class="col-md-6">

                <select name="zone" id="zone" class="form-control required" required >
                          <option value="">--Please Select--</option>
                          <?php

				$zn_query="SELECT * FROM zone_master where status = 'A' order by zonename";

				$zn_enty=mysqli_query($link1,$zn_query);

				while($zn_entity = mysqli_fetch_array($zn_enty)){

				?>
                          <option value="<?=$zn_entity['zonename']?>"<?php if($_REQUEST['zone']==$zn_entity['zonename']){ echo "selected";}?>><?php echo $zn_entity['zonename']?></option>
                          <?php }?>
                        </select>

              </div>

            </div>

          </div>
		  
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Balance Limit</label>
              <div class="col-md-6">
                <input name="balance_limit" id="balance_limit" type="text" class="number form-control" >
              </div>
            </div>
           	<div class="col-md-6"><label class="col-md-6 control-label">Shipping Address <span class="red_small">*</span></label>

              <div class="col-md-6">

                <textarea name="ship_address" id="ship_address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>

              </div>

            </div>
          </div>
		

          <div class="form-group">

            <div class="col-md-12" align="center">

              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>&nbsp;

              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_master.php?<?=$pagenav?>'">

            </div>

          </div>

    </form>

      </div><!--End form group-->

    </div><!--End col-sm-9-->

  </div><!--End row content-->

</div><!--End container fluid-->

<?php

include("../includes/footer.php");

include("../includes/connection_close.php");

?>

</body>

</html>