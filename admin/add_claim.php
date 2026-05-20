<?php
require_once("../includes/config.php");
/////get status//
$arrstatus = getFullStatus("master",$link1);
$getid = base64_decode($_REQUEST['refid']);
@extract($_POST);

if($_POST){
	//// initialize transaction parameters
	$flag = true;
    mysqli_autocommit($link1, false);
    $error_msg="";
    if ($_POST['add']=='ADD'){
    ///////// insert model data	   
	$modelsplit = explode("~",$modelid);
    $usr_add = "INSERT INTO claim_price set product_id ='".$product_name."', brand_id ='".$brand_name."', status='1',eff_date='".date("Y-m-d H:i:s")."',eff_by='".$_SESSION['userid']."', loc_iw_inst = '".$loc_iw_inst."', loc_iw_npu = '".$loc_iw_npu."', loc_iw_pu = '".$loc_iw_pu."',  gas_iw_pu = '".$gas_iw_pu."', gas_iw_npu = '".$gas_iw_npu."' ";
	
    $res_add=mysqli_query($link1,$usr_add);
	//// check if query is not executed
	if (!$res_add) {
		 $flag = false;
		 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
	}
	$insid = mysqli_insert_id($link1);
   
	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$insid,"CLAIM PRICE","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	////// return message
	$msg="You have successfully created a Claim Price ";
	$cflag="success";
	$cmsg="Success";
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
    header("location:claim_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-gears"></i> <?=$_REQUEST['op']?> Claim</h2><br/><br/>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
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
			<div class="col-md-4"><label class="col-md-4 control-label"> </label>
              <div class="col-md-8" >
                
              </div>
            </div>
          </div>
		  		  
		  <div class="form-group">
			  <div class="col-md-12">
			  <div class="panel-body">
				  <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1" style="margin-bottom:0px;">
				  
				    <tr>
						 
						  <td style="text-align:center;"><label class="control-label"> Installation</label></td>
						<td><input class="form-control number" type="text" name="loc_iw_inst" id="loc_iw_inst" value="0.00" /></td>
						  <td style="text-align:center;"><label class="control-label"> Repairs Without Part </label></td>
						     <td><input class="form-control number" type="text" name="loc_iw_npu" id="loc_iw_npu" value="0.00" /></td> 
					  </tr>
				  
				     <tr>
						 
						  <td style="text-align:center;"><label class="control-label">  Repairs With Parts</label></td>
						<td><input class="form-control number" type="text" name="loc_iw_pu" id="loc_iw_pu" value="0.00" /></td>  
						 <td style="text-align:center;"><label class="control-label">&nbsp;</label></td>
						  <td style="text-align:center;"><label class="control-label"> &nbsp;</label></td>
						
						 
					  </tr>
				  
		
					 
					  <tr>
						  <td rowspan="4" style="text-align:center;vertical-align: middle;"><label class="control-label"> Gas Charging </label></td>
						 
						  <td style="text-align:center;"><label class="control-label"> Gas Charging Without Part   </label></td>
						  <td style="text-align:center;"><label class="control-label"> Gas Charging With Part</label></td>
					  </tr>
					  <tr style="background-color:#d9edf7;">
						
						  <td><input class="form-control number" type="text" name="gas_iw_npu" id="gas_iw_npu" /></td> 
						  <td><input class="form-control number" type="text" name="gas_iw_pu" id="gas_iw_pu" /></td>  
					  </tr>
				
				  </table>
				 
			  </div>	  
			  </div>
		  </div>
		 
		  <br /><br />
          <div class="form-group">
            <div class="col-md-12" style="text-align:center;" >
              <input type="submit" class="btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New Price">            
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='claim_master.php?status=<?=$_REQUEST['status']?><?=$pagenav?>'">
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