<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from entity_type where id='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
   if ($_POST['add']=='ADD'){
    $usr_add="INSERT INTO entity_type set name ='".$name."',status_id='1',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	$dptid = mysqli_insert_id($link1); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$dptid,"Entity","ADD",$_SERVER['REMOTE_ADDR'],$link1,"");
	////// return message
	$msg="You have successfully created a Entity Type like ".$name;
	$cflag="success";
   }
   else if ($_POST['upd']=='Update'){
    $usr_upd="update entity_type set name ='".$name."',status_id='".$status."',updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."' where id = '".$refid."'";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$$refid,"Entity","UPDATE",$ip,$link1,"");
	////// return message
	$msg="You have successfully updated Entity details for ".$name;
	$cflag="success";
   }
   ///// move to parent page
    header("location:entity_master.php?msg=".$msg."&chkflag=".$cflag."".$pagenav);
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
<script src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-suitcase"></i> <?=$_REQUEST['op']?> Purchase From </h2>
      <br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Purchase From <span class="red_small">*</label>
              <div class="col-md-6">
                 <input type="text" name="name" class="required form-control" id="name" value="<?=$sel_result['name']?>" required/>
              </div>
            </div>
         </div>   <?php if($_REQUEST['op']!='Add'){ ?>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Status</label>
              <div class="col-md-6">
                  <select name="status" id="status" class="form-control">
                        <option value="1"<?php if($sel_result['statusid']=="1"){ echo "selected";}?>>Active</option>
                        <option value="2"<?php if($sel_result['statusid']=="2"){ echo "selected";}?>>Deactive</option>
                      </select>
              </div>
            </div>
          </div><?php }?>
          <div class="form-group">
            <div class="col-md-10" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Entity">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Entity Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['id']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='entity_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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