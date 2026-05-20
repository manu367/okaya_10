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
/*$(document).ready(function(){
    $('#myTable').dataTable();
});*/
$(document).ready(function() {
	var dataTable = $('#admin-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/permissionlog-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".admin-grid-error").html("");
				$("#admin-grid").append('<tbody class="admin-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
				$("#admin-grid_processing").css("display","none");
				
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
      <h2 align="center" style="border-bottom:1px solid #aaa8a8;padding:25px 0px;margin:0px;"><i class="fa fa-history"></i> Permission Log</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
		<form class="form-horizontal" role="form">
			<div class="form-group tab-area" id="page-wrap">
				<div class="col-md-12">
					<table  width="100%" id="admin-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
						<thead>
							<tr class="<?=$tableheadcolor?>">
								<th>#</th>
								<th>Username</th>
								<th>Type</th>
								<th>By User</th>
								<th>Date Time</th>
								<th>IP</th>
								<th style="text-align:center;">View</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</form>
		<div style="padding:15px;background:#e3e3e3;overflow:hidden;">
			<button title="View Status Log" type="button" class="btn<?=$btncolor?>" style="float:left;margin-right:5px;padding:5px 15px;background:#2e353d;" onClick="window.location.href='adminusermgt.php'"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i><span> Go Back</span></button>
		</div>
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<!-- Modal -->
<div class="modal fade" id="vP" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="vPTitle"></h5>
      </div>
      <div id="vPBody" class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
function loadItem(t,u,x,y)
{
    var request = new XMLHttpRequest();
    request.open('GET', 'permission_view.php?i='+x, true);
    request.send(null);
    request.onreadystatechange=function()
	{
        if(request.readyState === 4 && request.status === 200)
		{
			$('#vPTitle').html('<b><i class="fa fa-link" aria-hidden="true"></i> '+atob(t)+" - "+u+' ('+atob(y)+')</b>');
			$('#vPBody').html('<p>'+atob(t)+' permissions updated and user got permission to access following things: </p>'+request.responseText); 
			$('#vP').modal('show'); 
			//console.log(request.responseText);
        }
    }
}
</script>
</body>
</html>