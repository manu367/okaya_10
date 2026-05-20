<?php
require_once("../includes/config.php");

$dt = date("Y-m-d H:i:s");

$getid=base64_decode($_REQUEST['id']);
/*$array_escl = array();
$arr_selescl = $_REQUEST['esclate_to'];
for($i=0; $i<count($arr_selescl); $i++){
	$array_escl[$arr_selescl[$i]] = "Y";
}
$array_wh = array();
$arr_selwh = $_REQUEST['wh_to'];
for($i=0; $i<count($arr_selwh); $i++){
	$array_wh[$arr_selwh[$i]] = "Y";
}*/
/////get status//
$arrstatus = getFullStatus("master",$link1);
////// final submit form ////
@extract($_POST);
if($_POST)
{	
	$locationcode = base64_decode($locationcode);
	if($_POST['Submit1']=='Save')
	{
		// update all details of location //
		$sql = "UPDATE location_master set pwd='".$pwd."', erpid='".$erp_id."', othid='".$oth_id."',locationname='".ucwords($party_name)."',locationtype='".$party_type."', partner_type='".$propritortype."', contact_person='".ucwords($contact_person)."',landlineno='".$helpline_no."',emailid='".$email."',contactno1='".$phone1."',contactno2='".$phone2."',locationaddress='".ucwords($address)."',dispatchaddress='".ucwords($address)."',deliveryaddress='".ucwords($ship_address)."',districtid='".$locationdistrict."',cityid='".$locationcity."',stateid='".$locationstate."',countryid='".$country."',zipcode='".$pincode."',statusid='".$status."',loginstatus='".$status."',gstno='".$gst_no."',panno='".$pan_no."',cin='".$cin."',oth_taxr_no='".$othtaxr_no."',oth_tax_name='".$othtax_name."',updateby='".$_SESSION['userid']."',updatedate='".$datetime."' ,fix_claim='".$fix_claim."',fix_mnth='".$fix_mnth."',entity_type='".$entity_code."',zone='".$zone."', balance_limit = '".$balance_limit."' where locationid='".$getid."'";
		mysqli_query($link1,$sql)or die("ER3".mysqli_error($link1));
		
		######################
		### By Hemant
		######################
		$sql_csl = "INSERT INTO status_log SET userid='".$locationcode."', status='".$status."', create_dt='".$dt."', create_by='".$_SESSION['userid']."'";
		$res_csl = mysqli_query($link1, $sql_csl);
		######################
		
		if($status == 2){
			$loc_pin_ac = mysqli_query($link1,"update location_pincode_access set statusid='2' where location_code='".$locationcode."'");
		}

		//////////////////////////////////////////////////////////////
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$locationcode,"LOCATION","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated details of location ".$locationcode;
	}
	else if($_POST['Submit2']=='Save')
	{
		/////// map repair location for SFR case like send for repair to L3/L4(higher service)
		$res_upd = mysqli_query($link1,"update map_repair_location set status='' where location_code='".$locationcode."'");
		$post_repairdata = $_POST['esclate_to'];
		$count_rep = count($post_repairdata);
		$i=0;
		while($i < $count_rep){
			if($post_repairdata[$i]==''){
				$newstatus = "";
			}else{
				$newstatus = "Y";
			}
			// alrady exist
			if(mysqli_num_rows(mysqli_query($link1,"select id from map_repair_location where location_code='".$locationcode."' and repair_location='".$post_repairdata[$i]."'"))>0){
				$res_mapupd = mysqli_query($link1,"update map_repair_location set status='".$newstatus."' where location_code='".$locationcode."' and repair_location='".$post_repairdata[$i]."'");
			}else{
				$res_mapupd = mysqli_query($link1,"insert into map_repair_location set location_code='".$locationcode."', repair_location='".$post_repairdata[$i]."', status='".$newstatus."'");
			}
			$i++;
		}//// close while loop
		//////// map warehouse location for stock incoming and outgoing like PO in and faulty out to the mapped location
		$res_upd = mysqli_query($link1,"update map_wh_location set status='' where location_code='".$locationcode."'");
		$post_whdata = $_POST['wh_to'];
		$count_wh = count($post_whdata);
		$j=0;
		while($j < $count_wh){
			if($post_whdata[$j]==''){
				$newstatus = "";
			}else{
				$newstatus = "Y";
			}
			// alrady exist
			if(mysqli_num_rows(mysqli_query($link1,"select id from map_wh_location where location_code='".$locationcode."' and wh_location='".$post_whdata[$j]."'"))>0){
				$res_mapupd = mysqli_query($link1,"update map_wh_location set status='".$newstatus."' where location_code='".$locationcode."' and wh_location='".$post_whdata[$j]."'");
			}else{
				$res_mapupd = mysqli_query($link1,"insert into map_wh_location set location_code='".$locationcode."', wh_location='".$post_whdata[$j]."', status='".$newstatus."'");
			}
			$j++;
		}//// close while loop
		//////// map entity to location for AMC , installation and invoice etc.
		$res_upd = mysqli_query($link1,"update map_entity_location set status='' where location_code='".$locationcode."'");
		$post_entdata = $_POST['entity_to'];
		$count_ent = count($post_entdata);
		$e=0;
		while($e < $count_ent){
			if($post_entdata[$e]==''){
				$newstatus = "";
			}else{
				$newstatus = "Y";
			}
			// alrady exist
			if(mysqli_num_rows(mysqli_query($link1,"select id from map_entity_location where location_code='".$locationcode."' and entity_id='".$post_entdata[$e]."'"))>0){
				$res_mapupd = mysqli_query($link1,"update map_entity_location set status='".$newstatus."' where location_code='".$locationcode."' and entity_id='".$post_entdata[$e]."'");
			}else{
				$res_mapupd = mysqli_query($link1,"insert into map_entity_location set location_code='".$locationcode."', entity_id='".$post_entdata[$e]."', status='".$newstatus."'");
			}
			$e++;
		}//// close while loop
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$locationcode,"LOCATION(Repair/Warehouse)","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated mapping location ".$locationcode;
	}
	else if($_POST['Submit3']=='Save')
	{
		// update all details of location //
		$sql = "UPDATE location_master set ac_no='".$ac_no."', acholder_name='".$acholder_name."',ac_type='".$ac_type."',bank_name='".$bank_name."', ifsc_code='".$ifsc_code."', branch_name='".$branch_name."' where locationid='".$getid."'";
		mysqli_query($link1,$sql)or die("ER3".mysqli_error($link1));
		//////////////////////////////////////////////////////////////
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$locationcode,"LOCATION","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated details of location ".$locationcode;
	}else if($_POST['Submit7']=='Save'){
		$res_upd = mysqli_query($link1,"update access_brand set status='' where location_code='".$locationcode."'");
		$postmapdata=$_POST['mapbrand'];
		$count=count($postmapdata);
		$j=0;
		while($j < $count){
			if($postmapdata[$j]==''){
				$newstatus = "";
			}else{
				$newstatus = "Y";
			}
			// alrady exist
			if(mysqli_num_rows(mysqli_query($link1,"select id from access_brand where location_code='".$locationcode."' and brand_id='".$postmapdata[$j]."'"))>0){
				$res_mapupd = mysqli_query($link1,"update access_brand set status='".$newstatus."',area='".$zonebr."' where location_code='".$locationcode."' and brand_id='".$postmapdata[$j]."'");
			}else{
				$res_mapupd = mysqli_query($link1,"insert into access_brand set location_code='".$locationcode."', brand_id='".$postmapdata[$j]."', status='".$newstatus."',area='".$zonebr."'");
			}
			$j++;
		}//// close while loop
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$locationcode,"LOCATION(BRAND)","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated details of location ".$locationcode;
	}
	else if($_POST['Submit8']=='Save')
	{
		$res_upd = mysqli_query($link1,"update access_product set status='' where location_code='".$locationcode."'");
		$postmapdata=$_POST['mapproduct'];
		$count=count($postmapdata);
		$j=0;
		while($j < $count){
			if($postmapdata[$j]==''){
				$newstatus = "";
			}else{
				$newstatus = "Y";
			}
			// alrady exist
			if(mysqli_num_rows(mysqli_query($link1,"select id from access_product where location_code='".$locationcode."' and product_id='".$postmapdata[$j]."'"))>0){
				$res_mapupd = mysqli_query($link1,"update access_product set status='".$newstatus."' where location_code='".$locationcode."' and product_id='".$postmapdata[$j]."'");
			}else{
				$res_mapupd = mysqli_query($link1,"insert into access_product set location_code='".$locationcode."', product_id='".$postmapdata[$j]."', status='".$newstatus."'");
			}
			$j++;
		}//// close while loop
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$locationcode,"LOCATION(PRODUCT)","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated details of location ".$locationcode;
	}
	/////////////////////////////////////////////////access For Call center //////////////////////
	else if($_POST['Submit9']=='Save')
	{

		$res_upd = mysqli_query($link1,"update access_asp set status='' where cp_code='".$locationcode."'");
		$postmapdata=$_POST['mapasp'];
		$count=count($postmapdata);
		$j=0;
		while($j < $count){
			if($postmapdata[$j]==''){
				$newstatus = "";
			}else{
				$newstatus = "Y";
			}
			// alrady exist
			if(mysqli_num_rows(mysqli_query($link1,"select id from access_asp where cp_code='".$locationcode."' and location_code='".$postmapdata[$j]."'"))>0){
				$res_mapupd = mysqli_query($link1,"update access_asp set status='".$newstatus."' where cp_code='".$locationcode."' and location_code='".$postmapdata[$j]."'");
			}else{
				$res_mapupd = mysqli_query($link1,"insert into access_asp set cp_code='".$locationcode."', location_code='".$postmapdata[$j]."', status='".$newstatus."'");
			}
			$j++;
		}//// close while loop
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$locationcode,"CC(LOCATION)","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated details of location ".$locationcode;
	}
	/////////////////////////////////////////////////access For pincode to location //////////////////////
	else if($_POST['Submit10']=='Save')
	{
		if($_POST['search_pin']!=""){ $cityflag = " and pincode = '".$_POST['search_pin']."' "; }else{ $cityflag = " and cityid='".$pincity."' "; }
		//echo "update location_pincode_access set statusid='0' where location_code='".$locationcode."' ".$cityflag." "."<br><br>";
		$res_upd = mysqli_query($link1,"update location_pincode_access set statusid='0' where location_code='".$locationcode."' ".$cityflag." ");
		$postmapdata=$_POST['pincod'];
		$travel_type=$_POST['travel_type'];


		$count=count($postmapdata);
		echo $postmapdata[$j];
		$j=0;
		while($j < $count){
			if($postmapdata[$j]==''){
				$newstatus = "0";
				$area="";
				$travel="";

			}else{
				$newstatus = "1";
				$area="area".$postmapdata[$j];
				$travel="travel_type".$postmapdata[$j];
			}


			// alrady exist
			//echo "select id from location_pincode_access where location_code='".$locationcode."' and pincode='".$postmapdata[$j]."' ".$cityflag." "."<br><br>";
			if(mysqli_num_rows(mysqli_query($link1,"select id from location_pincode_access where location_code='".$locationcode."' and pincode='".$postmapdata[$j]."' ".$cityflag." "))>0){

				//echo "update location_pincode_access set statusid='".$newstatus."',area_type='".$_POST[$travel]."'  ,postoffice='".$_POST[$area]."' where location_code='".$locationcode."' and pincode='".$postmapdata[$j]."' ".$cityflag." "."<br><br>";

				$res_mapupd = mysqli_query($link1,"update location_pincode_access set statusid='".$newstatus."',area_type='".$_POST[$travel]."'  ,postoffice='".$_POST[$area]."' where location_code='".$locationcode."' and pincode='".$postmapdata[$j]."' ".$cityflag." ");
			}else{

				//echo "insert into location_pincode_access set location_code='".$locationcode."', pincode='".$postmapdata[$j]."', cityid='".$pincity."', statusid='".$newstatus."' ,postoffice='".$_POST[$area]."',area_type='".$_POST[$travel]."'"."<br><br>";
				if($_POST['search_pin']!=""){ $ctflg = getAnyDetails($_POST['search_pin'],"cityid","pincode","pincode_master",$link1); }else{ $ctflg = $pincity; }
				$res_mapupd = mysqli_query($link1,"insert into location_pincode_access set location_code='".$locationcode."', pincode='".$postmapdata[$j]."', cityid='".$ctflg."', statusid='".$newstatus."' ,postoffice='".$_POST[$area]."',area_type='".$_POST[$travel]."'");


			}
			$j++;
		}//// close while loop
		////// insert in activity table////
		dailyActivity($_SESSION['userid'],$locationcode,"PINCODE(MAP)","UPDATE",$ip,$link1,"");
		////// return message
		$msg="You have successfully updated details of location ".$locationcode;
	}
	else
	{
		////// return message
		$msg="Something went wrong. Please try again.";

	}	
	///// move to parent page
    header("Location:edit_location.php?id=".base64_encode($getid)."&msg=".$msg."".$pagenav);
	exit;
}
////// get details of selected location////
$res_locdet=mysqli_query($link1,"SELECT * FROM location_master where locationid='".$getid."'")or die(mysqli_error($link1));
$row_locdet=mysqli_fetch_array($res_locdet);
/////make mapped repair location array
$array_escl = array();
$res_repair = mysqli_query($link1,"select repair_location from map_repair_location where location_code='".$row_locdet['location_code']."' and status='Y'");
while($row_repair = mysqli_fetch_array($res_repair)){
	$array_escl[$row_repair['repair_location']] = "Y";
}
/////make mapped warehouse location array
$array_wh = array();
$res_wh = mysqli_query($link1,"select wh_location from map_wh_location where location_code='".$row_locdet['location_code']."' and status='Y'");
while($row_wh = mysqli_fetch_array($res_wh)){
	$array_wh[$row_wh['wh_location']] = "Y";
}
/////make mapped entity array
$array_entity = array();
$res_entity = mysqli_query($link1,"select entity_id from map_entity_location where location_code='".$row_locdet['location_code']."' and status='Y'");
while($row_entity = mysqli_fetch_array($res_entity)){
	$array_entity[$row_entity['entity_id']] = "Y";
}
?>

<!DOCTYPE html>
<html>
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
  <?=siteTitle?>
  </title>
  <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
  <script src="../js/jquery.js"></script>
  <link href="../css/font-awesome.min.css" rel="stylesheet">
  <link href="../css/abc.css" rel="stylesheet">
  <script src="../js/bootstrap.min.js"></script>
  <link href="../css/abc2.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/bootstrap.min.css">
  <script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
  <script src="../js/frmvalidate.js"></script>
  <script type="text/javascript" src="../js/jquery.validate.js"></script>
  <script type="text/javascript" src="../js/common_js.js"></script>
  <script language="javascript" type="text/javascript">
   /////////// function to get state on the basis of circle
  $(document).ready(function(){
	$('#country').change(function(){
	  var countryid=$('#country').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{cntryid:countryid},
		success:function(data){
	    $('#statediv').html(data);
	    }
	  });
    });
  });
 /////////// function to get city on the basis of state
 function get_citydiv(){
	  var name=$('#locationstate').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state:name},
		success:function(data){
	    $('#citydiv').html(data);
	    }
	  });
   
 }
 /////////// function to get district on the basis of state
 function get_distdiv(){
	  var name=$('#locationstate').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state2:name},
		success:function(data){
		$('#search_pin').val('');
	    $('#distctdiv').html(data);
	    }
	  });
   
 } 
 /////////// function to get city on the basis of state in pincode maping
 function get_pincitydiv(){
	  var name=$('#state_name').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{pinstatearea:name},
		success:function(data){
		$('#search_pin').val('');
	    $('#pincitydiv').html(data);
	    }
	  });
   
 }
 
 function get_pinareadiv(){
	  var cty=$('#pincity').val();
	  var sty=$('#state_name').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{pincityarea:sty,cty:cty},
		success:function(data){
	    $('#pinareadiv').html(data);
		getPincode();
	    }
	  });
   
 }
 
 function getPincodeByPinSerach(){
 	var searchpin=$('#search_pin').val();
	if(searchpin!=""){
		getPincode();
		$('#state_name').val('');
	    $('#pincity').val('');
	    $('#pinarea').val('');
	}else{
		$('#search_pin').val('');
	}
 }
 
 function getPincode(){
	  var stateid=$('#state_name').val();
	  var cityid=$('#pincity').val();
	  var areaname=$('#pinarea').val();
	  var searchpin=$('#search_pin').val();
	  $.ajax({
	    type:'post',
		url:'../includes/getAzaxFields.php',
		data:{state_pincode_area:stateid, city_pincode:cityid, pinloc:'<?=$row_locdet['location_code']?>', areaname:areaname, search_pin:searchpin},
		success:function(data){
		//alert(data);
	    $('#disp_pincode').html(data);
	    }
	  });
 }
 
$(document).ready(function() {
	$('#example-multiple-selected1').multiselect({
			includeSelectAllOption: true,
			enableFiltering: true,
			buttonWidth:"320"
            //enableFiltering: true
	});
	$('#example-multiple-selected2').multiselect({
			includeSelectAllOption: true,

			enableFiltering: true,
			buttonWidth:"320"
            //enableFiltering: true
	});
	$('#example-multiple-selected3').multiselect({
			includeSelectAllOption: true,
			enableFiltering: true,
			buttonWidth:"320"
            //enableFiltering: true
	});
}); 
  </script>
  <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
  <body>
  <div class="container-fluid">
    <div class="row content">
      <?php 
    include("../includes/leftnav2.php");
    ?>
      <div class="<?=$screenwidth?>">
        <h2 align="center"><i class="fa fa-id-badge"></i> View/Edit Location</h2>
        <h4 align="center">
          <?=$row_locdet['locationname']."  (".$row_locdet['location_code'].")";?>
          <?php /* if($_POST['Submit1']=='Save' || $_POST['Submit2']=='Save' || $_POST['Submit3']=='Save' || $_POST['Submit7']=='Save' || $_POST['Submit8']=='Save'){ ?>
          <br/>
          <span style="color:#FF0000"><?php echo $msg; ?></span>
          <?php } */ ?>
		  <?php if($_REQUEST['msg']){?><br>
		  <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
		  <?php }?>
        </h4>
        <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
          <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-id-card"></i> General Details</a></li>
            <li><a data-toggle="tab" href="#menu1"><i class="fa fa-sitemap"></i> Mapping</a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-university"></i> Bank Details</a></li>
			<?php if($row_locdet['locationtype']=='CC'){?>
            <li><a data-toggle="tab" href="#menu3"><i class="fa fa-dot-circle-o"></i> ASP Mapping</a></li>
			<?php }?>
            <li><a data-toggle="tab" href="#menu4"><i class="fa fa-map-marker"></i> Service Area</a></li>
          
            <li><a data-toggle="tab" href="#menu6"><i class="fa fa-tag"></i> Brands</a></li>
            <li><a data-toggle="tab" href="#menu7"><i class="fa fa-cubes"></i> Products</a></li>
			
			
			
			
          </ul>
          <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
              <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Country <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <select name="country" id="country" class="form-control required" required>
                        <option value="">--Please Select--</option>
                        <?php
                        $country_query="SELECT * FROM country_master where status = 'A' order by countryname";
                        $check_country=mysqli_query($link1,$country_query);
                        while($br_country = mysqli_fetch_array($check_country)){
                        ?>
                        <option value="<?=$br_country['countryid']?>"<?php if($row_locdet['countryid']==$br_country['countryid']){ echo "selected";}?>><?php echo $br_country['countryname']?></option>
                        <?php }?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Party Type <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <select name="party_type" id="party_type" class="form-control required" required>
                        <option value="">--Please Select--</option>
                        <?php
                        $lctype_query="SELECT * FROM  location_type_master order by displayname";
                        $check_lctype=mysqli_query($link1,$lctype_query);
                        while($br_lctype = mysqli_fetch_array($check_lctype)){
                        ?>
                        <option value="<?=$br_lctype['usedname']?>"<?php if($row_locdet['locationtype']==$br_lctype['usedname']){ echo "selected";}?>><?php echo $br_lctype['displayname']?></option>
                        <?php }?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">State <span class="red_small">*</span></label>
                    <div class="col-md-6" id="statediv">
                      <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();get_distdiv();" required>
                        <option value=''>--Please Select--</option>
                        <?php 
						 $state_query="select stateid, state from state_master where countryid='".$row_locdet['countryid']."' order by state";
						 $state_res=mysqli_query($link1,$state_query);
						 while($row_res = mysqli_fetch_array($state_res)){?>
                        <option value="<?=$row_res['stateid']?>"<?php if($row_locdet['stateid']==$row_res['stateid']){ echo "selected";}?>>
                        <?=$row_res['state']?>
                        </option>
                        <?php }?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Party Name <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <input name="party_name" type="text" class="form-control required" required id="party_name" style="background-color:#FFFFCC" value="<?=$row_locdet['locationname']?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">City <span class="red_small">*</span></label>
                    <div class="col-md-6" id="citydiv">
                      <select name="locationcity" id="locationcity" class="form-control required" required>
                        <option value=''>--Please Select-</option>
                        <?php 
						 $city_query="SELECT cityid, city FROM city_master where stateid='".$row_locdet['stateid']."' group by city order by city";
						 $city_res=mysqli_query($link1,$city_query);
						 while($row_city = mysqli_fetch_array($city_res)){?>
                        <option value="<?=$row_city['cityid']?>"<?php if($row_locdet['cityid']==$row_city['cityid']){ echo "selected";}?>>
                        <?=$row_city['city']?>
                        </option>
                        <?php }?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Contact Person <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <input name="contact_person" type="text" class="form-control required" required id="contact_person" value="<?=$row_locdet['contact_person']?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">District </label>
                    <div class="col-md-6" id="distctdiv">
                      <select name="locationdistrict" id="locationdistrict" class="form-control " >
                        <option value=''>--Please Select-</option>
                        <?php 
						 $city_query="SELECT cityid, city FROM city_master where stateid='".$row_locdet['stateid']."' and isdistrict='Y' group by city order by city";
						 $city_res=mysqli_query($link1,$city_query);
						 while($row_city = mysqli_fetch_array($city_res)){?>
                        <option value="<?=$row_city['cityid']?>"<?php if($row_locdet['districtid']==$row_city['cityid']){ echo "selected";}?>>
                        <?=$row_city['city']?>
                        </option>
                        <?php }?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Email <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <input name="email" type="email" class="email required form-control" id="email" required onBlur="return checkEmail(this.value,'email');" value="<?=$row_locdet['emailid']?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Pincode <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <input name="pincode" type="text" class="required form-control" id="pincode" required value="<?=$row_locdet['zipcode']?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Contact Number <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?=$row_locdet['contactno1']?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Address <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?php echo $row_locdet['locationaddress'];?></textarea>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Alternate Number</label>
                    <div class="col-md-6">
                      <input name="phone2" type="text" class="digits form-control"  id="phone2" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?=$row_locdet['contactno2']?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Firm Type <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <select name="propritortype" id="propritortype" class="form-control required"  required>
                        <option value="">Select Type--</option>
                        <option value="Area Franchisee"<?php if($row_locdet['partner_type']=="Area Franchisee"){ echo "selected";}?>>Area Franchisee</option>
                        <option value="Unit Franchisee"<?php if($row_locdet['partner_type']=="Unit Franchisee"){ echo "selected";}?>>Unit Franchisee</option>
                        <option value="owned"<?php if($row_locdet['partner_type']=="owned"){ echo "selected";}?>>Owned</option>
                        <option value="partner"<?php if($row_locdet['partner_type']=="partner"){ echo "selected";}?>>Partnership</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Helpline No.</label>
                    <div class="col-md-6">
                      <input name="helpline_no" type="text" class="form-control" id="helpline_no" value="<?=$row_locdet['landlineno']?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">PAN No. <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <input name="pan_no" type="text" class="form-control required" required id="pan_no" value="<?=$row_locdet['panno']?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">GST No. <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <input name="gst_no" type="text" class="form-control "  id="gst_no" value="<?=$row_locdet['gstno']?>">
                    </div>
                  </div>
                </div>
                
                
                
                 <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">CIN</label>
                    <div class="col-md-6">
                      <input name="cin" type="text" class="form-control "  id="cin" value="<?=$row_locdet['cin']?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label"></label>
                    <div class="col-md-6">
                     
                    </div>
                  </div>
                </div>
                
                
                
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Other Tax Reg. No.</label>
                    <div class="col-md-6">
                      <input name="othtaxr_no" type="text" class="form-control" id="othtaxr_no" value="<?=$row_locdet['oth_taxr_no']?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Other Tax Name</label>
                    <div class="col-md-6">
                      <input name="othtax_name" type="text" class="form-control "  id="othtax_name" value="<?=$row_locdet['oth_tax_name']?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">ERP/SAP Code <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <input name="erp_id" type="text" class="form-control required" required id="erp_id" value="<?=$row_locdet['erpid']?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Other Code</label>
                    <div class="col-md-6">
                      <input name="oth_id" type="text" class="form-control" id="oth_id" value="<?=$row_locdet['othid']?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Status</label>
                    <div class="col-md-6">
                      <select name="status" id="status" class="form-control">
                        <option value="1"<?php if($row_locdet['statusid']=="1"){ echo "selected";}?>>Active</option>
                        <option value="2"<?php if($row_locdet['statusid']=="2"){ echo "selected";}?>>Deactive</option>
						<option value="99"<?php if($row_locdet['statusid']=="99"){ echo "selected";}?>>On Hold</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="col-md-6 control-label">Password</label>
                    <div class="col-md-6">
                      <input name="pwd" type="text" class="form-control required" id="pwd" required value="<?=$row_locdet['pwd']?>">
                    </div>
                  </div>
                 
                </div>
                 <div class="form-group">

            <div class="col-md-6"><label class="col-md-6 control-label">Fixed Claim</label>

              <div class="col-md-6">
<input name="fix_claim" type="number" class="form-control" id="fix_claim" value="<?=$row_locdet['fix_claim']?>">
            
              </div>
              

            </div>

           	<div class="col-md-6"><label class="col-md-6 control-label">Month(Fixed Claim)</label>

              <div class="col-md-6">

            <input name="fix_mnth" type="number" class="form-control" id="fix_mnth" value="<?=$row_locdet['fix_mnth']?>">

              </div>

            </div>

          </div>
                <div class="form-group">
                   <div class="col-md-6">
                    <label class="col-md-6 control-label">Entity Type</label>
                    </label>
                    <div class="col-md-6">
                   
               <select name="entity_code" id="entity_code" class="form-control " >
                          <option value="">--Please Select--</option>
                          <?php



				$enty_query="SELECT * FROM entity_type where status_id = '1' order by name";



				$check_enty=mysqli_query($link1,$enty_query);



				while($br_entity = mysqli_fetch_array($check_enty)){



				?>
                          <option value="<?=$br_entity['id']?>"<?php if($row_locdet['entity_type']==$br_entity['id']){ echo "selected";}?>><?php echo $br_entity['name']?></option>
                          <?php }?>
                        </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                      <label class="col-md-6 control-label">Zone. <span class="red_small">*</span></label>
                    <div class="col-md-6">    <select name="zone" id="zone" class="form-control required" required >
                          <option value="">--Please Select--</option>
                          <?php



				$zn_query="SELECT * FROM zone_master where status = 'A' order by zonename";



				$zn_enty=mysqli_query($link1,$zn_query);



				while($zn_entity = mysqli_fetch_array($zn_enty)){



				?>
                          <option value="<?=$zn_entity['zonename']?>"<?php if($row_locdet['zone']==$zn_entity['zonename']){ echo "selected";}?>><?php echo $zn_entity['zonename']?></option>
                          <?php }?>
                        </select> </div>
                  </div>
                </div>
				
				<div class="form-group">
					<div class="col-md-6"><label class="col-md-6 control-label">Balance Limit</label>
					  <div class="col-md-6">
						<input name="balance_limit" id="balance_limit" type="text" class="number form-control" value="<?=$row_locdet['balance_limit']?>" >
					  </div>
					</div>
					<div class="col-md-6">
                    <label class="col-md-6 control-label">Shipping Address <span class="red_small">*</span></label>
                    <div class="col-md-6">
                      <textarea name="ship_address" id="ship_address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical"><?php echo $row_locdet['deliveryaddress'];?></textarea>
                    </div>
                  </div>
				</div>
				
                <div class="form-group">
                  <div class="col-md-12" align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="Submit1" id="save1" value="Save" title="" <?php if($_POST['Submit1']=='Save'){?>disabled<?php }?>>
                    &nbsp;
                    <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['locationid'])?>"/>
                    <input name="locationcode" id="locationcode" type="hidden" value="<?=base64_encode($row_locdet['location_code'])?>"/>
                    <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_master.php?<?=$pagenav?>'">
                  </div>
                </div>
              </form>
            </div>
            <div id="menu1" class="tab-pane fade"> <br/>
              <form  name="frm2" id="frm2" class="form-horizontal" action="" method="post">
                <div class="form-group">
                  <div class="col-md-10">
                    <label class="col-md-4 control-label">For Repair Esclation/SFR</label>
                    <div class="col-md-6">
                      <select name="esclate_to[]" id="example-multiple-selected1" multiple="multiple" class="form-control">
                        <?php
							$lctype_query="SELECT location_code,locationname FROM  location_master where locationtype in ('L3','L4') order by locationname";
							$check_lctype=mysqli_query($link1,$lctype_query);
							while($br_lctype = mysqli_fetch_array($check_lctype)){
							?>
                        <option value="<?=$br_lctype['location_code']?>" <?php if($array_escl[$br_lctype['location_code']]=="Y") { echo 'selected'; }?>>
                        <?=$br_lctype['locationname']?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-10">
                    <label class="col-md-4 control-label">For Part Requirement/WH</label>
                    <div class="col-md-6">
                      <select name="wh_to[]" id="example-multiple-selected2" multiple="multiple" class="form-control">
                        <?php
							$lctype_query="SELECT location_code,locationname,cityid FROM  location_master where locationtype in ('WH') order by locationname";
							$check_lctype=mysqli_query($link1,$lctype_query);
							while($br_lctype = mysqli_fetch_array($check_lctype)){
							?>
                        <option value="<?=$br_lctype['location_code']?>" <?php if($array_wh[$br_lctype['location_code']]=="Y") { echo 'selected'; }?>>
                        <?=$br_lctype['locationname']?>(<?=$br_lctype['location_code']?>)(<?php echo getAnyDetails($br_lctype["cityid"],"city","cityid","city_master",$link1);?>)
                        </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-10">
                    <label class="col-md-4 control-label">For Entity Mapping</label>
                    <div class="col-md-6">
                      <select name="entity_to[]" id="example-multiple-selected3" multiple="multiple" class="form-control">
                        <?php
							$enttype_query="SELECT id,name FROM entity_type where status_id ='1' order by name";
							$check_enttype=mysqli_query($link1,$enttype_query);
							while($br_enttype = mysqli_fetch_array($check_enttype)){
							?>
                        <option value="<?=$br_enttype['id']?>" <?php if($array_entity[$br_enttype['id']]=="Y") { echo 'selected'; }?>>
                        <?=$br_enttype['name']?>
                        </option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12" align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="Submit2" id="save2" value="Save" title="" <?php if($_POST['Submit2']=='Save'){?>disabled<?php }?>>
                    &nbsp;
                    <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['locationid'])?>"/>
                    <input name="locationcode" id="locationcode" type="hidden" value="<?=base64_encode($row_locdet['location_code'])?>"/>
                    <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_master.php?<?=$pagenav?>'">
                  </div>
                </div>
              </form>
            </div>
            <div id="menu2" class="tab-pane fade"> <br/>
              <form  name="frm3" id="frm3" class="form-horizontal" action="" method="post">
                <div class="form-group">
                  <div class="col-md-10">
                    <label class="col-md-4 control-label">A/C No.</label>
                    <div class="col-md-6">
                      <input name="ac_no" type="text" class="form-control" id="ac_no" maxlength="20" value="<?=$row_locdet['ac_no'];?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-10">
                    <label class="col-md-4 control-label">A/C Holder Name</label>
                    <div class="col-md-6">
                      <input name="acholder_name" type="text" class="form-control" id="acholder_name" value="<?=$row_locdet['acholder_name'];?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-10">
                    <label class="col-md-4 control-label">A/C Type</label>
                    <div class="col-md-6">
                      <select name="ac_type" type="text" class="form-control required" id="ac_type">
                        <option value="" selected> --Please Select--</option>
                        <option value='Current' <?php if($row_locdet['ac_type']=="Current"){echo "selected";}else{}?>> Current</option>
                        <option value='Saving'<?php if($row_locdet['ac_type']=="Saving"){echo "selected";}else{}?>>Saving</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-10">
                    <label class="col-md-4 control-label">Bank Name</label>
                    <div class="col-md-6">
                      <input name="bank_name" type="text" class="form-control" id="bank_name" value="<?=$row_locdet['bank_name'];?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-10">
                    <label class="col-md-4 control-label">IFSC Code</label>
                    <div class="col-md-6">
                      <input name="ifsc_code" type="text" class="form-control" id="ifsc_code" value="<?=$row_locdet['ifsc_code'];?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-10">
                    <label class="col-md-4 control-label">Branch Name</label>
                    <div class="col-md-6">
                      <input name="branch_name" type="text" class="form-control" id="branch_name" value="<?=$row_locdet['branch_name'];?>">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12" align="center">
                    <input type="submit" class="btn<?=$btncolor?>" name="Submit3" id="save3" value="Save" title="" <?php if($_POST['Submit3']=='Save'){?>disabled<?php }?>>
                    &nbsp;
                    <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['locationid'])?>"/>
                    <input name="locationcode" id="locationcode" type="hidden" value="<?=base64_encode($row_locdet['location_code'])?>"/>
                    <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_master.php?<?=$pagenav?>'">
                  </div>
                </div>
              </form>
            </div>
            <div id="menu3" class="tab-pane fade">
              <form  name="frm9" id="frm9" class="form-horizontal" action="" method="post">
                <div class="form-buttons" style="float:right">
                  <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm9.mapasp)" value="Check All" />
                  <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm9.mapasp)" value="Uncheck All" />
                </div>
                <table width="100%" id="aspmap" class="table table-bordered table-hover">
                  <tbody>
                    <?php
					
					$rs1=mysqli_query($link1,"select location_code,locationname  from location_master where statusid='1' and locationtype='ASP'  order by locationname");
					$num1=mysqli_num_rows($rs1);
					if($num1 > 0){
                   		$j=1;
                   		while($row1=mysqli_fetch_array($rs1)){
							if($j%4==1){
					?>
                    <tr>
                      <?php
                       		}
							///// check if any mapping entry with Y status is there
							$res_map = mysqli_query($link1,"select id from access_asp where cp_code='".$row_locdet['location_code']."' and location_code='".$row1['location_code']."' and status='Y'")or die(mysqli_error());
                    		$num_map = mysqli_fetch_assoc($res_map);
							?>
                      <td><input style="width:20px"  type="checkbox" id="mapasp" name="mapasp[]" value="<?=$row1['location_code']?>" <?php if($num_map > 0){ echo "checked";}?>/>
                        &nbsp;
                        <?=$row1['locationname']?></td>
                      <?php 
						  	if($j/4==0){
							?>
                    </tr>
                    <?php
						  }
						$j++;
						}
					}
					?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="4" align="center"><input type="submit" class="btn<?=$btncolor?>" name="Submit9" id="save9" value="Save" title="" <?php if($_POST['Submit9']=='Save'){?>disabled<?php }?>>
                        &nbsp;
                        <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['locationid'])?>"/>
                        <input name="locationcode" id="locationcode" type="hidden" value="<?=base64_encode($row_locdet['location_code'])?>"/>
                        <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_master.php?<?=$pagenav?>'"></td>
                    </tr>
                  </tfoot>
                </table>
              </form>
            </div>
            <div id="menu4" class="tab-pane fade">
              <form id="frm10" name="frm10" class="form-horizontal" action="" method="post">
              <div class="table-responsive">
           <?php
				//echo "select * from location_pincode_access where location_code='".$row_locdet['location_code']."' and statusid='1'";
				$res_pin = mysqli_query($link1,"select * from location_pincode_access where location_code='".$row_locdet['location_code']."' and statusid='1' group by pincode");
				if(mysqli_num_rows($res_pin)>0){ 
				?>
                <table class='table table-hover'>
                	<thead>
                    <tr>
                    	<th align='left'>Mapped Pincode</th>
                    </tr>
                    </thead>
                    <tbody>
    				<?php
					$i = 1;
                	while($row_pin = mysqli_fetch_array($res_pin)){
     				if($i%4==1){
        				echo "<tr>";
					}
					?>
  					<td width="25%"><?=$row_pin["pincode"]." (".getAnyDetails($row_pin["cityid"],"city","cityid","city_master",$link1).")--".$row_pin["area_type"]?></td>
					<?php 
					if($i/4==0){
						echo "</tr>";
					}
					$i++;
					}
					?>
					</tbody>
        		</table>
        		<?php }?>
                <table id="myTable2" class="table table-hover">
                    <tr>
                      <td width="15%" align="right"><label class="col-md-6 control-label">State</label></td>
                      <td width="20%" style="border:none">
                        <select name="state_name" id="state_name" class="form-control" onChange="get_pincitydiv();" style="width:200px;">
                          <option value="">--Select State--</option>
                          <?php 
							$rs2=mysqli_query($link1,"SELECT * FROM state_master ORDER BY state");
                			while($row=mysqli_fetch_array($rs2)){
                			?>
                          <option value="<?=$row['stateid']?>">
                          <?=$row['state']?>
                          </option>
                          <?php
							}
							?>
                        </select></td>
                        <td width="10%" align="right"><label class="col-md-6 control-label">City</label></td>
                        <td width="20%" style="border:none">
						<div id="pincitydiv">
                        <select name="pincity" id="pincity" class="form-control" onChange="get_pinareadiv();" style="width:200px;">
                          <option value="">--Select City--</option>
                        </select>
						</div>
						</td>
						<td width="15%" align="right"><label class="col-md-6 control-label">Area</label></td>
                        <td width="20%" style="border:none">
						<div id="pinareadiv">
                        <select name="pinarea" id="pinarea" class="form-control" style="width:200px;">
                          <option value="">--Select Area--</option>
                        </select>
						</div>
						</td>
                    </tr>
					<tr><td colspan="6" style="text-align:center;">OR</td></tr>
					<tr>
                    	<td colspan="3" style="text-align:right;"><label class="control-label">Pincode</label></td>
                      	<td colspan="3">
                        	<input type="text" class="form-control" name="search_pin" id="search_pin" onKeyUp="getPincodeByPinSerach();" style="width:200px;" />
						</td>
                    </tr>
                </table>
              <span id="disp_pincode"></span> 
              </div>
              <div class="form-buttons" align="center">
                <input type="submit" class="btn<?=$btncolor?>" name="Submit10" id="save10" value="Save" title="" <?php if($_POST['Submit10']=='Save'){?>disabled<?php }?>>
                        &nbsp;
                        <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['locationid'])?>"/>
                        <input name="locationcode" id="locationcode" type="hidden" value="<?=base64_encode($row_locdet['location_code'])?>"/>
                        <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_master.php?<?=$pagenav?>'">
              </div>
            </form>
            </div>
           
            <div id="menu6" class="tab-pane fade">
              <form  name="frm7" id="frm7" class="form-horizontal" action="" method="post">
                <div class="form-buttons" style="float:right">
                  <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm7.mapbrand)" value="Check All" />
                  <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm7.mapbrand)" value="Uncheck All" />
                </div>
                <table width="100%" id="brandmap" class="table table-bordered table-hover">
                  <tbody>
                    <?php
					$rs=mysqli_query($link1,"select brand_id,brand from brand_master where status='1' order by brand");
					$num=mysqli_num_rows($rs);
					if($num > 0){
                   		$j=1;
                   		while($row=mysqli_fetch_array($rs)){
							if($j%4==1){
					?>
                    <tr>
                      <?php
                       		}
							///// check if any mapping entry with Y status is there
							$res_map = mysqli_query($link1,"select id from access_brand where location_code='".$row_locdet['location_code']."' and brand_id='".$row['brand_id']."' and status='Y'")or die(mysqli_error());
                    		$num_map = mysqli_fetch_assoc($res_map);
							?>
                      <td><input style="width:20px"  type="checkbox" id="mapbrand" name="mapbrand[]" value="<?=$row['brand_id']?>" <?php if($num_map > 0){ echo "checked";}?>/>
                        &nbsp;
                        <?=$row['brand']?></td>
                      <?php 
						  	if($j/4==0){
							?>
                    </tr>
                    <?php
						  }
						$j++;
						}
					}
					?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="4" align="center">
  <input name="zonebr" type="hidden" class="form-control "  id="zonebr" value="<?=$row_locdet['zone']?>"> <input type="submit" class="btn<?=$btncolor?>" name="Submit7" id="save7" value="Save" title="" <?php if($_POST['Submit7']=='Save'){?>disabled<?php }?>>
                        &nbsp;
                        <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['locationid'])?>"/>
                        <input name="locationcode" id="locationcode" type="hidden" value="<?=base64_encode($row_locdet['location_code'])?>"/>
                        <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_master.php?<?=$pagenav?>'"></td>
                    </tr>
                  </tfoot>
                </table>
              </form>
            </div>
            <div id="menu7" class="tab-pane fade">
              <form  name="frm8" id="frm8" class="form-horizontal" action="" method="post">
                <div class="form-buttons" style="float:right">
                  <input name="CheckAll" type="button" class="btn btn-primary" onClick="checkAll(document.frm8.mapproduct)" value="Check All" />
                  <input name="UnCheckAll" type="button" class="btn btn-primary" onClick="uncheckAll(document.frm8.mapproduct)" value="Uncheck All" />
                </div>
                <table width="100%" id="productmap" class="table table-bordered table-hover">
                  <tbody>
                    <?php
					$rs=mysqli_query($link1,"select product_id,product_name from product_master where status='1' order by product_name");
					$num=mysqli_num_rows($rs);
					if($num > 0){
                   		$j=1;
                   		while($row=mysqli_fetch_array($rs)){
							if($j%4==1){
					?>
                    <tr>
                      <?php
                       		}
							///// check if any mapping entry with Y status is there
							$res_map = mysqli_query($link1,"select id from access_product where location_code='".$row_locdet['location_code']."' and product_id='".$row['product_id']."' and status='Y'")or die(mysqli_error());
                    		$num_map = mysqli_fetch_assoc($res_map);
							?>
                      <td><input style="width:20px"  type="checkbox" id="mapproduct" name="mapproduct[]" value="<?=$row['product_id']?>" <?php if($num_map > 0){ echo "checked";}?>/>
                        &nbsp;
                        <?=$row['product_name']?></td>
                      <?php 
						  	if($j/4==0){
							?>
                    </tr>
                    <?php
						  }
						$j++;
						}
					}
					?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="4" align="center"><input type="submit" class="btn<?=$btncolor?>" name="Submit8" id="save8" value="Save" title="" <?php if($_POST['Submit8']=='Save'){?>disabled<?php }?>>
                        &nbsp;
                        <input name="id" id="id" type="hidden" value="<?=base64_encode($row_locdet['locationid'])?>"/>
                        <input name="locationcode" id="locationcode" type="hidden" value="<?=base64_encode($row_locdet['location_code'])?>"/>
                        <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='location_master.php?<?=$pagenav?>'"></td>
                    </tr>
                  </tfoot>
                </table>
              </form>
            </div>
          </div>
        </div>
        <!--End form group--> 
      </div>
      <!--End col-sm-9--> 
    </div>
    <!--End row content--> 
  </div>
  <!--End container fluid-->
  <?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>