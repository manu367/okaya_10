<?php
	require_once("../includes/config.php");
	////get access brand details
	$access_brand = getAccessBrand($_SESSION['userid'],$link1);
	
if($_POST['Submit']=="GO"){	
	## selected  Date range
	$date_range = explode(" - ",$_REQUEST['daterange']);
	if($_REQUEST['daterange'] != ""){
		$daterange = "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";
		$rc_daterange = "close_date >= '".$date_range[0]."' and close_date <= '".$date_range[1]."'";
	}else{
		$daterange = "1";
		$rc_daterange = "1";
	}
	## selected  brand name
	if($_REQUEST['brand'] != ""){
		$brandid = " and brand_id = '".$_REQUEST['brand']."'";
	}else{
		$brandid = " and brand_id in (".$access_brand.")";
	}
		
	/********************* VOC Chart **************************/	
	$voc_str = "";
 	$arr_voc = array();
	$top_voc = mysqli_query($link1,"SELECT count(job_id) as jobs, cust_problem FROM jobsheet_data WHERE ".$daterange."".$brandid." group by cust_problem order by jobs DESC LIMIT 0, 10 ");
	while($top_vocrow = mysqli_fetch_array($top_voc)){
		$arr_voc[$top_vocrow["cust_problem"]] = $top_vocrow["jobs"];
	}

	arsort($arr_voc);
	$top_10qty = array_slice($arr_voc, 0, 10);
	$totVoc = array_sum($arr_voc);
	
	foreach($top_10qty as $statuss => $cont){
		$name = "";
		if($voc_str){
			$name = getAnyDetails($statuss,"voc_desc","voc_code","voc_master",$link1); 
			$voc_str .= ",{ name: '".$name."', y: ".$top_10qty[$statuss]." }";
		}else{
			$name = getAnyDetails($statuss,"voc_desc","voc_code","voc_master",$link1); 
			$voc_str .= "{ name: '".$name."', y: ".$top_10qty[$statuss]." }";
		}
	}
	/*********************************************************/
	
	/********************* Model Chart **************************/	
	$model_str = "";
 	$arr_model = array();
	$top_model = mysqli_query($link1,"SELECT count(job_id) as models, model_id FROM jobsheet_data WHERE ".$daterange."".$brandid." group by model_id order by models DESC LIMIT 0, 10 ");
	while($top_modelrow = mysqli_fetch_array($top_model)){
		$arr_model[$top_modelrow["model_id"]] = $top_modelrow["models"];
	}

	arsort($arr_model);
	$top_10_model_qty = array_slice($arr_model, 0, 10);
	$totModel = array_sum($arr_model);
	
	foreach($top_10_model_qty as $statuss4 => $cont){
		$name4 = "";
		if($model_str){
			$name4 = getAnyDetails($statuss4,"model","model_id","model_master",$link1); 
			$model_str .= ",{ name: '".$statuss4."', y: ".$top_10_model_qty[$statuss4]." }";
		}else{
			$name = getAnyDetails($statuss4,"model","model_id","model_master",$link1); 
			$model_str .= "{ name: '".$statuss4."', y: ".$top_10_model_qty[$statuss4]." }";
		}
	}
	
	/*********************************************************/
	
	/********************* Repair Code Chart **************************/	
	$repair_code_str = "";
 	$arr_repair_code = array();
	$top_repair_code = mysqli_query($link1,"SELECT count(id) as repaircode, repair_code FROM repair_detail WHERE ".$rc_daterange."".$brandid." group by repair_code order by repaircode DESC LIMIT 0, 10 ");
	while($top_repair_coderow = mysqli_fetch_array($top_repair_code)){
		$arr_repair_code[$top_repair_coderow["repair_code"]] = $top_repair_coderow["repaircode"];
	}

	arsort($arr_repair_code);
	$top_10qty = array_slice($arr_repair_code, 0, 10);
	$totRepair_code = array_sum($arr_repair_code);
	
	foreach($top_10qty as $statuss => $cont){
		$name = "";
		if($repair_code_str){
			$name = getAnyDetails($statuss,"rep_desc","rep_code","repaircode_master",$link1); 
			$repair_code_str .= ",{ name: '".$name."', y: ".$top_10qty[$statuss]." }";
		}else{
			$name = getAnyDetails($statuss,"rep_desc","rep_code","repaircode_master",$link1); 
			$repair_code_str .= "{ name: '".$name."', y: ".$top_10qty[$statuss]." }";
		}
	}
	/*********************************************************/
	
	/********************* Fault Code Chart **************************/	
	$fault_code_str = "";
 	$arr_fault_code = array();
	$top_fault_code = mysqli_query($link1,"SELECT count(id) as fault, fault_code FROM repair_detail WHERE ".$rc_daterange."".$brandid." group by fault_code order by fault DESC LIMIT 0, 10 ");
	while($top_fault_coderow = mysqli_fetch_array($top_fault_code)){
		$arr_fault_code[$top_fault_coderow["fault_code"]] = $top_fault_coderow["fault"];
	}

	arsort($arr_fault_code);
	$top_10_fault_qty = array_slice($arr_fault_code, 0, 10);
	$totfault_code = array_sum($arr_fault_code);
	
	foreach($top_10_fault_qty as $statuss3 => $cont){
		$name3 = "";
		if($fault_code_str){
			$name3 = getAnyDetails($statuss3,"defect_desc","defect_code","defect_master",$link1); 
			$fault_code_str .= ",{ name: '".$name3."', y: ".$top_10_fault_qty[$statuss3]." }";
		}else{
			$name3 = getAnyDetails($statuss3,"defect_desc","defect_code","defect_master",$link1); 
			$fault_code_str .= "{ name: '".$name3."', y: ".$top_10_fault_qty[$statuss3]." }";
		}
	}	
	/*********************************************************/
	
		
}  ///submit close

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
 <script type="text/javascript" src="../js/moment.js"></script>
 
<script type="text/javascript" language="javascript" >
	$(document).ready(function(){
		$('input[name="daterange"]').daterangepicker({
			<?php if($_REQUEST['daterange']==""){ ?>startDate:'<?=date("Y-m-01");?>',<?php }?>
			locale: {
				format: 'YYYY-MM-DD'
			}
		});
	});
		 
</script>
<!-- Include Date Range Picker -->
<script type="text/javascript" src="../js/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>

<script>
$(document).ready(function(){
});
</script>

<?php if($_POST['Submit']=="GO"){ ?>
<script type="text/javascript">
$(function() {
Highcharts.chart('container', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45
        }
    },
    title: {
        text: 'Top 10 VOC'
    },
    subtitle: {
        text: ''
    },
	credits: {
		enabled: false
	},
    plotOptions: {
        pie: {
            innerSize: 100,
            depth: 45
        }
    },
    series: [{
        name: 'VOC',
        data: [<?=$voc_str?>]
    }]
});

Highcharts.chart('container1', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45
        }
    },
    title: {
        text: 'Top 10 Repair Code'
    },
    subtitle: {
        text: ''
    },
    plotOptions: {
        pie: {
            innerSize: 100,
            depth: 45
        }
    },
	credits: {
		enabled: false
	},
    series: [{
        name: 'Repair Code',
        data: [<?=$repair_code_str?>]
    }]
});

Highcharts.chart('container2', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45
        }
    },
    title: {
        text: 'Top 10 Fault Code'
    },
    subtitle: {
        text: ''
    },
    plotOptions: {
        pie: {
            innerSize: 100,
            depth: 45
        }
    },
	credits: {
		enabled: false
	},
    series: [{
        name: 'Fault Code',
        data: [<?=$fault_code_str?>]
    }]
});

Highcharts.chart('container3', {
    chart: {
        type: 'pie',
        options3d: {
            enabled: true,
            alpha: 45
        }
    },
    title: {
        text: 'Top 10 Model'
    },
    subtitle: {
        text: ''
    },
    plotOptions: {
        pie: {
            innerSize: 100,
            depth: 45
        }
    },
	credits: {
		enabled: false
	},
    series: [{
        name: 'Model',
        data: [<?=$model_str?>]
    }]
});

});
</script>
<?php }?>

<script src="../high/highcharts.js" type="text/javascript"></script>

<script type="text/javascript">
    <!--
    var b_timer = null; // blink timer
    var b_on = true; // blink state
    var blnkrs = null; // array of spans
    function blink() {
    var tmp = document.getElementsByTagName("span");
    if (tmp) {
    blnkrs = new Array();
    var b_count = 0;
    for (var i = 0; i < tmp.length; ++i) {
    if (tmp[i].className == "blink") {
    blnkrs[b_count] = tmp[i];
    ++b_count;
    }
    }
    // time in m.secs between blinks
    // 500 = 1/2 second
    blinkTimer(500);
    }
    }
    function blinkTimer(ival) {
    if (b_timer) {
    window.clearTimeout(b_timer);
    b_timer = null;
    }
    blinkIt();
    b_timer = window.setTimeout('blinkTimer(' + ival + ')', ival);
    }
    function blinkIt() {
    for (var i = 0; i < blnkrs.length; ++i) {
    if (b_on == true) {
    blnkrs[i].style.visibility = "hidden";
    }
    else {
    blnkrs[i].style.visibility = "visible";
    }
    }
    b_on =!b_on;
    }
    //-->
    </script>
    <style type="text/css">
<!--
.style1 {font-family: Papyrus}
-->
</style>
</head>
<body onLoad="blink();">
	<div class="container-fluid">
		<div class="row content">
			<?php 
			include("../includes/leftnav2.php");
			?>
			<!---<div class="col-sm-9" >---->
				<!----<div style="background-image:url('../images/Banner.png');width:100%; height:auto;" >---->
					<!----  <span><img src="../images/Banner.png" width="108%"></span>  -------->
					
				<!--</div>---->
				
	<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h3 align="center"><i class="fa fa-pie-chart"></i> Top Ranked </h3><br /><br />
      <?php if($_REQUEST['msg']){?>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        <strong>
        <?=$_REQUEST['chkmsg']?>
        !</strong>&nbsp;&nbsp;
        <?=$_REQUEST['msg']?>
        . </div>
      <?php }?>
      <form class="form-horizontal" role="form" name="form1" action="" method="post">
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label">Date Range</label>
            <div class="col-md-6 input-append date" align="left">
              <div style="display:inline-block;float:left">
                <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/>
              </div>
              <div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-4 control-label">Brand</label>
            <div class="col-md-5" align="left"> 
				<select name="brand" id="brand" class="form-control">
					<option value="" <?php if($_REQUEST['brand'] == ""){ echo "selected"; } ?>> All </option>
					<?php
						$dept_query="SELECT * FROM brand_master where brand_id in ($access_brand) and status = '1'  order by brand";
						$check_dept=mysqli_query($link1,$dept_query);
						while($br_dept = mysqli_fetch_array($check_dept)){
						?>
					<option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
					<?php }?>
                </select>
			</div>
          </div>
        </div>
        <!--close form group-->
        
        <div class="form-group">
          <div class="col-md-12">
              <div style="text-align:center;">
                <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
              </div>
          </div>
        </div>
        <!--close form group-->		
      </form>
	  
      <form class="form-horizontal" role="form" name="form2">
		  <?php if($_POST['Submit']=="GO"){ ?>
		  	<br><br>
		  	 <div class="form-group">
			 	<div class="col-md-12">
				  <div class="col-md-6">
				  	  <div class="panel panel-info table-responsive">
						  <div class="panel-body">
							  <div style="text-align:center;">
								<div  id="container" class="form-group table-responsive"></div>
							  </div>
						  </div>
					  </div>
					  
				  </div>
				  <div class="col-md-6">
				  	  <div class="panel panel-info table-responsive">
						  <div class="panel-body">
							  <div style="text-align:center;">
								<div  id="container3" class="form-group table-responsive"></div>
							  </div>
						  </div>
					  </div>
				  </div>
			  </div>
			</div>
			<div class="form-group">
			 	<div class="col-md-12">
				  <div class="col-md-6">
				  	  <div class="panel panel-info table-responsive">
						  <div class="panel-body">
							  <div style="text-align:center;">
								<div id="container1" class="form-group table-responsive"></div>
							  </div>
						  </div>
					  </div>
				  </div>
				  <div class="col-md-6">
				  	  <div class="panel panel-info table-responsive">
						  <div class="panel-body">
							  <div style="text-align:center;">
								<div  id="container2" class="form-group table-responsive"></div>
							  </div>
						  </div>
					  </div>
				  </div>
			  </div>
			</div>
		  <?php } ?>
      </form>
    </div>
				
				
		   <!---</div>---->
	   </div>
   </div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
