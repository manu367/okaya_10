<?php
print("\n");
print("\n");
require_once("../includes/config.php");
require_once("../includes/common_function.php");

$utype=base64_decode($_REQUEST['u_type']);
$ustatus=base64_decode($_REQUEST['status']);

## selected  Status
if($ustatus==''){
	$status="status='1'";
}
else if($ustatus=="2"){
	$status="status='2'";
}
else{
	$status="1";
}
## selected user type
if($utype!=""){
	$utypename="utype='".$utype."'";
}else{
	$utypename="1";
}
if($_SESSION['userid']=="test"){
	$checkmainadmin = "";
}else{
	$checkmainadmin = " and username!='test'";
}
//////End filters value/////
$query="Select * from admin_users u LEFT JOIN designation  d ON u.des_id = d.des_id
         where ".$status." and ".$utypename."".$checkmainadmin."";
$sql=mysqli_query($link1,$query)or die("er1".mysqli_error($link1));
?>

<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>User Id</strong></td>
<td><strong>User Name</strong></td>
<td><strong>User Type</strong></td>
<td><strong>Contact No.</strong></td>
<td><strong>Email Id</strong></td>
<td><strong>Status</strong></td>
    <td><strong>State</strong></td>
    <td><strong>City</strong></td>
    <td><strong>Address</strong></td>
    <td><strong>Desiganation</strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
$result = getCityState($row_loc['city_id'], $link1);
$state="";
$city="";
foreach($result as $id => $value){
    list($s, $c) = explode(',', $value);
    $state=$s;
    $city=$c;
}
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['username']?></td>
<td align="left"><?=$row_loc['name']?></td>
<td align="left"><?=$row_loc['utype']?></td>
<td align="right"><?=$row_loc['phone']?></td>
<td align="left"><?=$row_loc['emailid']?></td>
<td align="left"><?=$arrstatus[$row_loc['status']]?></td>
    <td align="left"><?=$state?></td>
    <td align="left"><?=$city?></td>
    <td align="left"><?=!empty($row_loc['address']) ? $row_loc['address'] : 'NaN'?></td>
    <td align="left"><?= isset($row_loc['des_name']) && $row_loc['des_name'] !== null ? $row_loc['des_name'] : 'NaN' ?></td>
</tr>
<?php
$i+=1;
}
?>
</table>