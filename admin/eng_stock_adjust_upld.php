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

//////////////////////////////////////////////////////
if ($_FILES["file"]["error"] > 0){
	$code=$_FILES["file"]["error"];
	$file_path1 = "";
}else{
	$file_name = $_FILES['file']['name'];
	$file_tmp = $_FILES['file']['tmp_name'];
	
	$my = date("Y-M");
	$path = "../ExcelExportAPI/eng_stock_adj/".$my; 
	if (!is_dir($path)) {
		mkdir($path, 0777, 'R');
	}
	$file_path1 = $path.'/'.$today.$file_name;
	$img_upld2 = move_uploaded_file($file_tmp, $file_path1);
	chmod ($file_path1, 0755);
}

$filename=$file_path1;
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
			$sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
			$highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
			$highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
			$indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
			$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
			//Checking data
			$part_count=0;
			$error_msg = "";
			$browserid=session_id();
			
			$req_res=mysqli_query($link1,"delete from eng_adj_temp_upload where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
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
				$remark_info = $_POST['remark'];////

			  //// Check location is active /////
			  $loc_check = mysqli_num_rows(mysqli_query($link1,"select userloginid from locationuser_master where userloginid = '".$loc_no."' and statusid = '1'  ")); //
			  if($loc_check > 0)	
			  {	
			  
						/////Part master model, product, Brand  check is active		
						$part_check = mysqli_query($link1,"select  part_name,partcode,location_price,product_id,brand_id,model_id,hsn_code from partcode_master where partcode='".$part_no."'   ");
						if(mysqli_num_rows($part_check)>0){
							////////////////////////////////////////////////////////////////////////////
							$partcode_check=mysqli_fetch_array($part_check);
							$post_price=$partcode_check['location_price'];	

							$add_po_temp = "INSERT INTO eng_adj_temp_upload set to_location='".$loc_no."',brand_id='".$partcode_check['brand_id']."',model_id='".$partcode_check['model_id']."',product_id='".$partcode_check['product_id']."',partcode='".$partcode_check['partcode']."',price='".$post_price."',qty='".$qty_no."',file_name='".$filename."',entry_date='".$today."',remark='".$remark_info."',userid='".$_SESSION['userid']."',browserid='".$browserid."',po_type='Eng Stock Adjustment',part_name='".$partcode_check['part_name']."',stock_type='".$_POST['stock_type']."',opr_type='".$_POST['opr_type']."' ";

							$result_add_po_temp = mysqli_query($link1,$add_po_temp);	
							//// check if query is not executed
							if (!$result_add_po_temp) {
									$flag = false;
									$err_msg= "Error Fail_to_adjust_temp: " . mysqli_error($link1) . ".";
							}	

							/////////////////////////////////////////////////////////////////////////////
						}else{
							$flag = false;
					        $err_msg= "Partcode is not available in Partcode Master or deactivated in DB - ".$part_no;
						}
			  }else{
			  	$flag = false;
			  	$err_msg= "Engineer Code Not found or Deactivated in DB - ".$loc_no;
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
	header("location:eng_stock_adjust_temp_data.php?msg=".$msg."&chkflag=".$cflag."&doc_type=".$_POST['doc_type']."&chkmsg=".$cmsg."".$pagenav);
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
      <h2 align="center"> ENG Stock Adjustment By Upload </h2><div style="display:inline-block;float:right"> 

      

      <a href="../templates/ENG_STOCK_ADJUST_UPLOAD.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/> Download Excel Template</a>

      

      </div><br>

      <div class="form-group"  id="page-wrap" style="margin-left:10px;">

      <?php if($_REQUEST['msg']){?><br>

      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">

            <button type="button" class="close" data-dismiss="alert" aria-label="Close">

                <span aria-hidden="true">&times;</span>

              </button>

            <strong><?=$_REQUEST['chkmsg']?> !</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.

        </div>

      <?php }?>

	  

	  <br><br>

	  

        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">

          

		  

            

		  <div class="form-group">

           <div class="col-md-12"><label class="col-md-4 control-label">Stock Type<span class="red_small">*</span></label>
              <div class="col-md-4">
                <select name="stock_type" id="stock_type" class="form-control required" required >
					<option value=''>--Please Select--</option>
					<option value="okqty">OK QTY</option>
					<option value="faulty">Faulty</option>
					<option value="missing">Missing</option>
					<option value="broken">Damage</option>
                </select>      

			  </div>

             </div>

          </div>

          <div class="form-group">

              <div class="col-md-12"><label class="col-md-4 control-label">Stock Adjustment<span class="red_small">*</span></label>
              <div class="col-md-4">
				<select name="opr_type" id="opr_type" class="required form-control" required >
					<option value="" >Please Select</option>
                    <option value="P" >+</option>
					<option value="M">-</option>
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