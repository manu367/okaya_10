<?php
require_once("../includes/config.php");
require_once("dashboard-pendingcall-dbops.php");
global $link1;
$arrstate = getAccessState($_SESSION['userid'],$link1);
function loaderUser_12($link1,$sapid,$userid){
    $restunrData=['type'=>'','data'=>''];
    $sql="select sapid,username,name , designation_id from admin_users where sapid='$sapid' and username='$userid'";
    $result=mysqli_query($link1,$sql);
    if(!$result){
        exit('Error'.__LINE__);
    }
    if(mysqli_num_rows($result)===0){
        exit('Error'.__LINE__);
    }
    $row=mysqli_fetch_assoc($result);
    if($row['designation_id']==='45'){
        $restunrData['type']='bsi';
    }else{
        $restunrData['type']='admin';
    }
    $restunrData['data']=$row;
    return $restunrData;
}

$access_brand = getAccessBrand($_SESSION['userid'],$link1);
$access_product = getAccessProduct($_SESSION['userid'],$link1);


$userid=$_SESSION['userid']??'';
$sapid=$_SESSION['sapid']??'';
$user=loaderUser_12($link1,$sapid,$userid);

header("Content-type: application/json");


/**
 *  this class is used to play with user data and working like a bridge beween user and db
 *   acccodung to user requirement they will recive data from the db
 */
class FormInputHandling{
    public $wrapper_Data=[];
    public function giveRangeDataForBSI($link1,$arrstate,$access_product,$condition=[]){
        $bsi=new BSIFetchingFromDBMetaData($link1,$condition['bsi']);
        $inputresponse=[];
        $inputresponse['data_range']=[
            0=>"7",
            1=>"15",
            2=>"30",
            3=>"45",
            4=>"90",
        ];
        $inputresponse['zone']=$bsi->zoneDisplay($condition['bsi']);
        $inputresponse['zone_wise_state']=$bsi->getState($condition['bsi'],$arrstate);
        $inputresponse['bsi']=$bsi->getBSI($condition['bsi']);
        $inputresponse['enginnertype']=DataFetchingFromDB::enginnerType($link1);
        $inputresponse['poduct']=DataFetchingFromDB::getAllProducts($link1, $access_product);
        $inputresponse['all_busket']=[];
        $inputresponse['status']=[0=>"Active", 1=>"Pending"];
        return ($inputresponse);
    }

    public function giveRangeData($link1,$arrstate,$access_product){
        $inputresponse=[];
        $inputresponse['data_range']=[
            0=>"7",
            1=>"15",
            2=>"30",
            3=>"45",
            4=>"90",
        ];
        $inputresponse['zone']=DataFetchingFromDB::zoneFetching($link1);
        $inputresponse['zone_wise_state']=DataFetchingFromDB::getAllStatesbyZone($link1, $arrstate);
        $inputresponse['bsi']=DataFetchingFromDB::getAllBSI($link1,0);
        $inputresponse['enginnertype']=DataFetchingFromDB::enginnerType($link1);
        $inputresponse['poduct']=DataFetchingFromDB::getAllProducts($link1, $access_product);
        $inputresponse['all_busket']=[];
        $inputresponse['status']=[0=>"Active", 1=>"Pending"];
        return ($inputresponse);
    }

    public static function cardData($link1,$condition){
        $pending_2_days=DataFetchingFromDB::pendingDays($link1,$condition);

        return [
            "total_pending_calls"=>DataFetchingFromDB::totoalPendingCall($link1,$condition),
            "avg_aging"=>DataFetchingFromDB::avgAging($link1,$condition),
            "pending_days"=>$pending_2_days['total'],
            "pending_days_percentage"=>$pending_2_days['percentage'],
            "high_priority_pending"=>DataFetchingFromDB::high_priority_pending($link1,$condition),
        ];
    }

    public static function giveBarChartData($link1,$condition=[]){
        return DataFetchingFromDB::generateBarChartDataFromDB($link1,$condition);
    }
    public static function giveColumnChartData($link1,$condition=[]){
        return DataFetchingFromDB::generateColumnChartDataFromDb($link1,$condition);
    }
    public static function pieChartData($link1,$condition=[]){
        return DataFetchingFromDB::generatepieChartDataFromdb($link1,$condition);
    }
    public static function agingSnapshotData($link1,$condition=[]){
        return DataFetchingFromDB::generateagineSnapeShot($link1,$condition);
    }
    public static function pending_call_by_status($link1,$condition){
        return DataFetchingFromDB::generatependingCallByStatus($link1,$condition);
    }
    public static function stackData($link1,$condition){
        return DataFetchingFromDB::generateStackData($link1,$condition);
    }
    public static function responseData($status,$data){
        echo json_encode(['status'=>$status,'data'=>$data]);exit();
    }

    public static function rphPending($link1,$condition=[]){
        return DataFetchingFromDB::rphPendingData($link1,$condition);
    }
    public static function distributerPending($link1,$condition=[]){
        return DataFetchingFromDB::distribtePendingData($link1,$condition);
    }
    public static function dialerPending($link1,$condition=[]){
        return DataFetchingFromDB::dealerPendingData($link1,$condition);
    }
    public static function distributerPendingtat3($link1,$condition=[]){
        return DataFetchingFromDB::distribtePendingDatatat3($link1,$condition);
    }
    public static function dialerPendingtat3($link1,$condition=[]){
        return DataFetchingFromDB::dealerPendingDatagtat3($link1,$condition);
    }
    public static function rpdPending($link1,$condition=[]){
        return [0,0];
    }
    public static function podPending($link1,$condition=[]){
        return DataFetchingFromDB::podPendingData($link1,$condition);
    }
    public static function podPendingRPH($link1,$condition=[]){
        return DataFetchingFromDB::podPendingDataWithRph($link1,$condition);
    }

    /// === give bucket data
    public static function barBucket($link,$bucket){
        return DataFetchingFromDB::barBucketData($link,$bucket);
    }
    public static function donutBucket($link,$bucket){
        return DataFetchingFromDB::donutBucketData($link,$bucket);
    }
    public static function pieBucket($link,$bucket){
        return DataFetchingFromDB::pieBucketData($link,$bucket);
    }
    public static function columnStateBucket($link,$bucket){
        return DataFetchingFromDB::columnStateBucketData($link,$bucket);
    }
}



$formInputhandling=new FormInputHandling();
if(isset($_GET['form_input_data'])){
    $condition=[];
    if($user['type']==='bsi'){
        $condition['bsi']=$user['data']['sapid'];
        echo json_encode($formInputhandling->giveRangeDataForBSI($link1,$arrstate,$access_product,$condition));
        exit();
    }
    echo json_encode($formInputhandling->giveRangeData($link1,$arrstate,$access_product));
    exit();
}

if(isset($_REQUEST['form_submit'])){

    $wrapper_data=[];
    $condition=[];
    $condition['date_range']=$_REQUEST['data_range']??'';
    $condition['zone']=$_REQUEST['zone']??'';
    $condition['state']=$_REQUEST['state']??'';
    $condition['bsi']=$_REQUEST['bsi']??'';
    $condition['enginner']=$_REQUEST['enginner']??'';
    $condition['enginner_type']=$_REQUEST['enginner_type']??'';
    $condition['product']=$_REQUEST['product']??'';
    $condition['aging_bucket']=$_REQUEST['aging_bucket']??'';
    $condition['status']='1,2,3,7,81';


    $wrapper_data['cards_data']=FormInputHandling::cardData($link1,$condition);

    $wrapper_data['chart_details']=[
        'bar_chart'=>FormInputHandling::giveBarChartData($link1,$condition),
        'column_chart'=>FormInputHandling::giveColumnChartData($link1,$condition),
        'pie_chart'=>FormInputHandling::pieChartData($link1,$condition),
    ];
    $wrapper_data['aging_snapshot']=FormInputHandling::agingSnapshotData($link1,$condition);
    $wrapper_data['pending_call_by_status']=FormInputHandling::pending_call_by_status($link1,$condition);
    $wrapper_data['stack_data']=FormInputHandling::stackData($link1,$condition);

    $wrapper_data['pending_replacement_settlement']=[
        "rph_pending"=>FormInputHandling::rphPending($link1,$condition),
        "distributor_pending"=>FormInputHandling::distributerPending($link1,$condition),
        "dealer_pending"=>FormInputHandling::dialerPending($link1,$condition),
        "distributor_pending_tat3"=>FormInputHandling::distributerPendingtat3($link1,$condition),
        "dealer_pending_tat3"=>FormInputHandling::dialerPendingtat3($link1,$condition),
        "rprd_pending"=>FormInputHandling::rpdPending($link1,$condition),
        "pord_pending"=>FormInputHandling::podPending($link1,$condition),
        "pord_pending_rph"=>FormInputHandling::podPendingRPH($link1,$condition),
    ];
    echo json_encode($wrapper_data);exit();
}

if(isset($_REQUEST['bsi'])){
    $bsistate=$_REQUEST['bsi'];
    $data=DataFetchingFromDB::getAllBSI($link1,$bsistate);
    echo json_encode($data);exit();
}


// 0-3 , 4-5 , 6-10 , 11-15 , Above 15
if(isset($_REQUEST['bar_bucket'])){
    $bar_bucket=$_REQUEST['bar_bucket']??'';

    echo json_encode(FormInputHandling::barBucket($link1,$bar_bucket));exit();
}

// unassigned , assigned , part_not_assigned , work_in_progress , replacement_request
if(isset($_REQUEST['dunut_bucket'])){
    $dunut_bucket=$_REQUEST['dunut_bucket']??'';
    echo json_encode(FormInputHandling::donutBucket($link1,$dunut_bucket));exit();
}

//inverter , solar , battery
if(isset($_REQUEST['pie_bucket'])){
    $pie_bucket=$_REQUEST['pie_bucket']??'';
    echo json_encode(FormInputHandling::pieBucket($link1,$pie_bucket));exit();
}
// state name
if(isset($_REQUEST['state_bucket'])){
    $snapshoit_bucket=$_REQUEST['state_bucket']??'';
    echo json_encode(FormInputHandling::columnStateBucket($link1,$snapshoit_bucket));exit();
}