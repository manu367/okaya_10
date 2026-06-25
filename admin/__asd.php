<?php
require_once("../includes/config.php");
require_once ("tat_call_dashboard_process.php");
global $link1,$screenwidth;

$arrstate = getAccessState($_SESSION['userid'],$link1);
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
$access_product=getAccessProduct($_SESSION['userid'],$link1);

$loader=(new CheckUserImplementationTAT($link1,$arrstate,$access_product))->whichtypeofUser();
$pagination='dashboard-pendingcall-data-grid.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <script src="../js/highcharts.js"></script>
    <script src="../js/accessibility.js"></script>
    <script src="../js/jquery.min.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/frmvalidate.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/common_js.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════
           RESET & BASE
        ═══════════════════════════════════════════ */
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
            --r:        16px;
            --r-sm:     10px;
            --shadow:   0 1px 3px rgba(15,22,41,.06), 0 4px 16px rgba(15,22,41,.05);
            --shadow-md:0 4px 20px rgba(15,22,41,.09);
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }

        .mono { font-family: 'JetBrains Mono', monospace; }

        /* ═══════════════════════════════════════════
           LAYOUT WRAPPER
        ═══════════════════════════════════════════ */
        .tat-wrap {
            padding: 24px 20px 40px;
        }

        @media (max-width: 600px) { .tat-wrap { padding: 14px 12px 30px; } }

        /* ═══════════════════════════════════════════
           PAGE HEADER
        ═══════════════════════════════════════════ */
        .tat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 22px;
        }

        .tat-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .tat-icon-box {
            width: 44px; height: 44px;
            background: var(--blue);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .tat-icon-box i { color: #fff; font-size: 18px; }

        .tat-header-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--text);
            letter-spacing: -.4px;
            line-height: 1.2;
        }
        .tat-header-sub {
            font-size: 12px;
            color: var(--muted);
            font-weight: 500;
        }

        .tat-live-pill {
            display: flex; align-items: center; gap: 6px;
            background: var(--green-s);
            border: 1px solid rgba(10,170,110,.2);
            border-radius: 100px;
            padding: 7px 14px;
            font-size: 11px; font-weight: 700;
            color: var(--green);
            text-transform: uppercase; letter-spacing: .4px;
        }
        .tat-live-dot {
            width: 7px; height: 7px;
            background: var(--green);
            border-radius: 50%;
            animation: tatBlink 2s ease infinite;
        }
        @keyframes tatBlink {
            0%,100%{opacity:1} 50%{opacity:.3}
        }

        /* ═══════════════════════════════════════════
           FILTER BAR
        ═══════════════════════════════════════════ */
        .tat-filter-bar {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 16px 20px;
            margin-bottom: 24px;
            box-shadow: var(--shadow);
        }

        .tat-filter-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        @media (min-width: 600px)  { .tat-filter-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1024px) { .tat-filter-grid { grid-template-columns: repeat(6, 1fr); } }

        .tat-filter-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .7px;
            margin-bottom: 5px;
        }

        .tat-filter-select {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border2);
            border-radius: var(--r-sm);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            font-weight: 600;
            padding: 8px 28px 8px 10px;
            cursor: pointer;
            outline: none;
            transition: border-color .2s;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%238892aa' d='M5 6L0 0h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-color: var(--bg);
        }
        .tat-filter-select:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(35,85,245,.1); }

        .tat-filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
        }

        .tat-btn-primary {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--blue); color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px; font-weight: 700;
            padding: 9px 20px; border-radius: var(--r-sm);
            border: none; cursor: pointer;
            transition: opacity .2s, transform .1s;
        }
        .tat-btn-primary:hover { opacity: .88; }
        .tat-btn-primary:active { transform: scale(.97); }

        .tat-btn-secondary {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--white); color: var(--text2);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px; font-weight: 600;
            padding: 9px 20px; border-radius: var(--r-sm);
            border: 1px solid var(--border2); cursor: pointer;
            transition: background .2s;
        }
        .tat-btn-secondary:hover { background: var(--bg); }

        /* ═══════════════════════════════════════════
           KPI CARDS
        ═══════════════════════════════════════════ */
        .tat-kpi-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-bottom: 22px;
        }
        @media (min-width: 600px)  { .tat-kpi-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 1100px) { .tat-kpi-grid { grid-template-columns: repeat(5, 1fr); } }

        .tat-kpi-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 18px 20px;
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
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: var(--r) var(--r) 0 0;
        }
        .tat-kpi-card.c-blue::after   { background: var(--blue); }
        .tat-kpi-card.c-purple::after { background: var(--purple); }
        .tat-kpi-card.c-amber::after  { background: var(--amber); }
        .tat-kpi-card.c-red::after    { background: var(--red); }
        .tat-kpi-card.c-green::after  { background: var(--green); }

        .tat-kpi-inner { display: flex; align-items: flex-start; justify-content: space-between; }

        .tat-kpi-label {
            font-size: 12px; font-weight: 600;
            color: var(--muted);
            margin-bottom: 8px;
            text-transform: uppercase; letter-spacing: .4px;
        }
        .tat-kpi-value {
            font-size: 28px; font-weight: 800;
            line-height: 1; margin-bottom: 6px;
            font-family: 'JetBrains Mono', monospace;
        }
        .c-blue   .tat-kpi-value { color: var(--blue); }
        .c-purple .tat-kpi-value { color: var(--purple); }
        .c-amber  .tat-kpi-value { color: var(--amber); }
        .c-red    .tat-kpi-value { color: var(--red); }
        .c-green  .tat-kpi-value { color: var(--green); }

        .tat-kpi-sub { font-size: 11px; color: var(--muted); font-weight: 500; }

        .tat-kpi-icon {
            width: 42px; height: 42px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .c-blue   .tat-kpi-icon { background: var(--blue-s); }
        .c-purple .tat-kpi-icon { background: var(--purple-s); }
        .c-amber  .tat-kpi-icon { background: var(--amber-s); }
        .c-red    .tat-kpi-icon { background: var(--red-s); }
        .c-green  .tat-kpi-icon { background: var(--green-s); }

        /* ═══════════════════════════════════════════
           GENERIC CARD
        ═══════════════════════════════════════════ */
        .tat-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 20px 22px;
            box-shadow: var(--shadow);
        }

        .tat-card-title {
            font-size: 15px; font-weight: 700; color: var(--text);
            margin-bottom: 3px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .tat-card-sub {
            font-size: 11px; color: var(--muted); font-weight: 500;
            margin-bottom: 18px;
        }

        /* ═══════════════════════════════════════════
           ROW GRIDS
        ═══════════════════════════════════════════ */
        .tat-row { display: grid; gap: 16px; margin-bottom: 16px; }

        .tat-row-3  { grid-template-columns: 1fr; }
        .tat-row-2  { grid-template-columns: 1fr; }
        .tat-row-21 { grid-template-columns: 1fr; }

        @media (min-width: 768px) {
            .tat-row-2  { grid-template-columns: 1fr 1fr; }
            .tat-row-21 { grid-template-columns: 1.8fr 1fr; }
        }
        @media (min-width: 1100px) {
            .tat-row-3  { grid-template-columns: 1fr 1fr 1fr; }
        }

        /* ═══════════════════════════════════════════
           TREND + ZONE ROW
        ═══════════════════════════════════════════ */
        /* TAT Trend : spans full width on its own row */

        /* ═══════════════════════════════════════════
           ZONE WISE — PROGRESS BARS
        ═══════════════════════════════════════════ */
        .tat-zone-list { display: flex; flex-direction: column; gap: 18px; }

        .tat-zone-item {}
        .tat-zone-row {
            display: flex; justify-content: space-between;
            font-size: 12px; font-weight: 600; margin-bottom: 7px;
            color: var(--text2);
        }
        .tat-zone-pct { font-family: 'JetBrains Mono', monospace; }

        .tat-prog-bg {
            width: 100%; height: 10px;
            background: var(--bg);
            border-radius: 99px; overflow: hidden;
        }
        .tat-prog-fill {
            height: 10px; border-radius: 99px;
            transition: width 1.1s cubic-bezier(.34,1.56,.64,1);
        }

        /* ═══════════════════════════════════════════
           AGING BUCKET — PROGRESS
        ═══════════════════════════════════════════ */
        .tat-bucket-list { display: flex; flex-direction: column; gap: 13px; }

        .tat-bucket-row { display: flex; align-items: center; gap: 10px; }
        .tat-bucket-label {
            font-size: 11px; font-weight: 600; color: var(--muted);
            width: 72px; flex-shrink: 0;
        }
        .tat-bucket-bar-bg {
            flex: 1; height: 8px;
            background: var(--bg); border-radius: 99px; overflow: hidden;
        }
        .tat-bucket-bar-fill {
            height: 8px; border-radius: 99px;
            transition: width 1.2s cubic-bezier(.34,1.56,.64,1);
        }
        .tat-bucket-count {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px; font-weight: 600;
            min-width: 36px; text-align: right;
        }

        .tat-seg-tabs { display: flex; gap: 4px; margin-bottom: 14px; }
        .tat-seg-tab {
            padding: 5px 13px; border-radius: 100px;
            font-size: 11px; font-weight: 700;
            cursor: pointer; border: 1px solid var(--border2);
            color: var(--muted); background: transparent;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all .2s;
        }
        .tat-seg-tab.on { background: var(--blue); border-color: var(--blue); color: #fff; }

        /* ═══════════════════════════════════════════
           DAILY TAT BREAKDOWN — STACKED BARS
        ═══════════════════════════════════════════ */
        .tat-daily-legend {
            display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 18px;
        }
        .tat-daily-leg-item {
            display: flex; align-items: center; gap: 5px;
            font-size: 11px; font-weight: 600; color: var(--muted);
        }
        .tat-daily-leg-dot { width: 9px; height: 9px; border-radius: 50%; }

        .tat-daily-list { display: flex; flex-direction: column; gap: 14px; }
        .tat-daily-row { display: flex; align-items: center; gap: 12px; }
        .tat-daily-day { font-size: 12px; font-weight: 700; color: var(--text2); width: 34px; flex-shrink: 0; }
        .tat-daily-bars {
            flex: 1; height: 18px;
            border-radius: 99px; overflow: hidden;
            display: flex; background: var(--border);
        }
        .tat-daily-bar { height: 100%; }
        .tat-daily-total { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--muted); min-width: 32px; text-align: right; }

        /* ═══════════════════════════════════════════
           PRODUCT TAT TABLE
        ═══════════════════════════════════════════ */
        .tat-tbl { width: 100%; border-collapse: collapse; font-size: 12px; }
        .tat-tbl th {
            text-align: left; padding: 8px 10px;
            font-size: 10px; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: .6px;
            border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }
        .tat-tbl td {
            padding: 10px 10px; color: var(--text);
            border-bottom: 1px solid var(--border);
            font-weight: 500;
        }
        .tat-tbl tr:last-child td { border-bottom: none; }
        .tat-tbl tr:hover td { background: var(--bg); }

        .tat-pill {
            display: inline-flex; align-items: center;
            padding: 3px 9px; border-radius: 100px;
            font-size: 10px; font-weight: 700;
            letter-spacing: .3px;
        }
        .tat-pill-green  { background: var(--green-s);  color: var(--green); }
        .tat-pill-amber  { background: var(--amber-s);  color: var(--amber); }
        .tat-pill-red    { background: var(--red-s);    color: var(--red); }
        .tat-pill-blue   { background: var(--blue-s);   color: var(--blue); }

        /* ═══════════════════════════════════════════
           ALERTS PANEL
        ═══════════════════════════════════════════ */
        .tat-alert-list { display: flex; flex-direction: column; gap: 8px; }

        .tat-alert-item {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 10px 12px;
            border-radius: var(--r-sm); border: 1px solid var(--border);
        }
        .tat-alert-item.ai-red    { background: #fff8f8; border-color: rgba(232,52,74,.15); }
        .tat-alert-item.ai-amber  { background: #fffcf5; border-color: rgba(230,144,10,.15); }
        .tat-alert-item.ai-green  { background: #f5fdf9; border-color: rgba(10,170,110,.15); }

        .tat-alert-ico {
            width: 28px; height: 28px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; flex-shrink: 0;
        }
        .aico-r { background: var(--red-s);   }
        .aico-a { background: var(--amber-s); }
        .aico-g { background: var(--green-s); }

        .tat-alert-body { flex: 1; }
        .tat-alert-name { font-size: 12px; font-weight: 700; color: var(--text); }
        .tat-alert-meta { font-size: 10px; color: var(--muted); margin-top: 1px; }

        .tat-alert-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px; font-weight: 700;
            align-self: center; flex-shrink: 0;
        }
        .av-r { color: var(--red); }
        .av-a { color: var(--amber); }
        .av-g { color: var(--green); }

        /* ═══════════════════════════════════════════
           FOOTER
        ═══════════════════════════════════════════ */
        .tat-footer {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 0 4px;
            border-top: 1px solid var(--border);
            font-size: 11px; color: var(--muted);
            flex-wrap: wrap; gap: 8px;
            margin-top: 8px;
        }
        .tat-fleg { display: flex; gap: 12px; flex-wrap: wrap; }
        .tat-fleg-item { display: flex; align-items: center; gap: 5px; font-weight: 600; }
        .tat-fleg-dot  { width: 8px; height: 8px; border-radius: 50%; }

        /* ═══════════════════════════════════════════
           ANIMATIONS
        ═══════════════════════════════════════════ */
        @keyframes tatFadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .tat-anim { animation: tatFadeUp .45s ease both; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row content">
        <div id="hide_nav_">
            <?php include("../includes/leftnav2.php"); ?>
        </div>

        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
            <div class="tat-wrap">

                <!-- ── PAGE HEADER ── -->
                <div class="tat-header tat-anim">
                    <div class="tat-header-left">
                        <div class="tat-icon-box">
                            <i class="fa fa-tachometer"></i>
                        </div>
                        <div>
                            <div class="tat-header-title">TAT Command Center</div>
                            <div class="tat-header-sub">Turn Around Time — Real-Time Performance Monitor</div>
                        </div>
                    </div>
                    <div class="tat-live-pill">
                        <div class="tat-live-dot"></div> Live
                    </div>
                </div>

                <!-- ── FILTER BAR ── -->
                <form id="dashboard_form" method="post">
                    <div class="tat-filter-bar tat-anim" style="animation-delay:.05s">
                        <div class="tat-filter-grid">
                            <div>
                                <label class="tat-filter-label">Date Range</label>
                                <select class="tat-filter-select" id="date_range">
                                    <option value="">Select Range</option>
                                    <option value="7">Last 7 Days</option>
                                    <option value="30">Last 30 Days</option>
                                    <option value="90">Last 90 Days</option>
                                </select>
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
                        <div class="tat-filter-actions">
                            <button type="button" class="tat-btn-primary" id="submit_button">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                Apply Filters
                            </button>
                            <button type="button" class="tat-btn-secondary" id="reset_button">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Reset
                            </button>
                        </div>
                    </div>
                </form>

                <!-- ── KPI CARDS ── -->
                <div class="tat-kpi-grid">
                    <div class="tat-kpi-card c-blue tat-anim" style="animation-delay:.08s">
                        <div class="tat-kpi-inner">
                            <div>
                                <div class="tat-kpi-label">Total Jobs</div>
                                <div class="tat-kpi-value" id="kpi_total">—</div>
                                <div class="tat-kpi-sub">This period</div>
                            </div>
                            <div class="tat-kpi-icon">📋</div>
                        </div>
                    </div>
                    <div class="tat-kpi-card c-purple tat-anim" style="animation-delay:.12s">
                        <div class="tat-kpi-inner">
                            <div>
                                <div class="tat-kpi-label">Avg TAT</div>
                                <div class="tat-kpi-value" id="kpi_avg_tat">—</div>
                                <div class="tat-kpi-sub">Average TAT days</div>
                            </div>
                            <div class="tat-kpi-icon">📊</div>
                        </div>
                    </div>
                    <div class="tat-kpi-card c-amber tat-anim" style="animation-delay:.16s">
                        <div class="tat-kpi-inner">
                            <div>
                                <div class="tat-kpi-label">Within TAT</div>
                                <div class="tat-kpi-value" id="kpi_within">—</div>
                                <div class="tat-kpi-sub" id="kpi_within_pct">—</div>
                            </div>
                            <div class="tat-kpi-icon">✅</div>
                        </div>
                    </div>
                    <div class="tat-kpi-card c-red tat-anim" style="animation-delay:.2s">
                        <div class="tat-kpi-inner">
                            <div>
                                <div class="tat-kpi-label">SLA Breached</div>
                                <div class="tat-kpi-value" id="kpi_breached">—</div>
                                <div class="tat-kpi-sub" id="kpi_breached_pct">—</div>
                            </div>
                            <div class="tat-kpi-icon">🚩</div>
                        </div>
                    </div>
                    <div class="tat-kpi-card c-green tat-anim" style="animation-delay:.24s">
                        <div class="tat-kpi-inner">
                            <div>
                                <div class="tat-kpi-label">At Risk</div>
                                <div class="tat-kpi-value" id="kpi_risk">—</div>
                                <div class="tat-kpi-sub" id="kpi_risk_pct">—</div>
                            </div>
                            <div class="tat-kpi-icon">⚠️</div>
                        </div>
                    </div>
                </div>

                <!-- ── ROW 1: TAT Trend (full width) ── -->
                <div class="tat-row" style="margin-bottom:16px;">
                    <div class="tat-card tat-anim" style="animation-delay:.28s">
                        <div class="tat-card-title">TAT Trend Analysis</div>
                        <div class="tat-card-sub">Daily average TAT hours over selected period</div>
                        <div id="tatTrendChart" style="height:260px;"></div>
                    </div>
                </div>

                <!-- ── ROW 2: TAT Status Split + Calls by Aging Bucket ── -->
                <div class="tat-row tat-row-2">
                    <!-- Donut — TAT Status Split -->
                    <div class="tat-card tat-anim" style="animation-delay:.32s">
                        <div class="tat-card-title">TAT Status Split</div>
                        <div class="tat-card-sub">Jobs within / at risk / breached SLA</div>
                        <div id="tatDonutChart" style="height:280px;"></div>
                    </div>

                    <!-- Aging Bucket -->
                    <div class="tat-card tat-anim" style="animation-delay:.36s">
                        <div class="tat-card-title">
                            Calls by Aging Bucket
                            <span style="font-size:11px;color:var(--muted)">ℹ</span>
                        </div>
                        <div class="tat-card-sub">TAT distribution across time ranges</div>
                        <div class="tat-seg-tabs">
                            <button class="tat-seg-tab on" onclick="switchBucket(this,'count')">Count</button>
                            <button class="tat-seg-tab" onclick="switchBucket(this,'pct')">% Share</button>
                        </div>
                        <div class="tat-bucket-list" id="tatBucketList"></div>
                    </div>
                </div>

                <!-- ── ROW 3: Daily TAT Breakdown ── -->
                <div class="tat-row" style="margin-bottom:16px;">
                    <div class="tat-card tat-anim" style="animation-delay:.4s">
                        <div class="tat-card-title">
                            Daily TAT Breakdown (Last 7 Days)
                            <div class="tat-daily-legend">
                                <div class="tat-daily-leg-item"><div class="tat-daily-leg-dot" style="background:var(--green)"></div>Within TAT</div>
                                <div class="tat-daily-leg-item"><div class="tat-daily-leg-dot" style="background:var(--amber)"></div>At Risk</div>
                                <div class="tat-daily-leg-item"><div class="tat-daily-leg-dot" style="background:var(--red)"></div>Breached</div>
                            </div>
                        </div>
                        <div class="tat-card-sub">Job count per day split by TAT compliance</div>
                        <div class="tat-daily-list" id="tatDailyList"></div>
                    </div>
                </div>

                <!-- ── ROW 4: Product TAT Table + Zone Wise TAT + Alerts ── -->
                <div class="tat-row tat-row-3">

                    <!-- Avg TAT by Product -->
                    <div class="tat-card tat-anim" style="animation-delay:.44s">
                        <div class="tat-card-title">Avg TAT by Product</div>
                        <div class="tat-card-sub">Product-wise turnaround comparison</div>
                        <table class="tat-tbl">
                            <thead>
                            <tr>
                                <th>Product</th>
                                <th>Avg TAT</th>
                                <th>Min</th>
                                <th>Max</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody id="tatProductBody"></tbody>
                        </table>
                    </div>

                    <!-- Zone Wise TAT -->
                    <div class="tat-card tat-anim" style="animation-delay:.48s">
                        <div class="tat-card-title">Zone Wise TAT</div>
                        <div class="tat-card-sub">Avg TAT breach % per zone</div>
                        <div class="tat-zone-list" id="tatZoneList"></div>
                    </div>

                    <!-- Breach Alerts -->
                    <div class="tat-card tat-anim" style="animation-delay:.52s">
                        <div class="tat-card-title">
                            Breach Alerts
                            <span class="tat-pill tat-pill-red" id="tatAlertBadge">0 NEW</span>
                        </div>
                        <div class="tat-card-sub">Jobs requiring immediate attention</div>
                        <div class="tat-alert-list" id="tatAlertList"></div>
                    </div>

                </div>

                <!-- ── FOOTER ── -->
                <div class="tat-footer">
                    <div class="tat-fleg">
                        <div class="tat-fleg-item"><div class="tat-fleg-dot" style="background:var(--green)"></div>Within TAT (&lt;24 HRS)</div>
                        <div class="tat-fleg-item"><div class="tat-fleg-dot" style="background:var(--amber)"></div>At Risk (24–72 HRS)</div>
                        <div class="tat-fleg-item"><div class="tat-fleg-dot" style="background:var(--red)"></div>Breached (&gt;72 HRS)</div>
                    </div>
                    <div>Updated: <span id="tatTs">—</span></div>
                </div>

            </div><!-- /tat-wrap -->
        </div>
    </div>
</div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>

<!-- ══════════════════════════════════════════
     JAVASCRIPT — Sample data + render
══════════════════════════════════════════ -->
<script>
    /* ─── SAMPLE DATA (replace with API response) ─── */
    const TAT = {
        kpi: {
            total: '2,847', avg_tat: '15.15d',
            within: '1,593', within_pct: '55.9% calls',
            breached: '412',  breached_pct: '14.5% of jobs',
            risk: '842',      risk_pct: '29.6% of jobs',
        },
        trend: {
            days: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            vals: [42, 38, 55, 34, 29, 62, 38],
            target: 24
        },
        donut: [
            { name:'Within TAT', y:1593, color:'#0aaa6e' },
            { name:'At Risk',    y:842,  color:'#e6900a' },
            { name:'Breached',   y:412,  color:'#e8344a' },
        ],
        bucket: [
            { label:'< 24 HRS',   count:1593, color:'#0aaa6e' },
            { label:'24–48 HRS',  count:478,  color:'#2355f5' },
            { label:'48–72 HRS',  count:364,  color:'#7c3aed' },
            { label:'3–5 Days',   count:312,  color:'#e6900a' },
            { label:'6–10 Days',  count:186,  color:'#fb923c' },
            { label:'11–15 Days', count:98,   color:'#e8344a' },
            { label:'> 15 Days',  count:84,   color:'#b91c1c' },
        ],
        daily: [
            { day:'Mon', ok:238, risk:124, breach:62 },
            { day:'Tue', ok:202, risk:98,  breach:44 },
            { day:'Wed', ok:287, risk:132, breach:58 },
            { day:'Thu', ok:198, risk:116, breach:72 },
            { day:'Fri', ok:312, risk:108, breach:54 },
            { day:'Sat', ok:178, risk:88,  breach:38 },
            { day:'Sun', ok:178, risk:176, breach:84 },
        ],
        product: [
            { product:'Battery',      avg:'38.4 HRS', min:'4 HRS',  max:'127 Days', status:'Breached' },
            { product:'Inverter',     avg:'22.1 HRS', min:'2 HRS',  max:'72 HRS',   status:'Within TAT' },
            { product:'Solar Product',avg:'31.8 HRS', min:'6 HRS',  max:'18 Days',  status:'At Risk' },
        ],
        zone: [
            { name:'North Zone', pct:90, color:'#e8344a' },
            { name:'South Zone', pct:65, color:'#e6900a' },
            { name:'East Zone',  pct:45, color:'#2355f5' },
            { name:'West Zone',  pct:28, color:'#0aaa6e' },
        ],
        alerts: [
            { ico:'🚨', cls:'aico-r', item:'ai-red',   name:'JB-00421 — Battery',       meta:'UP · 127 days · Critical', val:'127d', vcls:'av-r' },
            { ico:'⚠️', cls:'aico-a', item:'ai-amber', name:'JB-00410 — Inverter',       meta:'Bihar · 96 HRS overdue',  val:'96h',  vcls:'av-a' },
            { ico:'⚠️', cls:'aico-a', item:'ai-amber', name:'JB-00415 — Solar Product',  meta:'Rajasthan · 42 HRS',      val:'42h',  vcls:'av-a' },
            { ico:'✅', cls:'aico-g', item:'ai-green', name:'JB-00399 — Battery',        meta:'Tamil Nadu · 2 HRS ✓',    val:'2h',   vcls:'av-g' },
        ],
    };

    /* ─── KPI ─── */
    function renderKPI(d) {
        document.getElementById('kpi_total').textContent       = d.total;
        document.getElementById('kpi_avg_tat').textContent     = d.avg_tat;
        document.getElementById('kpi_within').textContent      = d.within;
        document.getElementById('kpi_within_pct').textContent  = d.within_pct;
        document.getElementById('kpi_breached').textContent    = d.breached;
        document.getElementById('kpi_breached_pct').textContent= d.breached_pct;
        document.getElementById('kpi_risk').textContent        = d.risk;
        document.getElementById('kpi_risk_pct').textContent    = d.risk_pct;
    }

    /* ─── HIGHCHARTS AREA — TREND ─── */
    function renderTrend(d) {
        Highcharts.chart('tatTrendChart', {
            chart: {
                type: 'area',
                backgroundColor: 'transparent',
                margin: [20, 20, 40, 48],
                style: { fontFamily: "'Plus Jakarta Sans', sans-serif" }
            },
            title: { text: '' },
            credits: { enabled: false },
            xAxis: {
                categories: d.days,
                lineColor: '#e8ecf4', tickColor: '#e8ecf4',
                labels: { style: { fontSize:'11px', color:'#8892aa', fontWeight:'600' } }
            },
            yAxis: {
                title: { text: 'Avg TAT (hrs)', style: { fontSize:'10px', color:'#8892aa' } },
                labels: { style: { fontSize:'10px', color:'#8892aa' } },
                gridLineColor: '#f0f2f8',
                plotLines: [{
                    value: d.target,
                    color: '#e6900a', dashStyle: 'ShortDash', width: 2,
                    label: { text: 'Target 24h', style: { color:'#e6900a', fontSize:'10px', fontWeight:'700' }, align: 'right', x: -4 }
                }]
            },
            tooltip: {
                borderRadius: 10, shadow: false,
                pointFormat: '<b style="color:{series.color}">{point.y}h</b> avg TAT'
            },
            legend: { enabled: false },
            plotOptions: {
                area: {
                    fillOpacity: 0.12,
                    marker: { radius: 5, symbol: 'circle', lineWidth: 2, lineColor: '#fff', fillColor: '#2355f5' },
                    dataLabels: {
                        enabled: true,
                        format: '{y}h',
                        style: { fontSize:'10px', fontWeight:'700', color:'#2355f5', textOutline:'none' },
                        y: -8
                    }
                }
            },
            series: [{
                name: 'Avg TAT',
                data: d.vals,
                color: '#2355f5',
                fillColor: { linearGradient:{x1:0,y1:0,x2:0,y2:1}, stops:[[0,'rgba(35,85,245,.18)'],[1,'rgba(35,85,245,.01)']] }
            }]
        });
    }

    /* ─── HIGHCHARTS DONUT ─── */
    function renderDonut(d) {
        const total = d.reduce((s,x) => s+x.y, 0);
        Highcharts.chart('tatDonutChart', {
            chart: {
                type: 'pie',
                backgroundColor: 'transparent',
                style: { fontFamily: "'Plus Jakarta Sans', sans-serif" }
            },
            title: { text: '' },
            credits: { enabled: false },
            tooltip: {
                borderRadius: 10, shadow: false,
                pointFormat: '<b>{point.y}</b> jobs<br>({point.percentage:.1f}%)'
            },
            plotOptions: {
                pie: {
                    innerSize: '65%',
                    borderRadius: 4,
                    borderWidth: 2,
                    borderColor: '#fff',
                    dataLabels: { enabled: false },
                    showInLegend: true
                }
            },
            legend: {
                align: 'right', verticalAlign: 'middle', layout: 'vertical',
                itemStyle: { fontSize:'12px', fontWeight:'600', color:'#3d4966' },
                labelFormatter: function () {
                    return `${this.name}<br><span style="color:#8892aa;font-size:11px;font-weight:400">${this.y.toLocaleString()} (${(this.percentage).toFixed(1)}%)</span>`;
                }
            },
            series: [{
                name: 'Jobs',
                colorByPoint: false,
                data: d.map(x => ({ name: x.name, y: x.y, color: x.color }))
            }]
        });
    }

    /* ─── BUCKET BARS ─── */
    let bucketMode = 'count';
    function renderBuckets() {
        const total = TAT.bucket.reduce((s,b) => s+b.count, 0);
        const maxC  = Math.max(...TAT.bucket.map(b => b.count));
        document.getElementById('tatBucketList').innerHTML = TAT.bucket.map(b => {
            const w = bucketMode === 'count'
                ? (b.count/maxC*100).toFixed(1)
                : (b.count/total*100).toFixed(1);
            const lbl = bucketMode === 'count'
                ? b.count.toLocaleString()
                : (b.count/total*100).toFixed(1)+'%';
            return `<div class="tat-bucket-row">
      <div class="tat-bucket-label">${b.label}</div>
      <div class="tat-bucket-bar-bg">
        <div class="tat-bucket-bar-fill" style="width:${w}%;background:${b.color}"></div>
      </div>
      <div class="tat-bucket-count" style="color:${b.color}">${lbl}</div>
    </div>`;
        }).join('');
    }
    function switchBucket(btn, mode) {
        bucketMode = mode;
        document.querySelectorAll('.tat-seg-tab').forEach(t => t.classList.remove('on'));
        btn.classList.add('on');
        renderBuckets();
    }

    /* ─── DAILY BARS ─── */
    function renderDaily(d) {
        document.getElementById('tatDailyList').innerHTML = d.map(row => {
            const total = row.ok + row.risk + row.breach;
            const wOk    = (row.ok    /total*100).toFixed(1);
            const wRisk  = (row.risk  /total*100).toFixed(1);
            const wBreach= (row.breach/total*100).toFixed(1);
            return `<div class="tat-daily-row">
      <div class="tat-daily-day">${row.day}</div>
      <div class="tat-daily-bars">
        <div class="tat-daily-bar" style="width:${wOk}%;background:var(--green)" title="Within TAT: ${row.ok}"></div>
        <div class="tat-daily-bar" style="width:${wRisk}%;background:var(--amber)" title="At Risk: ${row.risk}"></div>
        <div class="tat-daily-bar" style="width:${wBreach}%;background:var(--red)" title="Breached: ${row.breach}"></div>
      </div>
      <div class="tat-daily-total">${total}</div>
    </div>`;
        }).join('');
    }

    /* ─── PRODUCT TABLE ─── */
    function renderProduct(d) {
        document.getElementById('tatProductBody').innerHTML = d.map(p => {
            const pc = p.status==='Within TAT'?'tat-pill-green':p.status==='At Risk'?'tat-pill-amber':'tat-pill-red';
            const vc = p.status==='Within TAT'?'color:var(--green)':p.status==='At Risk'?'color:var(--amber)':'color:var(--red)';
            return `<tr>
      <td style="font-weight:700">${p.product}</td>
      <td><span style="${vc};font-family:'JetBrains Mono',monospace;font-weight:600">${p.avg}</span></td>
      <td style="color:var(--muted)">${p.min}</td>
      <td style="color:var(--muted)">${p.max}</td>
      <td><span class="tat-pill ${pc}">${p.status}</span></td>
    </tr>`;
        }).join('');
    }

    /* ─── ZONE BARS ─── */
    function renderZone(d) {
        document.getElementById('tatZoneList').innerHTML = d.map(z => `
    <div class="tat-zone-item">
      <div class="tat-zone-row">
        <span>${z.name}</span>
        <span class="tat-zone-pct" style="color:${z.color}">${z.pct}%</span>
      </div>
      <div class="tat-prog-bg">
        <div class="tat-prog-fill" style="width:${z.pct}%;background:${z.color}"></div>
      </div>
    </div>`).join('');
    }

    /* ─── ALERTS ─── */
    function renderAlerts(d) {
        const cnt = d.filter(a => a.cls==='aico-r'||a.cls==='aico-a').length;
        document.getElementById('tatAlertBadge').textContent = cnt + ' ALERTS';
        document.getElementById('tatAlertList').innerHTML = d.map(a => `
    <div class="tat-alert-item ${a.item}">
      <div class="tat-alert-ico ${a.cls}">${a.ico}</div>
      <div class="tat-alert-body">
        <div class="tat-alert-name">${a.name}</div>
        <div class="tat-alert-meta">${a.meta}</div>
      </div>
      <div class="tat-alert-val ${a.vcls}">${a.val}</div>
    </div>`).join('');
    }

    /* ─── TIMESTAMP ─── */
    function setTs() {
        const n = new Date();
        document.getElementById('tatTs').textContent =
            n.toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'}) + ' ' +
            n.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'});
    }

    /* ─── INIT ─── */
    Highcharts.setOptions({
        chart: { style: { fontFamily:"'Plus Jakarta Sans',sans-serif" } },
        credits: { enabled: false },
        title: { text: '' },
        tooltip: { borderRadius: 10, shadow: false }
    });

    renderKPI(TAT.kpi);
    renderTrend(TAT.trend);
    renderDonut(TAT.donut);
    renderBuckets();
    renderDaily(TAT.daily);
    renderProduct(TAT.product);
    renderZone(TAT.zone);
    renderAlerts(TAT.alerts);
    setTs();

    /* ─── FORM SUBMIT placeholder ─── */
    document.getElementById('submit_button').addEventListener('click', function(e){
        e.preventDefault();
        /* TODO: fetch data from server and call render functions with real data */
    });
    document.getElementById('reset_button').addEventListener('click', function(e){
        e.preventDefault();
        document.getElementById('dashboard_form').reset();
    });

</script>
</body>
</html>