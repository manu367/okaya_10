<?php
require_once("../includes/config.php");
$today=date("Y-m-d");
//// get access brand /////
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

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
/////////// function to get city on the basis of state
 function get_citydiv(){
	  var name=$('#state').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state:name},
		success:function(data){
	    $('#citydiv').html(data);
	    }
	  });
   
 }
 
 /////////// function to get city on the basis of state
 function get_location(){
	  var name=$('#locationcity').val();
	   var name1=$('#state').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{citynew:name, statenew:name1},
		success:function(data){
	    $('#location').html(data);
	    }
	  });
   
 }
 
 //////////////////////// function to get model on basis of model dropdown selection///////////////////////////
 function getmodel(){
	  var brand=$('#brand').val();
	  var product=$('#prod_code').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{brandinfo:brand,productinfo:product},
		success:function(data){
		 $('#modeldiv').html(data);
	    }
	  });
  }

</script>
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
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-shopping-bag"></i>Pending PO</h2>
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
		  <div class="col-md-6"><label class="col-md-5 control-label">Status</label>	  
			<div class="col-md-5" align="left">
			  <select id="status"  name="status" class="form-control">
			  <option value=''>--Please Select-</option>
			   <option value="Pending" <?php if($_REQUEST['status'] == "Pending") { echo 'selected'; }?>>Pending</option>
			<option value="Processed" <?php if($_REQUEST['status'] == "Processed"){ echo 'selected'; }?>>Dispatch</option>				
			</select>
            </div>
          </div>
	    </div><!--close form group-->
      <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">State</label>	  
			<div class="col-md-6" >
				<select   name="state" id="state" class="form-control" onChange="get_citydiv();">
				<option value=''>--Please Select--</option>
				<?php
                $state = mysqli_query($link1,"select stateid, state from state_master "); 
                while($stateinfo = mysqli_fetch_assoc($state)){?>
                <option value="<?=$stateinfo['stateid']?>" <?php if($_REQUEST['state'] == $stateinfo['stateid']) { echo 'selected'; }?>><?=$stateinfo['state']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">City</label>	  
			<div class="col-md-5" id="citydiv">
                  <select name="locationcity" id="locationcity" class="form-control" onChange="get_location();">
                <option value=''>--Please Select-</option>
                 </select>
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Location</label>	  
			<div class="col-md-6"   id="location">
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
		  <div class="col-md-6"><label class="col-md-5 control-label">Type</label>	  
			<div class="col-md-5"  >
			 <select id="type"  name="type" class="form-control">
               <option value=''>--Please Select-</option>
			   <option value="PNA" <?php if($_REQUEST['type'] == "PNA") { echo 'selected'; }?>>PNA</option>
			<option value="PO" <?php if($_REQUEST['type'] == "PO"){ echo 'selected'; }?>>MSL</option>		
			</select>	 
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Product</label>	  
			<div class="col-md-6" >
				<select   name="prod_code" id="prod_code" class="form-control">
				<option value=''>--Please Select--</option>
				<?php
               $model_query="select product_id,product_name from product_master where status='1'";
			        $check1=mysqli_query($link1,$model_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['product_id']?>" <?php if($_REQUEST['prod_code'] == $br['product_id']) { echo 'selected'; }?>><?=$br['product_name']." | ".$br['product_id']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Brand</label>	  
			<div class="col-md-5" >
                 <select   name="brand" id="brand" class="form-control" onChange="getmodel();">
				<option value=''>--Please Select--</option>
				<?php
                $brand = mysqli_query($link1,"select brand_id, brand from brand_master where brand_id in ($access_brand) and status='1'" );
                while($brandinfo = mysqli_fetch_assoc($brand)){?>
                <option value="<?=$brandinfo['brand_id']?>" <?php if($_REQUEST['brand'] == $brandinfo['brand_id']) { echo 'selected'; }?>><?=$brandinfo['brand']." | ".$brandinfo['brand_id']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
	    </div>
		
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Model</label>	  
			<div class="col-md-6" id="modeldiv">
				<select name="model" id="model" class="form-control">
                <option value=''>--Please Select-</option>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">             
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
               <a id="pending_po_excel"
                       href="../excelReports/pendingpoexcel.php?daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>&status=<?=$_REQUEST['status']?>&city=<?=$_REQUEST['locationcity']?>&state=<?=$_REQUEST['state']?>&type=<?=$_REQUEST['type']?>&proid=<?=$_REQUEST['prod_code']?>&brand=<?=$_REQUEST['brand']?>&model=<?=$_REQUEST['model']?>"
                       title="Export Pending PO details in excel">
                   <i class="fa fa-file-excel-o fa-2x faicon" title="Export Pending PO details in excel"></i>
               </a>
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
<script>
    const pending_po_excel=document.getElementById("pending_po_excel");
    function creatModel(){
        const div=document.createElement("div");
        div.setAttribute("id","model_div");
        div.classList.add("open_model");
        if(div.classList.contains("open_model")){
            div.classList.remove("open_model");
        }
    }
    function OverView(){
        console.log("sddc");
        creatModel();
    }
    document.addEventListener("DOMContentLoaded", function () {
        const pending_po_excel = document.getElementById("pending_po_excel");

        function ReportGenerateData(){
            console.log("trigger click");
            pending_po_excel.click();
        }
        setTimeout(function (){
            //ReportGenerateData();
        }, 1000);
    });
</script>
</body>
</html>