<?php
require_once("../includes/config.php");

///// if dispatch details is updated like courier or docket of any invoice



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
	var dataTable = $('#asc_app-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/asp-req-app-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "daterange": "<?=$_REQUEST['daterange']?>", "state": "<?=$_REQUEST['statename']?>", "city": "<?=$_REQUEST['locationcity']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".po-grid-error").html("");
				$("#asc_app-grid").append('<tbody class="po-grid-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
				$("#po-grid_processing").css("display","none");
				
			}
		}
	} );
} );
////// function for open model to update courier details

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
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-book"></i> 
ASC Appointment Request</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php unset($_POST);
		
		 }?>
                 
     
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">	   
	    <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-4 control-label">State</label>	  
			<div class="col-md-6" align="left">
		   <select   name="statename" id="statename"  class="form-control "  onChange="document.form1.submit();" >
                <option value=''<?php if($_REQUEST['statename']=="") { echo 'selected'; } ?>> ALL</option>
                
                <?php 
$state = mysqli_query($link1,"select stateid, state from state_master  where 1  order by state " ); 
while($stateinfo = mysqli_fetch_assoc($state)){ 
?>
                <option value="<?=$stateinfo['stateid']?>" <?php if($_REQUEST['statename']==$stateinfo['stateid']) { echo 'selected'; } ?>>
                <?=$stateinfo['state']?>
                </option>
                <?php }?>
              </select>
            </div>
          </div>
	    </div><!--close form group-->
        <!--close form group-->
         <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">City</label>
            <div class="col-md-6">
 <select name="locationcity" id="locationcity" class="form-control required" required >

                       <option value=''>--Please Select-</option>

                       <?php 

					  

						 $city_query="SELECT cityid, city FROM city_master where stateid='".$_REQUEST['statename']."' ";

						 $city_res=mysqli_query($link1,$city_query);

						 while($row_city = mysqli_fetch_array($city_res)){

						?>

						<option value="<?=$row_city['cityid']?>"<?php if($row_customer['cityid']==$row_city['cityid']){ echo "selected";}?>><?=$row_city['city']?></option>

						<?php }

					

						?>

                       </select>            </div>
	      </div>
          <div class="col-md-6"><label class="col-md-4 control-label"></label>
            <div class="col-md-6">
            <div style="display:inline-block;float:left">
       
            </div>
            <div style="display:inline-block;float:right">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
			   <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="GO"></div>
            </div>
	      </div>
         </div>
		  </form>
		   <?php if ($_REQUEST['Submit']){
		   ?>
         <div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
               <?php
			    //// get excel section id ////
				//$sectionid=getExlCnclProcessid("Admin Users",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$sectionid,$link1)==1){
			   ?>
          <!--     <a href="po_report.php?daterange=<?=$_REQUEST['daterange']?>&status=<?=$_REQUEST['status']?>" title="Export PO details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export PO details in excel"></i></a>-->
               <?php
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
		 <?php }?>
	  
      <form class="form-horizontal" role="form" id="frm1" name="frm1">
        <button title="Add  Appointment Request" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='asp_add_appo.php?op=add<?=$pagenav?>'"><span>Add  Appointment Request</span></button>&nbsp;&nbsp;
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="asc_app-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
			  <tr class="<?=$tableheadcolor?>">
				  <th>S.No</th>
				  <th>Request Date</th>
				  <th>Request No.</th>
				  <th>Appointment<br>Status</th>
				  <th>Status</th>
				  <th>State </th>
				  <th>City </th>
				  <th>View/Update</th>
				  <th>ASC Creation Request</th>
			  </tr>
          </thead>
       </table>
      </div>
      <!--</div>-->
      </form>
      <!-- Start Model Mapped Modal -->
          <div class="modal modalTH fade" id="courierModel" role="dialog">
          <form class="form-horizontal" role="form" id="frm2" name="frm2" method="post">
            <div class="modal-dialog modal-dialogTH">
            
              <!-- Modal content-->
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title" align="center"><i class='fa fa-truck fa-lg faicon'></i> Update Dispatch Details</h4>
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