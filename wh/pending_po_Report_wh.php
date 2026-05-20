<?php
require_once("../includes/config.php");
$type =$_REQUEST['type'];
$status =$_REQUEST['status'];
$location =$_REQUEST['location_code'];

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
 ///// checkbox selection condition/////////////////////////////
$(document).ready(function()
{
    $("#pending").change(function() {
        if ($(this).is(":checked")) {
		 $("#dt_range").hide();	
                $("#ty").hide();			
				 $("#st").hide();	
				 $("#loc").hide();	
				 				
          } 
		  else
		  {
		  $("#dt_range").show();	
		   $("#st").show();	
		    $("#loc").show();		
		 $("#ty").show();	
		  }
       
    });
});
 $(document).ready(function(){
	if($("#pending").is(":checked")){
		$("#dt_range").hide();	
		 $("#st").hide();	
		 $("#ty").hide();	
		  $("#loc").hide();	
	}
});
 
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});

$(document).ready(function() {
	$('#status').multiselect({
			includeSelectAllOption: true
            //enableFiltering: true
	});
});

$(document).ready(function() {
	$('#type').multiselect({
			includeSelectAllOption: true
            //enableFiltering: true
	});
});

$(document).ready(function() {
	$('#state').multiselect({
			includeSelectAllOption: true
            //enableFiltering: true
	});
});

$(document).ready(function() {
	$('#locationcity').multiselect({
			includeSelectAllOption: true
            //enableFiltering: true
	});
});

$(document).ready(function() {
	$('#location_code').multiselect({
			includeSelectAllOption: true
            //enableFiltering: true
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
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-shopping-bag"></i> Pending PO</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	    <div class="form-group">
         <div  class="col-md-6"><label class="col-md-5 control-label">Total pending:</label>	  
			<div class="col-md-6 input-append date" align="left">
		 <input type="checkbox" name="pending"  id="pending"   value="checked"  <?php if($_REQUEST['pending']){echo "checked";}?>> 
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
			
            </div>
          </div>
	    </div><!--close form group-->
	    <div class="form-group">
         <div id= "dt_range" class="col-md-6" ><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6" id="st"><label class="col-md-5 control-label">Status</label>	  
			<div class="col-md-5" align="left">
			  <select id="status"  name="status[]" class="form-control" multiple="multiple" onChange="document.form1.submit();">
			   <option value="1" <?php for ($i=0; $i<count($status); $i++){if($status[$i] == "1") { echo 'selected'; }}?>>Pending</option>
				<option value="2" <?php for ($i=0; $i<count($status); $i++){ if($status[$i] == "2"){ echo 'selected'; }}?>>Processed</option>				
			</select>
            </div>
          </div>
	    </div><!--close form group-->
		<div class="form-group">
         <div class="col-md-6" id="loc"><label class="col-md-5 control-label">Location</label>	  
			<div class="col-md-6"   id="location">
			  <select name="location_code[]" id="location_code" class="form-control" multiple="multiple" onChange="document.form1.submit();">
                <?php
                $res_pro = mysqli_query($link1,"select from_code from po_master where to_code = '".$_SESSION['asc_code']."' group by from_code"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){
				 $asc_res  = mysqli_fetch_array(mysqli_query($link1,"select location_code,locationname from location_master where statusid = '1' and location_code ='".$row_pro['from_code']."' "));
				?>
                <option value="<?=$asc_res['location_code']?>" <?php for($i=0; $i<count($location); $i++){ if($location[$i] == $asc_res['location_code']) { echo 'selected'; }}?>><?=$asc_res['locationname']." (".$asc_res['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6" id="ty"><label class="col-md-5 control-label">Type</label>	  
			<div class="col-md-5">	
			 <select id="type"  name="type[]" class="form-control" multiple="multiple" onChange="document.form1.submit();">		
			 <option value="PNA" <?php for ($i=0; $i<count($type); $i++){if($type[$i] == "PNA") { echo 'selected'; }}?>>PNA</option>
			<option value="PO" <?php for ($i=0; $i<count($type); $i++){ if($type[$i] == "PO"){ echo 'selected'; }}?>>MSL(PO)</option>	
			</select>	 			
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
	   
	   //// array initialization to send by query string of  location
			$locationstr = "";
			$arr_loc = $_REQUEST['location_code'];
			for($i=0; $i<count($arr_loc); $i++){
				if($locationstr){
					$locationstr.="','".$arr_loc[$i];
				}else{
					$locationstr.= $arr_loc[$i];
				}
			}	 
			
			//// array initialization to send by query string of  status
			$statusstr = "";
			$arr_status = $_REQUEST['status'];
			for($i=0; $i<count($arr_status); $i++){
				if($statusstr){
					$statusstr.="','".$arr_status[$i];
				}else{
					$statusstr.= $arr_status[$i];
				}
			}	
			
			//// array initialization to send by query string of  type
			$typestr = "";
			$arr_type = $_REQUEST['type'];
			for($i=0; $i<count($arr_type); $i++){
				if($typestr){
					$typestr.="','".$arr_type[$i];
				}else{
					$typestr.= $arr_type[$i];
				}
			}		
		  	
	?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="../excelReports/pendingpoexcel.php?daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($locationstr);?>&status=<?=base64_encode($statusstr);?>&type=<?=base64_encode($typestr);?>&pending=<?=base64_encode($_REQUEST['pending'])?>" title="Export Pending PO details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Pending PO details in excel"></i></a>
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