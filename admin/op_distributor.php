<?php
require_once("../includes/config.php");
//@extract($_POST);
$edit=false;
$sel=[];
/* ================= LOAD EDIT ================= */
if($_REQUEST['op']=="Edit"){
    $edit=true;
    $id=$_REQUEST['id'];
    $q=mysqli_query($link1,"SELECT * FROM distributor_master WHERE distributorid='$id'");
    $sel=mysqli_fetch_assoc($q);
}

/* ================= ADD ================= */
if(isset($_POST['add'])){

    mysqli_autocommit($link1,false);

    // SAFE FETCH
    $name  = mysqli_real_escape_string($link1,$_POST['name'] ?? '');
    $email = mysqli_real_escape_string($link1,$_POST['email'] ?? '');
    $phone = mysqli_real_escape_string($link1,$_POST['phone'] ?? '');
    $bill_address = mysqli_real_escape_string($link1,$_POST['bill_address'] ?? '');
    $ship_address = mysqli_real_escape_string($link1,$_POST['ship_address'] ?? '');
    $city   = mysqli_real_escape_string($link1,$_POST['city'] ?? '');
    $state  = mysqli_real_escape_string($link1,$_POST['state'] ?? '');
    $pincode = mysqli_real_escape_string($link1,$_POST['pincode'] ?? '');
    $gst_no  = mysqli_real_escape_string($link1,$_POST['gst_no'] ?? '');
    $distributorcode = mysqli_real_escape_string($link1,$_POST['distributorcode'] ?? '');
    $utype = mysqli_real_escape_string($link1,$_POST['utype'] ?? '');

    $sql = "INSERT INTO distributor_master SET
        distributorname = '$name',
        distributorcode = '$distributorcode',
        email           = '$email',
        address1        = '$bill_address',
        address2        = '$ship_address',
        cityid          = '$city',
        stateid         = '$state',
        countryid       = '1',
        pincode         = '$pincode',
        phone           = '$phone',
        mobile          = '$phone',
        gst_no          = '$gst_no',
        type            = '$utype',
        status          = 'active',
        updateby        = '".$_SESSION['userid']."'";

    if(mysqli_query($link1,$sql)){
        mysqli_commit($link1);
        header("location:distributor_master.php?msg=Distributor Added&chkflag=success");
        exit;
    }else{
        mysqli_rollback($link1);
        die("ADD FAILED : ".mysqli_error($link1));
    }
}

/* ================= UPDATE ================= */
if(isset($_POST['upd'])){

    mysqli_autocommit($link1,false);

    $id = $_POST['refid'];

    // SAFE FETCH
    $name  = mysqli_real_escape_string($link1,$_POST['name'] ?? '');
    $email = mysqli_real_escape_string($link1,$_POST['email'] ?? '');
    $phone = mysqli_real_escape_string($link1,$_POST['phone'] ?? '');
    $bill_address = mysqli_real_escape_string($link1,$_POST['bill_address'] ?? '');
    $ship_address = mysqli_real_escape_string($link1,$_POST['ship_address'] ?? '');
    $city   = mysqli_real_escape_string($link1,$_POST['city'] ?? '');
    $state  = mysqli_real_escape_string($link1,$_POST['state'] ?? '');
    $pincode = mysqli_real_escape_string($link1,$_POST['pincode'] ?? '');
    $gst_no  = mysqli_real_escape_string($link1,$_POST['gst_no'] ?? '');
    $distributorcode = mysqli_real_escape_string($link1,$_POST['distributorcode'] ?? '');
    $utype = mysqli_real_escape_string($link1,$_POST['utype'] ?? '');

    $sql = "UPDATE distributor_master SET
        distributorname = '$name',
        distributorcode = '$distributorcode',
        email           = '$email',
        address1        = '$bill_address',
        address2        = '$ship_address',
        cityid          = '$city',
        stateid         = '$state',
        pincode         = '$pincode',
        phone           = '$phone',
        mobile          = '$phone',
        gst_no          = '$gst_no',
        type            = '$utype',
        status          = 'active',
        updateby        = '".$_SESSION['userid']."'
        WHERE distributorid = '$id'";

    if(mysqli_query($link1,$sql)){
        mysqli_commit($link1);
        header("location:distributor_master.php?msg=Distributor Updated&chkflag=success");
        exit;
    }else{
        mysqli_rollback($link1);
        die("UPDATE FAILED : ".mysqli_error($link1));
    }
}
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
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <script>
        $(document).ready(function(){
            //$("#form1").validate();
        });
    </script>
    <script src="../js/frmvalidate.js"></script>
    <script src="../js/jquery.validate.js"></script>
</head>

<body>
<div class="container-fluid">
    <div class="row content">
        <?php include("../includes/leftnav2.php"); ?>

        <div class="<?=$screenwidth?>">
            <h2 align="center"><i class="fa fa-shopping-basket"></i> <?=$_REQUEST['op']?> Distributer</h2><br><br>

            <div class="form-group" id="page-wrap" style="margin-left:10px;">
                <form id="form1" method="post" class="form-horizontal">
<!--                    <input type="hidden" name="refid" value="--><?php //= $_REQUEST['id'] ?? '' ?><!--">-->
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Distributor/Dealer <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <input placeholder="Name" name="name" class="form-control" value="<?=$sel['distributorname']?>" required>
                            </div></div>

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Contact No <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <input placeholder="+91XXXXXXXXXX" name="phone" class="form-control" maxlength="10" value="<?=$sel['phone']?>" required>
                            </div></div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="col-md-5 control-label">State <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <select id="state" name="state" class="form-control"></select>
                            </div></div>

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">City <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <select id="city" name="city" class="form-control"></select>
                            </div></div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Pin Code <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <input id="pincode" name="pincode" maxlength="6" class="form-control" value="<?=$sel['pincode']?>">
                            </div></div>

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Email </label>
                            <div class="col-md-5">
                                <input name="email" class="form-control" value="<?=$sel['email']?>">
                            </div></div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Billing Address <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <textarea name="bill_address" class="form-control"><?=$sel['address1']?></textarea>
                            </div></div>

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Shipping Address</label>
                            <div class="col-md-5">
                                <textarea name="ship_address" class="form-control"><?=$sel['address2']?></textarea>
                            </div></div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="col-md-5 control-label">GST</label>
                            <div class="col-md-5">
                                <input name="gst_no" class="form-control" value="<?=$sel['gst_no']?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-md-5 control-label">SAP Code <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <input name="distributorcode" class="form-control" value="<?=$sel['distributorcode']?>">
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Dealer Type <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <select name="utype" class="form-control">
                                    <option value="">Select Type</option>
                                    <option value="dealer" <?=($sel['type']=="dealer"?"selected":"")?>>Dealer</option>
                                    <option value="distributer" <?=($sel['type']=="distributer"?"selected":"")?>>Distributor</option>
                                </select>
                            </div></div>
                    </div>

                    <div class="form-group text-center">
                        <?php if($_REQUEST['op']=="Add"){ ?>
                            <input type="submit" name="add" value="ADD" class="btn<?=$btncolor?>">
                        <?php } else { ?>
                            <input type="submit" name="upd" value="Update" class="btn<?=$btncolor?>">
                        <?php } ?>
                        <input type="hidden" name="refid" value="<?=$sel['distributorid']?>">
                        <button type="button" onclick="window.location.href='distributor_master.php'" class="btn btn-danger">Back</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- ERROR MODAL -->
<div id="errModal" class="modal fade">
    <div class="modal-dialog"><div class="modal-content p-4 text-center">
            <h4 class="text-danger">Form Error</h4>
            <p id="errMsg"></p>
            <button class="btn btn-danger" data-dismiss="modal">OK</button>
        </div></div></div>

<script>
    let editState="<?= $sel['stateid'] ?>";
    let editCity="<?= $sel['cityid'] ?>";

    // LOAD STATES
    fetch("../pagination/state-grid.php").then(r=>r.json()).then(d=>{
        let s=document.getElementById("state");
        s.innerHTML='<option value="">Select State</option>';
        d.forEach(x=>s.innerHTML+=`<option value="${x.id}">${x.name}</option>`);
        if(editState){ s.value=editState; s.dispatchEvent(new Event('change')); }
    });

    // LOAD CITY
    document.getElementById("state").addEventListener("change",function(){
        fetch("../pagination/city-grid.php?state="+this.value).then(r=>r.json()).then(d=>{
            let c=document.getElementById("city");
            c.innerHTML='<option value="">Select City</option>';
            d.forEach(x=>c.innerHTML+=`<option value="${x.id}">${x.name}</option>`);
            if(editCity) c.value=editCity;
        });
    });

    // VALIDATION
    document.getElementById("form1").addEventListener("submit",function(e){
        let name=document.querySelector("[name=name]").value.trim();
        let phone=document.querySelector("[name=phone]").value.trim();
        let email=document.querySelector("[name=email]").value.trim();
        let state=document.getElementById("state").value;
        let city=document.getElementById("city").value;
        let pin=document.getElementById("pincode").value;
        let type=document.querySelector("[name=utype]").value;

        if(!name) return error("Name required"),e.preventDefault();
        if(!/^\d{10}$/.test(phone)) return error("10 digit phone required"),e.preventDefault();
        if(!email.includes("@")) return error("Valid email required"),e.preventDefault();
        if(!state) return error("Select state"),e.preventDefault();
        if(!city) return error("Select city"),e.preventDefault();
        if(!/^\d{6}$/.test(pin)) return error("6 digit pincode required"),e.preventDefault();
        if(!type) return error("Select dealer type"),e.preventDefault();
    });

    function error(msg){
        document.getElementById("errMsg").innerText=msg;
        $('#errModal').modal('show');
    }
</script>
</body>
<script>
    console.log("hello bro how are you");
</script>
</html>
