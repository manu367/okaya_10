<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
////////// make connection clone
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);     
////////
$engid = $_REQUEST['eng_id'];
$a = array();
$b = array();
if ($engid){    
    $res_enginfo = mysqli_query($conn,"SELECT * FROM locationuser_master WHERE userloginid='".$engid."'");
    if(mysqli_num_rows($res_enginfo)>0){
        while ($row_enginfo = mysqli_fetch_array($res_enginfo)) 
        { 
            $b["user_id"] =  $row_enginfo["userloginid"];
            $b["user_name"] =  $row_enginfo["locusername"];
            $b["user_email"] =  $row_enginfo["emailid"];
            $b["user_type"] =  $row_enginfo["type"];
            $b["user_contact"] =  $row_enginfo["contactmo"];
            $b["user_dob"] =  $row_enginfo["date_of_birth"];
            $b["user_doj"] =  $row_enginfo["date_of_joining"];
            $b["user_pan"] =  $row_enginfo["pan_no"];
            $b["user_aadhar"] =  $row_enginfo["aadhar_no"];
            $b["user_account_no"] =  $row_enginfo["account_no"];
            $b["user_status"] =  $row_enginfo["statusid"];
            ///check KYC is done or not
            if($row_enginfo["pan_no"]!="" && $row_enginfo["aadhar_no"]!="" && $row_enginfo["account_no"]!=""){
                $b["user_kyc"] =  "Y";
                $b["user_kyc_status"] =  "Done";
            }else{
                $b["user_kyc"] =  "N";
                $b["user_kyc_status"] =  "Pending";
            }
            array_push($a,$b);
        }
    }else{
        $b['err_msg'] = "Invalid User id.";
        array_push($a,$b);    
    }
}else{
    $b['err_msg'] = "User id cannot be blank.";
    array_push($a,$b);
}
echo json_encode($a);
?>