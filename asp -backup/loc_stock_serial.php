<?php
//require_once("../includes/config_mis.php");
require_once("../includes/config.php");

/// get access product
$get_accproduct = getAccessProduct($_SESSION['asc_code'],$link1);
/// get access brand
$get_accbrand = getAccessBrand($_SESSION['asc_code'],$link1);
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
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
 <script type="text/javascript" src="../js/ajax.js"></script>
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <!--  -->
  <style type="text/css">
	.modal-dialogTH{
		overflow-y: initial !important
	}
	.modal-bodyTH{
		max-height: calc(100vh - 212px);
		overflow-y: auto;
	}
	.modalTH {
	  width: 1000px;
	  margin: auto;
	}

</style>
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
 function makeDropdown(){
		$('.selectpicker').selectpicker();
	}
/////////// function to get model on the basis of brand
 
 ////// paging script 
$(document).ready(function() {
	var dataTable = $('#joblist-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"order": [[ 0, "DESC" ]],
		"ajax":{
			url :"../pagination/loc-stock-grid-serial.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "partcode": "<?=$_REQUEST['partcode']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".eng-stock-grid-error").html("");
				$("#eng-stock-grid").append('<tbody class="eng-stock-grid-error"><tr><th colspan="15">No data found in the server</th></tr></tbody>');
				$("#eng-stock-grid_processing").css("display","none");
				
			}
		}
	} );
} );
////// function for open model to see the task history
function checkMappedModel(partid){
	$.get('mapped_model.php?partcode=' + partid, function(html){
		 $('#mappedModel .modal-body').html(html);
		 $('#mappedModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}

////// function for open model to see the task history
function checkMappedPart(partid){
	$.get('checkAltPartcode.php?partcode=' + partid, function(html){
		 $('#mappedPart .modal-body').html(html);
		 $('#mappedPart').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
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
      <h2 align="center"><i class="fa fa-cubes"></i> Location Serial Stock </h2>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get" onChange="document.form1.submit();">

        <div class="form-group">
        
		  <div class="col-md-6"><label class="col-md-4 control-label">Partcode</label>	  
			<div class="col-md-6">
            	<div style="display:inline-block;float:left" id="partcodediv">
                    <select name="partcode" id="partcode" class="form-control required selectpicker" required  data-live-search="true" onChange="document.form1.submit();" >
                	<option value="All">All</option>
                    <?php 
					$model_query="SELECT partcode, part_name FROM partcode_master where status='1' order by part_name";
     				$model_res=mysqli_query($link1,$model_query);
     				while($row_model = mysqli_fetch_array($model_res)){
					?>
           			<option value="<?=$row_model['partcode']?>"<?php if($_REQUEST['partcode']==$row_model['partcode']){ echo "selected";}?>><?php echo $row_model['partcode']."( ".$row_model['part_name'].")"?></option>
	 				<?php }?>
             	</select>
                </div>
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
       <?php if ($_REQUEST['Submit']){?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="../excelReports/loc_stock_serial_report.php?rname=<?=base64_encode("inventory_stock_status")?>&rheader=<?=base64_encode("Stock Serial Report")?>&location_code=<?=base64_encode($_SESSION['asc_code'])?>&partcode=<?=base64_encode($_REQUEST['partcode']);?>&loc_type=<?=base64_encode($_SESSION['id_type'])?>" title="Export Login Logout details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Login Logout details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
        <form class="form-horizontal" role="form" name="form2">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="joblist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th>S.No</th>
			  
              <th>Location Code</th>
              <th>Location Name</th>
              <!---<th>Eng. Code</th>
              <th>Eng. Name</th>--->
              <th>Serial No.</th>
              <th>Partcode</th>
              <th>Part Name</th>
               <th>Entry Date</th>
              <th>Status</th>
              <th>Stock Type</th>
             
            </tr>
          </thead>
          </table>
          </div>
      <!--</div>-->
      </form>
      <!-- Start Model Mapped Modal -->
          <div class="modal modalTH fade" id="mappedModel" role="dialog">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center">Model Details</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div><!--close Model Mapped modal-->
          
          
           <!-- Start Alternate Partcode -->
          <div class="modal modalTH fade" id="mappedPart" role="dialog">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center">Alternate Partcode Details</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                <div class="modal-footer">
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div><!--close Alternate Partcode-->
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>