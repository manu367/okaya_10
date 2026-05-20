<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['request_no']);


////// final submit form ////
@extract($_POST);
if($_POST['Submit']=='Save')
{
	$sql="INSERT INTO location_master_req set locationname='".ucwords($party_name)."',locationtype='".$party_type."', partner_type='".$propritortype."', contact_person='".ucwords($contact_person)."',landlineno='".$helpline_no."',emailid='".$email."',contactno1='".$phone1."',contactno2='".$phone2."',locationaddress='".ucwords($address)."',dispatchaddress='".ucwords($address)."',deliveryaddress='".ucwords($address)."',districtid='".$locationdistrict."',cityid='".$locationcity."',stateid='".$locationstate."',countryid='".$country."',zipcode='".$pincode."',statusid='7',loginstatus='1',gstno='".$gst_no."',panno='".$pan_no."',oth_taxr_no='".$othtaxr_no."',oth_tax_name='".$othtax_name."',createby='".$_SESSION['userid']."',createdate='".$datetime."',req_no='".$_POST['req_no']."',requestdate='".$today."'";



   mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));

   ///// entry in job counter 


$sql1="INSERT INTO remark_master set req_id='".$_POST['req_no']."',module='ASC_CR',remark='ASC Creation Request', status='pending', type='".$_SESSION['id_type']."',req_by='".$_SESSION['userid']."',outcome='ASC Creation Request'";
mysqli_query($link1,$sql1)or die("error in insertion2".mysqli_error());
$sq2l="Update  asc_appo_request set status='AC' where request_no='".$_REQUEST['req_no']."' ";
mysqli_query($link1,$sq2l)or die("error in insertion2".mysqli_error());
for($i=1;$i<=10;$i++){
	
	$doc="doc".$i;
	$img="img".$i;
	
	
	$doc1=explode("-",$_POST[$doc]);
	$doc_ck=$doc1[0];
	$doc_name=$doc1[1];
	
if ($doc_ck=='Y'){
	$target_dir = "../handset_image/";
	
    $target_file = $target_dir.basename($_FILES["$img"]["name"]);
	
	move_uploaded_file($_FILES["$img"]["tmp_name"], $target_file);
}
	else{
		 $target_file = "";}
	mysqli_query($link1,"insert into doc_upload set type='".$_SESSION['id_type']."',url='".$target_file."',name='".$doc_name."',req_by='".$_SESSION['userid']."',req_id='".$_POST['req_no']."',doc_ck='".$doc_ck."',asc_cr='".$pad."'") or die("error2".mysqli_error($link1));
	
	
	}
	
	
	
	 $email=	mysqli_query($link1,"select email from email_user where (type='admin' or type='HO')");
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


	$message = "Dear Sir ,<br />";
	$message.="<br>Below ticket has beeen raised for creation of new ASC .<br />";
	$message.="<br>Request No  :".$_POST['req_no']."<br />";
	$message.="<br>Request Date: ".$today."<br />";
	$message.="<br>Kindly check your CRM id for approve the same<br />";


	// Always set content-type when sending HTML email
	$headers1 = "MIME-Version: 1.0\r\n";
	$headers1 .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers1 .= "From:doNotReply@cancrm.in". "\r\n";
	$subject = "ASC Creation Request";
	mail($toemail,$subject,$message ,$headers1);




   ////// insert in activity table////



	dailyActivity($_SESSION['userid'],$_POST['req_no'],"ASP CREATION REQUEST","ADD",$ip,$link1,"");
	
		$cflag="success";

		$cmsg="Success";
		$msg = "Sucessfully update Remark of Request No.".$docid;


	
   ///// move to parent page
  
header("location:asp_appo_detail.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);



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



      <h2 align="center"><i class="fa fa-id-badge"></i> Request New <?=$locationstr?></h2><br/><br/>



      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >



          <form  name="frm1" id="frm1" class="form-horizontal"  autocomplete="off"  action=""  enctype="multipart/form-data"  method="post">



          <div class="form-group">



            <div class="col-md-6"><label class="col-md-6 control-label">Country <span class="red_small">*</span></label>



              <div class="col-md-6">

 <input type="hidden" name="req_no" id="req_no" class=" inputtext"  value="<?=$docid?>">

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



            <div class="col-md-6">
              <label class="col-md-6 control-label">Contact Number <span class="red_small">*</span></label>



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



           	<div class="col-md-6">
           	  <label class="col-md-6 control-label">Alternate Number</label>



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




                  <!--<option value="Area Franchisee">Area Franchisee</option>

                   <option value="Unit Franchisee">Unit Franchisee</option>

-->

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
                    <div class="col-md-12" align="center">
 <table class="table table-bordered" width="100%">
   <tr bordercolor="#000000" class="Table_body">
              <td height="26">Address Proof of Proposed ASC <span class="red_small">*</span></td>
           
              <td height="26" ><input type="radio" name="doc1" id="doc1" value="Y-AP" onClick="fundisplay(1);"> Yes
  <input type="radio" name="doc1" id="doc11" value="N-AP" onClick="fundisplay(1);"   > No</td>
               <td height="26" colspan="3"><input type="file" name="img1" id="img1" /></td>
             
              </tr>   <tr bordercolor="#000000" class="Table_body">
              <td height="26">Identity Proof*&nbsp; <span class="red_small">*</span></td>
       
              <td height="26" ><input type="radio" name="doc2" id="doc2" value="Y-IP" onClick="fundisplay(2);" > Yes
  <input type="radio" name="doc2" id="doc12" value="N-IP" onClick="fundisplay(2);"  > No</td>
               <td height="26"  colspan="3"><input type="file" name="img2" id="img2" /></td>
             
             
              </tr>   <tr bordercolor="#000000" class="Table_body">
              <td height="26">PAN Card&nbsp; <span class="red_small">*</span></td>
    
              <td height="26" ><input type="radio" name="doc3" id="doc3" value="Y-PC" onClick="fundisplay(3);" > Yes
  <input type="radio" name="doc3" id="doc13" value="N-PC" onClick="fundisplay(3);" > No</td>
               <td height="26"  colspan="3"><input type="file" name="img3" id="img3" /></td>
             
              </tr>   <tr bordercolor="#000000" class="Table_body">
              <td height="26">TIN No/VAT No&nbsp; <span class="red_small">*</span></td>
          
              <td height="26" ><input type="radio" name="doc4" id="doc4" value="Y-TN" onClick="fundisplay(4);" > Yes
  <input type="radio" name="doc4" id="doc14" value="N-TN" onClick="fundisplay(4);" > No</td>
               <td height="26"  colspan="3"><input type="file" name="img4" id="img4"/></td>
           
              </tr>   <tr bordercolor="#000000" class="Table_body">
              <td height="26">Service Tax No&nbsp; <span class="red_small">*</span></td>
         
              <td height="26" ><input type="radio" name="doc5" id="doc5" value="Y-ST" onClick="fundisplay(5);" > Yes
  <input type="radio" name="doc5" id="doc15" value="N-ST" onClick="fundisplay(5);"  > No</td>
               <td height="26"  colspan="3"><input type="file" name="img5" id="img5" /></td>
             
              </tr>   <tr bordercolor="#000000" class="Table_body">
              <td height="26">Cancelled Cheque&nbsp; <span class="red_small">*</span></td>
    
              <td height="26" ><input type="radio" name="doc6" id="doc6" value="Y-CC" onClick="fundisplay(6);"> Yes
  <input type="radio" name="doc6" id="doc16" value="N-CC" onClick="fundisplay(6);" > No</td>
               <td height="26"  colspan="3"><input type="file" name="img6" id="img6" /></td>
             
              </tr>   <tr bordercolor="#000000" class="Table_body">
              <td height="26">ASC Front Image&nbsp; <span class="red_small">*</span></td>
     
              <td height="26" ><input type="radio" name="doc7" id="doc7" value="Y-AF" onClick="fundisplay(7);"> Yes
  <input type="radio" name="doc7" id="doc17" value="N-AF"  onclick="fundisplay(7);" >
  No</td>
               <td height="26"  colspan="3"><input type="file" name="img7" id="img7" /></td>
             
              </tr>   <tr bordercolor="#000000" class="Table_body">
              <td height="26">ASC Recipient Area Image&nbsp; <span class="red_small">*</span></td>
         
              <td height="26" ><input type="radio" name="doc8" id="doc8" value="Y-ARI" onClick="fundisplay(8);" > Yes
  <input type="radio" name="doc8" id="doc18" value="N-ARI" onClick="fundisplay(8);" >No</td>
               <td height="26"  colspan="3"><input type="file" name="img8" id="img8" /></td>
            
              </tr>   <tr bordercolor="#000000" class="Table_body">
              <td height="26">ASC TRC Area Image&nbsp; <span class="red_small">*</span></td>
           
              <td height="26" ><input type="radio" name="doc9" id="doc9" value="Y-ATI" onClick="fundisplay(9);"> Yes
  <input type="radio" name="doc9" id="doc19" value="N-ATI" onClick="fundisplay(9);"  >NO</td>
               <td height="26"  colspan="3"><input type="file" name="img9" id="img9" /></td>
            
              </tr>   <tr bordercolor="#000000" class="Table_body">
              <td height="26">ASC Spare Storage Area Image&nbsp; <span class="red_small">*</span></td>
     
              <td height="26"  ><input type="radio" name="doc10" id="doc10" value="Y-ASA" onClick="fundisplay(10);"> Yes
  <input type="radio" name="doc10" id="doc20" value="N-ASA" onClick="fundisplay(10);"  >No</td>
               <td height="26"  colspan="3"><input type="file" name="img10" id="img10"/></td>
              
              </tr> 
              
            
          </table>
					 
                     
                    </div>
                  </div> 

    <!--      <div class="form-group">



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



           	<div class="col-md-6"><label class="col-md-6 control-label">User id<span class="red_small">*</span></label>



              <div class="col-md-6">


  <input name="userid" type="text" class="form-control required" required id="userid">
            



              </div>



            </div>



          </div>
-->


          <!--<div class="form-group">



            <div class="col-md-6"><label class="col-md-6 control-label">Invoice Series <span class="red_small">*</span></label>



              <div class="col-md-6">



                <input name="inv_series" type="text" class="required form-control" id="inv_series" required placeholder="Please ensure before enter">



              </div>



            </div>



           	<div class="col-md-6"><label class="col-md-6 control-label">DC Series <span class="red_small">*</span></label>



              <div class="col-md-6">



              <input name="stn_series" type="text" class="required form-control" id="stn_series" required placeholder="Please ensure before enter">



              </div>



            </div>



          </div>-->



          <div class="form-group">



            <div class="col-md-12" align="center">



              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Save" title="" <?php if($_POST['Submit']=='Save'){?>disabled<?php }?>>&nbsp;



              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_appo_detail.php?<?=$pagenav?>'">



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