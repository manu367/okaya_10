<?php
require_once("../includes/config.php");
//$arrstatus = getFullStatus("process",$link1);
$docid=base64_decode($_REQUEST['refid']);
//// job details
$job_sql="SELECT * FROM jobsheet_data where job_no='".$docid."'";
$job_res=mysqli_query($link1,$job_sql);
$job_row=mysqli_fetch_assoc($job_res);
////// after hitting make invoice
@extract($_POST);
////// if we hit process button
if ($_POST){
	if ($_POST['makeinv'] == 'Make Invoice'){
		/// check service charge should not be blank
		if ($ser_charge != "") {
			mysqli_autocommit($link1, false);
            $flag = true;
            $err_msg = "";
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
					$invno = $row_invcount['inv_series']."".$row_invcount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);
					/////get basic details of location
					$fromlocdet = explode("~",getAnyDetails($_SESSION['asc_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
                    ///// Insert Master Data
                    $query1 = "INSERT INTO billing_master set from_location='" . $_SESSION['asc_code'] . "', to_location='" . $job_row['customer_name'] . "',from_gst_no='".$fromlocdet[8]."',to_gst_no='',
party_name='".$job_row['customer_name']."', challan_no='" . $invno . "', sale_date='" . $today . "', entry_date='" . $today . "', entry_time='" . $currtime . "', logged_by='" . $_SESSION['userid'] . "', document_type='INV' ,basic_cost='" . $tot_cost . "',tax_cost='',total_cost='" . $grandtotal . "',bill_from='" . $_SESSION['asc_code'] . "',from_stateid='".$_SESSION['stateid']."',to_stateid='".$job_row["state_id"]."',bill_to='".$job_row['customer_name']."',from_addrs='" . $fromlocdet[1] . "',disp_addrs='" . $fromlocdet[2] . "',round_off='" . $round_off . "',to_addrs='" . $job_row['address'] . "',deliv_addrs='" . $job_row['address'] . "',billing_rmk='OUT Warranty Invoice',po_no='FRONT_BILL', status='3', dc_date='" . $today . "',dc_time='" . $currtime . "',sgst_amt='" . $sgsttotal . "',cgst_amt='" . $cgsttotal. "',igst_amt='" . $igsttotal . "',driver_contact='".$job_row['contact_no']."',carrier_no='".$job_row['email']."',po_type='RETAIL',discount_amt='".$dis_count."'";				
                    $result = mysqli_query($link1, $query1);
                    //// check if query is not executed
                    if (!$result) {
                        $flag = false;
                        $err_msg = "Error Code1: ". mysqli_error($link1);
                    }
					///// fetch parts
					
					foreach ($part as $k => $val) {
						//$row_part = mysqli_fetch_assoc(mysqli_query($link1,"SELECT product_id,brand_id,part_name FROM partcode_master where partcode='".$val."'"));
						/////////// insert data
                        $query2 = "INSERT INTO billing_product_items set from_location='" . $_SESSION['asc_code'] . "', to_location='".$job_row['customer_name']."',challan_no='".$invno."', hsn_code='".$hsn_code[$k]."', partcode='" . $val . "', product_id='".$job_row['product_id']."', brand_id='".$job_row['brand_id']."', model_id='".$job_row['model_id']."', part_name='".$partname[$k]."', qty='1', okqty='1', price='" . $part_cost[$k]. "',uom='PCS', mrp='" . $part_cost[$k] . "', value='" . $part_cost[$k] . "', discount_amt='', item_total='" . $totalamt[$k] . "', pty_receive_date='" . $today . "',basic_amt='".$part_cost[$k]."', sgst_per='" . $sgstper[$k] . "',sgst_amt='" . $sgstamt[$k] . "' ,cgst_per='" . $cgstper[$k] . "',cgst_amt='" . $cgstamt[$k] . "',igst_per='" . $igstper[$k] . "',igst_amt='" . $igstamt[$k] . "'";						
                        $result = mysqli_query($link1, $query2);
                        //// check if query is not executed
                        if (!$result) {
                            $flag = false;
                            $err_msg = "Error Code2: ".mysqli_error($link1);
                        }
					}
					///// make one entry of service charge in billing item table
					$result3 = mysqli_query($link1,"INSERT INTO billing_product_items set  from_location='" . $_SESSION['asc_code'] . "', to_location='".$job_row['customer_name']."',challan_no='".$invno."',hsn_code='".$ser_tax_hsn."',partcode='SERVICE',job_no='".$docid."', product_id='".$job_row['product_id']."', brand_id='".$job_row['brand_id']."', model_id='".$job_row['model_id']."', part_name='SERVICE COST',qty='1', okqty='1',
price='".$ser_charge."',uom='PCS', mrp='" . $ser_charge . "',value='".$ser_charge."',basic_amt='".$ser_charge."',cgst_per='".$cgst_ser_tax_per."',cgst_amt='".$cgst_ser_tax_amt."',
sgst_per='".$sgst_ser_tax_per."',sgst_amt='".$sgst_ser_tax_amt."',igst_per='".$igst_ser_tax_per."',igst_amt='".$igst_ser_tax_amt."', item_total='" . $taotal_ser_tax_amt . "', pty_receive_date='" . $today . "'");
					//// check if query is not executed
					if (!$result3) {
						$flag = false;
						$err_msg = "Error Code3: ".mysqli_error($link1);
					}
					//// update status in job sheet
					$res_jobsheet = mysqli_query($link1,"UPDATE jobsheet_data set outws_inv='Y',outws_invno='".$invno."' where job_no='".$docid."'");
					//// check if query is not executed
					if (!$res_jobsheet) {
						 $flag = false;
						 $err_msg = "Error details4: " . mysqli_error($link1) . ".";
					}
					///// entry in call/job  history
					$flag = callHistory($docid,$_SESSION['asc_code'],$job_row['status'],"Invoice Generated","Ready For Delivery",$_SESSION['userid'],$job_row['warranty_status'],"Generated Invoice for OUT Job","","",$ip,$link1,$flag);
					////// insert in activity table////
					$flag = dailyActivity($_SESSION['userid'],$docid,"JOB INVOICE","GENERATED",$ip,$link1,$flag);
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
				}else {
                    $msg = "Request could not be processed invoice series not found. Please try again.";
					$cflag = "danger";
					$cmsg = "Failed";
                }
		}else{
			$msg = "Request could not be processed . Please enter service charge.";
			$cflag = "danger";
			$cmsg = "Failed";
		}
		///// move to parent page
        header("location:billing_invoice_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
 <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
 <script>
 function getCost_service(){
	if(document.getElementById('ser_charge').value){
		var ser_chargeV = parseFloat(document.getElementById('ser_charge').value);
	}else{
		var ser_chargeV = 0.00;
	}
	<?php if($job_row['state_id']==$_SESSION['stateid']){?>
		var cgst_ser_tax_perV = parseFloat(document.getElementById('cgst_ser_tax_per').value);
		var sgst_ser_tax_perV = parseFloat(document.getElementById('sgst_ser_tax_per').value);
		
		var cgst_ser_tax_amtV=(ser_chargeV*cgst_ser_tax_perV)/100;
		var sgst_ser_tax_amtV=(ser_chargeV*sgst_ser_tax_perV)/100;
		var igst_ser_tax_amtV=0.00;
		document.getElementById('cgst_ser_tax_amt').value=parseFloat(cgst_ser_tax_amtV,2);
		document.getElementById('sgst_ser_tax_amt').value=parseFloat(sgst_ser_tax_amtV,2);
	<?php }else{?>
		var cgst_ser_tax_amtV=0.00;
		var sgst_ser_tax_amtV=0.00;
		
		var igst_ser_tax_perV = parseFloat(document.getElementById('igst_ser_tax_per').value);
		var igst_ser_tax_amtV=(ser_chargeV*igst_ser_tax_perV)/100;
		document.getElementById('igst_ser_tax_amt').value=parseFloat(igst_ser_tax_amtV,2);
	<?php }?>
	var taotal_ser_tax_amtV=parseFloat(ser_chargeV)+parseFloat(cgst_ser_tax_amtV)+parseFloat(sgst_ser_tax_amtV)+parseFloat(igst_ser_tax_amtV);	
	document.getElementById('taotal_ser_tax_amt').value=parseFloat(taotal_ser_tax_amtV,2);
	var cgsttotal=0;
	var sgsttotal=0;
	var igsttotal=0;
	var tot_cost=0;
	var grandtotal=0;

	var n=document.getElementById('num').value;	
	for(var i=1;i<n;i++){
		var c1="cgstamt"+i	
		var c2="sgstamt"+i
		var c3="igstamt"+i
		var c4="part_cost"+i
		var c5="totalamt"+i;
		<?php if($job_row['state_id']==$_SESSION['stateid']){?>
		var cgsttotal=cgsttotal+parseFloat(document.getElementById(c1).value);
		var sgsttotal=sgsttotal+parseFloat(document.getElementById(c2).value);
		<?php }else{?>
		var igsttotal=igsttotal+parseFloat(document.getElementById(c3).value);
		<?php }?>
		var tot_cost=tot_cost+parseFloat(document.getElementById(c4).value);
		var grandtotal=grandtotal+parseFloat(document.getElementById(c5).value);
	}
	<?php if($job_row['state_id']==$_SESSION['stateid']){?>
		if(document.getElementById('cgst_ser_tax_amt').value){var cgst_srv = parseFloat(document.getElementById('cgst_ser_tax_amt').value);}else{ var cgst_srv = 0.00;};	
		if(document.getElementById('sgst_ser_tax_amt').value){var sgst_srv = parseFloat(document.getElementById('sgst_ser_tax_amt').value);}else{ var sgst_srv = 0.00;}	
		document.getElementById("cgsttotal").value=(cgsttotal+cgst_srv).toFixed(2);
		document.getElementById("sgsttotal").value=(sgsttotal+sgst_srv).toFixed(2);
	<?php }else{?>
		if(document.getElementById('igst_ser_tax_amt').value){var igst_srv = parseFloat(document.getElementById('igst_ser_tax_amt').value);}else{ var igst_srv = 0.00;}
		document.getElementById("igsttotal").value=(igsttotal+igst_srv).toFixed(2);
	<?php }?>
	if(document.getElementById('ser_charge').value){var totcost_srv = parseFloat(document.getElementById('ser_charge').value);}else{ var totcost_srv = 0.00;}
	if(document.getElementById('taotal_ser_tax_amt').value){var total_srv = parseFloat(document.getElementById('taotal_ser_tax_amt').value);}else{ var total_srv = 0.00;}
	
	document.getElementById("tot_cost").value=(tot_cost+totcost_srv).toFixed(2);
	document.getElementById("grandtotal").value=(grandtotal+total_srv).toFixed(2);	
	document.getElementById("grandtotal_dis").value=(grandtotal+total_srv).toFixed(2);	
 }
 
 function getCost_discount() {
	
		var ser_dis = parseFloat(document.getElementById("dis_count").value);
		var grand_tot = parseFloat(document.getElementById("grandtotal").value);
		
		if(grand_tot<ser_dis){
		document.getElementById("errmsg").innerHTML = "Discount Price is Greater than Invoice Price .";
			document.getElementById("makeinv").style.display="none";
		}else {
			document.getElementById("grandtotal_dis").value=grand_tot - ser_dis ;
		}


	}
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body onKeyPress="return keyPressed(event);" onLoad="getCost_service();">
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
		$cust_det = explode("~",getAnyDetails($job_row['customer_id'],"customer_id,landmark,email,phone,dob_date,mrg_date,alt_mobile ","customer_id","customer_master",$link1));
	$product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where job_no='".$job_row['job_no']."'"));
    ?>
   <div class="<?=$screenwidth?>">
      <h2 align="center"><i class="fa fa-edit"></i> Make Invoice for OUT Warranty Jobs</h2>
      <h4 align="center">Job No.- <?=$docid?></h4>
   <div class="panel-group">
    <div class="panel panel-info table-responsive">
        <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>
         <div class="panel-body">
          <table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Customer Name</label></td>
                <td width="30%"><?php echo $job_row['customer_name'];?></td>
                <td width="20%"><label class="control-label">Address</label></td>
                <td width="30%"><?php echo $job_row['address'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Contact No.</label><br/><span class="red_small">(For SMS Update)</span></td>
                <td><?php echo $job_row['contact_no'];?></td>
                <td><label class="control-label">Alternate Contact No.</label></td>
                <td><?php echo $cust_det[6];?></td>
              </tr>
              <tr>
                <td><label class="control-label">State</label></td>
                <td><?php echo getAnyDetails($job_row["state_id"],"state","stateid","state_master",$link1);?></td>
                <td><label class="control-label">Email</label></td>
                <td><?php echo $cust_det[2];?></td>
              </tr>
              <tr>
                <td><label class="control-label">City</label></td>
                <td><?php echo getAnyDetails($job_row["city_id"],"city","cityid","city_master",$link1);?></td>
                <td><label class="control-label">Pincode</label></td>
                <td><?php echo $job_row['pincode'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Customer Category</label></td>
                <td><?php echo $job_row['customer_type'];?></td>
                <td><label class="control-label">Residence No</label></td>
                <td><?php echo $cust_det[3];?></td>
              </tr>
			   <tr>
                <td><label class="control-label">Landmarks</label></td>
                <td><?php echo $cust_det[1];?></td>
                <td><label class="control-label"></label></td>
                <td><?php ?></td>
              </tr>
			  	   <tr>
                <td><label class="control-label">Date Of Birth</label></td>
                <td><?php echo $cust_det[4];?></td>
                <td><label class="control-label">Marriage Date</label></td>
                <td><?php  echo $cust_det[5]; ?></td>
              </tr>
            </tbody>
          </table>
        </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
      <div class="panel-body">
       <table class="table table-bordered" width="100%">
            <tbody>
            <tr>
                <td width="20%"><label class="control-label">Product</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["product_id"],"product_name","product_id","product_master",$link1);?></td>
                <td width="20%"><label class="control-label">Brand</label></td>
                <td width="30%"><?php echo getAnyDetails($job_row["brand_id"],"brand","brand_id","brand_master",$link1);?></td>
              </tr>
            <tr>
              <td><label class="control-label">Model</label></td>
              <td><?=$job_row['model']?></td>
              <td><label class="control-label">Date Of Installation</label></td>
              <td><?=dt_format($product_det['installation_date'])?></td>
            </tr>
            <tr>
              <td><label class="control-label"><?php echo SERIALNO ?></label></td>
              <td><?=$job_row['imei']?></td>
              <td><label class="control-label">Call Source</label></td>
              <td><?=$job_row['call_type']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Warranty Status</label></td>
              <td><?=$job_row['warranty_status']?></td>
              <td><label class="control-label">Job For</label></td>
              <td><?=$job_row['call_for']?></td>
            </tr>
            <tr>
              <td><label class="control-label">Purchase Date</label></td>
              <td><?=dt_format($job_row['dop'])?></td>
              <td><label class="control-label">Warranty End Date</label></td>
              <td><?=dt_format($product_det['warranty_end_date'])?></td>
            </tr>
			
			 <tr>
			  <td><label class="control-label">Date Of Installation</label></td>
              <td><?=dt_format($product_det['installation_date'])?></td>
              <td><label class="control-label">Purchase From </label></td>
              <td ><?php echo getAnyDetails($job_row['entity_type'],"name","id","entity_type",$link1);?></td>
              
            </tr>
			 <tr>
              <td><label class="control-label">Dealer Name</label></td>
              <td><?=$job_row['dname']?></td>
              <td><label class="control-label">Invoice No</label></td>
              <td><?=$job_row['inv_no']?></td>
            </tr>
            </tbody>
          </table>
      </div><!--close panel body-->
    </div><!--close panel-->
    <div class="panel panel-info table-responsive">
      <div class="panel-heading"><i class="fa fa-wrench fa-lg"></i>&nbsp;&nbsp;Repair Action</div>
      <div class="panel-body">
      <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
       <table class="table table-bordered" width="100%">
          <thead>
            <tr>
                <th width="20%"><label class="control-label">Consumed Part</label></th>
                <th width="8%"><label class="control-label">HSN Code</label></th>
                <th width="8%"><label class="control-label">Part/Service Cost</label></th>
                <?php if($job_row['state_id']==$_SESSION['stateid']){?>
                <th width="8%"><label class="control-label">CGST %</label></th>
                <th width="10%"><label class="control-label">CGST Amt</label></th>
                <th width="8%"><label class="control-label">SGST %</label></th>
                <th width="10%"><label class="control-label">SGST Amt</label></th>
                <?php }else{?>
                <th width="8%"><label class="control-label">IGST %</label></th>
                <th width="10%"><label class="control-label">IGST Amt</label></th>
                <?php }?>
                <th width="10%"><label class="control-label">Total Amt</label></th>
            </tr>
          </thead>
          <tbody>
          <?php
		  $i=1;
		  $res_consume = mysqli_query($link1,"SELECT * FROM repair_detail where job_no='".$docid."'  and  	warranty_status ='OUT'")or die("error1".mysqli_error($link1));
		  while($row_consume = mysqli_fetch_assoc($res_consume)){
			  if($row_consume['partcode']){
			  ///// fetch part details
			  $row_partdet = mysqli_fetch_assoc(mysqli_query($link1,"SELECT part_name, hsn_code, customer_price FROM  partcode_master where partcode='".$row_consume['partcode']."'"));
			  $part_tax = mysqli_fetch_array(mysqli_query($link1,"SELECT igst,sgst,cgst FROM tax_hsn_master where hsn_code='".$row_partdet['hsn_code']."' and status='1'")) ;
			  ///calculate taxes
			  $cgst_val = round(($part_tax['cgst'] * $row_partdet['customer_price']) / 100);
			  $sgst_val = round(($part_tax['sgst'] * $row_partdet['customer_price']) / 100);
			  $igst_val = round(($part_tax['igst'] * $row_partdet['customer_price']) / 100);
          ?>
            <tr>
              <td><?php echo $row_partdet['part_name'];?><input name="part[]" id="part<?=$i?>" type="hidden" value="<?=$row_consume['partcode']?>" readonly/><input name="partname[]" id="partname<?=$i?>" type="hidden" value="<?=$row_partdet['part_name']?>" readonly/></td>
              <td><input name="hsn_code[]" id="hsn_code<?=$i?>" type="text" class="form-control" style="width:100px;background-color:#CCCCCC;" value="<?=$row_partdet['hsn_code']?>" readonly /></td>
              <td><input name="part_cost[]" id="part_cost<?=$i?>" type="text" class="form-control" style="width:80px;background-color:#CCCCCC;text-align:right" value="<?=$row_partdet['customer_price']; ?>" readonly/></td>
              <?php if($job_row['state_id']==$_SESSION['stateid']){?>
              <td><input name="cgstper[]" id="cgstper<?=$i?>" type="text" class="form-control" style="width:80px;background-color:#CCCCCC;text-align:right" value="<?=$part_tax['cgst']?>" readonly/></td>
              <td><input name="cgstamt[]" id="cgstamt<?=$i?>" type="text"  class="form-control" style="width:100px;background-color:#CCCCCC;text-align:right" value="<?=$cgst_val?>" readonly /></td>
              <td><input name="sgstper[]" id="sgstper<?=$i?>" type="text" class="form-control" style="width:80px;background-color:#CCCCCC;text-align:right" value="<?=$part_tax['sgst']?>" readonly/></td>
              <td><input name="sgstamt[]" id="sgstamt<?=$i?>" type="text"  class="form-control" style="width:100px;background-color:#CCCCCC;text-align:right" value="<?=$sgst_val?>" readonly /></td>
              <?php $linetotal = $cgst_val + $sgst_val + $row_partdet['customer_price'];}else{?>
              <td><input name="igstper[]" id="igstper<?=$i?>" type="text" class="form-control" style="width:80px;background-color:#CCCCCC;text-align:right" value="<?=$part_tax['igst']?>" readonly/></td>
              <td><input name="igstamt[]" id="igstamt<?=$i?>" type="text"  class="form-control" style="width:100px;background-color:#CCCCCC;text-align:right" value="<?=$igst_val?>" readonly /></td>
              <?php $linetotal = $igst_val + $row_partdet['customer_price'];}?>
              <td><input name="totalamt[]" id="totalamt<?=$i?>" type="text"  class="form-control" style="width:100px;background-color:#CCCCCC;text-align:right" value="<?=$linetotal?>" readonly /></td>
            </tr>
         <?php
		 $i++;
			  }
		  }
		  ////// pick tax and HSN code for service charge
			  
		/// old
		///$ser_tax = mysqli_fetch_array(mysqli_query($link1,"SELECT hsn_code,igst,sgst,cgst FROM tax_hsn_master where hsn_code='998716' and status='1'")) ;
		$ser_tax = mysqli_fetch_array(mysqli_query($link1,"SELECT hsn_code,igst,sgst,cgst FROM tax_hsn_master where hsn_code='851212' and status='1'")) ;
			  
			  
		  $serprice= getAnyDetails($job_row["model_id"],"ser_charge","model_id","model_master",$link1);
		 ?>
		       
         <tr>
                <td width="20%"><label class="control-label">Service Charge</label></td>
                <td width="8%"><input name="ser_tax_hsn" type="text" class="form-control" id="ser_tax_hsn" value="<?=$ser_tax['hsn_code'];?>"  readonly="readonly" style="width:100px;background-color:#CCCCCC;"/></td>
                <td width="8%"><input name="ser_charge" type="text" class="number required form-control" id="ser_charge"  onKeyUp="getCost_service()" value="<?=$serprice?>"  style="width:80px;text-align:right" required/></td>
                <?php if($job_row['state_id']==$_SESSION['stateid']){?>
                <td width="8%"><input name="cgst_ser_tax_per" type="text" class="form-control" id="cgst_ser_tax_per" value="<?=$ser_tax['cgst'];?>"  readonly="readonly" style="width:80px;background-color:#CCCCCC;text-align:right" /></td>
                <td width="10%"><input name="cgst_ser_tax_amt" type="text" class="form-control" id="cgst_ser_tax_amt"  value="0.00" readonly style="width:100px;background-color:#CCCCCC;text-align:right"/></td>
                <td width="8%"><input name="sgst_ser_tax_per" type="text" class="form-control" id="sgst_ser_tax_per"  value="<?=$ser_tax['sgst'];?>"readonly="readonly" style="width:80px;background-color:#CCCCCC;text-align:right"/></td>
                <td width="10%"><input name="sgst_ser_tax_amt" type="text" class="form-control" id="sgst_ser_tax_amt" value="0.00" readonly style="width:100px;background-color:#CCCCCC;text-align:right"/></td>
                <?php }else{?>
                <td width="8%"><input name="igst_ser_tax_per" type="text" class="form-control" id="igst_ser_tax_per" value="<?=$ser_tax['igst'];?>" readonly style="width:80px;background-color:#CCCCCC;text-align:right"/></td>
                <td width="10%"><input name="igst_ser_tax_amt" type="text" class="form-control" id="igst_ser_tax_amt" value="0.00" readonly style="width:100px;background-color:#CCCCCC;text-align:right"/></td>
                <?php }?>
                <td width="10%"><input name="taotal_ser_tax_amt" type="text" class="form-control" id="taotal_ser_tax_amt"  value="0.00" readonly style="width:100px;background-color:#CCCCCC;text-align:right"/></td>
            </tr>
			   <tr>
                <td width="20%"><label class="control-label">Discount</label></td>
                <td width="8%">&nbsp;</td>
                <td width="8%"><input name="dis_count" type="text" class="number required form-control" id="dis_count"  onKeyUp="getCost_discount()"  value="0.00"  style="width:80px;text-align:right" required/></td>
                <?php if($job_row['state_id']==$_SESSION['stateid']){?>
				  <td width="8%" colspan="4"> <span id="errmsg" class="red_small"></span></td>
               
                <?php }else{?>
                <td width="8%" colspan="2"> <span id="errmsg" class="red_small"></span></td>
           
                <?php }?>
                  <td width="8%">&nbsp;</td>
            </tr>
         <tr>
           <td><strong>Total</strong></td>
           <td align="right"><strong>Part Cost</strong></td>
           <td><input name="tot_cost" type="text" class="form-control" id="tot_cost" value="0.00" readonly style="width:80px;background-color:#CCCCCC;text-align:right"/></td>
          <?php if($job_row['state_id']==$_SESSION['stateid']){?>
           <td align="right"><strong>CGST Amt</strong></td>
           <td><input name="cgsttotal" type="text" class="form-control" id="cgsttotal" value="0.00" readonly style="width:100px;background-color:#CCCCCC;text-align:right"/></td>
           <td align="right"><strong>SGST Amt</strong></td>
           <td><input name="sgsttotal" type="text" class="form-control" id="sgsttotal" value="0.00" readonly style="width:100px;background-color:#CCCCCC;text-align:right"/></td>
           <?php }else{?>
           <td align="right"><strong>IGST Amt</strong></td>
           <td><input name="igsttotal" type="text" class="form-control" id="igsttotal" value="0.00"  readonly="readonly" style="width:100px;background-color:#CCCCCC;text-align:right"/></td>
           <?php }?>
           <td><input name="grandtotal" type="text" class="form-control" id="grandtotal" value="0.00" readonly style="width:100px;background-color:#CCCCCC;text-align:right"/>
		   </td>
         </tr>
		   <tr>
                <td width="20%"><label class="control-label">Total Amount After Discount</label></td>
                <td width="8%">&nbsp;</td>
                <td width="8%"><input name="grandtotal_dis" type="text" class="number required form-control" id="grandtotal_dis"   value="0.00"  style="width:80px;text-align:right" required/></td>
                <?php if($job_row['state_id']==$_SESSION['stateid']){?>
                <td width="8%">&nbsp;</td>
                 <td width="8%">&nbsp;</td>
                <td width="8%">&nbsp;</td>
               <td width="8%">&nbsp;</td>
                <?php }else{?>
                <td width="8%">&nbsp;</td>
              <td width="8%">&nbsp;</td>
                <?php }?>
                 <td width="8%">&nbsp;</td>
            </tr>
         	<tr>
           		<td colspan="10" align="center"><input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='job_outws_inv.php?<?=$pagenav?>'"><input name="num" id="num" type="hidden"  class="form-control" style="width:60px;" value="<?=$i?>" readonly/>&nbsp;&nbsp;<input name="makeinv" id="makeinv" type="submit" class="btn<?=$btncolor?>" value="Make Invoice" title="Generate invoice for this job"></td>
            </tr>
           </tbody>
          </table>
          </form>
      </div><!--close panel body-->
    </div><!--close panel-->
	
  </div><!--close panel group-->
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>