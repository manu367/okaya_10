<?php
require_once("../includes/config.php");
$today=date("Y-m-d");
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

$product=$_REQUEST['prod_code'];
$brand_array=$_REQUEST['brand'];
$model_array=$_REQUEST['model'];
$statusarray=$_REQUEST['status'];
$substatusarray=$_REQUEST['substatus'];

/////////////////////////// get model on basis of product and model //////////////////////////////////////////////////////
$arr_prodstr = $_REQUEST['prod_code'];
			for($i=0; $i<count($arr_prodstr); $i++){
				if($prodstr){
					$prodstr.="','".$arr_prodstr[$i];
				}else{
					$prodstr.= $arr_prodstr[$i];
				}
			}
			
$arr_brandstr = $_REQUEST['brand'];
			for($i=0; $i<count($arr_brandstr); $i++){
				if($brandstr){
					$brandstr.="','".$arr_brandstr[$i];
				}else{
					$brandstr.= $arr_brandstr[$i];
				}
			}	
		
////////////////////////

?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});

////// checkbox selection condition/////////////////////////////
////// checkbox selection condition/////////////////////////////
$(document).ready(function()
{
    $("#pending").change(function() {
        if ($(this).is(":checked")) {
                $("#pending").show();
				 $("#dt_range").hide();	
				 $("#st").hide();	
				 				
          } 
		  else
		  {
		  $("#dt_range").show();	
		   $("#st").show();	
		    $("#subst").show();		
		  }
       
    });

$(document).ready(function(){
	if($("#pending").is(":checked")){
		$("#dt_range").hide();	
		 $("#st").hide();	
		 $("#subst").hide();	
	}
});
});

$(document).ready(function() {
	$('#prod_code').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

$(document).ready(function() {
	$('#brand').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

$(document).ready(function() {
	$('#model').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});
  
$(document).ready(function() {
	$('#status').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

$(document).ready(function() {
	$('#substatus').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

</script>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<!-- Include multiselect -->
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-volume-control-phone"></i> All Call</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1"  id="form1" action="" method="get">
	   
	    <div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">All pending:</label>	  
			<div class="col-md-5" align="left">
			 <input type="checkbox" name="pending"  id="pending"   value="checked"  <?php if($_REQUEST['pending']){echo "checked";}?>> 
            </div>
          </div>
	    </div><!--close form group-->
		
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Product</label>	  
			<div class="col-md-6" >
				<select   name="prod_code[]" id="prod_code" class="form-control" multiple="multiple" onChange="document.form1.submit();">
				<?php
               $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";
			        $check1=mysqli_query($link1,$model_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['product_id']?>" <?php for($i=0; $i<count($product); $i++){ if($product[$i] == $br['product_id']) { echo 'selected'; }}?>><?=$br['product_name']." | ".$br['product_id']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Brand</label>	  
			<div class="col-md-5">
                 <select   name="brand[]" id="brand" class="form-control"  multiple="multiple" onChange="document.form1.submit();">
				<?php
                $brand = mysqli_query($link1,"SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand" );
                while($brandinfo = mysqli_fetch_assoc($brand)){?>
                <option value="<?=$brandinfo['brand_id']?>" <?php for($i=0; $i<count($brand_array); $i++){ if($brand_array[$i] == $brandinfo['brand_id']) { echo 'selected'; }}?>><?=$brandinfo['brand']." | ".$brandinfo['brand_id']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
	    </div>
		
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Model</label>	  
			<div class="col-md-6" >
				<select name="model[]" id="model" class="form-control" multiple="multiple" onChange="document.form1.submit();">
                <?php 
				 $model_query=mysqli_query($link1,"SELECT distinct(model_id),model FROM model_master where product_id in ('$prodstr')  and brand_id in ('$brandstr')" );
				  while($model_res = mysqli_fetch_assoc($model_query)){?>
                <option value="<?=$model_res['model_id']?>" <?php for($i=0; $i<count($model_array); $i++){if($model_array[$i] == $model_res['model_id']) { echo 'selected'; }}?>><?=$model_res['model']." | ".$model_res['model_id']?></option>
                <?php } ?>
				
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Status</label>	  
			<div class="col-md-5">
                  <select id="status"  name="status[]" class="form-control" multiple="multiple" onChange="document.form1.submit();">
				<?php
                $res_status = mysqli_query($link1,"select  status_id , main_status_id,system_status from jobstatus_master where (status_id = main_status_id )")or die(mysqli_error($link1)); 
                while($row_status = mysqli_fetch_assoc($res_status)){?>
                <option value="<?=$row_status['status_id']?>" <?php for($i=0; $i<count($statusarray); $i++){ if($statusarray[$i]==$row_status['status_id']){ echo "selected";}}?>><?=$row_status['system_status']?></option>
                <?php } ?>
			</select>
              </div>
          </div>
	    </div>
        <div class="form-group">
         <div class="col-md-6" id="st"><label class="col-md-5 control-label">Sub Status</label>	  
			<div class="col-md-6" align="left">
				<select id="substatus"  name="substatus[]" class="form-control" multiple="multiple" onChange="document.form1.submit();">

				<?php
                $res_substatus = mysqli_query($link1,"select  status_id , main_status_id,system_status from jobstatus_master where (status_id != main_status_id )")or die(mysqli_error($link1)); 
                while($row_substatus = mysqli_fetch_assoc($res_substatus)){?>
                <option value="<?=$row_substatus['status_id']?>" <?php for($i=0; $i<count($substatusarray); $i++){if($substatusarray[$i]==$row_substatus['status_id']){ echo "selected";}}?>><?=$row_substatus['system_status']?></option>
                <?php } ?>
			</select>
              </div>
          </div>
		  <div class="col-md-6" id="subst"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5 ">
			<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!"> 
                  
              </div>
          </div>
	    </div>
       <!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){	
	   //// array initialization to send by query string of  product
			$prostr = "";
			$arr_product = $_REQUEST['prod_code'];
			for($i=0; $i<count($arr_product); $i++){
				if($prostr){
					$prostr.="','".$arr_product[$i];
				}else{
					$prostr.= $arr_product[$i];
				}
			}	
			
			//// array initialization to send by query string of  brand
			$brandstr = "";
			$arr_brand = $_REQUEST['brand'];
			for($i=0; $i<count($arr_brand); $i++){
				if($brandstr){
					$brandstr.="','".$arr_brand[$i];
				}else{
					$brandstr.= $arr_brand[$i];
				}
			}		
			//// array initialization to send by query string of  model
			$modelstr = "";
			$arr_model = $_REQUEST['model'];
			for($i=0; $i<count($arr_model); $i++){
				if($modelstr){
					$modelstr.="','".$arr_model[$i];
				}else{
					$modelstr.= $arr_model[$i];
				}
			}	  
			
			//// array initialization to send by query string of  status
			$statusstr = "";
			$arr_status = $_REQUEST['status'];
			for($i=0; $i<count($arr_status); $i++){
				if($statusstr){
					$statusstr.="','".$arr_status[$i];
				}else{
					$statusstr.= $arr_status[$i];
				}
			}	
			
			//// array initialization to send by query string of  substatus
			$substatusstr = "";
			$arr_substatus = $_REQUEST['substatus'];
			for($i=0; $i<count($arr_substatus); $i++){
				if($substatusstr){
					$substatusstr.="','".$arr_substatus[$i];
				}else{
					$substatusstr.= $arr_substatus[$i];
				}
			}		  		  
	?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="../excelReports/allcallaspexcel.php?daterange=<?=$_REQUEST['daterange']?>&modelid=<?=base64_encode($modelstr);?>&status=<?=base64_encode($statusstr);?>&substatus=<?=base64_encode($substatusstr);?>&pending=<?=base64_encode($_REQUEST['pending'])?>&proid=<?=base64_encode($prostr);?>&brand=<?=base64_encode($brandstr);?>?>" title="Export All Call details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export All Call details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>