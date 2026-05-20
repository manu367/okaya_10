<?php
require_once("../includes/config.php");
/////get state//
$arrstate = getState($link1);
//print_r($arrstate);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from vendor_master where id ='".$_REQUEST['id']."' ";
	$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
	$sel_result=mysqli_fetch_assoc($sel_res12);
}
////// case 2. if we want to Add new user
if($_POST){
   if ($_POST['add']=='ADD'){ 
   /////////// initialize parameter ///////////////////////////////////
   	mysqli_autocommit($link1, false);
	$flag = true;
   $rs2=mysqli_query($link1,"select max(temp) as cnt from vendor_master");
	$row_cnt=mysqli_fetch_array($rs2);
	$new_temp=$row_cnt[0]+1;
	$id='VNDR'.$new_temp;
 	
	/////////// INSERT INTO VENDOR MASTER TABLE////////////////////////////////
    $usr_add="insert into vendor_master set id ='".$id."', temp='".$new_temp."', name='".$name."', city='".$city."', address='".$address."',  phone='".$phone."', state='".$state."', email='".$email."', country='".$country."', ship_address='".$ship_address."', fax='".$fax."',vendor_orign='".$vendor_origin."',modeofshipment='".$mode_of_ship."',start_date='".$today."',ip_address='".$_SERVER['REMOTE_ADDR']."' , bill_address= '".$bill_address."' , gst_no = '".$gst_no."' , tax_reg = '".$tax_reg."' , status = '1',remark='".$remark."' ";
    $res_add=mysqli_query($link1,$usr_add)or die("error3".mysqli_error($link1));
	$dptid = mysqli_insert_id($link1); 
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$dptid,"Supplier","ADD",$_SERVER['REMOTE_ADDR'],$link1,'');
	////// return message
	$msg="You have successfully created Supplier with id. ".$dptid;
   }
   else if ($_POST['upd']=='Update'){
   /////////// initialize parameter ///////////////////////////////////
   	mysqli_autocommit($link1, false);
	$flag = true;
    $usr_upd= "update vendor_master set  name='".$name."', city='".$city."', address='".$address."',  phone='".$phone."', state='".$state."', email='".$email."', country='".$country."', ship_address='".$ship_address."', fax='".$fax."',vendor_orign='".$vendor_origin."',modeofshipment='".$mode_of_ship."',start_date='".$today."',ip_address='".$_SERVER['REMOTE_ADDR']."' , bill_address= '".$bill_address."' , gst_no = '".$gst_no."' , tax_reg = '".$tax_reg."'  ,status = '".$status."' ,remark='".$remark."' where id ='".$refid."' ";
    $res_upd=mysqli_query($link1,$usr_upd)or die("error4".mysqli_error($link1));
	////// insert in activity table////
	dailyActivity($_SESSION['userid'],$refid,"Supplier","UPDATE",$_SERVER['REMOTE_ADDR'],$link1,'');
	////// return message
	$msg="You have successfully updated Supplier details for ".$refid;
   }
   
    if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed.";
	} 
    mysqli_close($link1);
	   ///// move to parent page
   header("location:supplier_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
        $("#form1").validate();
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i> <?=$_REQUEST['op']?>Supplier</h2><br/><br/>     
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
	  <?php if($_REQUEST['msg']){?><br>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
          <form  name="form1" class="form-horizontal" action="" method="post" id="form1" >
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Name<span class="red_small">*</span></label>
              <div class="col-md-5">
                
          <input name="name" id="name" type="text" class="form-control" size="40" value="<?=$sel_result['name']?>" required />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Contact No<span class="red_small">*</span></label>
              <div class="col-md-5">
                
          <input name="phone" id="phone" type="text" class="form-control digits required" size="40" value="<?=$sel_result['phone']?>"  maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" required />
      
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Email:<span class="red_small">*</span></label>
              <div class="col-md-5">
                 <input name="email"  id="email" type="text" class="form-control" size="40" value="<?=$sel_result['email']?>" required  />
                 
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">City<span class="red_small">*</span></label>
              <div class="col-md-5">
               <input name="city"  id="city" type="text" value="<?=$sel_result['city']?>" class="form-control" size="40" required  />
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State<span class="red_small">*</span></label>
              <div class="col-md-5">
                <input name="state" id="state" type="text" value="<?=$sel_result['state']?>" class="form-control" size="40" required  />
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Country<span class="red_small">*</span></label>
              <div class="col-md-5">
                <input name="country"  id="country" type="text" value="<?=$sel_result['country']?>" class="form-control" size="40" required  />
              </div>
            </div>
            
          </div>
		   <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Billing  Address<span class="red_small">*</span></label>
              <div class="col-md-5">
               <textarea name="bill_address"  id="bill_address"  cols="40" rows="4" required class="form-control"><?=$sel_result['bill_address']?></textarea>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Shipping Address</label>
              <div class="col-md-5">
               <textarea name="ship_address"  id="ship_address"  cols="40" rows="4" class="form-control"><?=$sel_result['ship_address']?></textarea>
            </div>
          </div>
             </div>
             <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Origin<span class="red_small">*</span></label>
              <div class="col-md-5">
               <select name="vendor_origin" id="vendor_origin"  required class="form-control">
                  <option value="">--Please Select--</option>
				<option value="Foreign" <?php if($sel_result['vendor_orign'] == "Foreign") { echo 'selected'; }?>>Foreign</option>
       		 </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Mode of Shipment<span class="red_small">*</span></label>
              <div class="col-md-5">
               <select name="mode_of_ship" id="mode_of_ship"  required class="form-control">
          		<option value="">--Please Select--</option>
		  		<option value="By Air" <?php if($sel_result['modeofshipment'] == "By Air") { echo 'selected'; }?>>By Air</option>
		   		<option value="By Road" <?php if($sel_result['modeofshipment'] == "By Road") { echo 'selected'; }?>>By Road</option>
		    	<option value="By Train" <?php if($sel_result['modeofshipment'] == "By Train") { echo 'selected'; }?>>By Train</option>
			 <option value="By Ship" <?php if($sel_result['modeofshipment'] == "By Ship") { echo 'selected'; }?>>By Ship</option>
        	</select>
            </div>
          </div>
             </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">GST No.</label>
              <div class="col-md-5">
               <input name="gst_no" id="gst_no" type="text" value="<?=$sel_result['gst_no']?>" class="form-control"  />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Tax Registration</label>
              <div class="col-md-5">
              <input name="tax_reg" id="tax_reg"  type="text" value="<?=$sel_result['tax_reg']?>" class="form-control"    />
            </div>
          </div>
             </div>
              <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Fax</label>
              <div class="col-md-5">
               <input name="fax"  id="fax"  type="text" value="<?=$sel_result['fax']?>" class="form-control"  />
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Vendor Address<span class="red_small">*</span></label>
              <div class="col-md-5">
               <textarea name="address" id="address" cols="40" rows="4" required class="form-control"><?=$sel_result['address']?></textarea>
            </div>
          </div>
             </div>  
			 <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Remarks</label>
              <div class="col-md-5">
                <textarea name="remark" id="remark" cols="40" rows="4"  class="form-control"><?=$sel_result['remark']?></textarea>
              </div>
            </div>
			 <?php if($_REQUEST['op']=='Edit'){ ?>
            <div class="col-md-6"><label class="col-md-5 control-label">status</label>
              <div class="col-md-5">
             
			   <select name="status" id="status"   class="form-control">
			 
			   <option value="1" <?php if($sel_result['status'] == "1") { echo 'selected'; }?>>Active</option>
			   <option value="2" <?php if($sel_result['status'] == "2") { echo 'selected'; }?>>Deactive</option>
			   </select>
            </div>
          </div><?php }?>
             </div>   
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Supplier">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Supplier Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=$sel_result['id']?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='supplier_master.php?status=<?=$pagenav?>'">
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