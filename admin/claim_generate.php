<?php
require_once("../includes/config.php");
$_SESSION['messageIdentclaim'] = "";
////// get access staes //////
$states = getAccessState($_SESSION['userid'],$link1);

$today=date("Y-m-d");
$fyear=date("Y",strtotime("-1 months",strtotime($today)));
$fmonth=date("m",strtotime("-1 months",strtotime($today)));
$tyear=date("Y");
$tmonth=date("m");

 
//$claim_month=$fyear."-".$fmonth;
$to_date=$tyear."-".$tmonth."-"."05";

$from_date=$fyear."-".$fmonth."-"."05";
$claim_period=$from_date."To".$to_date;
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
      <h2 align="center"><i class="fa fa-check"></i>Claim Generate</h2>
	  <br></br>
	 <form class="form-horizontal" id="form1" name="form1" action="" method="post"> 
	  <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Month</label>	  
			<div class="col-md-6">
				<select class="form-control" name="month" id="month" >
					<option value=""   <?php if($_REQUEST['month']==""){ echo "selected"; } ?> >Please Select</option>
					<option value="01" <?php if($_REQUEST['month']=="01"){ echo "selected"; } ?> >Jan</option>
					<option value="02" <?php if($_REQUEST['month']=="02"){ echo "selected"; } ?> >Fab</option>
					<option value="03" <?php if($_REQUEST['month']=="03"){ echo "selected"; } ?> >Mar</option>
					<option value="04" <?php if($_REQUEST['month']=="04"){ echo "selected"; } ?> >Apr</option>
					<option value="05" <?php if($_REQUEST['month']=="05"){ echo "selected"; } ?> >May</option>
					<option value="06" <?php if($_REQUEST['month']=="06"){ echo "selected"; } ?> >Jun</option>
					<option value="07" <?php if($_REQUEST['month']=="07"){ echo "selected"; } ?> >Jul</option>
					<option value="08" <?php if($_REQUEST['month']=="08"){ echo "selected"; } ?> >Aug</option>
					<option value="09" <?php if($_REQUEST['month']=="09"){ echo "selected"; } ?> >Sep</option>
					<option value="10" <?php if($_REQUEST['month']=="10"){ echo "selected"; } ?> >Oct</option>
					<option value="11" <?php if($_REQUEST['month']=="11"){ echo "selected"; } ?> >Nov</option>
					<option value="12" <?php if($_REQUEST['month']=="12"){ echo "selected"; } ?> >Dec</option>
				</select>
            </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Year</label>	  
			<div class="col-md-6">
				<?php
					$old = date("Y")-1;
					$curr = date("Y");
					$new1 = date("Y")+1;
					$new2 = date("Y")+2;
				?>
				<select class="form-control" name="year" name="year" >
				    <option value="" <?php if($_REQUEST['month']=="12"){ echo "selected"; } ?> >Please Select</option>
					<option value="<?php echo $old; ?>"  <?php if($_REQUEST['year']==$old){ echo "selected"; } ?> ><?php echo $old; ?></option>
					<option value="<?php echo $curr; ?>" <?php if($_REQUEST['year']==$curr){ echo "selected"; } ?> ><?php echo $curr; ?></option>
					<option value="<?php echo $new1; ?>" <?php if($_REQUEST['year']==$new1){ echo "selected"; } ?> ><?php echo $new1; ?></option>
					<option value="<?php echo $new2; ?>" <?php if($_REQUEST['year']==$new2){ echo "selected"; } ?> ><?php echo $new2; ?></option>
				</select>
            </div>
          </div>
	    </div><!--close form group-->
   
				
        <div class="form-group" >
          <div class="col-md-6"><label class="col-md-5 control-label">Location</label>
            <div class="col-md-6" id="location">
             <select name="location_code" id="location_code" class="form-control required"   >
              <option value=''>--Please Select-</option>
                <?php
                $res_maploc = mysqli_query($link1,"select location_code,locationname from location_master  where locationtype != 'WH' order by locationname"); 
                while($row_maploc = mysqli_fetch_assoc($res_maploc)){
				
					?>
                <option value="<?=$row_maploc['location_code']?>" <?php if($_REQUEST['location_code'] == $row_maploc['location_code']) { echo 'selected'; }?>><?=$row_maploc['locationname']." (".$row_maploc['location_code'].")"?></option>
                <?php } ?>
              
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
	<form class="form-horizontal" role="form" method="post" action="claim_gen_add.php">
	<div class="panel panel-info">
	<div class="panel-body">
      	<table class="table table-bordered" width="100%">
           <thead>
            <tr>
            	<td><strong>S.No.</strong></td>
            <td><strong>Brand</strong></td>
			 	<td><strong>Product</strong></td>
			  <td><strong>Level</strong></td>
				<td><strong>Price</strong></td>
				<td><strong>Cost</strong></td>
				<td><strong>Total</strong></td>
             </tr>
            </thead>
			 <tbody>
                  <?php
				  										
					$hDate = $_REQUEST['year']."-".$_REQUEST['month']."-01";
					$time = strtotime($hDate);
				 $mont = date("Y-m-d", strtotime("+1 month", $time));
					$sel_month = $_REQUEST['year']."-".$_REQUEST['month'];
				    ///////////////////////////fetching data from Claim For Samrt Phone//////////////////////////////////////////					
					$myqr = "SELECT *, COUNT(CASE WHEN status = 48 then 1 ELSE NULL END) as count1, COUNT(CASE WHEN status = 6 then 1 ELSE NULL END) as count3, COUNT(CASE WHEN status = 8 then 1 ELSE NULL END) as count4 from job_claim_appr  where action_by = '".$_REQUEST['location_code']."' and  app_status='Y' and  hand_date < '".$mont."' and lb_claim_no='' group by brand_id, product_id, rep_lvl ";
					
					//echo $myqr."<br><br>";
					
					$sql = mysqli_query($link1, $myqr);
				  
					$j = 1;
					$loc = mysqli_query($link1,"SELECT fix_claim,fix_mnth,gstno ,fix_month_counter FROM location_master  where location_code='".$_REQUEST['location_code']."' ");
					$loc_row = mysqli_fetch_array($loc);
					
					if($loc_row['fix_mnth']!= $loc_row['fix_month_counter'] &&  $loc_row['fix_claim']>0.00 && $loc_row['fix_mnth']>0){ 
				  ?>
						  
				  <tr>  
				  	<td>1</td>
					<td>&nbsp;</td>  
					<td>Fixed Claim : <input type="hidden" name="rep_lvl1"  id="rep_lvl1" value="Fixed Claim" /></td>
					<td>&nbsp;</td>
					<td><?php echo $loc_row['fix_claim']; ?> <input type="hidden" name="level_price1"  id="level_price1" value="<?=$loc_row['fix_claim'];?>" /></td>
					<td><?php echo $loc_row['fix_claim']; ?> <input type="hidden" name="cost_avl1"  id="cost_avl1" value="<?=$loc_row['fix_claim'];?>" /></td> 
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				  </tr>
				  <tr> 
				    <td colspan="9"> <input type="hidden" name="row_count"  id="row_count" value="2" /> </td>
				  </tr>  
				<?php 
					} else {		  
					if (mysqli_num_rows($sql)>0  ){
					$tot_c1 = 0;
					$tot_call = 0;
					$tot_c2 = 0;
					$tot_c3 = 0;
					$tot_c4 = 0;
					$g_tot = 0;
					$data_iw_inst = 0;
					$data_iw_npu = 0;
					$data_iw_pu = 0;
					$data_repl_prc = 0;
					$status_info = "";
					while ($row = mysqli_fetch_array($sql)){
					////// status find out /////
					if($row['status'] == 48){
						$status_info = "48";
					}else if($row['status'] == 8){
						$status_info = "8";
					}else{
						if($row['part_repl'] == "Y"){
							$status_info = "6WP";
						}else{
							$status_info = "6NP";
						}
					}
				?>
                  <tr>
					 <td><?=$j;?></td>
					
					 <td align="left">
						 <?=getAnyDetails($row['brand_id'],"brand","brand_id","brand_master",$link1)?>
						 
					 </td>
					 <td align="left">
						 <?=getAnyDetails($row['product_id'],"product_name","product_id","product_master",$link1)?>
						 <input type="hidden" name="product<?=$j?>"  id="product<?=$j?>" value="<?=$row['product_id'];?>" />
					 </td>
					  <td align="left">
						<?=$row['rep_lvl'];?>
					 </td>
					 <td align="left">
					
					<?php  
					 	$price = mysqli_query($link1,"SELECT * FROM claim_master  where product_id='".$row['product_id']."' and brand_id='".$row['brand_id']."' and status='1'   and   level='".$row['rep_lvl']."' and  party='ALL' ") or die(mysqli_error($link1));
					 	$price_r= mysqli_fetch_array($price);
						/////// find the area type //////
						if(strtoupper($row['area_type'])=='UPCOUNTRY'){
						
							echo $price_r['level_value'];
						
						}else{
							
						echo $price_r['level_value'];
						}
					?>
				
					<td>
						<?php $c3 = (($row['count1']+$row['count3']+$row['count4'])* $price_r['level_value']); echo $row['count3']." x ".$data_iw_npu." = ".$c3;?>
						<input type="hidden" name="job_count_c3<?=$j?>"  id="job_count_c3<?=$j?>" value="<?=$row['count3'];?>" />
						<input type="hidden" name="price_c3<?=$j?>"  id="price_c3<?=$j?>" value="<?=$data_iw_npu;?>" />
					</td>   
					
					<td>
						<?php $sum_c = ($c1 + $c2 + $c3 + $c4); echo $sum_c; ?>
						<input type="hidden" name="cost_avl<?=$j?>"  id="cost_avl<?=$j?>" value="<?=$sum_c;?>" />
					</td>        
               		</tr>
					<?php
						$j++;
						$tot_call += $row['count1']+$row['count3']+$row['count4'];
						$tot_c1 += $c1;
						$tot_c2 += $c2;
						$tot_c3 += $c3;
						$tot_c4 += $c4;
						$g_tot = ($tot_c1 + $tot_c2 + $tot_c3 + $tot_c4);
						}
			         } 
					 ?>  
					  <tr> 
					    <td >Total Call <?php echo $tot_call; ?></td>
						  <td   colspan="2" align="right">Total</td>
						  
						  <td ><?php echo $g_tot; ?></td>
					  </tr>
                        <input type="hidden" name="row_count"  id="row_count" value="<?=$j?>" />
                      <?php }  ?>
       				<tr>
						<td align="center" colspan="9">  
							<input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Generate" title="Generate Claim">
							<input type="hidden" name="location_code"  id="location_code" value="<?=$_REQUEST['location_code']?>" />
							<input type="hidden" name="claim_month"  id="claim_month" value="<?=$sel_month?>" />
							<input type="hidden" name="mont"  id="mont" value="<?=$mont?>" />
							<input type="hidden" name="claim_month_eg"  id="claim_month_eg" value="<?=$sel_month_cr?>" />
							<input type="hidden" name="gst"  id="gst" value="<?=$loc_row['gstno']?>" />
						</td>
					</tr>
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