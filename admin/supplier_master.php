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
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">

 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
$(document).ready(function() {
	var dataTable = $('#supplier-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :"../pagination/supplier-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".supplier-grid-error").html("");
				$("#supplier-grid").append('<tbody class="supplier-grid-error"><tr><th colspan="9">No data found in the server</th></tr></tbody>');
				$("#supplier-grid_processing").css("display","none");
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
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa  fa-shopping-basket "></i> Supplier Master</h2>
     <?php if($_REQUEST['msg']){?><br>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>   
	    <div class="form-group">
		  <div class="col-md-6">  
			<div class="col-md-5" align="left">
			 
            </div>
          </div>
	    </div><!--close form group-->
      <form class="form-horizontal" role="form">
        <div style="display:inline-block;float:right"><button title="Add Foreign Supplier" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='add_supplier.php?op=Add<?=$pagenav?>'"><span>Add Foreign Supplier</span></button>&nbsp;&nbsp;</div>
        <div style="display:inline-block;float:right"><button title="Add Domestic Supplier" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='add_localsupplier.php?op=Add<?=$pagenav?>'"><span>Add Domestic Supplier</span></button></div>
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="supplier-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Supplier Name</th>
			   <th>City</th>
			   <th>State</th>
			<th>Country</th>
              <th>Contact No.</th>
			  <th>Email</th>
			  <th>Address</th>
              <th>View/Edit</th>
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