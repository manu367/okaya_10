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
$(document).ready(function() {
	var dataTable = $('#po-grid-cancel').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/po-grid-cancel-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>",  "locationname": "<?=$_REQUEST['location']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".po-grid-cancel-error").html("");
				$("#po-grid-cancel").append('<tbody class="po-grid-cancel-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#po-grid-cancel_processing").css("display","none");
				
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
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-shopping-basket"></i> PO Details</h2>
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
         <div class="col-md-6"><label class="col-md-5 control-label">Location Name</label>	  
			<div class="col-md-6">
			 <select id="location" name="location" class="form-control required" required>
             <option value="" >Please Select </option>
			 <?php
				   $location_query="SELECT locationname, location_code FROM location_master where  locationtype not in( 'WH' ,'L4' ,'L3') order by locationname";
     $loc_res=mysqli_query($link1,$location_query);
     while($loc_info = mysqli_fetch_array($loc_res)){?>
				  <option value="<?=$loc_info['location_code']?>" <?php if($_REQUEST['location'] == $loc_info['location_code']) { echo 'selected'; }?>><?=$loc_info['locationname']?></option>
				<?php }  ?>
             </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-5" align="left">
			 
            </div>
          </div>
	    </div><!--close form group-->
        <!--close form group-->
         <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
			   <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="GO">
            </div>
	      </div>
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
              
            </div>
	      </div>
         </div>
		  </form>
      <form class="form-horizontal" role="form">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="po-grid-cancel" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>PO No.</th>
			  <th>PO Date</th>
			  <th>Status</th>
              <th>To Location</th>
              <th>Delete</th>    
			   <th>Edit</th>            
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