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
			
			$rowp =1;
			for($row =2 ;$row <= $highest;$row++){
				
				$challan_no = trim($sheet->getCellByColumnAndRow(0,$row)->getValue()); ///// challan_no
				$courier_name = trim($sheet->getCellByColumnAndRow(1,$row)->getValue()); ///// courier_name 
				$docket_no = trim($sheet->getCellByColumnAndRow(2,$row)->getValue()); ///// docket_no
				$dispatch_remark = trim($sheet->getCellByColumnAndRow(3,$row)->getValue()); /////dispatch_remark
				
				if($challan_no!=""){ 	
					//echo "select id from stn_master where challan_no='".$challan_no."' and status in ('2','3','4')"."<br><br>";

					$challan_check=mysqli_num_rows(mysqli_query($link1,"select id from billing_master where challan_no='".$challan_no."' and status in ('2','3','4')"));
					if($challan_check>0)
					{				
						if($courier_name!=''){ 
							if($docket_no!=''){
								if($dispatch_remark!=''){
									$sql_doc = "UPDATE billing_master set courier = '".$courier_name."', docket_no='".$docket_no."',dc_date='".$today."', dc_time='".$currtime."', status='3',disp_rmk='".$dispatch_remark."', docket_update_mode = 'Uploader' where challan_no='".$challan_no."' ";
									
									//echo $sql_doc."<br><br>";
									
									$res_doc = mysqli_query($link1,$sql_doc);
									//// check if query is not executed
									if (!$res_doc) {
										 $flag = false;
										 $error_msg = "Error details 1 : " . mysqli_error($link1) . ".";
									}
									
								}else{
									$flag = false;
									$err_msg= "Dispatch Remark is empty For this Row -: ".$row;
								} 
							}else{
								$flag = false;
								$err_msg= "Docket No is Empty in Excel file for this Row -: ".$row;
							}			 
						}else{
							$flag = false;
							$err_msg= "Courier Name is Empty in Excel file for this Row -: ".$row;
						}
					
					}else{
						$flag = false;
						$err_msg= "Use valid challan no -: ".$challan_no;
					}
				}else{
					$flag = false;
					$err_msg= "Challan no is Empty in Excel file for this Row -: ".$row;
				}	
				
				$rowp++;		
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
	header("location:assgin_part_user.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
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
      <h2 align="center"><i class="fa fa-angle-double-right"></i>Docket Update Uploader</h2><div style="display:inline-block;float:right"><a href="../templates/SITE_DOCKET_UPLOAD.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/> Download Excel Template</a></div><br>

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
              <div class="col-md-4" align="right"></div>
            </div>
          </div>
         <div class="form-group">
            <div class="col-md-12" align="center">&nbsp;&nbsp;&nbsp;
              
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
            </div>
          </div> 
			
			
		<div class="form-group">
            <div class="col-md-12" align="center">&nbsp;&nbsp;&nbsp;
				<span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file. </span><br>
				<span class="red_small"><strong>* </strong>Please <strong>avoid special characters like (+, ., ', ") </strong> in excel. </span><br>
				<!------
				<span class="red_small"><strong>* </strong>Please <strong>avoid ,(comma) character from price value </strong> in excel. </span><br>
				<span class="red_small"><strong>* </strong>Only few special characters are allowed in excel like ( <strong>_ and -</strong> ). </span>--------->
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