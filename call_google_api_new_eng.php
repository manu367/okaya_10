<?php 
//require_once("includes/dbconnect.php");
require_once("security/dbh.php");	
require_once("google_api_function.php");
require_once("includes/common_function.php");
////// google api key
date_default_timezone_set("Asia/Calcutta"); 
//$apikey = "AIzaSyD_e0ruO5-kbEWig_tz6xMYExypn9K_XNU";
//$apikey = "AIzaSyA3Zh6BY9Ypc7O7nXKd9sddeH76ChNVDPI";////updated on 18 mar 2025
$today_time = date("H:i:s");
$travelmode = "driving";
$today = date("Y-m-d");
//$today = "2025-05-21";
$daybefore = date('Y-m-d', strtotime($today. ' - 1 days'));
//$daybefore = date('Y-m-d', strtotime($today. '  0 days'));
//print_r($daybefore);exit;
//$specific_usrs = "and user_id in('DLNCR01U9','DLNCR01U4','DLNCR01U10','North02U15','DLNCR01U14','DLNCR01U12')";
//$specific_usrs = "and user_id in('".$eng_id."')";
$specific_usrs = "";


$res_usr_trk = mysqli_query($link1,"SELECT ref_no,user_id,entry_date,entry_time FROM user_daily_track WHERE entry_date='".$daybefore."' ".$specific_usrs." group by user_id,entry_date");
while($row_usr_trk = mysqli_fetch_assoc($res_usr_trk)){
	$origin = "";
	$destination = "";
	$waypoints = "";
	$arr_origin = array();
	$arr_destination = array();
	$arr_waypoints = array();
	$edate = $row_usr_trk["entry_date"];
	$uid = $row_usr_trk["user_id"];
	$eid = $row_usr_trk["user_id"];
	$job_no = $row_usr_trk["ref_no"];
	//echo "dddddddddddddddddddddd<br/>";
	///// check api is already called or not for same user on specific date
	if(mysqli_num_rows(mysqli_query($link1,"SELECT id FROM eng_travel_details WHERE eng_id='".$uid."' AND entry_date='".$edate."'"))==0){
		/// intialize array
		$lat_arr = array();
		$lng_arr = array();
		//////get user track details
		//$res_usertrack = mysqli_query($link1,"SELECT latitude,longitude FROM user_track WHERE userid='".$uid."' AND entry_date='".$edate."' AND latitude!='' AND longitude!='' ORDER BY id ASC");
		//echo "SELECT latitude,longitude FROM user_daily_track WHERE userid='".$uid."' AND entry_date='".$edate."' AND latitude!='' AND longitude!='' ORDER BY id ASC";exit;
		$res_usertrack = mysqli_query($link1,"SELECT latitude,longitude,ref_no FROM user_daily_track WHERE user_id='".$uid."' AND entry_date='".$edate."' AND latitude!='' AND longitude!='' ORDER BY id ASC");
		while($row_usertrack = mysqli_fetch_assoc($res_usertrack)){
			
			$lat_arr[] = $row_usertrack["latitude"];
			$lng_arr[] = $row_usertrack["longitude"];
			
		}
		
		/// start script for those having more 25 waypoints written on 13 oct 2023 by shekhar
		$chunkSize = 25;
		$chunks_lat = [];
		$chunks_lng = [];

		for($i = 0; $i < count($lat_arr); $i += $chunkSize)
		{
			$chunks_lat[] = array_slice($lat_arr,$i,$chunkSize);
			$chunks_lng[] = array_slice($lng_arr,$i,$chunkSize);
		}
		
		$origin = "";
		$arr_origin = array();
		$s = 1;
		for($g=0; $g<count($chunks_lat); $g++){
			$waypoints = "";
			$arr_waypoints = array();
			$lat_arr_new = $chunks_lat[$g];
			$lng_arr_new = $chunks_lng[$g];
			///// make origin 
			if($origin == ""){
				$origin = $lat_arr_new[0].",".$lng_arr_new[0];
				$arr_origin['lat'] = $lat_arr_new[0];
				$arr_origin['lng'] = $lng_arr_new[0];
				$s = 1;
			}
			///// make destination
			$destination = end($lat_arr_new).",".end($lng_arr_new);
			$arr_destination['lat'] = end($lat_arr_new);
			$arr_destination['lng'] = end($lng_arr_new);
			///// make waypoints
			$n = 0;
			
			for($j=$s;$j<count($lat_arr_new);$j++){
				if($waypoints==""){
					$waypoints = $lat_arr_new[$j].",".$lng_arr_new[$j];
				}else{
					$waypoints .= "|".$lat_arr_new[$j].",".$lng_arr_new[$j];
				}
				$arr_waypoints[$n]['lat'] = $lat_arr_new[$j];
				$arr_waypoints[$n]['lng'] = $lng_arr_new[$j];
				$n++;
			}
			///// call function
			
			//$resp = getDistanceBtwWayPoints($uid,$eid,$edate,$arr_origin,$arr_destination,$arr_waypoints,$link1);
			$resp = getDistanceBtwWayPoints($uid,$edate,$arr_origin,$arr_destination,$arr_waypoints,$link1);

			

			$arr_wayps =$resp["routes"][0]["legs"];

			
           // echo "<pre>";
			//print_r($arr_wayps);
			//exit;
			

			//$arr_wayps =$resp['routes'][0]["waypoint_order"];
			//echo "<br/>";
			$no_of_wayps = count($arr_wayps);
			//echo "<br/>";
			$dist = "";
			$from_addrs = "";
			$to_addrs = "";
			$resp_lat1 = "";
			$resp_lng1 = "";
			$resp_lat2 = "";
			$resp_lng2 = "";
			//$insid = "";
			//$id = array();
			$gids="";
			$address="";
			$job_arrs="";
			$time_arrs="";
			$get_user_daily_track = mysqli_query($link1,"SELECT id,address,ref_no,entry_time FROM user_daily_track WHERE entry_date='".$edate."' and user_id='".$uid."'");
				while($row_usr_trk13 = mysqli_fetch_assoc($get_user_daily_track)){
					if ($gids == "") {
					$gids = $row_usr_trk13['id'];
					$address=$row_usr_trk13['address'];
					$ref_no = $row_usr_trk13['ref_no'];
					$entry_time = $row_usr_trk13['entry_time'];	
					} else {
					$gids .= "," . $row_usr_trk13['id'];
					$address.= "~" . $row_usr_trk13['address'];
					$ref_no.= "~" . $row_usr_trk13['ref_no'];
					$entry_time.= "~" . $row_usr_trk13['entry_time'];	
				   }
						
				}
			for($i=0;$i<($no_of_wayps);$i++){
				$resp_lat1 = $arr_wayps[$i]['startLocation']['latLng']['latitude'];
				$resp_lng1 = $arr_wayps[$i]['startLocation']['latLng']['longitude'];
				$resp_lat2 = $arr_wayps[$i]['endLocation']['latLng']['latitude'];
				$resp_lng2 = $arr_wayps[$i]['endLocation']['latLng']['longitude'];
				//$from_addrs = getGeoAddress($resp_lat1,$resp_lng1);
				//$to_addrs = getGeoAddress($resp_lat2,$resp_lng2);
				$dist = $arr_wayps[$i]['distanceMeters']/1000;
				$dist_in_mtr = $arr_wayps[$i]['distanceMeters'];
				
				
				
				
				
				//echo "SELECT id,address FROM user_daily_track WHERE entry_date='".$edate."' and eng_id='".$uid."'";

				
				$id_arrs = explode(",",$gids);
				$address_arrs = explode("~",$address);
				$job_arrs = explode("~",$ref_no);
				$time_arrs = explode("~",$entry_time);
				//print_r($id_arrs);
				//print_r($address_arrs);exit;
				//for($j=0;$j<count($id_arrs);$j++){
				    $n =$i;
					$m =$i-1;
					if($i==0){
					$from_addrs1 = "";
					$to_addrs1 = "";
					$job_no1 = 	$job_arrs[0];
					$punch_in_time = "00:00:00";
					$punch_out_time = "00:00:00";	
					}else{
					$from_addrs1 = $address_arrs[$m];
					$to_addrs1 = $address_arrs[$n];
					$job_no1 = 	$job_arrs[$n];
					$punch_in_time = $time_arrs[$m];
					$punch_out_time = $time_arrs[$n];	
					}
				
                 if($address_arrs[$i]!=''){
					$resp_qry = mysqli_query($link1,"INSERT INTO eng_travel_details SET job_no = '".$job_no1."', eng_id = '".$uid."', entry_date = '".$edate."', entry_time = '".$today_time."', current_loc = '".$jobsheet_data['current_location']."', job_open_date = '".$jobsheet_data['open_date']."', job_close_date = '".$jobsheet_data['close_date']."', distence_in_km = '".$dist."', distence_in_mtr = '".$dist_in_mtr."',from_address='".cleanData($from_addrs1)."',to_address='".cleanData($to_addrs1)."',punch_in_time='".$punch_in_time."',punch_out_time='".$punch_out_time."', update_date_time='".$edate."', from_longitude= '".$resp_lng1."', from_latitude= '".$resp_lat1."', to_longitude='".$resp_lng2."', to_latitude='".$resp_lat2."' ");
				 }else{
					//$from_addrs = getGeoAddress($resp_lat1,$resp_lng1);
				   // $to_addrs = getGeoAddress($resp_lat2,$resp_lng2);
					$resp_qry = mysqli_query($link1,"INSERT INTO eng_travel_details SET job_no = '".$job_no1."', eng_id = '".$uid."', entry_date = '".$edate."', entry_time = '".$today_time."', current_loc = '".$jobsheet_data['current_location']."', job_open_date = '".$jobsheet_data['open_date']."', job_close_date = '".$jobsheet_data['close_date']."', distence_in_km = '".$dist."', distence_in_mtr = '".$dist_in_mtr."',from_address='".cleanData($from_addrs)."',to_address='".cleanData($to_addrs)."',punch_in_time='".$punch_in_time."',punch_out_time='".$punch_out_time."', update_date_time='".$edate."', from_longitude= '".$resp_lng1."', from_latitude= '".$resp_lat1."', to_longitude='".$resp_lng2."', to_latitude='".$resp_lat2."' ");

				 }

				//}
				
				
				
				
				
				
				
				
				
				
				
				
				
	
		//$resp_qry = mysqli_query($link1,"INSERT INTO eng_travel_details SET job_no = '".$job_no."', eng_id = '".$uid."', entry_date = '".$edate."', entry_time = '".$today_time."', current_loc = '".$jobsheet_data['current_location']."', job_open_date = '".$jobsheet_data['open_date']."', job_close_date = '".$jobsheet_data['close_date']."', distence_in_km = '".$dist."', distence_in_mtr = '".$dist_in_mtr."',from_address='".cleanData($from_addrs)."',to_address='".cleanData($to_addrs)."',punch_in_time='".$punch_in_time."',punch_out_time='".$punch_out_time."', update_date_time='".$edate."', api_response = '".$response1."', from_longitude= '".$resp_lng1."', from_latitude= '".$resp_lat1."', to_longitude='".$resp_lng2."', to_latitude='".$resp_lat2."' ");
				
		//$resp_qry1 = mysqli_query($link1,"INSERT INTO eng_travel_details_daily_backup SET job_no = '".$job_no."', eng_id = '".$uid."', entry_date = '".$edate."', entry_time = '".$today_time."', distence_in_km = '".$dist."', distence_in_mtr = '".$dist_in_mtr."',from_address='".cleanData($from_addrs)."',to_address='".cleanData($to_addrs)."' ");		
				//$insid = mysqli_insert_id($link1);
				
				
				
			}
			$origin = end($lat_arr_new).",".end($lng_arr_new);
			$arr_origin['lat'] = end($lat_arr_new);
			$arr_origin['lng'] = end($lng_arr_new);
			$s = 0;
			

					
		
	//}
      }
	
	
	}			
	
	
	
}







?>
