<?php
/**
 * Workflow of the Application
 * USER REQUEST (HTTP)
 * │
 *
 * $condition = [
 * 'bsi'          => '5',
 * 'zone'         => '2',
 * 'state'        => '10',
 * 'enginner'     => '101',
 * 'segment'      => '1',
 * 'sub_segment'  => '6',
 * 'date_range'   => '30'
 * ]
 * │
 * ▼
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * bsiLoader($condition)
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * │
 * ├─ STEP 1: BSI Condition banao
 * │       $condition['bsi'] = '5'
 * │       $bsiCondition = "AND id = '5'"
 * │
 * ├─ STEP 2: getAllBSIFromtheEnginner($bsiCondition)
 * │       ↓
 * │       SELECT * FROM admin_users
 * │       WHERE status = '1'
 * │       AND designation_id = '45'
 * │       AND id = '5'
 * │       ↓
 * │       Result: [ {sapid: 'B001', name: 'Delhi BSI'} ]
 * │
 * ├─ STEP 3: Har BSI pe loop
 * │       $bsiId   = 'B001'
 * │       $bsiName = 'Delhi BSI'
 * │
 * │   ┌─────────────────────────────────────────┐
 * │   │  fetchBSIEnginners('B001', $condition)  │
 * │   └─────────────────────────────────────────┘
 * │       ↓
 * │       WHERE lum.mapped_bsi = 'B001'
 * │       AND lum.stateid IN (              ← zone filter
 * │           SELECT stateid FROM state_master
 * │           WHERE zoneid = '2'
 * │       )
 * │       AND lum.stateid = '10'            ← state filter
 * │       AND lum.userloginid = '101'       ← engineer filter
 * │       ↓
 * │       Result: [
 * │           {userloginid: '101', locusername: 'Rahul'},
 * │           {userloginid: '102', locusername: 'Amit'},
 * │       ]
 * │
 * ├─ STEP 4: Har Engineer pe loop
 * │
 * │   Engineer: Rahul (101)
 * │   ┌────────────────────────────────────────┐
 * │   │  fetchEngineerJobs('101', $condition)  │
 * │   └────────────────────────────────────────┘
 * │       ↓
 * │       SELECT jd.*, DATEDIFF(NOW(), open_date) as aging_days
 * │       FROM jobsheet_data jd
 * │       WHERE jd.eng_id = '101'
 * │       AND jd.status IN ('1','2','3','7','81')
 * │       AND jd.open_date >= NOW() - INTERVAL 30 DAY   ← date_range
 * │       AND jd.state_id IN (                           ← zone
 * │           SELECT stateid FROM state_master
 * │           WHERE zoneid = '2'
 * │       )
 * │       AND jd.state_id = '10'                        ← state
 * │       AND jd.eng_id IN (                            ← bsi
 * │           SELECT userloginid FROM locationuser_master
 * │           WHERE mapped_bsi = '5'
 * │       )
 * │       AND jd.eng_id = '101'                         ← engineer
 * │       AND jd.product_id IN ('1')                    ← segment=1 (Inverter)
 * │       AND jd.product_id = '6'                       ← sub_segment
 * │       ↓
 * │       Result: [
 * │           {job_id:1, status:'2', aging_days: 3},
 * │           {job_id:2, status:'7', aging_days: 8},
 * │           {job_id:3, status:'1', aging_days: 12},
 * │           {job_id:4, status:'3', aging_days: 1},
 * │           {job_id:5, status:'2', aging_days: 5},
 * │       ]
 * │
 * ├─ STEP 5: defaultStatusBucket() — fresh empty structure
 * │
 * │       $engineerData = [
 * │           'assigned'    => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 11=>0],
 * │           'pna'         => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 11=>0],
 * │           'wip'         => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 11=>0],
 * │           'replacement' => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 11=>0],
 * │           'unassigned'  => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 11=>0],
 * │           'assigned_total'    => 0,
 * │           'pna_total'         => 0,
 * │           'wip_total'         => 0,
 * │           'replacement_total' => 0,
 * │           'unassigned_total'  => 0,
 * │           'grand_total'       => 0
 * │       ]
 * │
 * ├─ STEP 6: Har Job pe loop + calculateBucket()
 * │
 * │       Job 1 → status='2' aging=3  → bucket=3
 * │               engineerData['assigned'][3]++     = 1
 * │               engineerData['assigned_total']++  = 1
 * │
 * │       Job 2 → status='7' aging=8  → bucket=6
 * │               engineerData['wip'][6]++          = 1
 * │               engineerData['wip_total']++       = 1
 * │
 * │       Job 3 → status='1' aging=12 → bucket=11
 * │               engineerData['unassigned'][11]++  = 1
 * │               engineerData['unassigned_total']++= 1
 * │
 * │       Job 4 → status='3' aging=1  → bucket=1
 * │               engineerData['pna'][1]++          = 1
 * │               engineerData['pna_total']++       = 1
 * │
 * │       Job 5 → status='2' aging=5  → bucket=5
 * │               engineerData['assigned'][5]++     = 1
 * │               engineerData['assigned_total']++  = 2
 * │
 * ├─ STEP 7: grand_total calculate karo
 * │
 * │       grand_total = assigned(2) + pna(1) + wip(1)
 * │                   + replacement(0) + unassigned(1)
 * │                   = 5
 * │
 * ├─ STEP 8: mergeTotals($bsiTotals, $engineerData)
 * │
 * │       Rahul ka data → BSI Totals mein ADD hoga
 * │
 * │       bsiTotals['assigned'][3]  += 1   → 1
 * │       bsiTotals['assigned'][5]  += 1   → 1
 * │       bsiTotals['wip'][6]       += 1   → 1
 * │       bsiTotals['unassigned'][11]+= 1  → 1
 * │       bsiTotals['pna'][1]       += 1   → 1
 * │       bsiTotals['assigned_total']+= 2  → 2
 * │       bsiTotals['grand_total']  += 5   → 5
 * │
 * ├─ STEP 9: engineerRows[] mein push
 * │
 * │       engineerRows[] = [
 * │           {
 * │               engineer_id:   '101',
 * │               engineer_name: 'Rahul',
 * │               data: {
 * │                   assigned:    [3=>1, 5=>1, ...],
 * │                   wip:         [6=>1, ...],
 * │                   pna:         [1=>1, ...],
 * │                   unassigned:  [11=>1, ...],
 * │                   assigned_total:    2,
 * │                   wip_total:         1,
 * │                   pna_total:         1,
 * │                   unassigned_total:  1,
 * │                   grand_total:       5
 * │               }
 * │           }
 * │           // Amit (102) ka data bhi isi tarah aayega...
 * │       ]
 * │
 * ▼
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 * FINAL OUTPUT
 * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
 *
 * $data = [
 * [
 * 'bsi_id'   => 'B001',
 * 'bsi_name' => 'Delhi BSI',
 *
 * 'engineers' => [
 * [
 * 'engineer_id'   => '101',
 * 'engineer_name' => 'Rahul',
 * 'data' => [
 * 'assigned'    => [1=>0, 2=>0, 3=>1, 4=>0, 5=>1, 6=>0, 11=>0],
 * 'pna'         => [1=>1, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 11=>0],
 * 'wip'         => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>1, 11=>0],
 * 'replacement' => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 11=>0],
 * 'unassigned'  => [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 11=>1],
 * 'assigned_total'    => 2,
 * 'pna_total'         => 1,
 * 'wip_total'         => 1,
 * 'replacement_total' => 0,
 * 'unassigned_total'  => 1,
 * 'grand_total'       => 5
 * ]
 * ],
 * // ... Amit ka data
 * ],
 *
 * 'totals' => [
 * // saare engineers ka combined data
 * 'assigned'    => [1=>0, 2=>0, 3=>1, 4=>0, 5=>1, 6=>0, 11=>0],
 * 'pna'         => [1=>1, 2=>0, ...],
 * 'wip'         => [6=>1, ...],
 * 'assigned_total'    => 2,
 * 'pna_total'         => 1,
 * 'wip_total'         => 1,
 * 'replacement_total' => 0,
 * 'unassigned_total'  => 1,
 * 'grand_total'       => 5
 * ]
 * ]
 * ]
 */

require_once("../includes/config.php");
require_once ("BsiContainer.php");
global $link1;
$arrstate = getAccessState($_SESSION['userid'],$link1);
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
$access_product=getAccessProduct($_SESSION['userid'],$link1);

$checkuser=(new CheckUserImplementation($link1,$arrstate,$access_product))->whichtypeofUser();
$data_range=$_REQUEST['date_range']??'';
$zone=$_REQUEST['zone']??'';
$state=$_REQUEST['state']??'';
$bsi=$_REQUEST['bsi']??'';
$enginner=$_REQUEST['enginner']??'';
$segment=$_REQUEST['segment']??'';
$sub_segment=$_REQUEST['sub_segment']??'';

$show=["status"=>false, "data"=>null];
$pagination="bsi_pending_call_dashboard-grid-data_copy.php";
$bsi_pending_export="bsi_pending_export.php";

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <script src="../js/jquery.min.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/dashboard2.css">
    <style>
        /* Loader Background */
        #loadeid{
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100vh;
            background:rgba(0,0,0,0.7);
            display:none;
            justify-content:center;
            align-items:center;
            z-index:99999;
        }

        /* Spinner */
        #loadeid .spinner{
            width:70px;
            height:70px;
            border:7px solid #ffffff30;
            border-top:7px solid #fff;
            border-radius:50%;
            animation:spin 1s linear infinite;
        }

        @keyframes spin{
            0%{
                transform:rotate(0deg);
            }
            100%{
                transform:rotate(360deg);
            }
        }
    </style>
</head>
<body>

<div id="loadeid">
    <div class="spinner"></div>
</div>

<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home" >
            <!-- HEADER -->
            <div class="crm-header" style="border-radius: 10px;">
                <h1 style="font-size:15px;!important;">Dashboard - Zone &amp; BSI Wise Pendency Summary (Details)</h1>
                <div class="header-right">
                    <button class="btn-export"> Export</button>
                </div>
            </div>
            <!-- FILTER BAR -->
            <form id="form1" action="" method="post">

                <div class="filter-bar">
                    <div class="filter-group">
                        <label>Date Range</label>
                        <input type="date" name="date_range" value="<?=$_REQUEST['date_range']??''?>">
                    </div>

                    <?php

                    $checkuser->zoneDisplay($_REQUEST);
                    $checkuser->stateDisplay($_REQUEST);
                    $checkuser->bsiDisplay($_REQUEST);
                    $checkuser->bsiEnginnerDisplay($_REQUEST);
                    ?>


                    <div class="filter-group">
                        <label>Segment</label>
                        <select name="segment" onchange="this.form.submit()">
                            <?php
                            $segment = $_REQUEST['segment'] ?? '';
                            ?>

                            <option value="">All</option>

                            <option value="1"
                                    <?= ($segment == '1') ? 'selected' : '' ?>>
                                Inverter
                            </option>

                            <option value="2"
                                    <?= ($segment == '2') ? 'selected' : '' ?>>
                                Battery
                            </option>

                            <option value="3"
                                    <?= ($segment == '3') ? 'selected' : '' ?>>
                                Solar
                            </option>
                        </select>
                    </div>

                    <?php if (isset($_REQUEST['segment']) && !empty($_REQUEST['segment'])) {?>
                        <div class="filter-group">
                            <label>Sub-Segment</label>
                            <select name="sub_segment" onchange="this.form.submit()">
                                <option value="">All</option>
                                <?php
                                $sub_segment = $_REQUEST['sub_segment'] ?? '';
                                $segmentFetch = $_REQUEST['segment'] ?? '';

                                $product_condition = "";

                                // inverter
                                if ($segmentFetch == '1') {
                                    $product_condition = "AND product_id = '1'";
                                }

                                // battery
                                if ($segmentFetch == '2') {
                                    $product_condition = "AND product_id NOT IN ('1','6','10','11','12','14')";
                                }

                                // solar
                                if ($segmentFetch == '3') {
                                    $product_condition = "AND product_id IN ('6','10','11','12')";
                                }


                                $sql = " SELECT product_id , product_name FROM product_master  WHERE 1   $product_condition ";
                                $result = mysqli_query($link1, $sql);
                                if (!$result || mysqli_num_rows($result) == 0) {
                                    echo "<option value=''>No Segments</option>";
                                } else {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $isSelected = ($sub_segment == $row['product_id']) ? 'selected' : '';
                                        echo "<option value='" . $row['product_id'] . "' $isSelected>" . $row['product_name'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    <?php }?>

                    <div class="filter-actions">
                        <button type="submit" name="search_d" class="btn-search">Search</button>
                        <button type="reset" class="btn-reset"> Reset</button>
                    </div>
                </div>
            </form>

            <!-- TABLE SECTION -->
            <div id="tablewrapper" class="table-section">

            </div>
        </div>
    </div>


</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        // initially hide all engineer rows
        document.querySelectorAll('.row-engineer').forEach(row => {
            row.style.display = 'none';
        });

        // expand collapse
        document.querySelectorAll('.expand-icon').forEach(icon => {

            icon.addEventListener('click', function () {

                let currentRow = this.closest('tr');
                let nextRow = currentRow.nextElementSibling;

                let isExpanded = this.textContent.trim() === '−';

                while (nextRow && nextRow.classList.contains('row-engineer')) {

                    nextRow.style.display = isExpanded ? 'none' : 'table-row';

                    nextRow = nextRow.nextElementSibling;
                }

                this.textContent = isExpanded ? '+' : '−';
            });

        });

    });
</script>
<script>
    class DashboardTableRenderer {

        constructor(containerSelector) {
            this.container = document.querySelector(containerSelector);
            if (!this.container) {
                console.error('DashboardTableRenderer: Container not found:', containerSelector);
            }
        }

        showLoader() {
            if (!this.container) return;
            this.container.innerHTML = `
            <style>
                @keyframes dashSpin { to { transform: rotate(360deg); } }
            </style>
            <div class="dashboard-loader" style="
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 60px 20px;
                gap: 16px;
            ">
                <div style="
                    width: 48px;
                    height: 48px;
                    border: 5px solid #e5e7eb;
                    border-top-color: #3b82f6;
                    border-radius: 50%;
                    animation: dashSpin 0.8s linear infinite;
                "></div>
                <p style="color:#6b7280;font-size:14px;margin:0;font-family:sans-serif;">
                    We're securely loading your data — please wait...
                </p>
            </div>
        `;
        }

        hideLoader() {
            const loader = this.container?.querySelector('.dashboard-loader');
            if (loader) loader.remove();
        }


        showError(message) {
            if (!this.container) return;
            this.container.innerHTML = `
            <div style="
                padding:24px;
                background:#fef2f2;
                border:1px solid #fca5a5;
                border-radius:8px;
                color:#dc2626;
                font-family:sans-serif;
                font-size:14px;
                margin:16px 0;
            ">
                &#9888; ${message || 'An error occurred while loading the data. Please try again.'}
            </div>
        `;
        }


        render(jsonData) {
            if (!this.container) return;

            this.hideLoader();

            const data = Array.isArray(jsonData)
                ? jsonData
                : (jsonData?.data ?? []);

            if (!data.length) {
                this.container.innerHTML = '<p style="padding:16px;color:#6b7280;">No Data Found.</p>';
                return;
            }

            const grandTotal    = this._emptyBucket();
            const tbodyRows     = data.map(bsiRow => this._buildBSIRows(bsiRow, grandTotal)).join('');
            const grandTotalRow = this._buildGrandTotalRow(grandTotal);

            this.container.innerHTML = `
            <div class="table-section">
                <div class="table-title">ZONE &amp; BSI WISE PENDENCY SUMMARY (DETAILS)</div>
                <div class="table-wrapper">
                    <table id="bsi-dashboard-table">
                        ${this._buildThead()}
                        <tbody>
                            ${tbodyRows}
                            ${grandTotalRow}
                        </tbody>
                    </table>
                </div>
            </div>
        `;

            this._bindExpandCollapse();
        }

        // =========================================================
        // THEAD
        // Buckets: 0-1 | 2 | 3-5 | 6-10 | 11-15 | Above 15 | Total
        // colspan = 7 per section (6 buckets + 1 total)
        // =========================================================
        _buildThead() {
            const subHeaders = `
            <th>0-1 Days</th>
            <th>2 Days</th>
            <th>3-5 Days</th>
            <th>6-10 Days</th>
            <th>11-15 Days</th>
            <th>Above 15</th>
            <th>Total</th>
        `;

            return `
            <thead>
                <tr class="thead-main">
                    <th class="th-zone"     rowspan="2">Zone</th>
                    <th class="th-bsi"      rowspan="2">BSI Name</th>
                    <th class="th-assigned" colspan="7">Assigned (Ageing in Days)</th>
                    <th class="th-replace"  colspan="7">Replacement Request (Ageing in Days)</th>
                    <th class="th-wip"      colspan="7">Work in Progress (Ageing in Days)</th>
                    <th class="th-pna"      colspan="7">PNA (Ageing in Days)</th>
                    <th class="th-grand"    rowspan="2">Grand Total</th>
                </tr>
                <tr class="thead-sub">
                    <!-- Assigned -->
                    <th class="sub-assigned">0-1 Days</th>
                    <th class="sub-assigned">2 Days</th>
                    <th class="sub-assigned">3-5 Days</th>
                    <th class="sub-assigned">6-10 Days</th>
                    <th class="sub-assigned">11-15 Days</th>
                    <th class="sub-assigned">Above 15</th>
                    <th class="sub-assigned">Total</th>
                    <!-- Replacement -->
                    <th class="sub-replace">0-1 Days</th>
                    <th class="sub-replace">2 Days</th>
                    <th class="sub-replace">3-5 Days</th>
                    <th class="sub-replace">6-10 Days</th>
                    <th class="sub-replace">11-15 Days</th>
                    <th class="sub-replace">Above 15</th>
                    <th class="sub-replace">Total</th>
                    <!-- WIP -->
                    <th class="sub-wip">0-1 Days</th>
                    <th class="sub-wip">2 Days</th>
                    <th class="sub-wip">3-5 Days</th>
                    <th class="sub-wip">6-10 Days</th>
                    <th class="sub-wip">11-15 Days</th>
                    <th class="sub-wip">Above 15</th>
                    <th class="sub-wip">Total</th>
                    <!-- PNA -->
                    <th class="sub-pna">0-1 Days</th>
                    <th class="sub-pna">2 Days</th>
                    <th class="sub-pna">3-5 Days</th>
                    <th class="sub-pna">6-10 Days</th>
                    <th class="sub-pna">11-15 Days</th>
                    <th class="sub-pna">Above 15</th>
                    <th class="sub-pna">Total</th>
                </tr>
            </thead>
        `;
        }

        // =========================================================
        // BSI ROW + ENGINEER ROWS
        // =========================================================
        _buildBSIRows(bsiRow, grandTotal) {
            const t = bsiRow.totals;

            this._mergeInto(grandTotal, t);

            const engineerRowsHtml = (bsiRow.engineers || [])
                .map(eng => this._buildEngineerRow(eng))
                .join('');

            return `
            <tr class="row-east">
                <td class="zone-label">${this._esc(bsiRow.bsi_zone || '')}</td>
                <td>
                    <div class="bsi-cell">
                        <span class="expand-icon" style="cursor:pointer;margin-right:6px;">+</span>
                        ${this._esc(bsiRow.bsi_name)}
                    </div>
                </td>
                ${this._bucketCells(t.assigned)}
                <td><b>${t.assigned_total}</b></td>
                ${this._bucketCells(t.replacement)}
                <td><b>${t.replacement_total}</b></td>
                ${this._bucketCells(t.wip)}
                <td><b>${t.wip_total}</b></td>
                ${this._bucketCells(t.pna)}
                <td><b>${t.pna_total}</b></td>
                <td><b>${t.grand_total}</b></td>
            </tr>
            ${engineerRowsHtml}
        `;
        }

        // =========================================================
        // ENGINEER ROW
        // =========================================================
        _buildEngineerRow(eng) {
            const d = eng.data;
            return `
            <tr class="row-engineer" style="display:none;">
                <td></td>
                <td style="padding-left:28px;color:#6b7280;font-size:11px;">
                    ${this._esc(eng.engineer_id)} - ${this._esc(eng.engineer_name)}
                </td>
                ${this._bucketCells(d.assigned)}
                <td><b>${d.assigned_total}</b></td>
                ${this._bucketCells(d.replacement)}
                <td><b>${d.replacement_total}</b></td>
                ${this._bucketCells(d.wip)}
                <td><b>${d.wip_total}</b></td>
                ${this._bucketCells(d.pna)}
                <td><b>${d.pna_total}</b></td>
                <td><b>${d.grand_total}</b></td>
            </tr>
        `;
        }

        // =========================================================
        // GRAND TOTAL ROW
        // =========================================================
        _buildGrandTotalRow(gt) {
            return `
            <tr class="row-grand-total">
                <td colspan="2" style="text-align:left;padding-left:8px;font-weight:bold;">
                    Grand Total
                </td>
                ${this._bucketCells(gt.assigned)}
                <td><b>${gt.assigned_total}</b></td>
                ${this._bucketCells(gt.replacement)}
                <td><b>${gt.replacement_total}</b></td>
                ${this._bucketCells(gt.wip)}
                <td><b>${gt.wip_total}</b></td>
                ${this._bucketCells(gt.pna)}
                <td><b>${gt.pna_total}</b></td>
                <td><b>${gt.grand_total}</b></td>
            </tr>
        `;
        }

        // =========================================================
        // HELPERS
        // b1=0-1, b2=2, b3=3-5, b4=6-10, b5=11-15, b6=16+
        // =========================================================
        _bucketCells(bucketObj) {
            return ['b1', 'b2', 'b3', 'b4', 'b5', 'b6']
                .map(k => `<td>${bucketObj[k] ?? 0}</td>`)
                .join('');
        }

        _emptyBucket() {
            const emptyB = { b1: 0, b2: 0, b3: 0, b4: 0, b5: 0, b6: 0 };
            return {
                assigned:    { ...emptyB },
                replacement: { ...emptyB },
                wip:         { ...emptyB },
                pna:         { ...emptyB },
                assigned_total:    0,
                replacement_total: 0,
                wip_total:         0,
                pna_total:         0,
                grand_total:       0
            };
        }

        _mergeInto(target, source) {
            ['assigned', 'replacement', 'wip', 'pna'].forEach(type => {
                ['b1', 'b2', 'b3', 'b4', 'b5', 'b6'].forEach(bucket => {
                    target[type][bucket] += (source[type]?.[bucket] ?? 0);
                });
            });
            target.assigned_total    += (source.assigned_total    ?? 0);
            target.replacement_total += (source.replacement_total ?? 0);
            target.wip_total         += (source.wip_total         ?? 0);
            target.pna_total         += (source.pna_total         ?? 0);
            target.grand_total       += (source.grand_total       ?? 0);
        }

        _esc(str) {
            return String(str ?? '')
                .replace(/&/g,  '&amp;')
                .replace(/</g,  '&lt;')
                .replace(/>/g,  '&gt;')
                .replace(/"/g,  '&quot;');
        }

        _bindExpandCollapse() {
            this.container.querySelectorAll('.expand-icon').forEach(icon => {
                icon.addEventListener('click', function () {
                    const currentRow = this.closest('tr');
                    const isExpanded = this.textContent.trim() === '−';
                    let next = currentRow.nextElementSibling;

                    while (next && next.classList.contains('row-engineer')) {
                        next.style.display = isExpanded ? 'none' : 'table-row';
                        next = next.nextElementSibling;
                    }

                    this.textContent = isExpanded ? '+' : '−';
                });
            });
        }
    }


    function Loader(){
        this.loader = document.getElementById("loadeid");
    }

    Loader.prototype.showLoader = function(){
        this.loader.style.display = "flex";
    }

    Loader.prototype.hideLoader = function(){
        this.loader.style.display = "none";
    }

    const renderer = new DashboardTableRenderer('#tablewrapper');
    const loader=new Loader();
    document.getElementById('form1').addEventListener('submit', function (e) {
        e.preventDefault();
        renderer.showLoader();
        const params = new URLSearchParams(new FormData(this)).toString();
        const url    = `../pagination/<?=$pagination?>?submit_data=&${params}`;

        // fetch url
        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error(`Server error: ${res.status}`);
                return res.json();
            })
            .then(jsonData => {
                renderer.render(jsonData);
            })
            .catch(err => {
                console.error('Dashboard fetch error:', err);
                renderer.showError('An error occurred while loading the data. Please try again.');
            });
    });

    document.querySelector(".btn-export").addEventListener("click", function () {
        loader.showLoader();
        const formData = new FormData(document.getElementById('form1'));
        const params   = new URLSearchParams(formData).toString();
        const exportUrl = `../excelReports/<?=$bsi_pending_export?>?${params}`;
        fetch(exportUrl)
            .then(res => {
                if (!res.ok) throw new Error(`Server error: ${res.status}`);

                // Content-Type check — PHP ne Excel bheja hai ya error?
                const contentType = res.headers.get('Content-Type') || '';
                if (!contentType.includes('spreadsheetml')) {
                    throw new Error('Excel file not found. Please check the server response.');
                }
                return res.blob();
            })
            .then(blob => {
                // 5. Blob se download trigger
                const url      = URL.createObjectURL(blob);
                const anchor   = document.createElement('a');
                const filename = `BSI_Pendency_${_todayStamp()}.xlsx`;

                anchor.href     = url;
                anchor.download = filename;
                document.body.appendChild(anchor);
                anchor.click();

                document.body.removeChild(anchor);
                URL.revokeObjectURL(url);
                loader.hideLoader();
            })
            .catch(err => {
                console.error('Export error:', err);
                loader.hideLoader();
                alert('An error occurred during export: ' + err.message);
            });
    });

    function _todayStamp() {
        const d   = new Date();
        const dd  = String(d.getDate()).padStart(2, '0');
        const mm  = String(d.getMonth() + 1).padStart(2, '0');
        const yy  = d.getFullYear();
        const hh  = String(d.getHours()).padStart(2, '0');
        const min = String(d.getMinutes()).padStart(2, '0');
        return `${dd}-${mm}-${yy}_${hh}${min}`;
    }
</script>
</body>
</html>;