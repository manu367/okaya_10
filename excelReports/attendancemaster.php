<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);

$requestData= $_REQUEST;
$daterange=base64_decode($_REQUEST['daterange']);
if ($daterange != ""){
	$seldate = explode(" - ",$daterange);
	$fromdate = $seldate[0];
	$todate = $seldate[1];
}
else{
	$seldate = $today;
	$fromdate = $today;
	$todate = $today;
}
//(open_date >= '".$fromdate."' and open_date <='".$todate."')
## selected location
$loc=base64_decode($_REQUEST['loc']);
if($loc == "All"){
	$loc_str = "1";
}else{
	$loc_str = $loc;
}
## selected eng
$eng=base64_decode($_REQUEST['eng']);
//print_r($eng);exit;
if($eng == ""){
	if($loc_str == "1"){
		$eng_str = " user_id in (select userloginid from locationuser_master where location_code in (select location_code from location_master where stateid in ($arrstate) )) ";
	}else{
		$eng_str = " user_id in (select userloginid from locationuser_master where location_code = '".$loc_str."') ";
	}
}else{
	$eng_str = " user_id = '".$eng."'";
}

if($loc=="" && $eng==""){
	$eng_str = " user_id in (select userloginid from locationuser_master where location_code in (select location_code from location_master where stateid in ($arrstate) )) ";
}
//////End filters value/////
//echo "Select * FROM mic_attendence_data where ".$eng_str." ";exit;
$sql=mysqli_query($link1,"Select * FROM mic_attendence_data where ".$eng_str." and (insert_date >= '".$fromdate."' and insert_date <='".$todate."') ")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Eng Code</strong></td>
<td><strong>Eng Name</strong></td>
<td><strong>Login Date</strong></td>
<td><strong>Login Address</strong></td>
<td><strong>Logout Date</strong></td>
<td><strong>Logout Address</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['user_id']?></td>
<td align="left"><?=getAnyDetails($row_loc['user_id'],"locusername","userloginid","locationuser_master",$link1)?></td>
<td align="left"><?=$row_loc['in_datetime']?></td>
<td align="left"><?=$row_loc['address_in']?></td>
<td align="left"><?=$row_loc['out_datetime']?></td>
<td align="left"><?=$row_loc['address_out']?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>