<?php
error_reporting(E_All);
require_once("../includes/config.php");
/////get status//
@extract($_POST);
$browserid=session_id();
	//////  if we want to Add new po
   if ($_POST['upd']=='Process'){
   ////// INITIALIZE PARAMETER/////////////////////////
   	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg = "";    	

	$data_sql12 = "select * from eng_adj_temp_upload where userid='".$_SESSION['userid']."' and browserid='".$browserid."' group by to_location  ";

	$data_res12=mysqli_query($link1,$data_sql12);
    $asp="";
	$file_name="";
	$rmk="";
	while($row1=mysqli_fetch_array($data_res12)){
		$asp=$row1['to_location'];
		$file_name=$row1['file_name'];
		$rmk=$row1['remark'];
		
		//// Make System document//////
    	$row_so=mysqli_fetch_array(mysqli_query($link1,"select max(temp_no) as no from stock_adjust_master where location_code='".$asp."' "));

   	 	$c_nos=$row_so['no']+1;
    	$so_no=$asp."ESA".$c_nos;

		$pno=array();
		$sql2 = "select * from eng_adj_temp_upload where userid='".$_SESSION['userid']."' and browserid='".$browserid."' and  to_location='".$row1['to_location']."' ";	

		$data_res=mysqli_query($link1,$sql2);
		while($row2=mysqli_fetch_array($data_res)){
			$post_qty= $row2['qty'];
			if($row2['stock_type']=="missing"){
				$adjcat=",adj_miss_type='".$row2['opr_type']."',adj_miss_qty='".$row2['qty']."',";
			}else if($row2['stock_type']=="faulty"){
				$adjcat=",adj_damg_type='".$row2['opr_type']."',adj_damg_qty='".$row2['qty']."',";
			}else if($row2['stock_type']=="broken"){
				$adjcat=",adj_broken_type='".$row2['opr_type']."',adj_broken_qty='".$row2['qty']."',";
			}else if($row2['stock_type']=="srnqty"){
				$adjcat=",adj_srn_type='".$row2['opr_type']."',adj_srn_qty='".$row2['qty']."',";	
			}else{
				$adjcat=",adj_ok_type='".$row2['opr_type']."',adj_ok_qty='".$row2['qty']."',"; 	
			}
			
			//// insert in data table
			$stock_data	= mysqli_query($link1,"insert into stock_adjust_data set system_ref_no='".$so_no."',partcode='".$row2['partcode']."'".$adjcat." entry_by='".$_SESSION['userid']."' , entry_date = '".$today."' , type = 'eng admin adjust', asc_code = '".$asp."', user_type = 'Eng Type'  ");
			/// check if query is not executed
			if (!$stock_data) {
				$flag = false;
				$error_msg = "Error details2: " . mysqli_error($link1) . ".";
			}

			/////check inventory again
			$price=$row2['price'];

			////// get location code
			$loc_code = mysqli_fetch_array(mysqli_query($link1,"select location_code from locationuser_master where userloginid='".$asp."' "));
			
			$stk_typ =$row2['stock_type'];
			if($row2['opr_type'] == "P"){
				// client_inventory added(24-07-2025)
				if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$row2['partcode']."' and location_code='".$loc_code['location_code']."'"))==0){
					$result=mysqli_query($link1,"insert into client_inventory set location_code = '".$loc_code['location_code']."', partcode='".$row2['partcode']."',brand_id='".$part_details['brand_id']."',product_id='".$part_details['product_id']."',part_name='".$part_details['part_name']."'");
				}
				
				if(mysqli_num_rows(mysqli_query($link1,"select partcode from user_inventory where partcode='".$row2['partcode']."' and locationuser_code='".$asp."'"))>0){
					///if product is exist in inventory then update its qty 
					$result=mysqli_query($link1,"update user_inventory set ".$stk_typ."=".$stk_typ."+'".$post_qty."' where partcode='".$row2['partcode']."' and locationuser_code='".$asp."'");
					
					$flag=stockLedger($so_no,$today,$row2['partcode'],$asp,$asp,$row2['opr_type'],$stk_typ,"Eng Stock Adjustment","Eng Stock Adjustment",$post_qty,$price,$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
				}else{
					
					//// if product is not exist then add in inventory
					$part_details=mysqli_fetch_array(mysqli_query($link1,"select brand_id,product_id,part_name from partcode_master where partcode='".$row2['partcode']."'"));
					//echo "insert into user_inventory set location_code = '".$loc_code['location_code']."', locationuser_code='".$asp."', partcode='".$row2['partcode']."',".$stk_typ."='".$post_qty."',brand_id='".$part_details['brand_id']."',product_id='".$part_details['product_id']."',part_name='".$part_details['part_name']."'";exit;
				
			$result=mysqli_query($link1,"insert into user_inventory set location_code = '".$loc_code['location_code']."', locationuser_code='".$asp."', partcode='".$row2['partcode']."',".$stk_typ."='".$post_qty."',part_name='".$part_details['part_name']."'");
					
					$flag=stockLedger($so_no,$today,$row2['partcode'],$asp,$asp,$row2['opr_type'],$stk_typ,"Eng Stock Adjustment","Eng Stock Adjustment",$post_qty,$price,$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);	
					
				}

			}else{
				
				if(mysqli_num_rows(mysqli_query($link1,"select partcode from user_inventory where partcode='".$row2['partcode']."' and locationuser_code='".$asp."' and ".$stk_typ.">='".$post_qty."'"))>0){
					///if product is exist in inventory then update its qty 
					$result=mysqli_query($link1,"update user_inventory set ".$stk_typ."=".$stk_typ."-'".$post_qty."' where partcode='".$row2['partcode']."' and locationuser_code='".$asp."'");
			
					$flag=stockLedger($so_no,$today,$row2['partcode'],$asp,$asp,$row2['opr_type'],$stk_typ,"Eng Stock Adjustment","Eng Stock Adjustment",$post_qty,$price,$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
				}else{	

				}
			}
			//// check if query is not executed
		}

		 $sql_so_master= "INSERT INTO stock_adjust_master set location_code='".$asp."',system_ref_no='".$so_no."',temp_no='".$c_nos."',adjust_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',status='PROCESSED',entry_by='".$_SESSION['userid']."',entry_ip='".$_SERVER['REMOTE_ADDR']."',entry_rmk='".$rmk."', file_name = '".$file_name."', type = 'eng admin adjust', user_type = 'Eng Type' ";
//print_r('dddddddddddddd');exit;
  		$sql=  mysqli_query($link1,$sql_so_master)or die("Error1".mysqli_error());
  		//// check if query is not executed
		if (!$sql) {
			$flag = false;
      	 	$error_msg = "Error details1: " . mysqli_error($link1) . ".";
    	}
	}

	////// insert in activity table////
	$flag = dailyActivity($_SESSION['userid'],$so_no,"Eng Stock Adjustment Stock","Adjustment",$ip,$link1,$flag);
	////////////////// Update In Po Master ///////////////////

	$result_temp=mysqli_query($link1,"delete from eng_adj_temp_upload where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result_temp) {
	   $flag = false;
	   $error_msg = "temp data not delete: " . mysqli_error($link1) . ".";
   	}

	    if($flag){
        	mysqli_commit($link1);
			$cflag = "success";
			$cmsg = "Success";
        	$msg = "Adjustment Successfully Done. Refrance no. is :".$so_no;
    	}else{
			mysqli_rollback($link1);
			$cflag = "danger";
			$cmsg = "Failed";
			$msg = "Request could not be processed. Please try again." .$error_msg ;
			mysqli_autocommit($link1, true);

			$result_temp=mysqli_query($link1,"delete from eng_adj_temp_upload where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
		} 
    	mysqli_close($link1);
	   	///// move to parent page
  		header("location:adminstock_adjustment_admin.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;	
}

//// if user hit cancel button
if($_POST['cancel']=='Cancel'){
	mysqli_autocommit( $link1, false);
	$flag = true;
	$err_msg="";	 

	$result=mysqli_query($link1,"delete from eng_adj_temp_upload where  userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
         $err_msg = "Temp data not delete:";
	}
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "All Excel Uploaded Data has been deleted.";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed ".$err_msg.". Please try again.";
	}
	mysqli_close($link1);
	///// move to parent page
	header("location:adminstock_adjustment_admin.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
}

?>

<!DOCTYPE html>

<html>

<head>

 <meta charset="utf-8">

 <meta name="viewport" content="width=device-width, initial-scale=1">

 <title><?=siteTitle?></title>

 <script src="../js/jquery.js"></script>

 <link href="../css/font-awesome.min.css" rel="stylesheet">

 <link href="../css/abc.css" rel="stylesheet">

 <script src="../js/bootstrap.min.js"></script>

 <link href="../css/abc2.css" rel="stylesheet">

 <link rel="stylesheet" href="../css/bootstrap.min.css">

<!-- datatable plugin-->

 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">

 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>

 <script type="text/javascript">

	$(document).ready(function(){

		$("#frm2").validate();

	});

	$(document).ready(function(){

    

	///// Search Show and Remove (use true and false)

		$('#myTable').dataTable( {

		  "searching": false

		} );

	});	

 </script>

 

<script type="text/javascript" src="../js/jquery.validate.js"></script>

<script src="../js/common_js.js"></script>





</head>

<body>

	<div class="container-fluid">

 		<div class="row content">

		<?php 

      include("../includes/leftnav2.php");

    	?>

   		<div class="<?=$screenwidth?> tab-pane fade in active">

      		

   			<div class="panel-group">

			  

		 <h2 align="center"><i class="fa fa-upload"></i> Part Details </h2>

      <h4 align="center" style="color:#060">Step 1 is completed (Excel file is uploaded) .</h4>

      <h4 align="center" style="color:#FF9900">Step 2 Please Go for next process or cancel uploaded data.</h4>

      <h4 align="center" style="color:#FF0000">Do Not Refersh while process is being execute.</h4>

	  <?php if($_REQUEST['msg']){?>

        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                <span aria-hidden="true">&times;</span>

          </button>

            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.

        </div>

        <?php }?>

   			<form id="frm2" name="frm2" class="form-horizontal" action="" method="post">

    			<div class="panel panel-info table-responsive">

      				<div class="panel-heading">Uploded Information</div>

      					<div class="panel-body">

       						<table class="table table-bordered" width="100%"  id="myTable">

            					<thead>

                                	<tr class="<?=$tableheadcolor?>"> 

              							<td width="2%">S.No</td>

										<td width="20%">To Location Name</td>

                                        <td width="20%">From Location Name</td>

                                        <td width="4%"> Type  </td>

                                        <td width="20%">Partcode Name</td>

                                        <td width="4%">Qty</td>

                                    </tr>

            					</thead>

            					<tbody>

            					<?php

								$i=1;

							 $data_sql="select userid,to_location,partcode,qty,po_type from eng_adj_temp_upload where userid='".$_SESSION['userid']."' and browserid='".$browserid."' order by to_location ";

								$data_shw=mysqli_query($link1,$data_sql);

								while($data_row=mysqli_fetch_assoc($data_shw)){

								?>

              						<tr>

                						<td><?=$i?></td>

                                        <td><?php echo getAnyDetails($data_row["to_location"],"locusername","userloginid","locationuser_master",$link1)."(".$data_row['to_location'].")";?></td>

										<td><?php echo $data_row["userid"];?></td>

										<td><?php echo $data_row["po_type"];?></td>

                                        <td ><?php echo getAnyDetails($data_row["partcode"],"part_name","partcode","partcode_master",$link1)."(".$data_row['partcode'].")";?></td>

                                        <td><?php echo $data_row['qty']; ?></td>

           							</tr>

            					<?php

									//$total+= $data_row['total_cost'];

									$i++;

								}

								?>

            					</tbody>

          					</table>

                            <input type="hidden" id="to_loc" name="to_loc" value="<?php echo $_REQUEST['to_location'] ?>">

                            <input type="hidden" id="stk_typ" name="stk_typ" value="<?php echo $_REQUEST['stock_type'] ?>">

							<div style="text-align:center;">

                            <?php if($_REQUEST['chkmsg']=='Success'){?>

                            <input type="submit" class="btn btn-success" name="upd" id="upd" value="Process" title="Process">

                            <?php } ?>&nbsp;        

                   			<input type="submit" class="btn btn-danger" name="cancel" id="cancel" value="Cancel" title="Cancel Uploaded Data" onClick="return myConfirm();"></div>

      					</div><!--close panel body-->

			          </div><!--close panel-->    

    				</form>

  				</div><!--close panel group-->

 			</div><!--close col-sm-9-->

		</div><!--close row content-->

	</div><!--close container-fluid-->



<?php

include("../includes/footer.php");

include("../includes/connection_close.php");

?>

</body>

</html>



