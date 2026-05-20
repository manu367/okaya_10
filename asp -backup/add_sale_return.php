<?php
require_once("../includes/config.php");
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
$docid = base64_decode($_REQUEST['id']);
$toloctiondet = explode("~", getAnyDetails($_REQUEST['bill_to'], "stateid","location_code","location_master", $link1));
@extract($_POST);

////// if we hit process button
if ($_POST){
	if ($_POST['upd'] == 'Process') {
		#######################################################
		$messageIdent_add_sale_ret = md5($_SESSION['asc_code'] . $docid . $_POST['upd']);
		//and check it against the stored value:
		$sessionMessageIdent_add_sale_ret = isset($_SESSION['messageIdent_add_sale_ret'])?$_SESSION['messageIdent_add_sale_ret']:'';
		if($messageIdent_add_sale_ret!=$sessionMessageIdent_add_sale_ret){//if its different:          
			//save the session var:
			$_SESSION['messageIdent_add_sale_ret'] = $messageIdent_add_sale_ret; 
			#####################################################################
			if($_POST['bill_typ']=="INV"){
				////////////     INV challan part start here 
				if ( ($parentcode != "")) {
					mysqli_autocommit($link1, false);
					$flag = true;
					$err_msg = "";
					if ($tot_qty>0) {
						//// Make System generated Invoice no.//////
						
						//echo "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'"."<br><br>";
						
						$res_invcount = mysqli_query($link1, "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'");
						
						if (mysqli_num_rows($res_invcount)) {
							//////pick max counter of INVOICE
							$row_invcount = mysqli_fetch_array($res_invcount);
							$next_invno = $row_invcount['inv_counter']+1;
							/////update next counter against invoice
							
							//echo "UPDATE invoice_counter set inv_counter = '".$next_invno."' where location_code='".$_SESSION['asc_code']."'"."<br><br>";
							
							$res_upd = mysqli_query($link1,"UPDATE invoice_counter set inv_counter = '".$next_invno."' where location_code='".$_SESSION['asc_code']."'");
							/// check if query is execute or not//
							if(!$res_upd){
								$flag = false;
								$err_msg = "Error1". mysqli_error($link1) . ".";
							}
							
							///// make invoice no.
							$invno = $row_invcount['inv_series']."".$row_invcount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);
							
							$fromlocdet = explode("~",getAnyDetails($_SESSION['asc_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
							$toloctiondet = explode("~",getAnyDetails($parentcode,"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
							
							///// Insert Master Data
							$query1 = "INSERT INTO billing_master set from_location='".$_SESSION['asc_code']."', to_location='".$parentcode."',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$toloctiondet[8]."',party_name='".$_SESSION['asc_code']."', challan_no='".$invno."', sale_date='".$today."', entry_date='".$today."', entry_time='".$currtime."', logged_by='".$_SESSION['userid']."', document_type='INV' ,basic_cost='".$sub_total."',total_cost='".$grand_total."',bill_from='".$_SESSION['asc_code']."',from_stateid='".$fromlocdet['5']."',to_stateid='".$toloctiondet[5]."',bill_to='".$parentcode."',from_addrs='".$fromlocdet[1]."',disp_addrs='".$fromlocdet[2]."',round_off='".$round_off."',to_addrs='".$toloctiondet[1]."',billing_rmk='".$remark."', status='2', dc_date='".$today."',dc_time='".$currtime."',sgst_amt='".$totsgstamt."',cgst_amt='".$totcgstamt."',igst_amt='".$totigstamt."',po_type='Sale Return'";		
							
							//echo $query1."<br><br>";
							
							$result = mysqli_query($link1, $query1)or die("ER1" . mysqli_error($link1));
							//// check if query is not executed
							if (!$result) {
								$flag = false;
								$err_msg = "Error Code1:";
							}
							///// Insert in item data by picking each data row one by one
							$arr_product = array();
							$arr_brand = array();
							$arr_model = array();
							$arr_prodcode = array();
							$arr_qty = array();
							$arr_price = array();
							$arr_hsncode = array();
							$arr_holdprice = array();
							$arr_linetotal = array();
							$arr_tax = array();
							$arr_taxamt = array();
							$arr_discount = array();
							$arr_totalval = array();
							foreach ($partcode as $k => $val) {
								// checking row value of product and qty should not be blank
								if ($partcode[$k] != '' && $bill_qty[$k] != '' && $bill_qty[$k] != 0) {              
									$arr_product[] = $prod_code[$k];
									$arr_brand[] = $brand[$k];
									$arr_model[] = $model[$k];
									$arr_prodcode[] = $partcode[$k];
									$arr_qty[] = $bill_qty[$k];
									$arr_price[] = $price[$k];
									$arr_cost[] = $cost[$k];
									$arr_hsncode[] = $hsn_code[$k];
									$arr_holdprice[] = $holdRate[$k];
									$arr_linetotal[] = $linetotal[$k];
									$arr_tax[] = $taxType[$k];
									$arr_taxamt[] = $rowtaxamount[$k];
									$arr_discount[] = $rowdiscount[$k];
									$arr_totalval[] = $total_val[$k];
								}// close if loop of checking row value of product and qty should not be blank
							}/// close for loop
							
							///// apply logic to insert data in data table//
							$uniq_prod = array_unique($arr_prodcode);
							foreach ($uniq_prod as $key => $value) {
								//// find all key of every product in main array
								$keyarr = array_keys($arr_prodcode, $value);
								// checking row value of product and qty should not be blank
								$getstk = getInventory($_SESSION['asc_code'],$partcode[$key],"okqty",$link1);
								//// check stock should be available ////
								if ($getstk =='' || $getstk < $bill_qty[$key]) {
									$flag = false;
									$err_msg = "Error Code3: Stock is not available: ".mysqli_error($link1);
								} else {
								}
								
								/////////// insert data
								$partname	 = getAnyDetails($partcode[$key],"part_name" ,"partcode","partcode_master" ,$link1);
								
								$query2 = "INSERT INTO billing_product_items set from_location='".$_SESSION['asc_code']."', to_location='".$parentcode."',challan_no='".$invno."', partcode='".$partcode[$key]."', product_id='".$prod_code[$key]."', brand_id='".$brand[$key]."', model_id='".$model[$key]."',part_name='".$partname."', qty='".$bill_qty[$key]."', price='".$price[$key]."',uom='PCS', mrp='', value='".$cost[$key]."', item_total='".$total_val[$key]."', pty_receive_date='".$today."', sgst_per='".$rowsgstper[$key]."',sgst_amt='".$rowsgstamount[$key]. "',cgst_per='".$rowcgstper[$key]."',cgst_amt='".$rowCgstamount[$key]."',igst_per='".$rowigstper[$key]."',igst_amt='".$rowigstamount[$key]. "',type = 'Sale Return' ";

								//echo $query2."<br><br>";
								
								$result2 = mysqli_query($link1, $query2);
								//// check if query is not executed
								if (!$result2) {
									$flag = false;
									$err_msg = "Error Code4: ".mysqli_error($link1);
								}
								
								//// update stock of from loaction
								
								//echo "UPDATE client_inventory set okqty = okqty-'" . $bill_qty[$key] . "',updatedate='" . $datetime . "' where location_code='" . $_SESSION['asc_code']. "' and partcode='" . $value . "' and  okqty >= '".$bill_qty[$key]."'"."<br><br>";
								
								$result3 = mysqli_query($link1, "UPDATE client_inventory set okqty = okqty-'" . $bill_qty[$key] . "',updatedate='" . $datetime . "' where location_code='" . $_SESSION['asc_code']. "' and partcode='" . $value . "' and  okqty >= '".$bill_qty[$key]."'");
								//// check if query is not executed
								if (!$result3) {
									$flag = false;
									$err_msg = "Error Code5: ".mysqli_error($link1);
								}
								
								///// update stock ledger table
								$flag = stockLedger($invno, $today, $value, $_SESSION['asc_code'], $parentcode, "OUT", "OK", "Sale Return","Process", $bill_qty[$key], $cost[$key], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
							}
							
							////// insert in activity table////
							$flag = dailyActivity($_SESSION['userid'], $invno, "Sale Return", "Process", $ip, $link1, $flag);
							///// check both master and data query are successfully executed
							if ($flag) {
								mysqli_commit($link1);
								$msg = "Sale Return is successfully done with ref. no. " . $invno;
								$cflag = "success";
								$cmsg = "Success";
							} else {
								mysqli_rollback($link1);
								$msg = "Request could not be processed " . $err_msg . ". Please try again.";
								$cflag = "danger";
								$cmsg = "Failed";
							}
							mysqli_close($link1);
						} else {
							$msg = "Request could not be processed invoice series not found. Please try again.";
							$cflag = "danger";
							$cmsg = "Failed";
						}
					} else {
						$msg = "Request could not be processed . Please dispatch some qty.";
						$cflag = "danger";
						$cmsg = "Failed";
					}
				} else {
					$msg = "Request could not be processed . Please select  Bill To .";
					$cflag = "danger";
					$cmsg = "Failed";
				}
			}else{
				////////////     DC challan part start here 
				if ( ($parentcode != "")) {
					mysqli_autocommit($link1, false);
					$flag = true;
					$err_msg = "";
					if ($tot_qty>0) {
						//// Make System generated Invoice no.//////
						
						//echo "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'"."<br><br>";
						
						$res_invcount = mysqli_query($link1, "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'");
						if (mysqli_num_rows($res_invcount)) {
							//////pick max counter of Challan
							$row_invcount = mysqli_fetch_array($res_invcount);
							$next_invno = $row_invcount['stn_counter']+1;
							
							/////update next counter against invoice
							
							//echo "UPDATE invoice_counter set stn_counter = '".$next_invno."' where location_code='".$_SESSION['asc_code']."'"."<br><br>";
							
							$res_upd = mysqli_query($link1,"UPDATE invoice_counter set stn_counter = '".$next_invno."' where location_code='".$_SESSION['asc_code']."'");
							/// check if query is execute or not//
							if(!$res_upd){
								$flag = false;
								$err_msg = "Error1". mysqli_error($link1) . ".";
							}
							
							///// make invoice no.
							$invno = $row_invcount['stn_series']."".$row_invcount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);
							
							$fromlocdet = explode("~",getAnyDetails($_SESSION['asc_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
							$toloctiondet = explode("~",getAnyDetails($parentcode,"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
							
							///// Insert Master Data
							$query1 = "INSERT INTO billing_master set from_location='" .$_SESSION['asc_code'] . "', to_location='" .$parentcode . "',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$toloctiondet[8]."',
							party_name='".$_SESSION['asc_code']."', challan_no='".$invno."', sale_date='".$today."', entry_date='".$today."', entry_time='".$currtime."', logged_by='".$_SESSION['userid']."', document_type='DC' ,basic_cost='".$sub_total."',total_cost='".$grand_total."',bill_from='".$_SESSION['asc_code']."',from_stateid='".$fromlocdet['5']."',to_stateid='".$toloctiondet[5]."',bill_to='".$parentcode."',from_addrs='".$fromlocdet[1]."',disp_addrs='".$fromlocdet[2] . "',round_off='".$round_off."',to_addrs='".$toloctiondet[1]."',billing_rmk='".$remark."', status='2', dc_date='".$today."',dc_time='".$currtime."',sgst_amt='".$totsgstamt."',cgst_amt='".$totcgstamt."',igst_amt='". $totigstamt."',po_type='Sale Return'";	
							
							//echo $query1."<br><br>";
							
							$result = mysqli_query($link1, $query1)or die("ER1" . mysqli_error($link1));
							//// check if query is not executed
							if (!$result) {
								$flag = false;
								$err_msg = "Error Code1:";
							}
							///// Insert in item data by picking each data row one by one
							$arr_product = array();
							$arr_brand = array();
							$arr_model = array();
							$arr_prodcode = array();
							$arr_qty = array();
							$arr_price = array();
							$arr_hsncode = array();
							$arr_holdprice = array();
							$arr_linetotal = array();
							$arr_tax = array();
							$arr_taxamt = array();
							$arr_discount = array();
							$arr_totalval = array();
							foreach ($partcode as $k => $val) {
								// checking row value of product and qty should not be blank
								if ($partcode[$k] != '' && $bill_qty[$k] != '' && $bill_qty[$k] != 0) {              
									$arr_product[] = $prod_code[$k];
									$arr_brand[] = $brand[$k];
									$arr_model[] = $model[$k];
									$arr_prodcode[] = $partcode[$k];
									$arr_qty[] = $bill_qty[$k];
									$arr_price[] = $price[$k];
									$arr_hsncode[] = $hsn_code[$k];
									$arr_holdprice[] = $holdRate[$k];
									$arr_linetotal[] = $linetotal[$k];
									$arr_tax[] = $taxType[$k];
									$arr_taxamt[] = $rowtaxamount[$k];
									$arr_discount[] = $rowdiscount[$k];
									$arr_totalval[] = $total_val[$k];
								}// close if loop of checking row value of product and qty should not be blank
							}/// close for loop
							
							///// apply logic to insert data in data table//
							$uniq_prod = array_unique($arr_prodcode);
							foreach ($uniq_prod as $key => $value) {
								// checking row value of product and qty should not be blank
								$getstk = getInventory($_SESSION['asc_code'],$value,"okqty",$link1);
								//// check stock should be available ////
								if ($getstk =='' || $getstk < $bill_qty[$key]) {
									$flag = false;
									$err_msg = "Error Code3: Stock is not available: ".mysqli_error($link1);
								} else {
								}
								
								/////////// insert data
								$partname = getAnyDetails("$value","part_name" ,"partcode","partcode_master" ,$link1);
								$query2 = "INSERT INTO billing_product_items set from_location='".$_SESSION['asc_code']."', to_location='".$parentcode."',challan_no='".$invno."',  partcode='".$value."', product_id='".$prod_code[$key]."', brand_id='".$brand[$key]."', model_id='".$model[$key]."',part_name='".$partname."', qty='".$bill_qty[$key]."', price='".$price[$key]."',uom='PCS', mrp='', value='".$cost[$key]."', item_total='".$total_val[$key]."', pty_receive_date='".$today."', sgst_per='".$rowsgstper[$key]."',sgst_amt='".$rowsgstamount[$key]. "',cgst_per='".$rowcgstper[$key]."',cgst_amt='".$rowCgstamount[$key]."',igst_per='".$rowigstper[$key]."',igst_amt='".$rowigstamount[$key]. "',type = 'Stock Transfer' ";		

								//echo $query2."<br><br>";
								
								$result2 = mysqli_query($link1, $query2);
								
								//// check if query is not executed
								if(!$result2){
									$flag = false;
									$err_msg = "Error Code4: ".mysqli_error($link1);
								}
								//// update stock of from loaction
								
								//echo "UPDATE client_inventory set okqty = okqty-'".$bill_qty[$key]."',updatedate='" . $datetime . "' where location_code='" . $_SESSION['asc_code']. "' and partcode='" . $value . "' and  okqty >= '".$bill_qty[$key]."'"."<br><br>";
								
								$result3 = mysqli_query($link1, "UPDATE client_inventory set okqty = okqty-'".$bill_qty[$key]."',updatedate='" . $datetime . "' where location_code='" . $_SESSION['asc_code']. "' and partcode='" . $value . "' and  okqty >= '".$bill_qty[$key]."'");
								//// check if query is not executed
								if(!$result3){
									$flag = false;
									$err_msg = "Error Code5: ".mysqli_error($link1);
								}
								///// update stock ledger table
								$flag = stockLedger($invno, $today, $value, $_SESSION['asc_code'], $parentcode, "OUT", "OK", "Sale Return","Process", $bill_qty[$key], $cost[$key], $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
							}
							////// insert in activity table////
							$flag = dailyActivity($_SESSION['userid'], $invno, "Stock Transfer", "Process", $ip, $link1, $flag);
							///// check both master and data query are successfully executed
							if ($flag) {
								mysqli_commit($link1);
								$msg = "Sale Return is successfully done with ref. no. " . $invno;
								$cflag = "success";
								$cmsg = "Success";
							} else {
								mysqli_rollback($link1);
								$msg = "Request could not be processed " . $err_msg . ". Please try again.";
								$cflag = "danger";
								$cmsg = "Failed";
							}
							mysqli_close($link1);
						}  else {
							$msg = "Request could not be processed invoice series not found. Please try again.";
							$cflag = "danger";
							$cmsg = "Failed";
						}
					} else {
						$msg = "Request could not be processed . Please dispatch some qty.";
						$cflag = "danger";
						$cmsg = "Failed";
					}
				} else {
					$msg = "Request could not be processed . Please select  Bill To .";
					$cflag = "danger";
					$cmsg = "Failed";
				}
			}	
			####################################################
		} else {
			//you've sent this already!
			$msg="Refresh or Re-Submittion is not Allowed.";
			$cflag="danger";
			$cmsg="Failed";
		}
		#######################################################
		///// move to parent page
		header("location:asp_sale_return.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
	}
}
?>

<?php
	///////////////////////// script for multi brand /////////////////////////////////////////////
	$wh_access_brand = getAccessBrand($_REQUEST['bill_to'],$link1);  
  
	if($wh_access_brand!=""){
		$brand_string = "  and brand_id in ($wh_access_brand)  ";
	}else{
		$brand_string = "";
	}
					  
 ?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= siteTitle ?></title>
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script type="text/javascript">
$(document).ready(function() {
$("#frm2").validate();
$("#frm1").validate();
});
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script>
//////////////////////// function to get model on basis of model dropdown selection///////////////////////////
function getmodel(indx){
var brandid=document.getElementById("brand["+indx+"]").value;
var productCode=document.getElementById("prod_code["+indx+"]").value;
$.ajax({
type:'post',
url:'../includes/getAzaxFields.php',
data:{brandinfo:brandid,productinfo:productCode,indxx:indx},
success:function(data){
var getValue = data.split("~");
document.getElementById("modeldiv"+getValue[1]).innerHTML=getValue[0];
}
});
}
function getpartcode(indx){
	var part_det="";
	var model=document.getElementById("model["+indx+"]").value;
	var totrows=document.getElementById('rowno').value;
	var opr_typ="SRN";
	if(indx>0){
		for(var i=0; i<=totrows; i++){
			var part_desc_val=document.getElementById("partcode["+i+"]").value;
			var part_desc="'"+part_desc_val+"'";
			if(part_det==""){
				part_det=part_desc;
			}else{
				part_det = part_det.concat(",",part_desc);
			}
		}
	}
	$.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{modelinfostn:model,indxx:indx,part_inf:part_det,oprtyp:opr_typ},
		success:function(data){
			var getValue = data.split("~");
			//alert(getValue);
			document.getElementById("partcodediv"+getValue[1]).innerHTML=getValue[0];
		}
	});
}

$(document).ready(function() {
$("#add_row").click(function() {
var numi = document.getElementById('rowno');
var itm = "partcode[" + numi.value + "]";
var qTy = "bill_qty[" + numi.value + "]";
var preno = document.getElementById('rowno').value;
var num = (document.getElementById("rowno").value - 1) + 2;
if ((document.getElementById(itm).value != "" && document.getElementById(qTy).value != "" && document.getElementById(qTy).value != "0") || ($("#addr" + numi.value + ":visible").length == 0)) {
numi.value = num;
var r = '<tr id="addr' + num + '"><td><select name="prod_code[' + num + ']" id="prod_code[' + num + ']" class="form-control required" required><option value="">Select</option><?php $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['product_id'];?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option><?php }?></select></td><td><select name="brand[' + num + ']" id="brand[' + num + ']" class="form-control required" onChange="getmodel(' + num + ')" required><option value="">Select</option><?php $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.")  ".$brand_string."  order by brand";$check_dept=mysqli_query($link1,$dept_query);while($br_dept = mysqli_fetch_array($check_dept)){?><option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option><?php }?></select></td><td><span id="modeldiv' + num + '"><select name="model[' + num + ']" id="model[' + num + ']" class="form-control required"  onChange="getpartcode(' + num + ')" required><option value="" selected="selected"> Select Model</option></select></span></td><td><span id="partcodediv' + num + '"><select name="partcode[' + num + ']" id="partcode[' + num + ']" class="form-control required"  onChange="getAvlStk(' + num + ')" required ><option value="" selected="selected"> Select Partcode</option></select></span></td><td><input type="text" class="number form-control" name="price[' + num + ']" id="price[' + num + ']" onblur="rowTotal(' + num + ');" autocomplete="off" required style="width:71px;text-align:right;padding: 4px"></td><td><input type="text" class="number form-control" name="bill_qty[' + num + ']" id="bill_qty[' + num + ']" onBlur="rowTotal(' + num + ');" autocomplete="off" required style="width:71px;text-align:right;padding: 4px"><input type="hidden" name="avl_stock[' + num + ']" id="avl_stock[' + num + ']" value=""></td> <td><input type="text" class="number form-control" name="cost[' + num + ']" id="cost[' + num + ']"  autocomplete="off" readonly required style="width:71px;text-align:right;padding: 4px"></td> <?php if($_REQUEST['transfer_type']== 'INV'){if($toloctiondet[0] == $_SESSION['stateid']){?><td><div style="display:inline-block; float:left"><input type="text" class="form-control" name="rowsgstper[' + num + ']" id="rowsgstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div style="display:inline-block; float:right">%</div><br/><input type="text" class="form-control" name="rowsgstamount[' + num + ']" id="rowsgstamount[' + num + ']"  readonly style="width:60px;text-align:right;padding:4px"></td><td><div style="display:inline-block; float:left"><input type="text" class="form-control" name="rowcgstper[' + num + ']" id="rowcgstper[' + num + ']"  readonly style="width:50px;text-align:right;padding: 4px"></div><div style="display:inline-block; float:right">%</div><br/><input type="text" class="form-control" name="rowcgstamount[' + num + ']" id="rowcgstamount[' + num + ']"  readonly style="width:60px;text-align:right;padding: 4px"></td><?php }else{?><td><input type="text" class="form-control" name="rowigstper[' + num + ']" id="rowigstper[' + num + ']"  readonly style="width:50px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowigstamount[' + num + ']" id="rowigstamount[' + num + ']"  readonly style="width:60px;text-align:right;padding: 4px"></td><?php }}?><td><div style="display:inline-block;float:left"><input type="text" class="form-control" name="total_val[' + num + ']" id="total_val[' + num + ']" autocomplete="off" readonly style="width:80px;text-align:right;padding: 4px"></div></td></tr>';
$('#itemsTable1').append(r);
}
});
});

<!------  close button <div style="display:inline-block;float:right;padding: 4px"><i class="fa fa-close fa-lg" onClick="fun_remove('+num+');"></i></div>  -------->

function fun_remove(con){
var c = document.getElementById('addr' + con);
c.parentNode.removeChild(c);
con--;
document.getElementById('rowno').value = con;
rowTotal(con);
}
/////////// function to get available stock of ho
function getAvlStk(indx){
	var productCode=document.getElementById("partcode["+indx+"]").value;
	var locCode=document.getElementById("asc_code").value;
	var dc_ty=document.getElementById("transfer_type").value;
	var stocktype="okqty";
	$.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{locstk:productCode,stktype:stocktype,indxx:indx,location:locCode},
		success:function(data){
			var getdata=data.split("~");
			if(getdata[0]!=""){
				document.getElementById("avl_stock["+getdata[1]+"]").value=getdata[0];
			}
			else
			{
				document.getElementById("avl_stock["+getdata[1]+"]").value="0";
			}
		}
	});
	////// get part price and tax details
	$.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{partpricetax:productCode},
		success:function(data){
			var getpartdet=data.split("~");
			
			if(getpartdet[5]){
				document.getElementById("price["+indx+"]").value=getpartdet[5];
				if(document.getElementById("bill_typ").value!="DC"){ 
					<?php if($toloctiondet[0] == $_SESSION['stateid']){?>
					document.getElementById("rowsgstper["+indx+"]").value=getpartdet[3];
					document.getElementById("rowcgstper["+indx+"]").value=getpartdet[4];
					<?php }else{?>
					document.getElementById("rowigstper["+indx+"]").value=getpartdet[2];
					<?php } ?>
				}else{
					//document.getElementById("price["+indx+"]").value=0.00;
					<?php if($toloctiondet[0] == $_SESSION['stateid']){?>
					document.getElementById("rowsgstper["+indx+"]").value=0.00;
					document.getElementById("rowcgstper["+indx+"]").value=0.00;
					<?php }else{?>
					document.getElementById("rowigstper["+indx+"]").value=0.00;
					<?php } ?>
				}
			}
			else{	
				document.getElementById("price["+indx+"]").value=0.00;	
				<?php if($toloctiondet[0] == $_SESSION['stateid']){?>
				document.getElementById("rowsgstper["+indx+"]").value=0.00;
				document.getElementById("rowcgstper["+indx+"]").value=0.00;
				<?php }else{?>
				document.getElementById("rowigstper["+indx+"]").value=0.00;
				<?php } ?>
			}
		}
	});
}
///////////////////////////
/////// calculate line total /////////////
function rowTotal(ind) {
var ent_qty = "bill_qty"+"[" + ind + "]";
var cost = "cost"+"[" + ind + "]";
var ent_rate = "price" + "[" + ind + "]";
var availableQty = "avl_stock" + "[" + ind + "]";
var rowsgstper = "rowsgstper" + "[" + ind + "]";
var rowcgstper = "rowcgstper" + "[" + ind + "]";
var rowigstper = "rowigstper" + "[" + ind + "]";
var rowsgstamount = "rowsgstamount" + "[" + ind + "]";
var rowcgstamount = "rowcgstamount" + "[" + ind + "]";
var rowigstamount = "rowigstamount" + "[" + ind + "]";
var totalvalField = "total_val" + "[" + ind + "]";
// check if entered qty is something
if (document.getElementById(ent_qty).value) {
var qty = document.getElementById(ent_qty).value;
var dc_ty=document.getElementById("transfer_type").value;
} else {
var qty = 0;
}
//  check if entered price is somthing
if (document.getElementById(ent_rate).value) {
var price = document.getElementById(ent_rate).value;
} else {
var price = 0.00;
}
<?php  if($_REQUEST['transfer_type']== 'INV'){
if($toloctiondet[0] == $_SESSION['stateid']){?>
//  check if sgst per
if (document.getElementById(rowsgstper).value) {
var sgstper = document.getElementById(rowsgstper).value;
} else {
var sgstper = 0.00;
}
// check if cgst per
if (document.getElementById(rowcgstper).value) {
var cgstper = document.getElementById(rowcgstper).value;
} else {
var cgstper = 0.00;
}
<?php }else{?>
// check if igst per
if (document.getElementById(rowigstper).value) {
var igstper = (document.getElementById(rowigstper).value);
} else {
var igstper = 0.00;
}
<?php }}?>
// check entered qty should be available
if (parseInt(document.getElementById(availableQty).value) >= parseInt(document.getElementById(ent_qty).value)) {
//alert("Stock is Available - "+(parseInt(document.getElementById(availableQty).value)));
var total = parseFloat(qty) * parseFloat(price);
document.getElementById(cost).value=parseFloat(qty) * parseFloat(price);
//alert(total);
//var totalcost = parseFloat(price) - parseFloat(dicountval);    
<?php if($_REQUEST['transfer_type']== 'INV'){
if($toloctiondet[0] == $_SESSION['stateid']){?>
var sgst_amt = ((total * sgstper) / 100);
document.getElementById(rowsgstamount).value = formatCurrency(sgst_amt);
var cgst_amt = ((total * cgstper) / 100); 
document.getElementById(rowcgstamount).value = formatCurrency(cgst_amt);
var tot = parseFloat(total) + parseFloat(sgst_amt) + parseFloat(cgst_amt);
<?php }else{?>
var igst_amt = ((total * igstper) / 100);
document.getElementById(rowigstamount).value = formatCurrency(igst_amt);
var tot = parseFloat(total) + parseFloat(igst_amt);
<?php } }else{?>
var tot = parseFloat(total) ;
<?php } ?>

document.getElementById(totalvalField).value = formatCurrency(tot);
calculatetotal();
}
else {
alert("Stock is not Available");
document.getElementById(ent_qty).value = "";
//document.getElementById(availableQty).value = "";
//document.getElementById(ent_rate).value = "";		
calculatetotal();
}
}
////// calculate final value of form /////
function calculatetotal() {
var rowno1 = (document.getElementById("rowno").value);  
var dc_ty=document.getElementById("transfer_type").value;             
var sum_qty = 0;
var total = 0.00;
var sum =0.00;
var qty =0;
var cost =0.00;
var priceamt = 0.00;
var tot_price = 0.00;
var sgstsum = 0.00;
var cgstsum = 0.00;
var igstsum = 0.00;
for (var i = 0; i <= rowno1; i++) {
var temp_qty = "bill_qty" + "[" + i + "]";
var total_cost = "cost" + "[" + i + "]";
var total_amt = "total_val" + "[" + i + "]";	 
var total_price = "price" + "[" + i + "]";	
var sgstamt = "rowsgstamount"+"[" + i + "]";
var cgstamt = "rowcgstamount"+"[" + i + "]";
var igstamt = "rowigstamount"+"[" + i + "]";
var totsgstval = 0.00;
var totcgstval = 0.00;
var totigstval = 0.00;
///// check if line total amount is something
if (document.getElementById(total_amt).value) {
total = document.getElementById(total_amt).value;
} else {
total = 0.00;
}
///// check if line qty is something
if (document.getElementById(temp_qty).value) {
sum_qty = document.getElementById(temp_qty).value;
} else {
sum_qty = 0.00;
}
///// check if line price  is something
if (document.getElementById(total_cost).value) {
priceamt = document.getElementById(total_cost).value;
} else {
priceamt = 0.00;
}
if(dc_ty=='INV'){
<?php if($toloctiondet[0] == $_SESSION['stateid']){?>
///// check if totsgstamt is something
if (document.getElementById(sgstamt).value) {
totsgstval = document.getElementById(sgstamt).value;
} else {
totsgstval = 0.00;
}
///// check if totcgstamt is something
if (document.getElementById(cgstamt).value) {
totcgstval = document.getElementById(cgstamt).value;
} else {
totcgstval = 0.00;
}

sgstsum += parseFloat(totsgstval);
cgstsum += parseFloat(totcgstval);
<?php }else{ ?>
///// check if totigstamt  is something
if (document.getElementById(igstamt).value) {
totigstval = document.getElementById(igstamt).value;
} else {
totigstval = 0.00;
}
igstsum += parseFloat(totigstval);
<?php } ?>
}
sum +=parseFloat(total);
qty +=parseInt(sum_qty);
tot_price +=parseFloat(priceamt);
}/// close for loop
document.getElementById("sub_total").value = formatCurrency(sum);
document.getElementById("tot_qty").value = formatCurrency(qty);
document.getElementById("total_price").value = formatCurrency(tot_price);  
var round_off = parseFloat(parseFloat(Math.round(sum)) - parseFloat(sum)).toFixed(2);	 
document.getElementById("round_off").value = formatCurrency(round_off); 
document.getElementById("grand_total").value = formatCurrency(Math.round(sum));
<?php if($toloctiondet[0] == $_SESSION['stateid']){?>
document.getElementById("totsgstamt").value = formatCurrency(sgstsum);   
document.getElementById("totcgstamt").value = formatCurrency(cgstsum);
<?php }else{?>
document.getElementById("totigstamt").value = formatCurrency(igstsum);
<?php }?>
}
</script>
<script type="text/javascript" language="javascript">
</script>
</head>
<body onKeyPress="return keyPressed(event);">
<div class="container-fluid">
<div class="row content">
<?php
include("../includes/leftnavemp2.php");
?>
<div class="<?=$screenwidth?>">
<h2 align="center"><i class="fa fa-reply-all"></i>Sale Return</h2>
<?php if($_REQUEST['msg']){?>
<div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
<strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
</div>
<?php }?>
<br></br>
<div class="form-group" id="page-wrap" style="margin-left:10px;">
<form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
<div class="form-group">

<?php 
		 ///////////////////////// script for multi brand /////////////////////////////////////////////
		 
		////get access brand details
		$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
		$qr_str = mysqli_query($link1,"SELECT  distinct(location_code)  from access_brand where  brand_id in ($access_brand) and status='Y' ");
		while($qr_qry = mysqli_fetch_array($qr_str)){
			$brand_ar[] =  $qr_qry[0];
		}
		
		$brand_wise_loc = implode("','",$brand_ar);
							
?>

<div class="col-md-6"><label class="col-md-5 control-label">Billing  To<span style="color:#F00">*</span></label>
<div class="col-md-7">
<select name="bill_to" id="bill_to" required class="form-control required"  onChange="document.frm1.submit();">
<option value="">Please Select</option>
<?php
$map_wh = mysqli_query($link1,"select wh_location  from map_wh_location where location_code ='".$_SESSION['asc_code']."'  and  status = 'Y'"); 
while($row_wh = mysqli_fetch_assoc($map_wh)){
$location = mysqli_fetch_array(mysqli_query($link1, "select locationname, location_code from location_master where  location_code = '".$row_wh['wh_location']."'  and location_code in ('$brand_wise_loc') "));				
?>

<?php if($location['location_code']!=""){ ?>
<option value="<?=$location['location_code']?>" <?php if($_REQUEST['bill_to'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
<?php } ?>
<?php } ?>
</select>
</div>

</div>
<div class="col-md-6"><label class="col-md-5 control-label">Transfer Type</label>
<div class="col-md-7">
<select name="transfer_type" id="transfer_type" required class="form-control required"   onChange="document.frm1.submit();">
<option value="INV" <?php if($_REQUEST['transfer_type']=="INV"){echo "selected";} ?> >Sale Return(Invoice)</option>
<?php if($_SESSION['id_type']!='ASP'){ ?>
<option value="DC" <?php if($_REQUEST['transfer_type']=="DC"){echo "selected";} ?> >Stock Transfer(Delivery Challan)</option>
<?php } ?>
</select>
</div>
</div>
</div>                   
</form>
<form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
<div class="form-group">
<table class="table table-bordered" width="108%" id="itemsTable1" style="width:1300px">
<thead>
<tr>
<th style="text-align:center" width="12%">Product</th>
<th style="text-align:center" width="12%">Brand</th>
<th style="text-align:center" width="12%">Model</th>
<th style="text-align:center" width="14%">Part</th>                                           
<th style="text-align:center" width="8%">Price</th>
<th style="text-align:center" width="8%">Qty</th>
<th style="text-align:center" width="8%">Cost</th>
<?php if($_REQUEST['transfer_type']== 'INV'){
if($toloctiondet[0] == $_SESSION['stateid']){?>
<th style="text-align:center;" width="8%">SGST(%)<br/>SGST Amt</th>
<th style="text-align:center;" width="8%">CGST(%)<br/>CGST Amt</th>
<?php }else{?>
<th style="text-align:center;" width="6%">IGST(%)</th>
<th style="text-align:center;" width="8%">IGST Amt</th>
<?php }} ?>
<th style="text-align:center" width="10%">Total </th>
</tr>
</thead>
<tbody>
<tr id="addr0">
<td><select name="prod_code[0]" id="prod_code[0]" class="form-control required" required>
<option value="">Select</option>
<?php 
$model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){?>
<option data-tokens="<?php echo $br['product_id'];?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option>
<?php }?>
</select></td>
<td><select name="brand[0]" id="brand[0]" class="form-control required" onChange="getmodel(0)" required>
<option value=''>Select</option>
<?php
$dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.")  $brand_string  order by brand";
$check_dept=mysqli_query($link1,$dept_query);
while($br_dept = mysqli_fetch_array($check_dept)){
?>
<option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
<?php }?>	
</select></td>
<td><span id="modeldiv0"><select name="model[0]" id="model[0]" class="form-control required"  onChange="getpartcode(0)" required><option value="" selected="selected"> Select Model</option></select></span></td>
<td><span id="partcodediv0"><select name="partcode[0]" id="partcode[0]" class="form-control required"  onChange="getAvlStk(0)" required ><option value="" selected="selected"> Select Partcode</option></select></span></td>                                       
<td><input type="text" class="number form-control" name="price[0]" id="price[0]" onKeyUp="rowTotal(0);" autocomplete="off" readonly required style="width:71px;text-align:right;padding: 4px"></td>
<td><input type="text" class="number form-control" name="bill_qty[0]" id="bill_qty[0]" onKeyUp="rowTotal(0);"  autocomplete="off" required style="width:71px;text-align:right;padding: 4px">
<input type="hidden" name="avl_stock[0]" id="avl_stock[0]" value="" >
<input type="hidden" name="asc_code" id="asc_code" value="<?php echo $_SESSION['asc_code']; ?>"></td>       
<td><input type="text" class="number form-control" name="cost[0]" id="cost[0]"  autocomplete="off" readonly required style="width:71px;text-align:right;padding: 4px"></td>                                                                    
<?php if($_REQUEST['transfer_type']== 'INV'){
if($toloctiondet[0] == $_SESSION['stateid']){?>
<td ><div style="float:left"><input type="text" class="form-control" name="rowsgstper[0]" id="rowsgstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div style="float:right">%</div><br/><input type="text" class="form-control" name="rowsgstamount[0]" id="rowsgstamount[0]" value="0" readonly style="width:60px;text-align:right;padding:4px"></td>
<td ><div style="float:left"><input type="text" class="form-control" name="rowcgstper[0]" id="rowcgstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div style="float:right">%</div><br/><input type="text" class="form-control" name="rowcgstamount[0]" id="rowcgstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td>
<?php }else{?>
<td ><input type="text" class="form-control" name="rowigstper[0]" id="rowigstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td>
<td ><input type="text" class="form-control" name="rowigstamount[0]" id="rowigstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td>
<?php } }?>
<td><input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off" readonly  style="width:80px;text-align:right;padding: 4px"></td>
</tr>
</tbody>
<tfoot id='productfooter' style="z-index:-9999;">
<tr class="0">
<td colspan="15" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
</tr>
</tfoot>
</table>
</div>
<div class="form-group">
<div class="col-md-12">
<label class="col-md-3 control-label">Total Price</label>
<div class="col-md-2">
<input type="text" name="total_price" id="total_price" class="form-control" value="0.00" readonly style="width:200px;"/>
</div>
<label class="col-md-2 control-label">Total Qty </label>
<div class="col-md-2">
<input type="text" name="tot_qty" id="tot_qty" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>         
</div>
</div>
</div>
<div class="form-group">
<div class="col-md-12">
<label class="col-md-3 control-label"></label>
<div class="col-md-2">
</div>
<label class="col-md-2 control-label">Sub Total </label>
<div class="col-md-2">
<input type="text" name="sub_total" id="sub_total" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>
</div>
</div>
</div>
<div class="form-group">
<div class="col-md-12">
<label class="col-md-3 control-label"></label>
<div class="col-md-2">
</div>
<label class="col-md-2 control-label">Round Off </label>
<div class="col-md-2">
<input type="text" name="round_off" id="round_off" class="form-control" value="0.00" readonly style="width:200px;text-align:right"/>
</div>
</div>
</div>
<div class="form-group">
<div class="col-md-12">
<label class="col-md-3 control-label"></label>
<div class="col-md-2">
</div>
<label class="col-md-2 control-label">Grand Total</label>
<div class="col-md-2">
<input type="text" name="grand_total" id="grand_total" class="form-control" value="<?php echo ($po_row['po_value'] - $po_row['discount']); ?>" readonly style="width:200px;text-align:right"/>
</div>
</div>
</div>
<div class="form-group">
<div class="col-md-12">
<label class="col-md-3 control-label"></label>
<div class="col-md-2">
</div>
<label class="col-md-2 control-label">Remark</label>
<div class="col-md-2">
<textarea name="remark" id="remark" class="form-control" style="resize:none;width:200px" ></textarea>
</div>
</div>
</div>
<div class="form-group">
<div class="col-md-12" align="center">
<input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Process" 
onclick = "this.style.visibility = 'hidden';"  title="Make Invoice">
&nbsp;<input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_sale_return.php?<?=$pagenav?>'">
<input type="hidden" name="parentcode" id="parentcode" value="<?= $_REQUEST['bill_to'] ?>"/>
<input type="hidden" name="bill_typ" id="bill_typ" value="<?= $_REQUEST['transfer_type'] ?>"/>
<input type="hidden" name="totsgstamt" id="totsgstamt"/>
<input type="hidden" name="totcgstamt" id="totcgstamt"/>
<input type="hidden" name="totigstamt" id="totigstamt"/>
</div>
</div>
</form>
</div><!--close panel group-->
</div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php if ( $_REQUEST['bill_to'] == '') { ?>
<script>
$("#frm2").find("input:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
</script>
<?php
}
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>