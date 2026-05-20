<?php
include_once 'db_functions.php';

$db = new DB_Functions();  

$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$time_zone=time() + 0;	
date_default_timezone_set ("Asia/Calcutta");
$today = date("Y-m-j");
$cur_time = date("H:i:s",$time_zone);
$ip=$_SERVER['REMOTE_ADDR'];
//$c_time=date("H:i",$time_zone);
function cleanData($instr) {
$str=trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9 ]/', ' ', urldecode(html_entity_decode(strip_tags($instr))))));
return $str;
}	
############################## Parameters of PushData API ##########################################################		
			$json = $_POST["FRESHJSON"];
			$z=json_decode($json, true);
			###### CHeck Location Mapping of Eng
			$chk_map=mysqli_fetch_array(mysqli_query($conn,"select location_code  from locationuser_master where userloginid='".$z['eng_id']."'"));
			
			if($z['eng_id']!='' && $chk_map['location_code']!='')
			{
			///////// Create Document No.
	 		$row_so=mysqli_fetch_array(mysqli_query($conn,"select max(id_old) as no from stn_master where from_location='".$z['eng_id']."'"));
	 		$c_nos=$row_so['no']+1;
	 		$ch_no="STN".$z['eng_id'].str_pad($c_nos,3,'0',STR_PAD_LEFT);
			}

  	 $len=count($z['STNdetails']);
  $chk_challan=mysqli_num_rows(mysqli_query($conn,"select id from stn_master where challan_no='".$ch_no."' "));
	   if($chk_challan['id'] == 0)
	   {
	$flag=1;
	$a=array();
	for($i=0;$i<$len;$i++){
	$partcode=$z['STNdetails'][$i]['partcode'];
	$qty=$z['STNdetails'][$i]['qty'];
	$eng_id=$eng_id;
	if($z['eng_id']!='' && $chk_map['location_code']!='' && $partcode!='' && $qty > '0')
	{
	
	 $chk_qty=mysqli_num_rows(mysqli_query($conn,"select id from user_inventory where locationuser_code='".$z['eng_id']."' and partcode='".$partcode."' and okqty >= '".$qty."'"));
	   if($chk_qty > 0)
	   {
	
		 $sql_part=mysqli_fetch_array(mysqli_query($conn,"select brand_id,product_id,part_name,customer_price,hsn_code from partcode_master where partcode='".$partcode."'"));
		 $value=($qty* $sql_part['customer_price']);
	
	     $result=mysqli_query($conn,"insert into stn_items set from_location='".$chk_map['location_code']."',challan_no='".$ch_no."',sale_date='".$today."',to_location='".$chk_map['location_code']."',type='ENG Return',hsn_code='".$sql_part['hsn_code']."',product_id='".$sql_part['product_id']."',brand_id='".$sql_part['brand_id']."',partcode='".$partcode."',part_name='".cleanData($sql_part['part_name'])."',qty='".$qty."',okqty='".$qty."',price='".$sql_part['customer_price']."',value='".$value."',basic_amt='".$value."',item_total='".$value."',status='2',stock_type='okqty'")or die(mysqli_error($conn));
	
	     $res_query=mysqli_query($conn,"update user_inventory set okqty=okqty-'".$qty."'  where locationuser_code='".$z['eng_id']."' and partcode='".$partcode."'");
		 $flag=0;
		  ###### Stock Ledger
	   mysqli_query($conn,"insert into stock_ledger set reference_no='".$ch_no."',reference_date='".$today."',brand_id='".$rs_part['brand_id']."',product_id='".$rs_part['product_id']."',partcode='".$partcode."',from_party='".$z['eng_id']."',to_party='".$chk_map['location_code']."',owner_code='".$z['eng_id']."',stock_transfer='OUT',stock_type='Fresh',type_of_transfer='ENG Return',action_taken='APP ENG Return',qty='".$qty."',create_by='".$z['eng_id']."',create_date='".$today."',create_time='".$cur_time."',rate='".$sql_part['customer_price']."',ip='".$ip."'");
	
	   }
	}
	}  ///////////// END FOR LOOP
	   } //////////stn master check
  else {
  $flag=1;
  }
	if($flag==0){
	##### Master Entry
	mysqli_query($conn,"insert into stn_master set id_old='".$c_nos."',from_location='".$z['eng_id']."',to_location='".$chk_map['location_code']."',challan_no='".$ch_no."',sale_date='".$today."',entry_date='".$today."',entry_time='".$cur_time."',status='2',document_type='DC',po_type='ENG-RETURN'");
	##############
	}
	if($result) {  
	 
     $a["response"]=1;
	 $a["challan_no"]=$ch_no;
	 $a["eng_id"]=$z['eng_id'];
     $a["msg"]='Challan Created';
	 
   echo json_encode($a);         
   
   } else {             
  if( mysqli_errno($conn) == 1062) {                
 
   $a["response"]=2;
   $a["challan_no"]='';
   $a["eng_id"]=$eng_id;
   $a["msg"]='Something Went Wrong';
   
    echo json_encode($a);              
   } else {                 
 
   $a["response"]=0;
   $a["challan_no"]='';
   $a["eng_id"]=$eng_id;
   $a["msg"]='Something Went Wrong';              
 	 echo json_encode($a);              
                     
 }
   }// end of else
?>

