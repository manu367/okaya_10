<?php 
require_once("../includes/config.php");
print("\n");
print("\n");
////// filters value/////
## selected location Status
$daterange = base64_decode($_REQUEST['daterange']);
$eng = base64_decode($_REQUEST['eng']);
if($status!=""){
	$loc_status="statusid='".$status."'";
}else{
	$loc_status="1";
}
if($eng!=""){
	$eng_id="eng_id in('".$eng."')";
}else{
	$eng_id="1";
}
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != "") {
	$seldate = explode(" - ", $_REQUEST['daterange']);
	$fromdate = $seldate[0];
	$todate = $seldate[1];
	$dt = " (entry_date >= '" . $fromdate . "' AND entry_date <='" . $todate . "')";
} else {
	$seldate = $today;
	$fromdate = $today;
	$todate = $today;
	$dt = " (entry_date >= '" . $fromdate . "' AND entry_date <='" . $todate . "')";
}
//////End filters value/////
//echo "Select job_no,distence_in_km,distence_in_mtr,current_loc,job_open_date,job_close_date,eng_id,update_date_time,entry_date,from_address,to_address,punch_in_time,punch_out_time from eng_travel_details where ".$dt." and ".$eng_id." and job_no!='' group by eng_id";exit;
$sql_distence_racking=mysqli_query($link1,"Select job_no,distence_in_km,distence_in_mtr,current_loc,job_open_date,job_close_date,eng_id,update_date_time,entry_date,from_address,to_address,punch_in_time,punch_out_time from eng_travel_details where ".$dt." and ".$eng_id." and job_no!='' ");
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Job No</strong></td>
<td><strong>Location Code</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>Eng Code</strong></td>
<td><strong>Eng Name</strong></td>
<td><strong>Distance In(KM)</strong></td>
<td><strong>Distance In(Mtr)</strong></td>
<td><strong>Job Open Date</strong></td>
<td><strong>Job Close Date</strong></td>
<td><strong>Distance Traking(Date/Time)</strong></td>
<td><strong>Punch In (Date/Time)</strong></td>
<td><strong>Punch Out (Date/Time)</strong></td>
<td><strong>From Address</strong></td>
<td><strong>To Address</strong></td>
</tr>
<?php
	
$i=1;
while($row_loc = mysqli_fetch_array($sql_distence_racking)){
	
	//print_r($row_loc);
 //$date =explode(" ",$row_loc['createdate']);
 //print_r($row_loc['entry_date']);exit;
	$datein=getAnyDetails($row_loc['job_no'],"entry_date","ref_no","user_daily_track",$link1);
	//$punch_indate_time=getAnyDetails($datein,"in_datetime","insert_date","mic_attendence_data",$link1);
	$punch_indate_time=mysqli_fetch_array(mysqli_query($link1,"Select in_datetime from mic_attendence_data where user_id='".$row_loc['eng_id']."' and insert_date='".$datein."' "));
	if($punch_indate_time['in_datetime']!=''){$punch_time=$punch_indate_time['in_datetime'];}else{$punch_time=getAnyDetails($row_loc['job_no'],"entry_time","ref_no","user_daily_track",$link1);}
	$loc_id=getAnyDetails($row_loc['job_no'],"current_location","job_no","jobsheet_data",$link1);
	$Km_dist = array();
	$m_dist = array();
	//echo "Select distence_in_km,distence_in_mtr from eng_travel_details where entry_date='".$row_loc['entry_date']."' and eng_id='".$row_loc['eng_id']."' and job_no!=''";exit;
	//$sql_distence_racking1=mysqli_query($link1,"Select distence_in_km,distence_in_mtr from eng_travel_details where entry_date='".$row_loc['entry_date']."' and eng_id='".$row_loc['eng_id']."' and job_no!=''  ");
	//echo "Select GROUP_CONCAT(distence_in_km SEPARATOR '-') as distence_in_km from eng_travel_details where entry_date='".$row_loc['entry_date']."' and eng_id='".$row_loc['eng_id']."' and job_no!=''  ";exit;
			//$sql_distence_racking1=mysqli_fetch_array(mysqli_query($link1,"Select GROUP_CONCAT(distence_in_km SEPARATOR '-') as distence_in_km from eng_travel_details where entry_date='".$row_loc['entry_date']."' and eng_id='".$row_loc['eng_id']."' and job_no!=''  "));
	       // $km = explode("-", $sql_distence_racking1['distence_in_km']);

	//print_r($km);exit;
	/*while($row_loc1 = mysqli_fetch_array($sql_distence_racking1)){
	//for($i=0;$i>mysqli_num_rows();$i++){
    array_push($Km_dist, $row_loc1['distence_in_km']);
	array_push($m_dist, $row_loc1['distence_in_mtr']);	
	}*/
	
	       // $sql = mysqli_fetch_array(mysqli_query($link1, "Select GROUP_CONCAT(partcode SEPARATOR '-') as partcode from auto_part_request where job_no= '" . $row_loc['job_no'] . "' group by job_no "));
        //$part_code = explode("-", $sql['partcode']);
	
	
	
	
	
	        $km="";
			$mm="";

			$get_user_daily_track = mysqli_query($link1,"Select distence_in_km,distence_in_mtr from eng_travel_details where entry_date='".$row_loc['entry_date']."' and eng_id='".$row_loc['eng_id']."' and job_no!=''  group by id");
	
				while($row_usr_trk13 = mysqli_fetch_assoc($get_user_daily_track)){
					if ($km == "") {
					$km = $row_usr_trk13['distence_in_km'];
					$mm=$row_usr_trk13['distence_in_mtr'];	
					} else {
					$km .= "," . $row_usr_trk13['distence_in_km'];
					$mm.= "~" . $row_usr_trk13['distence_in_mtr'];
					
				   }
						
				}
	
	$km_data = explode(",",$km);
	//print_r($km_data);
	//for($j=0;$j<(mysqli_num_rows($get_user_daily_track));$j++){
	
	
	
	
	//print_r($km_data);
	//if($row_loc['from_address']!=''){
	//	$d = $j;
		//$d = $i;
?>
<tr>
<td><?=$i?></td>
<td><?=$row_loc['job_no']?></td>
<td><?=$loc_id?></td>
<td><?=getAnyDetails($loc_id,"locationname","location_code","location_master",$link1);?></td>
<td><?=$row_loc['eng_id'];?></td>
<td><?=getAnyDetails($row_loc['eng_id'],"locusername","userloginid","locationuser_master",$link1);?></td>
<!--<td><?=$km_data[$d];?></td>
<td><?//=$km[$d];?></td>-->
<td><?=$row_loc['distence_in_km'];?></td>
<td><?=$row_loc['distence_in_mtr'];?></td>	
<td><?=getAnyDetails($row_loc['job_no'],"open_date","job_no","jobsheet_data",$link1);?></td>
<td><?=getAnyDetails($row_loc['job_no'],"close_date","job_no","jobsheet_data",$link1);?></td>
<td><?=$row_loc['update_date_time'];?></td>
<td><?=$row_loc['entry_date']?> <?=$row_loc['punch_in_time']?></td>
<td><?=$row_loc['entry_date']?> <?=$row_loc['punch_out_time']?></td>
<td><?=cleanData($row_loc['from_address']);?></td>
<td><?=cleanData($row_loc['to_address']);?></td>
</tr>
<?php
$i+=1;		
//}
}
?>
</table>