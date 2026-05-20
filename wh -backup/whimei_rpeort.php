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
	var dataTable = $('#imei-import-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 3, "asc" ]],
		"ajax":{
			url :"../pagination/whimei-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>" ,"status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".imei-import-grid-error").html("");
				$("#imei-import-grid").append('<tbody class="imei-import-grid-error"><tr><th colspan="10">No data found in the server</th></tr></tbody>');
				$("#imei-import-grid_processing").css("display","none");
				
			}
		}
	} );
} );
 

</script>

<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-cubes"></i>TAG/ <?php echo SERIALNO ?>  Details</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="post">
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Status</label>	  
			<div class="col-md-6" >
				<select  id="status" name="status" class="form-control required" onChange="document.form1.submit();">
				<option value="">All</option>
				<option value="1" <?php if($_REQUEST['status'] == '1'){ echo "selected";}?>>Available </option>
				<option value="3" <?php if($_REQUEST['status'] == '3'){ echo "selected";}?>>Dispatch </option>
				</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5 input-append date">
                  
              </div>
          </div>
	    </div>
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
			 <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
              <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
              <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">
               
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
              
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){
	?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="../excelReports/whimeiexcel.php?status=<?=$_REQUEST['status']?>" title="WH IMEI details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export WH IMEI details details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
		<form class="form-horizontal" role="form">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="imei-import-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>TAG/<?php echo SERIALNO ?> </th>
			
			  <th>Partcode</th>         
               <th>Model</th>
              <th>Grn No.</th>
			   <th>History</th>
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