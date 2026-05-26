<?php
/**
 * Workflow of the Application
 * ...same comments...
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
$pagination="bsi_pending_call_dashboard-grid-data.php";
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
        #loadeid {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0,0,0,0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 99999;
        }

        #loadeid .spinner {
            width: 70px;
            height: 70px;
            border: 7px solid #ffffff30;
            border-top: 7px solid #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ADDED: Scroll buttons styling */
        #scroll-btns {
            display: none;
            position: fixed;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 9999;
            flex-direction: column;
            gap: 10px;
        }

        #scroll-btns button {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #1F3864;
            color: #fff;
            border: none;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        #scroll-btns button:hover {
            background: #2a4a8a;
        }
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

<div id="loadeid">
    <div class="spinner"></div>
</div>

<!-- ADDED: Fixed scroll buttons -->
<div id="scroll-btns">
    <button onclick="tableScroll('left')" title="Scroll Left">&#8592;</button>
    <button onclick="tableScroll('right')" title="Scroll Right">&#8594;</button>
</div>

<div class="container-fluid">
    <div class="row content">
        <div  id="hide_nav_">
            <?php include("../includes/leftnav2.php"); ?>
        </div>
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">

            <!-- HEADER -->
            <div class="crm-header" style="border-radius: 10px;">
                <div style="display: flex;justify-content: space-around;justify-items: center">
                    <button onclick="dashoboardAsidebarToggle(this)" style="background: none;border: none"><i class="fa fa-bars" style="background: white"></i></button>
                    <h1 style="font-size:15px;!important;margin-left: 10px;">Dashboard - Zone &amp; BSI Wise Pendency Summary (Details)</h1>
                </div>
                <div class="header-right">
                    <button class="btn-export">Export</button>
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
                            <?php $segment = $_REQUEST['segment'] ?? ''; ?>
                            <option value="">All</option>
                            <option value="1" <?= ($segment == '1') ? 'selected' : '' ?>>Inverter</option>
                            <option value="2" <?= ($segment == '2') ? 'selected' : '' ?>>Battery</option>
                            <option value="3" <?= ($segment == '3') ? 'selected' : '' ?>>Solar</option>
                        </select>
                    </div>

                    <?php if (isset($_REQUEST['segment']) && !empty($_REQUEST['segment'])) { ?>
                        <div class="filter-group">
                            <label>Sub-Segment</label>
                            <select name="sub_segment" onchange="this.form.submit()">
                                <option value="">All</option>
                                <?php
                                $sub_segment    = $_REQUEST['sub_segment'] ?? '';
                                $segmentFetch   = $_REQUEST['segment']     ?? '';
                                $product_condition = "";

                                if ($segmentFetch == '1') {
                                    $product_condition = "AND product_id = '1'";
                                } elseif ($segmentFetch == '2') {
                                    $product_condition = "AND product_id NOT IN ('1','6','10','11','12','14')";
                                } elseif ($segmentFetch == '3') {
                                    $product_condition = "AND product_id IN ('6','10','11','12')";
                                }

                                $sql    = "SELECT product_id, product_name FROM product_master WHERE 1 $product_condition";
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
                    <?php } ?>

                    <div class="filter-actions">
                        <button type="submit" name="search_d" class="btn-search">Search</button>
                        <button type="reset" class="btn-reset">Reset</button>
                    </div>
                </div>
            </form>

            <!-- TABLE SECTION -->
            <div id="tablewrapper" class="table-section"></div>

        </div>
    </div>
</div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.row-engineer').forEach(row => {
            row.style.display = 'none';
        });

        document.querySelectorAll('.expand-icon').forEach(icon => {
            icon.addEventListener('click', function () {
                let currentRow = this.closest('tr');
                let nextRow    = currentRow.nextElementSibling;
                let isExpanded = this.textContent.trim() === '−';

                while (nextRow && nextRow.classList.contains('row-engineer')) {
                    nextRow.style.display = isExpanded ? 'none' : 'table-row';
                    nextRow = nextRow.nextElementSibling;
                }

                this.textContent = isExpanded ? '+' : '−';
            });
        });
    });
    let sidebarOpen = true
    function dashoboardAsidebarToggle(event){
        const home=document.getElementById("home");//style="width: 100%;"
        const nav=document.getElementById("hide_nav_");
        nav.classList.toggle('hide_nav_');
        home.classList.toggle("full-width");
        console.log(event);
    }
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
                // ADDED: hide scroll buttons if no data
                document.getElementById('scroll-btns').style.display = 'none';
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

            // ADDED: show scroll buttons after table renders
            document.getElementById('scroll-btns').style.display = 'flex';
        }

        _buildThead() {
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
                        <th class="sub-assigned">&le; 24 HRS</th>
                        <th class="sub-assigned">&le; 48 HRS</th>
                        <th class="sub-assigned">3-5</th>
                        <th class="sub-assigned">6-10</th>
                        <th class="sub-assigned">11-15</th>
                        <th class="sub-assigned">Above 15</th>
                        <th class="sub-assigned">Total</th>

                        <th class="sub-replace">&le; 24 HRS</th>
                        <th class="sub-replace">&le; 48 HRS</th>
                        <th class="sub-replace">3-5</th>
                        <th class="sub-replace">6-10</th>
                        <th class="sub-replace">11-15</th>
                        <th class="sub-replace">Above 15</th>
                        <th class="sub-replace">Total</th>

                        <th class="sub-wip">&le; 24 HRS</th>
                        <th class="sub-wip">&le; 48 HRS</th>
                        <th class="sub-wip">3-5</th>
                        <th class="sub-wip">6-10</th>
                        <th class="sub-wip">11-15</th>
                        <th class="sub-wip">Above 15</th>
                        <th class="sub-wip">Total</th>

                        <th class="sub-pna">&le; 24 HRS</th>
                        <th class="sub-pna">&le; 48 HRS</th>
                        <th class="sub-pna">3-5</th>
                        <th class="sub-pna">6-10</th>
                        <th class="sub-pna">11-15</th>
                        <th class="sub-pna">Above 15</th>
                        <th class="sub-pna">Total</th>
                    </tr>
                </thead>
            `;
        }

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

        _bucketCells(bucketObj) {
            return ['b1', 'b2', 'b3', 'b4', 'b5', 'b6']
                .map(k => `<td>${bucketObj[k] ?? 0}</td>`)
                .join('');
        }

        _emptyBucket() {
            const emptyB = { b1: 0, b2: 0, b3: 0, b4: 0, b5: 0, b6: 0 };
            return {
                assigned:          { ...emptyB },
                replacement:       { ...emptyB },
                wip:               { ...emptyB },
                pna:               { ...emptyB },
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


    function Loader() {
        this.loader = document.getElementById("loadeid");
    }

    Loader.prototype.showLoader = function () {
        this.loader.style.display = "flex";
    };

    Loader.prototype.hideLoader = function () {
        this.loader.style.display = "none";
    };


    // ADDED: Table scroll function
    function tableScroll(dir) {
        var wrapper = document.querySelector('#tablewrapper .table-wrapper');
        if (!wrapper) return;
        wrapper.scrollBy({ left: dir === 'left' ? -300 : 300, behavior: 'smooth' });
    }


    const renderer = new DashboardTableRenderer('#tablewrapper');
    const loader   = new Loader();

    document.getElementById('form1').addEventListener('submit', function (e) {
        e.preventDefault();
        renderer.showLoader();
        const params = new URLSearchParams(new FormData(this)).toString();
        const url    = `../pagination/<?=$pagination?>?submit_data=&${params}`;

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
        const formData  = new FormData(document.getElementById('form1'));
        const params    = new URLSearchParams(formData).toString();
        const exportUrl = `../excelReports/<?=$bsi_pending_export?>?${params}`;

        fetch(exportUrl)
            .then(res => {
                if (!res.ok) throw new Error(`Server error: ${res.status}`);
                const contentType = res.headers.get('Content-Type') || '';
                if (!contentType.includes('spreadsheetml')) {
                    throw new Error('Excel file not found. Please check the server response.');
                }
                return res.blob();
            })
            .then(blob => {
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
</html>