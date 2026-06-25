<?php
include("../includes/config.php");
if(isset($_REQUEST['location_code'])){$selrf=$_REQUEST['location_code'];}else{$selrf="";}
///// get access location details
$access_loc = getAccessLocation($_SESSION['userid'],$link1);
$arrstatus = getFullStatus("",$link1);
//// Initialize status variables 
$ongoing = 0;
$planned = 0;
$ready = 0;
$ongoing_array = array();
$planned_array = array();
$ready_array = array();
$array_plno = array();
///// check every planning in job card
$sql1 = "SELECT system_ref_no FROM request_production_p where status!='7'";
$res1 = mysqli_query($link1,$sql1);
while($row1 = mysqli_fetch_assoc($res1)){
	$array_plno[] = $row1['system_ref_no'];
	///check how many plannings are ongoing
	$num2 = mysqli_num_rows(mysqli_query($link1,"SELECT id FROM jobcard_master where planning_no = '".$row1['system_ref_no']."' and start_date <= '".$today."'"));
	if($num2 > 0){
		$ongoing += 1;
		$ongoing_array[] = $row1['system_ref_no'];
	}
	///check how many plannings are planned
	$planned += 1;
	$planned_array[] = $row1['system_ref_no'];
	///check how many plannings are ready
	$num3 = mysqli_num_rows(mysqli_query($link1,"SELECT id FROM jobcard_master where planning_no = '".$row1['system_ref_no']."' and status != '13' and status!='7'"));
	if($num3 == 0){
		$ready += 1;
		$ready_array[] = $row1['system_ref_no'];
	}
}
/////// data arrange for stock status vs planning graph
$array_part = array();
$array_plqty = array();
$array_uom = array();
$array_ref = array();
$res2 =  mysqli_query($link1,"SELECT system_ref_no, partcode, req_qty as planned_qty, purchase_unit FROM request_production_c where system_ref_no in ('".implode("','",$array_plno)."')");
while($row2 = mysqli_fetch_assoc($res2)){
	$array_part[] = $row2["partcode"];
	$array_plqty[] = $row2["planned_qty"];
	$array_uom[] = $row2["purchase_unit"];
	$array_ref[] = $row2["system_ref_no"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title><?=siteTitle?></title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <!--  Light Bootstrap Table core CSS    -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />
    <script src="../js/jquery.js"></script>
 <?php 
 include("../includes/fontawesome.html");
 include("../includes/main_css.html"); 
 include("../includes/bootstrap.html");
 include("../includes/datatables.html");
 ?>
 <style type="text/css">
 .modal-lg{
	 width:auto;
 }
 </style>
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav_admin.php");
    ?>
    	<div class="col-sm-9">
        		<nav class="navbar navbar-default navbar-fixed" style="margin-top:15px;" >
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">Dashboard</a>
                </div>
                <div class="navbar-header" style="float:right;margin-top:15px;">
                <select name="location_code" id="location_code" class="form-control">
					<?php
					$res_maploc = mysqli_query($link1,"select location_code,locationname from location_master where statusid='1' and location_code in (".$access_loc.") and locationtype='STR' order by locationname "); 
					while($row_maploc = mysqli_fetch_assoc($res_maploc)){
						?>
					<option value="<?=$row_maploc['location_code']?>" <?php if($selrf == $row_maploc['location_code']) { echo 'selected'; }?>><?=$row_maploc['locationname']." (".$row_maploc['location_code'].")"?></option>
					<?php } ?>
                </select>
                </div>
                <!--<div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-left">
                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-dashboard"></i>
								<p class="hidden-lg hidden-md">Dashboard</p>
                            </a>
                        </li>
                        <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-globe"></i>
                                    <b class="caret hidden-lg hidden-md"></b>
									<p class="hidden-lg hidden-md">
										5 Notifications
										<b class="caret"></b>
									</p>
                              </a>
                              <ul class="dropdown-menu">
                                <li><a href="#">Notification 1</a></li>
                                <li><a href="#">Notification 2</a></li>
                                <li><a href="#">Notification 3</a></li>
                                <li><a href="#">Notification 4</a></li>
                                <li><a href="#">Another notification</a></li>
                              </ul>
                        </li>
                        <li>
                           <a href="">
                                <i class="fa fa-search"></i>
								<p class="hidden-lg hidden-md">Search</p>
                            </a>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <li>
                           <a href="">
                               <p>Account</p>
                            </a>
                        </li>
                        <li class="dropdown">
                              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                    <p>
										Dropdown
										<b class="caret"></b>
									</p>

                              </a>
                              <ul class="dropdown-menu">
                                <li><a href="#">Action</a></li>
                                <li><a href="#">Another action</a></li>
                                <li><a href="#">Something</a></li>
                                <li><a href="#">Another action</a></li>
                                <li><a href="#">Something</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Separated link</a></li>
                              </ul>
                        </li>
                        <li>
                            <a href="#">
                                <p>Log out</p>
                            </a>
                        </li>
						<li class="separator hidden-lg"></li>
                    </ul>
                </div>-->
            </div>
        </nav>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">

                            <div class="header">
                                <h4 class="title">Production Status</h4>
                                <p class="category"></p>
                            </div>
                            <div class="content">
                                <div id="chartPreferences" class="ct-chart ct-perfect-fourth"></div>

                                <div class="footer">
                                    <div class="legend">
                                        <i class="fa fa-circle text-info"></i> Ready
                                        <i class="fa fa-circle text-success"></i> Ongoing
                                        <i class="fa fa-circle text-warning"></i> Planned
                                    </div>
                                    <!--<hr>
                                    <div class="stats">
                                        <i class="fa fa-clock-o"></i> Production sent 2 days ago
                                    </div>-->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Planning Status</h4>
                                <p class="category"></p>
                            </div>
                            <div class="content">
                                <table class="table tablesorter " id="">
                                    <thead class="text-primary">
                                      <tr>
                                        <th>Status</th>
                                        <th class="text-center">Count</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <!--<tr class="bg-success">
                                        <td>Complete</td>
                                        <td class="text-center"><button title="Click to view Complete planning details" type="button" class="btn btn-success" onClick="openModel('Complete','success');">1</button></td>
                                      </tr>-->
                                      <tr class="bg-success">
                                        <td>Ongoing</td>
                                        <td class="text-center"><button <?php if($ongoing > 0){ ?> title="Click to view Ongoing planning details" onClick="openModel('Ongoing','success');" <?php }?> type="button" class="btn btn-success"><?=$ongoing?></button></td>
                                      </tr>
                                      <tr class="bg-warning">
                                        <td>Planned</td>
                                        <td class="text-center"><button <?php if($planned > 0){ ?> title="Click to view Planned planning details" onClick="openModel('Planned','warning');" <?php }?> type="button" class="btn btn-warning" ><?=$planned?></button></td>
                                      </tr>
                                      <tr class="bg-info">
                                        <td>Ready</td>
                                        <td class="text-center"><button <?php if($ready > 0){ ?> title="Click to view Ready planning details" onClick="openModel('Ready','info');" <?php }?> type="button" class="btn btn-info" ><?=$ready?></button></td>
                                      </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="row">
                    <div class="col-md-12">
                    	<div class="card">
                            <div class="header">
                                <h4 class="title">Stock Status Vs Planning</h4>
                                <p class="category"></p>
                            </div>
                            <div class="content">
                            	<table class="table tablesorter " id="">
                                    <thead class=" text-primary">
                                        <tr>
                                            <th width="40%">Product</th>
                                            <th width="15%">Planning No.</th>
                                            <th width="15%">Planned Qty</th>
                                            <th width="15%">BOM Stock At Store</th>
                                            <th width="15%">Ready Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
									////// run for loop to display all planning product
									for($i = 0; $i < count($array_part); $i++){
										//// get inventory of product on store
										$ready_stock = getInventory($selrf,$array_part[$i],"ok_qty",$link1);
										if(!empty($ready_stock)){ $rdy_stk = $ready_stock;}else{ $rdy_stk = 0;}
									?>
                                   		<tr <?php if($i%2 == 0){ echo 'class="bg-success"'; $cls = "success";}else{ echo 'class="bg-warning"'; $cls = "warning";}?>>
                                        	<td><?php echo getAnyDetails($array_part[$i],"part_name","partcode","partcode_master",$link1)." (".$array_part[$i].")";?></td>
                                        	<td><button <?php if($array_plqty[$i] > 0){ ?> title="Click to view Planned planning details" onClick="openModel3('<?=$array_ref[$i]?>','<?=$array_part[$i]?>','<?=$selrf?>','<?=$cls?>');" <?php }?> type="button" class="btn btn-<?=$cls?>"><?=$array_ref[$i];?></button></td>
                                            <td><?=getQtyFormate($array_plqty[$i],0)?>  <?=$array_uom[$i]?></td>
                                            <td><?=getBomFgSfg($selrf,$array_part[$i],$link1)?>  <?=$array_uom[$i]?></td>
                                            <td><?=getQtyFormate($rdy_stk,0)?>  <?=$array_uom[$i]?></td>
                                    	</tr>
                                    <?php
									}
									?>
                                    </tbody>
                                </table>    
                            </div>
                    	</div>
                    </div>
                </div>

				<div class="row">
                    <div class="col-md-12">
                    	<div class="card">
                            <div class="header">
                                <h4 class="title">BOM Status</h4>
                                <p class="category"></p>
                            </div>
                            <div class="content">
                            	<table class="table tablesorter " id="">
                                    <thead class=" text-primary">
                                        <tr>
                                            <th width="40%">Product</th>
                                            <th width="20%">Ongoing QTY</th>
                                            <th width="20%">Completion</th>
                                            <th width="20%">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
									$i=1;
									$comp_per = 0;
									$res_bom = mysqli_query($link1,"SELECT job_part,SUM(job_qty) AS ongoingqty,purchase_unit,GROUP_CONCAT(system_ref_no) AS jobstr FROM jobcard_master WHERE status IN ('12','18') GROUP BY job_part");
									while($row_bom = mysqli_fetch_assoc($res_bom)){
										////// get part process step count
										/*$part_proc_count = mysqli_fetch_assoc(mysqli_query($link1,"SELECT process_steps FROM part_process_step WHERE partcode LIKE '".$row_bom['job_part']."' AND status='1'"));
										$expl_proc_count = explode(",",$part_proc_count['process_steps']);
										$process_cnt = count($expl_proc_count);
										echo $total_cnt = $process_cnt * round($row_bom['ongoingqty']);*/
										/////get job status done qty
										$job_doneqty = mysqli_fetch_assoc(mysqli_query($link1,"SELECT COUNT(id) AS doneqty FROM job_status WHERE partcode LIKE '".$row_bom['job_part']."' AND job_id IN ('".str_replace(",","','",$row_bom['jobstr'])."') AND pending_qty=0 AND complete_qty > 0"));
										$total_steps = mysqli_num_rows(mysqli_query($link1,"SELECT id FROM job_status WHERE partcode LIKE '".$row_bom['job_part']."' AND job_id IN ('".str_replace(",","','",$row_bom['jobstr'])."')"));
										$comp_per = number_format(($job_doneqty['doneqty']/$total_steps)*100,'2','.','');
									?>
                                   		<tr <?php if($i%2 == 0){ echo 'class="bg-success"'; $cls = "success";}else{ echo 'class="bg-warning"'; $cls = "warning";}?>>
                                        	<td><?php echo getAnyDetails($row_bom['job_part'],"part_name","partcode","partcode_master",$link1)." (".$row_bom['job_part'].")";?></td>
                                            <td><?=getQtyFormate($row_bom['ongoingqty'],0)?>  <?=$row_bom['purchase_unit']?></td>
                                            <td><?=$comp_per;?>%</td>
                                            <td><button title="Click to view job details" onClick="openModel5('<?=base64_encode($row_bom['job_part']);?>','<?=$cls?>');" type="button" class="btn btn-<?=$cls?>">Check Jobs</button></td>
                                    	</tr>
                                    <?php
									$i++;
									}
									?>
                                    </tbody>
                                </table>    
                            </div>
                    	</div>
                    </div>
                </div>
                
                <div class="row">
                	<div class="col-md-12">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home" class="btn btn-warning">IN-PROCESS</a></li>
                        <li><a data-toggle="tab" href="#menu1" class="btn btn-success">COMPLETE</a></li>
                        <li><a data-toggle="tab" href="#menu2" class="btn btn-info">ALL</a></li>
                      </ul>
                    
                      <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                          <h3><div style="float:left; display:inline-block">IN-PROCESS JOBS</div><div style="float:right;display:inline-block;padding-right:25px"><a href="../excelReports/dash_job_details.php?status=<?=base64_encode("12,18")?>" title="Export in-process jobs details in excel"><i class="fa fa-file-excel-o fa-lg faicon excelicon" title="Export in-process jobs details in excel"></i></a></div></h3>
                          <p>
                          <table class="table tablesorter " id="">
                                    <thead class=" text-primary">
                                        <tr>
                                            <th width="20%">Product</th>
                                            <th width="10%">Job QTY</th>
                                            <th width="15%">Planning No.</th>
                                            <th width="15%">Job No.</th>
                                            <th width="10%">Start Date</th>
                                            <th width="10%">End Date</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Process Steps</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
									$res_inproc = mysqli_query($link1,"SELECT job_part,job_qty,planning_no,system_ref_no,start_date,end_date,purchase_unit,status FROM jobcard_master WHERE status IN ('12','18')");
									while($row_inproc=mysqli_fetch_assoc($res_inproc)){
									?>
                                   		<tr class="bg-warning">
                                        	<td><?php echo getAnyDetails($row_inproc['job_part'],"part_name","partcode","partcode_master",$link1)." (".$row_inproc['job_part'].")";?></td>
                                            <td><?=getQtyFormate($row_inproc['job_qty'],0)." ".$row_inproc['purchase_unit'];?></td>
                                        	<td><?=$row_inproc['planning_no'];?></td>
                                            <td><?=$row_inproc['system_ref_no'];?></td>
                                            <td><?=$row_inproc['start_date'];?></td>
                                            <td><?=$row_inproc['end_date'];?></td>
                                            <td><?=$arrstatus[$row_inproc['status']]?></td>
                                            <td><button title="Click to view process steps details" onClick="openModel4('<?=$row_inproc['system_ref_no'];?>','<?=$row_inproc['job_part']?>','warning');" type="button" class="btn btn-warning">Check Process Steps</button></td>
                                    	</tr>
                                    <?php
									}
									?>
                                    </tbody>
                                </table>
                          </p>
                        </div>
                        <div id="menu1" class="tab-pane fade">
                          <h3><div style="float:left; display:inline-block">COMPLETE JOBS</div><div style="float:right;display:inline-block;padding-right:25px"><a href="../excelReports/dash_job_details.php?status=<?=base64_encode("13")?>" title="Export complete jobs details in excel"><i class="fa fa-file-excel-o fa-lg faicon excelicon" title="Export complete jobs details in excel"></i></a></div></h3>
                          <p><table class="table tablesorter " id="">
                                    <thead class=" text-primary">
                                        <tr>
                                            <th width="20%">Product</th>
                                            <th width="10%">Job QTY</th>
                                            <th width="15%">Planning No.</th>
                                            <th width="15%">Job No.</th>
                                            <th width="10%">Start Date</th>
                                            <th width="10%">End Date</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Process Steps</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
									$res_inproc = mysqli_query($link1,"SELECT job_part,job_qty,planning_no,system_ref_no,start_date,end_date,purchase_unit,status FROM jobcard_master WHERE status IN ('13')");
									while($row_inproc=mysqli_fetch_assoc($res_inproc)){
									?>
                                   		<tr class="bg-success">
                                        	<td><?php echo getAnyDetails($row_inproc['job_part'],"part_name","partcode","partcode_master",$link1)." (".$row_inproc['job_part'].")";?></td>
                                            <td><?=getQtyFormate($row_inproc['job_qty'],0)." ".$row_inproc['purchase_unit'];?></td>
                                        	<td><?=$row_inproc['planning_no'];?></td>
                                            <td><?=$row_inproc['system_ref_no'];?></td>
                                            <td><?=$row_inproc['start_date'];?></td>
                                            <td><?=$row_inproc['end_date'];?></td>
                                            <td><?=$arrstatus[$row_inproc['status']]?></td>
                                            <td><button title="Click to view process steps details" onClick="openModel4('<?=$row_inproc['system_ref_no'];?>','<?=$row_inproc['job_part']?>','success');" type="button" class="btn btn-success">Check Process Steps</button></td>
                                    	</tr>
                                    <?php
									}
									?>
                                    </tbody>
                                </table></p>
                        </div>
                        <div id="menu2" class="tab-pane fade">
                          <h3><div style="float:left; display:inline-block">ALL JOBS</div><div style="float:right;display:inline-block;padding-right:25px"><a href="../excelReports/dash_job_details.php?status=<?=base64_encode("")?>" title="Export all jobs details in excel"><i class="fa fa-file-excel-o fa-lg faicon excelicon" title="Export all jobs details in excel"></i></a></div></h3>
                          <p><table class="table tablesorter " id="">
                                    <thead class=" text-primary">
                                        <tr>
                                            <th width="20%">Product</th>
                                            <th width="10%">Job QTY</th>
                                            <th width="15%">Planning No.</th>
                                            <th width="15%">Job No.</th>
                                            <th width="10%">Start Date</th>
                                            <th width="10%">End Date</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Process Steps</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
									$res_inproc = mysqli_query($link1,"SELECT job_part,job_qty,planning_no,system_ref_no,start_date,end_date,purchase_unit,status FROM jobcard_master WHERE 1");
									while($row_inproc=mysqli_fetch_assoc($res_inproc)){
									?>
                                   		<tr class="bg-info">
                                        	<td><?php echo getAnyDetails($row_inproc['job_part'],"part_name","partcode","partcode_master",$link1)." (".$row_inproc['job_part'].")";?></td>
                                            <td><?=getQtyFormate($row_inproc['job_qty'],0)." ".$row_inproc['purchase_unit'];?></td>
                                        	<td><?=$row_inproc['planning_no'];?></td>
                                            <td><?=$row_inproc['system_ref_no'];?></td>
                                            <td><?=$row_inproc['start_date'];?></td>
                                            <td><?=$row_inproc['end_date'];?></td>
                                            <td><?=$arrstatus[$row_inproc['status']]?></td>
                                            <td><button title="Click to view process steps details" onClick="openModel4('<?=$row_inproc['system_ref_no'];?>','<?=$row_inproc['job_part']?>','info');" type="button" class="btn btn-info">Check Process Steps</button></td>
                                    	</tr>
                                    <?php
									}
									?>
                                    </tbody>
                                </table></p>
                        </div>
                        
                      </div>
                    </div>
                </div>
                
        	</div>
    	</div>
	</div>
</div>
<!-- Start Modal  Planning Request view -->
<div class="modal modalTH fade" id="courierModel" role="dialog">
		<div class="modal-dialog modal-dialogTH modal-lg">
  			<!-- Modal content-->
  			<div class="modal-content">
    			<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
      				<h2 class="modal-title" align="center"><i class='fa fa-bar-chart faicon'></i>&nbsp; &nbsp;Planning Details</h2>
    			</div>
    			<div class="modal-body modal-bodyTH">
     				<!-- here dynamic task details will show -->
    			</div>
    			<div class="modal-footer" id="close_btn">
      				<button type="button" id="btnCancel" class="btn<?=$btncolor?>" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>
    			</div> 
  			</div>
		</div>
</div><!--close Modal  Planning Request view -->
<!-- Start Modal  Planning Request view -->
<div class="modal modalTH fade" id="courierModel2" role="dialog">
		<div class="modal-dialog modal-dialogTH modal-lg">
  			<!-- Modal content-->
  			<div class="modal-content">
    			<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
      				<h2 class="modal-title" align="center"><i class='fa fa-wrench faicon'></i>&nbsp; &nbsp;Job Details</h2>
    			</div>
    			<div class="modal-body modal-bodyTH">
     				<!-- here dynamic task details will show -->
    			</div>
    			<div class="modal-footer" id="close_btn2">
      				<button type="button" id="btnCancel" class="btn<?=$btncolor?>" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>
    			</div> 
  			</div>
		</div>
</div><!--close Modal  Planning Request view -->
<!-- Start Modal  Stock Vs Planning view -->
<div class="modal modalTH fade" id="courierModel3" role="dialog">
		<div class="modal-dialog modal-dialogTH modal-lg">
  			<!-- Modal content-->
  			<div class="modal-content">
    			<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
      				<h2 class="modal-title" align="center"><i class='fa fa-wrench faicon'></i>&nbsp; &nbsp;Job Details</h2>
    			</div>
    			<div class="modal-body modal-bodyTH">
     				<!-- here dynamic task details will show -->
    			</div>
    			<div class="modal-footer" id="close_btn3">
      				<button type="button" id="btnCancel" class="btn<?=$btncolor?>" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>
    			</div> 
  			</div>
		</div>
</div><!--close Modal Stock Vs Planning view -->
<!-- Start Modal process steps view -->
<div class="modal modalTH fade" id="courierModel4" role="dialog">
		<div class="modal-dialog modal-dialogTH modal-lg">
  			<!-- Modal content-->
  			<div class="modal-content">
    			<div class="modal-header">
      				<button type="button" class="close" data-dismiss="modal">&times;</button>
      				<h2 class="modal-title" align="center"><i class='fa fa-cogs faicon'></i>&nbsp; &nbsp;Process Steps Details</h2>
    			</div>
    			<div class="modal-body modal-bodyTH">
     				<!-- here dynamic task details will show -->
    			</div>
    			<div class="modal-footer" id="close_btn4">
      				<button type="button" id="btnCancel" class="btn<?=$btncolor?>" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>
    			</div> 
  			</div>
		</div>
</div><!--close Modal process steps view -->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
	<!--  Charts Plugin -->
	<script src="assets/js/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="assets/js/bootstrap-notify.js"></script>

    <!--  Google Maps Plugin    -->
    <!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>-->

    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
	<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>
    <!-- Light Bootstrap Table DEMO methods, don't include it in your project! -->
	<!-- <script src="assets/js/demo.js"></script>-->
    <script type="text/javascript">
	type = ['','info','success','warning','danger'];
	demo = {
		initChartist: function(){
			 var dataPreferences = {
				series: [
					[25, 30, 20, 25]
				]
			};
	
			var optionsPreferences = {
				donut: true,
				donutWidth: 40,
				startAngle: 0,
				total: 100,
				showLabel: false,
				axisX: {
					showGrid: false
				}
			};
	
			Chartist.Pie('#chartPreferences', dataPreferences, optionsPreferences);
	
			Chartist.Pie('#chartPreferences', {
			  labels: ['<?=$ready?>%','<?=$ongoing?>%','<?=$planned?>%'],
			  series: [<?=$ready?>, <?=$ongoing?>, <?=$planned?>]
			});
		},
	showNotification: function(from, align, msg){
    	color = Math.floor((Math.random() * 4) + 1);

    	$.notify({
        	icon: "pe-7s-bell",
        	message: msg

        },{
            type: type[color],
            timer: 4000,
            placement: {
                from: from,
                align: align
            }
        });
	}

}
	</script>
	<script type="text/javascript">
    	$(document).ready(function(){

        	demo.initChartist();

        	$.notify({
            	icon: 'pe-7s-bell',
            	message: "Welcome to <b>Production Dashboard</b>."

            },{
                type: 'info',
                timer: 4000
            });

    	});
		////// function for open modal to view planning details
		function openModel(docid,clas,planarray){
			info = [];
			if(docid == "Ongoing"){ <?php for($i=0; $i<count($ongoing_array); $i++){ ?> info[<?=$i?>] = '<?=$ongoing_array[$i]?>'; <?php }?> }
			if(docid == "Planned"){ <?php for($i=0; $i<count($planned_array); $i++){ ?> info[<?=$i?>] = '<?=$planned_array[$i]?>'; <?php }?> }
			if(docid == "Ready"){ <?php for($i=0; $i<count($ready_array); $i++){ ?> info[<?=$i?>] = '<?=$ready_array[$i]?>'; <?php }?> }
			$.get('dashboard_planning_view.php?doc_id=' + docid + '&selclass=' + clas + '&refarray=' + info, function(html){
				 $('#courierModel .modal-body').html(html);
				 $('#courierModel').modal({
					show: true,
					backdrop:"static"
				});
			 });
			 $("#close_btn").html('<button type="button" id="btnCancel" class="btn btn-'+ clas +'" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
		}
		////// function for open modal to view planning details
		function openModel2(docid,clas,refid){
			$.get('dashboard_planning_view2.php?doc_id=' + docid + '&selclass=' + clas + '&refno=' + refid, function(html){
				 $('#courierModel2 .modal-body').html(html);
				 $('#courierModel2').modal({
					show: true,
					backdrop:"static"
				});
			 });
			 $("#close_btn2").html('<button type="button" id="btnCancel" class="btn btn-'+ clas +'" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
		}
		////// function for open modal to view Stock Vs planning details
		function openModel3(docid,modelid,locid,clas){
			$.get('dashboard_stock_vs_plan.php?doc_id=' + docid + '&selclass=' + clas + '&model_id=' + modelid + '&loc_id=' + locid, function(html){
				 $('#courierModel3 .modal-body').html(html);
				 $('#courierModel3').modal({
					show: true,
					backdrop:"static"
				});
			 });
			 $("#close_btn3").html('<button type="button" id="btnCancel" class="btn btn-'+ clas +'" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
		}
		////// function for open modal to check process steps of jobs
		function openModel4(docid,modelid,clas){
			$.get('dashboard_job_process_steps.php?doc_id=' + docid + '&selclass=' + clas + '&model_id=' + modelid, function(html){
				 $('#courierModel4 .modal-body').html(html);
				 $('#courierModel4').modal({
					show: true,
					backdrop:"static"
				});
			 });
			 $("#close_btn4").html('<button type="button" id="btnCancel" class="btn btn-'+ clas +'" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
		}
		////// function for open modal to view job details for BOM
		function openModel5(docid,clas){
			$.get('dashboard_joblist_view.php?doc_id=' + docid + '&selclass=' + clas, function(html){
				 $('#courierModel3 .modal-body').html(html);
				 $('#courierModel3').modal({
					show: true,
					backdrop:"static"
				});
			 });
			 $("#close_btn3").html('<button type="button" id="btnCancel" class="btn btn-'+ clas +'" data-dismiss="modal"><i class="fa fa-window-close fa-lg"></i> Close</button>');
		}
	</script>
</html>
