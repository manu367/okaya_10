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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">

 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
$(document).ready(function() {
	var dataTable = $('#approve_statusimei-grid').DataTable( {
		"sectioning": true,
		"serverSide": true,
			"bStateSave": true,
		//"order": [[ 4, "asc" ]],
		"ajax":{
			url :"../pagination/approve_statusimei-grid.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>",  "status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".approve_statusimei-grid").html("");
				$("#approve_statusimei-grid").append('<tbody class="approve_statusimei-grid"><tr><th colspan="6">No data found in the server</th></tr></tbody>');
				$("#approve_statusimei-grid").css("display","none");
				
			}
		}
	} );
} );





</script>
 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/bootstrap-datepicker.js"></script>

<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h3 align="center"><i class="fa fa-share fa-lg"></i> IMEI Approve Status</h3>
      <?php if($_REQUEST['msg']){?>
	  <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1"  id="form1" action="" method="get">
	   
	    <div class="form-group">
			<div class="col-md-6"><label class="col-md-5 control-label">Status</label>
			<div class="col-md-5">
				  <select  id="status" name="status" class="form-control"  onChange="document.form1.submit();">
				  <option value="">Please Select </option>
				   <option value="1" <?php if($_REQUEST['status'] == '1') {echo "selected" ;} ?>>Pending</option>
				  <option value="2" <?php if($_REQUEST['status'] == '2') {echo "selected" ;} ?>>Approved</option>
				  <option value="3" <?php if($_REQUEST['status'] == '3') {echo "selected" ;} ?>>Reject</option>
				  
			</select>
			 
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
             
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">  
              <a href="excelexport.php?rname=<?=base64_encode("imei_approval_report")?>&rheader=<?=base64_encode("IMEI Approval")?>&status=<?=base64_encode($_GET['status'])?>" title="Export partcode details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export partcode details in excel"></i></a>        
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="approve_statusimei-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
             <th>S.No</th>
			 <th>Model</th>
			  <th>IMEI 1</th>
			  <th>IMEI 2</th>
			  <th>Import Date</th> 
			  <th>Date of Purchase</th> 
              <th>Image1</th>  
              <th>Image2</th>             
			  <th>Status</th>
            </tr>
          </thead>
          </table>
          </div>
      <!--</div>-->
      </form>
	  <!-- Start Model Mapped Modal -->
 
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>