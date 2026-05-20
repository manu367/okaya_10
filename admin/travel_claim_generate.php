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
</script>
<!-- Include Date Range Picker -->
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
      <h2 align="center"><i class="fa fa-check"></i>Travel Claim Generate</h2>
	  <br></br>
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
   
				
        <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Location</label>
            <div class="col-md-5" id="location">
             <select name="location_code" id="location_code" class="form-control required"   >
               <option value=''>--Please Select-</option>
                <?php
                $res_maploc = mysqli_query($link1,"select location_code,locationname from location_master  where locationtype != 'WH' and locationtype != 'CC' order by locationname"); 
                while($row_maploc = mysqli_fetch_assoc($res_maploc)){
				
					?>
                <option value="<?=$row_maploc['location_code']?>" <?php if($_REQUEST['location_code'] == $row_maploc['location_code']) { echo 'selected'; }?>><?=$row_maploc['locationname']." (".$row_maploc['location_code'].")"?></option>
                <?php } ?>
              
                </select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
               <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">  
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
        <?php if ($_REQUEST['Submit']){
		   ?>
           <div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
            
            <!--   <a href="../excelReports/partyledger_excel.php?location=<?=$_REQUEST['location_code']?>&daterange=<?=$_REQUEST['daterange']?>" title="Export Party Ledger details in excel"><i class="fa fa-file-excel-o fa-2x faicon" title="Export Party Ledger details in excel"></i></a>-->
             
            </div>
          </div>
	    </div><!--close form group-->
			<br><br/>	 
	<form class="form-horizontal" role="form" method="post" action="claim_gen_add_travel.php">
	<div class="panel panel-info">
	<div class="panel-body">
      	<table class="table table-bordered" width="100%">
           <thead>
            <tr>
            <td><strong>S.No.</strong></td>
          
            <td><strong>Claim Type</strong></td>
             <td><strong>Distance Travel(In KM)</strong></td>
               <td><strong>Price (per KM)</strong></td>
            
			<td><strong>Cost</strong></td>
            
             </tr>
            </thead>
             
			 <tbody>
                  <?php
				  $sel_month="$_REQUEST[year]-$_REQUEST[month]";
				   $sel_month_cr="$_REQUEST[month]-$_REQUEST[year]";
					///////////////////////////fetching data from Claim For Samrt Phone//////////////////////////////////////////
					//echo "SELECT *,sum(travel_km) as sum_travel FROM job_claim_appr  where action_by = '".$_REQUEST['location_code']."'  and  app_status='Y' and hand_date like '%$sel_month%'  and claim_no='' group by action_by";
				  $sql_travel = mysqli_query($link1,"SELECT *,sum(travel_km) as sum_travel FROM job_claim_appr  where action_by = '".$_REQUEST['location_code']."'  and  app_status='Y' and hand_date like '%$sel_month%'  and claim_no='' group by action_by");
				  
				  $row_trvl=mysqli_fetch_array($sql_travel);
				
					 
							

		?>
						  
						  <tr>  <td>1</td>  <td>Travel Claim <input type="hidden" name="rep_lvl1"  id="rep_lvl1" value="Travel Claim" /></td><td>&nbsp;<?php echo $row_trvl['sum_travel']; ?><input type="hidden" name="count_lvl1"  id="count_lvl1" value="<?=$row_trvl['sum_travel'];?>"> </td><td ><?php echo "2.5";?><input type="hidden" name="level_price1"  id="level_price1" value="2.5" /></td><td ><?php echo $total_travl= $row_trvl['sum_travel']*2.5 ?> <input type="hidden" name="cost_avl1"  id="cost_avl1" value="<?=$total_travl;?>" /></td> </td></tr>
                         
                
                 
				
		
					 					  
					  <tr> <td  colspan="2"  align="right">Total</td><td ><?php echo $row_trvl['sum_travel']; ?></td><td >&nbsp;</td><td ><?php echo $total_travl; ?></td></tr>
                        <input type="hidden" name="row_count"  id="row_count" value="2" />
                      

       <tr><td align="center" colspan="5">  <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Generate" title="Generate Claim">
         
              <input type="hidden" name="location_code"  id="location_code" value="<?=$_REQUEST['location_code']?>" />
                 <input type="hidden" name="claim_month"  id="claim_month" value="<?=$sel_month?>" />
                  <input type="hidden" name="claim_month_eg"  id="claim_month_eg" value="<?=$sel_month_cr?>" />
                
               
              <input type="hidden" name="gst"  id="gst" value="<?=$loc_row['gstno']?>" />
            </td></tr>
            </tbody>
		  </table>
		</div>
	</div>
    
      <div class="form-group">
         
            
            </div>
      <!--</div>-->
      </form>
	 <?php }?>  
    </div>    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>