<?php
	require_once("../includes/config.php");
	////get access product details
	////get access brand details
$arrstate = getAccessState($_SESSION['userid'],$link1);
	
	
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
	$(document).ready(function() {
		$('#example-multiple-selected').multiselect({
				includeSelectAllOption: true,
				buttonWidth:"200",
				enableFiltering: true
		});
	});
	/////////// function to get model on the basis of brand
	  $(document).ready(function(){
		$('#brand').change(function(){
		  var brandid=$('#brand').val();
		  $.ajax({
			type:'post',
			url:'../includes/getAzaxFields.php',
			data:{filterbrand:brandid},
			success:function(data){
			$('#modeldiv').html(data);
			}
		  });
		});
	  });
	 ////// paging script 
	 info = [];
	<?php for($i=0; $i<count($arr_selstatus); $i++){ ?>
	 info[<?=$i?>] = '<?=$arr_selstatus[$i]?>';
	<?php }?>
	$(document).ready(function() {
		var dataTable = $('#joblist-grid').DataTable( {
			"processing": true,
			"serverSide": true,
			"order": [[ 0, "desc" ]],
			"ajax":{
				url :"../pagination/statetat_pending.php", // json datasource
				data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>", "info": info, "state": "<?=$_REQUEST['state']?>", "location_code": "<?=$_REQUEST['location_code']?>", "modelid": "<?=$_REQUEST['modelid']?>"},
				type: "post",  // method  , by default get
				error: function(){  // error handling
					$(".job-grid-error").html("");
					$("#joblist-grid").append('<tbody class="job-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
					$("#job-grid_processing").css("display","none");
					
				}
			}
		} );
	} );
	</script>
<!-- Include multiselect -->
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<!-- Include Date Range Picker -->
<script type="text/javascript" src="../js/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
<!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<title>
<?=siteTitle?>
</title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <?php 
		include("../includes/leftnav2.php");
		?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h3 align="center"><i class="fa fa-check-square"></i> Daily TAT% State Wise </h3>
      <?php if($_REQUEST['msg']){?>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        <strong>
        <?=$_REQUEST['chkmsg']?>
        !</strong>&nbsp;&nbsp;
        <?=$_REQUEST['msg']?>
        . </div>
      <?php }?>
      <form class="form-horizontal" role="form" name="form1" action="" method="get">
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
            <label class="col-md-4 control-label"></label>
            <div class="col-md-5" align="left"> </div>
          </div>
        </div>
        <!--close form group-->
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label">State</label>
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
          <div class="col-md-6">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-6" align="left">
             
            </div>
          </div>
        </div>
        <!--close form group-->
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label"></label>
             <div style="display:inline-block;float:left" id="modeldiv">
          
              </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-6">
          
              <div style="display:inline-block;float:right">
                <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
              </div>
            </div>
          </div>
        </div>
        <!--close form group-->
        <?php if ($_REQUEST['Submit']){		
			 
		
				
			  ?>
        <div class="form-group">
          <div class="col-md-10">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-6" align="left"> 
			
			
			   <a href="excelexport.php?rname=<?=base64_encode("State_tat_pending_report")?>&rheader=<?=base64_encode("State TAT ")?>&modelid=<?=$_GET['modelid']?>&state=<?=$_GET['state']?>&daterange=<?=$_GET['daterange']?>" title="Export employees details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export employees details in excel"></i></a> </div>
          </div>
        </div>
        <!--close form group-->
        <?php }?>
      </form>
      <form class="form-horizontal" role="form" name="form2">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
          <table  width="100%" id="joblist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th>S.No</th>
                <th>State</th>
				 <th>Completed</th>
                <th>Open </th>
                <th>Assign</th>
				   <th>Pending</th>
                <th>cancel</th>
                <th>Replacement</th>
				   <th>0 to 48 (Hr) </th>
                <th>Grand Total</th>
                <th>Tat %</th>
              
             
              </tr>
            </thead>
          </table>
        </div>
        <!--</div>-->
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
