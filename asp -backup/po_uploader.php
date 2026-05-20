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
	$req_res=mysqli_query($link1,"delete from temp_disp_upd where userid='".$_SESSION['userid']."' and browserid='".$browserid."'");
	//// check if query is not executed
	if (!$req_res) {
		$flag = false;
		$err_msg = "Error Code1.1:". mysqli_error($link1);
	}
	
              for($row =2 ;$row <= $highest;$row++){
					
                    $part_code = trim($sheet->getCellByColumnAndRow(0,$row)->getValue()); ///partcode in use 
                    $post_dispqty = trim($sheet->getCellByColumnAndRow(1,$row)->getValue()); ///// QTY
					 $model_id = trim($sheet->getCellByColumnAndRow(2,$row)->getValue()); ///// QTY
				
				
					$from_code=$_SESSION['asc_code'];  /////
					$to_code=$_POST['to_location'];/////
				
				
					$product_id=$_POST['prod_code'];
					$brand_id=$_POST['brand'];
	
				if($post_dispqty > 0 && $post_dispqty!='')	
				{
					/////product check is active
	
					
					$model_check=mysqli_query($link1,"select model_id from model_master where ven_code='".$model_id."' and status='1'");
					
			$row_model=	mysqli_fetch_array($model_check);
		
		
					
					    /////Brand check is active
	
								/////Part master model, product, Brand  check is active
								//echo "select partcode,product_id,brand_id,model_id from partcode_master where product_id='".$product_id."' and  brand_id='".$brand_id."' and partcode='".$part_code."'  and model_id like='%".$row_model['model_id']."%' and status='1'";
								$part_check=mysqli_query($link1,"select partcode,product_id,brand_id,model_id from partcode_master where   partcode='".$part_code."'  and status='1'");
									if(mysqli_num_rows($part_check)>0){
									    ///fetch  data form partcode master 		
										$partcode_check=mysqli_fetch_assoc($part_check);
										$tot_val=$post_price*$post_dispqty;
												 $add_po_temp="INSERT INTO temp_disp_upd set from_location='".$from_code."',to_location='".$to_code."',barnd_id='".$partcode_check['brand_id']."',model_id='".$model_id."',product_id='".$partcode_check['product_id']."',partcode='".$partcode_check['partcode']."',qty='".$post_dispqty."',price='".$post_price."',value='".$tot_val."',challan_no='".$grnno."',file_name='".$filename."',doc_type='".$doc_type."',entry_date='".$today."',userid='".$_SESSION['userid']."',browserid='".$browserid."'";
												$result_add_po_temp=mysqli_query($link1,$add_po_temp);	
												//// check if query is not executed
												if (!$result_add_po_temp) {
													$flag = false;
													$err_msg= "Error Fail_add_po_temp: " . mysqli_error($link1) . ".";
												}
									}////partcode check
									else {
									   $flag = false;
					                    $err_msg= "Partcode is not available in Partcode Master-".$part_code;
									     }
									     			 
				}////qty check
				else {
						$flag = false;
					  $err_msg= "Qty Zero and Empty in Excel file for this partcode-:".$part_code;
				     }	 				 
			
					
       		}////// end of for loop 
				
					  
					
		///////////Close Document type
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
header("location:show_po_temp_data.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-angle-double-right"></i> PURCHASE ORDER</h2>
      <div style="display:inline-block;float:right"><a href="../templates/PO_UPLOAD.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/> Download Excel Template</a></div><br></br> 

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
            <div class="col-md-12"><label class="col-md-4 control-label">To Location<span class="red_small">*</span></label>
              <div class="col-md-4">
                    <select name="to_location" id="to_location" class="form-control required" required >
                <option value="">Please Select</option>
                <?php
                $map_wh = mysqli_query($link1,"select wh_location  from map_wh_location where location_code ='".$_SESSION['asc_code']."'  and  status = 'Y'"); 
                while($row_wh = mysqli_fetch_assoc($map_wh)){
				 $location = mysqli_fetch_array(mysqli_query($link1, "select locationname, location_code from location_master where location_code = '".$row_wh['wh_location']."' "));				
				?>
                <option value="<?=$location['location_code']?>" <?php if($_REQUEST['location_code'] == $location['location_code']) { echo 'selected'; }?>><?=$location['locationname']." (".$location['location_code'].")"?></option>
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
                        <input type="file"  name="file" class="form-control required" required    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"  / > 
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