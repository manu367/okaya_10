<?php
require_once("../includes/config.php");
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['refid']);
////////////////////////////////////////// fetching datta from table///////////////////////////////////////////////
$po_sql="select * from billing_master where challan_no='".$po_no."' ";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

$msg="";
///// after hitting receive button ///
if($_POST){
 if ($_POST['upd']=='Receive'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	
	////// fetching data from data table//////////////////////////////////////////////////////////////////////////////////////////
 $sql_po_data="select * from billing_product_items where challan_no='".$po_no."'";
    $res_poData=mysqli_query($link1,$sql_po_data)or die("error1".mysqli_error());
    while($row_poData=mysqli_fetch_assoc($res_poData)){
		################### Insert in imei detail table By Vikas ########################
		$stock_type="stock_type".$row_poData['id'];
		$cat_type = getAnyDetails($row_poData['partcode'],"part_category","partcode","partcode_master",$link1);
		
		if(($cat_type=='UNIT')||($cat_type=='BOX')){
			 
			$sql_job_info = mysqli_fetch_assoc(mysqli_query($link1,"select imei,sec_imei,model_id from jobsheet_data where job_no='".$row_poData['job_no']."'"));
			
			$sql31=mysqli_query($link1,"insert into imei_details  set  location_code='".$_SESSION['asc_code']."',imei1 ='".$sql_job_info['imei']."',imei2 ='".$sql_job_info['sec_imei']."',model_id ='".$sql_job_info['model_id']."',status= '1' ,entry_date='".$today."', partcode = '".$row_poData['partcode']."',stock_type='faulty', grn_no='".$row_poData['challan_no']."',status_type='".$_POST[$stock_type]."',grn_date ='".$today."'");
		}
		
		###########################################################################
	
		  ///// initialize posted variables
		  
		
		  ///// update stock in  client inventory  Ok Receive//
		  if($_POST[$stock_type]=='OK'){
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['to_location']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set faulty=faulty+'1',updatedate='".$datetime."' where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['to_location']."'");
		  }		
		  else{			
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into client_inventory set location_code='".$row_poData['to_location']."',partcode='".$row_poData['partcode']."',faulty='1',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg = "Error details1: " . mysqli_error($link1) . ".";
           }
		     $flag=stockLedgerO($po_no,$today,$row_poData['partcode'],$row_poData['from_location'],$row_poData['to_location'],"IN","Fulty","Faulty Part Receive","",$_POST[$okqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag,$row_poData['to_location']);
			  $result1=mysqli_query($link1,"update billing_product_items set okqty='1', status='4' where id='".$row_poData['id']."'");
		  }
		  
		  
	////////////////////////////////Damage Receove///////////////////////////
	
	
	  ///// update stock in  client inventory  Ok Receive//
		  if($_POST[$stock_type]=='Damage'){
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['to_location']."'"))>0){
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set broken=broken+'1',updatedate='".$datetime."' where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['to_location']."'");
		  }		
		  else{			
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into client_inventory set location_code='".$row_poData['to_location']."',partcode='".$row_poData['partcode']."',broken='1',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg = "Error details1: " . mysqli_error($link1) . ".";
           }
		     $flag=stockLedgerO($po_no,$today,$row_poData['partcode'],$row_poData['from_location'],$row_poData['to_location'],"IN","Damage","Faulty Part Receive","",$_POST[$okqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag,$row_poData['to_location']);
			  $result1=mysqli_query($link1,"update billing_product_items set broken='1',status='4' where id='".$row_poData['id']."'");
		  }	  
		  	  ///// update stock in  client inventory  Ok Receive//
		  if($_POST[$stock_type]=='Missing'){
			  
		  if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['to_location']."'"))>0){
			  // print_r('kkkkkkkkkkkk');exit;
			 ///if product is exist in inventory then update its qty 
			$result=mysqli_query($link1,"update client_inventory set missing=missing+'1',updatedate='".$datetime."' where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['to_location']."'");
		  }		
		  else{		
			   //print_r('jjjjjjjjjj');exit;
			  //echo "insert into client_inventory set location_code='".$row_poData['to_location']."',partcode='".$row_poData['partcode']."',missing='1',updatedate='".$datetime."'";exit;
			 //// if product is not exist then add in inventory
			 $result=mysqli_query($link1,"insert into client_inventory set location_code='".$row_poData['to_location']."',partcode='".$row_poData['partcode']."',missing='1',updatedate='".$datetime."'");
		  }
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
               $error_msg = "Error details1: " . mysqli_error($link1) . ".";
           }
			 $flag=stockLedgerO($po_no,$today,$row_poData['partcode'],$row_poData['from_location'],$row_poData['to_location'],"IN","Missing","Faulty Part Missing","",$_POST[$okqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag,$row_poData['to_location']); 
			$result1 = mysqli_query($link1, "update client_inventory set faulty=faulty+'1',updatedate='" . $datetime . "' where partcode='" . $row_poData['partcode'] . "' and location_code='" . $row_poData['from_location'] . "'");

            $update_part = mysqli_query($link1, "update part_to_credit set status ='1', qty = '1', missing_in_challan='" . $po_no . "', remark = 'Part Missing' where partcode='" . $row_poData['partcode'] . "' and job_no='" . $row_poData['job_no'] . "' and challan_no='" . $row_poData['challan_no'] . "'");
            if (!$update_part) {
              $flag = false;
              $error_msg = "Error details#Part: " . mysqli_error($link1) . ".";
            }  
			  
	$flag=stockLedgerO($po_no,$today,$row_poData['partcode'],$row_poData['from_location'],$row_poData['to_location'],"IN","Faulty","Faulty Part Receive","",$_POST[$okqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag,$row_poData['from_location']);		  
			  
		      //print_r('ggggggggggggg');exit;
			  
			  $result1=mysqli_query($link1,"update billing_product_items set missing='1',status='4' where id='".$row_poData['id']."'");
		  }	
		  
		
	}//// close while loop
	//// Update status in  master table
    $result=mysqli_query($link1,"update billing_master set status='4',receive_date='".$today."',rcv_rmk='".$_POST['rcv_rmk']."'  where challan_no ='".$po_no."' ");
	//// check if query is not executed
    if (!$result) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$ref_no,"Sale Return","RECEIVE",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$msg="Successfully Sale Return Received for ".$po_no;
		$cflag="success";
		$cmsg="Success";
    } else {
		mysqli_rollback($link1);
		$cflag="danger";
		$cmsg="Failed";
		$msg = "Request could not be processed. Please try again. ".$error_msg;
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
   header("location:stock_in_salereturn.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
<script type="text/javascript">
function checkRecQty(a){
	var reqqty=0;
	var okqty=0;
	var damageqty=0;
	//// check requested qty
    if(document.getElementById("req_qty"+a).value==""){
       reqqty=0;
	}else{
	   reqqty=parseInt(document.getElementById("req_qty"+a).value);
	}
	//// check enter ok qty
    if(document.getElementById("ok_qty"+a).value==""){
       okqty=0;
    }else{
       okqty=parseInt(document.getElementById("ok_qty"+a).value);
    }
	//// check enter damage qty
    if(document.getElementById("damage_qty"+a).value==""){
       damageqty=0;
    }else{
       damageqty=parseInt(document.getElementById("damage_qty"+a).value);
    }
	//// check enter qty should not be greater than requested qty
    if(reqqty < (okqty + damageqty)){
       alert("Ok Qty & Damage Qty can not more than requested Qty!");
		document.getElementById("miss_qty"+a).value=0;
		document.getElementById("damage_qty"+a).value=0;
		//document.getElementById("ok_qty"+a).focus();
		document.getElementById("upd").disabled=true;
    }else{
		document.getElementById("miss_qty"+a).value=(reqqty - (okqty + damageqty));
		document.getElementById("miss_qty"+a).focus();
		document.getElementById("upd").disabled=false;
	}
}
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
      <h2 align="center"><i class="fa fa-book"></i> Receive Faulty Parts<br/>
   </h2>
      <div class="panel-group">
        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">Sale Return Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">From Location Name:</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["from_location"],"locationname","location_code","location_master",$link1)."(".$po_row['from_location'].")";?></td>
                <td width="20%"><label class="control-label">To Location Name:</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["to_location"],"locationname","location_code","location_master",$link1)."(".$po_row['to_location'].")";?></td>
              </tr>
              <tr>
                <td><label class="control-label">From State</label></td>
                <td><?php echo getAnyDetails($po_row['from_stateid'],"state","stateid","state_master",$link1 );?></td>
                <td><label class="control-label">To State</label></td>
                <td><?php echo getAnyDetails($po_row['to_stateid'],"state","stateid","state_master",$link1 );?></td>
              </tr>
              <tr>
                <td><label class="control-label">Address</label></td>
                <td><?php echo $po_row['from_addrs'];?></td>
                <td><label class="control-label">Address</label></td>
                <td><?php echo $po_row['to_addrs'];?></td>
              </tr>  
			  <tr>
                <td><label class="control-label">Challan No.</label></td>
                <td><?php echo $po_row['challan_no'];?></td>
                <td><label class="control-label">Entry Date</label></td>
                <td><?php echo dt_format($po_row['sale_date']);?></td>
              </tr> 
               <tr>
                <td><label class="control-label">Stock Category</label></td>
                <td><?php if($po_row['po_type']=='P2C'){ echo "Faulty"; } else { echo $po_row['po_type'];}?></td>
                <td><label class="control-label">Docket No.</label></td>
                <td><?php echo $po_row['docket_no'];?></td>
              </tr>           
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Items Information</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <thead>
               <td>S.No</td>
               <td>Brand</td>
              <td>Product</td>              
              <td>Model</td>
              <td>Part</td>
               <td>Job no</td>
              <td> Qty</td>
              <td>Receive Status</td>
             
            </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$data_sql="select * from billing_product_items where challan_no='".$po_no."' ";
			$data_res=mysqli_query($link1,$data_sql);
			while($data_row=mysqli_fetch_assoc($data_res)){
			?>
              <tr>
                <td><?=$i?></td>
                <td><?=getAnyDetails($data_row['brand_id'],"brand","brand_id" ,"brand_master",$link1);?><input type="hidden" name="brand_id<?=$data_row['id']?>" id="brand_id<?=$i?>" value="<?=$data_row['brand_id']?>"></td>
               <td><?=getAnyDetails($data_row['product_id'],"product_name","product_id" ,"product_master",$link1);?><input type="hidden" name="pro_id<?=$data_row['id']?>" id="pro_id<?=$i?>" value="<?=$data_row['product_id']?>"></td>
             
              <td><?=getAnyDetails($data_row['model_id'],"model","model_id" ,"model_master",$link1);?><input type="hidden" name="model_id<?=$data_row['id']?>" id="model_id<?=$i?>" value="<?=$data_row['model_id']?>"></td>
                <td><?=getAnyDetails($data_row['partcode'],"part_name","partcode" ,"partcode_master",$link1);?><input type="hidden" name="partcode<?=$data_row['id']?>" id="partcode<?=$i?>" value="<?=$data_row['partcode']?>"></td>
                <td><?=$data_row['job_no'];?></td> 
              <td><?=$data_row['qty'];?><input type="hidden" name="req_qty<?=$data_row['id']?>" id="req_qty<?=$i?>" value="<?=$data_row['qty']?>"></td>
               <td style="text-align:right">   <select name="stock_type<?=$data_row['id']?>" id="stock_type<?=$data_row['id']?>" class="form-control" style="width:150px;">
            
              <option value='OK'>OK</option>
              <option value='Damage'>Damage</option>
              <option value='Missing'>Missing</option>
            </select></td>
                   
                </tr>
            <?php
			$total+= $data_row['item_total'];
			$i++;
			}
			?>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading">Receive</div>
      <div class="panel-body">
        <table class="table table-bordered" width="100%">
            <tbody>          
               <tr>
			   <td><label class="control-label">Total Amount</label></td>
                 <td><input type="text" name="tot_amt" id="tot_amt" class="number form-control required"   value="<?=$total;?>"  readonly/></td>
                   <td><label class="control-label">Receive Remark <span style="color:#F00">*</span></label></td>
                 <td><textarea  name="rcv_rmk" id="rcv_rmk" class="form-control required"  required /></textarea></td>
                   
                 </tr>
               <tr>
                 <td colspan="4" align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive">&nbsp;
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onclick="window.location.href='stock_in_salereturn.php?<?=$pagenav?>'">
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
<meta http-equiv="Content-Type" content="text/html; charset=WINDOWS-1252" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
