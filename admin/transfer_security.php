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
 <script type="text/javascript">
    $(document).ready(function() {
        $("#form1").validate();
    });
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
  <!-- datatable plugin-->
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <!--  -->
 <script type="text/javascript" language="javascript" >
 $(document).ready(function() {
	var dataTable = $('#transfer-security-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 3, "asc" ]],
		"ajax":{
			url :"../pagination/transfer-security-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>"  ,"location_code": "<?=$_REQUEST['location_code']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".transfer-security-grid-error").html("");
				$("#transfer-security-grid").append('<tbody class="transfer-security-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#transfer-security-grid_processing").css("display","none");
				
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
    include("../includes/leftnav2.php");
    ?>
	  <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
		  <h2 align="center" style="border-bottom:1px solid #aaa8a8;padding:25px 0px;margin:0px;"><i class="fa fa-lock"></i> Transfer to Other A/C</h2>
		  <?php if($_REQUEST['msg']){?><br>
		  <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
		  <?php }?>
		  
		  <form class="form-horizontal" role="form" name="form1" action="" method="get">
			  <div class="form-group">
				  <div class="col-md-6" style="padding:10px 0px;">
					  <div class="col-md-4">
						  <label class="col-md-5 control-label">Location<span style="color:#F00">*</span></label>
					  </div>
					  <div class="col-md-5" align="left">
						  <select name="location_code" id="location_code" class="required form-control "  onChange="document.form1.submit();" required>
							  <option value=''>--Please Select-</option>
							  <?php
							  $res_maploc = mysqli_query($link1,"select location_code from map_wh_location where wh_location='".$_SESSION['asc_code']."' and status ='Y' "); 
							  while($row_maploc = mysqli_fetch_assoc($res_maploc)){
								  $locname = getAnyDetails($row_maploc['location_code'],"locationname","location_code","location_master",$link1);
							  ?>
							  <option value="<?=$row_maploc['location_code']?>" <?php if($_REQUEST['location_code'] == $row_maploc['location_code']) { echo 'selected'; }?>><?=$locname." (".$row_maploc['location_code'].")"?></option>
							  <?php } ?>
						  </select>
					  </div>
					  <div class="col-md-3" align="left">
						  <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
						  <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
						  <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
					  </div>
				  </div>			  
				  <div class="col-md-6" style="padding:10px 0px;">
					  <div class="col-md-10" align="left">
						  <label class="control-label"></label>
					  </div>
					  <div class="col-md-2" align="left">
					  </div>
				  </div>
			  </div>
		  </form>

		  <?php
	if($_REQUEST['Submit'])
	{
		  ?>
		  <div class="form-group">
			  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
				  <div class="col-md-5" align="left">
					  <?php if($_REQUEST['location_code'] == '' ) {?>		
					  <?php  }else {?>
					  <a href="../excelReports/security_transfer_excel.php?location=<?=base64_encode($_REQUEST['location_code'])?>" title="Export Account details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Security details in excel"></i></a>
					  <?php
									}
					  ?>
				  </div>
			  </div>
		  </div><!--close form group-->
		  <?php
	}
		  ?>
		  <div class="form-group" style="overflow:hidden;margin:0px;min-height:54px ">
			  <div class="col-md-12" style="padding:10px 0px;">
			  </div>
		  </div>
		  <form class="form-horizontal" role="form" method ="post">
			  <div class="form-group"  id="page-wrap">
				  <div class="col-md-12">
					  <table  width="100%" id="transfer-security-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
						  <thead>
							  <tr class="<?=$tableheadcolor?>">
								  <th>S.No</th>
								  <th>State</th>
								  <th>City</th>
								  <th>Location Name</th>
								  <th>Location Code</th>         
								  <th>Main A/C</th>
								  <th>Security A/C</th>
								  <th>Claim A/C</th>
								  <th>Last Update date</th>
								  <th>Transfer to Security</th>
								  <th>Transfer Claim to main </th>
							  </tr>
						  </thead>

					  </table>
				  </div>
			  </div>
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