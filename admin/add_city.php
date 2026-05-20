<?php
require_once("../includes/config.php");
/////get state//
$arrstate = getState($link1);
//print_r($arrstate);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from city_master where city ='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
	$expld_state = explode("~",$posstate);
   if ($_POST['add']=='ADD'){ 
   
   	$usr_code="select city from city_master where city='".$city_name."' and  stateid='".$expld_state[0]."' ";
		$result_user=mysqli_query($link1,$usr_code);
		///// if found \\\\\
		if (mysqli_num_rows($result_user)==0){
    $usr_add="INSERT INTO  city_master set  stateid='".$expld_state[0]."', state ='".$expld_state[1]."',city ='".ucwords($city_name)."',isdistrict='".$isdistrict."' ";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	$dptid = mysqli_insert_id($link1); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$dptid,"CITY","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	////// return message
  $msg="You have successfully created a city with ref. no. ".$state;
	}else{
	 $msg="City Already Created. ".$city_name;
	}

   }
   else if ($_POST['upd']=='Update'){
    $usr_upd= "update city_master set  stateid='".$expld_state[0]."', state ='".$expld_state[1]."',city ='".ucwords($city_name)."',isdistrict='".$isdistrict."'  where cityid ='".$refid."' ";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$state,"CITY","UPDATE",$ip,$link1,"");
	////// return message
	$msg="You have successfully updated city details for ".$city_name." (".$expld_state[1].")";
   }
   ///// move to parent page
    header("location:city_master.php?msg=".$msg."".$pagenav);
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
 <script type="text/javascript" src="../js/jquery.validate.js"></script></head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-location-arrow"></i> <?=$_REQUEST['op']?> City</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">State <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="posstate" id="posstate" class="form-control required">
				       	<option value="" <?php  echo 'selected';?>>Please Select </option>
                    <?php foreach($arrstate as $key => $value){?>
                    	<option value="<?=$key."~".$value?>" <?php if($sel_result['stateid'] == $key) { echo 'selected'; }?>><?=$value?></option>
                    <?php } ?>
                 </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">City Name<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="city_name" class="required form-control" id="city_name" value="<?=$sel_result['city']?>" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Is District?</label>
              <div class="col-md-6">
                 <input type="checkbox" name="isdistrict" class="form-control" id="isdistrict" value="Y" <?php if($sel_result['isdistrict']=="Y"){ echo "checked";}?>/>
              </div>
            </div>
          </div>
  
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New City">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update City Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['cityid']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='city_master.php?status=<?=$pagenav?>'">
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