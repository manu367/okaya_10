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

$sql=mysqli_query($link1,"Select * from model_master where ".$status."")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Model Code</strong></td>
<td><strong>Model Id</strong></td>	
<td><strong>Model Name</strong></td>
<td><strong>Technical Model Name</strong></td>
<td><strong>Brand</strong></td>
<td><strong>Product</strong></td>
<td><strong>SIM Type</strong></td>
<td><strong>Feature Type</strong></td>
<td><strong>IMEI/Serial No. length</strong></td>
<td><strong>Software Version</strong></td>
<td><strong>Make Job?</strong></td>
<td><strong>Make DOA?</strong></td>
<td><strong>Repairable?</strong></td>
<td><strong>Out Warranty?</strong></td>
<td><strong>Replacement?</strong></td>
<td><strong>Replacement Days</strong></td>
<td><strong>Check IMEI/Serial No.?</strong></td>
<td><strong>Warranty Days</strong></td>
<td><strong>Release Date</strong></td>
<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['model_id']?></td>
<td align="left"><?=$row_loc['model_id']?></td>	
<td align="left"><?=$row_loc['model']?></td>
<td align="left"><?=$row_loc['technical_model']?></td>
<td align="left"><?=getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1)?></td>
<td align="left"><?=getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1)?></td>
<td align="left"><?=$row_loc['sim_type']?></td>
<td align="left"><?=$row_loc['feature_type']?></td>
<td align="left"><?=$row_loc['len_serialno']?></td>
<td align="left"><?=$row_loc['software_version']?></td>
<td align="left"><?=$row_loc['make_job']?></td>
<td align="left"><?=$row_loc['make_doa']?></td>
<td align="left"><?=$row_loc['repairable']?></td>
<td align="left"><?=$row_loc['out_warranty']?></td>
<td align="left"><?=$row_loc['replacement']?></td>
<td align="left"><?=$row_loc['replace_days']?></td>
<td align="left"><?=$row_loc['chk_serimei']?></td>
<td align="left"><?=$row_loc['wp']?></td>
<td align="left"><?=$row_loc['release_date']?></td>
<td align="left"><?=$arrstatus[$row_loc['status']]?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>