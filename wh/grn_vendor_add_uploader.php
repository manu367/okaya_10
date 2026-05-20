<?php 
require_once("../includes/config.php");
////get access product details
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
 //echo $access_product;

////get access brand details
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);

////// get address of vendor / billfrom/bill to //////////////////////////////////
$vendor_addrs  = getAnyDetails($_REQUEST['vendor'],"address","id","vendor_master",$link1);

$from  = getAnyDetails($_REQUEST['bill_from'],"locationaddress,stateid","location_code","location_master",$link1);
//print_r($from);
$fromdet = explode("~", $from);
// echo $fromdet[1]."from";

$to  = getAnyDetails($_REQUEST['bill_to'],"locationaddress,stateid","location_code","location_master",$link1);
//print_r($to);
$todet = explode("~",$to);
//echo $todet[1]."to";

//////////////// after hitting upload button

@extract($_POST);
//////  if we want to Add new po
if ($_POST['add']=='ADD' && $_SESSION['asc_code']!=''){
	$flag = TRUE;
    mysqli_autocommit($link1, false);
    $error_msg = "";
	if ($_FILES["file"]["name"]) {
		require_once "../includes/simplexlsx.class.php";
		$xlsx = new SimpleXLSX($_FILES['file']['tmp_name']);	
		move_uploaded_file($_FILES["file"]["tmp_name"],"../ExcelExportAPI/upload/".$now.$_FILES["file"]["name"]);
		$f_name=$now.$_FILES["file"]["name"];
		list($cols) = $xlsx->dimension();		
		// Checkmodal and partcode

		// echo "Select model_id,partcode,status,hsn_code,l3_price from partcode_master where status='1'";

		 $partcode_details=mysqli_fetch_assoc(mysqli_query($link1,"Select product_id,brand_id,model_id,partcode,status,hsn_code,l3_price from partcode_master where status='1'"));
		$mod_dup1 = array(); ///modal code
		$part_dup2 = array(); ///Partcode code 
		$total_count = '0';
		// echo $total_count.'total count';

		$total_count = count($xlsx->rows()); ///// count no. of rows in uploading sheet ////
		// echo "count sheet ";
		// {
		// 	echo "Model id";
			foreach ($xlsx->rows() as $k => $r) {
				 //print_r($r);
				if ($k == 0 || $k == 1) continue; // skip first row
				for ($i = 0; $i < count($k); $i++) {
					//print_r($r);
					//  exit;
					if ($r[0] == '' && $r[1] == '' && $r[2] == '') {
					} 
					else if ($r[0] == "EOF") 
					{
						$eof = "1";
					} 
					else 
					{
						////Make Variable for each element of excel//////		
						$mod_dup1[] = "" . $r[1]; ///Prohibited Duplicate Moadal code
						$part_dup2[] =  "" . $r[2]; ///Prohibited Duplicate Partcode code
					}
				} /////for loop closed
			} /////////////froeach loop closed
			$mod_count1 = count($mod_dup1);
			$prod_count1 = count($part_dup2);
			$mod_count2 = count(array_unique($mod_dup1));
			$prod_count2 = count(array_unique($part_dup2));
			if ($mod_count1 == $mod_count2 && $prod_count1 == $prod_count2)
			{
				// echo "unique"   ;				
				foreach ($xlsx->rows() as $k => $r) 
			 	{
					if ($k == 0 || $k == 1)  continue; // skip first row 
					for ($i = 0; $i < count($k); $i++)
					{   
						if ($r[0] == "EOF")
						{
						$eof = "1";
						}   
						else
						{
							////Make Variable for each element of excel//////
							$modal=''; $quantity=''; $partcode=''; $price='';$row_cost='';$total_info='';
							
							$modal = "" .$r[0];
							$partcode = "" . $r[1];
							$quantity = "" . $r[2];	

							//echo "select l3_price from partcode_master where FIND_IN_SET('$modal',model_id) and partcode ='$partcode'";

							$check_price=mysqli_fetch_assoc(mysqli_query($link1, "select l3_price from partcode_master where FIND_IN_SET('$modal',model_id) and partcode ='$partcode'"));
							$price=$check_price['l3_price'];
							

							///// calculate line total
							$row_cost=$price*$quantity;
							
							//CHECK Partcode and Modal ID IS ALREADY PRESENT IN Partcode Master DATABASE OR NOT !

							//echo "Select model_id,partcode,status from partcode_master where partcode='$partcode'  and FIND_IN_SET('$modal',model_id)";
							// exit;
							$part_code_check=mysqli_fetch_assoc(mysqli_query($link1, "Select model_id,partcode,status from partcode_master where partcode='$partcode' and FIND_IN_SET('$modal',model_id)"));
							if($part_code_check<=0)
							{
								echo "Flag->1".$flag;
								$flag=false;
								$cflag = "danger";
								$error_msg = "Please Enter Correct Partcode With its Mapped Modal !";
								header("location:grn_vendor.php?msg=".$error_msg."&chkflag=".$cflag."&chkmsg=".$pagenav);
								exit;
							}
							if ($part_code_check['model_id']!="")
							{
									$chk_modal = $r[0];
								 //  echo $chk_modal."chk_modal";
							} 
							else
							{
								echo "Flag->2".$flag;
								$flag=false;
								$cflag = "danger";
								$error_msg = "Please Enter Correct Modal !";
								// header("location:grn_vendor.php?msg=".$error_msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
								//exit;
							}  							
							if ($part_code_check['status']!='1')
							{
								echo "Flag->3".$flag;
								$flag=false;
								$cflag = "danger";
								$error_msg = "Please Enter Active Partcode Only !". mysqli_error($link1) . ".";
								// header("location:grn_vendor.php?msg=".$error_msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
								// exit;
							} 
							if ($part_code_check['partcode']!="")
							{
									$partcode = $r[1];
									//   echo partcode;
							}
							
							if($quantity=='' && $price=='' || $quantity<=0 ||$price<=0)
							{
								echo "Flag->4".$flag;
								$flag=false;
								$err_msg = "Quantity Or Price is Blank Or Less than Zero";   
								$cflag = "danger";
								$error_msg = "Please Enter All the Values. ";
								// header("location:grn_vendor.php?msg=".$error_msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
								exit;             
							}
							$part_tax_cal=mysqli_fetch_assoc(mysqli_query($link1,"SELECT hsn_code,sgst,igst,cgst FROM `tax_hsn_master` where status='1' and hsn_code='".$partcode_details['hsn_code']."'"));
							//////// Check HSN Avilabale or not////////
							if($part_tax_cal!='')
							{
								//////intialize tax variables
								$sgst_final_val=0;
								$cgst_final_val=0;
								$igst_final_val=0;								
								////// initialize line tax variables
								$cgst_per=0;
								$cgst_val=0;
								$sgst_per=0;
								$sgst_val=0;
								$igst_per=0;
								$igst_val=0;
								$tot_val=0;
								
								//// check if dispatcher and receiver belongs to same state then tax should be apply as SGST&CGST (In india)
								if($fromstate==$to_state)
								{
									//---------- CGST & SGST Applicable-----------//
									$cgst_per = $part_tax_cal['cgst'];
									$sgst_per = $part_tax_cal['sgst'];
									$igst_per = "0";								
									/////// calculate cgst and sgst	
									$cgst_val = ($cgst_per * $row_cost) / 100;
									$cgst_final_val = $cgst_final_val + $cgst_val;
									$sgst_val = ($sgst_per * $row_cost) / 100;
									$sgst_final_val = $sgst_final_val + $sgst_val;
									$igst_final_val=0;
									$tot_val = $row_cost + $cgst_val + $sgst_val+$igst_final_val;
									$final_Amount+=$tot_val;
									$final_cgst+=$cgst_final_val;
									$final_sgst+=$sgst_final_val;
									$final_igst+=$igst_final_val;

									//echo "final Amount--->".	$final_Amount."<---";
								}
								// if($fromstate!=$to_state)
								else
								{
									//// check if dispatcher and receiver belongs to different state then tax should be apply as IGST (In india) 
									//--------------- IGST Applicable----------------------//
									$igst_per = $part_tax_cal['igst'];
									$cgst_per = "0";
									$sgst_per = "0";
									/////// calculate igst
									$igst_val = ($igst_per * $row_cost) / 100;
									$igst_final_val = $igst_final_val + $igst_val;
									$cgst_final_val=0;
									$sgst_final_val=0;
									$tot_val = $row_cost + $igst_val;
									$final_Amount+=$tot_val;									
									$final_cgst+=$cgst_final_val;
									$final_sgst+=$sgst_final_val;
									$final_igst+=$igst_final_val;
									
								}
							}// close hsn not empty
							else
							{
								echo "Flag->5".$flag;
								$flag=false;
								$cflag = "danger";
								$error_msg = "Tax is Not Available For Partcode";  
								header("location:grn_vendor.php?msg=".$error_msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
								exit;
							}							
							//echo "select max(ch_temp) as no from supplier_po_master where location_code='".$_SESSION['asc_code']."'";
							/////// genrate challan //////
							$res_po = mysqli_query($link1,"select max(ch_temp) as no from supplier_po_master where location_code='".$_SESSION['asc_code']."'");
							$row_po = mysqli_fetch_array($res_po);
							$c_nos = $row_po[no]+1;
							$po_no = $_SESSION['asc_code']."V".$c_nos;
								
							// echo "<br>"."<br>";
							//  echo"insert into supplier_po_data set location_code  ='".$_SESSION['asc_code']."' , system_ref_no='".$po_no."',product_id ='".$partcode_details['product_id']."', brand_id ='".$partcode_details['brand_id']."',model_id='".$r[0] . "', partcode ='".$r[1]."', qty='".$r[2]."' ,req_qty='".$r[2]."' ,price ='".$price."', cost='".$row_cost."',total_cost='".$tot_val."'  ,entry_date = '".$today."' ,status='7',flag='1' ,cgst_per='".$cgst_per."',cgst_amt='".$cgst_final_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_final_val."',igst_per='".$igst_per."',igst_amt='".$igst_final_val."' ";

							// echo "<br>"."<br>";
							

							$po_data_add = "insert into supplier_po_data set location_code  ='".$_SESSION['asc_code']."' , system_ref_no='".$po_no."',product_id ='".$partcode_details['product_id']."', brand_id ='".$partcode_details['brand_id']."',model_id='".$r[0] . "', partcode ='".$r[1]."', qty='".$r[2]."' ,req_qty='".$r[2]."' ,price ='".$price."', cost='".$row_cost."',total_cost='".$tot_val."'  ,entry_date = '".$today."' ,status='7',flag='1' ,cgst_per='".$cgst_per."',cgst_amt='".$cgst_final_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_final_val."',igst_per='".$igst_per."',igst_amt='".$igst_final_val."'";
							$result1 = mysqli_query($link1,$po_data_add);
							//// check if query is not executed
							if (!$result1) {
								echo "Flag->6".$flag;
								$flag = false;
								$error_msg = "Error details 3 : " . mysqli_error($link1) . ".";
							}
								
						}// close  else
					}// close for					
				}// for each
				////// insert in master table ///////
				// echo "<br>"."<br>";
				// echo "INSERT INTO supplier_po_master set system_ref_no = '".$po_no."' , entry_date = '".$today."' , location_code = '".$_SESSION['asc_code']."' , ship_address2 = '".$to_add1."', party_name = '".$supplier."' , bill_to = '".$billto."' , ch_temp='".$c_nos."' , bill_address ='".$fromadd."' , status='7' , po_type = 'PTV', comp_code = '".$billto."' , user_code = '".$supplier."',grand_amt='".$final_Amount."',total_amt='".$final_Amount."',total_sgst_amt='".$final_sgst."',total_cgst_amt='".$final_cgst."',total_igst_amt='".$final_igst."'";			
				//	die;
				$po_add = "INSERT INTO supplier_po_master set system_ref_no = '".$po_no."' , entry_date = '".$today."' , location_code = '".$_SESSION['asc_code']."' , ship_address2 = '".$to_add1."', party_name = '".$supplier."' , bill_to = '".$billto."' , ch_temp='".$c_nos."' , bill_address ='".$fromadd."' , status='7' , po_type = 'PTV', comp_code = '".$billto."' , user_code = '".$supplier."',grand_amt='".$final_Amount."',total_amt='".$final_Amount."',total_sgst_amt='".$final_sgst."',total_cgst_amt='".$final_cgst."',total_igst_amt='".$final_igst."'";
					$result=mysqli_query($link1,$po_add);

					//// check if query is not executed
					if (!$result) {
						echo "Flag->7".$flag;
						$flag = false;
						$error_msg = "Error details 2 : " . mysqli_error($link1) . ".";
					}
			}// close IF unique condition
			else
			{
				echo "Flag->8".$flag;
				$flag=false;
				$cflag = "danger";
				$error_msg = "Duplicate Modal Code OR Partcode is in Excel Sheet";  
				header("location:grn_vendor.php?msg=".$error_msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
				exit;
			}
			//// check excel file is completely uploaded///
			// if($eof=='1')
			// {
			// 	$cflag = "success";
			// 	$cmsg = "Success";
			// 	$error_msg = "PO to Vendor file is successfully placed with ref. no.".$po_no;
			// 	header("location:grn_vendor.php?msg=".$error_msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
			// 	exit;
			// }
			// else
			// {
			// 	$cflag = "danger";		  
			// 	$msg = "Error in file ofAdd PO to Vendor";
			//   	header("location:grn_vendor.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
			// 	exit;
			// }
		// }// Close if model_id
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
			$msg = "Request could not be processed. Please try again.";
		} 
		mysqli_close($link1);
		///// move to parent page
		header("location:grn_vendor.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
		exit;
	}// close file 
}//close submit

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
 <!-- <script type="text/javascript" src="../js/jquery.validate.js"></script> -->
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
      <h2 align="center"><i class="fa fa-ship"></i> Add PO UPLOADER </h2><br/>	 
      <div class="form-group" id="page-wrap" style="margin-left:10px;">
          <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
         <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Supplier Name</label>	  
			<div class="col-md-6" >
				<select   name="vendor" id="vendor" class="form-control required selectpicker" data-live-search="true" onChange="document.frm1.submit();">
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
                 <textarea id="from_addrs" name="from_addrs" style="resize:vertical" class="form-control required"><?=$fromdet[0];?></textarea>
              </div>
          </div>
	    </div>
		<div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label"> Ship to:</label>	  
			<div class="col-md-6" >
            
    
				 <select name="bill_to" id="bill_to" class="form-control required selectpicker" data-live-search="true" onChange="document.frm1.submit();" >
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
                  <textarea id="to_addrs" name="to_addrs" class="form-control required" style="resize:vertical"><?=$todet[0];?></textarea>
              </div>
          </div>
	    </div>
         </form>
          <h4 align="center"><span id="error_msg" class="red_small" style="text-align:center;margin:10px;"></span></h4>
		<form id="frm2" name="frm2" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
		<div class="form-group">
          <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-3 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-3">
                    <div>
                        <label >
                        <span>
                            <input type="file"  name="file" class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required / > 
                        </span>                    
                        </label>             
                        <div><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
                    </div>
                </div>
                <div style="float:right;margin-right:7%;"><a href="../templates/PO_UPLOADER.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/>Download Excel Template</a></div><br></br>             
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">     
              <input type="submit" class="btn btn<?=$btncolor?>" name="add" id="add" value="ADD" title="Add New PO">
			  <input type="hidden" name="billto" id="billto" value="<?=$_REQUEST['bill_to']?>"/>
              <input type="hidden" name="billfrom" id="billfrom" value="<?=$_REQUEST['bill_from']?>"/>
			  <input type="hidden" name="supplier" id="supplier" value="<?=$_REQUEST['vendor']?>"/>
              <input type="hidden" name="fromadd" id="fromadd" value="<?=$fromdet[0];?>"/>
			  <input type="hidden" name="to_add1" id="to_add1" value="<?=$todet[0]?>"/>
			  <input type="hidden" name="fromstate" id="fromstate" value="<?=$fromdet[1];?>"/>
			  <input type="hidden" name="to_state" id="to_state" value="<?=$todet[1];?>"/>
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
	// $("#frm2").find("input:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");

	$("#frm2").find("input:enabled").attr("disabled", "disabled");
</script>
<?php
}
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>