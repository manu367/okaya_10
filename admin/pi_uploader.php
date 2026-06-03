<?php
require_once("../includes/config.php");
$today=date("Y-m-d");
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
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
    <script type="text/javascript" language="javascript" >
        $(document).ready(function(){
            $('input[name="daterange"]').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });
    </script>
    <!-- Include Date Range Picker -->
    <script type="text/javascript" src="../js/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
    <!-- Include Date Picker -->
    <link rel="stylesheet" href="../css/datepicker.css">
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
            <h2 align="center"><i class="fa fa-shopping-bag"></i>PI Number Validateor</h2>

            <form class="form-horizontal" role="form" name="form1" action="pi_uploader_process.php" method="post" enctype="multipart/form-data" style="margin-top: 30px;">

                <div class="form-group">
                    <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>
                        <div class="col-md-6 input-append date" align="left">
                            <input type="date" name="date_range" id="date_rng1" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-5 control-label">PI Number</label>
                        <div class="col-md-5" align="left">
                            <input type="text" placeholder="PI Number" name="pi_number" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label class="col-md-5 control-label">File Upload</label>
                        <div class="col-md-6 input-append date" align="left">
                            <input type="file" name="file_upload" id="file_upload" class="form-control"/>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <div style="text-align: center;margin-top: 30px;">
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    <button type="submit" class="btn btn-secondary">Back</button>
                </div>
            </form>
        </div>

    </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>

</script>
</body>
</html>