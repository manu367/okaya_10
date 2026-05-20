<?php
require_once("../includes/config.php");
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

// $product=$_REQUEST['prod_code'];
// $brandarray=$_REQUEST['brand'];
// $modelarray=$_REQUEST['model'];

/////////////////////////// get model on basis of product and model //////////////////////////////////////////////////////
// $arr_prodstr = $_REQUEST['prod_code'];
// 			for($i=0; $i<count($arr_prodstr); $i++){
// 				if($prodstr){
// 					$prodstr.="','".$arr_prodstr[$i];
// 				}else{
// 					$prodstr.= $arr_prodstr[$i];
// 				}
// 			}
			
// $arr_brandstr = $_REQUEST['brand'];
// 			for($i=0; $i<count($arr_brandstr); $i++){
// 				if($brandstr){
// 					$brandstr.="','".$arr_brandstr[$i];
// 				}else{
// 					$brandstr.= $arr_brandstr[$i];
// 				}
// 			}		
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

// $(document).ready(function() {
// 	$('#prod_code').multiselect({
// 			includeSelectAllOption: true,
// 			buttonWidth:"200"
   
// 	});
// });

// $(document).ready(function() {
// 	$('#brand').multiselect({
// 			includeSelectAllOption: true,
// 			buttonWidth:"200"
   
// 	});
// });

// $(document).ready(function() {
// 	$('#model').multiselect({
// 			includeSelectAllOption: true,
// 			buttonWidth:"200"
   
// 	});
// });


  
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
      <h2 align="center"><i class="fa fa-tag"></i> Brand Dispatch Report</h2>
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
		 
		</div><!--close form group-->
		
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-success" style="margin-left:35%;" value="GO"  title="Go!"> 
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
               
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){	
	    //// array initialization to send by query string of  product
			// $prostr = "";
			// $arr_product = $_REQUEST['prod_code'];
			// for($i=0; $i<count($arr_product); $i++){
			// 	if($prostr){
			// 		$prostr.="','".$arr_product[$i];
			// 	}else{
			// 		$prostr.= $arr_product[$i];
			// 	}
			// }	
			
			// //// array initialization to send by query string of  brand
			// $brandstr = "";
			// $arr_brand = $_REQUEST['brand'];
			// for($i=0; $i<count($arr_brand); $i++){
			// 	if($brandstr){
			// 		$brandstr.="','".$arr_brand[$i];
			// 	}else{
			// 		$brandstr.= $arr_brand[$i];
			// 	}
			// }		
			// //// array initialization to send by query string of  model
			// $modelstr = "";
			// $arr_model = $_REQUEST['model'];
			// for($i=0; $i<count($arr_model); $i++){
			// 	if($modelstr){
			// 		$modelstr.="','".$arr_model[$i];
			// 	}else{
			// 		$modelstr.= $arr_model[$i];
			// 	}
			// }	  	  		  	  		  
	?>
        <div class="form-group">
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
           <span>Partwise Sale Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/branddispatchwh_report.php?daterange=<?=$_REQUEST['daterange']?>" title="Export Brand Dispatch Sale Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Brand Dispatch Sale Report details in excel"></i></a>
		   </div>
		   </div>
		   <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-6">             
		   <span>Detailed Sale Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/brand_saledetailwh_report.php?daterange=<?=$_REQUEST['daterange']?>" title="Export Brand Sale Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export BrandSale Report details in excel"></i></a>
            </div>
          </div>
		   

		   <!-- <div class="col-md-6" align="left">
           <span>Partwise Sale Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/salepartwisewh_report.php?daterange=<?=$_REQUEST['daterange']?>&prod_code=<?=base64_encode($prostr);?>&brand=<?=base64_encode($brandstr);?>&model=<?=base64_encode($modelstr);?>&to_loc=<?=base64_encode($_REQUEST['location_code'])?>" title="Export Sale Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Sale Report details in excel"></i></a>
		   </div> -->
          </div>
		  
		   <!-- <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">             
		   <span>Detailed Sale Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/saledetailwh_report.php?daterange=<?=$_REQUEST['daterange']?>&prod_code=<?=base64_encode($prostr);?>&brand=<?=base64_encode($brandstr);?>&model=<?=base64_encode($modelstr);?>&to_loc=<?=base64_encode($_REQUEST['location_code'])?>" title="Export Sale Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Sale Report details in excel"></i></a>
            </div>
          </div> -->
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