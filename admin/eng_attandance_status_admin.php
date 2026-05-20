<?php
require_once("../includes/config.php");
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);

$arr_locstr = $_REQUEST['locationname'];
			for($i=0; $i<count($arr_locstr); $i++){
				if($locstr){
					$locstr.="','".$arr_locstr[$i];
					$locstr.= $arr_locstr[$i];
				}
			}
$arr_engstr = $_REQUEST['eng_name'];
			for($i=0; $i<count($arr_engstr); $i++){
				if($engstr){
					$engstr.="','".$arr_engstr[$i];
				}else{
					$engstr.= $arr_engstr[$i];
				}
			}
//print_r($locstr);exit;

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
 
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">

 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" language="javascript" >
/*$(document).ready(function(){
    $('#myTable').dataTable();
});*/
	 
$(document).ready(function() {
	var dataTable = $('#bank-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		"order": [[ 2, "asc" ]],
		"ajax":{
			url :"../pagination/eng-app-attance-data-admin.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>", "loc": "<?=base64_encode($_REQUEST['locationname']);?>", "eng": "<?=base64_encode($_REQUEST['eng_name']);?>"},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".bank-grid-error").html("");
				$("#bank-grid").append('<tbody class="bank-grid-error"><tr><th colspan="7">No data found in the server</th></tr></tbody>');
				$("#bank-grid_processing").css("display","none");
				
			}
		}
	} );
} );
$(document).ready(function() {
	$('#locationname').multiselect({
		    enableFiltering: true,
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});	 
$(document).ready(function() {
	$('#eng_name').multiselect({
		    enableFiltering: true,
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
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
      <h2 align="center"><i class="fa fa-clock-o"></i> Attandence </h2>
      <?php if($_REQUEST['msg']){?>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
        <?php }?> 
	  <form class="form-horizontal" role="form" name="form1" action="" method="post">
	   <div class="form-group">
         <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-6" align="left">
			 
            </div>
          </div>
	    </div><!--close form group-->
	    <div class="form-group">
		 <div class="col-md-6"><label class="col-md-5 control-label"> Location </label>
			<div class="col-md-5" align="left">
			 	  <select name="locationname[]" multiple="multiple" id="locationname" class="form-control"  onChange="document.form1.submit();">
					  <!--<option value="All" <?php if($_REQUEST['locationname']=="All") { echo 'selected'; }?>>All</option>-->
					  <?php
						$location_query="SELECT locationname, location_code FROM location_master where stateid in ($arrstate) order by locationname ";
						$loc_res=mysqli_query($link1,$location_query);
						while($loc_info = mysqli_fetch_array($loc_res)){?>
					 <!-- <option value="<?=$loc_info['location_code']?>" <?php if($_REQUEST['locationname'] == $loc_info['location_code']) { echo 'selected'; }?>><?=$loc_info['locationname']?></option>-->
					  <option value="<?=$loc_info['location_code']?>" <?php for($i=0; $i<count($arr_locstr); $i++){if($arr_locstr[$i]==$loc_info['location_code']) { echo 'selected'; } }?>><?=$loc_info['locationname']?></option>
						<?php }  ?>
					  
					  
					  
                  </select>
            </div>
          </div>
         <div class="col-md-6"><label class="col-md-5 control-label"> Engineer </label>	  
			<div class="col-md-5" align="left">
			   <select name="eng_name[]" id="eng_name" class="form-control" multiple="multiple"  onChange="document.form1.submit();">
			   		<!--<option value=""<?php if($_REQUEST['eng_name']==''){ echo "selected";}?>>All</option>-->
			   		<?php
						if($_REQUEST['locationname']!="All"){ $loc_str = " location_code = '".$_REQUEST['locationname']."' "; 
						//$usr_qr = mysqli_query($link1, "select userloginid, locusername from locationuser_master where $loc_str order by  locusername ");
						$usr_qr = mysqli_query($link1, "select userloginid, locusername from locationuser_master where location_code in('".$locstr."') order by  locusername ");
						while($usr_row = mysqli_fetch_array($usr_qr)){
					?>
                    <!--<option value="<?=$usr_row['userloginid'];?>"<?php if($_REQUEST['eng_name']==$usr_row['userloginid']){ echo "selected";}?>><?=$usr_row['locusername'];?></option>-->
				    <option value="<?=$usr_row['userloginid'];?>" <?php for($i=0; $i<count($arr_engstr); $i++){if($arr_engstr[$i]==$usr_row['userloginid']) { echo 'selected'; } }?>><?=$usr_row['locusername'];?></option>
					<?php }} ?>
                </select>
            </div>
          </div>
	    </div><!--close form group-->
		
		<div class="form-group">
		  <div class="col-md-6" style="text-align:center;">
				<input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
          </div>
		  <div class="col-md-6" style="text-align:center;">
		  <?php if($_REQUEST['Submit']=="GO"){ 
			$locstr1 = "";  
	$arr_locstr = $_REQUEST['locationname'];
			for($i=0; $i<count($arr_locstr); $i++){
				if($locstr){
					$locstr.="','".$arr_locstr[$i];
				}else{
					$locstr.= $arr_locstr[$i];
				}
			}
	$engstr1="";
$arr_engstr = $_REQUEST['eng_name'];
			for($i=0; $i<count($arr_engstr); $i++){
				if($engstr){
					$engstr.="','".$arr_engstr[$i];
				}else{
					$engstr.= $arr_engstr[$i];
				}
			}
			  
			  
			  ?>
			  
		  	<!--<a href="excelexport.php?rname=<?=base64_encode("attendancemaster");?>&rheader=<?=base64_encode("Attendance Master");?>&daterange=<?=base64_encode($_REQUEST['daterange'])?>&loc=<?=base64_encode($engstr);?>&eng=<?=base64_encode($engstr);?>" title="Export bank details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export details in excel"></i></a>-->
			  <a href="excelexport.php?rname=<?=base64_encode("attendancemaster");?>&rheader=<?=base64_encode("Attendance Master");?>&daterange=<?=base64_encode($_REQUEST['daterange'])?>&eng=<?=base64_encode($engstr1);?>" title="Export bank details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export details in excel"></i></a>
			  
			 
			<?php } ?>
          </div>
	    </div><!--close form group-->
		
	  </form>
      <form class="form-horizontal" role="form">
     &nbsp;&nbsp;
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="bank-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
			  <th>Engineer Name</th>
			  <th>IN Date</th>
              <th>In Address</th>
              <th>Out Date</th>
			  <th>Out Address</th>
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