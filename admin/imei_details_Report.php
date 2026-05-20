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
 $(document).ready(function() {
	var dataTable = $('#imei-import-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 3, "asc" ]],
		"ajax":{
			url :"../pagination/imei-import-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>" ,"start_date": "<?=$_REQUEST['start_date']?>", "end_date": "<?=$_REQUEST['end_date']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".imei-import-grid-error").html("");
				$("#imei-import-grid").append('<tbody class="imei-import-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#imei-import-grid_processing").css("display","none");
				
			}
		}
	} );
} );
 
$(document).ready(function () {
	$('#start_date').datepicker({
		format: "yyyy-mm-dd",
        todayHighlight: true,
		autoclose: true
	});
});
$(document).ready(function () {
	$('#end_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});
</script>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-ticket"></i> IMEI Details</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">From</label>	  
			<div class="col-md-6 input-append date">
				<div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="start_date"  id="start_date" style="width:150px;" required value="<?php if($_REQUEST['start_date']!= '') echo $_REQUEST['start_date']; else echo $today;?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                  </div>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">To</label>	  
			<div class="col-md-5 input-append date">
                  <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="end_date"  id="end_date" style="width:150px;" required value="<?php if($_REQUEST['end_date']!= '') echo $_REQUEST['end_date']; else echo $today;?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                  </div>
              </div>
          </div>
	    </div>
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
			 <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
              <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
              <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">
               
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
              
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){
	?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="../excelReports/imeidetailsexcel.php?start_date=<?=$_REQUEST['start_date']?>&end_date=<?=$_REQUEST['end_date']?>" title="IMEI details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export IMEI details details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
		<form class="form-horizontal" role="form">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="imei-import-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>IMEI 1</th>
			  <th>IMEI 2</th>
			  <th>Message</th>         
               <th>Contact No.</th>
              <th>Model</th>
			  <th>Location</th>
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