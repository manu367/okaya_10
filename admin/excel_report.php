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
	$('#example-multiple-selected').multiselect({
			includeSelectAllOption: true
            //enableFiltering: true
	});
});
$(document).ready(function () {
	$('#start_date').datepicker({
		format: "yyyy-mm-dd",
        todayHighlight: true,
		autoclose: true
	});
});
$(document).ready(function () {
	$('#end_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});
</script>
<!-- Include multiselect -->
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<!-- Include Date Range Picker -->
 <script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-user-times"></i> Login Logout Details</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Status:</label>	  
			<div class="col-md-5" align="left">
			  <select id="example-multiple-selected" multiple="multiple" name="task_status[]" class="form-control">
				<?php
                $res_status = mysqli_query($link1,"select statusid, statusname from status_master where statusid in ('1','2')")or die(mysqli_error($link1)); 
                while($row_status = mysqli_fetch_assoc($res_status)){?>
                <option value="<?=$row_status['statusid']?>" <?php if($array_status[$row_status['statusid']]=="Y"){ echo "selected";}?>><?=$row_status['statusname']?></option>
                <?php } ?>
			</select>
            </div>
          </div>
	    </div><!--close form group-->
        <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">From</label>	  
			<div class="col-md-6 input-append date">
				<div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="start_date"  id="start_date" style="width:150px;" required value="<?=$today?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                  </div>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">To</label>	  
			<div class="col-md-5 input-append date">
                  <div style="display:inline-block;float:left;"><input type="text" class="form-control span2 required" name="end_date"  id="end_date" style="width:150px;" required value="<?=$today?>"></div><div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i>
                  </div>
              </div>
          </div>
	    </div>
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Location</label>
            <div class="col-md-5">
               <select name="location_code" id="location_code" class="form-control">
                <option value="">All</option>
                <?php
                $res_pro = mysqli_query($link1,"select location_code,locationname from location_master where statusid = '1'"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                <option value="<?=$row_pro['location_code']?>" <?php if($_REQUEST['location_code'] == $row_pro['location_code']) { echo 'selected'; }?>><?=$row_pro['locationname']." (".$row_pro['location_code'].")"?></option>
                <?php } ?>
                 </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){
		  	//// array initialization to send by query string
			$statusstr = "";
			$arr_selstatus = $_REQUEST['task_status'];
			for($i=0; $i<count($arr_selstatus); $i++){
				if($statusstr){
					$statusstr.=",".$arr_selstatus[$i];
				}else{
					$statusstr.= $arr_selstatus[$i];
				}
			}		  
	?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="excelexport.php?rname=<?=base64_encode("sample_report")?>&rheader=<?=base64_encode("Sample Report")?>&daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>&task_status=<?=base64_encode($statusstr);?>&start_date=<?=$_REQUEST['start_date']?>&end_date=<?=$_REQUEST['end_date']?>" title="Export Login Logout details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Login Logout details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>