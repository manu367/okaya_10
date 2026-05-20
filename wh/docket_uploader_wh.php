<?php 
require_once("../includes/config.php");
//$accessproduct = getAccessProduct($_SESSION['userid'],$link1);	

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
				
		$flag = true;		
	for($row =2 ;$row <= $highest;$row++){	
		$challan= trim($sheet->getCellByColumnAndRow(0,$row)->getValue()); ///// Challan No
		$docket= trim($sheet->getCellByColumnAndRow(1,$row)->getValue()); ///// job No
		$courier= trim($sheet->getCellByColumnAndRow(2,$row)->getValue()); /////repair level
		$remark= trim($sheet->getCellByColumnAndRow(3,$row)->getValue()); ///// Model Category					
		
		$sql_doc = "UPDATE billing_master set courier = '".$courier."', docket_no='".$docket."',dc_date='".$today."', dc_time='".$currtime."', status='3',disp_rmk='".$remark."' where challan_no='".$challan."' and (status='2' or status='3') ";
		
		$res_doc = mysqli_query($link1,$sql_doc);
		//// check if query is not executed
		if (!$res_doc) {
			$flag = false;
			$err_msg= "Error 1.1 : " . mysqli_error($link1) . ".";
		}	
	}
			  
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
	header("location:docket_uploader_wh.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-angle-double-right"></i>Docket Bulk Uploader</h2><div style="display:inline-block;float:right"> 
<a href="../templates/Docket_uploader.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/> Download Excel Template</a>

      </div><br>

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
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                    <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
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