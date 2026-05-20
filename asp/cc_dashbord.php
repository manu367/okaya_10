<?php
include("../includes/config.php");
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";
	
}else{
	$daterange_open = "1";
	
}
//echo "SELECT job_id FROM jobsheet_data WHERE location_code = '".$_SESSION['asc_code']."' and ".	$daterange_open."";
$tcl_cnt = mysqli_num_rows(mysqli_query($link1,"SELECT job_id FROM jobsheet_data WHERE location_code = '".$_SESSION['asc_code']."' and ".	$daterange_open.""));
$tsc_cnt = mysqli_num_rows(mysqli_query($link1,"SELECT job_id FROM jobsheet_data WHERE created_by = '".$_SESSION['userid']."' and ".	$daterange_open.""));
if($tcl_cnt>0){$tsc_per = round(($tsc_cnt / $tcl_cnt)*100);}else{ $tsc_per = 0;} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>
<?=siteTitle?>
</title>
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
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});
$(document).ready(function(){
	Highcharts.chart('container', {
    chart: {
        plotBackgroundColor: null,
        plotBorderWidth: null,
        plotShadow: false,
        type: 'pie'
    },
    title: {
        text: 'Total Call Login And Share Call'
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true
        }
    },
    series: [{
        name: 'Calls',
        colorByPoint: true,
        data: [{
            name: 'Total Share Calls',
            y: <?=$tsc_per?>,
            sliced: true,
            selected: true
        }, {
            name: 'Remaining Calls',
            y: <?=round(100-$tsc_per);?>
        }]
    }]
});
});
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
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bar-chart"></i>Call Details</h2>
      <br/>
	        <form class="form-horizontal" role="form" name="form1" action="" method="post">
	              <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		 
	    </div><!--close form group-->
		  <!--close form group-->
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label"></label>
            <div class="col-md-6" align="left"> </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-5 control-label"></label>
            <div class="col-md-6" align="left">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
              <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
              <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
        </div>
        <!--close form group-->
      </form>
      <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
        <tr>
          <td id="container"></td>
        </tr>
      </table>
      <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
        <tr  class="<?=$tableheadcolor?>">
          <th  colspan="3" style="text-align:center"><label class="control-label">Call Details</label></th>
        </tr>
        <tr >
          <th width="33%"    style="text-align:center"><label class="control-label">Total Call Login</label></th>
          <th width="33%"   style="text-align:center"><label class="control-label">Total Self Call</label></th>
          <th width="34%"   style="text-align:center"><label class="control-label">Total Share Call %</label></th>
        </tr>
        <tr >
          <td style="text-align:center"><?=$tcl_cnt?></td>
          <td style="text-align:center"><?php echo $tsc_cnt;?></td>
          <td  style="text-align:center"><?php echo $tsc_per."%";?></td>
        </tr>
      </table>
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
