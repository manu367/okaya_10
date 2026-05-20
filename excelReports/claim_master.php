<?php 
print("\n");
print("\n");
////// filters value/////
//// extract all encoded variables
$ustatus=base64_decode($_REQUEST['status']);

## selected  Status
if($ustatus!=""){
	$status="status='".$ustatus."'";
}else{
	$status="1";
}
//////End filters value/////

$sql=mysqli_query($link1,"Select * from claim_price where ".$status."")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>

 <td>Product Category</td>
			   <td>Brand</td>
              <td>Installation</td>
			 <td>Repairs Without Part</td>
			   <td> Repairs With Parts</td>
			   <td>  Gas Charging Without Part </td>
				<td>  Gas Charging With Part </td>



<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row = mysqli_fetch_array($sql)){

	
?>
<tr>
<td align="left"><?=$i?></td>


<td align="left"><?=getAnyDetails($row['product_id'],"product_name","product_id","product_master",$link1)?></td>
<td align="left"><?=getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1)?></td>

<td align="left"><?=$row["loc_iw_inst"]?></td>
<td align="left"><?=$row["loc_iw_npu"]?></td>
<td align="left"><?=$row["loc_iw_pu"]?></td>
<td align="left"><?=$row["gas_iw_pu"]?></td>
<td align="left"><?=$row["gas_iw_npu"]?></td>




<td align="left"><?=$arrstatus[$row['status']]?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>