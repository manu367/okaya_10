<?php
require_once("../includes/config.php");
$id= base64_decode($_REQUEST['refid']);
/////get master data
$query="select * from advance_docket_assign where id='$id' ";
$result=mysqli_query($link1,$query)or die("error1".mysqli_error($link1));
$show_result=mysqli_fetch_assoc($result);
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
		<script type="text/javascript" language="javascript">
			$(document).ready(function() {
				var dataTable = $('#joblist-grid').DataTable( {
					"processing": true,
					"serverSide": true,
					"bStateSave": true,
					"order": [[ 0, "desc" ]],
					"ajax":{
						url :"../pagination/view_advance_docket_upload-grid-data.php", // json datasource
						data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>","doc_no": "<?=$show_result['doc_no']?>"},
						type: "post",  // method  , by default get
						error: function(){  // error handling
							$(".job-grid-error").html("");
							$("#job-grid").append('<tbody class="job-grid-error"><tr><th colspan="5">No data found in the server</th></tr></tbody>');
							$("#job-grid_processing").css("display","none");
							
						}
					}
				} );
			} );
		</script>
		<title><?=siteTitle?></title>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row content">
				<?php 
				include("../includes/leftnav2.php");
				?>
				<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
					<h2 align="center"><i class="fa fa-tags"></i>View Advance Docket Upload</h2>
					<form class="form-horizontal" role="form" name="form1" action="" method="get">
						<div class="form-group">
							<div class="col-md-6"><label class="col-md-5 control-label">Assign From</label>	  
								<div class="col-md-6" align="left">
								<input type="text" name="assign_to" class="form-control" id="assign_from" value="<?=$show_result['assign_from']?>" disabled/>
								</div>
								 
							</div>
							<div class="col-md-6"><label class="col-md-4 control-label">Assign To</label>	  
								<div class="col-md-6" align="left">
								<input type="text" name="assign_to" class="form-control" id="assign_to" value="<?=$show_result['assign_to']?>" disabled/>
								
								</div>
							</div>
						</div><!--close form group-->
						<div class="form-group">
							<div class="col-md-6"><label class="col-md-5 control-label">Doc No. </label>	  
								<div class="col-md-6">
								<input type="text" name="doc_no" class="form-control" id="doc_no" value="<?=$show_result['doc_no']?>" disabled/>
								</div>
							</div>
							<div class="col-md-6"><label class="col-md-4 control-label">Doc Date</label>	  
								<div class="col-md-6">
									<div style="display:inline-block;float:left" id="modeldiv">
										<input type="text" name="doc_date" class="form-control" id="doc_date" value="<?=$show_result['doc_date']?>" disabled/>
									</div>	
								</div>
							</div>
						</div><!--close form group-->
                        <div class="form-group">
							<div class="col-md-6"><label class="col-md-5 control-label">Entry By</label>	  
								<div class="col-md-6" align="left">
								<input type="text" name="assign_by" class="form-control" id="assign_by" value="<?=$show_result['assign_by']?>" disabled/>
								</div>
							</div>
							<div class="col-md-6"><label class="col-md-4 control-label">Status</label>	  
								<div class="col-md-6" align="left">
								<input type="text" name="status" class="form-control" id="status" value="<?=$show_result['status']?>" disabled/>
								</div>
							</div>
						</div><!--close form group-->
					</form>
					<form class="form-horizontal" role="form" name="form2">
						<div class="form-group"  id="page-wrap" style="margin-left:10px;">
							<!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
                            <table  width="100%" id="joblist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
									<thead>
									<tr class="<?=$tableheadcolor?>">
										<th>S.No</th>
										<th>Docket No.</th>
										<th>Docket Company</th>
										<th>Mode Of Transport</th>
										<th>Response Msg</th>
									</tr>
								</thead>
							</table>
						</div><!--</div>-->
					</form>
                    <div class="col-md-12">	  
								<div class="col-md-6">
									<div style="display:inline-block;float:right">
                                    <a href="advance_docket_upload.php?&daterange=<?=$_REQUEST['daterange']?>&status=<?=$_REQUEST['status']?>&assign_to=<?=$_REQUEST['assign_to']?><?=$pagenav?>" class="btn btn-primary">Back</a>
                                    </div>
								</div>
							</div>
				</div>
			</div>
		</div>
		<?php
			include("../includes/footer.php");
			include("../includes/connection_close.php");
		?>
	</body>
</html>