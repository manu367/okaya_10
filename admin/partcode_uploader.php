<?php 
require_once("../includes/config.php");
//////////////// after hitting upload button
$arrstatus = getFullStatus("master",$link1);
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
				$doctype_flag = 1;
				
		if($_POST['Submit']=="Upload"){
			///////////////select counter 	
			$doctype_flag *= 1;
		}
		else{
			$doctype_flag *= 0;
		}
		
		////////////////////////if invoice /Document no generated
		if($doctype_flag==1){
	$browserid=session_id();
	
	echo "delete from temp_partcode_data where userid='".$_SESSION['userid']."' and browserid='".$browserid."'"."<br><br>";
	
	$req_res=mysqli_query($link1,"delete from temp_partcode_data where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$req_res) {
		$flag = false;
		$err_msg = "Error Code1.1:". mysqli_error($link1);
	}
	          $rowp =1;
              for($row =2 ;$row <= $highest;$row++){
					
                    $model = trim($sheet->getCellByColumnAndRow(0,$row)->getValue()); ///// Vendor partcode
                    $vendor_code = trim($sheet->getCellByColumnAndRow(1,$row)->getValue()); ///// Vendor partcode 
					$hsn_code = trim($sheet->getCellByColumnAndRow(2,$row)->getValue()); ///// HSN Code
					$part_name = trim($sheet->getCellByColumnAndRow(3,$row)->getValue()); /////Part Name
					$part_desc = trim($sheet->getCellByColumnAndRow(4,$row)->getValue()); ///// Part Desc
					$customer_price = trim($sheet->getCellByColumnAndRow(5,$row)->getValue()); ///// Customer price
					$repair_code = trim($sheet->getCellByColumnAndRow(6,$row)->getValue()); ///Repair code
					$aspprice = trim($sheet->getCellByColumnAndRow(7,$row)->getValue()); ///ASP Price
					$l3price = trim($sheet->getCellByColumnAndRow(8,$row)->getValue()); ///L3 Price
					$l4price = trim($sheet->getCellByColumnAndRow(9,$row)->getValue()); ///L4 Price
					$whprice = trim($sheet->getCellByColumnAndRow(10,$row)->getValue()); ///WH Price
					$thprice = trim($sheet->getCellByColumnAndRow(11,$row)->getValue()); ///Third Party Price
					$cust_warr_days = trim($sheet->getCellByColumnAndRow(14,$row)->getValue()); ///Customer warranty days
					$cust_part_code = trim($sheet->getCellByColumnAndRow(15,$row)->getValue()); ///Customer part code
					$dealer_warr_days = trim($sheet->getCellByColumnAndRow(16,$row)->getValue()); ///Dealer warranty days
					
					/////post data check for value
					$location=$_SESSION['userid'];  /////
					$part_category=$_POST['part_category'];////
					$part_for=$_POST['part_for'];  /////
					$part_status=$_POST['Status'];////
					$product_id=$_POST['prod_code'];
					$brand_id=$_POST['brand'];
					//////CRM partcode Start
					
					$sel_insid2=mysqli_query($link1,"select max(part_max) as cnt from partcode_master where 1")or die("error1".mysqli_error($link1));
			        $sel_result1=mysqli_fetch_assoc($sel_insid2);
			        $insid=$sel_result1['cnt']+$rowp;
			        /// make 5 digit padding
			        $pad=str_pad($insid,4,"0",STR_PAD_LEFT);
			       //// make logic of partcode code
			        $newpartcode="PA".$pad;
		if($model!='')	
				{ 				
					//////CRM partcode END 	
			/***********		
			if($vendor_code!='')	
				{ 
				  if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $vendor_code))
					{ *********/
				 	
							if($hsn_code!=''   && $part_name!=''   && $part_desc!='')	
								{
								/////product check is active
					
								if($customer_price!='')
								{
								  /////Model tax Check
								  	

								  
								  /////HSN tax Check
								  
									$hsn_check=mysqli_num_rows(mysqli_query($link1,"select id from tax_hsn_master where hsn_code='".$hsn_code."' and status='1'"));
									if($hsn_check>0)
									{
										/////product check is active
										
										$product_check=mysqli_num_rows(mysqli_query($link1,"select product_id from product_master where product_id='".$product_id."' and status='1'"));
										if($product_check>0)
										{
											/////Brand check is active
											
										   $brand_check=mysqli_num_rows(mysqli_query($link1,"select brand_id from brand_master where brand_id='".$brand_id."' and status='1'"));
											 if($brand_check>0)	{	
													/////Part master model, product, Brand  check is active
											/********		
													$repair_check=mysqli_num_rows(mysqli_query($link1,"select id from repaircode_master where status='1' and brand_id='".$brand_id."' and product_id='".$product_id."' and rep_code='".$repair_code."' order by rep_desc"));
					 if($repair_check>0)	{
					 				***********/
					 							
												$crmpart_check=mysqli_query($link1,"select partcode from partcode_master where partcode='".$newpartcode."'");
														if(mysqli_num_rows($crmpart_check)==0){ 
													
													/**********
													$part_check=mysqli_query($link1,"select vendor_partcode from partcode_master where vendor_partcode='".$vendor_code."'");
														if(mysqli_num_rows($part_check)==0){
														
														******************/
															///fetch  data form partcode master 		
															$partcode_check=mysqli_fetch_assoc($part_check);
															
													          $customer_price1=(round($customer_price));
															$total_asp_price1=(round($location_price));
															$total_l1_price1=(round($total_l1_price));
																	  $add_po_temp="INSERT INTO temp_partcode_data set location='".$location."',barnd_id='".$brand_id."',product_id='".$product_id."', model_id ='".$model."', hsn_code ='".$hsn_code."',partcode='".$newpartcode."',part_name='".$part_name."',part_desc='".$part_desc."',vendor_code='".$vendor_code."',customer_price='".$customer_price1."',file_name='".$filename."',part_category='".$part_category."',status='".$status."',entry_date='".$today."',part_for='".$part_for."',repair_code='".$repair_code."',location_price='".$aspprice."',l1_price ='".$l3price."' ,l2_price ='".$l4price."', l3_price ='".$l3price."',l4_price='".$whprice."',l5_price='".$thprice."',userid='".$_SESSION['userid']."',browserid='".$browserid."', customer_partcode='".$cust_part_code."', wp='".$cust_warr_days."', dwp='".$dealer_warr_days."' ";
																	 
																	$result_add_po_temp=mysqli_query($link1,$add_po_temp);	
																	//// check if query is not executed
																	if (!$result_add_po_temp) {
																		$flag = false;
																		$err_msg= "Error Fail_add_po_temp: " . mysqli_error($link1) . ".";
																	}
																	
														/******			
														}////partcode check
														else {
														   $flag = false;
															 $err_msg= "Venodr Partcode is available in Partcode Master-".$vendor_code;
															 }
														**************/	 
													}////CRM partcode check
													else {
														 $flag = false;
														  $err_msg= "CRM Partcode is available in Partcode Master-".$newpartcode;
														  }	
														  
												/*******		  
												  }
												  else  {
												         $flag = false;
														  $err_msg= "Repair Code is not  available in Repair Master-".$repair_code;
												  }	******/	  				 
												 } /// Brand check 
												 else {
													   $flag = false;
														 $err_msg= "Brand is not available in Brand Master";
													   }			 
															
										}///// product check 
										 else {
										 $flag = false;
										   $err_msg= "Product is not available in Product Master:";
										 
										 }
									}
									else {
										$flag = false;
										  $err_msg= "HSN Code is not available in HSN Master Check -:".$hsn_code;
										}	 
									}
									else {
									$flag = false;
										  $err_msg= "Model Id is not available in Model Master -:".$model;
									  }
											
								 } ////////Price
								 else {
									  $flag = false;
									   $err_msg= "Customer Price is empty For this vendor partcode Check -:".$vendor_code;
									  }
					
							 
							}////Check HSN ,Part Name ,Part Desc and Cusromer  Price
							else {
									$flag = false;
								   $err_msg= "HSN Code,Part Name,Part Desc,Customer Price  is Empty in Excel file for this Vendor partcode-:".$vendor_code;
								 }	
								 
					/************			 
					} else  {
					    $flag = false;
						$err_msg= "Special Characters found in Excel file Vendor partcode-:".$vendor_code;
					     }			 
								  				 
				}
				else {
						$flag = false;
					    $err_msg= "Vendor Partcode is Empty in Excel file for this Row -:".$row;
				      }**************/
		
		  
			$rowp++;		
       		}////// end of for loop 
				
					  
					
		}///////////Close Document type
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
	header("location:show_partcode_temp_data.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
   include("../includes/leftnav2.php");
    ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-angle-double-right"></i>PARTCODE UPLOADER</h2><div style="display:inline-block;float:right"><a href="../templates/PARTCODE_UPLOAD.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/> Download Excel Template</a></div><br></br> 

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
            <div class="col-md-12"><label class="col-md-4 control-label">Product<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="prod_code" id="prod_code"  class="form-control required" required >
               <!-- <option value=''>--Please Select--</option>-->
				<?php
               $model_query="select product_id,product_name from product_master where status='1'";
			        $check1=mysqli_query($link1,$model_query);
                while($br = mysqli_fetch_array($check1)){?>
                <option value="<?=$br['product_id']?>" <?php if($_REQUEST['prod_code'] == $br['product_id']) { echo 'selected'; }?>><?=$br['product_name']." | ".$br['product_id']?></option>
                <?php } ?>
	</select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Brand<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select   name="brand"  id="brand"  class="form-control required" required>
				<?php
                $brand = mysqli_query($link1,"select brand_id, brand from brand_master where status='1'" );
                while($brandinfo = mysqli_fetch_assoc($brand)){?>
                <option value="<?=$brandinfo['brand_id']?>" <?php if($_REQUEST['brand'] == $brandinfo['brand_id']) { echo 'selected'; }?>><?=$brandinfo['brand']." | ".$brandinfo['brand_id']?></option>
                <?php } ?>
	</select>
              </div>
            </div>
          </div>
          <div class="form-group">
		  <div class="col-md-12"><label class="col-md-4 control-label">Part Category<span class="red_small">*</span></label>
              <div class="col-md-4">
               <select name="part_category" id="part_category" class="form-control selectpicker" data-live-search="true" required>
			   		<option value="">--Please Select--</option>
                  <option value="ACCESSORY"<?php if($_REQUEST['part_category'] == "ACCESSORY"){ echo "selected";}?>>Accessory</option>
                  <option value="SPARE"<?php if($_REQUEST['part_category'] == "SPARE"){ echo "selected";}?>>Spare</option>
                  <option value="BOX"<?php if($_REQUEST['part_category'] == "BOX"){ echo "selected";}?>>Box</option>
                  <option value="UNIT"<?php if($_REQUEST['part_category'] == "UNIT"){ echo "selected";}?>>Unit</option>
				  <option value="PCB"<?php if($_REQUEST['part_category'] == "PCB"){ echo "selected";}?>>PCB</option>
              
                </select>
				</div>
               </div>  
			 </div>
		  <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Part For <span class="red_small">*</span></label>
              <div class="col-md-4">
               <select name="part_for" id="part_for" class="form-control required" required>
                  <option value="">--Please Select--</option>
                  <option value="ALL"<?php if($sel_result['part_for'] == "ALL"){ echo "selected";}?>>ALL</option>
                  <?php
					$dept_query="SELECT * FROM location_type_master order by usedname";
					$check_dept=mysqli_query($link1,$dept_query);
					while($br_dept = mysqli_fetch_array($check_dept)){
                  ?>
                  <option value="<?=$br_dept['usedname']?>"<?php if($sel_result['part_for'] == $br_dept['usedname']){ echo "selected";}?>><?php echo $br_dept['usedname']?></option>
                <?php }?>
                </select>
				</div>
             </div>
          </div>
		  
		  <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Status<span class="red_small">*</span></label>
              <div class="col-md-4">
               <select name="status" id="status" class="form-control">
                    <?php foreach($arrstatus as $key => $value){?>
                    	<option value="<?=$key?>" <?php if($sel_result['status'] == $key) { echo 'selected'; }?>><?=$value?></option>
                    <?php } ?>
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