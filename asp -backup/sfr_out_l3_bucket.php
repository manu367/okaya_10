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
	var dataTable = $('#sfr_bucketlist-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 0, "asc" ]],
		"ajax":{
			url :"../pagination/sfr_bucket-l3-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>","location_code": "<?=$_REQUEST['location_code']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".sfr_bucket-grid-error").html("");
				$("#sfr_bucket-grid").append('<tbody class="sfr_bucket-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
				$("#sfr_bucket-grid_processing").css("display","none");
				
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
      <h2 align="center"><i class="fa fa-list-alt"></i> SFR Bucket</h2>
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
                $res_pro = mysqli_query($link1,"select location_code,locationname from location_master where location_code in (select location_code from map_repair_location where repair_location='".$_SESSION['asc_code']."')"); 
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
               <a href="../excelReports/sfr_bucket_aspreport.php?rheader=<?=base64_encode("Sample Report")?>&daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>&job_status=<?=base64_encode($statusstr);?>&start_date=<?=$_REQUEST['start_date']?>&end_date=<?=$_REQUEST['end_date']?>" title="Export Login Logout details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Login Logout details in excel"></i></a>
            </div>
          </div>
	    </div>
        <?php }?>
        <form class="form-horizontal" role="form" name="form2">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="sfr_bucketlist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr>
              <th>S.No</th>
              <th>To Location</th>
            
             
             <th>Action</th>
           
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