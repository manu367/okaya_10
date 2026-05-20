<?php
require_once("../includes/config.php");

date_default_timezone_set('Asia/Calcutta');
$arrstatus = getJobStatus($link1);
$arrstate = getAccessState($_SESSION['userid'],$link1);


//// extract all encoded variables
 $pending = base64_decode($_REQUEST['pending']);
//echo "<br><br>";

//echo "<br><br>";
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
//echo "<br><br>";

//echo "<br><br>";

/////make brand array


/////make product master array







if($pending != 'checked'){

//////// get date /////////////////////////

if ($_REQUEST['daterange'] != ""){

$seldate = explode(" - ",$_REQUEST['daterange']);

$fromdate = $seldate[0];

$todate = $seldate[1];

}

else{

$seldate = $today;

$fromdate = $today;

$todate = $today;

}

/////////////////////////////////////STATE////////////////
/*if($state==""){
$state_nm="and location_code in(select location_code from location_master  where stateid in (".$arrstate.") )";
}else{
$state_nm="and location_code in(select location_code from location_master  where stateid ='".$state."')";
}*/

/////get location///////////////

if($locaftrselct !="" ){
$loc_nam=" and current_location='".$locaftrselct."'";
}else if($state!=""){
//$loc_nam=$state_nm;
$loc_nam=" and state_id ='".$state."'";
}else{
$loc_nam=" and 1";
}

////////////////////////////////
/////Get product& brand ////
//echo "<br><br>";
if($productid==''){
 $product_ids=" 1 ";
}else{
$product_ids="product_id = ".$productid."";
}

//echo "<br><br>";
if($brandid==''){
$brand_ids=" 1 ";
}else{
$brand_ids=" brand_id = ".$brandid."";
}

/////make madel array



/////get model///////////////
//if($modelid=="all"){
//$model_id=" and  model_id in(".$str_model.")";
//}else {
//$model_id="and model_id in ('".$str_model."' )";
//}
 if($status == "all"){
	 $st= " ";
	 }else{
		 $st= " and status= '".$status."' ";}

//$st="a}nd status in ('".$status."' )";
//$subst="and sub_status in ('".$substatus."' )";

//////

//////End filters value/////

/////////////////////////date type//////////

if($r_date==""){

$date_in="(open_date >= '".$fromdate."' and open_date <='".$todate."')";

}else{

$date_in=" ($r_date >= '".$fromdate."' and $r_date <='".$todate."')";

}


}
if($pending == 'checked'){
//echo "Select sub_status,status,symp_code,job_no,city_id,cust_problem,cust_problem3,address,pincode,close_date,open_date,current_location,customer_type,call_for,call_type,warranty_status,imei,sec_imei,model,customer_name,contact_no,activation,dop,hand_date,eng_id,remark,doa_remark,product_id,brand_id  from jobsheet_data where status not in('10' ,'11' , '12' , '13') and  state_id in(".$arrstate.")";
$sql_loc = mysqli_query($link1,"Select sub_status,status,symp_code,job_no,state_id,cust_problem,cust_problem3,address,pincode,close_date,open_date,current_location,customer_type,call_for,call_type,warranty_status,imei,sec_imei,model,customer_name,contact_no,activation,dop,hand_date,eng_id,remark,doa_remark,product_id,brand_id  from jobsheet_data where status not in('10' ,'11' , '12' , '13') and  brand_id in(".$access_brand.")")or die("Error1".mysqli_error($link1));

}

else{

//echo "Select sub_status,status,symp_code,job_no,cust_problem,cust_problem3,close_date,open_date,location_code,customer_type,call_for,call_type,warranty_status,imei,sec_imei,model,customer_name,contact_no,activation,dop,hand_date,eng_id,remark,doa_remark,product_id,brand_id  from jobsheet_data where ".$date_in." ".$loc_nam."  and ".$brand_ids." ".$st." ".$subst." ".$model_id." ";
//exit;
$sql_loc = mysqli_query($link1,"Select sub_status,status,symp_code,job_no,state_id,city_id,cust_problem,cust_problem3,address,pincode,close_date,open_date,current_location,customer_type,call_for,call_type,warranty_status,imei,sec_imei,model,customer_name,contact_no,activation,dop,hand_date,eng_id,remark,doa_remark,product_id,brand_id  from jobsheet_data where ".$date_in." and  brand_id in(".$access_brand.") ")or die("Error1".mysqli_error($link1));

}
?>
<table width="100%" border="1" cellpadding="2" cellspacing="1" bordercolor="#000000">

<tr align="left" style="background-color:#396; color:#FFFFFF;font-size:13px;font-family:Verdana, Arial, Helvetica, sans-serif;font-weight:normal;vertical-align:central">
    <td>S.No.</td>
    <td>State</td>
    <td>City</td>
    <td>ASP Name</td>
    <td>ASP Code</td>
    <td>Job Received From</td>
    <td>Job For</td>
    <td>Job Type</td>
    <td>Warranty Status</td>
    <td>Job No.</td>
    <td><?php echo SERIALNO ?></td>
      <td>Model</td>
    <td>Customer Name</td>
    <td>Contact No.</td>
	 <td>Address.</td>
	  <td>Pincode.</td>
    <td>Open Date</td>
  
    <td>POP Date</td>

    <td>Defect Reported</td>
    <td>PNA/PO No.</td>
    <td>PNA 1</td>
    <td>PNA 1 Description</td>
    <td>Close Date</td>
    <td>Handover Date</td>

 
    <td>Eng Name </td>
    <td>Job Status</td>
    <td>Remark</td>

    <td>Product</td>
    <td>Brand</td>
    <td>Aging</td>

  
  </tr>
<?php 
$count=1;

while($row_loc = mysqli_fetch_array($sql_loc)){
			$resst=$arrstatus[$row_loc['sub_status']][$row_loc['status']];
	if($resst!=''){
		$res_st=$resst;
	}else{
		$row_set=mysqli_fetch_array(mysqli_query($link1,"select  display_status from jobstatus_master status_id='".$row_loc['status']."' and main_status_id='".$row_loc['status']."'"));
		$res_st=$row_set['display_status'];
	}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////			 

if($row_loc['close_date'] =='0000-00-00' ){ $aging = daysDifference($today,$row_loc['open_date']);} else {$aging = "--" ;}



$statestr = '';

$partdesc ='';

$sql1 = mysqli_query($link1,"Select partcode from auto_part_request where job_no= '".$row_loc['job_no']."' ");

while($sql = mysqli_fetch_array($sql1)){	

if($statestr==""){$statestr = $sql['partcode'];}else {

$statestr.=",".$sql['partcode'];	}

if($partdesc==""){$partdesc = getAnyDetails($sql["partcode"],"part_desc","partcode","partcode_master",$link1);}else {

$partdesc.=",".getAnyDetails($sql["partcode"],"part_desc","partcode","partcode_master",$link1);	}

}


$voc1 = getAnyDetails($row_loc['cust_problem'] ,"voc_desc","voc_code","voc_master" ,$link1);
$voc2 = getAnyDetails($row_loc['cust_problem2'] ,"voc_desc","voc_code","voc_master" ,$link1);
$voc3 = getAnyDetails($row_loc['cust_problem3'] ,"voc_desc","voc_code","voc_master" ,$link1);



?>
  <tr>  	
    <td><?=$count;?></td>
    <td><?=getAnyDetails($row_loc['state_id'],"state","stateid","state_master",$link1);?></td>
    <td><?=getAnyDetails($row_loc['city_id'],"city","cityid","city_master",$link1);?></td>
    <td><?=getAnyDetails($row_loc['current_location'],"locationname","location_code","location_master",$link1)?></td>
    <td><?=$row_loc['current_location'];?></td>
    <td><?=$row_loc['customer_type'];?></td>
    <td><?=$row_loc['call_for'];?></td>
    <td><?=$row_loc['call_type'];?></td>
    <td><?=$row_loc['warranty_status'];?></td>
    <td><?=$row_loc['job_no'];?></td>
    <td><?=$row_loc['imei'];?></td>

    <td><?=$row_loc['model'];?></td>
    <td><?=cleanData($row_loc['customer_name']);?></td>
    <td><?=$row_loc['contact_no'];?></td>
   <td><?=$row_loc['address'];?></td>
    <td><?=$row_loc['pincode'];?></td>
    <td><?=dt_format($row_loc['open_date']);?></td>

    <td><?=dt_format($row_loc['dop']);?></td>
    <td><?=$voc1."/".$voc2."/".$voc3;?></td>

    <td><?=$pono['po_no'];?></td>
    <td><?=$statestr;?></td>
    <td><?=$partdesc;?></td>
    <td><?=dt_format($row_loc['close_date']);?></td>
    <td><?=dt_format($row_loc['hand_date']);?></td>

    <td><?=getAnyDetails($row_loc['eng_id'],"locusername","userloginid","locationuser_master",$link1);?></td>
    <td><?=$res_st;?></td>
    <td><?=cleanData($row_loc['remark']);?></td>
 
    <td><?=getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1);?></td>
    <td><?=getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1);?></td>
    <td><?=$aging;?></td>



  </tr>
  <?php
$count+=1;		
}


?>
</table>
