<?php
require_once("../includes/config.php");
require_once ("tat_call_dashboard_process.php");
global $link1,$screenwidth;
$arrstate = getAccessState($_SESSION['userid'],$link1);
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
$access_product=getAccessProduct($_SESSION['userid'],$link1);
$loader=(new CheckUserImplementationTAT($link1,$arrstate,$access_product))->whichtypeofUser();
$pagination='tat-call-data-grid.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="../js/highcharts.js"></script>
    <script src="../js/accessibility.js"></script>
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/jquery.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/moment.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <script src="../js/frmvalidate.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/common_js.js"></script>
    <script type="text/javascript" src="../js/daterangepicker.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
    <link rel="stylesheet" href="../css/datepicker.css">
    <script src="../js/bootstrap-datepicker.js"></script>
    <link rel="stylesheet" href="../css/tat_call_dashboard.css">
    <style>
        .full-width{
            width: 100% !important;
            transition: all 0.4s ease-in-out;
        }

        .hide_nav_{
            transform: translateX(-100%);
            display: none;
            transition: all 0.4s ease-in-out;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row content">
        <div id="hide_nav_">
            <?php include("../includes/leftnav2.php"); ?>
        </div>
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
            <div style="display: flex;flex-direction: row;justify-content: space-between;align-items: center;">
                <div  style="margin-top: 10px;width: max-content" onclick="collNav(this)">
                    <img  class="card" style="padding: 10px;" width="40" height="40" src="https://img.icons8.com/ios/50/menu--v7.png" alt="menu--v7"/>
                </div>
                <div  style="margin-top: 10px;width: max-content" onclick="collNav(this)">
                    <img  class="card" style="padding: 10px;" width="40" height="40" src="../img/filter.png" alt="menu--v7"/>
                </div>

            </div>
            <!-- ══ FILTER BOX ══ -->
            <form style="margin-top: 0;" id="dashboard_form" method="post">
                <div class="card filters" style="margin-top: 10px;">
                    <div class="filters-grid">
                        <div>
                            <label class="filter-label">Date Range</label>
                            <input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" />
                        </div>
                        <?php
                        try {
                            $loader->zoneDisplay($_REQUEST);
                            $loader->stateDisplay($_REQUEST);
                            $loader->bsiDisplay($_REQUEST);
                            $loader->enginnertype($_REQUEST);
                            $loader->product($_REQUEST);
                        } catch (Exception $e) {
                            $loader->errorUI($e->getMessage());
                        }
                        ?>
                    </div>
                    <div class="filters-row2">
                        <div class="filter-btn-group">
                            <button class="btn-primary" id="submit_button" type="button">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                Apply Filters
                            </button>
                            <button class="btn-secondary" id="reset_button" type="button">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div><!-- /home -->
    </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>