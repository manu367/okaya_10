<?php 
require_once("../includes/config.php");
//////////////// after hitting upload button
@extract($_POST);
if($_POST['Submit']=="Upload"){
mysqli_autocommit($link1, false);
$flag = true;
if ($_FILES["file"]["error"] > 0)
{	
$code=$_FILES["file"]["error"];
}
else
{
move_uploaded_file($_FILES["file"]["tmp_name"],
"../ExcelExportAPI/upload/".$now.$_FILES["file"]["name"]);
$file="../ExcelExportAPI/upload/".$now.$_FILES["file"]["name"];
chmod ($file, 0755);
}

$filename=$file;
////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
error_reporting(E_ALL ^ E_NOTICE);
 $path = '../ExcelExportAPI/Classes/';
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);//we specify the path" using linux"
        function __autoload($classe)
        {
            $var = str_replace
            (
                '_', 
                DIRECTORY_SEPARATOR,
                $classe
            ) . '.php' ;
            require_once($var);
        }   

 $indentityType = PHPExcel_IOFactory::identify($filename);
                $object = PHPExcel_IOFactory::createReader($indentityType);
                $object->setReadDataOnly(true);
                $objPHPExcel = $object->load($filename);
               /*echo '<script>alert("le fichier a été chargé avec succes !");</script>';*/
                $sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
                $highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
                $highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
                $indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
				$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
				//Checking data
				$part_count=0;
				$error_msg = "";
				
			
	$browserid=session_id();
	$req_res=mysqli_query($link1,"delete from po_pna_temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$req_res) {
		$flag = false;
		$err_msg = "Error Code1.1:". mysqli_error($link1);
	}
					
					
				//$p=array();
				//$q=array();
				$part_arr = array();
				   for($row1 =2 ;$row1 <= $highest;$row1++){	
				    $part_code1 = trim($sheet->getCellByColumnAndRow(5,$row1)->getValue()); ///partcode in use 
					$post_pendqty1 = trim($sheet->getCellByColumnAndRow(9,$row1)->getValue()); ///// dispatch QTY
				   	//$pt=array_push($p,$part_code1);
					//$qt=array_push($q,$post_pendqty1);
					$part_arr[$part_code1] += $post_pendqty1;
				   }
				  
					 //$unique_p= array_unique($p);
					 	//print_r($part_arr);
						//echo "<br/>";
					// foreach($unique_p as $pkey=>$pval){
					$minqty = 0;
					  foreach($part_arr as $partcode=>$qty){	 	
						 //$keyarr=array_keys($p,$pval);
						 //echo print_r($keyarr)."&nbsp;";
						 //$sum_q=0;
						 /*for($r =0 ;$r <= count($keyarr);$r++){
							 $sum_q+=$q[$keyarr[$r]]; 	
						 }
						  echo $sum_q;
						  echo "<br/>";*/
			 		$client_inventory   = mysqli_query($link1 , "select okqty  from  client_inventory where  okqty>=".$qty." and  partcode = '".$partcode."' and  location_code = '".$_SESSION['asc_code']."' "	);	
					 if(mysqli_num_rows($client_inventory)>0){
						 $process = true;
						 $minqty += $qty;
					 }////// Dispatch_qtyis equal to or less than to dispacht qty
					 else if(mysqli_num_rows($client_inventory)==0 && $qty==0){
					 	$process = true;
						$minqty += $qty;
					 }
					else {
						  $flag = false;
						  $process = false;
					      $err_msg= "Dispatch Qty is not avilable in your Stock-:".$partcode;
				     }	
					 
					 } 
					 //exit;
			//////  Check Dispactch Qty is Avilable to Wh Stock ////////////////////	
		if($process == true){		 
			if($minqty>0){
              for($row =2 ;$row <= $highest;$row++){	
			  		$frm_loc= trim($sheet->getCellByColumnAndRow(1,$row)->getValue()); ///// Requested Loction
			        $po_no= trim($sheet->getCellByColumnAndRow(2,$row)->getValue()); ///// po_no
					$job_no= trim($sheet->getCellByColumnAndRow(3,$row)->getValue()); ///// job_no
					$potype= trim($sheet->getCellByColumnAndRow(4,$row)->getValue()); ///// type of PO
                    $part_code = trim($sheet->getCellByColumnAndRow(5,$row)->getValue()); ///partcode in use 
					$post_reqdqty = trim($sheet->getCellByColumnAndRow(7,$row)->getValue()); ///// pending QTY
					$post_pendqty = trim($sheet->getCellByColumnAndRow(8,$row)->getValue()); ///// pending QTY
					$post_dispqty = trim($sheet->getCellByColumnAndRow(9,$row)->getValue()); ///// QTY
					
			if($post_dispqty>0){		
					$from_code=$_SESSION['asc_code'];  /////
					$doc_type=$_POST['doc_type'];////
					$po_type=$_POST['type'];////
					$dcket=$_POST['docket_no'];////
					$courier=$_POST['courier_name'];////
					

			  if($po_type==$potype)	{
			$sql1=mysqli_query($link1,"select id from po_master where po_no='".$po_no."' and  from_code='".$frm_loc."' and to_code='".$from_code."' and status in ('1','6') ");
			$check_po_master=mysqli_num_rows($sql1);
			/////////// Check PO no is pending or Partial ////////////
			if($check_po_master>0){
				$sql2=mysqli_query($link1,"select id from po_items where po_no='".$po_no."' and  partcode='".$part_code."' and status in ('1','6') and job_no='".$job_no."' ");
				$check_po_items=mysqli_num_rows($sql2);
				///////////// Check Po is Dispatched or not //////
				if($check_po_items>0){
		//////////////  Uploded Data as  equal to requestd type /////////// 
		  if($po_type==$potype)	
			{
				/////////// Check  PO No and Partcode is not blank ///////// 
			  if($po_no!='' && $part_code!='')	
				{	
				///////////////  Dispatched Qty is Not blank  ////////
				  if($post_dispqty!='' && $post_dispqty!=" " )	
				   {
					   /////////////// Check Dispatch Qty should be eaual or less then  to requested qty////////
				      if($post_dispqty <= $post_pendqty)	
						{
								/////Part master model, product, Brand  check is active
							$part_check=mysqli_query($link1,"select  part_name,partcode,location_price,product_id,brand_id,model_id,hsn_code,l5_price from partcode_master where partcode='".$part_code."'  and status='1'");
									if(mysqli_num_rows($part_check)>0){
										
									$partcode_check=mysqli_fetch_array($part_check);
									$product_id=$partcode_check['product_id'];
									$brand_id=$partcode_check['brand_id'];
									
									/////product check is active
					$product_check=mysqli_num_rows(mysqli_query($link1,"select product_id from product_master where product_id='".$product_id."' and status='1'"));
					if($product_check>0)
					{
									  /////Brand check is active
					   $res_brand = mysqli_query($link1,"select brand_id,brand,status from brand_master where brand_id='".$brand_id."'");
					   $row_brand = mysqli_fetch_assoc($res_brand);
						 if($row_brand["status"]=="1"){
						 
									/////// HSN MASTER AVILABLE OR NOT HSN CODE ////////
									$rs_hsn_tax  = mysqli_query($link1,"select id,cgst,igst,sgst from tax_hsn_master where hsn_code='".$partcode_check['hsn_code']."'")or die("part error2".mysqli_error($link1));
									$part_tax = mysqli_fetch_assoc($rs_hsn_tax) ;
									//////// Check HSN Avilabale or not////////
									if($part_tax['id']!=""){
									//////Part to cridet check
										 $fromlocdet = explode("~",getAnyDetails($from_code,"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));

	////// PO receiver

	$tolocdet = explode("~",getAnyDetails($to_code,"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,locationtype","location_code","location_master",$link1));
	
	if($tolocdet['9']=="WH"){
		$post_price=$partcode_check['l5_price'];
	}else{
		$post_price=$partcode_check['location_price'];
	}
										 
										 //////intialize tax variables
	$sgst_final_val=0;

	$cgst_final_val=0;

	$igst_final_val=0;

	$total_qty = 0;

	$total_reqqty = 0;

	$total_procqty = 0;
	///// calculate line total

				$cost = $post_price * $post_dispqty;	

				////// initialize line tax variables
				$cgst_per=0;

				$cgst_val=0;

				$sgst_per=0;

				$sgst_val=0;

				$igst_per=0;

				$igst_val=0;

				$tot_val=0;

				//// check if dispatcher and receiver belongs to same state then tax should be apply as SGST&CGST (In india) 

				if($fromlocdet['5'] == $tolocdet['5']){

				//----------------------------- CGST & SGST Applicable----------------------//

					if($_POST['doc_type']=='INV'){

						$cgst_per = $part_tax['cgst'];

						$sgst_per = $part_tax['sgst'];
						
						$igst_per = "0";

					}else{

						$cgst_per = "0";

						$sgst_per = "0";
						
						$igst_per = "0";

					}

					/////// calculate cgst and sgst	

					$cgst_val = ($cgst_per * $cost) / 100;

					$cgst_final_val = $cgst_final_val + $cgst_val;

					$sgst_val = ($sgst_per * $cost) / 100;

					$sgst_final_val = $sgst_final_val + $sgst_val;
					
					$igst_final_val=0;
	
					$tot_val = $cost + $cgst_val + $sgst_val+$igst_final_val;	

				}else{//// check if dispatcher and receiver belongs to different state then tax should be apply as IGST (In india) 

					//----------------------------- IGST Applicable----------------------//

					if($_POST['doc_type']=='INV'){

						$igst_per = $part_tax['igst'];
						
						$cgst_per = "0";

						$sgst_per = "0";

					}else{

						$cgst_per = "0";

						$sgst_per = "0";
						
						$igst_per = "0";

					}

					/////// calculate igst

					$igst_val = ($igst_per * $cost) / 100;

					$igst_final_val = $igst_final_val + $igst_val;
					
					$cgst_final_val=0;
					
					$sgst_final_val=0;

					$tot_val = $cost + $igst_val;

				}
			$pendqty=$post_reqdqty-$post_pendqty;
		 $add_po_temp="INSERT INTO po_pna_temp_disp_upd set from_location='".$from_code."',to_location='".$frm_loc."',brand_id='".$partcode_check['brand_id']."',model_id='".$partcode_check['model_id']."',product_id='".$partcode_check['product_id']."',partcode='".$partcode_check['partcode']."',req_qty='".$post_reqdqty."',qty='".$pendqty."',dis_qty='".$post_dispqty."',price='".$post_price."',cost='".$cost."',item_total='".$tot_val."',file_name='".$filename."',doc_type='".$doc_type."',entry_date='".$today."',remark='".$remark."',userid='".$_SESSION['userid']."',browserid='".$browserid."',po_type='".$po_type."',docket='".$dcket."',courier='".$courier."',job_no='".$job_no."',igst_per='".$igst_per."',cgst_per='".$cgst_per."',sgst_per='".$sgst_per."',igst_amt='".$igst_final_val."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',po_no='".$po_no."',hsn_code='".$partcode_check['hsn_code']."',part_name='".$partcode_check['part_name']."' ";
			//echo "<br><br>";
												$result_add_po_temp=mysqli_query($link1,$add_po_temp);	
												//// check if query is not executed
												if (!$result_add_po_temp) {
													$flag = false;
													$err_msg= "Error Fail_add_po_temp: " . mysqli_error($link1) . ".";
												}	
										
										
									/////// HSN Code Check //////
									}else {
									   $flag = false;
					                    $err_msg= "Tax not found in HSN TAX MASTER...-".$part_code;
									     }
							} /// Brand check 
							 else {
							       $flag = false;
					                $err_msg= $brand_id."-".$row_brand["brand"]." Brand is not available/deactive in Brand Master";
								   }
					 }///// product check 
					 else {
					 $flag = false;
					  $err_msg= "Product is not available in Product Master:";
					 }	
			 }////partcode check
									else {
									   $flag = false;
					                    $err_msg= "Partcode is not available in Partcode Master-".$part_code;
									 }			
								 
				
							}////dispatch qty is greater than pending qty
						else {
								$flag = false;
							  $err_msg= "Dispatch QTY is Greater Than To Pending qty partcode-:".$part_code;
							 }	 	
						}////qty check
						else {
								$flag = false;
							  $err_msg= "Qty Zero and Empty in Excel file for this partcode-:".$part_code;
							 }	 				 
						}
						else {
								$flag = false;
								$err_msg= "Excel file partcode and Po no empty For row-:".$row;
							  }
			  
					}
						else {
								$flag = false;
								$err_msg= "Excel file upload to Diffrent Of PO PNA";
							  }
			}else{
					$flag = false;
							  $err_msg="Part Already Dispatch Or PO Not Pending/Partial Processed-:".$part_code;
				}
			}else{
						$flag = false;
								  $err_msg= "PO No Already Dispatch Or Something Wrong-:".$po_no;
				}
					}
				else {
						$flag = false;
					    $err_msg= "Excel file upload to diffrent Of PO PNA";
				      }
				}
				}////// end of for loop 
				}else{
					$flag = false;
					$err_msg= "Dispatch atleast 1 qty";
				}
					}
					else{
					$flag = false;
					$err_msg= "Dispatch Qty is Greater Than To Avilable Qty-:".$err_msg;}

	   if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Successfully Uploaded ";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed.Please try again.".$err_msg;
	} 
    mysqli_close($link1);
	   ///// move to parent page
	 header("location:show_po_pna_temp_data.php?msg=".$msg."&chkflag=".$cflag."&to_location=".$_POST['to_location']."&doc_type=".$_POST['doc_type']."&chkmsg=".$cmsg."".$pagenav);
  exit;
            
}///// end of if condition



?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script type="text/javascript" src="../js/common_js.js"></script>
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/fileupload.js"></script>
<script language="javascript" type="text/javascript">
function makeDropdown(){
$('.selectpicker').selectpicker();
}
$(document).ready(function () {
	$('#ref_challan_date').datepicker({
		format: "yyyy-mm-dd",
        todayHighlight: true,
		autoclose: true
	});
});
</script>
<link rel="stylesheet" href="../css/datepicker.css">
  <script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-angle-double-right"></i>PO/PNA DISPATCH UPLOADER</h2><div style="display:inline-block;float:right"> 
      <?php if( $_REQUEST['type']!=''){?>
      <a href="../excelReports/pending_dispatch_po_pna.php?location=<?=base64_encode($_REQUEST['to_location']);?>&type=<?=base64_encode($_REQUEST['type']);?>" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/> Download Excel Template</a>
      <?php } ?>
      </div><br></br> 

      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <?php if($_REQUEST['msg']){?><br>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
           <!--<div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Dispatch Location Name<span class="red_small">*</span></label>
              <div class="col-md-4">
               <select name="to_location" id="to_location" class="form-control required" required onChange="document.frm1.submit();" >
                  
                  <?php
$map_wh = mysqli_query($link1,"select location_code,locationname from location_master where locationtype!='WH' and  statusid='1'"); 
while($row_wh = mysqli_fetch_assoc($map_wh)){				
?>
                  <option value="<?=$row_wh['location_code']?>" <?php if($_REQUEST['to_location'] == $row_wh['location_code']) { echo 'selected'; }?>>
                  <?=$row_wh['locationname']." (".$row_wh['location_code'].")"?>
                  </option>
                  <?php } ?>
                </select>
				</div>
             </div>
          </div>-->
           <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Type<span class="red_small">*</span></label>
              <div class="col-md-4">
               <select name="type" id="type" class="form-control required" required  onChange="document.frm1.submit();">
               <option value=''>--Please Select-</option>
                 <option value="PO" <?php if($_REQUEST['type'] == "PO") { echo 'selected'; }?>>MSL PO</option>
					  <option value="PNA" <?php if($_REQUEST['type'] == "PNA"){ echo 'selected'; }?>>PNA PO</option>
                </select>
				</div>
             </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Document Type<span class="red_small">*</span></label>
              <div class="col-md-4">
               <select name="doc_type" id="doc_type" class="form-control required" required >
               <option value=''>--Please Select-</option>
                 <option value="DC" <?php if($_REQUEST['doc_type'] == "DC") { echo 'selected'; }?>>DC</option>
					  <option value="INV" <?php if($_REQUEST['doc_type'] == "INV"){ echo 'selected'; }?>>INV</option>
                </select>
				</div>
             </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Courier Name</label>
              <div class="col-md-4">
               <input name="courier_name" id="courier_name" type="text" class="form-control " maxlength="50"  />
				</div>
             </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Docket No.</label>
              <div class="col-md-4">
               <input name="docket_no" id="docket_no" type="text" class="form-control" />
				</div>
             </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
                  <div>
                    <label >
                       <span>
                        <input type="file"  name="file" class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required / > 
                    </span>
                    </label>             
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">&nbsp;&nbsp;&nbsp;
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              
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