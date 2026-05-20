<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from company_master where companyid='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
   if ($_POST['add']=='ADD'){
    $usr_add="INSERT INTO company_master set  cname ='".$com_name."',groupid='".$cgroup."',status='".$status."',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	$dptid = mysqli_insert_id($link1); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$dptid,"COMPANY","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	////// return message
	$msg="You have successfully created a company with ref. no. ".$com_name;
   }
   else if ($_POST['upd']=='Update'){
    $usr_upd="update company_master set cname ='".$com_name."',groupid='".$cgroup."',status='".$status."',updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."' where companyid = '".$refid."'";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$com_name,"COMPANY","UPDATE",$ip,$link1,"");
	////// return message
	$msg="You have successfully updated company details for ".$refid;
   }
   ///// move to parent page
    header("location:company_master.php?msg=".$msg."".$pagenav);
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
    <div class="col-sm-8">
      <h2 align="center"><i class="fa fa-user-circle"></i> <?=$_REQUEST['op']?> Company</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Company Name</label>
              <div class="col-md-6">
                 <input type="text" name="com_name" class="required form-control" id="com_name" value="<?=$sel_result['cname']?>" required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Company Group</label>
              <div class="col-md-6">
                 <select name="cgroup" id="cgroup" class="required form-control" required>
                        <option value="">--Please Select--</option>
                    <?php
						$res_grp = mysqli_query($link1,"select * from group_master where status='1'"); 
						while($row_grp = mysqli_fetch_assoc($res_grp)){?>
                    	<option value="<?=$row_grp['groupid']?>" <?php if($sel_result['groupid'] == $row_grp['groupid']) { echo 'selected'; }?>><?=$row_grp['gname']?></option>
                    <?php } ?>
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
            <div class="col-md-10" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Company">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Company Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['companyid']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='company_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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