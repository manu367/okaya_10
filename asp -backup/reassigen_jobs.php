<?php
require_once("../includes/config.php");
/////get status//
$access_asp = getAccessASP($_SESSION['asc_code'],$link1);
$today=date("Y-m-d",$time_zone);
$job_sql="SELECT * FROM jobsheet_data where job_no='".$_REQUEST['job_no']."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
@extract($_POST);
////// case 1. if we want to update details

////// case 2. if we want to Add new user
if($_POST){
	//// initialize transaction parameters

    if ($_POST['upd']=='Update'){
    /////////  checking  sfr Transaction////////////////////////////
			$flag=true;
mysqli_autocommit($link1, false);
$error_msg="";   


   //////////////////////////////// Insert call  history//////////////////////////////////////
    $flag2 = callHistory($_POST['job_no'],$_SESSION['asc_code'],"1","Re-Assign","Re-Assign",$_SESSION['asc_code'],$ws,"","","",$ip,$link1,$flag);

	  
	  /////////////////////////////////////Job Sheet data////////////////////////////////////////////////////////
	 
   $up_job=mysqli_query($link1,"update jobsheet_data set current_location='".$_REQUEST['rep_location']."' ,doa_rej_rmk='',ack_remark='' where job_no='".$_POST['job_no']."'" );

   if ($flag2) {
                        mysqli_commit($link1);
                        $msg = "Successfully done with ref. no. " . $_POST['job_no'];
						$cflag = "success";
						$cmsg = "Success";
                    }
 header("location:reject_jobs_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);



 
 


}
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script>
$(document).ready(function(){
        $("#frm1").validate();
});
$(document).ready(function () {
	$('#release_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
});

	 function getmaploc(){
	
	  var pincode=$('#pincode').val();
	
	
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{Locpin:pincode},
		success:function(data){
	
	    $('#loc_pincode').html(data);
	    }
	  });
	
	};

</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body onLoad="getmaploc(<?=$row_customer['pincode']?>)" >
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-list-alt"></i>Reassign Jobs </h2>
      <h4 align="center">Job No.- <?=$_REQUEST['job_no']?></h4>
   <div class="panel-group">
     <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Customer Details</label></td>
                <td width="30%"><?php echo $job_row['customer_name']."<br>". $job_row['address'].",". $job_row['contact_no']?></td>
            
              </tr>
             
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Product</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
                <td width="20%"><label class="control-label">Brand</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
              </tr>
            <tr>
              <td><label class="control-label">Model</label></td>
              <td><?=$job_row['model']?></td>
              <td><label class="control-label">Warranty Status</label></td>
              <td><?php echo $job_row['warranty_status'];?>   <input type="hidden" name="warranty_status"  id="warranty_status" value="<?=$job_row['warranty_status']?>" /></td>
            </tr>
            <tr>
              <td><label class="control-label"><?php echo SERIALNO ?></label></td>
              <td><?=$job_row['imei']?></td>
              <td><label class="control-label">VOC</label></td>
              <td><?php 
			  
			 $voc= explode(",",$job_row['cust_problem2']); 
			           $vocpresent   = count($voc);
					   if($vocpresent == '1'){
					   $name = getAnyDetails($voc[0],"voc_desc","voc_code","voc_master",$link1 );
					   }
					   else if($vocpresent >1){
					     $name ='';
					   for($i=0 ; $i<$vocpresent; $i++){					 
			 			$name.=  getAnyDetails($voc[$i],"voc_desc","voc_code","voc_master",$link1 ).",";
			 			}}  
			  echo getAnyDetails($job_row["cust_problem"],"voc_desc","voc_code","voc_master",$link1),",".$name.",".$job_row['cust_problem3']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Job Type</label></td>
              <td><?=$job_row['call_type']?></td>
              <td><label class="control-label">Job For</label></td>
              <td><?=$job_row['call_for']?></td>
            </tr>
				 <tr>
              <td><label class="control-label">Acknowledge status</label></td>
              <td><?=$job_row['doa_rej_rmk']?></td>
              <td><label class="control-label">Acknowledge Remark</label></td>
              <td><?=$job_row['ack_remark']?></td>
            </tr>
			  <tr>
              <td><label class="control-label">Pincode</label></td>
              <td>  <input name="pincode" type="text" class="digits form-control required"   onKeyup="getmaploc(this.value);" maxlength="6" id="pincode" value="<?=$job_row['pincode']?>"></td>
              <td><label class="control-label">&nbsp;</label></td>
              <td>&nbsp;</td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Assign</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
              <td width="49%"><label class="control-label">Reassigen Job </label></td>
              <td width="51%"  id="loc_pincode"> <?php
			          
                      echo "<select  name='rep_location' id='rep_location' class='form-control required'>";

		//and location_code='".$_SESSION['asc_code']."'
		$pin_loc="select * from location_master where locationtype='ASP'  order by locationname  ";
		$loc_pin=mysqli_query($link1,$pin_loc);
		//echo "<option value=''>--Please Select--</option>";
		while($loc_cpin = mysqli_fetch_array($loc_pin)){
			//$loc_city=mysqli_query($link1,"SELECT cityid,city FROM  city_master where cityid='".$loc_cpin['cityid']."' ");
			//$row_city= mysqli_fetch_array($loc_city);
			echo "<option value='".$loc_cpin['location_code']."'>";
			echo $loc_cpin['location_code']." | ".$loc_cpin['locationname']." | ".getAnyDetails($loc_cpin['cityid'],"city","cityid","city_master",$link1)."</option>";
		
	}
     echo "</select>";
	 ?>
              </td>
            </tr>
            <tr>
                 <td align="center" colspan="2"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='reject_jobs_list.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="update els">  <input type="hidden" name="job_no"  id="job_no" value="<?=$job_row['job_no']?>" /></td>
               </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
 </form>
  </div><!--close panel group-->
 </div><!--close col-sm-9-->

  </div><!--close panel group-->
 </div><!--close col-sm-9-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>