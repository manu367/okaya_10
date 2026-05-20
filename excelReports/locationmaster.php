<?php 
require_once("../includes/config.php");

/** Error reporting */
//error_reporting(E_ALL);
ini_set('max_execution_time', 300);
ini_set('memory_limit', '2048M');

print("\n");
print("\n");
////// filters value/////
## selected location Status
if($ustatus==''){
	$status="statusid='1'";
}
else if($ustatus=='2'){
	$status="statusid='2'";
}else{
	$status="1";
}
//////End filters value/////

$sql_loc=mysqli_query($link1,"Select * from location_master where  $status ");
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
<td height="25"><strong>S.No.</strong></td>
<td><strong>State</strong></td>
<td><strong>City</strong></td>
<td><strong>Location Code</strong></td>
<td><strong>Location Type</strong></td>
<td><strong>Location Name</strong></td>
<td><strong> Contact Person</strong></td>
<td><strong>Mobile No.1</strong></td>
<td><strong>Mobile No.2</strong></td>
<td><strong>Email ID</strong></td>
<td><strong>Address</strong></td>
<td><strong>Shipping Address</strong></td>
<td><strong>Pin Code</strong></td>
<td><strong>Mapped Warehouse</strong></td>
<td><strong>Mapped L4</strong></td>
<td><strong>PAN Card No</strong></td>
<td><strong>GST No</strong></td>
<td><strong>Bank Name</strong></td>
<td><strong>IFSC Code</strong></td>
<td><strong>Bank Branch</strong></td>
<td><strong>Bank A/C Holder Name</strong></td>
<td><strong>Account No</strong></td>
<td><strong>Beneficiary Address</strong></td>
<td><strong>Account Type</strong></td>
<td><strong>Active ASP DD-MM-YY</strong></td>
<td><strong>Security Amount</strong></td>
<td><strong>Billing Amount</strong></td>
<td><strong>P2C/Sale Return</strong></td>
<td><strong>Claim Amount</strong></td>
<td><strong>Balance Amount</strong></td>

</tr>
<?php
$i=1;
while($row_loc = mysqli_fetch_array($sql_loc)){
	$date =explode(" ",$row_loc['createdate']);
	$mappedwh=getAnyDetails($row_loc['location_code'],"wh_location","location_code","map_wh_location",$link1);
	$mappedrepair= getAnyDetails($row_loc['location_code'],"repair_location","location_code","map_repair_location",$link1);
	
	/***** For Security Amount *******/
	$sec_amt = mysqli_fetch_array(mysqli_query($link1, "select security_amt from current_cr_status where location_code = '".$row_loc['location_code']."' "));	
	if($sec_amt['security_amt'] == ""){
		$security_amt = 0;
	}else{
		$security_amt = $sec_amt['security_amt'];
	}
	
	/***** For Billing Amount *******/
	$bill_amt = mysqli_fetch_array(mysqli_query($link1, "select sum(total_cost) as tot_bill_amt from billing_master where po_type in ('PO','PNA') and status != '5' and to_location = '".$row_loc['location_code']."' "));	
	if($bill_amt['tot_bill_amt'] == ""){
		$billing_amt = 0;
	}else{
		$billing_amt = $bill_amt['tot_bill_amt'];
	}
	
	/***** For P2C/Sale Return Amount *******/
	$pc_sr_amt = mysqli_fetch_array(mysqli_query($link1, "select sum(total_cost) as tot_pcsr_amt from billing_master where po_type in ('P2C','Sale Return') and status = '12' and from_location = '".$row_loc['location_code']."' "));	
	if($pc_sr_amt['tot_pcsr_amt'] == ""){
		$pcsr_amt = 0;
	}else{
		$pcsr_amt = $pc_sr_amt['tot_pcsr_amt'];
	}
	
	/***** For Claim Amount *******/
	$clm_amt = mysqli_fetch_array(mysqli_query($link1, "select sum(total_cost) as tot_claim_amt from billing_master where po_type in ('CLAIM') and (disp_rmk='Claim Release' or trvel_rmk='Travel Claim Release') and from_location = '".$row_loc['location_code']."' "));	
	if($clm_amt['tot_claim_amt'] == ""){
		$claim_amt = 0;
	}else{
		$claim_amt = $clm_amt['tot_claim_amt'];
	}
	
	/***** For Total Balance Amount *******/
	$balnc_amt = ($billing_amt - ($pcsr_amt + $claim_amt));
	
?>
<tr>
<td><?=$i?></td>
<td><?=getAnyDetails($row_loc['stateid'],"state","stateid","state_master",$link1);?></td>
<td><?=getAnyDetails($row_loc['cityid'],"city","cityid","city_master",$link1);?></td>
<td><?=$row_loc['location_code'];?></td>
<td><?=$row_loc['locationtype'];?></td>
<td><?=$row_loc['locationname'];?></td>
<td><?=$row_loc['contact_person'];?></td>
<td><?=$row_loc['contactno1'];?></td>
<td><?=$row_loc['contactno2'];?></td>
<td><?=$row_loc['emailid'];?></td>
<td><?=$row_loc['locationaddress'];?></td>
<td><?=$row_loc['deliveryaddress'];?></td>
<td><?=$row_loc['zipcode'];?></td>
<td><?=getAnyDetails($mappedwh,"locationname","location_code","location_master",$link1);?></td>
<td><?=getAnyDetails($mappedrepair,"locationname","location_code","location_master",$link1);?></td>
<td><?=$row_loc['panno'];?></td>
<td><?=$row_loc['gstno'];?></td>
<td><?=$row_loc['bank_name'];?></td>
<td><?=$row_loc['ifsc_code'];?></td>
<td><?=$row_loc['branch_name'];?></td>
<td><?=$row_loc['acholder_name'];?></td>
<td><?=$row_loc['ac_no'];?></td>
<td><?=$row_loc['locationaddress'];?></td>
<td><?=$row_loc['ac_type'];?></td>
<td><?=dt_format($date[0]);?></td>
<td><?=$security_amt?></td>
<td><?=$billing_amt?></td>
<td><?=$pcsr_amt?></td>
<td><?=$claim_amt?></td>
<td><?=$balnc_amt?></td>

</tr>
<?php
$i+=1;		
}
?>
</table>