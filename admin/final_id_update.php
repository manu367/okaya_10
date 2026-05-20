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
	

	$sql="Update  location_master_req  set  statusid='1'  where req_no='".$_REQUEST['req_no']."' ";
	
mysqli_query($link1,$sql)or die("error in insertion2".mysqli_error($link1));
$query11="select * from location_master_req where req_no='".$_REQUEST['req_no']."'";
$result1=mysqli_query($link1,$query11);
$row2=mysqli_fetch_array($result1);


   $sql="INSERT INTO location_master set erpid='".$row2['erp_id']."', othid='".$row2['oth_id']."',locationname='".$row2['locationname']."',locationtype='".$row2['locationtype']."', partner_type='".$row2['partner_type']."', contact_person='".$row2['contact_person']."',landlineno='".$row2['landlineno']."',emailid='".$row2['emailid']."',contactno1='".$row2['contactno1']."',contactno2='".$row2['contactno2']."',locationaddress='".$row2['locationaddress']."',dispatchaddress='".$row2['dispatchaddress']."',deliveryaddress='".$row2['deliveryaddress']."',districtid='".$row2['districtid']."',cityid='".$row2['cityid']."',stateid='".$row2['stateid']."',countryid='".$row2['countryid']."',zipcode='".$row2['zipcode']."',statusid='1',loginstatus='1',gstno='".$row2['gstno']."',panno='".$row2['panno']."',oth_taxr_no='".$row2['oth_taxr_no']."',oth_tax_name='".$row2['oth_tax_name']."',createby='".$_SESSION['userid']."',createdate='".$datetime."'";



   mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));



   $insid = mysqli_insert_id($link1);



   /// make 5 digit padding



   $pad=str_pad($insid,5,"0",STR_PAD_LEFT);



   //// make logic of employee code



   $newlocationcode=substr(strtoupper(BRANDNAME),0,3)."".$pad;



   //////// update system genrated code in location



   mysqli_query($link1,"UPDATE location_master set location_code='".$newlocationcode."', pwd='".$newlocationcode."' where locationid='".$insid."'")or die("ER2".mysqli_error($link1));



   ///// entry in job counter 



   $sql_jobcount="INSERT INTO job_counter set location_code='".$newlocationcode."', job_count='0'";



   mysqli_query($link1,$sql_jobcount)or die("ER2".mysqli_error($link1));



   ///// entry in invoice counter 



   $sql_invcount="INSERT INTO invoice_counter set location_code='".$newlocationcode."',fy='".date('y')."/',inv_series='I".$pad."/', inv_counter='0', stn_series='DC".$pad."/',stn_counter='0'";



   mysqli_query($link1,$sql_invcount)or die("ER2.1".mysqli_error($link1));



   ///// entry in current cr status



   $sql_crlimit="INSERT INTO current_cr_status set location_code='".$newlocationcode."',  credit_bal='0.00',   credit_limit='0.00',   total_credit_limit='0.00'";



   mysqli_query($link1,$sql_crlimit)or die("ER3".mysqli_error($link1));




$sql1="INSERT INTO remark_master set req_id='".$_POST['req_no']."',module='ID CREATED',remark='Final ID Created', status='ID CREATED', type='".$_SESSION['id_type']."',req_by='".$_SESSION['userid']."',outcome='Final ID Created'";
mysqli_query($link1,$sql1)or die("error in insertion2".mysqli_error($link1));




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
$headers1 .= "From:doNotReply@digicare.com". "\r\n";
$subject = "ASP CRM ID";
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
  
header("location:final_id_creation.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

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
			<td colspan="4" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_create_sap_code.php?<?=$pagenav?>'">&nbsp;
						
                    

                     <?php if($arr_result['statusid']=='18'){?>    <input type="submit" class="btn<?=$btncolor?>" name="savermk" id="savermk" value="Save" title="Save  Details" <?php if($_POST['savermk']=='Save'){?>disabled<?php }?>> <?php }?>&nbsp;</td>
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