<?php
require_once("../includes/config.php");
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

$array_status = array();
$arr_selstatus = $_REQUEST['job_status'];
for($i=0; $i<count($arr_selstatus); $i++){
	$array_status[$arr_selstatus[$i]] = "Y";
}
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
	var dataTable = $('#schdlist-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/reassign-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>", "info": info, "product_name": "<?=$_REQUEST['product_name']?>", "brand": "<?=$_REQUEST['brand']?>", "location_code": "<?=$_REQUEST['location_code']?>", "modelid": "<?=$_REQUEST['modelid']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".schd-grid-error").html("");
				$("#schd-grid").append('<tbody class="schd-grid-error"><tr><th colspan="15">No data found in the server</th></tr></tbody>');
				$("#schd-grid_processing").css("display","none");
				
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
      <h2 align="center"><i class="fa fa-list-alt"></i>Reassign Call To Engineer</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        
       
        <?php }?>
        
        <?php  if($_REQUEST['to']!='' && ($_REQUEST['status']== 1 || $_REQUEST['status']==6 ))
	{ ?>
<!--<iframe src="http://sms.foxxglove.com/api/mt/SendSMS?user=phonup&password=Pass@123&sende
rid=PHONUP&channel=Trans&DCS=0&flashsms=0&number=<?=$_REQUEST['to']?>&text=<?=$_REQUEST['smsmsg']?>" width="1" height="1" scrolling="No" style="background:#00FF33"></iframe>--><?php }?>
	 
	   
	
       <?php if ($_REQUEST['Submit']){
		  	//// array initialization to send by query string
			  
	?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
            <!--   <a href="excelexport.php?rname=<?=base64_encode("sample_report")?>&rheader=<?=base64_encode("Sample Report")?>&daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>&job_status=<?=base64_encode($statusstr);?>&start_date=<?=$_REQUEST['start_date']?>&end_date=<?=$_REQUEST['end_date']?>" title="Export Login Logout details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Login Logout details in excel"></i></a>-->
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
        <form class="form-horizontal" role="form" name="form2">
        <div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="schdlist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
            
	             <th>S.No</th>
			    <th>Job No.</th>
			    <th>Customer Id.</th>
			    <th>Customer Name</th>
			   <th>Contact No.</th>
              <th>Brand</th>
			   <th>Product Category</th>
              <th>Model</th>
		      <th>Area Type</th>
              
              <th><?php echo SERIALNO ?></th>
			  	   <th>Call Type</th>
              <th>Open Date</th>
              <th>Close Date</th>
           
              <th>Status</th>
			  <th>Engineer Name</th>
			  <th>Reassign</th>
              
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