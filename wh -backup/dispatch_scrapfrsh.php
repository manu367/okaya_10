<?php

require_once("../includes/config.php");

////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);

////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
$docid = base64_decode($_REQUEST['id']);

/*$toloctiondet = explode("~", getAnyDetails($_REQUEST['po_to'], "stateid,category,address","location_code","location_master", $link1));

if ($toloctiondet[0] == "" && $_REQUEST['po_from'] != '') {

    $toloctiondet = explode("~", getLocationDetails($_REQUEST['po_from'], "stateid,cityid","location_code","location_master", $link1));

}*/

@extract($_POST);
////// if we hit process button

    if ($_POST['upd'] == 'Process') {
	/////////////////  update by priya on 19 july to block multiple entry ///////////////////////////////////////////////////////////////////////////////////
$messageIdent_bill = md5($_SESSION['asc_code'] . $_POST['upd']);
//and check it against the stored value:
   	$sessionMessageIdent_bill = isset($_SESSION['messageIdent_bill'])?$_SESSION['messageIdent_bill']:'';
	if($messageIdent_bill!=$sessionMessageIdent_bill){//if its different:          
				//save the session var:
		$_SESSION['messageIdent_bill'] = $messageIdent_bill;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	

        if ( ($partycode != "" && $custContact != "")) {

            mysqli_autocommit($link1, false);

            $flag = true;

            $err_msg = "";

            if ($total_qty != '' &&  $total_qty != 0) {

                //// Make System generated Invoice no.//////

                $res_invcount = mysqli_query($link1, "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'");

                if (mysqli_num_rows($res_invcount)) {

                    //////pick max counter of INVOICE

					$row_invcount = mysqli_fetch_array($res_invcount);

					$next_invno = $row_invcount['inv_counter']+1;

					/////update next counter against invoice

					$res_upd = mysqli_query($link1,"UPDATE invoice_counter set inv_counter = '".$next_invno."' where location_code='".$_SESSION['asc_code']."'");

					/// check if query is execute or not//

					if(!$res_upd){

						$flag = false;

						$err_msg = "Error1". mysqli_error($link1) . ".";

					}

					///// make invoice no.

					$invno = $row_invcount['inv_series']."".$row_invcount['fy']."".str_pad($next_invno,3,"0",STR_PAD_LEFT);

					$fromlocdet = explode("~",getAnyDetails($_SESSION['asc_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));

                    ///// Insert Master Data
                    
                    if($_POST['stock_type']=="okqty") {
                    $po_type="Fresh Out";
                    $po_no="Fresh Out";	
                    }else {
                    $po_type="Scrap Out";
                    $po_no="Scrap Out";
                   }
                    $query1 = "INSERT INTO billing_master set from_location='" . $parentcode . "', to_location='" . $partycode . "',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$custgstn."',
party_name='".$partycode."', challan_no='" . $invno . "', sale_date='" . $today . "', entry_date='" . $today . "', entry_time='" . $currtime . "', logged_by='" . $_SESSION['userid'] . "', document_type='INV' ,basic_cost='" . $sub_total . "',discount_amt='" . $total_discount . "',tax_cost='" . $tax_amount . "',total_cost='" . $grand_total . "',bill_from='" . $parentcode . "',from_stateid='".$fromlocdet['5']."',to_stateid='".$state_name."',bill_to='".$partycode."',from_addrs='" . $fromlocdet[1] . "',disp_addrs='" . $fromlocdet[2] . "',round_off='" . $round_off . "',to_addrs='" . $delivery_address . "',deliv_addrs='" . $delivery_address . "',billing_rmk='" . $remark . "',po_no='".$po_no."', status='3', dc_date='" . $today . "',dc_time='" . $currtime . "',sgst_amt='" . $totsgstamt . "',cgst_amt='" . $totcgstamt. "',igst_amt='" . $totigstamt . "',driver_contact='".$custContact."',carrier_no='".$custEmail."',po_type='$po_type' ";				


                 $result = mysqli_query($link1, $query1);

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

					$arr_imei = array();
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

								$arr_imei[]=$imei[$k];

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
				

                        //// find all key of every product in main array

                        $keyarr = array_keys($arr_prodcode, $value);

                        $sum_qty = 0;

                        $sum_price = 0.00;

                        $sum_holdprice = 0.00;

                        $sum_linetotal = 0.00;

                        $sum_taxamt = 0.00;

                        $sum_discount = 0.00;

                        $sum_taxper = 0.00;

                        $sum_taxname = "";

                        $sum_taxhead = "";

                        $sum_totalval = 0.00;

                        $sumsgstper =0.00;

                        $sumcgstper =0.00;

                        $sumigstper =0.00;

                        $sumsgstamount = "";

                        $sumcgstamount = "";

                        $sumigstamount = "";

                        $denominator = 0;

                        ///// make product wise all deatils in consolidate form

                        for ($i = 0; $i < count($keyarr); $i++) {
                            $sum_qty =$arr_qty[$keyarr[$i]];

                            $sum_price =$arr_price[$keyarr[$i]];

                            $sum_holdprice =$arr_holdprice[$keyarr[$i]];

                            $sum_linetotal =$arr_linetotal[$keyarr[$i]];

                            $sum_taxamt =$arr_taxamt[$keyarr[$i]];

                            $sum_discount =$arr_discount[$keyarr[$i]];

                            $sum_totalval =$arr_totalval[$keyarr[$i]];

                            $sumsgstper = $rowsgstper[$keyarr[$i]];

                            $sumcgstper = $rowcgstper[$keyarr[$i]];

                            $sumigstper = $rowigstper[$keyarr[$i]];

                            $sumsgstamount = $rowsgstamount[$keyarr[$i]];

                            $sumcgstamount = $rowcgstamount[$keyarr[$i]];

                            $sumigstamount = $rowigstamount[$keyarr[$i]];

                            //// tax

                            $taxpick = explode("~", $arr_tax[$keyarr[$i]]);

                            $sum_taxper+=$taxpick[0];

                            //if($sum_taxper){$sum_taxper.=",".$taxpick[0];}else{$sum_taxper.=$taxpick[0];}

                            if ($sum_taxname) {

                                $sum_taxname.="," . $taxpick[1];

                            } else {

                                $sum_taxname.=$taxpick[1];

                            }

                            if ($sum_taxhead) {

                                $sum_taxhead.="," . $taxpick[2];

                            } else {

                                $sum_taxhead.=$taxpick[2];

                            }

                            $denominator++;

                    
					

                        // checking row value of product and qty should not be blank

						$getstk = getInventory($_SESSION['asc_code'],$value,"okqty",$link1);

                        //// check stock should be available ////
						
                        if ($getstk =='' || $getstk < $sum_qty) {
						
                            $flag = false;
                            $err_msg = "Error Code3: Stock is not available: ".mysqli_error($link1);

                        } else {

                        }
						

                        /////////// insert data
                            $query2 = "INSERT INTO billing_product_items set from_location='" . $parentcode . "', to_location='".$partycode."',challan_no='".$invno."', hsn_code='".$arr_hsncode[$key]."', partcode='" . $value . "', product_id='".$arr_product[$key]."', brand_id='".$arr_brand[$key]."', model_id='".$arr_model[$key]."',part_name='".$row_part['part_name']."', qty='" . $sum_qty . "', okqty='" . $sum_qty . "', price='" . $sum_price  . "',uom='PCS', mrp='" . $sum_holdprice . "', value='" . $sum_linetotal . "', discount_amt='" . $sum_discount / $denominator . "', item_total='" . $sum_totalval  . "', pty_receive_date='" . $today . "', sgst_per='" . $sumsgstper . "',sgst_amt='" . $sumsgstamount . "' ,cgst_per='" . $sumcgstper . "',cgst_amt='" . $sumcgstamount . "',igst_per='" . $sumigstper . "',igst_amt='" . $sumigstamount . "',imei1='".$arr_imei[$key]."'";						

                     $result1 = mysqli_query($link1, $query2);

                        //// check if query is not executed

                        if (!$result1) {

                            $flag = false;

                            $err_msg = "Error Code4: ".mysqli_error($link1);

                        }

				
					//	///////////////// update status  in imei_details_asp////////////////////////
						 $imei_asp = mysqli_query($link1, "UPDATE imei_details_asp set status ='3',challan_no='" .$invno. "' , dis_date = '".$today."' where location_code='".$parentcode."' and partcode='".$value."' and imei1='".$arr_imei[$key]."' ");

                        //// check if query is not executed

                        if (!$imei_asp) {

                            $flag = false;

                            $err_msg = "Error Code5: ".mysqli_error($link1);

                        }
						/////////////////////////////////////////////////////////////////////////////////////////////////////
$stock_type_qty=$_POST['stock_type'];
                        //// update stock of from loaction
                  $inventory = mysqli_query($link1, "UPDATE client_inventory set ".$stock_type_qty." = ".$stock_type_qty."-'" . $sum_qty . "',updatedate='" . $datetime . "' where location_code='" . $parentcode . "' and partcode='" . $value . "' and  okqty >= '".$sum_qty."'");

                        //// check if query is not executed

                        if (!$inventory) {

                            $flag = false;

                            $err_msg = "Error Code5: ".mysqli_error($link1);

                        }

                        ///// update stock ledger table

                   $flag = stockLedger($invno, $today, $value, $parentcode, $partycode, "OUT", $stock_type_qty, "Retail Invoice","Process", $sum_qty, $sum_price / $denominator, $_SESSION['userid'], $today, $currtime, $ip, $link1, $flag);
    }
                    }
				

                    ////// insert in activity table////

                    $flag = dailyActivity($_SESSION['userid'], $invno, "RETAIL INVOICE", "ADD", $ip, $link1, $flag);

                    ///// check both master and data query are successfully executed

                    if ($flag) {

                        mysqli_commit($link1);

                        $msg = "Invoice is successfully created with ref. no. " . $invno;

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

            $msg = "Request could not be processed . Please enter customer details(Name and Contact no.).";

			$cflag = "danger";

			$cmsg = "Failed";

        }

        ///// move to parent page

     header("location:invoice_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);

      exit;
	   }
	  else{
	$msg="Invoice is successfully created with ref. no. " . $invno;
		$cflag="success";
		$cmsg="Success";
		header("location:invoice_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit; 
	}

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
<!-- Include Date Picker -->

 <link rel="stylesheet" href="../css/datepicker.css">

 <script src="../js/bootstrap-datepicker.js"></script>


<script>


<?php if($_REQUEST['p_dop']!='' && $_REQUEST['p_dop']!='0000-00-00'){?>

    $(document).ready(function () {

	  $('#pop_date').attr('readonly', true);

	});

	<?php }else{?>

	$(document).ready(function () {

		$('#pop_date').datepicker({

			format: "yyyy-mm-dd",

			endDate: "<?=$today?>",

			todayHighlight: true,

			autoclose: true,

		})

	});

	<?php }?>

// copy contact & other to second form//
function copyContact(){

	document.getElementById("custContact").value=document.getElementById("customercontact").value;



}



function copyGST(){

	document.getElementById("custgstn").value=document.getElementById("customergstn").value;



}



function copyEmail(){

	document.getElementById("custEmail").value=document.getElementById("customeremail").value;



}



function copyName(){

	document.getElementById("partycode").value=document.getElementById("po_to").value;



}







//////////////////////// function to get model on basis of model dropdown selection///////////////////////////

function getmodel(indx){

  var brandid=document.getElementById("brand["+indx+"]").value;

  var productCode=document.getElementById("prod_code["+indx+"]").value;
  if(brandid!=''){

  $.ajax({

	type:'post',

	url:'../includes/getAzaxFields.php',

	data:{brandinfo:brandid,productinfo:productCode,indxx:indx},

	success:function(data){

	var getValue = data.split("~");
	//alert(getValue);

	document.getElementById("modeldiv"+getValue[1]).innerHTML=getValue[0];

	}

  });
  }

}

function getpartcode(indx){

  var model=document.getElementById("model["+indx+"]").value;
  var stock_type=document.getElementById('stock_type').value;
  
  if (stock_type=='') {
  	alert("Please Select Stock Type");
  	return false;
  	
  }else{
if(model!=''){

  $.ajax({

	type:'post',

	url:'../includes/getAzaxFields.php',

	data:{modelbilling:model,stock:stock_type,indxx:indx},

	success:function(data){

	var getValue = data.split("~");
//alert(getValue);
	document.getElementById("partcodediv"+getValue[1]).innerHTML=getValue[0];

	}

  });
  }
  }

}



///// function for checking duplicate IMEI value

function checkavl(fldIndx1) {

	var imei=document.getElementById("imei["+fldIndx1+"]").value;

	var part_code=document.getElementById("partcode["+fldIndx1+"]").value;

 	 var locationcode='<?=$_SESSION[asc_code]?>';
 	 

  	$.ajax({

	type:'post',

	url:'../includes/getAzaxFields.php',

	data:{imeino:imei,location:locationcode,partcode:part_code,indxx:fldIndx1},

	success:function(data){

		var getdata=data.split("~");

		if(getdata[0]  == "N"){

		alert("Imei does not exist in your stock");

		document.getElementById("imei["+getdata[1]+"]").value = '';

		}

		else

		{

		document.getElementById("imei["+getdata[1]+"]").value=getdata[2];

		}

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

			var r = '<tr id="addr' + num + '"><td><select name="prod_code[' + num + ']" id="prod_code[' + num + ']" class="form-control required" required><option value="">Select</option><?php $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";$check1=mysqli_query($link1,$model_query);while($br = mysqli_fetch_array($check1)){?><option data-tokens="<?php echo $br['product_id'];?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option><?php }?></select></td><td><select name="brand[' + num + ']" id="brand[' + num + ']" class="form-control required" onChange="getmodel(' + num + ')" required><option value="">Select</option><?php $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";$check_dept=mysqli_query($link1,$dept_query);while($br_dept = mysqli_fetch_array($check_dept)){?><option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option><?php }?></select><input type="hidden" name="cat[' + num + ']" id="cat[' + num + ']"  value=""></td><td><span id="modeldiv' + num + '"><select name="model[' + num + ']" id="model[' + num + ']" class="form-control required"  onChange="getpartcode(' + num + ')" required><option value="" selected="selected"> Select Model</option></select></span></td><td><span id="partcodediv' + num + '"><select name="partcode[' + num + ']" id="partcode[' + num + ']" class="form-control required"  onChange="getAvlStk(' + num + ')" required ><option value="" selected="selected"> Select Partcode</option></select></span></td><!--<td><input type="text" class="form-control" name="imei[' + num + ']" id="imei[' + num + ']"  autocomplete="off" onBlur="checkavl(' + num + ', this.value);checkDuplicateSerial(' + num + ', this.value);" style="padding: 4px;"></td>--><td><input type="text" class="form-control" name="bill_qty[' + num + ']" id="bill_qty[' + num + ']"  autocomplete="off" onBlur="rowTotal(' + num + ');" style="padding: 4px;"></td><td><input type="text" class="number form-control" name="price[' + num + ']" id="price[' + num + ']" autocomplete="off" required style="width:71px;text-align:right;padding: 4px"><input type="hidden" class="form-control" name="linetotal[' + num + ']" id="linetotal[' + num + ']"></td><td><input type="text" class="number form-control" name="cost[' + num + ']" id="cost[' + num + ']"  autocomplete="off" required style="width:71px;text-align:right;padding: 4px"><td><input type="text" class="number form-control" name="rowdiscount[' + num + ']" id="rowdiscount[' + num + ']" autocomplete="off" onblur="rowTotal(' + num + ');" style="width:66px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowsubtotal[' + num + ']" id="rowsubtotal[' + num + ']" value="0" style="width:71px;text-align:right;padding: 4px" readonly></td><?php if($_REQUEST['state'] == $_SESSION['stateid']){?><td><div style="display:inline-block; float:left"><input type="text" class="form-control" name="rowsgstper[' + num + ']" id="rowsgstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div style="display:inline-block; float:right">%</div><br/><input type="text" class="form-control" name="rowsgstamount[' + num + ']" id="rowsgstamount[' + num + ']" value="0" readonly style="width:60px;text-align:right;padding:4px"></td><td><div style="display:inline-block; float:left"><input type="text" class="form-control" name="rowcgstper[' + num + ']" id="rowcgstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div style="display:inline-block; float:right">%</div><br/><input type="text" class="form-control" name="rowcgstamount[' + num + ']" id="rowcgstamount[' + num + ']" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td><?php }else{?><td><input type="text" class="form-control" name="rowigstper[' + num + ']" id="rowigstper[' + num + ']" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td><td><input type="text" class="form-control" name="rowigstamount[' + num + ']" id="rowigstamount[' + num + ']" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td><?php }?><td><div style="display:inline-block;float:left"><input type="text" class="form-control" name="total_val[' + num + ']" id="total_val[' + num + ']" autocomplete="off" readonly style="width:80px;text-align:right;padding: 4px" ><input name="holdRate[' + num + ']" id="holdRate[' + num + ']" type="hidden"/><input type="hidden" name="avl_stock[' + num + ']" id="avl_stock[' + num + ']"><input type="hidden" id="hsn_code[' + num + ']" name="hsn_code[' + num + ']"></div><div style="display:inline-block;float:right;padding: 4px"><i class="fa fa-close fa-lg" onClick="fun_remove(' + num + ');"></i></div></td></tr>';

			$('#itemsTable1').append(r);

		}

	});

});

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

  var stocktype="okqty";

  var location1=document.getElementById("po_from").value;
if(productCode!=''){
  $.ajax({

	type:'post',

	url:'../includes/getAzaxFields.php',

	data:{locstk:productCode,stktype:stocktype,indxx:indx,location:location1},

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

  });}

  ////// get part price and tax details

  $.ajax({

	type:'post',

	url:'../includes/getAzaxFields.php',

	data:{partpricetax:productCode},

	success:function(data){

		var getpartdet=data.split("~");			

		if(getpartdet[0]!=""){

			document.getElementById("price["+indx+"]").value=getpartdet[0];
			
			document.getElementById("cat["+indx+"]").value=getpartdet[5];

			document.getElementById("hsn_code["+indx+"]").value=getpartdet[1];

			document.getElementById("holdRate["+indx+"]").value=getpartdet[0];

			<?php if($_REQUEST['state'] == $_SESSION['stateid']){?>

			document.getElementById("rowsgstper["+indx+"]").value=getpartdet[3];

			document.getElementById("rowcgstper["+indx+"]").value=getpartdet[4];

			<?php }else{?>

			document.getElementById("rowigstper["+indx+"]").value=getpartdet[2];

			<?php } ?>
			
			 

		}

		else{

			document.getElementById("price["+indx+"]").value=0.00;

			document.getElementById("hsn_code["+indx+"]").value=getpartdet[1];

			document.getElementById("holdRate["+indx+"]").value=0.00;

			<?php if($_REQUEST['state'] == $_SESSION['stateid']){?>

			document.getElementById("rowsgstper["+indx+"]").value=0.00;

			document.getElementById("rowcgstper["+indx+"]").value=0.00;

			<?php }else{?>

			document.getElementById("rowigstper["+indx+"]").value=0.00;

			<?php } ?>

		}
		rowTotal(indx,getpartdet[5]);

	}

  });

}

///////////////////////////

/////// calculate line total /////////////

function rowTotal(ind) {

	var ent_qty = "bill_qty" + "[" + ind + "]";

	var ent_rate = "price" + "[" + ind + "]";

	var hold_rate = "holdRate" + "[" + ind + "]";

	var availableQty = "avl_stock" + "[" + ind + "]";

	var discountField = "rowdiscount" + "[" + ind + "]";

	var totalvalField = "total_val" + "[" + ind + "]";

	var st = "rowsubtotal" + "[" + ind + "]";

	var rowsgstper = "rowsgstper" + "[" + ind + "]";

	var rowcgstper = "rowcgstper" + "[" + ind + "]";

	var rowigstper = "rowigstper" + "[" + ind + "]";

	var rowsgstamount = "rowsgstamount" + "[" + ind + "]";

	var rowcgstamount = "rowcgstamount" + "[" + ind + "]";

	var rowigstamount = "rowigstamount" + "[" + ind + "]";
	
	var value = "cost" + "[" + ind + "]";

	// check if entered qty is something
	

 if(((document.getElementById("cat["+ind+"]").value) != 'ACCESSORY') && ((document.getElementById("avl_stock["+ind+"]").value) !=0 )){
	document.getElementById(ent_qty).value =1;
	document.getElementById(ent_qty).readOnly = true; 
	var qty = 1;
	} 
	else  {
	document.getElementById(ent_qty).readOnly = false; 
	var qty = document.getElementById(ent_qty).value;
	}
	

	
	//  check if entered price is somthing

	if (document.getElementById(ent_rate).value) {

		var price = document.getElementById(ent_rate).value;

	} else {

		var price = 0.00;

	}

    // check if discount is something
    
    if (document.getElementById(value).value) {

		var costtotal = document.getElementById(value).value;

	} else {

		var costtotal = 0.00;

	}


	// check if discount value is something

	if (document.getElementById(discountField).value) {
	 if((document.getElementById("cat["+ind+"]").value) != 'ACCESSORY'){
	 document.getElementById(ent_qty).readOnly = true; 
		var dicountval = document.getElementById(discountField).value;
		}
		else
		{
		var dicountval = document.getElementById(discountField).value;
		}

	} else {

		var dicountval = 0.00;

	}

	<?php if($_REQUEST['state'] == $_SESSION['stateid']){?>

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

	<?php }?>

	// check entered qty should be available
	if (parseInt(document.getElementById(availableQty).value) > qty) {
		if (parseFloat(costtotal) >= parseFloat(dicountval)) {

			var total = parseFloat(qty) * parseFloat(price);
			document.getElementById(value).value = formatCurrency(total);

			var totalcost = parseFloat(total) - parseFloat(dicountval);                 

			document.getElementById(st).value = formatCurrency(totalcost);

			<?php if($_REQUEST['state'] == $_SESSION['stateid']){?>

			var sgst_amt = ((totalcost * sgstper) / 100);

			document.getElementById(rowsgstamount).value = formatCurrency(sgst_amt);

			var cgst_amt = ((totalcost * cgstper) / 100); 

			document.getElementById(rowcgstamount).value = formatCurrency(cgst_amt);

			var tot = parseFloat(totalcost) + parseFloat(sgst_amt) + parseFloat(cgst_amt);

			<?php }else{?>

		

			var igst_amt = ((totalcost * igstper) / 100);

			document.getElementById(rowigstamount).value = formatCurrency(igst_amt);

			var tot = parseFloat(totalcost) + parseFloat(igst_amt);

			<?php }?>

			document.getElementById(totalvalField).value = formatCurrency(tot);

			calculatetotal();

		} else {

			alert("Discount is exceeding from price");

			var total = parseFloat(qty) * parseFloat(price);

			var var3 = "linetotal" + "[" + ind + "]";

			document.getElementById(var3).value = formatCurrency(total);

			document.getElementById(discountField).value = "0.00";

			document.getElementById(totalvalField).value = formatCurrency(total);

			calculatetotal();

		}

	}

	else {

		alert("Stock is not Available");
		 if((document.getElementById("cat["+ind+"]").value) != 'ACCESSORY'){
		document.getElementById(value).value = "";
		document.getElementById(ent_qty).readOnly = true; 
		}
		
		document.getElementById(value).value = "";
		//document.getElementById(availableQty).value = "";

		//document.getElementById(ent_rate).value = "";

		document.getElementById(hold_rate).value = "";
		document.getElementById(value).value = "";
		

		document.getElementById("imei[" + ind + "]").value = "";

		calculatetotal();

	}

}

////// calculate final value of form /////

function calculatetotal() {

	var rowno1 = (document.getElementById("rowno").value);               

   // var sum_qty = 0;

	var sum_total = 0.00;

	var sum_discount = 0.00;

  //  var sum_tax = 0.00;

	var sum = 0.00;

	var priceVal = 0.00;

	var sgstsum = 0.00;

	var cgstsum = 0.00;

	var igstsum = 0.00;

	for (var i = 0; i <= rowno1; i++) {

	  //  var temp_qty = "bill_qty" + "[" + i + "]";

	  //  var temp_total = "linetotal" + "[" + i + "]";

		var temp_discount = "rowdiscount" + "[" + i + "]";

	   // var temp_taxamt = "rowtaxamount" + "[" + i + "]";

		var total_amt = "total_val" + "[" + i + "]";

		var price = "price" + "[" + i + "]";

		var sgstamt = "rowsgstamount"+"[" + i + "]";

		var cgstamt = "rowcgstamount"+"[" + i + "]";

		var igstamt = "rowigstamount"+"[" + i + "]";

		var discountvar = 0.00;

	   // var totaltaxamt = 0.00;

	   // var totalamtvar = 0.00;

		var total = 0.00;

		var price_val = 0.00;

		var totsgstval = 0.00;

		var totcgstval = 0.00;

		var totigstval = 0.00;

		///// check if discount value is something

		if (document.getElementById(temp_discount).value) {

			discountvar = document.getElementById(temp_discount).value;

		} else {

			discountvar = 0.00;

		}



		 ///// check if line total price is something

		if (document.getElementById(price).value) {

			price_val = document.getElementById(price).value;

		} else {

			price_val = 0.00;

		}

		 

		///// check if line total amount is something

		if (document.getElementById(total_amt).value) {

			total = document.getElementById(total_amt).value;

		} else {

			total = 0.00;

		}

		<?php if($_REQUEST['state'] == $_SESSION['stateid']){?>

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

		priceVal += parseFloat(price_val);

		sum_discount += parseFloat(discountvar);

		sum += parseFloat(total);

	}/// close for loop

	document.getElementById("sub_total").value = formatCurrency(sum);

	var round_off = parseFloat(parseFloat(Math.round(sum)) - parseFloat(sum)).toFixed(2);

	document.getElementById("total_qty").value = formatCurrency(priceVal);   

	document.getElementById("total_discount").value = formatCurrency(sum_discount);

	document.getElementById("round_off").value = formatCurrency(round_off); 

	document.getElementById("grand_total").value = formatCurrency(Math.round(sum));

	<?php if($_REQUEST['state'] == $_SESSION['stateid']){?>

	document.getElementById("totsgstamt").value = formatCurrency(sgstsum);   

	document.getElementById("totcgstamt").value = formatCurrency(cgstsum);

	<?php }else{?>

	document.getElementById("totigstamt").value = formatCurrency(igstsum);

	<?php }?>

}



///// check total discount is exceeding from total minimum price of all product

function check_total_discount() {

	if (parseFloat(document.getElementById("sub_total").value) < parseFloat(document.getElementById("total_discount").value)) {

		alert("Discount is exceeding..!!");

		document.getElementById("total_discount").value = "0.00";

		document.getElementById("grand_total").value = formatCurrency(parseFloat(document.getElementById("sub_total").value));

	} else {



		document.getElementById("grand_total").value = formatCurrency(parseFloat(document.getElementById("sub_total").value) - parseFloat(document.getElementById("total_discount").value) + parseFloat(document.getElementById("tax_amount").value));

	}

}

///// check total tax of all selling product

function check_total_tax() {

	if (document.getElementById("complete_tax").value) {

		var splittax = (document.getElementById("complete_tax").value).split("~");

		var completeTax = splittax[0];



	} else {

		var completeTax = 0.00;

	}



	var dis = document.getElementById("total_discount").value;

	if (dis) {

		var disc = dis

	} else {

		var disc = 0.00;

	}



	var calculateTax = (parseFloat(completeTax) * (parseFloat(document.getElementById("sub_total").value) - parseFloat(disc))) / 100;

	document.getElementById("tax_amount").value = formatCurrency(calculateTax);



	document.getElementById("grand_total").value = formatCurrency(parseFloat(document.getElementById("sub_total").value) - parseFloat(disc) + parseFloat(calculateTax));

}

</script>

<script type="text/javascript" language="javascript">

///// function for checking duplicate IMEI value

function checkDuplicateSerial(fldIndx1, enteredsno) {

	document.getElementById("upd").disabled = false;

	if (enteredsno != '') {

		var check2 = "imei[" + fldIndx1 + "]";

		var flag = 1;

		for (var i = 0; i <= fldIndx1; i++) {

			var check1 = "imei[" + i + "]";

			if (fldIndx1 != i && document.getElementById(check2).value != '' && document.getElementById(check1).value != '') {

				if ((document.getElementById(check2).value == document.getElementById(check1).value)) {

					alert("Duplicate IMEI NO.");

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





//// function to check whole form//

function checkdata() {

	var maxno = document.getElementById("rowno").value;

	var flag = 1;

	for (var i = 0; i <= maxno; i++) {

		var checkval = document.getElementById("imei[" + i + "]").value;

		for (var j = 0; j <= maxno; j++) {

			var checkvalin = document.getElementById("imei[" + j + "]").value;

			if (j != i && checkvalin != '' && checkval != '') {

				if (checkval == checkvalin) {

					alert("Duplicate IMEI NO.");

					document.getElementById("imei[" + j + "]").value = '';

					document.getElementById("imei[" + j + "]").style.backgroundColor = "#F66";

					document.getElementById("imei[" + j + "]").style.padding = '4';

					flag *= 0;

				} else {

					document.getElementById("imei[" + j + "]").style.backgroundColor = "#FFFFFF";

					document.getElementById("imei[" + j + "]").style.padding = '4';

					flag *= 1;

				}

			}

		}

		///// check available stock flag should not be N 

		if (parseInt(document.getElementById("avl_stock[" + i + "]").value) == 0) {

			flag *= 0;

		} else {

			flag *= 1;

		}

	}

	if (flag == 0) {

		document.getElementById("upd").disabled = true;

		return false;

	} else {

		document.getElementById("upd").disabled = false;

		return true;

	}

}

function chk_part(){

var count=document.getElementById("rowno").value;

for(var i=0; i<=count; i++ ){
	var partcode="partcode["+i+"]";
document.getElementById(partcode).value="";


}}

</script>

    </head>

    <body onKeyPress="return keyPressed(event);">

        <div class="container-fluid">

            <div class="row content">

                <?php

                include("../includes/leftnavemp2.php");

                ?>

                <div class="<?=$screenwidth?>">

                    <h2 align="center"><i class="fa fa-user"></i> Faulty/Fresh Stock Out(Invoice)</h2>

                    <?php if ($_GET['msg']) { ?><h4 align="center" style="color:#FF0000"><?= $_GET['msg'] ?></h4><?php } ?>

                    <div class="form-group" id="page-wrap" style="margin-left:10px;">

                        <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">

                            <div class="form-group">

                                <div class="col-md-12"><label class="col-md-3 control-label">Billing  From<span style="color:#F00">*</span></label>

                                    <div class="col-md-6">

                                        <select name="po_from" id="po_from" required class="form-control required" data-live-search="true" onChange="document.frm1.submit();">

                                            <?php

                                            $sql_parent = "select location_code,locationname from location_master where location_code='".$_SESSION['asc_code']."'";

                                            $res_parent = mysqli_query($link1, $sql_parent);

                                            while ($result_parent = mysqli_fetch_array($res_parent)) {

                                                ?>

                                                <option data-tokens="<?= $result_parent['locationname'] . " | " . $result_parent['location_code'] ?>" value="<?= $result_parent['location_code'] ?>" <?php if ($result_parent['location_code'] == $_REQUEST['po_from']) echo "selected"; ?> >

                                                    <?= $result_parent['locationname'] . " | " . $result_parent['location_code'] ?>

                                                </option>

                                                <?php

                                            }

                                            ?>

                                        </select>



                                    </div>

                                </div>

                            </div>

                            <div class="form-group">

                                <div class="col-md-12"><label class="col-md-3 control-label">Customer Name<span style="color:#F00">*</span></label>

                                    <div class="col-md-6">

                                            <input type="text" class="form-control required" required name="po_to" id="po_to" onKeyUp="copyName();" value="<?= $_REQUEST['po_to'] ?>">

                                    </div>

                                </div>

                            </div>

                                <div class="form-group">                      

                                    <div class="col-md-12">

                                        <label class="col-md-3 control-label">State<span style="color:#F00">*</span></label>

                                        <div class="col-md-3">

                                            <select name="state" id="state" required class="form-control required" data-live-search="true" onChange="document.frm1.submit();">

                                                <option value="" selected="selected">Please Select </option>

                                                <?php

                                                $sql_parent = "select stateid,state from state_master group by state";

                                                $res_parent = mysqli_query($link1, $sql_parent);

                                                while ($result_parent = mysqli_fetch_array($res_parent)) {

                                                    ?>

                                                    <option data-tokens="<?=$result_parent['state']?>" value="<?=$result_parent['stateid'] ?>" <?php if ($result_parent['stateid'] == $_REQUEST['state']) echo "selected"; ?> >

                                                        <?=$result_parent['state']?>

                                                    </option>

                                                    <?php

                                                }

                                                ?>

                                            </select>  

                                        </div>

                                        <label class="col-md-1 control-label">GSTN</label>

                                        <div class="col-md-3">

                                            <input type="text" class="form-control" name="customergstn" id="customergstn" style="width:150px;" value="<?= $_REQUEST['customergstn'] ?>" onKeyUp="copyGST();">

                                        </div>

                                    </div>

                                </div>

                                <div class="form-group">

                                    <div class="col-md-12">

                                        <label class="col-md-3 control-label">Contact No.<span style="color:#F00">*</span></label>

                                        <div class="col-md-3">

                                            <input type="text" class="digits required form-control" required name="customercontact" id="customercontact" style="width:150px;" value="<?= $_REQUEST['customercontact'] ?>" onKeyUp="copyContact();"  maxlength="10">

                                        </div>

                                        <label class="col-md-1 control-label">Email</label>

                                        <div class="col-md-3">

                                            <input type="text" class="form-control email" name="customeremail" id="customeremail" style="width:150px;" value="<?= $_REQUEST['customeremail'] ?>" onKeyUp="copyEmail();">

                                        </div>           

                                    </div>

                                </div>
                                
                                
                                <div class="form-group">

                                    <div class="col-md-12">

                                        <label class="col-md-3 control-label">Stock Type.<span style="color:#F00">*</span></label>

                                        <div class="col-md-3">
                                        <select name="stock_type" id="stock_type" class="form-control required" style="width:120px;" onChange="chk_part();">
                                        <option value="">Please Select</option>
                                        <option value="okqty">Fresh</option>
                                        <option value="faulty">Faulty</option>                                         
                                        </select>
                                            
                                        </div>

                                                 

                                    </div>

                                </div>

                        </form>

                        <form id="frm2" name="frm2" class="form-horizontal" action="" method="post">

                            <div class="form-group">

                                <table class="table table-bordered" width="108%" id="itemsTable1" style="width:auto;">

                                    <thead>

                                        <tr class="<?=$tableheadcolor?>">

                                            <th style="text-align:center" width="12%">Product</th>

                                            <th style="text-align:center" width="12%">Brand</th>

                                            <th style="text-align:center" width="12%">Model</th>

                                            <th style="text-align:center" width="14%">Part</th>

                                            <!--<th style="text-align:center" width="8%">IMEI/Serial</th>-->
											
											 <th style="text-align:center" width="8%">Qty</th>

                                            <th style="text-align:center" width="8%">Price</th>
											<th style="text-align:center" width="8%">Cost</th>

                                            

                                          <th style="text-align:center" width="10%">Value After Discount</th>

                                            <?php if($_REQUEST['state'] == $_SESSION['stateid']){?>

                                            <th style="text-align:center" width="8%">SGST(%)<br/>SGST Amt</th>

                                            <th style="text-align:center" width="8%">CGST(%)<br/>CGST Amt</th>

                                            <?php }else{?>

                                            <th style="text-align:center" width="6%">IGST(%)</th>

                                            <th style="text-align:center" width="8%">IGST Amt</th>

                                            <?php }?>

                                            <th style="text-align:center" width="10%">Total </th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        <tr id="addr0">

                                            <td><select name="prod_code[0]" id="prod_code[0]" class="form-control required" style="width:60px;text-align:left;padding: 2px" required>

                    <option value="">Select</option>

                    <?php 

					$model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";

			        $check1=mysqli_query($link1,$model_query);

			        while($br = mysqli_fetch_array($check1)){?>

                    <option data-tokens="<?php echo $br['product_id'];?>" value="<?php echo $br['product_id'];?>"><?=$br['product_name']." | ".$br['product_id']?></option>

                    <?php }?>

                  </select></td>

                                            <td><select name="brand[0]" id="brand[0]" class="form-control required" onChange="getmodel(0)" style="width:60px;text-align:left;padding: 2px" required>

                      <option value=''>Select</option>

                      <?php

                        $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";

                        $check_dept=mysqli_query($link1,$dept_query);

                        while($br_dept = mysqli_fetch_array($check_dept)){

                      ?>

                      <option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>

                    <?php }?>	

                    </select>

                                              <!--  <input type="hidden" name="bill_qty[0]" id="bill_qty[0]" value="1">-->
											  
											   <input type="hidden" name="cat[0]" id="cat[0]"  value="">

                                            </td>

                                            <td><span id="modeldiv0"><select name="model[0]" id="model[0]" class="form-control required"  onChange="getpartcode(0)"  style="width:60px;text-align:left;padding: 4px" required><option value="" selected="selected"> Select Model</option></select></span></td>

                                            <td><span id="partcodediv0"><select name="partcode[0]" id="partcode[0]" class="form-control required"  onChange="getAvlStk(0)" style="width:60px;text-align:left;padding: 2px" required ><option value="" selected="selected"> Select Partcode</option></select></span>
											
											</td>

                                           <!-- <td><input type="text" class="form-control" name="imei[0]" id="imei[0]"  autocomplete="off" onBlur="checkavl(0, this.value);checkDuplicateSerial(0, this.value);" style="padding: 4px;"></td>-->
											
											<td><input type="text" class="form-control" name="bill_qty[0]" id="bill_qty[0]"  autocomplete="off" onBlur="rowTotal(0);" style="padding: 4px;"></td>

                                            <td><input type="text" class="number form-control" name="price[0]" id="price[0]"  autocomplete="off" required style="width:71px;text-align:right;padding: 4px">

                                            <input type="hidden" class="form-control" name="linetotal[0]" id="linetotal[0]"></td>
<td><input type="text" class="number form-control" name="cost[0]"  id="cost[0]"  autocomplete="off" required style="width:71px;text-align:right;padding: 4px">
                                           

                                            <td><input type="hidden" class="number form-control" name="rowdiscount[0]" id="rowdiscount[0]" autocomplete="off" onBlur="rowTotal(0);" style="width:66px;text-align:right;padding: 4px"><input type="text" class="form-control" name="rowsubtotal[0]" id="rowsubtotal[0]" value="0" style="width:71px;text-align:right;padding: 4px" readonly></td>

                                            <?php if($_REQUEST['state'] == $_SESSION['stateid']){?>

                                            <td><div style="display:inline-block; float:left"><input type="text" class="form-control" name="rowsgstper[0]" id="rowsgstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div style="display:inline-block; float:right">%</div><br/><input type="text" class="form-control" name="rowsgstamount[0]" id="rowsgstamount[0]" value="0" readonly style="width:60px;text-align:right;padding:4px"></td>

                                            <td><div style="display:inline-block; float:left"><input type="text" class="form-control" name="rowcgstper[0]" id="rowcgstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></div><div style="display:inline-block; float:right">%</div><br/><input type="text" class="form-control" name="rowcgstamount[0]" id="rowcgstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td>

                                            <?php }else{?>

                                            <td><input type="text" class="form-control" name="rowigstper[0]" id="rowigstper[0]" value="0" readonly style="width:50px;text-align:right;padding: 4px"></td>

                                            <td><input type="text" class="form-control" name="rowigstamount[0]" id="rowigstamount[0]" value="0" readonly style="width:60px;text-align:right;padding: 4px"></td>

                                            <?php }?>

                                            <td><input type="text" class="form-control" name="total_val[0]" id="total_val[0]" autocomplete="off" readonly  style="width:80px;text-align:right;padding: 4px" >

                                                <input type="hidden" name="avl_stock[0]" id="avl_stock[0]">

                                                <input name="holdRate[0]" id="holdRate[0]" type="hidden"/>

                                                <input type="hidden" id="hsn_code[0]" name="hsn_code[0]">

                                            </td>



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

                                        <input type="text" name="total_qty" id="total_qty" class="form-control" value="0.00" readonly style="width:200px;"/>

                                    </div>

                                    <label class="col-md-2 control-label">Discount</label>

                                    <div class="col-md-2">

                                        <input type="text" name="total_discount" id="total_discount" class="form-control" value="0.00" style="width:200px;text-align:right" readonly/>

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

                                    <label class="col-md-3 control-label">Delivery Address <span style="color:#F00">*</span></label>

                                    <div class="col-md-2">

                                        <textarea name="delivery_address" id="delivery_address" class="form-control required" style="resize:none; width:200px" required><?php echo $toloctiondet[2]; ?></textarea>

                                    </div>

                                    <label class="col-md-2 control-label">Remark</label>

                                    <div class="col-md-2">

                                        <textarea name="remark" id="remark" class="form-control" style="resize:none;width:200px" ></textarea>

                                    </div>

                                </div>

                            </div>

                            <div class="form-group">

                                <div class="col-md-12" align="center">

                                    <input type="submit" class="btn<?=$btncolor?>" name="upd" id="upd" value="Process" title="Make Invoice" onClick="return checkdata();">

                                    &nbsp;

                                    <input type="hidden" name="parentcode" id="parentcode" value="<?= $_REQUEST['po_from'] ?>"/>

                                    <input type="hidden" name="partycode" id="partycode" value="<?= $_REQUEST['po_to'] ?>"/>

                                    <input type="hidden" name="state_name" id="state_name" value="<?= $_REQUEST['state'] ?>"/>

                                    <input type="hidden" name="custContact" id="custContact" value="<?= $_REQUEST['customercontact'] ?>"/>

                                    <input type="hidden" name="custgstn" id="custgstn" value="<?= $_REQUEST['customergstn'] ?>"/>

                                    <input type="hidden" name="custEmail" id="custEmail" value="<?= $_REQUEST['customeremail'] ?>"/>

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

        <?php if ($_REQUEST['po_from'] == '' || $_REQUEST['po_to'] == '') { ?>

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