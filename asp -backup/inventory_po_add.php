<?php

require_once("../includes/config.php");
$parentcode = $_POST['location_code'];

////get access product details

$access_product = getAccessProduct($_SESSION['asc_code'],$link1);

////get access brand details

$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

/////get status//

@extract($_POST);

//////  if we want to Add new po

   if ($_POST['add']=='ADD'){

	   mysqli_autocommit($link1, false);

		$flag = true;

		$err_msg = "";

   	$fromaddress = explode ("~" ,getAnyDetails($_SESSION['asc_code'],"locationaddress,stateid","location_code","location_master",$link1));

  	$locinfo= mysqli_fetch_array(mysqli_query($link1,"select * from location_master where location_code='".$parentcode."' "));

   //// Make System generated PO no.//////

	$res_po=mysqli_query($link1,"select max(po_id) as no from po_master where from_code='".$_SESSION['asc_code']."'");

	$row_po=mysqli_fetch_array($res_po);

	$c_nos=$row_po['no']+1;
$po_no=$_SESSION['asc_code']."".$todayt."PO".$c_nos; 
	//$po_no=$_SESSION['asc_code']."PO".$c_nos; 

	//////////////////

   	$usr_add="INSERT INTO po_master set po_no='".$po_no."', po_date='".$today."' , to_code ='".$parentcode."' , to_address='".$locinfo['locationaddress']."' ,to_state='".$locinfo['stateid']."',potype='PO', update_date='".$today."',entry_by='".$_SESSION['userid']."' ,entry_ip ='".$_SERVER['REMOTE_ADDR']."' ,status='1' ,from_code= '".$_SESSION['asc_code']."',  	from_address = '".$fromaddress[0]."',remark='".$remark."',po_id='".$c_nos."' , 	from_state = '".$fromaddress[1]."'  ";

    $result=mysqli_query($link1,$usr_add);

	$poid = mysqli_insert_id($link1);

	//// check if query is not executed

	if (!$result) {

	     $flag = false;

         $err_msg= "Error details1: " . mysqli_error($link1) . ".";

    }

	///// Insert in item data by picking each data row one by one

	foreach($prod_code as $k=>$val)

	{   

	    // checking row value of product and qty should not be blank

		if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0 && $partcode[$k]!='') {

			/////////// insert data

	   $query2="insert into po_items set from_code = '".$_SESSION['userid']."' , to_code = '".$parentcode."', po_id ='".$poid['po_id']."' , po_no='".$po_no."',product_id ='".$prod_code[$k]."', brand_id ='".$brand[$k]."', model_id ='".$model[$k]."', partcode ='".$partcode[$k]."',type = 'PO', qty='".$req_qty[$k]."' , update_date= '".$today."' ,status='1'";

		   $result = mysqli_query($link1, $query2);

		   //// check if query is not executed

		   if (!$result) {

	           $flag = false;

               $err_msg= "Error details2: " . mysqli_error($link1) . ".";

           }

		}// close if loop of checking row value of product and qty should not be blank

	}/// close for loop

	////// insert in activity table////

    $flag = dailyActivity($_SESSION['userid'], $po_no, "PO", "ADD", $ip, $link1, $flag);

	///// check both master and data query are successfully executed

	if ($flag) {

        mysqli_commit($link1);

		$cflag = "success";

		$cmsg = "Success";

        $msg = "Purchase Order is successfully placed with ref. no.".$po_no;

    } else {

		mysqli_rollback($link1);

		$cflag = "danger";

		$cmsg = "Failed";

		$msg = "Request could not be processed. Please try again.";

	} 

    mysqli_close($link1);

	   ///// move to parent page

  header("location:inventory_po.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

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

 ///////////////////////// function to get brand on basis of product dropdown selection///////////////////////////

/* function getbrand(indx){



	  var productCode=document.getElementById("prod_code["+indx+"]").value;

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{productid:productCode,indxx:indx},

		success:function(data){

		var getValue = data.split("~");

		document.getElementById("branddiv"+getValue[1]).innerHTML=getValue[0];

	    }

	  });

  }*/

  function makeDropdown(){
		$('.selectpicker').selectpicker();
   }


//////////Function to product blank all fileds
 function fun_product(indx){
	 document.getElementById("add").style.visibility = "";
	// document.getElementById("brand["+indx+"]").value = "";
	 document.getElementById("model["+indx+"]").value = "";
	 document.getElementById("partcode["+indx+"]").value = "";
	 document.getElementById("req_qty["+indx+"]").value = "";
  }
  //////////////////////// function to get model on basis of model dropdown selection///////////////////////////

 function getmodel(indx){
document.getElementById("add").style.visibility = "";
	  var brandid=document.getElementById("brand_id").value;
       var division=document.getElementById("division["+indx+"]").value;
	 
	  var productCode=document.getElementById("prod_code["+indx+"]").value;
	  document.getElementById("partcode["+indx+"]").value = "";
		document.getElementById("req_qty["+indx+"]").value = "";

	  $.ajax({

	    type:'post',

		url:'../includes/getAzaxFields.php',

		data:{brandinfo:brandid,productinfo:productCode,indxx:indx,division:division},

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

		data:{modelinfoasp:model,indxx:indx},

		success:function(data){

		var getValue = data.split("~");

		document.getElementById("partcodediv"+getValue[1]).innerHTML=getValue[0];
 makeDropdown();
	    }

	  });

  }

  

$(document).ready(function(){
document.getElementById("add").style.visibility = "";
     $("#add_row").click(function(){

		var numi = document.getElementById('rowno');

		var preno=document.getElementById('rowno').value;

		var num = (document.getElementById("rowno").value -1)+ 2;

		numi.value = num;
		 //getmodel('+num+')

     var r='<tr id="addr'+num+'"><td ><span id="pdtid'+num+'"><select name="prod_code['+num+']" id="prod_code['+num+']" class="form-control selectpicker required" data-live-search="true" onChange="fun_product('+num+');" ><option value="">--None--</option><?php $model_query="select product_id,product_name from product_master where status='1' and mapped_brand like '%".$_REQUEST['brand_id']."%' order by product_name";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?=$br['product_name']." | ".$br['product_id']?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option><?php }?></select></span><input type="hidden"  name="brand['+num+']" id="brand['+num+']" value="<?=$_REQUEST['brand_id']?>"></td><td><spam id="division'+num+'"><select name="division['+num+']" id="division['+num+']" class="form-control selectpicker required" data-live-search="true" onChange="fun_product('+num+');getmodel('+num+')" ><option value="">Select Division</option><option value="DOMESTIC">DOMESTIC</option> <option value="EXPORT">EXPORT</option></select></spam></td><td  ><span id="modeldiv'+num+'"><select name="model['+num+']" id="model['+num+']" class="form-control required"  onChange="getpartcode('+num+')" required><option value="" selected="selected"> Select Model</option></select></span></td><td ><span id="partcodediv'+num+'"><select name="partcode['+num+']" id="partcode['+num+']" class="form-control required"  onChange="getAvlStk(0) checkDuplicate(0, this.value);"  required><option value="" selected="selected"> Select Partcode</option></select></span></td><td ><input type="text" class="form-control digits" name="req_qty['+num+']" id="req_qty['+num+']"  autocomplete="off" style="width:100px;text-align:right;" required onblur="rowTotal(' + num + ');" onKeyPress="return onlyNumbers(this.value);"><span id="errmsg[' + num + ']" name="errmsg[' + num + ']" class="red_small"></span></td><td><input type="text" class="number form-control" name="price[' + num + ']" id="price[' + num + ']" autocomplete="off" required style="width:100px;text-align:right;"></td><td><input type="text" class="number form-control" name="cost[' + num + ']" id="cost[' + num + ']" autocomplete="off" required style="width:100px;text-align:right;"></td><td ><input type="text" class="form-control" name="avl_stock['+num+']" id="avl_stock['+num+']"  autocomplete="off" style="width:80px;"  readonly></td></tr>';

      $('#itemsTable1').append(r);
		  makeDropdown();

	

  });

});



/////////// function to get available stock of ho

  function getAvlStk(indx){
	  document.getElementById("add").style.visibility = "";
	  var productCode=document.getElementById("partcode["+indx+"]").value;
	  var stocktype="okqty";
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{locstk:productCode,stktype:stocktype, location:'<?=$_SESSION['asc_code']?>',indxx:indx},
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
	  
	   ////// get part price and tax details //////////////// updated by priya on 11 april///////////////////

  $.ajax({
	type:'post',
	url:'../includes/getAzaxFields.php',
	data:{partpricetax:productCode},
	success:function(data){
		var getpartdet=data.split("~");
		if(getpartdet[5]!=""){
			document.getElementById("price["+indx+"]").value=getpartdet[5];
		}
		else{
			document.getElementById("price["+indx+"]").value=0.00;			
		}
	}
  });

  }

/////// calculate line total /////////////  updated by priya on 11 april
	function rowTotal(ind) {
		document.getElementById("add").style.visibility = "";
	var ent_qty = "req_qty" + "[" + ind + "]";
	var ent_rate = "price" + "[" + ind + "]";
	var availableQty = "avl_stock" + "[" + ind + "]";
	var totalvalField = "cost" + "[" + ind + "]";
	var err_msg = "errmsg" + "[" + ind + "]";
	
	// check if entered qty is something

	if (document.getElementById(ent_qty).value) {
		var qty = document.getElementById(ent_qty).value;
	} else {
		var qty = 0;
	}

	//  check if entered price is somthing
	if (document.getElementById(ent_rate).value) {
		var price = document.getElementById(ent_rate).value;
	} else {
		var price = 0.00;
	}
	
			var total = parseFloat(qty) * parseFloat(price);
			var totalcost = parseFloat(total);  
			document.getElementById(totalvalField).value= totalcost;              
			calculatetotal();

	
}

////// calculate final value of form /////  updated by priya on 11 april
function calculatetotal() {
	var rowno1 = (document.getElementById("rowno").value);              
   	var totalqty = 0.00;
	var total_Amt = 0.00;

	for (var i = 0; i <= rowno1; i++) {

	 var temp_qty = "req_qty" + "[" + i + "]";
	var total_amt = "cost" + "[" + i + "]";
	
	///// check if line total qty is something
	if (document.getElementById(temp_qty).value) {
	var	sum_qty = document.getElementById(temp_qty).value;
		} else {
	var	sum_qty = 0;
		}

	///// check if line total amount is something
	if (document.getElementById(total_amt).value) {
		var	total = document.getElementById(total_amt).value;
		} else {
		var	total = 0.00;
		}

		totalqty += parseFloat(sum_qty);
		total_Amt += parseFloat(total);

	}/// close for loop

	document.getElementById("total_qty").value = formatCurrency(totalqty);   
	document.getElementById("grand_total").value = formatCurrency(Math.round(total_Amt));
	

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

///////////////////////////
  </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
  <!-- Include Date Picker -->
 <link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
 <!-- Include multiselect -->
 <script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<script type="text/javascript" src="../js/ajax.js"></script>
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
      <h2 align="center"><i class="fa fa-shopping-basket"></i> Add New Purchase Order </h2><br/>
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Brand <span style="color:#F00">*</span></label>
              <div class="col-md-9">
                 <select name="brand_id" id="brand_id" class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();" >
					 
                <option value="">-Please Select-</option>
          
					  <?php
$map_brand = mysqli_query($link1,"select brand_id  from access_brand where location_code ='".$_SESSION['asc_code']."'  and  status = 'Y'"); 
while($row_brand = mysqli_fetch_assoc($map_brand)){
$brand = mysqli_fetch_array(mysqli_query($link1, "select brand_id, brand from brand_master where brand_id = '".$row_brand['brand_id']."'  and status='1'"));				
?>
                  <option data-tokens="<?=$brand[1]." | ".$brand[0]?>" value="<?=$brand[0]?>" <?php if($_REQUEST['brand_id'] == $brand[0]) { echo 'selected'; }?>>
                  <?=$brand[1]." (".$brand[0].")"?>
                  </option>
                  <?php } ?>
                 </select>
              </div>
            </div>
          </div>
          
          
          
           <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">To Location/WH<span style="color:#F00">*</span></label>
              <div class="col-md-9">
          
                 <select name="location_code" id="location_code" class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();" >
					 
                <option value="">-Please Select-</option>
          
					  <?php
					//echo "select wh_location  from map_wh_location where location_code ='".$_SESSION['asc_code']."'  and  status = 'Y' and wh_location in (select location_code  from access_brand where brand_id ='".$_REQUEST['brand_id']."'  and  status = 'Y')";exit;
$map_wh = mysqli_query($link1,"select wh_location  from map_wh_location where location_code ='".$_SESSION['asc_code']."'  and  status = 'Y' and wh_location in (select location_code  from access_brand where brand_id ='".$_REQUEST['brand_id']."'  and  status = 'Y')"); 
while($row_wh = mysqli_fetch_assoc($map_wh)){
$location = mysqli_fetch_array(mysqli_query($link1, "select locationname, location_code, cityid from location_master where location_code = '".$row_wh['wh_location']."' "));				
?>
                  <option data-tokens="<?=$location['locationname']." | ".$location['location_code']?>" value="<?=$location['location_code']?>" <?php if($_REQUEST['location_code'] == $location['location_code']) { echo 'selected'; }?>>
                  <?=$location['locationname']." (".$location['location_code'].")"?> ( <?=getAnyDetails($location['cityid'] ,"city","cityid","city_master" ,$link1)?> )
                  </option>
                  <?php } ?>
                 </select>
              </div>
            </div>
          </div>

          <div class="form-group">
       <!--   <div class="col-md-10"> <label class="col-md-3 control-label">Available Credit Balance</label>
              <div class="col-md-3">
<?php //$current_cr_limit = getAnyDetails($_SESSION['asc_code'] ,"total_credit_limit","location_code","current_cr_status" ,$link1);?>
                <input type="text" name="cr_bal" id="cr_bal" class="form-control" value="<?=$current_cr_limit?>" readonly/>
              </div>
-->
              <div class="col-md-3">
              </div>
        
         </form>
 <h4 align="center"><span id="error_msg" class="red_small" style="text-align:center;margin:10px;"></span></h4>
         <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">

          <div class="form-group">
           <table width="100%" id="itemsTable1" class="table table-bordered table-hover" style="margin-left: 20px;">
            <thead>
              <tr>
               <th data-class="expand" class="col-md-3" style="font-size:13px;">Product</th>
				<th data-hide="division"  class="col-md-2" style="font-size:13px">Division</th>
               <th data-hide="phone"  class="col-md-2" style="font-size:13px">Model</th>
			    
				<th data-hide="phone"  class="col-md-2" style="font-size:13px">Part Name</th>
                <th class="col-md-1" style="font-size:13px">Qty</th>    	
				 <th class="col-md-1" style="font-size:13px">Price</th> 	  
				 <th class="col-md-1" style="font-size:13px">Cost</th>  
                <th data-hide="phone,tablet" class="col-md-2" style="font-size:13px">Available  Stock</th>
              </tr>
			    </thead>
            <tbody>
              <tr id='addr0'>
                <td class="col-md-2"><span id="pdtid0">
              
                  <!--<select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker required"  data-live-search="true" onChange="fun_product(0);getmodel(0)">-->
				   <select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker required"  data-live-search="true" onChange="fun_product(0);">	  
                    <option value="">Select Product</option>
                    <?php 
					$model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") and mapped_brand like '%".$_REQUEST['brand_id']."%' order by product_name";
			        $check1=mysqli_query($link1,$model_query);
			        while($br = mysqli_fetch_array($check1)){?>
                    <option data-tokens="<?=$br['product_name']." | ".$br['product_id']?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option>
                    <?php }?>
                  </select></span> <input type="hidden"  name="brand[0]" id="brand[0]"  value="<?=$_REQUEST['brand_id']?>"></td>
                  <td class="col-md-2"><span id="division0">
					   <select name="division[0]" id="division[0]" class="form-control selectpicker required"  data-live-search="true" onChange="fun_product(0);getmodel(0)">
						   <option value="">Select Division</option>
						   <option value="DOMESTIC">DOMESTIC</option>
						   <option value="EXPORT">EXPORT</option>
						    </select>
					  </span></td>
				 
                <td class="col-md-2" ><span id="modeldiv0"><select name="model[0]" id="model[0]" class="form-control required"  onChange="getpartcode(0)" required><option value="" selected="selected"> Select Model</option></select></span></td>

				<td class="col-md-2"><span id="partcodediv0"><select name="partcode[0]" id="partcode[0]" class="form-control required"  onChange="getAvlStk(0) checkDuplicate(0, this.value);" required ><option value="" selected="selected"> Select Partcode</option></select></span></td>

                <td ><input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]" style="width:100px;text-align:right;"  autocomplete="off" required  onBlur="rowTotal(0);" onKeyPress="return onlyNumbers(this.value);"><span id="errmsg[0]" name="errmsg[0]" class="red_small"></span></td>
				
				 <td class="col-md-2"><input type="text" class="number form-control" name="price[0]" id="price[0]"  autocomplete="off" required style="width:100px;text-align:right;"></td>
				 
				 <td class="col-md-2"><input type="text" class="number form-control" name="cost[0]" id="cost[0]"  autocomplete="off" required style="width:100px;text-align:right;"></td>
				 
                <td class="col-md-2"><input type="text" class="form-control" name="avl_stock[0]" id="avl_stock[0]"  autocomplete="off" style="width:80px;" value="" readonly></td>
              </tr>
            </tbody>
            <tfoot id='productfooter' style="z-index:-9999;">
              <tr class="0">
                <td colspan="8" style="font-size:13px;"><a id="add_row" style="text-decoration:none"><i class="fa fa-plus-square-o fa-2x"></i>&nbsp;Add Row</a><input type="hidden" name="rowno" id="rowno" value="0"/></td>
              </tr>
            </tfoot>
          </table>
          </div>
			<!------------------    updated by priya on 11 april   ------------------------>
			<div class="form-group">
                 <div class="col-md-6"><label class="col-md-5 control-label">Total Qty</label>	  
			<div class="col-md-6" >
					<input type="text" name="total_qty" id="total_qty" class="form-control" value="" readonly style="width:200px;"/>                        
                 </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label">Grand Total</label>	  
			<div class="col-md-5">
			 <input type="text" name="grand_total" id="grand_total" class="form-control" value="<?php echo ($po_row['po_value'] - $po_row['discount']); ?>" readonly style="width:200px;text-align:right"/>
                </div>
          </div>
	    </div>
             <div class="form-group">
                 <div class="col-md-6"><label class="col-md-5 control-label">Remark</label>	  
			<div class="col-md-6" >
					<textarea name="remark" id="remark" required></textarea>                  
                 </div>
          </div>
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			
          </div>
	    </div>
			<!------------------ ------------------------>
          <div class="form-group">

            <div class="col-md-12" align="center">     
              <input type="submit" class="btn btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New PO" onClick="checkAllRows();">
			  <input type="hidden" name="parentcode" id="parentcode" value="<?=$_REQUEST['location_code']?>"/>
              <input title="Back" type="button" class="btn btn<?=$btncolor?>" value="Back" onClick="window.location.href='inventory_po.php?<?=$pagenav?>'">
            </div>
          </div>
         </form>
      </div>
    </div>
  </div>
</div>
<?php  if ( $_REQUEST['location_code'] == '') { ?>
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