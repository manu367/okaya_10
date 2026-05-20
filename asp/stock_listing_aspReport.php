<?php
require_once("../includes/config.php");
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
$product=$_REQUEST['product_name'];
$brandarray=$_REQUEST['brand'];
$modelarray=$_REQUEST['modelid'];

/////////////////////////// get model on basis of product and model //////////////////////////////////////////////////////
$arr_prodstr = $_REQUEST['product_name'];
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
$arr_modelid = $_REQUEST['modelid'];
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
 <!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <!--  -->
  <style type="text/css">
	.modal-dialogTH{
		overflow-y: initial !important
	}
	.modal-bodyTH{
		max-height: calc(100vh - 212px);
		overflow-y: auto;
	}
	.modalTH {
	  width: 1000px;
	  margin: auto;
	}

</style>
 <script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});
$(document).ready(function() {
	$('#product_name').multiselect({
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
	$('#modelid').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

//////////////////////// function to get model on basis of model dropdown selection///////////////////////////
 function getmodel(){
	  var brand=$('#brand').val();
	  var product=$('#prod_code').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{brandinfo:brand,productinfo:product},
		success:function(data){
		 $('#modeldiv').html(data);
	    }
	  });
  }

 ////// paging script 
 brand = [];
<?php for($i=0; $i<count($arr_brandstr); $i++){ ?>
 brand[<?=$i?>] = '<?=$arr_brandstr[$i]?>';
<?php }?>

product = [];
<?php for($i=0; $i<count($arr_prodstr); $i++){ ?>
 product[<?=$i?>] = '<?=$arr_prodstr[$i]?>';
<?php }?>

model = [];
<?php for($i=0; $i<count($arr_modelid); $i++){ ?>
 model[<?=$i?>] = '<?=$arr_modelid[$i]?>';
<?php }?>
$(document).ready(function() {
	var dataTable = $('#joblist-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 0, "asc" ]],
		"ajax":{
			url :"../pagination/stock-list-data-asp.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "prod_code": product, "brand": brand, "location_code": "<?=$_SESSION['asc_code']?>", "model": model},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".job-grid-error").html("");
				$("#job-grid").append('<tbody class="job-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
				$("#job-grid_processing").css("display","none");
				
			}
		}
	} );
} );
////// function for open model to see the task history
function checkMappedModel(partid){
	$.get('mapped_model.php?partcode=' + partid, function(html){
		 $('#mappedModel .modal-body').html(html);
		 $('#mappedModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
</script>
<!-- Include multiselect -->
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
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
      <h2 align="center"><i class="fa fa-cubes"></i> Stock List</h2>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Product</label>	  
			<div class="col-md-6" align="left">
			 <select name="product_name[]" id="product_name" class="form-control" multiple="multiple" onChange="document.form1.submit();">
                <?php
				$dept_query="SELECT * FROM product_master where status = '1' and product_id in (".$access_product.") order by product_name";
				$check_dept=mysqli_query($link1,$dept_query);
				while($br_dept = mysqli_fetch_array($check_dept)){
			    ?>
                <option value="<?=$br_dept['product_id']?>"<?php for($i=0; $i<count($product); $i++){ if($product[$i] == $br_dept['product_id']) { echo 'selected'; }}?>><?php echo $br_dept['product_name']?></option>
                <?php }?>
              </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label">Brand</label>	  
			<div class="col-md-6" align="left">
			 <select name="brand[]" id="brand" class="form-control" multiple="multiple" onChange="document.form1.submit();">
                <?php
				$dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";
				$check_dept=mysqli_query($link1,$dept_query);
				while($brandinfo = mysqli_fetch_array($check_dept)){
			    ?>
                <option value="<?=$brandinfo['brand_id']?>"<?php for($i=0; $i<count($brandarray); $i++){if($brandarray[$i] == $brandinfo['brand_id']) { echo 'selected'; }}?>><?php echo $brandinfo['brand']?></option>
                <?php }?>
              </select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Model</label>	  
				<div class="col-md-6" id="modeldiv">
			<select name="modelid[]" id="modelid" class="form-control" multiple="multiple" onChange="document.form1.submit();">
                <?php 
				 $model_query=mysqli_query($link1,"SELECT distinct(model_id),model FROM model_master where product_id in ('$prodstr')  and brand_id in ('$brandstr')" );
				  while($model_res = mysqli_fetch_assoc($model_query)){?>
                <option value="<?=$model_res['model_id']?>" <?php for($i=0; $i<count($modelarray); $i++){if($modelarray[$i] == $model_res['model_id']) { echo 'selected'; }}?>><?=$model_res['model']." | ".$model_res['model_id']?></option>
           <?php } ?></select>
				</div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6">
           
                	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               		<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               		<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
  
            </div>
          </div>
	    </div>
        <!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){
	   //// array initialization to send by query string of  product
			$prostr = "";
			$arr_product = $_REQUEST['product_name'];
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
			$arr_model = $_REQUEST['modelid'];
			for($i=0; $i<count($arr_model); $i++){
				if($modelstr){
					$modelstr.="','".$arr_model[$i];
				}else{
					$modelstr.= $arr_model[$i];
				}
			}	  
	  
	   
	   
	   ?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="../excelReports/stocklistaspexcel.php?location_code=<?=$_SESSION['asc_code']?>&prod_code=<?=base64_encode($prostr);?>&brand=<?=base64_encode($brandstr);?>&model=<?=base64_encode($modelstr);?>" title="Export Login Logout details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Login Logout details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
        <form class="form-horizontal" role="form" name="form2">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="joblist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr>
              <th>S.No</th>
              <th>Partcode</th>
              <th>Description</th>
              <th>Product</th>
              <th>Brand</th>
              <th>Mapped Model</th>
              <th>Available Stock</th>
               <th>Offer Price</th>
              
            </tr>
          </thead>
          </table>
          </div>
      <!--</div>-->
      </form>
      <!-- Start Model Mapped Modal -->
          <div class="modal modalTH fade" id="mappedModel" role="dialog">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center">Model Details</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div><!--close Model Mapped modal-->
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>