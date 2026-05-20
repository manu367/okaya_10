<?php
require_once("../includes/config.php");

header('Content-Type: application/json');


if (empty($_SESSION['navigation_a'])) {
    $tab=[];
    $sql="select maintabname , maintabicon from tab_master where status = '1' and tabfor='admin' group by maintabname order by maintabseq";
    $result=mysqli_query($link1,$sql);
    if($result && mysqli_num_rows($result)>0){
        while($row=mysqli_fetch_assoc($result)){
            $subtab=[];
            $sql2="select tabid , subtabname , subtabicon , filename from tab_master where status = '1' and tabfor='admin' and  maintabname = '".$row['maintabname']."' order by subtabseq";
            $result2=mysqli_query($link1,$sql2);
            if($result && mysqli_num_rows($result2)>0){
                while($row2=mysqli_fetch_assoc($result2)){
                    $subtab[]=[
                            "tabid"=>$row2['tabid'],
                            "subtabname"=>$row2['subtabname'],
                            "subtabicon"=>$row2['subtabicon'],
                            "filename"=>$row2['filename'],
                    ];
                }
            }
            $tab[]=["icon"=>$row['maintabicon'],"tab"=>$row['maintabname'],"sub_tab"=>$subtab];
        }
    }
    $tab[]=["icon"=>'fa-power-off',"tab"=>'logout'];
    //$_SESSION['navigation_a']= json_encode($tab);
}

//var_dump($_SESSION);
echo json_encode($tab);
unset($_SESSION['navigation_a']);
exit();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
            $("#frm1").validate();
        });
    </script>
    <script type="text/javascript" src="../js/jquery.validate.js"></script>
    <!-- Include Date Picker -->
    <script type="text/javascript" src="../js/bootstrap-multiselect.js"></script>
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css" type="text/css"/>
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
            <h2 align="center"><i class="fa fa-users"></i> <?=$op==='edit'?'Update':'Add'?> New User</h2><br/><br/>
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
                                <textarea name="address" autocomplete="off" id="textarea"
                                          class="form-control">
                                    <?= $is_edit ? $edit_data['address'] : '' ?>
                                </textarea>

                            </div>
                        </div>
                    </div>
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
            window.location.href="enginner_master.php"
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
            window.location.href="enginner_master.php"
        };

        overlay.onclick = function(e) {
            if (e.target === overlay) {
                // overlay.remove();
            }
        };
    }
    // SSuccess("Data saved successfully!");

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
</body>
</html>
