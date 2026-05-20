<?php
require_once("../includes/config.php");
$id= base64_decode($_REQUEST['refid']);
//fetch data from advance_docket_assign//
$query="select * from advance_docket_assign where id='".$id."'";
$result=mysqli_query($link1,$query)or die("error1".mysqli_error($link1));
$show_result=mysqli_fetch_assoc($result);
	
//fetch data from advance_docket_upload//
@extract($_POST);
if($_POST){
  	if($_POST['Submit']=='Received'){
		////// transaction parameter initialization
		mysqli_autocommit($link1, false);
		$flag = true;
		$err_msg = "";

	 	///// Update Status and Remark ///////////
	  	$query1="UPDATE advance_docket_assign set `status`='Received',`receive_remark`='$remark',`receive_date`='$datetime',`receive_ip`='$ip',`receive_by`='$_SESSION[userid]' where `doc_no`='".$show_result['doc_no']."'";
		$result = mysqli_query($link1,$query1);
		if (!$result) {
			$flag = false;
			$error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
		/// get data
		$query2="select * from advance_docket_upload where `doc_no`='".$show_result['doc_no']."'";
		$result2=mysqli_query($link1,$query2)or die("error1".mysqli_error($link1));
		while($row = mysqli_fetch_array($result2))
		{
			$res2 = mysqli_query($link1,"INSERT INTO `docket_inventory` SET  `asp_code`='".$row ['asp_code']."', `docket_no`='".$row['docket_no']."', `docket_company`='".$row['docket_company']."',`in_stock`='Y', `courier_code`='".$row['courier_code']."'"); 	
			if (!$res2) {
				$flag = false;
				$error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}
		}
	   	///// check  master Table where query is successfully executed
	 	if ($flag) {
        	mysqli_commit($link1);
        	$msg = "Advance docket is successfully received." ;
			$chkflag  = "success";
			$chkmsg = "Success";
    	} else {
			mysqli_rollback($link1);
			$msg = "Request could not be processed. Please try again.".$error_msg;
			$chkflag = "danger";
			$chkmsg = "Failed";
		} 
    	mysqli_close($link1);	
  		///// move to parent page
		header("Location:Advance_Docket_Receive.php?msg=".$msg."&chkflag=".$chkflag."&chkmsg=".$chkmsg."".$pagenav);
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
<body>
	<div class="container-fluid">
		<div class="row content">
		<?php 
    	include("../includes/leftnavemp2.php");
		?>
   		<div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      		<h2 align="center"><i class="fa fa-tags"></i> Advance Docket Receive</h2>
	  		<form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
  				<div class="panel-group">
    				<div class="panel panel-info table-responsive">
      					<div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Location Details</div>
        				<div class="panel-body">
         					<table class="table table-bordered" width="100%">
           					<tbody>
								<tr>
									<td width="20%"><label class="control-label">Assign From</label></td>
									<td width="30%"><?php echo $show_result['assign_from'];?></td>

									<td width="20%"><label class="control-label">Assign To</label></td>
									<td width="30%"><?php echo $show_result['assign_to'];?></td>
								</tr> 
								<tr>
										<td width="20%"><label class="control-label">Doc. No.</label></td>
										<td width="30%"><?php echo $show_result['doc_no'];?></td>

										<td width="20%"><label class="control-label">Doc. Date.</label></td>
										<td width="30%"><?php echo $show_result['doc_date']; ?></td>
								</tr>
								<tr>
										<td width="20%"><label class="control-label">Entry By</label></td>
										<td width="30%"><?php echo $show_result['assign_by'];?></td>

										<td width="20%"><label class="control-label">Status</label></td>
										<td width="30%"><?php echo $show_result['status'];?></td>
								</tr>
							</tbody>
						</table>
					</div><!--close panel body-->
				</div><!--panel-heading-->
                <div class="panel panel-info">
                    <div class="panel-heading"><i class="fa fa-cubes fa-lg"></i>&nbsp;&nbsp;Docket Details</div>
                        <div class="panel-body">
                            <table class="table table-bordered" width="100%">
                                <thead>	
                                    <tr class="<?=$tableheadcolor?>">
                                        <td width="10%"><strong>S.No</strong></td>
                                        <td width="20%"><strong>Docket No.</strong></td>
                                        <td width="30%"><strong>Docket Company</strong></td>
                                        <td width="20%"><strong>Mode Of Transport</strong></td>
                                        <td width="20%"><strong>Response Msg</strong></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    
                                    $result=mysqli_query($link1,"SELECT * FROM `advance_docket_upload` WHERE `doc_no`='$show_result[doc_no]'");
                                    $sn=1;
                                    while($show_result1=mysqli_fetch_assoc($result))
                                    {
                                    
                                    ?>
                                    <tr>
                                        <td><?=$sn?></td>
                                        <td><?=$show_result1['docket_no']?></td>
                                        <td><?=$show_result1['docket_company']?></td>
                                        <td><?=$show_result1['mode_of_transport']?></td>
                                        <td><?=$show_result1['response_msg']?></td>	
                                    </tr>
                                    <?php
                                    $sn++;
                                    }
                                    
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">   
                    <label class="col-md-4 control-label">Remark</label> 
                    <div class="col-md-6">
                    <textarea class="form-control" name="remark" id="remark" style="resize:vertical" placeholder="Enter remark"><?=$show_result["receive_remark"]?></textarea>
                    </div>
                </div>
                <div class="col-md-12">   
                    
                    <div class="col-md-6">
                   &nbsp;
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-12" align="center">
                      <?php if($show_result['status']=='Pending'){?>
					 	<input type="submit" class="btn btn-primary" name="Submit" id="upd" value="Received" onClick="window.location.href='Advance_Docket_Receive.php?<?=$pagenav?>'"><?php }else{ 
						 echo "<span class='text-danger'>Docket Already Received !</span>";
						 }?>
                      &nbsp;&nbsp;&nbsp;
                      <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='Advance_Docket_Receive.php?<?=$pagenav?>'">
                    </div>
                  </div> 
			</form>
		</div>	<!--close panel body-->
    </div>		<!--close panel-->
</div>		<!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>