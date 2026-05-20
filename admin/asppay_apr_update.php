<?php
require_once("../includes/config.php");
$docid=base64_decode($_REQUEST['refid']);
//// job details

////// final submit form ////
if($_POST)
{
	@extract($_POST);
	if($_POST['savermk']=='Save')
	{
		//// initialize transaction parameters
		$flag = true;
		mysqli_autocommit($link1, false);
		$error_msg="";

		$sql="Update location_master_req  set     bkn='$_POST[bkn]',ac_no_cheque='$_POST[ac_no]',pm_mode='$_POST[pm_mode]',ch_no='$_POST[ch_no]',pay='$_POST[pay]',check_by='$_SESSION[userid]' 	,chq_dt='$today',statusid='16'  where req_no='$_REQUEST[req_no]' ";

		mysqli_query($link1,$sql)or die("error in insertion2".mysqli_error($link1));

		$sql1="INSERT INTO remark_master set req_id='".$_POST['req_no']."',module='ASC_PAY',remark='ASC Payment Upload', status='PAY', type='".$_SESSION['id_type']."',req_by='".$_SESSION['userid']."',outcome='Cheque Uploaded'";
		mysqli_query($link1,$sql1)or die("error in insertion2".mysqli_error($link1));


		$target_dir = "../handset_image/";

		$target_file = $target_dir.basename($_FILES["img_ch"]["name"]);

		move_uploaded_file($_FILES["img_ch"]["tmp_name"], $target_file);
		mysqli_query($link1,"insert into doc_upload set type='$_SESSION[id_type]',url='$target_file',name='Cheque Upload',req_by='$_SESSION[uname]',req_id='$_POST[req_no]',asc_cr='$_POST[tc_no]' ,status='pay'") or die("error2".mysql_error());

		$uid=mysqli_query($link1,"select emailid from admin_users where userid='".$_REQUEST['up_by']."'");
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
		$subject = "ASC Pay Request Status";
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

		header("location:asp_create_pay.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

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
              <td height="26"><label class="control-label">Party Name&nbsp; </td>
        
              <td><?php echo $arr_result['locationname'] ?><input type="hidden" name="req_no" id="req_no" class=" inputtext" value="<?php echo $arr_result['req_no'] ?>"></td>
              <td height="26"><label class="control-label">State</td>
            
              <td><?php echo getAnyDetails($arr_result["stateid"],"state","stateid","state_master",$link1); ?></td>
            </tr>
            <tr  >
              <td width="17%" height="25"><label class="control-label">City</td>
              
              <td><?php echo getAnyDetails($arr_result["cityid"],"city","cityid","city_master",$link1); ?></td>
              <td width="13%"><label class="control-label">Contact Person&nbsp; </td>
         
              <td><?php echo $arr_result['contact_person'] ?></td>
            </tr>
             
              <tr  >
              <td><label class="control-label">Address&nbsp;</td>
     
              <td><?php echo $arr_result['locationaddress'] ?></td>
              <td width="14%" height="24" ><label class="control-label">Pincode&nbsp; </td>
           
              <td><?php echo $arr_result['zipcode'] ?></td>
            </tr>
             <tr  >
              <td height="26">Contact Number&nbsp; </td>
 
              <td><?php echo $arr_result['contactno1'] ?></td>
               <td height="26">Alternate Number&nbsp;</td>
     
            <td><?php echo $arr_result['contactno2'] ?></td>
            </tr> 
				<tr  >
					<td height="26">Helpline No &nbsp; </td>
					<td><?php echo $arr_result['landlineno'] ?></td>
					<td height="26">Email&nbsp;</td>
					<td>
						<?php echo $arr_result['emailid'] ?>
						
					</td>
				</tr> 
				<tr  >
              <td height="26">PAN No&nbsp; </td>
         
               <td><?php echo $arr_result['panno'] ?></td>
               <td height="26">GST No.&nbsp;</td>
        
               <td><?php echo $arr_result['gstno'] ?></td>
              </tr>  
         
          
            </tbody>
          </table>
		  
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Payment Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
			
			 
                <tr bordercolor="#000000" class="Table_body">
              <td height="26">Bank Name</td>
        
            <td ><input type="text" name="bkn" id="bkn"   value="<?php echo $arr_result['bkn'] ?>" class="form-control required" required /></td>
              <td height="26">A/C no</td>
       
              <td height="26"><input type="text" name="ac_no" value="<?php echo $arr_result['ac_no_cheque'] ?>"   id="ac_no" class="form-control required" required></td>
              </tr> 
               <tr bordercolor="#000000" class="Table_body">
              <td height="26">Transaction type </td>
     
<!--            <td ><input type="text" name="pm_mode" id="pm_mode" class="inputtext" /></td>
-->              
<td >
<select name="pm_mode" id="pm_mode" class="form-control required" required>
<option value="" <?php if($arr_result['pm_mode']==''){ echo "selected";}?>>Select Mode</option>
<option value="NEFT"<?php if($arr_result['pm_mode']=='NEFT'){ echo "selected";}?>>NEFT</option>
<option value="RTGS" <?php if($arr_result['pm_mode']=='RTGS'){ echo "selected";}?>>RTGS</option>
<option value="Cash Deposited" <?php if($arr_result['pm_mode']=='Cash Deposited'){ echo "selected";}?>>Cash Deposited</option>
<option value="Cheque Deposited" <?php if($arr_result['pm_mode']=='Cheque Deposited'){ echo "selected";}?>>Cheque Deposited</option>
<option value="DD Deposited" <?php if($arr_result['pm_mode']=='DD Deposited'){ echo "selected";}?>>DD Deposited</option>
</select>
</td>
<td height="26">Cheque No/Transaction No</td>
 
              <td height="26"><input type="text" name="ch_no" id="ch_no" value="<?php echo $arr_result['ch_no'] ?>" class="form-control required" required></td>
              </tr> 
                <tr bordercolor="#000000" class="Table_body">
              <td height="26">Amount Paid</td>
  
            <td ><input type="text" name="pay" id="pay" class="form-control required" value="<?php echo $arr_result['pay'] ?>" required  nKeyPress="return onlyNumbers(this);"/></td>
              <td height="26">Payment Confirmation Proof</td>
           
              <td height="26"><input type="file" name="img_ch" id="img_ch" class="form-control required" required /></td>
              </tr> 
        
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->


  <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp; Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
			
			   <tr bordercolor="#000000" class="Table_body">
                    <td height="26">Step</td>
                    <td>Update By</td>
                    <td height="26" >Update Date</td>
                 </tr>
                 	<?php   $req="SELECT * FROM remark_master where req_id ='".$_REQUEST['req_no']."' ";

						 $crem_q=mysqli_query($link1,$req);

						 while($row_rmk = mysqli_fetch_array($crem_q)){
						 ?>
						 
						   <tr bordercolor="#000000" class="Table_body">
                 
                    <td height="26"><?=$row_rmk['outcome']?></td>
                   <td height="26"><?=$row_rmk['req_by']?></td>
					   <td height="26"><?=$row_rmk['update_dt']?></td>
                 </tr><?php }?>
              
         <tr>
			<td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_create_appr.php?<?=$pagenav?>'">&nbsp;
						
                    

                     <?php if($arr_result['statusid']=='14'){?>    <input type="submit" class="btn<?=$btncolor?>" name="savermk" id="savermk" value="Save" title="Save  Details" <?php if($_POST['savermk']=='Save'){?>disabled<?php }?>> <?php }?>&nbsp;</td>
			</tr>
            </tbody>
          </table>
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