<?php 

require_once("../includes/common_function.php");
$access_product = getAccessProduct($_SESSION['userid'],$link1);

////get access brand details

$access_brand = getAccessBrand($_SESSION['userid'],$link1);
////// filters value/////
//// extract all encoded variables
$ustatus=base64_decode($_REQUEST['status']);
 if($_REQUEST['product']!='' && $_REQUEST['brand']=='' && $_REQUEST['model']==""){
	$mod_id =  "product_id='".$_REQUEST['product']."'";
	}else if($_REQUEST['product']!='' && $_REQUEST['brand']!='' && $_REQUEST['model']==""){
		$mod_id =  "product_id='".$_REQUEST['product']."' and brand_id  = '".$_REQUEST['brand']."'";
		}else if($_REQUEST['product']!='' && $_REQUEST['brand']!='' && $_REQUEST['model']!=""){
			$mod_id =  "product_id='".$_REQUEST['product']."' and brand_id  = '".$_REQUEST['brand']."' and model_id  Like '%".$_REQUEST['model']."%'";
		}else if($_REQUEST['product']=='' && $_REQUEST['brand']!='' && $_REQUEST['model']!=""){
			$mod_id =  " brand_id  = '".$_REQUEST['brand']."' and model_id  Like '%".$_REQUEST['model']."%'";
		}else if($_REQUEST['product']=='' && $_REQUEST['brand']!='' && $_REQUEST['model']==""){
			$mod_id =  " brand_id  = '".$_REQUEST['brand']."' ";
		}else if($_REQUEST['product']=='' && $_REQUEST['brand']=='' && $_REQUEST['model']==""){
			$mod_id =  "brand_id in (".$access_brand.") ";
		}else{
			$mod_id= " brand_id in (".$access_brand.")";
			}

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

$sql=mysqli_query($link1,"Select * from partcode_master where  status='1' and ".$mod_id."")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Brand</strong></td>
<td><strong>Product</strong></td>
<td><strong>Model</strong></td>
<td><strong>Partcode</strong></td>
<td><strong>HSN CODE</strong></td>
<td><strong>Part Name</strong></td>
<td><strong>Part Description</strong></td>
<td><strong>Vendor Partcode</strong></td>
<td><strong>Customer Price</strong></td>
<td><strong>Location Price</strong></td>

<td><strong>Part Category</strong></td>

<td><strong>Part For</strong></td>
<td><strong>Repair Code</strong></td>
<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row_part = mysqli_fetch_array($sql)){
	
	$mod_id=$row_part['model_id'];
	$explodee = explode(",",$mod_id);
	$rslt_str="";
	for($k=0;$k < count($explodee);$k++){
		$model_name = getAnyDetails($explodee[$k],"model","model_id","model_master",$link1);
		if($rslt_str==""){
          		$rslt_str= $model_name;
	   		}else{
          		$rslt_str.= ",".$model_name;
			}
	}
	
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?=getAnyDetails($row_part['brand_id'],"brand","brand_id","brand_master",$link1)?></td>
<td align="left"><?=getAnyDetails($row_part['product_id'],"product_name","product_id","product_master",$link1)?></td>
<td align="left"><?=$row_part['part_name']?></td>
<td align="left"><?=$row_part['partcode']?></td>
<td align="left"><?=$rslt_str?></td>
<td align="left"><?=$row_part['hsn_code']?></td>
<td align="left"><?=$row_part['part_desc']?></td>
<td align="left"><?=$row_part['vendor_partcode']?></td>
<td align="left"><?=$row_part['customer_price']?></td>
<td align="left"><?=$row_part['location_price']?></td>

<td align="left"><?=$row_part['part_category']?></td>

<td align="left"><?=$row_part['part_for']?></td>
<td align="left"><?=$row_part['repair_code']?></td>
<td align="left"><?=$arrstatus[$row_part['status']]?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>