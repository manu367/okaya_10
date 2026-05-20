<?php
require_once("../includes/config.php");
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['refid']);
////////////////////////////////////////// fetching datta from table///////////////////////////////////////////////
$msg="";
///// after hitting receive button ///
if($_POST){
 if ($_POST['upd']=="Reconciliation Process"){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	////// fetching data from data table//////////////////////////////////////////////////////////////////////////////////////////
 $sql_po_data="select * from billing_product_items where missing_reco='R' and missing >0 and (type='PO' or type='PNA') and challan_no='".$po_no."'";
    $res_poData=mysqli_query($link1,$sql_po_data)or die("error1".mysqli_error());
    while($row_poData=mysqli_fetch_assoc($res_poData)){
	
		 if($row_poData['missing']!="" && $row_poData['missing']!=0 && $row_poData['missing']!=0.00){
			    $req_miss_reco = mysqli_query($link1,"UPDATE billing_product_items set missing_reco = '".$_POST['status']."',opration_rmk='".$_POST['mss_reco_rmk']."' where id='".$row_poData['id']."'");
			if(!$req_miss_reco){
				$flag = false;
				$error_msg = "Error details7:req_miss_reco " . mysqli_error($link1) . ".";
				}

				if($_REQUEST['status']=="Y"){
				
				//////wh inventory in case is appr by 
					 $upd_wh = mysqli_query($link1 , "update client_inventory set okqty= okqty+'".$row_poData['missing']."'  where  location_code = '".$row_poData['from_location']."'  and  partcode = '".$row_poData['partcode']."' " );
	   //// check if query is not executed
		  if (!$upd_wh) {
			 $flag = false;
			 echo "Error details: " . mysqli_error($link1) . ".";
		  }
		  /////WH Stock Ladger in case is apprive by 
		  $flag=stockLedger($po_no,$today,$row_poData['partcode'],$row_poData['to_location'],$row_poData['from_location'],"IN","OK","Stock IN","Missing Reconciliation",$row_poData['missing'],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
		 //////ASC inventory OUT case is appr by WH 
		  $upd_asc = mysqli_query($link1 , "update client_inventory set missing= missing-'".$row_poData['missing']."'  where  location_code = '".$_POST['from_code']."'  and  partcode = '".$row_poData['partcode']."' " );
   //// check if query is not executed
	  if (!$upd_asc) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      } 
	  
	    /////ASC Stock Ladger OUT case is apprive by 
		  $flag=stockLedger($po_no,$today,$row_poData['partcode'],$row_poData['to_location'],$row_poData['from_location'],"OUT","Missing","Stock OUT","Missing Reconciliation",$row_poData['missing'],$row_poData['price'],$row_poData['to_location'],$today,$currtime,$ip,$link1,$flag);
				
				}
				if($_REQUEST['status']=="N"){
					$upd_asc = mysqli_query($link1 , "update client_inventory set missing= missing-'".$row_poData['missing']."'  where  location_code = '".$_POST['from_code']."'  and  partcode = '".$row_poData['partcode']."' " );
   //// check if query is not executed
	  if (!$upd_asc) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      }
		 $flag=stockLedger($po_no,$today,$row_poData['partcode'],$row_poData['to_location'],"","OUT","Missing","Stock OUT","Missing Reconciliation",$row_poData['missing'],$row_poData['price'],$row_poData['to_location'],$today,$currtime,$ip,$link1,$flag);
		  
				
				}
		  }
	}
	//// close while loop  
		///// update credit limit of receiver
	if($_REQUEST['status']=="Y"){
		$res_cr = mysqli_query($link1,"UPDATE current_cr_status set credit_bal = credit_bal + '".$_POST['tot_amt']."', total_credit_limit = total_credit_limit + '".$_POST['tot_amt']."' where location_code='".$_POST['
		']."'");
		if(!$res_cr){
			$flag = false;
			$error_msg = "Error details7: " . mysqli_error($link1) . ".";
		}
		////// insert in location account ledger
		$res_ac_ledger = mysqli_query($link1,"INSERT INTO location_account_ledger set location_code='".$_POST['from_code']."',entry_date='".$today."',remark='Credit Aginst- Missing PO Part Reconciliation', transaction_type = 'Missing Reconciliation',month_year='".date("m-Y")."',crdr='CR',amount='".$_POST['tot_amt']."'");
		if(!$res_ac_ledger){
			$flag = false;
			$error_msg = "Error details8: " . mysqli_error($link1) . ".";
		}
		
		
	   $upd_asc = mysqli_query($link1 , "update client_inventory set missing= missing-'".$row_poData['missing']."'  where  location_code = '".$_POST['from_code']."'  and  partcode = '".$row_poData['partcode']."' " );
   //// check if query is not executed
	  if (!$upd_asc) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      }
	   
		$st= "Missing Reconciliation Approved";
	}else{
	  
	  $res_cr = mysqli_query($link1,"UPDATE current_cr_status set credit_bal = credit_bal - '".$_POST['tot_amt']."', total_credit_limit = total_credit_limit - '".$_POST['tot_amt']."' where location_code='".$_POST['from_code']."'");
		if(!$res_cr){
			$flag = false;
			$error_msg = "Error details7: " . mysqli_error($link1) . ".";
		}
		////// insert in location account ledger
		$res_ac_ledger = mysqli_query($link1,"INSERT INTO location_account_ledger set location_code='".$_POST['from_code']."',entry_date='".$today."',remark='Debit Aginst- Missing PO Part Reconciliation', transaction_type = 'Missing Reconciliation',month_year='".date("m-Y")."',crdr='DR',amount='".$_POST['tot_amt']."'");
		if(!$res_ac_ledger){
			$flag = false;
			$error_msg = "Error details8: " . mysqli_error($link1) . ".";
		}
	  
	  $upd_asc = mysqli_query($link1 , "update client_inventory set missing= missing-'".$row_poData['missing']."'  where  location_code = '".$_POST['from_code']."'  and  partcode = '".$row_poData['partcode']."' " );
   //// check if query is not executed
	  if (!$upd_asc) {
	     $flag = false;
         echo "Error details: " . mysqli_error($link1) . ".";
      }
		$st= "Missing Reconciliation Rejected";
	}
	//
 	////// insert in activity table////
	
	$flag=dailyActivity($_SESSION['userid'],$po_no,$st,"Process Aginst Reco Request",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$msg="Successfully Faulty Return Received for ".$po_no;
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
   header("location:wh_reco_mss_req_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <script src="../js/jquery.min.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script type="text/javascript">
$(document).ready(function(){
    $("#frm2").validate();
});
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-book"></i> Reconciliation Against Missing Parts<br/><br />
   </h2>
      <div class="panel-group">
        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    
     
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Damage Items Information</div>
      <div class="panel-body">
        <table width="100%" height="104" class="table table-bordered">
          <thead>
          </thead>
          <tr class="<?=$tableheadcolor?>">
            <td width="3%">S.No</td>
            <td width="10%">Challan No.</td>
            <td width="10%">Job No.</td>
            <td width="10%">Model</td>
            <td width="15%">Part</td>
            <td width="8%">Price</td>
            <td width="7%"> Qty</td>
            <td width="9%">Value</td>
           
          </tr>
          <tbody>
            <?php
			$i=1;
			$data_sql="select * from billing_product_items where missing_reco='R' and missing >0 and (type='PO' or type='PNA') and challan_no='".$po_no."' ";
			$data_res=mysqli_query($link1,$data_sql);
			while($data_row=mysqli_fetch_assoc($data_res)){
			?>
            <tr>
              <td><?=$i?></td>
              <td><?=$po_no;?></td>
              <td><?=$data_row['job_no'];?></td>
              <td><?=getAnyDetails($data_row['model_id'],"model","model_id" ,"model_master",$link1);?></td>
              <td><?=getAnyDetails($data_row['partcode'],"part_name","partcode" ,"partcode_master",$link1);?>-(<?=$data_row['partcode']?>)</td>
              <td><?=$data_row['price'];?></td>
              <td><?=$data_row['missing'];?></td>
              <td style="text-align:right"><?php $value=$data_row['missing']*$data_row['price']; echo $value;?></td>
             
            </tr>
            <?php
			$total+=$value;
			$i++;
			}
			?>
          </tbody>
        </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Action</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>          
               <tr>
			   <td><label class="control-label">Total Amount</label></td>
                 <td>
                 <input type="hidden" name="from_code" id="from_code" value="<?=$_REQUEST['f_location'];?>"/>
                 <input type="text" name="tot_amt" id="tot_amt" class="number form-control required"   value="<?=$total;?>"  readonly/></td>
                   <td><label class="control-label">Missing Reconciliation Remark <span style="color:#F00">*</span></label></td>
                 <td><textarea  name="mss_reco_rmk" id="mss_reco_rmk" class="form-control required" required /></textarea></td>
                   
                 </tr>
                  <tr>
			      <td><label class="control-label">Approve/Reject</label></td>
                 <td>
                  <select   id="status" name="status" class="form-control" required ><option value="">Please Select</option>
                <option value="Y" >Approved</option><option value="N" >Rejected</option></select></td>
                   <td></td>
                 <td></td>
                   
                 </tr>
                
               <tr>
                 <td colspan="4" align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="upd" id="Request Reco" value="Reconciliation Process" title="Reconciliation Process">&nbsp;
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='wh_reco_mss_req_list.php?<?=$pagenav?>'">
                 </td>
                </tr>
            </tbody>
          </table>
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
</html><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
