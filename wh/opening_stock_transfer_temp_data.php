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



	$data_sql12 = "select * from ost_temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."' group by to_location  ";

				

	$data_res12=mysqli_query($link1,$data_sql12);

	while($row1=mysqli_fetch_array($data_res12)){

		 $sql_dccount = "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'";

		 

		 $res_dccount = mysqli_query($link1,$sql_dccount)or die("error1".mysqli_error($link1));



		  $row_dccount = mysqli_fetch_array($res_dccount);

		  	if($_REQUEST['doc_type']=="INV"){

				$next_invno = $row_dccount['inv_counter']+1;

				$invoice_no = $row_dccount['inv_series']."".$row_dccount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);	

				/////update next counter against invoice

				$res_upd = mysqli_query($link1,"UPDATE invoice_counter set inv_counter = '".$next_invno."' where location_code='".$_SESSION['asc_code']."'");

				/// check if query is execute or not//

				if(!$res_upd){

					$flag = false;

					$error_msg = "Error1". mysqli_error($link1) . ".";

				}

				

			}else{

					

				$next_dcno = $row_dccount['stn_counter']+1;

				/////update next counter against invoice

				$res_upd = mysqli_query($link1,"UPDATE invoice_counter set stn_counter = '".$next_dcno."' where location_code='".$_SESSION['asc_code']."'");

				/// check if query is execute or not//

				if(!$res_upd){

					$flag = false;

					$error_msg = "Error1". mysqli_error($link1) . ".";

				}

				///// make invoice no.

				$invoice_no = $row_dccount['stn_series']."".$row_dccount['fy']."".str_pad($next_dcno,4,"0",STR_PAD_LEFT);

			}

	

	 		//$fromlocdet = explode("~",getAnyDetails($_SESSION['asc_code'],"location_code,locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));

			$tolocdet = explode("~",getAnyDetails($row1['to_location'],"location_code,locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));

		$fromlocdet=$tolocdet;

	//////intialize tax variables

	$sgst_final_val=0;

	$cgst_final_val=0;

	$igst_final_val=0;

	$basic_cost=0;

	$total_qty = 0;

	$total_reqqty = 0;

	$total_procqty = 0;

	$inv_tot_cost=0;

	$pno=array();



	$sql2 = "select * from ost_temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."' and  to_location='".$row1['to_location']."' ";	

	

	$data_res=mysqli_query($link1,$sql2);

	while($row2=mysqli_fetch_array($data_res)){
$stock_type=$row2['stock_type'];
		//////pick max counter of INVOICE

		$sql_billdata = "INSERT INTO billing_product_items set from_location='".$fromlocdet[0]."', to_location='".$tolocdet[0]."',challan_no='".$invoice_no."',type='".$row2['po_type']."',product_id='".$row2['product_id']."',brand_id='".$row2['brand_id']."',model_id='".$row2['model_id']."', hsn_code='".$row2['hsn_code']."',partcode='".$row2['partcode']."',part_name='".$row2['part_name']."',qty='".$row2['qty']."',okqty='".$row2['qty']."',price='".$row2['price']."',uom='PCS',value='".$row2['cost']."',basic_amt='".$row2['cost']."',cgst_per='".$row2['cgst_per']."',cgst_amt='".$row2['cgst_amt']."',sgst_per='".$row2['sgst_per']."',sgst_amt='".$row2['sgst_amt']."',igst_per='".$row2['igst_per']."',igst_amt='".$row2['igst_amt']."',item_total='".$row2['item_total']."',stock_type='".$stock_type."',attach_file='".$row2['file_name']."',sale_date = '".$today."' ";
		
		
		//// check if query is not executed

		$res_bilitems = mysqli_query($link1,$sql_billdata);

		if (!$res_bilitems) {

			$flag = false;

			$error_msg = "Error details_bill: " . mysqli_error($link1) . ".";

		}

									

		//////////////////////// for IN transit QTY update///////////////////////////////// 

		

		if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$row2['partcode']."' and location_code='".$tolocdet[0]."'"))>0){

			$result=mysqli_query($link1,"update client_inventory set  in_transit  = in_transit+'".$row2['qty']."',updatedate='".$datetime."' where partcode='".$row2['partcode']."' and location_code='".$tolocdet[0]."' " );

				

			//// check if query is not executed

			if (!$result) {

				$flag = false;

				$error_msg = "Error details5_inv: " . mysqli_error($link1) . ".";

			}

			

		}else{					

			$result=mysqli_query($link1,"insert into client_inventory set location_code='".$tolocdet[0]."',partcode='".$row2['partcode']."',in_transit='".$row2['qty']."',updatedate='".$datetime."',product_id='".$row2['product_id']."',brand_id='".$row2['brand_id']."'");

			

			//// check if query is not executed

			if (!$result) {

				$flag = false;

				$error_msg = "Error details5_inv_in: " . mysqli_error($link1) . ".";

			}		

		}	

		

		///// entry in stock ledger

		/*if($row2['qty']>0){

			$flag = stockLedger($invoice_no,$today,$row2['partcode'],$_SESSION['asc_code'],$tolocdet[0],"OST",$stock_type,"Opening Stock","",$row2['qty'],$row2['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);

	

		}*/

			

		$basic_cost+=$row2['cost'];

		$inv_tot_cost += $row2['cost'] + $row2['cgst_amt'] + $row2['sgst_amt'] + $row2['igst_amt'];

		$total_qty += $row2['qty'];

		$cgst_final_val+=$row2['cgst_amt'];

		$sgst_final_val+=$row2['sgst_amt'];

		$igst_final_val+=$row2['igst_amt'];

	

	}

	

		$sql_billmaster = "INSERT INTO billing_master set from_location='".$fromlocdet[0]."', to_location='".$tolocdet[0]."',from_gst_no='".$fromlocdet[9]."',to_gst_no='".$tolocdet[9]."', party_name='".$tolocdet[1]."',challan_no='".$invoice_no."',po_no='',sale_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',logged_by='".$_SESSION['userid']."',billing_rmk='Against Opening Stock Transfer',bill_from='".$fromlocdet[1]."',from_stateid='".$fromlocdet['6']."',to_stateid='".$tolocdet[6]."' ,bill_to='".$tolocdet[1]."',from_addrs='".$fromlocdet[2]."',disp_addrs='".$fromlocdet[3]."',to_addrs='".$tolocdet[2]."',deliv_addrs='".$tolocdet[4]."',status='3',document_type='".$row1['doc_type']."',finvoice_no='',po_type='Opening Stock',basic_cost='".$basic_cost."',  total_cost='".$inv_tot_cost."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',igst_amt='".$igst_final_val."'"; 

	

		$res_billmaster = mysqli_query($link1,$sql_billmaster);

		//// check if query is not executed

		if (!$res_billmaster) {

			$flag = false;

			$error_msg = "Error details6: " . mysqli_error($link1) . ".";

		}

	

	}

	

	////// insert in activity table////

	$flag = dailyActivity($_SESSION['userid'],$invoice_no,"Opening Stock","Dispatch",$ip,$link1,$flag);

	////////////////// Update In Po Master ///////////////////

	

	$result_temp=mysqli_query($link1,"delete from ost_temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");

	//// check if query is not executed

	if (!$result_temp) {

	   $flag = false;

	   $error_msg = "temp data not delete: " . mysqli_error($link1) . ".";

   	}



	   if ($flag) {

        	mysqli_commit($link1);

			$cflag = "success";

			$cmsg = "Success";

        	$msg = "Temp data deleted  And  Successfully Dispatched Challan. no.".$invoice_no;

    	} else {

			mysqli_rollback($link1);

			$cflag = "danger";

			$cmsg = "Failed";

			$msg = "Request could not be processed. Please try again." .$error_msg ;

			mysqli_autocommit($link1, true);

			$result_temp=mysqli_query($link1,"delete from ost_temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");

		} 

    	mysqli_close($link1);

	   	///// move to parent page

  		header("location:opening_stock_transfer_upld.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

		exit;

		

}



//// if user hit cancel button

if($_POST['cancel']=='Cancel'){

	mysqli_autocommit( $link1, false);

	$flag = true;

	$err_msg="";	        

	$result=mysqli_query($link1,"delete from ost_temp_disp_upd where  userid='".$_SESSION['userid']."' and browserid='".$browserid."'");

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

	header("location:opening_stock_transfer_upld.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

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

      include("../includes/leftnavemp2.php");

    	?>

   		<div class="<?=$screenwidth?> tab-pane fade in active">

      		

   			<div class="panel-group">

			  

		 <h2 align="center"><i class="fa fa-upload"></i> Part Details</h2>

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

                                         <td width="6%">Stock Type</td>
                                         <td width="6%">Document Type  </td>

              							<td width="20%">Partcode Name</td>

                                        <td width="4%">Qty</td>

                                        <td width="10%">Price</td>

                                        <td width="10%">Cost</td>
            						</tr>
            					</thead>

            					<tbody>

            					<?php

								$i=1;

							 $data_sql="select from_location,to_location,partcode,qty,price,cost,po_type,stock_type,doc_type,item_total from ost_temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."' order by to_location ";

								$data_shw=mysqli_query($link1,$data_sql);

								while($data_row=mysqli_fetch_assoc($data_shw)){

								?>

              						<tr>

                						<td><?=$i?></td>

                                        <td><?php echo getAnyDetails($data_row["to_location"],"locationname","location_code","location_master",$link1)."(".$data_row['to_location'].")";?></td>

										<td><?php echo getAnyDetails($data_row["from_location"],"locationname","location_code","location_master",$link1)."(".$data_row['from_location'].")";?></td>

										<td><?php echo $data_row["po_type"];?></td>

                                        <td><?php echo $data_row["stock_type"];?></td>
                                        <td><?php echo $data_row["doc_type"];?></td>

                						<td ><?php echo getAnyDetails($data_row["partcode"],"part_name","partcode","partcode_master",$link1)."(".$data_row['partcode'].")";?></td>

                                        <td><?php echo $data_row['qty']; ?></td>

              							<td><?php echo $data_row['price']; ?></td>   

										<td><?php echo $data_row['item_total']; ?></td>       
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



