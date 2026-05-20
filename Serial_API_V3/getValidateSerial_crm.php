<?php 
include_once 'jwt_functions.php';
$jwtf = new JWT_Functions();
//print_r($jwtf);exit;
/**  * Creates fault detail data as JSON  */    
include_once 'get_functions_crm.php';
$get = new GET_Functions();
////// get JSON data
$data = json_decode(file_get_contents("php://input"));
$ak = $data->accessKey;

$serial_no = preg_replace('/[^a-zA-Z0-9]/s', '', $data->serialNumber);
//$serial_no = $data->serialNumber;
$product_id2 = $data->product_id;
//// validate parameter
$access_key = $jwtf->validateParameter('Access Key',$ak,STRING);
$serialno = $jwtf->validateParameter('Serial No.',$serial_no,STRING);
$product_id = $jwtf->validateParameter('Product Id',$product_id2,STRING);
//try{
	////// get JWT token
	///$token = $jwtf->getBearerToken();
	///// validate token
	//$decode_resp = $jwtf->decodeJWT($token,$user_id);
	//if($decode_resp == "SUCCESS_RESPONSE"){
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     // The request is using the POST method
		//print_r(ACCESS_KEY);exit;
		if($access_key==ACCESS_KEY){
			if($serialno==""){
				$jwtf->returnResponse(FAILED_RESPONSE,$pager,"Serial No. should not be blank");
			}else{
				$resp_sr = $get->getValidSerial($serialno,$product_id);
				
				if (is_array($resp_sr)){
					$a = $resp_sr;
					$jwtf->returnResponse(SUCCESS_RESPONSE,$pager,$a);    
				}
			}
		}else{
			$jwtf->returnResponse(ACCESS_TOKEN_ERROR,$pager,"Invalid Access Key");
		}		
	}else{
		$jwtf->returnResponse(REQUEST_METHOD_NOT_VALID,$pager,"Method Not Allowed");
	}
?>