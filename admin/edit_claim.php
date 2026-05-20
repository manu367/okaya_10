<?php
require_once("../includes/config.php");
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
		// update all details of partcode //
		$modelsplit = explode("~",$modelid);
		$usr_upd = "UPDATE claim_price set product_id ='".$product_name."', brand_id ='".$brand_name."', model_id='".$modelsplit[0]."', status='1',eff_date='".date("Y-m-d H:i:s")."',eff_by='".$_SESSION['userid']."', loc_iw_inst = '".$loc_iw_inst."', loc_iw_npu = '".$loc_iw_npu."', loc_iw_pu = '".$loc_iw_pu."',  gas_iw_pu = '".$gas_iw_pu."', gas_iw_npu = '".$gas_iw_npu."'  where sno = '".$getid."' ";
		
		//echo $usr_upd;
		
		$res_upd = mysqli_query($link1,$usr_upd);
		//// check if query is not executed
		if (!$res_upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
   		//////////////////////////////////////////////////////////////
   		////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$getid,"CLAIM PRICE","UPDATE",$ip,$link1,$flag);
		////// return message
		$msg="You have successfully updated Claim Price details for ".$level_price;
		$cflag="success";
		$cmsg="Success";
	}////close 1st tab
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
	//mysqli_close($link1);
	///// move to parent page
    header("location:claim_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
}
////// get details of selected partcode////
$sel_usr="select * from claim_price where sno='".$getid."'";
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
  /////////// function to get model on the basis of brand
	function getMod(){
	  var brandid=$('#brand_name').val();
	  var product_name=document.getElementById("product_name").value;
	  $.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{brandModel:brandid,product_id:product_name},
		success:function(data){
		$('#modeldiv').html(data);
		}
	  });
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
      <h2 align="center"><i class="fa fa-gears"></i> View/Edit Claim</h2>
      <h4 align="center">
      <?php if($_POST['Submit1']=='Save' ){ ?>
      <br/>

      <span style="color:#FF0000"><?php echo $msg; ?></span>
      <?php } ?>
      </h4>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
      	
    	  <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
              <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                  <div class="form-group">
            <div class="col-md-4"><label class="col-md-4 control-label">Product Name <span class="red_small">*</span></label>
                <div class="col-md-8">
               	<select name="product_name" id="product_name" class="form-control required" onChange="getMod();" required>
                  <option value=""> -- Please Select -- </option>
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
            <div class="col-md-4"><label class="col-md-4 control-label">Brand <span class="red_small">*</span></label>
              <div class="col-md-8">
                 <select name="brand_name" id="brand_name" class="form-control required"  required>
                  <option value=""> -- Please Select -- </option>
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
			<div class="col-md-4"><label class="col-md-4 control-label"></label>
               <div class="col-md-8" id="modeldiv">
				
              </div>
            </div>
          </div>
		  
		  <div class="form-group">
			  <div class="col-md-12">
			  <div class="panel-body">
				   <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1" style="margin-bottom:0px;">
				  
				    <tr>
						 
						  <td style="text-align:center;"><label class="control-label"> Installation</label></td>
						<td><input class="form-control number" type="text" name="loc_iw_inst" id="loc_iw_inst" value="<?=$sel_result['loc_iw_inst']?>" /></td>
						  <td style="text-align:center;"><label class="control-label"> Repairs Without Part </label></td>
						     <td><input class="form-control number" type="text" name="loc_iw_npu" id="loc_iw_npu" value="<?=$sel_result['loc_iw_npu']?>" /></td> 
					  </tr>
				  
				     <tr>
						 
						  <td style="text-align:center;"><label class="control-label">  Repairs With Parts</label></td>
						<td><input class="form-control number" type="text" name="loc_iw_pu" id="loc_iw_pu" value="<?=$sel_result['loc_iw_pu']?>"/></td>  
						 <td style="text-align:center;"><label class="control-label">&nbsp;</label></td>
						  <td style="text-align:center;"><label class="control-label"> &nbsp;</label></td>
						
						 
					  </tr>
				  
		
					 
					  <tr>
						  <td rowspan="4" style="text-align:center;vertical-align: middle;"><label class="control-label"> Gas Charging </label></td>
						 
						  <td style="text-align:center;"><label class="control-label"> Gas Charging Without Part   </label></td>
						  <td style="text-align:center;"><label class="control-label"> Gas Charging With Part</label></td>
					  </tr>
					  <tr style="background-color:#d9edf7;">
						
						  <td><input class="form-control number" type="text" name="gas_iw_npu" id="gas_iw_npu"  value="<?=$sel_result['gas_iw_npu']?>"/></td> 
						  <td><input class="form-control number" type="text" name="gas_iw_pu" id="gas_iw_pu"  value="<?=$sel_result['gas_iw_pu']?>" /></td>  
					  </tr>
				
				  </table>
			  </div>	  
			  </div>
		  </div>
		 
		  <br /><br />		  		  
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit1" id="save1" value="Save" title="" <?php if($_POST['Submit1']=='Save'){?>disabled<?php }?>>&nbsp;
              <input type="hidden" name="refid"  id="refid" value="<?=base64_encode($sel_result['sno'])?>" />
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='claim_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
            </div>
          </div>
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