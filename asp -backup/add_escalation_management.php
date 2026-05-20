<?php
require_once("../includes/config.php");
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

/////get status//
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from escalation_master where crm_id='".$getid."'";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
	
	$state_details = mysqli_fetch_array(mysqli_query($link1,"SELECT stateid,state FROM state_master  where stateid='".$stateid."'"));
	$state=$state_details['state'];	
	//// initialize transaction parameters
	$flag = true;
    mysqli_autocommit($link1, false);
    $error_msg="";
    if ($_POST['add']=='ADD'){
    ///////// insert name data	   
    $usr_add="INSERT INTO escalation_master set product_id ='".$product_name."', brand_id ='".$brand_name."',email='".$email."',phone='".$contact_no."',	level='".$level."',stateid='".$stateid."',state='".$state."',days='".$days."',hours='".$hours."',sendsms='".$sendsms."',sendemail='".$sendemail."',name='".$name."',status='".$status."',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."'";
    $res_add=mysqli_query($link1,$usr_add);
	//// check if query is not executed
	if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	$insid = mysqli_insert_id($link1);
    /// make 5 digit padding
    $pad=str_pad($insid,4,"0",STR_PAD_LEFT);
    //// make logic of employee code
    $newcrmid="CRM".$pad; 
	//////// update system genrated code in name
    $req_res = mysqli_query($link1,"UPDATE escalation_master set crm_id='".$newcrmid."' where id='".$insid."'");
	//// check if query is not executed
	if (!$req_res) {
		 $flag = false;
		 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$newcrmid,"Escalation Master","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully created a CRM ID like ".$newcrmid;
	$cflag="success";
	$cmsg="Success";
   }
   else if ($_POST['upd']=='Update'){ 
   
    $usr_upd = "UPDATE escalation_master set product_id ='".$product_name."', brand_id ='".$brand_name."',email='".$email."',phone='".$contact_no."',	level='".$level."',stateid='".$stateid."',state='".$state."',days='".$days."',hours='".$hours."',name='".$name."',sendsms='".$sendsms."' ,sendemail='".$sendemail."',status='".$status."' , updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."' where crm_id = '".$getid."'";
    $res_upd = mysqli_query($link1,$usr_upd);
	//// check if query is not executed
	if (!$res_upd) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$getid,"Escalation Management","UPDATE",$ip,$link1,$flag);
	////// return message
	$msg="You have successfully updated name details for ".$getid;
	$cflag="success";
	$cmsg="Success";
   }else{
	    $flag = false;
	   	$cflag = "info";
		$cmsg = "Warning";
   		$msg = "Request could not be processed. Please try again.";
   }
   ///// check both master and data query are successfully executed
	if ($flag) {
		mysqli_commit($link1);
	} else {
		mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	mysqli_close($link1);
   ///// move to parent page
    header("location:escalation_management.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
	$('#release_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});
	 
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-cube"></i> <?=$_REQUEST['op']?> Escalation Level</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Product Name <span class="red_small">*</span></label>
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
            <div class="col-md-6"><label class="col-md-6 control-label">Brand <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="brand_name" id="brand_name" onchange="return get_branddeatis();" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM brand_master where brand_id in (".$access_brand.") and status = '1' order by brand";
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
            <div class="col-md-6"><label class="col-md-6 control-label">Name <span class="red_small">*</span></label>
                <div class="col-md-6">
               	 <input type="text" name="name" class="required form-control" id="name" value="<?=$sel_result['name']?>" required/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Email</label>
              <div class="col-md-6">
                 <input type="text" name="email" class="form-control required" id="email" value="<?=$sel_result['email']?>"/>
              </div>
            </div>
          </div>
        
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Contact No.</label>
                <div class="col-md-6">
               	 <input type="text" name="contact_no" class="digits form-control required" id="contact_no" maxlength="10" value="<?=$sel_result['phone']?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Level</label>
              <div class="col-md-6">
				  <select name="level" id="level" class="form-control required" required>
					   <option value="">--Please Select--</option>
					   <option value="1"<?php if($sel_result['level'] == 1){ echo "selected";}?>>Lavel 1</option>
					  <option value="2"<?php if($sel_result['level'] == 2){ echo "selected";}?>>Lavel 2</option>
					   <option value="3"<?php if($sel_result['level'] == 3){ echo "selected";}?>>Lavel 3</option>
				  </select>
               
              </div>
            </div>
          </div>
			   
           
           <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Send SMS<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="sendsms"  id="sendsms1" value="Y" required <?php if($sel_result['sendsms']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="sendsms"  id="sendsms2" value="N" required <?php if($sel_result['sendsms']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
			   <div class="col-md-6"><label class="col-md-6 control-label">Send EMAIL<span class="red_small">*</span></label>
              <div class="col-md-6">
                 <div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="sendemail"  id="sendemail1" value="Y" required <?php if($sel_result['sendemail']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="sendemail"  id="sendemail2" value="N" required <?php if($sel_result['sendemail']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
          </div>
			  
			
			  
			 
          
          <div class="form-group">
			  <div class="col-md-6"><label class="col-md-6 control-label">State</label>
                <div class="col-md-6">
               
						<select name="stateid" id="stateid" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT stateid,state FROM state_master order by state";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['stateid']?>"<?php if($sel_result['stateid'] == $br_dept['stateid']){ echo "selected";}?>><?php echo $br_dept['state']?></option>
                <?php }?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Status</label>
             <div class="col-md-6">
             <select name="status" id="status" class="form-control required">
                    <?php foreach($arrstatus as $key => $value){?>
                    	<option value="<?=$key?>" <?php if($sel_result['status'] == $key) { echo 'selected'; }?>><?=$value?></option>
                    <?php } ?>
                 </select>
            </div>
            </div>
             
            
          </div>
			  
			  <div class="form-group">
			 
            <div class="col-md-6"><label class="col-md-6 control-label">Days</label>
             <div class="col-md-6">
             <select name="days" id="days" class="form-control required">
				 <option value="">--Please Select--</option>
                    <?php for($j=1;$j<=20; $j++){?>
                    	<option value="<?=$j?>" <?php if($sel_result['days'] == $j) { echo 'selected'; }?>><?=$j?> Days</option>
                    <?php } ?>
                 </select>
            </div>
            </div>
              <div class="col-md-6"><label class="col-md-6 control-label"></label>
               
            </div>
            
          </div>
          
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['crm_id'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='escalation_management.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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