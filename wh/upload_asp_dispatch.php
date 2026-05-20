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
$model = $_POST['model'];
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
				$doctype_flag = 1;
		if($_POST['doc_type']=="INV"){
			//////pick max counter of INVOICE
			$sql_invcount = "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'";
			$res_invcount = mysqli_query($link1,$sql_invcount)or die("error1".mysqli_error($link1));
			$row_invcount = mysqli_fetch_array($res_invcount);
			$next_invno = $row_invcount['inv_counter']+1;
			/////update next counter against invoice
			$res_upd = mysqli_query($link1,"UPDATE invoice_counter set inv_counter = '".$next_invno."' where location_code='".$_SESSION['asc_code']."'");
			/// check if query is execute or not//
			if(!$res_upd){
				$flag = false;
				$error_msg = "Error1". mysqli_error($link1) . ".";
			}
			///// make invoice no.
			$invoice_no = $row_invcount['inv_series']."".$row_invcount['fy']."".str_pad($next_invno,4,"0",STR_PAD_LEFT);	
			$doctype_flag *= 1;
		}else if($_POST['doc_type']=="DC"){
			//////pick max counter of INVOICE
			$sql_dccount = "SELECT * FROM invoice_counter where location_code='".$_SESSION['asc_code']."'";
			$res_dccount = mysqli_query($link1,$sql_dccount)or die("error1".mysqli_error($link1));
			$row_dccount = mysqli_fetch_array($res_dccount);
			$next_dcno = $row_dccount['stn_counter']+1;
			/////update next counter against invoice
			$res_upd = mysqli_query($link1,"UPDATE invoice_counter set stn_counter = '".$next_dcno."' where location_code='".$_SESSION['asc_code']."'");
			/// check if query is execute or not//
			if(!$res_upd){
				$flag = false;
				$error_msg = "Error1". mysqli_error($link1) . ".";
			}
			///// make invoice no.
			$invoice_no = $row_dccount['stn_series']."".$row_dccount['fy']."".str_pad($next_dcno,4,"0",STR_PAD_LEFT);
			$doctype_flag *= 1;
		}else{
			$doctype_flag *= 0;
		}
		
		////////////////////////if invoice /Document no generated

			
					
					$asc_code=$_POST['asc_code'];
					$parentcode=$_SESSION['asc_code'];
					
			
						////// get basic details of both parties
					////// PO dispatcher
					$fromlocdet = explode("~",getAnyDetails($_SESSION['asc_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));
					////// PO receiver
					$tolocdet = explode("~",getAnyDetails($_POST['asc_code'],"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno,locationtype","location_code","location_master",$link1));
				//initialize variables
				$sgst_final_val=0;
				$cgst_final_val=0;
				$igst_final_val=0;
				$basic_cost=0;
				$total_qty = 0;
				$total_reqqty = 0;
				$total_procqty = 0;
                for($row =2 ;$row <= $highest;$row++){
					////initialize post variables
                    $part1 = trim($sheet->getCellByColumnAndRow(0,$row)->getValue());
                    $post_dispqty1 = trim($sheet->getCellByColumnAndRow(1,$row)->getValue());
					
					//echo "select partcode from partcode_master where partcode='".$part1."' and status='1'"."<br><br>";
					
						$partcheck=mysqli_query($link1,"select partcode from partcode_master where partcode='".$part1."' and status='1'");
					$partcheck1=mysqli_num_rows($partcheck);
					$part_check=mysqli_fetch_array($partcheck);
					if($partcheck1 > 0){
						  $part_code=$part_check['partcode'];
					}else{
						$flag = false;
						 $part_code="";
						$error_msg= "partcode Not found:";
					
					}
					$part_code1=explode("~",getAnyDetails($part_code,"partcode,location_price,l3_price,product_id,brand_id","partcode","partcode_master",$link1));
					//////////price according to location type//////////////////////////
					$post_price=$part_code1[1];
					/////////////////////////////////////////
						///// if post dispatch qty is more than zero
						
						//echo $post_dispqty1." post_dispqty1 > 0 <br><br> ";
						
						if($post_dispqty1> 0){
							/////check inventory again
							$avlqty = getInventory($_SESSION['asc_code'],$part_code1[0],"okqty",$link1);
							
							//echo $avlqty." >= ".$post_dispqty1." aaaa <br><br> ";
							
							if($avlqty >= $post_dispqty1){
								////////check hsn code of part
								$res_part = mysqli_query($link1,"SELECT hsn_code,part_name FROM partcode_master where partcode='".$part_code1[0]."' and status='1'");
								$row_part = mysqli_fetch_assoc($res_part) ;
								if($row_part['hsn_code'] == ""){
									$flag=false;
									$error_msg="HSN Code not found in partcode master";
								}
								//  get tax on HSN Code
								$res_tax = mysqli_query($link1,"SELECT id,sgst,igst,cgst FROM tax_hsn_master where hsn_code='".$row_part['hsn_code']."'");
								$row_tax = mysqli_fetch_assoc($res_tax) ;
								if($row_tax['id']==""){
									$flag=false;
									$error_msg="Tax not found in HSN TAX MASTER";
								}
								///// calculate line total
								$linetotal = $post_price * $post_dispqty1;	
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
										$cgst_per = $row_tax['cgst'];
										$sgst_per = $row_tax['sgst'];
									}else{
										$cgst_per = "0";
										$sgst_per = "0";
									}
									/////// calculate cgst and sgst	
									$cgst_val = ($cgst_per * $linetotal) / 100;
									$cgst_final_val = $cgst_final_val + $cgst_val;
									
									$sgst_val = ($sgst_per * $linetotal) / 100;
									$sgst_final_val = $sgst_final_val + $sgst_val;
				
									$basic_cost = $basic_cost + $linetotal;	
									$tot_val = $linetotal + $cgst_val + $sgst_val;	
								}else{//// check if dispatcher and receiver belongs to different state then tax should be apply as IGST (In india) 
									//----------------------------- IGST Applicable----------------------//
									if($_POST['doc_type']=='INV'){
										$igst_per = $row_tax['igst'];
									}else{
										$igst_per = "0";
									}
									/////// calculate igst
									$igst_val = ($igst_per * $linetotal) / 100;
									$igst_final_val = $igst_final_val + $igst_val;
								
									$basic_cost = $basic_cost + $linetotal;
									$tot_val = $linetotal + $igst_val;
								}
								//--------------------------------- inserting in  billing_product_items------------------------------//
								$sql_billdata = "INSERT INTO billing_product_items set from_location='".$_SESSION['asc_code']."', to_location='".$_POST['asc_code']."',challan_no='".$invoice_no."',request_no='Direct Dispatch', hsn_code='".$row_part['hsn_code']."',partcode='".$part_code1[0]."',part_name='".$row_part['part_name']."',qty='".$post_dispqty1."',okqty='".$post_dispqty1."',price='".$post_price."',uom='PCS',value='".$linetotal."',sale_date='".$today."',basic_amt='".$linetotal."',cgst_per='".$cgst_per."',cgst_amt='".$cgst_val."',sgst_per='".$sgst_per."',sgst_amt='".$sgst_val."',igst_per='".$igst_per."',igst_amt='".$igst_val."',item_total='".$tot_val."',stock_type='okqty' , product_id ='".$part_code1[3]."' , brand_id = '".$part_code1[4]."',type = 'PO'  ";
								$res_billdata = mysqli_query($link1,$sql_billdata);
								//// check if query is not executed
								if (!$res_billdata) {
									$flag = false;
									$error_msg = "Error details3: " . mysqli_error($link1) . ".";
								}
															
								//----------------------------- inventory upadate------------------------//
								$sql_invt = "UPDATE client_inventory set okqty = okqty - '".$post_dispqty1."',updatedate='" . $datetime . "' where partcode='".$part_code1[0]."' and location_code='".$_SESSION['asc_code']."' and  okqty >= '".$post_dispqty1."'";
								$res_invt = mysqli_query($link1,$sql_invt);
								//// check if query is not executed
								if (!$res_invt) {
									$flag = false;
									$error_msg = "Error details5: " . mysqli_error($link1) . ".";
								}
								///// entry in stock ledger
								if($post_dispqty1 > 0){
									$flag = stockLedger($invoice_no,$today,$part_code1[0],$_SESSION['asc_code'],$_POST['asc_code'],"OUT","OK","Sale Return","Process",$post_dispqty1,$post_price,$_SESSION['userid'],$today,$currtime,$_SERVER['REMOTE_ADDR'],$link1,$flag);
								}
								/////// total invoice amount
								$inv_tot_cost = $basic_cost + $cgst_final_val + $sgst_final_val + $igst_final_val;
								$total_qty += $post_dispqty1;
								$total_reqqty += $post_dispqty1;
								$total_procqty += $post_dispqty1;
							}///close inventory check if
						}/// close post dispatch qty if
       			}////// end of for loop	
					//// check dispatch qty should not be zero
						if($total_qty == 0){
							$flag = false;
							$error_msg = "Error details5.1: You are dispatch 0 qty";
						}
						//--------------------------------- inserting in billing_master------------------------------//
						$sql_billmaster = "INSERT INTO billing_master set from_location='".$_SESSION['asc_code']."', to_location='".$_POST['asc_code']."',from_gst_no='".$fromlocdet[8]."',to_gst_no='".$tolocdet[8]."',party_name='".$tolocdet[0]."',challan_no='".$invoice_no."',po_no='MSL PO',sale_date='".$today."',entry_date='".$today."',entry_time='".$currtime."',logged_by='".$_SESSION['userid']."',billing_rmk='Against SRN',bill_from='".$fromlocdet[0]."',from_stateid='".$fromlocdet['5']."',to_stateid='".$tolocdet[5]."' ,bill_to='".$tolocdet[0]."',from_addrs='".$fromlocdet[1]."',disp_addrs='".$fromlocdet[2]."',to_addrs='".$tolocdet[1]."',deliv_addrs='".$tolocdet[3]."',status='2',document_type='".$_POST['doc_type']."',po_type='PO',basic_cost='".$basic_cost."',  total_cost='".$inv_tot_cost."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',igst_amt='".$igst_final_val."'"; 
						$res_billmaster = mysqli_query($link1,$sql_billmaster);
						//// check if query is not executed
						if (!$res_billmaster) {
							$flag = false;
							$error_msg = "Error details6: " . mysqli_error($link1) . ".";
						}
						
						
							$res_cr = mysqli_query($link1,"UPDATE current_cr_status set credit_bal = credit_bal - '".$inv_tot_cost."', total_credit_limit = total_credit_limit - '".$inv_tot_cost."' where location_code='".$_POST['asc_code']."'");
	if(!$res_cr){
		$flag = false;
		$error_msg = "Error details7: " . mysqli_error($link1) . ".";
	}
	
	
	
	
	////// insert in location account ledger
	$res_ac_ledger = mysqli_query($link1,"INSERT INTO location_account_ledger set location_code='".$_POST['asc_code']."',entry_date='".$today."',remark='".$invoice_no."', transaction_type = 'Direct Po Dispatch',month_year='".date("m-Y")."',crdr='DR',amount='".$inv_tot_cost."' , transaction_no='".$invoice_no."' ");
	if(!$res_ac_ledger){
		$flag = false;
		$error_msg = "Error details8: " . mysqli_error($link1) . ".";
	}
						///// update credit limit of receiver

						////// insert in location account ledger

						
						////// insert in activity table////
						$flag = dailyActivity($_SESSION['userid'],$invoice_no,"Part dispatch ","Part dispatch ",$ip,$link1,$flag);
								
		
	
	   if ($flag) {
		
		mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Successfully Uploaded ";
	
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed.Please try again.".$error_msg;
	} 
    mysqli_close($link1);
	///// move to parent page
	header("location:invoice_list.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
            
///// end of if condition
}


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
	$(document).ready(function(){

        $("#frm1").validate();

    });
function makeDropdown(){
$('.selectpicker').selectpicker();
}
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
   include("../includes/leftnavemp2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-angle-double-right"></i>Direct Dispatch To ASP</h2><div style="display:inline-block;float:right"><a href="../templates/SRN_UPLOAD.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/> Download Excel Template</a></div><br></br> 

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
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label"></label>
              <div class="col-md-4">
            
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label"></label>
              <div class="col-md-4">
            
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Location<span class="red_small">*</span></label>
              <div class="col-md-4">
               <select name="asc_code" id="asc_code" class="form-control required selectpicker" data-live-search="true">
              		<option value=''>--Please Select--</option>
					  <?php
                $map_wh = mysqli_query($link1,"select locationname, location_code from location_master where locationtype='ASP' and statusid='1' order by locationname"); 
                while( $location = mysqli_fetch_assoc($map_wh)){
				
				?>
                <option value="<?=$location['location_code']?>" <?php if($_REQUEST['asc_code'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
                <?php } ?>
                </select>
				</div>
             </div>
          </div>
           <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Document Type<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="doc_type" id="doc_type" class="form-control">
                     <option value="INV">Invoice</option>
                     <option value="DC">Challan</option>
                    </select>
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
                        <input type="file"  name="file"  required class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ > 
                    </span>
                    </label>             
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">&nbsp;&nbsp;&nbsp;
              
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='asp_sale_return.php?<?=$pagenav?>'">
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