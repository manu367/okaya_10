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

 <script type="text/javascript" language="javascript">

$(document).ready(function() {

	var dataTable = $('#stock-adjust-grid').DataTable( {

		"processing": true,

		"serverSide": true,

		"order": [[ 0, "desc" ]],

		"ajax":{

			url :"../pagination/stock-adjust-grid-data_admin.php", // json datasource

			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>"},

			type: "post",  // method  , by default get

			error: function(){  // error handling

				$(".stock-adjust-grid-error").html("");

				$("#stock-adjust-grid").append('<tbody class="stock-adjust-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');

				$("#stock-adjust-grid_processing").css("display","none");

				

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

      <h2 align="center"><i class="fa fa-adjust"></i> Stock Adjustment</h2>

      <?php if($_REQUEST['msg']){?>

        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                <span aria-hidden="true">&times;</span>

              </button>

            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.

        </div>

        <?php }?>

	  

      <form class="form-horizontal" role="form">
 <button title="Entry" type="button" class="btn<?=$btncolor?>" style="float:right; margin-left:10px;" onClick="window.location.href='eng_stock_adjust_upld.php?op=Add<?=$pagenav?>'"><span>ENG Stock Adjustment By Uploader</span></button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <button title="Entry" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='add_stock_adjustment_admin.php?op=Add<?=$pagenav?>'"><span>Add New Entry</span></button>&nbsp;&nbsp;
		  
		 

        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>

      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->

       <table  width="100%" id="stock-adjust-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">

          <thead>

            <tr class="<?=$tableheadcolor?>">

              <th>S.No</th>

              <th>Location</th>

			   <th>Entry By</th>

			  <th>System Ref No.</th>

			  <th>Entry Date</th>         

			  <th>View</th>

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