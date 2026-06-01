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
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:       #f4f7fb;
            --white:    #ffffff;
            --border:   #e8ecf4;
            --border2:  #d1d9ed;
            --text:     #0f1629;
            --text2:    #3d4966;
            --muted:    #8892aa;
            --blue:     #2355f5;
            --blue-s:   #eef1fe;
            --green:    #0aaa6e;
            --green-s:  #e6f9f2;
            --amber:    #e6900a;
            --amber-s:  #fef3e2;
            --red:      #e8344a;
            --red-s:    #fdeef0;
            --purple:   #7c3aed;
            --purple-s: #f3effe;
            --r:        14px;
            --r-sm:     9px;
            --shadow:   0 1px 3px rgba(15,22,41,.06), 0 4px 16px rgba(15,22,41,.04);
            --shadow-md:0 4px 20px rgba(15,22,41,.09);
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        /* ── FILTER STYLES ── */
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }
        .filter-select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-color: #fff;
            padding-right: 28px;
            width: 100%;
            font-size: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding-left: 12px;
            padding-top: 8px;
            padding-bottom: 8px;
            color: #374151;
            outline: none;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
        }
        .filter-select:focus { box-shadow: 0 0 0 2px #bfdbfe; border-color: #93c5fd; }
        .filters { padding: 12px 16px; margin-bottom: 16px; }
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .filter-label {
            display: block;
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
            margin-bottom: 4px;
        }
        .filters-row2 {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f3f4f6;
        }
        .filter-item { flex: 1; }
        .btn-primary {
            display: flex; align-items: center; gap: 8px;
            background: #2563eb; color: #fff;
            font-size: 12px; font-weight: 500;
            padding: 8px 16px; border-radius: 8px;
            border: none; cursor: pointer;
            transition: background 0.15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
        }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary {
            display: flex; align-items: center; gap: 8px;
            background: #fff; color: #4b5563;
            font-size: 12px; font-weight: 500;
            padding: 8px 16px; border-radius: 8px;
            border: 1px solid #e5e7eb; cursor: pointer;
            transition: background 0.15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            white-space: nowrap;
        }
        .btn-secondary:hover { background: #f9fafb; }
        .filter-btn-group { display: flex; align-items: flex-end; gap: 8px; }
        @media (min-width: 640px) {
            .filters-grid { grid-template-columns: repeat(3, 1fr); }
            .filters-row2 { flex-direction: row; }
        }
        @media (min-width: 1024px) {
            .filters-grid { grid-template-columns: repeat(6, 1fr); }
        }

        /* ── KPI CARDS ── */
        .tat-kpi-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        @media (min-width: 600px)  { .tat-kpi-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1100px) { .tat-kpi-grid { grid-template-columns: repeat(5, 1fr); } }

        .tat-kpi-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 16px 18px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            transition: transform .2s, box-shadow .2s;
            cursor: default;
        }
        .tat-kpi-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
        .tat-kpi-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 3px;
            border-radius: var(--r) var(--r) 0 0;
        }
        .tat-kc-blue::after   { background: var(--blue); }
        .tat-kc-purple::after { background: var(--purple); }
        .tat-kc-amber::after  { background: var(--amber); }
        .tat-kc-red::after    { background: var(--red); }
        .tat-kc-green::after  { background: var(--green); }

        .tat-kpi-top { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 10px; }
        .tat-kpi-icon {
            width: 40px; height: 40px; border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .tat-kc-blue   .tat-kpi-icon { background: var(--blue-s); }
        .tat-kc-purple .tat-kpi-icon { background: var(--purple-s); }
        .tat-kc-amber  .tat-kpi-icon { background: var(--amber-s); }
        .tat-kc-red    .tat-kpi-icon { background: var(--red-s); }
        .tat-kc-green  .tat-kpi-icon { background: var(--green-s); }

        .tat-kpi-label { font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 6px; }
        .tat-kpi-value { font-size: 26px; font-weight: 800; line-height: 1; margin-bottom: 5px; font-family: 'JetBrains Mono', monospace; }
        .tat-kc-blue   .tat-kpi-value { color: var(--blue); }
        .tat-kc-purple .tat-kpi-value { color: var(--purple); }
        .tat-kc-amber  .tat-kpi-value { color: var(--amber); }
        .tat-kc-red    .tat-kpi-value { color: var(--red); }
        .tat-kc-green  .tat-kpi-value { color: var(--green); }
        .tat-kpi-sub { font-size: 11px; color: var(--muted); font-weight: 500; }

        .tat-kpi-bar-bg {
            width: 100%; height: 5px;
            background: var(--border);
            border-radius: 99px; overflow: hidden;
            margin-top: 10px;
        }
        .tat-kpi-bar-fill { height: 5px; border-radius: 99px; transition: width 0.6s ease; max-width: 100%; }
        .tat-kpi-bar-amber { background: var(--amber); }
        .tat-kpi-bar-red   { background: var(--red); }
        .tat-kpi-bar-green { background: var(--green); }

        /* ── SHARED CARD ── */
        .tat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 18px 20px;
            box-shadow: var(--shadow);
        }
        .tat-ct { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 3px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
        .tat-cs { font-size: 11px; color: var(--muted); font-weight: 500; margin-bottom: 16px; }

        /* ── GRID ROWS ── */
        .tat-row { display: grid; gap: 14px; margin-bottom: 14px; }
        .tat-col-1 { grid-template-columns: 1fr; }
        .tat-col-2 { grid-template-columns: 1fr; }
        .tat-col-3 { grid-template-columns: 1fr; }
        @media (min-width: 768px)  { .tat-col-2 { grid-template-columns: 1fr 1fr; } }
        @media (min-width: 1100px) { .tat-col-3 { grid-template-columns: 1fr 1fr 1fr; } }

        .tat-col-21 { display: grid; grid-template-columns: 1fr; gap: 14px; }
        @media (min-width: 768px)  { .tat-col-21 { grid-template-columns: repeat(2, 1fr); } }
        @media (min-width: 1200px) { .tat-col-21 { grid-template-columns: repeat(3, 1fr); } }

        /* ── BUCKET BARS ── */
        .tat-bucket-list { display: flex; flex-direction: column; gap: 11px; }
        .tat-bucket-row  { display: flex; align-items: center; gap: 10px; }
        .tat-bucket-label { font-size: 11px; font-weight: 600; color: var(--muted); width: 70px; flex-shrink: 0; }
        .tat-bucket-bg { flex: 1; height: 8px; background: var(--bg); border-radius: 99px; overflow: hidden; }
        .tat-bucket-fill { height: 8px; border-radius: 99px; max-width: 100%; transition: width .5s ease; }
        .tat-bucket-val { font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600; min-width: 48px; text-align: right; }

        /* ── PRODUCT TABLE ── */
        .tat-tbl { width: 100%; border-collapse: collapse; font-size: 12px; }
        .tat-tbl th {
            text-align: left; padding: 8px 10px;
            font-size: 10px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .6px;
            border-bottom: 2px solid var(--border); white-space: nowrap;
        }
        .tat-tbl td { padding: 10px 10px; border-bottom: 1px solid var(--border); font-weight: 500; color: var(--text); }
        .tat-tbl tr:last-child td { border-bottom: none; }
        .tat-tbl tr:hover td { background: var(--bg); }

        .tat-pill { display: inline-flex; align-items: center; padding: 3px 9px; border-radius: 100px; font-size: 10px; font-weight: 700; letter-spacing: .3px; }
        .tat-pill-green { background: var(--green-s); color: var(--green); }
        .tat-pill-amber { background: var(--amber-s); color: var(--amber); }
        .tat-pill-red   { background: var(--red-s);   color: var(--red); }

        /* ── ZONE BARS ── */
        .tat-zone-list { display: flex; flex-direction: column; gap: 16px; }
        .tat-zone-row  { display: flex; justify-content: space-between; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text2); }
        .tat-zone-pct  { font-family: 'JetBrains Mono', monospace; }
        .tat-prog-bg   { width: 100%; height: 10px; background: var(--bg); border-radius: 99px; overflow: hidden; }
        .tat-prog-fill { height: 10px; border-radius: 99px; }

        /* ── FOOTER ── */
        .tat-footer {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 0 4px; border-top: 1px solid var(--border);
            font-size: 11px; color: var(--muted);
            flex-wrap: wrap; gap: 8px; margin-top: 6px;
        }
        .tat-fleg { display: flex; gap: 12px; flex-wrap: wrap; }
        .tat-fleg-item { display: flex; align-items: center; gap: 5px; font-weight: 600; }
        .tat-fleg-dot  { width: 8px; height: 8px; border-radius: 50%; }

        @keyframes tatFadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        .tat-anim { animation: tatFadeUp .4s ease both; }

        /* ── LOADER ── */
        .dashboard-loader {
            position: fixed; inset: 0;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(3px);
            z-index: 99999;
            display: none; align-items: center; justify-content: center;
        }
        .dashboard-loader.active { display: flex; }
        .loader-box {
            background: white; padding: 30px 40px;
            border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            text-align: center; min-width: 220px;
        }
        .spinner {
            width: 55px; height: 55px;
            border: 5px solid #e5e7eb; border-top-color: #2563eb;
            border-radius: 50%; animation: spin 0.7s linear infinite; margin: auto;
        }
        .loader-box p { margin-top: 16px; font-size: 14px; font-weight: 600; color: #374151; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── CUSTOM ALERT ── */
        .custom-alert {
            position: fixed; top: 20px; right: 20px;
            min-width: 280px; padding: 14px 18px;
            border-radius: 10px; color: white;
            font-size: 14px; font-weight: 600;
            z-index: 999999;
            transform: translateX(120%); transition: 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .custom-alert.show { transform: translateX(0); }

        .hidden { display: none !important; }
    </style>
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
            <div style="margin-top: 10px;" onclick="collNav(this)">
                <img  class="card" style="padding: 10px;" width="40" height="40" src="https://img.icons8.com/ios/50/menu--v7.png" alt="menu--v7"/>
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

            <section class="hidden" id="dashboard_home">

                <!-- ══ KPI CARDS ══ -->
                <div class="tat-kpi-grid" style="margin-top:20px;">

                    <!-- Total Jobs -->
                    <div id="total_jobs" class="tat-kpi-card tat-kc-blue tat-anim" style="animation-delay:.05s">
                        <div class="tat-kpi-top">
                            <div class="tat-kpi-label">Total Closed Jobs</div>
                            <div class="tat-kpi-icon">📋</div>
                        </div>
                        <div class="tat-kpi-value">—</div>
                        <div class="tat-kpi-sub">This period</div>
                    </div>

                    <!-- Avg TAT -->
                    <div id="avg_tat_card" class="tat-kpi-card tat-kc-purple tat-anim" style="animation-delay:.09s">
                        <div class="tat-kpi-top">
                            <div class="tat-kpi-label">Avg TAT</div>
                            <div class="tat-kpi-icon">📊</div>
                        </div>
                        <div class="tat-kpi-value">—</div>
                        <div class="tat-kpi-sub">Average TAT days</div>
                    </div>

                    <!-- TAT-1 -->
                    <div id="within_tat_card" class="tat-kpi-card tat-kc-amber tat-anim" style="animation-delay:.13s">
                        <div class="tat-kpi-top">
                            <div class="tat-kpi-label">TAT-1 Close Jobs</div>
                            <div class="tat-kpi-icon">🎯</div>
                        </div>
                        <div class="tat-kpi-value" id="tat1_val">—</div>
                        <div class="tat-kpi-sub">— jobs</div>
                        <div class="tat-kpi-sub" id="tat1_pct" style="display: none">— calls</div>
                        <div class="tat-kpi-bar-bg" style="display: none">
                            <div class="tat-kpi-bar-fill tat-kpi-bar-amber" id="tat1_bar" style="width:0%"></div>
                        </div>
                    </div>

                    <!-- TAT-2 -->
                    <div id="sla_breached_card" class="tat-kpi-card tat-kc-red tat-anim" style="animation-delay:.17s">
                        <div class="tat-kpi-top">
                            <div class="tat-kpi-label">TAT-2 Close Job</div>
                            <div class="tat-kpi-icon">💾</div>
                        </div>
                        <div class="tat-kpi-value" id="tat2_val">—</div>
                        <div class="tat-kpi-sub">— of jobs</div>
                        <div class="tat-kpi-sub" id="tat2_pct" style="display: none">— of jobs</div>
                        <div class="tat-kpi-bar-bg hidden">
                            <div class="tat-kpi-bar-fill tat-kpi-bar-red" id="tat2_bar" style="width:0%"></div>
                        </div>
                    </div>

                    <!-- TAT-3 -->
                    <div id="at_risk_card" class="tat-kpi-card tat-kc-green tat-anim" style="animation-delay:.21s">
                        <div class="tat-kpi-top">
                            <div class="tat-kpi-label">TAT-3 Close Job</div>
                            <div class="tat-kpi-icon">📝</div>
                        </div>
                        <div class="tat-kpi-value" id="tat3_val">—</div>
                        <div class="tat-kpi-sub">— of jobs</div>
                        <div class="tat-kpi-sub hidden" id="tat3_pct">— of jobs</div>
                        <div class="tat-kpi-bar-bg hidden">
                            <div class="tat-kpi-bar-fill tat-kpi-bar-green" id="tat3_bar" style="width:0%"></div>
                        </div>
                    </div>

                </div>

                <!-- ══ ROW 1: TAT Trend Chart — full width ══ -->
                <div class="tat-row tat-col-1" style="margin-bottom:14px;">
                    <div class="tat-card tat-anim" style="animation-delay:.25s">
                        <div class="tat-ct">TAT Trend Analysis</div>
                        <div class="tat-cs">Daily TAT trend over selected period — multiple series</div>
                        <div id="tatTrendChart" style="height:280px;"></div>
                    </div>
                </div>

                <!-- ══ ROW 2: TAT Bucket Cards (TAT-1, TAT-2, TAT-3) ══ -->
                <div class="tat-row tat-col-21" style="margin-bottom:14px;">

                    <!-- TAT-1 Bucket -->
                    <div class="tat-card tat-anim" style="animation-delay:.33s">
                        <div class="tat-ct">Calls by TAT-1 Bucket</div>
                        <div class="tat-cs">TAT distribution across time ranges</div>
                        <div class="tat-bucket-list" id="tatBucketWrap">
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=24 HRS</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#0aaa6e" data-count="0" data-pct="0%" data-color="#0aaa6e"></div></div>
                                <div class="tat-bucket-val" style="color:#0aaa6e">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"><=36 HRS</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#2355f5" data-count="0" data-pct="0%" data-color="#2355f5"></div></div>
                                <div class="tat-bucket-val" style="color:#2355f5">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"><=48 HRS</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#7c3aed" data-count="0" data-pct="0%" data-color="#7c3aed"></div></div>
                                <div class="tat-bucket-val" style="color:#7c3aed">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=72 HRS</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#e6900a" data-count="0" data-pct="0%" data-color="#e6900a"></div></div>
                                <div class="tat-bucket-val" style="color:#e6900a">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> >72HRS </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#e8344a" data-count="0" data-pct="0%" data-color="#e8344a"></div></div>
                                <div class="tat-bucket-val" style="color:#e8344a">—</div>
                            </div>
                        </div>
                    </div>

                    <!-- TAT-2 Bucket -->
                    <div class="tat-card tat-anim" style="animation-delay:.36s">
                        <div class="tat-ct">Calls by TAT-2 Bucket</div>
                        <div class="tat-cs">TAT distribution across time ranges</div>
                        <div class="tat-bucket-list" id="tatBucketWrap-2">
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=3 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#0aaa6e" data-count="0" data-pct="0%" data-color="#0aaa6e"></div></div>
                                <div class="tat-bucket-val" style="color:#0aaa6e">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=5 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#2355f5" data-count="0" data-pct="0%" data-color="#2355f5"></div></div>
                                <div class="tat-bucket-val" style="color:#2355f5">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"><=7 Days</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#7c3aed" data-count="0" data-pct="0%" data-color="#7c3aed"></div></div>
                                <div class="tat-bucket-val" style="color:#7c3aed">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> >7 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#e6900a" data-count="0" data-pct="0%" data-color="#e6900a"></div></div>
                                <div class="tat-bucket-val" style="color:#e6900a">—</div>
                            </div>
                        </div>
                    </div>

                    <!-- TAT-3 Bucket -->
                    <div class="tat-card tat-anim" style="animation-delay:.39s">
                        <div class="tat-ct">Calls by TAT-3 Bucket</div>
                        <div class="tat-cs">TAT distribution across time ranges</div>
                        <div class="tat-bucket-list" id="tatBucketWrap-3">
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=7 Days  </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#0aaa6e" data-count="0" data-pct="0%" data-color="#0aaa6e"></div></div>
                                <div class="tat-bucket-val" style="color:#0aaa6e">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=15 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#2355f5" data-count="0" data-pct="0%" data-color="#2355f5"></div></div>
                                <div class="tat-bucket-val" style="color:#2355f5">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> <=21 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#7c3aed" data-count="0" data-pct="0%" data-color="#7c3aed"></div></div>
                                <div class="tat-bucket-val" style="color:#7c3aed">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"><=30 Days</div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#e6900a" data-count="0" data-pct="0%" data-color="#e6900a"></div></div>
                                <div class="tat-bucket-val" style="color:#e6900a">—</div>
                            </div>
                            <div class="tat-bucket-row">
                                <div class="tat-bucket-label"> >30 Days </div>
                                <div class="tat-bucket-bg"><div class="tat-bucket-fill" style="width:0%;background:#e8344a" data-count="0" data-pct="0%" data-color="#e8344a"></div></div>
                                <div class="tat-bucket-val" style="color:#e8344a">—</div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ══ ROW 3: Product TAT + Zone Wise ══ -->
                <div class="tat-row tat-col-2" style="margin-bottom:14px;">

                    <!-- Avg TAT by Product -->
                    <div class="tat-card tat-anim" style="animation-delay:.41s">
                        <div class="tat-ct">Avg TAT by Product</div>
                        <div class="tat-cs">Product-wise turnaround comparison</div>
                        <table class="tat-tbl" id="tat_table">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Avg TAT</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;">Apply filters to load data</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Zone Wise TAT -->
                    <div class="tat-card tat-anim" style="animation-delay:.45s">
                        <div class="tat-ct">Zone Wise TAT</div>
                        <div class="tat-cs">Avg TAT breach % per zone</div>
                        <div class="tat-zone-list" id="tat-zone-list"></div>
                    </div>

                </div>

                <!-- ══ FOOTER ══ -->
                <div class="tat-footer">
                    <div class="tat-fleg">
                        <div class="tat-fleg-item"><div class="tat-fleg-dot" style="background:var(--green)"></div>Within TAT (&lt;24 HRS)</div>
                        <div class="tat-fleg-item"><div class="tat-fleg-dot" style="background:var(--amber)"></div>At Risk (24–72 HRS)</div>
                        <div class="tat-fleg-item"><div class="tat-fleg-dot" style="background:var(--red)"></div>Breached (&gt;72 HRS)</div>
                    </div>
                    <div>Updated: <?=date('d M Y, h:i A')?></div>
                </div>

            </section>
        </div><!-- /home -->
    </div>
</div>
<!-- Loader UI -->
<div id="dashboardLoader" class="dashboard-loader">
    <div class="loader-box">
        <div class="spinner"></div>
        <p>Loading Dashboard...</p>
    </div>
</div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>

<!-- ══ JS ══ -->
<script>
    // ============================================================
    //  HIGHCHARTS GLOBAL DEFAULTS
    // ============================================================
    Highcharts.setOptions({
        chart: { style: { fontFamily: "'Plus Jakarta Sans',sans-serif" } },
        credits: { enabled: false },
        title: { text: '' },
        tooltip: { borderRadius: 10, shadow: false }
    });

    var tatTrendChartInst = null;
    var tatDailyChartInst = null;

    function collNav(e){
        const home=document.getElementById("home");//style="width: 100%;"
        const nav=document.getElementById("hide_nav_");
        nav.classList.toggle('hide_nav_');
        home.classList.toggle("full-width");
        setTimeout(function () {
            Highcharts.charts.forEach(function (chart) {
                if (chart) chart.reflow();
            });
        }, 420);
    }

    // ============================================================
    //  Multi-line TAT Trend Chart
    //  JSON: line_chart.x_axis = string[]
    //        line_chart.series = [{name, color, data[]}, ...]
    // ============================================================
    function drawTrendChart(xAxis, seriesArr) {
        if (tatTrendChartInst) tatTrendChartInst.destroy();

        var series = seriesArr.map(function (s) {
            return {
                name:  s.name,
                color: s.color,
                data:  s.data.map(function (v) {
                    var n = parseFloat(v);
                    return isNaN(n) ? 0 : n;
                })
            };
        });

        tatTrendChartInst = Highcharts.chart('tatTrendChart', {
            chart: {
                type: 'line',
                backgroundColor: 'transparent',
                margin: [60, 20, 60, 55]  // ✅ top margin badha diya legend ke liye
            },
            xAxis: {
                categories: xAxis,
                lineColor: '#e8ecf4',
                tickColor: '#e8ecf4',
                labels: {
                    style: { fontSize: '11px', color: '#8892aa', fontWeight: '600' }
                }
            },
            yAxis: {
                title: {
                    text: 'AVG TAT',
                    style: { fontSize: '10px', color: '#8892aa' }
                },
                labels: {
                    style: { fontSize: '10px', color: '#8892aa' }
                },
                gridLineColor: '#f0f2f8',
                min: 0
            },
            tooltip: {
                shared: true,
                borderRadius: 10,
                shadow: false,
                backgroundColor: '#ffffff',
                borderColor: '#e8ecf4',
                style: { fontSize: '12px', color: '#0f1629' },
                // ── FIX: TAT-1 hours mein hai, TAT-2/3 days mein
                formatter: function () {
                    var s = '<b>' + this.x + '</b><br/>';
                    this.points.forEach(function (p) {
                        var unit = p.series.name.indexOf('hrs') !== -1 ? ' days' : ' days';
                        s += '<span style="color:' + p.color + '">●</span> '
                            + p.series.name + ': <b>'
                            + (p.y !== null ? p.y.toFixed(2) + unit : '—')
                            + '</b><br/>';
                    });
                    return s;
                }
            },
            legend: {
                enabled: true,
                align: 'center',
                verticalAlign: 'top',      // ✅ bottom se top
                itemStyle: { fontSize: '11px', fontWeight: '600', color: '#3d4966' }
            },
            plotOptions: {
                line: {
                    lineWidth: 2.5,
                    connectNulls: false,   // null pe line break hogi — sahi behaviour
                    marker: {
                        radius: 4,
                        symbol: 'circle',
                        lineWidth: 2,
                        lineColor: '#fff'
                    }
                }
            },
            series: series
        });
    }

    // ============================================================
    //  setAllData — master setter
    //  JSON keys:
    //    card_data, line_chart,
    //    call_by_tat_1, call_by_tat_2, call_by_tat_3,
    //    avg_tat_by_product, zone_wise_tat
    // ============================================================
    TatDocmentManager.prototype.setAllData = function (data) {
        if (data.card_data)          this.setCardData(data.card_data);

        // ── FIX: line_chart ke andar x_axis aur series hain
        if (data.line_chart && data.line_chart.x_axis && data.line_chart.series) {
            drawTrendChart(data.line_chart.x_axis, data.line_chart.series);
        }

        if (data.call_by_tat_1)      this.setBucketDataById_tat1('tatBucketWrap',   data.call_by_tat_1);
        if (data.call_by_tat_2)      this.setBucketDataById_tat2('tatBucketWrap-2', data.call_by_tat_2);
        if (data.call_by_tat_3)      this.setBucketDataById_tat3('tatBucketWrap-3', data.call_by_tat_3);
        if (data.avg_tat_by_product) this.setProductTable(data.avg_tat_by_product);
        if (data.zone_wise_tat)      this.setZoneData(data.zone_wise_tat);
    };

    // ============================================================
    //  TatDocmentManager
    // ============================================================
    function TatDocmentManager() {
        this.dashboardpage     = null;
        this.total_job_card    = null;
        this.avg_tat_card      = null;
        this.within_tat_card   = null;
        this.sla_breached_card = null;
        this.at_risk_card      = null;
        this.tat_table         = null;
        this.tat_zone_list     = null;
        this.dataRange         = null;
        this.zone              = null;
        this.state             = null;
        this.bsi               = null;
        this.enginertype       = null;
        this.product           = null;
        this.submit_button     = null;
        this.loader            = null;
    }

    TatDocmentManager.prototype.init = function () {
        this.dashboardpage     = document.getElementById('dashboard_home');
        this.total_job_card    = document.getElementById('total_jobs');
        this.avg_tat_card      = document.getElementById('avg_tat_card');
        this.within_tat_card   = document.getElementById('within_tat_card');
        this.sla_breached_card = document.getElementById('sla_breached_card');
        this.at_risk_card      = document.getElementById('at_risk_card');
        this.tat_table         = document.getElementById('tat_table');
        this.tat_zone_list     = document.getElementById('tat-zone-list');
        this.dataRange         = document.getElementById('date_rng');
        this.zone              = document.querySelector('[name="zone"]');
        this.state             = document.querySelector('[name="state"]');
        this.bsi               = document.querySelector('[name="bsi"]');
        this.enginertype       = document.querySelector('[name="enginer_type"]');
        this.product           = document.querySelector('[name="product"]');
        this.submit_button     = document.getElementById('submit_button');
        this.loader            = document.getElementById('dashboardLoader');
    };

    TatDocmentManager.prototype.showLoader    = function () { this.loader.classList.add('active');     this.hideDashboard(); };
    TatDocmentManager.prototype.hideLoader    = function () { this.loader.classList.remove('active');  };
    TatDocmentManager.prototype.showDashboard = function () { this.dashboardpage.classList.remove('hidden'); };
    TatDocmentManager.prototype.hideDashboard = function () { this.dashboardpage.classList.add('hidden');    };

    TatDocmentManager.prototype.bindEvent = function () {
        var self = this;
        document.getElementById('submit_button').addEventListener('click', function (e) {
            e.preventDefault();
            self.formSubmit();
        });
        document.getElementById('reset_button').addEventListener('click', function () {
            document.getElementById('dashboard_form').reset();
        });
    };

    TatDocmentManager.prototype.showMessage = function (message, type) {
        type = type || 'error';
        var bgColor = type === 'success' ? '#22c55e'
            : type === 'warning'         ? '#f59e0b'
                :                              '#ef4444';
        var div = document.createElement('div');
        div.className = 'custom-alert';
        div.style.background = bgColor;
        div.innerText = message;
        document.body.appendChild(div);
        setTimeout(function () { div.classList.add('show'); }, 100);
        setTimeout(function () {
            div.classList.remove('show');
            setTimeout(function () { div.remove(); }, 300);
        }, 3500);
    };

    // ============================================================
    //  setCardData
    //  JSON keys: total_jobs, avg_tat, tat_1[0/1], tat2[0/1], tat_3[0/1]
    // ============================================================
    TatDocmentManager.prototype.setCardData = function (card_data) {
        // Total Jobs
        this.total_job_card.querySelector('.tat-kpi-value').textContent = card_data.total_jobs;

        // Avg TAT
        this.avg_tat_card.querySelector('.tat-kpi-value').textContent = card_data.avg_tat;

        // TAT-1  (key: tat_1)
        document.getElementById('tat1_val').textContent = card_data.tat_1[0];
        document.getElementById('tat1_pct').textContent = card_data.tat_1[1] + ' calls';
        document.getElementById('tat1_bar').style.width = Math.min(parseFloat(card_data.tat_1[1]) || 0, 100) + '%';

        // TAT-2  (key: tat2)
        document.getElementById('tat2_val').textContent = card_data.tat2[0];
        document.getElementById('tat2_pct').textContent = card_data.tat2[1] + ' of jobs';
        document.getElementById('tat2_bar').style.width = Math.min(parseFloat(card_data.tat2[1]) || 0, 100) + '%';

        // TAT-3  (key: tat_3)
        document.getElementById('tat3_val').textContent = card_data.tat_3[0];
        document.getElementById('tat3_pct').textContent = card_data.tat_3[1] + ' of jobs';
        document.getElementById('tat3_bar').style.width = Math.min(parseFloat(card_data.tat_3[1]) || 0, 100) + '%';
    };

    // ============================================================
    //  setBucketDataById — generic, works for all 3 wrappers
    //  JSON bucket keys: 0_24, 24_48, 3_6, 6_10, 11_15, above_15
    // ============================================================
    TatDocmentManager.prototype.setBucketDataById_tat1 = function (wrapperId, bucket) {
        var keys = ['24', '36', '48', '72', '72_plus'];
        var wrap = document.getElementById(wrapperId);
        if (!wrap) return;

        wrap.querySelectorAll('.tat-bucket-row').forEach(function (row, i) {
            var fill  = row.querySelector('.tat-bucket-fill');
            var valEl = row.querySelector('.tat-bucket-val');
            var entry = bucket[keys[i]];

            var count  = (entry && entry[0] !== undefined) ? entry[0] : '0';
            var pctStr = (entry && entry[1] !== undefined) ? entry[1] : '0%';
            var pctNum = Math.min(parseFloat(pctStr) || 0, 100);

            fill.setAttribute('data-count', count);
            fill.setAttribute('data-pct',   pctStr);
            fill.style.width  = pctNum + '%';
            valEl.textContent = parseInt(count).toLocaleString();
            valEl.style.color = fill.getAttribute('data-color') || fill.style.background;
        });
    };
    TatDocmentManager.prototype.setBucketDataById_tat2 = function (wrapperId, bucket) {
        var keys = ['3', '5', '7', '7_plus'];
        var wrap = document.getElementById(wrapperId);
        if (!wrap) return;

        wrap.querySelectorAll('.tat-bucket-row').forEach(function (row, i) {
            var fill  = row.querySelector('.tat-bucket-fill');
            var valEl = row.querySelector('.tat-bucket-val');
            var entry = bucket[keys[i]];

            var count  = (entry && entry[0] !== undefined) ? entry[0] : '0';
            var pctStr = (entry && entry[1] !== undefined) ? entry[1] : '0%';
            var pctNum = Math.min(parseFloat(pctStr) || 0, 100);

            fill.setAttribute('data-count', count);
            fill.setAttribute('data-pct',   pctStr);
            fill.style.width  = pctNum + '%';
            valEl.textContent = parseInt(count).toLocaleString();
            valEl.style.color = fill.getAttribute('data-color') || fill.style.background;
        });
    };
    TatDocmentManager.prototype.setBucketDataById_tat3 = function (wrapperId, bucket) {
        var keys = ['7', '15', '21', '30', '30_plus'];
        var wrap = document.getElementById(wrapperId);
        if (!wrap) return;

        wrap.querySelectorAll('.tat-bucket-row').forEach(function (row, i) {
            var fill  = row.querySelector('.tat-bucket-fill');
            var valEl = row.querySelector('.tat-bucket-val');
            var entry = bucket[keys[i]];

            var count  = (entry && entry[0] !== undefined) ? entry[0] : '0';
            var pctStr = (entry && entry[1] !== undefined) ? entry[1] : '0%';
            var pctNum = Math.min(parseFloat(pctStr) || 0, 100);

            fill.setAttribute('data-count', count);
            fill.setAttribute('data-pct',   pctStr);
            fill.style.width  = pctNum + '%';
            valEl.textContent = parseInt(count).toLocaleString();
            valEl.style.color = fill.getAttribute('data-color') || fill.style.background;
        });
    };

    // ============================================================
    //  setProductTable
    //  JSON: avg_tat_by_product.{key}.{name,avg_tat,min,max,status,status_color}
    // ============================================================
    TatDocmentManager.prototype.setProductTable = function (products) {
        var colorMap = { red: 'var(--red)', green: 'var(--green)', yellow: 'var(--amber)' };
        var pillMap  = { red: 'tat-pill-red', green: 'tat-pill-green', yellow: 'tat-pill-amber' };
        var labelMap = { red: 'Breached', green: 'Within TAT', yellow: 'At Risk' };

        var tbody = this.tat_table.querySelector('tbody');
        tbody.innerHTML = '';

        Object.keys(products).forEach(function (key) {
            var p   = products[key];
            var col = colorMap[p.status_color]  || 'var(--muted)';
            var pill= pillMap[p.status_color]   || '';
            var lbl = labelMap[p.status_color]  || p.status;
            tbody.innerHTML +=
                '<tr>' +
                '<td style="font-weight:700">' + p.name + '</td>' +
                '<td><span style="color:' + col + ';font-family:\'JetBrains Mono\',monospace;font-weight:600">' + p.avg_tat + '</span></td>' +
                '<td style="color:var(--muted)">' + p.min + ' HRS</td>' +
                '<td style="color:var(--muted)">' + p.max + ' HRS</td>' +
                '<td><span class="tat-pill ' + pill + '">' + lbl + '</span></td>' +
                '</tr>';
        });
    };

    // ============================================================
    //  setZoneData
    //  JSON: zone_wise_tat = [{name, per}, ...]
    // ============================================================
    TatDocmentManager.prototype.setZoneData = function (zones) {
        var colorScale = ['#e8344a', '#e6900a', '#2355f5', '#0aaa6e', '#7c3aed'];
        var html = '';
        zones.forEach(function (z, idx) {
            var pctNum = Math.min(parseInt(z.per) || 0, 100);
            var col    = colorScale[idx % colorScale.length];
            html +=
                '<div>' +
                '<div class="tat-zone-row">' +
                '<span>' + z.name + ' Zone</span>' +
                '<span class="tat-zone-pct" style="color:' + col + '">' + z.per + '</span>' +
                '</div>' +
                '<div class="tat-prog-bg"><div class="tat-prog-fill" style="width:' + pctNum + '%;background:' + col + '"></div></div>' +
                '</div>';
        });
        this.tat_zone_list.innerHTML = html;
    };

    TatDocmentManager.prototype.setAllData = function (data) {
        if (data.card_data)           this.setCardData(data.card_data);
        if (data.line_chart)          drawTrendChart(data.line_chart.x_axis, data.line_chart.series);
        if (data.call_by_tat_1)       this.setBucketDataById_tat1('tatBucketWrap',   data.call_by_tat_1);
        if (data.call_by_tat_2)       this.setBucketDataById_tat2('tatBucketWrap-2', data.call_by_tat_2);
        if (data.call_by_tat_3)       this.setBucketDataById_tat3('tatBucketWrap-3', data.call_by_tat_3);
        if (data.avg_tat_by_product)  this.setProductTable(data.avg_tat_by_product);
        if (data.zone_wise_tat)       this.setZoneData(data.zone_wise_tat);
    };

    // ============================================================
    //  formSubmit
    // ============================================================
    TatDocmentManager.prototype.formSubmit = function () {
        var self = this;

        if (!navigator.onLine) {
            this.showMessage('No internet connection detected', 'error');
            return;
        }

        this.hideDashboard();
        this.showLoader();
        this.submit_button.disabled = true;

        var formdata = new FormData();
        formdata.set('data_range',    this.dataRange   ? this.dataRange.value   : '');
        formdata.set('zone',          this.zone        ? this.zone.value        : '');
        formdata.set('state',         this.state       ? this.state.value       : '');
        formdata.set('bsi',           this.bsi         ? this.bsi.value         : '');
        formdata.set('enginner_type', this.enginertype ? this.enginertype.value : '');
        formdata.set('product',       this.product     ? this.product.value     : '');
        formdata.set('tat_data', '1');

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '../pagination/<?=$pagination?>', true);
        xhr.timeout = 150000;

        xhr.ontimeout = function () {
            self.submit_button.disabled = false;
            self.hideLoader();
            self.showMessage('Request timed out. Server is taking too long.', 'error');
        };

        xhr.onerror = function () {
            self.submit_button.disabled = false;
            self.hideLoader();
            self.showMessage('Unable to connect to server. Check your network.', 'error');
        };

        xhr.onload = function () {
            self.submit_button.disabled = false;
            self.hideLoader();

            if (xhr.status < 200 || xhr.status >= 300) {
                self.showMessage('Server error (' + xhr.status + '): ' + xhr.statusText, 'error');
                return;
            }

            var data;
            try {
                data = JSON.parse(xhr.responseText);
            } catch (e) {
                self.showMessage('Server returned invalid data. Please try again.', 'error');
                return;
            }

            if (!data || (typeof data === 'object' && Object.keys(data).length === 0)) {
                self.showMessage('No data found for the selected filters.', 'warning');
                return;
            }

            if (data.success === false) {
                self.showMessage(data.message || 'Something went wrong on the server.', 'error');
                return;
            }

            self.setAllData(data);
            self.showDashboard();
        };

        xhr.send(formdata);
    };

    // ============================================================
    //  Boot
    // ============================================================
    document.addEventListener('DOMContentLoaded', function () {
        var tat = new TatDocmentManager();
        tat.init();
        tat.bindEvent();
        tat.hideDashboard();
    });
</script>

<script>
    $(document).ready(function () {
        $('input[name="daterange"]').daterangepicker({
            maxDate: moment(),
            locale: { format: 'YYYY-MM-DD' }
        });
    });
</script>

</body>
</html>