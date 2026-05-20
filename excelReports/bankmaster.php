<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
## selected  Status
if($_REQUEST['status'] == ""){
	$status_str = " 1 ";
}else{
	$status_str = " statusid = '".$_REQUEST['status']."' ";
}
//////End filters value/////
$sql=mysqli_query($link1,"SELECT * FROM mic_attendence_data where user_id in (select userloginid from locationuser_master where ".$status_str." and location_code = '".$_SESSION['asc_code']."') ")or die("er1".mysqli_error($link1));
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