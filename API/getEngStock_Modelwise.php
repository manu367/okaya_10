<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$serial_no=$_REQUEST['serial_no'];
$job_no=$_REQUEST['job_no'];
$users = $db->getstockeng_mdel($_REQUEST['eng_id'],$_REQUEST['model_id'],$_REQUEST['u_type']);

function cleanData($instr) {
$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
return $str;
}

$a = array();     
$b = array(); 
if ($users != false){
    ////////////Check For Out Waranty Part Counsme 
    $sale_price="";  
	$close_re="";
$job_row=mysqli_fetch_array(mysqli_query($conn,"select job_no,open_date from jobsheet_data where job_no='".$job_no."'"));
 
    $rowk=mysqli_query($conn,"select close_date, job_no  from jobsheet_data where imei = '".$serial_no."' and status in ('10','6') and imei not like 'Z%' and close_date!='0000-00-00' and warranty_status='OUT' order by job_id desc"); 
	 $job_close = mysqli_fetch_assoc($rowk);
    if(mysqli_num_rows($rowk)>0){
       
	 	//$close_re = daysDifference($job_row['open_date'],$job_close['close_date']);
       
		if($job_row['open_date'] > $job_close['close_date']){
	 		$close_re =$db-> daysDifference($job_row['open_date'],$job_close['close_date']);
		}else{
			$close_re =$db-> daysDifference($job_close['close_date'],$job_row['open_date']);
		}
    }
    
while ($row = mysqli_fetch_array($users)) 
{       
   
$p_name=$db->getAnyDetails($row['partcode'],"customer_price,part_category,model_id,hsn_code,part_category,serial_part,part_group,uom,additional_price","partcode","partcode_master");
	
 $exp_name=explode("~",$p_name);
$tax_details=mysqli_fetch_array(mysqli_query($conn,"select igst from tax_hsn_master where hsn_code='".$exp_name['3']."' and status='1'"));
//print_r($exp_name);exit;  
$get_part_name_dt = "";
$get_part_name=mysqli_fetch_array(mysqli_query($conn,"select part_desc from partcode_master where partcode='".trim($row['partcode'])."' "));
$get_part_name_dt = cleanData($get_part_name['part_desc']);

//echo $row["partcode"]."-".$tax_details['igst']."-".$exp_name['3']."</br>";
	$taxamt=($exp_name[0] * $tax_details['igst'])/100;
$tot_amt= $exp_name[0]+$taxamt;
     if(mysqli_num_rows($rowk)>0){       
    if($close_re <= 30 && $close_re >= 0 ){
            $repair_detail=mysqli_fetch_array(mysqli_query($conn,"select partcode,close_date from repair_detail where (job_no='".$job_close['job_no']."' OR  old_serial = '".$serial_no."') and partcode='".$row['partcode']."' "));
			$job_type='A';
			$rpt_flg = $job_close['job_no'];
           if($repair_detail['partcode']==$row["partcode"]) {
		   $item_close_date =$db-> daysDifference($job_row['open_date'],$repair_detail['close_date']);
		   if($item_close_date <= 30){
           $sale_price="0.00";
		   }else{
		    $sale_price=$exp_name[0];
		   }
           }
            else {
                $sale_price=$exp_name[0];
            }
            
		} elseif($close_re <= 60 && $close_re >= 31){
			$job_type='B';
			$rpt_flg = $job_close['job_no'];
             $sale_price=$exp_name[0];
		} elseif($close_re <= 90 && $close_re >= 61){
			$job_type='C';
			$rpt_flg = $job_close['job_no'];
             $sale_price=$exp_name[0];
		} else{
			$job_type='Normal';
			$rpt_flg = "";
             $sale_price=$exp_name[0];
		}}else{
			$job_type='Normal';
			$rpt_flg = "";
             $sale_price=$exp_name[0];
		}
    
$b["partcode"] =  $row["partcode"];
$b["partname"] =  cleanData($get_part_name_dt);
//$b["partname"] =  $row["part_name"];
$b["sale_price"] =  $sale_price;
$b["Category"] =  $exp_name[4];
$b["model_id"] =  $exp_name[2];
$b["Fresh Qty"] = $row["okqty"];
$b["Faulty Qty"] = $row["faulty"];
$b["salepricegst"] = number_format($tot_amt,'2','.','');
#### If Part serialized 
if($exp_name[5]=='Y'){
$b["part_searilized"] ='Y';	
}
else{
$b["part_searilized"] = 'N';	
}
$b["part_group"] = $exp_name[6];
$b["uom"] = $exp_name[7];
$b["additional_price"] =  $exp_name[8];


 array_push($a,$b);        
}          
echo json_encode($a);     
} 
?>