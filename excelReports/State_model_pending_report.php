<?php 
require_once("../includes/config.php");
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

print("\n");
print("\n");
////// filters value/////
## selected location Status
if($_REQUEST['product_id'] != ""){
	$productid = "product_id = '".$_REQUEST['product_id']."'";
}else{
	$productid = "1";
}
## selected  product name
if($_REQUEST['brand'] != ""){
	$brandid = "brand_id = '".$_REQUEST['brand']."'";
}else{
	$brandid = "brand_id in (".$access_brand.")";
}
if($_REQUEST['modelid'] != ""){
	$modelid = "model_id = '".$_REQUEST['modelid']."'";
}else{
	$modelid = "1";
}
function job_model_details($model,$state,$daterange,$link1){
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";

$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";
	
}

//echo "select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and model_id='".$model."' and close_date='0000-00-00' and ".$daterange_open."";
$res_eng_p = mysqli_query($link1,"select count(job_id) as job_count from jobsheet_data where state_id='".$state."' and model_id='".$model."' and close_date='0000-00-00' and ".$daterange_open." ");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['job_count']!=''){
$count_job=$row_count['job_count'];

}else{
$count_job=0;
}

return $count_job;
}
//////End filters value/////

$sql_loc=mysqli_query($link1,"select *from model_master where 1 and ".$productid." and ".$brandid." and ".$modelid." ");
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">
<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
   <th>S.No</th>
                <th>Model</th>
				  <th>Product</th>
                <th>Delhi</th>
                <th>Haryana</th>
                <th>Punjab</th>
                <th>Himachal Pradesh</th>
                <th>Jammu and Kashmir</th>
                <th>Uttar Pradesh</th>
                <th>Uttarakhand</th>
                <th>West Bengal</th>
                <th>Orissa</th>
                <th>Bihar</th>
                <th>Rajasthan</th>
                <th>Chandigarh</th>
                <th>Madhya Pradesh</th>
                <th>Maharashtra</th>
                <th>Chhattisgarh</th>
                <th>Goa</th>
                <th>Gujarat</th>
                <th>ANDAMAN AND NICOBAR ISLANDS</th>
                <th>Andhra Pradesh</th>
                <th>Arunachal Pradesh</th>
                <th>Assam</th>
                <th>Daman & Diu</th>
                <th>Jharkhand</th>
                <th>Karnataka</th>
                <th>Kerala</th>
                <th>Manipur</th>
                <th>Meghalaya</th>
                <th>Mizoram</th>
                <th>Nagaland</th>
                <th>Pondicherry</th>
                <th>Sikkim</th>
                <th>Tamilnadu</th>
                <th>Telangana</th>
                <th>Tripura</th>
				  <th>Total</th>

</tr>
<?php
$i=1;
while($row = mysqli_fetch_array($sql_loc)){

?>
<tr>
<td><?=$i?></td>
<td><?=$row["model"];?></td>
<td><?=getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1);?></td>
<td><?php echo $row15=job_model_details($row["model_id"],'15',$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row16=job_model_details($row["model_id"],'16',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row8=job_model_details($row["model_id"],'8',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo  $row3=job_model_details($row["model_id"],'3',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row9=job_model_details($row["model_id"],'9',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row22=job_model_details($row["model_id"],'22',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row10=job_model_details($row["model_id"],'10',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row19= job_model_details($row["model_id"],'19',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row20=job_model_details($row["model_id"],'20',$_REQUEST['daterange'],$link1);;?></td>
<td><?php  echo $row4= job_model_details($row["model_id"],'4',$_REQUEST['daterange'],$link1);;?></td>


<td><?php  echo $row7=job_model_details($row["model_id"],'7',$_REQUEST['daterange'],$link1);?></td>
<td><?php echo $row33=job_model_details($row["model_id"],'33',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row1=job_model_details($row["model_id"],'1',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row6=job_model_details($row["model_id"],'6',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row18=job_model_details($row["model_id"],'18',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row14=job_model_details($row["model_id"],'14',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo  $row5=job_model_details($row["model_id"],'5',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row34= job_model_details($row["model_id"],'34',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row2=job_model_details($row["model_id"],'2',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row27= job_model_details($row["model_id"],'27',$_REQUEST['daterange'],$link1);;?></td>


<td><?php echo $row24=job_model_details($row["model_id"],'24',$_REQUEST['daterange'],$link1);?></td>
<td><?php echo  $row35=job_model_details($row["model_id"],'35',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row17=job_model_details($row["model_id"],'17',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row13=job_model_details($row["model_id"],'13',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row12=job_model_details($row["model_id"],'12',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row26=job_model_details($row["model_id"],'26',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo  $row28=job_model_details($row["model_id"],'28',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row29= job_model_details($row["model_id"],'29',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row30=job_model_details($row["model_id"],'30',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row32= job_model_details($row["model_id"],'32',$_REQUEST['daterange'],$link1);;?></td>

<td><?php echo  $row21=job_model_details($row["model_id"],'21',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row11= job_model_details($row["model_id"],'11',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row25=job_model_details($row["model_id"],'25',$_REQUEST['daterange'],$link1);;?></td>
<td><?php echo $row31= job_model_details($row["model_id"],'31',$_REQUEST['daterange'],$link1);;?></td>

<td><?=$row15+$row16+$row8+$row3+$row9+$row22+$row10+$row19+$row20+$row4+$row7+$row33+$row1+$row18+$row14+$row5+$row34+$row2+$row27+$row24+$row35+$row17+$row13+$row12+$row26+$row28+$row29+$row30+$row32+$row21+$row11+$row25+$row31;?></td>
</tr>
<?php
$i+=1;		
}
?>
</table>