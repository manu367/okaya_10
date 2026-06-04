<?php
require_once("../includes/config.php");

$pagination='dashboard-pendingcall-data-grid.php';

/*
 *  Workflow :
 *   1.by default when page load the fetch latet data from db and set in input and chart (latest data)
 *   2. when used wanted to change charts and more data then provide the customization from <form>
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <script src="../js/highcharts.js"> </script>
    <script src="../js/accessibility.js"></script>
    <script src="../js/jquery.min.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script src="../js/frmvalidate.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/common_js.js"></script>
    <style>
        /* ===== Modal Table Wrapper ===== */
        .table-responsive-custom{
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
        }

        /* ===== Table Design ===== */
        #_modal_table_{
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            table-layout: fixed;
            min-width: 850px;
            font-family: Arial, sans-serif;
        }

        /* ===== Header ===== */
        #_modal_table_ thead tr{
            background: #f5f7fb;
        }

        #_modal_table_ th{
            padding: 14px 12px;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        /* ===== Table Cells ===== */
        #_modal_table_ td{
            padding: 14px 12px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            text-align: center;
            vertical-align: middle;
        }

        /* ===== Hover Row ===== */
        #_modal_table_ tbody tr:hover{
            background: #fafafa;
            transition: 0.2s;
        }

        /* ===== Text Ellipsis ===== */
        .table-text{
            display: inline-block;
            max-width: 110px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            vertical-align: middle;
            cursor: pointer;
        }

        /* ===== Bold Modal Name ===== */
        .td-bold{
            font-weight: 700;
            color: #111827;
        }

        /* ===== View Button ===== */
        .btn-view{
            display: inline-block;
            padding: 10px 22px;
            background: #2563eb;
            color: #fff;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .btn-view:hover{
            background: #1d4ed8;
        }

        /* ===== Mobile Responsive ===== */
        @media(max-width:768px){

            #_modal_table_{
                min-width: 700px;
            }

            #_modal_table_ th,
            #_modal_table_ td{
                padding: 12px 10px;
                font-size: 13px;
            }

            .btn-view{
                padding: 8px 16px;
                font-size: 13px;
            }

            .table-text{
                max-width: 80px;
            }
        }

    </style>
    <style>
        /* Card */
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }

        /* Stat icon */
        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Filter select */
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
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
        }
        .filter-select:focus {
            box-shadow: 0 0 0 2px #bfdbfe;
            border-color: #93c5fd;
        }
        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-green  { background: #dcfce7; color: #16a34a; }
        .badge-orange { background: #fff7ed; color: #ea580c; }
        .badge-red    { background: #fef2f2; color: #dc2626; }
        .badge-blue   { background: #eff6ff; color: #2563eb; }

        /* ── HEADER ── */
        .header {
            padding: 12px 16px;
            margin-bottom: 16px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .header-left  { display: flex; align-items: center; gap: 12px; }
        .header-right { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6b7280; }

        .header-title    { font-size: 18px; font-weight: 700; color: #111827; }
        .header-subtitle { font-size: 12px; color: #6b7280; }

        .icon-btn {
            background: none; border: none; cursor: pointer;
            color: #6b7280; display: flex; align-items: center;
        }
        .icon-btn:hover { color: #374151; }
        .icon-btn-blue { color: #3b82f6; }
        .icon-btn-blue:hover { color: #1d4ed8; }

        .last-updated-label { color: #3b82f6; font-weight: 500; }

        /* ── FILTERS ── */
        .filters {
            padding: 12px 16px;
            margin-bottom: 16px;
        }
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
            font-family: 'DM Sans', sans-serif;
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
            font-family: 'DM Sans', sans-serif;
            white-space: nowrap;
        }
        .btn-secondary:hover { background: #f9fafb; }

        .filter-btn-group {
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        /* ── KPI STATS ── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }
        .stat-card {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-label  { font-size: 12px; color: #6b7280; margin-bottom: 4px; }
        .stat-value  { font-size: 24px; font-weight: 700; }
        .stat-sub    { font-size: 12px; color: #9ca3af; margin-top: 2px; }

        .text-blue   { color: #2563eb; }
        .text-orange { color: #f97316; }
        .text-purple { color: #a855f7; }
        .text-amber  { color: #f59e0b; }
        .text-red    { color: #ef4444; }
        .text-gray   { color: #374151; }

        .bg-blue-50   { background: #eff6ff; }
        .bg-orange-50 { background: #fff7ed; }
        .bg-purple-50 { background: #faf5ff; }
        .bg-amber-50  { background: #fffbeb; }
        .bg-red-50    { background: #fef2f2; }

        .icon-blue   { color: #3b82f6; }
        .icon-orange { color: #fb923c; }
        .icon-purple { color: #c084fc; }
        .icon-amber  { color: #fbbf24; }
        .icon-red    { color: #f87171; }

        .col-span-2 { grid-column: span 2; }

        /* ── CHARTS ROW ── */
        .charts-row1 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }
        .chart-card { padding: 16px; }
        .chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .chart-header-left { display: flex; align-items: center; gap: 8px; }
        .chart-title  { font-size: 14px; font-weight: 600; color: #1f2937; }
        .chart-info   { color: #9ca3af; font-size: 12px; cursor: pointer; }
        .chart-select { font-size: 12px; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 8px; color: #4b5563; outline: none; background: #fff; font-family: 'DM Sans', sans-serif; }

        /* ── BOTTOM SECTION ── */
        .bottom-section {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        /* Aging Snapshot Table */
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; font-size: 12px; border-collapse: collapse; }
        thead tr { background: #f9fafb; color: #6b7280; }
        th {
            text-align: left;
            padding: 8px;
            font-weight: 500;
        }
        th:first-child { border-radius: 6px 0 0 6px; }
        th:last-child  { border-radius: 0 6px 6px 0; }
        th.text-center, td.text-center { text-align: center; }

        tbody tr { border-bottom: 1px solid #f9fafb; }
        tbody tr:hover { background: #f8fafc; }
        td { padding: 8px; color: #374151; }

        .td-bold { font-weight: 600; color: #1f2937; }
        .tfoot-row { background: #f9fafb; }
        .tfoot-row td { font-weight: 600; color: #1f2937; }

        /* Status section */
        .status-card { padding: 16px; }
        .status-list { display: flex; flex-direction: column; gap: 12px; }
        .status-item { display: flex; align-items: center; gap: 12px; }

        .status-icon {
            width: 28px; height: 28px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .si-blue   { background: #dbeafe; color: #3b82f6; }
        .si-orange { background: #ffedd5; color: #f97316; }
        .si-yellow { background: #fef9c3; color: #eab308; }
        .si-gray   { background: #f3f4f6; color: #6b7280; }
        .si-green  { background: #dcfce7; color: #22c55e; }

        .status-detail { flex: 1; }
        .status-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 4px;
        }
        .status-name  { color: #4b5563; }
        .status-count { font-weight: 600; color: #1f2937; }
        .status-pct   { color: #9ca3af; font-weight: 400; }

        .progress-bg   { width: 100%; background: #f3f4f6; border-radius: 9999px; height: 8px; }
        .progress-fill { height: 8px; border-radius: 9999px; }
        .fill-blue   { background: #3b82f6; }
        .fill-orange { background: #f97316; }
        .fill-yellow { background: #facc15; }
        .fill-gray   { background: #9ca3af; }
        .fill-green  { background: #22c55e; }

        .status-total-row {
            padding-top: 8px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }
        .status-total-label { font-weight: 600; color: #1f2937; }
        .status-total-val   { font-weight: 700; color: #1f2937; }

        /* Info cards grid */
        .info-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }
        .info-card {
            padding: 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .info-label  { font-size: 12px; color: #6b7280; font-weight: 500; margin-bottom: 8px; }
        .info-value-lg { font-size: 24px; font-weight: 700; margin-bottom: 4px; }
        .info-value-md { font-size: 18px; font-weight: 700; line-height: 1.3; margin-bottom: 4px; }
        .info-sub    { font-size: 12px; color: #6b7280; }
        .info-sub-bold { font-weight: 500; color: #374151; }

        .sla-card {
            border: 1px solid #fee2e2;
            background: rgba(254,242,242,0.3);
        }

        /* Footer */
        .footer-note {
            font-size: 12px;
            color: #9ca3af;
            padding: 0 4px 16px;
        }

        /* ── RESPONSIVE ── */
        @media (min-width: 640px) {

            .header {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }

            .filters-grid { grid-template-columns: repeat(3, 1fr); }
            .filters-row2 { flex-direction: row; }

            .stat-grid { grid-template-columns: repeat(3, 1fr); }
            .col-span-sm-1 { grid-column: span 1; }
        }

        @media (min-width: 1024px) {
            .filters-grid { grid-template-columns: repeat(6, 1fr); }

            .stat-grid { grid-template-columns: repeat(5, 1fr); }

            .charts-row1 { grid-template-columns: repeat(3, 1fr); }

            .bottom-section { grid-template-columns: repeat(3, 1fr); }
        }

        svg { display: block; }
    </style>
    <style>
        .dashboard-loader{
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(3px);
            z-index: 99999;

            display: none;
            align-items: center;
            justify-content: center;
        }

        .dashboard-loader.active{
            display: flex;
        }

        .loader-box{
            background: white;
            padding: 30px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            text-align: center;
            min-width: 220px;
        }

        .spinner{
            width: 55px;
            height: 55px;
            border: 5px solid #e5e7eb;
            border-top-color: #2563eb;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            margin: auto;
        }

        .loader-box p{
            margin-top: 16px;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        @keyframes spin{
            to{
                transform: rotate(360deg);
            }
        }
    </style>
    <style>
        .custom-alert{
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 280px;
            padding: 14px 18px;
            border-radius: 10px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            z-index: 999999;
            transform: translateX(120%);
            transition: 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .custom-alert.show{
            transform: translateX(0);
        }
    </style>
    <style>
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0,0,0,0.7);
            justify-content: center;
            align-items: flex-start;      /* center ki jagah flex-start */
            overflow-y: auto;             /* modal itself scroll karega */
            z-index: 999;
            box-sizing: border-box;
            padding: 30px 16px;           /* upar neeche breathing room */
        }
        /* Modal box */
        .modal-content {
            width: 90%;
            max-width: 1100px;
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            box-sizing: border-box;
            overflow: hidden;             /* andar overflow nahi hoga */
        }

        .modal-content h2,
        .modal-content h3 {
            margin-top: 0;
            font-size: 22px;
            margin-bottom: 16px;
        }

        /* DataTables modal fix */
        .modal-content .dataTables_wrapper {
            padding: 0;
        }
        .modal-content .dataTables_filter {
            text-align: right;
            margin-bottom: 10px;
        }
        .modal-content .dataTables_filter input {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 13px;
            outline: none;
            margin-left: 6px;
        }
        .modal-content .dataTables_info {
            font-size: 12px;
            color: #6b7280;
            padding-top: 8px;
        }
        .modal-content .dataTables_paginate {
            padding-top: 8px;
        }
        .modal-content .dataTables_paginate .paginate_button {
            padding: 4px 10px;
            border-radius: 6px;
            border: 1px solid #e5e7eb !important;
            background: #fff !important;
            font-size: 13px;
            color: #374151 !important;
            margin: 0 2px;
            cursor: pointer;
        }
        .modal-content .dataTables_paginate .paginate_button.current {
            background: #2563eb !important;
            color: #fff !important;
            border-color: #2563eb !important;
            font-weight: 600;
        }
        .modal-content .dataTables_paginate .paginate_button:hover:not(.current) {
            background: #eff6ff !important;
            color: #2563eb !important;
            border-color: #93c5fd !important;
        }
        .modal-content .dataTables_paginate .paginate_button.disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        .dt-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            flex-wrap: wrap;
            gap: 8px;
        }
        /* Table wrapper — yahi asli fix hai */
        .table-responsive-custom {
            width: 100%;
            overflow-x: auto;
            border-radius: 12px;
        }

        /* DataTables wrapper fix */
        .modal-content .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }

        /* Pagination bottom row */
        .modal-content .dt-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
            flex-wrap: wrap;
            gap: 8px;
        }
    </style>
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
</head>
<body>

<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
            <!-- Header -->
            <div class="card header" style="display: none;">
                <div style="display: none" class="header-left">
                    <button class="icon-btn">
                        <svg width="20" height="20" fill="none" style="display: none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h1 class="header-title">Dashboard</h1>
                        <p class="header-subtitle">Real-time overview</p>
                    </div>
                </div>
                <div class="header-right" style="visibility: hidden">
                    <button class="icon-btn icon-btn-blue">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                </div>
            </div>

            <!-- Filters -->
            <form style="margin-top: 20px;" id="dashboard_form">
                <div class="card filters">
                    <div class="filters-grid">
                        <div>
                            <label class="filter-label">Date Range</label>
                            <select class="filter-select" id="date_range">
                                <option>Last 7 Days</option>
                                <option>Last 30 Days</option>
                                <option>Last 90 Days</option>
                            </select>
                        </div>
                        <div>
                            <label class="filter-label">Zone</label>
                            <select class="filter-select" id="zone_filter">
                                <option value="">All Zones</option>
                            </select>
                        </div>
                        <div>
                            <label class="filter-label">State</label>
                            <select class="filter-select" id="state_filter">
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div>
                            <label class="filter-label">BSI</label>
                            <select class="filter-select" id="bsi_filter">
                                <option value="">All BSIs</option>
                            </select>
                        </div>
                        <div>
                            <label class="filter-label">Engineer Type</label>
                            <select class="filter-select" id="engineer-type">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div>
                            <label class="filter-label">Product</label>
                            <select class="filter-select" id="product_filter">
                                <option value="">All Products</option>
                                <option value="2">Battery</option>
                                <option value="1">Inverter</option>
                                <option value="3">Solar Product</option>
                            </select>
                        </div>
                    </div>
                    <div class="filters-row2">
                        <div class="filter-item" style="display: none">
                            <label class="filter-label">Aging Bucket</label>
                            <select class="filter-select" id="bucket_filter">
                                <option value="">All Buckets</option>
                                <option value="">0-2 Days</option>
                                <option value="">3-5 Days</option>
                            </select>
                        </div>
                        <div class="filter-item" style="display: none">
                            <label class="filter-label">Status</label>
                            <select class="filter-select" id="status_filter">
                                <option value="">Pending</option>
                            </select>
                        </div>
                        <div class="filter-btn-group">
                            <button class="btn-primary" id="submit_button">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                                Apply Filters
                            </button>
                            <button class="btn-secondary" id="reset_button">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div id="dashboard_m" class="hidden">
                <!-- KPI Stats -->
                <div class="stat-grid">
                    <!-- Total Pending -->
                    <div class="card stat-card">
                        <div>
                            <p class="stat-label">Total Pending Calls</p>
                            <p class="stat-value text-blue mono" id="total_pending_calls">1,248</p>
                            <p class="stat-sub">All pending calls</p>
                        </div>
                        <div class="stat-icon bg-blue-50">
                            <svg width="20" height="20" class="icon-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                    </div>
                    <!-- Avg Aging -->
                    <div class="card stat-card">
                        <div>
                            <p class="stat-label">Avg Aging (Days)</p>
                            <p class="stat-value text-purple mono" id="avg_aging">23</p>
                            <p class="stat-sub">Average pending days</p>
                        </div>
                        <div class="stat-icon bg-purple-50">
                            <svg width="20" height="20" class="icon-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                    </div>
                    <!-- >2 Days -->
                    <div class="card stat-card">
                        <div>
                            <p class="stat-label">&gt; 2 Days Pending</p>
                            <p class="stat-value text-amber mono" id="days_pending">986</p>
                            <p class="stat-sub">79.0% of total</p>
                        </div>
                        <div class="stat-icon bg-amber-50">
                            <svg width="20" height="20" class="icon-amber" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                    </div>
                    <!-- High Priority -->
                    <div class="card stat-card col-span-2" style="grid-column: span 2;">
                        <div>
                            <p class="stat-label">High Priority Pending</p>
                            <p class="stat-value text-red mono" id="hight_priority_pending">212</p>
                            <p class="stat-sub">High priority calls</p>
                        </div>
                        <div class="stat-icon bg-red-50">
                            <svg width="20" height="20" class="icon-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                        </div>
                    </div>
                </div>

                <!-- Charts Row 1 -->
                <div class="charts-row1">
                    <!-- Bar Chart -->
                    <div class="card chart-card">
                        <div class="chart-header">
                            <div class="chart-header-left">
                                <h2 class="chart-title">Pending Calls by Aging Bucket (Days)</h2>
                                <span class="chart-info">ℹ</span>
                            </div>
                            <select class="chart-select">
                                <option>Bar Chart</option>
                            </select>
                        </div>
                        <div id="agingBucketChart" style="height:240px;"></div>
                    </div>

                    <!-- Status Card -->
                    <div class="card status-card">
                        <div class="chart-header-left" style="margin-bottom:12px;">
                            <h2 class="chart-title">Pending Calls by Status</h2>
                            <span class="chart-info">ℹ</span>
                        </div>
                        <div class="status-list">
                            <!-- Assigned -->
                            <div class="status-item">
                                <div class="status-icon si-blue">
                                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                                </div>
                                <div class="status-detail">
                                    <div class="status-row" id="assigned_row">
                                        <span class="status-name">Assigned</span>
                                        <span class="status-count">512 <span class="status-pct">(41.0%)</span></span>
                                    </div>
                                    <div class="progress-bg"><div class="progress-fill fill-blue" style="width:41%"></div></div>
                                </div>
                            </div>
                            <!-- Part Not Available -->
                            <div class="status-item">
                                <div class="status-icon si-orange">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div class="status-detail">
                                    <div class="status-row" id="part_not_available_row">
                                        <span class="status-name">Part Not Available</span>
                                        <span class="status-count">286 <span class="status-pct">(22.9%)</span></span>
                                    </div>
                                    <div class="progress-bg"><div class="progress-fill fill-orange" style="width:23%"></div></div>
                                </div>
                            </div>
                            <!-- Work In Progress -->
                            <div class="status-item">
                                <div class="status-icon si-yellow">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="status-detail">
                                    <div class="status-row" id="work_in_progress_row">
                                        <span class="status-name">Work In Progress</span>
                                        <span class="status-count">248 <span class="status-pct">(19.9%)</span></span>
                                    </div>
                                    <div class="progress-bg"><div class="progress-fill fill-yellow" style="width:20%"></div></div>
                                </div>
                            </div>
                            <!-- Unassigned -->
                            <div class="status-item">
                                <div class="status-icon si-gray">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="status-detail">
                                    <div class="status-row" id="unassigned_row">
                                        <span class="status-name">Unassigned</span>
                                        <span class="status-count">132 <span class="status-pct">(10.6%)</span></span>
                                    </div>
                                    <div class="progress-bg"><div class="progress-fill fill-gray" style="width:11%"></div></div>
                                </div>
                            </div>
                            <!-- Replacement Request -->
                            <div class="status-item">
                                <div class="status-icon si-green">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                </div>
                                <div class="status-detail">
                                    <div class="status-row" id="replacement_request">
                                        <span class="status-name">Replacement Request</span>
                                        <span class="status-count">70 <span class="status-pct">(5.6%)</span></span>
                                    </div>
                                    <div class="progress-bg"><div class="progress-fill fill-green" style="width:6%"></div></div>
                                </div>
                            </div>
                            <!-- Total -->
                            <div class="status-total-row" style="display: none">
                                <span class="status-total-label">Total</span>
                                <span class="status-total-val">1,248 <span class="status-pct">(100%)</span></span>
                            </div>
                        </div>
                    </div>
                    <!-- Donut -->
                    <div class="card chart-card">
                        <div class="chart-header">
                            <h2 class="chart-title">Pending Calls by Product</h2>
                        </div>
                        <div id="productChart" style="height:240px;"></div>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="bottom-section">
                    <!-- Aging Snapshot Table -->
                    <div class="card chart-card">
                        <h2 class="chart-title" style="margin-bottom:12px;">Aging Snapshot (By Buckets)</h2>
                        <div class="table-wrapper">
                            <table id="snapshot_table" style="border: 2px solid rgba(128,128,128,0.26);border-radius: 15px;!important;">
                                <thead>
                                <tr>
                                    <th>Aging Bucket (Day)</th>
                                    <th class="text-center">Battery</th>
                                    <th class="text-center">Inverter</th>
                                    <th class="text-center">Solar Product</th>
                                    <th class="text-center">Total Calls</th>
                                    <th class="text-center">% of Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- Info Cards -->
                    <div class="info-cards-grid">
                        <!-- Oldest Pending -->
                        <div class="card info-card">
                            <p class="info-label">Oldest Pending Call</p>
                            <p class="info-value-lg text-orange mono" id="oldest_pending_call_days">127 Days</p>
                            <div class="info-sub">
                                <p class="info-sub-bold" id="oldest_pending_call_product">Battery</p>
                                <p id="oldest_pending_call_address">UP - Lucknow</p>
                            </div>
                        </div>
                        <!-- Top State -->
                        <div class="card info-card">
                            <p class="info-label">Top State (By Aging)</p>
                            <p class="info-value-md text-blue" id="top_state_state">Uttar Pradesh</p>
                            <div class="info-sub">
                                <p>Highest aging</p>
                                <p class="info-sub-bold" id="top_state_days">28 Days</p>
                            </div>
                        </div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <div class="info-cards-grid">
                        <!-- Top Product -->
                        <div class="card info-card">
                            <p class="info-label">Top Product (By Aging)</p>
                            <p class="info-value-md text-blue" id="top_product">Battery</p>
                            <div class="info-sub">
                                <p>Highest avg aging</p>
                                <p class="info-sub-bold" id="top_product_days">25 Days</p>
                            </div>
                        </div>
                        <!-- SLA Breached -->
                        <div class="card info-card sla-card">
                            <p class="info-label">SLA Breached</p>
                            <p class="info-value-lg text-red mono" id="sla_breached">98</p>
                            <p class="info-sub">SLA breached calls</p>
                        </div>
                        <div></div>
                        <div></div>
                    </div>
                </div>

                <div class="container-fluid" style="margin-bottom: 20px;">
                    <div class="row">
                        <div class="col-md-6">
                            <div style="min-height: 200px;!important;">
                                <div class="chart-header">
                                    <div class="chart-header-left">
                                        <h2 class="chart-title">Pending Calls by State (Above 2 Days)</h2>
                                        <span class="chart-info">ℹ</span>
                                    </div>
                                </div>
                                <div id="stateChart"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card status-card">
                                <div class="chart-header-left" style="margin-bottom:12px;">
                                    <h2 class="chart-title">Pending Replacement Settlement Calls</h2>
                                    <span class="chart-info">ℹ</span>
                                </div>
                                <div class="status-list">
                                    <!-- Assigned -->
                                    <div class="status-item">
                                        <div class="status-icon si-blue">
                                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/></svg>
                                        </div>
                                        <div class="status-detail">
                                            <div class="status-row" id="assigned_row">
                                                <span class="status-name">RPH Pending </span>
                                                <span class="status-count">512 <span class="status-pct">(41.0%)</span></span>
                                            </div>
                                            <div class="progress-bg"><div class="progress-fill fill-blue" style="width:41%"></div></div>
                                        </div>
                                    </div>
                                    <!-- Part Not Available -->
                                    <div class="status-item">
                                        <div class="status-icon si-orange">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <div class="status-detail">
                                            <div class="status-row" id="part_not_available_row">
                                                <span class="status-name">RPD Pending</span>
                                                <span class="status-count">286 <span class="status-pct">(22.9%)</span></span>
                                            </div>
                                            <div class="progress-bg"><div class="progress-fill fill-orange" style="width:23%"></div></div>
                                        </div>
                                    </div>
                                    <!-- Work In Progress -->
                                    <div class="status-item">
                                        <div class="status-icon si-yellow">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="status-detail">
                                            <div class="status-row" id="work_in_progress_row">
                                                <span class="status-name">POD Pending</span>
                                                <span class="status-count">248 <span class="status-pct">(19.9%)</span></span>
                                            </div>
                                            <div class="progress-bg"><div class="progress-fill fill-yellow" style="width:20%"></div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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

<!-- GLOBAL LOADER -->
<div id="dashboardLoader" class="dashboard-loader">
    <div class="loader-box">
        <div class="spinner"></div>
        <p>Loading Dashboard...</p>
    </div>
</div>


<div class="modal" id="myModal">
    <div class="modal-content">
        <h3 id="popup_modal">Popup Modal</h3>
        <div class="table-wrapper">
            <div class="table-responsive-custom">

                <table id="_modal_table_">

                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Job No</th>
                        <th>Contact No</th>
                        <th>Customer Name</th>
                        <th>Products</th>
                        <th>View</th>
                    </tr>
                    </thead>

                    <tbody>

                    <tr>
                        <td>1</td>

                        <td>
                <span class="table-text"
                      title="UP2605130001">
                      UP2605130001
                </span>
                        </td>

                        <td>
                <span class="table-text"
                      title="6589658745">
                      6589658745
                </span>
                        </td>

                        <td>
                <span class="table-text"
                      title="Test User Very Long Name">
                      Test User Very Long Name
                </span>
                        </td>

                        <td>
                <span class="table-text"
                      title="Inverter Battery Solar">
                      Inverter Battery Solar
                </span>
                        </td>

                        <td class="td-bold">
                <span class="table-text"
                      title="M001">
                      M001
                </span>
                        </td>

                        <td>
                            <a href="job_view.php?refid=VVAyNjA1MTMwMDAx" class="btn-view">
                                View
                            </a>
                        </td>
                    </tr>

                    </tbody>

                </table>

            </div>
        </div>
        <div style="text-align: center;margin-top: 100px"><button class="btn btn-warning" onclick="modalClose()">Close</button></div>
    </div>
</div>

<script>
    async function fetchBarJobData(category){
        category = category.replace(/\s*days?/i, '').trim();
        const response=await fetch(`../pagination/<?=$pagination?>?bar_bucket=${category}`)
        const data=await response.json();
        return data;
    }
    async function fetchdonutJobData(category){
        const response=await fetch(`../pagination/<?=$pagination?>?dunut_bucket=${category}`)
        const data=await response.json();
        return data;
    }
    async function fetchpieJobData(category){
        const response=await fetch(`../pagination/<?=$pagination?>?pie_bucket=${category}`)
        const data=await response.json();
        return data;
    }
    async function fetchstateBucketJobData(category){
        const response=await fetch(`../pagination/<?=$pagination?>?state_bucket=${category}`)
        const data=await response.json();
        return data;
    }
    async function rendeModalTable(data) {
        const tableBody = document.querySelector("#_modal_table_ tbody");
        tableBody.innerHTML = "";

        // Pehle se initialized DataTable destroy karo
        if ($.fn.DataTable.isDataTable("#_modal_table_")) {
            $("#_modal_table_").DataTable().destroy();
        }

        if (!data || data.length === 0) {
            tableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align:center;padding:20px;">
                    No Data Found
                </td>
            </tr>
        `;
            return;
        }

        let rows = "";
        data.forEach((item, index) => {
            rows += `
            <tr>
                <td>${index + 1}</td>
                <td><span class="table-text" title="${item.job_no}">${item.job_no}</span></td>
                <td><span class="table-text" title="${item.contact_no}">${item.contact_no}</span></td>
                <td><span class="table-text" title="${item.customer_name}">${item.customer_name}</span></td>
                <td><span class="table-text" title="${item.product_name}">${item.product_name}</span></td>
                <td>
                    <a href="job_view.php?refid=${item.job_no}" class="btn-view" target="_blank">
                        View
                    </a>
                </td>
            </tr>
        `;
        });

        tableBody.innerHTML = rows;

        // DataTable initialize
        $("#_modal_table_").DataTable({
            pageLength: 10,
            lengthChange: false,
            ordering: false,
            searching: true,
            dom: '<"dt-top"f>rt<"dt-bottom"ip>',
            language: {
                search: "",
                searchPlaceholder: "Search...",
                paginate: {
                    previous: "‹",
                    next: "›"
                },
                info: "Showing _START_–_END_ of _TOTAL_",
            }
        });
    }

    async function modalOpen(self, category, title = "Unknown", bucket = "") {
        const modal = document.getElementById("myModal");
        const modalTitle = document.getElementById("popup_modal");
        const tableBody = document.querySelector("#_modal_table_ tbody");
        modal.style.display = "flex";
        modalTitle.innerHTML = title;
        tableBody.innerHTML = `
        <tr>
            <td colspan="6" style="text-align:center;padding:20px;">
                Loading...
            </td>
        </tr>
    `;
        self.showLoader();
        try {
            let data = [];
            if (bucket === "") {
                self.showMessage("Something is wrong", "error");
                return;
            }
            if (bucket === "bar") {
                data = await fetchBarJobData(category);
            }
            if (bucket === "pie") {
                data = await fetchpieJobData(category);
            }
            if (bucket === "donut") {
                data = await fetchdonutJobData(category);
            }
            if (bucket === "state") {
                data = await fetchstateBucketJobData(category);
            }

            await rendeModalTable(data);
        } catch (error) {
            console.error(error);
            tableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align:center;padding:20px;color:red;">
                    Failed to load data
                </td>
            </tr>
        `;
            self.showMessage("Failed to load data", "error");
        } finally {
            self.hideLoader();
        }
    }
    function modalClose(){
        document.getElementById("myModal").style.display = "none";
    }
    window.onclick = function(event){
        const modal = document.getElementById("myModal");

        /* Prevent closing when clicking outside */
        if(event.target === modal){
            return;
        }
    }
    Highcharts.setOptions({
        chart: { style: { fontFamily: "'DM Sans', sans-serif" } },
        credits: { enabled: false },
        title: { text: '' },
        tooltip: { borderRadius: 8, shadow: false }
    });
    async function getAllDataFetchFromServer() {
        const response = await fetch(
            '../pagination/<?=$pagination?>?form_input_data'
        );
        return await response.json();
    }
    function DashboardCreations() {
        this.loader = document.getElementById("dashboardLoader");
        this.form = document.getElementById("dashboard_form");
        this.dataRange = document.getElementById("date_range");
        this.zone = document.getElementById("zone_filter");
        this.state = document.getElementById("state_filter");
        this.bsi = document.getElementById("bsi_filter");
        this.enginertype = document.getElementById("engineer-type");
        this.product = document.getElementById("product_filter");
        this.bucket = document.getElementById("bucket_filter");
        this.status = document.getElementById("status_filter");
        this.submit_button = document.getElementById("submit_button");
        this.reset_button = document.getElementById("reset_button");

        this.allData = {};
    }
    DashboardCreations.prototype.showLoader = function () {
        this.loader.classList.add("active");
    }
    DashboardCreations.prototype.hideLoader = function () {
        this.loader.classList.remove("active");
        document.getElementById("dashboard_m").classList.remove("hidden");
    }
    DashboardCreations.prototype.init = async function () {

        this.allData = await getAllDataFetchFromServer();
        this.fillDateRange();
        this.fillZone();
        //this.fillbsi(); // <-- ye add karo
        this.fillProducts();
        this.fillEngineerType();
        this.fillStatus();
        this.bindEvent();
    }
    DashboardCreations.prototype.createOption = function (selectBox, value, text){
        let option = document.createElement("option");
        option.value = value??'';
        option.textContent = text;
        selectBox.appendChild(option);
    }
    DashboardCreations.prototype.createOptionForDateRange = function (selectBox, value, text){
        let option = document.createElement("option");
        option.value = value;
        option.textContent = `${text} days`;
        selectBox.appendChild(option);
    }
    DashboardCreations.prototype.fillDateRange = function () {
        this.dataRange.innerHTML = '<option value="">Select Date Range</option>';
        this.allData.data_range.forEach((item) => {
            this.createOptionForDateRange(this.dataRange, item, item);
        });
    }
    DashboardCreations.prototype.fillZone = function () {
        this.zone.innerHTML = '<option value="">Select Zone</option>';
        this.allData.zone.forEach((item) => {
            this.createOption(
                this.zone,
                item.zone,
                item.zone_name
            );
        });
    }
    DashboardCreations.prototype.fillState = function (zoneid) {
        this.state.innerHTML = '<option value="">Select State</option>';

        let states = this.allData.zone_wise_state.filter((item) => {
            return item.zoneid == zoneid;
        });
        states.forEach((item) => {
            this.createOption(
                this.state,
                item.stateid,
                item.state
            );
        });
    }
    DashboardCreations.prototype.fillbsi = async function (state) {
        this.showLoader();
        try {
            const response = await fetch(
                `../pagination/<?=$pagination?>?bsi=${state}`
            );
            const data = await response.json();
            this.bsi.innerHTML = '<option value="">Select BSI</option>';
            data.forEach((item) => {
                this.createOption(
                    this.bsi,
                    item.sapid,
                    item.username
                );
            });
        } catch (error) {
            console.error(error);
        } finally {
            this.hideLoader();
        }
    };
    DashboardCreations.prototype.fillProducts = function () {
        this.product.innerHTML = '<option value="">Select Product</option>';
        this.allData.poduct.forEach((item) => {
            this.createOption(
                this.product,
                item.product_id,
                item.product_name
            );
        });
    }
    DashboardCreations.prototype.fillEngineerType = function () {
        this.enginertype.innerHTML = '<option value="">Select Engineer Type</option>';
        this.allData.enginnertype.forEach((item) => {
            this.createOption(
                this.enginertype,
                item,
                item
            );
        });
    }
    DashboardCreations.prototype.fillStatus = function () {
        this.status.innerHTML =
            '<option value="">Select Status</option>';
        this.allData.status.forEach((item) => {
            this.createOption(
                this.status,
                item,
                item
            );
        });
    }
    DashboardCreations.prototype.bindEvent = function () {
        let self = this;
        this.zone.addEventListener("change", function (e) {
            self.fillState(e.target.value);
        });

        this.state.addEventListener("change", function (e) {
            let state = e.target.value;
            if (state !== '') {
                self.fillbsi(state);
            } else {
                self.bsi.innerHTML = '<option value="">Select BSI</option>';
            }
        });

        this.submit_button.addEventListener("click", async function (e) {
            e.preventDefault();
            self.formSubmit();
        });
        this.reset_button.addEventListener("click", function (e) {
            e.preventDefault();
            self.resetAll();
        });
    }
    DashboardCreations.prototype.validateAll = function () {
        // if (this.dataRange.value == "") {
        //     alert("Please Select Date Range");
        //     return false;
        // }
        // if (this.zone.value == "") {
        //     alert("Please Select Zone");
        //     return false;
        // }
        // if (this.state.value == "") {
        //     alert("Please Select State");
        //     return false;
        // }
        return true;
    }
    DashboardCreations.prototype.formSubmit = async function () {

        if (!this.validateAll()) {
            return false;
        }

        if (!navigator.onLine) {
            this.showMessage(
                "No internet connection detected",
                "error"
            );
            return;
        }

        try{
            this.showLoader();
            // form me data add
            const formdata = new FormData();
            formdata.set('data_range', this.dataRange.value);
            formdata.set('zone', this.zone.value);
            formdata.set('state', this.state.value);
            formdata.set('bsi', this.bsi.value);
            formdata.set('enginner_type', this.enginertype.value);
            formdata.set('product', this.product.value);
            formdata.set('bucket', this.bucket.value);
            formdata.set('status', this.status.value);
            formdata.set('form_submit', this.status.value);

            const controller = new AbortController();

            const timeout = setTimeout(() => {
                controller.abort();
            }, 15000);

            const response = await fetch(
                '../pagination/<?=$pagination?>',
                {
                    method: "POST",
                    body: formdata,
                    signal: controller.signal
                }
            );

            clearTimeout(timeout);


            // SERVER ERROR
            if (!response.ok) {
                throw new Error("Server error : " + response.status);
            }

            let data;
            try {
                data = await response.json();
            } catch {
                throw new Error("Invalid server response");
            }

            // yha data print
            console.log(data);

            // EMPTY DATA
            if (!data) {
                throw new Error("No data received from server");
            }
            document.getElementById("total_pending_calls").innerText = data.cards_data.total_pending_calls;
            document.getElementById("avg_aging").innerText = data.cards_data.avg_aging;
            document.getElementById("days_pending").innerText = data.cards_data.pending_days;
            document.getElementById("hight_priority_pending").innerText = data.cards_data.high_priority_pending;
            const self=this;
            Highcharts.chart('agingBucketChart', {
                chart: {
                    type: 'column',
                    backgroundColor: 'transparent',
                    margin: [10, 10, 50, 45]
                },
                xAxis: {
                    categories: data.chart_details.bar_chart.x_categories,
                    labels: { style: { fontSize: '10px', color: '#6b7280' }, rotation: -20 },
                    lineColor: '#e5e7eb', tickColor: '#e5e7eb'
                },
                yAxis: {
                    title: { text: data.chart_details.bar_chart.y_label, style: { fontSize: '10px', color: '#9ca3af' } },
                    labels: { style: { fontSize: '10px', color: '#6b7280' } },
                    gridLineColor: '#f3f4f6'
                },
                legend: { enabled: false },
                plotOptions: {
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function () {
                                    let category = this.category;
                                    let value = this.y;
                                    let index = this.index;
                                    alert("Aging Bucket: " + category + "\nCalls: " + value + "\nIndex: " + index);
                                    self.showLoader();
                                    setTimeout(()=>{
                                        self.hideLoader();
                                        modalOpen(self,category,category,'bar');
                                    },1000);
                                }
                            }
                        }
                    },
                    column: {
                        borderRadius: 4,
                        dataLabels: {
                            enabled: true,
                            format: '{y}',
                            style: { fontSize: '10px', fontWeight: '600', textOutline: 'none', color: '#374151' }
                        }
                    }
                },
                series: [{
                    name: 'Calls',
                    data: data.chart_details.bar_chart.data
                }]
            });
            Highcharts.chart('stateChart',  {
                chart: {
                    type: 'bar',
                    backgroundColor: 'transparent',
                    margin: [5, 60, 20, 100]
                },
                xAxis: {
                    categories: data.chart_details.column_chart.x_categories,
                    labels: { style: { fontSize: '10px', color: '#6b7280' } },
                    lineColor: '#e5e7eb', tickColor: '#e5e7eb'
                },
                yAxis: {
                    title: { text: data.chart_details.column_chart.y_label, style: { fontSize: '10px', color: '#9ca3af' } },
                    labels: { style: { fontSize: '10px', color: '#6b7280' } },
                    gridLineColor: '#f3f4f6'
                },
                legend: { enabled: false },
                plotOptions: {
                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function () {
                                    let category = this.category;
                                    let value = this.y;
                                    let index = this.index;
                                    alert("Aging Bucket: " + category + "\nCalls: " + value + "\nIndex: " + index);
                                    console.log("Bar Clicked =>", this);
                                    self.showLoader();
                                    setTimeout(()=>{
                                        self.hideLoader();
                                        modalOpen(self,category,category,'state');
                                    },1000);
                                }
                            }
                        }
                    },
                    bar: {
                        borderRadius: 3,
                        color: '#3b82f6',
                        dataLabels: {
                            enabled: true,
                            style: { fontSize: '10px', fontWeight: '500', textOutline: 'none', color: '#374151' }
                        }
                    }
                },
                series: [{
                    name: 'Calls',
                    data: data.chart_details.column_chart.data
                }]
            });
            Highcharts.chart('productChart', {
                chart: {
                    type: 'pie',
                    backgroundColor: 'transparent',
                    margin: [0, 0, 0, 0]
                },
                plotOptions: {
                    pie: {
                        innerSize: '60%',
                        dataLabels: { enabled: false },
                        showInLegend: true,
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    },

                    series: {
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function () {
                                    let name  = this.name;
                                    let value = this.y;
                                    let pct   = this.percentage.toFixed(1);
                                    console.log("Pie Clicked =>", this);
                                    self.showLoader();
                                    setTimeout(()=>{
                                        self.hideLoader();
                                        modalOpen(self,name,name,'pie');
                                    },1000);
                                }
                            }
                        }
                    }
                },
                legend: {
                    align: 'right',
                    verticalAlign: 'middle',
                    layout: 'vertical',
                    itemStyle: { fontSize: '11px', fontWeight: '500', color: '#374151' },
                    labelFormatter: function () {
                        return `${this.name}<br><span style="color:#6b7280;font-size:10px">${this.y} (${(this.percentage).toFixed(1)}%)</span>`;
                    }
                },
                tooltip: { pointFormat: '<b>{point.y}</b> calls ({point.percentage:.1f}%)' },
                series: [{
                    name: data.chart_details.pie_chart.name,
                    data: data.chart_details.pie_chart.data
                }]
            });
            DashboardCreations.prototype.tableRendering(document.getElementById("snapshot_table"),data.aging_snapshot);
            DashboardCreations.prototype.pendingcallByStatus( document.querySelector(".status-card"),data.pending_call_by_status);
            this.stackData(data.stack_data);
        }
        catch(error){
            console.error(error);
            this.hideLoader();
            if(error.name === "AbortError"){
                this.showMessage("Request timeout. Server taking too long.", "error");
                return;
            }

            if(error.message.includes("Failed to fetch")){
                this.showMessage("Unable to connect to server", "error");
                return;
            }
            // other errors
            this.showMessage(error.message || "Something went wrong",
                "error"
            );
        }finally {
            this.submit_button.disabled = false;
            this.hideLoader();
        }
    }
    DashboardCreations.prototype.stackData = function(stackdata){

        const oldest_pending_call_days =
            document.getElementById("oldest_pending_call_days");

        const oldest_pending_call_product =
            document.getElementById("oldest_pending_call_product");

        const oldest_pending_call_address =
            document.getElementById("oldest_pending_call_address");
        const sla_breached=document.getElementById("sla_breached");

        const top_state_state =
            document.getElementById("top_state_state");

        const top_state_days =
            document.getElementById("top_state_days");


        const top_product =
            document.getElementById("top_product");

        const top_product_days =
            document.getElementById("top_product_days");


        // Oldest Pending Call

        oldest_pending_call_days.innerText =
            stackdata.oldest_pending_call.days || "0days";

        oldest_pending_call_product.innerText =
            stackdata.oldest_pending_call.product || "-";

        oldest_pending_call_address.innerText =
            stackdata.oldest_pending_call.address || "-";
        sla_breached.innerHTML=stackdata.sla || "-";

        // Top State
        top_state_state.innerText =
            stackdata.top_state.state_name || "-";

        top_state_days.innerText =
            stackdata.top_state.aging_days || "0days";


        // Top Product

        top_product.innerText =
            stackdata.top_product.product || "-";

        top_product_days.innerText =
            stackdata.top_product.days || "0days";
    };
    DashboardCreations.prototype.resetAll = function () {
        this.form.reset();
        this.state.innerHTML = '<option value="">Select State</option>';
        document.getElementById("dashboard_m").classList.add("hidden");
    }
    DashboardCreations.prototype.cardSetValue=function(){}
    DashboardCreations.prototype.columncartSetup=function(){}
    DashboardCreations.prototype.piechartSetup=function(){}
    document.addEventListener("DOMContentLoaded", async function () {
        let dashboard = new DashboardCreations();
        await dashboard.init();
    });
    DashboardCreations.prototype.tableRendering = function(table, data = []) {

        let tbody = table.querySelector("tbody");

        if (!tbody) {

            tbody = document.createElement("tbody");

            table.appendChild(tbody);
        }

        tbody.innerHTML = "";

        /*
        |--------------------------------------------------------------------------
        | Loop Main Data
        |--------------------------------------------------------------------------
        */

        data.forEach(item => {

            /*
            |--------------------------------------------------------------------------
            | ROWS
            |--------------------------------------------------------------------------
            */

            if (item.rows && Array.isArray(item.rows)) {

                item.rows.forEach(row => {

                    let tr = document.createElement("tr");

                    row.forEach((cell, index) => {

                        let td = document.createElement("td");

                        td.textContent = cell;

                        /*
                        |--------------------------------------------------------------------------
                        | Last Column = %
                        |--------------------------------------------------------------------------
                        */

                        if (index === row.length - 1) {

                            td.style.fontWeight = "600";
                        }

                        tr.appendChild(td);
                    });

                    tbody.appendChild(tr);
                });
            }

            /*
            |--------------------------------------------------------------------------
            | TOTAL ROW
            |--------------------------------------------------------------------------
            */

            if (item.total && Array.isArray(item.total)) {

                let tr = document.createElement("tr");

                tr.classList.add("total-row");

                item.total.forEach(cell => {

                    let td = document.createElement("td");

                    td.textContent = cell;

                    td.style.fontWeight = "700";

                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            }
        });
    };
    DashboardCreations.prototype.pendingcallByStatus = function(element, data) {

        if (!data || !data.length) return;

        let statusData = data[0];

        let statusMap = {
            assigned: "assigned_row",
            part_not_assigned: "part_not_available_row",
            work_in_progress: "work_in_progress_row",
            unassigned: "unassigned_row",
            replacement_request: "replacement_request"
        };

        Object.keys(statusMap).forEach(key => {

            let rowId = statusMap[key];

            let row = element.querySelector("#" + rowId);

            if (!row || !statusData[key]) return;

            let count = statusData[key][0];
            let percent = statusData[key][1];

            let countElement = row.querySelector(".status-count");

            countElement.innerHTML = `
            ${count}
            <span class="status-pct">(${percent})</span>
        `;

            let progressFill = row
                .closest(".status-detail")
                .querySelector(".progress-fill");
            progressFill.style.width = percent;

        });

        let totalData = data.find(item => item.total);

        if (totalData) {
            let totalRow = element.querySelector(".status-total-row");
            if (totalRow) {
                totalRow.querySelector(".status-total-val").innerHTML = `
                ${totalData.total[0]}
                <span class="status-pct">(${totalData.total[1]})</span>`;
            }
        }
    };
    DashboardCreations.prototype.showMessage = function (message, type = "error"){

        let bgColor = "#ef4444";
        if(type === "success"){
            bgColor = "#22c55e";
        }
        if(type === "warning"){
            bgColor = "#f59e0b";
        }
        const div = document.createElement("div");

        div.className = "custom-alert";

        div.style.background = bgColor;

        div.innerText = message;

        document.body.appendChild(div);

        setTimeout(() => {
            div.classList.add("show");
        }, 100);

        setTimeout(() => {

            div.classList.remove("show");

            setTimeout(() => {
                div.remove();
            }, 300);

        }, 3500);
    }


</script>
</body>
</html>