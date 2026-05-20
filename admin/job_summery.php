<?php
require_once("../includes/config.php");
$year = $_REQUEST['year'];
$month1 = str_pad($_REQUEST['month'],2,"0",STR_PAD_LEFT);
$tostatenew = $_REQUEST['to_state'];
$arrstate = getAccessState($_SESSION['userid'],$link1);
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
$arr_statestr = $_REQUEST['to_state'];
			for($i=0; $i<count($arr_statestr); $i++){
				if($statestr){
					$statestr.="','".$arr_statestr[$i];
				}else{
					$statestr.= $arr_statestr[$i];
				}
			}
$zoneid=explode("~",$_REQUEST['region']);
if($_REQUEST['region']==''){

$area="";
$statezone="";
}

else{
$area="and area='".$zoneid[0]."'";

$statezone="and zoneid='".$zoneid[1]."'";
}


if($_POST['Submit']=="GO"){

if($_REQUEST['locationcode']==''){

$location="and current_location in (select location_code from location_master where stateid in('".$statestr."'))";
}

else{
$location="and current_location='".$_REQUEST['locationcode']."'";
}



///////////////////////// for month wise Job /////////////////////
if($_REQUEST['type']== 'month' ){
	
	$sql_ct="select job_id from jobsheet_data where  open_date LIKE '$year-$month1-%' $location $area and status not in ('6','10','11','411','12','58','48','49','8')";
	$rs_ct=mysqli_query($link1,$sql_ct);
	$pending=mysqli_num_rows($rs_ct);
	
	$sql_ctc="select job_id from jobsheet_data where  open_date LIKE '$year-$month1-%' and status!='12' $location $area";
	$rs_ctc=mysqli_query($link1,$sql_ctc);
	$total=mysqli_num_rows($rs_ctc);
	
	$sql_cancel="select job_id from jobsheet_data where  '$year-$month1-%'  and status='12' $location $area ";
	$rs_cancel=mysqli_query($link1,$sql_cancel);
	$total_cancel=mysqli_num_rows($rs_cancel);

   $sql = "select count(job_id) as qty,status from jobsheet_data where  open_date LIKE '$year-$month1-%' $location $area  group by status";

$st = mysqli_query($link1,$sql);
$arr_job_month="";
  $innerdrill_str="";
while($row_st= mysqli_fetch_assoc($st)){
		$qty = 0;
		//echo "select * from jobstatus_master where status_id='$row_st[status]'";
		$res_status=mysqli_query($link1,"select * from jobstatus_master where status_id='".$row_st['status']."'");	
		$status_name=mysqli_fetch_array($res_status);
		
		//print_r($proddet);
		//echo $status_name['system_status'];
	 	$qty=$row_st['qty'];
		if ($arr_job_month == ""){
			$arr_job_month.="{ 
		                name: '".$status_name['display_status']."',
						 y: ".$qty.",
					    drilldown: '".$status_name['display_status']."'
					   }";
        }
		 else {
			 $arr_job_month.=",{ 
		                name: '".$status_name['display_status']."',
						 y: ".$qty.",
						 drilldown: '".$status_name['display_status']."'
					   }";
					
		}
			  $sql_inner = "select count(job_id) as qty,status,sub_status from jobsheet_data where  open_date LIKE '$year-$month1-%'  $location  and status='".$row_st['status']."' $area group by sub_status";

$st_inner = mysqli_query($link1,$sql_inner);
$make_substatusstr="";
while($row_inner= mysqli_fetch_assoc($st_inner)){
	$res_status1=mysqli_query($link1,"select * from jobstatus_master where status_id='".$row_inner['sub_status']."'");	
		$status_name1=mysqli_fetch_array($res_status1);
		  //// make Inner drill string
		if($make_substatusstr==""){
			$make_substatusstr.= "['".$status_name1['system_status']."', ".$row_inner['qty']."]";
		}else{
			$make_substatusstr.= ",['".$status_name1['system_status']."', ".$row_inner['qty']."]";
		}
		
	}///close of while loop
	  //// make Inner drill string
	///close of while loop
 	  if($innerdrill_str==""){
		  $innerdrill_str.="{ 
		                     name: '".$status_name['display_status']."',
							 id: '".$status_name['display_status']."', 
							 data: [".$make_substatusstr."]
						}";
	  }else{
		  $innerdrill_str.=",{ 
		                     name: '".$status_name['display_status']."', 
							 id: '".$status_name['display_status']."', 
							 data: [".$make_substatusstr."]
						}";
	

}
		
		
	}
	//print_r($arr_job_month);
}//// while loop close

/////////////////////////end of month wise ////////////////////////


//////////////////////////for year wise sale///////////////////////////
if($_REQUEST['type']=='Date' ){

$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "open_date  >= '".$date_range[0]."' and open_date  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
//echo "select count(job_id) as qty,status from jobsheet_data where  ".$daterange." $location $area group by status";
		$res_qry=mysqli_query($link1,"select count(job_id) as qty,status from jobsheet_data where  ".$daterange." $location $area group by status");	
	  
           $sql_ct="select job_id from jobsheet_data where  ".$daterange." $location $area and status not in ('6','10','11','411','12','58','48','49','8')";	  
	       $rs_ct=mysqli_query($link1,$sql_ct);
	$pending=mysqli_num_rows($rs_ct);
	
	$sql_ctc="select job_id from jobsheet_data where  ".$daterange." and status!='12' $location $area ";
	$rs_ctc=mysqli_query($link1,$sql_ctc);
	$total=mysqli_num_rows($rs_ctc);
	
		$sql_cancel="select job_id from jobsheet_data where  ".$daterange." and status='12' $location $area ";
	$rs_cancel=mysqli_query($link1,$sql_cancel);
	$total_cancel=mysqli_num_rows($rs_cancel);
    
      	       
	       
	       
	$arr_model = "";
	$innerdrill_str_year="";
	while($row_qry= mysqli_fetch_assoc($res_qry)){
		$qty = 0;
	//	$proddet=explode("~",getProductDetails($row_qry['model'],"productname,productcolor", $link1));
		//print_r($proddet);
			$res_status=mysqli_query($link1,"select * from jobstatus_master where status_id='".$row_qry['status']."'");	
		$status_name=mysqli_fetch_array($res_status);
		 $qty=$row_qry['qty'];
		if ($arr_model == ""){
			$arr_model.="{ 
		                name: '".$status_name['display_status']."',
						 y: ".$qty.",
						  drilldown: '".$status_name['display_status']."'
					
					   }";
        }
		 else {
			 $arr_model.=",{ 
		                name: '".$status_name['display_status']."',
						 y: ".$qty.",
						  drilldown: '".$status_name['display_status']."'
					   }";
					
		}
		
		
			  $sql_inner1 = "select count(job_id) as qty,status,sub_status from jobsheet_data where   ".$daterange."  $location and status='".$row_qry['status']."' group by sub_status";

$make_substatusstr="";
$st_inner1 = mysqli_query($link1,$sql_inner1);
while($row_inner1= mysqli_fetch_assoc($st_inner1)){
	$res_status1=mysqli_query($link1,"select * from jobstatus_master where status_id='".$row_inner1['sub_status']."'");	
	$status_name1=mysqli_fetch_array($res_status1);
	  //// make Inner drill string
		if($make_substatusstr==""){
			$make_substatusstr.= "['".$status_name1['system_status']."', ".$row_inner1['qty']."]";
		}else{
			$make_substatusstr.= ",['".$status_name1['system_status']."', ".$row_inner1['qty']."]";
		}
		
	}///close of while loop
 	  if($innerdrill_str_year==""){
		  $innerdrill_str_year.="{ 
		                     name: '".$status_name['display_status']."',
							 id: '".$status_name['display_status']."', 
							 data: [".$make_substatusstr."]
						}";
	  }else{
		  $innerdrill_str_year.=",{ 
		                     name: '".$status_name['display_status']."', 
							 id: '".$status_name['display_status']."', 
							 data: [".$make_substatusstr."]
						}";
	
	
}
}
}
}
//////////////////////////////close of year wise/////////////////////////
//echo "<br/>fdff";

?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script src="../high/js/highcharts.js"></script>
<script src="../high/js/modules/data.js"></script>
<script src="../high/js/modules/drilldown.js"></script>
<script src="../high/js/highcharts-3d.js"></script>
<script src="../high/js/modules/exporting.js"></script>
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
 <!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <!--  -->
 <script>
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});
$(document).ready(function(){
    $('#myTable').dataTable();
});

$(document).ready(function() {
	$('#to_state').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});
</script>
<?php if($_POST['Submit']=="GO"){ ?>
<script type="text/javascript">

$(function() {

	 <?php if ($_REQUEST['type'] == 'month') {?>
    // Create the chart
    $('#container').highcharts({
		

	
        chart: {
            type: 'pie'
        },
        title: {
            text: 'Job Status'
        },
        subtitle: {
            text: 'Click the slices to view Job status count'
        },
        plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y:.0f}'
                }
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b> of total<br/>'
        },
        series: [{
            name: 'Jobs',
            colorByPoint: true,
            data: [<?=$arr_job_month?>]
        }],
        drilldown: {
            series: [<?=$innerdrill_str?>]
        }
    });	
	
	
	
   <?php } if ($_REQUEST['type'] == 'Date') { ?>

	$('#container1').highcharts({
		
		
		
		        chart: {
            type: 'pie'
        },
        title: {
            text: 'Job Status'
        },
        subtitle: {
            text: 'Click the slices to view Job status count'
        },
        plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.name}: {point.y:.0f}'
                }
            }
        },

        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b> of total<br/>'
        },
        series: [{
            name: 'Jobs',
            colorByPoint: true,
            data: [<?=$arr_model?>]
        }],
        drilldown: {
            series: [<?=$innerdrill_str_year?>]
        }
    });	
		
    
<?php } ?>
});


</script>
<?php }?>

</head>
<body>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-pie-chart"></i>Job  Summary</h2><br/><br/>
	  <form class="form-horizontal" role="form" name="form1" id="form1"  action="" method="post">
	  
	  
	  		       <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Region</label>
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
            </div>
            
          <div class="col-md-6"><label class="col-md-2 control-label"> State</label>
            <div class="col-md-6">
           <select   name="to_state[]" id="to_state" class="form-control required" required multiple="multiple" onChange="document.form1.submit();">
				   
				<?php 
                $state = mysqli_query($link1,"select stateid, state from state_master  where stateid in ($arrstate) $statezone order by state" ); 
				
                while($stateinfo = mysqli_fetch_assoc($state)){ 
				?>		
             <option value="<?=$stateinfo['stateid']?>" <?php  for($i=0; $i<count($tostatenew); $i++){ if($tostatenew[$i] == $stateinfo['stateid']) { echo 'selected'; } }?>><?=$stateinfo['state']?></option>
                <?php }?>
	</select>
              </div>
            <div class="col-md-2">
            
              </div>
          </div>
	   </div><!--close form group-->
      <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Period Type</label>
              <div class="col-md-6">
                <select name="type" id="type" class="form-control required" required onChange="document.form1.submit();" >
                    <option value="" selected="selected">--Select--</option>
                    <option value="Date" <?php if ($_REQUEST['type'] == 'Date') echo ' selected="selected"'; ?>>Date Wise</option>
                    <option value="month" <?php if ($_REQUEST['type'] == 'month') echo ' selected="selected"'; ?>>Month</option>
                </select>
              </div>
            </div>
                
          <div class="col-md-6"><label class="col-md-2 control-label"> Location </label>
            <div class="col-md-6">
        <select name="locationcode" id="locationcode" class="form-control">
				 <option value=""<?php if($_REQUEST['locationcode']=="") { echo 'selected'; }?>>All</option>
                <?php
                $res_pro = mysqli_query($link1,"select location_code,locationname from location_master where statusid='1' and stateid in( '".$statestr."') order by locationname "); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
				
                <option value="<?=$row_pro['location_code']?>" <?php if($_REQUEST['locationcode'] == $row_pro['location_code']) { echo 'selected'; }?>><?=$row_pro['locationname']." (".$row_pro['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
            <div class="col-md-2">
            
              </div>
          </div>
	   </div><!--close form group-->

<?php   if ($_REQUEST['type'] == 'Date') {?> 
	  
	  		       <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Date</label>
              <div class="col-md-6">
             	 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
              </div>
            </div>
            
          <div class="col-md-6"><label class="col-md-2 control-label"> </label>
            <div class="col-md-6">
        
              </div>
            <div class="col-md-2">
            
              </div>
          </div>
	   </div><!--close form group-->

<?php } if ($_REQUEST['type'] == 'month') {?> 


 <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label">Year</label>
              <div class="col-md-6">
             	   <select name="year" class="form-control " style="width:100px;" id="year">
              
                        <?php for ($i = 0, $j = 19; $i < 9; $i++, $j++) { ?>
                        <option value="<?php echo '20' . $j; ?>" <?php if ($_POST['year'] == '20' . $j) echo ' selected="selected"'; ?>><?php echo '20' . $j; ?></option>
                    <?php } ?>
                </select>         
              </div>
            </div>
            
          <div class="col-md-6"><label class="col-md-2 control-label"> Month</label>
            <div class="col-md-6">
          <select name="month" id="month" class="form-control " style="width:120px;">
                        <option value="" selected="selected">--Select--</option><?php
                       for ($i = 0, $m2 = 1; $i < 12; $i++, $m2++) {
       $m = date("F", mktime(0, 0, 0, $m2, 10));
                            ?>
                            <option value="<?php echo $m2; ?>" <?php if ($_POST['month'] == $m2) echo ' selected="selected"'; ?>><?php echo $m; ?></option>
                        <?php } ?>
                    </select>
              </div>
            <div class="col-md-2">
            
              </div>
          </div>
	   </div><!--close form group--><?php }?> 
       <div class="form-group">
            <div class="col-md-6"><label class="col-md-6 control-label"></label>
              <div class="col-md-6">
             	
              </div>
            </div>
            
          <div class="col-md-6"><label class="col-md-2 control-label"> </label>
            <div class="col-md-6">
            
              </div>
            <div class="col-md-2">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                  <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
              </div>
          </div>
	   </div><!--close form group-->
	  </form>
         <?php if($_POST['Submit']=="GO"){ ?>
        <?php if ($_REQUEST['type'] == 'month') {?>
        <div id="container" style="height: 400px; width: 500px; margin: 0 auto; border-bottom: solid; border-top: solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
         <?php } if ($_REQUEST['type'] == 'Date') { ?>
        <div id="container1" style="height: 400px; width: 500px; margin: 0 auto; border-bottom: solid; border-top: solid; border-top-left-radius: 30px;  border-top-right-radius: 30px; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; border-color: #0099CC";></div>
        <?php }?> 
       
      <br>
  <center>    
  <table class="table table-bordered" width="50%">
<tr>
    <td width="20%"><label class="control-label">Pending Calls:</td><td width="30%"><?=$pending?></td></tr>
<tr> <td width="20%"><label class="control-label">Close Calls:</td><td width="30%"><?=$total-$pending?></td></tr>
<tr> <td width="20%"><label class="control-label">Cancel Calls:</td><td width="30%"><?=$total_cancel?></td></tr>
<tr> <td width="20%"><label class="control-label">Total Calls:</td><td width="30%"><?=$total+$total_cancel?></td></tr>
<tr> <td width="20%"><label class="control-label">Close Calls %:</td><td width="30%"><?php $per=($total-$pending)/$total*100; echo number_format($per,2);?></td></tr>
</table></center>
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