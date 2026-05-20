<?php

require_once("../includes/dbconnect.php");

//////////  Counter updater ////////////

////////////////////////////////////////////



$today=date("Y-m-d",$time_zone);

$chk_asc=mysqli_query($link1,"select distinct(location_code) as a from auto_part_request where status='3'  ");

while($asc1=mysqli_fetch_array($chk_asc)){

	 $sel_req="select max(po_id) as request_no from po_master where from_code='".$asc1['a']."'";

	$req_res=mysqli_query($link1,$sel_req);
	
	$req_result=mysqli_fetch_array($req_res);
	
	$req_no=$req_result['request_no'] + 1;
	
	$reqId=$asc1['a']."PNA".$req_no;
	
	

		$fromaddress = mysqli_fetch_array ( mysqli_query($link1,"select locationaddress,stateid from location_master where location_code ='".$asc1['a']."' and   statusid='1'"));
	 $res_pro = mysqli_fetch_array ( mysqli_query($link1,"select location_code,locationaddress,stateid from location_master where location_code in (select wh_location from map_wh_location where location_code='".$asc1['a']."' and status='Y') and statusid='1'")); 
	
	$parentcode = $res_pro['location_code'];
	$toaddress=$res_pro['locationaddress'];
	$tostate=$res_pro['stateid'];
	///////////////////

	
	
	////////////////////
 $usr_add="INSERT INTO po_master set 	po_no='".$reqId."', po_date='".$today."' , to_code ='".$parentcode."' , to_address='".$toaddress."' ,to_state='".$tostate."', update_date='".$today."',entry_by='".$asc1['a']."' ,entry_ip ='".$_SERVER['REMOTE_ADDR']."' ,status='1' ,from_code= '".$asc1['a']."', from_address = '".$fromaddress['locationaddress']."' ,  	from_state = '".$fromaddress['stateid']."'  , potype = 'PNA',cron='y' ,po_id='".$req_no."'";
	
	
	$result3=mysqli_query($link1,$usr_add);
	
	$chk_detail=mysqli_query($link1,"select * from auto_part_request where status='3' and location_code='".$asc1['a']."' ");
	while($row_part=mysqli_fetch_array($chk_detail)){		
			
		$result1 = mysqli_query($link1," insert into po_items set po_no = '".$reqId."' ,job_no ='".$row_part['job_no']."' , status = '1' , partcode = '".$row_part['partcode']."' ,type = 'PNA' , update_date = '".$today."' , qty = '1' ,product_id ='".$row_part['product_id']."', brand_id='".$row_part['brand_id']."', model_id= '".$row_part['model_id']."',cron='Y'  ");
		//echo "UPDATE auto_part_request SET status='1',flag='cron' where id='".$row_part['id']."'";
		
		$res1=mysqli_query($link1,"UPDATE auto_part_request SET status='1',flag='cron' where id='".$row_part['id']."'");
	
		
		$result2 = mysqli_query($link1," update jobsheet_data set sub_status = '31'  where job_no ='".$row_part['job_no']."'");
		
		///// entry in call/job  history
		//echo "INSERT INTO call_history set job_no='".$row_part['job_no']."',location_code='".$asc1['a']."',status='31',activity='PNA PO Raised',outcome='Part Requested',updated_by='".$asc1['a']."', ip='".$ip."'";
		$query=mysqli_query($link1,"INSERT INTO call_history set job_no='".$row_part['job_no']."',location_code='".$asc1['a']."',status='31',activity='PNA PO Raised',outcome='Part Requested',updated_by='".$asc1['a']."', ip='".$ip."'");
		
	}
	

}

?>