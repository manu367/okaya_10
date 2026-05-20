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
	var dataTable = $('#wh-reco-mss-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		//"order": [[ 4, "desc" ]],
		"ajax":{
			url :"../pagination/wh-reco-mss-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".wh-reco-mss-grid-error").html("");
				$("#wh-reco-mss-grid").append('<tbody class="wh-reco-mss-grid-error"><tr><th colspan="9">No data found in the server</th></tr></tbody>');
				$("#wh-reco-mss-grid_processing").css("display","none");
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
      <h2 align="center"><i class="fa fa-plus-square"></i> Missing Stock Reconciliation</h2><br>
      <?php if($_REQUEST['msg']){?>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>  
        <form class="form-horizontal" role="form" name="form1" action="" method="get">	   
	    
         <div class="form-group">
         
		  <div class="col-md-6">
			<label class="col-md-5 control-label"> Status</label>	  
			<div class="col-md-6" align="left">
			  <select id="status"  name="status" class="form-control">
              <option value="" <?php if($_REQUEST['status'] == "") { echo 'selected'; }?>>--Please Select--</option>
               <option value="R" <?php if($_REQUEST['status'] == "R") { echo 'selected'; }?>>Pending For Reconciliation</option>
			   <option value="Y" <?php if($_REQUEST['status'] == "Y") { echo 'selected'; }?>>Reconciliation Approved</option>
              	<option value="N" <?php if($_REQUEST['status'] == "N") { echo 'selected'; }?>>Reconciliation Rejected</option>
			  </select>
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-4 control-label"> </label>
            <div class="col-md-6">
            <div style="display:inline-block;float:left">
           
            </div>
            <div style="display:inline-block;float:left">&nbsp;
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
			   <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="GO">
              </div>
            </div>
	      </div>
         </div>
		  </form>
      <form class="form-horizontal" role="form">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="wh-reco-mss-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>From Location Name</th>
			 <th>To Location Name</th>
			  <th>Challan No.</th>
			  <th>Status</th> 
                <th>Type</th> 
                <th>Action</th>             
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