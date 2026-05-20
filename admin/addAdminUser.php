<?php
require_once("../includes/config.php");

$dt = date("Y-m-d H:i:s");

/////get status//
$arrstatus = getFullStatus("master",$link1);
@extract($_POST);
////// case 1. if we want to update details
if($_REQUEST['op']=='edit')
{
	$sel_usr="select * from admin_users where username='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysql_error());
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST)
{
	barCheck($link1);
	if($_POST['add']=='ADD')
	{
		$query_code="select MAX(uid) as qc from admin_users";
		$result_code=mysqli_query($link1,$query_code)or die("error2".mysqli_error($link1));
		$arr_result2=mysqli_fetch_array($result_code);
		$code_id=$arr_result2[0]+1;

		$pad=str_pad($code_id,3,"0",STR_PAD_LEFT);

		$admiCode=strtoupper(BRANDNAME)."USR".$pad;
		//$pwd=$admiCode."@123";

		$usr_add="INSERT INTO admin_users set  
username ='".$admiCode."',
password ='".$pwd."',
name= '".ucwords($usrname)."',
utype='".$u_type."',
phone='".$phone."',
emailid= '".$email."',
status='".$status."',
state_id='".$state."',
city_id='".$city."',
des_id='".$designation."',
address='".$address."',
createdate='".date("Y-m-d H:i:s")."',
uid='".$code_id."'";
		$res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1)); 
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$admiCode,"ADMIN USER","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
		////// return message
		$msg="You have successfully created an user with ref. no. ".$admiCode;
	}
	else if($_POST['upd']=='Update')
	{
//        var_dump(des_id);exit();
		$usr_upd="update admin_users set
                       password ='".$pwd."' ,
                       name= '".ucwords($usrname)."',
                       utype='".$u_type."',
                       phone= '".$phone."',
                       emailid= '".$email."',
                       status='".$status."',
                       state_id='".$state."',
                       city_id='".$city."',
                       des_id='".$designation."',
                       address='".$address."',
                       updatedate='".date("Y-m-d H:i:s")."' where username = '".$usrid2."'";
		$res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$usrid2,"ADMIN USER","UPDATE",$ip,$link1,"");
		////// return message
		######################
		### By Hemant
		######################
		$sql_csl = "INSERT INTO status_log SET userid='".$usrid2."', status='".$status."', create_dt='".$dt."', create_by='".$_SESSION['userid']."'";
		$res_csl = mysqli_query($link1, $sql_csl);
		######################
		$msg="You have successfully updated user details for ".$usrid2;
	}
	///// move to parent page
	header("location:adminusermgt.php?msg=".$msg."".$pagenav);
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script>
$(document).ready(function(){
        $("#frm1").validate({
		submitHandler: function (form) {
				if(!this.wasSent){
					this.wasSent = true;
					$(':submit', form).val('Please wait...')
									  .attr('disabled', 'disabled')
									  .addClass('disabled');
					//spinner.show();				  
					form.submit();
				} else {
					return false;
				}
          }
		});
    $('#state').change(function(){
        var state_id = $(this).val();
        if(state_id != ''){
            $.ajax({
                url:"fetch_cities.php",
                method:"POST",
                data:{state_id:state_id},
                success:function(data){
                    console.log(data);
                    $('#city').html(data);
                }
            });
        }else{
            $('#city').html('<option value="">--Select City--</option>');
        }
    })
});
</script>
 <script language="javascript" type="text/javascript">
function checkPWD(val){

  var val;
  var upperCase= new RegExp('[A-Z]');
  var lowerCase= new RegExp('[a-z]');
  var numbers = new RegExp('[0-9]');
 
  if(val.match(upperCase) && val.match(lowerCase) &&  val.match(numbers))  
  {
	  //$("#passwordErrorMsg").html("OK")
	  $("#passwordErrorMsg").html("")
	 
	 if('<?php $_REQUEST['op']=='edit' ?>'){
	 document.getElementById('upd').style.visibility = 'visible';
	 }
    document.getElementById('add').style.visibility = 'visible';
	
  }
  else
  {
  
	  $("#passwordErrorMsg").html("Your password must be between 6 and 20 characters. It must contain a mixture of upper and lower case letters, and at least one number or symbol.");
	 
	 	 if('<?php $_REQUEST['op']=='edit' ?>'){
	 document.getElementById('upd').style.visibility = 'visible';
	 }
	 document.getElementById('add').style.visibility = 'hidden';

	
  }
}
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center" style="border-bottom:1px solid #aaa8a8;padding:25px 0px;margin:0px;"><i class="fa fa-users"></i> Admin Users Details</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post" >
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">User Name <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <input type="text" name="usrname" class="required form-control" id="usrname" value="<?=$sel_result['name']?>" required/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">User Id</label>
              <div class="col-md-5">
                <input type="text" name="uid" id="uid" class="form-control" value="<?php echo $sel_result['username'];?> " readonly/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Password <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <input type="text" name="pwd" class="required form-control" id="pwd" value="<?=$sel_result['password']?>" onBlur=" checkPWD(this.value);" onKeyUp=" checkPWD(this.value);"  maxlength="20" minlength="6" required/>
                 <span id="passwordErrorMsg" style="color:#F00"></span>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">User Type <span class="red_small">*</span></label>
              <div class="col-md-5">
                <select name='u_type' id='u_type' class="required form-control" required>
                    <option value="">--Please Select--</option>
                    <option value="admin" <?php if($sel_result['utype'] =='admin') {echo 'selected'; }?>>Admin</option>
                    <?php /*?><?php
					$res_utype=mysqli_query($link1,"select * from usertype_master where status='A' order by refid")or die("erro1".mysql_error());
					while($row_utype=mysqli_fetch_assoc($res_utype)){
					?>
                    <option value="<?=$row_utype[refid]?>"<?php if($sel_result['utype'] ==$row_utype[refid]) { echo 'selected'; }?>><?=$row_utype[typename]?></option>
                    <?php
					}
					?><?php */?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Mobile No. <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <input type="text" name="phone" id="phone" class="digits form-control" minlength="10" maxlength="10" value="<?=$sel_result['phone']?>"  required/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Email-Id <span class="red_small">*</span></label>
              <div class="col-md-5">
                <input type="email" class="form-control email" name="email" id="email" value="<?=$sel_result['emailid']?>"  required/>
              </div>
            </div>
          </div>
              <!-- State & City -->
              <div class="form-group">
                  <div class="col-md-6">
                      <label class="col-md-5 control-label">State <span class="red_small">*</span></label>
                      <div class="col-md-5">
                          <select name="state" id="state" class="form-control" required>
                              <option value="">--Select State--</option>
                              <?php
                              $state_query = "SELECT stateid, state FROM state_master ORDER BY state ASC";
                              $state_res = mysqli_query($link1, $state_query);
                              while($state = mysqli_fetch_assoc($state_res)){
                                  $selected = (!empty($sel_result['state_id']) && $sel_result['state_id']==$state['stateid']) ? 'selected' : '';
                                  echo "<option value='{$state['stateid']}' $selected>{$state['state']}</option>";
                              }
                              ?>
                          </select>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <label class="col-md-5 control-label">City <span class="red_small">*</span></label>
                      <div class="col-md-5">
                          <select name="city" id="city" class="form-control" required>
                              <option value="">--Select City--</option>
                              <?php
                              if(!empty($sel_result['state_id'])){
                                  $city_query = "SELECT cityid, city FROM city_master WHERE stateid='".$sel_result['state_id']."' ORDER BY city ASC";
                                  $city_res = mysqli_query($link1, $city_query);
                                  while($city = mysqli_fetch_assoc($city_res)){
                                      $selected = (!empty($sel_result['city_id']) && $sel_result['city_id']==$city['cityid']) ? 'selected' : '';
                                      echo "<option value='{$city['cityid']}' $selected>{$city['city']}</option>";
                                  }
                              }
                              ?>
                          </select>
                      </div>
                  </div>
              </div>

              <div class="form-group">
                  <div class="col-md-6">
                      <label class="col-md-5 control-label">Designation <span class="red_small">*</span></label>
                      <div class="col-md-5">
                          <select name="designation" id="designation" class="form-control" required>
                              <option value="">--Select Designation--</option>
                              <?php
                              $des_query = "SELECT des_id, des_name FROM designation ORDER BY des_name ASC";
                              $des_res = mysqli_query($link1, $des_query);
                              while($des = mysqli_fetch_assoc($des_res)){
                                  $selected = (!empty($sel_result['des_id']) && $sel_result['des_id']==$des['des_id']) ? 'selected' : '';
                                  echo "<option value='{$des['des_id']}' $selected>{$des['des_name']}</option>";
                                //  echo "<script>console.log(admin-table".$sel_result['des_id'].")<script>";
                              }
                              ?>
                          </select>
                      </div>
                  </div>

                  <div class="col-md-6">
                      <label class="col-md-5 control-label">Address <span class="red_small">*</span></label>
                      <div class="col-md-5">
                          <textarea name="address" id="address" class="form-control" rows="2" required><?= $sel_result['address'] ?></textarea>
                      </div>
                  </div>
              </div>

              <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Status <span class="red_small">*</span></label>
              <div class="col-md-5">
                 <select name="status" id="status" class="form-control">
                    <?php foreach($arrstatus as $key => $value){?>
                    	<option value="<?=$key?>" <?php if($sel_result['status'] == $key) { echo 'selected'; }?>><?=$value?></option>
                    <?php } ?>
					 <option value="99" <?=(($sel_result['status']=='99')?'selected':'');?>>On Hold</option>
                 </select>
              </div>

            </div>
            <div class="col-md-6">
              <div class="col-md-5">
              </div>
            </div>
          </div>
          <div class="form-group" style="padding:15px 0px;">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add User">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update User Details">
              <input name="usr_permission" type="button" id="usr_permission" class="btn<?=$btncolor?>" onClick="window.location='update_permission.php?userid=<?=$sel_result['username']?>&utype=<?=$sel_result['utype']?>&u_name=<?=$sel_result['name']?>&page=<?=$page?>&srch=<?=$_REQUEST['srch']?><?=$pagenav?>'" value="Update Permission"/>
              <?php }?>
              <input type="hidden" name="usrid2"  id="usrid2" value="<?=$sel_result['username']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='adminusermgt.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
            </div>
          </div>
    </form>
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