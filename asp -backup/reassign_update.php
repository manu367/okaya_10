<?php
require_once("../includes/config.php");
/////get status//
$today=date("Y-m-d",$time_zone);
$job_sql="SELECT * FROM jobsheet_data where job_no='".$_REQUEST['job_no']."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
@extract($_POST);
////// case 1. if we want to update details

////// case 2. if we want to Add new user
if($_POST){
	//// initialize transaction parameters
	$flag=true;
mysqli_autocommit($link1, false);
$error_msg="";
    if ($_POST['upd']=='Update'){
    /////////  checking  sfr Transaction////////////////////////////
		   


   //////////////////////////////// Insert call  history//////////////////////////////////////
    $flag2 = callHistory($_POST['job_no'],$_SESSION['asc_code'],"2","Re-Assigned to Engineer","Re-Assigned to Engineer",$eng_name,$ws,$_REQUEST['els_status'],"","",$ip,$link1,$flag);

	  
	  /////////////////////////////////////Job Sheet data////////////////////////////////////////////////////////
	 
   $up_job=mysqli_query($link1,"update jobsheet_data set eng_id='".$eng_name."' ,pen_status='2' where job_no='".$_POST['job_no']."'" );

   if (!$up_job) {
    $flag = false;
   $msg = "Error details2.1: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:job_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }

  else  if (!$flag2) {
    $flag = false;
   $msg = "Error details2.2: " . mysqli_error($link1) . ".";
    mysqli_rollback($link2);
 	mysqli_close($link1);
 header("location:job_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  }
  
  else {
	  $eng_det = mysqli_fetch_assoc(mysqli_query($link1,"select locusername, contactmo from locationuser_master where userloginid='".$eng_name."'"));
	  $cust_det = mysqli_fetch_assoc(mysqli_query($link1,"select address1, 	customer_name ,mobile  from customer_master where customer_id='".$job_row["customer_id"]."'"));
	  $prod_det = mysqli_fetch_assoc(mysqli_query($link1,"select product_name from product_master where product_id='".$job_row["product_id"]."'"));
	  $brand_det = mysqli_fetch_assoc(mysqli_query($link1,"select brand from brand_master where brand_id='".$job_row["brand_id"]."'"));
	  ///////////////
	  $sms_msg = "Dear ".$cust_det['customer_name'].",  our service engineer  Mr ".$eng_det['locusername']."  ".$eng_det['contactmo']."   will contact you shortly for attending your complaint ".$_POST['job_no']."  You may also contact to him";
	 // $msg_to_eng = "SR No. ".$job_row["imei"].", Customer- ".$job_row["customer_name"].", ".$job_row["contact_no"]." ".$cust_det["address1"].", Product- ".$prod_det["product_name"]." ".$brand_det["brand"]." ".$job_row["model"]."";
   ////// message send to customer
 // $send_sms_to_cust = file_get_contents("http://sms.foxxglove.com/api/mt/SendSMS?user=cancrm&password=123456&senderid=CANCRM&channel=Trans&DCS=0&flashsms=0&number=8510044758&text=".$msg_to_cust);
  /////// message send to engineer
/*  $send_sms_to_eng = file_get_contents("http://sms.foxxglove.com/api/mt/SendSMS?user=cancrm&password=123456&senderid=CANCRM&channel=Trans&DCS=0&flashsms=0&number=".$eng_det["contactmo"]."&text=".$msg_to_eng);*/
  $cflag="success";
		$cmsg="Success";
		$msg="You have successfully Assign   job no ".$_REQUEST['job_no'];
		mysqli_commit($link1);
	header("location:job_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."&smsmsg=".base64_encode($sms_msg)."&to=".$cust_det['mobile']."&status=2".$pagenav);
  }
 


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
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <!-- Include Date Picker -->
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-list-alt"></i> Job Assign </h2>
      <h4 align="center">Job No.- <?=$_REQUEST['job_no']?></h4>
   <div class="panel-group">
     <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Location Details</div>
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
                <td><?php echo getAnyDetails($job_row["cust_problem"],"voc_desc","voc_code","voc_master",$link1);?><br/><?php 	$voc= explode(",",$job_row['cust_problem2']); 
			           $vocpresent   = count($voc);
					   if($vocpresent == '1'){
					   $name = getAnyDetails($voc[0],"voc_desc","voc_code","voc_master",$link1 );
					   }
					   else if($vocpresent >1){
					     $name ='';
					   for($i=0 ; $i<$vocpresent; $i++){					 
			 			$name.=  getAnyDetails($voc[$i],"voc_desc","voc_code","voc_master",$link1 ).",";
			 			}} echo $name;?><br/><?=$job_row['cust_problem3']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Job Type</label></td>
              <td><?=$job_row['call_type']?></td>
              <td><label class="control-label">Job For</label></td>
              <td><?=$job_row['call_for']?></td>
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
              <td width="49%"><label class="control-label">Engineer Name </label></td>
              <td width="51%"> <select name="eng_name" id="eng_name" class="form-control requried">
                <?php
				$qry_usr="Select * from locationuser_master where location_code = '".$_SESSION['asc_code']."' and statusid='1'";
$result_usr=mysqli_query($link1,$qry_usr);
		  while($arr_usr=mysqli_fetch_array($result_usr)){
		  ?>
                <option value="<?=$arr_usr['userloginid']?>" <?php if($_POST['eng_name']==$arr_usr['userloginid']) echo " selected"?>>
                  <?=$arr_usr['locusername']?>
                  </option>
                <?php
		  }
		  ?>
                </select>
                </select></td>
            </tr>
            <tr>
                 <td align="center" colspan="2"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_list.php?daterange=<?=$_REQUEST['daterange']?>&product_name=<?=$_REQUEST['product_name']?>&brand=<?=$_REQUEST['brand']?>&modelid=<?=$_REQUEST['modelid']?><?=$pagenav?>'"> <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Update" title="update els">  <input type="hidden" name="job_no"  id="job_no" value="<?=$job_row['job_no']?>" /></td>
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