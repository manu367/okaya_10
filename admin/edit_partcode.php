<?php
require_once("../includes/config.php");
global $link1;
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
////// final submit form ////
$msg="";
@extract($_POST);
if($_POST){
	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg="";
	if($_POST['Submit1']=='Save'){
		if($warranty_days != ""){ $warrantyDays = $warranty_days; }else { $warrantyDays = "365"; }

//$part_namenew = upper($part_name);
//$part_descnew  = upper($part_desc);

		// update all details of partcode //
	   	$usr_upd = "UPDATE partcode_master set l3_price='".$l3_price."',product_id ='".$product_name."', brand_id ='".$brand_name."', part_name='".$part_name."' , hsn_code ='".$hsn_code."' , vendor_partcode='".$vend_partcode."',part_desc='".$part_desc."', customer_price ='".$customer_price."', servicekit_qty='".$servicekit_qty."',servicekit_flag='".$servicekit_flag."', part_category='".$part_category."', part_for='".$part_for."', status='".$status."' , updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."',location_price ='".$location_price."' ,faulty_part='".$faulty_part."',serial_part='".$serial_part."', warranty_days = '".$warrantyDays."', wp = '".$warrantyDays."', dwp = '".$warrantyDays."',wight='".$wight."', ext_warranty_days = '".$ext_warranty_days."',part_group='".$part_group."'  where partcode = '".$getid."'";
		$res_upd = mysqli_query($link1,$usr_upd);
		//// check if query is not executed
		if (!$res_upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
   		//////////////////////////////////////////////////////////////
   		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$getid,"PARTCODE","UPDATE",$ip,$link1,$flag);
		////// return message
		$msg="You have successfully updated partcode details for ".$getid;
		$cflag="success";
		$cmsg="Success";
	}////close 1st tab
	else if($_POST['Submit2']=='Save'){//// update partcode model mapping
		/*// Update Function Rights
		$res_upd = mysqli_query($link1,"update map_partcode_model set status='' where partcode='".$getid."'");
		//// check if query is not executed
		if (!$res_upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
		$postmapdata=$_POST['mappartmodel'];
		$count=count($postmapdata);
		$j=0;
		while($j < $count){
			if($postmapdata[$j]==''){
				$newstatus = "";
			}else{
				$newstatus = "Y";
			}
			// alrady exist
			if(mysqli_num_rows(mysqli_query($link1,"select id from map_partcode_model where partcode='".$getid."' and model_id='".$postmapdata[$j]."'"))>0){
				$res_mapupd = mysqli_query($link1,"update map_partcode_model set status='".$newstatus."' where partcode='".$getid."' and model_id='".$postmapdata[$j]."'");
			}else{
				$res_mapupd = mysqli_query($link1,"insert into map_partcode_model set partcode='".$getid."', model_id='".$postmapdata[$j]."', status='".$newstatus."'");
			}
			//// check if query is not executed
			if (!$res_mapupd) {
				 $flag = false;
				 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
			$j++;
		}//// close while loop
*/
        if(count($_POST['mappartmodel'])>0){
			$array_mappedmodel = implode(",", $_POST['mappartmodel']);
		}else{
			$array_mappedmodel = "";
		}
		// update all details of partcode //
	   	$usr_upd = "UPDATE partcode_master set model_id ='".$array_mappedmodel."', updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."' where partcode = '".$getid."'";
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
	}////close 2nd tab
	else if($_POST['Submit3']=='Save'){//// update partcode repaircode mapping
		if(count($_POST['mappartrepcode'])>0){
			$array_mappedrepcode = implode(",", $_POST['mappartrepcode']);
		}else{
			$array_mappedrepcode = "";
		}
		// update all details of partcode //
	   	$usr_upd = "UPDATE partcode_master set repair_code ='".$array_mappedrepcode."', updatedate='".date("Y-m-d H:i:s")."',updateby='".$_SESSION['userid']."' where partcode = '".$getid."'";
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
	}////close 3rd tab 
	
	else if($_POST['Submit4']=='Save'){//// update partcode Alternate partcode mapping
	
	$res_upd = mysqli_query($link1,"update alt_part_map set status='0' where  partcode = '".$getid."'");
	
	$postmapdata=$_POST['mappartatercode'];
	
	$count=count($postmapdata);
		$j=0;
	while($j < $count){
		if($postmapdata[$j]==''){
			$newstatus = "0";
		}else{
			$newstatus = "1";
		}
	
	
if(mysqli_num_rows(mysqli_query($link1,"select sno from alt_part_map where  partcode = '".$getid."' and  alter_partcode='".$postmapdata[$j]."' "))>0){
		//echo "update location_pincode_access set statusid='".$newstatus."' where location_code='".$locationcode."' and pincode='".$postmapdata[$j]."',area_type='".$_POST[$travel]."' and cityid='".$pincity."'";
			$res_mapupd = mysqli_query($link1,"update alt_part_map set status='".$newstatus."' where partcode = '".$getid."' and alter_partcode='".$postmapdata[$j]."' ");
		}else{
		//echo "insert into location_pincode_access set location_code='".$locationcode."', pincode='".$postmapdata[$j]."', cityid='".$pincity."', statusid='".$newstatus."',area_type='".$_POST[$travel]."'";
			$res_mapupd = mysqli_query($link1,"insert into alt_part_map set status='".$newstatus."' , partcode = '".$getid."' ,alter_partcode='".$postmapdata[$j]."'");
		}
		$j++;
	}
		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$getid,"PARTCODE","UPDATE",$ip,$link1,$flag);
		////// return message
		$msg="You have successfully updated partcode details for ".$getid;
		$cflag="success";
		$cmsg="Success";
	}////close 3rd tab 
	else{
		////// return message
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


////// get details of selected partcode////
$sel_usr="select * from partcode_master where partcode='".$getid."'";
$sel_res12=mysqli_query($link1,$sel_usr)or die("error1".mysqli_error($link1));
$sel_result=mysqli_fetch_assoc($sel_res12);
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

    function getmappartcode(){
        var partcode_map=$('#part_search').val();
        var partcode=$('#partcode').val();
        $.ajax({
            type:'post',
            url:'../includes/getAzaxFields.php',
            data:{alt_part_serch:partcode, part_serch:partcode_map},
            success:function(data){
                $('#disp_pincode').html(data);
            }
        });
    };

    function checkAll(field){
        for (i = 0; i < field.length; i++)
            field[i].checked = true ;
    }

    function uncheckAll(field){
        for (i = 0; i < field.length; i++)
            field[i].checked = false ;
    }
 </script>
 <script src="../js/frmvalidate.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-gears"></i> View/Edit Partcode</h2>
      <h4 align="center"><?=$sel_result['part_name']."  (".$sel_result['partcode'].")";?>
      <?php if($_POST['Submit1']=='Save' || $_POST['Submit2']=='Save' || $_POST['Submit3']=='Save'){ ?>
      <br/>
      <span style="color:#FF0000"><?php echo $msg; ?></span>
      <?php } ?>
      </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
      	 <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-gear"></i> General Details</a></li>
            <li><a data-toggle="tab" href="#menu1"><i class="fa fa-cube"></i> Model Mapping</a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-cog"></i> Solution Given Mapping</a></li>
			  <li><a data-toggle="tab" href="#menu3"><i class="fa fa-cog"></i> Alternate Part Mapping</a></li>
          </ul>
    	  <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
              <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                  <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Product Name <span class="red_small">*</span></label>
                <div class="col-md-6">
               		<select name="product_name" id="product_name" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM product_master where status = '1' order by product_name";
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
                 <select name="brand_name" id="brand_name" class="form-control">
                  <option value="">--Please Select--</option>
                  <?php
					$dept_query="SELECT * FROM brand_master where status = '1' order by brand";
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
               	       $rs_hsn=mysqli_query($link1,$sql_hsn) or die('mysql exception');
               	       while($row_hsn=mysqli_fetch_assoc($rs_hsn)){?>
                        <option value="<?=$row_hsn['hsn_code']?>"<?php if($sel_result['hsn_code']==$row_hsn['hsn_code']) echo 'selected';?>><?=$row_hsn['hsn_code']?></option>
                        <?php }?>
                        </select>               	       
               	        
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Partcode</label>
              <div class="col-md-6">
                 <input type="text" name="partcode" class="form-control" id="partcode" value="<?=$sel_result['partcode']?>" readonly required/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Vendor Partcode</label>
                <div class="col-md-6">
               	 <input type="text" name="vend_partcode" class="form-control" id="vend_partcode" value="<?=$sel_result['vendor_partcode']?>"/>
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
               	 <input type="text" name="part_name" class="addressfield  form-control required" id="part_name" value="<?=$sel_result['part_name']?>" required/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Part Description</label>
              <div class="col-md-6">
                 <textarea name="part_desc" id="part_desc" class="addressfield  form-control required" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?=$sel_result['part_desc']?></textarea>
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
                  <option value="ACCESSORY"<?php if($sel_result['part_category'] == "ACCESSORY"){ echo "selected";}?>>Accessory</option>
                  <option value="SPARE"<?php if($sel_result['part_category'] == "SPARE"){ echo "selected";}?>>Component</option>
                  <option value="BOX"<?php if($sel_result['part_category'] == "BOX"){ echo "selected";}?>>Box</option>
                  <option value="UNIT"<?php if($sel_result['part_category'] == "UNIT"){ echo "selected";}?>>Unit</option>
				  <option value="MOTER"<?php if($sel_result['part_category'] == "MOTER"){ echo "selected";}?>>Moter</option>
                    <option value="GLOBAL"<?php if($sel_result['part_category'] == "GLOBAL"){ echo "selected";}?>>General</option>
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
          <div class="form-group">
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
          </div>
          <div class="form-group">
               <div class="col-md-6"><label class="col-md-6 control-label">Faulty Part Dispatch to WH <span class="red_small">*</span></label>
              <div class="col-md-6">
                	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="faulty_part"  id="faulty_part1" value="Y" required <?php if($sel_result['faulty_part']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="faulty_part"  id="faulty_part2" value="N" required <?php if($sel_result['faulty_part']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
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
            <div class="col-md-6"><label class="col-md-6 control-label">Warranty Days</label>
                <div class="col-md-6">
               	 <input type="text" name="warranty_days" class="number form-control" id="warranty_days" value="<?=$sel_result['warranty_days']?>"/>
              </div>
            </div>
            <div class="col-md-6"><label class="col-md-6 control-label">Extended Warranty Days</label>
              <div class="col-md-6">
                 <input type="text" name="ext_warranty_days" class="number form-control" id="ext_warranty_days" value="<?=$sel_result['ext_warranty_days']?>"/>
              </div>
            </div>
          </div>
          
          <div class="form-group">
              <div class="col-md-6"><label class="col-md-6 control-label">Weight IN KG</label>
                <div class="col-md-6">
               	 <input type="text" name="wight" class="form-control" id="wight" value="<?=$sel_result['wight']?>"/>
              </div>
            </div>
               <div class="col-md-6"><label class="col-md-6 control-label">Serial Part<span class="red_small">*</span></label>
              <div class="col-md-6">
                	<div style="display:inline-block; float:left"><input type="radio" class="col-md-offset-2" name="serial_part"  id="serial_part1" value="Y" required <?php if($sel_result['serial_part']=="Y"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                <div style="display:inline-block; float:left;"><input type="radio" class="col-md-offset-2" name="serial_part"  id="serial_part2" value="N" required <?php if($sel_result['serial_part']=="N"){ echo "checked";} ?>></div><div style="display:inline-block; float:left;">&nbsp;No</div>
              </div>
            </div>
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
            <div class="col-md-6"><label class="col-md-6 control-label">&nbsp;</label>
                <div class="col-md-6">
              
            	</div>
            </div>
          </div>
            <div class="col-md-6"><label class="col-md-6 control-label">  </label>
              <div class="col-md-6">
                 
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit1" id="save1" value="Save" title="" <?php if($_POST['Submit1']=='Save'){?>disabled<?php }?>>&nbsp;
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['partcode'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='partcode_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
            </div>
          </div>
            </form>
            </div>
            <div id="menu1" class="tab-pane fade">
              <form  name="frm2" id="frm2" class="form-horizontal" action="" method="post">
                <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm2.mappartmodel)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm2.mappartmodel)" value="Uncheck All" />
                </div>
				<table width="100%" id="modelmap" class="table table-bordered table-hover">
                	<tbody>
                    <?php
					$map_model = explode(",",$sel_result['model_id']);
					$rs=mysqli_query($link1,"select model_id,model from model_master where status='1' and brand_id='".$sel_result['brand_id']."' and product_id='".$sel_result['product_id']."' order by model");
					$num=mysqli_num_rows($rs);
					if($num > 0){
                   		$j=1;
                   		while($row=mysqli_fetch_array($rs)){
							if($j%4==1){
					?>
                    	<tr>
                           <?php
                       		}
							///// check if any mapping entry with Y status is there
							//$res_map = mysqli_query($link1,"select id from map_partcode_model where partcode='".$sel_result['partcode']."' and model_id='".$row['model_id']."' and status='Y'")or die(mysqli_error());
                    		$num_map = in_array($row['model_id'], $map_model);
							?>
                          <td><input style="width:20px"  type="checkbox" id="mappartmodel" name="mappartmodel[]" value="<?=$row['model_id']?>" <?php if($num_map==1){ echo "checked";}?>/>&nbsp;<?=$row['model']?></td>
                           <?php 
						  	if($j/4==0){
							?>
                        </tr>
                    <?php
						  }
						$j++;
						}
					}
					?>    
                    </tbody>
                    <tfoot>
                    	<tr>
                          <td colspan="4" align="center">
                          <input type="submit" class="btn<?=$btncolor?>" name="Submit2" id="save2" value="Save" title="" <?php if($_POST['Submit2']=='Save'){?>disabled<?php }?>>
                          <input name="refid" id="refid" type="hidden" value="<?=base64_encode($sel_result['partcode'])?>"/>
                          <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='partcode_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'"></td>
                        </tr>
                    </tfoot>
                </table>
              </form>
            </div>
			<div id="menu2" class="tab-pane fade">
              <form  name="frm3" id="frm3" class="form-horizontal" action="" method="post">
                <div class="form-buttons" style="float:right">
                <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm3.mappartrepcode)" value="Check All" />
                <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm3.mappartrepcode)" value="Uncheck All" />
                </div>
				<table width="100%" id="repcodemap" class="table table-bordered table-hover">
                	<tbody>
                    <?php
					$map_repcode = explode(",",$sel_result['repair_code']);
					$rs=mysqli_query($link1,"select rep_code, rep_desc from repaircode_master where status='1' and brand_id='".$sel_result['brand_id']."' and product_id='".$sel_result['product_id']."' order by rep_desc");
					$num=mysqli_num_rows($rs);
					if($num > 0){
                   		$j=1;
                   		while($row=mysqli_fetch_array($rs)){
							if($j%4==1){
					?>
                    	<tr>
                           <?php
                       		}
							///// check if any mapping entry with Y status is there
							//$res_map = mysqli_query($link1,"select id from map_partcode_model where partcode='".$sel_result['partcode']."' and model_id='".$row['model_id']."' and status='Y'")or die(mysqli_error());
                    		$num_map = in_array($row['rep_code'], $map_repcode);
							?>
                          <td><input style="width:20px"  type="checkbox" id="mappartrepcode" name="mappartrepcode[]" value="<?=$row['rep_code']?>" <?php if($num_map==1){ echo "checked";}?>/>&nbsp;<?=$row['rep_desc']?></td>
                           <?php 
						  	if($j/4==0){
							?>
                        </tr>
                    <?php
						  }
						$j++;
						}
					}
					?>    
                    </tbody>
                    <tfoot>
                    	<tr>
                          <td colspan="4" align="center">
                          <input type="submit" class="btn<?=$btncolor?>" name="Submit3" id="save3" value="Save" title="" <?php if($_POST['Submit3']=='Save'){?>disabled<?php }?>>
                          <input name="refid" id="refid" type="hidden" value="<?=base64_encode($sel_result['partcode'])?>"/>
                          <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='partcode_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'"></td>
                        </tr>
                    </tfoot>
                </table>
              </form>
            </div>
            	<div id="menu3" class="tab-pane fade">
              <form  name="frm4" id="frm4" class="form-horizontal" action="" method="post">
             
				
				
				<table width="100%" class="table table-bordered table-hover">
				<tr><td><input type="text" name="part_search" class="form-control"  maxlength="30"  id="part_search" value="<?=$_REQUEST['part_search']?>" onKeyup="getmappartcode(this.value);" placeholder="Enter part name" width="200px"/>
           </td></tr>
                	<tbody>
                 <tr><td id="disp_pincode"></td></tr>
                    	<tr>
                          <td colspan="4" align="center">
                          <input type="submit" class="btn<?=$btncolor?>" name="Submit4" id="save4" value="Save" title="" <?php if($_POST['Submit4']=='Save'){?>disabled<?php }?>>
						   <input name="partcode" id="partcode" type="hidden" value="<?=$sel_result['partcode']?>"/>
                          <input name="refid" id="refid" type="hidden" value="<?=base64_encode($sel_result['partcode'])?>"/>
                          <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='partcode_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'"></td>
                        </tr>
                    </tbody>
                </table>
              </form>
            </div>
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