<?php
require_once("../includes/config.php");
$today=date("Y-m-d");
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewpaymentrt" content="width=device-width, initial-scale=1">
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
$(document).ready(function() {
	var dataTable = $('#payment-receive-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 9, "desc" ]],
		"ajax":{
			url :"../pagination/payment-receive-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".payment-receive-grid-error").html("");
				$("#payment-receive-grid").append('<tbody class="payment-receive-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#payment-receive-grid_processing").css("display","none");
				
			}
		}
	} );
} );
</script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-money"></i> Payment Details</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
		
		 <div class="form-group">
		  <div class="col-md-10"><label class="col-md-10 control-label"></label>	  
			<div class="col-md-10" align="right">     
               <a href="../excelReports/whpayment_receivereport.php?location=<?=$_SESSION['asc_code']?>" title="Export Account details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Account details in excel"></i></a>
             
            </div>
          </div>
	    </div>
		
      <form class="form-horizontal" role="form">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-respaymentnsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="payment-receive-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>From Location</th>
			 <th>Document No.</th>
			  <th>Document Date</th>
			  <th>Payment Mode</th>
              <th>Amount</th>
               <th>Status</th>
              <th>Attachment</th>
			  <th>Receive</th>
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