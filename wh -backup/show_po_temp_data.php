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
 
    

							   //// Make System generated PO no.//////
	$fromaddress = explode ("~" ,getAnyDetails($_SESSION['asc_code'],"locationaddress,stateid","location_code","location_master",$link1));

	$res_po=mysqli_query($link1,"select max(ch_temp) as no from supplier_po_master where location_code='".$_SESSION['asc_code']."'");
	$row_po=mysqli_fetch_array($res_po);
	 $c_nos=$row_po['no']+1;
	$po_no=$_SESSION['asc_code']."V".$c_nos; 
	mysqli_autocommit($link1, false);
	$flag = true;
	
		/////////////////////////// insert into master table//////////////////////////////////////////////////////////
    $po_add="INSERT INTO supplier_po_master set system_ref_no='".$po_no."', entry_date='".$today."' , location_code  ='".$_SESSION['asc_code']."' ,   ch_temp='".$c_nos."',bill_address ='".$fromaddress[0]."',status='7' ,po_type  ='PTV' ,remark='".$_REQUEST['remark']."',up_file='Y'  ";
   $result=mysqli_query($link1,$po_add);
	//// check if query is not executed
	if (!$result) {
	     $flag = false;
       $error_msg = "Error details1: " . mysqli_error($link1) . ".";
    }

	
	
	//////////////////////////////////////////
///// Insert in item data by picking each data row one by one
		       $data_tem1="select partcode,qty,price,spp_code,value,barnd_id,product_id,model_id,to_location,from_location  from temp_disp_upd where userid='".$_SESSION['asc_code']."' and browserid='".$browserid."'";							
				$data_tem_reus=mysqli_query($link1,$data_tem1);
				$tot=0;
				while($data_tem_item=mysqli_fetch_assoc($data_tem_reus)){   
	    	// checking row value of product and qty should not be blank
			
			$partdet = explode("~",getAnyDetails($data_tem_item['partcode'] ,"l3_price" ,"partcode", "partcode_master" ,$link1));
			$shipdet = getAnyDetails($data_tem_item['to_location'] ,"locationaddress" ,"location_code","location_master",$link1);
			$cst=$partdet[0]*$data_tem_item['qty'];
			$tot+=$cst;
			$sap_code=$data_tem_item['spp_code'];
			$to_code=$data_tem_item['to_location'];
			$to_address=$shipdet;
			
			if($data_tem_item['partcode']!='' && $data_tem_item['qty']!='') {
					/////////// insert data
$query2="insert into supplier_po_data set location_code  ='".$_SESSION['asc_code']."',system_ref_no='".$po_no."',product_id ='".$data_tem_item['product_id']."', brand_id ='".$data_tem_item['barnd_id']."', model_id ='".$data_tem_item['model_id']."', partcode ='".$data_tem_item['partcode']."', qty='".$data_tem_item['qty']."' ,req_qty='".$data_tem_item['qty']."' ,price = '".$partdet[0]."', cost='".$cst."',tax_name='', item_tax='0.0', tax_cost='0.0' , total_cost = '".$cst."'  ,entry_date = '".$today."' ,status='7',flag='1'  ";
		  $result = mysqli_query($link1, $query2);
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
              $error_msg = "Error details: " . mysqli_error($link1) . ".";
           }


		   	/////////// insert  BILLING PRODUCT data
		  
  
			}// close if loop of checking row value of product and qty challan
			
			 else {
			    $flag = false;
				$error_msg = "Challan partcode condistion check ".$data_row1['challan_no'];
			       }// close if loop of checking row value of product and qty should not be blank

		
		}/// close for loop
		
		$query3="update supplier_po_master set total_amt='".$tot."',grand_amt='".$tot."',party_name ='".$sap_code."',bill_to ='".$to_code."',ship_address2='".$to_address."'  where system_ref_no='".$po_no."' ";
	
$result3 = mysqli_query($link1, $query3);
		   //// check if query is not executed
		   if (!$result3) {
	           $flag = false;
              $error_msg = "Error details: " . mysqli_error($link1) . ".";
           }
	////// insert in activity table////

    $flag = dailyActivity($_SESSION['userid'], $po_no, "PO", "ADD", $ip, $link1, $flag);
		///// check both master and data query are successfully executed
$result_temp=mysqli_query($link1,"delete from temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
					//// check if query is not executed
						if (!$result_temp) {
	 		 		   $flag = false;
       				   $error_msg = "temp data not delete: " . mysqli_error($link1) . ".";
   						 }
					 			
                             
   if ($flag) {
        	mysqli_commit($link1);
			$cflag = "success";
			$cmsg = "Success";
        	$msg = "Purchase Order is successfully placed with ref. no.".$po_no;
    	} else {
		
			mysqli_rollback($link1);
			$cflag = "danger";
			$cmsg = "Failed";
			$msg = "Request could not be processed. Please try again." .$error_msg ;
			mysqli_autocommit($link1, true);
			$result_temp=mysqli_query($link1,"delete from temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
			
		} 
		
    	mysqli_close($link1);
	   	///// move to parent page
  		header("location:grn_vendor.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
   
   }
   //// if user hit cancel button
	if($_POST['cancel']=='Cancel'){
	mysqli_autocommit( $link1, false);
	$flag = true;
	$err_msg="";
	$result=mysqli_query($link1,"delete from temp_disp_upd where  userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
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
	header("location:grn_vendor.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  
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
			  
		 <h2 align="center"><i class="fa fa-upload"></i>  Purchase Order  Details</h2>
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
    			<div class="panel panel-info table-responsive">
      				<div class="panel-heading">Uploded Information</div>
      					<div class="panel-body">
       						<table class="table table-bordered" width="100%"  id="myTable">
            					<thead>
                                	<tr class="<?=$tableheadcolor?>"> 
              							<td width="4%">S.No</td>
										<td width="20%">From Location Name</td>
                                        <td width="20%">To Location </td>
              							<td width="20%">Partcode Name</td>
                                        <td width="6%">Qty</td>
                                        <td width="10%">Price</td>
                                    
            						</tr>
            					</thead>
            					<tbody>
            					<?php
								$i=1;
								$data_sql="select challan_no,from_location,to_location,partcode,qty,price from temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'";
								$data_res=mysqli_query($link1,$data_sql);
								while($data_row=mysqli_fetch_assoc($data_res)){
								?>
              						<tr><input type="hidden" name="challan_no" id="challan_no" class="form-control" value="<?php echo $data_row['challan_no']; ?>"  readonly/>
                						<td><?=$i?></td>
									<td><?php echo getAnyDetails($data_row["from_location"],"locationname","location_code","location_master",$link1)."(".$data_row['from_location'].")";?> <input type="hidden" name="from_location" id="from_location" class="form-control" value="<?php echo $data_row["from_location"]; ?>"  readonly/></td>
										<td><?php echo getAnyDetails($data_row["to_location"],"locationname","location_code","location_master",$link1)."(".$data_row['to_location'].")";?> <input type="hidden" name="to_location" id="to_location" class="form-control" value="<?php echo $data_row["to_location"]; ?>"  readonly/></td>
                						<td ><?php echo getAnyDetails($data_row["partcode"],"part_name","partcode","partcode_master",$link1)."(".$data_row['partcode'].")";?></td>
              							<td><?php echo $data_row['qty']; ?></td>
              							
              							<td><?php echo $data_row['price']; ?></td>   
									
                					</tr>
            					<?php
									//$total+= $data_row['total_cost'];
									$i++;
								}
								?>
                                   
              				
            					</tbody>
          					</table>
							<div style="text-align:center;"><input type="submit" class="btn btn-success" name="upd" id="upd" value="Process" title="Process">&nbsp;
                                       
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

