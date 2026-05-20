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
	var dataTable = $('#party-account').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 3, "asc" ]],
		"ajax":{
			url :"../pagination/party-accountasp-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>" ,"location": "<?=$_SESSION['asc_code']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".party-account-error").html("");
				$("#party-account").append('<tbody class="party-account-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#party-account_processing").css("display","none");
				
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
      <h2 align="center"><i class="fa fa-university"></i> Party Account</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <br></br>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">				
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5" id="location">
             
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
           
            </div>
          </div>
	    </div><!--close form group-->
	  </form>

           <div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
        
               <a href="../excelReports/partyacount_aspexcel.php?location=<?=$_SESSION['asc_code']?>" title="Export Account details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Account details in excel"></i></a>
             
            </div>
          </div>
	    </div><!--close form group-->
	
      <form class="form-horizontal" role="form">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="party-account" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>State</th>
			 <th>City</th>
			  <th>Location Name</th>
			  <th>Location Code</th>         
               <th>Amount</th>
              <th>Last Update date</th>
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