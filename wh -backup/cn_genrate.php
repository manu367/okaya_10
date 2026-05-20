<?php
require_once("../includes/config.php");
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['refid']);
$dt = date('m-Y');
////////////////////////////////////////// fetching datta from table///////////////////////////////////////////////
 $po_sql="select * from billing_master where challan_no='".$po_no."' ";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

$msg="";
///// after hitting receive button ///
if($_POST){
 if ($_POST['upd']=='CN GENERATE'){
	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg="";
	////// fetching data from data table//////////////////////////////////////////////////////////////////////////////////////////
$sql_po_data="select * from billing_product_items where challan_no='".$po_no."'";
    $res_poData=mysqli_query($link1,$sql_po_data)or die("error1".mysqli_error());
    while($row_poData=mysqli_fetch_assoc($res_poData)){
	
    $result1=mysqli_query($link1,"update billing_product_items set status='12' where id='".$row_poData['id']."'");
}//// close while loop
	//// Update status in  master table
	   /// insert  into  location_account_ledger table //////////////////////////
	 ///echo "insert into location_account_ledger set transaction_type ='CN Generate', transaction_no='".$po_no."',month_year ='".$dt ."' , crdr = 'CR' , amount = '".$_POST['tot_amt']."' , entry_date = '".$today."' , remark = 'Payment Against Parts' , location_code = '".$_POST['from_location']."' ";
   $result1=mysqli_query($link1,"insert into location_account_ledger set transaction_type ='CN Generate', transaction_no='".$po_no."',month_year ='".$dt ."' , crdr = 'CR' , amount = '".$_POST['tot_amt']."' , entry_date = '".$today."' , remark = 'Payment Against Parts' , location_code = '".$_POST['from_location']."' ");
      $result2=mysqli_query($link1,"update current_cr_status set  credit_bal = credit_bal +'".$_POST['tot_amt']."'  , total_credit_limit = total_credit_limit +'".$_POST['tot_amt']."'   where  location_code = '".$_POST['from_location']."' ");
    $result=mysqli_query($link1,"update billing_master set status='12',receive_date='".$today."',rcv_rmk='".$_POST['rcv_rmk']."'  where challan_no ='".$po_no."' ");
	//// check if query is not executed
    if (!$result) {
	   $flag = false;
	   $error_msg = "Error details3: " . mysqli_error($link1) . ".";
    }
	////// insert in activity table////
	$flag=dailyActivity($_SESSION['userid'],$po_no,"CN Generate","Generate",$_SERVER['REMOTE_ADDR'],$link1,$flag);
		
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$msg="Successfully CN Generated for ".$po_no;
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
 header("location:cn_against_part.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
                <td width="30%"><?php echo getAnyDetails($po_row["from_location"],"locationname","location_code","location_master",$link1)."(".$po_row['from_location'].")";?>
               <input type="hidden" name="from_location" id="from_location" value="<?=$po_row["from_location"]?>"> </td>
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
               <th>S.No</th>
               <th style="text-align:center" width="20%">Product</th>
             
                <th style="text-align:center" width="8%">HSN Code</th>
				 <th style="text-align:center" width="8%">Old Invoice No.</th>
                <th style="text-align:center" width="8%">Bill Qty</th>
                <th style="text-align:center" width="8%">Price</th>                
                <th style="text-align:center" width="11%">Discount/<br>Unit</th>
                <th style="text-align:center" width="8%">Value After Discount</th>
                <?php if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
                <th style="text-align:center" width="12%">SGST(%)</th>
                <th style="text-align:center" width="12%">SGST Amount</th>
                <th style="text-align:center" width="12%">CGST(%)</th>
                <th style="text-align:center" width="12%">CGST Amount</th>
                <?php }else{ ?>
                <th style="text-align:center" width="12%">IGST(%)</th>
                <th style="text-align:center" width="12%">IGST Amount</th>
                <?php }?>
                <th style="text-align:center" width="15%">Total</th>
             
            </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$data_sql="select * from billing_product_items where challan_no='".$po_no."' ";
			$data_res=mysqli_query($link1,$data_sql);
			while($podata_row=mysqli_fetch_assoc($data_res)){
			?>
              <tr>
                <td><?=$i?></td>
                 <td><?=getAnyDetails($podata_row['partcode'],"part_name","partcode" ,"partcode_master",$link1);?></td>
                <td style="text-align:right"><?=$podata_row['hsn_code']?></td>
				  <td style="text-align:right"><?=$podata_row['old_challan']?></td>
                <td style="text-align:right"><?=$podata_row['qty']?></td>
                <td style="text-align:right"><?=$podata_row['price']?></td>
                <td style="text-align:right"><?=$podata_row['discount_amt']?></td>
                <td style="text-align:right"><?=$podata_row['price'] - $podata_row['discount_amt']*$podata_row['qty']?></td>
                <?php if($po_row['to_stateid']==$po_row['from_stateid']){ ?>
                <td style="text-align:right"><?=$podata_row['sgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['sgst_amt']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_per']?></td>
                <td style="text-align:right"><?=$podata_row['cgst_amt']?></td>
                <?php }else{ ?>
                <td style="text-align:right"><?=$podata_row['igst_per']?></td>
                <td style="text-align:right"><?=$podata_row['igst_amt']?></td>
                <?php }?>
                <td style="text-align:right"><?=$podata_row['item_total']?></td>
                   
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
                 <td><input type="text" name="tot_amt" id="tot_amt" class="number form-control required"   value="<?=$po_row['total_cost'];?>"  readonly/></td>
                   <td><label class="control-label">Receive Remark <span style="color:#F00">*</span></label></td>
                 <td><textarea  name="rcv_rmk" id="rcv_rmk" class="form-control required"  required /></textarea></td>
                   
                 </tr>
               <tr>
                 <td colspan="4" align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="CN GENERATE" title="CN GENERATE">&nbsp;
                   <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='cn_against_part.php?<?=$pagenav?>'">
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
