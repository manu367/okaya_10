<?php
require_once("../includes/config.php");
$tostatenew = $_REQUEST['to_state'];
$tolocnew = $_REQUEST['to_loc']; 
$brandarray=$_REQUEST['brand'];

/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);

////////////////////  array initialization to make querry string used to get to location/////////////////////////////////////////
$arr_statestr = $_REQUEST['to_state'];
			for($i=0; $i<count($arr_statestr); $i++){
				if($tostatestr){
					$tostatestr.="','".$arr_statestr[$i];
				}else{
					$tostatestr.= $arr_statestr[$i];
				}
			}

///////////////////////////////// get product and brand on basis of location////////////////////////////////////////									
$tostr=$_REQUEST['to_loc'];
$count=count($locstr);
////get access product details
for($i=0; $i<count($tostr); $i++){
				if($to_str){
					$to_str.="','".$tostr[$i];
				}else{
					$to_str.= $tostr[$i];
				}
$access_product = getAccessProduct($to_str,$link1);				
			}						
////get access brand details
for($i=0; $i<count($tostr); $i++){
				if($name){
					$name.="','".$tostr[$i];
				}else{
					$name.= $tostr[$i];
				}
$access_brand = getAccessBrand($name,$link1);
}

/////////////////////////// get model on basis of product and model //////////////////////////////////////////////////////
$arr_prodstr = $_REQUEST['prod_code'];
			for($i=0; $i<count($arr_prodstr); $i++){
				if($prodstr){
					$prodstr.="','".$arr_prodstr[$i];
				}else{
					$prodstr.= $arr_prodstr[$i];
				}
			}
			
$arr_brandstr = $_REQUEST['brand'];
			for($i=0; $i<count($arr_brandstr); $i++){
				if($brandstr){
					$brandstr.="','".$arr_brandstr[$i];
				}else{
					$brandstr.= $arr_brandstr[$i];
				}
			}				
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
 <script type="text/javascript">
    $(document).ready(function() {
        $("#form1").validate();
    });
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

$(document).ready(function() {
	$('#to_state').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

$(document).ready(function() {
	$('#to_loc').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

$(document).ready(function() {
	$('#brand').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
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
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-pencil-square-o"></i>Sales Return </h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1"  id="form1" action="" method="get">
	   
	    <div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
            </div>
          </div>
	    </div><!--close form group-->
		
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"> State <span style="color:#F00">*</span></label>	  
			<div class="col-md-6" >
				
				<select   name="to_state[]" id="to_state" class="form-control required" multiple="multiple" onChange="document.form1.submit();" required >
					 <?php
					$tostate="select stateid , state from state_master where stateid in (select from_stateid from billing_master where from_stateid not in ('0')  and  from_stateid in (".$arrstate.") group by from_stateid) order by state ";
					$check1=mysqli_query($link1,$tostate);
					while($br = mysqli_fetch_array($check1)){
					$res = mysqli_fetch_array(mysqli_query($link1,""));
					?>
					<option value="<?=$br['stateid']?>" <?php  for($i=0; $i<count($tostatenew); $i++){ if($tostatenew[$i] == $br['stateid']) { echo 'selected'; } }?>><?=$br['state']?></option>
					<?php } ?>
				</select>
				
				<?php /* ?>
				
				<select   name="to_state[]" id="to_state" class="form-control required" multiple="multiple" onChange="document.form1.submit();" required >
				 <?php
                $tostate="select from_stateid from billing_master where from_stateid not in ('0')  and  from_stateid in (".$arrstate.") group by from_stateid";
			    $check1=mysqli_query($link1,$tostate);
                while($br = mysqli_fetch_array($check1)){
				$res = mysqli_fetch_array(mysqli_query($link1,"select stateid , state from state_master where stateid = '".$br['from_stateid']."' order by state "));
				?>
                <option value="<?=$res['stateid']?>" <?php  for($i=0; $i<count($tostatenew); $i++){ if($tostatenew[$i] == $res['stateid']) { echo 'selected'; } }?>><?=$res['state']?></option>
                <?php } ?>
			</select>
				
				<?php */ ?>
				
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"> Location <span style="color:#F00">*</span></label>	  
			<div class="col-md-5">
                  <select name="to_loc[]" id="to_loc" class="form-control required" multiple="multiple" onChange="document.form1.submit();" required >
					 <?php
						 $toloc=" select locationname , location_code from location_master where location_code in ( select from_location from billing_master where from_location in (select location_code from location_master where stateid in ('$tostatestr')) group by from_location ) order by locationname ";
						 $check1=mysqli_query($link1,$toloc);
						 while($br = mysqli_fetch_array($check1)){
						// $sql = mysqli_query($link1,"select locationname , location_code from location_master where location_code = '".$br['from_location']."' ");
						 if(mysqli_num_rows($sql)>0){
						 $res = mysqli_fetch_array($sql);
                    ?>
                    <!-------------
                    <option value="<?=$res['location_code']?>" <?php for($i=0; $i<count($tolocnew); $i++){ if($tolocnew[$i] == $res['location_code']) { echo 'selected'; } }?>>
                    	<?=$res['locationname']." | ".$res['location_code']?>
                    </option> ----------------->
                    <?php } else {?>
                    <option value="<?=$br['location_code']?>" <?php for($i=0; $i<count($tolocnew); $i++){ if($tolocnew[$i] == $br['location_code']) { echo 'selected'; } }?>>
                    	<?=$br['locationname']." (".$br['location_code'].")"?>
                    </option>
                    <?php }}?>
                 </select>
              </div>
          </div>
	    </div>  
		<div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label">Brand</label>	  
			<div class="col-md-5">
			<select   name="brand[]" id="brand" class="form-control"  multiple="multiple" onChange="document.form1.submit();">
				<?php
                $brand = mysqli_query($link1,"SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand" );
                while($brandinfo = mysqli_fetch_assoc($brand)){?>
                <option value="<?=$brandinfo['brand_id']?>" <?php for($i=0; $i<count($brandarray); $i++){if($brandarray[$i] == $brandinfo['brand_id']) { echo 'selected'; }}?>><?=$brandinfo['brand']." | ".$brandinfo['brand_id']?></option>
                <?php } ?>
	</select>
			
            </div>
          </div>
          <div class="col-md-6"><label class="col-md-5 control-label">Stock Type</label>	  
			<div class="col-md-6 ">
		   		 <select   name="doc_typ" id="doc_typ"  class="form-control" onChange="document.form1.submit();">
                 	<option value="" <?php if($_REQUEST['doc_typ'] == "") { echo 'selected'; } ?>>All</option>
                    <option value="P2C" <?php if($_REQUEST['doc_typ'] == "P2C") { echo 'selected'; } ?>>Faulty Return</option>
					<option value="Sale Return" <?php if($_REQUEST['doc_typ'] == "Sale Return") { echo 'selected'; } ?>>Fresh Return</option>
				 </select>
            </div>
          </div>
	    </div><!--close form group--> 
		 
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!"> 
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
               
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){	
	   			
			//// array initialization to send by query string of  brand
			$brandstr = "";
			$arr_brand = $_REQUEST['brand'];
			for($i=0; $i<count($arr_brand); $i++){
				if($brandstr){
					$brandstr.="','".$arr_brand[$i];
				}else{
					$brandstr.= $arr_brand[$i];
				}
			}		
									
			//// array initialization to send by query string of  to state
			$tostatestr = "";
			$arr_tostate = $_REQUEST['to_state'];
			for($i=0; $i<count($arr_tostate); $i++){
				if($tostatestr){
					$tostatestr.="','".$arr_tostate[$i];
				}else{
					$tostatestr.= $arr_tostate[$i];
				}
			}		   			
						
			//// array initialization to send by query string of  to location
			$tolocationstr = "";
			$arr_toloc = $_REQUEST['to_loc'];
			for($i=0; $i<count($arr_toloc); $i++){
				if($tolocationstr){
					$tolocationstr.="','".$arr_toloc[$i];
				}else{
					$tolocationstr.= $arr_toloc[$i];
				}
				
			}	 	
				  		  
	?>
        <div class="form-group">
		  <div class="col-md-6" style="text-align:center;">
			<?php if ($_REQUEST['to_state'] == '' || $_REQUEST['to_loc'] == '') {?>		
			<?php  }else {?>  
           <span>Partwise Sales Return Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/salepartwise_report.php?daterange=<?=$_REQUEST['daterange']?>&doc_typ=<?=base64_encode($_REQUEST['doc_typ']);?>&brand=<?=base64_encode($brandstr);?>&to_state=<?=base64_encode($tostatestr);?>&to_loc=<?=base64_encode($tolocationstr);?>" title="Export Sales Return Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Sales Return Report details in excel"></i></a>
		    <?php
				}
				?>
          </div>
		   <div class="col-md-6" style="text-align:center;">
			<?php if ($_REQUEST['to_state'] == '' || $_REQUEST['to_loc'] == '') {?>		
			<?php  }else {?>             
		   <span>Detailed Sales Return Report</span>&nbsp;&nbsp;&nbsp;&nbsp;<a href="../excelReports/saledetail_report.php?daterange=<?=$_REQUEST['daterange']?>&frm_state=<?=base64_encode($fromstatestr);?>&frm_loc=<?=base64_encode($locationstr);?>&to_state=<?=base64_encode($tostatestr);?>&to_loc=<?=base64_encode($tolocationstr);?>&doc_typ=<?=base64_encode($_REQUEST['doc_typ']);?>" title="Export Sales Return Report in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Sales Return Report details in excel"></i></a>
		    <?php
				}
				?>
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