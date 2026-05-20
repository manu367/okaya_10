 <?php
 require_once("../includes/config.php");
 if (empty($_SESSION['csrf_token'])) {
     $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
 }

 $edit_data = [];
 $is_edit = false;
 $op="";
 $selected_bsi = [];
 $selected_rm  = [];

 /**
  *  When User Open this Page on Edit mode with based on Userloginid
  */
if (isset($_GET['op']) && $_GET['op'] == 'edit' && isset($_GET['id'])) {
    $op=$_GET['op'];
    $is_edit = true;
    $loginid = $_GET['id'];

    $stmt = $link1->prepare("SELECT * FROM locationuser_master WHERE userloginid=?");
    $stmt->bind_param("s", $loginid);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
}

/**
 * this function is Handle POST Request
 * POST = ADD , UPDATE Handling
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get All post Data
    $user_id   = $_POST['user_id'];
    $username  = $_POST['username'];
    $password  = $_POST['password'];
    $contact   = $_POST['contact_no'];
    $email     = $_POST['email'];
    $address   = $_POST['address'];
    $pincode   = $_POST['pincode'];
    $state     = $_POST['state'];
    $city      = $_POST['city'];
    $status    = $_POST['status'];
    $mapped_bsi=$_POST['mapped_bsi'];
    $mapped_rm=$_POST['mapped_rm'];

    if (!isset($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        header('location:enginner_master_op.php?op='.$_GET['op'].'id='.$_GET['id'].'&type=error&msg=invalid csrf token');
        exit();
    }
    /**
     *  We are using **is_edit** variale for checking the operations
     *  is_edit is = Update , Save
     * if is_edit = 1 => update
     * else is_edit => save
     */
    if (isset($_POST['is_edit']) && $_POST['is_edit'] == 1) {
        $stmt = $link1->prepare("
            UPDATE locationuser_master 
            SET pwd=?, contactmo=?, emailid=?, address=?, 
                pincode=?, stateid=?, cityid=?, 
                mapped_bsi=?, mapped_rm=?, statusid=?, 
                updatedate=NOW()
            WHERE userloginid=?
        ");

        $stmt->bind_param(
                "ssssiiissis",
                $password,
                $contact,
                $email,
                $address,
                $pincode,
                $state,
                $city,
                $mapped_bsi,
                $mapped_rm,
                $status,
                $user_id
        );
        $stmt->execute();
        unset($_SESSION['csrf_token']);
        header('location:enginner_master_op.php?op='.$_GET['op'].'&id='.$_GET['id'].'&type=success&msg=user update successfully');
        exit();
    }
    else {
        $address=trim($address);
        $sql = "INSERT INTO locationuser_master (spare_location_code,card_code,userloginid,locusername,pwd,emailid,type,contactmo,mapped_bsi,mapped_rm,cityid,stateid,address,
                                 pincode,date_of_birth,date_of_joining,statusid, createdate)VALUES ('','','$user_id','$username','$password','$email','','$contact','$mapped_bsi','$mapped_rm',$city,$state,'$address',$pincode,'','',$status,NOW())";
        if (mysqli_query($link1, $sql)) {
            header('location:enginner_master_op.php?op='.$_GET['op'].'id='.$_GET['id'].'&type=success&msg=user Added successfully');
            exit();
        } else {
            header('location:enginner_master_op.php?op='.$_GET['op'].'id='.$_GET['id'].'&type=error&msg=Some things Wrong , Please Try Again Laters');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
    <meta http-equiv="refresh" content="1800">
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
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <!-- Include Date Picker -->
    <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
    <style>
        .custom-error-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeIn 0.2s ease;
        }

        .custom-error-modal {
            background: #fff;
            padding: 25px 30px;
            width: 320px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            text-align: center;
            animation: slideUp 0.25s ease;
            font-family: Arial, sans-serif;
        }

        .custom-error-modal h3 {
            color: #e63946;
            margin-bottom: 10px;
        }

        .custom-error-modal p {
            margin-bottom: 20px;
            color: #333;
        }

        .custom-error-modal button {
            padding: 8px 18px;
            border: none;
            background: #e63946;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
        }

        .custom-error-modal button:hover {
            background: #c1121f;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
    <style>
        .custom-success-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .custom-success-modal {
            background: #ffffff;
            padding: 25px;
            width: 300px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: fadeIn 0.3s ease-in-out;
        }
        .custom-success-modal h3 {
            color: #28a745;
            margin-bottom: 10px;
        }
        .custom-success-modal button {
            margin-top: 15px;
            padding: 8px 18px;
            border: none;
            border-radius: 5px;
            background: #28a745;
            color: white;
            cursor: pointer;
        }
        .custom-success-modal button:hover {
            background: #218838;
        }
        @keyframes fadeIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
    </style>
    <style>
        #customLoader {
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.94);
            top: 0;
            left: 0;
            z-index: 9999;
            display: none;
        }

        .spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 6px solid #f3f3f3;
            border-top: 6px solid #28a745;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
    <style>

    </style>
</head>
<body>

<div id="customLoader">
    <div class="spinner"></div>
</div>
<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="<?=$screenwidth?>">
            <h2 align="center"><i class="fa fa-users"></i> <?=$op==='edit'?'Update':'Add'?> Enginner</h2><br/><br/>
            <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
                <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                    <input type="hidden" name="csrf_token"
                           value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">

                    <!--                    userid and username -> read only -->
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="user_id" class="col-md-6 control-label">
                                User ID<span class="red_small">*</span>
                            </label>
                            <div class="col-md-6">
                                <input name="user_id" type="text" id="user_id"
                                       value="<?= $is_edit ? $edit_data['userloginid'] : '' ?>"
                                       class="form-control"
                                        <?= $is_edit ? 'readonly' : '' ?>>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="col-md-6 control-label">User Name<span class="red_small">*</span></label>
                            <div class="col-md-6">
                                <input name="username" id="username" type="text" autocomplete="off"
                                       value="<?= $is_edit ? $edit_data['locusername'] : '' ?>"
                                       class="form-control"
                                        <?= $is_edit ? 'readonly' : '' ?>>
                            </div>
                        </div>
                    </div>
                    <!--                    password id and mobile no-->
                    <div class="form-group">
                        <div class="col-md-6"><label class="col-md-6 control-label">Password</label>
                            <div class="col-md-6">
                                <input name="password" type="text"
                                       value="<?= $is_edit ? $edit_data['pwd'] : '' ?>"
                                       class="form-control">

                            </div>
                        </div>
                        <div class="col-md-6"><label class="col-md-6 control-label">Contact No.<span class="red_small">*</span></label>
                            <div class="col-md-6">
                                <input name="contact_no"
                                       type="text"
                                       class="digits form-control"
                                       id="contact_no"
                                       maxlength="10"
                                       minlength="10"
                                       value="<?= $is_edit ? $edit_data['contactmo'] : '' ?>"
                                       placeholder="+91XXXXXXX"
                                       required>
                            </div>
                        </div>
                    </div>
                    <!--                    email id , address-->
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="email" class="col-md-6 control-label">Email</label>
                            <div class="col-md-6">
                                <input name="email"
                                       value="<?= $is_edit ? $edit_data['emailid'] : '' ?>"
                                       type="email" class="form-control"  id="email" placeholder="demo@gmail.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="textarea" class="col-md-6 control-label">Address<span class="red_small">*</span></label>
                            <div class="col-md-6">
                                <textarea name="address" autocomplete="off" id="textarea" class="form-control"><?= $is_edit ? trim($edit_data['address']) : '' ?></textarea>
                            </div>
                        </div>
                    </div>
                    <scrit></scrit>
                    <!--                    pincode , state , -->
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="pincode" class="col-md-6 control-label">Pin code</label>
                            <div class="col-md-6">
                                <input name="pincode"
                                       type="text"
                                       class="form-control"
                                       id="pincode"
                                       value="<?= $is_edit ? $edit_data['pincode'] : '' ?>"
                                       maxlength="6">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="state" class="col-md-6 control-label">State</label>
                            <div class="col-md-6">
                                <select name="state" id="state" class="form-control select-search">
                                    <option value="">--Select State--</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!--                    city , mapped bsdi [checkboxes]-->
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="city" class="col-md-6 control-label">City</label>
                            <div class="col-md-6">
                                <select name="city" id="city" class="form-control select-search">
                                    <option value="">--Select City--</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-md-6 control-label">Mapped BSI</label>
                            <div class="col-md-6">
                                <select name="mapped_bsi" id="prod_code" class="form-control">
                                    <option value="">--Select BSI--</option>
                                    <?php
                                    $sql="SELECT * FROM admin_users where designation_id=45";
                                    $result=mysqli_query($link1,$sql);
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $isSelected=$edit_data['mapped_bsi']===$row['sapid']?"selected":"";
                                            echo '<option value="' . htmlspecialchars($row['sapid']) . '" '.$isSelected.'>'
                                                    . htmlspecialchars($row['name']) .
                                                    '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!--                    mapped rm [multiple checkbox= ek row me  2] and status-->
                    <div class="form-group">
                        <div class="col-md-6">
                            <label class="col-md-6 control-label">Status</label>
                            <div class="col-md-6">
                                <select name="status" id="status" class="form-control select-search">
                                    <option value="">--Select Status--</option>
                                    <option value="1" <?=$edit_data['statusid']==1?'selected':''?>>Active</option>
                                    <option value="0" <?=$edit_data['statusid']==0?'selected':''?>>Deactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-md-6 control-label">Mapped RSI</label>
                            <div class="col-md-6">
                                <select name="mapped_rm" id="mapped_rm" class="form-control select-multiple" required>
                                    <option value="">--Select BSI--</option>
                                    <?php
                                    $sql="SELECT * FROM admin_users where designation_id=2";
                                    $result=mysqli_query($link1, $sql);
                                    if($result&&mysqli_num_rows($result)>0){
                                        while($row=mysqli_fetch_assoc($result)){
                                            $isSelected=$edit_data['mapped_rm']===$row['sapid']?"selected":"";
                                            echo '<option value="' . htmlspecialchars($row['sapid']) . '" '.$isSelected.'>'
                                                    . htmlspecialchars($row['name']) .
                                                    '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="is_edit" value="<?= $is_edit ? 1 : 0 ?>">
<!--                     dummy data-->
                    <style>
                        .main_container{
                            border:1px solid lightgray;
                            width:220px;
                            padding:8px;
                            border-radius:8px;
                            position:relative;
                            display:flex;
                            align-items:center;
                        }
                        .main_container input{
                            border:none;
                            outline:none;
                            flex:1;
                        }

                        .icon{
                            font-size:16px;
                            margin-left:5px;
                            cursor: pointer;
                        }

                        .valid{
                            color:green;
                        }

                        .invalid{
                            color:red;
                        }

                        .main_container label{
                            position:absolute;
                            left:10px;
                            top:10px;
                            transition:0.2s;
                            background:white;
                            padding:0 4px;
                            color:gray;
                        }

                        /* label upar */
                        .main_container.active label{
                            top:-8px;
                            font-size:12px;
                        }

                        #op_custom{
                            border:none;
                            outline:none;
                            width:100%;
                        }
                    </style>
                    <div style="display: none !important;" class="main_container" id="container">
                        <label for="op_custom">Name</label>
                        <input id="op_custom" type="text" name="op">
                        <span id="icon" class="icon"></span>
                    </div>
                    <script>
                        const input = document.getElementById("op_custom");
                        const container = document.getElementById("container");
                        const icon = document.getElementById("icon");

                        input.addEventListener("input", function(){

                            let value = input.value.trim();

                            if(value.length === 0){
                                icon.innerHTML = "";
                                return;
                            }

                            // validation (only letters, min 3 characters)
                            let regex = /^[A-Za-z]{3,}$/;

                            if(regex.test(value)){
                                icon.innerHTML = "✔";
                                icon.className = "icon valid";
                            }else{
                                icon.innerHTML = "✖";
                                icon.className = "icon invalid";
                            }

                        });
                        icon.addEventListener("click",function(){
                            if(icon.classList.contains("invalid")){
                                input.value = "";
                                container.classList.remove("active");
                                icon.classList.remove("invalid");
                                icon.textContent=""
                            }
                        });
                        input.addEventListener("focus", ()=>{
                            container.classList.add("active");
                        });

                        input.addEventListener("blur", ()=>{
                            if(input.value === ""){
                                container.classList.remove("active");
                            }
                        });
                    </script>
<!--                    end here-->

                    </div>
                    <div class="text-center mt-5">
                        <button class="btn btn-primary">
                            <span id="operation_name"><?=$op==='edit'?'Update':'Add'?></span>
                        </button>
                        <span class="btn btn-primary" onclick="window.location.href='enginner_master.php'">
                            <span id="operation_name">Cancel</span>
                        </span>
                    </div>
                </form>
            </div><!--End form group-->
        </div>
        <!--End col-sm-9-->
    </div>
</div>

<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
    const BASE_URL="../pagination/fetch_state_city.php";
    // const BASE_URL="Li4vcGFnaW5hdGlvbi9mZXRjaF9zdGF0ZV9jaXR5LnBocA==";
    console.log(btoa(BASE_URL));
    console.log(atob("Li4vcGFnaW5hdGlvbi9mZXRjaF9zdGF0ZV9jaXR5LnBocA=="));
    const state=document.getElementById("state");
    const city=document.getElementById("city");
    const pincode=document.getElementById("pincode");
    loadStates();
    state.addEventListener("change", function(){
        // console.log(state.value);
        $("#customLoader").show();
        loadCities(state.value);
        $("#customLoader").hide();
    })

    /**
     * THis function is load the state using the Fetch API;
     * @param selectedId : page is open in edit mode and provided privious value for by default selection;
     * @returns {Promise<void>} : return promise for getting the error handling
     */
    async function loadStates(selectedId=null){
        try{
            const response = await fetch(`${BASE_URL}?state`);
            if(!response.ok){
                throw new Error("Failed to fetch states");
            }
            const data = await response.text();
            state.innerHTML = data;
            if(selectedId){
                state.value = selectedId;
            }
        }catch(error){
            console.error("State Error:", error);
        }
    }

    /**
     * This function is used to loadCitites on the basic of
     * @stateId and @selectedCityId;
     * @param stateId : this paramerter is used to getting state id;
     * @param selectedCityId : this paramerter is help to  when user open is edit mode and
     * value is already exists then selectedcity provided the value and compare previous and current value;
     * @returns {Promise<void>}: return promise for better response
     */
    async function loadCities(stateId,selectedCityId=null){
        try{
            const response = await fetch(`${BASE_URL}?state_id=${stateId}`);

            if(!response.ok){
                EError("Somethings is Wrong , City Data is not loaded");
                throw new Error("Failed to fetch cities");
            }
            const data = await response.text();
            city.innerHTML = data;
            if(selectedCityId){
                city.value = selectedCityId;
            }
        }catch(error){
            console.error("City Error:", error);
        }
    }

    /**
     * This is Event , is helpful for
     * when user enter the pincode (6 digits) then this event is trigger
     * and getting the state and city and set value in city and state
     */
    pincode.addEventListener("input", async function(){
        this.value = this.value.replace(/\D/g, '');
        if(this.value.length === 6){
            try{
                const response = await fetch(`${BASE_URL}?pincode=${this.value}`);
                const data = await response.json();
                if(data.stateid){
                    await loadStates(data.stateid);
                    await loadCities(data.stateid, data.cityid);
                }
            }catch(err){
                EError("Somethings is Wrong , Please Try Again Later");
                console.error("Pincode error:", err);
            }
        }
    });

    /**
     * when user change the city value then this Event is triggers
     * and fetch the pincode and set value in #pincode
     */
    city.addEventListener("change", async function(){
        const cityId = this.value;
        if(cityId){
            try{
                const response = await fetch(`${BASE_URL}?city_id=${cityId}`);
                const data = await response.text();
                pincode.value = data;
            }catch(err){
                EError("Somethings is Wrong , Please Try Again Later");
                console.error("City to Pincode error:", err);
            }
        }
    });

    /**
     * This Event Perform , When User Open page in Edit mode then
     * if pincode have a value then those fetch the state and city
     * according to user pincode
     */
    window.addEventListener("load", async function () {
        $("#customLoader").show();
        <?php
        if ($_REQUEST['msg'] && $_REQUEST['type']==='error'){
            echo "EError('".$_REQUEST['msg']."');";
        }else if ($_REQUEST['msg'] && $_REQUEST['type']==='success'){
            echo "SSuccess('".$_REQUEST['msg']."');";
        }
        ?>
        const existingPincode = pincode.value;
        if (existingPincode && existingPincode.length === 6) {
            try {
                const response = await fetch(`${BASE_URL}?pincode=${existingPincode}`);
                const data = await response.json();
                if (data && data.stateid) {
                    await loadStates(data.stateid);
                    await loadCities(data.stateid, data.cityid);

                }
            } catch (error) {
                console.error("Edit Mode Pincode Error:", error);
            }
        }
        $("#customLoader").hide();
    });

    /**
     *  this function is help to load all States
     * @returns {Promise<void>}
     */
    async function loadAll(){
        try{
            const response = await fetch(`${BASE_URL}?state`);
            const data=await response.text();
            console.log(data);
        }catch (error){
            EError("Somethings is Wrong , Please Try Again Later");
            console.log("erro in pincode")
        }
    }

    /**
     * this fuction is used to showing the Error Message
     * @param message
     * @constructor
     */
    function EError(message) {
        message=message.toUpperCase()
        const overlay = document.createElement("div");
        overlay.className = "custom-error-overlay";
        overlay.innerHTML = `
      <div class="custom-error-modal">
        <h3>Error</h3>
        <p>${message}</p>
        <button id="closeErrorBtn">Close</button>
      </div>
    `;
        document.body.appendChild(overlay);
        document.getElementById("closeErrorBtn").onclick = function() {
            overlay.remove();
            window.location.href="enginner_master.php??type=error&msg=Successfully user is Added/Updated=<?=$_GET['id']?>"
        };
        overlay.onclick = function(e) {
            if (e.target === overlay) {
                // overlay.remove();
            }
        };
    }

    /**
     * This is a helper function and is used to showing the Success Messages
     * @param message
     * @constructor
     */
    function SSuccess(message) {
        message=message.toUpperCase();
        const overlay = document.createElement("div");
        overlay.className = "custom-success-overlay";
        overlay.innerHTML = `
      <div class="custom-success-modal">
        <h3>Success</h3>
        <p>${message}</p>
        <button id="closeSuccessBtn">OK</button>
      </div>
    `;
        document.body.appendChild(overlay);

        document.getElementById("closeSuccessBtn").onclick = function() {
            overlay.remove();
            window.location.href="enginner_master.php?type=success&msg=Successfully user is added"
        };

        overlay.onclick = function(e) {
            if (e.target === overlay) {
                // overlay.remove();
            }
        };
    }


    /**
     * This is a helper function ,
     * somethings background task is work
     * then showing the loader
     */
    function showingLoader(){}

    /**
     * This is a helper function ,
     * somethings background task is work
     * then hide the loader
     */
    function hideLoader(){}
</script>
<script>
   function EnginnerMaster(obj={
       id:"",
       name:"",
       pass:"",
       contact:"",
       email:"",
       address:"",
       pincode:"",
       state:0,
       city:0,
       bsi:0,
       rsi:0,
       status:false
   }){
        this.data=obj;
        this.init();
   }
   function ObserverTracking(fn){
       if(typeof fn==="function"){
           Promise.resolve(fn())
               .then(res => console.log("Resolved:", res))
               .catch(err => console.error("Rejected:", err));

       }
       return;
   }
   const value1=0;
   ObserverTracking(function(){
       if(value1===0){
          return  Promise.resolve(value1);
       }else{
          return  Promise.reject(value1);
       }
   });
   const key=[];
   function fetchPincode(pincodevalue){}
   function cityFetch(){}
   function statefetch(){}
   EnginnerMaster.prototype.init=function(){
       const en=this.encode(this.data.id);
       const de=this.decode(en);
       this.picodeSetter(this.data.pincode);
       this.stateSetter(this.data.state);
   }
   EnginnerMaster.prototype.encode = function(str) {
       return btoa(unescape(encodeURIComponent(str)));
   }
   EnginnerMaster.prototype.decode = function(str) {
       return decodeURIComponent(escape(atob(str)));
   }
   EnginnerMaster.prototype.picodeSetter=function(pincode=0){
       if(pincode!==0){
           console.log("Already exists - Pincode");
           this.stateSetter(1);
           this.citySetter(1)
       }
       else{
           console.log("drop-down set");
       }
   }
   EnginnerMaster.prototype.stateSetter=function(state=0){
       if(state!==0){
           console.log("Already exists");
       }else{
           console.log("drop-down state");
       }
   }
   EnginnerMaster.prototype.citySetter=function(city=0){
       if(city!==0){
           console.log("Already exists");
       }else{
           console.log("drop-down city");
       }
   }
   const obj={
       id:"<?=$edit_data['userloginid']?>",
       name:"<?=empty($edit_data['locusername'])?'':$edit_data['locusername']?>",
       pass:"<?=empty($edit_data['pwd'])?'N/A':$edit_data['pwd']?>",
       contact:"<?=empty($edit_data['contactmo'])?'0000000000':$edit_data['contactmo']?>",
       email:"<?=empty($edit_data['emailid'])?'N/A':$edit_data['emailid']?>",
       address:"<?=empty($edit_data['address'])?'N/A':$edit_data['address']?>",
       pincode:"<?=empty($edit_data['pincode'])?0:$edit_data['pincode']?>",
       state:<?=empty($edit_data['stateid'])?0:$edit_data['stateid']?>,
       city:<?=empty($edit_data['cityid'])?0:$edit_data['cityid']?>,
       bsi:"<?=empty($edit_data['mapped_bsi'])?0:$edit_data['mapped_bsi']?>",
       rsi:"<?=empty($edit_data['mapped_rm'])?0:$edit_data['mapped_rm']?>",
       status:<?=empty($edit_data['statusid'])?true:$edit_data['statusid']?>
   }
   const enginner=new EnginnerMaster(obj);
   // Regex - pattern matching
   let str = "hxllo world";
   console.log(/h.llo/.test(str));
</script>
</body>
</html>