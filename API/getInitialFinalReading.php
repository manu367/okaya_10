<?php 
/**  * Creates fault detail data as JSON  */    
include_once 'db_functions.php'; 
//print_r($_REQUEST);exit;

$today=date("Y-m-d");    
$db = new DB_Functions();     
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
//print_r($_REQUEST);exit;
if(!empty($_REQUEST['job_no'])){  
    $users = $db->getJobMaster(); 
}   

function path_check($path){
    $set_path = "";
    if (is_file($path)) {
        $set_path = substr($path, 3);
    } else {
        $set_path = "";
    }
    return $set_path;
}

$a = array();     
$b = array();   
$c = array();   
$d = array();    
$e = array();   
if ($users != false){         
while ($row = mysqli_fetch_array($users)) 
{  
$b["job_no"] = $row["job_no"];                       
//$b["serial_image"] = $row["path_img4"];
//$b["warranty_card_image"] = $row["path_img5"];

###### Image Uploaded Data
$image_path_data = mysqli_query($conn, "select img_url,img_url1,img_url2,img_url3,img_url4,img_url5 from image_upload_details where job_no ='".$row["job_no"]."' ORDER BY sno DESC limit 0,1 ");
$imagePathObj = array();
while($image_path_row_data=mysqli_fetch_array($image_path_data)){
    $e["img_url"] = path_check($image_path_row_data['img_url']);
    $e["img_url1"] = path_check($image_path_row_data['img_url1']);
    $e["img_url2"] = path_check($image_path_row_data['img_url2']);
    $e["img_url3"] = path_check($image_path_row_data['img_url3']);
    $e["img_url4"] = path_check($image_path_row_data['img_url4']);
    $e["img_url5"] = path_check($image_path_row_data['img_url5']);

    array_push($imagePathObj,$e);
}
$b["imagepathdetails"] = $imagePathObj;
###### END Image Uploaded data

###### Initial Reading Data
$initial_reading_data = mysqli_query($conn, "select ocv,eng_id,c1,c2,c3,c4,c5,c6 from initial_btr_data where job_no ='".$row["job_no"]."'");
$initialReadingObj = array();
while($initial_reading_row_data=mysqli_fetch_array($initial_reading_data)){
    $c["initial_c1"] = $initial_reading_row_data['c1'];
    $c["initial_c2"] = $initial_reading_row_data['c2'];
    $c["initial_c3"] = $initial_reading_row_data['c3'];
    $c["initial_c4"] = $initial_reading_row_data['c4'];
    $c["initial_c5"] = $initial_reading_row_data['c5'];
    $c["initial_c6"] = $initial_reading_row_data['c6'];
    $c["initial_ocv"] = $initial_reading_row_data['ocv'];

    array_push($initialReadingObj,$c);
}
$b["initialreadingdetails"] = $initialReadingObj;
###### END Initial Reading data

###### Final Reading Data
$final_reading_data = mysqli_query($conn, "select use_load,ocv,eng_id,toc,charging_hour,backup_load,backup_time,c1,c2,c3,c4,c5,c6 from final_btr_data where job_no ='".$row["job_no"]."'");
$finalReadingObj = array();
while($final_reading_row_data=mysqli_fetch_array($final_reading_data)){
    $d["final_c1"] = $final_reading_row_data['c1'];
    $d["final_c2"] = $final_reading_row_data['c2'];
    $d["final_c3"] = $final_reading_row_data['c3'];
    $d["final_c4"] = $final_reading_row_data['c4'];
    $d["final_c5"] = $final_reading_row_data['c5'];
    $d["final_c6"] = $final_reading_row_data['c6'];
    $d["final_ocv"] = $final_reading_row_data['ocv'];
    $d["final_toc"] = $final_reading_row_data['toc'];
    $d["final_charging_hour"] = $final_reading_row_data['charging_hour'];
    $d["final_backup_load"] = $final_reading_row_data['backup_load'];
    $d["final_backup_time"] = $final_reading_row_data['backup_time'];
    $d["final_use_load"] = $final_reading_row_data['use_load'];

    array_push($finalReadingObj,$d);
}
$b["finalreadingdetails"] = $finalReadingObj;
###### END Final Reading data

       
array_push($a,$b);         
}         
echo json_encode($a);  
mysqli_close($conn);   
} 
?>