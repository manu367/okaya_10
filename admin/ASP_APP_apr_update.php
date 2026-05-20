<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// job details

////// final submit form ////
if($_POST){
@extract($_POST);
	if($_POST['savermk']=='Save'){
		//// initialize transaction parameters
		$flag = true;
		mysqli_autocommit($link1, false);
		$error_msg="";
			
		/////////////////////  entry in call history table ///////////////////////////
	

$sql="update asc_appo_request set app_status='".$_POST['app_status']."',status='".$_POST['app_status']."',remark='".$_POST['app_remark']."',app_date='".$today."',app_by='".$_SESSION['userid']."' where sno='".$_REQUEST['sno']."'";
mysqli_query($link1,$sql)or die("error in insertion1".mysqli_error($link1));
if($_POST['app_status']=="14"){
	
	$app="ASC Appointment Request Approved";
	}else{
		$app="ASC Appointment Request Rejected";
		
		}

$sql1="INSERT INTO remark_master set req_id='".$_POST['request']."',module='APPO_APP',remark='".$_POST['app_remark']."', status='".$_POST['app_status']."', type='".$_SESSION['id_type']."',req_by='".$_SESSION['userid']."',outcome='".$app."'";
mysqli_query($link1,$sql1)or die("error in insertion2".mysqli_error($link1));

$uid=mysqli_query($link1,"select emailid from admin_users where username='".$_REQUEST['up_by']."'");
 //$email_to=mysql_fetch_array($email);
 
$cn1=mysqli_fetch_array($uid);

$email=mysqli_query($link1,"select email from email_user where type='admin'");
 //$email_to=mysql_fetch_array($email);
 
$cn=mysqli_num_rows($email);

$toemail="";
while($row=mysqli_fetch_array($email)){
	if($toemail==""){
	    $toemail.=$row['email'].",".$cn1['emailid'];
	}else{
		$toemail.=",".$row['email'].",".$cn1['emailid'];
	}
}
//echo $toemail;
if($_POST['app_status']=="APP"){
	
	$app1="Approved";
	}else{
		$app1="Rejected";
		
		}

$message = "Dear Sir ,<br />";
$message.="<br>Status of below  ticket  .<br />";
$message.="<br>Request No  :".$_POST['request']."<br />";
$message.="<br>Request Status: ".$app1."<br />";
$message.="<br>Remark: ".$_POST['app_remark']."<br />";

//$message.="<br>Kindly check your CRM id for approve the same<br />";


// Always set content-type when sending HTML email
$headers1 = "MIME-Version: 1.0\r\n";
$headers1 .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers1 .= "From:doNotReply@cancrm.in". "\r\n";
$subject = "ASC Appointment Request Status";
mail($toemail,$subject,$message ,$headers1);
	////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$_POST['request'],$app,"ASC Appointment Approval",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	///// check query are successfully executed
	if ($flag) {
		$cflag="success";

		$cmsg="Success";
		$msg = "Sucessfully update Remark of Request No.".$docid;
	    mysqli_commit($link1);
	} else {
	mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
	
   ///// move to parent page
  
header("location:asp_appo_detail_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

  exit;
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
</head>
<script>
function bigImg(x) {
  x.style.height = "300px";
  x.style.width = "300px";
}

function normalImg(x) {
  x.style.height = "100px";
  x.style.width = "100px";
}
</script>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
<div class="row content">
<?php 
    include("../includes/leftnav2.php");

	$arr_result = mysqli_fetch_assoc(mysqli_query($link1,"select * from asc_appo_request where sno='".$_REQUEST['sno']."'"));
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-ticket"></i> ASP Location Details</h2>
      <h4 align="center">Request No.- <?=$docid?></h4>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
      <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr  >
              <td height="26"><label class="control-label">Distributor Name&nbsp; </td>
        
              <td><?php echo $arr_result['name'] ?><input type="hidden" name="sno" id="sno" class=" inputtext" value="<?php echo $arr_result['sno'] ?>"></td>
              <td height="26"><label class="control-label">Association Tenure</td>
            
              <td><?php echo $arr_result['assc_ten'] ?></td>
            </tr>
            <tr  >
              <td width="17%" height="25"><label class="control-label">Brand</td>
              
              <td><?php echo $arr_result['brand'] ?></td>
              <td width="13%"><label class="control-label">District&nbsp; </td>
         
              <td><?php echo $arr_result['dis_distric'] ?></td>
            </tr>
             
              <tr  >
              <td><label class="control-label">State&nbsp;</td>
     
              <td><?php echo $arr_result['state'] ?></td>
              <td width="14%" height="24" ><label class="control-label">City&nbsp; </td>
           
              <td><?php echo $arr_result['city'] ?></td>
            </tr>
             <tr  >
              <td height="26">YTD Business Qty&nbsp; </td>
 
              <td><?php echo $arr_result['qty'] ?></td>
               <td height="26">&nbsp;</td>
     
              <td height="26">&nbsp;</td>
            </tr> 
               <tr  >
              <td height="26">M+1(Sales Quantity)&nbsp; </td>
        
               <td><?php echo $arr_result['m1'] ?></td>
      
              <td height="26">&nbsp;</td>
               <td>&nbsp;</td>
              </tr> 
               <tr  >
              <td height="26">M+2(Sales Quantity)&nbsp; </td>
         
               <td><?php echo $arr_result['m2'] ?></td>
               <td height="26">&nbsp;</td>
        
              <td height="26">&nbsp;</td>
              </tr>  <tr  >
              <td height="26">M+3(Sales Quantity)&nbsp; </td>
          
               <td><?php echo $arr_result['m3'] ?></td>
               <td height="26">&nbsp;</td>
        
              <td height="26">&nbsp;</td>
              </tr>  <tr  >
              <td height="26">&nbsp;Next 3 Month Business Plan in Qty </td>
         
             <td><?php echo $arr_result['tm'] ?></td>
               <td height="26">&nbsp;</td>
         
              <td height="26">&nbsp;</td>
              </tr> 
         
          
            </tbody>
          </table>
		  
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;ASP Required Location</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr  >
              <td width="16%">State&nbsp;</td>
              <td width="1%">:</td>
              <td><?php echo $arr_result['asc_state'] ?></td>
              <td width="19%" height="24" >City&nbsp; </td>
              <td width="1%" align="left" >:</td>
              <td><?php echo $arr_result['asc_city'] ?></td>
            </tr>
            <tr  >
              <td height="28"><label class="control-label">District&nbsp; </td>
              <td>:</td>
              <td><?php echo $arr_result['asc_dis'] ?></td>
              <td><label class="control-label">Expt. Call Load (Begining) p.m&nbsp; </td>
              <td>:</td>
              <td><?php echo $arr_result['ec'] ?></td>
            </tr>
            <tr  >
              <td height="25"><label class="control-label">ASC Type&nbsp;</td>
              <td>:</td>
              <td > 
              <?php if($arr_result['asc_type']=='new_asc'){ echo "New ASC";} else { echo "Replaced ASC"; }?> 
               </td>
             <td width="13%"><label class="control-label">Justification&nbsp;</td>
              <td width="1%">:</td>
              <td width="34%"><?php echo $arr_result['asc_remark'];?> </td>
            </tr>
           
            <tr  >
              <td height="25"><label class="control-label">Expt. Call Load (M+3) p.m&nbsp; </td>
              <td>:</td>
               <td><?php echo $arr_result['ec3'] ?></td>
              <td height="26"><label class="control-label">Distance of Nearest ASC from Proposed ASC City</td>
              <td>:</td>
              <td><?php echo $arr_result['nd'] ?> km</td>
            </tr>
            <tr  >
              <td height="28"><label class="control-label">Per Month Repair Load of the Nearest ASC</td>
              <td>:</td>
            <td><?php echo $arr_result['pma'] ?></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->





  <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Approval</div>
      <div class="panel-body">
    
		   <div class="form-group">
                    <div class="col-md-12" align="center">
	    <table class="table table-bordered" width="100%">
        <?php if($arr_result['status']=='7'){?>
            <tr >
              <td width="17%" height="25">Approve Status</td>
         
              <td width="34%"><span class="Table_body_white">
                <select name="app_status" id="app_status" class="form-control required">
                 
                  <option value="14">Approve</option>
                  <option value="15">Reject</option>
                 
                </select>
                </span></td>
              <td width="13%">Remark&nbsp; <span class="red_small">*</span></td>
         
              <td width="34%"><input type="text" name="app_remark" id="app_remark" class="form-control required"></td>
            </tr><?php } else{ ?>
            <tr >
              <td height="25">Approved By</td>
            
               <td><?php echo $arr_result['app_by']; ?></td>
              <td height="26">Approval Status</td>
              
              <td><?php if($arr_result['status']=='14'){ echo "Approved";} elseif($arr_result['status']=='15') { echo "Rejected"; } else { }  ?></td>
            </tr>
			<tr >
              <td height="25">Approval Date</td>
            
               <td><?php echo $arr_result['app_date'] ?></td>
              <td height="26">Approval Remark</td>
        
              <td><?php echo $arr_result['remark'] ?></td>
            </tr><?php }?>
			
			<tr>
			<td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_appo_detail_list.php?<?=$pagenav?>'">&nbsp;
						
                    

                     <?php if($arr_result['status']=='7'){?>    <input type="submit" class="btn<?=$btncolor?>" name="savermk" id="savermk" value="Save" title="Save  Details" <?php if($_POST['savermk']=='Save'){?>disabled<?php }?>> <?php }?>&nbsp;</td>
			</tr>
          </table>
					 
                     
                    </div>
                  </div> 
      </div><!--close panel body-->
    </div><!--close panel-->
	</form>
 </div><!--close col-sm-9-->
</div><!--close container-fluid-->
<?php
include("../includes/connection_close.php");
?>
</body>
</html>