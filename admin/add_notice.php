<?php
require_once("../includes/config.php");
$getid=base64_decode($_REQUEST['id']);
$array_escl = array();

////// get details of selected location////
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from notice where sno='".$getid."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
/////get status//
$arrstatus = getFullStatus("master",$link1);
////// final submit form ////
@extract($_POST);
if($_POST){
	
$url="../download/Notice/".$file_url;
$date_range = explode(" - ",$_REQUEST['daterange']);
    if ($_POST['add']=='ADD'){
    ///////// insert folder condition
	
	
 

   $usr_add="insert into notice set subject='$subject',msg='$msg',date='$date_range[0]',end_date='$date_range[1]', type='Notice', status='1',filename='$file_url',url='$url' ";
    $res_add=mysqli_query($link1,$usr_add);




	
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$newpartcode,"Add Notice","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="Data Insert  successfully ";
	$cflag="success";
	$cmsg="Success";
   }
   else if ($_POST['upd']=='Update'){ 
   $usr_upd = "Update notice  set subject='$subject',msg='$msg',date='$date_range[0]',end_date='$date_range[1]', filename='$file_name', type='Notice', status='$status' ,filename='$file_url',url='$url' where sno='".$getid."'";
    $res_upd = mysqli_query($link1,$usr_upd);
///// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$getid,"Notice Details","UPDATE",$ip,$link1,$flag);
	////// return message
	$msg="You have successfully updated for ".$getid;
	$cflag="success";
	$cmsg="Success";
   }else{
	    $flag = false;
	   	$cflag = "info";
		$cmsg = "Warning";
   		$msg = "Request could not be processed. Please try again.";
   }
 
	
   ///// move to parent page
 header("location:notice_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
   exit;
}
?>

<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});


  
</script>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa-sticky-note-o "></i> <?=$_REQUEST['op']?> Notice</h2><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
      
    	  <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
              <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Subject <span class="red_small">*</span></label>
                      <div class="col-md-6">
                      <input name="subject" type="text" class="required form-control" id="subject" required value="<?=$sel_result['subject']?>">
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">Message <span class="red_small">*</span></label>
                      <div class="col-md-6">
                         <textarea name="msg" id="msg" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?php echo $sel_result['msg'];?></textarea>
                      </div>
                    </div>
                  </div>
               
                 
                  <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label">Date Range <span class="red_small">*</span></label>
                        <div class="col-md-6" >
                      <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
                      </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-6 control-label">File Name </label>
                      <div class="col-md-6">
                       
                             <input name="file_url" type="text" class=" form-control" id="file_url"  value="<?=$sel_result['filename']?>"><span class="red_small">Enter File name with Extenstion like(software.zip,software.rar)</span>
                      </div>
                    </div>
                  </div>		 
                <div class="form-group">
                    <div class="col-md-6"><label class="col-md-6 control-label"></label>
                      <div class="col-md-6">
                       
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="col-md-6 control-label">Status <span class="red_small">*</span></label>
                    
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
                      <input name="id" id="id" type="hidden" value="<?=base64_encode($sel_result['sno'])?>"/>
                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='notice_master.php?<?=$pagenav?>'">
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