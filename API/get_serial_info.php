<?php
ob_clean(); // clear any output buffer that may send unwanted characters
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // allow browser access if needed
include("../includes/check_serial_no_for_job.php");
include("constant.php");
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$link1 = $private_variable->getValue($db);

////// get JSON data
$data = json_decode(file_get_contents("php://input"));

$serial_no = $data->serial_no;
$job_no = $data->job_no;
$product_id = $data->product_id;
$location_code = $data->location_code;
$ref_no = $data->ref_no;
$access_key = $data->accessKey;
$arr = array();
try{
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if($access_key==ACCESS_KEY){
			//print_r($serial_no);exit;
			if($serial_no){
                /////check in jobsheet data
                //$resp_jd = getJobSheetValidate($serial_no,$job_no,$product_id,$location_code,$ref_no,$link1);
				$resp_jd = getserial_infodata($serial_no,$job_no,$product_id,$location_code,$ref_no,$link1);
                $decod_jd = json_decode($resp_jd,true);
				//print_r($decod_jd);exit;
                if($decod_jd['status']==1){
                    echo json_encode($decod_jd,JSON_UNESCAPED_UNICODE);
                    exit;
                }
				/*else{
                    /////check in DMS data
                    $resp_sd = getSaleDataValidate($serial_no,$job_no,$product_id,$location_code,$ref_no,$link1);
                    $decod_sd = json_decode($resp_sd,true);
                    if($decod_sd['status']==1){
                        echo json_encode($decod_sd);
                        exit;
                    }else{
                        /////check in Spilt serial data
                        $resp_ss = getSerialSplitLogic($serial_no,$job_no,$product_id,$location_code,$ref_no,$link1);
                        $decod_ss = json_decode($resp_ss,true);
                        if($decod_ss['status']==1){
                            echo json_encode($decod_ss);
                            exit;
                        }else{
                            $arr["msg"] = "Not found in DB";
                            $arr["status"] = "0";
                        }
                    }
                }*/
            }else{
                $arr["msg"] = "Serial no. is mandatory";
                $arr["status"] = "0";    
            }
		}else{
            $arr["msg"] = "Invalid Access Key";
            $arr["status"] = "0";
		}
	}else{
        $arr["msg"] = "Method Not Allowed";
        $arr["status"] = "0";
	}	
}catch(Exception $e){
	$arr["msg"] = "something went wrong";
    $arr["status"] = "0";
}
echo json_encode($arr,JSON_UNESCAPED_UNICODE);
?>