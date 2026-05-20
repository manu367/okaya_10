<?php
require_once("../includes/config.php");
require_once ("../ExcelExportAPI/Classes/PHPExcel.php");
require_once ("../ExcelExportAPI/Classes/PHPExcel/IOFactory.php");


@extract($_POST);

if($_POST['Submit']=="Upload") {

    $flag = true;
    // validation arrays

    $modal_master = array();
    $series_master = array();

    $modal_data = mysqli_query($link1, "SELECT model_id,modelcode FROM model_master");
    if (mysqli_num_rows($modal_data) > 0) {
        while ($atrow = mysqli_fetch_assoc($modal_data)) {
            $modal_master[] = strtoupper($atrow['modelcode']);
        }
    }

    // seris number fetch
    $serial_number = mysqli_query($link1, "SELECT serial_no FROM warranty_data");
    if (mysqli_num_rows($serial_number) > 0) {
        while ($row = mysqli_fetch_assoc($serial_number)) {
            //  echo $row['serial_no']."<br/>";
            $series_master[] = $row['serial_no'];
        }
    }

    //
    mysqli_autocommit($link1, false);
    $flag = true;

    if ($_FILES["file"]["error"] == 0){
        move_uploaded_file($_FILES["file"]["tmp_name"],
            "../ExcelExportAPI/upload/".$today.$_FILES["file"]["name"]);

        $file="../ExcelExportAPI/upload/".$today.$_FILES["file"]["name"];
        chmod ($file, 0755);
    }

    $filename=$file;

    // LOAD EXCEL
    $identityType = PHPExcel_IOFactory::identify($filename);
    $object = PHPExcel_IOFactory::createReader($identityType);
    $object->setReadDataOnly(true);
    $objPHPExcel = $object->load($filename);

    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow();
    $sereial_mila=false;

    $global_serialno=array();
    for($row=2;$row<=$highestRow;$row++){
        $serial_no_1  = trim($sheet->getCellByColumnAndRow(1,$row)->getValue());
        $global_serialno[]=$serial_no_1;
    }

    for($row=0;$row<count($global_serialno);$row++){
       if(in_array($global_serialno[$row],$series_master)){ //
           $cflag="danger"; $cmsg="Failed"; $msg="Serieal Already exists"."SerialNo Code=".$global_serialno[$row];
           header("location: manu.php?chkflag=".$cflag."&chkmsg=".$cmsg."&msg=".$msg);
           exit;
       }
    }

    var_dump("yha tak aaya kaise");exit();

    for($row = 2; $row <= $highestRow; $row++){
        $model_code = trim($sheet->getCellByColumnAndRow(0,$row)->getValue());
        $serial_no  = trim($sheet->getCellByColumnAndRow(1,$row)->getValue());
        $start_date = excelDateToDate($sheet->getCellByColumnAndRow(2,$row)->getValue());
        $end_date   = excelDateToDate($sheet->getCellByColumnAndRow(3,$row)->getValue());
        $dist_code  = trim($sheet->getCellByColumnAndRow(4,$row)->getValue());
//        var_dump($model_code);exit;
        if($model_code == ''){
            $cflag="danger"; $cmsg="Failed"; $msg="Model Code Missing".($row+1);
            header("location: manu.php?chkflag=".$cflag."&chkmsg=".$cmsg."&msg=".$msg);
            exit;
        }
        if(in_array(strtoupper($model_code), $modal_master)){ // modal mila
//            while (in_array(in_array($serial_no, $series_master))){ // ek bhi serial duplicate mila
//                $cflag="danger"; $cmsg="Failed"; $msg="Serieal Already exists".($row+1)."SerialNo Code=".$serial_no;
//                header("location: manu.php?chkflag=".$cflag."&chkmsg=".$cmsg."&msg=".$msg);
//                exit();
//            }
            if(in_array($serial_no, $series_master)){ // serial h
                $cflag="danger"; $cmsg="Failed"; $msg="Serieal Already exists".($row+1)."SerialNo Code=".$serial_no;
                header("location: manu.php?chkflag=".$cflag."&chkmsg=".$cmsg."&msg=".$msg);
                exit;
            }else{

                if(!insert($link1,$model_code,$serial_no,$start_date,$end_date,$dist_code)){
                    $flag = false;
                }
            }
        }else{
            $cflag="danger"; $cmsg="Failed"; $msg="Model is not exists".($row+1)."Model Code=".$model_code;
            header("location: manu.php?chkflag=".$cflag."&chkmsg=".$cmsg."&msg=".$msg);
            exit;
        }
    }


    if ($flag) {
        mysqli_commit($link1);
        $cflag="success"; $cmsg="Success"; $msg="Successfully Uploaded";
    } else {
        mysqli_rollback($link1);
        $cflag="danger"; $cmsg="Failed"; $msg="Something Went Wrong";
    }
    mysqli_close($link1);
    header("location: manu.php?chkflag=".$cflag."&chkmsg=".$cmsg."&msg=".$msg);
    exit();

}

function insert($link1,$model_code,$serial_no,$start_date,$end_date,$dist_code)
{
    $model = mysqli_query($link1,"SELECT model_id,product_id,	brand_id FROM model_master WHERE modelcode='$model_code'");
    $mdata = mysqli_fetch_assoc($model);
    $mode_id=$mdata['model_id'];
    $brand_id=$mdata['brand_id'];
    $product_id=$mdata['product_id'];
    $sql = "INSERT INTO warranty_data
            (serial_no, start_date, end_date, brand_id, product_id, model_id, model_code, dealer_code, remark, dist_channel, division_code, update_date, status, pcb, transformer, entry_by, entry_date)
            VALUES
            ('$serial_no', '$start_date', '$end_date',
             '$brand_id', '$product_id', '$mode_id',
             '$model_code', '$dist_code', '', '', '',
             NOW(), '1', '', '', 'USER', NOW())";
//    var_dump($sql);exit;
    return mysqli_query($link1,$sql);
}
function excelDateToDate($v)
{
    if(is_numeric($v)){
        return gmdate("Y-m-d", ($v - 25569) * 86400);
    }
    return date("Y-m-d", strtotime($v));
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
            <h2 align="center"><i class="fa fa-upload"></i>Upload SALE IMEI</h2><div style="display:inline-block;float:right">
                <a href="../templates/batterySerieluploader.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"/></a></div>	<br></br>

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

                        <script>
                            // 3 second baad fade out
                            setTimeout(() => {
                                const table = document.getElementById("table");
                                table.classList.add("hide");
                            }, 3000);
                        </script>


                    </div>
                </div>
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