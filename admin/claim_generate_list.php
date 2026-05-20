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
	var dataTable = $('#claim-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 0, "desc" ]],
		"ajax":{
			url :"../pagination/claimlist-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "month": "<?=$_REQUEST['month']?>", "status": "<?=$_REQUEST['status']?>", "location_code": "<?=$_REQUEST['location_code']?>", "year": "<?=$_REQUEST['year']?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".claim-grid-error").html("");
				$("#claim-grid").append('<tbody class="claim-grid-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
				$("#claim-grid_processing").css("display","none");
				
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
      <h2 align="center"><i class="fa fa-book"></i> ASP Claim Invoices</h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php unset($_POST);
		 $_SESSION['messageIdentclaim'] ="";
		 }?>
                 
        <?php  if($_REQUEST['to']!='')
	{ ?>
<?php }?>
 <form class="form-horizontal" id="form1" name="form1" action="" method="post">    
  <div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Month</label>	  
			<div class="col-md-6 input-append date" align="left">
			   <select name="month" id="month"  class="form-control required"  >
               <option value=''>--Please Select-</option>
       <option value="01" <?php if($_REQUEST['month']=="01") echo "selected";?>>January</option>
      <option value="02" <?php if($_REQUEST['month']=="02") echo "selected";?>>February</option>
      <option value="03" <?php if($_REQUEST['month']=="03") echo "selected";?>>March</option>
      <option value="04" <?php if($_REQUEST['month']=="04") echo "selected";?>>April</option>
      <option value="05" <?php if($_REQUEST['month']=="05") echo "selected";?>>May</option>
      <option value="06" <?php if($_REQUEST['month']=="06") echo "selected";?>>June</option>
      <option value="07" <?php if($_REQUEST['month']=="07") echo "selected";?>>July</option>
      <option value="08" <?php if($_REQUEST['month']=="08") echo "selected";?>>August</option>
      <option value="09" <?php if($_REQUEST['month']=="09") echo "selected";?>>September</option>
      <option value="10" <?php if($_REQUEST['month']=="10") echo "selected";?>>October</option>
      <option value="11" <?php if($_REQUEST['month']=="11") echo "selected";?>>November</option>
      <option value="12" <?php if($_REQUEST['month']=="12") echo "selected";?>>December</option>
              
                </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Year</label>	  
			<div class="col-md-5" align="left">
               <select name="year" id="year" class="form-control required"  >
               <option value=''>--Please Select-</option>
        <?php for($i=date("Y")-1; $i<=date("Y"); $i++){?>

<option value='<?=$i?>' <?php if($_REQUEST['year']==$i) echo "selected";?> ><?=$i?></option>";
		<?php }?>
                </select>
            </div>
          </div>
	    </div><!--close form group-->
        <!--close form group-->
         <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Location Name</label>
            <div class="col-md-6">
              <select name="location_code" id="location_code" class="form-control">
              <option value=''>--Please Select-</option>
              <?php
                $res_pro = mysqli_query($link1,"select location_code,locationname from location_master where locationtype = 'ASP' order by locationname"); 
                while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['location_code']?>" <?php if($_REQUEST['location_code'] == $row_pro['location_code']) { echo 'selected'; }?>>
                  <?=$row_pro['locationname']." (".$row_pro['location_code'].")"?>
                  </option>
                  <?php } ?>
              </select>
            </div>
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
            <!--   <a href="po_report.php?daterange=<?=$_REQUEST['daterange']?>&status=<?=$_REQUEST['status']?>" title="Export PO details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export PO details in excel"></i></a>-->
               <?php
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
		 <?php }?>
	  
      <form class="form-horizontal" role="form" id="frm1" name="frm1">
        <!--<button title="Location-wise PO" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='inventory_po_add.php?op=Add<?=$pagenav?>'"><span>Location-wise PO</span></button>&nbsp;&nbsp;-->
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="claim-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Document No.</th>
			  <th>Document Date</th>
			
              <th>Location Code</th>
              <th>Location Name</th>
              <th>Claim month</th>
                <th>Claim Release</th>
			  <th>Status</th>
            
			  <th>Print</th>
			
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