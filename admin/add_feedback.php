<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from feedback_master where sno='".$getid."'";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
	//// initialize transaction parameters

    if ($_POST['add']=='ADD'){
    ///////// insert model data	   
   $usr_add="INSERT INTO feedback_master set feedback ='".$name."', status='".$status."'";
    $res_add=mysqli_query($link1,$usr_add)or die(mysqli_error($link1));

	
	//// check if query is not executed


	
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$name,"Feedback","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully created a Feedback Details like ".$newmodelcode;
	$cflag="success";
	$cmsg="Success";
   }
     else if ($_POST['upd']=='Update'){ 
   $usr_upd = "Update feedback_master set feedback ='".$name."', status='".$status."' where sno='".$getid."'";
    $res_upd = mysqli_query($link1,$usr_upd)or die(mysqli_error($link1));
///// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$getid,"feedback details","UPDATE",$ip,$link1,$flag);
	////// return message
	$msg="You have successfully updated feedback details for ".$getid;
	$cflag="success";
	$cmsg="Success";
   }
 
	mysqli_close($link1);
   ///// move to parent page
   header("location:confrim_reason_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-bank"></i> <?=$_REQUEST['op']?> Feedback </h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Feedback<span class="red_small">*</span></label>
                <div class="col-md-6">
               <input name="name" type="text" id="name" class="required form-control " value="<?=$sel_result['feedback']?>" required>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Status <span class="red_small">*</span></label>
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
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Feedback">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Feedback Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['sno'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='confrim_reason_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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