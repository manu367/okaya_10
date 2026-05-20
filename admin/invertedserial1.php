<?php
require_once("../includes/config.php");
require_once ("../ExcelExportAPI/Classes/PHPExcel.php");
require_once ("../ExcelExportAPI/Classes/PHPExcel/IOFactory.php");

// here handle exception all page from this block
set_exception_handler(function($e){
    if($e instanceof InvertedSerialException){
        $e->location();
    }
});

global $today;
global $link1;
// step 0 = here all global object whose used All Page So Manage from Here
$pid=isset($_REQUEST['pid'])?$_REQUEST['pid']:'homeadmin';
$hid=isset($_REQUEST['hid'])?$_REQUEST['hid']:'home';
$location="invertedserial1.php";

class InvertedSerialException extends Exception
{
    protected $type;
    protected $location;
    protected $pid,$hid;

    public function __construct($type,
                                $message,
                                $location,$pid,$hid){
        $this->type = $type;
        $this->location = $location;
        $this->pid = $pid;
        $this->hid = $hid;
        parent::__construct($message, 0, null);
    }
    public function location(){
        header("location:$this->location?pid=$this->pid&hid=$this->hid&type=$this->type&msg=$this->message");
        exit();
    }
}
class RequestHandling{
    private $today;
    private $filename,$pid,$hid,$location;
    private $connection;
    function __construct($today,$pid,$hid,$location){
        $this->today = $today;
        $this->pid = $pid;
        $this->hid = $hid;
        $this->location = $location;
    }
    function fileUploader($file_up){

        $this->filename = $file_up['file']['name'];
        $tmp = $file_up['file']['tmp_name'];
        $allowedExt = ['xls','xlsx','csv','xlsm'];
        $ext = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION));

        if(in_array($ext, $allowedExt)){

        }else{
            throw new InvertedSerialException("error","File Format is not Correct",$this->location,$this->pid,$this->hid);
        }
        if ($file_up["file"]["error"] == 0){
            $name="../ExcelExportAPI/upload/".$this->today.$this->filename;
            move_uploaded_file($tmp, $name);
            $file=$name;
            chmod ($file, 0755);
            $this->filename=$file;
        }
    }
    function loadSheets(){
        $identityType = PHPExcel_IOFactory::identify($this->filename);  // detect excel file
        $object = PHPExcel_IOFactory::createReader($identityType);     //Ab jo file type detect hua hai uske according reader object create kiya ja raha hai.
        $object->setReadDataOnly(true); //data read karo
        $objPHPExcel = $object->load($this->filename); // Excel file ko memory me load
        $sheet = $objPHPExcel->getSheet(0); // first sheet
        return $sheet;
    }
    function getAllExcelData($sheet,$highestRow){
        $data_array = array();
        for($i=2;$i<=$highestRow;$i++){
            $model_code = trim($sheet->getCellByColumnAndRow(0,$i)->getValue()); // model_code
            $serial_no = trim($sheet->getCellByColumnAndRow(1,$i)->getValue()); // serial_num
            $warrent_start = trim($sheet->getCellByColumnAndRow(2,$i)->getValue()); // warrenty_start
            $warrent_end = trim($sheet->getCellByColumnAndRow(3,$i)->getValue()); // warrent_end
            $distributed_code = trim($sheet->getCellByColumnAndRow(4,$i)->getValue()); // distributed
            $data_array[]=["modelcode"=>$model_code, "serial_no"=>$serial_no, "warrent_start"=>$warrent_start, "warrent_end"=>$warrent_end, "distributed_code"=>$distributed_code];
        }
        return $data_array;
    }
    function mysql_dataValiaotp($link,$sql)
    {
        $result=mysqli_query($link,$sql);
        if($result && mysqli_num_rows($result)>0){
            while ($row=mysqli_fetch_assoc($result)){
                $data[]=$row;
            }
        }
        return $data;
    }
    function setconnection($conn){
        $this->connection=$conn;
    }
    function getConnection(){
        return $this->connection;
    }
    function uploadData($data){
//        $serial_no=$data['serial_no'];
//        $start_date=$data['start_date'];
//        $end_date=$data['end_date'];
//        $brand_id=$data['brand_id'];
//        $product_id=$data['product_id'];
//        $mode_id=$data['mode_id'];
//        $model_code=$data['model_code'];
//        $dist_code=$data['dist_code'];
//        $pcb=$data['pcb'];
//        $tfx=$data['tfx'];
//        $sql = "INSERT INTO warranty_data
//            (
//             serial_no,start_date, end_date, brand_id, product_id, model_id, model_code, dealer_code, remark, dist_channel, division_code, update_date, status,
//             pcb, transformer, entry_by, entry_date) values (
//            '$serial_no', '$start_date', '$end_date','$brand_id', '$product_id','$mode_id','$model_code', '$dist_code', '', '', '',NOW(), '1', '$pcb', '$tfx','USER', NOW())";
//
//        $flag=false;
//        mysqli_autocommit($this->connection,false);
//
//        $result=mysqli_query($this->connection,$sql);
//        if($result){
//            $flag=true;
//        }
//        else{
//            $flag=false;
//        }
//
//        if(!$flag){
//            mysqli_rollback($this->connection);
//            return $flag;
//        }else{
//            mysqli_commit($this->connection);
//            return $flag;
//        }
        return true;
    }
    function responseSender($type,$data){
        header("location:$this->location?pid=$this->pid&hid=$this->hid&type=$type&msg=$data");
        exit();
    }
}

// Step-1=  initaliatize the RequestHandling Object and set mysqli connection here
$handler=new RequestHandling($today,$pid,$hid,$location);
$handler->setconnection($link1);

// Step 2= Submit Request then Check on SUbmit button
if(isset($_POST['Submit'])){
    $handler->fileUploader($_FILES);
    $sheet=$handler->loadSheets();

    $highestRow=$sheet->getHighestRow(); // get the highest row
    $d1=$handler->getAllExcelData($sheet,$highestRow);
    $d2=$handler->mysql_dataValiaotp($link1,"SELECT model_id,modelcode FROM model_master");

    $d1_codes = array_column($d1, 'modelcode');
    $d2_codes = array_column($d2, 'modelcode');

    $data_match = ["right_data" => [], "wrong_data" => []];
    for ($i = 0; $i < count($d1_codes); $i++) {
        if (in_array($d1_codes[$i], $d2_codes)) {
            $data_match["right_data"][] = $d1_codes[$i];
        } else {
            if(!empty($d1_codes[$i])){
                $data_match["wrong_data"][] = $d1_codes[$i];
            }
        }
    }

    if(count($data_match["wrong_data"])>0){
        throw new InvertedSerialException("table",implode(",",$data_match["wrong_data"])."",$location,$pid,$hid);
    }

    if(count($data_match["right_data"])>0 && $handler->uploadData($data_match["right_data"])){
        $handler->responseSender("success","file data Uploaded Successfully");
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
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
    <style>
        :root {
            --error-mess: #bf1212;
            --success-mess: #07f61a;
        }
    #popup {
    position: fixed;
    top: 20px;
    right: 20px;
    color: white;
        font-weight: bolder;

    padding: 15px 20px;
    border-radius: 8px;
    font-family: Arial, sans-serif;
    box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    display: none;
    }
    </style>
    <style>
        /* Modal background */
        .modal{
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Modal box */
        .modal-content{
            background: white;
            padding: 25px;
            width: 300px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .modal-content-success{
            background: white;
            padding: 25px;
            width: 40%;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }

        /* Close button */
        .close-btn{
            margin-top: 15px;
            padding: 8px 16px;
            border: none;
            background: red;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }

        .close-btn:hover{
            background: darkred;
        }
    </style>
    <script>
        $(document).ready(function() {
            $("#error_master").DataTable();
        });
    </script>
</head>
<body>
<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
            <h2 align="center"><i class="fa fa-upload"></i>Upload Inverted Serial</h2><div style="display:inline-block;float:right">
                <a href="../templates/batterySerieluploader.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div>	<br></br>
            <div class="form-group"  id="page-wrap" style="margin-left:10px;">
                <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">


                    <div class="form-group">
                        <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
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
                        <div class="col-md-12" align="center">
                            <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
                            &nbsp;&nbsp;&nbsp;
                        </div>
                    </div>
                </form>
                <?php
                if(isset($_REQUEST['msg']) && isset($_REQUEST['type']) && $_REQUEST['type'] == "table"){
                    ?>
                    <div class="text-center">
                        <div class="container mt-4">
                            <div class="card shadow-sm">
                                <div class="card-header text-white">
                                    <h5 class="mb-0" style="color: red;font-weight: bolder;font-size: large">Wrong Model Code List</h5>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive text-center">
                                        <table class="table table-bordered table-hover align-middle text-center" id="error_master">
                                            <thead class="table-dark">
                                            <tr>
                                                <th class="text-center" >Sr.No</th>
                                                <th class="text-center">Wrong ModelCode</th>
                                                <th class="text-center" style="width:120px">Action</th>
                                            </tr>
                                            </thead>
                                            <tbody id="error_body">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="container mt-5">
                            <!-- Danger Alert Table -->
                            <?php
                            // MODEL ERROR TABLE
                            if( isset($_SESSION['missing_model_list']) && count($_SESSION['missing_model_list']) > 0){ ?>

                                <table class="table bg-danger mx-auto w-auto text-center" id="table">
                                    <tbody>
                                    <?php foreach($_SESSION['missing_model_list'] as $m){ ?>
                                        <tr><td><span>Missing Model : <?= $m ?></span></td></tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <?php unset($_SESSION['missing_model_list']); } ?>
                            <?php
                            // DUPLICATE SERIAL TABLE
                            if(isset($_SESSION['duplocate_serial_list'])&& count($_SESSION['duplocate_serial_list']) > 0){ ?>
                                <table class="table bg-warning mx-auto w-auto text-center" id="table">
                                    <tbody>
                                    <?php foreach($_SESSION['duplocate_serial_list'] as $d){ ?>
                                        <tr><td><span>Duplicate Serial : <?= $d ?></span></td></tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <?php unset($_SESSION['duplocate_serial_list']); } ?>
                        </div>
                        <style>
                            /* Fade out animation */
                            #table {
                                transition: opacity 1s ease, transform 1s ease; /* opacity + slide up */
                                opacity: 1;
                                transform: translateY(0);
                                border:none;
                            }
                            #table.hide {
                                opacity: 0;
                                transform: translateY(-20px); /* thoda upar slide hote hue fade out */
                            }
                        </style>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if(isset($_REQUEST['msg']) && isset($_REQUEST['type']) && $_REQUEST['type'] == "error"){
    ?>

    <div class="modal" id="modal">
        <div class="modal-content">
            <h2> ❌ Error</h2>
            <p style="text-transform: capitalize;font-size: large;font-family: "Helvetica Neue", Helvetica, Arial, sans-serif"><?= $_REQUEST['msg'] ?? 'Something is wrong' ?></p>
            <button class="close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>

    <script>
        function closeModal(){
            document.getElementById("modal").style.display = "none";
        }
    </script>

    <?php
}
?>
<?php  if(isset($_REQUEST['msg']) && isset($_REQUEST['type']) && $_REQUEST['type'] == "success"){
    ?>

    <div class="modal" id="modal">
        <div class="modal-content-success">
            <h2 style="text-transform: uppercase;color: lawngreen" >✔ Success</h2>
            <p style="text-transform: uppercase;font-size: large;font-family: "Helvetica Neue", Helvetica, Arial, sans-serif"><?= $_REQUEST['msg'] ?? 'Something is wrong' ?></p>
            <button class="close-btn" style="background-color: #00CC00" onclick="closeModal()">Close</button>
        </div>
    </div>

    <script>
        function closeModal(){
            document.getElementById("modal").style.display = "none";
        }
    </script>

    <?php
}
?>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
    document.addEventListener("DOMContentLoaded", function (){
        const tableBody = document.getElementById("error_body");
        let data = "<?= isset($_REQUEST['msg']) ? addslashes($_REQUEST['msg']) : '' ?>";
        if(!data) return;
        data = data.split(",");
        data.forEach((element, index)=>{
            const row = document.createElement("tr");
            row.innerHTML = `
            <td>${index + 1}</td>
            <td>${element}</td>
            <td style="text-align:center;cursor:pointer" onclick="removeRow(this)"><button class="btn btn-primary">X</button></td>`;
            tableBody.appendChild(row);
        });
    });

    function removeRow(btn){
        btn.parentElement.remove();
    }
</script>
</body>
</html>