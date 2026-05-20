<?php 
require_once("../includes/config.php");
echo $num;
ini_set('memory_limit', '-1');
set_time_limit(0);
$wrong_data=[];
@extract($_POST);
if($_POST['Submit']=="Upload"){
ini_set('max_execution_time', 500);
mysqli_autocommit($link1, false);
$flag = true;
if ($_FILES["file"]["error"] > 0)
{
$code=$_FILES["file"]["error"];
}
else
{
	$folder_nm="upload_".date("Y-M");
	$folder_path="../ExcelExportAPI/".$folder_nm;
	if (!is_dir($folder_path)) {
		mkdir($folder_path, 0777, 'R');
		$file_path=$folder_path."/";
	}else{
		$file_path=$folder_path."/";
	}

$model = $_POST['model'];
move_uploaded_file($_FILES["file"]["tmp_name"],
$file_path.$now.$_FILES["file"]["name"]);
$file=$file_path.$now.$_FILES["file"]["name"];
chmod ($file, 0755);
}
$filename=$file;
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
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}
for($row =2 ;$row <= $highest;$row++){
    $job_no = trim($sheet->getCellByColumnAndRow(0,$row)->getValue());
    $hand_date = trim($sheet->getCellByColumnAndRow(1,$row)->getValue());
    $checkDate = validateDate($hand_date);
    if(strtotime($hand_date) > strtotime(date('Y-m-d'))){
        header("location:job_handover_uploader.php?msg=Future date not allowed at row ".$row." (".$hand_date.")&chkflag=danger&chkmsg=Failed");
        exit;
    }
    if($job_no != ""){
        if($hand_date!='' && $checkDate==1){
            $check_job_status = "SELECT status FROM jobsheet_data where job_no='".$job_no."' and status='6'";
            $num_result = mysqli_num_rows(mysqli_query($link1,$check_job_status));
            if($num_result > 0){
                $sql = "update jobsheet_data set status='10', sub_status='10', hand_date='".$hand_date."' where job_no='".$job_no."' and status='6'";
                $result = mysqli_query($link1,$sql);
                if (!$result) {
                    $flag = false;
                    $error_msg = "Error details: " . mysqli_error($link1) . ".";
                }
			//Update repair_detail into data base
                $sql_repair = "update repair_detail set status='10', handover_date='".$hand_date."' where job_no='".$job_no."' ";
                $result_repair = mysqli_query($link1,$sql_repair);
			//// check if query is not executed
                if (!$result_repair) {
                    $flag = false;
                    $error_msg = "Error details: " . mysqli_error($link1) . ".";
                }
                $flag = callHistory($job_no,$_SESSION['userid'],"10","Complaint Handover by Uploader","Job Status update",$_SESSION['userid'],$warranty_status,$remark,"","",$ip,$link1,$flag);
                $flag = dailyActivity($_SESSION['userid'],$job_no,"Complaint Handover by Uploader","Update",$_SERVER['REMOTE_ADDR'],$link1,$flag);
            }else{
                $flag = false;
                $error_msg = "Error details: The status of this job is not repair done. Please check." . $job_no . ".";
            }
        }else{
            $flag = false;
            $error_msg = "Error details: Handover Date is blank or Handover Date format is wrong" . mysqli_error($link1) . ".";
        }
    }
    else{
        $flag = false;
        $error_msg = "Error details: job no is blank" . mysqli_error($link1) . ".";
    }
}
    if ($flag) {
        mysqli_commit($link1);
        $cflag = "success";
        $cmsg = "Success";
        $msg = "Successfully Uploaded ";
    }
    else {
        mysqli_rollback($link1);
        $cflag = "danger";
        $cmsg = "Failed";
        $msg = "Request could not be processed. Please try again.".$error_msg;
    }
    mysqli_close($link1);
    header("location:job_handover_uploader.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>
<?=siteTitle?>
</title>
<script src="../js/jquery.min.js"></script>
<link href="../css/font-awesome.min.css" rel="stylesheet">
<link href="../css/abc.css" rel="stylesheet">
<script src="../js/bootstrap.min.js"></script>
<link href="../css/abc2.css" rel="stylesheet">
<link rel="stylesheet" href="../css/bootstrap.min.css">
<link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<link rel="stylesheet" href="../css/jquery.dataTables.min.css">
<script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
<script>
//////////////////////// function to get model on basis of model dropdown selection///////////////////////////
function getmodel(){
var brand=$('#brand').val();
var product=$('#prod_code').val();
$.ajax({
type:'post',
url:'../includes/getAzaxFields.php',
data:{brandinfo:brand,productinfo:product},
success:function(data){
$('#modeldiv').html(data);
}
});
}
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/common_js.js"></script>
<link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/jquery-1.10.1.min.js"></script>
<script src="../js/bootstrap-datepicker.js"></script>
<script src="../js/fileupload.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <?php include("../includes/leftnav2.php"); ?>
    <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i>Upload Job Handover Uploader</h2>
      <div style="display:inline-block;float:right"><a href="../templates/Job_Handover_Uploader.xlsx" title="Download Excel Template"><img src="../templates/template.png" title="Download Excel Template"/></a></div>
      <br>
      <br>
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
        <?php if($_REQUEST['msg']){?>
        <br>
        <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
          <strong>
          <?=$_REQUEST['chkmsg']?>
          !</strong>&nbsp;&nbsp;
          <?=$_REQUEST['msg']?>
          . </div>
        <?php }?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
         <!-- <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-4 control-label">Product<span class="red_small">*</span></label>
              <div class="col-md-4">
                <select name="prod_code" id="prod_code" required class="form-control required" >
                  <option value=''>--Please Select--</option>
                  <?php
$model_query="select product_id,product_name from product_master where status='1' order by product_name";
$check1=mysqli_query($link1,$model_query);
while($br = mysqli_fetch_array($check1)){?>
                  <option value="<?=$br['product_id']?>" <?php if($_REQUEST['prod_code'] == $br['product_id']) { echo 'selected'; }?>>
                  <?=$br['product_name']." | ".$br['product_id']?>
                  </option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-4 control-label">Brand<span class="red_small">*</span></label>
              <div class="col-md-4">
                <select   name="brand"  id="brand"  required class="form-control required" onChange="getmodel();">
                  <option value=''>--Please Select--</option>
                  <?php
$brand = mysqli_query($link1,"select brand_id, brand from brand_master where status='1' order by brand" );
while($brandinfo = mysqli_fetch_assoc($brand)){?>
                  <option value="<?=$brandinfo['brand_id']?>" <?php if($_REQUEST['brand'] == $brandinfo['brand_id']) { echo 'selected'; }?>>
                  <?=$brandinfo['brand']." | ".$brandinfo['brand_id']?>
                  </option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>-->
          <!-- <div class="form-group">
<div class="col-md-12"><label class="col-md-4 control-label">Model<span class="red_small">*</span></label>
<div class="col-md-4" id="modeldiv">
<select name="model" id="model"  required class="form-control">
<option value=''>--Please Select-</option>
</select>
</div>
</div>
</div> -->
          <div class="form-group">
            <div class="col-md-12">
              <label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
                <div>
                  <label > <span>
                    <input type="file"  name="file"  required class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ >
                    </span> </label>
                </div>
              </div>
              <div class="col-md-4" align="right"><span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span></div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              &nbsp;&nbsp;&nbsp; </div>
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