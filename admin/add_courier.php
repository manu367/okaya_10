<?php
require_once("../includes/config.php");
$getid=base64_decode($_REQUEST['id']);
$array_escl = array();

////// get details of selected location////
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from courier_master where courier_id='".$getid."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
/////get status//
$arrstatus = getFullStatus("master",$link1);
////// final submit form ////
@extract($_POST);
if($_POST){
    if ($_POST['add']=='ADD'){
    ///////// insert HSN data	   

   $usr_add="insert into courier_master set name='$party_name',Contact_person='$contact_person', city='$locationcity', state='$locationstate', email='$email',country='$country', status='$status',district='$locationdistrict',addrs='$address',phone='$phone1',pincode='$pincode',type='$party_type'";
    $res_add=mysqli_query($link1,$usr_add);

$insid = mysqli_insert_id($link1);
    /// make 5 digit padding
    $pad=str_pad($insid,4,"0",STR_PAD_LEFT);
    //// make logic of partcode code
    $newpartcode="C".$pad; 
	//////// update system genrated code in model
    $req_res = mysqli_query($link1,"UPDATE courier_master set  	courier_id='".$newpartcode."' where id='".$insid."'");


	
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$newpartcode,"Courier Master","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully created a Courier like ".$newpartcode;
	$cflag="success";
	$cmsg="Success";
   }
   else if ($_POST['upd']=='Update'){ 
  $usr_upd = "Update courier_master set name='$party_name',Contact_person='$contact_person', city='$locationcity', state='$locationstate', email='$email',country='$country', status='$status',district='$locationdistrict',addrs='$address',phone='$phone1',pincode='$pincode',type='$party_type' where courier_id='".$getid."'";
    $res_upd = mysqli_query($link1,$usr_upd);
///// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$getid,"Courier Details","UPDATE",$ip,$link1,$flag);
	////// return message
	$msg="You have successfully updated Courier details for ".$getid;
	$cflag="success";
	$cmsg="Success";
   }else{
	    $flag = false;
	   	$cflag = "info";
		$cmsg = "Warning";
   		$msg = "Request could not be processed. Please try again.";
   }
 
	
   ///// move to parent page
 header("location:courier_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
$(document).ready(function() {
	$('#example-multiple-selected1').multiselect({
			includeSelectAllOption: true,
			enableFiltering: true,
			buttonWidth:"320"
            //enableFiltering: true
	});
	$('#example-multiple-selected2').multiselect({
			includeSelectAllOption: true,
			enableFiltering: true,
			buttonWidth:"320"
            //enableFiltering: true
	});
}); 
  </script>
  <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-truck"></i> <?=$_REQUEST['op']?> Courier</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
      
    	  <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
              <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Country </label>
                      <div class="col-md-6">
                         <select name="country" id="country" class="form-control">
                          <option value="">--Please Select--</option>
                          <?php
                        $country_query="SELECT * FROM country_master where status = 'A' order by countryname";
                        $check_country=mysqli_query($link1,$country_query);
                        while($br_country = mysqli_fetch_array($check_country)){
                        ?>
                        <option value="<?=$br_country['countryid']?>"<?php if($sel_result['country']==$br_country['countryid']){ echo "selected";}?>><?php echo $br_country['countryname']?></option>
                        <?php }?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Party Type <span class="red_small">*</span></label>
                      <div class="col-md-6">
                       <select name="party_type" id="party_type" class="form-control required" required>
                         
                          <option value="Courier Service"<?php if($sel_result['type']=="Courier Service"){ echo "selected";}?>>Courier Service</option>
                          <option value="Transport Service"<?php if($sel_result['type']=="Transport Service"){ echo "selected";}?>>Transport Service</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">State</label>
                      <div class="col-md-6" id="statediv">
                         <select name="locationstate" id="locationstate" class="form-control"  onchange="get_citydiv();get_distdiv();">
                          <option value=''>--Please Select--</option>
                          <?php 
						 $state_query="select stateid, state from state_master where countryid='".$sel_result['country']."' order by state";
						 $state_res=mysqli_query($link1,$state_query);
						 while($row_res = mysqli_fetch_array($state_res)){?>
						   <option value="<?=$row_res['stateid']?>"<?php if($sel_result['state']==$row_res['stateid']){ echo "selected";}?>><?=$row_res['state']?></option>
						 <?php }?> 	
                        </select>               
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Party Name <span class="red_small">*</span></label>
                      <div class="col-md-6"> 
                       <input name="party_name" type="text" class="form-control required" required id="party_name" style="background-color:#FFFFCC" value="<?=$sel_result['name']?>">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">City</label>
                        <div class="col-md-6" id="citydiv">
                       <select name="locationcity" id="locationcity" class="form-control">
                       <option value=''>--Please Select-</option>
                       <?php 
						 $city_query="SELECT cityid, city FROM city_master where stateid='".$sel_result['state']."' group by city order by city";
						 $city_res=mysqli_query($link1,$city_query);
						 while($row_city = mysqli_fetch_array($city_res)){?>
						   <option value="<?=$row_city['cityid']?>"<?php if($sel_result['city']==$row_city['cityid']){ echo "selected";}?>><?=$row_city['city']?></option>
						 <?php }?>
                       </select>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Contact Person</label>
                      <div class="col-md-6">
                       <input name="contact_person" type="text" class="form-control" id="contact_person" value="<?=$sel_result['Contact_person']?>">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">District </label>
                        <div class="col-md-6" id="distctdiv">
                       <select name="locationdistrict" id="locationdistrict" class="form-control" >
                       <option value=''>--Please Select-</option>
                       <?php 
						 $city_query="SELECT cityid, city FROM city_master where stateid='".$sel_result['state']."' and isdistrict='Y' group by city order by city";
						 $city_res=mysqli_query($link1,$city_query);
						 while($row_city = mysqli_fetch_array($city_res)){?>
						   <option value="<?=$row_city['cityid']?>"<?php if($sel_result['district']==$row_city['cityid']){ echo "selected";}?>><?=$row_city['city']?></option>
						 <?php }?>
                       </select>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Email</label>
                      <div class="col-md-6">
                          <input name="email" type="email" class="email form-control" id="email"  onBlur="return checkEmail(this.value,'email');" value="<?=$sel_result['email']?>">
                      </div>
                    </div>
                  </div>		 
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Pincode</label>
                      <div class="col-md-6">
                        <input name="pincode" type="text" class="form-control" id="pincode" value="<?=$sel_result['pincode']?>">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="col-md-6 control-label">Phone Number</label>
                      <div class="col-md-6">
                      <input name="phone1" type="text" class="digits form-control" id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?=$sel_result['phone']?>">
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Address</label>
                      <div class="col-md-6">
                        <textarea name="address" id="address" class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?php echo $sel_result['addrs'];?></textarea>
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Status</label>
                      <div class="col-md-6">
                    <select name="status" id="status" class="form-control">
                    <?php foreach($arrstatus as $key => $value){?>
                    	<option value="<?=$key?>" <?php if($sel_result['status'] == $key) { echo 'selected'; }?>><?=$value?></option>
                    <?php } ?>
                 </select>
                      </div>
                    </div>
                  </div>
                
           
                  <div class="form-group">
                    <div class="col-md-12" align="center">
                          <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Courier">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Courier Details">
              <?php }?>
                      <input name="id" id="id" type="hidden" value="<?=base64_encode($sel_result['courier_id'])?>"/>
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='courier_master.php?<?=$pagenav?>'">
                    </div>
                  </div>
            </form>
            </div>


        
                 
                 
                
              </form>
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