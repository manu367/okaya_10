<?php
require_once("../includes/config.php");
/////get status//
$today=date("Y-m-d",$time_zone);
$rs=mysqli_query($link1,"select * from sfr_challan where challan_no='".$_REQUEST['challan_no']."' ");
$row=mysqli_fetch_array($rs);
@extract($_POST);
////// case 1. if we want to update details

////// case 2. if we want to Add new user
if($_POST){
	//// initialize transaction parameters
	$flag=true;
mysqli_autocommit($link1, false);
$error_msg="";
    if ($_POST['upd']=='Receive'){
    /////////  checking  sfr Transaction////////////////////////////
		   
 $sql="select * from sfr_transaction where challan_no='".$_REQUEST['challan_no']."' and status='1'";
$rs=mysqli_query($link1,$sql)or die("error1".mysql_error());
while($row=mysqli_fetch_array($rs)){
$imei_b="imei".$row['sno'];
$job_b="job_no".$row['sno'];
$status_b="status_type".$row['sno'];
$part="partcode".$row['sno'];



//////////////////// SFR OK Receive/////////////////////////////
if($_POST[$status_b]=='OK'){

//////////////////////////////// update SFR Transaction//////////////////////////////////////

 $up_sfr_trac=mysqli_query($link1,"update sfr_transaction set rec_type='".$_POST[$status_b]."'  where sno='".$row['sno']."'" );
 //////////////////////////////// update SFR Bin//////////////////////////////////////
  $up_sfr_bin=mysqli_query($link1,"update sfr_bin set status='419'  where job_no='".$_POST[$job_b]."'" );
   //////////////////////////////// update call  history//////////////////////////////////////
    $flag2 = callHistory($_POST[$job_b],$_SESSION['asc_code'],"419","SFR Received at L3/L4","SFR Received at L3/L4",$_SESSION['userid'],"","","","",$ip,$link1,$flag);
	////////////////////////////////////////////SFR Challan /////////////////////////////////////////////////////
	//echo "update sfr_challan set  receive_date='".$today."',status='4'  where challan_no='".$_REQUEST['challan_no']."'";
	  $up_sfr_challan=mysqli_query($link1,"update sfr_challan set  receive_date='".$today."',status='4'  where challan_no='".$_REQUEST['challan_no']."'" );
	  
	  /////////////////////////////////////Job Sheet data////////////////////////////////////////////////////////
   $up_job=mysqli_query($link1,"update jobsheet_data set sub_status='419',current_location='".$_SESSION['asc_code']."',close_date='".$today."',close_time='".$currtime."'  where job_no='".$_POST[$job_b]."'" );
}

//////////////////////////////////////////////SFR Damage Recieve/////////////////////////////////////////////////

if($_POST[$status_b]=='Damage'){
//////////////////////////////// update SFR Transaction//////////////////////////////////////
 $up_sfr_trac=mysqli_query($link1,"update sfr_transaction set rec_type='".$_POST[$status_b]."'  where sno='".$row['sno']."'" );
 //////////////////////////////// update SFR Bin//////////////////////////////////////
  $up_sfr_bin=mysqli_query($link1,"update sfr_bin set status='419'  where job_no='".$_POST[$job_b]."'" );
   //////////////////////////////// update call  history//////////////////////////////////////
    $flag2 = callHistory($_POST[$job_b],$_SESSION['asc_code'],"419","SFR Received at L3/L4","SFR Received at L3/L4",$_SESSION['userid'],"","","","",$ip,$link1,$flag);
	////////////////////////////////////////////SFR Challan /////////////////////////////////////////////////////
	  $up_sfr_challan=mysqli_query($link1,"update sfr_challan set  receive_date='".$today."',status='4'  where challan_no='".$_REQUEST['challan_no']."'" );
	  
	  /////////////////////////////////////Job Sheet data////////////////////////////////////////////////////////
   $up_job=mysqli_query($link1,"update jobsheet_data set sub_status='419' ,current_location='".$_SESSION['asc_code']."'  where  where job_no='".$_POST[$job_b]."'" );
}
/////////////////////////////SFR Missing////////////////////////////////////////////////////////
if($_POST[$status_b]=='Missing'){
//////////////////////////////// update SFR Transaction//////////////////////////////////////
 $up_sfr_trac=mysqli_query($link1,"update sfr_transaction set rec_type='".$_POST[$status_b]."'  where sno='".$row['sno']."'" );
 //////////////////////////////// update SFR Bin//////////////////////////////////////
  $up_sfr_bin=mysqli_query($link1,"update sfr_bin set status='421'  where job_no='".$_POST[$job_b]."'" );
   //////////////////////////////// update call  history//////////////////////////////////////
    $flag2 = callHistory($_POST[$job_b],$_SESSION['asc_code'],"421","SFR Missing at L3/L4","SFR Missing at L3/L4",$_SESSION['userid'],"","","","",$ip,$link1,$flag);
	////////////////////////////////////////////SFR Challan /////////////////////////////////////////////////////
	  $up_sfr_challan=mysqli_query($link1,"update sfr_challan set  receive_date='".$today."',status='6'  where challan_no='".$_REQUEST['challan_no']."'" );
	  
	  /////////////////////////////////////Job Sheet data////////////////////////////////////////////////////////
   $up_job=mysqli_query($link1,"update jobsheet_data set sub_status='421' ,current_location='".$_SESSION['asc_code']."' where job_no='".$_POST[$job_b]."'" );
}
///////////////////////////////////////////////////SFR Rejection////////////////////////////////////////////////
if($_POST[$status_b]=='Reject'){
//////////////////////////////// update SFR Transaction//////////////////////////////////////
 $up_sfr_trac=mysqli_query($link1,"update sfr_transaction set rec_type='".$_POST[$status_b]."'  where sno='".$row['sno']."'" );
 //////////////////////////////// update SFR Bin//////////////////////////////////////
  $up_sfr_bin=mysqli_query($link1,"update sfr_bin set status='421'  where job_no='".$_POST[$job_b]."'" );
   //////////////////////////////// update call  history//////////////////////////////////////
    $flag2 = callHistory($_POST[$job_b],$_SESSION['asc_code'],"421","SFR Missing at L3/L4","SFR Missing at L3/L4",$_SESSION['userid'],"","","","",$ip,$link1,$flag);
	////////////////////////////////////////////SFR Challan /////////////////////////////////////////////////////
	  $up_sfr_challan=mysqli_query($link1,"update sfr_challan set  receive_date='".$today."',status='6'  where challan_no='".$_POST[$job_no]."'" );
	  
	  /////////////////////////////////////Job Sheet data////////////////////////////////////////////////////////
   $up_job=mysqli_query($link1,"update jobsheet_data set sub_status='421',current_location='".$_SESSION['asc_code']."'  where job_no='".$_POST[$job_b]."'" );
}

}


}

   if (!$up_job) {
    $flag = false;
   $msg = "Error details2.1: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_in_receive.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
    else if (!$up_sfr_bin) {
    $flag = false;
    $msg = "Error details2.2: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_in_receive.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
 else  if (!$up_sfr_challan) {
    $flag = false;
    $msg = "Error details2.3: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_in_receive.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
  else  if (!$flag2) {
    $flag = false;
   $msg = "Error details2.4: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_in_receive.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
    else  if (!$up_sfr_trac) {
    $flag = false;
   $msg = "Error details2.5: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:sfr_in_receive.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
  else {
  
  $cflag="success";
		$cmsg="Success";
		$msg="You have successfully Receive  Handset With challan no ".$_REQUEST['challan_no'];
		mysqli_commit($link1);
	header("location:sfr_els_job.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
 


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
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-8">
      <h2 align="center"><i class="fa fa-bug"></i> SFR Receive </h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-12control-label">Genrate By <span class="red_small">*</span></label>
                <div class="col-md-12">
                <table  class="table table-bordered" width="100%" id="myTable"  align="center">
		  <tr><td width="50%" colspan="8"><b><?=$row['from_location']?></b>
                              <br>
                              <?=$row['from_address']?>
                           
                            GSTIN No.-  
                            <b><?=$row['from_gst_no']?></b></td>
                            <td width="50%" colspan="8">Challan No - <strong>
                            <?=$row['challan_no']?>
                            </strong><br>
                               Genrate Date -
                          <?=dt_format($row['challan_date'])?></td>
               </tr></table>
              </div>
            </div>
           
          </div>
      
          <div class="form-group">
          
                <div class="col-md-12">
               	   <table class="table table-bordered" width="100%">
                <tbody>
                  <tr>
                    <td width="5%"><label class="control-label">Sno</label></td>
                    <td width="15%"><label class="control-label">Job No</label></td>
                    <td width="20%"><label class="control-label">IMEI</label></td>
                    <td width="20%"><label class="control-label">Model</label></td>
					 <td width="30%"><label class="control-label">Receive Type</label></td>
                  </tr>
				  
				 <?PHP 
				 $sno=0;
				 	$sel_tras="select * from sfr_transaction where challan_no='".$_REQUEST['challan_no']."'";
	$sel_res12=mysqli_query($link1,$sel_tras)or die("error1".mysqli_error($link1));
                 while($sfr = mysqli_fetch_array($sel_res12)){ 
				 $sno=$sno+1;   ?>
				 <tr>
				 
				   <td width="5%"><label class="control-label"><?php echo $sno;?></label></td>
                    <td width="15%"><label class="control-label"><?=$sfr['job_no']?>  <input type="hidden" name="job_no<?=$sfr['sno']?>" class="number form-control" id="job_no<?=$sfr['sno']?>" value="<?=$sfr['job_no']?>"/></label></label></td>
                    <td width="20%"><label class="control-label"><?=$sfr['imei']?><input type="hidden" name="imei" class="number form-control" id="imei" value="<?=$sfr['imei']?>"/></label></td>
                    <td width="20%"><label class="control-label"><?=getAnyDetails($sfr["part_id"],"part_name","partcode","partcode_master",$link1)."-".getAnyDetails($sfr["model_id"],"model","model_id","model_master",$link1)?><input type="hidden" name="model_id" class="number form-control" id="model_id" value="<?=$sfr['partcode']?>"/></label></td>
					 <td width="30%"><label class="control-label">  <select name="status_type<?=$sfr['sno']?>" id="status_type<?=$sfr['sno']?>" class="form-control" >
                    <option value="OK"<?php if($_REQUEST['status_type']=='OK'){ echo "selected";}?>>OK</option>
                  <!--  <option value="Damage"<?php if($_REQUEST['status_type']=='Damage'){ echo "selected";}?>>Damage</option>
                    <option value="Missing"<?php if($_REQUEST['status_type']=='Missing'){ echo "selected";}?>>Missing</option>
					 <option value="Reject"<?php if($_REQUEST['status_type']=='Reject'){ echo "selected";}?>>Reject</option>-->
                </select></label></td>
				 </tr>
				 <?php }?>
				  </tbody>
              </table> 
              </div>
          
             
          </div>
        
       
           
           
          <div class="form-group">
            <div class="col-md-12" align="center">
          
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive Challan">
             
              <input type="hidden" name="challan_no"  id="challan_no" value="<?=$_REQUEST['challan_no']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='sfr_in_receive.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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