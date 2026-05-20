<?php
require_once("../includes/config.php");
global $link1;
$access_product = getAccessProduct($_SESSION['asc_code'],$link1);
$access_brand = getAccessBrand($_SESSION['asc_code'],$link1);
$access_asp = getAccessASP($_SESSION['asc_code'],$link1);

$tY=date("Y");
$tM=date("m");
$td=date("d");

$val_y=substr($tY,2,2);
$job_dt=$val_y."".$tM."".$td;

if($_REQUEST['mobileno']){
    $srch_criteria = "where ( mobile = '".$_REQUEST['mobileno']."')";
}else if($_REQUEST['email_id']){
    $srch_criteria = "where email = '".$_REQUEST['email_id']."'";
}else if($_REQUEST['customer_id']){
    $srch_criteria = "where customer_id = '".$_REQUEST['customer_id']."'";
}else if($_REQUEST['imei_serial']){
    $sql_customer_id=mysqli_query($link1,"SELECT customer_id FROM jobsheet_data  where imei='".$_REQUEST['imei_serial']."' ");
    $job_cust = mysqli_fetch_assoc($sql_customer_id);
    $srch_criteria="where customer_id = '".$job_cust['customer_id']."'";
}else{
    $srch_criteria="";
}

$sql_query="select * from customer_master ".$srch_criteria." order by id desc";
$sql_cust	= mysqli_query($link1,$sql_query);
$row_customer=mysqli_fetch_array($sql_cust);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <script src="../js/jquery.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/ajax.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/bootstrap-select.min.css">
    <script src="../js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/common_js.js"></script>
    <link rel="stylesheet" href="../css/datepicker.css">
    <script src="../js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: -350px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 77, 79, 0.95);
            backdrop-filter: blur(8px);

            color: #fff;
            padding: 14px 18px;
            border-radius: 10px;

            box-shadow: 0 8px 25px rgba(0,0,0,0.2);

            font-size: 14px;
            font-weight: bold;
            min-width: 250px;

            transition: all 0.4s ease;
            opacity: 0;
        }

        .toast.show {
            right: 20px;
            opacity: 1;
        }

        .toast .icon {
            font-size: 18px;
        }

        .toast .message {
            flex: 1;
        }
        .toast::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            background: #fff;
            animation: progress 3s linear;
        }

        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
    <style>
        /* Modal background */
        .modal{
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Modal box */
        .modal-content{
            background: white;
            padding: 25px;
            width: 300px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }
        .modal-content-success{
            background: white;
            padding: 25px;
            width: 40%;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }

        /* Close button */
        .close-btn{
            margin-top: 15px;
            padding: 8px 16px;
            border: none;
            background: red;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }

        .close-btn:hover{
            background: darkred;
        }
    </style>
</head>
<body>

<?php
if(isset($_REQUEST['msg']) && isset($_REQUEST['type']) && $_REQUEST['type'] == "error"){
    ?>

    <div class="modal" id="modal">
        <div class="modal-content">
            <h2> ❌ Error</h2>
            <p style="text-transform: capitalize;font-size: large;font-family: "Helvetica Neue", Helvetica, Arial, sans-serif"><?= $_REQUEST['msg'] ?? 'Something is wrong' ?></p>
            <button class="close-btn" onclick="closeModal()">Close</button>
        </div>
    </div>

    <script>
        function closeModal(){
            document.getElementById("modal").style.display = "none";
        }
    </script>

    <?php
}
?>
<?php  if(isset($_REQUEST['msg']) && isset($_REQUEST['type']) && $_REQUEST['type'] == "success"){
    ?>

    <div class="modal" id="modal">
        <div class="modal-content-success">
            <h2 style="text-transform: uppercase;color: lawngreen" >✔ Success</h2>
            <p style="text-transform: uppercase;font-size: large;font-family: "Helvetica Neue", Helvetica, Arial, sans-serif"><?= $_REQUEST['msg'] ?? 'Something is wrong' ?></p>
            <button class="close-btn" style="background-color: #00CC00" onclick="closeModal()">Close</button>
        </div>
    </div>

    <script>
        function closeModal(){
            document.getElementById("modal").style.display = "none";
        }
    </script>

    <?php
}
?>

<div class="container-fluid">
    <div class="row content">
        <?php include("../includes/leftnavemp2.php"); ?>
        <div class="<?=$screenwidth?>">
            <h2 align="center"><i class="fa fa-id-badge"></i> Enter Complaint Details</h2>
            <form  name="frm1" id="frm1" class="form-horizontal" enctype="multipart/form-data" autocomplete="off" onsubmit="return really('save')" action="" method="post">

        <div class="panel-group">

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp;Customer Details</div>

              <div class="panel-body">

              	  <div class="form-group">
              	    <div class="col-md-6">
              	      <label class="col-md-6 custom_label">Customer Category <span class="red_small">*</span></label>
                      <div class="col-md-6">
                        <select name="customer_type" id="customer_type" class="form-control required" required>
                         
                          <?php



				$cus_query="SELECT * FROM customer_type where status = '1' order by customer_type";



				$check_cust=mysqli_query($link1,$cus_query);


if($row_customer['type']==""){?>
 <option value="">--Please Select--</option>
			<?php	while($br_cust = mysqli_fetch_array($check_cust)){



				?>
                          <option value="<?=$br_cust['customer_type']?>"<?php if($row_customer['type']==$br_cust['customer_type']){ echo "selected";}?>><?php echo $br_cust['customer_type']?></option>
                          <?php }} else{?>
						   <option value="<?=$row_customer['type']?>"><?php echo $row_customer['type']?></option><?php }?>
                        </select>
                      </div>
           	        </div>
              	    <div class="col-md-6"><label class="col-md-6 custom_label">Pin Code<span class="red_small">*</span></label>

                      <div class="col-md-6">
  <input name="pincode" type="text" class="digits form-control"  onKeyup="getmapinstate(this.value);eng_assign();" maxlength="6" id="pincode" value="<?=$row_customer['pincode']?>" >
  
  <span class="red_small">OR You can use state/city/area option to find pincode</span>
                      </div>

                    </div>

                </div>

                  <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Customer Name <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      	<input name="customer_name" id="customer_name" type="text" value="<?=ucwords($row_customer['customer_name']);?>" class="form-control required" style="text-transform: uppercase;" />
						<input name="custo_id" id="custo_id" type="hidden" value="<?=$row_customer['customer_id'];?>" class="form-control required"/>

                      </div>

                    </div>

                                      <div class="col-md-6"><label class="col-md-6 custom_label">State <span class="red_small">*</span></label>

                      <div class="col-md-6" id="loc_pincodestate">

                         <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();" required >

                          <option value=''>--Please Select--</option>

                          <?php 

						 $state_query="select stateid, state from state_master where countryid='1' order by state";

						 $state_res=mysqli_query($link1,$state_query);

						 while($row_res = mysqli_fetch_array($state_res)){?>

						   <option value="<?=$row_res['stateid']?>"<?php if($row_customer['stateid']==$row_res['stateid']){ echo "selected";}?>><?=$row_res['state']?></option>

						 <?php }  ?> 	

                        </select>               

                      </div>

                    </div>

                  </div>
				                    <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Landmark </label>

                      <div class="col-md-6">

                        	<input name="landmark" id="landmark" type="text" class="form-control " value="<?=ucwords($row_customer['landmark']);?>" style="text-transform: uppercase;" /> 

                      </div>

                    </div>
                    <div class="col-md-6"><label class="col-md-6 custom_label">City <span class="red_small">*</span></label>

                        <div class="col-md-6" id="citydiv">

                       <select name="locationcity" id="locationcity" class="form-control required" required >

                       <option value=''>--Please Select-</option>

                       <?php 

					  

						 $city_query="SELECT cityid, city FROM city_master where stateid='".$row_customer['stateid']."' and cityid='".$row_customer['cityid']."'";

						 $city_res=mysqli_query($link1,$city_query);

						 while($row_city = mysqli_fetch_array($city_res)){

						?>

						<option value="<?=$row_city['cityid']?>"<?php if($row_customer['cityid']==$row_city['cityid']){ echo "selected";}?>><?=$row_city['city']?></option>

						<?php }

					

						?>

                       </select>

                      </div>

                    </div>


                  </div>

                   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Contact No<span class="red_small">(For SMS Update)</span> <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <input name="phone1" type="text" class="digits required form-control" required id="phone1" maxlength="10" onKeyPress="return onlyNumbers(this.value);" onBlur="return phoneN();" value="<?php if($row_customer['mobile']!=''){ echo $row_customer['mobile'];}else{ echo $_REQUEST['mobileno'];}?>" <?php if($row_customer['mobile']!=''|| $_REQUEST['mobileno'] !="" ){?> readonly <?php }else{}?>>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Area <!---<span class="red_small">*</span>----></label>

                      <div class="col-md-6" id="Areadiv">

                     <select name="locationarea" id="locationarea" class="form-control" >

                       <option value=''>--Please Select-</option>
						<?php 
						$pin_area = "SELECT area,pincode FROM  pincode_master where cityid='".$row_customer['cityid']."' and pincode='".$row_customer['pincode']."'";
						$respin_area=mysqli_query($link1,$pin_area);
						while($rowpin_area = mysqli_fetch_array($respin_area)){
						?>
						<option value='<?php echo $rowpin_area['area']."~".$rowpin_area['pincode'];?>'<?php if($rowpin_area['area']."~".$rowpin_area['pincode']==$row_customer['custarea']){ echo "selected";}?>><?php echo $rowpin_area['area']?></option>
							<?php 
						}
						?>
					</select>
                  

                       </select>    

                      </div>

                    </div>

                  </div>

                
                  <div class="form-group">


					
					  <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <textarea name="address" id="address" required class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="resize:vertical;text-transform: uppercase;" ><?=ucwords($row_customer['address1']);?></textarea>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Email</label>

                      <div class="col-md-6">

                          <input name="email" type="email" class="email form-control" id="email" value="<?=$row_customer['email'];?>" style="text-transform: uppercase;">

                      </div>

                    </div>

                  </div>
				  
				  
                  <div class="form-group">
					  <div class="col-md-6"><label class="col-md-6 custom_label">Alternate Contact No/Dealar No </label>
                      <div class="col-md-6">
					  	<input name="phone2" type="text" class="digits form-control " id="phone2" maxlength="10" value="<?=$row_customer['alt_mobile'];?>" >
                      </div>
                    </div>
                   <div class="col-md-6"><label class="col-md-6 custom_label">Residence No</label>
                      <div class="col-md-6">
                        <input name="res_no" type="text" class="digits form-control" id="res_no" value="<?=$row_customer['phone']?>"  >
                      </div>
                    </div>
                  </div>
				  
 				<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">GST No</label>

                        <div class="col-md-6" id="citydiv">

                         <input name="gst_no" type="text" class=" form-control" id="gst_no" value="<?=$row_customer['gst_no']?>" >
                      </div>

                    </div>

                   <div class="col-md-6"><label class="col-md-6 custom_label">Registration Name</label>

                      <div class="col-md-6">

                        <input name="reg_name" type="text" class=" form-control" id="reg_name" value="<?=ucwords($row_customer['reg_name'])?>" style="text-transform: uppercase;">

                      </div>

                    </div>

                  </div>
       
 
 <!-- <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Birthday<span class="small">(For SMS Update)</span></label>

                      <div class="col-md-6">

                     <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="dob_date"  id="dob_date" style="width:150px;" value="<?php if( $row_customer['dob_date']!='0000-00-00'  ){ echo $row_customer['dob_date'];?>  <?php }else{ echo "";}?>"   ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Anniversary Date </label>

                      <div class="col-md-6">

                    <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="mrg_date"  id="mrg_date" style="width:150px;" value="<?php if($row_customer['mrg_date']!='0000-00-00'){ echo $row_customer['mrg_date'];?>  <?php }else{ echo "";}?>"   ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                      </div>

                    </div>

                  </div>-->
				  
            		</div>
			    </div>


        

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>

              <div class="panel-body">
				  
				  
				  <div class="form-group">
						<div class="col-md-6"><label class="col-md-6 custom_label">Sold/Unsold<span class="red_small">*</span></label>

                      <div class="col-md-6">

                 <select name="sold_unsold" id="sold_unsold" class="form-control required"  onchange="sold_unsold1(this.value);"  required>

                          <option value=''>--Please Select--</option>
						
						   <option value='Sold'>Sold</option>
                          <option value='Unsold'>Unsold</option>
					

                        </select>

                      </div>
                    </div>

                    <div class="col-md-6">

                      <div class="col-md-6">

                   

                      </div>

                    </div>

                  </div>
				  
				  
				  
                 <div class="form-group">

                    <div class="col-md-6">
                      <label class="col-md-6 custom_label">Product <span class="red_small">*</span></label>

                      
<div class="col-md-6" id="productdiv">

                         <select name="product_name" id="product_name" class="form-control required" onChange="resetProdModel();getprdwisebrand(this.value);" required><!--getmapproduct(this.value);-->

                        

                          <?php
						  if($product_det['product_id']==''){?>
						    <option value=''>--Select Product--</option>

							<?php $dept_query="SELECT * FROM product_master where status = '1'   and product_id in (".$access_product.") order by product_name";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>

						  <option value="<?=$br_dept['product_id']?>"<?php if($sel_product == $br_dept['product_id']){ echo "selected";}?>><?php echo $br_dept['product_name']?></option>

						<?php }} else {?>	
                              <option value='<?=$product_det['product_id']?>'><?=getAnyDetails($product_det['product_id'],"product_name","product_id","product_master",$link1);?></option>
							  <?php }?>
                        </select>

                      </div>

                    </div>

                    <div class="col-md-6" ><label class="col-md-4 custom_label"><?php echo SERIALNO ?> </label>

                      <div class="col-md-8" >
    <input name="imei_serial1" id="imei_serial1" type="text" value="<?=$_REQUEST['imei_serial']?>" class="form-control required alphanumeric" minlength='09' maxlength="27"  style="text-transform: uppercase;float: left;" <?php if($product_det['serial_no']!=''){?> readonly <?php }else{}?> required />

	<!--<input type="button" name="search_sr" id="search_sr" style="float: left;margin-top: 4px;border: 1px solid #225702; border-radius: 4px; background-color: #225702; color: #f8f3f6; font-weight: bold; margin-left:5px;" onClick="return getSerialdeatils();" value="Go!" />-->
						  
				<input type="button" name="search_sr" id="search_sr" style="float: left;margin-top: 4px;border: 1px solid #225702; border-radius: 4px; background-color: #225702; color: #f8f3f6; font-weight: bold; margin-left:5px;" onClick="getSerialdeatils();checkSerialdeatil();fetchSerialDOP($('#imei_serial1').val());" value="Go!" />
                      </div>

                    </div>

                  </div>

                  <script>
                      imei_serial1//
                  </script>
              	<div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Model <span class="red_small">*</span></label>




<div class="col-md-6" id="modeldiv">

                        <select name="modelid" id="modelid" class="form-control required selectpicker" data-live-search="true" required>

                          <?php
/*
						  	if($product_det['model_id']!=""){
							$model_det2 = explode("~",getAnyDetails($product_det['model_id'],"product_id,brand_id,model,make_doa,doa_days,out_warranty,wp,make_job,dwp","model_id","model_master",$link1));
echo $model_det2['7'];
						  		if($model_det2['7']!='N'){
						  ?>
							
                          <option value="<?=$product_det['model_id']."~".$model_det2['2']."~".$model_det2['6']."~".$model_det2['8'];?>"><?=$model_det2['2']?></option>

						  <?php
								} //// END IF COndition of Model Check
							}else if($_REQUEST['p_modelcode'] || $_REQUEST['modelid']){

						  ?>
                                        <option value=''>--Select Model--</option>
                          <option value="<?=$model_code."~".$model_name."~".$model_det2['8']."~".$model_det2['8'];?>"><?=$model_det[2]?></option>

                          <?php }else{?>

                          <option value=''>--Select Model--</option>

                          <?php } */?>

                        </select>

                      </div>
                      
                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Brand <span class="red_small">*</span></label>

                      <div class="col-md-6" id="selectedbrand">

                       <!--<select name="brand" id="brand" class="form-control required" onChange="getmaploc();resetProdModel();" required>-->
						    <select name="brand" id="brand" class="form-control required" onChange="resetProdModel();" required><!--getmapbrand(this.value);--->

                        <?php  if($product_det['brand_id']==''){?>
						    <option value=''>--Select Product--</option>
                          <?php

						  	

							$dept_query="SELECT * FROM brand_master where status = '1'  and brand_id in (".$access_brand.")   order by brand";

							$check_dept=mysqli_query($link1,$dept_query);

							while($br_dept = mysqli_fetch_array($check_dept)){

						  ?>

						  <option value="<?=$br_dept['brand_id']?>"<?php if($sel_brand == $br_dept['brand_id']){ echo "selected";}?>><?php echo $br_dept['brand']?></option>

						<?php }} else {?>	
                              <option value='<?=$product_det['brand_id']?>'><?=getAnyDetails($product_det['brand_id'],"brand","brand_id","brand_master",$link1);?></option>
							  <?php }?>

                        </select>

                      </div>

                    </div>

                  </div>

              	

                  <div class="form-group" id="dop_date1">

                    <div class="col-md-6" ><label class="col-md-6 custom_label">Bill Purchase Date <span class="red_small">*</span></label>

                      <div class="col-md-6">

                      <div style="display:inline-block;float:left;"><input type="text" class="form-control required" name="pop_date"  id="pop_date" style="width:150px;" value="<?php if($product_det['purchase_date']!='' && $product_det['purchase_date']!='0000-00-00'){ echo $product_det['purchase_date'];?>  <?php }else{ echo "";}?>"   onChange="getdate4();" required placeholder="0000-00-00"></div><div style="display:inline-block;float:left;"><?php if($product_det['purchase_date']=='') {?><i class="fa fa-calendar fa-lg"></i><?php }?></div>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Warranty End Date</label>

                      <div class="col-md-6">

                       <input name="warraty_date" id="warraty_date" type="text" value="<?=$product_det['warranty_end_date']?>"  class="form-control" readonly/>

                      </div>

                    </div>

                  </div>


                  <div class="form-group">
						<div class="col-md-6"><label class="col-md-6 custom_label">Date Of Installation</label>

                      <div class="col-md-6">

            
  <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="install_date"  id="install_date" style="width:150px;" value="<?=$product_det['installation_date'];?>"   readonly  ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>
                      </div>
                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Call Type <span class="red_small">*</span></label>

                      <div class="col-md-6">

                        <select name="call_for" id="call_for" class="form-control required"  onchange="reinsallationfun();"  required>

                          <option value=''>--Select Call Type--</option>
						
						   <option value='Repair'>Repair</option>
                          <option value='Installation'>Installation</option>
						<!--	<option value='PicknDrop'>Pick & Drop Service </option>
						    <option value='Reinstallation'>Re-installation</option>-->
						        <!--<option value='Demo'>Demo</option>-->
						 
						  <!-- <option value='Warehouse Stock Inspection (PDI)'>Warehouse Stock Inspection (PDI)</option
						  ><option value='Dealer Stock Set Repair'>Dealer Stock Set Repair</option>
						     <option value='Split AC I/W Maintenance Service'>Split AC I/W Maintenance Service</option>
							   <option value='Annual Maintenance Contract (AMC)'>Annual Maintenance Contract (AMC)</option>-->
					
						  <!--  <option value='Replacement Handling'>Replacement Handling</option>-->
							
							
						

                        </select>

                      </div>

                    </div>

                  </div>

                <!-- <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">AMC Number</label>

                      <div class="col-md-6">
						 <input name="amc_no" id="amc_no" type="text" value="<?=$amc_det['amcid']?>"  class="form-control"  <?php if($amc_det['amcid']!=''){?> readonly <?php }else{}?>/>
						  <input name="amc_day" id="amc_day" type="hidden" value="<?=$amc_det['amc_duration']?>"  class="form-control" />

                      </div>

                    </div>


                    <div class="col-md-6"><label class="col-md-6 custom_label">AMC Expiry Date </label>

                      <div class="col-md-6">

                      
					     <div style="display:inline-block;float:left;"><input type="text" class="form-control " name="amc_exp_date"  id="amc_exp_date" style="width:150px;" value="<?=$amc_det['amc_end_date'];?>"  <?php if($amc_det['amc_end_date']!=''){?> readonly <?php }else{}?> ></div><div style="display:inline-block;float:left;"><i class="fa fa-calendar fa-lg"></i></div>

                      </div>

                    </div>

                  </div> -->
				  
				                  <div class="form-group">

                    <div class="col-md-6" id='warr_sts'><label class="col-md-6 custom_label">Warranty status<span class="red_small">*</span></label>

                      <div class="col-md-6">
					  
						 <input name="warranty_status" id="warranty_status" type="text" value="<?=$ws?>" class="form-control required" required readonly/>
						 <input name="warranty_status1" id="warranty_status1" type="hidden" value="<?=$ws?>" class="form-control " readonly/>
                      </div>

                    </div>


                    <div class="col-md-6"><label class="col-md-6 custom_label">Dealer Name </label>

                      <div class="col-md-6">

                         	<input name="dealer_name" id="dealer_name" type="text" value="" style="text-transform: uppercase;" class="form-control"/>

                      </div>

                    </div>

                  </div> 
				   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Invoice No </label>

                      <div class="col-md-6">

                      <input name="invoice_no" id="invoice_no" type="text" value="" class="form-control" style="text-transform: uppercase;"/>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Call source <span class="red_small">*</span></label>

                      <div class="col-md-6">

                       <select name="call_type" id="call_type" class="form-control required" required>

                          <option value=''>--Select --</option>	
						    <option value='Customer Helpline'>Customer Helpline </option>
							   <!--<option value='Dealer'>Dealer </option>
						   <option value='Distributor'>Distributor </option>-->
						   <option value='Whats App'>What's App </option>
						   <option value='HO Escalation'>Email/Web</option>
						  <!-- <option value='HO Escalation'>HO Escalation</option>
						     <option value='Direct Walkin'>Direct Walkin </option>
						   <option value='HO Escalation'>HO Escalation</option>
                      <!--    <option value='Social Media'>Social Media</option>
						  <option value='Web'>Web</option>-->
						
						<!--  <option value='SMS Feedback'>SMS Feedback</option>-->
						 
						  <!-- <option value='Customer'>Customer </option>-->
						

                        </select>

                      </div>

                    </div>

                  </div> 
				  
				  	   <div class="form-group">

                    <div class="col-md-6"><label class="col-md-6 custom_label">Purchase From </label>

                      <div class="col-md-6">

                        <select name="entity_type" id="entity_type" class="form-control required" required>
                          <option value="Others">Others</option>
                          <?php



				$enty_query="SELECT * FROM entity_type where status_id = '1' order by name";



				$check_enty=mysqli_query($link1,$enty_query);



				while($br_entity = mysqli_fetch_array($check_enty)){



				?>
                          <option value="<?=$br_entity['id']?>"<?php if($_REQUEST['entity_type']==$br_entity['id']){ echo "selected";}?>><?php echo $br_entity['name']?></option>
                          <?php }?>
                        </select>

                      </div>

                    </div>

                    <div class="col-md-6"><label class="col-md-6 custom_label">Accessory Required</label>

                      <div class="col-md-6" id="accdiv">

                       <select name="acc_present[]" id="example-multiple-selected2" multiple="multiple" class="form-control">
						<?php $acc_part = mysqli_fetch_assoc(mysqli_query($link1,"select partcode,part_name from partcode_master where model_id='".$model_code."' and part_category='ACCESSORY' and status='1'"));

							while($br_acc = mysqli_fetch_array($acc_part)){

						  ?>
<option value="<?=$br_acc['part_name']?>"><?php echo $br_acc['part_name']?></option>

						<?php }?>
                        </select>
                      </div>

                    </div>

                  </div> 
				  
				  
				  <div class="form-group">
					  <div class="col-md-6"><label class="col-md-6 custom_label">Assign Location </label>
                    <!--  <div class="col-md-6" id="loc_pincode">-->
						<div class="col-md-6" id="loc_pincode">	
					  <select name="rep_location" id="rep_location" class="form-control required" required>
                     
						 <option value="ASP">TEST ASP</option>
                      </select>
                      </div>
                    </div>
                   <div class="col-md-6"><label class="col-md-6 custom_label">MFD</label>
                      <div class="col-md-6">
								<input name="mfd" id="mfd" type="text" value=""  class="form-control" readonly/>
						        <input name="mfd_ex" id="mfd_ex" type="hidden" value=""  class="form-control" readonly/>
						  		<input name="wp_days" id="wp_days" type="hidden" value=""  class="form-control" readonly/>

                      </div>
                    </div>
                  </div>
				  
				  

              </div>

            </div>
            

            
            
            
            
            
            

            <div class="panel panel-info">

              <div class="panel-heading"><i class="fa fa-pencil-square-o fa-lg"></i>&nbsp;&nbsp;Observation</div>

              <div class="panel-body">



                  <div class="form-group" id="vocdisplay" >

                    <div class="col-md-6"><label class="col-md-6 custom_label">VOC <span class="red_small">*</span></label>

                      <div class="col-md-6" id="vocdiv">
						
						<select  name='voc1' id='voc1' class='form-control required'  required>
						<option value=''>--Please Select--</option>     
						<?php
						$vocpro="SELECT * FROM voc_master where   status='1' group by voc_desc";
						$row_res=mysqli_query($link1,$vocpro);
						while($vocrow = mysqli_fetch_array($row_res)){
						?>    
						<option value="<?php echo $vocrow['voc_code']; ?>" <?php if($vocrow['voc_code'] == $job_det_t['cust_problem']){ echo "selected"; } ?> > <?php echo $vocrow['voc_desc']; ?> </option>
						<?php
						}
						?>   
						</select>
                      </div>

                    </div>

                    <div class="col-md-6">

                    	<div class="col-md-6" id="mutlivoc">

                      
							<?php $vo22 = explode(",", $job_det_t['cust_problem2']); ?>
							<select name="voc2[]" id="example-multiple-selected1" multiple="multiple" class="form-control">
							<?php
							$vocpro="SELECT * FROM voc_master where  status='1' group by voc_desc";
							$row_res=mysqli_query($link1,$vocpro);
							while($vocrow = mysqli_fetch_array($row_res)){
							?>
							<option value="<?php echo $vocrow['voc_code']; ?>" <?php for($i=0; $i<count($vo22); $i++){ if($vo22[$i] == $vocrow['voc_code']) { echo 'selected'; }}?>  ><?php echo $vocrow['voc_desc']; ?></option>
							<?php
							}
							?>
							</select>	
                        </div>

                      	<div class="col-md-6">

                            <input name="voc3" id="voc3" type="text" value="" class="form-control" placeholder="Enter Other VOC"/>

                      	</div>

                    </div>

                  </div>

				  <div class="form-group">
					  <div class="col-md-12">
						  <div class="col-md-3">
							  <label class="custom_label">Remark</label>
						  </div>
						  <div class="col-md-9">
							  <textarea name="remark" id="remark"  class="form-control" onkeypress = " return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"  onContextMenu="return false" style="text-transform: uppercase;resize:none;"><?=$ticket_det['remark']?></textarea>
						  </div>
					  </div>
				  </div>
				  
				  <div class="form-group">
					  <div class="col-md-12">
						  <div class="col-md-3">
							  <label class="custom_label">Image Attachement<br><span style="color:#ff5f5f;font-weight:normal;">( png, jpg, pdf | max: 3 MB )</span></label>
						  </div>
						  <div class="col-md-5" style="">

							  <input type="file" name="handset_img" id="handset_img" onChange="return validateImage('handset_img','0');" class="form-control "  accept=".png,.jpg,.jpeg" style="margin-bottom:10px;">
							  <span id="errmsg0" class="red_small"></span>
						  </div>
					  </div>
				  </div>
				  <div class="form-group">
					  <div class="col-md-12">
						  <div class="col-md-3">
							  <label class="custom_label">Video Attachement <br><span style="color:#ff5f5f;font-weight:normal;">( mp4 | max: 10MB )</span></label>
						  </div>
						  <div class="col-md-5" style="">
							  <input type="file" name="att_video" id="att_video" onChange="return validateVid('att_video','AV');" class="form-control" accept=".mp4" style="margin-bottom:10px;">
							  <span id="errmsgAV" class="red_small"></span>
						  </div>
					  </div>
				  </div>

                  <div class="form-group">

                    <div class="col-md-12" align="center">

                      <span id="errmsg" class="red_small"></span>

                      <input title="Back" type="button" class="btn<?=$btncolor?>" value="Back" onClick="window.location.href='complaint_create.php?<?=$pagenav?>'">&nbsp;
						<input name="wsd" id="wsd" value="<?=$_REQUEST['p_wsd'];?>" type="hidden"/>
                      <input name="ticketno" id="ticketno" value="<?=base64_encode($ticket_det['ticket_no']);?>" type="hidden"/>
                      <input name="day_diff" id="day_diff" value="<?=$days_diference;?>" type="hidden"/>
                      
                      <input name="symptom" id="symptom" value="<?=$count['symp_code']?>" type="hidden"/>

                      <input type="button" class="btn<?=$btncolor?>" name="savejob" id="savejob" value="Save" title="Save Job Details" <?php if($_POST['savejob']=='Save'){?>disabled<?php }?>>&nbsp;

                    </div>

                  </div> 

              </div>

            </div><!-- end panal-->

        </div><!-- end panal group-->

        </form>
        </div>
    </div><!--End row content-->
</div><!--End container fluid-->

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<div id="errorPopup" class="toast">
    <span class="icon">⚠️</span>
    <span class="message"></span>
</div>
</body>
</html>