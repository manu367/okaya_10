<?php
require_once("../includes/config.php");
$today=date("Y-m-d");



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
 <script type="text/javascript" language="javascript" >
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});

$(document).ready(function() {
	var dataTable = $('#joblist-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 0, "asc" ]],
		"ajax":{
			url :"../pagination/eng-app-attance-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>","daterange": "<?=$_REQUEST['daterange']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".eng-stock-grid-error").html("");
				$("#eng-stock-grid").append('<tbody class="eng-stock-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#eng-stock-grid_processing").css("display","none");
				
			}
		}
	} );
} );

</script>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<!-- Include multiselect -->
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-snowflake-o"></i>Engineer Attandance</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" id="form1" action="" method="get">
	   
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Engineer Name</label>	  
			<div class="col-md-5" align="left">
			 	<select name="location_code" id="location_code" class="form-control" onChange="document.form1.submit();">
                <option value="">ALL</option>
                <?php
                $res_pro = mysqli_query($link1,"select userloginid,locusername from locationuser_master where location_code='".$_SESSION['asc_code']."'"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                <option value="<?=$row_pro['userloginid']?>" <?php if($_REQUEST['location_code'] == $row_pro['userloginid']) { echo 'selected'; }?>><?=$row_pro['locusername']." (".$row_pro['userloginid'].")"?></option>
                <?php } ?>
                 </select>
            </div>
          </div>
	    </div><!--close form group-->
      
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-6" >
			
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
            
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-6"  >
		
        
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" >
               
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
	   //// array initialization to send by query string of  product
		
	?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="../excelReports/excelAttandenceeng.php?daterange=<?=$_REQUEST['daterange']?>&model=<?=base64_encode($modelstr);?>&typedate=<?=$_REQUEST['typedate']?>&proid=<?=base64_encode($prostr);?>&brand=<?=base64_encode($brandstr);?>" title="Export Part Consume details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Part Consume details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
		  <form class="form-horizontal" role="form" name="form2">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="joblist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr>
              <th>S.No</th>
			    <th>Engineer Name</th>
			   <th>Status</th>
			   <th>In Date Time</th>
              <th>Mapped Model</th>
              <th>Partcode</th>
			  
              <th>Description</th>
            
              <th>Alternate Partcode</th>
              <th>Customer Price</th>
           
              <th>Fresh</th>
              <th>Defective</th>
              
          
			 
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