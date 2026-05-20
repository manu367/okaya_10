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
                        <?php	while($br_cust = mysqli_fetch_array($check_cust)){?>
                            <option value="<?=$br_cust['customer_type']?>"<?php
                            if($row_customer['type']==$br_cust['customer_type']){
                                echo "selected";
                            }?>><?php echo $br_cust['customer_type']?>
                            </option>
                        <?php } }
                    else{?>
                        <option value="<?=$row_customer['type']?>">
                            <?php echo $row_customer['type']?></option>

                    <?php }?>
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