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
	var dataTable = $('#tab-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"ajax":{
			url :"../pagination/tab-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".tab-grid-error").html("");
				$("#tab-grid").append('<tbody class="tab-grid-error"><tr><th colspan="9">No data found in the server</th></tr></tbody>');
				$("#tab-grid_processing").css("display","none");
				
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
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-th-list"></i> Tab Master</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"> Status</label>	  
			<div class="col-md-5" align="left">
			   <select name="status" id="status" class="form-control"  onChange="document.form1.submit();">
                    <option value=""<?php if($_REQUEST['status']==''){ echo "selected";}?>>All</option>
                    <option value="active"<?php if($_REQUEST['status']=='active'){ echo "selected";}?>>Active</option>
                    <option value="deactive"<?php if($_REQUEST['status']=='deactive'){ echo "selected";}?>>Deactive</option>
                </select>
            </div>
          </div>
		  <div class="col-md-6">  
			<div class="col-md-5" align="left">
			 
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST[pid]?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST[hid]?>"/>
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">
        <button title="Add New Tab" type="button" class="btn btn-primary" style="float:right;" onClick="window.location.href='addTab.php?op=add<?=$pagenav?>'"><span>Add New Tab</span></button>&nbsp;&nbsp;
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="tab-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr>
              <th>S.No</th>
              <th>Sub Tab Name</th>
              <th>Sub Tab Seq.</th>
              <th>Sub Tab Icon</th>
              <th>Main Tab Name</th>
              <th>Main Tab Seq.</th>
              <th>Main Tab Icon</th>
              <th>Status</th>
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