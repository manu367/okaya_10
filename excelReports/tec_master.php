<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
//////End filters value/////

$sql=mysqli_query($link1,"Select * from tech_training where 1 ")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Loctaion Name</strong></td>
<td><strong>Loctaion's State</strong></td>
<td><strong>Loctaion's City</strong></td>
<td><strong>Eng Name</strong></td>
<td><strong>Trainor Name</strong></td>
<td><strong>Training Type </strong></td>
<td><strong>Description </strong></td>
<td><strong>Training Start </strong></td>
<td><strong>Training End </strong></td>
<td><strong>Score </strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<?php $det=getAnyDetails($row_loc["location_code"],"locationname,stateid,cityid","location_code","location_master",$link1);
$details= explode("~",$det);
?>
<td align="left"><?php  echo $details[0];?></td>
<td align="left"><?=getAnyDetails($details[1],"state","stateid","state_master",$link1);?></td>
<td align="left"><?=getAnyDetails($details[2],"city","cityid","city_master",$link1);?></td>
<td align="left"><?=getAnyDetails($row_loc["user_code"],"locusername","userloginid","locationuser_master",$link1);?></td>
<td align="left"><?=$row_loc['trainername']?></td>
<td align="left"><?=$row_loc['type']?></td>
<td align="left"><?=$row_loc['tr_desc']?></td>
<td align="left"><?=$row_loc['t_date']?></td>
<td align="left"><?=$row_loc['e_date']?></td>
<td align="left"><?=$row_loc['score']?></td></tr>
<?php
$i+=1;		
}
?>
</table>