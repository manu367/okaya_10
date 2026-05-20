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
	var dataTable = $('#po-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/po-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>", "status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".po-grid-error").html("");
				$("#po-grid").append('<tbody class="po-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#po-grid_processing").css("display","none");
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
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-shopping-basket"></i>Purchase Order</h2>
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
			 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label">PO Status</label>	  
			<div class="col-md-5" align="left">
			  <select id="status"  name="status" class="form-control">
			  <option value=''>--Please Select-</option>
			   <option value="1" <?php if($_REQUEST['status'] == "1") { echo 'selected'; }?>>Pending</option>
               <option value="2" <?php if($_REQUEST['status'] == "2") { echo 'selected'; }?>>Processed</option>
               <option value="3" <?php if($_REQUEST['status'] == "3") { echo 'selected'; }?>>Dispatched</option>
               <option value="4" <?php if($_REQUEST['status'] == "4") { echo 'selected'; }?>>Received</option>
			   <option value="5" <?php if($_REQUEST['status'] == "5"){ echo 'selected'; }?>>Cancelled</option>				
			</select>
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
               <a href="po_report.php?daterange=<?=$_REQUEST['daterange']?>&status=<?=$_REQUEST['status']?>" title="Export PO details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export PO details in excel"></i></a>
               <?php
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
		 <?php }?>
	  
      <form class="form-horizontal" role="form">
<button title="Add New PO" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='inventory_po_add.php?op=Add<?=$pagenav?>'"><span>Add New PO</span></button> <br/><br/> &nbsp;&nbsp;
		 &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
<button title="Add New PO" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='po_uploader.php?op=Add<?=$pagenav?>'"><span>Add PO By Uploader</span></button>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;<br/>
     &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;
       <!-- <div class="form-group"  id="page-wrap" style="margin-left:10px;">-->
		
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="po-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>PO No.</th>
			  <th>PO Date</th>
			  <th>Ageing</th>
			  <th>Status</th>
              <th>To Location</th>
              <th>Action</th>
              <th>View</th>
			  <th>Print</th>
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