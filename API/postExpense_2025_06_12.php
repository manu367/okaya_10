<?php 
include_once 'db_functions.php'; 
$db = new DB_Functions(); 
$json = $_POST["usersJSON"];
if (get_magic_quotes_gpc()){ 
		$json = stripslashes($json); 
	}
//Decode JSON into an Array 
$data = json_decode($json); 
//Util arrays to create response JSON 
$a=array();
$b=array();
	//print_r($data);
//Store User into MySQL DB 
$res = $db-> Expense_Claim($data->food_expns,$data->courier_expns,$data->local_expns,$data->mobile_expns,$data->other_expns,$data->food_expns_Img,$data->courier_expns_Img,$data->local_expns_Img,$data->mobile_expns_Img,$data->other_expns_Img,$data->sap_code,$data->personName,$data->travelling_state,$data->travelling_city,$data->hotel_name,$data->other_hotel_name,$data->hotel_address,$data->hotel_city,$data->hotel_state,$data->limit_hotel,$data->checkInDate,$data->checkOutDate,$data->accomdation_days,$data->roomCharge,$data->travellExpense,$data->difference,$data->totalExpenses,$data->expense_date,$data->eng_id);
//Based on inserttion, create JSON response     
if($res){               
$b["status"] = 'yes';     
array_push($a,$b); 
}else{             
$b["status"] = 'no'; 
 array_push($a,$b); 
}
 echo json_encode($a); 
 ?>