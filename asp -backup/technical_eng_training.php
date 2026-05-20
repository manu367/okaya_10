<?php
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

 ////// paging script 
$(document).ready(function() {
	var dataTable = $('#teclist-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 0, "asc" ]],
		"ajax":{
			url :"../pagination/traing-eng-details.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "location_code": "<?=$_REQUEST['location_code']?>" },
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".teclist-grid-error").html("");
				$("#teclist-grid").append('<tbody class="teclist-grid-error"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
				$("#teclist-grid_processing").css("display","none");
				
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
      <h2 align="center"><i class="fa fa-cubes"></i> Training </h2>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get" onChange="document.form1.submit();">
      
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Engineer Name</label>	  
			<div class="col-md-6">
				<select name="location_code" id="location_code" class="form-control" onChange="document.form1.submit();">
                <option value="">ALL</option>
                <?php
                $res_pro = mysqli_query($link1,"select userloginid,locusername from locationuser_master where location_code='".$_SESSION['asc_code']."'"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                <option value="<?=$row_pro['userloginid']?>" <?php if($_REQUEST['location_code'] == $row_pro['userloginid']) { echo 'selected'; }?>><?=$row_pro['locusername']." (".$row_pro['userloginid'].")"?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6">
            	<div style="display:inline-block;float:left" id="modeldiv">
             
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
               <a href="../excelReports/eng_training_report.php?rname=<?=base64_encode("inventory_stock_status")?>&rheader=<?=base64_encode("Inventory Report")?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>&brand=<?=base64_encode($_REQUEST['brand']);?>&modelid=<?=base64_encode($_REQUEST['modelid'])?>&product_name=<?=base64_encode($_REQUEST['product_name'])?>" title="Export Login Logout details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Login Logout details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
        <form class="form-horizontal" role="form" name="form2">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="teclist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr>
              <th>S.No</th>
			   <th>Training Type</th>
              <th>Description</th>
              <th>Training Start</th>
              <th>Training End</th>
              <th>Score(Out Of 100)</th>
              <th>Trainer Name</th>
		    <th>Engineer Name</th>
             
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