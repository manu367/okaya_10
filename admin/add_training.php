<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from tech_training where sno='".$getid."'";
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
   $usr_add="INSERT INTO tech_training set location_code ='".$locationname."', user_code ='".$engname."',  type='".$type."',trainername='".$trinorname."',tr_desc='".$description."',t_date='".$pop_date."',e_date='".$end_date."',score='".$score."' ";
    $res_add=mysqli_query($link1,$usr_add);
	//// check if query is not executed
	if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
   
	////// insert in activity table////
	$flag =  dailyActivity($_SESSION['userid'],$type,"Training ","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);

	////// return message
	$msg="You have successfully created a Training Type ".$type;
	$cflag="success";
	$cmsg="Success";
	
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
    header("location:tecnical_tec.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
   }
   else if ($_POST['upd']=='Update'){ 
    $usr_upd = "UPDATE tech_training set type='".$type."',trainername='".$trinorname."',tr_desc='".$description."',t_date='".$pop_date."',e_date='".$end_date."',score='".$score."' where sno = '".$getid."'";
    $res_upd = mysqli_query($link1,$usr_upd);
	//// check if query is not executed
	if (!$res_upd) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
	 

	$flag =  dailyActivity($_SESSION['userid'],$type,"Training ","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully updated Training details for ".$type;
	$cflag="success";
	$cmsg="Success";
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
    header("location:tecnical_tec.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
   
   }else{}
     ///// check both master and data query are successfully executed
   
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
			endDate: "<?=$today?>",
			todayHighlight: true,
			autoclose: true,
		}).on('changeDate', function(ev){
    		//checkJobType();
			//getWarranty();
		})
	});



	<?php }?>
	
	<?php
if($_REQUEST['p_dop']!='' ){?>
    $(document).ready(function () {
	  $('#end_date').attr('readonly', true);
	});
	<?php }else{?>
	$(document).ready(function () {
		$('#end_date').datepicker({
			format: "yyyy-mm-dd",
			endDate: "<?=$today?>",
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
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-gears"></i> <?=$_REQUEST['op']?> Training</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Location <span class="red_small">*</span></label>	  
			<div class="col-md-5" >
           <?php if($_REQUEST['op']=='Edit'){?> 
           <input type="text" name="locationname" id="locationname" readonly  class="form-control" value="<?=getAnyDetails($sel_result["location_code"],"locationname","location_code","location_master",$link1);?>" />
           <?php }else{?>
                  <select name="locationname" id="locationname"  class="form-control"  onChange="document.frm1.submit();">
                  <option value="">Select Location </option>
				  <?php
				   $location_query="SELECT locationname, location_code FROM location_master where locationtype = 'ASP' and statusid='1' order by locationname asc ";
     $loc_res=mysqli_query($link1,$location_query);
     while($loc_info = mysqli_fetch_array($loc_res)){?>
				  <option value="<?=$loc_info['location_code']?>" <?php if($_REQUEST['locationname'] == $loc_info['location_code']) { echo 'selected'; }?>><?=$loc_info['locationname']?></option>
				<?php }  ?>
                 </select>
                 <?php } ?>
              </div>
          </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Eng Name <span class="red_small">*</span></label>
                <div class="col-md-5">
                 <?php if($_REQUEST['op']=='Edit'){?> 
           <input type="text" name="engname" id="engname" readonly class="form-control" value="<?=getAnyDetails($sel_result["user_code"],"locusername","userloginid","locationuser_master",$link1)?> " />
           <?php }else{?>
               	 <select name="engname" id="engname"  class="form-control"  onChange="document.frm1.submit();">
				  <?php
				   $location_query="SELECT locusername, userloginid FROM locationuser_master where location_code = '".$_REQUEST['locationname']."' and statusid='1' order by locusername asc ";
     $loc_res=mysqli_query($link1,$location_query);
     while($loc_info = mysqli_fetch_array($loc_res)){?>
				  <option value="<?=$loc_info['userloginid']?>" <?php if($_REQUEST['engname'] == $loc_info['userloginid']) { echo 'selected'; }?>><?=$loc_info['locusername']?></option>
				<?php }  ?>
                 </select>
                 <?php } ?>
              </div>
            </div>
            </div>
            <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Type of Training <span class="red_small">*</span></label>
              <div class="col-md-6">
               <input type="text" name="type" id="type" required class="form-control" value="<?php echo $sel_result['type'];?>" />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Description <span class="red_small">*</span> </label>
                <div class="col-md-6">
               	 <textarea name="description" id="description" required class="form-control"   onContextMenu="return false" style="resize:vertical"><?php echo $sel_result['tr_desc'];?></textarea>
              </div>
            </div>
          </div>
           <div class="form-group">
             <div class="col-md-6"><label class="col-md-6 control-label">Start Date <span class="red_small">*</span></label>
                     <?php if($_REQUEST['op']=='Edit'){?> <div class="col-md-6">

                      <div style="display:inline-block;float:left;">

                      	<input name="pop_date" id="pop_date" type="text" value="<?=$sel_result['t_date']?>"  style="width:150px;" class="form-control required" readonly/>

                      </div>

                    </div> <?php } else{?>
                      <div class="col-md-6" ><div style="display:inline-block;float:left;">
                     
                     <input type="text" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="<?php if($sel_result['t_date']!=''){echo $sel_result['date']; } else{}?>" >
                     </div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                    
              </div>
              <?php }?>          
            </div>
          <div class="col-md-6"> <label class="col-md-6 control-label">End Date <span class="red_small">*</span></label>
                     <?php if($_REQUEST['op']=='Edit'){?> <div class="col-md-6">

                      <div style="display:inline-block;float:left;">

                      	<input name="end_date" id="end_date" type="text" value="<?=$sel_result['e_date']?>"  style="width:150px;" class="form-control required" readonly/>

                      </div>

                    </div> <?php } else{?>
                      <div class="col-md-6" ><div style="display:inline-block;float:left;">
                     
                     <input type="text" class="form-control required" name="end_date"  id="end_date" style="width:150px;" value="<?php if($sel_result['e_date']!=''){echo $sel_result['date']; } else{}?>" >
                     </div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                    
              </div>
              <?php }?>          
            </div>
          </div>
        <div class="form-group">
             <div class="col-md-6"><label class="col-md-6 control-label">Score <span class="red_small">*<br>Out of 100</span></label>
                <div class="col-md-6">
               	  <input type="text" name="score" id="score" required class="form-control number" min="0" max="100" value="<?php echo $sel_result['score'];?>" />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Trainor Name <span class="red_small">*</span></label>
              <div class="col-md-6">
               <input type="text" name="trinorname" id="trinorname" required class="form-control" value="<?php echo $sel_result['trainername'];?>" />
              </div>
            </div>
          </div>
         
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Training">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Training Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['sno'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='tecnical_tec.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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