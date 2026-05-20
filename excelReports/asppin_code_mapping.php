<?php 
print("\n");
print("\n");

$sql=mysqli_query($link1,"Select a.stateid,a.cityid,a.location_code,a.locationname,b.pincode,b.postoffice, b.statusid  FROM location_master a, location_pincode_access b where a.location_code=b.location_code  and  b.location_code='".$_REQUEST['locationcode']."'  ")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>location State</strong></td>
<td><strong>Location Code</strong></td>
<td><strong>Location Name</strong></td>
<td><strong>Pincode</strong></td>
<td><strong>Area</strong></td>
<td><strong>Status</strong></td>


</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td><?=getAnyDetails($row_loc['stateid'],"state","stateid","state_master",$link1);?></td>

<td><?=$row_loc['location_code']?></td>
<td><?=$row_loc['locationname']?></td>
<td><?=$row_loc['pincode']?></td>
<td><?=$row_loc['postoffice']?></td>
<td><?php if($row_loc['statusid'] == 1){ echo "Active"; }else{ echo "Deactive"; } ?></td>


</tr>
<?php
$i+=1;		
}
?>
</table>