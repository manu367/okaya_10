<?php
require_once("dbconnect.php");
require_once("common_function.php");
session_start();
$today_mm=date("Y-m");
switch($_REQUEST["action"]){ 
    case getPicodeDetails:
     $city_query="SELECT  *  FROM pincode_master where pincode='".$_REQUEST['value']."' ";
     $city_res=mysqli_query($link1,$city_query);
    $row_city = mysqli_fetch_array($city_res);
	$sql12="select * from city_master where cityid='".$row_city['cityid']."' ";
	$cityrow=mysqli_query($link1,"select * from city_master where cityid='".$row_city['cityid']."' ")or die(mysqli_error($link1));
        
			$row_city1 = mysqli_fetch_array($cityrow);
	
	echo $row_city1['city']."^".$row_city1['state']."^".$row_city['cityid'];
break; 
    
case chkProductSno:
$level_query="SELECT product_id,product_name FROM product_master where productcode='".$_REQUEST['value']."'";
$check2=mysqli_query($link1,$level_query);
if(mysqli_num_rows($check2) > 0){
$br = mysqli_fetch_array($check2);
echo $br['product_name']."^".$br['product_id']."^".$_REQUEST['target'];
 }
else {
echo ""."^".""."^".$_REQUEST['target'];
    }
break; 
        
case chkModelSno:
  $level_query="SELECT model,model_id, wp,dwp FROM model_master where modelcode='".$_REQUEST['value']."'  and status='1'";
$check2=mysqli_query($link1,$level_query);
    if(mysqli_num_rows($check2) > 0){
$br = mysqli_fetch_array($check2);
echo $br['model_id']."^".$br['model']."^".$br['wp']."^".$br['dwp']."^".$_REQUEST['target'];
    }
else {
 echo ""."^".""."^".""."^".$_REQUEST['target'];
}
break;
        
case chkPlantSno:
$level_query="SELECT companyid,cname FROM company_master where companycode='".$_REQUEST['value']."'";
$check2=mysqli_query($link1,$level_query);
 if(mysqli_num_rows($check2) > 0){
$br = mysqli_fetch_array($check2);
echo $br['cname']."^".$br['companyid']."^".$_REQUEST['target'];
 }
else {
  echo ""."^".""."^".$_REQUEST['target'];
 }
break;


case chkPlant_nSno:
$level_query_n="SELECT plantname FROM plant_master where plantcode='".$_REQUEST['value']."' and status='A'";
$check2_n=mysqli_query($link1,$level_query_n);
 if(mysqli_num_rows($check2_n) > 0){
$br_n = mysqli_fetch_array($check2_n);
echo $br_n['plantname']."^".$_REQUEST['target'];
 }
else {
echo ""."^".$_REQUEST['target'];
}
break;
        
case chkLineSno:
$level_query_nw="SELECT linename FROM line_master where linecode='".$_REQUEST['value']."' and plantcode='".$_REQUEST['value2']."' and status='A'";
$check2_nw=mysqli_query($link1,$level_query_nw);
if(mysqli_num_rows($check2_nw) > 0){
$br_n = mysqli_fetch_array($check2_nw);
echo $br_n['linename']."^".$_REQUEST['target'];
}
else {
    echo ""."^".$_REQUEST['target'];
}
break;

case chkComponentSno:
$level_query_ne="SELECT sku_model,curr_ecn_no FROM component_master where ccode='".$_REQUEST['value']."' and model='".$_REQUEST['value2']."' and status='A' ";
$check2_ne=mysqli_query($link1,$level_query_ne);
if(mysqli_num_rows($check2_ne) > 0){
$br = mysqli_fetch_array($check2_ne);
echo $br['sku_model'].",".$br['curr_ecn_no'].",".$br['combo_no'].",".$br['ecn_issue_date'].",".$br['implement_date'].",".$br['part1'].",".$br['part2'].",".$br['part3'].",".$br['part4'].",".$br['part5']."^".$_REQUEST['target'];
}
else {
echo "".","."".","."".","."".","."".","."".","."".","."".","."".",".""."^".$_REQUEST['target'];
}
break;
   
        
    ////////////// Plant details as per serial no \\\\\\\\\\\\\\\\				
case getWarrantyDataSno:
$warranty_month=0;
$model_query="SELECT warrantymonth FROM model_master where modelcode='".$_REQUEST['value2']."' and productcode='".$_REQUEST['prod_id']."' and status='1'";
$check3=mysqli_query($link1,$model_query) or die(mysqli_error($link1)); 
$br_model = mysqli_fetch_array($check3);
$warranty_month=$br_model['warrantymonth']+2;
$dateOneMonthAdded = strtotime(date("Y-m", strtotime($_REQUEST[value3])) . "+$warranty_month month");
		
/*$mfd_exp=date('Y-m', $dateOneMonthAdded);
$query="update product_registered set manufactured_expiry_date='".$mfd_exp."' where serial_no='".$_REQUEST['value']."'";
$war_add=mysqli_query($link1, $query)or die(mysqli_error($link1)); */

$ex_war="select war_month from warranty_master  where serial_no='".$_REQUEST['value']."'";
$war_ex=mysqli_query($link1, $ex_war)or die(mysqli_error($link1)); 
$br_ex= mysqli_fetch_array($war_ex);
if($br_ex[war_month]==""){$extdwar="0";}else {$extdwar="$br_ex[war_month]";}

$level_query="SELECT installation_date,purchase_date,warranty_end_date,manufactured_expiry_date FROM product_registered where serial_no='".$_REQUEST['value']."' ORDER BY `id` DESC";
$check1=mysqli_query($link1, $level_query) or die(mysqli_error($link1)); 
$br = mysqli_fetch_array($check1);
$install_date=$br["purchase_date"];
		
$level_query_date_install="select installation_date as date_intallation,close_date from jobsheet_data where imei='".$_REQUEST['value']."' and call_for='Installation'";
$check1_date_install=mysqli_query($link1, $level_query_date_install) or die(mysqli_error($link1)); 
if(mysqli_num_rows($check1_date_install)>0){
$br_date_install = mysqli_fetch_array($check1_date_install);
    if($install_date=='0000-00-00'){
    if($br_date_install['date_intallation']!="0000-00-00"){
$install_date=$br_date_install['date_intallation'];
    }else {$install_date=$br_date_install['close_date'];	}
    }
}
if($install_date==''){ $install_date='0000-00-00';}
$install_end_date=date("Y-m-d",strtotime($install_date."+$br_model[warrantymonth] month"));
		
$amc_query="SELECT purchase_date,amc_start_date,amc_end_date,amcid FROM amc where serial_no='".$_REQUEST['value']."'  and app_status='51' order by sno desc";
$check2=mysqli_query($link1, $amc_query) or die(mysqli_error($link1)); 
$br_amc = mysqli_fetch_array($check2);
        $to_day=date("Y-m-d");
        $purchase_date=$br["purchase_date"];
        if($purchase_date=='0000-00-00'){$purchase_date=$br_amc['purchase_date'];}
        if($purchase_date > $to_day){$purchase_date='0000-00-00';}
         if($install_date > $to_day){$install_date='0000-00-00';}
echo $install_date."^".$purchase_date."^".$br['warranty_end_date']."^".$br['manufactured_expiry_date']."^".$br_amc['purchase_date']."^".$br_amc['amc_start_date']."^".$br_amc['amc_end_date']."^".$br_amc['amcid']."^".$warranty_month."^".$today_mm."^".$_REQUEST['target']."^".$_REQUEST['value3']."^".$extdwar."^".$install_end_date."^".$install_date."^".$bill_exp_date."^"."end";
break; 
        
case getAMCend:
if(!empty($_REQUEST[value1])){		
$value1=$_REQUEST[value1];
$value2=$_REQUEST[value2];
if($value1 && $value2){
$dateOneMonthAdded = strtotime(date("Y-m-d", strtotime($value2)) . "+$value1 month");
$amc_end=date('Y-m-d', $dateOneMonthAdded);
} 
}
else {
$amc_end='';
}
echo $amc_end;
break;     
				
case chkTRC_Parts:
$level_query="SELECT product_id,substring_index(substring_index(model_id, ',', 1), ',', - 1) as model FROM partcode_master where partcode='".$_REQUEST['value']."'";
$check2=mysqli_query($link1,$level_query);
if(mysqli_num_rows($check2) > 0){
$br = mysqli_fetch_array($check2);
	
	$model_query = mysqli_fetch_array(mysqli_query($link1,"SELECT model,wp FROM model_master where model_id='" . $br['model'] . "'"));
	
echo $br['product_id']."^".$br['model']."^".$model_query['model']."^".$model_query['wp'];
 }
else {
echo ""."^"."";
    }
break;
		
case chkSrValidationFlg:
$level_query="SELECT is_valid FROM product_registered where serial_no='".strtoupper(trim($_REQUEST['value']))."'";
$check2=mysqli_query($link1,$level_query);
if(mysqli_num_rows($check2) > 0){
	$br = mysqli_fetch_array($check2);
	echo $br['is_valid']."^".$_REQUEST['target'];
} else {
	echo ""."^".$_REQUEST['target'];
}
break; 
		
case chkProductSnoAMC:
    $level_query="SELECT product_id,product_name FROM product_master where product_id='".$_REQUEST['value']."'";
    $check2=mysqli_query($link1,$level_query);
    if(mysqli_num_rows($check2) > 0){
        $br = mysqli_fetch_array($check2);
        //echo $br['product_name']."^".$br['product_id']."^".$_REQUEST['target'];
        $str = "<select name='product_name' id='product_name' class='form-control required' required>";
        $str .= "<option value='".$br['product_id']."'>".$br['product_name']."</option>";
        $str .= "</select>"."^".$_REQUEST['target'];
     }
    else {
        $str = "<select name='product_name' id='product_name' class='form-control required' required>";
        $str .= "<option value=''>Please Select</option>";
        $str .= "</select>"."^".$_REQUEST['target'];
        //echo ""."^".""."^".$_REQUEST['target'];
    }
    echo $str;
break;

case chkModelSnoAMC:
    $level_query="SELECT model,model_id, wp FROM model_master where model_id='".$_REQUEST['value']."' and status='1'";
    $check2=mysqli_query($link1,$level_query);
    if(mysqli_num_rows($check2) > 0){
        $br = mysqli_fetch_array($check2);
        //echo $br['model']."^".$br['model_id']."^".$br['wp']."^".$_REQUEST['target'];
        $str = "<select name='modelid' id='modelid' class='form-control required' required>";
        $str .= "<option value='".$br['model_id']."'>".$br['model']." (".$br['model_id'].")"."</option>";
        $str .= "</select>"."^".$_REQUEST['target'];
    }else{
        //echo ""."^".""."^".""."^".$_REQUEST['target'];
        $str = "<select name='modelid' id='modelid' class='form-control required' required>";
        $str .= "<option value=''>Please Select</option>";
        $str .= "</select>"."^".$_REQUEST['target'];
    }
    echo $str;    
break;  
		
case chkBrandSnoAMC:
    $level_query="SELECT brand_id,brand FROM brand_master where brand_id='".$_REQUEST['value']."'";
    $check2=mysqli_query($link1,$level_query);
    if(mysqli_num_rows($check2) > 0){
        $br = mysqli_fetch_array($check2);
        //echo $br['brand']."^".$br['brand_id']."^".$_REQUEST['target'];
        $str = "<select name='brand' id='brand' class='form-control required' required>";
        $str .= "<option value='".$br['brand_id']."'>".$br['brand']."</option>";
        $str .= "</select>"."^".$_REQUEST['target'];
    }else{
        //echo ""."^".""."^".$_REQUEST['target'];
        $str = "<select name='brand' id='brand' class='form-control required' required>";
        $str .= "<option value=''>Please Select</option>";
        $str .= "</select>"."^".$_REQUEST['target'];
    }
    echo $str; 
break;	
		
case getAMCendNew:
    if(!empty($_REQUEST['value1'])){		
        $value1=$_REQUEST['value1'];
        $value2=$_REQUEST['value2'];
        if($value1 && $value2){
            $dateOneMonthAdded = strtotime(date("Y-m-d", strtotime($value2)) . "+$value1 month");
            $amc_end=date('Y-m-d', $dateOneMonthAdded);
        } 
    }else {
        $amc_end='';
    }
    echo $amc_end;
break;		
		
		
        }////////////////////action close
?>
