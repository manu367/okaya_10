<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$getstatus = base64_decode($_REQUEST['task_status']);
$empsapid = base64_decode($_REQUEST['location_code']);
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != ""){
	$seldate = explode(" - ",$_REQUEST['daterange']);
	$fromdate = $seldate[0];
	$todate = $seldate[1];
}
else{
	$seldate = $today;
	$fromdate = $today;
	$todate = $today;
}

## selected  location code//////////
if($empsapid!=""){
	$employee_id=" location_code='".$empsapid."'";
}
else {
	$employee_id="1";
}
/// selected status
if($getstatus){
	$statusstr="";
	$post_statusarr = explode(",",$getstatus);
	for($i=0; $i<count($post_statusarr); $i++){
		if($statusstr){
			$statusstr .= ",'".$post_statusarr[$i]."'";
		}else{
			$statusstr .= "'".$post_statusarr[$i]."'";
		}
	}
	$status="statusid in (".$statusstr.")";
}else{
	$status="1";
}
//////End filters value/////
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>SAP Code</strong></td>
<td><strong>Employee Id</strong></td>
<td><strong>Employee Name</strong></td>
<td><strong>Address</strong></td>
<td><strong>City</strong></td>
<td><strong>State</strong></td>
<td><strong>Country</strong></td>
<td><strong>Email Id</strong></td>
<td><strong>Contact No.</strong></td>
<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
$sql_loc=mysqli_query($link1,"Select * from location_master where ".$status." and ".$employee_id)or die("er1".mysqli_error($link1));
while($row_loc = mysqli_fetch_array($sql_loc)){
?>
<tr>
<td><?=$i?></td>
<td><?=$row_loc['erpid'];?></td>
<td><?=$row_loc['location_code'];?></td>
<td><?=$row_loc['locationname']?></td>
<td><?=cleanData($row_loc['locationaddress'])?></td>
<td><?=getAnyDetails($row_loc['cityid'],"city","cityid","city_master",$link1);?></td>
<td><?=getAnyDetails($row_loc['stateid'],"state","stateid","state_master",$link1);?></td>
<td><?=getAnyDetails($row_loc['countryid'],"countryname","countryid","country_master",$link1);?></td>
<td><?=$row_loc['emailid']?></td>
<td><?=$row_loc['contactno1']?></td>
<td><?=$arrstatus[$row_loc['statusid']]?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>