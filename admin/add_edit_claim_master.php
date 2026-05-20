<?php
require_once("../includes/config.php");
/////get state//
$arrstate = getState($link1);
$arrstatus = getFullStatus("master",$link1);
//print_r($arrstate);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from claim_master where id ='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
 	$flag = true;
    mysqli_autocommit($link1, false);
    $error_msg="";
   if ($_POST['add']=='ADD'){
	   $sel_claim="select * from claim_master where product_id ='".$product_name."' and brand_id = '".$brand."' and level_type='".$level_type."' and level='".$level."' and party='".$party."' ";
	$cliam_res12=mysqli_query($link1,$sel_claim)or die("error1".mysqli_error($link1));
	   if(mysqli_num_rows($cliam_res12)==0){
     $usr_add="INSERT INTO  claim_master set   brand_id = '".$brand."',product_id ='".$product_name."',level_type='".$level_type."',level='".$level."',level_value='".$level_value."' ,update_by ='".$_SESSION['userid']."',status='".$status."',party='".$party."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));  
	   if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details add: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],"","Claim","ADD",$ip,$link1,$flag);
	   if (!$flag) {
		 $flag = false;
		 $error_msg = "Error details add daily: " . mysqli_error($link1) . ".";
	}
	////// return message
	   }///////check for data
	   else {
	   $flag = false;
		 $error_msg = "Already created Claim Master";
	   }
   }
   else if ($_POST['upd']=='Update'){
    $usr_upd= "update claim_master set brand_id = '".$brand."',product_id ='".$product_name."',level_type='".$level_type."',level='".$level."',level_value='".$level_value."' ,update_by ='".$_SESSION['userid']."',status='".$status."',party='".$party."'  where id ='".$refid."' ";
    $res_upd=mysqli_query($link1,$usr_upd) or die("error4".mysqli_error($link1));
	   if (!$res_upd) {
		 $flag = false;
		 $error_msg = "Error details edit: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],"","Claim","Edit",$ip,$link1,$flag);
	   if (!$flag) {
		 $flag = false;
		 $error_msg = "Error details edit daily: " . mysqli_error($link1) . ".";
	}
	////// return message

   }
   ///// move to parent page
	 ///// check both master and data query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
		$cflag="success";
		$cmsg="Success";
		$msg = "Successfully Updated";
	} else {
		mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	mysqli_close($link1);
    header("location:list_claim_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-map-marker"></i> <?=$_REQUEST['op']?>Claim Master</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Product</label>
              <div class="col-md-6">
               
                	<select name="product_name" id="product_name" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM product_master where status = '1' order by product_name";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['product_id']?>"<?php if($sel_result['product_id'] == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>
                <?php }?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Brand</label>
              <div class="col-md-6">
               
                 	  <select name="brand" id="brand" class="form-control required">
                  <option value="">Please Select</option>
                <?php
					$dept_query="SELECT * FROM brand_master where status = '1'"." ".$brandfiltteradmin." "." order by brand";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['brand_id']?>"<?php if($sel_result['brand_id'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
                <?php }?>
                </select>
              </div>
            </div>
          </div>
		   <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">level</label>
              <div class="col-md-6">
            
				   <select name="level" id="level" class="form-control required">
                  <option value="">Please Select</option>
                <?php
					$dept_query="SELECT id,name FROM repair_level where status = '1'";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['id']?>"<?php if($sel_result['level'] == $br_dept['id']){ echo "selected";}?>><?php echo $br_dept['name']?></option>
                <?php }?>
                </select>
              </div>
            </div>
          </div>
			  <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Type</label>
              <div class="col-md-6"> 
			 <select name="level_type" id="level_type" class="form-control required">
                  <option value="">Please Select</option>
                <?php
					$dept_query="SELECT locationtypeid,displayname FROM location_type_master";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['locationtypeid']?>"<?php if($sel_result['level_type'] == $br_dept['locationtypeid']){ echo "selected";}?>><?php echo $br_dept['displayname']?></option>
                <?php }?>
                </select>
              </div>
            </div>
          </div>
			   <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Price</label>
              <div class="col-md-6">
                 <input type="text" name="level_value" class="required form-control number" id="level_value" value="<?=$sel_result['level_value']?>" required/> 
              </div>
            </div>
          </div>
			   <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Party</label>
              <div class="col-md-6">
               
                 	  <select name="party" id="party" class="form-control required">
                  <option value="All">ALL</option>
               <!-- <?php
					$dept_query="SELECT * FROM brand_master where status = '1'"." ".$brandfiltteradmin." "." order by brand";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['party']?>"<?php if($sel_result['party'] == $br_dept['party']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
                <?php }?>-->
                </select>
              </div>
            </div>
          </div>
			  <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Status</label>
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
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Claim">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Claim Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['id']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='list_claim_master.php?status=<?=$pagenav?>'">
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