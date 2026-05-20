<?php
require_once("../includes/config.php");
include("../includes/brand_access.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);
////// case 1. if we want to update details
if ($_REQUEST['op']=='Edit'){
	$sel_usr="select * from partcode_master where partcode='".$getid."'";
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
		
	if($warranty_days != ""){ $warrantyDays = $warranty_days; }else { $warrantyDays = "365"; }	
		
$partnamenew = strtoupper($part_name);
$part_descnew = strtoupper($part_desc);

    ///////// insert model data	   
    $usr_add="INSERT INTO partcode_master set product_id ='".$product_name."', brand_id ='".$brand_name."', part_name='".$partnamenew."' , hsn_code ='".$hsn_code."' , vendor_partcode='".$vend_partcode."',part_desc='".$part_descnew."', customer_price ='".$customer_price."', part_category='".$part_category."',part_group='".$part_group."', part_for='".$part_for."', repair_code='".$rep_code."', status='".$status."',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."',location_price='".$location_price."',serial_part='".$serial_part."',l3_price='".$l3_price."', warranty_days = '".$warrantyDays."', wp = '".$warrantyDays."', dwp = '".$warrantyDays."'";
    $res_add=mysqli_query($link1,$usr_add);
	//// check if query is not executed
	if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	$insid = mysqli_insert_id($link1);
    /// make 5 digit padding
    $pad=str_pad($insid,5,"0",STR_PAD_LEFT);
    //// make logic of partcode code
	//$newpartcode="P".$pad; 
    $newpartcode=$partcode; 
	//////// update system genrated code in model
    $req_res = mysqli_query($link1,"UPDATE partcode_master set partcode='".$newpartcode."' where id='".$insid."'");
	//// check if query is not executed
	if (!$req_res) {
		 $flag = false;
		 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$newpartcode,"PARTCODE","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully created a partcode like ".$newpartcode;
	$cflag="success";
	$cmsg="Success";
   }
   else if ($_POST['upd']=='Update'){ 
   
   if($warranty_days != ""){ $warrantyDays = $warranty_days; }else { $warrantyDays = "365"; }

$partnamenew = upper($part_name);
$part_descnew = upper($part_desc);
   
    $usr_upd = "UPDATE partcode_master set product_id ='".$product_name."', brand_id ='".$brand_name."', part_name='".$partnamenew."' , hsn_code ='".$hsn_code."' , vendor_partcode='".$vend_partcode."',part_desc='".$part_descnew."',location_price='".$location_price."', customer_price ='".$customer_price."', part_category='".$part_category."',part_group='".$part_group."', part_for='".$part_for."', repair_code='".$rep_code."', status='".$status."' , updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."', warranty_days = '".$warrantyDays."'  where partcode = '".$getid."'";
    $res_upd = mysqli_query($link1,$usr_upd);
	//// check if query is not executed
	if (!$res_upd) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$getid,"PARTCODE","UPDATE",$ip,$link1,$flag);
	////// return message
	$msg="You have successfully updated partcode details for ".$getid;
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
    header("location:partcode_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 
///check special char///
var specialChars = "<>@!#$%^&*()_+[]{}?:;|'\"\\,./~`=";
var check = function(string){
    for(i = 0; i < specialChars.length;i++){
        if(string.indexOf(specialChars[i]) > -1){
            return true
        }
    }
    return false;
}
$(document).ready(function() {
$("#partcode").focusout(function(){	
if(check($('#partcode').val()) == true){
    $("#partcode").val("");
      $('#add').hide();
    alert("Partcode can not contains special char");
  
}else {
	$('#add').show();
}  
 
});
});
 
 
 
$(document).ready(function(){
        $("#frm1").validate();
});

$(document).ready(function() {
	
	$("#partcode").focusout(function(){
		var partcode=$("#partcode").val();
	$.post("chkpart.php?part="+partcode,
  
	function(data, status){
		if (data!='') {
			alert("this partcode "+data+" is already in CRM");
			$("#partcode").val("");
			$('#add').hide();
			}else {
			$('#add').show();	
			}
  });
    });
	});


</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-gears"></i> <?=$_REQUEST['op']?> Partcode</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Product Name <span class="red_small">*</span></label>
                <div class="col-md-6">
               		<select name="product_name" id="product_name" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM product_master where status = '1'"." ".$productfiltteradmin." "." order by product_name";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['product_id']?>"<?php if($sel_result['product_id'] == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>
                <?php }?>
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Brand <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <select name="brand_name" id="brand_name" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM brand_master where status = '1'"." ".$brandfiltteradmin." "." order by brand";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['brand_id']?>"<?php if($sel_result['brand_id'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
                <?php }?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">HSN CODE <span class="red_small">*</span></label>
                <div class="col-md-6">
               	 <select  class="required form-control" id="hsn_code" name="hsn_code" required>
               	 <option value="">Please Select</option>
               	 <?php $sql_hsn="select hsn_code from tax_hsn_master";
               	       $rs_hsn=mysqli_query($link1,$sql_hsn) or die(mysql_error());
               	       while($row_hsn=mysqli_fetch_assoc($rs_hsn)){?>
                        <option value="<?=$row_hsn['hsn_code']?>"><?=$row_hsn['hsn_code']?></option>
                        <?php }?>
                        </select>               	       
               	        
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Partcode&nbsp;</label>
              <div class="col-md-6">
                <input type="text" name="partcode" class="addressfield form-control required" id="partcode" value="<?=$sel_result['partcode']?>" required/>&nbsp;
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Vendor Partcode</label>
                <div class="col-md-6">
               	 <input type="text" name="vend_partcode" class="addressfield form-control required" id="vend_partcode" value="<?=$sel_result['vendor_partcode']?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Purchase Price</label>
              <div class="col-md-6">
                 <input type="text" name="l3_price" class="number form-control" id="l3_price" value="<?=$sel_result['l3_price']?>"/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Part Name <span class="red_small">*</span></label>
                <div class="col-md-6">
               	 <input type="text" name="part_name" class="addressfield form-control required" id="part_name" value="<?=$sel_result['part_name']?>" required/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Part Description</label>
              <div class="col-md-6">
                 <textarea name="part_desc" id="part_desc" class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$sel_result['part_desc']?></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Location Price</label>
                <div class="col-md-6">
               	 <input type="text" name="location_price" class="number form-control" id="location_price" value="<?=$sel_result['location_price']?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Customer Price</label>
              <div class="col-md-6">
                 <input type="text" name="customer_price" class="number form-control" id="customer_price" value="<?=$sel_result['customer_price']?>"/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Part Category <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<select name="part_category" id="part_category" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <option value="ACCESSORIES"<?php if($sel_result['part_category'] == "SCRAP"){ echo "selected";}?>>ACCESSORIES</option>
                  <option value="STATIONARY"<?php if($sel_result['part_category'] == "SCRAP"){ echo "selected";}?>>STATIONARY</option>
                  <option value="SPARE"<?php if($sel_result['part_category'] == "SPARE"){ echo "selected";}?>>SPARE</option>
                  <option value="TOOLS"<?php if($sel_result['part_category'] == "MISC"){ echo "selected";}?>>TOOLS</option>	
                  <option value="UNIT"<?php if($sel_result['part_category'] == "UNIT"){ echo "selected";}?>>UNIT</option>
				 
                </select>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Part For <span class="red_small">*</span></label>
              <div class="col-md-6">
                <select name="part_for" id="part_for" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <option value="ALL"<?php if($sel_result['part_for'] == "ALL"){ echo "selected";}?>>ALL</option>
                  <?php
					$dept_query="SELECT * FROM location_type_master order by usedname";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['usedname']?>"<?php if($sel_result['part_for'] == $br_dept['usedname']){ echo "selected";}?>><?php echo $br_dept['usedname']?></option>
                <?php }?>
                </select>
              </div>
            </div>
          </div>
         <?php /*?> <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Service Kit Flag <span class="red_small">*</span></label>
              <div class="col-md-6">
               	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="servicekit_flag"  id="servicekit_flag1" value="Y" required <?php if($sel_result['servicekit_flag']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="servicekit_flag"  id="servicekit_flag2" value="N" required <?php if($sel_result['servicekit_flag']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Service Kit Qty <span class="red_small">*</span></label>
              <div class="col-md-6">
                 <input type="text" name="servicekit_qty" class="digits form-control" id="servicekit_qty" value="<?=$sel_result['servicekit_qty']?>"/>
              </div>
            </div>
          </div><?php */?>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Part Group</label>
              <div class="col-md-6">
                	<select name="part_group" id="part_group" class="form-control" >
                  <option value="">--Please Select--</option>
                 
                  <?php
					$dept_query="SELECT * FROM part_group order by name";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['name']?>"<?php if($sel_result['part_group'] == $br_dept['name']){ echo "selected";}?>><?php echo $br_dept['name']?></option>
                <?php }?>
                </select>
                	
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Status </label>
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
            <div class="col-md-6"><label class="col-md-6 control-label">Warranty Days <span class="red_small">*</span></label>
                <div class="col-md-6">
               	 <input type="text" name="warranty_days" class="number form-control" id="warranty_days" value="<?=$sel_result['warranty_days']?>" required/>
              </div>
            </div>
            <?php /*?><div class="col-md-6"><label class="col-md-6 control-label">Extended Warranty Days</label>
              <div class="col-md-6">
                 <input type="text" name="ext_warranty_days" class="number form-control" id="ext_warranty_days" value="<?=$sel_result['ext_warranty_days']?>"/>
              </div>
            </div><?php */?>
            <div class="col-md-6"><label class="col-md-6 control-label">Serial Part <span class="red_small">*</span></label>
              <div class="col-md-6">
                	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="serial_part"  id="serial_part1" value="Y" required <?php if($sel_result['serial_part']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="serial_part"  id="serial_part2" value="N" required <?php if($sel_result['serial_part']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
          </div>
        
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($_REQUEST['op']=='Add'){ ?>
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Partcode">
              <?php }else{?>
              <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="Update Partcode Details">
              <?php }?>
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['partcode'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='partcode_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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