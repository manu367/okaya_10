<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$a=array();
$userid=$_REQUEST['userid'];
$oldpassword=$_REQUEST['oldpassword'];
$password=$_REQUEST['newpassword'];
$sql_chk="select id from locationuser_master where userloginid='".$userid."' and statusid='1' and pwd='".$oldpassword."'";
$rs_chk=mysqli_query($conn,$sql_chk) or die(mysqli_error($conn));
if(mysqli_num_rows($rs_chk)>0){
	$row=mysqli_fetch_array($rs_chk);
$rs=mysqli_query($conn,"update locationuser_master set pwd='".$password."' where userloginid='".$userid."' and id='".$row['id']."'") or die(mysqli_error($conn));
$a["msg"]="password change successfully";
$a["status"]=1;
$a["change_flag"]="Y";	
}else{
$a["msg"]="Unable change password";
$a["status"]=0; 
$a["change_flag"]="N";	
}
echo json_encode($a);    
?>