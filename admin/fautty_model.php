<?php
include("../includes/config.php");
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);
/////// get Access brand////////////////////////
$arrbrand = getAccessBrand($_SESSION['userid'],$link1);
/////// get Access product category////////////////////////
$arrproduct = getAccessProduct($_SESSION['userid'],$link1);
if($_POST['Submit']=="GO"){
////// filter value
if($_POST["state"]!=""){
	$state_condi = " stateid = '".$_POST["state"]."'";
}else{
	$state_condi = " stateid in (".$arrstate.")";
}
/////////
if($_POST["location_code"]!=""){
	$loc_condi = "current_location = '".$_POST["location_code"]."'";
}else{
	$loc_condi = " current_location in (select location_code from location_master where ".$state_condi.")";
}

//////// 
if($_POST["brand"]!=""){
	$brand_condi = " brand_id = '".$_POST["brand"]."'";
}else{
	$brand_condi = " brand_id in (".$arrbrand.")";
}
if($_POST["product_cat"]!=""){
	$prodcat_condi = " product_id = '".$_POST["product_cat"]."'";
}else{
	$prodcat_condi = " product_id in (".$arrproduct.")";
}
////////
if($_POST["product"]!=""){
	$prod_condi = " model_id = '".$_POST["product"]."'";
}else{
	$prod_condi = " model_id in (select model_id from model_master where ".$brand_condi." and ".$prodcat_condi.")";
}
//////// 
if($_POST['daterange'] != ""){
	$date_range = explode(" - ",$_POST['daterange']);
	$daterange = "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
//////
$amc_str1 = "";
$amc_str2 = "";
//echo "select SUM(amc_amount) as amcamt, model_id from amc where ".$loc_condi." and ".$prod_condi." and ".$daterange." group by model_id";
//echo "select SUM(amc_amount) as amcamt, model_id from amc where ".$loc_condi." and ".$prod_condi." and ".$daterange." group by model_id";
$res_sale = mysqli_query($link1,"select count(job_no) as modcount, model_id from jobsheet_data where ".$loc_condi." and ".$prod_condi." and ".$daterange." group by model_id");
while($row_sale = mysqli_fetch_assoc($res_sale)){
	$part_name = getAnyDetails($row_sale['model_id'],"model","model_id","model_master",$link1);
	if($amc_str1){
		$amc_str1 .= ",'".$part_name."'";
		$amc_str2 .= ",".$row_sale["modcount"]."";
	}else{
		$amc_str1 .= "'".$part_name."'";
		$amc_str2 .= "".$row_sale["modcount"]."";
	}
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title><?=siteTitle?></title>
  <meta charset="utf-8">
  <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <style type="text/css">
 #container, #sliders {
    min-width: 310px; 
    max-width: 800px;
    margin: 0 auto;
}
#container {
    height: 400px; 
}
 </style>
 <script language="javascript" type="text/javascript">
/////////// function to get location on the basis of state
$(document).ready(function(){
	$('#state').change(function(){
		var stateid=$('#state').val();
		if(stateid!=""){
	  	$.ajax({
	    	type:'post',
			url:'../includes/getAzaxFields.php',
			data:{getlocationdrop:stateid},
			success:function(data){
	    		$('#locdiv').html(data);
			}
	  	});
		}
    });
});
/////////// function to get product on the basis of brand or product
function getModel(){
		var brandid=$('#brand').val();
		var productcatid=$('#product_cat').val();
		if(brandid!=""){
	  	$.ajax({
	    	type:'post',
			url:'../includes/getAzaxFields.php',
			data:{getproductdrop:brandid, prdcat:productcatid},
			success:function(data){
	    		$('#proddiv').html(data);
			}
	  	});
		}
}
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		<?php if($_REQUEST['daterange']==""){ ?>startDate:'<?=date("Y-m-01");?>',<?php }?>
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});
<?php if($_POST['Submit']=="GO"){ ?>
$(document).ready(function(){
Highcharts.chart('container', {
    chart: {
        type: 'cylinder',
        options3d: {
            enabled: true,
            alpha: 15,
            beta: 15,
            depth: 50,
            viewDistance: 25
        }
    },
    title: {
        text: 'Model'
    },
    plotOptions: {
        series: {
            depth: 25,
            colorByPoint: true
        }
    },
    xAxis: {
        categories: [<?=$amc_str1?>],
        labels: {
            skew3d: true,
            style: {
                fontSize: '16px'
            }
        }
    },
    yAxis: {
        //min: 0,
        title: {
            text: 'Number Of Model '
        }
    },
    tooltip: {
        pointFormat: '<b> {point.y:.2f}</b>'
    },
    series: [{
        data: [<?=$amc_str2?>],
        //name: 'AMC Amount',
        showInLegend: false
    }]
});
});
<?php }?>
</script>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <script src="../high/highcharts_new.js"></script>
 <script src="../high/js/modules/exporting.js"></script>
 <script src="../high/highcharts-3d.js"></script>
<script src="../high/js/modules/cylinder.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9">
        	<h2 align="center"><i class="fa fa-area-chart"></i>Defect Model Report</h2><br/>
            <form class="form-horizontal" role="form" name="form1" action="" method="post">
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">State</label>
			<div class="col-md-6" align="left">
			   <select name="state" id="state" class="form-control">
              	<option value="">All</option>
              	<?php 
                $state = mysqli_query($link1,"select stateid, state from state_master  where stateid in (".$arrstate.") order by state"); 
                while($stateinfo = mysqli_fetch_assoc($state)){ 
				?>		
             	<option value="<?=$stateinfo['stateid']?>" <?php if($_REQUEST['state'] == $stateinfo['stateid']) { echo 'selected'; }?>><?=$stateinfo['state']?></option>
                <?php }?>
              </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Location</label>
			<div class="col-md-6" align="left" id="locdiv">
                  <select name="location_code" id="location_code" class="form-control">
              		<option value="">All</option>
                    <?php 
					$res_loc = mysqli_query($link1,"select location_code,locationname from location_master  where statusid='1' and stateid in (".$arrstate.") order by locationname"); 
					while($row_loc = mysqli_fetch_assoc($res_loc)){ 
					?>		
					<option value="<?=$row_loc['location_code']?>" <?php if($_REQUEST['location_code'] == $row_loc['location_code']) { echo 'selected'; }?>><?=$row_loc['locationname']." ".$row_loc['location_code']?></option>
					<?php }?>
              	  </select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Brand</label>	  
			<div class="col-md-6" align="left">
				<select name="brand" id="brand" class="form-control custom-select" onChange="getModel();">
              	 <option value="">All</option>
              	 <?php 
				 $sql ="select brand_id,brand from brand_master where status='1' and brand_id in (".$arrbrand.") order by brand";
			  	 $qry = mysqli_query($link1,$sql) ;
			  	 while ($row=mysqli_fetch_array($qry)){?>
                <option value="<?php echo $row['brand_id'];?>"<?php if($_REQUEST['brand']==$row['brand_id']){echo "selected";}?>><?php echo $row['brand'];?></option>
                <?php } ?>
              </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>
			<div class="col-md-6" align="left">
			 	
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
        <div class="col-md-6"><label class="col-md-5 control-label">Product</label>
			<div class="col-md-6" align="left">
			 	<select name="product_cat" id="product_cat" class="form-control" onChange="getModel();">
              		<option value="">All</option>
                    <?php 
					$res_prod = mysqli_query($link1,"select product_id,product_name from product_master  where product_id in (".$arrproduct.") and status='1' order by product_name"); 
					while($row_prod = mysqli_fetch_assoc($res_prod)){ 
					?>		
					<option value="<?=$row_prod['product_id']?>" <?php if($_REQUEST['product_cat'] == $row_prod['product_id']) { echo 'selected'; }?>><?=$row_prod['product_name']?></option>
					<?php }?>
              	</select>
            </div>
          </div>
         <div class="col-md-6"><label class="col-md-5 control-label">Model</label>	  
			<div class="col-md-6" align="left" id="proddiv">
				<select name="product" id="product" class="form-control">
              		<option value="">All</option>
                    <?php 
					$res_prod = mysqli_query($link1,"select model_id,model from model_master  where brand_id in (".$arrbrand.") and product_id in (".$arrproduct.") and status='1' order by model"); 
					while($row_prod = mysqli_fetch_assoc($res_prod)){ 
					?>		
					<option value="<?=$row_prod['model_id']?>" <?php if($_REQUEST['product'] == $row_prod['model_id']) { echo 'selected'; }?>><?=$row_prod['model']." ".$row_prod['model_id']?></option>
					<?php }?>
              	</select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>
			<div class="col-md-6" align="left">
            	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">		
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <?php if($_POST['Submit']=="GO"){ ?>
      <div id="container" style="height: 400px; width: auto; margin: 0 auto; border-bottom: solid; border-top: solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
      <?php } ?>
   		</div>
	</div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
