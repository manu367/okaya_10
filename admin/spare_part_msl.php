<?php
include("../includes/config.php");
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);
if($_POST['Submit']=="GO"){
////// filter value
if($_POST["state"]!=""){
	$state_condi = " stateid = '".$_POST["state"]."'";
}else{
	$state_condi = " stateid in (".$arrstate.")";
}
/////////
if($_POST["location_code"]!=""){
	$loc_condi = " location_code = '".$_POST["location_code"]."'";
}else{
	$loc_condi = " location_code in (select location_code from location_master where ".$state_condi.")";
}
////// calculate spare part MSL
$drill_engperf_str="";
$innerdrill_engperf_str="";
$res_ci = mysqli_query($link1,"select location_code from client_inventory where ".$loc_condi." group by location_code");
while($row_ci = mysqli_fetch_assoc($res_ci)){
	$loc_name = getAnyDetails($row_ci['location_code'],"locationname","location_code","location_master",$link1);
	///// calculate part percentage
	$loc_partpercentage = 0;
	$cnt = 0;
	$totpart_per = 0;
	$data_str = "";
	$res_ci2 = mysqli_query($link1,"select partcode,okqty,msl_qty from client_inventory where location_code='".$row_ci['location_code']."'");
	while($row_ci2 = mysqli_fetch_assoc($res_ci2)){
		$part_name = getAnyDetails($row_ci2['partcode'],"part_name","partcode","partcode_master",$link1);
		$part_per = round(($row_ci2["okqty"] / $row_ci2["msl_qty"]) * 100);
		$totpart_per += $part_per;
		if($data_str){
			$data_str .= ",['".$part_name."', ".$part_per."]";
		}else{
			$data_str .= "['".$part_name."', ".$part_per."]";
		}
		$cnt++;
	}
	$loc_partpercentage = round($totpart_per / $cnt);
	if($drill_engperf_str==""){
		$drill_engperf_str.="{ 
					 name: '".$loc_name."',
					 y: ".$loc_partpercentage.",
					 drilldown: '".$loc_name."'
				   }";
	}else{
	  	$drill_engperf_str.=",{ 
					 name: '".$loc_name."',
					 y: ".$loc_partpercentage.",
					 drilldown: '".$loc_name."'
				   }";
	}
	//// make Inner drill string
	if($innerdrill_engperf_str==""){
		$innerdrill_engperf_str.="{ 
		                     name: '".$loc_name."',
							 id: '".$loc_name."', 
							 data: [ 
							     ".$data_str."
							 ]
						}";
	}else{
		$innerdrill_engperf_str.=",{ 
		                     name: '".$loc_name."', 
							 id: '".$loc_name."', 
							 data: [ 
							     ".$data_str."
							 ]
						}";
	}
}
}
/*echo $drill_engperf_str;
echo "<br/>";
echo $innerdrill_engperf_str;*/
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
<?php if($_POST['Submit']=="GO"){ ?>
///////////////// Spare Part MSL
$(function () {
    // Create the chart
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: 'Spare Part MSL'
        },
        subtitle: {
            text: 'Click the columns to view Partwise.'
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Location wise spare availability %'
            }

        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:.0f} %'
                }
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br/>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f} %</b><br/>'
        },

        series: [{
            name: 'Location',
            colorByPoint: true,
            data: [<?=$drill_engperf_str?>]
        }],
        drilldown: {
            series: [<?=$innerdrill_engperf_str?>]
        }
    });
});
<?php }?>
</script>
 <script src="../high/highcharts_new.js"></script>
 <script src="../high/js/modules/data.js"></script>
 <script src="../high/drilldown.js"></script>
 <script src="../high/js/highcharts-3d.js"></script>
<script src="../high/js/modules/exporting.js"></script>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9">
        	<h2 align="center"><i class="fa fa-pie-chart"></i> Spare Part MSL</h2><br/>
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
         <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-6" align="left">
				
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
