<?php 

require_once("../includes/config.php");

////get access product details

$access_product = getAccessProduct($_SESSION['asc_code'],$link1);

////get access brand details

$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);



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

			

			$req_res=mysqli_query($link1,"delete from ost_temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");

			//// check if query is not executed

			if (!$req_res) {

				$flag = false;

				$err_msg = "Error Code1.1:". mysqli_error($link1);

			}

				

            for($row =2 ;$row <= $highest;$row++){	

				$loc_no = trim($sheet->getCellByColumnAndRow(0,$row)->getValue()); ///// loction_no

				$part_no = trim($sheet->getCellByColumnAndRow(1,$row)->getValue()); ///// part_no

				$qty_no = trim($sheet->getCellByColumnAndRow(2,$row)->getValue()); ///// qty_no

		

				$session_code = $_SESSION['asc_code'];  /////

				$doc_type_info = $_POST['doc_type'];////

				$brand_info = $_POST['brand'];////

				$prod_code_info = $_POST['prod_code'];////

				$remark_info = $_POST['remark'];////
				$stock_type = $_POST['stock_type'];////

					

			  //// Check location is active /////


				
			   $loc_check = mysqli_num_rows(mysqli_query($link1,"select location_code from location_master where location_code = '".$loc_no."' and statusid = '1' "));

			//	exit;
			  if($loc_check > 0)	

			  {	
				  
				  

			  	/////Brand check is active

				//$brand_check=mysqli_num_rows(mysqli_query($link1,"select brand_id from brand_master where brand_id='".$brand_info."' and status='1'"));

				//if($brand_check>0){

					/////product check is active	

			  		//$product_check=mysqli_num_rows(mysqli_query($link1,"select product_id from product_master where product_id='".$prod_code_info."' and status='1'"));

					//if($product_check>0){

						/////Part master model, product, Brand  check is active						

						//$part_check = mysqli_query($link1,"select  part_name,partcode,location_price,product_id,brand_id,model_id,hsn_code from partcode_master where partcode='".$part_no."'  and status='1'  ");
						$part_check = mysqli_query($link1,"select part_name,partcode,location_price,product_id,brand_id,model_id,hsn_code from partcode_master where partcode='".$part_no."'");

						if(mysqli_num_rows($part_check)>0){

						

							////////////////////////////////////////////////////////////////////////////

							$partcode_check=mysqli_fetch_array($part_check);

							$post_price=$partcode_check['location_price'];	

						 

							/////// HSN MASTER AVILABLE OR NOT HSN CODE ////////

							$rs_hsn_tax  = mysqli_query($link1,"select id,cgst,igst,sgst from tax_hsn_master where hsn_code='".$partcode_check['hsn_code']."'")or die("part error2".mysqli_error($link1));

							$part_tax = mysqli_fetch_assoc($rs_hsn_tax) ;

							//////// Check HSN Avilabale or not////////

							if($part_tax['id']!=""){

								//////Part to cridet check

								//$fromlocdet = explode("~",getAnyDetails($session_code,"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));

								////// PO receiver

							 	$tolocdet = explode("~",getAnyDetails($loc_no,"locationname,locationaddress,dispatchaddress,deliveryaddress,cityid,stateid,zipcode,emailid,gstno","location_code","location_master",$link1));

								$fromlocdet	=	$tolocdet; 
								//////intialize tax variables

								$sgst_final_val=0;

								$cgst_final_val=0;

								$igst_final_val=0;

								$total_qty = 0;

								$total_reqqty = 0;

								$total_procqty = 0;

								

								///// calculate line total

								$cost = $post_price * $qty_no;	

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

								

		 						$add_po_temp = "INSERT INTO ost_temp_disp_upd set from_location='".$session_code."',to_location='".$loc_no."',brand_id='".$partcode_check['brand_id']."',model_id='".$partcode_check['model_id']."',product_id='".$partcode_check['product_id']."',partcode='".$partcode_check['partcode']."',stock_type='".$stock_type."',qty='".$qty_no."',price='".$post_price."',cost='".$cost."',item_total='".$tot_val."',file_name='".$filename."',doc_type='".$doc_type."',entry_date='".$today."',remark='".$remark_info."',userid='".$_SESSION['userid']."',browserid='".$browserid."',po_type='Opening Stock',igst_per='".$igst_per."',cgst_per='".$cgst_per."',sgst_per='".$sgst_per."',igst_amt='".$igst_final_val."',cgst_amt='".$cgst_final_val."',sgst_amt='".$sgst_final_val."',hsn_code='".$partcode_check['hsn_code']."',part_name='".$partcode_check['part_name']."' ";

								

								$result_add_po_temp = mysqli_query($link1,$add_po_temp);	

								//// check if query is not executed

								if (!$result_add_po_temp) {

									$flag = false;

									$err_msg= "Error Fail_add_po_temp: " . mysqli_error($link1) . ".";

								}	

										

							/////// HSN Code Check //////

							}else {

								$flag = false;

								$err_msg= "Tax not found in HSN TAX MASTER - ".$part_no;

							}

							

							/////////////////////////////////////////////////////////////////////////////

						

						}else{

							$flag = false;

					        $err_msg= "Partcode is not available in Partcode Master or deactivated in DB - ".$part_no;

						}

					//}else{

						//$flag = false;

					    //$err_msg= "Product is not available in Product Master or deactivated in DB - ".$prod_code_info;

					//}

			  	//}else{

					//$flag = false;

					//$err_msg= "Brand is not available in Brand Master or deactivated in DB - ".$brand_info;

				//}

			  }else{

			  	$flag = false;

			  	$err_msg= "Location Code Not found or Deactivated in DB - ".$loc_no;

			  }

			  				

		}////// end of for loop 



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

	header("location:opening_stock_transfer_temp_data.php?msg=".$msg."&chkflag=".$cflag."&doc_type=".$_POST['doc_type']."&chkmsg=".$cmsg."".$pagenav);

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

      <h2 align="center"><i class="fa fa-exchange"></i> Opening Stock Upload </h2><div style="display:inline-block;float:right"> 

      

      <a href="../templates/OPENING_STOCK_TRANSFER_UPLOAD.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/> Download Excel Template</a>

      

      </div><br>

      <div class="form-group"  id="page-wrap" style="margin-left:10px;">

      <?php 
	//print_r('dddddddddddd');exit;
	if($_REQUEST['msg']){?><br>

      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                <span aria-hidden="true">&times;</span>

              </button>

            <strong><?=$_REQUEST['chkmsg']?> !</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.

        </div>

      <?php }?>

	  

	  <br><br>

	  

        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">

          

		  <?php /*?><div class="form-group">

            <div class="col-md-12"><label class="col-md-4 control-label">Brand <span class="red_small">*</span></label>

              <div class="col-md-4">

                 	<select name="brand" id="brand" class="form-control required" required>

						 <option value=''>--Select Brand--</option>

						 <?php

						  $dept_query="SELECT * FROM brand_master where status = '1' and brand_id in (".$access_brand.") order by brand";

						  $check_dept=mysqli_query($link1,$dept_query);

						  while($br_dept = mysqli_fetch_array($check_dept)){

						?>

						 <option value="<?=$br_dept['brand_id']?>"<?php if($_REQUEST['brand'] == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']." | ".$br_dept['brand_id']?></option>

						 <?php }?>

					</select>

				</div>

             </div>

          </div>

		  <div class="form-group">

            <div class="col-md-12"><label class="col-md-4 control-label">Product <span class="red_small">*</span></label>

              <div class="col-md-4">

               <select name="prod_code" id="prod_code" class="form-control required" required>

						<option value="">--Select Product--</option>

						<?php 

					  $model_query="select product_id,product_name from product_master where status='1' and product_id in (".$access_product.") order by product_name";

					  $check1=mysqli_query($link1,$model_query);

					  while($br = mysqli_fetch_array($check1)){?>

						<option data-tokens="<?php echo $br['product_id'];?>" value="<?php echo $br['product_id'];?>">

						 <?=$br['product_name']." | ".$br['product_id']?>

						 </option>

						<?php }?>

			  	</select>

			  </div>

             </div>

          </div><?php */?>

          <div class="form-group">

            <div class="col-md-12"><label class="col-md-4 control-label">Document Type <span class="red_small">*</span></label>

              <div class="col-md-4">

               <select name="doc_type" id="doc_type" class="form-control required" required >

               <option value=''>--Please Select--</option>

                 <option value="DC" <?php if($_REQUEST['doc_type'] == "DC") { echo 'selected'; }?>>DC</option>

					  <!-- <option value="INV" <?php if($_REQUEST['doc_type'] == "INV"){ echo 'selected'; }?>>INV</option>-->

                </select>

				</div>
              

             </div>

          </div>
            <div class="form-group">

            <div class="col-md-12"><label class="col-md-4 control-label">Stock Type <span class="red_small">*</span></label> 
              <div class="col-md-4">

               <select name="stock_type" id="stock_type" class="form-control required" required >

               <option value=''>--Please Select--</option>

                 <option value="okqty" <?php if($_REQUEST['stock_type'] == "okqty") { echo 'selected'; }?>>OK/Fresh</option>

					   <option value="faulty" <?php if($_REQUEST['stock_type'] == "faulty"){ echo 'selected'; }?>>Faulty/Defective</option>

                </select>

				</div>
                </div>
                </div>

		  <div class="form-group">

            <div class="col-md-12"><label class="col-md-4 control-label">Remark </label>

              <div class="col-md-4">

               <textarea name="remark" id="remark" class="form-control" ></textarea>

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