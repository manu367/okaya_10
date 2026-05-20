<?php
require_once("../includes/config.php");
/////////////////////////////// get address of vendor / billfrom/bill to /////////////////////////////////////////////////////////////////////////////////
$vendor_addrs  = getAnyDetails($_REQUEST['vendor'],"address","id","vendor_master",$link1);
$from  = getAnyDetails($_REQUEST['bill_from'],"locationaddress","location_code","location_master",$link1);
$to  = getAnyDetails($_REQUEST['bill_to'],"locationaddress","location_code","location_master",$link1);

////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
/////get status//
@extract($_POST);
//////  if we want to Add new po
if ($_POST['add']=='ADD' && $_SESSION['asc_code']!=''){  
	mysqli_autocommit($link1, false);
	$flag = true;
	
	$len = count($_POST['partcode']);
	if($len>0){
		/////// genrate challan //////
		$res_po = mysqli_query($link1,"select max(ch_temp) as no from supplier_po_master where location_code='".$_SESSION['asc_code']."'");
		$row_po = mysqli_fetch_array($res_po);
		$c_nos = $row_po[no]+1;
		$po_no = $_SESSION['asc_code']."V".$c_nos; 
		
		////// insert in master table ///////
		$po_add = "INSERT INTO supplier_po_master set system_ref_no = '".$po_no."' , entry_date = '".$today."' , location_code = '".$_SESSION['asc_code']."' , ship_address2 = '".$to_add1."', party_name = '".$supplier."' , bill_to = '".$billto."' , ch_temp='".$c_nos."' , bill_address ='".$fromadd."' , status='7' , po_type = 'PTV', comp_code = '".$billto."' , user_code = '".$supplier."' , remark = '".$remark."' ";
		
		$result=mysqli_query($link1,$po_add);
		//// check if query is not executed
		if (!$result) {
			$flag = false;
			$error_msg = "Error details 1 : " . mysqli_error($link1) . ".";
		}
		
		////// pick all parts and insert in data table
		for($i = 0; $i < $len; $i++){
			$prod_info = $_POST['prod_code'][$i];
			$brand_info = $_POST['brand'][$i];
			$model_info = $_POST['model'][$i];
			$part_info = $_POST['partcode'][$i];
			$req_qty_info = $_POST['req_qty'][$i];
			$price_info = $_POST['price'][$i];
			$total_info = $_POST['rowsubtotal'][$i];
			
			if($part_info!='' && $prod_info!='' && $brand_info!=''){
				 if($req_qty_info > 0){
			$po_data_add = "insert into supplier_po_data set location_code  ='".$_SESSION['asc_code']."' , system_ref_no='".$po_no."',product_id ='".$prod_info."', brand_id ='".$brand_info."',model_id='".$model_info."', partcode ='".$part_info."', qty='".$req_qty_info."' ,req_qty='".$req_qty_info."'  ,price = '".$price_info."', cost='".$total_info."',total_cost='".$total_info."'  ,entry_date = '".$today."' ,status='7',flag='1'  ";
			$result1 = mysqli_query($link1,$po_data_add);
			//// check if query is not executed
			if (!$result1) {
				$flag = false;
				$error_msg = "Error details 2 : " . mysqli_error($link1) . ".";
			}
			
				 }//////////qty check
				 else {
					 $flag = false;
					 $error_msg = "QTY is Zero for this partoce - ".$part_info." .Please try Again";
					 }
			
			}///////part details check
			else {
				$flag = false;
				$error_msg = "Part Details is empty Please try Again";
				}
			
		}///// end of for loop
	}//// end of div length
	else {
		$flag = false;
		$error_msg = "Atleast one Item Select";
		}
	

	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "PO to Vendor  is successfully placed with ref. no.".$po_no;
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again." .$error_msg ;
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
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
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
	function makeDropdown(){
		$('.selectpicker').selectpicker();
   }
</script> 
 <script language="javascript" type="text/javascript">
 
 //////////Function to product blank all fileds
 function fun_product(indx){
	 document.getElementById("add").style.visibility = "";
	 document.getElementById("brand["+indx+"]").value = "";
	 document.getElementById("model["+indx+"]").value = "";
	 document.getElementById("partcode["+indx+"]").value = "";
	 document.getElementById("req_qty["+indx+"]").value = "";
  }
  //////////////////////// function to get model on basis of model dropdown selection///////////////////////////
 function getmodel(indx){
	 document.getElementById("add").style.visibility = "";
	  var brandid=document.getElementById("brand["+indx+"]").value;
	  var productCode=document.getElementById("prod_code["+indx+"]").value;
	  document.getElementById("partcode["+indx+"]").value = "";
	  document.getElementById("req_qty["+indx+"]").value = "";
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{brandinfo:brandid,productinfo:productCode,indxx:indx},
		success:function(data){
		var getValue = data.split("~");
		document.getElementById("modeldiv"+getValue[1]).innerHTML=getValue[0];
			makeDropdown();
	    }
	  });
  }
  function getpartcode(indx){
	  document.getElementById("add").style.visibility = "";
	  var model=document.getElementById("model["+indx+"]").value;
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{modelinfogrn:model,indxx:indx},
		success:function(data){
		var getValue = data.split("~");
		document.getElementById("partcodediv"+getValue[1]).innerHTML=getValue[0];
			makeDropdown();
	    }
	  });
  }
   function getAvlStk(indx){
	   document.getElementById("add").style.visibility = "";
	  var productCode=document.getElementById("partcode["+indx+"]").value;
		////// get part price and tax details
	  $.ajax({
		type:'post',
		url:'../includes/getAzaxFields.php',
		data:{partpricetax:productCode},
		success:function(data){
			var getpartdet=data.split("~");
			if(getpartdet[6]!=""){
				document.getElementById("price["+indx+"]").value=getpartdet[6];
				get_tot(indx);
			}
			else{
				document.getElementById("price["+indx+"]").value=0.00;
				get_tot(indx);
			}
		}
	  });
  }
$(document).ready(function(){
	document.getElementById("add").style.visibility = "";
     $("#add_row").click(function(){
		var numi = document.getElementById('rowno');
		var itm = "partcode["+numi.value+"]";
		var preno=document.getElementById('rowno').value;
		var num = (document.getElementById("rowno").value -1)+2;
		numi.value = num;
		if(document.getElementById(itm).value != ""){
     		var r='<tr id="adrw'+num+'"><td><span id="pdtid'+num+'"><select name="prod_code['+num+']" id="prod_code['+num+']"  onChange="fun_product('+num+')" class="form-control required selectpicker" data-live-search="true" required><option value="">Select Product</option><?php $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?=$br['product_name']." | ".$br['product_id']?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option><?php }?></select></span></td><td><select name="brand['+num+']" id="brand['+num+']" class="form-control required selectpicker" onChange="getmodel('+num+')" required><option value="">Select Brand</option><?php $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";$check_dept=mysqli_query($link1,$dept_query);while($br_dept = mysqli_fetch_array($check_dept)){?><option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option><?php }?></select></td><td><span id="modeldiv'+num+'"><select name="model['+num+']" id="model['+num+']" class="form-control required"  onChange="getpartcode('+num+')" required><option value="" selected="selected">Select Model</option></select></span></td><td><span id="partcodediv'+num+'"><select name="partcode['+num+']" id="partcode['+num+']" onChange="getAvlStk('+num+'); checkDuplicate(' + num + ',this.value);" class="form-control required" required><option value="" selected="selected">Select Partcode</option></select></span></td><td><input type="text" class="form-control digits" name="req_qty['+num+']" id="req_qty['+num+']"  autocomplete="off" style="width:100px;text-align:right;padding: 4px" required onKeyUp="get_tot('+num+')"></td><td ><input type="text" class="form-control " name="price['+num+']" id="price['+num+']" style="width:100px;text-align:right;padding: 4px" autocomplete="off" required readonly onKeyUp="get_tot('+num+')"></td><td><input type="text" class="form-control" name="rowsubtotal['+num+']" id="rowsubtotal['+num+']" value="0" style="width:100px;text-align:right;padding: 4px" readonly><div style="display:inline-block;float:left;"><i class="fa fa-close fa-lg" onClick="fun_remove('+num+');"></i></div></td></tr>';
      	  $('#itemsTable1').append(r);
			makeDropdown();
	  }
  });
});

/////////// function to get amount
function get_tot(indx){
	document.getElementById("add").style.visibility = "";
//////////////////////////// getting row wise amount  by multiplying price and qty////////////////////////////////////////
	if(document.getElementById("req_qty["+indx+"]").value){ var qty=document.getElementById("req_qty["+indx+"]").value;}else{ var qty=0;}
	if(document.getElementById("price["+indx+"]").value){ var price=document.getElementById("price["+indx+"]").value;}else{ var price=0.00;}
	//// calculate subtotal
	var amt = parseFloat(qty) * parseFloat(price) ;
	///// total line amount
	document.getElementById("rowsubtotal["+indx+"]").value = amt;
	get_cal();	
}
///////////////////////////
function get_cal(){
var rowno1 = (document.getElementById("rowno").value); 
var grandtotal = 0.00;
var qtytotal = 0;
	////////////// calculating sum of totalqty, subtotal, amount///////////////////////////////	
	for (var i = 0; i <=rowno1; i++) {
		var line_reqqty = "req_qty["+i+"]";	
		var line_amt = "rowsubtotal["+i+"]";	
		grandtotal += parseInt(document.getElementById(line_amt).value);	
		qtytotal += parseInt(document.getElementById(line_reqqty).value);
	}
	document.getElementById("total_qty").value = qtytotal;
	document.getElementById("grand_total").value = grandtotal;
}
/////// row remove ////////////
function fun_remove(con){
	var c = document.getElementById('adrw' + con);
	c.parentNode.removeChild(c);
	con--;
	document.getElementById('rowno').value = con;
}
////////// check duplicate parts ////
function checkAllRows(){
	document.getElementById("add").style.visibility = "hidden";
	var rowno1 = document.getElementById("rowno").value;
	var flag=1;
	if(rowno1 > 0){
		for(var j=0;j<rowno1;j++){
        	var check2="partcode["+j+"]";
				for(var i=0;i<=rowno1;i++){
					if(j!=i){
						var check1="partcode["+i+"]";
						var rqty="req_qty["+i+"]";
						var bqty="brand["+i+"]";
						var mqty="model["+i+"]";
						var pqty="price["+i+"]";
						var rowqty="rowsubtotal["+i+"]";
		 				if(document.getElementById(check2).value==document.getElementById(check1).value){
			 				document.getElementById("error_msg").innerHTML="Duplicate Part selection";
							document.getElementById(check1).value="";
							document.getElementById(rqty).value="";
							document.getElementById(bqty).value="";
							document.getElementById(mqty).value="";
							document.getElementById(pqty).value="";
							document.getElementById(rowqty).value="";
							
							flag*=0;
						}
		 				else{
			 			//document.getElementById(defQty).value='1';
						flag*=1;
						}
					}
				}
		}
		//////////
		if(flag==0){ 
			return false;
		 }else{ 
			return true;
		 }
		}
		
}

  ///// function for checking duplicate Product value
            function checkDuplicate(fldIndx1, enteredsno) { 
			document.getElementById("add").style.visibility = "";		 
			 document.getElementById("add").disabled = false;
                if (enteredsno != '') {
                    var check2 = "partcode[" + fldIndx1 + "]";
                    var flag = 1;
                    for (var i = 0; i <= fldIndx1; i++) {
                        var check1 = "partcode[" + i + "]";
                        if (fldIndx1 != i && document.getElementById(check2).value != '' && document.getElementById(check1).value != '') {
                            if ((document.getElementById(check2).value == document.getElementById(check1).value)) {
                                alert("Duplicate Partcode Selection.");
                                document.getElementById(check2).value = '';
                                document.getElementById(check2).style.backgroundColor = "#F66";
                                flag *= 0;
                            }
                            else {
                                document.getElementById(check2).style.backgroundColor = "#FFFFFF";
                                flag *= 1;
                                ///do nothing
                            }
                        }
                    }//// close for loop
                    if (flag == 0) {
                        return false;
                    } else {
                        return true;
                    }
                }
				
            }
  

  </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
	<link rel="stylesheet" href="../css/bootstrap-select.min.css">
	<script src="../js/bootstrap-select.min.js"></script>
 <style type="text/css">
 .custom_label {
	 text-align:left;
	 vertical-align:middle
 }
 </style>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
include("../includes/leftnavemp2.php");
    ?>
     <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-ship"></i> Add New PO </h2><br/>
	 
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
         <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Supplier Name</label>	  
			<div class="col-md-6" >
				<select   name="vendor" id="vendor" class="form-control required selectpicker" 
data-live-search="true" onChange="document.frm1.submit();">
					<option value="" <?php if($_REQUEST['vendor'] == "") { echo 'selected'; }?> > Please Select </option>
					<?php
				   		$vendor_query="select name,id from vendor_master where status='1'";
						$check1=mysqli_query($link1,$vendor_query);
					while($br = mysqli_fetch_array($check1)){?>
					<option value="<?=$br['id']?>" <?php if($_REQUEST['vendor'] == $br['id']) { echo 'selected'; }?>><?=$br['name']." | ".$br['id']?></option>
					<?php } ?>
				</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Supplier Address</label>	  
			<div class="col-md-6" id="venaddress">
                 <textarea id="ven_addrs" name="ven_addrs" style="resize:vertical" class="form-control required"><?=$vendor_addrs;?></textarea>
              </div>
          </div>
	    </div>
          <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Bill To</label>	  
			<div class="col-md-6" >
				 <select name="bill_from" id="bill_from" class="form-control required selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                <option value="" > Please Select </option>
                <?php
                $map_wh = mysqli_query($link1,"select locationname, location_code from location_master where locationtype IN ('WH','ASP') and location_code='".$_SESSION['asc_code']."'"); 
                while( $location = mysqli_fetch_assoc($map_wh)){
				
				?>
                <option data-tokens="<?=$location['locationname']." | ".$location['location_code']?>" value="<?=$location['location_code']?>" <?php if($_REQUEST['bill_from'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Billing Address</label>	  
			<div class="col-md-6">
                 <textarea id="from_addrs" name="from_addrs" style="resize:vertical" class="form-control required"><?=$from;?></textarea>
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"> Ship to:</label>	  
			<div class="col-md-6" >
            
    
				 <select name="bill_to" id="bill_to" class="form-control required 
selectpicker" data-live-search="true" onChange="document.frm1.submit();" >
                <option value="" > Please Select </option>
                <?php
                $map_wh1 = mysqli_query($link1,"select locationname, location_code from location_master where locationtype IN ('WH','ASP') and location_code='".$_SESSION['asc_code']."' "); 
                while($location1 = mysqli_fetch_array($map_wh1)){
				
				?>
                <option data-tokens="<?=$location1['locationname']." | ".$location1['location_code']?>" value="<?=$location1['location_code']?>" <?php if($_REQUEST['bill_to'] == $location1['location_code']) { echo 'selected'; }?>><?=$location1['locationname']." (".$location1['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Shipping Address:</label>	  
			<div class="col-md-6" >
                  <textarea id="to_addrs" name="to_addrs" class="form-control required" style="resize:vertical"><?=$to;?></textarea>
              </div>
          </div>
	    </div>
         </form>
          <h4 align="center"><span id="error_msg" class="red_small" style="text-align:center;margin:10px;"></span></h4>
<form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
<div class="form-group">
<table width="100%" id="itemsTable1" class="table table-bordered table-hover">
	<thead>
    	<tr id='adhd0' class="<?=$tableheadcolor?>">
        	<th class="col-md-2" style="font-size:13px;">Product</th>
            <th class="col-md-2" style="font-size:13px">Brand</th>
            <th class="col-md-2" style="font-size:13px">Model</th>
            <th class="col-md-2" style="font-size:13px">Partcode</th>
			<th class="col-md-1" style="width:100px;font-size:13px">Qty</th>  
            <th class="col-md-1" style="font-size:13px">Price</th>
            <th class="col-md-2" style="font-size:13px">SubTotal</th>
		</tr>
	</thead>
    <tbody>
    	<tr id='adrw0'>
        	<td class="col-md-2">
            	<span id="pdtid0">
                <select name="prod_code[0]" id="prod_code[0]" class="form-control required selectpicker" data-live-search="true" onChange="fun_product(0)"  required>
                	<option value="">Select Product</option>
                    <?php 
					$model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option data-tokens="<?=$br['product_name']." | ".$br['product_id']?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option>
                    <?php }?>
                </select>
                </span>
            </td>
			<td class="col-md-2">
            	<select name="brand[0]" id="brand[0]" class="form-control required selectpicker" onChange="getmodel(0)" required>
                	<option value=''>Select Brand</option>
                    <?php
					$dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){?>
                    <option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
                    <?php }?>	
                </select>
            </td>
            <td class="col-md-2">
            	<span id="modeldiv0">
                <select name="model[0]" id="model[0]" class="form-control required"  onChange="getpartcode(0)" required>
                	<option value="" selected="selected"> Select Model</option>
                </select>
                </span>
            </td>
			<td class="col-md-2">
            	<span id="partcodediv0">
                <select name="partcode[0]" id="partcode[0]" class="form-control required"  required onChange="getAvlStk(0); checkDuplicate(0, this.value);">
                	<option value="" selected="selected"> Select Partcode</option>
                </select>
                </span>
            </td>
			<td class="col-md-1">
            	<input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]"  autocomplete="off" required onKeyUp="get_tot(0);" style="width:100px;text-align:right;padding: 4px">
            </td>
			<td class="col-md-1">
            	<input type="text" class="form-control " name="price[0]" id="price[0]"  autocomplete="off" required readonly onKeyUp="get_tot(0);" style="width:100px;text-align:right;padding: 4px">
            </td>
			<td class="col-md-2">
            	<input type="text" class="form-control" name="rowsubtotal[0]" id="rowsubtotal[0]" value="0" style="width:100px;text-align:right;padding: 4px" readonly>
            </td>
        </tr>
	</tbody>
    <tfoot id='productfooter' style="z-index:-9999;">
    	<tr class="0">
        	<td colspan="7" style="font-size:13px;">
            <?php  if ($_REQUEST['bill_to'] != '' && $_REQUEST['bill_from'] != '' && $_REQUEST['vendor'] != '') { ?>
            	<a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/>
            <?php }?>    
            </td>
        </tr>
    </tfoot>
</table>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Total Qty</label>
              <div class="col-md-3">
                <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly/>
              </div>
			  <label class="col-md-3 control-label">Grand Total</label>
              <div class="col-md-3">
                <input type="text" name="grand_total" id="grand_total" class="form-control" value="0.00" readonly/>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
			<!-------------
              <label class="col-md-3 control-label">Delivery Address</label>
              <div class="col-md-3">
                <textarea name="delivery_address" id="delivery_address" class="form-control" required style="resize:none" ></textarea>
              </div>------------>
              <label class="col-md-3 control-label">Remark</label>
              <div class="col-md-3">
                <textarea name="remark" id="remark" class="form-control" style="resize:none"></textarea>
              </div>
			  <label class="col-md-3 control-label"></label>
              <div class="col-md-3">
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">     
              <input type="submit" class="btn btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New PO" onClick="checkAllRows();" >
			  <input type="hidden" name="billto" id="billto" value="<?=$_REQUEST['bill_to']?>"/>
              <input type="hidden" name="billfrom" id="billfrom" value="<?=$_REQUEST['bill_from']?>"/>
			  <input type="hidden" name="supplier" id="supplier" value="<?=$_REQUEST['vendor']?>"/>
              <input type="hidden" name="fromadd" id="fromadd" value="<?=$from?>"/>
			  <input type="hidden" name="to_add1" id="to_add1" value="<?=$to?>"/>
              <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='grn_vendor.php?<?=$pagenav?>'">
            </div>
          </div>
         </form>
      </div>

    </div>
  </div>
</div>
<?php 
if ($_REQUEST['bill_to'] == '' || $_REQUEST['bill_from'] == '' || $_REQUEST['vendor'] == '') { ?>
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