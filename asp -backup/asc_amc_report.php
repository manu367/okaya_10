<?php
require_once("../includes/config.php");
$today=date("Y-m-d");
$statename = $_REQUEST['statename'];
$locationname=$_REQUEST['locationname'];
$product=$_REQUEST['prod_code'];
$brandarray=$_REQUEST['brand'];
$modelarray=$_REQUEST['model'];
$statusarray=$_REQUEST['status'];
	
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);
$access_product = getAccessProduct($_SESSION['userid'],$link1);	
$access_brand = getAccessBrand($_SESSION['userid'],$link1);


////////////////////////// get city  and location /////////////////////////////////////
if($_REQUEST['statename'] ==''){
	$statestr="";
	}
else{
	$statestr=" stateid='".$_REQUEST['statename']."' ";
	}
	
	
	
if($_REQUEST['prod_code'] == '' && $_REQUEST['brand'] == '' ){
	$prodstr= "";
	}
else if($_REQUEST['prod_code'] == '' && $_REQUEST['brand'] != '' ){
	$prodstr= "brand_id ='".$_REQUEST['brand']."'";
	}
	else if($_REQUEST['prod_code'] != '' && $_REQUEST['brand'] == ''){
	$prodstr= "product_id ='".$_REQUEST['prod_code']."'";
	}
else{
	$brdstr= " product_id ='".$_REQUEST['prod_code']."' and brand_id ='".$_REQUEST['brand']."'";
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
 <script type="text/javascript">
    $(document).ready(function() {
        $("#form1").validate();
    });
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
 <script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
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
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-volume-control-phone"></i> AMC Call</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" id="form1" name="form1" action="" method="post">  
	    <div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6">
			<div class="col-md-5" align="left">
			 
            </div>
          </div>
	    </div><!--close form group-->
		
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Product</label>	  
			<div class="col-md-5" id="location">
             
                   <select   name="prod_code" id="prod_code" class="form-control" onChange="document.form1.submit();">
                   <option value="">All</option>
				<?php
               $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";
			        $check1=mysqli_query($link1,$model_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['product_id']?>" <?php if($_REQUEST['prod_code'] == $br['product_id']) { echo 'selected'; }?>><?=$br['product_name']." | ".$br['product_id']?></option>
                <?php } ?>
	</select>
              </div>
              </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Brand</label>	  
			<div class="col-md-5" >
               <select   name="brand" id="brand" class="form-control"  onChange="document.form1.submit();">
                <option value="">All</option>
				<?php
                $brand = mysqli_query($link1,"SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand" );
                while($brandinfo = mysqli_fetch_assoc($brand)){?>
                <option value="<?=$brandinfo['brand_id']?>" <?php if($_REQUEST['brand'] == $brandinfo['brand_id']) { echo 'selected'; }?>><?=$brandinfo['brand']." | ".$brandinfo['brand_id']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
	    </div>
		
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Model</label>
		 <div class="col-md-5" >	  
			 <select name="model" id="model" class="form-control"  onChange="document.form1.submit();">
              <option value="">All</option>
                <?php
				 $model_query=mysqli_query($link1,"SELECT distinct(model_id),model FROM model_master where $prodstr  $brdstr" );
				  while($model_res = mysqli_fetch_assoc($model_query)){?>
                <option value="<?=$model_res['model_id']?>" <?php if($_REQUEST['model'] == $model_res['model_id']) { echo 'selected'; }?>><?=$model_res['model']." | ".$model_res['model_id']?></option>
                <?php } ?>
				
                 </select>
          </div>
		  </div>
		 
		  <div class="col-md-6" ><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5 ">
				<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">                 
              </div>
          </div>
	    </div>
	  </form>
       <?php if ($_REQUEST['Submit']){?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
			
               <a href="../excelReports/allamc_asc_report.php?daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['locationname']);?>&modelid=<?=base64_encode($_REQUEST['model']);?>&state=<?=base64_encode($_REQUEST['statename']);?>&proid=<?=base64_encode($_REQUEST['prod_code']);?>&brand=<?=base64_encode($_REQUEST['brand']);?>" title="Export All Call details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export All Call details in excel"></i></a>
			  
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