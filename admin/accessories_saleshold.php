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
	$loc_condi = " a.from_location = '".$_POST["location_code"]."'";
}else{
	$loc_condi = " a.from_location in (select location_code from location_master where ".$state_condi.")";
}
if($_POST["entity_name"]!=""){
	$enti_condi = " b.entity_type = '".$_POST["entity_name"]."'";
}else{
	$enti_condi = " 1";
}
//////// 
if($_POST["brand"]!=""){
	$brand_condi = " a.brand_id = '".$_POST["brand"]."'";
}else{
	$brand_condi = " a.brand_id in (".$arrbrand.")";
}
if($_POST["product_cat"]!=""){
	$prodcat_condi = " a.product_id = '".$_POST["product_cat"]."'";
}else{
	$prodcat_condi = " a.product_id in (".$arrproduct.")";
}
////////
if($_POST["product"]!=""){
	$prod_condi = " a.partcode = '".$_POST["product"]."'";
}else{
	$prod_condi = " a.partcode in (select a.partcode from partcode_master a where ".$brand_condi." and ".$prodcat_condi.")";
}
//////// 
if($_POST['daterange'] != ""){
	$date_range = explode(" - ",$_POST['daterange']);
	$daterange = "b.sale_date >= '".$date_range[0]."' and b.sale_date <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
////// count Closed tat from jobsheet data
$accessories_str = "";
$res_sale = mysqli_query($link1,"select SUM(a.qty) as saleqty, a.partcode from billing_product_items a, billing_master b where ".$loc_condi." and ".$prod_condi." and ".$daterange."  and a.challan_no=b.challan_no group by a.partcode");
while($row_sale = mysqli_fetch_assoc($res_sale)){
	$part_name = getAnyDetails($row_sale['partcode'],"part_name","partcode","partcode_master",$link1);
	if($accessories_str){
		$accessories_str .= ",['".$part_name."', ".$row_sale['saleqty'].", false]";
	}else{
		$accessories_str .= "['".$part_name."', ".$row_sale['saleqty'].", false]";
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
			data:{getproductdrop1:brandid, prdcat1:productcatid},
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
    styledMode: true
  },

  title: {
    text: 'Accessories Sales'
  },

  xAxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
	plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.y:.0f}'
                }
            }
        },
	 tooltip: {
            headerFormat: '<span style="font-size:11px"><strong>Accessories Sales</strong></span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b> of total count<br/>'
        },		
  series: [{
    type: 'pie',
    allowPointSelect: true,
    keys: ['name', 'y', 'selected', 'sliced'],
    data: [
		<?=$accessories_str?>    
	],
    showInLegend: true
  }]
});});
<?php }?>
</script>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <script src="../high/highcharts_new.js"></script>
 <script src="../high/js/modules/exporting.js"></script>
 <link rel="stylesheet" href="../high/highcharts.css">
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9">
        	<h2 align="center"><i class="fa fa-line-chart"></i> Accessories Sales</h2><br/>
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
		  <div class="col-md-6"><label class="col-md-5 control-label">Entity Name</label>
			<div class="col-md-6" align="left">
			 	<select name="entity_name" id="entity_name" class="form-control">
              		<option value="">All</option>
                    <?php 
					$res_prod = mysqli_query($link1,"select id,name from entity_type where status_id='1' order by name"); 
					while($row_prod = mysqli_fetch_assoc($res_prod)){ 
					?>		
					<option value="<?=$row_prod['id']?>" <?php if($_REQUEST['entity_name'] == $row_prod['id']) { echo 'selected'; }?>><?=$row_prod['name']?></option>
					<?php }?>
              	</select>
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
         <div class="col-md-6"><label class="col-md-5 control-label">Accessories</label>	  
			<div class="col-md-6" align="left" id="proddiv">
				<select name="product" id="product" class="form-control">
              		<option value="">All</option>
                    <?php 
					$res_prod = mysqli_query($link1,"select partcode,part_name from partcode_master where brand_id in (".$arrbrand.") and product_id in (".$arrproduct.") and status='1' and part_category='ACCESSORY' order by part_name"); 
					while($row_prod = mysqli_fetch_assoc($res_prod)){ 
					?>		
					<option value="<?=$row_prod['partcode']?>" <?php if($_REQUEST['product'] == $row_prod['partcode']) { echo 'selected'; }?>><?=$row_prod['part_name']." ".$row_prod['partcode']?></option>
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
