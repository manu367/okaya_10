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
	$status="";
}
//////End filters value/////

$sql=mysqli_query($link1,"Select * from imei_data_temp where ".$status."")or die("er1".mysqli_error($link1));
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>Req by</strong></td>
<td><strong>Model</strong></td>
<td><strong>IMEI 1</strong></td>
<td><strong>IMEI 2</strong></td>
<td><strong>Import Date</strong></td>

<td><strong>Status</strong></td>
</tr>
<?php
$i=1;
while($row = mysqli_fetch_array($sql)){

	
?>
<tr>
<td align="left"><?=$i?></td>
<td align="left"><?= getAnyDetails($row["req_by"],"locationname" ,"location_code","location_master",$link1);?></td>
<td align="left"><?=getAnyDetails($row["model_id"],"model","model_id","model_master",$link1);?></td>
<td align="left"><?=$row["imei1"]?></td>
<td align="left"><?=$row["imei2"]?></td>
<td align="left"><?=$row["import_date"]?></td>
<td align="left"><?php if($row["status"] == "1"){$st = "Pending";} elseif($row["status"] == "2") {$st = "Approved";} elseif($row["status"] == "3") {$st = "Rejected";} else{} echo $st;?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>