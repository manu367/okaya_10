<?php
require_once("../includes/config.php");
/////////////////////////////// get info of vendor / billfrom/bill to /////////////////////////////////////////////////////////////////////////////////
$vendor_addrs  = explode('~' ,getAnyDetails($_REQUEST['vendor'],"address,gst_no,state","id","vendor_master",$link1));
$from  = explode("~",getAnyDetails($_REQUEST['bill_to'],"locationaddress,gstno,stateid","location_code" ,"location_master",$link1));
$to  = getAnyDetails($_REQUEST['ship_to'],"locationaddress","location_code","location_master",$link1);
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
/////get status//
@extract($_POST);
//////  if we want to Add new po
   if ($_POST['add']=='ADD'){
   ////// INITIALIZE PARAMETER/////////////////////////
   	mysqli_autocommit($link1, false);
	$flag = true;
   //// pick max count of grn
		$res_grncount = mysqli_query($link1,"SELECT grn_counter from invoice_counter where location_code='".$_SESSION['asc_code']."'");
		$row_grncount = mysqli_fetch_assoc($res_grncount);
	///// make grn sequence
		$nextgrnno = $row_grncount['grn_counter'] + 1;
		$grnno = "GRN".str_pad($nextgrnno,4,0,STR_PAD_LEFT);
		//// first update the job count
		$upd = mysqli_query($link1,"UPDATE invoice_counter set grn_counter='".$nextgrnno."' where location_code='".$_SESSION['asc_code']."'");
		//// check if query is not executed
		if (!$upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}

	/////////////////////////////// insert data into grn master  table///////////////////////////////////////////////
 		 $grn_master="insert into grn_master set location_code ='".$_POST['billto']."', party_code ='".$_POST['supplier']."' ,  receive_date='".$today."' , entry_date_time='".$datetime."' , status='4' , grn_no='".$grnno."' , remark='Add local GRN',comp_code='".$_SESSION['asc_code']."',update_by='".$_SESSION['userid']."',ip_address='".$_SERVER['REMOTE_ADDR']."'";
		$result5=mysqli_query($link1,$grn_master);
		//// check if query is not executed
		if (!$result5) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}

	////////////// insert data into billing master TABLE ///////////////////////////////////////////////
	$party_name  = getAnyDetails($_POST['supplier'] ,"name" ,"id" ,"vendor_master",$link1);
	$party_state  = getAnyDetails($_POST['ven_state'] ,"stateid" ,"state" ,"state_master",$link1);
	
 	 $bill_master="insert into billing_master set from_location ='".$_POST['supplier']."', to_location ='".$_POST['billto']."'  ,party_name ='".$party_name."' ,  challan_no='".$grnno."' ,sale_date='".$today."', entry_date='".$date."' , status='4' , from_gst_no='".$_POST['ven_gstno']."' , to_gst_no = '".$_POST['bill_gstno']."' , from_stateid = '".$party_state."'  , to_stateid= '".$_POST['bill_state']."'   ,po_type= 'Local GRN' ,total_cost = '".$_POST['grand_total']."'  ";
		$result6=mysqli_query($link1,$bill_master);
		//// check if query is not executed
		if (!$result6) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}	


	///// Insert in item data by picking each data row one by one
	foreach($prod_code as $k=>$val)
	{   
	    // checking row value of product and qty should not be blank
		if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0) {
			/////////// insert  GRN data
	    $query2="insert into grn_data set   grn_no  ='".$grnno."' ,product_id ='".$prod_code[$k]."', brand_id ='".$brand[$k]."', model_id ='".$model[$k]."', okqty='".$req_qty[$k]."'   ,price = '".$price[$k]."' , amount = '".$amount[$k]."'  ,type='Local GRN'  ";
		 $result = mysqli_query($link1, $query2);
		   //// check if query is not executed
		   if (!$result) {
	           $flag = false;
              $error_msg = "Error details: " . mysqli_error($link1) . ".";
           }
		   
		   $hsncode = getAnyDetails("$partcode[$k]" , "hsn_code" ,"partcode", "partcode_master" ,$link1);
		  $tax_info = mysqli_fetch_array(mysqli_query($link1, "select cgst,sgst, igst from tax_hsn_master where  hsn_code = '".$hsncode."' ")) ;
		   
		   /////////// insert  BILLING PRODUCT data
		   if($party_state == $_POST['bill_state'] ){
		   $cgstamt = ($tax_info[cgst]*$amount[$k])/100;
		   $sgstamt = ($tax_info[sgst]*$amount[$k])/100;
		   
		  $bill_data="insert into billing_product_items set  from_location='".$_POST['supplier']."'  ,to_location='".$_POST['billto']."' , challan_no  ='".$grnno."' ,product_id ='".$prod_code[$k]."', brand_id ='".$brand[$k]."', model_id ='".$model[$k]."', partcode ='".$partcode[$k]."', hsn_code= '".$hsncode."' ,cgst_per= '".$tax_info['cgst']."' ,sgst_per= '".$tax_info['sgst']."' , cgst_amt= '".$cgstamt ."' , sgst_amt= '".$sgstamt."'  , type='Local GRN' , price='".$price[$k]."' ,value ='".$amount[$k]."' , item_total = '".$amount[$k]."' ,qty ='".$req_qty[$k]."' ,okqty='".$req_qty[$k]."' ";
		 $result3 = mysqli_query($link1, $bill_data);
		  			 //// check if query is not executed
		   				if (!$result3) {
	         			  $flag = false;
              				$error_msg = "Error details: " . mysqli_error($link1) . ".";
           						}	    
		   }
		   else
		   {
		   $igstamt = ($tax_info[igst]*$amount[$k])/100;
		   
		  $bill_data="insert into billing_product_items set  from_location='".$_POST['supplier']."'  ,to_location='".$_POST['billto']."' , challan_no  ='".$grnno."' ,product_id ='".$prod_code[$k]."', brand_id ='".$brand[$k]."', model_id ='".$model[$k]."', partcode ='".$partcode[$k]."', hsn_code= '".$hsncode."' ,igst_per= '".$tax_info['igst']."'  , igst_amt= '".$igstamt ."'  , type='Local GRN' , price='".$price[$k]."' ,value ='".$amount[$k]."' , item_total = '".$amount[$k]."' ,qty ='".$req_qty[$k]."' ,okqty='".$req_qty[$k]."' ";
		 $result3 = mysqli_query($link1, $bill_data);
		  			 //// check if query is not executed
		   				if (!$result3) {
	         			  $flag = false;
              				$error_msg = "Error details: " . mysqli_error($link1) . ".";
           						}		   		   
		   }			   
		 
		   /////////////////////// check whether partcode and location code exist in client inventory or not //////////////////////
		$check = mysqli_query($link1 , "select location_code , partcode from client_inventory where location_code = '".$_POST['billto']."'  and partcode = '".$partcode[$k]."' ");
		if(mysqli_num_rows($check) >=1)
			{ 
			
		////////////// update  okqty in client inventory table //////////////////////////////////////////////////////////	 
	   $client   = mysqli_query($link1 , " update  client_inventory set okqty=okqty+'".$req_qty[$k]."' where partcode = '".$partcode[$k]."' and  location_code = '".$_POST['billto']."' "	);	   
		}
		else {
		////////////// insert  okqty in client inventory table //////////////////////////////////////////////////////////	 
	  $client   = mysqli_query($link1 , " insert into  client_inventory set okqty=okqty+'".$req_qty[$k]."' , partcode = '".$partcode[$k]."' ,  location_code = '".$_POST['billto']."',  	updatedate = '".$datetime."' ");	   
		
		}
			 //// check if query is not executed
		   if (!$client) {
	           $flag = false;
               echo "Error details: " . mysqli_error($link1) . ".";
          				 }	
						 
		/////////////////// insert in stock ledger////				 
		$flag=stockLedger($grnno,$today,$partcode[$k],$_POST['supplier'],$_POST['billto'],"IN","OK","Stock Receive","",$req_qty[$k],$price[$k],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);   
		   	   
		}// close if loop of checking row value of product and qty should not be blank
	}/// close for loop
	///// check both master and data query are successfully executed
	if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Local GRN  is successfully placed with ref. no.".$grnno;
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again." .$error_msg ;
	} 
    mysqli_close($link1);
	   ///// move to parent page
  header("location:grn_local.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 
 <script language="javascript" type="text/javascript">
  
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
	  var model=document.getElementById("model["+indx+"]").value;
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{modelinfo:model,indxx:indx},
		success:function(data){
		var getValue = data.split("~");
		document.getElementById("partcodediv"+getValue[1]).innerHTML=getValue[0];
	    }
	  });
  }
  
$(document).ready(function(){
     $("#add_row").click(function(){
		var numi = document.getElementById('rowno');
		var preno=document.getElementById('rowno').value;
		var num = (document.getElementById("rowno").value -1)+2;
		numi.value = num;
     var r='<tr id="addr'+num+'"><td ><span id="pdtid'+num+'"><select name="prod_code['+num+']" id="prod_code['+num+']" class="form-control required" required><option value="">--None--</option><?php $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['product_id'];?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option><?php }?></select></span></td><td><select name="brand['+num+']" id="brand['+num+']" class="form-control required" onChange="getmodel('+num+')" required><option value="">--Select Brand--</option><?php $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";$check_dept=mysqli_query($link1,$dept_query);while($br_dept = mysqli_fetch_array($check_dept)){?><option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option><?php }?></select></td><td  ><span id="modeldiv'+num+'"><select name="model['+num+']" id="model['+num+']" class="form-control required"  onChange="getpartcode('+num+')" required><option value="" selected="selected"> Select Model</option></select></span></td><td ><span id="partcodediv'+num+'"><select name="partcode['+num+']" id="partcode['+num+']" class="form-control required" required><option value="" selected="selected"> Select Partcode</option></select></span></td><td><input type="text" class="form-control digits" name="req_qty['+num+']" id="req_qty['+num+']"  autocomplete="off" required  onKeyPress="return onlyNumbers(this.value);" onBlur="get_tot('+num+')"></td><td ><input type="text" class="form-control digits" name="price['+num+']" id="price['+num+']"  autocomplete="off" required  onKeyPress="return onlyNumbers(this.value);" onBlur="get_tot('+num+')"></td><td ><input type="text" class="form-control" name="amount['+num+']" id="amount['+num+']"  autocomplete="off" style="width:13'+num+'px;" value="" readonly></td></tr>';
      $('#itemsTable1').append(r);
	
  });
});

/////////// function to get amount
  function get_tot(indx){
  //////////////////////////// getting row wise amount  by multiplying price and qty////////////////////////////////////////
	  var qty=document.getElementById("req_qty["+indx+"]").value;
	  var price=document.getElementById("price["+indx+"]").value;          
	  if (qty != '' && price != '')
		{
		var amt = qty *price ;
	   document.getElementById("amount["+indx+"]").value=amt;
		}	
		get_cal();	
  }
///////////////////////////
function get_cal(){
 var rowno1 = (document.getElementById("rowno").value); 
 var sum =0;
 var pricesum =0;
  var total =0;
 ////////////// calculating sum of totalqty, subtotal, amount///////////////////////////////	
		for (var i = 0; i <=rowno1; i++) {
		var temp = "req_qty["+i+"]";	
		var price = "price["+i+"]";	
		var amt = "amount["+i+"]";	
		sum += parseInt(document.getElementById(temp).value);	
		pricesum += parseInt(document.getElementById(price).value);	
		total += parseInt(document.getElementById(amt).value);	
		}
		document.getElementById("total_qty").value =sum;
		document.getElementById("sub_total").value =pricesum;
		document.getElementById("grand_total").value =total;

}

  </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script src="../js/frmvalidate.js"></script>
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
      <h2 align="center"><i class="fa fa-arrows-alt"></i> Add Local GRN </h2><br/>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
         <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Supplier Name</label>	  
			<div class="col-md-6" >
				<select   name="vendor" id="vendor" class="form-control" onChange="document.frm1.submit();">
				<option value=''>--Please Select--</option>
				<?php
               $vendor_query="select name,id from vendor_master where status='1' and vendor_orign = 'Domestic' ";
			        $check1=mysqli_query($link1,$vendor_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['id']?>" <?php if($_REQUEST['vendor'] == $br['id']) { echo 'selected'; }?>><?=$br['name']." | ".$br['id']?></option>
                <?php } ?>
	</select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Supplier Address</label>	  
			<div class="col-md-5" id="venaddress">
                 <input id="ven_addrs" name="ven_addrs" value="<?=$vendor_addrs[0];?>" type="text" class="form-control required">
              </div>
          </div>
	    </div>
          <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Bill To</label>	  
			<div class="col-md-6" >
				 <select name="bill_to" id="bill_to" class="form-control required"  onChange="document.frm1.submit();">
                <option value="">Please Select</option>
                <?php
                $map_wh = mysqli_query($link1,"select locationname, location_code from location_master where locationtype='WH'"); 
                while( $location = mysqli_fetch_assoc($map_wh)){
				
				?>
                <option value="<?=$location['location_code']?>" <?php if($_REQUEST['bill_to'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Billing Address</label>	  
			<div class="col-md-5">
                 <input id="from_addrs" name="from_addrs" value="<?=$from[0];?>" type="text" class="form-control required">
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"> Ship to:</label>	  
			<div class="col-md-6" >
            
    
				 <select name="ship_to" id="ship_to" class="form-control required" onChange="document.frm1.submit();" >
                <option value="">Please Select</option>
                <?php
                $map_wh = mysqli_query($link1,"select locationname, location_code from location_master where locationtype='WH'"); 
                while($location = mysqli_fetch_array($map_wh)){
				
				?>
                <option value="<?=$location['location_code']?>" <?php if($_REQUEST['ship_to'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
                <?php } ?>
                 </select>
              </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Shipping Address:</label>	  
			<div class="col-md-5" >
                  <input id="to_addrs" name="to_addrs" value="<?=$to;?>" type="text" class="form-control required">
              </div>
          </div>
	    </div>
         </form>
         <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
          <div class="form-group">
           <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
            <thead>
              <tr>
               <th data-class="expand" class="col-md-3" style="font-size:13px;">Product</th>
				<th data-hide="phone"  class="col-md-2" style="font-size:13px">Brand</th>
                <th data-hide="phone"  class="col-md-2" style="font-size:13px">Model</th>
				<th data-hide="phone"  class="col-md-2" style="font-size:13px">Partcode</th>
                <th class="col-md-1" style="font-size:13px">Qty</th>  
				 <th class="col-md-2" style="font-size:13px">Price</th>    
                <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Amount</th>
              </tr>
			    </thead>
            <tbody>
              <tr id='addr0'>
                <td class="col-md-2"><span id="pdtid0">
                  <select name="prod_code[0]" id="prod_code[0]" class="form-control required" required>
                    <option value="">Select Product</option>
                    <?php 
					$model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option data-tokens="<?php echo $br['product_id'];?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option>
                    <?php }?>
                  </select></span></td>
				  <td class="col-md-2"><select name="brand[0]" id="brand[0]" class="form-control required" onChange="getmodel(0)" required>
                      <option value=''>--Select Brand--</option>
                      <?php
                        $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";
                        $check_dept=mysqli_query($link1,$dept_query);
                        while($br_dept = mysqli_fetch_array($check_dept)){
                      ?>
                      <option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>
                    <?php }?>	
                    </select></td>
                <td class="col-md-2" ><span id="modeldiv0"><select name="model[0]" id="model[0]" class="form-control required"  onChange="getpartcode(0)" required><option value="" selected="selected"> Select Model</option></select></span></td>
				<td class="col-md-2"><span id="partcodediv0"><select name="partcode[0]" id="partcode[0]" class="form-control required"  required ><option value="" selected="selected"> Select Partcode</option></select></span></td>
                <td class="col-md-1"><input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]"  autocomplete="off" required  onKeyPress="return onlyNumbers(this.value);" onBlur="get_tot(0)"></td>
				<td class="col-md-3"><input type="text" class="form-control digits" name="price[0]" id="price[0]"  autocomplete="off" required  onKeyPress="return onlyNumbers(this.value);" onBlur="get_tot(0)"></td>
                <td class="col-md-2"><input type="text" class="form-control" name="amount[0]" id="amount[0]"  autocomplete="off" style="width:130px;" value="" readonly >                                  
                </td>
              </tr>
            </tbody>
            <tfoot id='productfooter' style="z-index:-9999;">
              <tr class="0">
                <td colspan="7" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
              </tr>
            </tfoot>
          </table>
          </div>
		  <div class="form-group">
            <div class="col-md-6">
              <label class="col-md-3 control-label">Total Qty</label>
              <div class="col-md-3">
              <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly/>
              </div>
              <label class="col-md-3 control-label">Sub Total</label>
              <div class="col-md-3">
                <input type="text" name="sub_total" id="sub_total" class="form-control" value="0.00" readonly/>
              </div>
            </div>
			<div class="col-md-6">
			 <label class="col-md-3 control-label">Grand Total</label>
              <div class="col-md-3">
              <input type="text" name="grand_total" id="grand_total" class="form-control" value="0.00" readonly/>
              </div>
			  </div>
          </div>
          <div class="form-group">
            <div class="col-md-10">
              <label class="col-md-3 control-label">Delivery Address <span style="color:#F00">*</span></label>
              <div class="col-md-3">
                <textarea name="delivery_address" id="delivery_address" class="form-control " style="resize:none" required></textarea>
              </div>
              <label class="col-md-3 control-label">Remark</label>
              <div class="col-md-3">
                <textarea name="remark" id="remark" class="form-control" style="resize:none"></textarea>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">     
              <input type="submit" class="btn btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New PO">
                <input type="hidden" name="billto" id="billto" value="<?=$_REQUEST['bill_to']?>"/>
				   <input type="hidden" name="supplier" id="supplier" value="<?=$_REQUEST['vendor']?>"/>
				   <input type="hidden" name="ven_gstno" id="ven_gstno" value="<?=$vendor_addrs[1]; ?>"/>
				   <input type="hidden" name="ven_state" id="ven_state" value="<?=$vendor_addrs[2];?>"/>
				   <input type="hidden" name="bill_gstno" id="bill_gstno" value="<?=$from[1]; ?>"/>
				   <input type="hidden" name="bill_state" id="bill_state" value="<?=$from[2];?>"/>
              <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='wh_local_GRN.php?<?=$pagenav?>'">
            </div>
          </div>
         </form>
      </div>
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>