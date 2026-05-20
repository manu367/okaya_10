<?php
require_once("../includes/config.php");
////// final submit form ////
@extract($_POST);
if($_POST['Submit']=='Add'){
	/////////// initialize transcation parameter ////////
	mysqli_autocommit($link1, false);
	$flag = true;
	
	//// pick max count ///////
		$res_tempcount = mysqli_query($link1,"SELECT max(tempid) as a from locationuser_master where location_code='".$_SESSION['asc_code']."'");
		$row_tempcount = mysqli_fetch_assoc($res_tempcount);
		///// make userloginid sequence
		$userlogin = $row_tempcount['a'] + 1;
		$userloginid = $_SESSION['asc_code']."U".$userlogin;
		
	////////////////////////// insert into  locationuser_master table ///////////////////////////////////////////////
   $usr_add="INSERT INTO locationuser_master set  userloginid =  '".$userloginid."'  , locusername ='".ucwords($user_name)."',  pwd ='".$password."' , emailid  ='".$emailid."' ,  	contactmo='".$contact_no."',date_of_birth='".$dob."',date_of_joining='".$doj."' , statusid='1',tempid='".$userlogin."', createby ='".$_SESSION['userid']."' , 	createdate ='".$datetime."'  ,location_code= '".$_SESSION['asc_code']."',type='".$type."'  ";
    $result=mysqli_query($link1,$usr_add);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         echo "Error details1: " . mysqli_error($link1) . ".";
    }
     	//// update the tempid count

   
   ////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$userloginid,"LOCATION USER","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	///// check  query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "User is added Successfully";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	   ///// move to parent page
 	header("location:myaccount_users.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
	$(document).ready(function () {
		$('#dob').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true
		});
		$('#doj').datepicker({
			format: "yyyy-mm-dd",
			//endDate: "<?//=$today?>",
			todayHighlight: true,
			autoclose: true
		});
	});
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
   <!-- Include Date Picker -->
 <link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
     include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-users"></i> Add New User</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">User Name<span class="red_small">*</span></label>
              <div class="col-md-6">
                <input name="user_name" type="text" class="form-control" id="user_name" required>
              </div>
            </div>
           	<div class="col-md-6"><label class="col-md-6 control-label">User Password<span class="red_small">*</span></label>
              <div class="col-md-6">
              <input name="password" type="text" class="form-control" id="password" required>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Email Id</label>
              <div class="col-md-6">
                <input name="emailid" type="email" class="email form-control"  id="emailid">
              </div>
            </div>
           	<div class="col-md-6"><label class="col-md-6 control-label">Contact No.<span class="red_small">*</span></label>
              <div class="col-md-6">
              <input name="contact_no" type="text" class="digits form-control" id="contact_no" maxlength="10" minlength="10" required>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Date Of Birth</label>
              <div class="col-md-6">
                <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="dob"  id="dob" style="width:150px;" value="" ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
              </div>
            </div>
           	<div class="col-md-6"><label class="col-md-6 control-label">Date Of Joining</label>
              <div class="col-md-6">
              <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="doj"  id="doj" style="width:150px;" value="" ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
              </div>
            </div>
          </div>
			  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">User Type</label>
              <div class="col-md-6">
                <select name="type" id="type" class="form-control" required>
				  <option value="">--Select Type--</option>
					<option value="Engineer">Engineer</option>
					<option value="Freelancer">Freelancer</option>
				  </select>
              </div>
            </div>
           	<div class="col-md-6"><label class="col-md-6 control-label"> </label>
              <div class="col-md-6">
            
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Add" title="" <?php if($_POST['Submit']=='Add'){?>disabled<?php }?>>&nbsp;
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='myaccount_users.php?<?=$pagenav?>'">
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