<?php
require_once("../includes/config.php");
$response = ["status" => true, "message" => ""];
global $link1;

set_exception_handler(function ($e){
    header('Content-Type: application/json');
    global $response;
    if ($e instanceof Exception) {
        $response["status"] = false;
        $response["message"] = $e->getMessage();
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
});

if (isset($_GET['contacts'])) {
    header('Content-Type: application/json');
    try{
        $contact = mysqli_real_escape_string($link1, $_GET['contacts']);
        RequestHandler::contactchecker($link1,$contact);
        exit();
    }catch (Exception $exception){
        $response["status"] = false;
        $response["message"] = $exception->getMessage();
    }
}

if(isset($_GET['imei'])){

    header('Content-Type: application/json');
    try{
        $imei = mysqli_real_escape_string($link1, $_GET['imei']);
        RequestHandler::imeicheck($link1,$imei);
    }
    catch (Exception $e){
        RequestHandler::response(false,$e->getMessage());
    }

    exit();
}








//SELECT distinct(model_id),model FROM model_master where product_id in ('$prodstr')  and brand_id in ('$brandstr')
//select  status_id , main_status_id,system_status from jobstatus_master where status_id in ('1','2','3','5','6','7','8','11','12','48','49','50','55','56') and (status_id = main_status_id )
class RequestHandler
{
    public static function contactchecker($link1,$contact){
        $response = ["status"=>false, "message"=>[]];

        $sql1 = "SELECT contact_no FROM jobsheet_data 
             WHERE contact_no LIKE '%$contact%'";

        $sql2 = "SELECT contact_no FROM ticket_master 
             WHERE contact_no LIKE '%$contact%' 
             OR alternate_no LIKE '%$contact%'";

        $result1 = mysqli_query($link1, $sql1);
        $result2 = mysqli_query($link1, $sql2);

        if ($result1 && mysqli_num_rows($result1)>0) {
            $response["status"] = true;
            while ($row = mysqli_fetch_assoc($result1)) {
                $response['message'][] = ['contact' => $row['contact_no']];
            }
        }

        if ($result2 && mysqli_num_rows($result2)>0) {
            $response["status"] = true;
            while ($row = mysqli_fetch_assoc($result2)) {
                $response['message'][] = ['contact' => $row['contact_no']];
            }
        }

        // remove duplicate numbers (optional but recommended)
        if (!empty($response['message'])) {
            $response['message'] = array_values(
                array_unique($response['message'], SORT_REGULAR)
            );

            echo json_encode($response);
        } else {
            throw new Exception("Empty Data");
        }
    }
    public static function imeicheck($link1,$imei){
        $imei_data=[];
        $sql1="SELECT imei FROM `jobsheet_data` WHERE imei LIKE '%$imei%'";
        $result=mysqli_query($link1,$sql1);

        if ($result && mysqli_num_rows($result)>0) {

            while ($row = mysqli_fetch_assoc($result)) {
                $imei_data[]=$row['imei'];
            }
        }else{
            RequestHandler::response(false,"No Data Found");
        }

        if (!empty($imei_data)) {
            $imei_data= array_values(
                array_unique($imei_data, SORT_REGULAR)
            );
            RequestHandler::response(true,$imei_data);
        } else {
            RequestHandler::response(false,"No Data Found");
        }
    }

    public static function response($status,$messgae){
        echo json_encode([
            "status"=>$status,
            "message"=>$messgae
        ]);
        exit();
    }
}
