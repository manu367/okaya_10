<?php
require_once("../includes/config.php");
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

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
<!-- datatable plugin-->
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<!--  -->
<script>
	$(document).ready(function(){
        $("#frm").validate();
    });
</script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" >
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$(document).ready(function() {
	var dataTable = $('#pna-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 3, "asc" ]],
		"ajax":{
			url :"../pagination/pnabucketbrand-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".pna-grid-error").html("");
				$("#pna-grid").append('<tbody class="pna-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#pna-grid_processing").css("display","none");
				
			}
		}
	} );
} );

</script>
<title>
<?=siteTitle?>
</title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-shopping-basket"></i> PNA Bucket Brand Wise </h2>
      <?php if($_REQUEST['msg']){?>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        <strong>
        <?=$_REQUEST['chkmsg']?>
        !</strong>&nbsp;&nbsp;
        <?=$_REQUEST['msg']?>
        . </div>
      <?php }?>
      <form class="form-horizontal" role="form" id="frm" name="frm" method="post" >
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/>
          <br/>
          <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
          <table  width="100%" id="pna-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
            <thead>
              <tr>
                <th><div align="center">S.No</div></th>
                <th><div align="center">Brand name</div></th>
                <th><div align="center">Job Counter</div></th>
                <th><div align="center">View</div></th>
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