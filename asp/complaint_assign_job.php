<?php
require_once("../includes/config.php");
$today=date("Y-m-d");



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
	var dataTable = $('#complaint_assignlist-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 0, "asc" ]],
		"ajax":{
			url :"../pagination/complaint_assign-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>","location_code": "<?=$_REQUEST['location_code']?>", "status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".complaint_assign-grid-error").html("");
				$("#complaint_assign-grid").append('<tbody class="complaint_assign-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#complaint_assign-grid_processing").css("display","none");
				
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
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-list-alt"></i> Assign/Repair</h2>
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
         <div class="col-md-6"><label class="col-md-5 control-label"><?=$locationstr?></label>	  
			<div class="col-md-6">
				<select name="location_code" id="location_code" class="form-control">
                <?php
                $res_pro = mysqli_query($link1,"select location_code,locationname from location_master where 1"); 
				?>
				   <option value="" <?php if($_REQUEST['status']=="all"){ echo "selected"; } ?> >All</option>
				   <?php
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
				                 

                <option value="<?=$row_pro['location_code']?>" <?php if($_REQUEST['location_code'] == $row_pro['location_code']) { echo 'selected'; }?>><?=$row_pro['locationname']." (".$row_pro['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
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
	  </form>
       <?php if ($_REQUEST['Submit']){
	
		
	?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="excelexport.php?rname=<?=base64_encode("sample_report")?>&rheader=<?=base64_encode("Sample Report")?>&daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>&job_status=<?=base64_encode($statusstr);?>&start_date=<?=$_REQUEST['start_date']?>&end_date=<?=$_REQUEST['end_date']?>" title="Export Login Logout details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Login Logout details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
        <form class="form-horizontal" role="form" name="form2">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="complaint_assignlist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr>
              <th>S.No</th>
              <th>Location name.</th>
               <th>Job No.</th>
              <th>IMEI/Serial No.</th>
              <th>Product</th>
              <th>Brand</th>
              <th>Model</th>
              <th>Open Date</th>
              <th>Status</th>
			  <th>Assign</th>
			  <th>Repair</th>
              <th>View</th>
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