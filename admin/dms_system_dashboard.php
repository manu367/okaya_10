    <?php
    /**
     * DMS Dashboard — updated
     * Changes vs original:
     *  1. Filter selects are now empty shells; JS populates from API
     *  2. .db-content wrapper is hidden on load; shown after first Apply Filters
     *  3. Apply Filters fetches data and renders dashboard
     *  4. Reset clears all selects and hides dashboard again
     *  5. Zone → State cascade via ?state_by_zone=
     *  6. State → BSI cascade via ?bsi=
     */
    require_once("../includes/config.php");
    global $link1, $screenwidth;
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= siteTitle ?></title>
        <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
        <script src="../js/highcharts.js"></script>
        <script src="../js/accessibility.js"></script>
        <script src="../js/jquery.min.js"></script>
        <link href="../css/font-awesome.min.css" rel="stylesheet">
        <link href="../css/abc.css" rel="stylesheet">
        <link href="../css/abc2.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <script src="../js/moment.js"></script>
        <script src="../js/frmvalidate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/common_js.js"></script>
        <script type="text/javascript" src="../js/daterangepicker.js"></script>
        <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
        <link rel="stylesheet" href="../css/datepicker.css">
        <script src="../js/bootstrap-datepicker.js"></script>
        <script>
            $(document).ready(function () {
                $('input[name="daterange"]').daterangepicker({
                    maxDate: moment(),
                    locale: { format: 'YYYY-MM-DD' }
                });
            });
        </script>
        <!-- dashboard module loaded at bottom of body -->
        <style>
            /* ── Reset & Base ── */
            *, *::before, *::after { box-sizing: border-box; }
    
            .db-wrap {
                padding: 16px;
                max-width: 1440px;
                margin: auto;
            }
    
            /* ── Card ── */
            .card {
                background: #fff;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0,0,0,.07), 0 1px 8px rgba(0,0,0,.04);
            }
    
            /* ── Header ── */
            .hdr {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 14px 20px;
                margin-bottom: 14px;
            }
            .hdr-left { display: flex; align-items: center; gap: 12px; }
            .hdr-title { font-size: 17px; font-weight: 700; color: #111827; }
            .hdr-sub   { font-size: 11px; color: #6b7280; margin-top: 2px; }
            .hdr-right { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6b7280; }
    
            .dot-live {
                width: 7px; height: 7px; border-radius: 50%;
                background: #22c55e; display: inline-block;
                animation: pulse 1.8s infinite;
            }
            @keyframes pulse {
                0%,100% { opacity:1 } 50% { opacity:.35 }
            }
    
            .icon-btn {
                background: none; border: none; cursor: pointer;
                color: #6b7280; display: flex; align-items: center; padding: 4px;
            }
            .icon-btn:hover { color: #374151; }
    
            /* ── Filter Card ── */
            .flt-card { padding: 16px 18px; margin-bottom: 14px; }
            .flt-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            @media(min-width: 600px) { .flt-grid { grid-template-columns: repeat(3, 1fr); } }
            @media(min-width: 900px) { .flt-grid { grid-template-columns: repeat(6, 1fr); } }
    
            .flt-label {
                display: block; font-size: 11px; color: #6b7280;
                font-weight: 600; margin-bottom: 4px; letter-spacing: .3px;
            }
            .flt-select {
                appearance: none; -webkit-appearance: none;
                background: #f9fafb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 10px center;
                border: 1px solid #e5e7eb; border-radius: 8px;
                padding: 7px 28px 7px 10px; font-size: 12px; color: #374151;
                width: 100%; outline: none; cursor: pointer;
            }
            .flt-select:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px #bfdbfe55; }
            .flt-select:disabled { opacity: .55; cursor: not-allowed; }
    
            .flt-actions {
                display: flex; gap: 8px; align-items: center;
                margin-top: 14px; padding-top: 12px; border-top: 1px solid #f3f4f6;
                flex-wrap: wrap;
            }
            .btn-p {
                display: inline-flex; align-items: center; gap: 6px;
                background: #2563eb; color: #fff; font-size: 12px; font-weight: 500;
                padding: 8px 16px; border-radius: 8px; border: none; cursor: pointer;
                transition: background .15s;
            }
            .btn-p:hover { background: #1d4ed8; }
            .btn-p:disabled { background: #93c5fd; cursor: not-allowed; }
            .btn-s {
                display: inline-flex; align-items: center; gap: 6px;
                background: #fff; color: #4b5563; font-size: 12px; font-weight: 500;
                padding: 8px 16px; border-radius: 8px; border: 1px solid #e5e7eb; cursor: pointer;
                transition: background .15s;
            }
            .btn-s:hover { background: #f9fafb; }
    
            /* ── Loader skeleton shown while filters are loading ── */
            .flt-skeleton {
                display: flex; gap: 10px; flex-wrap: wrap;
            }
            .sk-bar {
                height: 34px; border-radius: 8px; background: #f3f4f6;
                animation: shimmer 1.4s infinite;
                flex: 1; min-width: 120px;
            }
            @keyframes shimmer {
                0%,100% { opacity:1 } 50% { opacity:.45 }
            }
    
            /* ── Dashboard content — hidden until Apply Filters ── */
            .db-content { display: none; }
            .db-content.visible { display: block; }
    
            /* ── Empty state shown before first apply ── */
            .empty-state {
                text-align: center;
                padding: 56px 20px;
                color: #9ca3af;
            }
            .empty-state svg { margin-bottom: 14px; opacity: .4; }
            .empty-state p { font-size: 13px; margin: 0; }
    
            /* ── KPI Row ── */
            .kpi-row {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 12px; margin-bottom: 14px;
            }
            @media(min-width: 600px) { .kpi-row { grid-template-columns: repeat(3, 1fr); } }
            @media(min-width: 900px) { .kpi-row { grid-template-columns: repeat(5, 1fr); } }
    
            .kpi-card {
                padding: 16px; display: flex; flex-direction: column;
                justify-content: space-between; min-height: 104px;
                position: relative; overflow: hidden;
            }
            .kpi-card::after {
                content: ''; position: absolute; top: 0; left: 0;
                width: 4px; height: 100%; border-radius: 12px 0 0 12px;
            }
            .kpi-blue::after   { background: #2563eb; }
            .kpi-green::after  { background: #16a34a; }
            .kpi-amber::after  { background: #d97706; }
            .kpi-red::after    { background: #dc2626; }
            .kpi-purple::after { background: #7c3aed; }
    
            .kpi-icon {
                width: 36px; height: 36px; border-radius: 8px;
                display: flex; align-items: center; justify-content: center; margin-bottom: 10px;
            }
            .kpi-label { font-size: 11px; color: #6b7280; font-weight: 600; margin-bottom: 3px; letter-spacing:.3px; }
            .kpi-value { font-size: 24px; font-weight: 800; line-height: 1; }
            .kpi-sub   { font-size: 10px; color: #9ca3af; margin-top: 4px; }
    
            .bg-bl { background: #eff6ff; } .bg-gr { background: #f0fdf4; }
            .bg-am { background: #fffbeb; } .bg-rd { background: #fef2f2; }
            .bg-pu { background: #faf5ff; }
            .cl-bl { color: #2563eb; } .cl-gr { color: #16a34a; }
            .cl-am { color: #d97706; } .cl-rd { color: #dc2626; }
            .cl-pu { color: #7c3aed; }
    
            /* ── 3-Column Row ── */
            .row3 {
                display: grid;
                grid-template-columns: 1fr;
                gap: 14px; margin-bottom: 14px;
            }
            @media(min-width: 900px) {
                .row3 { grid-template-columns: 1.15fr 1fr 0.85fr; }
            }
    
            /* ── Chart Cards ── */
            .chart-card { padding: 18px; }
            .chart-hdr  {
                display: flex; align-items: center;
                justify-content: space-between; margin-bottom: 14px;
            }
            .chart-title { font-size: 13px; font-weight: 700; color: #1f2937; }
            .chart-badge {
                font-size: 10px; padding: 3px 9px;
                border-radius: 999px; font-weight: 600; letter-spacing:.3px;
            }
            .badge-bl { background: #eff6ff; color: #1d4ed8; }
            .badge-gr  { background: #f0fdf4; color: #15803d; }
    
            /* ── Highcharts containers ── */
            #pie-chart-container { width: 100%; height: 320px; }
            #multi-line          { width: 100%; height: 320px; }
            #column_chart        { width: 100%; height: 320px; }
    
            /* ── Aging Table ── */
            .snap-table {
                width: 100%; font-size: 11px; border-collapse: collapse;
            }
            .snap-table thead tr { background: #f9fafb; }
            .snap-table th {
                padding: 8px 10px; font-weight: 600; color: #6b7280;
                text-align: left; border-bottom: 1px solid #f0f0f0;
            }
            .snap-table th:not(:first-child) { text-align: center; }
            .snap-table tbody tr { border-bottom: 1px solid #f3f4f6; transition: background .12s; }
            .snap-table tbody tr:hover { background: #f8fafc; }
            .snap-table td { padding: 8px 10px; color: #374151; }
            .snap-table td:not(:first-child) { text-align: center; }
            .snap-table tfoot td {
                font-weight: 700; color: #111827;
                background: #f9fafb; padding: 8px 10px;
            }
            .snap-table tfoot td:not(:first-child) { text-align: center; }
    
            .bucket-pill {
                display: inline-block; padding: 2px 8px;
                border-radius: 6px; font-size: 10px; font-weight: 600;
            }
            .b0  { background: #dcfce7; color: #166534; }
            .b2  { background: #fef9c3; color: #854d0e; }
            .b5  { background: #fee2e2; color: #991b1b; }
            .b14 { background: #fce7f3; color: #9d174d; }
    
            .bucket-legend {
                display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px;
            }
            .bucket-legend span {
                display: flex; align-items: center; gap: 5px;
                font-size: 10px; color: #4b5563;
            }
            .bucket-legend i {
                width: 10px; height: 10px; border-radius: 3px; display: inline-block;
            }
    
            /* ── IQC / Status List ── */
            .st-list { display: flex; flex-direction: column; gap: 13px; margin-top: 4px; }
            .st-item { display: flex; align-items: center; gap: 10px; }
            .st-icon {
                width: 30px; height: 30px; border-radius: 50%;
                display: flex; align-items: center; justify-content: center;
                flex-shrink: 0; font-size: 13px;
            }
            .si-bl { background:#dbeafe; color:#2563eb; }
            .si-or { background:#ffedd5; color:#ea580c; }
            .si-yw { background:#fef9c3; color:#ca8a04; }
            .si-gy { background:#f3f4f6; color:#6b7280; }
            .si-gn { background:#dcfce7; color:#16a34a; }
    
            .st-detail { flex: 1; min-width: 0; }
            .st-row { display: flex; justify-content: space-between; font-size: 11px; margin-bottom: 4px; }
            .st-name  { color: #4b5563; font-weight: 500; }
            .st-count { font-weight: 700; color: #111827; }
            .st-pct   { color: #9ca3af; font-weight: 400; }
    
            .prog-bg   { width: 100%; background: #f3f4f6; border-radius: 999px; height: 6px; }
            .prog-fill { height: 6px; border-radius: 999px; transition: width .4s ease; }
            .f-bl { background: #3b82f6; } .f-or { background: #f97316; }
            .f-yw { background: #facc15; } .f-gy { background: #9ca3af; }
            .f-gn { background: #22c55e; }
    
            .st-total {
                display: flex; justify-content: space-between; font-size: 11px;
                padding-top: 10px; border-top: 1px solid #f3f4f6; margin-top: 6px;
            }
            .st-total-l { font-weight: 600; color: #111827; }
            .st-total-v { font-weight: 800; color: #111827; }
    
            /* ── 2-Column Row ── */
            .row2 {
                display: grid; grid-template-columns: 1fr;
                gap: 14px; margin-bottom: 14px;
            }
            @media(min-width: 900px) { .row2 { grid-template-columns: 1fr 1fr; } }
    
            /* ── Footer note ── */
            .footer-note {
                font-size: 11px; color: #9ca3af;
                padding: 0 4px 14px; text-align: center;
            }
    
            /* ── Scrollable table on small screens ── */
            .table-wrapper { overflow-x: auto; }
        </style>
    </head>
    <body>
        <div class="container-fluid">
        <div class="row content">
            <div id="hide_nav_">
                <?php include("../includes/leftnav2.php"); ?>
            </div>
            <div class="<?= $screenwidth ?> tab-pane fade in active" id="home">
                <div class="db-wrap">
    
                    <!-- ── PAGE HEADER ── -->
                    <div class="card hdr">
                        <div class="hdr-left">
                            <button class="icon-btn">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                            <div>
                                <div class="hdr-title">DMS Dashboard</div>
                            </div>
                        </div>
                        <div class="hdr-right">
                            <span class="dot-live"></span> Live
                        </div>
                    </div>
    
                    <!-- ── FILTERS ── -->
                    <div class="card flt-card">
                        <!-- Skeleton shown while ?init loads -->
                        <div id="flt-skeleton" class="flt-skeleton">
                            <div class="sk-bar"></div><div class="sk-bar"></div>
                            <div class="sk-bar"></div><div class="sk-bar"></div>
                            <div class="sk-bar"></div><div class="sk-bar"></div>
                        </div>
    
                        <!-- Actual filters — hidden until init data arrives -->
                        <div id="flt-body" style="display:none">
                            <div class="flt-grid">
                                <!-- Date Range -->
                                <div>
                                    <label class="flt-label">Date Range</label>
                                    <input type="text" name="daterange" id="date_rng" class="form-control" value="" />
                                    <select class="flt-select hidden" id="sel-timing">
                                        <option value="">Select Range</option>
                                    </select>
                                </div>
                                <!-- location aayege -->
                                <div>
                                    <label class="flt-label">Locations</label>
                                    <select class="flt-select" id="sel-zone">
                                        <option value="">All Zones</option>
                                    </select>
                                </div>
                                <!-- Product -->
                                <div>
                                    <label class="flt-label">Product</label>
                                    <select class="flt-select" id="sel-product">
                                        <option value="">All Products</option>
                                    </select>
                                </div>
                            </div>
    
                            <div class="flt-actions">
                                <button class="btn-p" id="btn-apply">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                                    </svg>
                                    Apply Filters
                                </button>
                                <button class="btn-s" id="btn-reset">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Reset
                                </button>
                            </div>
                        </div><!-- /#flt-body -->
                    </div>
    
                    <!-- ── EMPTY STATE (visible before first Apply) ── -->
                    <div id="empty-state" class="card empty-state">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="display:block;margin:0 auto 14px">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13
                                  13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017
                                  21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        <p>Select your filters above and click <strong>Apply Filters</strong> to load the dashboard.</p>
                    </div>
    
                    <!-- ── DASHBOARD CONTENT — hidden until Apply Filters ── -->
                    <div class="db-content" id="db-content">

                        <!-- ── KPI CARDS ── -->
                        <div class="kpi-row">
                            <div class="card kpi-card kpi-blue">
                                <div class="kpi-icon bg-bl">
                                    <svg width="18" height="18" class="cl-bl" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="kpi-label">TOTAL PLANNED</div>
                                    <div class="kpi-value cl-bl" id="kpi1">—</div>
                                    <div class="kpi-sub hidden">All scheduled calls</div>
                                </div>
                            </div>
                            <div class="card kpi-card kpi-green">
                                <div class="kpi-icon bg-gr">
                                    <svg width="18" height="18" class="cl-gr" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="kpi-label">TOTAL READY</div>
                                    <div class="kpi-value cl-gr" id="kpi2">—</div>
                                    <div class="kpi-sub hidden">Ready to dispatch</div>
                                </div>
                            </div>
                            <div class="card kpi-card kpi-amber">
                                <div class="kpi-icon bg-am">
                                    <svg width="18" height="18" class="cl-am" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="kpi-label" style="text-transform: uppercase">STOCK Shortage</div>
                                    <div class="kpi-value cl-am" id="kpi3">—</div>
                                    <div class="kpi-sub hidden">Units in storage</div>
                                </div>
                            </div>
                            <div class="card kpi-card kpi-red">
                                <div class="kpi-icon bg-rd">
                                    <svg width="18" height="18" class="cl-rd" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="kpi-label">TOTAL DELAY</div>
                                    <div class="kpi-value cl-rd" id="kpi4">—</div>
                                    <div class="kpi-sub hidden">Delayed past SLA</div>
                                </div>
                            </div>
                            <div class="card kpi-card kpi-purple">
                                <div class="kpi-icon bg-pu">
                                    <svg width="18" height="18" class="cl-pu" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="kpi-label">AVG AGING (DAYS)</div>
                                    <div class="kpi-value cl-pu" id="kpi5">—</div>
                                    <div class="kpi-sub hidden">Avg days pending</div>
                                </div>
                            </div>
                        </div>
    
                        <!-- ── ROW 3: Aging Table | Pie Chart | IQC List ── -->
                        <div class="row3">
    
                            <!-- Aging Jobs Table -->
                            <div class="card chart-card">
                                <div class="chart-hdr">
                                    <span class="chart-title">Aging Jobs (By Buckets)</span>
                                    <span class="chart-badge badge-bl">Live</span>
                                </div>
                                <div class="table-wrapper">
                                    <table class="snap-table">
                                        <thead>
                                        <tr>
                                            <th>Bucket</th>
                                            <th>P1</th><th>P2</th><th>P3</th>
                                            <th>Total</th><th>% Share</th>
                                        </tr>
                                        </thead>
                                        <tbody id="aging-tbody">
                                        <tr>
                                            <td><span class="bucket-pill b0">0–30 days</span></td>
                                            <td>—</td><td>—</td><td>—</td>
                                            <td style="font-weight:700">—</td><td>—</td>
                                        </tr>
                                        <tr>
                                            <td><span class="bucket-pill b2">31–60 days</span></td>
                                            <td>—</td><td>—</td><td>—</td>
                                            <td style="font-weight:700">—</td><td>—</td>
                                        </tr>
                                        <tr>
                                            <td><span class="bucket-pill b5">61–90 days</span></td>
                                            <td>—</td><td>—</td><td>—</td>
                                            <td style="font-weight:700">—</td><td>—</td>
                                        </tr>
                                        <tr>
                                            <td><span class="bucket-pill b14">&gt;90 days</span></td>
                                            <td>—</td><td>—</td><td>—</td>
                                            <td style="font-weight:700">—</td><td>—</td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr id="aging-foot">
                                            <td>Grand Total</td>
                                            <td>—</td><td>—</td><td>—</td><td>—</td>
                                            <td>100%</td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="bucket-legend">
                                    <span><i style="background:#dcfce7"></i>0–30 Days</span>
                                    <span><i style="background:#fef9c3"></i>31–60 Days</span>
                                    <span><i style="background:#fee2e2"></i>61–90 Days</span>
                                    <span><i style="background:#fce7f3"></i>&gt;90 Days</span>
                                </div>
                            </div>
    
                            <!-- Pie Chart -->
                            <div class="card chart-card">
                                <div class="chart-hdr">
                                    <span class="chart-title">Product Status %</span>
                                    <span class="chart-badge badge-bl">Live</span>
                                </div>
                                <div id="pie-chart-container"></div>
                            </div>
    
                            <!-- IQC Failure Rate -->
                            <div class="card chart-card">
                                <div class="chart-hdr">
                                    <span class="chart-title">IQC Failure Rate</span>
                                    <span class="chart-badge badge-gr">5 Priorities</span>
                                </div>
                                <div class="st-list">
                                    <div class="st-item">
                                        <div class="st-icon si-bl">
                                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                            </svg>
                                        </div>
                                        <div class="st-detail">
                                            <div class="st-row">
                                                <span class="st-name">P1 — Critical</span>
                                                <span class="st-count" id="s1">— <span class="st-pct" id="sp1"></span></span>
                                            </div>
                                            <div class="prog-bg"><div class="prog-fill f-bl" id="pb1" style="width:0%"></div></div>
                                        </div>
                                    </div>
                                    <div class="st-item">
                                        <div class="st-icon si-or">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/>
                                            </svg>
                                        </div>
                                        <div class="st-detail">
                                            <div class="st-row">
                                                <span class="st-name">P2 — High</span>
                                                <span class="st-count" id="s2">— <span class="st-pct" id="sp2"></span></span>
                                            </div>
                                            <div class="prog-bg"><div class="prog-fill f-or" id="pb2" style="width:0%"></div></div>
                                        </div>
                                    </div>
                                    <div class="st-item">
                                        <div class="st-icon si-yw">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                        <div class="st-detail">
                                            <div class="st-row">
                                                <span class="st-name">P3 — Medium</span>
                                                <span class="st-count" id="s3">— <span class="st-pct" id="sp3"></span></span>
                                            </div>
                                            <div class="prog-bg"><div class="prog-fill f-yw" id="pb3" style="width:0%"></div></div>
                                        </div>
                                    </div>
                                    <div class="st-item">
                                        <div class="st-icon si-gy">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <div class="st-detail">
                                            <div class="st-row">
                                                <span class="st-name">P4 — Low</span>
                                                <span class="st-count" id="s4">— <span class="st-pct" id="sp4"></span></span>
                                            </div>
                                            <div class="prog-bg"><div class="prog-fill f-gy" id="pb4" style="width:0%"></div></div>
                                        </div>
                                    </div>
                                    <div class="st-item">
                                        <div class="st-icon si-gn">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </div>
                                        <div class="st-detail">
                                            <div class="st-row">
                                                <span class="st-name">P5 — Watch</span>
                                                <span class="st-count" id="s5">— <span class="st-pct" id="sp5"></span></span>
                                            </div>
                                            <div class="prog-bg"><div class="prog-fill f-gn" id="pb5" style="width:0%"></div></div>
                                        </div>
                                    </div>
                                    <div class="st-total">
                                        <span class="st-total-l">Total Failures</span>
                                        <span class="st-total-v" id="iqc-total">—</span>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.row3 -->
    
                        <!-- ── ROW 2: Multi-line Chart | Column Chart ── -->
                        <div class="row2">
                            <div class="card chart-card">
                                <div id="multi-line"></div>
                            </div>
                            <div class="card chart-card">
                                <div id="column_chart"></div>
                            </div>
                        </div>
    
                        <p class="footer-note">
                            Data refreshes on Apply Filters &nbsp;·&nbsp; Last updated: <span id="last-updated">—</span>
                        </p>
                    </div><!-- /#db-content -->
    
                </div><!-- /.db-wrap -->
            </div>
        </div>
    </div>
    
    <?php
    include("../includes/footer.php");
    include("../includes/connection_close.php");
    ?>
    
    <!-- ══════════════════════════════════════════════════════════
         DASHBOARD JS — paste this <script> block at bottom of body
         in place of the existing one.
         All PHP ↔ JS key mismatches are fixed here.
         ══════════════════════════════════════════════════════════ -->
    <script>
        (function () {
            /* ── Config ── */
            const API = '../pagination/dms-data-grid.php';

            /* ── Chart instances (kept so we can destroy on Reset) ── */
            let initData  = null;
            let pieChart  = null;
            let lineChart = null;
            let colChart  = null;

            /* ── DOM helpers ── */
            const $ = id => document.getElementById(id);

            /* Only elements that actually exist in the HTML.
               sel-state / sel-bsi / sel-engtype removed — those selects
               don't exist in the markup, so referencing them threw
               "Cannot read properties of null" and killed every line
               after it, including loadInit(). */
            const selZone    = $('sel-zone');
            const selProduct = $('sel-product');
            const btnApply   = $('btn-apply');
            const btnReset   = $('btn-reset');
            const dbContent  = $('db-content');
            const emptyState = $('empty-state');
            const skeleton   = $('flt-skeleton');
            const fltBody    = $('flt-body');
            const date_rng   = $('date_rng');

            /* ════════════════════════════════════════════════════
               1. INIT — page load → ?init fetch → populate filters
               Actual API shape:
               { "product":[{vendor_code, vendor_part_name}, ...],
                 "location":[{location_code, locationname}, ...] }
               ════════════════════════════════════════════════════ */
            async function loadInit() {
                try {
                    const res  = await fetch(`${API}?init`);
                    const data = await res.json();
                    initData   = data;
                    populateFilters(data);
                    skeleton.style.display = 'none';
                    fltBody.style.display  = 'block';
                } catch (err) {
                    skeleton.innerHTML =
                        '<p style="font-size:12px;color:#dc2626;padding:8px 0">Filter load failed. Please refresh the page.</p>';
                    console.error('Init fetch error:', err);
                }
            }

            function populateFilters(data) {
                fillSelect(selZone,    data.location, 'locationname',     'location_code', 'All Locations');
                fillSelect(selProduct, data.product,  'vendor_part_name', 'vendor_code',   'All Products');
            }

            function fillSelect(sel, arr, labelKey, valueKey, placeholder) {
                if (!sel) return;
                sel.innerHTML = `<option value="">${placeholder}</option>`;
                (arr || []).forEach(item => {
                    const opt = document.createElement('option');
                    opt.value       = item[valueKey];
                    opt.textContent = item[labelKey];
                    sel.appendChild(opt);
                });
            }

            /* ════════════════════════════════════════════════════
               2. APPLY FILTERS → fetch ?dashboard=1
               PHP returns: { kpi, aging:{rows,grand_total}, pie,
                              iqc, trend, column }
               ════════════════════════════════════════════════════ */
            btnApply.addEventListener('click', async function () {
                btnApply.disabled    = true;
                btnApply.textContent = 'Loading…';

                const params = new URLSearchParams({
                    dashboard : 1,
                    zone      : selZone.value,
                    product   : selProduct.value,
                    daterange : date_rng.value
                });

                try {
                    const res = await fetch(`${API}?${params}`);

                    if (!res.ok) throw new Error(`HTTP ${res.status}`);

                    const data = await res.json();

                    /* ── Show dashboard ── */
                    emptyState.style.display = 'none';
                    dbContent.classList.add('visible');

                    renderKPIs(data.kpi      || {});
                    renderAging(data.aging   || { rows: [], grand_total: {} });
                    renderPie(data.pie       || []);
                    renderIQC(data.iqc       || []);
                    renderLine(data.trend    || {});
                    renderColumn(data.column || {});

                    $('last-updated').textContent =
                        new Date().toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });

                    /* Reflow after container becomes visible */
                    setTimeout(() => {
                        [pieChart, lineChart, colChart].forEach(c => c && c.reflow());
                    }, 120);

                } catch (err) {
                    console.error('Dashboard fetch error:', err);
                    alert('Dashboard data load failed. Check console for details.');
                } finally {
                    btnApply.disabled  = false;
                    btnApply.innerHTML =
                        `<svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707
                            L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017
                            21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                 </svg> Apply Filters`;
                }
            });

            /* ════════════════════════════════════════════════════
               3. RESET — hide dashboard, restore defaults
               ════════════════════════════════════════════════════ */
            btnReset.addEventListener('click', function () {
                /* Reset selects + date range */
                [selZone, selProduct].forEach(s => { if (s) s.selectedIndex = 0; });
                if (date_rng) date_rng.value = '';

                /* Hide dashboard content */
                dbContent.classList.remove('visible');
                emptyState.style.display = '';

                /* Destroy Highcharts instances */
                [pieChart, lineChart, colChart].forEach(c => { if (c) c.destroy(); });
                pieChart = lineChart = colChart = null;
                ['pie-chart-container', 'multi-line', 'column_chart']
                    .forEach(id => { const el = $(id); if (el) el.innerHTML = ''; });

                /* Reset KPI values */
                ['kpi1','kpi2','kpi3','kpi4','kpi5'].forEach(id => {
                    const el = $(id); if (el) el.textContent = '—';
                });
                const lu = $('last-updated'); if (lu) lu.textContent = '—';

                /* Reset aging table to dashes */
                const tbody = $('aging-tbody');
                if (tbody) {
                    const PILLS  = ['b0','b2','b5','b14'];
                    const LABELS = ['0–30 days','31–60 days','61–90 days','&gt;90 days'];
                    tbody.innerHTML = PILLS.map((p, i) => `
                <tr>
                    <td><span class="bucket-pill ${p}">${LABELS[i]}</span></td>
                    <td>—</td><td>—</td><td>—</td>
                    <td style="font-weight:700">—</td><td>—</td>
                </tr>`).join('');
                }
                const tfoot = $('aging-foot');
                if (tfoot) tfoot.innerHTML =
                    '<td>Grand Total</td><td>—</td><td>—</td><td>—</td><td>—</td><td>100%</td>';

                /* Reset IQC bars */
                for (let n = 1; n <= 5; n++) {
                    const s = $(`s${n}`); const pb = $(`pb${n}`); const sp = $(`sp${n}`);
                    if (s)  { s.childNodes[0] && (s.childNodes[0].textContent = '— '); }
                    if (sp) sp.textContent = '';
                    if (pb) pb.style.width = '0%';
                }
                const tot = $('iqc-total'); if (tot) tot.textContent = '—';
            });

            /* ════════════════════════════════════════════════════
               RENDERERS
               ════════════════════════════════════════════════════ */

            /* KPI Cards
               PHP: { total_planned, total_ready, stock_storage, total_delay, avg_aging } */
            function renderKPIs(kpi) {
                const map = {
                    kpi1: kpi.total_planned ?? '—',
                    kpi2: kpi.total_ready   ?? '—',
                    kpi3: kpi.stock_storage ?? '—',
                    kpi4: kpi.total_delay   ?? '—',
                    kpi5: kpi.avg_aging     ?? '—',
                };
                Object.entries(map).forEach(([id, val]) => {
                    const el = $(id); if (el) el.textContent = val;
                });
            }

            /* Aging Table
               PHP sends: { rows:[{bucket,P1,P2,P3,total,pct},...], grand_total:{P1,P2,P3,total,pct} } */
            function renderAging(aging) {
                const rows  = aging.rows        || [];
                const grand = aging.grand_total || {};

                const PILLS  = ['b0', 'b2', 'b5', 'b14'];
                const tbody  = $('aging-tbody');
                const tfoot  = $('aging-foot');
                if (!tbody) return;

                tbody.innerHTML = rows.map((r, i) => `
            <tr>
                <td><span class="bucket-pill ${PILLS[i] || 'b0'}">${r.bucket || '—'}</span></td>
                <td>${r.P1    ?? '—'}</td>
                <td>${r.P2    ?? '—'}</td>
                <td>${r.P3    ?? '—'}</td>
                <td style="font-weight:700">${r.total ?? '—'}</td>
                <td>${r.pct != null ? r.pct + '%' : '—'}</td>
            </tr>`).join('');

                if (tfoot) tfoot.innerHTML = `
            <td>Grand Total</td>
            <td>${grand.P1    ?? '—'}</td>
            <td>${grand.P2    ?? '—'}</td>
            <td>${grand.P3    ?? '—'}</td>
            <td>${grand.total ?? '—'}</td>
            <td>${grand.pct != null ? grand.pct + '%' : '100%'}</td>`;
            }

            /* Pie Chart
               PHP: array of { name, y, color } — directly Highcharts-compatible */
            function renderPie(pieData) {
                if (pieChart) { pieChart.destroy(); pieChart = null; }

                const series = pieData.length ? pieData : [
                    { name: 'No Data', y: 100, color: '#e5e7eb' }
                ];

                pieChart = Highcharts.chart('pie-chart-container', {
                    chart: {
                        type: 'pie', height: 320,
                        margin: [10, 10, 30, 10],
                        style: { fontFamily: 'inherit' }
                    },
                    title: { text: null },
                    credits: { enabled: false },
                    tooltip: {
                        headerFormat: '',
                        pointFormat: '<span style="color:{point.color}">●</span> ' +
                            '<b>{point.name}</b>: {point.percentage:.1f}%'
                    },
                    accessibility: { point: { valueSuffix: '%' } },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true, borderWidth: 2,
                            cursor: 'pointer', size: '80%',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b><br>{point.percentage:.1f}%',
                                distance: 18,
                                style: { fontSize: '11px', fontWeight: '600' }
                            },
                            showInLegend: true
                        }
                    },
                    legend: {
                        enabled: true, layout: 'horizontal',
                        align: 'center', verticalAlign: 'bottom',
                        itemStyle: { fontSize: '11px', fontWeight: '500', color: '#374151' }
                    },
                    series: [{ name: 'Share', animation: { duration: 900 }, colorByPoint: true, data: series }]
                });
            }

            /* IQC Priorities
               PHP: [{ priority:"P1", count:45 }, ...]  sorted P1→P5 */
            function renderIQC(iqcData) {
                if (!iqcData.length) return;

                const total = iqcData.reduce((s, r) => s + (+r.count || 0), 0);
                iqcData.forEach((r, i) => {
                    const n   = i + 1;
                    const cnt = +r.count || 0;
                    const pct = total ? Math.round(cnt / total * 100) : 0;
                    const elCount = $(`s${n}`);
                    const elBar   = $(`pb${n}`);
                    const elPct   = $(`sp${n}`);
                    if (elCount) {
                        if (elCount.childNodes[0]) elCount.childNodes[0].textContent = cnt + ' ';
                    }
                    if (elPct) elPct.textContent = `(${pct}%)`;
                    if (elBar) elBar.style.width  = pct + '%';
                });
                const tot = $('iqc-total'); if (tot) tot.textContent = total;
            }

            /* Multi-line Trend Chart
               PHP key: "trend"  →  { actual[], avg7[], target[], startDate }  */
            function renderLine(trend) {
                const actual = trend.actual    || [990,652,965,1048,939,1012,2089,1995,1123,1302,2289,2115,1723,1462,1889,1812,1427,1312,1156,1305,958,984,1032,1099,1200,1345,1100,876,920,1050];
                const avg7   = trend.avg7      || actual.map(v => Math.round(v * 0.9));
                const target = trend.target    || Array(actual.length).fill(800);
                const start  = trend.startDate
                    ? new Date(trend.startDate).getTime()
                    : Date.UTC(2024, 0, 1);

                if (lineChart) { lineChart.destroy(); lineChart = null; }

                lineChart = Highcharts.chart('multi-line', {
                    chart: {
                        type: 'line', height: 320,
                        style: { fontFamily: 'inherit' },
                        plotBorderColor: '#e5e7eb', plotBorderWidth: 1,
                        spacingTop: 4, spacingBottom: 4
                    },
                    title: {
                        text: 'Cost Complaint — Daily Trend', align: 'left',
                        style: { fontSize: '13px', fontWeight: '700', color: '#1f2937' }
                    },
                    credits: { enabled: false },
                    xAxis: {
                        type: 'datetime',
                        crosshair: { width: 1, color: '#e5e7eb' },
                        dateTimeLabelFormats: { day: '%d %b', week: '%d %b' },
                        lineWidth: 0, tickLength: 4, tickColor: '#e5e7eb',
                        labels: { style: { fontSize: '10px', color: '#9ca3af' } }
                    },
                    yAxis: {
                        title: { text: 'Complaints', style: { fontSize: '11px', color: '#9ca3af' } },
                        gridLineDashStyle: 'Dot', gridLineColor: '#f3f4f6',
                        labels: { style: { fontSize: '10px', color: '#9ca3af' } }
                    },
                    legend: {
                        enabled: true, align: 'right', verticalAlign: 'top', layout: 'horizontal',
                        itemStyle: { fontSize: '11px', fontWeight: '500', color: '#374151' },
                        symbolRadius: 3, symbolWidth: 16
                    },
                    tooltip: {
                        shared: true, useHTML: true,
                        backgroundColor: '#fff', borderColor: '#e5e7eb', borderRadius: 8, shadow: true,
                        headerFormat: '<div style="font-size:11px;color:#6b7280;margin-bottom:6px">{point.key:%d %b %Y}</div>',
                        pointFormat:
                            '<div style="display:flex;justify-content:space-between;gap:16px;font-size:11px;padding:2px 0">' +
                            '<span><span style="color:{series.color}">●</span> {series.name}</span>' +
                            '<b>{point.y}</b></div>',
                        footerFormat: ''
                    },
                    plotOptions: {
                        series: {
                            pointStart: start,
                            pointInterval: 24 * 3600 * 1000,
                            marker: { enabled: false, radius: 4, symbol: 'circle' },
                            states: { hover: { lineWidthPlus: 1 } }
                        }
                    },
                    series: [
                        { name: 'Actual',    color: '#2563eb', lineWidth: 2.5, data: actual },
                        { name: '7-Day Avg', color: '#8b5cf6', lineWidth: 1.8, dashStyle: 'ShortDash', data: avg7 },
                        { name: 'Target',    color: '#10b981', lineWidth: 1.5, dashStyle: 'ShortDot',  data: target }
                    ]
                });
            }

            /* Column Chart
               PHP key: "column"  →  { categories[], typeA[], typeB[] }  */
            function renderColumn(col) {
                const categories = col.categories || ['P1','P2','P3','P4','P5','P6'];
                const typeA      = col.typeA      || [387749, 280000, 129000, 64300, 54000, 34300];
                const typeB      = col.typeB      || [45321, 140000, 10000, 140500, 19500, 113500];

                if (colChart) { colChart.destroy(); colChart = null; }

                colChart = Highcharts.chart('column_chart', {
                    chart: {
                        type: 'column', height: 320,
                        style: { fontFamily: 'inherit' },
                        spacingTop: 4, spacingBottom: 4
                    },
                    title: {
                        text: 'Pending by Product (QTY)', align: 'left',
                        style: { fontSize: '13px', fontWeight: '700', color: '#1f2937' }
                    },
                    credits: { enabled: false },
                    xAxis: {
                        categories,
                        crosshair: { width: 1, color: '#e5e7eb' },
                        lineColor: '#e5e7eb', tickColor: '#e5e7eb',
                        labels: { style: { fontSize: '11px', color: '#6b7280', fontWeight: '500' } }
                    },
                    yAxis: {
                        min: 0,
                        title: { text: 'Quantity', style: { fontSize: '11px', color: '#9ca3af' } },
                        gridLineDashStyle: 'Dot', gridLineColor: '#f3f4f6',
                        labels: { style: { fontSize: '10px', color: '#9ca3af' } }
                    },
                    legend: {
                        enabled: true,
                        itemStyle: { fontSize: '11px', fontWeight: '500', color: '#374151' },
                        symbolRadius: 3
                    },
                    tooltip: {
                        shared: true, useHTML: true,
                        backgroundColor: '#fff', borderColor: '#e5e7eb', borderRadius: 8,
                        headerFormat: '<div style="font-size:11px;color:#6b7280;margin-bottom:6px">{point.key}</div>',
                        pointFormat:
                            '<div style="display:flex;justify-content:space-between;gap:16px;font-size:11px;padding:2px 0">' +
                            '<span><span style="color:{series.color}">■</span> {series.name}</span>' +
                            '<b>{point.y:,.0f} units</b></div>',
                        footerFormat: ''
                    },
                    plotOptions: {
                        column: { pointPadding: 0.1, borderWidth: 0, borderRadius: 4, groupPadding: 0.12 }
                    },
                    series: [
                        { name: 'Type A', color: '#3b82f6', data: typeA },
                        { name: 'Type B', color: '#10b981', data: typeB }
                    ]
                });
            }

            /* ── Resize reflow ── */
            let _rt;
            window.addEventListener('resize', () => {
                clearTimeout(_rt);
                _rt = setTimeout(() => {
                    [pieChart, lineChart, colChart].forEach(c => c && c.reflow());
                }, 150);
            });

            /* ── Boot ── */
            loadInit();
        })();

    </script>
    </body>
    </html>