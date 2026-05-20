<?php
require_once("../includes/config.php");
$getid=base64_decode($_REQUEST['id']);
$array_escl = array();
$arr_selescl = $_REQUEST['esclate_to'];
for($i=0; $i<count($arr_selescl); $i++){
	$array_escl[$arr_selescl[$i]] = "Y";
}
$array_wh = array();
$arr_selwh = $_REQUEST['wh_to'];
for($i=0; $i<count($arr_selwh); $i++){
	$array_wh[$arr_selwh[$i]] = "Y";
}
////// get details of selected location////
$res_locdet=mysqli_query($link1,"SELECT * FROM location_master where locationid='".$getid."'")or die(mysqli_error($link1));
$row_locdet=mysqli_fetch_array($res_locdet);
/////get status//
$arrstatus = getFullStatus("master",$link1);
////// final submit form ////
@extract($_POST);
if($_POST){
if($_POST['Submit1']=='Save'){
   // update all details of location //
   $sql = "UPDATE location_master set erpid='".$erp_id."', othid='".$oth_id."',locationname='".$party_name."',locationtype='".$party_type."', partner_type='".$propritortype."', contact_person='".$contact_person."',landlineno='".$helpline_no."',emailid='".$email."',contactno1='".$phone1."',contactno2='".$phone2."',locationaddress='".$address."',dispatchaddress='".$address."',deliveryaddress='".$address."',districtid='".$locationdistrict."',cityid='".$locationcity."',stateid='".$locationstate."',countryid='".$country."',zipcode='".$pincode."',statusid='1',loginstatus='1',gstno='".$gst_no."',panno='".$pan_no."',oth_taxr_no='".$othtaxr_no."',oth_tax_name='".$othtax_name."',updateby='".$_SESSION['userid']."',updatedate='".$datetime."' where locationid='".$getid."'";
   mysqli_query($link1,$sql)or die("ER3".mysqli_error($link1));
   //////////////////////////////////////////////////////////////
   ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$loccode,"LOCATION","UPDATE",$ip,$link1,"");
	////// return message
	$msg="You have successfully updated details of location ".$loccode;
}else{
	////// return message
	$msg="Something went wrong. Please try again.";
	
}	
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
$(document).ready(function() {
    if (location.hash) {
        $("a[href='" + location.hash + "']").tab("show");
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });
});
$(window).on("popstate", function() {
    var anchor = location.hash || $("a[data-toggle='tab']").first().attr("href");
    $("a[href='" + anchor + "']").tab("show");
	if(location.hash=="#menu1"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="";
		document.getElementById("menu2").style.display="none";
	}
	else if(location.hash=="#menu2"){
		document.getElementById("home").style.display="none";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="";
	}
	else{
		document.getElementById("home").style.display="";
		document.getElementById("menu1").style.display="none";
		document.getElementById("menu2").style.display="none";
	}
});
  </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-id-badge"></i> Enter Job Details</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
      	 <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-id-card fa-lg"></i> Customer Details</a></li>
            <li><a data-toggle="tab" href="#menu1"><i class="fa fa-tablet fa-lg"></i> Device Details</a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-university"></i> Other Details</a></li>
          </ul>
    	  <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
              <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Customer Name <span class="red_small">*</span></label>
                      <div class="col-md-6">
                      	<input name="customer_name" id="customer_name" type="text" value="" class="form-control required"/>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Address <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"></textarea>
                      </div>
                    </div>
                  </div>
                   <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Contact No. <span class="small">(For SMS Update)</span> <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?=$_REQUEST['contact_no']?>">
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Alternate Contact No.</label>
                      <div class="col-md-6">
                      <input name="phone2" type="text" class="digits form-control" id="phone2" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">State <span class="red_small">*</span></label>
                      <div class="col-md-6">
                         <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();" required>
                          <option value=''>--Please Select--</option>
                          <?php 
						 $state_query="select stateid, state from state_master where countryid='1' order by state";
						 $state_res=mysqli_query($link1,$state_query);
						 while($row_res = mysqli_fetch_array($state_res)){?>
						   <option value="<?=$row_res['stateid']?>"<?php if($row_locdet['stateid']==$row_res['stateid']){ echo "selected";}?>><?=$row_res['state']?></option>
						 <?php }?> 	
                        </select>               
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Email <span class="red_small">*</span></label>
                      <div class="col-md-6">
                          <input name="email" type="email" class="email required form-control" id="email" required onBlur="return checkEmail(this.value,'email');" value="<?=$row_locdet['emailid']?>">
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
                   <div class="col-md-6"><label class="col-md-6 control-label">Pincode</label>
                      <div class="col-md-6">
                        <input name="pincode" type="text" class="digits form-control" id="pincode" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-12" align="center">
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_create.php?<?=$pagenav?>'">&nbsp;
                      <input type="submit" class="btn<?=$btncolor?>" name="Submit1" id="save1" value="Save" title="" <?php if($_POST['Submit1']=='Save'){?>disabled<?php }?>>&nbsp;
                      <button title="Next" type="button" class="btn<?=$btncolor?>" onClick="window.location.href='#menu1'">Next >></button>
                    </div>
                  </div>
            </form>
            </div>
            <div id="menu1" class="tab-pane fade">
              <br/>
              <form  name="frm2" id="frm2" class="form-horizontal" action="" method="post">
      			<div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">For Repair Esclation/SFR</label>
                      <div class="col-md-6">
                         <select name="esclate_to[]" id="example-multiple-selected1" multiple="multiple" class="form-control">
                            <?php
							$lctype_query="SELECT location_code,locationname FROM  location_master where locationtype in ('L3','L4') order by locationname";
							$check_lctype=mysqli_query($link1,$lctype_query);
							while($br_lctype = mysqli_fetch_array($check_lctype)){
							?>
                                <option value="<?=$br_lctype['location_code']?>" <?php if($array_escl[$br_lctype['location_code']]=="Y") { echo 'selected'; }?>><?=$br_lctype['locationname']?></option>
                            <?php } ?>
                         </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">For Part Requirement/WH</label>
                      <div class="col-md-6">
                         <select name="wh_to[]" id="example-multiple-selected2" multiple="multiple" class="form-control">
                            <?php
							$lctype_query="SELECT location_code,locationname FROM  location_master where locationtype in ('WH') order by locationname";
							$check_lctype=mysqli_query($link1,$lctype_query);
							while($br_lctype = mysqli_fetch_array($check_lctype)){
							?>
                                <option value="<?=$br_lctype['location_code']?>" <?php if($array_wh[$br_lctype['location_code']]=="Y") { echo 'selected'; }?>><?=$br_lctype['locationname']?></option>
                            <?php } ?>
                         </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-12" align="center">
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_create.php?<?=$pagenav?>'">&nbsp;
                      <button title="Previous" type="button" class="btn btn-primary" onClick="window.location.href='#home'"><< Previous</button>&nbsp;
                      <input type="submit" class="btn<?=$btncolor?>" name="Submit2" id="save2" value="Save" title="" <?php if($_POST['Submit2']=='Save'){?>disabled<?php }?>>&nbsp;
                      <button title="Next" type="button" class="btn btn-primary" onClick="window.location.href='#menu2'">Next >></button>
                    </div>
                  </div>      	
              </form>
            </div>
            <div id="menu2" class="tab-pane fade">
              <br/>
              <form  name="frm3" id="frm3" class="form-horizontal" action="" method="post">
      			 <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">A/C No.</label>
                      <div class="col-md-6">
                         <input name="ac_no" type="text" class="form-control" id="ac_no" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">A/C Holder Name</label>
                      <div class="col-md-6">
                         <input name="acholder_name" type="text" class="form-control" id="acholder_name" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">A/C Type</label>
                      <div class="col-md-6">
                         <input name="ac_type" type="text" class="form-control" id="ac_type" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">Bank Name</label>
                      <div class="col-md-6">
                         <input name="bank_name" type="text" class="form-control" id="bank_name" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">IFSC Code</label>
                      <div class="col-md-6">
                         <input name="ifsc_code" type="text" class="form-control" id="ifsc_code" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-10"><label class="col-md-4 control-label">Branch Name</label>
                      <div class="col-md-6">
                         <input name="branch_name" type="text" class="form-control" id="branch_name" value="">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-12" align="center">
                      <input type="submit" class="btn<?=$btncolor?>" name="Submit3" id="save3" value="Save" title="" <?php if($_POST['Submit3']=='Save'){?>disabled<?php }?>>&nbsp;
                      <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['locationid'])?>"/>
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_master.php?<?=$pagenav?>'">
                    </div>
                  </div>      	
              </form>
            </div>
        </div>
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