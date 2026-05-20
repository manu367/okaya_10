<?php
require_once("../includes/config.php");
$today=date("Y-m-d");
$statename = $_REQUEST['statename'];
$locationname=$_REQUEST['locationname'];
$product=$_REQUEST['prod_code'];
$brandarray=$_REQUEST['brand'];
$modelarray=$_REQUEST['model'];
$statusarray=$_REQUEST['status'];
$substatusarray=$_REQUEST['substatus'];
	
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
////////////////////////// get city  and location /////////////////////////////////////
$arr_statestr = $_REQUEST['statename'];
			for($i=0; $i<count($arr_statestr); $i++){
				if($statestr){
					$statestr.="','".$arr_statestr[$i];
				}else{
					$statestr.= $arr_statestr[$i];
				}
			}
//$access_product = getAccessProduct($_REQUEST['locationname'],$link1);
$access_product = getAccessProduct($_SESSION['userid'],$link1);
//print_r($_REQUEST['locationname']);exit;
///////// access brand ////		
//$access_brand = getAccessBrand($_REQUEST['locationname'],$link1);
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
////statezone///
$zoneid=explode("~",$_REQUEST['region']);
if($_REQUEST['region']==''){

$area="";
$statezone="";
}

else{
$area="and area='".$zoneid[0]."'";

$statezone="and zoneid='".$zoneid[1]."'";
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
	$('#statename').multiselect({
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
});
$(document).ready(function(){
	if($("#pending").is(":checked")){
		$("#dt_range").hide();	
		 $("#st").hide();	
		 $("#subst").hide();	
	}
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
      <h2 align="center"><i class="fa fa-volume-control-phone"></i> All Call</h2>
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
		  <div class="col-md-6"><label class="col-md-5 control-label">All pending:</label>	  
			<div class="col-md-6" align="left">
			 <input type="checkbox" name="pending"  id="pending"   value="checked"  <?php if($_REQUEST['pending']){echo "checked";}?>> 
            </div>
          </div>
	    </div><!--close form group-->
	     <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Region</label>
              <div class="col-md-6">
             	<select name="region" id="region" class="form-control"  onChange="document.form1.submit();">
				 <option value=""<?php if($_REQUEST['region']=="") { echo 'selected'; }?>>All</option>
                <?php
                $res_zone = mysqli_query($link1,"select  zonename,zoneid from zone_master where 1 "); 
                while($row_zone = mysqli_fetch_assoc($res_zone)){?>
				
                <option value="<?=$row_zone['zonename']."~".$row_zone['zoneid']?>" <?php if($_REQUEST['region'] == $row_zone['zonename']."~".$row_zone['zoneid']) { echo 'selected'; }?>><?=$row_zone['zonename']?></option>
                <?php } ?>
                 </select>
              </div>
            </div><div class="col-md-6"><label class="col-md-5 control-label">State <span style="color:#F00">*</span></label>	  
			<div class="col-md-6" >

                <select   name="statename[]" id="statename" multiple="multiple" class="form-control required"  onChange="document.form1.submit();" required>
				<?php 
                $state = mysqli_query($link1,"select stateid, state from state_master  where stateid in ($arrstate) $statezone order by state" ); 
                while($stateinfo = mysqli_fetch_assoc($state)){ 
				?>		
             <option value="<?=$stateinfo['stateid']?>" <?php for($i=0; $i<count($statename); $i++){if($statename[$i]==$stateinfo['stateid']) { echo 'selected'; } }?>><?=$stateinfo['state']?></option>
                <?php }?>
	</select>
              </div>
          </div></div>
		<div class="form-group">

		  <div class="col-md-6"><label class="col-md-5 control-label">Location</label>	  
			<div class="col-md-6" id="citydiv">
				<select name="locationname" id="locationname"  class="form-control"  onChange="document.form1.submit();">
					<option value=""> Please Select </option>
				  <?php
				   $location_query="SELECT locationname, location_code FROM location_master where stateid in('$statestr') and (locationtype != 'WH' and locationtype != 'CC' ) and statusid='1' order by locationname ";
     $loc_res=mysqli_query($link1,$location_query);
     while($loc_info = mysqli_fetch_array($loc_res)){?>
				  <option value="<?=$loc_info['location_code']?>" <?php if($loc_info['location_code'] == $_REQUEST['locationname']) { echo 'selected'; } ?>><?=$loc_info['locationname']?></option>
				<?php }  ?>
                 </select>
              </div>
          </div>
            <div class="col-md-6"><label class="col-md-5 control-label">Product</label>	  
			<div class="col-md-6" id="location">
                   <select   name="prod_code[]" id="prod_code"  multiple="multiple" class="form-control" onChange="document.form1.submit();">
				<?php
               echo $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";
			        $check1=mysqli_query($link1,$model_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['product_id']?>" <?php for($i=0; $i<count($product); $i++){ if($product[$i] == $br['product_id']) { echo 'selected'; }}?>><?=$br['product_name']." | ".$br['product_id']?></option>
                <?php } ?>
	</select>
              </div>
              </div>
	    </div>
		
		<div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label">Brand</label>	  
			<div class="col-md-6" >
               <select   name="brand[]" id="brand" class="form-control"  multiple="multiple" onChange="document.form1.submit();">
				<?php
				   //echo "SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";exit;
                $brand = mysqli_query($link1,"SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand" );
                while($brandinfo = mysqli_fetch_assoc($brand)){?>
                <option value="<?=$brandinfo['brand_id']?>" <?php for($i=0; $i<count($brandarray); $i++){if($brandarray[$i] == $brandinfo['brand_id']) { echo 'selected'; }}?>><?=$brandinfo['brand']." | ".$brandinfo['brand_id']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
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
	    </div>
		
		<div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label">Status</label>	  
			<div class="col-md-6" id="modeldiv">
				<select id="status"
                        name="status[]"
                        class="form-control"
                        multiple="multiple"
                        onChange="document.form1.submit();" >
                    <?php
                    $res_status = mysqli_query($link1,"select  status_id , main_status_id,system_status from jobstatus_master where status_id in ('1','2','3','5','6','7','8','11','12','48','49','50','55','56') and (status_id = main_status_id )")or die(mysqli_error($link1));
                    while($row_status = mysqli_fetch_assoc($res_status)){?>
                        <option value="<?=$row_status['status_id']?>" <?php
                        for($i=0; $i<count($statusarray); $i++){
                            if($statusarray[$i]==$row_status['status_id']){ echo "selected";}}?>>
                            <?=$row_status['system_status']?></option>
                    <?php } ?>
                </select>
              </div>
          </div>
          <div class="col-md-6" id="st">
			
          </div>
	    </div>

        <div class="form-group">
         
		  <div class="col-md-12" style="text-align:center;" >
				<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">                 
          </div>
	    </div>
	  </form>
       <?php if ($_REQUEST['Submit']){		   
	    //// array initialization to send by query string of  state
			$statestr = "";
			$arr_statestatus = $_REQUEST['statename'];
			for($i=0; $i<count($arr_statestatus); $i++){
				if($statestr){
					$statestr.="','".$arr_statestatus[$i];
				}else{
					$statestr.= $arr_statestatus[$i];
				}
			}					
			
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
	?>
        <div class="form-group">
		  <div class="col-md-12" style="text-align:center;margin-top:20px;margin-bottom:30px;">
			<?php 
	//print_r($_REQUEST);
	if ($_REQUEST['statename'] == '') {?>		
			<?php  }else {?>
               Download Excel : <a href="../excelReports/allcallexcel.php?daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['locationname']);?>&modelid=<?=base64_encode($modelstr);?>&status=<?=base64_encode($statusstr);?>&pending=<?=base64_encode($_REQUEST['pending'])?>&state=<?=base64_encode($statestr);?>&proid=<?=base64_encode($prostr);?>&brand=<?=base64_encode($brandstr);?>"
                                   title="Export All Call details in excel">
            <i class="fa fa-file-excel-o fa-2x faicon" title="Export All Call details in excel"></i></a>
			   <?php }?>
          </div>
	    </div><!--close form group-->
		
		<?php 
			if($_REQUEST['pending']==""){ 
		?>
		<div class="panel panel-info table-responsive">
		  <div class="panel-heading" style="text-align:center;"> Jobs Counter </div>
		  <div class="panel-body">
			<table class="table table-bordered" width="100%">
				<tbody>          
					<tr class="<?=$tableheadcolor?>" >
						<td style="text-align:center;" ><label class="control-label">Location</label></td>
						<td style="text-align:center;" ><label class="control-label">Pending</label></td>
						<td style="text-align:center;" ><label class="control-label">Part Pending</label></td>
						<td style="text-align:center;" ><label class="control-label">Repair Done</label></td> 
						<td style="text-align:center;" ><label class="control-label">Demo Done</label></td> 
						<td style="text-align:center;" ><label class="control-label">Installation Done</label></td> 
						<td style="text-align:center;" ><label class="control-label">Cancel</label></td> 
						<td style="text-align:center;" ><label class="control-label">RWR</label></td> 
						<td style="text-align:center;" ><label class="control-label">Completed</label></td> 
						<td style="text-align:center;" ><label class="control-label">All Jobs</label></td> 
					</tr>
					<?php
						//// extract all encoded variables
						$modelid = $modelstr;
						$productid = $prostr;
						$brandid = $brandstr;
						$state = $statestr;
						$status = $statusstr;
						$loc_code = $_REQUEST['locationname'];
			
						//////// get date /////////////////////////
						if ($_REQUEST['daterange'] != ""){
							$seldate = explode(" - ",$_REQUEST['daterange']);
							$fromdate = $seldate[0];
							$todate = $seldate[1];
						}
						else{
							$seldate = $today;
							$fromdate = $today;
							$todate = $today;
						}
						/////get location///////////////
						if($loc_code!=""){
							$locationcode=" current_location in ('".$loc_code."')";
						}
						else {
							$locationcode="1";
						}
						/////get model///////////////
						if($modelid!=""){
							$model_id=" and model_id in ('".$modelid."')";
						}
						else {
							$model_id="";
						}
						/////get product///////////////
						if($productid !=""){
							$product_id=" and product_id in ('".$productid."')";
						}
						else {
							$product_id="";
						}
						/////get brand///////////////
						if($brandid !=""){
							$brand_id=" and brand_id in ('".$brandid."')";
						}
						else {
							$brand_id="";
						}
						if($status !=""){
							$st=" and status in ('".$status."')";
						}
						else {
							$st="";
						}
						if($state !=""){
							$stateid=" and state_id in ('".$state."') ";
						}
						else {
							$stateid="";
						}

						$t_pending = 0;
						$t_pna = 0;
						$t_repair = 0;
						$t_demo = 0;
						$t_installation = 0;
						$t_cancel = 0;
						$t_rwr = 0;
						$t_total = 0;
						$total = 0;
						$t_oth_job = 0;
						$uj = mysqli_fetch_array(mysqli_query($link1,"Select count(job_id) as jn  from jobsheet_data where current_location = '' and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$st." ".$stateid." ".$product_id." ".$brand_id." "));
					?>
					<!---- Unallocated Jobs ---->
					<!----
					<tr style="background-color:#d0f1bc;">
						<td align="left"> UNALLOCATED JOBS </td>
						<td align="center"><?php //echo $uj['jn']; ?></td>
						<td colspan="7" align="left"></td>
						<td align="center"><label class="control-label"><?php //echo $uj['jn']; ?></label></td>
					</tr>---->
					<?php	
						/////// use for location grouping //////			
						$qrr = mysqli_query($link1,"Select current_location  from jobsheet_data where ".$locationcode." and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$st." ".$stateid." ".$product_id." ".$brand_id." group by current_location order by current_location ");
											
						while($rw = mysqli_fetch_array($qrr)){ 
						
						$pending = 0;
						$pna = 0;
						$repair = 0;
						$demo = 0;
						$installation = 0;
						$cancel = 0;
						$rwr = 0;
						$sum_of = 0;
						$total = 0;
						$oth_job = 0;
						$loc_name = getAnyDetails($rw['current_location'],"locationname","location_code","location_master",$link1);
						///// asp ////
						$c1 = mysqli_fetch_array(mysqli_query($link1,"Select count(job_id) as jn  from jobsheet_data where current_location = '".$rw['current_location']."' and status in ('1','2','5','7','50','56','58') and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$stateid." ".$product_id." ".$brand_id.""));
						$pending = $c1['jn'];
						///// pna ////
						$c2 = mysqli_fetch_array(mysqli_query($link1,"Select count(job_id) as jn  from jobsheet_data where current_location = '".$rw['current_location']."' and status in ('3') and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$stateid." ".$product_id." ".$brand_id.""));
						$pna = $c2['jn'];
						///// repair ////
						$c3 = mysqli_fetch_array(mysqli_query($link1,"Select count(job_id) as jn  from jobsheet_data where current_location = '".$rw['current_location']."' and status in ('6') and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$stateid." ".$product_id." ".$brand_id.""));
						$repair = $c3['jn'];
						///// demo ////
						$c4 = mysqli_fetch_array(mysqli_query($link1,"Select count(job_id) as jn  from jobsheet_data where current_location = '".$rw['current_location']."' and status in ('48') and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$stateid." ".$product_id." ".$brand_id.""));
						$demo = $c4['jn'];
						///// installation ////
						$c5 = mysqli_fetch_array(mysqli_query($link1,"Select count(job_id) as jn  from jobsheet_data where current_location = '".$rw['current_location']."' and status in ('49') and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$stateid." ".$product_id." ".$brand_id.""));
						$installation = $c5['jn'];
						///// cancel ////
						$c6 = mysqli_fetch_array(mysqli_query($link1,"Select count(job_id) as jn  from jobsheet_data where current_location = '".$rw['current_location']."' and status in ('12') and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$stateid." ".$product_id." ".$brand_id.""));
						$cancel = $c6['jn'];
						///// rwr ////
						$c7 = mysqli_fetch_array(mysqli_query($link1,"Select count(job_id) as jn  from jobsheet_data where current_location = '".$rw['current_location']."' and status in ('11') and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$stateid." ".$product_id." ".$brand_id.""));
						$rwr = $c7['jn'];
						///// total ////
						$c8 = mysqli_fetch_array(mysqli_query($link1,"Select count(job_id) as jn  from jobsheet_data where current_location = '".$rw['current_location']."' and (open_date >= '".$fromdate."' and open_date <='".$todate."') ".$model_id." ".$stateid." ".$product_id." ".$brand_id.""));
						$sum_of = $c8['jn'];
						$total = $sum_of;
						$oth_job = ($total - ( $pending + $pna + $repair + $demo + $installation + $cancel + $rwr));
						$t_pending += $pending;
						$t_pna += $pna;
						$t_repair += $repair;
						$t_demo += $demo;
						$t_installation += $installation;
						$t_cancel += $cancel;
						$t_rwr += $rwr;
						$t_total += $total;
						$t_oth_job += $oth_job;
					?>
				    <tr>
                        <td align="left">
							<?php 
								if($rw['current_location']!=""){ 
									echo $loc_name." (".$rw['current_location'].")"; 
								}else{ 
									echo "<span style='color:green;font-weight:800;'> UNALLOCATED JOBS </span>"; 
								} 
							?>
						</td>
						<td align="center"><?php echo $pending; ?></td>
						<td align="center"><?php echo $pna; ?></td>
						<td align="center"><?php echo $repair; ?></td>
						<td align="center"><?php echo $demo; ?></td>
						<td align="center"><?php echo $installation; ?></td>
						<td align="center"><?php echo $cancel; ?></td>
						<td align="center"><?php echo $rwr; ?></td>
						<td align="center"><?php echo $oth_job; ?></td>
						<td style="background-color:#bce8f1;" align="center"><label class="control-label"><?php echo $total; ?></label></td>
					</tr>
					<?php } ?>
					<tr  style="background-color:#bce8f1;">
						<td align="right"><label class="control-label"> Total </label></td>
						<td align="center"><label class="control-label"><?php echo $t_pending; ?></label></td>
						<td align="center"><label class="control-label"><?php echo $t_pna; ?></label></td>
						<td align="center"><label class="control-label"><?php echo $t_repair; ?></label></td>
						<td align="center"><label class="control-label"><?php echo $t_demo; ?></label></td>
						<td align="center"><label class="control-label"><?php echo $t_installation; ?></label></td>
						<td align="center"><label class="control-label"><?php echo $t_cancel; ?></label></td>
						<td align="center"><label class="control-label"><?php echo $t_rwr; ?></label></td>
						<td align="center"><label class="control-label"><?php echo $t_oth_job; ?></label></td>
						<td style="background-color:#337ab7;color:#ffffff;" align="center"><label class="control-label"><?php echo $t_total; ?></label></td>
					</tr>
				</tbody>
			  </table>
		  </div><!--close panel body-->
		</div><!--close panel-->
		<?php } ?>
        <?php }?>
    </div>
  </div>
</div>
<script>
    function ReportGenerations(){
        this.data=data;
    }
    ReportGenerations.prototype.generate=function(){
    }
</script>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>