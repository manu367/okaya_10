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

$sql=mysqli_query($link1,"Select * from voc_master where ".$status."")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>VOC Code</strong></td>
<td><strong>VOC</strong></td>

<td><strong>Brand</strong></td>
<td><strong>Product</strong></td>


<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql)){

	//// array initialization to send by query string of  brand
	$prdstr = "";
	$arr_prd = explode(",",$row_loc['mapped_product']);
	for($i=0; $i<count($arr_prd); $i++){
		if($prdstr){
			$prdstr.=", ".getAnyDetails($arr_prd[$i],"product_name","product_id","product_master",$link1);
		}else{
			$prdstr.= getAnyDetails($arr_prd[$i],"product_name","product_id","product_master",$link1);
		}
	}
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=$row_loc['voc_code']?></td>
<td align="left"><?=$row_loc['voc_desc']?></td>

<td align="left"><?=getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1)?></td>
<td align="left"><?=$prdstr;?></td>


<td align="left"><?=$arrstatus[$row_loc['status']]?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>