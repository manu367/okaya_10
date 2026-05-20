<?php 
require_once("../includes/config.php");
print("\n");
print("\n");
$arrstate = getAccessState($_SESSION['userid'],$link1);
////// filters value/////
## selected location Status
if($_REQUEST['state'] != ""){
	$state = "stateid = '".$_REQUEST['state']."'";
	$state_id = "state_id = '".$_REQUEST['state']."'";
}else{
	$state = "stateid in (".$arrstate.")";
	$state_id = "state_id in (".$arrstate.")";
}
## selected  product name

function job_state_details($type,$state,$daterange,$link1){
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";

$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";
	
}
if($type=="COM"){

$status="status in('6','48','49','10','11')";
}else if($type=="OPEN"){

$status="status in('1','55','56')";
}
else if($type=="Assign"){

$status="status in('2')";
}
else if($type=="CANCEL"){

$status="status in('12')";
}
else if($type=="Replacement"){

$status="status in('8')";
}
else if($type=="GT"){

$status="1";
}
else if($type=="PEN"){
$status="status  in('3','5','7','50')";
}

//echo "select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and ".$status." and ".$daterange_open."";

$res_eng_p = mysqli_query($link1,"select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and ".$status." and ".$daterange_open." ");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['job_count']!=''){
$count_job=$row_count['job_count'];

}else{
$count_job=0;
}

return $count_job;
}

function job_state_details_tat($type,$state,$daterange,$link1){
$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";
	
}

//echo "select datediff(close_date,open_date) as ageing from jobsheet_data where ".$state_id."  and close_date!='0000-00-00' and ".$daterange."";

//echo "select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and close_date!='0000-00-00' and ".$daterange_open." and  close_tat<=2";
//echo "select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and close_date!='0000-00-00' and ".$daterange_open." and  close_tat<3";
$res_jd = mysqli_query($link1,"select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and close_date!='0000-00-00' and ".$daterange_open." and  close_tat<3 and status!='12'");

$rowcount=mysqli_num_rows($res_jd);

$row_count = mysqli_fetch_array($res_jd);
if($row_count['job_count']!=''){
$count_job=$row_count['job_count'];

}else{
$count_job=0;
}

return $count_job;
}
//////End filters value/////

$sql_loc=mysqli_query($link1,"select * from state_master where 1 and ".$state." ");
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">

                   <th>S.No</th>
                <th>State</th>
				 <th>Completed</th>
                <th>Open </th>
                <th>Assign</th>
				   <th>Pending</th>
                <th>cancel</th>
                <th>Replacement</th>
				 <th>0 To 48</th>
                <th>Grand Total</th>
                <th>Tat %</th>

</tr>
<?php
$i=1;
while($row = mysqli_fetch_array($sql_loc)){

?>
<tr>
<td><?=$i?></td>
<td><?=$row["state"];?></td>

<td><?php echo $row1=job_state_details("COM",$row["stateid"],$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row2=job_state_details("OPEN",$row["stateid"],$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row3=job_state_details("Assign",$row["stateid"],$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row4=job_state_details("PEN",$row["stateid"],$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row5= job_state_details("CANCEL",$row["stateid"],$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row6= job_state_details("Replacement",$row["stateid"],$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $interval2= job_state_details_tat("GT",$row["stateid"],$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row7= job_state_details("GT",$row["stateid"],$_REQUEST['daterange'],$link1);?></td>

<?php $row8=$row1+$row6;

if($interval2==0 && $row8==0){
$row_per=0;
}
else if($interval2>0 && $row8>0){

$row_per=($interval2/$row8)*100;
}

else{
$row_per=0;
}?>



<td><?=round($row_per)?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>