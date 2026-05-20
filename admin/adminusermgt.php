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
		"ajax":{
			url :"../pagination/adminusr-grid-data.php", // json datasource
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
    <div class="<?=$screenwidth?>  tab-pane fade in active" id="home">
      <h2 align="center" style="border-bottom:1px solid #aaa8a8;padding:25px 0px;margin:0px;"><i class="fa fa-users"></i> Admin Users Master</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   
		  <div class="form-group">
			  <div class="col-md-6" style="padding:10px 0px;">
				  <div class="col-md-4">
					  <label class="control-label"> Status</label>
				  </div>
				  <div class="col-md-5" align="left">
					  <select name="status" id="status" class="form-control"  onChange="document.form1.submit();">
						  <option value="" <?php if($_REQUEST['status']==''){ echo "selected";}?>>All</option>
						  <option value="1" <?php if($_REQUEST['status']=='1'){ echo "selected";}?>>Active</option>
						  <option value="2" <?php if($_REQUEST['status']==2){ echo "selected";}?>>Deactive</option>
						  <option value="99" <?php if($_REQUEST['status']==99){ echo "selected";}?>>On Hold</option>
					  </select>
				  </div>
				  <div class="col-md-3" align="left">
				  </div>
			  </div>			  
			  <div class="col-md-6" style="padding:10px 0px;">
				  
				  <div class="col-md-10" align="left">
					  <label class="control-label"></label>
				  </div>
				  <div class="col-md-2" align="left">
					  <a href="excelexport.php?rname=<?=base64_encode("adminuser")?>&rheader=<?=base64_encode("Admin User Master")?>&u_type=<?=base64_encode($_GET['u_type'])?>&status=<?=base64_encode($_GET['status'])?>" title="Export user details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export user details in excel"></i></a>
				  </div>
			  </div>
		  </div>
	  </form>
		
		<div class="form-group" style="overflow:hidden;margin:0px;">
			<div class="col-md-12" style="padding:10px 0px;">
				<button title="Bulk Upload" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='addAdminUser_uploader.php?op=add<?=$pagenav?>'"><span>Bulk Upload</span></button>
				<button title="Add New User" type="button" class="btn<?=$btncolor?>" style="float:right;margin-right:5px;" onClick="window.location.href='addAdminUser.php?op=add<?=$pagenav?>'"><span>Add User</span></button>
				<button title="View Status Log" type="button" class="btn<?=$btncolor?>" style="float:left;margin-right:5px;background:#78797b;" onClick="window.location.href='statuslog_view.php'"><span>View Status Log</span></button>
				<button title="View Status Log" type="button" class="btn<?=$btncolor?>" style="float:left;margin-right:5px;background:#78797b;" onClick="window.location.href='permissionlog_view.php'"><span>View Permission Log</span></button>
			</div>
		</div>
		
		<form class="form-horizontal" role="form">
			<div class="form-group tab-area" id="page-wrap">
				<div class="col-md-12">
					<table  width="100%" id="admin-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
						<thead>
							<tr class="<?=$tableheadcolor?>">
								<th>S.No</th>
								<th>Login Id</th>
								<th>User Name</th>
								<th>User Type</th>
								<th>Phone No.</th>
								<th>Email-id</th>
								<th>Status</th>
								<th>View/Edit</th>
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