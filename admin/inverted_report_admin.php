<?php
require_once("../includes/config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form submit hua hai
    $daterange = $_POST['daterange'] ?? '';
    $brand     = $_POST['brand'] ?? '';
    $models    = $_POST['model'] ?? [];

    if($daterange=="" && $brand==""){
        $cflag="danger"; $cmsg="Failed"; $msg="Some thing is missing, Please check again";
        header("location: batteryserialuploader.php?chkflag=".$cflag."&chkmsg=".$cmsg."&msg=".$msg);
        exit;
    }

    $startdate = $enddate = '';
    if (strpos($daterange, ' - ') !== false) {
        list($startdate, $enddate) = explode(' - ', $daterange);
    } else {
        // Agar user sirf ek date daale
        $startdate = $enddate = $daterange;
    }//

    // convert array → CSV
    $model_ids = implode(',', $models); // M00002,M00005

    $file_link="excelexport.php?"
            . "rname=" . base64_encode("demo")
            . "&rheader=" . base64_encode("Inverted_reports")
            . "&startdate=" . ($startdate ?? '2004-10-12')
            . "&enddate=" . ($enddate ?? '2024-12-31')
            . "&brandId=" . ($brand ?? '1')
            . "&model_id=" . ($model_ids ?? 'M00002');
//    echo $file_link;
    $_SESSION['download_file'] = $file_link;
//    var_dump($_SESSION);
//    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/jquery.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/moment.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script type="text/javascript">
        $(document).ready(function() {
            $("#form1").validate();
        });
    </script>
    <script src="../js/frmvalidate.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/common_js.js"></script>
    <script type="text/javascript" language="javascript" >
        $(document).ready(function(){
            $('input[name="daterange"]').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });




        $(document).ready(function() {
            $('#frm_state').multiselect({
                includeSelectAllOption: true,
                buttonWidth:"200"

            });
        });

        $(document).ready(function() {
            $('#frm_loc').multiselect({
                includeSelectAllOption: true,
                buttonWidth:"200"

            });
        });

        $(document).ready(function() {
            $('#model').multiselect('rebuild');
            $('#brand').change(function() {
                var brand_id = $(this).val(); // selected brand ka id
                if(brand_id != "0") { // agar koi valid brand select hua ho
                    $.ajax({
                        url: 'get_brands.php', // aapka URL
                        type: 'GET',
                        data: { brand_id: brand_id },
                        success: function(response) {
                            // response me model options aayenge
                            $('#model').html(response); // model dropdown me add karo
                            $('#model').prop('disabled', false); // enable the dropdown
                             $('#model').multiselect('rebuild'); // agar multiselect use ho rha hai
                        },
                        error: function() {
                            alert('Error fetching models');
                        }
                    });
                } else {
                    $('#model').html(''); // empty karo agar "--Select--" select ho
                    $('#model').prop('disabled', true);
                    $('#model').multiselect('rebuild');
                }
            });
        });



    </script>
    <!-- Include Date Range Picker -->
    <script type="text/javascript" src="../js/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
    <!-- Include Date Picker -->
    <link rel="stylesheet" href="../css/datepicker.css">
    <!-- Include multiselect -->
    <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
    <script src="../js/bootstrap-datepicker.js"></script>
    <title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
            <h2 align="center"><i class="fa fa-pencil-square-o"></i> Inverted Serial Report</h2>
            <?php if($_REQUEST['msg']){?><br>
                <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
            <?php }?>
            <form class="form-horizontal" role="form" name="form1"  id="form1" action="" method="post">

<!--                date-range-->
                <div class="form-group">
                    <div id= "dt_range" class="col-md-6"><label class="col-md-5 control-label">Date Range</label>
                        <div class="col-md-6 input-append date" align="left">
                            <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
                        </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-5 control-label"></label>
                        <div class="col-md-5" align="left">

                        </div>
                    </div>
                </div><!--close form group-->


                <div class="form-group">
                    <div class="col-md-6">
                        <label class="col-md-5 control-label">Select Brand <span style="color:#F00">*</span></label>
                        <div class="col-md-5" id="branddiv">
                            <select name="brand" id="brand" class="form-control">
                                <!-- Brand options dynamically loaded here -->
                                <option value="">--Select option--</option>
                                <?php
                                $brnd = mysqli_query($link1,"SELECT * FROM brand_master");
                                while($stateinfo = mysqli_fetch_assoc($brnd)){
                                    ?>
                                    <option value="<?=$stateinfo['brand_id']?>"><?=$stateinfo['brand']?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="col-md-5 control-label">Select Model<span style="color:#F00">*</span></label>
                        <div class="col-md-6">
                            <select disabled name="model[]" id="model" multiple="multiple" class="form-control">
<!--                                here add option-->
<!--                                <option>--Select option--</option>-->
                            </select>
                        </div>
                    </div>
                </div>





                <div class="form-group">

                    <div class="col-md-6"><label class="col-md-5 control-label"></label>
                        <div class="col-md-5">
                            <input name="Submit" type="submit" class="btn btn-success" value="GO"  title="Go!">
                        </div>
                    </div>
                </div><!--close form group-->
            </form>

            <div class="container " id="file">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <?php
                        if(isset($_SESSION['download_file'])):  ?>
                            <a href="<?= $_SESSION['download_file'] ?>" title="Download Inverted report file">
                                <i class="fa fa-file-excel-o fa-2x faicon" title="Export user details in Excel"></i>
                            </a>
                            <script>
                                let file=document.getElementById("file");
                                setTimeout(()=>{
                                    file.classList.toggle("hide")
                                },10000);
                            </script>
                            <?php unset($_SESSION['download_file']); ?>
                        <?php endif; ?>
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