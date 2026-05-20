<?php
require_once("../includes/config.php");
//global $link1;
ini_set('memory_limit', '-1');
set_time_limit(0);
$key="myvoilcation_data";

class JobHandoverUploader {
    private $db;
    private $filePath;
    private $flag = true;
    private $errorMsg = "";
    public function __construct($db){
        $this->db = $db;
        mysqli_autocommit($this->db,false);
    }
    public function uploadFile($file){

        if($file["error"]>0){
            throw new Exception("No file uploaded OR upload error.");
        }

        $allowed = [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if(!in_array($file['type'],$allowed)){
            throw new Exception("Only Excel file allowed (.xls / .xlsx)");
        }

        $ext = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));

        if(!in_array($ext,['xls','xlsx'])){
            throw new Exception("Invalid file extension. Upload Excel only.");
        }

        $folder_nm="upload_".date("Y-M");
        $folder_path="../ExcelExportAPI/".$folder_nm;

        if (!is_dir($folder_path)) {
            mkdir($folder_path,0777,true);
        }

        $now=time();
        $this->filePath=$folder_path."/".$now.$file["name"];

        if(!move_uploaded_file($file["tmp_name"],$this->filePath)){
            throw new Exception("Failed to move uploaded file.");
        }

        chmod($this->filePath,0755);
    }

    private function validateExcelFormat($sheet){
        $jobHeader = trim(strtolower($sheet->getCellByColumnAndRow(0,1)->getValue()));
        $dateHeader = trim(strtolower($sheet->getCellByColumnAndRow(1,1)->getValue()));
        if(strpos($jobHeader,'job')===false){
            throw new Exception("Excel missing JOB NO column in A1");
        }
        if(strpos($dateHeader,'hand')===false){
            throw new Exception("Excel missing HANDOVER DATE column in B1");
        }
    }

    private function validateDate($date,$format='Y-m-d'){
        $d = DateTime::createFromFormat($format,$date);
        return $d && $d->format($format)===$date;
    }
    private function loadExcel(){
        $path = '../ExcelExportAPI/Classes/';
        set_include_path(get_include_path().PATH_SEPARATOR.$path);

        function __autoload($classe){
            $var=str_replace('_',DIRECTORY_SEPARATOR,$classe).'.php';
            require_once($var);
        }

        $type = PHPExcel_IOFactory::identify($this->filePath);
        $reader = PHPExcel_IOFactory::createReader($type);
        $reader->setReadDataOnly(true);
        return $reader->load($this->filePath);
    }
    public function process(){
        $excel=$this->loadExcel(); // loadsheet data
        $sheet=$excel->getSheet(0);
        $this->validateExcelFormat($sheet);
        $highest=$sheet->getHighestRow();

        for($row=2;$row<=$highest;$row++){
            $job_no=trim($sheet->getCellByColumnAndRow(0,$row)->getValue());
            $hand_date=trim($sheet->getCellByColumnAndRow(1,$row)->getValue());

            if(!$job_no){
                $this->fail("Job no blank at row $row");
                break;
            }

            if(!$hand_date || !$this->validateDate($hand_date)){
                $this->fail("Invalid handover date at row $row");
                break;
            }

            if(strtotime($hand_date)>strtotime(date('Y-m-d'))){
                $this->fail("Future date not allowed at row $row ($hand_date)");
                break;
            }

            $q="SELECT status FROM jobsheet_data 
                WHERE job_no='$job_no' AND status='6'";

            $res=mysqli_query($this->db,$q);

            if(mysqli_num_rows($res)==0){
                $this->fail("Job not in repair-done status : $job_no");
                break;
            }

            $sql="UPDATE jobsheet_data 
                  SET status='10',
                      sub_status='10',
                      hand_date='$hand_date'
                  WHERE job_no='$job_no' AND status='6'";

            if(!mysqli_query($this->db,$sql)){
                $this->fail(mysqli_error($this->db));
                break;
            }

            $sql2="UPDATE repair_detail 
                   SET status='10',
                       handover_date='$hand_date'
                   WHERE job_no='$job_no'";

            if(!mysqli_query($this->db,$sql2)){
                $this->fail(mysqli_error($this->db));
                break;
            }


            global $link1,$warranty_status,$remark,$ip;

            $this->flag = callHistory(
                    $job_no,
                    $_SESSION['userid'],
                    "10",
                    "Complaint Handover by Uploader",
                    "Job Status update",
                    $_SESSION['userid'],
                    $warranty_status,
                    $remark,"","",$ip,$link1,$this->flag
            );

            $this->flag = dailyActivity(
                    $_SESSION['userid'],
                    $job_no,
                    "Complaint Handover by Uploader",
                    "Update",
                    $_SERVER['REMOTE_ADDR'],
                    $link1,
                    $this->flag
            );
        }

        return $this->finish();
    }
    private function fail($msg){
        $this->flag=false;
        $this->errorMsg=$msg;
    }
    private function finish(){
        if($this->flag){
            mysqli_commit($this->db);
            return [
                    "flag"=>"success",
                    "msg"=>"Successfully Uploaded"
            ];
        }
        else{
            mysqli_rollback($this->db);
            return [
                    "flag"=>"danger",
                    "msg"=>"Failed : ".$this->errorMsg
            ];
        }
    }
}

if(isset($_POST['Submit']) && $_POST['Submit']=="Upload"){
    try{
        $uploader=new JobHandoverUploader($link1);
        $uploader->uploadFile($_FILES['file']);
        $result=$uploader->process();
        mysqli_close($link1);
        header("location:job_handover_uploader1.php?msg=".base64_encode($result['msg'])."&chkflag=".$result['flag']);
        exit;
    }
    catch(Exception $e){
        mysqli_rollback($link1);
        mysqli_close($link1);
        header("location:job_handover_uploader1.php?msg=".base64_encode($e->getMessage())."&chkflag=danger".$pagenav);
        exit;
    }
}else{
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
        <?php
        include("../includes/leftnav2.php");
        ?>
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
                        <?=base64_decode($_REQUEST['msg'])?>
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