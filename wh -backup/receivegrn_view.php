<?php
require_once("../includes/config.php");
//////////////// decode challan number////////////////////////////////////////////////////////
$po_no = base64_decode($_REQUEST['refid']);
////////////////////////////////////////// fetching datta from table///////////////////////////////////////////////
$po_sql="select * from supplier_po_master where system_ref_no='".$po_no."'  and status!='4' ";
$po_res=mysqli_query($link1,$po_sql);
$po_row=mysqli_fetch_assoc($po_res);

$msg="";
///// after hitting receive button ///
if($_POST){
	if ($_POST['upd']=='Receive'){
		mysqli_autocommit($link1, false);
		$flag = true;
		$error_msg="";
		//// pick max count of grn
		$res_grncount = mysqli_query($link1,"SELECT grn_counter from invoice_counter where location_code='".$_POST['to_loc']."' ");
		$row_grncount = mysqli_fetch_assoc($res_grncount);
		///// make grn sequence
		$nextgrnno = $row_grncount['grn_counter'] + 1;
		$grnno = "GRN"."".$_POST['to_loc']."".str_pad($nextgrnno,4,0,STR_PAD_LEFT);
		//// first update the job count
		$upd = mysqli_query($link1,"UPDATE invoice_counter set grn_counter='".$nextgrnno."' where location_code='".$_POST['to_loc']."'");
		//// check if query is not executed
		if (!$upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
		
		/////////////////////////////// insert data into grn master  table///////////////////////////////////////////////
 	  	$grn_master="insert into grn_master set inv_no='".$_POST['inv_no']."',inv_date='".$_POST['inv_date']."',location_code ='".$_POST['to_loc']."', party_code='".$_POST['supplier']."' ,po_no='".$_POST['po_no']."', receive_date='".$today."' ,receive_time='".$time."', entry_date_time='".$datetime."' , status='4' , grn_no='".$grnno."', sub_total='".$_POST['sub_total']."', tax_total='".$_POST['tax_total']."' , cost='".$_POST['grand_total']."', inv_no_date='".$today."' , gate_entry_no='".$_POST['gate_entry_no1']."', remark='".$_POST['rcv_rmk']."',grn_type='GRN',comp_code='".$_SESSION['asc_code']."',update_by='".$_SESSION['userid']."',ip_address='".$_SERVER['REMOTE_ADDR']."',tran_name='".$_POST['tran_name']."',veh_no='".$_POST['veh_no']."',doc_no='".$_POST['doc_no']."' , total_igst_amt = '".$_POST['igst_total']."' , total_sgst_amt = '".$_POST['sgst_total']."' , total_cgst_amt = '".$_POST['cgst_total']."',po_date='".$po_row['entry_date']."'  ";
	$result=mysqli_query($link1,$grn_master);
		//// check if query is not executed
		if (!$result) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
		
		
		
		////// run data cycle of grn and get posted value of receive qty
		$tot_rcvqty=0;
		$sql_sp="select * from supplier_po_data where system_ref_no='".$_POST['po_no']."' and status!='4'";
		$res_grnData=mysqli_query($link1,$sql_sp);
		while($row_grnData=mysqli_fetch_assoc($res_grnData)){
		///// initialize posted variables
			$bill_qty="qty".$row_grnData['id'];
		  	$bill_shipqty="shippedqty".$row_grnData['id'];
		  	$bill_okqty="ok_qty".$row_grnData['id'];
		  	$bill_damageqty="damage_qty".$row_grnData['id'];
		  	$bill_missqty="miss_qty".$row_grnData['id'];
		  	//$bill_excessqty="excess".$row_grnData['id'];
		  	$partcode="partcode".$row_grnData['id'];
			$cgst_per = "cgst_per".$row_grnData['id'];
			$cgst_amt = "cgst_amt".$row_grnData['id'];
			$sgst_per = "sgst_per".$row_grnData['id'];
			$sgst_amt = "sgst_amt".$row_grnData['id'];
			$igst_per = "igst_per".$row_grnData['id'];
			$igst_amt = "igst_amt".$row_grnData['id'];
			$total_cost = "total_cost".$row_grnData['id'];
			if($_POST[$bill_qty]==$_POST[$bill_shipqty]){ 
				$each_status="4";
			}else{ 
				$each_status="9";
			}
            if($_POST[$bill_shipqty]!=""){
			//	$sub_total = ($_POST[$bill_shipqty] + $_POST[$bill_excessqty]) * $row_grnData['price'];
			$sub_total = ($_POST[$bill_shipqty]) * $row_grnData['price'];
			if($_POST[$cgst_per]!='' && $_POST[$cgst_per] > 0){
			$cgst_AMT=($sub_total*$_POST[$cgst_per])/100;
			$sgst_AMT=($sub_total*$_POST[$sgst_per])/100;
			$igst_AMT=0.00;
			}
			else {
			$cgst_AMT=0.00;
			$sgst_AMT=0.00;
			$igst_AMT=($sub_total*$_POST[$igst_per])/100;
			}
				$tax_total =$cgst_AMT+$sgst_AMT+$igst_AMT;
				$grand_total = $sub_total+$tax_total;
				///for master variable update
				$c_amt+=$cgst_AMT;
				$s_amt+=$sgst_AMT;
				$i_amt+=$igst_AMT;
				$grn_total+=$grand_total;
				$grn_sub+=$sub_total;
				
				
				
				//
		    	//// insert grn data 
			 	$req_ins2="insert into grn_data  set grn_no='".$grnno."' , product_id='".$row_grnData['product_id']."',brand_id='".$row_grnData['brand_id']."',model_id='".$row_grnData['model_id']."',partcode='".$_POST[$partcode]."', shipped_qty='".$_POST[$bill_shipqty]."',okqty='".$_POST[$bill_okqty]."',damage='".$_POST[$bill_damageqty]."',missing='".$_POST[$bill_missqty]."' , price='".$row_grnData['price']."',sub_total='".$sub_total."',tax_name='".$row_grnData['tax_name']."',tax_amt='".$tax_total."',amount='".$grand_total."' , type='PO' , cgst_per = '".$_POST[$cgst_per]."' , cgst_amt = '".$cgst_AMT."' , igst_per = '".$_POST[$igst_per]."' , igst_amt = '".$igst_AMT."' , sgst_per = '".$_POST[$sgst_per]."' , sgst_amt = '".$sgst_AMT."' ";
		       $req_res2=mysqli_query($link1,$req_ins2);
				//// check if query is not executed
				if (!$req_res2) {
					 $flag = false;
					 $error_msg = "Error details2: " . mysqli_error($link1) . ".";
				}	   			   
				//// update supplier PO data 
			$upd_spd = mysqli_query($link1,"update supplier_po_data set qty=qty-'".$_POST[$bill_shipqty]."',status='".$each_status."' where id='".$row_grnData['id']."'");
				//// check if query is not executed
				if (!$upd_spd) {
					 $flag = false;
					 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
				}
				//$okqty=$_POST[$bill_okqty]+$_POST[$bill_excessqty];		
				$okqty=$_POST[$bill_okqty];				
				if(mysqli_num_rows(mysqli_query($link1,"select partcode from client_inventory where partcode='".$_POST[$partcode]."' and location_code='".$_SESSION['asc_code']."'"))>0){
					$result=mysqli_query($link1,"update client_inventory set okqty=okqty+'".$okqty."',broken=broken+'".$_POST[$bill_damageqty]."',missing=missing+'".$_POST[$bill_missqty]."',updatedate='".$datetime."' where partcode='".$_POST[$partcode]."' and location_code='".$_SESSION['asc_code']."'");
				}
				else{
					//// if product is not exist then add in inventory
					$result=mysqli_query($link1,"insert into client_inventory set location_code='".$_SESSION['asc_code']."',partcode='".$_POST[$partcode]."',okqty='".$okqty."',broken='".$_POST[$bill_damageqty]."',missing='".$_POST[$bill_missqty]."',updatedate='".$datetime."'");
				}
				//// check if query is not executed
				if (!$result) {
					 $flag = false;
					 $error_msg = "Error details4: " . mysqli_error($link1) . ".";
				}
				if($okqty!=0 && $okqty!="" && $okqty!=0.00){
					$flag=stockLedger($grnno,$today,$_POST[$partcode],$_POST['supplier'],$_SESSION['asc_code'],"IN","OK","STOCK IN","Receive Against GRN",$okqty,$row_grnData['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
				}
				if($_POST[$bill_damageqty]!=0 && $_POST[$bill_damageqty]!="" && $_POST[$bill_damageqty]!=0.00){
					$flag=stockLedger($grnno,$today,$_POST[$partcode],$_POST['supplier'],$_SESSION['asc_code'],"IN","DAMAGE","STOCK IN","Receive Against GRN",$_POST[$bill_damageqty],$row_grnData['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
				}
				if($_POST[$bill_missqty]!=0 && $_POST[$bill_missqty]!="" && $_POST[$bill_missqty]!=0.00){
					$flag=stockLedger($grnno,$today,$_POST[$partcode],$_POST['supplier'],$_SESSION['asc_code'],"IN","MISSING","STOCK IN","Receive Against GRN",$_POST[$bill_missqty],$row_grnData['price'],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);
				}
				$tot_rcvqty+=$_POST[$bill_shipqty];
			}
		}//// close while loop
		
		$res_grn = mysqli_query($link1,"update grn_master set sub_total='".$grn_sub."', tax_total='".$tot_grn_tax."' , cost='".$grn_total."',total_igst_amt = '".$i_amt."' , total_sgst_amt = '".$s_amt."' , total_cgst_amt = '".$c_amt."' where inv_no='".$_POST['inv_no']."' and po_no='".$_POST['po_no']."' ");
		//// check if query is not executed
		if (!$res_grn) {
			 $flag = false;
			 $error_msg = "Error details_grn: " . mysqli_error($link1) . ".";
		}	
		
		if($tot_rcvqty==0){
			$flag = false;
			$error_msg = "Error details4.1: You have not entered any receive qty.";
		}
		////// insert in location account ledger
		$res_ac_ledger = mysqli_query($link1,"INSERT INTO location_account_ledger set location_code='".$_SESSION['asc_code']."',entry_date='".$today."',remark='".$_POST['rcv_rmk']."', transaction_type = 'GRN',transaction_no='".$grnno."',month_year='".date("m-Y")."',crdr='DR',amount='".$_POST['grand_total']."'");
		if(!$res_ac_ledger){
			$flag = false;
			$error_msg = "Error details8: " . mysqli_error($link1) . ".";
		}
		//// update gate entry no. details
		$res = mysqli_query($link1,"update gate_entry_detail set entry_status='4' where request_no='".$_REQUEST['gate_entry_no1']."'");
		//// check if query is not executed
		if (!$res) {
			 $flag = false;
			 $error_msg = "Error details5: " . mysqli_error($link1) . ".";
		}
		//// update po no. details
	 	$sel_pend_qty=mysqli_fetch_array(mysqli_query($link1,"select sum(qty) as pend_qty from supplier_po_data where system_ref_no='".$_POST['po_no']."'"));
	 	if($sel_pend_qty['pend_qty']==0){ $final_status="4";$chk_status="Y";}else{ $chk_status="";$final_status="10";}
		$res2 = mysqli_query($link1,"update supplier_po_master set status='".$final_status."',gate_entry_flag='".$chk_status."' where system_ref_no='".$_POST['po_no']."'");
		//// check if query is not executed
		if (!$res2) {
			 $flag = false;
			 $error_msg = "Error details6: " . mysqli_error($link1) . ".";
		}	
		////// insert in activity table////
	   $flag=dailyActivity($_SESSION['asc_code'],$grnno,"GRN","RECEIVE",$_SERVER['REMOTE_ADDR'],$link1,$flag);	
		///// check both master and data query are successfully executed
		if ($flag) {
        	mysqli_commit($link1);
			$msg="Successfully Stock  Received  for ".$grnno;
			$cflag="success";
			$cmsg="Success";
    	} else {
			mysqli_rollback($link1);
			$cflag="danger";
			$cmsg="Failed";
			$msg = "Request could not be processed. Please try again. ".$error_msg;
		}
		$asc_contact=getAnyDetails($_SESSION['asc_code'],"contactno1","location_code","location_master",$link1);
    	mysqli_close($link1);
		///// move to parent page
		//$sms_msg="Dear Partner. your consignment has been Received against PO no ".$_POST['po_no']."";
		header("location:receive_grn.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav."&smsmsg=".$sms_msg."&to=".$asc_contact."");
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
 <script src="../js/jquery.js"></script>
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
	var pendqty=0;
	var reqqty=0;
	var shipqty=0;
	var okqty=0;
	var damageqty=0;
	//// check pending qty
    if(document.getElementById("qty"+a).value==""){ pendqty=0; }else{ pendqty=parseInt(document.getElementById("qty"+a).value); }
	//// check requested qty
    <!--if(document.getElementById("req_qty"+a).value==""){ reqqty=0; }else{ reqqty=parseInt(document.getElementById("req_qty"+a).value);}-->
	//// entered shipped qty
    if(document.getElementById("shippedqty"+a).value==""){ shipqty=0; }else{ shipqty=parseInt(document.getElementById("shippedqty"+a).value);}
	//// check enter ok qty
    if(document.getElementById("ok_qty"+a).value==""){ okqty=0; }else{ okqty=parseInt(document.getElementById("ok_qty"+a).value); }
	//// check enter damage qty
    if(document.getElementById("damage_qty"+a).value==""){ damageqty=0; }else{ damageqty=parseInt(document.getElementById("damage_qty"+a).value); }
	///// check req. qty should not more than pending qty
	if(pendqty < shipqty){
		alert("Receive qty can not more than pending Qty!");
		document.getElementById("shippedqty"+a).value=pendqty;
		document.getElementById("ok_qty"+a).value=pendqty;
		document.getElementById("upd").disabled=true;
	}else{
	
	}
	//// entered shipped qty
    if(document.getElementById("shippedqty"+a).value==""){ shipqty=0; }else{ shipqty=parseInt(document.getElementById("shippedqty"+a).value);}
	if(shipqty < (okqty + damageqty)){
        alert("Ok Qty & Damage Qty can not more than requested Qty!");
		document.getElementById("ok_qty"+a).value=shipqty;
		document.getElementById("miss_qty"+a).value=0;
		document.getElementById("damage_qty"+a).value=0;
		//document.getElementById("ok_qty"+a).focus();
		document.getElementById("upd").disabled=true;
    }else{
		document.getElementById("miss_qty"+a).value=(shipqty - (okqty + damageqty));
		document.getElementById("upd").disabled=false;
	}
	calculateTotal();
}
///// calculate total amount //
function calculateTotal(){
	var maxrow = document.getElementById("maxcnt").value;
	var subtotal=0.00;
	var taxtotal=0.00;
	var grandtotal=0.00;
	var qtytotal=0;
	for(var i=1; i < maxrow; i++){
		if(document.getElementById("shippedqty"+i).value==""){ var shipqty=0; }else{ var shipqty=parseInt(document.getElementById("shippedqty"+i).value);}
		<!--if(document.getElementById("excess"+i).value==""){ var excessqty=0; }else{ var excessqty=parseInt(document.getElementById("excess"+i).value);}-->
	//	var totqty = shipqty + excessqty;
		var totqty = shipqty;
		qtytotal+= totqty;
		
		var calsub = parseFloat(totqty) * parseFloat(document.getElementById("price"+i).value);
		subtotal+=  calsub;
		
		var caltax = (calsub * parseFloat(document.getElementById("taxper"+i).value))/100;
		taxtotal+=  caltax;
		
		grandtotal+= calsub + caltax;
	}
	document.getElementById("tot_qty").value = qtytotal;
	document.getElementById("sub_total").value = subtotal;
	document.getElementById("tax_total").value = taxtotal;
	document.getElementById("grand_total").value = grandtotal;
}

$(document).ready(function () {
	$('#inv_date').datepicker({
		format: "yyyy-mm-dd",
		//startDate: "<?=$today?>",
        todayHighlight: true,
		autoclose: true
	});
	});
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script src="../js/jquery.js"></script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>

 <script type="text/javascript" src="../js/common_js.js"></script>

 <link href="../css/font-awesome.min.css" rel="stylesheet">

 <link href="../css/abc.css" rel="stylesheet">

 <script src="../js/bootstrap.min.js"></script>

 <link href="../css/abc2.css" rel="stylesheet">

 <link rel="stylesheet" href="../css/bootstrap.min.css">
 
  <link rel="stylesheet" href="../css/datepicker.css">

 <script src="../js/bootstrap-datepicker.js"></script>

</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
   <div class="<?=$screenwidth?> tab-pane fade in active">
      <h2 align="center"><i class="fa fa-ship"></i> Receive GRN View</h2><br/>
   <div class="panel-group">
   <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading">GATE Entry Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Supplier Name:</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["party_name"],"name","id","vendor_master",$link1)."(".$po_row['party_name'].")";?><input name="supplier" id="supplier" type="hidden" value="<?=$po_row['party_name']?>"/></td>
                <td width="20%"><label class="control-label">Location</label></td>
                <td width="30%"><?php echo getAnyDetails($po_row["location_code"],"locationname","location_code","location_master",$link1)."(".$po_row['location_code'].")";?></td>
              </tr>
             
              <tr>
                <td><label class="control-label">PO No.</label></td>
                <td><?php echo $po_row['system_ref_no'];?><input name="po_no" id="po_no" type="hidden" value="<?=$po_row['system_ref_no']?>"/></td>
                <td><label class="control-label">PO Date</label></td>
                <td><?php echo dt_format($po_row['entry_date']);?></td>
              </tr>  
			  <tr>
                <td><label class="control-label">Status</label></td>
                <td><?php  echo getdispatchstatus($po_row["status"])?></td>
                <td><label class="control-label"></label></td>
                <td></td>
              </tr>       
			   <tr>
                <td><label class="control-label">Type.</label></td>
                <td><?php echo $po_row['voucher_type'];?></td>
                <td><label class="control-label">Document Type.</label></td>
                <td><?=$po_row['ship_type']?></td>
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
            <tr class="<?=$tableheadcolor?>">
              <td>S.No</td>
              <td>Partcode</td>
              <td>Req. Qty</td>
			  <td>Price</td>
              <td>SubTotal</td>
              <td>Tax</td>
			  <?php if($po_row['total_igst_amt'] == '0.00'){?>
              <td>CGST %</td>
			  <td>CGST Amt</td>
			  <td>SGST %</td>
			  <td>SGST Amt</td>
			  <?php } else {?>
			  <td>IGST %</td>
			  <td>IGST Amt</td>
			  <?php } ?>
              <td>Total Amt</td>
              <td>Pending Qty</td>
              <td>Receive Qty</td>
              <td>OK</td>
              <td>Damaged</td>
              <td>Missing</td>
            </tr>
            </thead>
            <tbody>
            <?php
			$i=1;
			$data_sql="select * from supplier_po_data where system_ref_no='".$po_no."' and status!='4' ";
			$data_res=mysqli_query($link1,$data_sql);
			while($data_row=mysqli_fetch_assoc($data_res)){
			?>
              <tr>
              <td><?=$i?></td>
              <td><?=getAnyDetails($data_row['partcode'],"part_name","partcode" ,"partcode_master",$link1);?><input type="hidden" name="partcode<?=$data_row['id']?>" id="partcode<?=$i?>" value="<?=$data_row['partcode']?>"></td>
              <td><?=$data_row['req_qty'];?><?php /*?><input type="hidden" name="req_qty<?=$data_row['id']?>" id="req_qty<?=$i?>" value="<?=$data_row['req_qty']?>"><?php */?></td>
              <td><?=$data_row['price'];?><input name="price<?=$data_row['id']?>" id="price<?=$i?>" type="hidden" value="<?=$data_row['price']?>"/></td>
              <td><?=$data_row['cost'];?></td> 
              <td><?=$data_row['tax_name']." ".$data_row['item_tax'];?><input name="taxper<?=$data_row['id']?>" id="taxper<?=$i?>" type="hidden" value="<?=$data_row['cgst_per']+$data_row['sgs_per']+$data_row['igst_per']?>"/></td>
			  <?php if($po_row['total_igst_amt'] == '0.00') {?>
               <td><?=$data_row['cgst_per'];?>%<input name="cgst_per<?=$data_row['id']?>" id="cgst_per<?=$i?>" type="hidden" value="<?=$data_row['cgst_per']?>"/></td>
			   <td><?=$data_row['cgst_amt'];?><input name="cgst_amt<?=$data_row['id']?>" id="cgst_amt<?=$i?>" type="hidden" value="<?=$data_row['cgst_amt']?>"/></td>
			    <td><?=$data_row['sgst_per'];?>%<input name="sgst_per<?=$data_row['id']?>" id="sgst_per<?=$i?>" type="hidden" value="<?=$data_row['sgst_per']?>"/></td>
			   <td><?=$data_row['sgst_amt'];?><input name="sgst_amt<?=$data_row['id']?>" id="sgst_amt<?=$i?>" type="hidden" value="<?=$data_row['sgst_amt']?>"/></td>
			   <?php } else {?>
			    <td><?=$data_row['igst_per'];?>%<input name="igst_per<?=$data_row['id']?>" id="igst_per<?=$i?>" type="hidden" value="<?=$data_row['igst_per']?>"/></td>
			   <td><?=$data_row['igst_amt'];?><input name="igst_amt<?=$data_row['id']?>" id="igst_amt<?=$i?>" type="hidden" value="<?=$data_row['igst_amt']?>"/></td>
			   <?php }?>
              <td><?=$data_row['total_cost'];?></td><input name="total_cost<?=$data_row['id']?>" id="total_cost<?=$i?>" type="hidden" value="<?=$data_row['total_cost']?>"/>
              <td><?=$data_row['qty'];?><input name="qty<?=$data_row['id']?>" id="qty<?=$i?>" type="hidden" value="<?=$data_row['qty']?>"/></td>
              <td align="center"><input name="shippedqty<?=$data_row['id']?>" id="shippedqty<?=$i?>" type="text" size="3" class="required digits form-control" required onKeyUp="checkRecQty('<?=$i?>');" value="<?=$data_row['qty'];?>"/></td>
              <td style="text-align:right"><input type="text" class="digits form-control" style="width:50px;" name="ok_qty<?=$data_row['id']?>" id="ok_qty<?=$i?>"  autocomplete="off" required onKeyUp="checkRecQty('<?=$i?>');" value="<?=$data_row['qty'];?>"></td>
              <td style="text-align:right"><input type="text" class="digits form-control" style="width:50px;" name="damage_qty<?=$data_row['id']?>" id="damage_qty<?=$i?>"  autocomplete="off" required onKeyUp="checkRecQty('<?=$i?>');" value="0"></td>
                <td style="text-align:right"><input type="text" class="digits form-control" style="width:50px;background-color:#CCCCCC" name="miss_qty<?=$data_row['id']?>" id="miss_qty<?=$i?>"  autocomplete="off" value="0" readonly></td>
             <!-- <td><input name="excess<?=$data_row['id']?>" id="excess<?=$i?>"  class="digits form-control" type="text"  size="3" value="0" onKeyUp="checkRecQty('<?=$i?>');"/></td>-->
            </tr>
            <?php
			$total_qty+= $data_row['req_qty'];
			$sub_total+= $data_row['cost'];
			$tax_total+= $data_row['tax_cost'];
			$grand_total+= $data_row['total_cost'];
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
                 <td width="25%"><label class="control-label">Total Qty</label></td>
                 <td width="25%"><input type="text" name="tot_qty" id="tot_qty" class="form-control" value="<?=$total_qty;?>" style="width:150px;text-align:right" readonly/><input name="maxcnt" id="maxcnt" type="hidden" value="<?=$i?>"/></td>
                 <td width="25%"><label class="control-label">Sub Total</label></td>
                 <td width="25%"><input type="text" name="sub_total" id="sub_total" class="form-control" value="<?=$sub_total?>" style="width:150px;text-align:right" readonly/></td>
               </tr>
               <tr>
                 <td><label class="control-label">Tax Total</label></td>
                 <td><input type="text" name="tax_total" id="tax_total" class="form-control" value="<?=$tax_total;?>" style="width:150px;text-align:right" readonly/></td>
                 <td><label class="control-label">Grand Total</label></td>
                 <td><input type="text" name="grand_total" id="grand_total" class="form-control" value="<?=$grand_total;?>" style="width:150px;text-align:right" readonly/></td>
               </tr>
			   
			      <tr>
                 <td><label class="control-label"> Transport name</label></td>
                 <td><input type="text" name="tran_name" id="tran_name" class="form-control" style="width:150px;text-align:right" /></td>
                 <td><label class="control-label">Billty/Docket number</label></td>
                 <td><input type="text" name="doc_no" id="doc_no" class="form-control" style="width:150px;text-align:right" /></td>
               </tr>
                  <tr>
                 <td><label class="control-label"> Invoice No <span style="color:#F00">*</span></label></label></td>
                 <td><input type="text" name="inv_no" id="inv_no" class="form-control required" style="width:150px;text-align:right" required /></td>
                 <td><label class="control-label">Invoice Date</label> <span style="color:#F00">*</span></td>
                 <td><input type="text" class="form-control required" name="inv_date"  id="inv_date" style="width:150px;"><i class="fa fa-calendar fa-lg"></i></td>
               </tr>
			       <tr>
                 <td><label class="control-label"> Vehicle number</label></td>
                 <td><input type="text" name="veh_no" id="veh_no" class="form-control" style="width:150px;text-align:right" /></td>
                 <td><label class="control-label">&nbsp;</label></td>
                 <td>&nbsp;</td>
               </tr>
			   
               <tr>
			   <td><label class="control-label">Receive Remark <span style="color:#F00">*</span></label></td>
                 <td colspan="3">
                   <textarea  name="rcv_rmk" id="rcv_rmk"  class=" form-control required" style="width:500px; resize:vertical"  required /></textarea></td>
                  </tr>
               <tr>
                 <td colspan="4" align="center">
                <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Receive" title="Receive">&nbsp;
              <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='receive_grn.php?<?=$pagenav?>'">
                     <input type="hidden" id="gate_entry_no1" name="gate_entry_no1" value="<?=base64_decode($_REQUEST['gate_entry_no'])?>">
					 <input type="hidden" id="cgst_total" name="cgst_total" value="<?=$po_row['total_cgst_amt']?>">
					 <input type="hidden" id="sgst_total" name="sgst_total" value="<?=$po_row['total_sgst_amt']?>">
					 <input type="hidden" id="igst_total" name="igst_total" value="<?=$po_row['total_igst_amt']?>">
					 <input name="to_loc" id="to_loc" type="hidden" value="<?=$po_row['location_code']?>"/>
					
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