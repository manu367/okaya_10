<?php 
require_once("../includes/config.php");
print("\n");
print("\n");
////// filters value/////
## selected location Status
if($_REQUEST['state'] != ""){
	$state = "stateid = '".$_REQUEST['state']."'";
}else{
	$state = "1";
}
## selected  product name
function job_asp_aging($type,$loc,$daterange,$tdat,$link1){
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";

$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";
	
}
$p_interval1 = 0;
$p_interval2 = 0;
$p_interval3 = 0;
$p_interval4 = 0;
$p_interval5 = 0;

$res_jd_p = mysqli_query($link1,"select datediff(close_date,open_date) as ageing from jobsheet_data where  current_location='".$loc."' and close_date!='0000-00-00' and status!='12' and ".$daterange_open." ");
while($row_jd_p = mysqli_fetch_assoc($res_jd_p)){
	if($row_jd_p["ageing"] >= 0 && $row_jd_p["ageing"] <= 1){
		$p_interval1 ++;
	}else if($row_jd_p["ageing"] > 1 && $row_jd_p["ageing"] <= 2){
		$p_interval2 ++;
	}else if($row_jd_p["ageing"] > 2 && $row_jd_p["ageing"] <= 3){
		$p_interval3 ++;
	}else if($row_jd_p["ageing"] > 3 && $row_jd_p["ageing"] <= 4){
		$p_interval4 ++;
	}else{
		$p_interval5 ++;
	}
	
}
return $p_interval1 ."~". $p_interval2 ."~". $p_interval3 ."~". $p_interval4 ."~". $p_interval5;
}
function job_state_details($type,$loc,$daterange,$link1){
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

$res_eng_p = mysqli_query($link1,"select count(job_id) as job_count from jobsheet_data where  current_location='".$loc."' and ".$status." and ".$daterange_open." ");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['job_count']!=''){
$count_job=$row_count['job_count'];

}else{
$count_job=0;
}

return $count_job;
}
//////End filters value/////

$sql_loc=mysqli_query($link1,"select * from location_master where locationtype ='ASP' and ".$state." ");
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">

                               <th>S.No</th>
                <th>State</th>
				 <th>ASP Name</th>
				  <th>ASP Code</th>
				 <th>Complaint Upto Month</th>
				  <th>Completed Upto Month</th>
				    <th>Cancel</th>
                <th>Open </th>
                <th>Assign</th>
				   <th>Pending</th>
              
                <th>Age 0-24</th>
				  <th>Age 25-48</th>
				    <th>Age 49-72</th>
					  <th>Age 73-96</th>
					    <th>Age 96></th>
                
                <th>Tat %</th>

</tr>
<?php
$i=1;
while($row = mysqli_fetch_array($sql_loc)){
 $row6= job_state_details("Replacement",$row["location_code"],$_REQUEST['daterange'],$link1);
  $row1=job_state_details("COM",$row["location_code"],$_REQUEST['daterange'],$link1);
   $row7= job_state_details("GT",$row["location_code"],$_REQUEST['daterange'],$link1);
 $row8=$row1+$row6;
//$row_per=($row8/$row7)*100;

$row9= job_asp_aging("ageing",$row["location_code"],$_REQUEST['daterange'],$today,$link1);

$ageing_day=explode("~",$row9);

$interval2=$ageing_day[0]+$ageing_day[1];
if($interval2==0 && $row8==0){
$row_per=0;
}
else if($interval2>0 && $row8>0){

$row_per=($interval2/$row8)*100;
}

else{
$row_per=0;
}

?>
<tr>
<td><?=$i?></td>
<td><?= getAnyDetails($row['stateid'],"state","stateid","state_master",$link1);?></td>
<td><?=$row["locationname"]?></td>
<td><?=$row["location_code"]?></td>
<td><?php echo $row7?></td>
<td><?php echo  $row8;?></td>
<td><?php echo $row5= job_state_details("CANCEL",$row["location_code"],$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row2=job_state_details("OPEN",$row["location_code"],$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row3=job_state_details("Assign",$row["location_code"],$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row4=job_state_details("PEN",$row["location_code"],$_REQUEST['daterange'],$link1);?></td>

<td><?=$ageing_day[0]?></td>
<td><?=$ageing_day[1]?></td>
<td><?=$ageing_day[2]?></td>
<td><?=$ageing_day[3]?></td>
<td><?=$ageing_day[4]?></td>




<td><?=round($row_per,2)?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>