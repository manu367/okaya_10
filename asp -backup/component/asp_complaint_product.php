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