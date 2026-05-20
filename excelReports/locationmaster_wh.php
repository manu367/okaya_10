<?php 
require_once("../includes/config.php");
print("\n");
print("\n");
////// filters value/////
## selected location Status
if($ustatus==''){
	$status="statusid='1'";
}
else if($ustatus=='2'){
	$status="statusid='2'";
}else{
	$status="1";
}
//////End filters value/////

$sql_loc=mysqli_query($link1,"Select * from location_master where  $status ");
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>State</strong></td>
<td><strong>City</strong></td>
<td><strong>User ID</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>Location Type</strong></td>
<td><strong> Contact Person</strong></td>
<td><strong>Mobile No.1</strong></td>
<td><strong>Mobile No.2</strong></td>
<td><strong>Email ID</strong></td>
<td><strong>Address</strong></td>
<td><strong>Shipping Address</strong></td>
<td><strong>Pin Code</strong></td>
<td><strong>Mapped Warehouse</strong></td>
<td><strong>Mapped L4</strong></td>
<td><strong>PAN Card No</strong></td>
<td><strong>GST No</strong></td>
<td><strong>Bank Name</strong></td>
<td><strong>IFSC Code</strong></td>
<td><strong>Bank Branch</strong></td>
<td><strong>Bank A/C Holder Name</strong></td>
<td><strong>Account No</strong></td>
<td><strong>Beneficiary Address</strong></td>
<td><strong>Account Type</strong></td>
<td><strong> Active ASP DD-MM-YY</strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql_loc)){
 $date =explode(" ",$row_loc['createdate']);
 $mappedwh=getAnyDetails($row_loc['location_code'],"wh_location","location_code","map_wh_location",$link1);
$mappedrepair= getAnyDetails($row_loc['location_code'],"repair_location","location_code","map_repair_location",$link1);
?>
<tr>
<td><?=$i?></td>
<td><?=getAnyDetails($row_loc['stateid'],"state","stateid","state_master",$link1);?></td>
<td><?=getAnyDetails($row_loc['cityid'],"city","cityid","city_master",$link1);?></td>
<td><?=$row_loc['location_code'];?></td>
<td><?=$row_loc['locationname'];?></td>
<td><?=$row_loc['locationtype'];?></td>
<td><?=$row_loc['contact_person'];?></td>
<td><?=$row_loc['contactno1'];?></td>
<td><?=$row_loc['contactno2'];?></td>
<td><?=$row_loc['emailid'];?></td>
<td><?=$row_loc['locationaddress'];?></td>
<td><?=$row_loc['deliveryaddress'];?></td>
<td><?=$row_loc['zipcode'];?></td>
<td><?=getAnyDetails($mappedwh,"locationname","location_code","location_master",$link1);?></td>
<td><?=getAnyDetails($mappedrepair,"locationname","location_code","location_master",$link1);?></td>
<td><?=$row_loc['panno'];?></td>
<td><?=$row_loc['gstno'];?></td>
<td><?=$row_loc['bank_name'];?></td>
<td><?=$row_loc['ifsc_code'];?></td>
<td><?=$row_loc['branch_name'];?></td>
<td><?=$row_loc['acholder_name'];?></td>
<td><?=$row_loc['ac_no'];?></td>
<td><?=$row_loc['locationaddress'];?></td>
<td><?=$row_loc['ac_type'];?></td>
<td><?=dt_format($date[0]);?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>