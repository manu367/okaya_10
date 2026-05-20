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
///// Insert in item data by picking each data row one by one
              $rowp =1;
			  $sel_insid2=mysqli_query($link1,"select max(part_max) as cnt from partcode_master where 1")or die("error1".mysqli_error($link1));
			  $sel_result1=mysqli_fetch_assoc($sel_insid2);
		      $data_tem1="select * from temp_partcode_data where userid='".$_SESSION['userid']."' and browserid='".$browserid."' and location='".$_SESSION['userid']."'";							
			  $data_tem_reus=mysqli_query($link1,$data_tem1);
				while($data_tem_item=mysqli_fetch_assoc($data_tem_reus)){   
	    	//////CRM partcode Start
					
			        
			        $insid=$sel_result1['cnt']+$rowp;
			        /// make 5 digit padding
			        $pad=str_pad($insid,4,"0",STR_PAD_LEFT);
			       //// make logic of partcode code
			        $newpartcode="PA".$pad;
					//////CRM partcode END 	
	// CRM partcode make end
			if($data_tem_item['partcode']!='' && $data_tem_item['vendor_code']!='' && $data_tem_item['barnd_id']!=''  && $data_tem_item['product_id']!='' && $newpartcode==$data_tem_item['partcode']) {
			
	    	$query2="INSERT INTO partcode_master set product_id ='".$data_tem_item['product_id']."', brand_id ='".$data_tem_item['barnd_id']."', model_id ='".$data_tem_item['model_id']."',partcode='".$data_tem_item['partcode']."', part_name='".$data_tem_item['part_name']."' , hsn_code ='".$data_tem_item['hsn_code']."' , vendor_partcode='".$data_tem_item['vendor_code']."',part_desc='".$data_tem_item['part_desc']."', customer_price ='".$data_tem_item['customer_price']."', part_category='".$data_tem_item['part_category']."', part_for='".$data_tem_item['part_for']."', status='".$data_tem_item['status']."',createdate='".date("Y-m-d H:i:s")."',createby='".$_SESSION['userid']."',l1_price='".$data_tem_item['l1_price']."',location_price='".$data_tem_item['location_price']."',repair_code='".$data_tem_item['repair_code']."',uploader='Y',part_max='".$pad."', customer_partcode = '".$data_tem_item['customer_partcode']."', wp = '".$data_tem_item['wp']."', dwp = '".$data_tem_item['dwp']."' ";
		 	$result = mysqli_query($link1, $query2);
		   	//// check if query is not executed
		   	if (!$result) {
	        	$flag = false;
              	$error_msg = "Error details4: " . mysqli_error($link1) . ".";
			}		   
		   
		}/// close for loop
		$flag = dailyActivity($_SESSION['userid'],$data_tem_item['partcode'],"PARTCODE","ADD",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		///// check both master and data query are successfully executed
		
   $rowp++;	
								} /////close while loop
					/////// final delete all data from tem 
					 
					$result_temp=mysqli_query($link1,"delete from temp_partcode_data where userid='".$_SESSION['userid']."' and browserid='".$browserid."'  and location='".$_SESSION['userid']."'");
					//// check if query is not executed
						if (!$result_temp) {
	 		 		   $flag = false;
       				   $error_msg = "temp data not delete: " . mysqli_error($link1) . ".";
   						 }
					 			
                             
   if ($flag) {
        	mysqli_commit($link1);
			$cflag = "success";
			$cmsg = "Success";
        	$msg = "Partcode Create  is successfully Uploaded";
    	} else {
		
			mysqli_rollback($link1);
			$cflag = "danger";
			$cmsg = "Failed";
			$msg = "Request could not be processed. Please try again." .$error_msg ;
			mysqli_autocommit($link1, true);
			$result_temp=mysqli_query($link1,"delete from temp_partcode_data where userid='".$_SESSION['userid']."' and browserid='".$browserid."'  and location='".$_SESSION['userid']."'");
			
		} 
		
    	mysqli_close($link1);
	   	///// move to parent page
  		header("location:partcode_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
   
   }
   //// if user hit cancel button
	if($_POST['cancel']=='Cancel'){
	mysqli_autocommit( $link1, false);
	$flag = true;
	$err_msg="";
	$result=mysqli_query($link1,"delete from temp_partcode_data where  userid='".$_SESSION['userid']."' and browserid='".$browserid."'  and location='".$_SESSION['userid']."'");
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
	header("location:partcode_master.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
  
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
			  
		 <h2 align="center"><i class="fa fa-upload"></i> Partcode Details</h2>
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
              							<td width="10%">S.No</td>
                                        <td width="15%">Vendor Partcode </td>
                                        <td width="15%">Customer price</td>
                                        <td width="15%">Partner Price</td>
                                        <td width="15%">ASC Price</td>
										<td width="15%">Part For</td>
										<td width="15%">Part Category</td>
            						</tr>
            					</thead>
            					<tbody>
            					<?php
								$i=1;
								$data_sql="select location,vendor_code,partcode,customer_price,location_price,l1_price,part_for,part_category from temp_partcode_data where userid='".$_SESSION['userid']."' and browserid='".$browserid."'";
								$data_res=mysqli_query($link1,$data_sql);
								while($data_row=mysqli_fetch_assoc($data_res)){
								?>
              						<tr>
                						<td><?=$i?></td>
										<td><?php echo $data_row['vendor_code']; ?></td>
              							<td><?php echo $data_row['customer_price']; ?></td>
              							<td><?php echo $data_row['location_price']; ?></td>   
										<td><?php echo $data_row['l1_price']; ?></td>   
			  							<td><?php echo $data_row['part_for']; ?></td>
										<td><?php echo $data_row['part_category']; ?></td>           
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

