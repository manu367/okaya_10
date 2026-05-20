<?php
require_once("../includes/config.php");
////get access product details
$access_product = getAccessProduct($_SESSION['userid'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
include("../includes/brand_access.php");
$array_status = array();
$arr_selstatus = $_REQUEST['job_status'];
for($i=0; $i<count($arr_selstatus); $i++){
	$array_status[$arr_selstatus[$i]] = "Y";
}

$brandarray=$_REQUEST['brand'];
if($access_brand!=''){
	$brandfiltter="and brand_id in (".$access_brand.")";
}else {
	$brandfiltter="";
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
            //enableFiltering: true
	});
});
$(document).ready(function() {
	$('#brand').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200",
   			//enableFiltering: true
	});
});

/////////// function to get model on the basis of brand
  $(document).ready(function(){
	$('#brand').change(function(){
	  var brandid=$('#brand').val();
	  var brandstr = "";
	  if(brandid.length>0){
		  for(var r=0; r<brandid.length; r++){
			brandstr = brandstr+brandid[r]+"','";
		  }
		  var newStr = brandstr.slice(0, -3);
	  }else{
	  	var newStr = "";
	  }
	  
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{filterbrand_nnn:newStr},
		success:function(data){
		
		//alert(data);
		
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
////// paging script 
 brdd = [];
<?php for($i=0; $i<count($brandarray); $i++){ ?>
 brdd[<?=$i?>] = '<?=$brandarray[$i]?>';
<?php }?>
$(document).ready(function() {
	var dataTable = $('#joblist-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"bStateSave": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/job-grid-data-repl-btr-dms.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>", "info": info, "product_name": "<?=$_REQUEST['product_name']?>", "brdd": brdd, "location_code": "<?=$_REQUEST['location_code']?>", "modelid": "<?=$_REQUEST['modelid']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".job-grid-error").html("");
				$("#joblist-grid").append('<tbody class="job-grid-error"><tr><th colspan="16">No data found in the server</th></tr></tbody>');
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
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h3 align="center"><i class="fa fa-check-square"></i> DMS REPL SERIAL APPROVAL </h3>
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
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   
	  <?php /* ?>
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Pending for approval </label>	  
			<div class="col-md-6 input-append date" align="left">
			 	<input type="checkbox" name="pending"  id="pending"   value="checked"  <?php if($_REQUEST['pending']){echo "checked";}?>>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-5" align="left">
			
            </div>
          </div>
	    </div><?php */ ?><!--close form group-->
		
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label"><!----Job Status----></label>	  
			<div class="col-md-5">
			<div style="display:inline-block;float:right">
                	<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               		<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               		<input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                </div>
            </div>
          </div>
	    </div><!--close form group-->
		

		 <?php if ($_REQUEST['Submit']){		
		 
		//// array initialization to send by query string of  status
			$statusstr = "";
			$arr_status = $_REQUEST['job_status'];
			for($i=0; $i<count($arr_status); $i++){
				if($statusstr){
					$statusstr.="','".$arr_status[$i];
				}else{
					$statusstr.= $arr_status[$i];
				}
			}	
			
			//// array initialization to send by query string of  brand
			$brandstr = "";
			$arr_brand = $_REQUEST['brand'];
			for($i=0; $i<count($arr_brand); $i++){
				if($brandstr){
					$brandstr.="','".$arr_brand[$i];
				}else{
					$brandstr.= $arr_brand[$i];
				}
			}		
			
		  ?>   
		  <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">	
				<!---
               <a href="../excelReports/replacement_report.php?daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['location_code']);?>&status=<?=base64_encode($statusstr);?>&product=<?=base64_encode($_REQUEST['product_name']);?>&brand=<?=base64_encode($brandstr);?>&model=<?=base64_encode($_REQUEST['modelid']);?>&pending=<?=base64_encode($_REQUEST['pending']);?>" title="Export details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export details in excel"></i></a>------>
            </div>
          </div>
	    </div><!--close form group-->
		 
		  <?php }?>
		  <!-------
		  <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">	
               <a href="../excelReports/replacement_report.php" title="Export details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
		  
	  </form>

        <form class="form-horizontal" role="form" name="form2">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="joblist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Job No.</th>
              <th>SAP Material Code</th>
			  <th>CRM Model ID</th>
              <th>REPL Serial No</th>
              <th>Entry Date</th>
              <th>Status</th>
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