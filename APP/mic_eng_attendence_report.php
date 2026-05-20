<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();    
$from=$_REQUEST['from'];
$to=$_REQUEST['to'];
$eng_id=$_REQUEST['eng_id']; 
$users = $db->micAttendence_report($from,$to,$eng_id);  

//echo "select * from mic_attendence_data where insert_date>='".$from."' and insert_date<='".$to."' and  user_id='".$eng_id."'";
$a = array();     
$b = array();  

if ($users != false){         
while($row = mysqli_fetch_array($users)){ 
         
$b["userid"] =$row['user_id'];     
$b["status_in"] =$row['status_in'];  
$b["in_datetime"] =$row['in_datetime']; 
$b["status_out"]=$row['status_out'];
$b["out_datetime"]=$row['out_datetime'];  

$b["longitude_in"] =$row['longitude_in']; 
$b["latitude_in"]=$row['latitude_in'];
$b["longitude_out"]=$row['longitude_out']; 
$b["latitude_out"]=$row['latitude_out']; 
$b["address_in"]=$row['address_in']; 
$b["address_out"]=$row['address_out'];  

array_push($a,$b);         
        }
echo json_encode($a);     
} 
/////////////////////


?>