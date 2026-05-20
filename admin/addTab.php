<?php
require_once("../includes/config.php");
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='edit'){
	$sel_usr="select * from tab_master where tabid='".$_REQUEST['tabid']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysql_error());
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
   if ($_POST['add']=='ADD'){
    $usr_add="INSERT INTO tab_master set subtabname ='".$subtabname."', subtabicon ='".$subtabicon."', subtabseq= '".$subtabseq."', maintabname='".$maintabname."', maintabicon='".$maintabicon."', maintabseq= '".$maintabseq."', status='".$status."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysql_error()); 
	////// return message
	$msg="You have successfully created a tab like ".$subtabname;
   }
   else if ($_POST['upd']=='Update'){
    $usr_upd="update tab_master set subtabname ='".$subtabname."', subtabicon ='".$subtabicon."', subtabseq= '".$subtabseq."', maintabname='".$maintabname."', maintabicon='".$maintabicon."', maintabseq= '".$maintabseq."', status='".$status."' where tabid = '".$tabid."'";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysql_error());
	////// return message
	$msg="You have successfully updated tab details for ".$subtabname;
   }
   ///// move to parent page
    header("location:tabmaster.php?msg=".$msg."".$pagenav);
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
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
$(document).ready(function(){
        $("#frm1").validate();
});
</script>
<script src="../js/frmvalidate.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-8">
      <h2 align="center"><i class="fa fa-th-list"></i> Add/Edit Tab</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Main Tab Name</label>
              <div class="col-md-5">
                 <input type="text" name="maintabname" class="required form-control" id="maintabname" value="<?=$sel_result['maintabname']?>" required/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Main Tab Seq.</label>
              <div class="col-md-5">
                <input type="text" name="maintabseq" id="maintabseq" class="form-control" value="<?php echo $sel_result['maintabseq'];?> " required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Main Tab Icon</label>
              <div class="col-md-5">
                 <input type="text" name="maintabicon" class="form-control" id="maintabicon" value="<?=$sel_result['maintabicon']?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label"></label>
              <div class="col-md-5">
          
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Sub Tab Name</label>
              <div class="col-md-5">
                 <input type="text" name="subtabname" class="required form-control" id="subtabname" value="<?=$sel_result['subtabname']?>" required/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Sub Tab Seq.</label>
              <div class="col-md-5">
                <input type="text" name="subtabseq" id="subtabseq" class="form-control" value="<?php echo $sel_result['subtabseq'];?> " required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Sub Tab Icon</label>
              <div class="col-md-5">
                 <input type="text" name="subtabicon" class="form-control" id="subtabicon" value="<?=$sel_result['subtabicon']?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label"></label>
              <div class="col-md-5">
          
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Status</label>
              <div class="col-md-5">
                 <select name='status' id='status' class="form-control">
                    <option value="1" <?php if($sel_result['status'] =='1') {echo 'selected'; }?>>Activate</option>
                    <option value="2" <?php if($sel_result['status'] =='2') {echo 'selected'; }?>>Deactivate</option>
                 </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-5">
                &nbsp;
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='add'){ ?>
              <input type="submit" class="btn btn-primary" name="add" id="add" value="ADD" title="Add Tab">
              <?php }else{?>
              <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Update" title="Update Tab Details">
              <?php }?>
              <input type="hidden" name="tabid"  id="tabid" value="<?=$sel_result['tabid']?>" />
              <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='tabmaster.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">            </div>
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