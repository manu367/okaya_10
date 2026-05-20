<?php
require_once("../includes/config.php");
/////////////////////////////// get info of vendor / billfrom/bill to /////////////////////////////////////////////////////////////////////////////////
$vendor_addrs  = explode('~' ,getAnyDetails($_REQUEST['vendor'],"address,gst_no,state","id","vendor_master",$link1));
$from  = explode("~",getAnyDetails($_SESSION['asc_code'],"locationaddress,gstno,stateid","location_code" ,"location_master",$link1));
$to  = getAnyDetails($_REQUEST['ship_to'],"deliveryaddress","location_code","location_master",$link1);
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
/////get status//
@extract($_POST);
//////  if we want to Add new po
   if ($_POST['add']=='Issue'){
   ////// INITIALIZE PARAMETER/////////////////////////
   	mysqli_autocommit($link1, false);
	$flag = true;
	$error_msg = "";
   //// pick max count of grn
		$res_grncount = mysqli_query($link1,"SELECT fy,stn_counter,stn_series  from invoice_counter where location_code='".$_SESSION['asc_code']."'");
		$row_grncount = mysqli_fetch_assoc($res_grncount);
	///// make grn sequence
		$nextgrnno = $row_grncount['stn_counter'] + 1;
		$grnno=$row_grncount['stn_series']."".$row_grncount['fy']."".str_pad($nextgrnno,4,"0",STR_PAD_LEFT);
		//// first update the job count
		$upd = mysqli_query($link1,"UPDATE invoice_counter set stn_counter ='".$nextgrnno."' where location_code='".$_SESSION['asc_code']."'");
		//// check if query is not executed
		if (!$upd) {
			 $flag = false;
			 $error_msg = "Error details1: " . mysqli_error($link1) . ".";
		}
		/////////////////////////////// insert data into grn master  table///////////////////////////////////////////////

		////////////// insert data into billing master TABLE ///////////////////////////////////////////////
		$party_name  = getAnyDetails($_POST['supplier'] ,"name" ,"id" ,"vendor_master",$link1);
		$party_state  = getAnyDetails($_POST['ven_state'] ,"stateid" ,"state" ,"state_master",$link1);
		
		if($_POST['billto']==""){
		 $flag = false;
			 $error_msg = "Engineer Name Blank";
		}
			
 	 	$bill_master="insert into stn_master set from_location ='".$_POST['supplier']."', to_location ='".$_POST['billto']."'  ,party_name ='".$party_name."' ,  challan_no='".$grnno."' ,sale_date='".$today."', entry_date='".$date."' , status='4'  , from_stateid = '".$_POST['bill_state']."'  , to_stateid= '".$_POST['bill_state']."'   ,po_type= 'ISSUE-TO-ENG' ,total_cost = '".$_POST['grand_total']."', from_addrs='".$_POST['bill_addrs']."', disp_addrs='".$_POST['bill_addrs']."', to_addrs='".$_POST['bill_addrs']."', deliv_addrs='".$_POST['bill_addrs']."',po_no='".$_POST['job_no']."', 	document_type='DC',billing_rmk='".$_POST['remark']."' ";
		$result6=mysqli_query($link1,$bill_master);
		//// check if query is not executed
		if (!$result6) {
			 $flag = false;
			 $error_msg = "Error details3: " . mysqli_error($link1) . ".";
		}
		///// Insert in item data by picking each data row one by one
		foreach($prod_code as $k=>$val){   
	    	// checking row value of product and qty should not be blank
			if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0) {
			$partdet = explode("~",getAnyDetails($partcode[$k] , "hsn_code,part_name" ,"partcode", "partcode_master" ,$link1));
		  	$tax_info = mysqli_fetch_array(mysqli_query($link1, "select cgst,sgst, igst from tax_hsn_master where  hsn_code = '".$partdet[0]."' ")) ;	
			/////////// insert  GRN data
		  
		  		$bill_data="insert into stn_items set  from_location='".$_POST['supplier']."'  ,to_location='".$_POST['billto']."' , challan_no  ='".$grnno."' ,product_id ='".$prod_code[$k]."', brand_id ='".$brand[$k]."', model_id ='".$model[$k]."', partcode ='".$partcode[$k]."', part_name='".$partdet[1]."', hsn_code= '".$partdet[0]."' ,igst_per= '".$tax_info['igst']."'  , igst_amt= '".$igstamt ."'  , type='ISSUE-TO-ENG' , price='".$price[$k]."' ,value ='".$amount[$k]."' , item_total = '".$amount[$k]."' ,qty ='".$req_qty[$k]."' ,okqty='".$req_qty[$k]."',job_no='".$_POST['job_no']."' ";
		 		$result3 = mysqli_query($link1, $bill_data);
		  		//// check if query is not executed
		   		if (!$result3) {
	         		$flag = false;
              		$error_msg = "Error details6: " . mysqli_error($link1) . ".";
				}	
				///////////////////////////update location inventory///////////////////////////////
											$result3 = mysqli_query($link1, "UPDATE client_inventory set okqty = okqty-'" .$req_qty[$k]. "',updatedate='" . $datetime . "' where location_code='" . $_POST['supplier']. "' and partcode='" . $partcode[$k]. "' and  okqty >= '".$req_qty[$k]."'");
							//// check if query is not executed
							if (!$result3) {
								$flag = false;
								$err_msg = "Error Code5: ".mysqli_error($link1);
							}	   		   
		
		   	/////////////////////// check whether partcode and location code exist in user inventory or not //////////////////////
			$check = mysqli_query($link1 , "select location_code , partcode from user_inventory where location_code = '".$_POST['supplier']."' and   locationuser_code ='".$_POST['billto']."' and partcode = '".$partcode[$k]."' ");
			if(mysqli_num_rows($check)>0){ 
				////////////// update  okqty in client inventory table //////////////////////////////////////////////////////////	 
	   			$client   = mysqli_query($link1 , " update  user_inventory set okqty=okqty+'".$req_qty[$k]."' where partcode = '".$partcode[$k]."' and  location_code = '".$_POST['supplier']."' and   locationuser_code ='".$_POST['billto']."'  "	);	   
			}
			else {
				////////////// insert  okqty in client inventory table //////////////////////////////////////////////////////////	 
	  			$client   = mysqli_query($link1 , " insert into  user_inventory set okqty=okqty+'".$req_qty[$k]."' , partcode = '".$partcode[$k]."' ,  location_code = '".$_POST['supplier']."',  	updatedate = '".$datetime."',  locationuser_code ='".$_POST['billto']."' ");	   
			}
			
				//$client   = mysqli_query($link1 , " update  client_inventory set mount_qty=mount_qty+'".$req_qty[$k]."' where partcode = '".$partcode[$k]."' and  location_code='" . $_POST['supplier']. "'   "	);	  
			//// check if query is not executed
		   	if (!$client) {
	        	$flag = false;
               	$error_msg = "Error details7: " . mysqli_error($link1) . ".";
			}			 
			/////////////////// insert in stock ledger////				 
			$flag=stockLedger($grnno,$today,$partcode[$k],$_POST['supplier'],$_POST['billto'],"OUT","OK","Issue To Eng","",$req_qty[$k],$price[$k],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);   
				$flag=stockLedger($grnno,$today,$partcode[$k],$_POST['supplier'],$_POST['billto'],"IN","OK","Recieve Stock From Location","",$req_qty[$k],$price[$k],$_POST['billto'],$today,$currtime,$ip,$link1,$flag);   
			}// close if loop of checking row value of product and qty should not be blank
		}/// close for loop
		////// insert in activity table////
        $flag = dailyActivity($_SESSION['userid'], $grnno, "Stock Issue", "ADD", $ip, $link1, $flag);
		///// check both master and data query are successfully executed
		if ($flag) {
        	mysqli_commit($link1);
			$cflag = "success";
			$cmsg = "Success";
        	$msg = "Part successfully issue with ref. no.".$grnno;
    	} else {
			mysqli_rollback($link1);
			$cflag = "danger";
			$cmsg = "Failed";
			$msg = "Request could not be processed. Please try again." .$error_msg ;
		} 
    	mysqli_close($link1);
	   	///// move to parent page
  		header("location:assgin_part_user.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
   }
?>
<!DOCTYPE html>
<html>
 <head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>
 <?=siteTitle?>
 </title>
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script language="javascript" type="text/javascript">
 function changemode(){


	
	var issuetype = $('#issue_type').val();



	if( issuetype == "Against job"){

		document.getElementById("jobdetail1").style.display = "";

	}else{

	document.getElementById("jobdetail1").style.display = 'none';
	
	}

}
  $(document).ready(function(){
        $("#frm2").validate();
  });
    $(document).ready(function(){
        $("#frm1").validate();
  });
  //////////////////////// function to get model on basis of model dropdown selection///////////////////////////
 function getmodel(indx){
	  var brandid=document.getElementById("brand["+indx+"]").value;
	  var productCode=document.getElementById("prod_code["+indx+"]").value;
	 var division=document.getElementById("division["+indx+"]").value;
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{brandinfo:brandid,productinfo:productCode,indxx:indx,division:division},
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
		data:{modelinfostn:model,indxx:indx},
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
     var r='<tr id="addr'+num+'"><td ><span id="pdtid'+num+'"><select name="prod_code['+num+']" id="prod_code['+num+']" class="form-control required" required><option value="">--None--</option><?php $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['product_id'];?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option><?php }?></select></span></td><td><spam id="division'+num+'"><select name="division['+num+']" id="division['+num+']" class="form-control selectpicker required" data-live-search="true" onChange="fun_product('+num+');getmodel('+num+')" ><option value="">Select Division</option><option value="DOMESTIC">DOMESTIC</option> <option value="EXPORT">EXPORT</option></select></spam></td><td><select name="brand['+num+']" id="brand['+num+']" class="form-control required" onChange="getmodel('+num+')" required><option value="">--Select Brand--</option><?php $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";$check_dept=mysqli_query($link1,$dept_query);while($br_dept = mysqli_fetch_array($check_dept)){?><option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option><?php }?></select></td><td  ><span id="modeldiv'+num+'"><select name="model['+num+']" id="model['+num+']" class="form-control required"  onChange="getpartcode('+num+')" required><option value="" selected="selected"> Select Model</option></select></span></td><td ><span id="partcodediv'+num+'"><select name="partcode['+num+']" id="partcode['+num+']" class="form-control required" required><option value="" selected="selected"> Select Partcode</option></select></span></td><td><input type="text" class="form-control" name="avl_stock['+num+']" id="avl_stock['+num+']"  autocomplete="off" readonly ></td><td><input type="text" style="width: 50px;" class="form-control digits" name="req_qty['+num+']" id="req_qty['+num+']"  autocomplete="off" required onKeyUp="calculateline('+num+')"></td><td ><input type="text" class="form-control " name="price['+num+']" id="price['+num+']"  autocomplete="off"   style="width:13'+num+'px;"  readonly></td><td ><input type="text" class="form-control" name="amount['+num+']" id="amount['+num+']"  autocomplete="off" style="width:13'+num+'px;" value="" readonly></td></tr>';
      $('#itemsTable1').append(r);
	
  });
});



/////////// function to get available stock of ho
function getAvlStk(indx){
  var productCode=document.getElementById("partcode["+indx+"]").value;
  var locCode=document.getElementById("asc_code").value;
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
	
		if(getpartdet[0]!=""){
			document.getElementById("price["+indx+"]").value=getpartdet[5];
		}

	}
  });
}
	function calculateline(indx){
		////// get dispatching qty
	
			var dispqty = document.getElementById("req_qty["+indx+"]").value;
			
				var avlqty = document.getElementById("avl_stock["+indx+"]").value;
		

		//// check dispatch qty should not more then requested qty
	 if( parseFloat(avlqty) < parseFloat(dispqty) ){
			document.getElementById("errormsg").innerHTML = "Qty is not more than available Qty.<br/>";
			document.getElementById("req_qty["+indx+"]").className="digits form-control alert-danger";
			document.getElementById("add").disabled = true;
		}else{
			document.getElementById("errormsg").innerHTML = "";
			document.getElementById("req_qty["+indx+"]").className="digits form-control alert-success";
			document.getElementById("add").disabled = false;
		
		}
		get_tot(indx);
		}

/////////// function to get amount
function get_tot(indx){
//////////////////////////// getting row wise amount  by multiplying price and qty////////////////////////////////////////
	if(document.getElementById("req_qty["+indx+"]").value){ var qty = document.getElementById("req_qty["+indx+"]").value;}else{ var qty = 0;}
	if(document.getElementById("price["+indx+"]").value){ var price = document.getElementById("price["+indx+"]").value;}else{ var price =0.00;}          
	
	var amt = parseFloat(qty) * parseFloat(price) ;
	document.getElementById("amount["+indx+"]").value = amt;
	get_cal();	
}
///////////////////////////
function get_cal(){
	var rowno1 = (document.getElementById("rowno").value); 
 	var sum = 0.00;
 	//var pricesum = 0.00;
  	var total = 0.00;
 	////////////// calculating sum of totalqty, subtotal, amount///////////////////////////////	
	for (var i = 0; i <= rowno1; i++) {
		if(document.getElementById("req_qty["+i+"]").value){ var sumqty = document.getElementById("req_qty["+i+"]").value; }else{ var sumqty = 0;}
		//if(document.getElementById("price["+i+"]").value){ var sumprice = document.getElementById("price["+i+"]").value; }else{ var sumprice = 0.00;}
		if(document.getElementById("amount["+i+"]").value){ var sumamt = document.getElementById("amount["+i+"]").value; }else{ var sumamt = 0.00;}	
		
		sum += parseInt(sumqty);	
		//pricesum += parseFloat(sumprice);	
		total += parseFloat(sumamt);	
	}
	document.getElementById("total_qty").value = sum;
	//document.getElementById("sub_total").value = pricesum;
	document.getElementById("grand_total").value = total;
}



  </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
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
       <h2 align="center"><i class="fa fa-car"></i>Stock Issue </h2>
       <br/>
       <div class="form-group" id="page-wrap" style="margin-left:10px;">
        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
           <div class="form-group">
            <div class="col-md-6">
               <label class="col-md-5 control-label">Engineer Name</label>
               <div class="col-md-6" >
                <select   name="vendor" id="vendor" class="form-control required" required onChange="document.frm1.submit();">
                   <option value="">--Please Select--</option>
                   <?php
               $vendor_query="select userloginid ,locusername,type  from locationuser_master where statusid ='1' and location_code  = '".$_SESSION['asc_code']."' ";
			        $check1=mysqli_query($link1,$vendor_query);
                while($br = mysqli_fetch_array($check1)){?>
                   <option value="<?=$br['userloginid']?>" <?php if($_REQUEST['vendor'] == $br['userloginid']) { echo 'selected'; }?>>
                  <?=$br['locusername']." | ".$br['userloginid']." | ".$br['type']?>
                  </option>
                   <?php } ?>
                 </select>
              </div>
             </div>
            <div class="col-md-6">
			 <label class="col-md-5 control-label"></label>
			   <div class="col-md-6" >
                
              </div>
            </div>
          </div>
        
         </form>
        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
           <div class="form-group">
            <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
               <thead>
                <tr class="<?=$tableheadcolor?>">
                   <th class="col-md-3" style="font-size:13px;">Product</th>
					<th class="col-md-2" style="font-size:13px">Division</th>
                   <th class="col-md-2" style="font-size:13px">Brand</th>
                   <th class="col-md-2" style="font-size:13px">Model</th>
                   <th class="col-md-2" style="font-size:13px">Partcode</th>
				    <th class="col-md-2" style="font-size:13px">Avl Qty</th>
                   <th class="col-md-1" style="font-size:13px">Qty</th>
                  <th class="col-md-2" style="font-size:13px">Price</th>
                   <th class="col-md-2" style="font-size:13px">Amount</th>
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
                      <option data-tokens="<?php echo $br['product_id'];?>" value="<?php echo $br['product_id'];?>">
                       <?=$br['product_name']." | ".$br['product_id']?>
                       </option>
                      <?php }?>
                    </select>
                     </span></td>
					 <td class="col-md-2"><span id="division0">
					   <select name="division[0]" id="division[0]" class="form-control selectpicker required"  data-live-search="true" onChange="fun_product(0);getmodel(0)">
						   <option value="">Select Division</option>
						   <option value="DOMESTIC">DOMESTIC</option>
						   <option value="EXPORT">EXPORT</option>
						    </select>
					  </span></td>
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
                   <td class="col-md-2" ><span id="modeldiv0">
                     <select name="model[0]" id="model[0]" class="form-control required"  onChange="getpartcode(0)" required>
                      <option value="" selected="selected"> Select Model</option>
                    </select>
                     </span></td>
                   <td class="col-md-2"><span id="partcodediv0">
                     <select name="partcode[0]" id="partcode[0]" class="form-control required"   onChange="getAvlStk(0)" required >
                      <option value="" selected="selected"> Select Partcode</option>
                    </select>
                     </span></td>
					  <td class="col-md-1"><input type="text" class="form-control " name="avl_stock[0]" id="avl_stock[0]"  autocomplete="off" readonly></td>
					  
                   <td class="col-md-1"><input type="text" style="width: 50px;" class="form-control digits" name="req_qty[0]" id="req_qty[0]"  autocomplete="off" required onKeyUp="calculateline(0);"></td>
                   <td class="col-md-3"><input type="text" class="form-control " name="price[0]" id="price[0]" style="width:130px;" autocomplete="off" readonly></td>
                   <td class="col-md-2"><input type="text" class="form-control" name="amount[0]" id="amount[0]"  autocomplete="off" style="width:130px;" value="" readonly ></td>
                 </tr>
              </tbody>
               <tfoot id='productfooter' style="z-index:-9999;">
                <tr class="0">
                   <td colspan="8" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a>
                    <input type="hidden" name="rowno" id="rowno" value="0"/></td>
                 </tr>
              </tfoot>
             </table>
          </div>
           <div class="form-group">
            <div class="col-md-6">
               <label class="col-md-5 control-label">Total Qty</label>
               <div class="col-md-6">
                <input type="text" name="total_qty" id="total_qty" class="form-control" value="0" readonly/>
              </div>
            </div>
            <div class="col-md-6">
              <label class="col-md-5 control-label">Grand Total</label>
              <div class="col-md-6">
                <input type="text" name="grand_total" id="grand_total" class="form-control" value="0.00" readonly/>
              </div>
          	</div>
          </div>
           <div class="form-group">
            <div class="col-md-6">
               <label class="col-md-5 control-label">Issue Type <span style="color:#F00">*</span></label>
               <div class="col-md-6">
                 <select name="issue_type" id="issue_type"  class="form-control required" required onChange="changemode();">
                    <option value="">--Please Select--</option>
                    <option value="Against job">Against job</option>
                    <option value="Bulk Part issue" >Bulk Part issue</option>
                    
                 </select>  
              </div>
            </div>
            <div class="col-md-6">
               <label class="col-md-5 control-label">Remark</label>
               <div class="col-md-6">
                <textarea name="remark" id="remark" class="form-control" style="resize:none;width:250px;"></textarea>
              </div>
            </div>
          </div>
   <div class="form-group"  id="jobdetail1" style="display:none">
            <div class="col-md-6">
               <label class="col-md-5 control-label">Job No.</label>
               <div class="col-md-6" >
               <select name="job_no" id="job_no" class="form-control required" >
                       <option value=''>--Select Job NO--</option>
                       <?php
                      echo   $dept_query="SELECT job_no FROM jobsheet_data where eng_id='".$_REQUEST['vendor']."' and status in ('1','2','5','7','3')  order by job_no";
                        $check_dept=mysqli_query($link1,$dept_query);
                        while($br_dept = mysqli_fetch_array($check_dept)){
                      ?>
                       <option value="<?=$br_dept['job_no']?>"<?php if($_REQUEST['job_no'] == $br_dept['job_no']){ echo "selected";}?>><?php echo $br_dept['job_no']?></option>
                       <?php }?>
                     </select>
                 
              </div>
             </div>
            <div class="col-md-6">
               <label class="col-md-5 control-label"></label>
               <div class="col-md-6">
                
              </div>
             </div>
          </div>
           <div class="form-group">
            <div class="col-md-12" align="center"><span id="errormsg" class="red_small"></span>
               <input type="submit" class="btn btn<?=$btncolor?>" name="add" id="add" value="Issue" title="Receive GRN">
               <input type="hidden" name="billto" id="billto" value="<?=$_REQUEST['vendor']?>"/>
               <input type="hidden" name="shipto" id="shipto" value="<?=$_REQUEST['ship_to']?>"/>
               <input type="hidden" name="supplier" id="supplier" value="<?=$_SESSION['asc_code']?>"/>
               <input type="hidden" name="ven_gstno" id="ven_gstno" value="<?=$vendor_addrs[1]; ?>"/>
               <input type="hidden" name="ven_state" id="ven_state" value="<?=$vendor_addrs[2];?>"/>
			   	 <input type="hidden" name="asc_code" id="asc_code" value="<?php echo $_SESSION['asc_code']; ?>">
               <input type="hidden" name="bill_addrs" id="bill_addrs" value="<?=$from[0]; ?>"/>
               <input type="hidden" name="bill_state" id="bill_state" value="<?=$from[2];?>"/>
               <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='assgin_part_user.php?<?=$pagenav?>'">
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