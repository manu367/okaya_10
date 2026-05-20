<?php
//require_once("../includes/config.php");
//global $link1;
//
//$access_brand = getAccessBrand($_SESSION['userid'],$link1);
//$arrstate = getAccessState($_SESSION['userid'],$link1);
//$access_product = getAccessProduct($_SESSION['userid'],$link1);
//
//
//
//header("Content-type: application/json");
//
//
//
///**
// * Class: DataFetchingFromDB
// * Purpose:
// * This class handles all direct database fetching operations.
// *
// * Responsibility:
// * - Fetch master data from database
// * - Return formatted array response
// * - Keep DB related logic centralized
// *
// * Notes:
// * - Only SELECT/FETCH operations should exist here
// * - No business logic should be written here
// * - All methods are static for direct access
// *
// * Example:
// * DataFetchingFromDB::zoneFetching($link1);
// * ------------------------------------------------------------
// */
//class DataFetchingFromDB{
//    /**
//     *
//     * Fetch All Zones
//     *
//     * Retrieves all active zones from zone_master table.
//     *
//     * @param mysqli $link1
//     * Database connection object
//     *
//     * @return array
//     * Returns formatted zone array
//     *
//     * Output Example:
//     * [
//     *   [
//     *      "zone" => 1,
//     *      "zone_name" => "North"
//     *   ]
//     * ]
//     * --------------------------------------------------------
//     */
//    public static function zoneFetching($link1){
//        $zone = [];
//        $res_zone = mysqli_query($link1,
//            "SELECT zonename, zoneid FROM zone_master"
//        );
//        while($row_zone = mysqli_fetch_assoc($res_zone)){
//
//            $zone[] = [
//                "zone" => $row_zone['zoneid'],
//                "zone_name" => $row_zone['zonename']
//            ];
//
//        }
//        return $zone;
//    }
//    /**
//     *
//     * Fetch States By Zone
//     *
//     * Retrieves state list based on:
//     * - accessible state ids
//     * - optional zone filter
//     *
//     * @param mysqli $link1
//     * Database connection object
//     *
//     * @param string $arrstate
//     * Comma separated state ids
//     * Example: "1,2,3"
//     *
//     * @param int $statezone
//     * Optional zone id filter
//     *
//     * @return array
//     * Returns formatted state data
//     *
//     */
//    public static function getAllStatesbyZone($link1, $arrstate, $statezone = 0){
//        $states = [];
//        $condition = "";
//        if (!empty($statezone)) {
//            $condition = " AND zoneid = '$statezone'";
//        }
//
//        $query = "
//        SELECT stateid, state, zoneid, statecode
//        FROM state_master
//        WHERE stateid IN ($arrstate)
//        $condition
//        ORDER BY state";
//
//        $result = mysqli_query($link1, $query);
//
//        while ($stateinfo = mysqli_fetch_assoc($result)) {
//
//            $states[] = [
//                "stateid"   => $stateinfo['stateid'],
//                "state"     => $stateinfo['state'],
//                "zoneid"    => $stateinfo['zoneid'],
//                "statecode" => $stateinfo['statecode']
//            ];
//        }
//        return $states;
//    }
//    /**
//     *
//     * Fetch Locations By State
//     *
//     * Retrieves all service locations using state ids.
//     *
//     * Excluded Types:
//     * - WH
//     * - CC
//     *
//     * Conditions:
//     * - Only active locations
//     *
//     * @param mysqli $link1
//     * Database connection object
//     *
//     * @param array $statestr_1
//     * Array of state ids
//     *
//     * @return array
//     * Returns formatted location list
//     *
//     */
//    public static function getAllLocations($link1, $statestr_1 = [])
//    {
//        $locations = [];
//
//        $stateIds = array_map('intval', $statestr_1);
//        $statestr = implode(",", $stateIds);
//
//        $location_query = "
//        SELECT locationname, location_code
//        FROM location_master
//        WHERE stateid IN ($statestr)
//        AND locationtype NOT IN ('WH', 'CC')
//        AND statusid = '1'
//        ORDER BY locationname";
//        $loc_res = mysqli_query($link1, $location_query);
//        while ($loc_info = mysqli_fetch_assoc($loc_res)) {
//            $locations[] = [
//                "locationname" => $loc_info['locationname'],
//                "location_code" => $loc_info['location_code']
//            ];
//        }
//        return $locations;
//    }
//
//    /**
//     *
//     * Fetch Accessible Products
//     *
//     * Retrieves product list based on accessible product ids.
//     *
//     * Conditions:
//     * - Product status must be active
//     *
//     * @param mysqli $link1
//     * Database connection object
//     *
//     * @param string $access_product
//     * Comma separated product ids
//     *
//     * @return array
//     * Returns formatted product list
//     *
//     */
//    public static function getAllProducts($link1, $access_product = '')
//    {
//        $products = [];
////        $access_product = str_replace("'", "", $access_product);
////        // Convert string to array
////        $productArray = explode(",", $access_product);
////        $productIds = array_map('intval', $productArray);
////        $productStr = implode(",", $productIds);
//
////        if (empty($productStr)) {
////            return [];
////        }
////        $model_query = " SELECT product_id, product_name FROM product_master WHERE status = '1'
////        AND product_id IN ($productStr) ORDER BY product_name ";
////
////        $check1 = mysqli_query($link1, $model_query);
////
////        while ($br = mysqli_fetch_assoc($check1)) {
//////            $products[] = ["product_id"   => $br['product_id'], "product_name" => $br['product_name']];
////        }
//        $products[] = ["product_id"   => "1", "product_name" => "Inverter"];
//        $products[] = ["product_id"   => "2", "product_name" => "Battery"];
//        $products[] = ["product_id"   => "3", "product_name" => "Solor"];
//
//        return $products;
//    }
//
//    /**
//     * --------------------------------------------------------
//     * Fetch Engineer Types
//     * --------------------------------------------------------
//     * Static engineer type mapping.
//     *
//     * @param mysqli $link1
//     * Unused DB parameter (kept for consistency)
//     *
//     * @return array
//     * Returns engineer type list
//     * --------------------------------------------------------
//     */
//    public static function enginnerType($link1){
//        $types = [];
//        $sql = "SELECT type FROM `locationuser_master` GROUP BY type";
//        $result = mysqli_query($link1, $sql);
//        $i=0;
//        while ($row = mysqli_fetch_assoc($result)) {
//            $types[$i] =  $row['type'];
//            $i++;
//        }
//        return $types;
//    }
//    public static function getAllBSI($link1){
//        $bsi=[];
//        $sql="SELECT * FROM admin_users WHERE designation_id = 45 AND status = '1'";
//        $result = mysqli_query($link1, $sql);
//        while ($row = mysqli_fetch_assoc($result)) {
//            $bsi[] = ["sapid"=>$row['sapid'],"username"=>$row['username']];
//        }
//        return $bsi;
//    }
//}
//
///**
// *  this class is used to play with user data and working like a bridge beween user and db
// *   acccodung to user requirement they will recive data from the db
// */
//class FormInputHandling{
//    public $wrapper_Data=[];
//    public function giveRangeData($link1,$arrstate,$access_product){
//        $inputresponse=[];
//        $inputresponse['data_range']=[
//            0=>"7",
//            1=>"15",
//            2=>"30",
//            3=>"45",
//            4=>"90",
//        ];
//        $inputresponse['zone']=DataFetchingFromDB::zoneFetching($link1);
//        $inputresponse['zone_wise_state']=DataFetchingFromDB::getAllStatesbyZone($link1, $arrstate);
//        $inputresponse['bsi']=DataFetchingFromDB::getAllBSI($link1);
//        $inputresponse['enginnertype']=DataFetchingFromDB::enginnerType($link1);
//        $inputresponse['poduct']=DataFetchingFromDB::getAllProducts($link1, $access_product);
//        $inputresponse['all_busket']=[];
//        $inputresponse['status']=[0=>"Active", 1=>"Pending"];
//        return ($inputresponse);
//    }
//
//    public static function cardData(){
//        return [
//            "total_pending_calls"=>"12481",
//            "avg_aging"=>"24",
//            "pending_days"=>"987",
//            "high_priority_pending"=>"2121",
//        ];
//    }
//
//    public static function giveBarChartData(){
//        return [
//            "x_categories"=>['0 - 3 Days', '4 - 5 Days', '5 - 10 Days', '10 - 15 Days', 'Above 15 Days'],
//            "y_label"=>"Number of calls",
//            "data"=>[
//                [ "y"=> 112, "color"=> '#22c55e' ],
//                [ "y"=> 236, "color"=> '#f97316' ],
//                [ "y"=> 430, "color"=> '#ef4444' ],
//                [ "y"=> 280, "color"=> '#f97316' ],
//                [ "y"=> 190, "color"=> '#b91c1c']
//            ]
//        ];
//    }
//    public static function giveColumnChartData(){
//        return [
//            "x_categories"=>['Delhi','Karnataka','West Bengal','Gujarat','Madhya Pradesh','Bihar','Rajasthan','Maharashtra','Uttar Pradesh'],
//            "y_label"=>"Number of Calls",
//            "data"=>[66, 72, 90, 98, 122, 154, 186, 298, 362]
//        ];
//    }
//    public static function pieChartData(){
//        return [
//            "name"=>"Pie Charts",
//            "data"=>[
//                ["name"=> 'Battery', "y"=> 652, "color"=> '#3b82f6' ],
//                ["name"=> 'Inverter',      "y"=> 412,  "color"=> '#22c55e' ],
//                ["name"=> 'Solar Product', "y"=> 184, "color"=> '#f59e0b' ],
//            ]
//        ];
//    }
//    public static function agingSnapshotData(){
//        return [
//            ["rows"=>["0-2 Days","65","31","16","112","9.0%"],
//                ["0-2 Days","65","31","16","112","9.0%"],
//                ["0-2 Days","65","31","16","112","9.0%"],
//                ["0-2 Days","65","31","16","112","9.0%"],
//                ["0-2 Days","65","31","16","112","9.0%"],
//                ["0-2 Days","65","31","16","112","9.0%"]
//            ],
//            ["total"=>["total","65","31","16","112","100%"]]
//        ];
//    }
//    public static function pending_call_by_status(){
//        return [
//            [
//                "assigned"=>["900","10%"],
//                "part_not_assigned"=>["900","10%"],
//                "work_in_progress"=>["900","10%"],
//                "unassigned"=>["900","10%"],
//                "replacement_request"=>["900","10%"]
//            ],
//            [
//                "total"=>["2000","100%"],
//            ]
//        ];
//    }
//}
//
//
//
//$formInputhandling=new FormInputHandling();
//if(isset($_GET['form_input_data'])){
//    echo json_encode($formInputhandling->giveRangeData($link1,$arrstate,$access_product));
//    exit();
//}
//
//
//if(isset($_REQUEST['form_submit'])){
//
//    $wrapper_data=[];
//    $wrapper_data['cards_data']=FormInputHandling::cardData();
//
//    $wrapper_data['chart_details']=[
//        'bar_chart'=>FormInputHandling::giveBarChartData(),
//        'column_chart'=>FormInputHandling::giveColumnChartData(),
//        'pie_chart'=>FormInputHandling::pieChartData(),
//    ];
//    $wrapper_data['aging_snapshot']=FormInputHandling::agingSnapshotData();
//    $wrapper_data['pending_call_by_status']=FormInputHandling::pending_call_by_status();
//    echo json_encode($wrapper_data);exit();
//}
