<?php
require_once("../includes/config.php");
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
		<!-- datatable plugin-->
		<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
		<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
		<!--  -->
		<script type="text/javascript" language="javascript" >
			$(document).ready(function(){
				$('input[name="daterange"]').daterangepicker({
					locale: {
						format: 'YYYY-MM-DD'
					}
				});
			});			
			////// paging script 
			$(document).ready(function() {
				var dataTable = $('#joblist-grid').DataTable( {
					"processing": true,
					"serverSide": true,
					"bStateSave": true,
					"order": [[ 0, "desc" ]],
					"ajax":{
						url :"../pagination/Advance_Docket_Receive-grid-data.php", // json datasource
						data: {  "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>", "assign_to": "<?=$_REQUEST['assign_to']?>", "status": "<?=$_REQUEST['status']?>"},
						type: "post",  // method  , by default get
						error: function(){  // error handling
							$(".job-grid-error").html("");
							$("#job-grid").append('<tbody class="job-grid-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
							$("#job-grid_processing").css("display","none");
							
						}
					}
				} );
			} );
		</script>
		<!-- Include Date Range Picker -->
		<script type="text/javascript" src="../js/daterangepicker.js"></script>
		<link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
		<title><?=siteTitle?></title>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row content">
				<?php 
				include("../includes/leftnavemp2.php");
				?>
				<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
					<h2 align="center"><i class="fa fa-tags"></i> Advance Docket Receive</h2>
					<?php if($_REQUEST['msg']){?>
						<div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
							<strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
						</div>
					<?php }?>
					<form class="form-horizontal" role="form" name="form1" action="" method="get">
						<div class="form-group">
							<div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
								<div class="col-md-6 input-append date" align="left">
									<div style="display:inline-block;float:left">
										<input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/>
									</div>
									<div style="display:inline-block;float:right">
										<i class="fa fa-calendar fa-lg"></i>
									</div>
								</div>
							</div>
							<div class="col-md-6"><label class="col-md-4 control-label">ASP NAME</label>	  
							<div class="col-md-6" align="left">
									<select name="assign_to" id="aspname" class="form-control" onChange="document.form1.submit();">
										<?php
											$res_asp = mysqli_query($link1,"SELECT location_code, locationname FROM location_master WHERE locationtype='ASP' AND location_code='".$_SESSION['asc_code']."' AND statusid='1' ORDER BY locationname");
											while($row_asp=mysqli_fetch_assoc($res_asp))
											{
										?>
										<option value="<?=$row_asp['location_code'];?>"<?php if($_REQUEST['assign_to']==$row_asp['location_code']){ echo 'selected';} ?>><?=$row_asp['locationname']." (".$row_asp['location_code'].")";?></option>
										<?php }?>
										
									</select>
								</div>
							</div>
						</div><!--close form group-->
						<div class="form-group">
							<div class="col-md-6"><label class="col-md-5 control-label">Status</label>	  
								<div class="col-md-6" align="left">
									<select name="status" id="status" class="form-control"  onChange="document.form1.submit();">
                                        <option value=""<?php if($_REQUEST['status']==''){ echo "selected";}?>>All</option>
                                        <option value="Pending"<?php if($_REQUEST['status']=='Pending'){ echo "selected";}?>>Pending</option>
                                        <option value="Received"<?php if($_REQUEST['status']=='Received'){ echo "selected";}?>>Received</option>
									</select>
								</div>
							</div>	
                            <div class="col-md-6">	  
								<div class="col-md-6">
									<div style="display:inline-block;float:right">
										<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
										<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
										<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
									</div>
								</div>
							</div>
						</div><!--close form group-->
						
					</form>
                    <?php if ($_REQUEST['Submit']){	  
					?>
					<div class="form-group">
						<div class="col-md-10"><label class="col-md-4 control-label"></label>	  
							<div class="col-md-6" align="left">
								<a href="../excelReports/advance_docket_report.php?daterange=<?=$_REQUEST['daterange']?>&status=<?=$_REQUEST['status']?>&assign_to=<?=$_REQUEST['assign_to']?>" title="Export Advance Docket Details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Advance Docket Details in excel"></i></a>
							</div>
						</div>
					</div><!--close form group-->
					<?php }?>
					<form class="form-horizontal" role="form" name="form2">
						<div class="form-group"  id="page-wrap" style="margin-left:10px;">
							<!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
								<table  width="100%" id="joblist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
									<thead>
									<tr class="<?=$tableheadcolor?>">
										<th>S.No</th>
										<th>Assign From</th>
										<th>Assign To</th>
										<th>Doc No.</th>
										<th>Doc. Date</th>
										<th>Status</th>
										<th>View/Receive</th>
									</tr>
								</thead>
							</table>
						</div><!--</div>-->
					</form>
				</div>
			</div>
		</div>
		<?php
			include("../includes/footer.php");
			include("../includes/connection_close.php");
		?>
	</body>
</html>