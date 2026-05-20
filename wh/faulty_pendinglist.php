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
 <script type="text/javascript" language="javascript" >

$(document).ready(function() {
	var dataTable = $('#faulty-pending').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 0, "asc" ]],
		"ajax":{
			url :"../pagination/faultypending-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>","locationcode": "<?=$_REQUEST['location_code']?>","status": "<?=$_REQUEST['status']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".faulty-pending-error").html("");
				$("#faulty-pending").append('<tbody class="faulty-pending-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
				$("#faulty-pending_processing").css("display","none");
				
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
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-cubes"></i> Faulty Pending List</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	 
		<div class="form-group">
         <div class="col-md-6" ><label class="col-md-5 control-label">Location</label>	  
			<div class="col-md-6"  >
			  <select name="location_code" id="location_code" class="form-control"  >
			  <option value="">Please Select</option>
                <?php
                $res_pro = mysqli_query($link1,"select from_location from part_to_credit where status ='1'  group by from_location"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){
				?>
                <option value="<?=$row_pro['from_location']?>" <?php if($_REQUEST['location_code'] == $row_pro['from_location']) { echo 'selected'; }?>><?=getAnyDetails($row_pro['from_location'],"locationname","location_code","location_master",$link1 )?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6" ><label class="col-md-5 control-label">Status</label>	  
			<div class="col-md-6">	
			<select name="status" id="status" class="form-control"  >
			<option value="">Please Select</option>
			<option value="1" <?php if($_REQUEST['status'] == '1' ){ echo "selected";} ?>>Pending at ASC</option>
	

			</select>
              </div>
          </div>
	    </div>
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5" align="left">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" >
               
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
       <?php if ($_REQUEST['Submit']){
	?>
        <div class="form-group">
		  <div class="col-md-10"><label class="col-md-4 control-label"></label>	  
			<div class="col-md-6" align="left">
               <a href="../excelReports/faultypending.php?location_code=<?=$_REQUEST['location_code'];?>&status=<?=$_REQUEST['status'];?>" title="Export details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Pending details in excel"></i></a>
            </div>
          </div>
	    </div><!--close form group-->
        <?php }?>
     <form class="form-horizontal" role="form" name="form2">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;">
       <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="faulty-pending" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr>
              <th>S.No</th>
			  <th>Location Name</th>
              <th>Job No</th>
              <th>IMEI</th>
              <th>Model</th>
              <th>Part</th>
            </tr>
          </thead>
          </table>
          </div>
      <!--</div>-->
      </form>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>