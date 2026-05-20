<?php 
require_once("../includes/config.php");

print("\n");
print("\n");

function getAccBrandDetail($gettingfor,$link1){
	$arr_status = "";
	if($gettingfor){ $used_in = " location_code = '".$gettingfor."'";}else{ $used_in = "1";}
	$result_set = mysqli_query($link1,"select brand_id from access_brand where ".$used_in." and status='Y'  group by brand_id ") or die(mysqli_error($link1));
	while($row_set=mysqli_fetch_assoc($result_set)){
		$arr_status .= getAnyDetails($row_set['brand_id'],"brand","brand_id","brand_master",$link1).", ";
	}
	return $arr_status;
}

function getAccProDetail($gettingfor,$link1){
	$arr_status = "";
	
	if($gettingfor){ $used_in = " location_code = '".$gettingfor."'";}else{ $used_in = "1";}
	$result_set = mysqli_query($link1,"select product_id from access_product where ".$used_in." and status='Y' group by product_id ") or die(mysqli_error($link1));
	while($row_set=mysqli_fetch_assoc($result_set)){
		$arr_status .= getAnyDetails($row_set['product_id'],"product_name","product_id","product_master",$link1).", ";
	}
	return $arr_status;
}

////// filters value/////
## selected location Status
$ustatus = base64_decode($_REQUEST['status']);
if($ustatus==''){
	$status="statusid='1'";
}
else if($ustatus=='2'){
	$status="statusid='2'";
}else{
	$status="1";
}
//////End filters value/////

$sql_loc=mysqli_query($link1,"Select * from location_master where locationtype='ASP' and ".$status." ");
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No</strong></td>
<td><strong>Name</strong></td>
<td><strong>Address</strong></td>
<td><strong>Contact Info</strong></td>
<td><strong>Mapped Pin Code</strong></td>
<td><strong>Mapped Brands</strong></td>
<td><strong>Mapped Product</strong></td>
<td><strong>Additional Holiday</strong></td>
<td><strong>Woking Time</strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql_loc)){	
	$pin_det=mysqli_query($link1,"SELECT pincode FROM `location_pincode_access` WHERE `location_code` = '".$row_loc['location_code']."'  and statusid='1' GROUP by pincode" );
	$pin_details="";
	$p=0;
	while($pin=mysqli_fetch_array($pin_det)){
		if(($p%7)== 0){ 
			$pin_details.="<br>";
		}else{
			$pin_details.=$pin['pincode'].",";
		}
	$p++;
	}
	
	$add_de=mysqli_query($link1,"SELECT date,description  FROM holidays where location_code='".$row_loc['location_code']."' and type='Additional Holiday'");
	$add_dates="";
	$i=1;
	while($addi_detials=mysqli_fetch_array($add_de)){
          $add_dates.=$addi_detials['date']."<br/><br/><b>Description-</b>&nbsp;&nbsp;".$addi_detials['description']."<br><br>";
	$i++;
	}
	
	$time_details=mysqli_query($link1,"SELECT weekly,start_time,end_time  FROM holidays where location_code='".$row_loc['location_code']."' and type='ASC Working Time'");
	$timeing_detials=mysqli_fetch_array($time_details);
	$time_update=mysqli_num_rows($time_details);
	if($time_update>0){
		$det=$timeing_detials['start_time']." To ".$timeing_detials['end_time']."<br/><br/><b>Holiday-</b>&nbsp;&nbsp;".$timeing_detials['weekly'];
	}else{
		$det="";
	}
	
	$acc_brd = getAccBrandDetail($row_loc['location_code'],$link1);
	$acc_prd = getAccProDetail($row_loc['location_code'],$link1);
	
?>
<tr>
<td><?=$i?></td>
<td><?=$row_loc['locationname'];?></td>
<td><?=$row_loc['locationaddress'].", ".getAnyDetails($row_loc["stateid"],"state","stateid","state_master",$link1).", ".getAnyDetails($row_loc["cityid"],"city","cityid","city_master",$link1);?></td>
<td><?=$row_loc['contact_person'].", ".$row_loc['emailid'].", ".$row_loc['contactno1'];?></td>
<td><?=$pin_details;?></td>
<td><?php echo $acc_brd; //print_r($acc_brd); ?></td>
<td><?php echo $acc_prd; //print_r($acc_prd); ?></td>
<td><?=$add_dates;?></td>
<td><?=$det;?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>