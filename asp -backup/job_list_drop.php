<?php
require_once("../includes/config.php");
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

$array_status = array();
$arr_selstatus = $_REQUEST['job_status'];
for($i=0; $i<count($arr_selstatus); $i++){
	$array_status[$arr_selstatus[$i]] = "Y";
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
  $(document).ready(function(){
	$('#brand').change(function(){
	  var brandid=$('#brand').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{filterbrand:brandid},
		success:function(data){
	    $('#modeldiv').html(data);
	    }
	  });
    });
  });
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
			url :"../pagination/job-grid-data_drop.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>", "info": info, "product_name": "<?=$_REQUEST['product_name']?>", "brand": "<?=$_REQUEST['brand']?>", "location_code": "<?=$_REQUEST['location_code']?>", "modelid": "<?=$_REQUEST['modelid']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".job-grid-error").html("");
				$("#job-grid").append('<tbody class="job-grid-error"><tr><th colspan="20">No data found in the server</th></tr></tbody>');
				$("#job-grid_processing").css("display","none");
				
			}
		}
	} ).on( 'draw', function () {
		checkrow();
	});
} );
function checkrow(){
	var table = $('#joblist-grid').DataTable();
	var data = table.rows().data();
	for(var i=0; i < data.length; i++){
		if(data[i][13] > 3){
	 		$('#joblist-grid tr').eq(i+1).css('color', 'red');
		}
	}
}

////// function for Stock Details to see the task history
function checkvisittime(job_id){
	$.get('doc_visit_time.php?job_no=' + job_id, function(html){
		 $('#mappedModel .modal-body').html(html);
		 $('#mappedModel').modal({
			show: true,
			backdrop:"static"
		});
	 });
}
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
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-list-alt"></i>Jobs Status- Ready to Deliver(Drop)</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?>
        <?php  
if($_REQUEST['to']!='' && ( $_REQUEST['status']==6 || $_REQUEST['status']=="HG" ||  $_REQUEST['status']=="2"   ))
	{ ?>
	
	<?php /* ?>
	<iframe src="http://203.212.70.200/smpp/sendsms?username=lazer&password=lazer7891&to=<?=$_REQUEST['to']?>&from=LAZERC&text=<?=base64_decode($_REQUEST['smsmsg'])?>"  width="1" height="1" scrolling="No" style="background:#00FF33"></iframe><?php */ ?>
	
<!--	<iframe src="http://sms.rvsolutions.in/smsapi/api/notification/sms?ApiKey=fc2a71e0-04ae-4488-8c1d-90c85d336d8f&apisecret=PathOnSite&number=<?=$_REQUEST['to']?>&message=<?=base64_decode($_REQUEST['smsmsg'])?>"  width="1" height="1" scrolling="No" style="background:#00FF33"></iframe>-->
	
<?php }?>
	  	<form class="form-horizontal" role="form" name="form1" action="" method="get">
	    	<div class="form-group">
         		<div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
					<div class="col-md-6 input-append date" align="left">
			 			<div style="display:inline-block;float:left">
                        	<input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/>
                        </div>
                        <div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            		</div>
          		</div>
		  		<div class="col-md-6"><label class="col-md-4 control-label">Job Status</label>	  
					<div class="col-md-5" align="left">
			  			<select id="example-multiple-selected" multiple="multiple" name="job_status[]" class="form-control">
                            <option value="open">Open Call</option>
                            <option value="close">Close Call</option>
                        </select>
            		</div>
          		</div>
	    	</div><!--close form group-->
        	<div class="form-group">
         		<div class="col-md-6"><label class="col-md-5 control-label">Product</label>	  
					<div class="col-md-6" align="left">
                         <select name="product_name" id="product_name" class="form-control">
                            <option value="">All</option>
                            <?php
                            $dept_query="SELECT * FROM product_master where status = '1' and product_id in (".$access_product.") order by product_name";
                            $check_dept=mysqli_query($link1,$dept_query);
                            while($br_dept = mysqli_fetch_array($check_dept)){
                            ?>
                            <option value="<?=$br_dept['product_id']?>"<?php if($_REQUEST['product_name'] == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>
                           <?php }?>
                         </select>
            		</div>
          		</div>
		  		<div class="col-md-6"><label class="col-md-4 control-label">Brand</label>	  
					<div class="col-md-6" align="left">
                    <select name="brand" id="brand" class="form-control">
                        <option value="">All</option>
                        <?php
                        $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";
                        $check_dept=mysqli_query($link1,$dept_query);
                        while($br_dept = mysqli_fetch_array($check_dept)){
                        ?>
                        <option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
                        <?php }?>
                    </select>
            		</div>
          		</div>
	    	</div><!--close form group-->
        	<div class="form-group">
         		<div class="col-md-6"><label class="col-md-5 control-label"><?=$locationstr?></label>	  
					<div class="col-md-6">
                        <select name="location_code" id="location_code" class="form-control">
                        <?php
                        $res_pro = mysqli_query($link1,"select location_code,locationname from location_master where location_code='".$_SESSION['userid']."'"); 
                        while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                        <option value="<?=$row_pro['location_code']?>" <?php if($_REQUEST['location_code'] == $row_pro['location_code']) { echo 'selected'; }?>><?=$row_pro['locationname']." (".$row_pro['location_code'].")"?></option>
                        <?php } ?>
                         </select>
              		</div>
          		</div>
		  		<div class="col-md-6"><label class="col-md-4 control-label">Model</label>	  
					<div class="col-md-6">
            			<div style="display:inline-block;float:left" id="modeldiv">
                			<select name="modelid" id="modelid" class="form-control" style="width:150px;">
                                <option value="">All</option>
                                <?php 
                                $model_query="SELECT model_id, model FROM model_master where brand_id='".$_REQUEST['brand']."' order by model";
                                $model_res=mysqli_query($link1,$model_query);
                                while($row_model = mysqli_fetch_array($model_res)){
                                ?>
                                <option value="<?=$row_model['model_id']?>"<?php if($_REQUEST['modelid']==$row_model['model_id']){ echo "selected";}?>><?php echo $row_model['model']?></option>
                                <?php }?>
                            </select>
                		</div>
                		<div style="display:inline-block;float:right">
                			<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
                		</div>
            		</div>
          		</div>
        </div>
        <!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){/*
		  	//// array initialization to send by query string
			$statusstr = "";
			$arr_selstatus = $_REQUEST['job_status'];
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
              <!-- <a href="excelexport.php?rname=<?=base64_encode("sample_report")?>&rheader=<?=base64_encode("Sample Report")?>&daterange=<?=$_REQUEST['daterange']?>&location_code=<?=base64_encode($_REQUEST['location_code'])?>&job_status=<?=base64_encode($statusstr);?>&start_date=<?=$_REQUEST['start_date']?>&end_date=<?=$_REQUEST['end_date']?>" title="Export Login Logout details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Login Logout details in excel"></i></a>-->
            </div>
          </div>
	    </div><!--close form group-->
        <?php */}?>
        <form class="form-horizontal" role="form" name="form2">
        <div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="joblist-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
           
              <th>S.No</th>
			    <th>Job No.</th>
			    <th>Customer Id.</th>
			    <th>Customer Name</th>
			   <th>Contact No.</th>
              <th>Brand</th>
			   <th>Product Category</th>
              <th>Model</th>
		      <th>Area Type</th>
              
              <th><?php echo SERIALNO ?></th>
			   <th>Call Type</th>
              <th>Open Date</th>
              <th>Close Date</th>
			  <th>Aging</th>
              <th>Status</th>
			  <th>Acknowledgement</th>
			  <th>Appointment</th>
			   <th>Assign</th>
              <th>Print</th>
			 <th>Action</th>
              <th>View</th>
            </tr>
          </thead>
          </table>
          </div>
      <!--</div>-->
      </form>
      <!-- Start Model Mapped Modal -->
          <div class="modal modalTH fade" id="mappedModel" role="dialog">
		      <form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center">Complaint Visit</h4>
                </div>
                <div class="modal-body modal-bodyTH">
                 <!-- here dynamic task details will show -->
                </div>
                 <div class="modal-footer">
                  <input type="submit" class="btn<?=$btncolor?>" name="upddckt" id="upddckt" value="Update" title="" <?php if($_POST['upddckt']=='Update'){?>disabled<?php }?>>
                  <button type="button" id="btnCancel" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </div>
              </div>
			     </form>
          </div><!--close Model Mapped modal-->
    </div>
  </div>
</div>
 	    
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>