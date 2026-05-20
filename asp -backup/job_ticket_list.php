<?php
require_once('../includes/config.php');
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
  <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
 <script language="javascript" type="text/javascript">
 $(document).ready(function() {	
    /////// if user enter contact no. then search button  should be enabled
	 $("#contact_no").keyup(function() {
		 if($("#contact_no").val()!=""){ 
			$("#Submit").attr("disabled",false);
		 }else{
			 $("#Submit").attr("disabled",true);
		 }
    });
 });
 ///////////////////////// pagination////////////////////////////////////////////////////////////////
 $(document).ready(function() {
	var dataTable = $('#job-ticket').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/job-ticket-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".job-ticket-error").html("");
				$("#job-ticket").append('<tbody class="job-ticket-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#job-ticket_processing").css("display","none");
				
			}
		}
	} );
} );
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
       <h2 align="center"><i class="fa fa-list"></i> Ticket Status</h2>
	   <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>    
   &nbsp;&nbsp; 
      	<div class="form-group"  id="page-wrap" style="margin-left:10px;">
		<br></br> 
		
			<form class="form-horizontal" role="form">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="job-ticket" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
			   <th>Ticket No</th>
              <th>Customer Name</th>
			 <th>Contact No.</th>
			  <th>City </th>
			  <th>State</th> 
			  <th>Make Job</th>    
              <th>View</th>
            </tr>
          </thead>
          </table>
          </div>
      </form>
	
	</div>
  </div>
</div>
</body>
</html>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>