  <?php
  require_once("../includes/config.php");
  $price_type = getAnyDetails($_POST['locationcode'] ,"price_lvl" ,"location_code" ,"location_master",$link1);
  ////get access product details
  $access_product = getAccessProduct($_SESSION['asc_code'],$link1);
  ////get access brand details
  $access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
  /////get status//
  @extract($_POST);
  //////  if we want to Add new po
	 if ($_POST['add']=='Dispatch' && $_SESSION['asc_code']!=''){
	
	 ////// INITIALIZE PARAMETER/////////////////////////
	  mysqli_autocommit($link1, false);
	  $flag = true;
	  $error_msg = "";
	 	  //// pick max count of inv
		  $sql_dccount = "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'";
		  $res_dccount = mysqli_query($link1,$sql_dccount)or die("error1".mysqli_error($link1));
		  $row_dccount = mysqli_fetch_array($res_dccount);
		  $next_dcno = $row_dccount['stn_counter']+1;
		 
		  $invoice_no = $row_dccount['stn_series']."".$row_dccount['fy']."".str_pad($next_dcno,4,"0",STR_PAD_LEFT);
		  
		 	$inv_check = mysqli_query($link1,"SELECT challan_no  from billing_master where challan_no='".$invoice_no."'");
		$avil_grncount = mysqli_fetch_assoc($inv_check);
		if($avil_grncount['challan_no']!=""){
		 $flag = false;
			 $error_msg = "Invoice is already available - ".$invoice_no;
			 mysqli_rollback($link1);
			$cflag = "danger";
			$cmsg = "Failed";
			$msg = "Request could not be processed. Please try again." .$error_msg ;
			header("location:invoice_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
			exit;
		}	 
			  ////// get basic details of both parties
			   /////update next counter against invoice
		  $res_upd = mysqli_query($link1,"UPDATE invoice_counter set stn_counter = '".$next_dcno."' where location_code='".$_SESSION['asc_code']."'");
		  /// check if query is execute or not//
		  if(!$res_upd){
			  $flag = false;
			  $error_msg = "Error1". mysqli_error($link1) . ".";
		  }
		  ///// make invoice no.
		  ////// PO ship from
	  $shipfromlocdet = explode("~",getAnyDetails($_POST['ship_from'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
	  ////// PO dispatcher
	  $fromlocdet = explode("~",getAnyDetails($_SESSION['asc_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,contactno1","location_code","location_master",$link1));
	  ////// PO receiver
	  $tolocdet = explode("~",getAnyDetails($_POST['locationcode'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,contactno1","location_code","location_master",$link1));
		////// get from city details
		$fromloccity = explode("~",getAnyDetails($fromlocdet[4],"city,state","cityid","city_master",$link1));
		////// get to city details
		$toloccity = explode("~",getAnyDetails($tolocdet[4],"city,state","cityid","city_master",$link1));
  
		  ///// Insert in item data by picking each data row one by one
		  foreach($prod_code as $k=>$val){   
  
			  // checking row value of product and qty should not be blank
			  if($prod_code[$k]!='' && $req_qty[$k]!='' && $req_qty[$k]!=0) {
			  $partdet = explode("~",getAnyDetails($partcode[$k] , "hsn_code,part_name" ,"partcode", "partcode_master" ,$link1));
				 
	  
	  $sql_billdata = "INSERT INTO billing_product_items set from_location='".$_SESSION['asc_code']."', to_location='".$locationcode."',challan_no='".$invoice_no."',type='STN', hsn_code='".$partdet[0]."',product_id ='".$prod_code[$k]."', brand_id ='".$brand[$k]."', model_id ='".$model[$k]."', partcode ='".$partcode[$k]."', part_name='".$partdet[1]."',qty ='".$req_qty[$k]."' ,okqty='".$req_qty[$k]."',price='".$price[$k]."',uom='PCS',value='".$amount[$k]."',basic_amt='".$amount[$k]."',item_total='".$amount[$k]."',stock_type='".$stock_type."'";
				  $res_billdata = mysqli_query($link1,$sql_billdata);
				  //// check if query is not executed
				  if (!$res_billdata) {
					  $flag = false;
					  $error_msg = "Error details3: " . mysqli_error($link1) . ".";
				  }
  
				  ////////////// update  okqty in client inventory table //////////////////////////////////////////////////////////	 
				//  echo " update  client_inventory set $stock_type=$stock_type-'".$req_qty[$k]."' where partcode = '".$partcode[$k]."' and  location_code = '".$_SESSION['asc_code']."' ";
				  $client   = mysqli_query($link1 , " update  client_inventory set $stock_type=$stock_type-'".$req_qty[$k]."' where partcode = '".$partcode[$k]."' and  location_code = '".$_SESSION['asc_code']."' "	);	   
		  
		  
			  //// check if query is not executed
			  if (!$client) {
				  $flag = false;
				  $error_msg = "Error details7: " . mysqli_error($link1) . ".";
			  }			 
			  /////////////////// insert in stock ledger////				 
			  $flag=stockLedger($invoice_no,$today,$partcode[$k],$_SESSION['asc_code'],$_POST['locationcode'],"OUT",$stock_type,"STN","STN",$req_qty[$k],$price[$k],$_SESSION['userid'],$today,$currtime,$ip,$link1,$flag);   
			  }// close if loop of checking row value of product and qty should not be blank
		  }/// close for loop
		  
			  //--------------------------------- inserting in billing_master------------------------------//
	  $sql_billmaster = "INSERT INTO billing_master set from_location='".$_SESSION['asc_code']."', to_location='".$_POST['locationcode']."',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$tolocdet[8]."',from_partyname='".$fromlocdet[0]."', party_name='".$tolocdet[0]."',challan_no='".$invoice_no."',sale_date='".$today."',entry_date='".$today."',entry_time='".$currtime."', logged_by='".$_SESSION['userid']."',billing_rmk='Against ".$remark."',bill_from='".$fromlocdet[0]."',from_stateid='".$fromlocdet['5']."',to_stateid='".$tolocdet[5]."' ,from_state='".$fromloccity[1]."',to_state='".$toloccity[1]."',from_cityid='".$fromlocdet[4]."',from_city='".$fromloccity[0]."',to_cityid='".$tolocdet[4]."',to_city='".$toloccity[0]."',from_pincode='".$fromlocdet[6]."',to_pincode='".$tolocdet[6]."',from_phone='".$fromlocdet[9]."',to_phone='".$tolocdet[9]."',from_email='".$fromlocdet[7]."',to_email='".$tolocdet[7]."',bill_to='".$tolocdet[0]."',from_addrs='".$fromlocdet[1]."',disp_addrs='".$fromlocdet[2]."',to_addrs='".$tolocdet[1]."',deliv_addrs='".$tolocdet[3]."',status='2',document_type='DC',po_type='STN',basic_cost='".$grand_total."',  total_cost='".$grand_total."',purpose='".$purpose."', ship_from_code = '".$_POST['ship_from']."', ship_from_gst = '".$shipfromlocdet[8]."', ship_from_state = '".$shipfromlocdet[5]."', ship_from_addr = '".$shipfromlocdet[1]."' "; 
	  $res_billmaster = mysqli_query($link1,$sql_billmaster);
	  	//// check if query is not executed
	if (!$res_billmaster) {
    	$flag = false;
    	$error_msg = "Error details6: " . mysqli_error($link1) . ".";
	}
	  
		  ////// insert in activity table////
		  $flag = dailyActivity($_SESSION['userid'], $invoice_no, "STN", "Stock OUT", $ip, $link1, $flag);
		  ///// check both master and data query are successfully executed
		
		  if ($flag) {
			  mysqli_commit($link1);
			  $cflag = "success";
			  $cmsg = "Success";
			  $msg = "Stock Transfer Note is successfully placed with ref. no.".$invoice_no;
		  } else {
			  mysqli_rollback($link1);
			  $cflag = "danger";
			  $cmsg = "Failed";
			  $msg = "Request could not be processed. Please try again." .$error_msg ;
		  } 
		  mysqli_close($link1);
		  ///// move to parent page
		 // echo "ffjhjj";
		 // echo $msg ;
   ///// move to parent page
    header("location:invoice_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
	$(document).ready(function(){
	$("#frm1").validate();
		  $("#frm2").validate();
	});
	function makeDropdown(){
		$('.selectpicker').selectpicker();
   }
	//////////Function to product blank all fileds
 function fun_product(indx){
	 document.getElementById("add").style.visibility = "";
	 document.getElementById("brand["+indx+"]").value = "";
	 document.getElementById("model["+indx+"]").value = "";
	 document.getElementById("partcode["+indx+"]").value = "";
	 document.getElementById("req_qty["+indx+"]").value = "";
  }
	/////////// function to get available stock of 
	function getAvlStk(indx){
		document.getElementById("add").style.visibility = "";
		var partcode=document.getElementById("partcode["+indx+"]").value;
		var locationCode='<?=$_SESSION['asc_code']?>';
		var stocktype='<?=$_REQUEST['stock_type']?>';
if(stocktype==''){
					 alert("Please select Stock Type");
			 
			 }
			 else{
		$.ajax({
		  type:'post',
		  url:'../includes/getAzaxFields.php',
		  data:{partcodestkPriceVelo:partcode,locationcode:locationCode,stk_type:stocktype,indxx:indx},
		  success:function(data){
			  var getdata=data.split("~");
			 
			  if(getdata[0]!=""){
				  
			  document.getElementById("avl_stock["+getdata[1]+"]").value=getdata[0];
			  document.getElementById("price["+getdata[1]+"]").value=getdata[2];
				  makeDropdown();
			  
			  }
			  
		  }
		});
			 }
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
		var stocktype='<?=$_REQUEST['stock_type']?>';
		$.ajax({
		  type:'post',
		  url:'../includes/getAzaxFields.php',
		  data:{modelinfo:model,stk_type:stocktype,indxx:indx},
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
		  var num = (document.getElementById("rowno").value -1)+2;
		  numi.value = num;
	   var r='<tr id="addr'+num+'"><td ><span id="pdtid'+num+'"><select name="prod_code['+num+']" id="prod_code['+num+']" onChange="fun_product('+num+')" class="form-control selectpicker required" data-live-search="true" required><option value="">--None--</option><?php $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?=$br['product_name']." | ".$br['product_id']?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option><?php }?></select></span></td><td><select name="brand['+num+']" id="brand['+num+']" class="form-control selectpicker required" onChange="getmodel('+num+')" required><option value="">--Select Brand--</option><?php $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";$check_dept=mysqli_query($link1,$dept_query);while($br_dept = mysqli_fetch_array($check_dept)){?><option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option><?php }?></select></td><td  ><span id="modeldiv'+num+'"><select name="model['+num+']" id="model['+num+']" class="form-control required"  onChange="getpartcode('+num+')" required><option value="" selected="selected"> Select Model</option></select></span></td><td ><span id="partcodediv'+num+'"><select name="partcode['+num+']" id="partcode['+num+']" class="form-control required" onChange="getAvlStk('+num+'); checkDuplicate(' + num + ',this.value);" required><option value="" selected="selected"> Select Partcode</option></select></span></td><td><input type="text" class="form-control digits" name="req_qty['+num+']" id="req_qty['+num+']"  autocomplete="off" required onKeyUp="get_tot('+num+')" style="width:130px;" ><span id="errormsg'+num+'" class="red_small"></span></td><td><input type="text" class="form-control digits" name="avl_stock['+num+']" id="avl_stock['+num+']"  autocomplete="off" onKeyUp="get_tot('+num+') "readonly style="width:130px;" ></td><td ><input type="text" class="form-control " name="price['+num+']" id="price['+num+']"  autocomplete="off" required  onKeyUp="get_tot('+num+')" style="width:130px;" ></td><td ><input type="text" class="form-control" name="amount['+num+']" id="amount['+num+']"  autocomplete="off" style="width:130px;" value="" readonly></td></tr>';
		$('#itemsTable1').append(r);
		   makeDropdown();
	  
	});
  });
  
  /////////// function to get amount
  function get_tot(indx){
	  document.getElementById("add").style.visibility = "";
  var availableQty = "avl_stock"+"["+indx+"]";
  var enteredQty = "req_qty"+"["+indx+"]";
  ////////// check whether entered qty is greater or less than Available qty /////////////////////////////////////////////////////////////////////////
  if (parseInt(document.getElementById(availableQty).value) >= parseInt(document.getElementById(enteredQty).value)) {
  //////////////////////////// getting row wise amount  by multiplying price and qty////////////////////////////////////////
	  if(document.getElementById("req_qty["+indx+"]").value){ var qty = document.getElementById("req_qty["+indx+"]").value;}else{ var qty = 0;}
	  if(document.getElementById("price["+indx+"]").value){ var price = document.getElementById("price["+indx+"]").value;}else{ var price =0.00;}          	
	  var amt = parseFloat(qty) * parseFloat(price) ;
  
	  document.getElementById("amount["+indx+"]").value = amt;
		  get_cal();
	  document.getElementById("errormsg"+indx).innerHTML = "";
	  document.getElementById("req_qty"+indx).className="";
	  
	  }
	  else{
		  document.getElementById("errormsg"+indx).innerHTML = "Dispatch qty is More then Available qty.<br/>";
			  document.getElementById("req_qty"+indx).className="digits form-control alert-danger";
		  //	document.getElementById("save").disabled = true;
  
	  }
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
  function ShowButton(){
	    document.getElementById("add").style.visibility = "";
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
		 <h2 align="center"><i class="fa fa-reply"></i>&nbsp;STN  </h2>
		 <br/>
		 <div class="form-group" id="page-wrap" style="margin-left:10px;">
		 	 
		  <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
		  	<div class="form-group">
			  <div class="col-md-6">
			  	
			  </div>
			  <div class="col-md-6">
			  <!--	<button title="STN Uploader" type="button" class="btn<?=$btncolor?>" style="float:right;" onClick="window.location.href='upload_stn_wh.php?op=Add<?=$pagenav?>'"><span>STN Uploader</span></button> -->
			  </div>
		  	 </div> 
			 
			 <div style="margin-top:30px;" class="form-group">
			  <div class="col-md-6">
				 <label class="col-md-5 control-label">Dispatch To Location</label>
				 <div class="col-md-6" >
				   <select name="locationcode" id="locationcode" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();" required>
					 <option value=''>--Please Select-</option>
				  <?php
				  $res_pro = mysqli_query($link1,"select location_code,locationname from location_master where locationtype IN ('WH','ASP') and  location_code != '".$_SESSION['asc_code']."' and statusid='1' order by locationname "); 
				  while($row_pro = mysqli_fetch_assoc($res_pro)){?>
				  <option data-tokens="<?=$row_pro['locationname']." | ".$row_pro['location_code']?>" value="<?=$row_pro['location_code']?>" <?php if($_REQUEST['locationcode'] == $row_pro['location_code']) { echo 'selected'; }?>><?=$row_pro['locationname']." (".$row_pro['location_code'].")"?></option>
				  <?php } ?>
				   </select>
				  
							<input name="price_lvl" id="price_lvl" type="hidden" value="<?=$price_type?>"/>
				</div>
			   </div>
			  <div class="col-md-6">  <label class="col-md-5 control-label">Stock Type</label>
			  <div class="col-md-6" >
				  <select   name="stock_type" id="stock_type" class="form-control required"   onChange="document.frm1.submit();" required>
					 <option value=''>--Please Select-</option>
					  <option value="okqty" <?php if($_REQUEST['stock_type'] == "okqty") { echo 'selected'; }?>>OK</option>
					  <option value="faulty" <?php if($_REQUEST['stock_type'] == "faulty"){ echo 'selected'; }?>>Faulty</option>
					  
				   </select>
				</div>
			  </div>
			</div>
			
			
			<div class="form-group">
				<div class="col-md-6">
					<label class="col-md-5 control-label">Ship From</label>
					<div class="col-md-6" > <!------data-live-search="true" selectpicker  ------>
						<select name="ship_from" id="ship_from" class="form-control required" onChange="document.frm1.submit();" required >
							<option value=''>--Please Select--</option>
							  <?php
							$map_wh = mysqli_query($link1,"select locationname, location_code from location_master where locationtype='ASP' and location_code='".$_SESSION['asc_code']."' order by locationname "); 
							while( $location = mysqli_fetch_assoc($map_wh)){
							
							?>
							<option value="<?=$location['location_code']?>" <?php if($_REQUEST['ship_from'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-md-6">  
					<div class="col-md-6" >
				  
					</div>
				</div>
			</div>
			
			
			
			
		   </form>
		   <br>
            <h4 align="center"><span id="error_msg" class="red_small" style="text-align:center;margin:10px;"></span></h4><br/>
		  <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">
			 <div class="form-group">
			  <table width="100%" id="itemsTable1" class="table table-bordered table-hover">
				 <thead>
				  <tr class="<?=$tableheadcolor?>">
					 <th class="col-md-3" style="font-size:13px;">Product</th>
					 <th class="col-md-2" style="font-size:13px">Brand</th>
					 <th class="col-md-2" style="font-size:13px">Model</th>
					 <th class="col-md-2" style="font-size:13px">Partcode</th>
					 <th class="col-md-1" style="font-size:13px">Qty</th>
					 <th class="col-md-1" style="font-size:13px">Avl Qty</th>		   
					 <th class="col-md-2" style="font-size:13px">Price</th>
					 <th class="col-md-2" style="font-size:13px">Amount</th>
				   </tr>
				</thead>
				 <tbody>
				  <tr id='addr0'>
					 <td class="col-md-2"><span id="pdtid0">
					   <select name="prod_code[0]" id="prod_code[0]" class="form-control selectpicker required"  onChange="fun_product(0)" data-live-search="true" required>
						<option value="">Select Product</option>
						<?php 
					  $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";
					  $check1=mysqli_query($link1,$model_query);
					  while($br = mysqli_fetch_array($check1)){?>
						<option data-tokens="<?=$br['product_name']." | ".$br['product_id']?>" value="<?php echo $br['product_id'];?>">
						 <?=$br['product_name']." | ".$br['product_id']?>
						 </option>
						<?php }?>
					  </select>
					   </span></td>
					 <td class="col-md-2"><select name="brand[0]" id="brand[0]" class="form-control selectpicker required" onChange="getmodel(0)" required>
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
					   <select name="partcode[0]" id="partcode[0]" class="form-control required"  onChange="getAvlStk(0); checkDuplicate(0, this.value);" required >
						<option value="" selected="selected"> Select Partcode</option>
					  </select>
					   </span></td>
					 <td class="col-md-1"><input type="text" class="form-control digits" name="req_qty[0]" id="req_qty[0]"  autocomplete="off" required onKeyUp="get_tot(0)" style="width:130px;" ><span id="errormsg0" class="red_small"></span></td>
					 <td class="col-md-1"><input type="text" class="form-control digits" name="avl_stock[0]" id="avl_stock[0]"  autocomplete="off" onKeyUp="get_tot(0)" style="width:130px;"  readonly></td>
					 <td class="col-md-3"><input type="text" class="form-control " name="price[0]" id="price[0]"  autocomplete="off" required onKeyUp="get_tot(0)" style="width:130px;" ></td>
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
				 <label class="col-md-5 control-label">Purpose <span style="color:#F00">*</span></label>
				 <div class="col-md-6">
				<select   name="purpose" id="purpose" class="form-control required" onChange="ShowButton();"   >
					 <option value=''>--Please Select-</option>
					  <option value="DOA" <?php if($_REQUEST['purpose'] == "DOA") { echo 'selected'; }?>>DOA</option>
					  <option value="faulty" <?php if($_REQUEST['stock_type'] == "faulty"){ echo 'selected'; }?>>Faulty</option>
					  <option value="Fresh" <?php if($_REQUEST['stock_type'] == "Fresh"){ echo 'selected'; }?>>Fresh</option>	
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
			<div class="form-group">
			  <div class="col-md-6">
				 <label class="col-md-5 control-label"></label>
				 <div class="col-md-6">
				 
				</div>
			  </div>
			  <div class="col-md-6">
				 <label class="col-md-5 control-label"></label>
				 <div class="col-md-6">
				 
				</div>
			  </div>
			</div>
			 <div class="form-group">
			  <div class="col-md-12" align="center">
				 <input type="submit" class="btn btn<?=$btncolor?>" name="add" id="add" value="Dispatch" title="Stock transfer" onClick="checkAllRows();">
				 <input type="hidden" name="billto" id="billto" value="<?=$_SESSION['asc_code']?>"/>
				 <input type="hidden" name="locationcode" id="locationcode" value="<?=$_REQUEST['locationcode']?>"/>
				 <input type="hidden" name="stock_type" id="stock_type" value="<?=$_REQUEST['stock_type']?>"/>
				 <input type="hidden" name="ship_from" id="ship_from" value="<?=$_REQUEST['ship_from']?>"/>
				 
			   </div>
			</div>
		   </form>
		</div>
	   </div>
	</div>
   </div>
   <?php  if ( $_REQUEST['stock_type'] == '' || $_REQUEST['locationcode'] == '' ) { ?>
            <script>
                $("#frm2").find("input:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
            </script>
            <?php
        }
?>	
  <?php
  include("../includes/footer.php");
  include("../includes/connection_close.php");
  ?>
  </body>
  </html>