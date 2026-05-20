<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$getid = $_REQUEST['id'];
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from holidays where sno='".$getid."'";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
	//// initialize transaction parameters
	$flag = true;
    mysqli_autocommit($link1, false);
    $error_msg="";
    if ($_POST['add']=='ADD'){
    ///////// insert model data	   
   $usr_add="INSERT INTO holidays set date ='".$pop_date."', description ='".$description."',  status='".$status."',eff_date='".date("Y-m-d H:i:s")."',eff_by='".$_SESSION['userid']."',location_code='".$_SESSION['asc_code']."',add_holiday='".$add_holiday."',weekly='".$weekly."',start_time='".$start_time."',end_time='".$end_time."',type='Additional Holiday' ";
	
    $res_add=mysqli_query($link1,$usr_add);
	//// check if query is not executed
	if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	$insid = mysqli_insert_id($link1);
   
	////// insert in activity table////
	$flag =  dailyActivity($_SESSION['userid'],$description,"Holiday ","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);

	////// return message
	$msg="You have successfully created a Holiday ".$description;
	$cflag="success";
	$cmsg="Success";
   }
   else if ($_POST['upd']=='Update'){ 
    $usr_upd = "UPDATE holidays set description ='".$description."', status='".$status."' , eff_date='".date("Y-m-d H:i:s")."',eff_by='".$_SESSION['userid']."',add_holiday='".$add_holiday."',weekly='".$weekly."',start_time='".$start_time."',end_time='".$end_time."'  where sno = '".$getid."'";
    $res_upd = mysqli_query($link1,$usr_upd);
	//// check if query is not executed
	if (!$res_upd) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
	 

	$flag =  dailyActivity($_SESSION['userid'],$description,"Holiday ","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully updated Holiday details for ".$description;
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
    header("location:asc_holiday_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
   <!-- Include Date Picker -->
 <link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 
 <script>

$(document).ready(function(){

        $("#frm1").validate();

    });
	



<?php
if($_REQUEST['p_dop']!='' ){?>
    $(document).ready(function () {
	  $('#pop_date').attr('readonly', true);
	});
	<?php }else{?>
	$(document).ready(function () {
		$('#pop_date').datepicker({
			format: "yyyy-mm-dd",
			startDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true,
		}).on('changeDate', function(ev){
    		//checkJobType();
			//getWarranty();
		})
	});



	<?php }?>
	
	<?php
if($_REQUEST['add_dop']!='' ){?>
    $(document).ready(function () {
	  $('#add_holiday').attr('readonly', true);
	});
	<?php }else{?>
	$(document).ready(function () {
		$('#add_holiday').datepicker({
			format: "yyyy-mm-dd",
			startDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true,
		}).on('changeDate', function(ev){
    		//checkJobType();
			//getWarranty();
		})
	});



	<?php }?>



</script>




</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-trophy"></i> <?=$_REQUEST['op']?> Additional Holiday</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Date <span class="red_small">*</span></label>
                     <?php if($_REQUEST['op']=='Edit'){?> <div class="col-md-6">

                      <div div style="display:inline-block;float:left;">

                      	<input name="pop_date" id="pop_date" type="text" value="<?=$sel_result['date']?>"  style="width:150px;" class="form-control required" readonly/>

                      </div>

                    </div> <?php } else{?>
                      <div class="col-md-6" ><div style="display:inline-block;float:left;">
                     
                     <input type="text" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="<?php if($sel_result['date']!=''){echo $sel_result['date']; } else{}?>" >
                     </div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                    
              </div>
              <?php }?>          
            </div>
            </div>
            <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Description/Purpose <span class="red_small">*</span></label>
              <div class="col-md-6">
               
                  <textarea name="description" id="description" required class="form-control"   onContextMenu="return false" style="resize:vertical"><?php echo $sel_result['description'];?></textarea>

              </div>
            </div>
  </div>
         
         
         <div class="form-group">
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
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Holiday">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update holiday Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['partcode'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asc_holiday_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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