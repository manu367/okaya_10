<?php
require_once("../includes/config.php");
$frmstatenew = $_REQUEST['frm_state'];
$frmlocnew = $_REQUEST['frm_loc']; 
$tostatenew = $_REQUEST['to_state'];
$tolocnew = $_REQUEST['to_loc']; 
$product=$_REQUEST['prod_code'];
$brandarray=$_REQUEST['brand'];
$modelarray=$_REQUEST['model'];

//// get access brand /////
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);

////////////////////  array initialization to make querry string used to get from location/////////////////////////////////////////
$arr_statestr = $_REQUEST['frm_state'];
			for($i=0; $i<count($arr_statestr); $i++){
				if($statestr){
					$statestr.="','".$arr_statestr[$i];
				}else{
					$statestr.= $arr_statestr[$i];
				}
			}

///////////////////////////////// get product and brand on basis of location////////////////////////////////////////									
$locstr=$_REQUEST['frm_loc'];
$count=count($locstr);
////get access product details
for($i=0; $i<count($locstr); $i++){
				if($str){
					$str.="','".$locstr[$i];
				}else{
					$str.= $locstr[$i];
				}
$access_product = getAccessProduct($str,$link1);				
			}				
////get access brand details
for($i=0; $i<count($locstr); $i++){
				if($name){
					$name.="','".$locstr[$i];
				}else{
					$name.= $locstr[$i];
				}
//$access_brand = getAccessBrand($name,$link1);
}

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
	$('#frm_state').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

$(document).ready(function() {
	$('#frm_loc').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

$(document).ready(function() {
	$('#to_state').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

$(document).ready(function() {
	$('#to_loc').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
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
      <h2 align="center"><i class="fa fa-pencil-square-o"></i> Sale Report</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1"  id="form1" action="" method="post">
	   
	    <div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
			
            </div>
          </div>
	    </div><!--close form group-->
		
		
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">From State<span style="color:#F00">*</span></label>	  
			<div class="col-md-6" >
				<select   name="frm_state[]" id="frm_state" multiple="multiple" class="form-control required"  onChange="document.form1.submit();" required>
				<?php 
                $state = mysqli_query($link1,"select stateid, state from state_master  where stateid in ($arrstate)" ); 
                while($stateinfo = mysqli_fetch_assoc($state)){ 
				?>		
             <option value="<?=$stateinfo['stateid']?>" <?php for($i=0; $i<count($frmstatenew); $i++){if($frmstatenew[$i]==$stateinfo['stateid']) { echo 'selected'; } }?>><?=$stateinfo['state']?></option>
                <?php }?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">From Location <span style="color:#F00">*</span></label>	  
			<div class="col-md-5" id="branddiv">
                  <select name="frm_loc[]" id="frm_loc" class="form-control required" multiple="multiple" onChange="document.form1.submit();" required>
                <?php     
				   $location_query="SELECT locationname, location_code FROM location_master where stateid in('$statestr')   ";
     $loc_res=mysqli_query($link1,$location_query);
     while($loc_info = mysqli_fetch_array($loc_res)){?>
				  <option value="<?=$loc_info['location_code']?>" <?php for($i=0; $i<count($frmlocnew); $i++){if($frmlocnew[$i] == $loc_info['location_code']) { echo 'selected'; }}?>><?=$loc_info['locationname']?></option>
				<?php }  ?>
                 </select>
              </div>
          </div>
	    </div>
		
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">To State</label>	  
			<div class="col-md-6" >
				<select   name="to_state[]" id="to_state" class="form-control" multiple="multiple" onChange="document.form1.submit();">
				 <?php
               $tostate="select to_stateid from billing_master where to_stateid not in('0') group by to_stateid";
			        $check1=mysqli_query($link1,$tostate);
                while($br = mysqli_fetch_array($check1)){
				$res = mysqli_fetch_array(mysqli_query($link1,"select stateid , state from state_master where stateid = '".$br['to_stateid']."' "));
				?>
                <option value="<?=$res['stateid']?>" <?php  for($i=0; $i<count($tostatenew); $i++){ if($tostatenew[$i] == $res['stateid']) { echo 'selected'; } }?>><?=$res['state']?></option>
                <?php } ?>
			</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">To Location</label>	  
			<div class="col-md-5">
                  <select name="to_loc[]" id="to_loc" class="form-control" multiple="multiple" onChange="document.form1.submit();">
                 <?php
               $toloc="select to_location from billing_master  group by to_location";
			     $check1=mysqli_query($link1,$toloc);
                while($br = mysqli_fetch_array($check1)){
				 $sql = mysqli_query($link1,"select locationname , location_code from location_master where location_code = '".$br['to_location']."' ");
				 if(mysqli_num_rows($sql)>0){
				 $res = mysqli_fetch_array($sql);
				?>
                <option value="<?=$res['location_code']?>" <?php for($i=0; $i<count($tolocnew); $i++){ if($tolocnew[$i] == $res['location_code']) { echo 'selected'; } }?>><?=$res['locationname']." | ".$res['location_code']?></option> <?php } else {?><option value="<?=$br['to_location']?>" <?php for($i=0; $i<count($tolocnew); $i++){ if($tolocnew[$i] == $br['to_location']) { echo 'selected'; } }?>><?=$br['to_location']?></option>
				
				<?php }}?>
                 </select>
              </div>
          </div>
	    </div>  
		<div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Product</label>	  
			<div class="col-md-6 ">
			 <select   name="prod_code[]" id="prod_code"  multiple="multiple" class="form-control" onChange="document.form1.submit();">
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
                <option value="<?=$brandinfo['brand_id']?>" <?php for($i=0; $i<count($brandarray); $i++){if($brandarray[$i] == $brandinfo['brand_id']) { echo 'selected'; }}?>><?=$brandinfo['brand']." | ".$brandinfo['brand_id']?></option>
                <?php } ?>
	</select>
			
            </div>
          </div>
	    </div><!--close form group--> 
		 <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Model</label>	  
			<div class="col-md-6" >
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
			<div class="col-md-5">
                  
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
			
			 //// array initialization to send by query string of  from state
			$fromstatestr = "";
			$arr_fromstate = $_REQUEST['frm_state'];
			for($i=0; $i<count($arr_fromstate); $i++){
				if($fromstatestr){
					$fromstatestr.="','".$arr_fromstate[$i];
				}else{
					$fromstatestr.= $arr_fromstate[$i];
				}
			}					
			//// array initialization to send by query string of  to state
			$tostatestr = "";
			$arr_tostate = $_REQUEST['to_state'];
			for($i=0; $i<count($arr_tostate); $i++){
				if($tostatestr){
					$tostatestr.="','".$arr_tostate[$i];
				}else{
					$tostatestr.= $arr_tostate[$i];
				}
			}		   			
			//// array initialization to send by query string of  from location
			$locationstr = "";
			$arr_loc = $_REQUEST['frm_loc'];
			for($i=0; $i<count($arr_loc); $i++){
				if($locationstr){
					$locationstr.="','".$arr_loc[$i];
				}else{
					$locationstr.= $arr_loc[$i];
				}
			}	 	
			
			//// array initialization to send by query string of  to location
			$tolocationstr = "";
			$arr_toloc = $_REQUEST['to_loc'];
			for($i=0; $i<count($arr_toloc); $i++){
				if($tolocationstr){
					$tolocationstr.="','".$arr_toloc[$i];
				}else{
					$tolocationstr.= $arr_toloc[$i];
				}
				
			}	 	
			
			  
				  		  
	?>
        <div class="form-group">
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
			<?php if ($_REQUEST['frm_state'] == '' || $_REQUEST['frm_loc'] == '') {?>		
			<?php  }else {?>
           <span>Partwise Sale Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/salepartwise_report.php?daterange=<?=$_REQUEST['daterange']?>&prod_code=<?=base64_encode($prostr);?>&brand=<?=base64_encode($brandstr);?>&model=<?=base64_encode($modelstr);?>&frm_state=<?=base64_encode($fromstatestr);?>&frm_loc=<?=base64_encode($locationstr);?>&to_state=<?=base64_encode($tostatestr);?>&to_loc=<?=base64_encode($tolocationstr);?>" title="Export Sale Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Sale Report details in excel"></i></a>
		   <?php }?>
		   </div>
          </div>
		   <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">  
			<?php if ($_REQUEST['frm_state'] == '' || $_REQUEST['frm_loc'] == '') {?>		
			<?php  }else {?>           
		   <span>Detailed Sale Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/saledetail_report.php?daterange=<?=$_REQUEST['daterange']?>&frm_state=<?=base64_encode($fromstatestr);?>&frm_loc=<?=base64_encode($locationstr);?>&to_state=<?=base64_encode($tostatestr);?>&to_loc=<?=base64_encode($tolocationstr);?>" title="Export Sale Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Sale Report details in excel"></i></a>
		     <?php }?>
			  </div>
			<div class="col-md-5"> 
			  <span>e-Invoice Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/sale_dispatch_einvoice.php?daterange=<?=$_REQUEST['daterange']?>&to_state=<?=base64_encode($_REQUEST['to_state']);?>&to_loc=<?=base64_encode($_REQUEST['to_loc']);?>&status_id=<?=base64_encode($_REQUEST['status_id']);?>" title="Export Sales Dispatch Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export e-Invoice Report details in excel"></i></a>  </div>
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
<script>
    alert("ddssd");
</script>
</body>
</html>