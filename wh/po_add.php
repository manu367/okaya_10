<?php
include "../includes/config.php";
include "../includes/procurement_function.php";
$today=date("Y-m-d");
$today_t=date("Ymd"); 
$datetime=date("Y-m-d H:i:s");
$now=date("His");
//////make a date 30 days before from today/////
$next = strtotime ( '-30 day' , strtotime ( $today ) ) ;
$block_dt = date ( 'Y-m-d' , $next );

function dateFormat($date){
	return substr($date,0,4)."".substr($date,5,2)."".substr($date,8,2);
}
$flag1=1;
if(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') || strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') || strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') || strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape')){
	$flag1=1;
                $prop="margin-left:0px;";
}
else {
	$flag1=0;
                $prop="margin-left:0px;margin-top:10px;";
}

$party_det=mysqli_fetch_array(mysqli_query($link1,"select * from vendor_master where id='$_REQUEST[party_name]'"));
////////////////////////////////////////////////////////////////////////////////////////////////////
if($_REQUEST[b1]=='Submit'){
//$chk_PO=mysqli_fetch_array(mysqli_query($link1,"select grn_code from company_master where comp_code='$_REQUEST[comp_code]'"));
if($_FILES["file"]["name"]){
move_uploaded_file($_FILES["file"]["tmp_name"], "po_upload/".$now.$_FILES["file"]["name"]);
$file_name=$now.$_FILES["file"]["name"];
$attachment="../po_upload/".$now.$_FILES["file"]["name"];
chmod($attachment, 0777);
}

	////////////////insert into Expenditure Tracker///////////////////////////////
	if(is_array($_POST['item_id'])){
		$item=$_POST['item_id'];
        $desc=$_POST['desc'];
	    $rate=$_POST['rate'];
		$amt=$_POST['amt'];
		$discount=$_POST['discount'];
		$itmtax=$_POST['item_tax'];
		$tot_amt=$_POST['tot_amt'];
 	    $qty=$_POST['qty'];
       //$len2=count($_POST['item_id']);
	   $len2=$_POST['theValue3'];
	   $po_days= po_grn_gate_format();
	   $max_temp=  po_grn_temp($po_days,"po");
       if($len2>=0){
    mysqli_query($link1,"update counter_master set po=po+1 where year_detail='$po_days'") or die("error in updating counter".mysqli_error($link1));
	$challan_no="PO/".$po_days."/".$max_temp;
	
	$explodeTax=explode("-",$_REQUEST['tax']);

	if( isset( $_POST['chk_addrs'] ) ) {
         $foo = 'Yes';
    }
    else {
         $foo = 'No';
    }
	
	if($foo!='Yes'){
	$shipto=$_REQUEST['cust_name'];
	$shipaddrs=$_REQUEST['ship_address2'];
	}
	else{
	$shipto=$_REQUEST['bill_name'];
	$shipaddrs=$_REQUEST['bill_address2'];
	}
	if($_REQUEST['status']=='Open'){
	$potype_status=1;
	}
	else{
	$potype_status=2;
	}
	
	/////////////////insert into Expenditure challan////////////////////////////////////
	mysqli_query($link1,"insert into supplier_po_master set user_code='$_SESSION[userid]',asc_code='$_POST[asc_code]' , party_name='$_REQUEST[party_name]' ,address='$_REQUEST[ship_address]',bill_to='$_REQUEST[bill_name]',bill_address='$_REQUEST[bill_address2]', cust_name='$shipto' ,ship_address2='$shipaddrs', system_ref_no='$challan_no' , ch_temp='$temp_id' , entry_date_time='$datetime' , total_amt='$_REQUEST[total]', pending_amt='$_REQUEST[total]',actual_amt='$_REQUEST[actual_total]',grand_amt='$_REQUEST[grand_total]',tax_type='$explodeTax[0]',tax_per='$explodeTax[1]' , status='$_REQUEST[status]' , file_name='$file_name',bill_date='$_REQUEST[bill_date]', remark='$_REQUEST[remark]',currency='$_REQUEST[currency]',bill_no='$_REQUEST[bill_no]',upd_by='$_SESSION[userid]',flag='1',ip_address='$_SERVER[REMOTE_ADDR]',deliv_schedule_date='$_REQUEST[ds_date]',po_type='$_REQUEST[po_type]',ship_type='$_REQUEST[ship_type]',status_id='$potype_status'")or die("Error2".mysqli_error($link1));

//$trntypeid=getTrnTypeid($_REQUEST[voucher_type]);

//	mysqli_query($link1,"insert into ledger set company_code='$_POST[comp_code]', party_code='$_REQUEST[party_name]', trn='Supplier Bill', trn_no='$challan_no' , update_date='$datetime' ,trn_date='$today' ,amt='$_REQUEST[total]', cr_dr='CR',trn_type='$trntypeid',voucher_type='$_REQUEST[voucher_type]',voucher_no='$_REQUEST[bill_no]',status='Received',remark='$_REQUEST[remark]'")or die("Error2".mysqli_error($link1));
		   
          for($i=0;$i<=$len2;$i++){
			 $bill_itemtemp=$item[$i]; 
             $bill_desc=$desc[$i];
  		     $bill_rate=$rate[$i];
		     $bill_qty=$qty[$i];
			 $cost=$amt[$i];
			 $discPer=$discount[$i];
			 $itmtaxPer=$itmtax[$i];
			 $totCost=$tot_amt[$i];
		  $bill_item1=explode(":",$bill_itemtemp);
		  $bill_item=$bill_item1[0];
                    if($bill_item!=""){
		               $req_ins2="insert into supplier_po_data  set product='$bill_item' , description='$bill_desc', req_qty='$bill_qty' , qty='$bill_qty' , price='$bill_rate' ,cost='$cost',discount='$discPer',item_tax='$itmtaxPer',total_cost='$totCost', system_ref_no='$challan_no' , entry_date='$today' ,asc_code='$_REQUEST[asc_code]', flag='1' , status='$_REQUEST[status]'";
		               $req_res2=mysqli_query($link1,$req_ins2)or die("Error3".mysqli_error($link1));
					}
		  }
	$msg="Purchase Order of  Rs.".$_REQUEST['grand_total']." has been Saved.";
         header("Location:purchase_order_details.php?msg=".$msg."&srch=".$challan_no."&asc_code=".$_POST[asc_code]."");
	   }
	   else{
		   header("Location:po_add.php?asc_code=".$_POST['asc_code']."");
		   exit;
	   }
	}
	/////////////////////////////////////////////////////////////////////////////
}
?>
<!doctype html>
<html lang="en-us" dir="ltr">
<head>
<meta charset="utf-8">
<title></title>
<script src="../js/jquery.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<script language="JavaScript" src="jquery/jquery-1.6.4.js"></script>
<script language="JavaScript" src="../js/ajax.js"></script>

<link href="../css/auto_suggetion.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/bootstrap-datepicker.js"></script>
<script type="text/javascript" language="javascript">
////////////////// Auto Fill Script//
function suggest(inputString,indx){
		if(inputString.length == 0) {
			$('#suggestions'+indx).fadeOut();
		} else {
		$('#item_id').addClass('load');
			$.post("autosuggest_combox.php", {queryString: ""+inputString+"", indx1: ""+indx+"" , user_id: ""+'<?=$_SESSION[userid]?>'+""}, function(data){
				if(data.length >0) {
					$('#suggestions'+indx).fadeIn();
					$('#suggestionsList'+indx).html(data);
					$('#item_id').removeClass('load');
				}
			});
		}
	}

	function fill(thisVal) {
		var str=thisVal;
		//alert(str);
        var res=new Array(4);
		res=str.split('~');
		//alert(res);
		var price=res[2];
		var ablqty=res[3];
		//alert(ablqty);
		var inde=res[1];
		var rs="#suggestions"+inde;
		if(res[0]!=""){
			var val1=res[0];
			var prodId="item_id["+inde+"]";
			var rateId="rate["+inde+"]";
			var qtyId="qty["+inde+"]";
			var ablqtyId="ablqty["+inde+"]";
			var qtyval=document.getElementById(qtyId).value;
		document.getElementById(prodId).value=val1;
		document.getElementById(rateId).value=price;
		document.getElementById(ablqtyId).value=ablqty;
		
		fillCost2(qtyval,inde);
		///////////////////////////////////////////////////////////////
		setTimeout($('#suggestions'+inde).fadeOut(), 6);
		}
		else{
			document.getElementById("item_code["+inde+"]").value='';
		}
		getDescp(inde,res[0]);
	}
//////////// End Auto Fill Script //


function onlyNumbers(evt){  
var e = event || evt; // for trans-browser compatibility
var charCode = e.which || e.keyCode;  
if (charCode > 31 && (charCode < 48 || charCode > 57))
{
return false;
}
return true;
}
///// Enter Only Float Value/////////
function onlyFloatNum(evt){  
var e = event || evt; // for trans-browser compatibility
var charCode = e.which || e.keyCode;  
if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode!=46)
{
return false;
}
return true;
}
///////////////////////////////////////
function disableFunc(val) {
document.getElementById("ship_address").disabled = true;
}
///////////////////////////////////////
function checknumb(field){
//	alert(field);
pattern = /^[0-9][0-9]*\.?[0-9]*$/;
if(pattern.test(field.value) == false){
alert("Only Numeric Value Enter: " + field.value);
field.value=9;
field.focus();
field.select();
}
}
function makeCalDOP3(ind){
    Calendar.setup({
    inputField : "bill_date["+ind+"]",
    trigger    : "calendar-3trigger"+ind,
min: <?=dateFormat($block_dt);?>,
max: <?=$today_t?>,
	weekNumbers   : true,
    selectionType : Calendar.SEL_MULTIPLE,
    selection     : Calendar.dateToInt(new Date()),
    onSelect   : function() { this.hide() }
});
}
// In this function print function not Enable when all fields not fillup
function chk_data(){
var error=false;
var errorMsg="Sorry we can not complete your request.Following Information is missing: \n";
doc=document.form1;
if(doc.party_name.value==""){
errorMsg+="Please Select Supplier Name. \n";
error=true;
}
if(doc.cust_name.value==""){
errorMsg+="Please Select Ship To. \n";
error=true;
}
if(doc.ship_address2.value==""){
errorMsg+="Please Enter the Shipping Address. \n";
error=true;
}
if(doc.bill_name.value==""){
errorMsg+="Please Select Billing To . \n";
error=true;
}
if(doc.bill_address2.value==""){
errorMsg+="Please Enter the Billing Address. \n";
error=true;
}
if(doc.bill_no.value==""){
errorMsg+="Please Enter Ship Via. \n";
error=true;
}
if(doc.po_type.value==""){
errorMsg+="Please Select PO Type. \n";
error=true;
}
if(doc.currency.value==""){
errorMsg+="Please Select Currency Type. \n";
error=true;
}
if(doc.ship_type.value==""){
errorMsg+="Please Select Shipping Type. \n";
error=true;
}
var num = (document.getElementById("theValue3").value);
//alert(num);
for(j=0;j<=num;j++){

if(document.getElementById("item_id["+j+"]").value=="" && document.getElementById("chk["+j+"]").checked){
  errorMsg+="Product should not be blank on Line no."+(j+1)+". \n";
  error=true;
  }
 if((document.getElementById("qty["+j+"]").value=="" ||  document.getElementById("qty["+j+"]").value==0) && document.getElementById("chk["+j+"]").checked){
  errorMsg+="Qty should not be blank on Line no."+(j+1)+". \n";
  error=true;
  } 
/*  if(document.getElementById("desc["+j+"]").value==""){
  errorMsg+="Description should not be blank on Line no."+(j+1)+". \n";
  error=true;
  }
*/  /*if(document.getElementById("qty["+j+"]").value==""){
  errorMsg+="Qty should not be blank on Line no."+(j+1)+". \n";
  error=true;
  }*/
  
  
}
if(error==true){
alert(errorMsg);
return false;
}
}
function formatCurrency(num) {
num = num.toString().replace(/\$|\,/g,'');
if(isNaN(num))
num = "0";
signt = (num == (num = Math.abs (num)));
num = Math.floor(num*100+0.50000000001);
cents = num%100;
num = Math.floor(num/100).toString();
if(cents<10)
cents = "0" + cents;
for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++) 
num = num.substring(0,num.length-(4*i+3))+''+
num.substring(num.length-(4*i+3));
return (((signt)?'':'-') + '' + num + '.' + cents);
}

var findex='';
function addEvent3(findex,tindex)
{
	//alert(findex);
//alert(findex+"--"+tindex);
var ni = document.getElementById('myDiv3');
var numi = document.getElementById('theValue3');
var itm="desc["+numi.value+"]";
var itemid="item_id["+numi.value+"]";
var pqty="qty["+numi.value+"]";

var num = (document.getElementById("theValue3").value -1)+ 2;
//if(document.getElementById(itemid).value!=""){
numi.value = num;
var divIdName = "my"+num+"Div3";
var newdiv = document.createElement('div');
newdiv.setAttribute("id",divIdName);
newdiv.innerHTML = "<table width=100%  border=1 cellpadding=2 cellspacing=0 class=form-control required><tr><td width=2%><input name=sno["+num+"] id=sno["+num+"] class='form-control required' type='text' size=2 value="+(num+1)+" readonly='readonly'  style='width:20px'/></td><td width=19%><input name='item_id["+num+"]' id='item_id["+num+"]' class='form-control required' type='text' onkeyup='suggest(this.value,"+num+");' autocomplete='off' style='width:250px;background:#D4FFFF;background-image:url(../images/find.png);background-repeat:no-repeat;background-position:right;'/><div class='suggestionsBox' id='suggestions"+num+"' style='display:none; <?php echo $prop;?>'><img src='../images/arrow.png' style='position: absolute; top: -12px; left: 20px;' alt='upArrow' /><span class='suggestionList' id='suggestionsList"+num+"'></span></div></td><td width=22% ><input name=desc["+num+"]  id=desc["+num+"] class='form-control required' type='text'  TABINDEX="+(tindex+8)+"  style='width:250px' /></td><td width='4%'><input name=qty["+num+"] class='form-control required' onkeyup='fillCost2(this.value,"+num+")' onKeyPress='return onlyNumbers(this.value);' id=qty["+num+"] value='' type='text'  TABINDEX="+(tindex+8)+"  style='width:50px' /></td><td width='6%'><input name=rate["+num+"] onkeyup='fillCost(this.value,"+num+")' id=rate["+num+"] class='form-control required' type='text' value='0.00' onKeyPress='return onlyFloatNum();'  TABINDEX="+(tindex+8)+"  style='width:80px' /></td><td width='8%'><input name=amt["+num+"]  id=amt["+num+"] type='text' class='form-control required' size=12 value='0.00' readonly TABINDEX="+(tindex+8)+" onKeyPress='return onlyFloatNum();' style='text-align:right;'/></td><td width=10.1%><input name=discount["+num+"]  id=discount["+num+"] TABINDEX="+(tindex+8)+" type='text' class='form-control required' size=8 value='0.00' onKeyPress='return onlyFloatNum();' onkeyup='calculatetotal();' style='text-align:right;'/></td><td width='7%'><input name=item_tax["+num+"] TABINDEX="+(tindex+8)+"  id=item_tax["+num+"] type='text' class='form-control required' size=8 value='0.00' onKeyPress='return onlyFloatNum();' onkeyup='calculatetotal();' style='text-align:right;'/></td><td width='8%'><input name=tot_amt["+num+"] TABINDEX="+(tindex+8)+" id=tot_amt["+num+"] type='text' class='form-control required' size=12 value='0.00' readonly  onKeyPress='return onlyFloatNum();' style='text-align:right;'/></td><td width='6%'><input name=ablqty["+num+"]  id=ablqty["+num+"] class='form-control required' value='' type='text'  TABINDEX="+(tindex+10)+"  style='width:50px;background-color:#FF5;' /></td><td width=7%><input type=checkbox name=chk["+num+"] TABINDEX="+(tindex+8)+" id=chk["+num+"] checked onclick='return getRowDisable("+num+")'></td></tr></table>";
ni.appendChild(newdiv);

var e="qty["+findex+"]";
var c="rate["+findex+"]";
var d="amt["+findex+"]";
document.getElementById(d).value=formatCurrency((document.getElementById(c).value)*(document.getElementById(e).value));  
var itm1="item_id["+(num)+"]"; 
//alert(itm1);
document.getElementById(itm1).focus();
//}
/*else{
alert("Product of previous line Not selected!");
document.getElementById(itemid).focus();
}
*/

}

//////////// for remove function //////////////

function getRowDisable(ind) {
//alert(ind);
     var id="my"+ind+"Div3";
	 var chkid="chk"+"["+ind+"]";
	 var prodid="item_id"+"["+ind+"]";
	 var itm="desc"+"["+ind+"]";
     var rateid="rate"+"["+ind+"]";
	 var qtyid="qty"+"["+ind+"]";
	 var discountid="discount"+"["+ind+"]";
	 	 var itemtax="item_tax"+"["+ind+"]";
	 var amtid="amt"+"["+ind+"]";
	 var totalamtid="tot_amt"+"["+ind+"]";
	 var snoid="sno"+"["+ind+"]";
	 // hide fieldset \\
	 
if(!document.getElementById(chkid).checked){	 
    document.getElementById(id).disabled=true
	// Reset Value\\
	// Blank the Values \\
	document.getElementById(prodid).value="";
	document.getElementById(prodid).disabled=true
	
	document.getElementById(itm).value="";
	document.getElementById(itm).disabled=true
	
	document.getElementById(qtyid).value="0";
	document.getElementById(qtyid).disabled=true
	
	document.getElementById(rateid).value="0.00";
	document.getElementById(rateid).disabled=true
	
		document.getElementById(amtid).value="0.00";
	document.getElementById(amtid).disabled=true
	
		document.getElementById(discountid).value="0.00";
	document.getElementById(discountid).disabled=true
	
		document.getElementById(totalamtid).value="0.00";
	document.getElementById(totalamtid).disabled=true
	
			document.getElementById(itemtax).value="0.00";
	document.getElementById(itemtax).disabled=true
	
	document.getElementById(snoid).disabled=true
calculatetotal();
}
else{
 //  document.getElementById(id).disabled=true
	// Reset Value\\
	// Blank the Values \\
	//document.getElementById(prodid).value="";
	document.getElementById(prodid).disabled=false
	
	//document.getElementById(itm).value="";
	document.getElementById(itm).disabled=false
	
	//document.getElementById(qtyid).value="";
	document.getElementById(qtyid).disabled=false
	
	//document.getElementById(rateid).value="0.00";
	document.getElementById(rateid).disabled=false
	
		//document.getElementById(amtid).value="0.00";
	document.getElementById(amtid).disabled=false
	
	//	document.getElementById(discountid).value="0.00";
	document.getElementById(discountid).disabled=false
	
//		document.getElementById(totalamtid).value="0.00";
	document.getElementById(totalamtid).disabled=false
	
	document.getElementById(snoid).disabled=false
	document.getElementById(itemtax).disabled=false
calculatetotal();	
}
}

function displayShipAddrs_same(){
//alert(val);	
if(document.getElementById('chk_addrs').checked){
var a=document.getElementById('bill_name').value;	
var b=document.getElementById('bill_address2').value;	

document.getElementById('cust_name').value=a;
document.getElementById('ship_address2').value=b;
document.getElementById('cust_name').disabled=true;
document.getElementById('ship_address2').disabled=true;
}
else{
document.getElementById('cust_name').value='';
document.getElementById('ship_address2').value='';
document.getElementById('cust_name').disabled=false;
document.getElementById('ship_address2').disabled=false;
}
}

function calculatetotal(){
var rowno=(document.getElementById("theValue3").value);
var sum=0.00; 
var actualTotal=0.00;
var tot_qty=0;
for(var i=0;i<=rowno;i++){
var temp="amt["+i+"]";
var t_qty="qty["+i+"]";
var disc="discount["+i+"]";
var itmtax="item_tax["+i+"]";
var totalamount="tot_amt["+i+"]";

if(document.getElementById(disc).value==''){
	document.getElementById(disc).value=0;
}
else if(document.getElementById(itmtax).value==''){
	document.getElementById(itmtax).value=0;
}
	else{
	
	}
//alert();
var discRate=document.getElementById(disc).value;
var discAMT=((parseFloat(discRate) * parseFloat(document.getElementById(temp).value))/100);
//alert(discAMT);
var afterDisc=(parseFloat(document.getElementById(temp).value)-parseFloat(discAMT));
//alert(afterDisc);
var itmtaxRate=((parseFloat(document.getElementById(itmtax).value) * parseFloat(afterDisc))/100);
//alert(itmtaxRate);
//var totAmt=(parseFloat(itmtaxRate)+parseFloat(document.getElementById(temp).value))-((parseFloat(discRate) * parseFloat(document.getElementById(temp).value))/100); 
var totAmt=(parseFloat(itmtaxRate)+parseFloat(document.getElementById(temp).value)-parseFloat(discAMT)); 
document.getElementById(totalamount).value=totAmt.toFixed(2);
sum+=parseFloat(document.getElementById(totalamount).value);
actualTotal+=parseFloat(document.getElementById(temp).value);
tot_qty+=parseInt(document.getElementById(t_qty).value);
}
document.getElementById("total_qty").value=parseInt(tot_qty);
document.getElementById("actual_total").value=formatCurrency(actualTotal);
document.getElementById("total").value=formatCurrency(sum);
document.getElementById("grand_total").value=formatCurrency(sum);
calculate_tax();
}
//// calculate Tax
function calculate_tax(){
  var taxDetails=document.getElementById("tax").value;
  var expldTax=taxDetails.split("-");
  var taxPer=expldTax[1];
  var subTot=document.getElementById("total").value;
  if(taxPer){
  var grandTot=(parseFloat(subTot))+((parseFloat(subTot)*parseFloat(taxPer))/100);
  document.getElementById("grand_total").value=formatCurrency(grandTot);	
  }else{
  document.getElementById("grand_total").value=formatCurrency(subTot);	
  }
}
///////////////////
function getPartyaddress(val){
if(val!="")
{
var strSubmit="action=getPartyaddress&value="+val;
var strURL="../includes/getField.php";
var strResultFunc="displayAddress_party";
xmlhttpPost(strURL,strSubmit,strResultFunc);
return false;	
}
}
/////////////////////////////////// getting avalable part list as per model & repaircode ///////////////////////////////////
function displayAddress_party(result){
document.getElementById('ship_address').value=result;
alert(result)
}

function getPartyaddress2(val){
if(val!="")
{
var strSubmit="action=getPartyaddress&value="+val;
var strURL="../includes/getField.php";
var strResultFunc="displayAddress_party2";
xmlhttpPost(strURL,strSubmit,strResultFunc);
return false;	
}
}
/////////////////////////////////// getting avalable part list as per model & repaircode ///////////////////////////////////
function displayAddress_party2(result){
var str = result;
var res = str.split("~");	
document.getElementById('ship_address2').value=res[0];

}
//////////////Address for Billing//////////////////////////
function getPartyaddress_bill(val){
//alert(val);
if(val!="")
{
document.getElementById('chk_addrs').disabled=false;	
var strSubmit="action=getPartyaddressbill&value="+val;
var strURL="../includes/getField.php";
var strResultFunc="displayAddress_bill";
xmlhttpPost(strURL,strSubmit,strResultFunc);
return false;	
}
else{
document.getElementById('chk_addrs').disabled=true;
document.getElementById('bill_address2').value='';				
}
}
/////////////////////////////////// fetching Address ///////////////////////////////////
function displayAddress_bill(result){
	//alert(result);
document.getElementById('bill_address2').value=result;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function  fillCost(val1,val2){
	//	alert(val1);
	//		alert(val2);
var p="qty["+val2+"]";
var price=document.getElementById(p).value;
//alert(price);
var total= val1*price;
var var3="amt["+val2+"]";
document.getElementById(var3).value=total.toFixed(2);

calculatetotal();
}

function  fillCost2(val1,val2){
		//alert(val1);
			//alert(val2);
var p="rate["+val2+"]";
var price=document.getElementById(p).value;
//alert(price);
var total= val1*price;
//alert(total);
var var3="amt["+val2+"]";
document.getElementById(var3).value=total;
calculatetotal();
}
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}


</script>

    
    <style type="text/css">
<!--
.style1 {color: #FF0000}
.style11 {color: #FF0000}
-->
    </style>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
  <?php 
    include("../includes/leftnavemp2.php");
    ?>
<div class="col-sm-9">
<h3 align="center"><i class="fa fa-ship"></i> Add Purchase Order</h3><br/>
<div class="form-group"  id="page-wrap" style="margin-left:10px;">
<form name="form1" id="form1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" onSubmit="return chk_data()" >
 <div class="form-group">
      <div class="col-md-6"><label class="col-md-5 control-label">Supplier:</label>
        <div class="col-md-5" id="pty"><select name="party_name" id="party_name" style="width:250px;" class="required form-control" onChange="return getPartyaddress(this.value);">
             						<option value="">Please Select </option>
									<?php $sql_ch2="select distinct(id),name,state from vendor_master where status='Active'";
									$res_ch2=mysqli_query($link1,$sql_ch2);
									while($result_ch2=mysqli_fetch_array($res_ch2)){?>
									<option value="<?=$result_ch2['id']?>"<?php if($_REQUEST['party_name']==$result_ch2['id'])echo "selected";?> >
                					<?=$result_ch2['name']." | ".$result_ch2['id']." | ".$result_ch2['state']?>
                					</option>
									<?php
									}
								$sql_ch3="select distinct(id),name,state from temp_vendor_master  where  status='Active' and asc_code='$_SESSION[asc_code]'";
								$res_ch3=mysqli_query($link1,$sql_ch3);
								while($result_ch3=mysqli_fetch_array($res_ch3)){?>
								<option value="<?=$result_ch3['id']?>" <?php if($_REQUEST['party_name']==$result_ch3['id'])echo "selected";?> >
                				<?=$result_ch3['name']." | ".$result_ch3['id']." | ".$result_ch3['state']."(Temporary)"?>
                				</option>
                    			<?php
								}
								?>
              					</select><a href="#" onClick="window.open('../master/addVendor.php?str=S', 'addSupplier', 'toolbar=No, status=No, resizable=yes, scrollbars=yes, width=800, height=310, top=100, left=340');return false"><img src="../images/file_add.png" width="20" height="20" border="0" title="Add New" /></a>&nbsp;<a id="refresh" href="javascript:void(0);"><img src="../images/refresh.gif" border="0" title="Refresh..."></a>
								<script>$(function() {
      							$("#refresh").click(function(evt) {
        						 $("#pty").load("div_supplier.php")
        						 evt.preventDefault();
								 document.getElementById('ship_address').value="";
      							})
    							})
								</script>
                                </div>
    					</div>
              <div class="col-md-6"><label class="col-md-5 control-label"><span class="style1">*</span>&nbsp;Mailing Address: </label>
              <div class="col-md-5"><textarea name="ship_address" id="ship_address" class="form-control required" style="width:250px;resize:vertical"><?=$party_det['ship_address']?></textarea>
           </div>
           </div>
           </div>
           <div class="form-group">
           <div class="col-md-6"><label class="col-md-5 control-label"><span class="style1">*</span>Bill to:</label>
    		<div class="col-md-5" id="cust"><select name="bill_name" id="bill_name" style="width:250px;" class="required form-control" onChange="return getPartyaddress_bill(this.value);">
				<option value="">Please Select </option>
				 <?php $sql_chl="select name,city,state,asc_code from asc_master where id_type='WH' and status='Active'";
				$res_chl=mysqli_query($link1,$sql_chl);
				while($result_chl=mysqli_fetch_array($res_chl)){
				?>
				<option value="<?=$result_chl['asc_code']?>" <?php if($_REQUEST['bill_name']==$result_chl['asc_code'])echo "selected";?> >
				<?=$result_chl['name']." | ".$result_chl['asc_code']." | ".$result_chl['state']?>
				</option>
				<?php
                }
				?>
				</select></div>
  			 </div>
              <div class="col-md-6"><label class="col-md-5 control-label"><span class="style11">*</span>&nbsp;Billing Address:</label>
              <div class="col-md-5"><textarea name="bill_address2" id="bill_address2" class="form-control required" style="width:250px;resize:vertical"></textarea></div>
              </div>
              </div>
             <div class="form-group"> 
         		<div class="col-md-6"><label class="col-md-5 control-label"><span class="style1">*</span>  Ship to:</label>
                <div class="col-md-5" id="cust"><select name="cust_name" id="cust_name" style="width:250px;" class="required form-control" onChange="return getPartyaddress2(this.value);">

             <option value="">Please Select </option>

                    <?php 
$sql_chl="select name,city,state,asc_code from asc_master where id_type='WH' and status='Active'";
$res_chl=mysqli_query($link1,$sql_chl);
while($result_chl=mysqli_fetch_array($res_chl))

{

?>

                <option value="<?=$result_chl['asc_code']?>" <?php if($_REQUEST['cust_name']==$result_chl['asc_code'])echo "selected";?> >

                <?=$result_chl['name']." | ".$result_chl['asc_code']." | ".$result_chl['state']?>

                </option>

  

            

<?php

}

?>

              </select>Same as Bill To<input name="chk_addrs" id="chk_addrs" type="checkbox" value="Yes" disabled="disabled"  onclick="return displayShipAddrs_same();" /></div>
               </div>
               <div class="col-md-6"><label class="col-md-5 control-label"><span class="style11">*</span>&nbsp;Shipping Address:</label>
                <div class="col-md-5"><textarea name="ship_address2" class="form-control" id="ship_address2" style="width:250px;resize:vertical"></textarea></div>
                </div>
                </div>
              <div class="form-group">  
          		<div class="col-md-6"><label class="col-md-5 control-label"><span class="style1">*</span>Currency:</label>
                <div class="col-md-5"><select name="currency" id="currency" class="required form-control" style="width:250px;">
                          <option value="" selected="selected">Please Select </option>
                          <?php $sql_chl="select * from currency_master where status='A' order by name";
								$res_chl=mysqli_query($link1,$sql_chl);
								while($result_chl=mysqli_fetch_array($res_chl)){
						  ?>
                          <option value="<?=$result_chl['code']?>" <?php if($result_chl['code']==$_REQUEST['currency'])echo "selected";?> >
                            <?=$result_chl['name']?>
                          </option>
                          <?php }
							?>
                        </select></div>
            <div class="col-md-6">&nbsp;</div>
            </div>
            </div>
          <div class="form-group">
          <div class="col-md-6"><label class="col-md-5 control-label">Status:</label>
           <div class="col-md-5">
           			<select name="status" id="status" class="required form-control" style="width:250px;">
                      <option value="Open" selected="selected">Open</option>
                      <option value="Closed">Closed</option>
                    </select>
            </div>
            </div>
             <div class="col-md-6"><label class="col-md-5 control-label"><span class="style11">*</span> Ship via:</label>
             <div class="col-md-5"><input name="bill_no" id="bill_no"  type="text"  class="required form-control" style="width:250px;"/></div>
             </div>
             </div>
            <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label"><span class="style11">*</span>PO Date:</label>
            <div class="col-md-5"><input name="bill_date" id="bill_date" value="<?=$today;?>" type="text" class="required form-control" readonly />
              <img src="../images/calicon.jpg" id="calendar-3trigger0" />
           </div>
           </div>
           <div class="col-md-6"><label class="col-md-5 control-label"><span class="style11">*</span>Delivery Schedule Date:</label>
            <div class="col-md-5"><input name="ds_date" id="ds_date" value="<?=$today;?>" type="text" class="required form-control" readonly />
              <img src="../images/calicon.jpg" id="calendar-3trigger0" />
           </div>
           </div>
           </div> 
           <div class="form-group">
           <div class="col-md-6"><label class="col-md-5 control-label"><span class="style11">*</span>PO Type:</label>
            <div class="col-md-5"><select name="po_type" id="po_type" class="required form-control" style="width:250px;">
              <option value="">--Please Select--</option>
              <option value="PO">PO</option>
              <option value="SAMPLE">SAMPLE</option>
              <option value="GRN">GRN</option>
              <option value="LOCAL">LOCAL</option>
             <option value="CANNIBALIZATION">CANNIBALIZATION</option> 
             </select></div>
             </div>
            <div class="col-md-6"><label class="col-md-5 control-label"><span class="style11">* </span>Shipping Type:</label>
             <div class="col-md-5"><select name="ship_type" id="ship_type" class="required form-control" style="width:250px;">
              <option value="">--Please Select--</option>
              <option value="R">Returnable</option>
              <option value="NR">Non-returnable</option>
              <option value="ADJUST">Adjust To The Original QTY</option>
              <option value="FOC">Free Of Cost(FOC)</option>
            </select></div>
            </div>
            </div>          
<div class="form-group">
<table width="100%" cellpadding="2" cellspacing="1" border="1" align="center"  class="table table-bordered" id="itemsTable1">
<thead>
	<tr>
                  <th width="2%">S.No.</th>
                  <th width="20%">Partcode (SKU)</th>
                  <th width="17%"> Remark</th>
                  <th width="5%">Qty</th>
                  <th width="7%">Rate</th>
                  <th width="7%">Amount</th>
                  <th width="10%">Discount(%)</th>
                  <th width="6%">Tax(%)</th>
                  <th width="9%">Total Amount</th>
                  <th width="6%">Avl. Qty</th>
                  <th width="6%">Select</th>
                </tr>
       </thead>
       <tbody>
                 <tr id="my0Div3">
                  <td width="2%" ><input name="sno[0]"  id="sno[0]" type="text" class="form-control" style="width:50px; text-align:left;"  value="1" readonly/></td>
        <td width="20%" align="left"><input name="item_id[0]" id="item_id[0]" class="form-control" onBlur="return getField(0,this.value);"  type="text" style="width:202px; background:#D4FFFF; background-image:url(../images/find.png); background-repeat:no-repeat; background-position:right;" onKeyUp="suggest(this.value,0);" autocomplete="off"/><div class="suggestionsBox" id="suggestions0" style="display:none; <?php echo $prop;?>"> 
                         <img src="../images/arrow.png" style="position: absolute; top: -12px; left: 20px;" alt="upArrow" />
              <span class="suggestionList" id="suggestionsList0"></span>
            </div></td>
                  <td width="22%" align="left"><input name="desc[0]" id="desc[0]"  type="text"  class="form-control" style="width:200px;"/></td>
                  <td width="5%"><input name="qty[0]" id="qty[0]" class="form-control"  type="text" value="" onKeyPress="return onlyNumbers(this.value);"  tabindex="12" onKeyUp="fillCost2(this.value,0)"  style="width:50px;"/></td>
                  <td width="7%"><input name="rate[0]" id="rate[0]" value="0.00" class="form-control" type="text" onKeyPress="return onlyFloatNum(this.value);" onKeyUp="fillCost(this.value,0)"  TABINDEX=13 style="width:80px;"/></td>
                  <td width="7%"><input name="amt[0]" id="amt[0]" type="text" class="form-control" size="12"  value="0.00"  readonly onKeyPress="return onlyFloatNum(this.value);"  TABINDEX=14 style="text-align:right;"/></td>
                  <td width="10%"><input name="discount[0]" id="discount[0]" type="text" class="form-control" size="8"  value="0.00" onKeyPress="return onlyFloatNum(this.value);" onKeyUp="calculatetotal();"  TABINDEX=15 style="text-align:right;"/></td>
                  <td width="6%"><input name="item_tax[0]" id="item_tax[0]" type="text" class="form-control" size="8"  value="0.00" onKeyPress="return onlyFloatNum(this.value);" onKeyUp="calculatetotal();"  TABINDEX=16 style="text-align:right;"/></td>
                  <td width="9%"><input name="tot_amt[0]" id="tot_amt[0]" type="text" class="form-control" size="12"  value="0.00"  readonly onKeyPress="return onlyFloatNum(this.value);"  TABINDEX=17 style="text-align:right;"/></td>
                  <td width="6%"><input name="ablqty[0]" id="ablqty[0]"  type="text" value="" class="form-control"  readonly  style="width:50px; background-color:#FF5;"/></td>
                  <td width="6%"><input type="checkbox" name="chk[0]" id="chk[0]" tabindex="18" checked onClick="return getRowDisable([0])"></td>
                
                <div id="myDiv3"> </div>
              <input name="theValue3" type="hidden"  id="theValue3" value="0" />
              </tbody>
      </tr>
      <tfoot id='productfooter' style="z-index:-9999;">
            <tr class="0">
            <td>&nbsp;</td>
              <td width="13%"><input name="button3" type="button"  class="btn<?=$btncolor?>" onClick="addEvent3(0,10);" value="Add Lines+"/></td>
              <td align="right" valign="middle">Total Qty:&nbsp;</td>
              <td align="right"><input name="total_qty" id="total_qty" type="text" class="form-control" size="10"  value="0"  readonly   style="text-align:right;"/></td>
              <td align="right">&nbsp;</td>
              <td align="right">&nbsp;</td>
              <td align="right">&nbsp;</td>
              <td align="right">Total:</td>
              <td width="30%"><input name="total" id="total" type="text" class="form-control" size="10"  value="0.00"  readonly onKeyPress="return onlyFloatNum(this.value);"  style="text-align:right;"/><input name="actual_total" id="actual_total" type="hidden" class="textbox" size="10" value="0.00"  readonly style="text-align:right;"/></td>
              
            </tr>
            </tfoot>
      </table>
      </div>
      <div class="form-group">
      <table width="100%" cellpadding="2" cellspacing="1" border="1" align="center" class="table table-bordered">
       <tr>
          <td colspan="2" align="left" valign="top" >Attachment :<input type="file" name="file" id="file" class="form-control"> Max Size 2MB <span class="style1">*</span></td>
          <td width="17%" align="left" valign="top" >Tax: <select name="tax" id="tax" class="form-control" style="width:250px;" onChange="calculate_tax();">
             <option value="">--Please Select--</option>
             <?php 
			   $res_tax=mysqli_query($link1,"select * from tax_master where status='Active'")or die("error in tax fetching".mysqli_error($link1));
			   while($row_tax=mysqli_fetch_array($res_tax)){
			  ?>
                <option value="<?=$row_tax['name']."-".$row_tax['per']?>"><?=$row_tax['name']." | ".$row_tax['per']."% | ".$row_tax['state']." | ".$row_tax['cat']?></option>
              <?php
			  }
              ?>
          </select></td>
          <td width="12%" align="right" valign="top" >Grand Total: </td>
          <td align="left" valign="top"><input name="grand_total" id="grand_total" type="text" class="form-control" size="10"  value="0.00"  readonly onKeyPress="return onlyFloatNum(this.value);" style="text-align:right;"/></td>
        </tr>
        <tr>
          <td colspan="5" align="left" valign="top">Terms &amp; Conditions :
            <textarea name="remark" rows="2" cols="80" tabindex="22" style="width:250px"></textarea>
          <input type="hidden" id="party_name1" name="party_name1">   <input type="hidden" id="asc_code" name="asc_code" value="<?=$_SESSION['asc_code']?>">        </td>
        </tr>
            <tr>
              <td colspan="5" align="center"><input name="req" id="req" type="button" class="btn<?=$btncolor?>"   onClick="window.location='purchase_order_details.php?asc_code=<?=$_REQUEST['asc_code']?>'" value="<<Back">&nbsp;&nbsp;<input type="submit" class="btn<?=$btncolor?>"  value="Submit" name="b1" id="b1" tabindex="23"></td>
            </tr>
    </table>
</div>
</form>
</div>
</body>
</html>