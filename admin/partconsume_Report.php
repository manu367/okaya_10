<?php
require_once("../includes/config.php");
$today=date("Y-m-d");
$statename = $_REQUEST['statename'];
$locationname=$_REQUEST['locationname'];
$product=$_REQUEST['prod_code'];
$brandarray=$_REQUEST['brand'];
$modelarray=$_REQUEST['model'];
$arrstate = getAccessState($_SESSION['userid'],$link1);
$arr_statestr = $_REQUEST['statename'];
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
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
    include("../includes/leftnav2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-snowflake-o"></i>Part Consume</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1"  id="form1" action="" method="get">
          <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Date Type</label>	  
			<div class="col-md-5" align="left">
			  <select id="typedate"  name="typedate" class="form-control">
			   <option value="close_date" <?php if($_REQUEST['typedate'] == "close_date") { echo 'selected'; }?>>Close Date</option>
			   <option value="handover_date" <?php if($_REQUEST['typedate'] == "handover_date"){ echo 'selected'; }?>>Handover Date</option>				
			</select>
            </div>
          </div>
	    </div><!--close form group-->
      <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">State<span style="color:#F00">*</span></label>	  
		<div class="col-md-6" >
			<select name="statename" id="statename" class="form-control"
                    onChange="document.form1.submit();">
                <option value="ALL"<?php if($_REQUEST['statename']=="ALL") { echo 'selected'; }?>>All</option>
				<?php 
                $state = mysqli_query($link1,"select stateid, state from state_master  where stateid in (".$arrstate.") order by state" ); 
                while($stateinfo = mysqli_fetch_assoc($state)){ 
				?>		
             	<option value="<?=$stateinfo['stateid']?>" <?php if($_REQUEST['statename']==$stateinfo['stateid']) { echo 'selected'; }?>><?=$stateinfo['state']?></option>
                <?php }?>
			</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Location</label>	  
			<div class="col-md-5" id="citydiv">
                   <select name="locationname" id="locationname" class="form-control"  onChange="document.form1.submit();">
                  <option value=""<?php if($_REQUEST['locationname']=="") { echo 'selected'; }?>>All</option>
				  <?php
				    if($_REQUEST['statename']!=""){ $selstate = "stateid = '".$_REQUEST['statename']."'";}else{ $selstate = "stateid in (".$arrstate.")";}
				   	$location_query="SELECT locationname, location_code FROM location_master where ".$selstate." and locationtype != 'WH'  order by locationname ";
     				$loc_res=mysqli_query($link1,$location_query);
     				while($loc_info = mysqli_fetch_array($loc_res)){?>
				  <option value="<?=$loc_info['location_code']?>" <?php if($_REQUEST['locationname'] == $loc_info['location_code']) { echo 'selected'; }?>><?=$loc_info['locationname']?></option>
					<?php }  ?>
                 </select>
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Product</label>	  
			<div class="col-md-5" id="location">
                    <select name="prod_code[]"
                            id="prod_code"
                            multiple="multiple"
                            class="form-control"
                            onChange="document.form1.submit();"
                           >
				<?php
               $model_query="select product_id,product_name from product_master where status='1' order by product_name";
			        $check1=mysqli_query($link1,$model_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['product_id']?>" <?php for($i=0; $i<count($product); $i++){ if($product[$i] == $br['product_id']) { echo 'selected'; }}?>><?=$br['product_name']." | ".$br['product_id']?></option>
                <?php } ?>
	</select>
            </div>
          </div>
		  <div class="col-md-6">
              <label class="col-md-5 control-label">Brand</label>
			<div class="col-md-5" >
              <select   name="brand[]" id="brand" class="form-control"  multiple="multiple" onChange="document.form1.submit();">
				<?php
                $brand = mysqli_query($link1,"SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand" );
                while($brandinfo = mysqli_fetch_assoc($brand)){?>
                <option value="<?=$brandinfo['brand_id']?>" <?php for($i=0; $i<count($brandarray); $i++){if($brandarray[$i] == $brandinfo['brand_id']) { echo 'selected'; }}?>><?=$brandinfo['brand']." | ".$brandinfo['brand_id']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
	    </div>
<!--          dummy group here-->
<!--          <div class="form-group">-->
<!--              <div class="col-md-6">-->
<!--                  <label class="col-md-5 control-label">Product_1</label>-->
<!--                  <div class="col-md-5" id="location_alternative">-->
<!--                      <select name="prod_code_1[]"-->
<!--                              id="prod_code_1"-->
<!--                              multiple="multiple"-->
<!--                              class="form-control">-->
<!--                          <option>--a--</option>-->
<!--                          <option>--a--</option>-->
<!--                      </select>-->
<!--                      <script>-->
<!--                          function mutliselect(id){-->
<!--                              const target=document.getElementById(id);-->
<!--                              -->
<!--                              target.style.width = '200px';-->
<!--                              target.style.height = '30px';-->
<!--                              console.log(target);-->
<!--                              return function(){-->
<!--                              }-->
<!--                          }-->
<!--                          mutliselect("prod_code_1");-->
<!--                      </script>-->
<!--                  </div>-->
<!--              </div>-->
<!--              <div class="col-md-6">-->
<!--              </div>-->
<!--          </div>-->
<!--          end here-->
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Model</label>
		 <div class="col-md-5" >	  
			 <select name="model[]" id="model" class="form-control" multiple="multiple" onChange="document.form1.submit();">
                <?php 
				 $model_query=mysqli_query($link1,"SELECT distinct(model_id),model FROM model_master where product_id in ('$prodstr')  and brand_id in ('$brandstr')" );
				  while($model_res = mysqli_fetch_assoc($model_query)){?>
                <option value="<?=$model_res['model_id']?>" <?php for($i=0; $i<count($modelarray); $i++){if($modelarray[$i] == $model_res['model_id']) { echo 'selected'; }}?>><?=$model_res['model']." | ".$model_res['model_id']?></option>
                <?php } ?>
             </select>
          </div>
		  </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-6" id="modeldiv">
				<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">
              </div>
          </div>
	    </div>
	  </form>
       <?php if ($_REQUEST['Submit']){
	   ////
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
	?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
			<?php if ($_REQUEST['statename'] == '') {?>		
			<?php  }else {?>
               <a  href="../excelReports/partconsumeexcel.php?daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['locationname'])?>&model=<?=base64_encode($modelstr);?>&state=<?=base64_encode($arr_statestr);?>&typedate=<?=$_REQUEST['typedate']?>&proid=<?=base64_encode($prostr);?>&brand=<?=base64_encode($brandstr);?>" title="Export Part Consume details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Part Consume details in excel"></i></a>
            <?php }?>
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