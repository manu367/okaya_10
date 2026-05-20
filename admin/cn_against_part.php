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
$(document).ready(function() {
	var dataTable = $('#cn-parts-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 4, "desc" ]],
		"ajax":{
			url :"../pagination/cn-parts-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>", "status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".cn-parts-grid-error").html("");
				$("#cn-parts-grid").append('<tbody class="cn-parts-grid-error"><tr><th colspan="9">No data found in the server</th></tr></tbody>');
				$("#cn-parts-grid_processing").css("display","none");
				
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
    include("../includes/leftnav2.php");
    ?>
	  <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
		  <h2 align="center" style="border-bottom:1px solid #aaa8a8;padding:25px 0px;margin:0px;"><i class="fa fa-money"></i> CN Generate Against Part</h2>
		  <?php if($_REQUEST['msg']){?>
		  <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
			  </button>
			  <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
		  </div>
		  <?php }?>
		  <form class="form-horizontal" role="form" name="form1" action="" method="get">
			  <div class="form-group" style="min-height:49px;">
				  <div class="col-md-6" style="padding:10px 0px;">
					  <div class="col-md-4">
					  </div>
					  <div class="col-md-5" align="left">
					  </div>
					  <div class="col-md-3" align="left">
					  </div>
				  </div>			  
				  <div class="col-md-6" style="padding:10px 0px;">

					  <div class="col-md-10" align="left">
						  <label class="control-label"></label>
					  </div>
					  <div class="col-md-2" align="left">
					  </div>
				  </div>
			  </div>
		  </form>
		  <div class="form-group" style="overflow:hidden;margin:0px;min-height:54px ">
			  <div class="col-md-12" style="padding:10px 0px;">
			  </div>
		  </div>
		  <form class="form-horizontal" role="form">
			  <div class="form-group"  id="page-wrap">
				  <div class="col-md-12">
					  <table  width="100%" id="cn-parts-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
						  <thead>
							  <tr class="<?=$tableheadcolor?>">
								  <th>S.No</th>
								  <th>From Location Name</th>
								  <th>To Location Name</th>
								  <th>Challan No.</th>
								  <th>Entry Date</th>
								  <th>Status</th> 
								  <th>Type</th> 
								  <th>Print</th>   
								  <th>Action</th>             
								  <th>View</th>
							  </tr>
						  </thead>
					  </table>
				  </div>
			  </div>
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