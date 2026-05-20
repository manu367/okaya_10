<?php
require_once("../includes/config.php");
//$docid=base64_decode($_REQUEST['refid']);
//// job details

////// final submit form ////
if($_POST){
@extract($_POST);
if($_POST['savermk']=='Save')
{
	//// initialize transaction parameters
	$flag = true;
	mysqli_autocommit($link1, false);
	$error_msg="";
	
	$req_email = ($_POST['e'] !="")?base64_decode($_POST['e']):"";
	
	$sql="Update location_master_req set statusid='".$_POST['app_status']."', updatedate ='".$datetime."' where req_no='".$_REQUEST['req_no']."' ";
mysqli_query($link1,$sql)or die("error in insertion2".mysqli_error($link1));
	if($_POST['app_status']=='14')
	{
		$status1="ASC Approved By" .$_SESSION['userid'];
	}
	else
	{
		$status1="ASC Rejected By" . $_SESSION['userid'];
	}
	
	$sql1="INSERT INTO remark_master set req_id='".$_POST['req_no']."', module='ASP_CREATION_APPROVAL', remark='".$_POST['app_remark']."', status='".$_POST['app_status']."', type='".$_SESSION['id_type']."',req_by='".$_SESSION['userid']."',outcome='".$status1."'";
mysqli_query($link1,$sql1)or die("error in insertion2".mysqli_error($link1));
	$sql_srch1="select * from doc_upload where req_id='".$_REQUEST['req_no']."'";
	$res_srch1=mysqli_query($link1,$sql_srch1);
	while($row=mysqli_fetch_array($res_srch1))
	{
		$doc="doc".$row['id'];
		mysqli_query($link1,"update doc_upload set status='$_POST[app_status]',doc_check_rcsm='$_POST[$doc]',ck_dt_rcsm='$today' where id='$row[id]'") or die("error2".mysql_error($link1));	
	}
	
	
	/*
	$uid=mysqli_query($link1,"select emailid from admin_users where userid='".$_REQUEST['up_by']."'");
	//$email_to=mysql_fetch_array($email);
	$cn1=mysqli_fetch_array($uid);
	*/
	$cn1 = $req_email;
	
	$email=mysqli_query($link1,"select email from email_user where type='admin'");
	//$email_to=mysql_fetch_array($email);
	$cn=mysqli_num_rows($email);
	$toemail="";
	while($row=mysqli_fetch_array($email))
	{
		if($toemail=="")
		{
			$toemail.=$row['email'].",".$cn1['emailid'];
		}
		else
		{
			$toemail.=",".$row['email'].",".$cn1['emailid'];
		}
	}
	//echo $toemail;
	if($_POST['app_status']=="APP")
	{
		$app1="Approved";
	}
	else
	{
		$app1="Rejected";
	}
	$message = "Dear Sir ,<br />";
	$message.="<br>Status of below  ticket  .<br />";
	$message.="<br>Request No  :".$_POST['request']."<br />";
	$message.="<br>Request Status: ".$app1."<br />";
	$message.="<br>Remark: ".$_POST['app_remark']."<br />";
	// Always set content-type when sending HTML email
	$headers1 = "MIME-Version: 1.0\r\n";
	$headers1 .= "Content-type: text/html; charset=iso-8859-1\r\n";
	$headers1 .= "From:doNotReply@cancrm.in". "\r\n";
	$subject = "ASC Appointment Request Status";
	mail($toemail,$subject,$message ,$headers1);
	
	////// insert in activity table////
		$flag = dailyActivity($_SESSION['userid'],$_POST['req_no'],$app,"ASC Creation Approval",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	///// check query are successfully executed
	if ($flag) {
		$cflag="success";

		$cmsg="Success";
		$msg = "Request ".$_REQUEST['req_no']." successfully processed!";
	    mysqli_commit($link1);
	} else {
	mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
	} 
   ///// move to parent page
	header("location:asp_create_appr.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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

	$arr_result = mysqli_fetch_assoc(mysqli_query($link1,"select * from location_master_req where req_no='".$_REQUEST['req_no']."'"));
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-ticket"></i> ASP Location Details</h2>
      <h4 align="center">Request No.- <?=$_REQUEST['req_no']?></h4>
	  <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
      <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr  >
              <td height="26"><label class="control-label">Party Name&nbsp;</lable> </td>
        
              <td><?php echo $arr_result['locationname'] ?><input type="hidden" name="req_no" id="req_no" class=" inputtext" value="<?php echo $arr_result['req_no'] ?>"></td>
              <td height="26"><label class="control-label">State</lable></td>
            
              <td><?php echo getAnyDetails($arr_result["stateid"],"state","stateid","state_master",$link1); ?></td>
            </tr>
            <tr  >
              <td width="17%" height="25"><label class="control-label">City</lable></td>
              
              <td><?php echo getAnyDetails($arr_result["cityid"],"city","cityid","city_master",$link1); ?></td>
              <td width="13%"><label class="control-label">Contact Person&nbsp; </lable></td>
         
              <td><?php echo $arr_result['contact_person'] ?></td>
            </tr>
             
              <tr  >
              <td><label class="control-label">Address&nbsp;</lable></td>
     
              <td><?php echo $arr_result['locationaddress'] ?></td>
              <td width="14%" height="24" ><label class="control-label">Pincode&nbsp;</lable> </td>
           
              <td><?php echo $arr_result['zipcode'] ?></td>
            </tr>
             <tr  >
              <td ><label class="control-label">Contact Number&nbsp; </lable></td>
 
              <td><?php echo $arr_result['contactno1'] ?></td>
               <td ><label class="control-label">Alternate Number&nbsp;</lable></td>
     
            <td><?php echo $arr_result['contactno2'] ?></td>
            </tr> 
				<tr >
					<td ><label class="control-label">Helpline No &nbsp; </lable></td>
					<td><?php echo $arr_result['landlineno'] ?></td>
					<td ><label class="control-label">Email&nbsp;</lable></td>
					<td>
						<?php echo $arr_result['emailid'] ?>
						<input type="hidden" name="e" value="<?=base64_encode($arr_result['emailid']);?>" />
					</td>
				</tr> 
               <tr  >
              <td ><label class="control-label">PAN No&nbsp;</lable> </td>
         
               <td><?php echo $arr_result['panno'] ?></td>
               <td ><label class="control-label">GST No.&nbsp;</lable></td>
        
               <td><?php echo $arr_result['gstno'] ?></td>
              </tr>  
         
          
            </tbody>
          </table>
		  
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Document List</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
                <?php	  $sql_srch="select * from doc_upload where req_id='".$_REQUEST['req_no']."' order by id";
		  $res_srch=mysqli_query($link1,$sql_srch);
		  
		  while($result_srch=mysqli_fetch_array($res_srch)){?>
                <tr>
          <td width="21%"><?=$result_srch['name']?>
	 </td>
           <td width="34%"><?php if($result_srch['url']!='') {?> <input type="radio" name="doc<?=$result_srch['id']?>" id="doc1<?=$result_srch['id']?>" value="Y" > Approved
  <input type="radio" name="doc<?=$result_srch['id']?>" id="doc2<?=$result_srch[id]?>" value="N"  > Rejected    <?php }?></td>
           <td width="25%"><?php if($result_srch['url']!='') {?><a href="<?=$result_srch['url']?>" download target="_blank">Download </a><?php }else { echo "Not Uploaded"; }?>  </td>
             
              </tr>  <?php }?> 
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
        <?php if($arr_result['statusid']=='7'){?>
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
            </tr><?php } ?>
			
			<tr>
			<td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_create_appr.php?<?=$pagenav?>'">&nbsp;
						
                    

                     <?php if($arr_result['statusid']=='7'){?>    <input type="submit" class="btn<?=$btncolor?>" name="savermk" id="savermk" value="Save" title="Save  Details" <?php if($_POST['savermk']=='Save'){?>disabled<?php }?>> <?php }?>&nbsp;</td>
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