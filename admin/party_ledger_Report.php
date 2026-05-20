<?php
require_once("../includes/config.php");
$statename = $_REQUEST['statename'];
$locationname=$_REQUEST['locationname'];
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);

////////////////////////// get city ad location /////////////////////////////////////
$arr_statestr = $_REQUEST['statename'];
			for($i=0; $i<count($arr_statestr); $i++){
				if($statestr){
					$statestr.="','".$arr_statestr[$i];
				}else{
					$statestr.= $arr_statestr[$i];
				}
			}
			
$arr_locationstr = $_REQUEST['locationname'];
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
 $(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});
 
 loc = [];
<?php for($i=0; $i<count($arr_locationstr); $i++){ ?>
 loc[<?=$i?>] = '<?=$arr_locationstr[$i]?>';
<?php }?>
 $(document).ready(function() {
	var dataTable = $('#party-ledger-grid').DataTable( {
		"processing": true,
		"serverSide": true,
		//"order": [[ 3, "asc" ]],
		"ajax":{
			url :"../pagination/party-ledger-grid-data.php", // json datasource
			data: { "pid": "<?=$_REQUEST['pid']?>", "hid": "<?=$_REQUEST['hid']?>" ,"daterange": "<?=$_REQUEST['daterange']?>", "location": loc},
			type: "post",  // method  , by default get
			error: function(){  // error handling
				$(".party-ledger-grid-error").html("");
				$("#party-ledger-grid").append('<tbody class="party-ledger-grid-error"><tr><th colspan="11">No data found in the server</th></tr></tbody>');
				$("#party-ledger-grid_processing").css("display","none");
				
			}
		}
	} );
} );

$(document).ready(function() {
	$('#statename').multiselect({
			includeSelectAllOption: true,
			buttonWidth:"200"
   
	});
});

$(document).ready(function() {
	$('#locationname').multiselect({
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
<script src="../js/bootstrap-datepicker.js"></script>
 <!-- Include multiselect -->
<script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-check"></i>Party Ledger</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <br></br>
	  <form class="form-horizontal" role="form" name="form1"  id="form1" action="" method="post">
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
         <div class="col-md-6"><label class="col-md-5 control-label">State<span style="color:#F00">*</span></label>	  
			<div class="col-md-6" >
				<select   name="statename[]" id="statename" multiple="multiple" class="form-control required" onChange="document.form1.submit();" required>
				<?php 
                $state = mysqli_query($link1,"select stateid, state from state_master  where stateid in ($arrstate)" ); 
                while($stateinfo = mysqli_fetch_assoc($state)){ 
				?>		
             <option value="<?=$stateinfo['stateid']?>" <?php for($i=0; $i<count($statename); $i++){if($statename[$i]==$stateinfo['stateid']) { echo 'selected'; } }?>><?=$stateinfo['state']?></option>
                <?php }?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Location<span style="color:#F00">*</span></label>	  
			<div class="col-md-5" id="citydiv">
                 <select name="locationname[]" id="locationname"  multiple="multiple" class="form-control required"  onChange="document.form1.submit();"required>
				  <?php
				   $location_query="SELECT locationname, location_code FROM location_master where stateid in('$statestr')  ";
     $loc_res=mysqli_query($link1,$location_query);
     while($loc_info = mysqli_fetch_array($loc_res)){?>
				  <option value="<?=$loc_info['location_code']?>" <?php for($i=0; $i<count($locationname); $i++){if($locationname[$i] == $loc_info['location_code']) { echo 'selected'; }}?>><?=$loc_info['locationname']?></option>
				<?php }  ?>
                 </select>
              </div>
          </div>
	    </div>
				
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label"></label>
            <div class="col-md-5" >
			 <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">  
             
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
             
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
        <?php if ($_REQUEST['Submit']){
	
			   			
			//// array initialization to send by query string of  location
			$locationstr = "";
			$arr_loc = $_REQUEST['locationname'];
			for($i=0; $i<count($arr_loc); $i++){
				if($locationstr){
					$locationstr.="','".$arr_loc[$i];
				}else{
					$locationstr.= $arr_loc[$i];
				}				
			}	 	
		   ?>
           <div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
               <?php if ($_REQUEST['statename'] == '' ) {?>		
			<?php  }else {?>
               <a href="../excelReports/partyledger_excel.php?location=<?=$locationstr?>&daterange=<?=$_REQUEST['daterange']?>" title="Export Party Ledger details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Party Ledger details in excel"></i></a>
               <?php
				}
				?>
            </div>
          </div>
	    </div><!--close form group-->
		 <?php }?>
	  
      <form class="form-horizontal" role="form">
        <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <!--<div class="form-group table-responsive"  id="page-wrap" style="margin-left:10px;"><br/><br/>-->
       <table  width="100%" id="party-ledger-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th>S.No</th>
              <th>Trasaction Details</th>
			  <th>Trsacation Type</th>
			  <th>Trasaction Date</th>         
               <th>Amount Cr</th>
              <th>Amount Dr</th>
            </tr>
          </thead>
          </table>
          </div>
      <!--</div>-->
      </form>
    </div>    
  </div>
</div>
<script>
</script>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>