<?php
require_once("../security/dbh.php");
header("content-type: application/json");
$db_user1 = 'root';
$db_pass1 = '';
$db_host1 = 'localhost';
$db1 = "prderp_demo_sql";
$link12 = mysqli_connect($db_host1, $db_user1, $db_pass1, $db1) or die("Unable to connect to MySQL");
global $link12;


//################## THis all DB Method : All Methods are fetch data from the Database ###############
/*
 *
 * if you want to change any method with any condition
 * You will follow Some steps
 *  1. Copy Past All Condition ( if) and change only attribute value in if condtions
 *  2. Change the SQL Query onlye , do nothing
 *
 */

function totalPending($condition, $link12)
{
    $where = " WHERE 1=1 ";



    if (!empty($condition['dateRange'])) {
        $dates = explode(' - ', $condition['dateRange']);

        if (count($dates) == 2) {
            $from = mysqli_real_escape_string($link12, trim($dates[0]));
            $to   = mysqli_real_escape_string($link12, trim($dates[1]));

            $where .= " AND repc.production_date BETWEEN '$from' AND '$to' ";
        }
    }


    // Locations
    if (!empty($condition['locations'])) {
        $locations = $condition['locations'];
        $where .= " AND rpp.location_code IN ('$locations') ";
    }

    // Products

    if (!empty($condition['products'])) {
        $products = $condition['products'];
        $where .= " AND repc.partcode IN ('$products' ) ";
    }

    $sql = "SELECT COUNT(DISTINCT repc.system_ref_no) AS total_planed
            FROM request_production_c repc
            LEFT JOIN request_production_p rpp
            ON repc.system_ref_no = rpp.system_ref_no
            $where";

    $result = mysqli_query($link12, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['total_planed'];
    }

    return 0;
}

function totalReady($condition,$link12){
    $where="WHERE jm.job_qty = jm.ready_qty";

    if (!empty($condition['dateRange'])) {
        $dates = explode(' - ', $condition['dateRange']);

        if (count($dates) == 2) {
            $from = mysqli_real_escape_string($link12, trim($dates[0]));
            $to   = mysqli_real_escape_string($link12, trim($dates[1]));
            $where .= " AND jm.start_date >= '$from' AND jm.end_date <= '$to' ";
        }
    }

    // Locations
    if (!empty($condition['locations'])) {
        $locations = $condition['locations'];
        $where .= " AND jm.location_code IN ('$locations') ";
    }

    // Products

    if (!empty($condition['products'])) {
        $products = $condition['products'];
        $where .= " AND jm.job_part IN ('$products' ) ";
    }

    $sql="SELECT COUNT(*) AS total_records, SUM(job_qty) AS total_qty FROM jobcard_master jm $where";

    $result=mysqli_query($link12, $sql);
    $row = mysqli_fetch_assoc($result);
    $total_records = $row['total_qty'];
    return $total_records??0;
}

function stockStorage($condition, $link12)
{
    $result = array();
    $totalReadyQty = 0;

    //  Products list nikal lo (jin jin product/model ka shortage check karna hai)
    if (!empty($condition['products'])) {
        $products = explode(',', $condition['products']); // agar multiple productcode comma-separated aaye
    } else {
        $products = array();
    }

    // ----- Location filter -----
    $location_code = !empty($condition['locations']) ? $condition['locations'] : '';

    foreach ($products as $productcode) {

        $productcode = mysqli_real_escape_string($link12, trim($productcode));

        // ----- BOM master se is product/model ki saari parts nikalo -----
        // NOTE: yahan 'bomid' us product ke against jo bhi BOM define hai usse link hota hai.
        // Agar tumhare bom_master mein productcode se bomid lena padta hai to pehle wo nikal lo:
        $bomid_row = mysqli_fetch_assoc(
            mysqli_query($link12, "SELECT bomid FROM bom_master WHERE bom_modelcode='$productcode' AND status='1' LIMIT 1")
        );
        $bomid = $bomid_row ? $bomid_row['bomid'] : 0;

        if (!$bomid) {
            continue; // is product ka BOM nahi mila to skip
        }

        // ----- Date range filter (agar createdate column hai bom_master mein) -----
        $where = "where bomid='$bomid' and status='1' and bom_qty > 0 ";
        if (!empty($condition['dateRange'])) {
            $dates = explode(' - ', $condition['dateRange']);
            if (count($dates) == 2) {
                $from = mysqli_real_escape_string($link12, trim($dates[0]));
                $to   = mysqli_real_escape_string($link12, trim($dates[1]));
                $where .= " AND createdate BETWEEN '$from' AND '$to' ";
            }
        }

        $res_bom = mysqli_query($link12, "SELECT bom_partcode, bom_qty, conversion_factor FROM bom_master $where");

        $min_issue = 900000; // bahut bada starting number, jaisa original code mein hai

        while ($row_bom = mysqli_fetch_assoc($res_bom)) {

            $bom_partcode = $row_bom['bom_partcode'];
            $bom_qty      = $row_bom['bom_qty'];

            // ----- Available stock (location-wise) -----
            $avl_stk = getDMSInventory($location_code, $bom_partcode, "ok_qty", $link12);

            $stock_qty = number_format(($avl_stk), 6, '.', '');

            // ----- EXACT SAME calculation jaisa original page mein hai -----
            $minissue = round(($stock_qty) / $bom_qty);

            if (is_nan($minissue)) {
                $minissue = 0;
            }

            // har component mein se sabse kam wala (bottleneck) le lo
            $min_issue = min($minissue, $min_issue);
        }

        if ($min_issue == 900000) {
            $min_issue = 0; // koi BOM row nahi mili
        }

        $result[$productcode] = $min_issue;
        $totalReadyQty += $min_issue;
    }

    // Agar single product ka answer chahiye:
    // return reset($result);

    // Agar sabhi products ka combined total chahiye:
    var_dump("Sdsd");exit();
    return array(
        'per_product' => $result,
        'total'       => round($totalReadyQty, 2)
    );
}
////// Get inventory qty for a given location + partcode + column /////
function getDMSInventory($location_code, $partcode, $column, $link12)
{
    $location_code = mysqli_real_escape_string($link12, $location_code);
    $partcode      = mysqli_real_escape_string($link12, $partcode);
    $column        = mysqli_real_escape_string($link12, $column); // ok_qty, hold_qty, etc.

    $sql = "SELECT `$column` FROM inventory_status 
            WHERE location_code = '$location_code' 
            AND partcode = '$partcode' 
            LIMIT 1";

    $res = mysqli_query($link12, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        return ($row[$column] != '') ? $row[$column] : 0;
    }

    return 0; // koi record nahi mila to 0 return karo
}

function totalDelay($condition, $link12)
{
    $where = " WHERE 1=1 ";

    if (!empty($condition['dateRange'])) {
        $dates = explode(' - ', $condition['dateRange']);

        if (count($dates) == 2) {
            $from = mysqli_real_escape_string($link12, trim($dates[0]));
            $to   = mysqli_real_escape_string($link12, trim($dates[1]));

            $where .= " AND jm.entry_date BETWEEN '$from' AND '$to' ";
        }
    }

    // Locations
    if (!empty($condition['locations'])) {
        $locations = $condition['locations'];
        $where .= " AND jm.location_code IN ('$locations') ";
    }

    // Products

    if (!empty($condition['products'])) {
        $products = $condition['products'];
        $where .= " AND jm.bom_modelcode IN ('$products' ) ";
    }

    $sql = "
        SELECT SUM(DATEDIFF(end_date, start_date)) AS total_delay
        FROM jobcard_master jm
        $where
        AND start_date IS NOT NULL
        AND end_date IS NOT NULL
    ";

    $result = mysqli_query($link12, $sql);
    $row = mysqli_fetch_assoc($result);

    return ($row['total_delay'] ?? 0);
}
function avgAging($condition,$link12){
    $where="WHERE jm.start_date IS NOT NULL AND jm.end_date IS NOT NULL";
    if (!empty($condition['dateRange'])) {
        $dates = explode(' - ', $condition['dateRange']);

        if (count($dates) == 2) {
            $from = mysqli_real_escape_string($link12, trim($dates[0]));
            $to   = mysqli_real_escape_string($link12, trim($dates[1]));

            $where .= " AND jm.start_date BETWEEN '$from' AND '$to' ";
        }
    }

    // Locations
    if (!empty($condition['locations'])) {
        $locations = $condition['locations'];
        $where .= " AND jm.location_code IN ('$locations') ";
    }

    // Products

    if (!empty($condition['products'])) {
        $products = $condition['products'];
        $where .= " AND jm.bom_modelcode IN ('$products' ) ";
    }
    $sql="SELECT AVG(DATEDIFF(end_date, start_date)) AS avg_aging
FROM jobcard_master jm $where";
    $result = mysqli_query($link12, $sql);
    if (!$result) {
        return 0;
    }
    $row = mysqli_fetch_assoc($result);
    return $row['avg_aging'] ?? 0;
}

function agingJobTable($condition,$link12){
    $where="where 1=1";
//    $where.=conditionValidator($where,$condition);

    $rows = [
        ["bucket" => "0-30 Days",  "P1" => 200, "P2" => 200, "P3" => 200, "total" => 600, "pct" => "20"],
        ["bucket" => "31-60 Days", "P1" => 200, "P2" => 200, "P3" => 200, "total" => 600, "pct" => "20"],
        ["bucket" => "61-90 Days", "P1" => 200, "P2" => 200, "P3" => 200, "total" => 600, "pct" => "20"],
        ["bucket" => ">90 Days",   "P1" => 200, "P2" => 200, "P3" => 200, "total" => 600, "pct" => "20"],
    ];

    // Grand total computed here so JS doesn't have to guess
    $grand = ["P1" => 0, "P2" => 0, "P3" => 0, "total" => 0, "pct" => "80"];
    foreach ($rows as $r) {
        $grand["P1"]    += $r["P1"];
        $grand["P2"]    += $r["P2"];
        $grand["P3"]    += $r["P3"];
        $grand["total"] += $r["total"];
    }

    return [
        "rows"        => $rows,
        "grand_total" => $grand,
    ];
}

function pieProductStatus($condition, $link12)
{
    $ongoing = 0;
    $planned = 0;
    $ready   = 0;

    $today = date('Y-m-d');

    $where_p = "where status!='7' ";

    if (!empty($condition['dateRange'])) {
        $dates = explode(' - ', $condition['dateRange']);
        if (count($dates) == 2) {
            $from = mysqli_real_escape_string($link12, trim($dates[0]));
            $to   = mysqli_real_escape_string($link12, trim($dates[1]));
            $where_p .= " AND entry_date BETWEEN '$from' AND '$to' ";
        }
    }

    if (!empty($condition['locations'])) {
        $location_code = mysqli_real_escape_string($link12, $condition['locations']);
        $where_p .= " AND location_code = '$location_code' ";
    }


    $sql1 = "SELECT system_ref_no FROM request_production_p $where_p";
    $res1 = mysqli_query($link12, $sql1);

    $array_plno = array();
    while ($row1 = mysqli_fetch_assoc($res1)) {
        $array_plno[] = $row1['system_ref_no'];
    }

    if (empty($array_plno)) {
        return [
            ["name" => "Ready",   "y" => 0, "color" => "#5bc0de"],
            ["name" => "Ongoing", "y" => 0, "color" => "#5cb85c"],
            ["name" => "Planned", "y" => 0, "color" => "#f0ad4e"],
        ];
    }

    $planno_list = "'" . implode("','", $array_plno) . "'";

    // ===================== Product filter (jobcard_master.job_part) =====================
    $product_filter = "";
    if (!empty($condition['products'])) {
        $product_code = mysqli_real_escape_string($link12, $condition['products']);
        $product_filter = " AND job_part = '$product_code' ";
    }

    // Loop through each planning
    foreach ($array_plno as $planning_no) {

        // Ongoing: jin planning ka jobcard start_date <= today
        $num2 = mysqli_num_rows(
            mysqli_query($link12, "SELECT id FROM jobcard_master 
                WHERE planning_no = '$planning_no' AND start_date <= '$today' $product_filter")
        );
        if ($num2 > 0) {
            $ongoing += 1;
        }

        // ----- Planned: har planning count ho jata hai (lekin product filter ke saath bhi check karna better hai) -----
        $num_planned_check = mysqli_num_rows(
            mysqli_query($link12, "SELECT id FROM jobcard_master 
                WHERE planning_no = '$planning_no' $product_filter")
        );
        if (empty($product_filter) || $num_planned_check > 0) {
            $planned += 1;
        }

        // ----- Ready: jin planning ke jobcard mein koi pending (status 13, 7 ke alawa) nahi hai -----
        $num3 = mysqli_num_rows(
            mysqli_query($link12, "SELECT id FROM jobcard_master 
                WHERE planning_no = '$planning_no' AND status != '13' AND status != '7' $product_filter")
        );
        if ($num3 == 0) {
            $ready += 1;
        }
    }

    // ===================== Pie chart data format =====================
    return [
        ["name" => "Ready",   "y" => (float)$ready,   "color" => "#5bc0de"], // text-info
        ["name" => "Ongoing", "y" => (float)$ongoing, "color" => "#5cb85c"], // text-success
        ["name" => "Planned", "y" => (float)$planned, "color" => "#f0ad4e"], // text-warning
    ];
}

function iqCFailureRate($condition,$link12){
    $where="where 1=1";
//    $where.=conditionValidator($where,$condition);
    return [
        ["priority" => "P1", "count" => 45],
        ["priority" => "P2", "count" => 32],
        ["priority" => "P3", "count" => 28],
        ["priority" => "P4", "count" => 15],
        ["priority" => "P5", "count" => 10],
    ];
}
function lineCharts($condition,$link12){
    $where="where 1=1";
//    $where.=conditionValidator($where,$condition);
    return [
        'actual' => [
            990, 652, 965, 1048, 939, 1012, 2089, 1995, 1123, 1302,
            2289, 2115, 1723, 1462, 1889, 1812, 1427, 1312, 1156, 1305,
            958, 984, 1032, 1099, 1200, 1345, 1100, 876, 920, 1050,
        ],
        'avg7' => [
            891, 587, 869, 943, 845, 911, 1880, 1796, 1011, 1172,
            2060, 1904, 1551, 1316, 1700, 1631, 1284, 1181, 1040, 1175,
            862, 886, 929, 989, 1080, 1211, 990, 788, 828, 945,
        ],
        'target'    => array_fill(0, 30, 800),
        'startDate' => '2024-01-01',
    ];
}
function pendingBarChart($condition,$link12){
    $where="where 1=1";
//    $where.=conditionValidator($where,$condition);
    return [
        'categories' => ['P1', 'P2', 'P3', 'P4', 'P5', 'P6'],
        'typeA'      => [387749, 280000, 129000, 64300, 54000, 34300],
        'typeB'      => [45321, 140000, 10000, 140500, 19500, 113500],
    ];
}
//###################################################################### end of all DB Methods here ##################



/*
 * Data Validator Class : This Class help to Wrap data in single Unit
 */
class DataConsumer {
    public static function getKPIData($condition,$link12) {

        return [
            "total_planned" => totalPending($condition,$link12),
            "total_ready"   => totalReady($condition,$link12),
            "stock_storage" => stockStorage($condition,$link12),
            "total_delay"   => totalDelay($condition,$link12),
            "avg_aging"     => avgAging($condition,$link12),
        ];
    }

    public static function agingBucket($conditions,$link12) {
        return agingJobTable($conditions,$link12);
    }

    public static function pieChartData($conditions,$link12) {
        return pieProductStatus($conditions,$link12);
    }

    public static function iqcData($conditions,$link12) {
        return  iqCFailureRate($conditions,$link12);
    }

    public static function lineChartData($conditions,$link12) {
        return lineCharts($conditions,$link12);
    }

    public static function barChartData($conditions,$link12) {
        return pendingBarChart($conditions,$link12);
    }


    //------------- header data-------------
    public static function productData($link12){
        $sql="SELECT vendor_code,vendor_part_name FROM `partcode_master`  WHERE itemtypeid in (3,5)"; // where status in (3,5)
        $result=mysqli_query($link12,$sql);
        $data=[];
        while ($row=mysqli_fetch_assoc($result)) {
            $data[]=$row;
        }
        return $data;
    }
    public static function locationData($link12){
        $sql="SELECT locationname,location_code FROM `location_master` WHERE location_code in (SELECT location_code FROM `access_location` where status=1)";
        $result=mysqli_query($link12,$sql);
        $data=[];
        while ($row=mysqli_fetch_assoc($result)) {
            $data[]=$row;
        }
        return $data;
    }
}

// ye complete hai
if (isset($_REQUEST['init'])) {
    $data=[
        "product"=>DataConsumer::productData($link12),
        "location"=>DataConsumer::locationData($link12),
    ];
    echo json_encode($data);
    exit();
}

// dashboard submit button
if (isset($_REQUEST['dashboard'])) {

    $conditions=[];
    $conditions['dateRange']=$_REQUEST['daterange']??''; // 2026-05-01 - 2026-06-02
    $conditions['locations']=$_REQUEST['zone']??'';
    $conditions['products']=$_REQUEST['product']??'';

    $data = [
        "kpi"    => DataConsumer::getKPIData($conditions,$link12),    // = renderKPIs()
        "aging"  => DataConsumer::agingBucket($conditions,$link12),                    // = renderAging()  ← {rows[], grand_total{}}
        "pie"    => DataConsumer::pieChartData($conditions,$link12),  // = renderPie()
        "iqc"    => DataConsumer::iqcData($conditions,$link12),       // = renderIQC()
        "trend"  => DataConsumer::lineChartData($conditions,$link12), // = renderLine()   ← KEY: "trend"
        "column" => DataConsumer::barChartData($conditions,$link12),  // = renderColumn() ← KEY: "column"
    ];
    echo json_encode($data);
    exit();
}


// No matching endpoint
http_response_code(400);
echo json_encode(["error" => "Invalid request"]);
exit();