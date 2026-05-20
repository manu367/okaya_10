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
		////////////////////  update by priya on 20 july to block multiple entry 
		$messageIdent_srnrecv = md5($_SESSION['asc_code'] . $po_no . $_POST['upd']);
		//and check it against the stored value:
		$sessionMessageIdent_srnrecv = isset($_SESSION['messageIdent_srnrecv'])?$_SESSION['messageIdent_srnrecv']:'';
		if($messageIdent_srnrecv!=$sessionMessageIdent_srnrecv){//if its different:          
			//save the session var:
			$_SESSION['messageIdent_srnrecv'] = $messageIdent_srnrecv;
			///////////////////////////////////////////////////////////////////////////////	
			mysqli_autocommit($link1, false);
			$flag = true;
			$error_msg="";
			
			////// fetching data from data table//////////////////////////////
			$sql_po_data="select * from billing_product_items where challan_no='".$po_no."'";
			$res_poData=mysqli_query($link1,$sql_po_data)or die("error1".mysqli_error());
			
			while($row_poData=mysqli_fetch_assoc($res_poData)){
				///// initialize posted variables
				$reqqty="req_qty".$row_poData['id'];
				$okqty="ok_qty".$row_poData['id'];
				$damageqty="damage_qty".$row_poData['id'];
				$missqty="miss_qty".$row_poData['id'];
				
				///// update stock in  client inventory //
				if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['to_location']."'"))>0){
					///if product is exist in inventory then update its qty 
					$result=mysqli_query($link1,"update client_inventory set okqty=okqty+'".$_POST[$okqty]."',broken=broken+'".$_POST[$damageqty]."',missing=missing+'".$_POST[$missqty]."',updatedate='".$datetime."' where partcode='".$row_poData['partcode']."' and location_code='".$row_poData['to_location']."'");
				}else{			
					//// if product is not exist then add in inventory
					$result=mysqli_query($link1,"insert into client_inventory set location_code='".$row_poData['to_location']."',partcode='".$row_poData['partcode']."',okqty='".$_POST[$okqty]."',broken='".$_POST[$damageqty]."',missing='".$_POST[$missqty]."',updatedate='".$datetime."'");
				}
				//// check if query is not executed
				if (!$result) {
					$flag = false;
					$error_msg = "Error details1: " . mysqli_error($link1) . ".";
				}
				////// insert in stock ledger////
				### CASE 1 if user enter somthing in ok qty
				if($_POST[$okqty]!="" && $_POST[$okqty]!=0 && $_POST[$okqty]!=0.00){
					$flag=stockLedger($po_no,$today,$row_poData['partcode'],$row_poData['to_location'],$row_poData['from_location'],"IN","OK","Sale Return Receive","",$_POST[$okqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
				}
				### CASE 2 if user enter somthing in damage qty
				if($_POST[$damageqty]!="" && $_POST[$damageqty]!=0 && $_POST[$damageqty]!=0.00){
					$flag=stockLedger($po_no,$today,$row_poData['partcode'],$row_poData['to_location'],$row_poData['from_location'],"IN","DAMAGE","Sale Return Receive","" ,$_POST[$damageqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
				}
				### CASE 3 if user enter somthing in missing qty
				if($_POST[$missqty]!="" && $_POST[$missqty]!=0 && $_POST[$missqty]!=0.00){
					$flag=stockLedger($po_no,$today,$row_poData['partcode'],$row_poData['to_location'],$row_poData['from_location'],"IN","MISSING","Sale Return Receive","" ,$_POST[$missqty],$row_poData['price'],$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
				}
				
				///// update data table		   
				$result=mysqli_query($link1,"update billing_product_items set okqty='".$_POST[$okqty]."',broken='".$_POST[$damageqty]."',missing='".$_POST[$missqty]."' where id='".$row_poData['id']."'");
				//// check if query is not executed
				if(!$result) {
					$flag = false;
					$error_msg = "Error details2: " . mysqli_error($link1) . ".";
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
			} 
			mysqli_close($link1);
			///// move to parent page
			header("location:stock_in_salereturn.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
			exit;
		}else {
			$msg="Re-submission not allowed.";
			$cflag="danger";
			$cmsg="Failed";
			header("location:stock_in_salereturn.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
			exit; 
		}
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
      <h2 align="center"><i class="fa fa-book"></i><?php if($po_row['po_type']=='Sale Return'){  ?> Receive Purchase Return <?php } if($po_row['po_type']=='STN' || $po_row['po_type']=='Stock Transfer'){ ?> Receive STN <?php } ?></h2><br/>
   <div class="panel-group">
      <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"></div>
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
              <td>Product</td>
              <td>Brand</td>
              <td>Partcode</td>
              <td>Requested Qty</td>
              <td>OK</td>
              <td>Damaged</td>
              <td>Missing</td>
			  <td>Total Amt</td>
			    <td>Old Invoice No</td>
            </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$data_sql="select * from billing_product_items where challan_no='".$po_no."' ";
			$data_res=mysqli_query($link1,$data_sql);
			while($data_row=mysqli_fetch_assoc($data_res)){
				$part_vender=explode("~",getAnyDetails($data_row['partcode'],"part_name","partcode","partcode_master",$link1));
			?>
              <tr>
                <td><?=$i?></td>
               <td><?=getAnyDetails($data_row['product_id'],"product_name","product_id" ,"product_master",$link1);?><input type="hidden" name="pro_id<?=$data_row['id']?>" id="pro_id<?=$i?>" value="<?=$data_row['product_id']?>"></td>
              <td><?=getAnyDetails($data_row['brand_id'],"brand","brand_id" ,"brand_master",$link1);?><input type="hidden" name="brand_id<?=$data_row['id']?>" id="brand_id<?=$i?>" value="<?=$data_row['brand_id']?>"></td>
              <td><?=$part['0']." (".$part_vender['0'].")"?><input type="hidden" name="model_id<?=$data_row['id']?>" id="model_id<?=$i?>" value="<?=$data_row['model_id']?>"></td>
              <td><?=$data_row['qty'];?><input type="hidden" name="req_qty<?=$data_row['id']?>" id="req_qty<?=$i?>" value="<?=$data_row['qty']?>"></td>
               <td style="text-align:right"><input type="text" class="form-control" style="width:50px;" name="ok_qty<?=$data_row[id]?>" id="ok_qty<?=$i?>"  autocomplete="off" required onblur="checkRecQty('<?=$i?>');myFunction(this.value,'<?=$i?>','ok_qty');" onkeypress="return onlyNumbers(this.value);" value="<?=$data_row['qty']?>"></td>
                <td style="text-align:right"><input type="text" class="form-control" style="width:50px;" name="damage_qty<?=$data_row[id]?>" id="damage_qty<?=$i?>"  autocomplete="off" required onblur="checkRecQty('<?=$i?>');myFunction(this.value,'<?=$i?>','damage_qty');" onkeypress="return onlyNumbers(this.value);" value="0"></td>
                <td style="text-align:right"><input type="text" class="form-control" style="width:50px;" name="miss_qty<?=$data_row[id]?>" id="miss_qty<?=$i?>"  autocomplete="off" value="0" readonly></td>
			  <td><?=$data_row['item_total'];?></td>      
			    <td><?=$data_row['old_challan'];?></td>           
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
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
