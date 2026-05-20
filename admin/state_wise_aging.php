<?php
	require_once("../includes/config.php");
	////get access product details
	$access_product = getAccessProduct($_SESSION['userid'],$link1);
	////get access brand details
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
<!-- datatable plugin-->
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<!--  -->
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
				includeSelectAllOption: true,
				buttonWidth:"200",
				enableFiltering: true
		});
	});
	/////////// function to get model on the basis of brand
/*	  $(document).ready(function(){
		$('#brand').change(function(){
		  var brandid=$('#brand').val();
		  var product_name=$('#product_name').val();
		  $.ajax({
			type:'post',
			url:'../includes/getAzaxFields.php',
			data:{brandModel_product:brandid,product_id:product_name},
			success:function(data){
			$('#modeldiv').html(data);
			}
		  });
		});
	  });*/
	 ////// paging script 
	 info = [];
	<?php for($i=0; $i<count($arr_selstatus); $i++){ ?>
	 info[<?=$i?>] = '<?=$arr_selstatus[$i]?>';
	<?php }?>
	$(document).ready(function() {
		var dataTable = $('#joblist-grid').DataTable( {
			"processing": true,
			"serverSide": true,
			"order": [[ 0, "desc" ]],
			"ajax":{
				url :"../pagination/state_wise_madel_pending.php", // json datasource
				data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>", "info": info, "product_name": "<?=$_REQUEST['product_name']?>", "brand": "<?=$_REQUEST['brand']?>", "location_code": "<?=$_REQUEST['location_code']?>", "modelid": "<?=$_REQUEST['modelid']?>"},
				type: "post",  // method  , by default get
				error: function(){  // error handling
					$(".job-grid-error").html("");
					$("#joblist-grid").append('<tbody class="job-grid-error"><tr><th colspan="37">No data found in the server</th></tr></tbody>');
					$("#job-grid_processing").css("display","none");
					
				}
			}
		} );
	} );
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
<title>
<?=siteTitle?>
</title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <?php 
		include("../includes/leftnav2.php");
		?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h3 align="center"><i class="fa fa-check-square"></i> Daily Active Pending Complaints State Product Wise </h3>
      <?php if($_REQUEST['msg']){?>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        <strong>
        <?=$_REQUEST['chkmsg']?>
        !</strong>&nbsp;&nbsp;
        <?=$_REQUEST['msg']?>
        . </div>
      <?php }?>
      <form class="form-horizontal" role="form" name="form1" action="" method="get">
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label">Date Range</label>
            <div class="col-md-6 input-append date" align="left">
              <div style="display:inline-block;float:left">
                <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/>
              </div>
              <div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-5" align="left"> </div>
          </div>
        </div>
        <!--close form group-->
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label">Product</label>
            <div class="col-md-6" align="left">
              <select name="product_name" id="product_name" class="form-control" onChange="document.form1.submit();">
                <option value="">All</option>
                <?php
					$dept_query="SELECT * FROM product_master where status = '1' order by product_name";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
					?>
                <option value="<?=$br_dept['product_id']?>"<?php if($_REQUEST['product_name'] == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>
                <?php }?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-4 control-label">Brand</label>
            <div class="col-md-6" align="left">
              <select name="brand" id="brand" class="form-control" onChange="document.form1.submit();">
                <option value="">All</option>
                <?php
					$dept_query="SELECT * FROM brand_master where brand_id in ($access_brand) and status = '1'  order by brand";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
					?>
                <option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
                <?php }?>
              </select>
            </div>
          </div>
        </div>
        <!--close form group-->
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label">Model</label>
			    <div class="col-md-6">
             <div style="display:inline-block;float:right" id="modeldiv">
                <select name="modelid" id="modelid" class="form-control" style="width:150px;">
                  <option value="">All</option>
                  <?php 
						$model_query="SELECT model_id, model FROM model_master where brand_id='".$_REQUEST['brand']."' and  product_id='".$_REQUEST['product_name']."' order by model";
						$model_res=mysqli_query($link1,$model_query);
						while($row_model = mysqli_fetch_array($model_res)){
						?>
                  <option value="<?=$row_model['model_id']?>"<?php if($_REQUEST['modelid']==$row_model['model_id']){ echo "selected";}?>><?php echo $row_model['model']?></option>
                  <?php }?>
                </select>
              </div> </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-6">
          
              <div style="display:inline-block;float:right">
                <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
              </div>
            </div>
          </div>
        </div>
        <!--close form group-->
        <?php if ($_REQUEST['Submit']){		
			 
		
				
			  ?>
        <div class="form-group">
          <div class="col-md-10">
            <label class="col-md-4 control-label"></label>
            <div class="col-md-6" align="left"> 
			
			
			   <a href="excelexport.php?rname=<?=base64_encode("State_model_pending_report")?>&rheader=<?=base64_encode("State Madel Pending")?>&modelid=<?=$_GET['modelid']?>&product_id=<?=$_GET['product_name']?>&daterange=<?=$_GET['daterange']?>" title="Export employees details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export employees details in excel"></i></a> </div>
          </div>
        </div>
        <!--close form group-->
        <?php }?>
      </form>
      <form class="form-horizontal" role="form" name="form2">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
          <table  width="100%" id="joblist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
            <thead>
              <tr class="<?=$tableheadcolor?>">
                <th>S.No</th>
                <th>Model</th>
				  <th>Product</th>
                <th>Delhi</th>
                <th>Haryana</th>
                <th>Punjab</th>
                <th>Himachal Pradesh</th>
                <th>Jammu and Kashmir</th>
                <th>Uttar Pradesh</th>
                <th>Uttarakhand</th>
                <th>West Bengal</th>
                <th>Orissa</th>
                <th>Bihar</th>
                <th>Rajasthan</th>
                <th>Chandigarh</th>
                <th>Madhya Pradesh</th>
                <th>Maharashtra</th>
                <th>Chhattisgarh</th>
                <th>Goa</th>
                <th>Gujarat</th>
                <th>ANDAMAN AND NICOBAR ISLANDS</th>
                <th>Andhra Pradesh</th>
                <th>Arunachal Pradesh</th>
                <th>Assam</th>
                <th>Daman & Diu</th>
                <th>Jharkhand</th>
                <th>Karnataka</th>
                <th>Kerala</th>
                <th>Manipur</th>
                <th>Meghalaya</th>
                <th>Mizoram</th>
                <th>Nagaland</th>
                <th>Pondicherry</th>
                <th>Sikkim</th>
                <th>Tamilnadu</th>
                <th>Telangana</th>
                <th>Tripura</th>
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
