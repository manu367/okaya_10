<?php 
/**  * Creates Unsynced rows data as JSON  */    
include_once 'db_functions_test.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db); 
$serial_no=$_REQUEST['serial_no'];
$job_no=$_REQUEST['job_no'];
$users = $db->getstockeng_mdel($_REQUEST['eng_id'],$_REQUEST['model_id']);
$a = array();     
$b = array(); 
if ($users != false){  
   $sale_price="";   
$job_row=mysqli_fetch_array(mysqli_query($conn,"select job_no,open_date from jobsheet_data where job_no='".$job_no."'"));
 
    $rowk=mysqli_query($conn,"select close_date, job_no  from jobsheet_data where imei = '".$serial_no."' and status in ('10','6') and imei not like 'Z%' and close_date!='0000-00-00' order by job_id desc"); 
    if(mysqli_num_rows($rowk)>0){
        $job_close = mysqli_fetch_assoc($rowk);
	 	//$close_re = daysDifference($job_row['open_date'],$job_close['close_date']);
       
		if($job_row['open_date'] > $job_close['close_date']){
	 		$close_re =$db-> daysDifference($job_row['open_date'],$job_close['close_date']);
		}else{
			$close_re =$db-> daysDifference($job_close['close_date'],$job_row['open_date']);
		}
    }
while ($row = mysqli_fetch_array($users)) 
{       
   
$p_name=$db->getAnyDetails($row['partcode'],"customer_price,part_category,model_id,hsn_code,part_category,serial_part","partcode","partcode_master");
	
 $exp_name=explode("~",$p_name);
$tax_details=mysqli_fetch_array(mysqli_query($conn,"select igst from tax_hsn_master where hsn_code='".$exp_name['3']."' and status='1'"));
//echo $row["partcode"]."-".$tax_details['igst']."-".$exp_name['3']."</br>";
	$taxamt=($exp_name[0] * $tax_details['igst'])/100;
$tot_amt= $exp_name[0]+$taxamt;
    
		if($close_re <= 30){
            $repair_detail=mysqli_fetch_array(mysqli_query($conn,"select partcode from repair_detail where job_no='".$job_close['job_no']."' and partcode='".$row['partcode']."'"));
			$job_type='A';
			$rpt_flg = $job_close['job_no'];
           if($repair_detail['partcode']==$row["partcode"]) {
           $sale_price="0.00";
           }
            else {
                $sale_price=$exp_name[0];
            }
            
		}
        elseif($close_re <= 60 && $close_re >= 31){
			$job_type='B';
			$rpt_flg = $job_close['job_no'];
             $sale_price=$exp_name[0];
		}
        elseif($close_re <= 90 && $close_re >= 61){
			$job_type='C';
			$rpt_flg = $job_close['job_no'];
             $sale_price=$exp_name[0];
		}
        else{
			$job_type='Normal';
			$rpt_flg = "";
             $sale_price=$exp_name[0];
		}
	 
 
$b["partcode"] =  $row["partcode"];
$b["partname"] =  $row["part_name"];
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



 array_push($a,$b);        
}          
echo json_encode($a);     
} 
?>