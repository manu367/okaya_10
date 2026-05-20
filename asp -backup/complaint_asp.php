<?php
require_once("../includes/config.php");
$op="edit";
$cusmterTYpe="ASP";
$customername="Manu Pathak";
$pincode="244412";
$state="UP";
$city="sambhal";
$area="local1";
$email="pathakmanu174@gmail.com";
$residence_no="120 No";
$registration_name="Manu Pathak";

$gst_no="GSTBKAB9y29384y";
$alternative_contact="+91131232331";
$address="sdnkjcs";
$contact_for="312112323123";
$Landmark="dskbckbbscj";
$formData = [
    "op" => $op,
    "customer_type" => $cusmterTYpe,
    "customer_name" => $customername,
    "pincode" => $pincode,
    "state" => $state,
    "city" => $city,
    "area" => $area,
    "email" => $email,
    "residence_no" => $residence_no,
    "registration_name" => $registration_name,
    "gst_no" => $gst_no,
    "alternative_contact" => $alternative_contact,
    "address" => $address,
    "contact_no" => $contact_for,
    "landmark" => $Landmark
];

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
    <style>
        .custom_label {

            text-align:left;

            vertical-align:middle

        }
        .hidden { display: none; }

        /* Loader */
        .app-loader{
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,.7);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .spinner{
            width: 48px;
            height: 48px;
            border: 5px solid #ccc;
            border-top-color: #225702;
            border-radius: 50%;
            animation: spin .8s linear infinite;
        }

        @keyframes spin{
            to { transform: rotate(360deg); }
        }

        .loader-text{
            margin-top: 12px;
            font-weight: 600;
        }

        /* Modal */
        .app-modal{
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 99998;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-box{
            width: 400px;
            background: #fff;
            border-radius: 10px;
            padding: 15px;
        }
        .modal-header{
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }
        .modal-close{
            cursor: pointer;
        }
        .modal-footer{
            text-align: right;
            margin-top: 15px;
        }

    </style>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <script type="text/javascript" src="../js/common_js.js"></script>
    <link rel="stylesheet" href="../css/datepicker.css">
    <script src="../js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
</head>
<?php
if($row_customer['pincode']!="" && $product_det['job_no']=="" ){
?>
<body onLoad="getmaploc(<?=$row_customer['pincode']?>)">
<?php } else {?>
    <body>
<?php } ?>
<!--main forma start here-->
<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnavemp2.php");
        ?>
        <div class="<?=$screenwidth?>">
            <h2 align="center"><i class="fa fa-id-badge"></i> Enter Complaint Details</h2>
            <?php if($model_det[5]=="Y"){ ?>
                <h4 align="center" style="color:#F00">You are making a Complaint for OUT warranty model .</h4>
            <?php } ?>
            <form  name="frm1" id="frm1" class="form-horizontal" enctype="multipart/form-data" autocomplete="off" onsubmit="return really('save')" action="" method="post">
                <div class="panel-group">
                    <div class="panel panel-info" style="text-align: center">
                        <div class="panel-heading"
                             style="display: flex;justify-content: space-between">
                            <div><i class="fa fa-id-card fa-lg"></i>&nbsp;&nbsp Customer Details</div>
                            <div class="form-check" id="check">
                                <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                                <label class="form-check-label" for="checkDefault">
                                    Edit the Form
                                </label>
                            </div>
                        </div>
                        <div class="panel-body">
<!--                            customer category and pin_code-->
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="col-md-6 custom_label">
                                        Customer Category <span class="red_small">*</span>
                                    </label>
                                    <div class="col-md-6">
                                        <select name="customer_type" id="customer_type" class="form-control required" required>
                                            <option value="">--Please Select--</option>
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
<!--                            cusomer name and state-->
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="col-md-6 custom_label">
                                        Customer Name <span class="red_small">*</span>
                                    </label>
                                    <div class="col-md-6">
                                        <input name="customer_name" id="customer_name" type="text" value="<?=ucwords($row_customer['customer_name']);?>" class="form-control required" style="text-transform: uppercase;" />
                                        <input name="custo_id" id="custo_id" type="hidden" value="<?=$row_customer['customer_id'];?>" class="form-control required"/>
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-6 custom_label">State <span class="red_small">*</span></label>
                                    <div class="col-md-6" id="loc_pincodestate">
                                        <select name="locationstate" id="locationstate" class="form-control required"  onchange="get_citydiv();" required >
                                            <option value=''>--Please Select--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- landmark and city-->
                            <div class="form-group">
                                <div class="col-md-6"><label class="col-md-6 custom_label">Landmark </label>
                                    <div class="col-md-6">
                                        <input name="landmark" id="landmark" type="text" class="form-control " value="<?=ucwords($row_customer['landmark']);?>" style="text-transform: uppercase;" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-md-6 custom_label">
                                        City <span class="red_small">*</span>
                                    </label>
                                    <div class="col-md-6" id="citydiv">
                                        <select name="locationcity" id="locationcity" class="form-control required" required >
                                            <option value=''>--Please Select-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                          <!-- contact us and area-->
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label
                                        class="col-md-6 custom_label">Contact No<span class="red_small">(For SMS Update)</span> <span class="red_small">*</span>
                                    </label>
                                    <div class="col-md-6">
                                        <input name="phone1"
                                               type="text"
                                               class="digits required form-control"
                                               required id="phone1"
                                               maxlength="10"
                                               onKeyPress="return onlyNumbers(this.value);"
                                               onBlur="return phoneN();" value="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-md-6 custom_label">Area</label>
                                    <div class="col-md-6" id="Areadiv">
                                        <select name="locationarea" id="locationarea" class="form-control" >
                                            <option value=''>--Please Select-</option>
                                        </select>
                                        </select>
                                    </div>
                                </div>
                            </div>
<!--                            address and email-->
                            <div class="form-group">
                                <div class="col-md-6"><label class="col-md-6 custom_label">Address <span class="red_small">*</span></label>
                                    <div class="col-md-6">
                                        <textarea name="address"
                                                  id="address"
                                                  required
                                                  class="form-control"
                                                  onkeypress = "return ( (event.keyCode ? event.keyCode : event.which ? event.which : event.charCode)!= 13);"
                                                  onContextMenu="return false" style="resize:vertical;text-transform: uppercase;"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-6 custom_label">Email</label>
                                    <div class="col-md-6">
                                        <input name="email"
                                               type="email"
                                               class="email form-control"
                                               id="email" value="dd"
                                               style="text-transform: uppercase;">
                                    </div>
                                </div>
                            </div>
<!--                            alternative contact and Residence no-->
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="col-md-6 custom_label">Alternate Contact No/Dealar No </label>
                                    <div class="col-md-6">
                                        <input name="phone2"
                                               type="text"
                                               class="digits form-control"
                                               id="phone2"
                                               maxlength="10"
                                               value="" >
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="col-md-6 custom_label">Residence No</label>
                                    <div class="col-md-6">
                                        <input name="res_no"
                                               type="text"
                                               class="digits form-control"
                                               id="res_no"
                                               value="<?=$row_customer['phone']?>"  >
                                    </div>
                                </div>
                            </div>
<!--                            gst no and registration name -->
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="col-md-6 custom_label">GST No</label>
                                    <div class="col-md-6" id="citydiv">
                                        <input name="gst_no"
                                               type="text"
                                               class=" form-control"
                                               id="gst_no"
                                               value="<?=$row_customer['gst_no']?>"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="col-md-6 custom_label">Registration Name</label>
                                    <div class="col-md-6">
                                        <input name="reg_name"
                                               type="text"
                                               class="form-control"
                                               id="reg_name"
                                               value=""
                                               style="text-transform: uppercase;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-success m-4" id="customer_save" style="display: none">Save</button>
                        <br/>
                        <br/>
                    </div>
                    <div class="panel panel-info">
                        <div class="panel-heading"><i class="fa fa-desktop fa-lg"></i>&nbsp;&nbsp;Product Details</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="col-md-6 custom_label">
                                        Sold/Unsold<span class="red_small">*</span>
                                    </label>
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
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" ><label class="col-md-4 custom_label"><?php echo SERIALNO ?> </label>
                                    <div class="col-md-8" >
                                        <input name="imei_serial1" id="imei_serial1" type="text" value="<?=$_REQUEST['imei_serial']?>" class="form-control required alphanumeric" minlength='09' maxlength="27"  style="text-transform: uppercase;float: left;" <?php if($product_det['serial_no']!=''){?> readonly <?php }else{}?> required />
                                        <input type="button"
                                               name="search_sr"
                                               id="search_sr"
                                               style="float: left;margin-top: 4px;border: 1px solid #225702; border-radius: 4px; background-color: #225702; color: #f8f3f6; font-weight: bold; margin-left:5px;"
                                               onClick="getSerialdeatils();checkSerialdeatil();fetchSerialDOP($('#imei_serial1').val());" value="Go!" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="col-md-6 custom_label">Model <span class="red_small">*</span></label>
                                    <div class="col-md-6" id="modeldiv">
                                        <select name="modelid" id="modelid" class="form-control required selectpicker" data-live-search="true" required>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-6 custom_label">Brand <span class="red_small">*</span></label>
                                    <div class="col-md-6" id="selectedbrand">
                                        <select name="brand" id="brand" class="form-control required" onChange="resetProdModel();" required><!--getmapbrand(this.value);--->
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
                                        <select name="voc2[]" id="example-multiple-selected1"
                                                multiple="multiple" class="form-control scroll-select">
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

                                    <input type="submit" class="btn<?=$btncolor?>" name="savejob" id="savejob" value="Save" title="Save Job Details" <?php if($_POST['savejob']=='Save'){?>disabled<?php }?>>&nbsp;

                                </div>

                            </div>

                        </div>

                    </div><!-- end panal-->
                </div><!-- end panal group-->
            </form>
        </div>
    </div>
</div>

<!--mainloader-->
<div id="app-loader" class="app-loader hidden">
    <div class="spinner"></div>
    <div class="loader-text"></div>
</div>
<div id="app-modal" class="app-modal hidden">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title"></span>
            <span class="modal-close">&times;</span>
        </div>
        <div class="modal-body"></div>
        <div class="modal-footer">
            <button class="btn-ok">OK</button>
        </div>
    </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script src="../js/complaint_asp.js"></script>
    <script>
        window.APP_FORM_DATA = <?= json_encode($formData, JSON_UNESCAPED_UNICODE); ?>;
        const loader=new LoaderAndModel();
        const button=document.getElementById("customer_save");
        const checkbox=document.getElementById("checkDefault");
        const customer=new CustomerDtails(APP_FORM_DATA);
        document.addEventListener("DOMContentLoaded", () => {
            loader.showLoader("Please wait...")
            if(APP_FORM_DATA.op==='edit'){
                customer.loadData();
                button.style.display = "none"; // save hidden
                checkbox.checked = false;
            }
            loader.hideLoader();
        });
        // if op = edit
        checkbox.addEventListener("change", function (event) {
            if (!this.checked) {
                if (observations()) {
                    loader.showModal(
                        "warning",
                        "Unsaved Changes",
                        "You have unsaved changes. Please save first."
                    );
                    this.checked = true;
                    return;
                }
                customer.disableAll();
                button.style.display = "none";
            }
            else {
                customer.enableAll();
                button.style.display = "inline-block";
            }
        });

    </script>
    </body>
</html>