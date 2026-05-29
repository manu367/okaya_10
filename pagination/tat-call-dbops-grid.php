<?php
require_once("../includes/config.php");
class TATCAllDBOps{
    private $connection,$user,$typeofuser,$accessproduct,$accessstate;
    public function __construct($connection,$user,$typeofuser,$accessproduct,$accessstate){
        $this->connection=$connection;
        $this->user=$user;
        $this->typeofuser=$typeofuser;
        $this->accessproduct=$accessproduct;
        $this->accessstate=$accessstate;
    }

    private function getDateRange($condition) {
        if (!empty($condition['date_range'])) {
            $dates     = explode(" - ", $condition['date_range']);
            $from_date = trim($dates[0]) . " 00:00:00";
            $to_date   = trim($dates[1]) . " 23:59:59";
        } else {
            $from_date = date('Y-m-d 00:00:00');
            $to_date   = date('Y-m-d 23:59:59');
        }
        return [$from_date, $to_date];
    }
    //  Helper: percentage string
    //  Usage: $this->calcPct($part, $total) → "23.45%"
    private function calcPct($part, $total) {
        if (!$total || $total == 0) return '0%';
        return round(($part / $total) * 100, 2) . '%';
    }
    private function createWhereCondition($condition){
        $arrstate = $this->accessstate;
        $where=" where jd.eng_id is not null and jd.eng_id <> ''  and jd.state_id in ($arrstate) ";

        if( isset($condition['date_range']) && !empty($condition['date_range'])){
            // 2026-05-28 - 2026-05-28
            $daterange=mysqli_real_escape_string($this->connection,$condition['date_range']);
            $dates = explode(" - ", $daterange);
            $from_date = trim($dates[0]);
            $to_date   = trim($dates[1]);
//            $where .=" and  jd.close_date BETWEEN '$from_date' AND '$to_date'  ";
            $where .=" and  jd.close_date >= '$from_date' AND jd.close_date <= '$to_date'  ";
//            var_dump($where);exit();
        }

        if( isset($condition['zone']) && !empty($condition['zone'])){
            $zone=mysqli_real_escape_string($this->connection,$condition['zone']);
            $where .=" and jd.state_id in (SELECT sm.stateid FROM `state_master` sm WHERE sm.zoneid='$zone') ";
        }

        if( isset($condition['state']) && !empty($condition['state'])){
            $state=mysqli_real_escape_string($this->connection,$condition['state']);
            $where .=" and jd.state_id='$state'";
        }

        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($this->connection, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}') and mapped_bsi is not null and mapped_bsi <> '')";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($this->connection, $condition['enginner_type']);
            $where .= " AND lum.eng_type = '{$engType}' and lum.eng_type is not null and lum.eng_type <> '' ";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($this->connection, $condition['product']);
            if($productid==='1'){
                // inverter
                $where .= " AND jd.product_id in ('1') ";
            }
            if($productid==='2'){
                // battery
                $where .= " AND jd.product_id NOT IN ('1','6','10','11','12','14') ";
            }
            if ($productid==='3') {
                // solor
                $where .= " AND jd.product_id in ('6','10','11','12')";
            }
        }

        return $where;
    }
    public function totalTat($condition){
        $where=" ";
        $where .= $this->createWhereCondition($condition);

        $sql="SELECT COUNT(jd.job_id) as total FROM jobsheet_data jd 
              LEFT JOIN locationuser_master lum ON
                   jd.eng_id=lum.userloginid
             $where and jd.status in ('8','10','12','48')
               ";
        $result=mysqli_query($this->connection,$sql);
        if (!$result) return 0;
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }
    public function avgTat($condition){
        $where=" ";
        $where .= $this->createWhereCondition($condition);
        $sql = "SELECT ROUND(AVG(DATEDIFF(NOW(), jd.close_date)), 2) as avg_pending_days
            FROM `jobsheet_data` jd
            LEFT JOIN `locationuser_master` lm 
                ON lm.userloginid = jd.eng_id
            {$where} and jd.status in ('8','10','12','48')";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return $row['avg_pending_days'] ?? 0;
    }
    public function tat_1_data($condition=[]){
        $where = "";

        $temp_con=$condition;

        if (!empty($temp_con['date_range'])) {

            $daterange = $temp_con['date_range'];

            $dates = explode(" - ", $daterange);

            $from_date = trim($dates[0]) . " 00:00:00";
            $to_date   = trim($dates[1]) . " 23:59:59";

        }
        else {
            $from_date = date('Y-m-d 00:00:00');
            $to_date   = date('Y-m-d 23:59:59');
        }

        unset($temp_con['date_range']);

        $where .= $this->createWhereCondition($temp_con);

        $sql = " SELECT COUNT(jd.job_id) AS total FROM jobsheet_data jd 
              LEFT JOIN locationuser_master lum on
                  jd.eng_id=lum.userloginid
              $where AND (
        ( 
        jd.status IN (10,12,48)
            AND TIMESTAMP(jd.close_date, jd.close_time)
            BETWEEN '$from_date' AND '$to_date'
        )
        OR
        (
            jd.status IN (8,82)
            AND TIMESTAMP(jd.repl_appr_date, jd.repl_appr_time)
            BETWEEN '$from_date' AND '$to_date'
        )
    )
";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return [$row['total'] ?? 0,'0%'];
    }
    public function tat_2_data($condition=[]){
        $where=" ";
        $where .= $this->createWhereCondition($condition);

        $sql="select count(jd.job_id) as total_tat from jobsheet_data jd
LEFT JOIN locationuser_master lum on
    jd.eng_id=lum.userloginid"
            .$where.
            " AND jd.status='8'  AND jd.job_no IN ( select job_no from replacement_data ) ";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return 0;
        $row = mysqli_fetch_assoc($result);
        return [$row['total_tat'] ?? 0,'0%'];
    }
    public function tat_3_data($condition=[]){
        $where = "";
        $temp_con=$condition;
        if (!empty($temp_con['date_range'])) {
            $daterange = $temp_con['date_range'];
            $dates = explode(" - ", $daterange);
            $from_date = trim($dates[0]) . " 00:00:00";
            $to_date   = trim($dates[1]) . " 23:59:59";
        }
        else {
            $from_date = date('Y-m-d 00:00:00');
            $to_date   = date('Y-m-d 23:59:59');
        }

        unset($temp_con['date_range']);
        $where.=$this->createWhereCondition($temp_con);
        $sql="select count(jd.job_id) as tat_3 from jobsheet_data jd
              LEFT JOIN locationuser_master lum on
                  jd.eng_id=lum.userloginid"
            .$where.
            " and jd.status='8'  AND jd.job_no IN 
            (select job_no from rpd_replacement_data where 
                     (delivery_date BETWEEN '$from_date' AND '$to_date'))";

        $result = mysqli_query($this->connection, $sql);
        if (!$result) return 0;
        $row = mysqli_fetch_assoc($result);
        return [$row['tat_3'] ?? 0,'0%'];
    }

    public function call_by_tat_1_bucket($condition = []) {

        // Date Range
        $_date_ = $this->getDateRange($condition);

        $from_date = $_date_[0];
        $to_date   = $_date_[1];

        // Remove date_range from common condition
        $temp_con = $condition;
        unset($temp_con['date_range']);

        // Common WHERE
        $where = $this->createWhereCondition($temp_con);

        // Final Query
        $sql = "
        SELECT
        SUM( CASE  WHEN tat_hours <= 24 THEN 1 ELSE 0 END ) AS b_24,
        SUM( CASE  WHEN tat_hours >  24 AND  tat_hours <= 36 THEN 1 ELSE 0   END ) AS b_36,
        SUM( CASE  WHEN tat_hours >  36 AND  tat_hours <= 48 THEN 1 ELSE  0  END  ) AS b_48,
        SUM( CASE  WHEN tat_hours >  48 AND  tat_hours <= 72 THEN 1 ELSE  0  END  ) AS b_72,
        SUM( CASE  WHEN tat_hours > 72  THEN 1 ELSE 0 END ) AS b_72_plus,
            COUNT(*) AS grand_total
        FROM (
            SELECT CASE WHEN jd.status IN (10,12,48)
                    THEN TIMESTAMPDIFF(HOUR,TIMESTAMP(jd.open_date, jd.open_time),TIMESTAMP(jd.close_date, jd.close_time))            
                    WHEN jd.status IN (8,82)
                    THEN TIMESTAMPDIFF(HOUR,TIMESTAMP(jd.open_date, jd.open_time),TIMESTAMP(jd.repl_appr_date, jd.repl_appr_time)) END AS tat_hours
            FROM jobsheet_data jd
            LEFT JOIN locationuser_master lum
                ON jd.eng_id = lum.userloginid
            $where

            AND 
            (
            (jd.status IN (10,12,48)
            AND TIMESTAMP(jd.close_date, jd.close_time) BETWEEN '$from_date' AND '$to_date' )
            OR
            ( jd.status IN (8,82) AND TIMESTAMP(jd.repl_appr_date, jd.repl_appr_time) BETWEEN '$from_date' AND '$to_date')
            )
            AND jd.open_date IS NOT NULL
            AND jd.open_date <> '0000-00-00'
            AND jd.open_time IS NOT NULL
            AND jd.open_time <> ''
        ) AS tat_sub
    ";

        // Execute Query
        $result = mysqli_query($this->connection, $sql);

        if (!$result) {
            return $this->emptyBucket1();
        }

        // Fetch Data
        $row = mysqli_fetch_assoc($result);

        $total = (int)($row['grand_total'] ?? 0);

        if ($total === 0) {
            return $this->emptyBucket1();
        }

        // Return Bucket Data
        return [

            "24" => [
                (string)((int)$row['b_24']),
                $this->calcPct($row['b_24'], $total)
            ],

            "36" => [
                (string)((int)$row['b_36']),
                $this->calcPct($row['b_36'], $total)
            ],

            "48" => [
                (string)((int)$row['b_48']),
                $this->calcPct($row['b_48'], $total)
            ],

            "72" => [
                (string)((int)$row['b_72']),
                $this->calcPct($row['b_72'], $total)
            ],

            "72_plus" => [
                (string)((int)$row['b_72_plus']),
                $this->calcPct($row['b_72_plus'], $total)
            ]

        ];
    }
    public function call_by_tat_2_bucket($condition = []) {
        $_date_ = $this->getDateRange($condition);
        $from_date=$_date_[0];
        $to_date=$_date_[1];

        $temp_con = $condition;
        unset($temp_con['date_range']);
        $where = $this->createWhereCondition($temp_con);

        $sql = "SELECT
                    SUM(CASE WHEN tat_days <= 3                   THEN 1 ELSE 0 END) AS b_3,
                    SUM(CASE WHEN tat_days > 3  AND tat_days <= 5 THEN 1 ELSE 0 END) AS b_5,
                    SUM(CASE WHEN tat_days > 5  AND tat_days <= 7 THEN 1 ELSE 0 END) AS b_7,
                    SUM(CASE WHEN tat_days > 7                    THEN 1 ELSE 0 END) AS b_7_plus,
                    COUNT(*) AS grand_total
                FROM (
                    SELECT
                        DATEDIFF(NOW(), jd.close_date) AS tat_days
                    FROM jobsheet_data jd
                    LEFT JOIN locationuser_master lum ON jd.eng_id = lum.userloginid
                    $where
                    AND jd.status = '8'
                    AND jd.close_date BETWEEN '$from_date' AND '$to_date'
                    AND jd.job_no IN (SELECT job_no FROM replacement_data)
                ) AS tat_sub";

        $result = mysqli_query($this->connection, $sql);
        if (!$result) return $this->emptyBucket2();

        $row   = mysqli_fetch_assoc($result);
        $total = (int)($row['grand_total'] ?? 0);

        if ($total === 0) return $this->emptyBucket2();

        return [
            "3"      => [(string)((int)$row['b_3']),      $this->calcPct($row['b_3'],      $total)],
            "5"      => [(string)((int)$row['b_5']),      $this->calcPct($row['b_5'],      $total)],
            "7"      => [(string)((int)$row['b_7']),      $this->calcPct($row['b_7'],      $total)],
            "7_plus" => [(string)((int)$row['b_7_plus']), $this->calcPct($row['b_7_plus'], $total)],
        ];
    }

    //  call_by_tat_3_bucket
    //  TAT-3 = RPD replacement delivered in window (same as tat_3_data)
    //  TAT measured in DAYS from jd.close_date → rpd.delivery_date
    //  (how long it took to deliver the replacement)
    //  Buckets: <=7d | <=15d | <=21d | <=30d | >30d
    public function call_by_tat_3_bucket($condition = []) {

        $_date_ = $this->getDateRange($condition);
        $from_date=$_date_[0];
        $to_date=$_date_[1];

        // Remove date_range from common condition
        $temp_con = $condition;
        unset($temp_con['date_range']);

        // Common WHERE
        $where = $this->createWhereCondition($temp_con);

        // Final Query
        $sql = "SELECT
            SUM( CASE  WHEN tat_days <= 7  THEN 1 ELSE 0  END ) AS b_7,
            SUM( CASE  WHEN tat_days > 7  AND tat_days <= 15 THEN 1 ELSE 0  END ) AS b_15,
            SUM( CASE  WHEN tat_days > 15  AND tat_days <= 21 THEN 1 ELSE 0 END ) AS b_21,
            SUM( CASE  WHEN tat_days > 21  AND tat_days <= 30 THEN 1 ELSE 0  END ) AS b_30,
            SUM( CASE  WHEN tat_days > 30 THEN 1 ELSE 0  END ) AS b_30_plus,
            COUNT(*) AS grand_total
            FROM ( 
            SELECT DATEDIFF( rpd.delivery_date, jd.close_date ) AS tat_days FROM jobsheet_data jd
            LEFT JOIN locationuser_master lum
                ON jd.eng_id = lum.userloginid
            INNER JOIN rpd_replacement_data rpd
                ON jd.job_no = rpd.job_no
            $where
            AND jd.status = '8'
            AND rpd.delivery_date BETWEEN '$from_date'
            AND '$to_date'
        ) AS tat_sub";

        // Execute Query
        $result = mysqli_query($this->connection, $sql);

        if (!$result) {
            return $this->emptyBucket3();
        }

        // Fetch Data
        $row = mysqli_fetch_assoc($result);

        $total = (int)($row['grand_total'] ?? 0);

        if ($total === 0) {
            return $this->emptyBucket3();
        }

        // Return Bucket Data
        return [

            "7" => [
                (string)((int)$row['b_7']),
                $this->calcPct($row['b_7'], $total)
            ],

            "15" => [
                (string)((int)$row['b_15']),
                $this->calcPct($row['b_15'], $total)
            ],

            "21" => [
                (string)((int)$row['b_21']),
                $this->calcPct($row['b_21'], $total)
            ],

            "30" => [
                (string)((int)$row['b_30']),
                $this->calcPct($row['b_30'], $total)
            ],

            "30_plus" => [
                (string)((int)$row['b_30_plus']),
                $this->calcPct($row['b_30_plus'], $total)
            ]

        ];
    }
    private function emptyBucket3() {
        return [
            "7"       => ["0","0%"],
            "15"      => ["0","0%"],
            "21"      => ["0","0%"],
            "30"      => ["0","0%"],
            "30_plus" => ["0","0%"],
        ];
    }
    private function emptyBucket2() {
        return [
            "3"      => ["0","0%"],
            "5"      => ["0","0%"],
            "7"      => ["0","0%"],
            "7_plus" => ["0","0%"],
        ];
    }
    private function emptyBucket1() {
        return [
            "24"      => ["0","0%"],
            "36"      => ["0","0%"],
            "48"      => ["0","0%"],
            "72"      => ["0","0%"],
            "72_plus" => ["0","0%"],
        ];
    }

    public function ananlysisTreandTAT($condition = []) {

        // ── Step 1: Date range
        $_date_ = $this->getDateRange($condition);
        $from_date = trim($_date_[0]);
        $to_date   = trim($_date_[1]);

        $temp_con = $condition;
        unset($temp_con['date_range']);
        $where = $this->createWhereCondition($temp_con);

        // Clean date strings — no spaces, no time part for BETWEEN
        $fd = date('Y-m-d', strtotime($from_date)); // "2026-05-01"
        $td = date('Y-m-d', strtotime($to_date));   // "2026-05-09"

        // Step 2: Grouping mode decide karo
        $diff_days = (int) ceil(
            (strtotime($to_date) - strtotime($from_date)) / 86400
        );

        if ($diff_days <= 30) {
            $group_mode = 'daily';
        } elseif ( $diff_days > 30 && $diff_days <= 365) {
            $group_mode = '15day';
        } else {
            $group_mode = 'monthly';
        }

        // ── Step 3: Group expressions — status-aware for SQL1
        switch ($group_mode) {

            case 'daily':
                // SQL1 — Part A (status 10,12,48): close_date
                $grp_sql1_close   = "DATE(TIMESTAMP(jd.close_date, jd.close_time))";
                // SQL1 — Part B (status 8,82): repl_appr_date
                $grp_sql1_repl    = "DATE(TIMESTAMP(jd.repl_appr_date, jd.repl_appr_time))";
                // SQL2
                $grp_sql2         = "DATE(jd.close_date)";
                // SQL3
                $grp_sql3         = "DATE(rpd.delivery_date)";

                $label_fn = function ($k) {
                    return date('d M', strtotime($k));
                };
                break;

            case '15day':
                $grp_sql1_close   = "FLOOR(DATEDIFF(DATE(TIMESTAMP(jd.close_date, jd.close_time)), '{$fd}') / 15)";
                $grp_sql1_repl    = "FLOOR(DATEDIFF(DATE(TIMESTAMP(jd.repl_appr_date, jd.repl_appr_time)), '{$fd}') / 15)";
                $grp_sql2         = "FLOOR(DATEDIFF(DATE(jd.close_date), '{$fd}') / 15)";
                $grp_sql3         = "FLOOR(DATEDIFF(DATE(rpd.delivery_date), '{$fd}') / 15)";

                $label_fn = function($k) use ($fd) {
                    $s = strtotime($fd) + ((int)$k * 15 * 86400);
                    return date('d M', $s) . ' – ' . date('d M', $s + 14 * 86400);
                };
                break;

            case 'monthly':
            default:
                $grp_sql1_close   = "DATE_FORMAT(TIMESTAMP(jd.close_date, jd.close_time), '%Y-%m')";
                $grp_sql1_repl    = "DATE_FORMAT(TIMESTAMP(jd.repl_appr_date, jd.repl_appr_time), '%Y-%m')";
                $grp_sql2         = "DATE_FORMAT(jd.close_date, '%Y-%m')";
                $grp_sql3         = "DATE_FORMAT(rpd.delivery_date, '%Y-%m')";

                $label_fn = function ($k) {
                    return date('M Y', strtotime($k . '-01'));
                };
                break;
        }

        // SQL-1  TAT-1: Open → Close/ReplacementApproval
        $sql1 = "
        SELECT grp_key, ROUND(AVG(tat_hours), 2) AS avg_tat
        FROM (
            SELECT
                {$grp_sql1_close} AS grp_key,
                TIMESTAMPDIFF(
                    HOUR,TIMESTAMP(jd.open_date, jd.open_time), TIMESTAMP(jd.close_date, jd.close_time)
                    ) AS tat_hours
               FROM jobsheet_data jd
                   LEFT JOIN locationuser_master lum ON jd.eng_id = lum.userloginid
                   {$where}
                   AND jd.status IN (10, 12, 48)
            AND jd.open_date  IS NOT NULL AND jd.open_date  <> '0000-00-00'
            AND jd.open_time  IS NOT NULL AND jd.open_time  <> ''
            AND jd.close_date IS NOT NULL AND jd.close_date <> '0000-00-00'
            AND jd.close_time IS NOT NULL AND jd.close_time <> ''
            
            AND DATE(TIMESTAMP(jd.close_date, jd.close_time))
                    BETWEEN '{$fd}' AND '{$td}'
            UNION ALL
            -- Part-B: Status 8, 82 → repl_appr_date filter + group
            SELECT
                {$grp_sql1_repl} AS grp_key,
                TIMESTAMPDIFF(
                    HOUR,
                    TIMESTAMP(jd.open_date, jd.open_time),
                    TIMESTAMP(jd.repl_appr_date, jd.repl_appr_time)
                ) AS tat_hours
            FROM jobsheet_data jd
            LEFT JOIN locationuser_master lum ON jd.eng_id = lum.userloginid
            {$where}
            AND jd.status IN (8, 82)
            AND jd.open_date      IS NOT NULL AND jd.open_date      <> '0000-00-00'
            AND jd.open_time      IS NOT NULL AND jd.open_time      <> ''
            AND jd.repl_appr_date IS NOT NULL AND jd.repl_appr_date <> '0000-00-00'
            AND jd.repl_appr_time IS NOT NULL AND jd.repl_appr_time <> ''
            AND DATE(TIMESTAMP(jd.repl_appr_date, jd.repl_appr_time))
                    BETWEEN '{$fd}' AND '{$td}'
        ) AS combined
        GROUP BY grp_key
        ORDER BY grp_key ASC
    ";

        // SQL-2  TAT-2: Close → Replacement Approval (days)
        // Filter + Group dono close_date use kar rahe hain
        // TAT = repl_appr_date - close_date (fixed gap, NOW() nahi)
        $sql2 = "
        SELECT
            {$grp_sql2} AS grp_key,
            ROUND(AVG(
                DATEDIFF(
                    jd.close_date, 
                    jd.repl_appr_date
                )
            ), 2) AS avg_tat
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lum ON jd.eng_id = lum.userloginid
        {$where}
        AND jd.status IN (8)
        AND jd.close_date     IS NOT NULL AND jd.close_date     <> '0000-00-00'
        AND jd.repl_appr_date IS NOT NULL AND jd.repl_appr_date <> '0000-00-00'
        AND jd.job_no IN (SELECT job_no FROM replacement_data)
        AND DATE(jd.close_date) BETWEEN '{$fd}' AND '{$td}'
        GROUP BY {$grp_sql2}
        ORDER BY grp_key ASC
    ";


        // SQL-3  TAT-3: Close → Delivery (days)
        // Filter + Group dono delivery_date use kar rahe hain
        $sql3 = "
        SELECT
            {$grp_sql3} AS grp_key,
            ROUND(AVG(
                DATEDIFF(rpd.delivery_date, jd.close_date)
            ), 2) AS avg_tat
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lum ON jd.eng_id = lum.userloginid
        INNER JOIN rpd_replacement_data rpd ON jd.job_no = rpd.job_no
        {$where}
        AND jd.status IN (8, 82)
        AND jd.close_date     IS NOT NULL AND jd.close_date     <> '0000-00-00'
        AND rpd.delivery_date IS NOT NULL AND rpd.delivery_date <> '0000-00-00'
        AND DATE(rpd.delivery_date) BETWEEN '{$fd}' AND '{$td}'
        GROUP BY {$grp_sql3}
        ORDER BY grp_key ASC
    ";


        $r1 = mysqli_query($this->connection, $sql1);
        $r2 = mysqli_query($this->connection, $sql2);
        $r3 = mysqli_query($this->connection, $sql3);

        // Step 5: Results maps mein collect karo
        $map1 = []; $map2 = []; $map3 = [];

        if ($r1) {
            while ($row = mysqli_fetch_assoc($r1)) {
                $map1[$row['grp_key']] = $row['avg_tat'];
            }
        }
        if ($r2) {
            while ($row = mysqli_fetch_assoc($r2)) {
                $map2[$row['grp_key']] = $row['avg_tat'];
            }
        }
        if ($r3) {
            while ($row = mysqli_fetch_assoc($r3)) {
                $map3[$row['grp_key']] = $row['avg_tat'];
            }
        }


        $all_keys = array_unique(array_merge(
            array_keys($map1),
            array_keys($map2),
            array_keys($map3)
        ));


        // 15day mode mein numeric sort, baaki mein string sort
        if ($group_mode === '15day') {
            sort($all_keys, SORT_NUMERIC);
        } else {
            sort($all_keys);
        }

        //  Step 7: Chart data build
        $x_axis = [];
        $data1  = [];
        $data2  = [];
        $data3  = [];

        foreach ($all_keys as $key) {
            $x_axis[] = $label_fn($key);
            $data1[]  = $map1[$key] ?? 0;
            $data2[]  = $map2[$key] ?? 0;
            $data3[]  = $map3[$key] ?? 0;
        }
        // Step 8: Return
        return [
            "x_axis" => $x_axis,
            "y_axis" => "AVG TAT",
            "series" => [
                [
                    "name"  => "TAT-1 (Open → Close/Delivery, Days)",
                    "color" => "#2355f5",
                    "data"  => $data1
                ],
                [
                    "name"  => "TAT-2 (Close/Delivery, days)",
                    "color" => "#0aaa6e",
                    "data"  => $data2
                ],
                [
                    "name"  => "TAT-3 (Close/Delivery, days)",
                    "color" => "#e8344a",
                    "data"  => $data3
                ],
            ]
        ];
    }
}