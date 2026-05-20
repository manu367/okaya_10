<?php 
/**  * Creates Unsynced rows data as JSON  */ 
function clean($string) {
   //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.

   return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
}

/**  * Creates fault detail data as JSON  */ 
include_once 'db_functions.php';  
$db = new DB_Functions(); 
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$serial_no=$_REQUEST['serial_no']; 
$users = $db->getserialmodel($serial_no);     
$a = array();     
$b = array();    
///warranty calculation/// 
$today=date('Y-m');
$check_f=substr($serial_no,0,1);
$nanserial=substr($serial_no,1);
$nanserial;
/////////////////sale data////

////////////////////////////////////
if (is_numeric($check_f)){ 

$check_y = substr($serial,0,1); 	

	$mm_year = substr($serial_no,0,2); 
	$mm_month = substr($serial_no,2,1); 
	$mm_type = substr($serial_no,3,1); 
	$mm_model = substr($serial_no,4,1); 
	
		
}else {

$check_y =substr($nanserial,0,1); 	
	if (is_numeric($check_y)){
	
	$mm_year =substr($nanserial,0,2); 
	$mm_month =substr($nanserial,2,1); 
	$mm_type = substr($nanserial,3,1); 
	$mm_model =substr($nanserial,4,1); 

		}

}
//echo $mm_year.$mm_month.$mm_type.$mm_model;



////////////////// Year \\\\\\\\\\\\\\\\\\\\\
		if($mm_year=="0" || $mm_year=="1" ||$mm_year=="2" ||$mm_year=="3" ||$mm_year=="4" ||$mm_year=="5" ||$mm_year=="6" ||$mm_year=="7" ||$mm_year=="7" || $mm_year=="8" || $mm_year=="9"){
	 $mm_yearn=200;}
		else if($mm_year>"9"){
		 $mm_yearn=20;}
		else{$mm_yearn='';}
		
////////////////// Month \\\\\\\\\\\\\\\\\\\\\
			 if($mm_month=="A" || $mm_month=="a"){
		 $mm_monthn='01';}
		else if($mm_month=="B" || $mm_month=="b"){
	 $mm_monthn='02';}
		else if($mm_month=="C" || $mm_month=="c"){
		 $mm_monthn='03';}
		else if($mm_month=="D" || $mm_month=="d"){
	 $mm_monthn='04';}
		else if($mm_month=="E" || $mm_month=="e"){
		$mm_monthn='05';}
		else if($mm_month=="F" || $mm_month=="f"){
	$mm_monthn='06';}
		else if($mm_month=="G" || $mm_month=="g"){
		$mm_monthn='07';}
		else if($mm_month=="H" || $mm_month=="h"){
	 $mm_monthn='08';}
		else if($mm_month=="I" || $mm_month=="i"){
		$mm_monthn='09';}
		else if($mm_month=="J" || $mm_month=="j"){
		$mm_monthn=10;}
		else if($mm_month=="K" || $mm_month=="k"){
		$mm_monthn=11;}
		else if($mm_month=="L" || $mm_month=="l"){
	$mm_monthn=12;}
		else {$mm_monthn='';}



$level_query3="SELECT warrantymonth,model,model_id FROM model_master where modelcode='".$mm_model."' and productcode='".$mm_type."' and status='1'";
$check3=mysqli_query($conn,$level_query3);
$br3 = mysqli_fetch_array($check3);
//echo $br3['model']."^".$br3['modelid']."^".$br3['warrantymonth']."^".$_REQUEST['target'];
$warranty_month=$br3['warrantymonth']+ 2;
//echo $warranty_month;
$mm_mfd = $mm_yearn.$mm_year.'-'.$mm_monthn;


$ex_war="select war_month from warranty_master  where serial_no='".$serial_no."'";
$war_ex=mysqli_query($conn,$ex_war)or die(mysqli_error($conn)); 
if(mysqli_num_rows($war_ex)>0) {
$br_ex= mysqli_fetch_array($war_ex);
if($br_ex[war_month]==""){$extdwar="0";}else {$extdwar="$br_ex[war_month]";}
}else {$extwar='0';}
//echo $extdwar;
$month=$warranty_month+$extdwar;

$mfd_f=date('Y-m',strtotime(+$month.'months',strtotime($mm_mfd)));
echo $mfd_f;

$level_query_date_install="select mfd_expiry_date from installation_master where serial_no='$serial_no' order by installation_id desc";
$check1_date_install=mysqli_query($conn,$level_query_date_install) or die(mysqli_error($conn));
if(mysqli_num_rows($check1_date_install)>0) { 
$br_date_install = mysqli_fetch_array($check1_date_install);
$install_date=date('Y-m',strtotime($br_date_install['mfd_expiry_date']));
}else {
	$install_date='0000-00-00';
	}

$amc_query="SELECT purchase_date,amc_start_date,amc_end_date,amcid FROM amc where serial_no='".$serial_no."'  and app_status='51' order by sno desc";
$check2=mysqli_query($conn,$amc_query) or die(mysqli_error($conn)); 
if(mysqli_num_rows($check2)>0){
$br_amc = mysqli_fetch_array($check2);
$amcid=$br_amc['amcid'];
$amc_date=date('Y-m',strtotime($br_amc['amc_end_date']));
}else {$amc_date='0000-00-00';}

if($mfd_f>=$today){
$warranty='IN';
    echo 1;
//$mfd_ex=$mfd_f;
}elseif($install_date>=$today) {
$warranty='IN';
  echo 2;
//$mfd_ex=$install_date;	
}elseif($amc_date>=$today){
$warranty='IN';
      echo 3;
//$mfd_ex=$amc_date;
}else{
$warranty='OUT';
}
$mfd_ex=max($mfd_f,$install_date,$amc_date);

$sql_dop="select dop from jobsheet_data where imei='".$serial_no."' order by job_id";
$rs_dop=mysqli_query($conn,$sql_dop) or die(mysqli_error($conn));
$row_dop=mysqli_fetch_assoc($rs_dop);
if($row_dop['dop']!=''){
$dop=$row_dop["dop"];
}else {
$dop="0000-00-00";	
}

$b['serial_no']=$serial_no;
$b['warranty_status']=$warranty;
$b['product']=$mm_type;
$b['model_name']=$br3['model'];
$b['model_id']=$br3['model_id'];
$b['mfd']=$mm_mfd;
$b['mfd_ex']=$mfd_ex;
$b["amc_expiry_date"]=$amc_date;
$b["amcid"]=$amcid;
$b["dop"]=$dop;
$b["status"]=1;






array_push($a,$b);         
      
echo json_encode($a);     

?>