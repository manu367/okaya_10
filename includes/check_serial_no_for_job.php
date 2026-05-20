<?php
function getserial_infodata($serialno,$jobno="",$productid="",$locationcode="",$refno="",$link1){

	//$mfd_ex=date('Y-m-d', strtotime($mm_mfd. ' + '.$ws_days.' days'));
	$level_3_query="SELECT * FROM  warranty_data where serial_no='".$serialno."'";
	$serial_check1=mysqli_query($link1,$level_3_query);
	if(mysqli_num_rows($serial_check1)>0){
	$serial_row1 = mysqli_fetch_array($serial_check1);
	$level_query1="SELECT model,model_id, wp,product_id,brand_id FROM model_master where model_id='".$serial_row1['model_id']."'  and status='1'";
	$check21=mysqli_query($link1,$level_query1);
	$br1 = mysqli_fetch_array($check21);
	
	$sql_br="SELECT brand FROM brand_master where brand_id='".$br1['brand_id']."'";
	$rs_br=mysqli_query($link1,$sql_br);
	$row_br = mysqli_fetch_array($rs_br);
	
	$sql_prd="SELECT product_name FROM product_master where product_id='".$br1['product_id']."'";
	$rs_prd=mysqli_query($link1,$sql_prd);
	$row_prd = mysqli_fetch_array($rs_prd);
		//print_r($br1);exit;
	if($serialno!=''){
	//print_r($br);exit;
	//print_r($mm_mfd);exit;
	 $arr_jd["msg"] = "success";
     $arr_jd["status"] = "1";
     $arr_jd["source"] = "WR";
	 $arr_jd["product_id"] = $br1['product_id'];
     $arr_jd["brand_id"] = $br1['brand_id'];
     $arr_jd["model_id"] = $br1['model_id'];
	 $arr_jd["brand"]=$row_br['brand'];
	 $arr_jd["product"]=$row_prd['product_name'];
	 $arr_jd["model_code"] = $serial_row1['model_code'];
     $arr_jd["model"] = $br1['model'];
	 $arr_jd["warrenty_day"] = $br1['wp'];
     $arr_jd["dwp"] = $br1['dwp'];
     $arr_jd["mfd"] = "";
	 $arr_jd["mfd_ex"] = "";
     $arr_jd["dop"] = "";
	}else{
	 $arr_jd["msg"] = "fail";
     $arr_jd["status"] = "0";
	}
	}
	
	//print_r($arr_jd);exit;
	return json_encode($arr_jd);
	
}

//////////// check from jobsheet data
function getJobSheetValidate($serialno,$jobno="",$productid="",$locationcode="",$refno="",$link1){
	
    $arr_jd = array();
    /////fetch details from JD
    $filter = "";
    if($jobno){
        $filter .= " AND job_no='".$jobno."'";
    }
    if($productid){
        $filter .= " AND product_id='".$productid."'";
    }
    if($locationcode){
        $filter .= " AND current_location='".$locationcode."'";
    }
    $res_jd = mysqli_query($link1,"SELECT * FROM jobsheet_data WHERE imei='".$serialno."' ".$filter." ORDER BY job_id DESC");
	
    if(mysqli_num_rows($res_jd)>0){
		
        $row_jd = mysqli_fetch_assoc($res_jd);
		
        $arr_jd["msg"] = "success";
        $arr_jd["status"] = "1";
        $arr_jd["source"] = "JD";
        $arr_jd["product_id"] = $row_jd['product_id'];
        $arr_jd["brand_id"] = $row_jd['brand_id'];
        $arr_jd["model_id"] = $row_jd['model_id'];
        $arr_jd["model"] = $row_jd['model'];
        $arr_jd["open_date"] = $row_jd['open_date'];
        $arr_jd["dop"] = $row_jd['dop'];
		
        $arr_jd["mfd"] = $row_jd['mfd'];
        $arr_jd["mfd_ex"] = $row_jd['manufactured_expiry_date'];
		$arr_jd["warrenty_day"] = $row_jd['warranty_days'];
        $arr_jd["dwp"] = getAnyDetails2($row_jd['model_id'],"dwp","model_id","model_master",$link1);
        $arr_jd["call_for"] = $row_jd['call_for'];
        $arr_jd["customer_name"] = $row_jd['customer_name'];
        $arr_jd["customer_id"] = $row_jd['customer_id'];
        $arr_jd["contact_no"] = $row_jd['contact_no'];
        $arr_jd["close_date"] = $row_jd['close_date'];
        $arr_jd["hand_date"] = $row_jd['hand_date'];
        $arr_jd["job_status"] = $row_jd['status'];
		//print_r($row_jd['product_id']);exit;
		
        $arr_jd["product_name"] = getAnyDetails2($row_jd['product_id'],"product_name","product_id","product_master",$link1);
		//print_r('dddddddddd');exit;
        $arr_jd["brand_name"] = getAnyDetails2($row_jd['brand_id'],"brand","brand_id","brand_master",$link1);
		
    }else{
		
        $arr_jd["msg"] = "fail";
        $arr_jd["status"] = "0";
    }
	
    return json_encode($arr_jd);
}
//////////check from DMS api
function getSaleDataValidate($serialno,$jobno="",$productid="",$locationcode="",$refno="",$link1){
	
    $arr_sd = array();
    ///////call dms api 
    $curl = curl_init();
    
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://sukam.cancrm.in/dms/salesapi/getSerialInfo.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_POSTFIELDS =>'{"serialno":"'.$serialno.'","accessKey":"862fb088559a6bb31cdc099deb1fa0e50d56150dcb4e72028fc133f09da11cea"}',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    $decode_resp = json_decode($response);
	//print_r($decode_resp);
	 
    ///////if success
    if($decode_resp->response->code == 200){
        $data = $decode_resp->response->message->serialinfo[0];
		//echo "SELECT model,model_id, wp,dwp,product_id,brand_id FROM model_master where model_id='".$data->prodCode."'  and status='1'";exit;
		
		//print_r($data);
        $arr_sd["msg"] = "success";
        $arr_sd["status"] = "1";
        $arr_sd["source"] = "DMS";
        $arr_sd["product_id"] = $data->productCatId;
        $arr_sd["brand_id"] = $data->brandId;
        $arr_sd["model_id"] = $data->prodCode;
		
		$arr_sd["warrenty_day"] = $data->wp;
        $arr_sd["dwp"] = $data->wp;
        $arr_sd["model"] = $data->prodName;
        $arr_sd["location_name"] = $data->locationName;
        $arr_sd["ref_no"] = $data->refNo;
        $arr_sd["ref_date"] = $data->refDate;
        $arr_sd["dop"] = $data->refDate;
        $arr_sd["import_date"] = $data->importDate;
        $arr_sd["mfd"] = $data->importDate;
        $mfd_ex=date('Y-m-d', strtotime($data->importDate. ' + '.$data->wp.' days'));
        $arr_sd["mfd_ex"] = $mfd_ex;

        $arr_sd["product_name"] = $data->productCat;
        $arr_sd["brand_name"] = $data->brand;
    }else{
        $arr_sd["msg"] = "fail";
        $arr_sd["status"] = "0";
    }
    return json_encode($arr_sd);
}

function getAnyDetails2($keyid,$fields,$lookupname,$tbname,$link1){
	///// check no. of column
	$chk_keyword = substr_count($fields, ',');
   	if($chk_keyword > 0){
		$explodee = explode(",",$fields);
   		$tb_details = mysqli_fetch_array(mysqli_query($link1,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'"));
   		$rtn_str = "";
   		for($k=0;$k < count($explodee);$k++){
       		if($rtn_str==""){
          		$rtn_str.= $tb_details[$k];
	   		}
       		else{
          		$rtn_str.= "~".$tb_details[$k];
			}
		}
	}
	else{
		$tb_details = mysqli_fetch_array(mysqli_query($link1,"select ".$fields." from ".$tbname." where ".$lookupname." = '".$keyid."'"));
		$rtn_str = $tb_details[$fields];
	}
   return $rtn_str;
}
?>