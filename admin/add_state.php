<?php
require_once("../includes/config.php");
/////get state//
$arrstate = getState($link1);
//print_r($arrstate);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from state_master where stateid ='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){

   if ($_POST['add']=='ADD'){ 
 
    $usr_add="INSERT INTO  state_master set   state ='".$state."',statecode='".$code."',zoneid='".$circle."' ,countryid ='".$country."'";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	$dptid = mysqli_insert_id($link1); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$state,"State","ADD",$_SERVER['REMOTE_ADDR'],$link1,'');
	////// return message
	$msg="You have successfully created a state with ref. no. ".$state;
   }
   else if ($_POST['upd']=='Update'){
    $usr_upd= "update state_master set statecode='".$code."',zoneid='".$circle."' ,countryid ='".$country."'  where stateid ='".$refid."' ";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$state2,"State","Edit",$_SERVER['REMOTE_ADDR'],$link1,'');
	////// return message
	$msg="You have successfully updated state details for ".$state2;
   }
   ///// move to parent page
    header("location:state_master.php?msg=".$msg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-map-marker"></i> <?=$_REQUEST['op']?>State</h2><br/><br/>
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">State</label>
              <div class="col-md-6">
          <?php if ($_REQUEST['op']!='Edit'){?>
                 <input type="text" name="state" class="required form-control" id="state" value="<?=$sel_result['state']?>" required/> 
                 <?php } else {
					 echo $sel_result['state'];
				 }?>
                 <input type="hidden" name="state2" class="required form-control" id="state2" value="<?=$sel_result['state']?>" required/> 
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Circle</label>
              <div class="col-md-6">
               
                 	  <select name="circle" id="circle" class="form-control required">
                  <option value="">Please Select</option>
                  <?php
                $res_pro = mysqli_query($link1,"select zoneid,zonename from zone_master where 1"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['zoneid']?>" <?php if($_REQUEST['circle'] == $row_pro['zoneid']) { echo 'selected'; }?>>
                  <?=$row_pro['zonename']?>
                  </option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Code</label>
              <div class="col-md-6">
        <input type="text" name="code" class="required form-control" id="code" value="<?=$sel_result['statecode']?>" required/>  
              </div>
            </div>
          </div>
		   <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Country</label>
              <div class="col-md-6">
                  <select name="country" id="country" class="form-control">
				  	<option value="<?php echo "India";?>" ><?php echo "India";?></option>
                  </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New State">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update State Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['stateid']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='state_master.php?status=<?=$pagenav?>'">
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