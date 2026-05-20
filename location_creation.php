<?php
require_once("security/dbh.php");	


 $res_data = mysqli_query($link1 , "select * from loc_temp where loc_name != 'XYZ'");
   while($data = mysqli_fetch_array($res_data))
   {
	   
	   $state = mysqli_fetch_array(mysqli_query($link1 , "select stateid  from state_master where state = '".$data['state']."' "));
	   $city = mysqli_fetch_array(mysqli_query($link1 , "select cityid  from city_master where city = '".$data['city']."' "));
	   
	   $district = mysqli_fetch_array(mysqli_query($link1 , "SELECT cityid FROM city_master where stateid='".$state['stateid']."' and isdistrict='Y' group by city order by city"));

//insert all details of location //
	 $sql="INSERT INTO location_master set location_code='".$location_user_code."', erpid='".$data['erpid']."',locationname='".ucwords($data['loc_name'])."',locationtype='".$data['loc_type']."', partner_type='Owned',contact_person='".ucwords($data['person'])."',contactno1='".$data['contact1']."',contactno2='".$data['contact2']."',locationaddress='".ucwords($data['loc_address'])."', dispatchaddress='".ucwords($data['loc_address'])."',deliveryaddress='".ucwords($data['ship_address'])."',districtid='".$district['cityid']."',cityid='".$city['cityid']."',stateid='".$state['stateid']."',countryid='1',zipcode='".$data['pincode']."',statusid='1',loginstatus='1',panno='".$data['pan_no']."',createby='".$_SESSION['userid']."',createdate='".$datetime."',zone='".$data[zone]."' ";
	mysqli_query($link1,$sql)or die("ER1".mysqli_error($link1));

	$insid = mysqli_insert_id($link1);
	/// make 4 digit padding
	$pad=str_pad($insid,4,"0",STR_PAD_LEFT);
	//// make logic of employee code
	$newlocationcode="OKEV".$party_type.$pad;

	//////// update system genrated code in location
	mysqli_query($link1,"UPDATE location_master set location_code='".$newlocationcode."',old_location_code='".$newlocationcode."', pwd='".$newlocationcode."' where locationid='".$insid."'")or die("ER2".mysqli_error($link1));

	///// entry in job counter 
	$sql_jobcount="INSERT INTO job_counter set location_code='".$newlocationcode."', job_count='0',job_series='VS".$insid."' ";

	mysqli_query($link1,$sql_jobcount)or die("ER2".mysqli_error($link1));

	///// entry in invoice counter 

	$yr=0;
	$yr1=0;
	if(date('m')<'04'){
		$yr=date('y');
		//$yr1=date('y');
	}else{
		$yr=date('y')+1;
		//$yr1=date('y')+1;
	}
	$cyr=$yr;	

	$sql_invcount="INSERT INTO invoice_counter set location_code='".$newlocationcode."',fy='".$cyr."/',inv_series='I".$pad."/', inv_counter='0', stn_series='DC".$pad."/',stn_counter='0'";

	mysqli_query($link1,$sql_invcount)or die("ER2.1".mysqli_error($link1));

	///// entry in current cr status
	$sql_crlimit="INSERT INTO current_cr_status set location_code='".$newlocationcode."',  credit_bal='0.00',   credit_limit='0.00',   total_credit_limit='0.00'";

	mysqli_query($link1,$sql_crlimit)or die("ER3".mysqli_error($link1));
	
	

   } 


?>

