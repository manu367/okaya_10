<?php 
print("\n");
print("\n");

$sql=mysqli_query($link1,"Select * from city_master ORDER BY state asc, city desc")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>State</strong></td>
<td><strong>City</strong></td>


</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['state']?></td>
<td align="left"><?=$row_loc['city']?></td>


</tr>
<?php
$i+=1;		
}
?>
</table>