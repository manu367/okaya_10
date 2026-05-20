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
	

	$sql="Update  location_master_req  set     erpid='".$_POST['sap_code']."',othid='$_POST[v_code]',statusid='18'  where req_no='$_REQUEST[req_no]' ";
	
mysqli_query($link1,$sql)or die("error in insertion2".mysqli_error($link1));



$sql1="INSERT INTO remark_master set req_id='".$_POST['req_no']."',module='GO_LIVE',remark='GO_LIVE', status='GO_LIVE', type='".$_SESSION['id_type']."',req_by='".$_SESSION['userid']."',outcome='GO-LIVE'";
mysqli_query($link1,$sql1)or die("error in insertion2".mysqli_error($link1));

$target_dir = "../handset_image/";
    $target_file = $target_dir.basename($_FILES["img10"]["name"]);
	move_uploaded_file($_FILES["img10"]["tmp_name"], $target_file);
	mysqli_query($link1,"insert into doc_upload set type='$_SESSION[id_type]',url='$target_file',name='Go-Live',req_by='$_SESSION[uname]',req_id='$_POST[req_no]',asc_cr='$_POST[tc_no]' ,status='Go-Live'") or die("error2".mysql_error());


 $email=mysqli_query($link1,"select email from email_user where (type='admin' or type='HO')");
 //$email_to=mysql_fetch_array($email);
$cn=mysqli_num_rows($email);
$toemail="";
while($row=mysqli_fetch_array($email)){
	if($toemail==""){
	    $toemail.=$row[email];
	}else{
		$toemail.=",".$row[email];
	}
}
$query11="select * from location_master_req where req_no='$_REQUEST[req_no]'";
$result1=mysqli_query($link1,$query11);
$row2=mysqli_fetch_array($result1);
$message = "Dear Sir ,<br />";
$message.="<br>GO Live .<br />";
$message.="<br>ASC Name  : ".$row2[name]."<br />";
$message.="<br>State  : ".$row2[state]."<br />";
$message.="<br>City  : ".$row2[city]."<br />";
$message.="<br>District  : ".$row2[district]."<br />";
$message.="<br>Contact person name  : ".$row2[contact_person]."<br />";
$message.="<br>Contact person number  : ".$row2[phone]."<br />";
$message.="<br>ASC Email ID  : ".$row2[email]."<br />";

$message.="<br>SAP Code: ".$row2[sap_cust_code]."<br />";
$message.="<br>Vender Code: ".$row2[sap_v_code]."<br />";
// Always set content-type when sending HTML email
$headers1 = "MIME-Version: 1.0\r\n";
$headers1 .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers1 .= "From:doNotReply@vmhd.in". "\r\n";
$subject = "ASC GO Live";
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
  
header("location:golive_asp.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

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
      <h2 align="center"><i class="fa fa-ticket"></i> ASP GO Live Details</h2>
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
             <td><?php echo $arr_result['emailid'] ?></td>
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
                    <td height="26">Technical Training<span class="red_small">*</span></td>
               
                    <td height="26" colspan="3">
                      <input name="dateA" class="inputtext" id="dateA" size="1" type="checkbox">
                 </td>
                  </tr>
                  <tr bordercolor="#000000" class="Table_body">
                    <td height="26">Process Training&nbsp; <span class="red_small">*</span></td>
           
                    <td height="26"  colspan="3"><input name="dateB" class="inputtext" id="dateB"  size="15" type="checkbox"/>
                  </td>
                  </tr>
                  <tr bordercolor="#000000" class="Table_body">
                  
                    <td height="26">MSL Kit Received&nbsp; <span class="red_small">*</span></td>
             
                    <td height="26"  colspan="3"><input name="dateC" class="inputtext" id="dateC"  size="15" type="checkbox"/>
                   </td>
                  </tr>
                  <tr bordercolor="#000000" class="Table_body">
                    <td height="26">ASC Details Updated in Website&nbsp; <span class="red_small">*</span></td>
                 
                    <td height="26"  colspan="3"><input name="dateD" class="inputtext" id="dateD" size="15" type="checkbox"/>
                   </td>
                  </tr>
                  <tr bordercolor="#000000" class="Table_body">
                    <td height="26">Branding Done&nbsp; <span class="red_small">*</span></td>
             
                    <td height="26"  colspan="3"><input name="dateE" class="inputtext" id="dateE"  size="15" type="checkbox"/>
                  </td>
                  </tr>
                  <tr bordercolor="#000000" class="Table_body">
                    <td height="26">Notice Board Content Received <span class="red_small">*</span></td>
                  
                    <td height="26"  colspan="3"><input name="dateF" class="inputtext" id="dateF"  size="15" type="checkbox"/>
                    </td>
                  </tr>
                  <tr bordercolor="#000000" class="Table_body">
                    <td height="26">ASC Auth. Certificate&nbsp; <span class="red_small">*</span></td>
                  
                    <td height="26"  colspan="3"><input name="dateG" class="inputtext" id="dateG"  size="15" type="checkbox"/>
                   </td>
                  </tr>
                  <tr bordercolor="#000000" class="Table_body">
                    <td height="26">Warrnty Terms & Condition Receive&nbsp; <span class="red_small">*</span></td>
                 
                    <td height="26"  colspan="3"><input name="dateH" class="inputtext" id="dateH"  size="15" type="checkbox"/>
                  </td>
                  </tr>
                  <tr bordercolor="#000000" class="Table_body">
                    <td height="26">O/W Charges Display&nbsp; <span class="red_small">*</span></td>
             
                    <td height="26"  colspan="3"><input name="dateI" class="inputtext" id="dateI"  size="15" type="checkbox"/>
                    </td>
                  </tr>
                  <tr bordercolor="#000000" class="Table_body">
                    <td height="26">ASC GO-Live Declaration&nbsp; <span class="red_small">*</span></td>
            
                    <td height="26"  ><input type="file" name="img10" id="img10"/></td>
                 
                  </tr>
				           <tr>
			<td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_create_sap_code.php?<?=$pagenav?>'">&nbsp;
						
                    

                     <?php if($arr_result['statusid']=='17'){?>    <input type="submit" class="btn<?=$btncolor?>" name="savermk" id="savermk" value="Save" title="Save  Details" <?php if($_POST['savermk']=='Save'){?>disabled<?php }?>> <?php }?>&nbsp;</td>
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