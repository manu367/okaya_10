<?php
include("../includes/config.php");
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);

if($_POST['Submit']=="GO"){
////// filter value
if($_POST["state"]!=""){
	$state_condi = " state_id = '".$_POST["state"]."'";
}else{
	$state_condi = " 1 ";
}
if($_POST["eng_type"]!=""){
	$loc_condi = " current_location in (select location_code from location_master where locationtype = '".$_POST["eng_type"]."') ";
}else{
	$loc_condi = " 1 ";
}
if($_POST["entity_name"]!=""){
	$enti_condi = " eng_id in (select userloginid from locationuser_master where mapped_bsi = '".$_POST["entity_name"]."')";
}else{
	$enti_condi = " 1";
}
//////// 
if($_POST["locationcity"]!=""){
	$city_condi = " city_id = '".$_POST["locationcity"]."'";
}else{
	$city_condi = " 1 ";
}
###Segment Filter
if($_POST["product"]=="Inverter"){
	$product = " product_id = '1'";
}
else if($_POST["product"]=="Battery"){
	$product = " product_id NOT IN ('1','6','10','11','12','14')";
}
else if($_POST["product"]=="Solar"){
	$product = " product_id IN ('6','10','11','12')";
}
else{
	$product = " 1 ";
}
////////
if($_POST["eng_id"]!=""){
	$eng_id = " eng_id ='".$_POST["eng_id"]."' ";
}else{
	$eng_id = " 1 ";
}
//////// 

if($_POST['daterange'] != ""){
	$date_range = explode(" - ",$_POST['daterange']);
	$daterange = "close_date >= '".$date_range[0]."' and close_date <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
////// count Closed tat from jobsheet data
$interval1 = 0;
$interval2 = 0;
$interval3 = 0;
$interval4 = 0;
$interval5 = 0;
//	echo "select datediff(close_date,open_date) as ageing from jobsheet_data where ".$loc_condi." and ".$state_condi." and ".$city_condi." and ".$enti_condi." and ".$eng_id." and ".$product." and close_date!='0000-00-00' and ".$daterange."";
$res_jd = mysqli_query($link1,"select datediff(close_date,open_date) as ageing from jobsheet_data where ".$loc_condi." and ".$state_condi." and ".$city_condi." and ".$enti_condi." and ".$eng_id." and ".$product." and close_date!='0000-00-00' and ".$daterange."");
while($row_jd = mysqli_fetch_assoc($res_jd)){
	if($row_jd["ageing"] >= 0 && $row_jd["ageing"] <= 1){
		$interval1 ++;
	}else if($row_jd["ageing"] > 1 && $row_jd["ageing"] <= 2){
		$interval2 ++;
	}else if($row_jd["ageing"] > 2 && $row_jd["ageing"] <= 3){
		$interval3 ++;
	}else if($row_jd["ageing"] > 3 && $row_jd["ageing"] <= 4){
		$interval4 ++;
	}else{
		$interval5 ++;
	}
}
//echo $interval1." - ".$interval2." - ".$interval3." - ".$interval4;
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
 <link href="../css/abc2.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">

 <script language="javascript" type="text/javascript">

$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		<?php if($_REQUEST['daterange']==""){ ?>startDate:'<?=date("Y-m-01");?>',<?php }?>
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});

<?php
$selectedCity = $_POST['locationcity'] ?? '';
?>	 
/////////// function to get city on the basis of state
 function get_citydiv(){
	  var name=$('#state').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state_dashboard:name},
		success:function(data){
			//alert(data);
	    $('#citydiv').html(data);
	    }
	  });
   
 }


<?php if($_POST['Submit']=="GO"){ ?>

$(document).ready(function(){

    var stateVal = "<?php echo $_POST['state'] ?? ''; ?>";
    var cityVal  = "<?php echo $_POST['locationcity'] ?? ''; ?>";

    if(stateVal !== ""){
        $.ajax({
            type:'post',
            url:'../includes/getAzaxFields.php',
            data:{state_dashboard:stateVal, selected_city:cityVal},
            success:function(data){
                $('#citydiv').html(data);
            }
        });
    }
});

$(document).ready(function(){
Highcharts.chart('container', {
  chart: {
    styledMode: true
  },

  title: {
    text: 'Closed TAT'
  },

  xAxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
	plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.percentage:.0f}%'
                }
            }
        },
	 tooltip: {
            headerFormat: '<span style="font-size:11px"><strong>Closed Call TAT</strong></span><br>',
          //  pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.percentage:.0f}%</b> of total count<br/>'
			pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b> of total count<br/>'
        },		
  series: [{
    type: 'pie',
    allowPointSelect: true,
    keys: ['name', 'y', 'selected', 'sliced'],
    data: [
      ['0 - 24 hrs', <?=$interval1?>, false],
      ['25 - 48 hrs', <?=$interval2?>, false],
      ['49 - 72 hrs', <?=$interval3?>, false],
	  ['73 - 96 hrs', <?=$interval4?>, false],
      ['Above 96 hrs', <?=$interval5?>, false]
    ],
    showInLegend: true
  }]
});});
<?php }?>
</script>
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
        	<h2 align="center"><i class="fa fa-bar-chart"></i> Closed calls TAT</h2><br/>
            <form class="form-horizontal" role="form" name="form1" action="" method="post">
			             <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		 
	    </div><!--close form group-->
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
		   <div class="col-md-6"><label class="col-md-5 control-label">Engineer Type</label>
			<div class="col-md-6" align="left" id="locdiv">
                  <select name="eng_type" id="eng_type" class="form-control">
              		<option value="">All</option>
                    <option value="ASP"<?php if($_REQUEST['eng_type'] == "ASP") { echo 'selected'; }?>>ASC</option>
                    <option value="BR" <?php if($_REQUEST['eng_type'] == "BR") { echo 'selected'; }?>>Okaya Engineer</option>
                    <option value="SSD" <?php if($_REQUEST['eng_type'] == "SSD") { echo 'selected'; }?>>SSD</option>
                   
              	  </select>
            </div>
          </div>
	    </div><!--close form group-->
       <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">City</label>	  
			<div class="col-md-6" align="left" id="citydiv">
				<select name="locationcity" id="locationcity" class="form-control">
                        <option value=''>All</option>
                        <?php 
						 $city_query="SELECT cityid, city FROM city_master where stateid='".$row_locdet['stateid']."' group by city order by city";
						 $city_res=mysqli_query($link1,$city_query);
						while($row_city = mysqli_fetch_array($city_res)){
    
						$selected = ($selectedCity == $row_city['cityid']) ? "selected" : "";
						
						echo "<option value='".$row_city['cityid']."' $selected>";
						echo $row_city['city']."</option>";
						}?>
                      </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">BSI</label>
			<div class="col-md-6" align="left">
			 	<select name="entity_name" id="entity_name" class="form-control">
              		<option value="">All</option>
                    <?php 
					$res_prod = mysqli_query($link1,"select sapid,name,username from admin_users where status='1' AND designation_id='45' order by name"); 
					while($row_prod = mysqli_fetch_assoc($res_prod)){ 
					?>		
					<option value="<?=$row_prod['sapid']?>" <?php if($_REQUEST['entity_name'] == $row_prod['sapid']) { echo 'selected'; }?>><?=$row_prod['name']?></option>
					<?php }?>
              	</select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
        <div class="col-md-6"><label class="col-md-5 control-label">Segment</label>
			<div class="col-md-6" align="left">
			 	<select name="product" id="product" class="form-control">
              		<option value="">All</option>
                    <option value="Battery" <?php if($_REQUEST['product'] == "Battery") { echo 'selected'; }?>>Battery</option>
                    <option value="Inverter"<?php if($_REQUEST['product'] == "Inverter") { echo 'selected'; }?>>Inverter</option>
                    <option value="Solar" <?php if($_REQUEST['product'] == "Solar") { echo 'selected'; }?>>Solar</option>
                   
              	</select>
            </div>
          </div>
         <div class="col-md-6"><label class="col-md-5 control-label">Engineer</label>	  
			<div class="col-md-6" align="left" id="proddiv">
				<select name="eng_id" id="eng_id" class="form-control">
              		<option value="">All</option>
                    <?php 
					$res_prod = mysqli_query($link1,"select userloginid,locusername from locationuser_master  where statusid='1'  order by locusername"); 
					while($row_prod = mysqli_fetch_assoc($res_prod)){ 
					?>		
					<option value="<?=$row_prod['userloginid']?>" <?php if($_REQUEST['eng_id'] == $row_prod['userloginid']) { echo 'selected'; }?>><?=$row_prod['locusername']." ".$row_prod['userloginid']?></option>
					<?php }?>
              	</select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-6" align="left">
				<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>
			<div class="col-md-6" align="left">
            			
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
