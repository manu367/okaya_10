<?php
require_once("../includes/config.php");
global $link1;
$arrstate   = getAccessState($_SESSION['userid'], $link1);
$access_brand = getAccessBrand($_SESSION['userid'], $link1);
$loadbsi    = getAllBSI_1($link1);

/* ── helpers ─────────────────────────────────────────────── */
function getAllBSI_1($link){
    $bsi_user = [];
    $sql = "SELECT * FROM `admin_users`
            WHERE `designation_id`='45' AND status=1";
    $result = mysqli_query($link,$sql);
    while ($row = mysqli_fetch_assoc($result)) $bsi_user[] = $row;
    return $bsi_user;
}

/* ═══════════════════════════════════════════════════════════
   DASHBOARD LOADER CLASS
   ═══════════════════════════════════════════════════════════ */
class DashboardLoader
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /* ── main entry ──────────────────────────────────────── */
    public function bsiLoader(array $condition = []): array
    {
        $data = [];

        /* ---------- build BSI-level WHERE ---------- */
        $bsiCondition = "";

        if (!empty($condition['state']) && $condition['state'] !== 'all') {
            $state        = (int)$condition['state'];
            $bsiCondition .= " AND stateid = '$state' ";
        }
        if (!empty($condition['bsi']) && $condition['bsi'] !== 'all') {
            $bsi          = (int)$condition['bsi'];
            $bsiCondition .= " AND id = '$bsi' ";
        }

        $allBSI = $this->getAllBSI($bsiCondition);
        if (empty($allBSI)) return [];

        foreach ($allBSI as $bsi) {
            $bsiId   = $bsi['id'];
            $bsiName = $bsi['username'];

            /* zone lookup for this BSI's state */
            $zoneName = $this->getZoneName((int)$bsi['stateid']);

            $enginners   = $this->fetchBSIEngineers($bsiId);
            $engineerRows = [];
            $bsiTotals   = $this->defaultStatusBucket();

            foreach ($enginners as $eng) {
                $engId   = $eng['userloginid'];
                $engName = $eng['locusername'];

                /* apply engineer filter */
                if (!empty($condition['enginner']) &&
                    $condition['enginner'] !== 'all' &&
                    $condition['enginner'] != $engId) {
                    continue;
                }

                $jobs = $this->fetchEngineerJobs($engId, $condition);
                if (empty($jobs)) continue;

                $engineerData = $this->defaultStatusBucket();

                foreach ($jobs as $job) {
                    $status = $job['status'];
                    $aging  = (int)$job['aging_days'];
                    $bucket = $this->calculateBucket($aging);
                    if (!$bucket) continue;

                    switch ($status) {
                        case '2':  /* Assigned */
                            $engineerData['assigned'][$bucket]++;
                            $engineerData['assigned_total']++;
                            break;
                        case '3':  /* PNA */
                            $engineerData['pna'][$bucket]++;
                            $engineerData['pna_total']++;
                            break;
                        case '7':  /* WIP */
                            $engineerData['wip'][$bucket]++;
                            $engineerData['wip_total']++;
                            break;
                        case '81': /* Replacement */
                            $engineerData['replacement'][$bucket]++;
                            $engineerData['replacement_total']++;
                            break;
                        case '1':  /* Unassigned */
                            $engineerData['unassigned'][$bucket]++;
                            $engineerData['unassigned_total']++;
                            break;
                    }
                }

                $engineerData['grand_total'] =
                    $engineerData['assigned_total']
                    + $engineerData['pna_total']
                    + $engineerData['wip_total']
                    + $engineerData['replacement_total']
                    + $engineerData['unassigned_total'];

                $this->mergeTotals($bsiTotals, $engineerData);

                $engineerRows[] = [
                    'engineer_id'   => $engId,
                    'engineer_name' => $engName,
                    'data'          => $engineerData,
                ];
            }

            /* skip BSI rows that have no engineer data */
            if (empty($engineerRows)) continue;

            $data[] = [
                'bsi_id'    => $bsiId,
                'bsi_name'  => $bsiName,
                'zone_name' => $zoneName,
                'engineers' => $engineerRows,
                'totals'    => $bsiTotals,
            ];
        }

        return $data;
    }

    /* ── zone name via state ────────────────────────────── */
    private function getZoneName(int $stateId): string
    {
        if (!$stateId) return '';
        $sql    = "SELECT zm.zonename
                   FROM zone_master zm
                   JOIN state_master sm ON sm.zoneid = zm.zoneid
                   WHERE sm.stateid = '$stateId'
                   LIMIT 1";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return '';
        $row = mysqli_fetch_assoc($result);
        return $row ? $row['zonename'] : '';
    }

    /* ── fetch all BSI users matching condition ─────────── */
    private function getAllBSI(string $condition = ''): array
    {
        $sql    = "SELECT * FROM admin_users
                   WHERE status = '1'
                   AND designation_id = '45'
                   $condition
                   ORDER BY username";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return [];
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }

    /* ── engineers mapped to a BSI ──────────────────────── */
    public function fetchBSIEngineers(int $bsiId): array
    {
        $sql    = "SELECT *
                   FROM locationuser_master
                   WHERE mapped_bsi = '$bsiId'
                   AND statusid = '1'
                   ORDER BY locusername";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return [];
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }

    /* ── jobs for one engineer ───────────────────────────── */
    public function fetchEngineerJobs(string $engId, array $condition = []): array
    {
        $where = "";

        /* Date filter */
        if (!empty($condition['date_range'])) {
            $parts = explode(' - ', $condition['date_range']);
            if (count($parts) === 2) {
                $from = mysqli_real_escape_string($this->connection, trim($parts[0]));
                $to   = mysqli_real_escape_string($this->connection, trim($parts[1]));
                /* support dd-Mon-yyyy format from the UI datepicker */
                $where .= " AND DATE(open_date) BETWEEN
                              STR_TO_DATE('$from','%d-%b-%Y')
                              AND STR_TO_DATE('$to','%d-%b-%Y') ";
            } elseif (count($parts) === 1) {
                $date  = mysqli_real_escape_string($this->connection, trim($parts[0]));
                $where .= " AND DATE(open_date) = STR_TO_DATE('$date','%d-%b-%Y') ";
            }
        }

        /* State filter */
        if (!empty($condition['state']) && $condition['state'] !== 'all') {
            $state = (int)$condition['state'];
            $where .= " AND state_id = '$state' ";
        }

        /* ── Segment filter (FIXED: conditions were swapped) ── */
        if (!empty($condition['segment']) && $condition['segment'] !== 'all') {
            $segment = $condition['segment'];
            if ($segment === 'Battery') {
                /* Battery = everything except Inverter, Solar, and excluded ids */
                $where .= " AND product_id NOT IN ('1','6','10','11','12','14') ";
            } elseif ($segment === 'Inverter') {
                $where .= " AND product_id = '1' ";
            } elseif ($segment === 'Solar') {
                $where .= " AND product_id IN ('6','10','11','12') ";
            }
        }

        /* Sub-segment overrides segment when present */
        if (!empty($condition['sub_segment']) && $condition['sub_segment'] !== 'all') {
            $subSegment = (int)$condition['sub_segment'];
            $where     .= " AND product_id = '$subSegment' ";
        }

        $sql    = "SELECT *,
                          DATEDIFF(NOW(), open_date) AS aging_days
                   FROM jobsheet_data
                   WHERE eng_id = '$engId'
                   AND status IN ('1','2','3','7','81')
                   $where";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return [];
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) $data[] = $row;
        return $data;
    }

    /* ── aging bucket logic ─────────────────────────────── */
    public function calculateBucket(int $aging)
    {
        if ($aging <= 0)                        return false;
        if ($aging <= 5)                        return $aging;  /* 1,2,3,4,5 */
        if ($aging >= 6  && $aging <= 10)       return 6;       /* "6–10 days" */
        if ($aging >= 11)                       return 11;      /* "11+ days"  */
        return false;
    }

    /* ── default empty bucket structure ─────────────────── */
    public function defaultStatusBucket(): array
    {
        $buckets = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 11=>0];
        return [
            'assigned'    => $buckets,
            'replacement' => $buckets,
            'wip'         => $buckets,
            'pna'         => $buckets,
            'unassigned'  => $buckets,

            'assigned_total'    => 0,
            'replacement_total' => 0,
            'wip_total'         => 0,
            'pna_total'         => 0,
            'unassigned_total'  => 0,
            'grand_total'       => 0,
        ];
    }

    /* ── merge one engineer row into BSI / grand totals ─── */
    public function mergeTotals(array &$total, array $eng): void
    {
        foreach (['assigned','replacement','wip','pna','unassigned'] as $type) {
            foreach ($eng[$type] as $bucket => $count) {
                $total[$type][$bucket] += $count;
            }
        }
        foreach (['assigned_total','replacement_total','wip_total',
                     'pna_total','unassigned_total','grand_total'] as $key) {
            $total[$key] += $eng[$key];
        }
    }
}

/* ── init ────────────────────────────────────────────────── */
$dashboard = new DashboardLoader($link1);
$show      = ['status' => false, 'data' => null];

/* ── AJAX request → return JSON and exit ────────────────── */
if (isset($_REQUEST['ajax_search'])) {
    $condition = [
        'date_range'  => $_REQUEST['date_range']  ?? '',
        'zone'        => $_REQUEST['zone']         ?? '',
        'state'       => $_REQUEST['state']        ?? '',
        'bsi'         => $_REQUEST['bsi']          ?? '',
        'enginner'    => $_REQUEST['enginner']     ?? '',
        'segment'     => $_REQUEST['segment']      ?? '',
        'sub_segment' => $_REQUEST['sub_segment']  ?? '',
    ];
    header('Content-Type: application/json');
    echo json_encode($dashboard->bsiLoader($condition));
    exit();
}

/* ── Regular (non-AJAX) search ──────────────────────────── */
if (isset($_REQUEST['search_d'])) {
    $condition = [
        'date_range'  => $_REQUEST['date_range']  ?? '',
        'zone'        => $_REQUEST['zone']         ?? '',
        'state'       => $_REQUEST['state']        ?? '',
        'bsi'         => $_REQUEST['bsi']          ?? '',
        'enginner'    => $_REQUEST['enginner']     ?? '',
        'segment'     => $_REQUEST['segment']      ?? '',
        'sub_segment' => $_REQUEST['sub_segment']  ?? '',
    ];
    $show['data']   = $dashboard->bsiLoader($condition);
    $show['status'] = true;
}

/* ── arrstate as comma list for SQL ─────────────────────── */
if (is_array($arrstate)) {
    $arrstate = implode(',', array_map('intval', $arrstate));
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= siteTitle ?></title>
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <script src="../js/jquery.min.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/dashboard2.css">
    <style>
        /* ── header ─────────────────────────── */
        .crm-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:10px 16px;
            background:#fff;
            border-bottom:1px solid #e5e7eb;
        }
        .crm-header h1{
            font-size:16px;
            font-weight:600;
            color:#1e3a5f;
            margin:0;
        }
        .btn-export{
            background:#1e3a5f;
            color:#fff;
            border:none;
            padding:6px 14px;
            border-radius:4px;
            cursor:pointer;
            font-size:13px;
        }
        /* ── filter bar ──────────────────────── */
        .filter-bar{
            display:flex;
            flex-wrap:wrap;
            gap:10px;
            padding:10px 16px;
            background:#f9fafb;
            border-bottom:1px solid #e5e7eb;
            align-items:flex-end;
        }
        .filter-group{
            display:flex;
            flex-direction:column;
            gap:3px;
        }
        .filter-group label{
            font-size:11px;
            font-weight:600;
            color:#374151;
            text-transform:uppercase;
            letter-spacing:.4px;
        }
        .filter-group input,
        .filter-group select{
            border:1px solid #d1d5db;
            border-radius:4px;
            padding:5px 8px;
            font-size:13px;
            color:#111827;
            background:#fff;
            min-width:130px;
        }
        .filter-actions{
            display:flex;
            gap:8px;
            align-items:flex-end;
        }
        .btn-search{
            background:#1563c5;
            color:#fff;
            border:none;
            padding:6px 18px;
            border-radius:4px;
            cursor:pointer;
            font-size:13px;
            font-weight:600;
        }
        .btn-reset{
            background:#fff;
            color:#374151;
            border:1px solid #d1d5db;
            padding:6px 14px;
            border-radius:4px;
            cursor:pointer;
            font-size:13px;
        }
        /* ── table section ───────────────────── */
        .table-section{
            padding:12px 16px;
        }
        .table-title{
            text-align:center;
            font-size:13px;
            font-weight:700;
            color:#1e3a5f;
            padding:8px 0;
            letter-spacing:.5px;
        }
        .table-wrapper{
            overflow-x:auto;
        }
        table{
            border-collapse:collapse;
            width:100%;
            font-size:11px;
            white-space:nowrap;
        }
        th,td{
            border:1px solid #d1d5db;
            padding:4px 6px;
            text-align:center;
        }
        /* ── header rows ─────────────────────── */
        .thead-main th{
            background:#1e3a5f;
            color:#fff;
            font-weight:700;
            font-size:11px;
        }
        .th-assigned{ background:#d97706 !important; }
        .th-replace { background:#b45309 !important; }
        .th-wip     { background:#15803d !important; }
        .th-pna     { background:#7c3aed !important; }
        .th-grand   { background:#1e3a5f !important; }

        .thead-sub th{ font-size:10px; font-weight:600; }
        .sub-assigned{ background:#fef3c7; color:#92400e; }
        .sub-replace { background:#fde68a; color:#78350f; }
        .sub-wip     { background:#d1fae5; color:#065f46; }
        .sub-pna     { background:#ede9fe; color:#4c1d95; }

        /* ── data rows ───────────────────────── */
        .row-bsi td{
            background:#dbeafe;
            font-weight:600;
            color:#1e3a5f;
        }
        .zone-label{
            font-weight:700;
            font-size:11px;
            color:#1e3a5f;
            vertical-align:middle;
        }
        .bsi-cell{
            display:flex;
            align-items:center;
            gap:5px;
            text-align:left;
        }
        .expand-icon{
            cursor:pointer;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:16px;
            height:16px;
            border:1px solid #3b82f6;
            border-radius:3px;
            font-size:12px;
            font-weight:700;
            color:#1d4ed8;
            background:#eff6ff;
            user-select:none;
            flex-shrink:0;
        }
        .row-engineer td{
            background:#f9fafb;
            color:#374151;
            font-size:10.5px;
        }
        .eng-cell{
            text-align:left;
            padding-left:24px !important;
            color:#6b7280;
            font-size:10px;
        }
        .row-zone-total td{
            background:#bfdbfe;
            font-weight:700;
            color:#1e3a5f;
            font-size:11px;
        }
        .row-grand-total td{
            background:#1e3a5f;
            color:#fff;
            font-weight:700;
            font-size:11.5px;
        }
        /* ── zero dimming ────────────────────── */
        .zero{ color:#cbd5e1; }
        /* ── note ────────────────────────────── */
        .table-note{
            font-size:11px;
            color:#6b7280;
            padding:6px 16px;
        }
        .table-note span{ color:#1563c5; font-style:italic; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row content">
        <?php include("../includes/leftnav2.php"); ?>

        <div class="<?= $screenwidth ?> tab-pane fade in active" id="home">

            <!-- HEADER -->
            <div class="crm-header">
                <h1>CRM Dashboard &mdash; Zone &amp; BSI Wise Pendency Summary (Details)</h1>
                <div class="header-right">
                    <span style="font-size:12px;color:#6b7280;margin-right:12px;">
                        Date As On : <?= date('jth M\'y - H:i') ?>
                    </span>
                    <button class="btn-export" onclick="exportTable()">&#8659; Export</button>
                </div>
            </div>

            <!-- FILTER BAR -->
            <form id="form1" action="" method="post">

                <div class="filter-bar">

                    <!-- Date Range -->
                    <div class="filter-group">
                        <label>Date Range</label>
                        <input type="date"
                               name="date_range"
                               id="date_range"
                               placeholder="dd-Mon-yyyy - dd-Mon-yyyy"
                               value="<?= htmlspecialchars($_REQUEST['date_range'] ?? '') ?>"
                               style="min-width:190px;">
                    </div>

                    <!-- Zone -->
                    <?php $zone          = $_REQUEST['zone']  ?? ''; ?>
                    <div class="filter-group">
                        <label>Zone</label>
                        <select name="zone" onchange="changeZone(this.value)">
                            <option value="">All</option>
                            <?php
                            $res_zone = mysqli_query($link1,
                                "SELECT zoneid, zonename FROM zone_master ORDER BY zonename");
                            while ($rz = mysqli_fetch_assoc($res_zone)) {
                                $sel = ($rz['zoneid'] == $zone) ? 'selected' : '';
                                echo "<option value=\"{$rz['zoneid']}\" $sel>"
                                    . htmlspecialchars($rz['zonename'])
                                    . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- State -->
                    <?php $selectedState = $_REQUEST['state'] ?? ''; ?>
                    <div class="filter-group">
                        <label>State</label>
                        <select name="state" onchange="this.form.submit()">
                            <option value="all">All</option>
                            <?php
                            $zoneCond = '';
                            if (!empty($zone)) {
                                $zoneCond = " AND zoneid = '" . (int)$zone . "' ";
                            }
                            $stateResult = mysqli_query($link1,
                                "SELECT stateid, state
                                 FROM state_master
                                 WHERE stateid IN ($arrstate)
                                 $zoneCond
                                 ORDER BY state");
                            while ($si = mysqli_fetch_assoc($stateResult)) {
                                $sel = ($selectedState == $si['stateid']) ? 'selected' : '';
                                echo "<option value=\"{$si['stateid']}\" $sel>"
                                    . htmlspecialchars($si['state'])
                                    . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- BSI -->
                    <?php
                    $bsiSel    = $_REQUEST['bsi']    ?? '';
                    $stateSel  = $_REQUEST['state']  ?? '';
                    ?>
                    <div class="filter-group">
                        <label>BSI</label>
                        <select name="bsi" onchange="this.form.submit()">
                            <option value="all" <?= (empty($bsiSel)||$bsiSel==='all')?'selected':'' ?>>All</option>
                            <?php
                            $bsiSQL = "SELECT id, username FROM admin_users
                                       WHERE designation_id = 45 AND status = '1'";
                            if (!empty($stateSel) && $stateSel !== 'all' && $stateSel !== '-1') {
                                $bsiSQL .= " AND stateid = '" . (int)$stateSel . "'";
                            }
                            $bsiSQL .= " ORDER BY username";
                            $bsiRes  = mysqli_query($link1, $bsiSQL);
                            while ($br = mysqli_fetch_assoc($bsiRes)) {
                                $sel = ($bsiSel == $br['id']) ? 'selected' : '';
                                echo "<option value=\"{$br['id']}\" $sel>"
                                    . htmlspecialchars($br['username'])
                                    . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Engineer -->
                    <?php $enginnerSel = $_REQUEST['enginner'] ?? ''; ?>
                    <div class="filter-group">
                        <label>Engineer</label>
                        <select name="enginner" onchange="this.form.submit()">
                            <option value="all">All</option>
                            <?php
                            $engSQL = "SELECT userloginid, locusername
                                       FROM locationuser_master
                                       WHERE statusid='1'";
                            if (!empty($bsiSel) && $bsiSel !== 'all') {
                                $engSQL .= " AND mapped_bsi = '" . (int)$bsiSel . "'";
                            }
                            $engSQL .= " ORDER BY locusername";
                            $engRes  = mysqli_query($link1, $engSQL);
                            while ($er = mysqli_fetch_assoc($engRes)) {
                                $sel = ($enginnerSel == $er['userloginid']) ? 'selected' : '';
                                echo "<option value=\"{$er['userloginid']}\" $sel>"
                                    . htmlspecialchars($er['locusername'])
                                    . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Segment -->
                    <?php $segmentSel = $_REQUEST['segment'] ?? ''; ?>
                    <div class="filter-group">
                        <label>Segment</label>
                        <select name="segment" onchange="this.form.submit()">
                            <option value="all"     <?= $segmentSel===''||$segmentSel==='all'?'selected':'' ?>>All</option>
                            <option value="Battery" <?= $segmentSel==='Battery'?'selected':'' ?>>Battery</option>
                            <option value="Inverter"<?= $segmentSel==='Inverter'?'selected':'' ?>>Inverter</option>
                            <option value="Solar"   <?= $segmentSel==='Solar'?'selected':'' ?>>Solar</option>
                        </select>
                    </div>

                    <!-- Sub-Segment (only when segment selected) -->
                    <?php
                    $subSegSel = $_REQUEST['sub_segment'] ?? '';
                    if (!empty($segmentSel) && $segmentSel !== 'all') {
                        /* FIXED: conditions now match the segment correctly */
                        $prodCond = "";
                        if ($segmentSel === 'Battery') {
                            $prodCond = "AND product_id NOT IN ('1','6','10','11','12','14')";
                        } elseif ($segmentSel === 'Inverter') {
                            $prodCond = "AND product_id = '1'";
                        } elseif ($segmentSel === 'Solar') {
                            $prodCond = "AND product_id IN ('6','10','11','12')";
                        }
                        ?>
                        <div class="filter-group">
                            <label>Sub-Segment</label>
                            <select name="sub_segment" onchange="this.form.submit()">
                                <option value="all">All</option>
                                <?php
                                $prodRes = mysqli_query($link1,
                                    "SELECT product_id, product_name
                                     FROM product_master
                                     WHERE status = '1' $prodCond
                                     ORDER BY product_name");
                                if ($prodRes && mysqli_num_rows($prodRes) > 0) {
                                    while ($pr = mysqli_fetch_assoc($prodRes)) {
                                        $sel = ($subSegSel == $pr['product_id']) ? 'selected' : '';
                                        echo "<option value=\"{$pr['product_id']}\" $sel>"
                                            . htmlspecialchars($pr['product_name'])
                                            . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No products</option>";
                                }
                                ?>
                            </select>
                        </div>
                    <?php } ?>

                    <div class="filter-actions">
                        <button type="submit" name="search_d" class="btn-search">Search</button>
                        <button type="button" class="btn-reset"
                                onclick="window.location.href=window.location.pathname">
                            &#8635; Reset
                        </button>
                    </div>
                </div><!-- /filter-bar -->
            </form>

            <!-- ═══ TABLE SECTION ═══════════════════════════════════ -->
            <?php if ($show['status']) : ?>
                <div id="layout_c" class="table-section">
                    <div class="table-title">ZONE &amp; BSI WISE PENDENCY SUMMARY (DETAILS)</div>

                    <div class="table-wrapper">
                        <table id="pendencyTable">
                            <thead>
                            <tr class="thead-main">
                                <th rowspan="2" style="min-width:70px;">Zone</th>
                                <th rowspan="2" style="min-width:130px;">BSI Name</th>
                                <th class="th-assigned" colspan="8">Assigned (Ageing in Days)</th>
                                <th class="th-replace"  colspan="8">Replacement Request (Ageing in Days)</th>
                                <th class="th-wip"      colspan="8">Work in Progress (Ageing in Days)</th>
                                <th class="th-pna"      colspan="8">PNA (Ageing in Days)</th>
                                <th class="th-grand"    rowspan="2">Grand<br>Total</th>
                            </tr>
                            <tr class="thead-sub">
                                <?php
                                $cols = ['1 Day','2 Days','3 Days','4 Days','5 Days','6-10 Days','11+ Days'];
                                $groups = [
                                    ['class'=>'sub-assigned','total'=>'Assigned Total'],
                                    ['class'=>'sub-replace', 'total'=>'RR Total'],
                                    ['class'=>'sub-wip',     'total'=>'WIP Total'],
                                    ['class'=>'sub-pna',     'total'=>'PNA Total'],
                                ];
                                foreach ($groups as $g) {
                                    foreach ($cols as $c) {
                                        echo "<th class=\"{$g['class']}\">$c</th>";
                                    }
                                    echo "<th class=\"{$g['class']}\" style=\"font-weight:700;\">{$g['total']}</th>";
                                }
                                ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            /* helper: render 7 bucket cells + total cell */
                            function renderBuckets(array $bucket, int $total, string $boldClass = ''): string {
                                $keys = [1,2,3,4,5,6,11];
                                $out  = '';
                                foreach ($keys as $k) {
                                    $v    = $bucket[$k] ?? 0;
                                    $dim  = $v == 0 ? ' class="zero"' : '';
                                    $out .= "<td$dim>" . ($v ?: '0') . "</td>";
                                }
                                $out .= "<td><b>$total</b></td>";
                                return $out;
                            }

                            $grandTotal  = $dashboard->defaultStatusBucket();
                            $zoneData    = [];   /* zone → aggregated totals */

                            if (!empty($show['data'])) {

                                /* Group BSI rows by zone for zone-total rows */
                                foreach ($show['data'] as $bsiRow) {
                                    $z = $bsiRow['zone_name'] ?: 'N/A';
                                    if (!isset($zoneData[$z])) {
                                        $zoneData[$z] = $dashboard->defaultStatusBucket();
                                    }
                                    $dashboard->mergeTotals($zoneData[$z], $bsiRow['totals']);
                                }

                                /* ── render ── */
                                $lastZone = null;

                                foreach ($show['data'] as $idx => $bsiRow) {
                                    $bsiName   = $bsiRow['bsi_name'];
                                    $bsiTotals = $bsiRow['totals'];
                                    $zoneName  = $bsiRow['zone_name'] ?: 'N/A';

                                    /* zone-total row before new zone starts */
                                    if ($lastZone !== null && $lastZone !== $zoneName) {
                                        $zt = $zoneData[$lastZone];
                                        ?>
                                        <tr class="row-zone-total" data-zone="<?= htmlspecialchars($lastZone) ?>">
                                            <td colspan="2" style="text-align:left;padding-left:8px;">
                                                <?= htmlspecialchars($lastZone) ?> Total
                                            </td>
                                            <?= renderBuckets($zt['assigned'],   $zt['assigned_total']) ?>
                                            <?= renderBuckets($zt['replacement'],$zt['replacement_total']) ?>
                                            <?= renderBuckets($zt['wip'],        $zt['wip_total']) ?>
                                            <?= renderBuckets($zt['pna'],        $zt['pna_total']) ?>
                                            <td><b><?= $zt['grand_total'] ?></b></td>
                                        </tr>
                                        <?php
                                    }
                                    $lastZone = $zoneName;

                                    /* BSI row */
                                    ?>
                                    <tr class="row-bsi" data-bsi="<?= $bsiRow['bsi_id'] ?>">
                                        <td class="zone-label"><?= htmlspecialchars($zoneName) ?></td>
                                        <td>
                                            <div class="bsi-cell">
                                                <span class="expand-icon" title="Expand / Collapse">+</span>
                                                <?= htmlspecialchars($bsiName) ?>
                                            </div>
                                        </td>
                                        <?= renderBuckets($bsiTotals['assigned'],   $bsiTotals['assigned_total']) ?>
                                        <?= renderBuckets($bsiTotals['replacement'],$bsiTotals['replacement_total']) ?>
                                        <?= renderBuckets($bsiTotals['wip'],        $bsiTotals['wip_total']) ?>
                                        <?= renderBuckets($bsiTotals['pna'],        $bsiTotals['pna_total']) ?>
                                        <td><b><?= $bsiTotals['grand_total'] ?></b></td>
                                    </tr>

                                    <?php
                                    /* Engineer rows */
                                    foreach ($bsiRow['engineers'] as $eng) {
                                        $ed = $eng['data'];
                                        ?>
                                        <tr class="row-engineer" data-parent-bsi="<?= $bsiRow['bsi_id'] ?>" style="display:none;">
                                            <td></td>
                                            <td class="eng-cell">
                                                <?= htmlspecialchars($eng['engineer_id']) ?>
                                                &ndash;
                                                <?= htmlspecialchars($eng['engineer_name']) ?>
                                            </td>
                                            <?= renderBuckets($ed['assigned'],   $ed['assigned_total']) ?>
                                            <?= renderBuckets($ed['replacement'],$ed['replacement_total']) ?>
                                            <?= renderBuckets($ed['wip'],        $ed['wip_total']) ?>
                                            <?= renderBuckets($ed['pna'],        $ed['pna_total']) ?>
                                            <td><b><?= $ed['grand_total'] ?></b></td>
                                        </tr>
                                        <?php

                                        $dashboard->mergeTotals($grandTotal, $ed);
                                    }
                                }

                                /* last zone total */
                                if ($lastZone !== null && isset($zoneData[$lastZone])) {
                                    $zt = $zoneData[$lastZone];
                                    ?>
                                    <tr class="row-zone-total" data-zone="<?= htmlspecialchars($lastZone) ?>">
                                        <td colspan="2" style="text-align:left;padding-left:8px;">
                                            <?= htmlspecialchars($lastZone) ?> Total
                                        </td>
                                        <?= renderBuckets($zt['assigned'],   $zt['assigned_total']) ?>
                                        <?= renderBuckets($zt['replacement'],$zt['replacement_total']) ?>
                                        <?= renderBuckets($zt['wip'],        $zt['wip_total']) ?>
                                        <?= renderBuckets($zt['pna'],        $zt['pna_total']) ?>
                                        <td><b><?= $zt['grand_total'] ?></b></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="34" style="text-align:center;padding:20px;color:#6b7280;">No records found for the selected filters.</td></tr>';
                            }
                            ?>

                            <!-- Grand Total Row -->
                            <tr class="row-grand-total">
                                <td colspan="2" style="text-align:left;padding-left:8px;">Grand Total</td>
                                <?= renderBuckets($grandTotal['assigned'],   $grandTotal['assigned_total']) ?>
                                <?= renderBuckets($grandTotal['replacement'],$grandTotal['replacement_total']) ?>
                                <?= renderBuckets($grandTotal['wip'],        $grandTotal['wip_total']) ?>
                                <?= renderBuckets($grandTotal['pna'],        $grandTotal['pna_total']) ?>
                                <td><b><?= $grandTotal['grand_total'] ?></b></td>
                            </tr>

                            </tbody>
                        </table>
                    </div><!-- /table-wrapper -->

                    <p class="table-note">
                        Note: PNA ageing is not applicable in segment
                        <span>'Inverter'</span> and <span>'Solar Products'</span>.
                    </p>
                </div><!-- /#layout_c -->
            <?php endif; ?>

        </div><!-- /main col -->
    </div>
</div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>

<script>
    /* ── Zone change → reset state dropdown, resubmit ── */
    function changeZone(zoneId) {
        document.querySelector('select[name="state"]').value = 'all';
        document.getElementById('form1').submit();
    }

    /* ── Expand / Collapse engineer rows ── */
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.expand-icon').forEach(function (icon) {
            icon.addEventListener('click', function () {
                var bsiRow    = this.closest('tr');
                var bsiId     = bsiRow.dataset.bsi;
                var expanded  = this.textContent.trim() === '−';
                var engRows   = document.querySelectorAll(
                    '.row-engineer[data-parent-bsi="' + bsiId + '"]'
                );
                engRows.forEach(function (r) {
                    r.style.display = expanded ? 'none' : 'table-row';
                });
                this.textContent = expanded ? '+' : '−';
            });
        });

    });

    /* ── Export to CSV ── */
    function exportTable() {
        var table = document.getElementById('pendencyTable');
        if (!table) { alert('No data to export.'); return; }
        var rows  = table.querySelectorAll('tr');
        var csv   = [];
        rows.forEach(function (row) {
            var cells = row.querySelectorAll('th,td');
            var line  = [];
            cells.forEach(function (c) {
                var txt = c.innerText.replace(/"/g, '""').replace(/\n/g, ' ');
                line.push('"' + txt + '"');
            });
            csv.push(line.join(','));
        });
        var blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        var a    = document.createElement('a');
        a.href   = URL.createObjectURL(blob);
        a.download = 'pendency_summary_' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
    }
</script>
</body>
</html>