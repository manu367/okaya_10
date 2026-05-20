<?php
require_once("../includes/config.php");
$today=date("Y-m-d");
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);
//$accessproduct = getAccessProduct($_SESSION['userid'],$link1);	
//$access_brand = getAccessBrand($_SESSION['userid'],$link1);
/////////////////////////// get model on basis of product and brand//////////////////////////////////////////////////////
$prodstr=$_REQUEST['prod_code'];
$brandstr=$_REQUEST['brand'];
//////////////////get status and substatus ///////////////
if($_REQUEST['status']=='all'){
$arr_substatus= mysqli_query($link1,"select  status_id from jobstatus_master order by  system_status");
while($substatus_query=mysqli_fetch_array($arr_substatus)){
if($statusstring){
$statusstring.="','".$substatus_query['status_id'];
}else{
$statusstring.=$substatus_query['status_id'];
}
}//////close while
}
else{
$statusstring = $_REQUEST['status'];
}
////////////////////////
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">


<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet" />-->
<script src="../js/jquery.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/moment.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">

 <link rel="stylesheet" href="../css/bootstrap-select.min.css">

 <script src="../js/bootstrap-select.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
$("#form1").validate();
});



function makeDropdown(){
$('.selectpicker').selectpicker();
}

</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script type="text/javascript" language="javascript" >



$(document).ready(function(){
$('input[name="daterange"]').daterangepicker({
locale: {
format: 'YYYY-MM-DD'
}
});
});
;
////// checkbox selection condition/////////////////////////////
$(document).ready(function()
{
$("#pending").change(function() {
if ($(this).is(":checked")) {
$("#pending").show();
$("#dt_range").hide();	
$("#st").hide();	
$("#subst").hide();
$("#stat").hide();
$("#loc").hide();
$("#prod").hide();
$("#brnd").hide();
$("#mdl").hide();
$("#rpt_frm_dt").hide();
} 
else
{
$("#dt_range").show();	
$("#st").show();	
$("#subst").show();		
$("#stat").show();
$("#loc").show();
$("#prod").show();
$("#brnd").show();
$("#mdl").show();
$("#rpt_frm_dt").show();
}
});
});
$(document).ready(function(){
if($("#pending").is(":checked")){
$("#dt_range").hide();	
$("#st").hide();	
$("#subst").hide();	
$("#stat").hide();
$("#loc").hide();
$("#prod").hide();
$("#brnd").hide();
$("#mdl").hide();
$("#rpt_frm_dt").hide();
}
});
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
<title>
<?=siteTitle?>
</title>
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
  <div class="row content">
    <?php 
  include("../includes/leftnavemp2.php");
?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-volume-control-phone"></i> All Call</h2>
      <?php if($_REQUEST['msg']){?>
      <br>
      <h4 align="center" style="color:#FF0000">
        <?=$_REQUEST['msg']?>
      </h4>
      <?php }?>
      <form class="form-horizontal" id="form1" name="form1" action="" method="post">
        <div class="form-group">
          <div id= "dt_range" class="col-md-6">
            <label class="col-md-5 control-label">Date Range</label>
            <div class="col-md-6 input-append date" align="left">
              <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-5 control-label">All pending:</label>
            <div class="col-md-5" align="left">
              <input type="checkbox" name="pending"  id="pending"   value="checked"  <?php if($_REQUEST['pending']){echo "checked";}?>>
            </div>
          </div>
        </div>
        <!--close form group-->
      
       
      
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label"></label>
            <div class="col-md-5">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
              <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
              <input type="hidden" name="locationaft_selc" id="locationaft_selc" value="<?php echo $access_loc?>"/>
              <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-5 control-label"></label>
            <div class="col-md-5"> </div>
          </div>
        </div>
        <!--close form group-->
      </form>
      <?php if ($_REQUEST['Submit']){		
//// array initialization to send by query string of  status
?>
      <div class="form-group">
        <div class="col-md-10">
          <label class="col-md-4 control-label"></label>
          <div class="col-md-6" align="left">
            <?php /*?><?php if ($_REQUEST['statename'] == '') {?>		
<?php  }else {?><?php */?>
            <!--<a href="../excelReports/allcallexcel.php?daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['locationname']);?>&locationaft_selcid=<?=base64_encode($_REQUEST['locationaft_selc']);?>&modelid=<?=base64_encode($_REQUEST['model']);?>&status=<?=base64_encode($statusstr);?>&substatus=<?=base64_encode($substatusstr);?>&pending=<?=base64_encode($_REQUEST['pending'])?>&state=<?=base64_encode($_REQUEST['statename']);?>&proid=<?=base64_encode($_REQUEST['prod_code']);?>&brand=<?=base64_encode($_REQUEST['brand']);?>&rdate=<?=$_REQUEST['date_type']?>" title="Export All Call details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export All Call details in excel"></i></a>-->
             <a href="excelexport.php?rname=<?=base64_encode("allcallexcel_cc")?>&pending=<?=base64_encode($_REQUEST['pending'])?>&daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['locationname']);?>&modelid=<?=base64_encode($_REQUEST['model']);?>&status=<?=base64_encode($_REQUEST['status']);?>&substatus=<?=base64_encode($_REQUEST['substatus']);?>&pending=<?=base64_encode($_REQUEST['pending'])?>&state=<?=base64_encode($_REQUEST['statename']);?>&proid=<?=base64_encode($_REQUEST['prod_code']);?>&brand=<?=base64_encode($_REQUEST['brand']);?>&rdate=<?=$_REQUEST['date_type']?>" title="Export city details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export city details in excel"></i></a>
			<!--<a href="../excelReports/allcallexcel.php?pending=<?=base64_encode($_REQUEST['pending'])?>&daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['locationname']);?>&modelid=<?=base64_encode($_REQUEST['model']);?>&status=<?=base64_encode($_REQUEST['status']);?>&substatus=<?=base64_encode($_REQUEST['substatus']);?>&pending=<?=base64_encode($_REQUEST['pending'])?>&state=<?=base64_encode($_REQUEST['statename']);?>&proid=<?=base64_encode($_REQUEST['prod_code']);?>&brand=<?=base64_encode($_REQUEST['brand']);?>&rdate=<?=$_REQUEST['date_type']?>" title="Export All Call details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export All Call details in excel"></i></a>--> </div>
        </div>
      </div>
      <!--close form group-->
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