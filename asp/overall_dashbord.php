<?php
include("../includes/config.php");
/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);
/////// get Access brand////////////////////////
$arrbrand = getAccessBrand($_SESSION['userid'],$link1);
/////// get Access product category////////////////////////
$arrproduct = getAccessProduct($_SESSION['userid'],$link1);

////// filter value

/////////
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";
	$daterange_close= "close_date >= '".$date_range[0]."' and close_date <= '".$date_range[1]."'";
}else{
	$daterange_open = "1";
	$daterange_close="1";
}
//////// 
if($_POST["brand"]!=""){
	$brand_condi = " brand_id = '".$_POST["brand"]."'";
}else{
	$brand_condi = " brand_id in (".$arrbrand.")";
}
////////
if($_POST["product_cat"]!=""){
	$prod_condi = " product_id = '".$_POST["product_cat"]."'";
}else{
	$prod_condi = " 1";
}
//////// 

////// count Closed tat from jobsheet data
$interval1 = 0;
$interval2 = 0;
$interval3 = 0;
$interval4 = 0;
$interval5 = 0;
//echo "select datediff(close_date,open_date) as ageing from jobsheet_data where (current_location='".$_SESSION['asc_code']."' or location_code='".$_SESSION['asc_code']."') and ".$prod_condi."  and close_date!='0000-00-00'";
$res_jd = mysqli_query($link1,"select datediff(close_date,open_date) as ageing from jobsheet_data where (current_location='".$_SESSION['asc_code']."' or location_code='".$_SESSION['asc_code']."') and ".$prod_condi."  and close_date!='0000-00-00' and ".$daterange_close."");
while($row_jd = mysqli_fetch_assoc($res_jd)){
	if($row_jd["ageing"] >= 0 && $row_jd["ageing"] <= 1){
		$interval1 ++;
	}else if($row_jd["ageing"] > 1 && $row_jd["ageing"] <= 2){
		$interval2 ++;
	}else if($row_jd["ageing"] > 2 && $row_jd["ageing"] <= 3){
		$interval3 ++;
	}else if($row_jd["ageing"] > 3 && $row_jd["ageing"] <= 4){
		$interval4 ++;
	}else{
		$interval5 ++;
	}
}
//echo $interval1." - ".$interval2." - ".$interval3." - ".$interval4;

$p_interval1 = 0;
$p_interval2 = 0;
$p_interval3 = 0;
$p_interval4 = 0;
$p_interval5 = 0;
//echo "select datediff('".$today."',open_date) as ageing from jobsheet_data where  (current_location='".$_SESSION['asc_code']."' or location_code='".$_SESSION['asc_code']."')   and ".$prod_condi."  and close_date='0000-00-00'";
$res_jd_p = mysqli_query($link1,"select datediff('".$today."',open_date) as ageing from jobsheet_data where  (current_location='".$_SESSION['asc_code']."' or location_code='".$_SESSION['asc_code']."')   and ".$prod_condi."  and close_date='0000-00-00' and ".$daterange_open." ");
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

function job_eng_details($eng_name,$status,$daterange,$link1){
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";

$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";
	$daterange_close= "close_date >= '".$date_range[0]."' and close_date <= '".$date_range[1]."'";
}else{
	$daterange_open = "1";
	$daterange_close="1";
}
if($status=='49'|| $status=='48' || $status=='6' || $status=='10' ){
$date_dt=$daterange_close;

}
else{
$date_dt=$daterange_open;
}
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' and ".$date_dt."";
$res_eng_p = mysqli_query($link1,"select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' and ".$date_dt." ");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['job_count']!=''){
$count_job=$row_count['job_count'];

}else{
$count_job=0;
}

return $count_job;
}


function po_details($loc,$status,$daterange,$link1){

$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "po_date >= '".$date_range[0]."' and po_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";

}
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";
$res_eng_p = mysqli_query($link1,"select count(id) as po_count from po_master where status='".$status."'  and from_code='".$loc."'  and ".$daterange_open."");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['po_count']!=''){
$count_job=$row_count['po_count'];

}else{
$count_job=0;
}

return $count_job;
}

function inv_details($loc,$status,$daterange,$link1){
$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "sale_date >= '".$date_range[0]."' and sale_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";

}
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";
$res_eng_p = mysqli_query($link1,"select count(id) as bill_count from billing_master where status='".$status."'  and to_location ='".$loc."' and ".$daterange_open." ");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['bill_count']!=''){
$count_job=$row_count['bill_count'];

}else{
$count_job=0;
}

return $count_job;
}



function inv_details_out($loc,$status,$daterange,$type,$link1){
$date_range = explode(" - ",$daterange);
if($daterange != ""){
	$daterange_open= "sale_date >= '".$date_range[0]."' and sale_date <= '".$date_range[1]."'";

}else{
	$daterange_open = "1";

}
if($type=="OUT"){

$st="status in('3','4') and  from_location='".$loc."' and ".$daterange_open." ";
$locgrou="from_location";
}
else if($type=="ASC"){

$st="status in('3') and  from_location='".$loc."' and ".$daterange_open." ";
$locgrou="from_location";
}
else if($type=="WH"){

$st="status in('3') and  to_location='".$loc."' and ".$daterange_open." ";
$locgrou="to_location";
}
else{
$st="status in('3') and  from_location='".$loc."' and ".$daterange_open." ";
$locgrou="from_location";
}
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";
//echo "select sum(total_cost) as bill_amt from billing_master where ".$st." group by from_location ";
$res_eng_p = mysqli_query($link1,"select sum(total_cost) as bill_amt from billing_master where ".$st." group by ".$locgrou." ");

$row_count = mysqli_fetch_array($res_eng_p);
if($row_count['bill_amt']!=''){
$count_job=$row_count['bill_amt'];

}else{
$count_job=0;
}

return $count_job;
}

function stock_details($loc,$product,$link1){
//echo "select count(job_id) as job_count from jobsheet_data where status='".$status."'  and eng_id='".$eng_name."' ";
$res_eng_p = mysqli_query($link1,"SELECT a.*, b.part_name, b.product_id, b.brand_id, b.location_price  FROM client_inventory a, partcode_master b where a.location_code='".$loc."' and  product_id='".$product."' ");
$row_qty='';
$row_sum='';
$row_faulty ='';
$total_qty='';
if(mysqli_num_rows($res_eng_p)==0){
$row_qty=0;
$row_amt=0;
$row_faulty=0;
}else{
while($row_count = mysqli_fetch_array($res_eng_p))
{
$row_qty+=$row_count['okqty'];
$row_faulty+=$row_count['faulty'];
$total_qty+=$row_count['okqty']+$row_count['faulty'];
$row_amt+=((int)$row_count['okqty']*(int)$row_count['location_price']);


}
}
$row_sum=$row_amt;
//echo $row_qty."~".$row_sum."~".$row_faulty;

return $row_qty."~".$row_sum."~".$row_faulty;
}
////////////////////// ASC ledger details
$asc_ledger = mysqli_fetch_assoc(mysqli_query($link1,"SELECT claim_amt,security_amt FROM current_cr_status WHERE location_code='".$_SESSION['asc_code']."'"));
////// ASP spare amount calculation
$spare_amt = 0.00;
$asc_inv = mysqli_query($link1,"SELECT partcode,okqty,faulty,broken FROM client_inventory WHERE location_code='".$_SESSION['asc_code']."'");
while($row_inv = mysqli_fetch_assoc($asc_inv)){
	///// get spare price
	$spare_price = mysqli_fetch_assoc(mysqli_query($link1,"SELECT location_price FROM partcode_master WHERE partcode='".$row_inv["partcode"]."'"));
	///// calculate spare amount
	$spare_amt += ($row_inv["okqty"] + $row_inv["faulty"] + $row_inv["broken"]) * $spare_price["location_price"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>
<?=siteTitle?>
</title>

  <meta charset="utf-8">
  <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <script src="../js/bootstrap.min.js"></script>
 <script type="text/javascript" src="../js/moment.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">

<script language="javascript" type="text/javascript">

/////////// function to get location on the basis of state

/////////// function to get product on the basis of brand or product
function getModel(){
		var brandid=$('#brand').val();
		var productcatid=$('#product_cat').val();
		if(brandid!=""){
	  	$.ajax({
	    	type:'post',
			url:'../includes/getAzaxFields.php',
			data:{getproductdrop:brandid, prdcat:productcatid},
			success:function(data){
	    		$('#proddiv').html(data);
			}
	  	});
		}
}
$(document).ready(function(){
	$('input[name="daterange"]').daterangepicker({
		locale: {
			format: 'YYYY-MM-DD'
		}
	});
});
$(document).ready(function(){
Highcharts.chart('container', {
  chart: {
    styledMode: true
  },

  title: {
    text: 'Closed TAT'
  },

  xAxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
	plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.percentage:.0f}%'
                }
            }
        },
	 tooltip: {
            headerFormat: '<span style="font-size:11px"><strong>Closed Call TAT</strong></span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.percentage:.0f}%</b> of total count<br/>'
        },		
  series: [{
     type: 'pie',
   allowPointSelect: true,
    keys: ['name', 'y', 'selected', 'sliced'],
    data: [
      ['0 - 24 hrs', <?=$interval1?>, false],
      ['25 - 48 hrs', <?=$interval2?>, false],
      ['49 - 72 hrs', <?=$interval3?>, false],
	  ['73 - 96 hrs', <?=$interval4?>, false],
      ['Above 96 hrs', <?=$interval5?>, false]
    ],
    showInLegend: true
  }]
});});
//// Ageing TAT/////////////////////////////
$(document).ready(function(){
Highcharts.chart('container_pending', {
  chart: {
    styledMode: true
  },

  title: {
    text: 'Ageing'
  },

  xAxis: {
    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
	plotOptions: {
            series: {
                dataLabels: {
                    enabled: true,
                    format: '{point.percentage:.0f}%'
                }
            }
        },
	 tooltip: {
            headerFormat: '<span style="font-size:11px"><strong>Ageing</strong></span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.percentage:.0f}%</b> of total count<br/>'
        },		
  series: [{
    type: 'pie',
    allowPointSelect: true,
    keys: ['name', 'y', 'selected', 'sliced'],
    data: [
      ['0 - 24 hrs', <?=$p_interval1?>, false],
      ['25 - 48 hrs', <?=$p_interval2?>, false],
      ['49 - 72 hrs', <?=$p_interval3?>, false],
	  ['73 - 96 hrs', <?=$p_interval4?>, false],
      ['Above 96 hrs', <?=$p_interval5?>, false]
    ],
    showInLegend: true
  }]
});});

</script>
<script type="text/javascript" src="../js/daterangepicker.js"></script>
 <link rel="stylesheet" type="text/css" href="../css/daterangepicker.css"/>
 <script src="../high/highcharts_new.js"></script>
 <script src="../high/js/modules/exporting.js"></script>
 <link rel="stylesheet" href="../high/highcharts.css">
</head>
<body>
<div class="container-fluid">
  <div class="row content">
    <?php 
    include("../includes/leftnavemp2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-bar-chart"></i>Call Details</h2>
      <br/>
      <form class="form-horizontal" role="form" name="form1" action="" method="post">
	              <div class="form-group">
         <div class="col-md-6"><label class="col-md-5 control-label">Date Range</label>	  
			<div class="col-md-6 input-append date" align="left">
			 <div style="display:inline-block;float:left"><input type="text" name="daterange" id="date_rng" class="form-control" value="<?=$_REQUEST['daterange']?>" style="width:185px"/></div><div style="display:inline-block;float:right"><i class="fa fa-calendar fa-lg"></i></div>
            </div>
          </div>
		 
	    </div><!--close form group-->
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label">Brand</label>
            <div class="col-md-6" align="left">
              <select name="brand" id="brand" class="form-control custom-select" onChange="getModel();">
                <option value="">All</option>
                <?php 
				 $sql ="select brand_id,brand from brand_master where status='1' and brand_id in (".$arrbrand.") order by brand";
			  	 $qry = mysqli_query($link1,$sql) ;
			  	 while ($row=mysqli_fetch_array($qry)){?>
                <option value="<?php echo $row['brand_id'];?>"<?php if($_REQUEST['brand']==$row['brand_id']){echo "selected";}?>><?php echo $row['brand'];?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-5 control-label"></label>
            <div class="col-md-6" align="left"> </div>
          </div>
        </div>
        <!--close form group-->
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label">Product</label>
            <div class="col-md-6" align="left">
              <select name="product_cat" id="product_cat" class="form-control" onChange="getModel();">
                <option value="">All</option>
                <?php 
					$res_prod = mysqli_query($link1,"select product_id,product_name from product_master  where product_id in (".$arrproduct.") and status='1' order by product_name"); 
					while($row_prod = mysqli_fetch_assoc($res_prod)){ 
					?>
                <option value="<?=$row_prod['product_id']?>" <?php if($_REQUEST['product_cat'] == $row_prod['product_id']) { echo 'selected'; }?>>
                <?=$row_prod['product_name']?>
                </option>
                <?php }?>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-5 control-label">Model</label>
            <div class="col-md-6" align="left" id="proddiv">
              <select name="product" id="product" class="form-control">
                <option value="">All</option>
                <?php 
					$res_prod = mysqli_query($link1,"select model_id,model from model_master  where brand_id in (".$arrbrand.") and product_id in (".$arrproduct.") and status='1' order by model"); 
					while($row_prod = mysqli_fetch_assoc($res_prod)){ 
					?>
                <option value="<?=$row_prod['model_id']?>" <?php if($_REQUEST['product'] == $row_prod['model_id']) { echo 'selected'; }?>>
                <?=$row_prod['model']." ".$row_prod['model_id']?>
                </option>
                <?php }?>
              </select>
            </div>
          </div>
		  </div>

       
        <!--close form group-->
        <div class="form-group">
          <div class="col-md-6">
            <label class="col-md-5 control-label"></label>
            <div class="col-md-6" align="left"> </div>
          </div>
          <div class="col-md-6">
            <label class="col-md-5 control-label"></label>
            <div class="col-md-6" align="left">
              <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
              <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
              <input name="Submit" type="submit" class="btn<?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
        </div>
        <!--close form group-->
      </form>
      <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
        <tr>
          <td  id="container" width="50%" ></td>
          <td id="container_pending"  width="50%"></td>
        </tr>
      </table>
      <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
        <tr  class="<?=$tableheadcolor?>">
          <th width="100%"  colspan="7" style="text-align:center"><label class="control-label">Engineer Details</label></th>
        </tr>
        <tr >
          <th    style="text-align:center"><label class="control-label">Engineer Name</label></th>
          <th   style="text-align:center"><label class="control-label">Assigned Call</label></th>
          <th   style="text-align:center"><label class="control-label">PNA Call</label></th>
          <th   style="text-align:center"><label class="control-label">Demo Call</label></th>
          <th   style="text-align:center"><label class="control-label">Installation Call</label></th>
          <th   style="text-align:center"><label class="control-label">Repair Done</label></th>
          <th  style="text-align:center"><label class="control-label">Confimation Done</label></th>
        </tr>
        <?php //echo "select * from part_to_credit where status ='1' and from_location='".$_SESSION['asc_code']."'";


$sel_tras="select * from locationuser_master where  location_code='".$_SESSION['asc_code']."'";


$sel_res12=mysqli_query($link1,$sel_tras)or die("error1".mysqli_error($link1));


$j=1;
$assign='';
$pna='';
$demo='';
$install='';
$repair='';
$jobhand='';
while($location_detail = mysqli_fetch_array($sel_res12)){ ?>
        <tr >
          <td ><?=$location_detail['locusername']?></td>
          <td  style="text-align:center"><?php echo $job_assign=job_eng_details($location_detail['userloginid'],2,$_REQUEST['daterange'],$link1);?></td>
          <td  style="text-align:center"><?php echo $job_pna=job_eng_details($location_detail['userloginid'],3,$_REQUEST['daterange'],$link1);?></td>
          <td  style="text-align:center"><?php echo $demo_job=job_eng_details($location_detail['userloginid'],49,$_REQUEST['daterange'],$link1);?></td>
          <td  style="text-align:center"><?php echo $install_job=job_eng_details($location_detail['userloginid'],48,$_REQUEST['daterange'],$link1);?></td>
          <td  style="text-align:center"><?php echo $job_repair=job_eng_details($location_detail['userloginid'],6,$_REQUEST['daterange'],$link1);?></td>
          <td  style="text-align:center"><?php echo $job_hand=job_eng_details($location_detail['userloginid'],10,$_REQUEST['daterange'],$link1);?></td>
        </tr>
        <?php
$assign+= $job_assign;
$pna+=$job_pna;
$demo+=$demo_job;
$install+=$install_job;
$repair+=$job_repair;
$jobhand+=$job_hand;
  $j++; }?>
        <tr>
          <td ><strong>Total</strong></td>
          <td  style="text-align:center"><strong><?php echo $assign;?></strong></td>
          <td  style="text-align:center"><strong><?php echo $pna;?></strong></td>
          <td  style="text-align:center"><strong><?php echo $demo;?></strong></td>
          <td  style="text-align:center"><strong><?php echo $install;?></strong></td>
          <td  style="text-align:center"><strong><?php echo $repair;?></strong></td>
          <td  style="text-align:center"><strong><?php echo $jobhand;?></strong></td>
        </tr>
      </table>
      <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
        <tr  class="<?=$tableheadcolor?>">
          <th width="100%"  colspan="5" style="text-align:center"><label class="control-label">PO Details</label></th>
        </tr>
        <tr >
          <th    style="text-align:center"><label class="control-label">#</label></th>
          <th   style="text-align:center"><label class="control-label">Raised</label></th>
          <th   style="text-align:center"><label class="control-label">Process</label></th>
          <th   style="text-align:center"><label class="control-label">Dispatch</label></th>
          <th  style="text-align:center"><label class="control-label">Received</label></th>
        </tr>
        <tr >
          <td >PO</td>
          <td  style="text-align:center"><?php echo $pen_po=po_details($_SESSION['asc_code'],1,$_REQUEST['daterange'],$link1);?></td>
          <td  style="text-align:center"><?php echo $pro_po=job_eng_details($_SESSION['asc_code'],2,$_REQUEST['daterange'],$link1);?></td>
          <td  style="text-align:center">0</td>
          <td  style="text-align:center">0</td>
        </tr>
        <tr >
          <td >Invoice</td>
          <td  style="text-align:center">0</td>
          <td  style="text-align:center"><?php echo $inv_pro=inv_details($_SESSION['asc_code'],2,$_REQUEST['daterange'],$link1);?></td>
          <td  style="text-align:center"><?php echo $inv_dis=inv_details($_SESSION['asc_code'],3,$_REQUEST['daterange'],$link1);?></td>
          <td  style="text-align:center"><?php echo $inv_rec=inv_details($_SESSION['asc_code'],4,$_REQUEST['daterange'],$link1);?></td>
        </tr>
      </table>
      <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
        <tr  class="<?=$tableheadcolor?>">
          <th width="100%"  colspan="6" style="text-align:center"><label class="control-label">Ledger Details</label></th>
        </tr>
        <tr >
          <th   style="text-align:center" ><label class="control-label">Claim Amount</label></th>
		   <th   style="text-align:center"><label class="control-label">Oustanding</label></th>
          <th   style="text-align:center"><label class="control-label"> Spare Valution</label></th>
          <th   style="text-align:center" ><label class="control-label">Security Amount</label></th>
		  <th   style="text-align:center" ><label class="control-label">Intransit ASP to Company</label></th>
		    <th   style="text-align:center" ><label class="control-label">Company to ASP Intransit</label></th>
        </tr>
        <tr >
          <td  style="text-align:center"><?php if($asc_ledger["claim_amt"]){echo $asc_ledger["claim_amt"];}else{ echo "0.00";}?></td>
		 <td  style="text-align:center"><?php echo $inv_out=inv_details_out($_SESSION['asc_code'],3,$_REQUEST['daterange'],'OUT',$link1);?></td>
          <td  style="text-align:center"><?php if($spare_amt){echo $spare_amt;}else{ echo "0.00";}?></td>
          <td  style="text-align:center"><?php if($asc_ledger["security_amt"]){echo $asc_ledger["security_amt"];}else{ echo "0.00";}?></td>
		  <td  style="text-align:center"><?php echo $inv_inst_asc=inv_details_out($_SESSION['asc_code'],3,$_REQUEST['daterange'],'ASC',$link1);?></td>
          <td  style="text-align:center"><?php echo $inv_inst_wh=inv_details_out($_SESSION['asc_code'],3,$_REQUEST['daterange'],'WH',$link1);?></td>
        </tr>
      </table>
      <?php /*?>   
     <table width="100%"  class="table table-bordered"  cellpadding="4" cellspacing="0" border="1">
                <tr  class="<?=$tableheadcolor?>">


                  <th width="100%"  colspan="4" style="text-align:center"><label class="control-label">Stock Details</label></th>

</tr>
                
 <tr >


                  <th    style="text-align:center"><label class="control-label">Product Name</label></th>
				   <th   style="text-align:center"><label class="control-label">Fresh Qty</label></th>
			
					  <th   style="text-align:center"><label class="control-label">Faulty Qty</label></th>
					    <th   style="text-align:center"><label class="control-label">Value</label></th>
						  
</tr>
                 <?php //echo "select * from part_to_credit where status ='1' and from_location='".$_SESSION['asc_code']."'";


$prodt="select * from product_master where  1";


$produ_sl=mysqli_query($link1,$prodt)or die("error1".mysqli_error($link1));


$j=1;
 $row_okqty='';
 $row_faulty='';
 $row_sum='';
while($row_pro= mysqli_fetch_array($produ_sl)){
$row_part=stock_details($_SESSION['asc_code'],$row_pro['product_id'],$link1);  
  $row_array=explode("~",$row_part); ?>   

 <tr >
 <td ><?=$row_pro['product_name']?></td>
 <td  style="text-align:center"><?php  echo $row_array[0]; ?></td>

 <td  style="text-align:center"><?php echo $row_array[2];?></td>
  <td  style="text-align:center"><?php echo $row_array[1];?></td>
   

 </tr>  
 <?php 
 $row_okqty+=$row_array[0];
  $row_faulty+=$row_array[2];
  $row_sum+=$row_array[1];
 $j++; }?>   
 
 <tr >
 <td ><strong>Total</strong></td>
 <td  style="text-align:center"><strong><?php  echo $row_okqty; ?></strong></td>

 <td  style="text-align:center"><strong><?php echo  $row_faulty;?></strong></td>
  <td  style="text-align:center"><strong><?php echo  $row_sum;?></strong></td>
   

 </tr>  
   </table><?php */?>
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>
