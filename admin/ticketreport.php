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
 
 $(document).ready(function() {
	var dataTable = $('#ticket-report-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 3, "asc" ]],
		"ajax":{
			url :"../pagination/ticket-report-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>" ,"daterange": "<?=$_REQUEST['daterange']?>", "location": "<?=$_REQUEST['location']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".ticket-report-grid-error").html("");
				$("#ticket-report-grid").append('<tbody class="ticket-report-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#ticket-report-grid_processing").css("display","none");
				
			}
		}
	} );
} );

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
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-ticket"></i>Ticket</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <br></br>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	  <div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Location</label>	  
			<div class="col-md-5" align="left">
			<select   name="location" id="location" class="form-control" >
				<option value=''>--Please Select--</option>
				<?php
                $loc = mysqli_query($link1,"select location_code, locationname from location_master where statusid = '1'  "); 
                while($locinfo = mysqli_fetch_assoc($loc)){?>
                <option value="<?=$locinfo['location_code']?>" <?php if($_REQUEST['location'] == $locinfo['location_code']) { echo 'selected'; }?>><?=$locinfo['locationname']?></option>
                <?php } ?>
	</select>
            </div>
          </div>
	    </div><!--close form group-->
    
				
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5" id="location">        
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">  
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
        <?php if ($_REQUEST['Submit']){
		   ?>
           <div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
               <?php
			   ?>
               <a href="../excelReports/ticket_excel.php?location=<?=$_REQUEST['location']?>&daterange=<?=$_REQUEST['daterange']?>" title="Export Ticket details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Ticket details in excel"></i></a>
               <?php
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
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