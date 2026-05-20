<?php
require_once("../includes/config.php");
$_SESSION['auditsaveclick'] = "";
//$yesterd = date('Y-m-d',strtotime("-1 days"));
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
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">

 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
/*$(document).ready(function(){
    $('#myTable').dataTable();
});*/
$(document).ready(function() {
	var dataTable = $('#stockaudit').DataTable( {
		"processing": true,
		"serverSide": true,
		"bStateSave": true,
		"ajax":{
			url :"../pagination/stockauditloc-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "locationcode": "<?=$_REQUEST['locationName']?>", "auditdate": "<?=base64_decode($_REQUEST['auditdate'])?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".stockaudit-error").html("");
				$("#stockaudit").append('<tbody class="stockaudit-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
				$("#stockaudit_processing").css("display","none");
				
			}
		}
	} );
} );
</script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#myTable').dataTable();
    });
	$(document).ready(function() {
		$("#frm2").validate();
	});
    </script>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-check-circle-o"></i> Stock Audit List</h2>
      <?php if(isset($_REQUEST['msg'])){?>
        <div class="alert alert-<?php echo $_REQUEST['chkflag'];?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?php echo $_REQUEST['chkmsg'];?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
                <form class="form-horizontal" role="form" name="form1" action="" method="post">
                	<div class="form-group">
                    	<div class="col-md-5"><label class="col-md-4 control-label">Self Audit Date</label>	  
                            <div class="col-md-4" align="left">
                                <select name="selyear" id="selyear" class="form-control" onChange="document.form1.submit();">
                                	<option value="" selected>--Select Year--</option>
									<?php 
                                    for($i=0; $i<3; $i++){ 
                                        $year = date('Y', strtotime(date("Y"). ' - '.$i.' year'));
                                    ?>
                                    <option value="<?=$year?>"<?php if($_REQUEST["selyear"]==$year){ echo "selected";}?>><?=$year?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4" align="left">
                            	<?php if($_REQUEST["selyear"]){ ?>
                            	<select name="selmonth" id="selmonth" class="form-control">
									<?php 
                                    ///// check if current year is selected then month should be come till current month
                                    if($_REQUEST["selyear"]==date("Y")){ $nmonth = date("m", strtotime(date("F")."-".$_REQUEST["selyear"]));}else{ $nmonth = 12;}
                                    for($j=0; $j<$nmonth; $j++){ 
                                        if($_REQUEST["selyear"]==date("Y")){ if($j==0){continue;}else{$month = date ( 'F' , strtotime ( "-".$j." month"	 , strtotime ( date("F") ) ));}}else{$month = date('F', strtotime(date("F"). ' + '.$j.' month'));}
                                    ?>
                                    <option value="<?=$month?>"<?php if($_REQUEST["selmonth"]==$month){ echo "selected";}?>><?=$month?></option>
                                    <?php } ?>
                                </select>
                                <?php }?>
                            </div>
                        </div>
						<div class="col-md-5"><label class="col-md-4 control-label">Location Name</label>	  
                            <div class="col-md-8" align="left">
                                <select name="locationName" id="locationName"  class="form-control selectpicker required" data-live-search="true">
                                    <!--<option value="" selected="selected">Please Select </option>-->
                                    <?php 
                                    $sql_chl="SELECT location_code,locationname,locationtype,cityid,stateid FROM location_master WHERE statusid='1' AND location_code='".$_SESSION["asc_code"]."' ORDER BY locationname";
                                    $res_chl=mysqli_query($link1,$sql_chl);
                                    while($result_chl=mysqli_fetch_array($res_chl)){
										////// get state name
										$statename= mysqli_fetch_assoc(mysqli_query($link1,"SELECT state FROM state_master WHERE stateid='".$result_chl['state']."'"));
                                    ?>
                                    <option data-tokens="<?=$result_chl['location_code']." | ".$result_chl['locationname']?>" value="<?=$result_chl["location_code"]?>" <?php if($result_chl['location_code']==$_REQUEST['locationName'])echo "selected";?>><?=$result_chl['locationname']." | ".$result_chl['locationtype']." | ".$statename['state']." | ".$result_chl['location_code']?>
                                    </option>
                                    <?php
                                    }
                                    ?>
                                 </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                        	<input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
                            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                            <input name="auditdate" id="auditdate" type="hidden" value="<?=base64_encode(date('Y-m-d', strtotime("last day of ".$_REQUEST["selmonth"]." ".$_REQUEST["selyear"])))?>"/>
                        </div>
                    </div>
                  </form>
      <form class="form-horizontal" role="form">
        <button title="Add New Audit" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='stock_audit_loc.php?op=add<?=$pagenav?>'"><span>Add New Audit</span></button>&nbsp;&nbsp;
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="stockaudit" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Location Name</th>
              <th>Ref. No.</th>
              <th>Stock Taken Date</th>
              <th>Entry Date</th>
              <th>Entry By</th>
              <th>Entry IP</th>
              <th>View</th>
              <th>Print</th>
              <th>Excel</th>
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