<?php

require_once("../includes/config.php");
global $link1;

class DataFetchingFromDB{

    public static function zoneFetching($link1){
        $zone = [];
        $res_zone = mysqli_query($link1,
            "SELECT zonename, zoneid FROM zone_master"
        );
        while($row_zone = mysqli_fetch_assoc($res_zone)){

            $zone[] = [
                "zone" => $row_zone['zoneid'],
                "zone_name" => $row_zone['zonename']
            ];

        }
        return $zone;
    }
    /**
     *
     * Fetch States By Zone
     *
     * Retrieves state list based on:
     * - accessible state ids
     * - optional zone filter
     *
     * @param mysqli $link1
     * Database connection object
     *
     * @param string $arrstate
     * Comma separated state ids
     * Example: "1,2,3"
     *
     * @param int $statezone
     * Optional zone id filter
     *
     * @return array
     * Returns formatted state data
     *
     */
    public static function getAllStatesbyZone($link1, $arrstate, $statezone = 0){
        $states = [];
        $condition = "";
        if (!empty($statezone)) {
            $condition = " AND zoneid = '$statezone'";
        }

        $query = "
        SELECT stateid, state, zoneid, statecode
        FROM state_master
        WHERE stateid IN ($arrstate)
        $condition
        ORDER BY state";

        $result = mysqli_query($link1, $query);

        while ($stateinfo = mysqli_fetch_assoc($result)) {

            $states[] = [
                "stateid"   => $stateinfo['stateid'],
                "state"     => $stateinfo['state'],
                "zoneid"    => $stateinfo['zoneid'],
                "statecode" => $stateinfo['statecode']
            ];
        }
        return $states;
    }
    /**
     *
     * Fetch Locations By State
     *
     * Retrieves all service locations using state ids.
     *
     * Excluded Types:
     * - WH
     * - CC
     *
     * Conditions:
     * - Only active locations
     *
     * @param mysqli $link1
     * Database connection object
     *
     * @param array $statestr_1
     * Array of state ids
     *
     * @return array
     * Returns formatted location list
     *
     */
    public static function getAllLocations($link1, $statestr_1 = [])
    {
        $locations = [];

        $stateIds = array_map('intval', $statestr_1);
        $statestr = implode(",", $stateIds);

        $location_query = "
        SELECT locationname, location_code
        FROM location_master
        WHERE stateid IN ($statestr)
        AND locationtype NOT IN ('WH', 'CC')
        AND statusid = '1'
        ORDER BY locationname";
        $loc_res = mysqli_query($link1, $location_query);
        while ($loc_info = mysqli_fetch_assoc($loc_res)) {
            $locations[] = [
                "locationname" => $loc_info['locationname'],
                "location_code" => $loc_info['location_code']
            ];
        }
        return $locations;
    }

    /**
     *
     * Fetch Accessible Products
     *
     * Retrieves product list based on accessible product ids.
     *
     * Conditions:
     * - Product status must be active
     *
     * @param mysqli $link1
     * Database connection object
     *
     * @param string $access_product
     * Comma separated product ids
     *
     * @return array
     * Returns formatted product list
     *
     */
    public static function getAllProducts($link1, $access_product = '')
    {
        $products = [];
//        $access_product = str_replace("'", "", $access_product);
//        // Convert string to array
//        $productArray = explode(",", $access_product);
//        $productIds = array_map('intval', $productArray);
//        $productStr = implode(",", $productIds);

//        if (empty($productStr)) {
//            return [];
//        }
//        $model_query = " SELECT product_id, product_name FROM product_master WHERE status = '1'
//        AND product_id IN ($productStr) ORDER BY product_name ";
//
//        $check1 = mysqli_query($link1, $model_query);
//
//        while ($br = mysqli_fetch_assoc($check1)) {
////            $products[] = ["product_id"   => $br['product_id'], "product_name" => $br['product_name']];
//        }
        $products[] = ["product_id"   => "1", "product_name" => "Inverter"];
        $products[] = ["product_id"   => "2", "product_name" => "Battery"];
        $products[] = ["product_id"   => "3", "product_name" => "Solor"];

        return $products;
    }

    /**
     * --------------------------------------------------------
     * Fetch Engineer Types
     * --------------------------------------------------------
     * Static engineer type mapping.
     *
     * @param mysqli $link1
     * Unused DB parameter (kept for consistency)
     *
     * @return array
     * Returns engineer type list
     * --------------------------------------------------------
     */
    public static function enginnerType($link1){
        $types = [];
        $sql = "SELECT COUNT(*) AS total, eng_type FROM locationuser_master WHERE eng_type IS NOT NULL AND eng_type != '' GROUP BY eng_type ORDER BY eng_type";
        $result = mysqli_query($link1, $sql);
        $i=0;
        while ($row = mysqli_fetch_assoc($result)) {
            $types[$i] =  $row['eng_type'];
            $i++;
        }
        return $types;
    }
    public static function getAllBSI($link1,$state)
    {
        $bsi = [];
        $state_c="";
        if($state!==0) {
            $state_c="and ar.stateid='$state'";
        }

        $sql = "
       SELECT ar.*, au.id, au.uid, au.username, 
              au.name, au.designation_id, au.phone, au.emailid,au.sapid 
       FROM access_region ar INNER JOIN admin_users au 
           ON ar.userid = au.username 
       WHERE ar.status = 'Y' $state_c 
         AND au.designation_id = '45' AND au.status = '1'
    ";
        $result = mysqli_query($link1, $sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $bsi[] = [
                    "sapid"   => $row['sapid'],
                    "username" => $row['name']
                ];
            }
        }
        return $bsi;
    }

    public static function zoneByState($link1, $zone = '')
    {
        $condition = "";

        if ($zone !== '') {
            $condition = " WHERE zm.zoneid = '$zone'";
        }

        $sql = "SELECT sm.stateid
            FROM zone_master zm
            LEFT JOIN state_master sm 
                ON sm.zoneid = zm.zoneid
            $condition";

        $result = mysqli_query($link1, $sql);

        $data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            if (!empty($row['stateid'])) {
                $data[] = $row['stateid'];
            }
        }
        return $data;
    }


    //===================== | dashboard data |=======================
    // ===================== | DASHBOARD DATA | =======================

    public static function totoalPendingCall($link1, $condition = [])
    {
        // STATUS FILTER AB HAMESHA LAGEGA — condition ka wait nahi
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }
        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }
        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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

        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }


        // Aging bucket filter (jo pehle commented tha — ab active)
        if (isset($condition['aging_bucket']) && !empty($condition['aging_bucket'])) {
            $aging = (int)$condition['aging_bucket'];
//            switch ($aging) {
//                case 1: $where .= " AND DATEDIFF(NOW(), jd.open_date) BETWEEN 0 AND 2 "; break;
//                case 2: $where .= " AND DATEDIFF(NOW(), jd.open_date) BETWEEN 3 AND 7 "; break;
//                case 3: $where .= " AND DATEDIFF(NOW(), jd.open_date) BETWEEN 8 AND 15 "; break;
//                case 4: $where .= " AND DATEDIFF(NOW(), jd.open_date) > 15 "; break;
//            }
        }

        $sql = "SELECT COUNT(jd.job_id) as total
            FROM `jobsheet_data` jd
            LEFT JOIN `locationuser_master` lm 
                ON lm.userloginid = jd.eng_id
            {$where}";
        $result = mysqli_query($link1, $sql);
        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

// ---------------------------------------------------------------

    public static function avgAging($link1, $condition = [])
    {
        // STATUS FILTER HAMESHA ACTIVE
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }

        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }

        if (isset($condition['aging_bucket']) && !empty($condition['aging_bucket'])) {
            $aging = (int)$condition['aging_bucket'];
            switch ($aging) {
                case 1: $where .= " AND DATEDIFF(NOW(), jd.open_date) BETWEEN 0 AND 2 "; break;
                case 2: $where .= " AND DATEDIFF(NOW(), jd.open_date) BETWEEN 3 AND 7 "; break;
                case 3: $where .= " AND DATEDIFF(NOW(), jd.open_date) BETWEEN 8 AND 15 "; break;
                case 4: $where .= " AND DATEDIFF(NOW(), jd.open_date) > 15 "; break;
            }
        }

        $sql = "SELECT ROUND(AVG(DATEDIFF(NOW(), jd.open_date)), 2) as avg_pending_days
            FROM `jobsheet_data` jd
            LEFT JOIN `locationuser_master` lm 
                ON lm.userloginid = jd.eng_id
            {$where}";

        $result = mysqli_query($link1, $sql);
        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return $row['avg_pending_days'] ?? 0;
    }

// ---------------------------------------------------------------

    public static function pendingDays($link1, $condition = [])
    {
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";
        $where .= " AND DATEDIFF(NOW(), jd.open_date) > 2 ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }

        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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

        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }

        $sql = "SELECT COUNT(jd.job_id) as total
            FROM `jobsheet_data` jd
            LEFT JOIN `locationuser_master` lm 
                ON lm.userloginid = jd.eng_id
            {$where}";


        $result = mysqli_query($link1, $sql);
        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

// ---------------------------------------------------------------

    public static function high_priority_pending($link1, $condition = [])
    {
        // STATUS FILTER HAMESHA ACTIVE — conditional nahi
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }

        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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

        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }


        if (isset($condition['aging_bucket']) && !empty($condition['aging_bucket'])) {
            $aging = (int)$condition['aging_bucket'];
        }

        $where .= "
AND TIMESTAMPDIFF(
    HOUR,
    STR_TO_DATE(
        CONCAT(jd.open_date, ' ', jd.open_time),
        '%Y-%m-%d %H:%i:%s'
    ),
    NOW()
) > 48
";

        $sql = "SELECT COUNT(jd.job_id) as total
            FROM `jobsheet_data` jd
            LEFT JOIN `locationuser_master` lm 
                ON lm.userloginid = jd.eng_id
            {$where}";

        $result = mysqli_query($link1, $sql);
        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

// ---------------------------------------------------------------

    public static function generateBarChartDataFromDB($link1, $condition = [])
    {
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }

        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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

        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }



        $sql = "
        SELECT
            SUM(CASE WHEN DATEDIFF(NOW(), jd.open_date) BETWEEN 0 AND 3  THEN 1 ELSE 0 END) as bucket_0_3,
            SUM(CASE WHEN DATEDIFF(NOW(), jd.open_date) BETWEEN 4 AND 5  THEN 1 ELSE 0 END) as bucket_4_5,
            SUM(CASE WHEN DATEDIFF(NOW(), jd.open_date) BETWEEN 6 AND 10 THEN 1 ELSE 0 END) as bucket_6_10,
            SUM(CASE WHEN DATEDIFF(NOW(), jd.open_date) BETWEEN 11 AND 15 THEN 1 ELSE 0 END) as bucket_11_15,
            SUM(CASE WHEN DATEDIFF(NOW(), jd.open_date) > 15 THEN 1 ELSE 0 END) as bucket_above_15
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm ON lm.userloginid = jd.eng_id
        {$where}
    ";

        $result = mysqli_query($link1, $sql);
        if (!$result) return [];

        $row = mysqli_fetch_assoc($result);

        return [
            "x_categories" => ['0-3 Days', '4-5 Days', '6-10 Days', '11-15 Days', 'Above 15 Days'],
            "y_label"      => "Number of calls",
            "data"         => [
                ["y" => (int)$row['bucket_0_3'],      "color" => '#22c55e'],
                ["y" => (int)$row['bucket_4_5'],      "color" => '#f97316'],
                ["y" => (int)$row['bucket_6_10'],     "color" => '#ef4444'],  // variable name fix: bucket_5_10 → bucket_6_10
                ["y" => (int)$row['bucket_11_15'],    "color" => '#f97316'],
                ["y" => (int)$row['bucket_above_15'], "color" => '#b91c1c'],
            ]
        ];
    }

// ---------------------------------------------------------------

    public static function generateColumnChartDataFromDb($link1, $condition = [])
    {
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }


        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }


        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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


        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }

        $sql = "
        SELECT sm.state, COUNT(jd.job_id) as total_calls
        FROM jobsheet_data jd
        LEFT JOIN state_master sm ON sm.stateid = jd.state_id
        LEFT JOIN locationuser_master lm ON lm.userloginid = jd.eng_id
        {$where}
        GROUP BY jd.state_id, sm.state
        ORDER BY total_calls ASC
    ";

        $result = mysqli_query($link1, $sql);
        if (!$result) return [];

        $x_categories = [];
        $data         = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $x_categories[] = $row['state'];
            $data[]         = (int)$row['total_calls'];
        }

        return [
            "x_categories" => $x_categories,
            "y_label"      => "Number of Calls",
            "data"         => $data
        ];
    }

    public static function giveTopState($link1, $condition = [])
    {

        $where = " WHERE jd.status IN ('1','2','3','7','81') ";


        // Date Range
        if (isset($condition['date_range']) && !empty($condition['date_range'])) {

            $date = (int)$condition['date_range'];

            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }


        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }


        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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

        // State
        if (isset($condition['state']) && !empty($condition['state'])) {

            $stateid = mysqli_real_escape_string($link1, $condition['state']);

            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        // Engineer
        if (isset($condition['enginner']) && !empty($condition['enginner'])) {

            $engid = mysqli_real_escape_string($link1, $condition['enginner']);

            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        // Engineer Type
        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {

            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);

            $where .= " AND lm.eng_type = '{$engType}' ";
        }




        $sql = "
        SELECT
            sm.state,
            COUNT(jd.job_id) as total_calls,
            ROUND(
                AVG(
                    DATEDIFF(NOW(), jd.open_date)
                ),
            0) as avg_days
        FROM jobsheet_data jd
        LEFT JOIN state_master sm
            ON sm.stateid = jd.state_id
        LEFT JOIN locationuser_master lm
            ON lm.userloginid = jd.eng_id
        {$where}
        GROUP BY jd.state_id, sm.state
        ORDER BY total_calls DESC
        LIMIT 1
    ";

        $result = mysqli_query($link1, $sql);
        if (!$result || mysqli_num_rows($result) == 0) {
            return [
                "state" => "-",
                "calls" => 0,
                "days" => 0
            ];
        }
        $row = mysqli_fetch_assoc($result);
        return [
            "state" => $row['state'] ?? '-',
            "calls" => (int)($row['total_calls'] ?? 0),
            "days" => (int)($row['avg_days'] ?? 0)
        ];
    }

// ---------------------------------------------------------------

    public static function generatepieChartDataFromdb($link1, $condition = [])
    {
        $batteryIds  = [1, 6, 10, 11, 12, 14];
        $inverterIds = [1];
        $solarIds    = [6, 10, 11, 12];

        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }


        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }


        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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

        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }

        // Product filter aaya — sirf wahi product dikhao
        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = (int)$condition['product'];

            if ($productid === 1) {
                // Inverter
                $where .= " AND jd.product_id IN ('1') ";
                $productName = "Inverter";
                $color = '#3b82f6';

            } elseif ($productid === 2) {
                // Battery
                $where .= " AND jd.product_id NOT IN ('1','6','10','11','12','14') ";
                $productName = "Battery";
                $color = '#22c55e';
            } elseif ($productid === 3) {
                // Solar
                $where .= " AND jd.product_id IN ('6','10','11','12','14') ";
                $productName = "Solar Product";
                $color = '#f59e0b';
            }

            $sql = "
        SELECT COUNT(jd.job_id) as total
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm 
            ON lm.userloginid = jd.eng_id
        {$where}
    ";

            $result = mysqli_query($link1, $sql);

            if (!$result) {
                return [];
            }

            $row = mysqli_fetch_assoc($result);

            return [
                "name" => "Pie Charts",
                "data" => [
                    ["name" => $productName, "y" => (int)$row['total'], "color" =>$color]
                ]
            ];
        }

        // No product filter — Battery / Inverter / Solar
        $batteryIdsString  = implode(',', $batteryIds);
        $inverterIdsString = implode(',', $inverterIds);
        $solarIdsString    = implode(',', $solarIds);

        $sql = "
        SELECT
            SUM(CASE WHEN jd.product_id NOT IN ({$batteryIdsString})  THEN 1 ELSE 0 END) as battery_total,
            SUM(CASE WHEN jd.product_id IN ({$inverterIdsString}) THEN 1 ELSE 0 END) as inverter_total,
            SUM(CASE WHEN jd.product_id IN ({$solarIdsString})    THEN 1 ELSE 0 END) as solar_total
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm ON lm.userloginid = jd.eng_id
        {$where}
    ";

        $result = mysqli_query($link1, $sql);
        if (!$result) return [];

        $row = mysqli_fetch_assoc($result);

        return [
            "name" => "Pie Charts",
            "data" => [
                ["name" => "Battery",       "y" => (int)$row['battery_total'],  "color" => '#3b82f6'],
                ["name" => "Inverter",      "y" => (int)$row['inverter_total'], "color" => '#22c55e'],
                ["name" => "Solar Product", "y" => (int)$row['solar_total'],    "color" => '#f59e0b'],
            ]
        ];
    }

// ---------------------------------------------------------------

    public static function generateagineSnapeShot($link1, $condition = [])
    {
        $batteryIds  = [1, 6, 10, 11, 12, 14];
        $inverterIds = [1];
        $solarIds    = [6, 10, 11, 12];

        $batteryIdsString  = implode(',', $batteryIds);
        $inverterIdsString = implode(',', $inverterIds);
        $solarIdsString    = implode(',', $solarIds);

        // UPDATED BUCKETS
        $buckets = [
            ['label' => '0-3 Days',     'min' => 0,  'max' => 3,  'open' => false],
            ['label' => '4-5 Days',     'min' => 4,  'max' => 5,  'open' => false],
            ['label' => '6-10 Days',    'min' => 6,  'max' => 10, 'open' => false],
            ['label' => '11-15 Days',   'min' => 11, 'max' => 15, 'open' => false],
            ['label' => 'Above 15 Days','min' => 16, 'max' => null, 'open' => true],
        ];

        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }

        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN (
            SELECT zm.stateid
            FROM state_master zm
            WHERE zm.zoneid IN ('{$zoneid}')
        )";
        }

        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN (
            SELECT userloginid
            FROM locationuser_master
            WHERE mapped_bsi IN ('{$bsiId}')
        )";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);

            if($productid === '1'){
                $where .= " AND jd.product_id IN ('1') ";
            }

            if($productid === '2'){
                $where .= " AND jd.product_id NOT IN ('1','6','10','11','12','14') ";
            }

            if ($productid === '3') {
                $where .= " AND jd.product_id IN ('6','10','11','12')";
            }
        }

        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }

        // TOTAL PENDING CALL SAME AS totoalPendingCall()
        $totalSql = "
        SELECT COUNT(jd.job_id) as total_calls
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm
            ON lm.userloginid = jd.eng_id
        {$where}
    ";

        $totalResult = mysqli_query($link1, $totalSql);
        if (!$totalResult) return [];

        $totalRow   = mysqli_fetch_assoc($totalResult);
        $grandTotal = (int)($totalRow['total_calls'] ?? 0);

        $bucketCases_battery  = [];
        $bucketCases_inverter = [];
        $bucketCases_solar    = [];
        $bucketCases_total    = [];

        foreach ($buckets as $i => $bucket) {

            $min = $bucket['min'];

            $agingCond = $bucket['open']
                ? "DATEDIFF(NOW(), jd.open_date) >= {$min}"
                : "DATEDIFF(NOW(), jd.open_date) BETWEEN {$min} AND {$bucket['max']}";

            $bucketCases_battery[]  =
                "SUM(
                CASE
                    WHEN ({$agingCond})
                    AND jd.product_id NOT IN ({$batteryIdsString})
                    THEN 1 ELSE 0
                END
            ) as b_battery_{$i}";

            $bucketCases_inverter[] =
                "SUM(
                CASE
                    WHEN ({$agingCond})
                    AND jd.product_id IN ({$inverterIdsString})
                    THEN 1 ELSE 0
                END
            ) as b_inverter_{$i}";

            $bucketCases_solar[] =
                "SUM(
                CASE
                    WHEN ({$agingCond})
                    AND jd.product_id IN ({$solarIdsString})
                    THEN 1 ELSE 0
                END
            ) as b_solar_{$i}";

            $bucketCases_total[] =
                "SUM(
                CASE
                    WHEN ({$agingCond})
                    THEN 1 ELSE 0
                END
            ) as b_total_{$i}";
        }

        $allCases = array_merge(
            $bucketCases_battery,
            $bucketCases_inverter,
            $bucketCases_solar,
            $bucketCases_total
        );

        $sql = "
        SELECT " . implode(', ', $allCases) . "
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm
            ON lm.userloginid = jd.eng_id
        {$where}
    ";

        $result = mysqli_query($link1, $sql);
        if (!$result) return [];

        $row = mysqli_fetch_assoc($result);

        $rows          = [];
        $totalBattery  = 0;
        $totalInverter = 0;
        $totalSolar    = 0;
        $totalCalls    = 0;

        foreach ($buckets as $i => $bucket) {

            $battery  = (int)($row["b_battery_{$i}"] ?? 0);
            $inverter = (int)($row["b_inverter_{$i}"] ?? 0);
            $solar    = (int)($row["b_solar_{$i}"] ?? 0);
            $calls    = (int)($row["b_total_{$i}"] ?? 0);

            $percentage = $grandTotal > 0
                ? round(($calls / $grandTotal) * 100, 2)
                : 0;

            $rows[] = [
                $bucket['label'],
                (string)$battery,
                (string)$inverter,
                (string)$solar,
                (string)$calls,
                $percentage . '%'
            ];

            $totalBattery  += $battery;
            $totalInverter += $inverter;
            $totalSolar    += $solar;
            $totalCalls    += $calls;
        }

        $finalPercentage = $grandTotal > 0
            ? round(($totalCalls / $grandTotal) * 100, 2)
            : 0;

        return [
            [
                "rows" => $rows
            ],
            [
                "total" => [
                    "Total",
                    (string)$totalBattery,
                    (string)$totalInverter,
                    (string)$totalSolar,
                    (string)$totalCalls,
                    $finalPercentage . '%'
                ]
            ]
        ];
    }

// ---------------------------------------------------------------

    public static function generatependingCallByStatus($link1, $condition = [])
    {
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }

        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }


        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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

        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = (int)$condition['product'];
            $where .= " AND jd.product_id = '{$productid}' ";
        }

        // FIX: Total aur status counts — ek hi query mein
        $sql = "
        SELECT
            COUNT(jd.job_id) as total_jobs,
            SUM(CASE WHEN jd.status = '1'  THEN 1 ELSE 0 END) as unassigned,
            SUM(CASE WHEN jd.status = '2'  THEN 1 ELSE 0 END) as assigned,
            SUM(CASE WHEN jd.status = '3'  THEN 1 ELSE 0 END) as part_not_assigned,
            SUM(CASE WHEN jd.status = '7'  THEN 1 ELSE 0 END) as work_in_progress,
            SUM(CASE WHEN jd.status = '81' THEN 1 ELSE 0 END) as replacement_request
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm ON lm.userloginid = jd.eng_id
        {$where}
    ";

        $result = mysqli_query($link1, $sql);
        if (!$result) return [];

        $row       = mysqli_fetch_assoc($result);
        $totalJobs = (int)($row['total_jobs'] ?? 0);

        $pct = function($count) use ($totalJobs) {
            return $totalJobs > 0 ? round(($count / $totalJobs) * 100, 2) : 0;
        };

        $assignedCount        = (int)$row['assigned'];
        $partNotAssignedCount = (int)$row['part_not_assigned'];
        $workInProgressCount  = (int)$row['work_in_progress'];
        $unassignedCount      = (int)$row['unassigned'];
        $replacementCount     = (int)$row['replacement_request'];

        return [
            [
                "assigned"          => [(string)$assignedCount,        $pct($assignedCount)        . "%"],
                "part_not_assigned" => [(string)$partNotAssignedCount, $pct($partNotAssignedCount) . "%"],
                "work_in_progress"  => [(string)$workInProgressCount,  $pct($workInProgressCount)  . "%"],
                "unassigned"        => [(string)$unassignedCount,      $pct($unassignedCount)      . "%"],
                "replacement_request" => [(string)$replacementCount,   $pct($replacementCount)     . "%"],
            ],
            [
                "total" => [(string)$totalJobs, "100%"]
            ]
        ];
    }

// ---------------------------------------------------------------
// Pending Replacement Settlement Calls
    public static function rphPendingData($link1,$condition=[]){
        $where = " ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }
        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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
        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }
        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }
        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }
        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = (int)$condition['product'];
            $where .= " AND jd.product_id = '{$productid}' ";
        }
        // value , totoal
        $sql = "SELECT
        COUNT(jd.job_id) AS total_jobs,
        SUM(
            CASE 
                WHEN jd.job_no IN (
                    SELECT job_no
                    FROM replacement_data
                    WHERE 
                        (
                            replace_serial_no = ''
                            AND repl_settle_cat = 'REPLACEMENT'
                        )
                        OR
                        (
                            repl_settle_cat IN ('MIPL_REPLACEMENT', 'CREDIT_NOTE')
                            AND delivery_status = ''
                        )
                )
                THEN 1
                ELSE 0
            END
        ) AS rph
    FROM jobsheet_data jd
    LEFT JOIN locationuser_master lm 
        ON lm.userloginid = jd.eng_id
    WHERE jd.status IN ('82','8') $where";
        $result = mysqli_query($link1, $sql);
        if (!$result) return [];
        $row       = mysqli_fetch_assoc($result);
        $totalJobs = (int)($row['total_jobs'] ?? 0);
        $pct = function($count) use ($totalJobs) {
            return $totalJobs > 0 ? round(($count / $totalJobs) * 100, 2) : 0;
        };
        $assignedCount        = (int)$row['rph'];
        return [(string)$assignedCount,        $pct($assignedCount)."%"];
    }
    public static function rpdPendingData($link1,$condition){
        $where = " ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }
        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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
        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }
        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }
        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }
        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = (int)$condition['product'];
            $where .= " AND jd.product_id = '{$productid}' ";
        }
        $sql = "
    SELECT
        COUNT(jd.job_id) AS total_jobs,

        SUM(
            CASE 
                WHEN jd.job_no IN (
                    SELECT job_no
                    FROM rpd_replacement_data
                    WHERE replace_serial_no = ''
                )
                THEN 1
                ELSE 0
            END
        ) AS rpd_pending

    FROM jobsheet_data jd

    LEFT JOIN locationuser_master lm 
        ON lm.userloginid = jd.eng_id

    WHERE jd.status IN ('82','8') $where
";
        $result = mysqli_query($link1, $sql);
        if (!$result) return [];
        $row       = mysqli_fetch_assoc($result);
        $totalJobs = (int)($row['total_jobs'] ?? 0);
        $pct = function($count) use ($totalJobs) {
            return $totalJobs > 0 ? round(($count / $totalJobs) * 100, 2) : 0;
        };
        $assignedCount        = (int)$row['rpd_pending'];
        return [(string)$assignedCount,        $pct($assignedCount)."%"];
    }
    public static function podPendingData($link1,$condition){
        $where = " ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }
        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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
        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }
        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }
        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }
        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = (int)$condition['product'];
            $where .= " AND jd.product_id = '{$productid}' ";
        }
        // value , totoal
        $sql = "SELECT COUNT(jd.job_id) AS total_jobs,
        
        SUM(
            CASE
                WHEN jd.job_no IN (
                    SELECT job_no
                    FROM rpd_replacement_data
                    WHERE 
                        replace_serial_no != ''
                        AND delivery_status = 'YES'
                        AND delivery_doc_path = ''
                )
                THEN 1
                ELSE 0
            END
        ) AS pod_pending_tat3,

    
        SUM(
            CASE
                WHEN jd.job_no IN (
                    SELECT job_no
                    FROM replacement_data
                    WHERE 
                        replace_serial_no != ''
                        AND delivery_status = 'YES'
                        AND delivery_doc_path = ''
                )
                THEN 1
                ELSE 0
            END
        ) AS pod_pending_rph

    FROM jobsheet_data jd

    LEFT JOIN locationuser_master lm 
        ON lm.userloginid = jd.eng_id

    WHERE jd.status IN ('82','8') $where";
        $result = mysqli_query($link1, $sql);
        if (!$result) return [];
        $row       = mysqli_fetch_assoc($result);
        $totalJobs = (int)($row['total_jobs'] ?? 0);
        $pct = function($count) use ($totalJobs) {
            return $totalJobs > 0 ? round(($count / $totalJobs) * 100, 2) : 0;
        };
        $assignedCount        = (int)$row['pod_pending_tat3'];
        return [(string)$assignedCount,        $pct($assignedCount)."%"];
    }
    public static function podPendingDataWithRph($link1,$condition){
        $where = " ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }
        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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
        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }
        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }
        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }
        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = (int)$condition['product'];
            $where .= " AND jd.product_id = '{$productid}' ";
        }
        // value , totoal
        $sql = "SELECT COUNT(jd.job_id) AS total_jobs,
    
    
        SUM(
            CASE
                WHEN jd.job_no IN (
                    SELECT job_no
                    FROM replacement_data
                    WHERE 
                        replace_serial_no != ''
                        AND delivery_status = 'YES'
                        AND delivery_doc_path = ''
                )
                THEN 1
                ELSE 0
            END
        ) AS pod_pending_rph

    FROM jobsheet_data jd

    LEFT JOIN locationuser_master lm 
        ON lm.userloginid = jd.eng_id

    WHERE jd.status IN ('82','8') $where";
//        echo $sql;exit;
        $result = mysqli_query($link1, $sql);
        if (!$result) return [];
        $row       = mysqli_fetch_assoc($result);
        $totalJobs = (int)($row['total_jobs'] ?? 0);
        $pct = function($count) use ($totalJobs) {
            return $totalJobs > 0 ? round(($count / $totalJobs) * 100, 2) : 0;
        };
        $assignedCount1        = $row['pod_pending_rph'];
        return [(string)$assignedCount1,        $pct($assignedCount1)."%"];
    }
//--------------------------------
    public static function SLABreached($link1, $condition = [])
    {
        // STATUS FILTER HAMESHA ACTIVE — conditional nahi
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }

        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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

        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }


        if (isset($condition['aging_bucket']) && !empty($condition['aging_bucket'])) {
            $aging = (int)$condition['aging_bucket'];
        }

        $where .= "
AND TIMESTAMPDIFF(
    HOUR,
    STR_TO_DATE(
        CONCAT(jd.open_date, ' ', jd.open_time),
        '%Y-%m-%d %H:%i:%s'
    ),
    NOW()
) > 72
";

        $sql = "SELECT COUNT(jd.job_id) as total
            FROM `jobsheet_data` jd
            LEFT JOIN `locationuser_master` lm 
                ON lm.userloginid = jd.eng_id
            {$where}";

        $result = mysqli_query($link1, $sql);
        if (!$result) return 0;

        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }
    public static function generateStackData($link1, $condition = [])
    {
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        if (isset($condition['date_range']) && !empty($condition['date_range'])) {
            $date = (int)$condition['date_range'];
            $where .= " AND jd.open_date >= NOW() - INTERVAL {$date} DAY ";
        }

        if(isset($condition['zone']) && !empty($condition['zone'])) {
            $zoneid  = mysqli_real_escape_string($link1, $condition['zone']);
            $where .= " AND jd.state_id IN ( SELECT zm.stateid  FROM state_master zm  WHERE zm.zoneid IN ('{$zoneid}'))";
        }
        if(isset($condition['bsi']) && !empty($condition['bsi'])) {
            $bsiId  = mysqli_real_escape_string($link1, $condition['bsi']);
            $where .= " AND jd.eng_id IN ( SELECT userloginid  FROM locationuser_master  WHERE mapped_bsi in ('{$bsiId}'))";
        }


        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = mysqli_real_escape_string($link1, $condition['product']);
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

        if (isset($condition['state']) && !empty($condition['state'])) {
            $stateid = mysqli_real_escape_string($link1, $condition['state']);
            $where .= " AND jd.state_id = '{$stateid}' ";
        }

        if (isset($condition['enginner']) && !empty($condition['enginner'])) {
            $engid = mysqli_real_escape_string($link1, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$engid}' ";
        }

        if (isset($condition['enginner_type']) && !empty($condition['enginner_type'])) {
            $engType = mysqli_real_escape_string($link1, $condition['enginner_type']);
            $where .= " AND lm.eng_type = '{$engType}' ";
        }

        if (isset($condition['product']) && !empty($condition['product'])) {
            $productid = (int)$condition['product'];
            $where .= " AND jd.product_id = '{$productid}' ";
        }

        // 1. Oldest Pending Call
        $oldestSql = "
        SELECT
            DATEDIFF(NOW(), jd.open_date) as aging_days,
            pm.product_name,
            sm.state,
            cm.city
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm ON lm.userloginid = jd.eng_id
        LEFT JOIN product_master pm      ON pm.product_id  = jd.product_id
        LEFT JOIN state_master sm        ON sm.stateid     = jd.state_id
        LEFT JOIN city_master cm         ON cm.cityid      = jd.city_id
        {$where}
        ORDER BY aging_days DESC
        LIMIT 1
    ";

        $oldestResult = mysqli_query($link1, $oldestSql);
        if (!$oldestResult) return [];
        $oldestRow = mysqli_fetch_assoc($oldestResult);

        // 2. Top State — Highest Average Aging
        $topStateSql = "
        SELECT sm.state, ROUND(AVG(DATEDIFF(NOW(), jd.open_date)), 0) as avg_aging
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm ON lm.userloginid = jd.eng_id
        LEFT JOIN state_master sm        ON sm.stateid     = jd.state_id
        {$where}
        GROUP BY jd.state_id, sm.state
        ORDER BY avg_aging DESC
        LIMIT 1
    ";

        $topStateResult = mysqli_query($link1, $topStateSql);
        if (!$topStateResult) return [];
        $topStateRow = mysqli_fetch_assoc($topStateResult);

        // 3. Top Product — Highest Average Aging
        $topProductSql = "
        SELECT pm.product_name, ROUND(AVG(DATEDIFF(NOW(), jd.open_date)), 0) as avg_aging
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm ON lm.userloginid = jd.eng_id
        LEFT JOIN product_master pm      ON pm.product_id  = jd.product_id
        {$where}
        GROUP BY jd.product_id, pm.product_name
        ORDER BY avg_aging DESC
        LIMIT 1
    ";

        $topProductResult = mysqli_query($link1, $topProductSql);
        if (!$topProductResult) return [];
        $topProductRow = mysqli_fetch_assoc($topProductResult);

        $d=self::giveTopState($link1,$condition);
        return [
            "oldest_pending_call" => [
                // FIX: space add kiya — "0days" → "0 days"
                "days"    => ($oldestRow['aging_days'] ?? 0) . " days",
                "product" => $oldestRow['product_name'] ?? "N/A",
                "address" => trim(($oldestRow['state'] ?? '') . " " . ($oldestRow['city'] ?? '')) ?: "N/A",
            ],
            "top_state" => [
                "state_name" => $d['state']     ?? "N/A",
                "aging_days" => $d['days']. " days",  // FIX: space
            ],
            "top_product" => [
                "product" => $topProductRow['product_name'] ?? "N/A",
                "days"    => ($topProductRow['avg_aging']   ?? 0) . " days",  // FIX: space
            ],
            "sla"=>self::SLABreached($link1,$condition)
        ];
    }

    public static function barBucketData($link1, $bucket){
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";
        $bucket=trim($bucket);
        switch ($bucket) {
            case '0-3':
                $bucketCondition = " DATEDIFF(NOW(), jd.open_date) BETWEEN 0 AND 3 ";
                break;

            case '4-5':
                $bucketCondition = " DATEDIFF(NOW(), jd.open_date) BETWEEN 4 AND 5 ";
                break;

            case '6-10':
                $bucketCondition = " DATEDIFF(NOW(), jd.open_date) BETWEEN 6 AND 10 ";
                break;

            case '11-15':
                $bucketCondition = " DATEDIFF(NOW(), jd.open_date) BETWEEN 11 AND 15 ";
                break;

            case 'Above 15':
                $bucketCondition = " DATEDIFF(NOW(), jd.open_date) > 15 ";
                break;

            default:
                return [];
        }

        $sql = "
        SELECT jd.job_id, jd.job_no,jd.current_location,jd.customer_name,jd.customer_id,jd.contact_no , pm.product_name
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm 
            ON lm.userloginid = jd.eng_id
        LEFT JOIN  product_master pm ON
            jd.product_id=pm.product_id
        {$where}
        AND {$bucketCondition}
        ORDER BY jd.open_date DESC ";

        $result = mysqli_query($link1, $sql);
        if (!$result) {
            return [];
        }
        $data = [];
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }
        return $data;
    }

    public static function donutBucketData($link1, $bucket){

        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        switch ($bucket) {

            case 'unassigned':
                $bucketCondition = " jd.status = '1' ";
                break;

            case 'assigned':
                $bucketCondition = " jd.status = '2' ";
                break;

            case 'part_not_assigned':
                $bucketCondition = " jd.status = '3' ";
                break;

            case 'work_in_progress':
                $bucketCondition = " jd.status = '7' ";
                break;
            case 'replacement_request':
                $bucketCondition = " jd.status = '81' ";break;

            default:
                return [];
        }

        $sql = "
       SELECT jd.job_id, jd.job_no,jd.current_location,jd.customer_name,jd.customer_id,jd.contact_no , pm.product_name FROM jobsheet_data jd LEFT JOIN locationuser_master lm  ON lm.userloginid = jd.eng_id
        LEFT JOIN  product_master pm ON
            jd.product_id=pm.product_id
        {$where}
        AND {$bucketCondition}
        ORDER BY jd.open_date DESC
    ";
        $result = mysqli_query($link1, $sql);
        if (!$result) {
            return [];
        }
        $data = [];
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }
        return $data;
    }
    public static function pieBucketData($link1, $bucket)
    {
        $batteryIds  = [1, 6, 10, 11, 12, 14];
        $inverterIds = [1];
        $solarIds    = [6, 10, 11, 12];
        $bucket=strtolower($bucket);
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        switch ($bucket) {

            case 'battery':
                // jo battery category me aati hain
                $ids = implode(',', $batteryIds);
                $bucketCondition = " jd.product_id IN ({$ids}) ";
                break;

            case 'inverter':
                $ids = implode(',', $inverterIds);
                $bucketCondition = " jd.product_id IN ({$ids}) ";
                break;

            case 'solar product':
                $ids = implode(',', $solarIds);
                $bucketCondition = " jd.product_id IN ({$ids}) ";
                break;

            default:
                return [];
        }

        $sql = "
        SELECT jd.job_id, jd.job_no,jd.current_location,jd.customer_name,jd.customer_id,jd.contact_no , pm.product_name
        FROM jobsheet_data jd
        LEFT JOIN locationuser_master lm 
            ON lm.userloginid = jd.eng_id
            LEFT JOIN  product_master pm ON
            jd.product_id=pm.product_id
        {$where}
        AND {$bucketCondition}
        ORDER BY jd.open_date DESC
    ";

        $result = mysqli_query($link1, $sql);
        if (!$result) {
            return [];
        }
        $data = [];
        while($row = mysqli_fetch_assoc($result)){
            $data[] = $row;
        }

        return $data;
    }
    public static function columnStateBucketData($link1, $bucket)
    {
        $where = " WHERE jd.status IN ('1','2','3','7','81') ";

        // state name safe banane ke liye
        $bucket = mysqli_real_escape_string($link1, $bucket);

        $sql = "
        SELECT jd.job_id, jd.job_no,jd.current_location,jd.customer_name,jd.customer_id,jd.contact_no , pm.product_name
        FROM jobsheet_data jd LEFT JOIN state_master sm 
            ON sm.stateid = jd.state_id LEFT JOIN locationuser_master lm  ON lm.userloginid = jd.eng_id
            LEFT JOIN  product_master pm ON
            jd.product_id=pm.product_id 
            {$where}
        AND sm.state LIKE '%{$bucket}%'
        ORDER BY jd.open_date DESC
    ";

        $result = mysqli_query($link1, $sql);

        if (!$result) {
            return [];
        }

        $data = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }

}