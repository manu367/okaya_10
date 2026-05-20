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
	var dataTable = $('#claim-job').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/travel_claim-job-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>","location_code": "<?=$_REQUEST['location_code']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".claim-job-error").html("");
				$("#claim-job").append('<tbody class="claim-job-error"><tr><th colspan="10">No data found in the server</th></tr></tbody>');
				$("#claim-job_processing").css("display","none");
				
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
      <h2 align="center"><i class="fa fa-balance-scale"></i> Claim Approval</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
          </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
		<br><br/>
	  <form class="form-horizontal" role="form" name="form1" method="post">	   
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label">Location Name</label>	  
			<div class="col-md-5">
			  <select name="location_code" id="location_code" class="form-control required">
                  <option value="">All</option>
                  <?php
                $res_pro = mysqli_query($link1,"select location_code,locationname from location_master where locationtype != 'WH' and locationtype != 'CC'"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['location_code']?>" <?php if($_REQUEST['location_code'] == $row_pro['location_code']) { echo 'selected'; }?>>
                  <?=$row_pro['locationname']." (".$row_pro['location_code'].")"?>
                  </option>
                  <?php } ?>
              </select>
            </div>
          </div>
	    </div><!--close form group-->
		
        <!--close form group-->
         <div class="form-group">
          <div class="col-md-6">
            <div class="col-md-6">
			
		    </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-5">
   
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
			  <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!"> 
      
          </div>
	    </div>
		</div>
	  </form>
     &nbsp;&nbsp;
   		   <?php if ($_REQUEST['Submit']){
		   ?>
         <div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
               <?php
			    //// get excel section id ////
				//$sectionid=getExlCnclProcessid("Admin Users",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$sectionid,$link1)==1){
			   ?>
               <a href="../excelReports/claim_job_excel.php?daterange=<?=$_REQUEST['daterange']?>&status=<?=$_REQUEST['status']?>" title="Export Claim details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export PO details in excel"></i></a>
               <?php
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
		
           	<form class="form-horizontal" role="form" method="post"  action="claim_app_update.php" >
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="claim-job" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
                 <th>S.No</th>
                 <th>Location name</th>
                 <th>Job No</th>
                 <th>Travel By</th>
				 <th>Travel (In KM)</th>
				  <th>Close TAT</th>
				   <th>Area Type</th>
                 <th>Remark </th>
                 <th>Approval Status</th>
                 <th>Details</th>
            </tr>
          </thead>
          </table>
          </div>
                       <input type="submit" name="save" id="save"  class="btn btn<?=$btncolor?>" value="SAVE"/>
      <!--</div>--> <?php }?>
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