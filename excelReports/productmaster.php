<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$ustatus=base64_decode($_REQUEST['status']);

## selected  Status
if($ustatus==''){
	$status="status='1'";
}
else if($ustatus=='2'){
	$status="status='2'";
}else{
	$status="1";
}
//////End filters value/////

$sql=mysqli_query($link1,"Select * from product_master where ".$status." order by product_name")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Product Name</strong></td>
<td><strong>Created By</strong></td>
<td><strong>Updated By</strong></td>
<td><strong>Update Date</strong></td>
<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['product_name']?></td>
<td align="left"><?=$row_loc['createby']?></td>
<td align="left"><?=$row_loc['updateby']?></td>
<td align="left"><?=$row_loc['updatedate']?></td>
<td align="left"><?=$arrstatus[$row_loc['status']]?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>