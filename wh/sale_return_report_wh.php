<?php
require_once("../includes/config.php");
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

$product=$_REQUEST['prod_code'];
$brandarray=$_REQUEST['brand'];
$modelarray=$_REQUEST['model'];

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
      <h2 align="center"><i class="fa fa-pencil-square-o"></i>Sale Return Report</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   
	    <div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Product</label>	  
			<div class="col-md-5" align="left">
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
	    </div><!--close form group-->
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Brand</label>	  
			<div class="col-md-6" >
				<select   name="brand[]" id="brand" class="form-control"  multiple="multiple" onChange="document.form1.submit();">
				<?php
                $brand = mysqli_query($link1,"SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand" );
                while($brandinfo = mysqli_fetch_assoc($brand)){?>
                <option value="<?=$brandinfo['brand_id']?>" <?php for($i=0; $i<count($brandarray); $i++){if($brandarray[$i] == $brandinfo['brand_id']) { echo 'selected'; }}?>><?=$brandinfo['brand']." | ".$brandinfo['brand_id']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Model</label>	  
			<div class="col-md-5" id="modeldiv">
                 <select name="model[]" id="model" class="form-control" multiple="multiple" onChange="document.form1.submit();">
                <?php 
				 $model_query=mysqli_query($link1,"SELECT distinct(model_id),model FROM model_master where product_id in ('$prodstr')  and brand_id in ('$brandstr')" );
				  while($model_res = mysqli_fetch_assoc($model_query)){?>
                <option value="<?=$model_res['model_id']?>" <?php for($i=0; $i<count($modelarray); $i++){if($modelarray[$i] == $model_res['model_id']) { echo 'selected'; }}?>><?=$model_res['model']." | ".$model_res['model_id']?></option>
                <?php } ?>
				
                 </select>
              </div>
          </div>
	    </div>
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!"> 
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
               
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){		  		  
	?>
        <div class="form-group">
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
           <span>Partwise Sale Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/salesrnpartwise_report.php?daterange=<?=$_REQUEST['daterange']?>&prod_code=<?=base64_encode($prostr);?>&brand=<?=base64_encode($brandstr);?>&model=<?=base64_encode($modelstr);?>" title="Export Sale Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Sale Report details in excel"></i></a>
		   </div>
          </div>
		   <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">             
		   <span>Detailed Sale Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/salesrndetail_report.php?daterange=<?=$_REQUEST['daterange']?>&prod_code=<?=base64_encode($prostr);?>&brand=<?=base64_encode($brandstr);?>&model=<?=base64_encode($modelstr);?>" title="Export Sale Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Sale Report details in excel"></i></a>
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