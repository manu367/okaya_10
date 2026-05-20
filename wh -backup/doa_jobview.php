<?php 
require_once("../includes/config.php");
 $row = mysqli_query($link1,"select * from temp_doa_job where 1");
 @extract($_POST);
 if ($_POST['save']=='Save'){
	//// initialize transaction parameters
		$flag = true;
		mysqli_autocommit($link1, false);
		$error_msg="";
		$countjob = count($job_no);
		
		for($i= 0 ; $i<$countjob ; $i++){
		//////// update doa_receive_flag  in jobsheet data table ///////////////////////////	
		$sql1=mysqli_query($link1,"Update jobsheet_data set doa_receive_flag='Y'  where job_no='".$job_no[$i]."' " );
	//// check if query is not executed
		if (!$sql1) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 
		
	////////////////////// insert into  faulty data Table///////////////
		$sql2=mysqli_query($link1,"insert into faulty_doa_receive  set  distributer_code='".$distributer_code[$i]."',job_no ='".$job_no[$i]."',imei ='".$imei[$i]."',model_id ='".$model_id[$i]."',update_by='".$_SESSION['userid']."',update_date='".$today."',status='1'  ");
			//// check if query is not executed
		if (!$sql2) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 
		

		////////////////////// insert into  faulty data Table///////////////
		//echo "insert into imei_details  set  location_code='".$_SESSION['asc_code']."',imei1 ='".$imei[$i]."',imei2 ='".$sec_imei[$i]."',model_id ='".$model_id[$i]."',status= '1' ,entry_date='".$today."', partcode = '".$partcode[$i]."'";
		$sql3=mysqli_query($link1,"insert into imei_details  set  location_code='".$_SESSION['asc_code']."',imei1 ='".$imei[$i]."',imei2 ='".$sec_imei[$i]."',model_id ='".$model_id[$i]."',status= '1' ,entry_date='".$today."', partcode = '".$partcode[$i]."',stock_type='faulty'");
			//// check if query is not executed
		if (!$sql3) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		} 	
		
		/////////////////////// check whether partcode and location code exist in client inventory or not //////////////////////
		echo "select location_code , partcode from client_inventory where location_code = '".$_SESSION['asc_code']."'  and partcode = '".$partcode[$i]."' ";
		$check = mysqli_query($link1 , "select location_code , partcode from client_inventory where location_code = '".$_SESSION['asc_code']."'  and partcode = '".$partcode[$i]."' ");
		if(mysqli_num_rows($check) >=1)
			{ 
		////////////// update  okqty in client inventory table //////////////////////////////////////////////////////////	 
	   $result2   = mysqli_query($link1 , " update  client_inventory set faulty = faulty+1 where partcode = '".$partcode[$i]."' and  location_code = '".$_SESSION['asc_code']."' "	);	   
		}
		else {
		////////////// insert  okqty in client inventory table //////////////////////////////////////////////////////////	 
	  $result2   = mysqli_query($link1 , " insert into  client_inventory set faulty = '1' , partcode = '".$partcode[$i]."' ,  location_code = '".$_SESSION['asc_code']."',  	updatedate = '".$datetime."' ");	   
		
		}
			 //// check if query is not executed
		   if (!$result2) {
	           $flag = false;
               echo "Error details2: " . mysqli_error($link1) . ".";
          				 }	
						 
			 $flag=stockLedger($grnno,$today,$partcode[$i],$_SESSION['asc_code'],$vendor,"IN","faulty","Stock In","Receive Against DOA Faulty",$okqty,$price,$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag); 
		///// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$job_no[$i],"Faulty Doa Receive","Upload",$_SERVER['REMOTE_ADDR'],$link1,$flag);
	}	
		///// check both master and data query are successfully executed
		if ($flag) {
			mysqli_commit($link1);
			$msg="Successfully Uploaded ";
			$cflag="success";
			$cmsg="Success";
		} else {
			mysqli_rollback($link1);
			$cflag="danger";
			$cmsg="Failed";
			$msg = "Request could not be processed. Please try again. ".$error_msg;
		} 
		mysqli_close($link1);
		///// move to parent page
		header("location:faulty_doa_receive.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
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
 <script type="text/javascript" src="../js/moment.js"></script>
 <script type="text/javascript" language="javascript" > 
</script>
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
 <div class="row content">
	<?php 
  include("../includes/leftnavemp2.php");
    ?>
 
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-upload"></i> DOA Details</h2>
	  <br><br/>
      
    <form name="frm1"  id= "frm1" method="post" >
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;Upload Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
       <tr><td width="5%"  align="center" colspan="2"><strong>Distributor Name</strong></td>  <td width="20%" align="center" colspan="3"><?php echo  getAnyDetails($_REQUEST['dist_code'],"locationname","location_code","location_master",$link1);?></td></tr>
       <tr><td></td></tr>
            <tbody>              
            <tr>
			<td width="5%"  align="center"><strong>SNo</strong></td>
          
              <td width="20%" align="center"><strong>Job No</strong></td>
              <td width="6%" align="center"><strong>IMEI</strong></td>
			   <td width="10%" align="center"><strong>Model</strong></td>
			   <td width="25%" align="center"><strong>Status</strong></td>
				<td width="10%" align="center"><strong>Confirm</strong></td>
            </tr>
			 <tr>
			 <?php 
			 $i =1;
			  while ($res = mysqli_fetch_array($row)){
			  $val = getAnyDetails($res['job_no'],"job_no,imei,sec_imei,partcode,model,model_id,doa_receive_flag" ,"job_no" ,"jobsheet_data" ,$link1);
			  $job_val=explode("~",$val);
			  
			  if($val == '') {$jobval = "Job Not found" ;}
			  else if($job_val[6]=='Y'){
				  $jobval = "Job Already Received" ;
				  }
			  else {$jobval = "Detail  Matched" ;}
			  
			   ?>
			<td width="5%"><?php echo $i; ?></td>
            
              <td width="20%" align="center"><?php echo $res['job_no']?></td>
              <td width="6%" align="center"><?php echo $res['imei']?></td>
			   <td width="15%" align="center"><?php echo $job_val[4];?></td>
				<td width="10%" align="center"><?php echo $jobval  ;?></td>
				<td width="10%" align="center"><?php if($jobval == "Detail  Matched"){?> <input type="checkbox" name="check"  id="check" value="" checked><input id="job_no"  type="hidden"    name="job_no[]"  value='<?=$res['job_no']?>'><input id="imei"  type="hidden"    name="imei[]"  value='<?=$res['imei']?>'> <input id="sec_imei"  type="hidden"    name="sec_imei[]"  value='<?=$job_val[2]?>'>
                <input id="partcode"  type="hidden"    name="partcode[]"  value='<?=$job_val[3]?>'> <input id="model_id"  type="hidden"    name="model_id[]"  value='<?=$job_val[5]?>'><input id="distributer_code"  type="hidden"    name="distributer_code"  value='<?=$res['distributer_code']?>'><?php }?> </td>
            </tr>
			<?php $i++;}	?>
			<tr>
                 <td width="100%" align="center" colspan="7"><input type="submit" class="btn<?=$btncolor?>" name="save" id="save" value="Save" title="Save Score">&nbsp;&nbsp;<input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='faulty_doa_receive.php?<?=$pagenav?>'"></td>
              </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel--><!--close panel group-->
    </form>
 </div><!--close col-sm-9-->
</div><!--close row content-->

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>