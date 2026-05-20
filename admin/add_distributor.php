<?php
class Operations {
    private $conn;
    public function __construct(mysqli $conn){
        $this->conn = $conn;
    }
    public function loadUser($id): array
    {
        $data = [];

        $sql = "SELECT * FROM distributor_master WHERE distributorid = '$id'";
        $result = mysqli_query($this->conn, $sql);

        if($result && mysqli_num_rows($result) > 0){
            $data = mysqli_fetch_assoc($result);   // <-- pure row as array
        }

        return $data;
    }
    public function addUser(array $d): bool {

        if(!$this->validateDetails(
            $d['name'],$d['email'],$d['contact'],$d['state'],$d['city'],
            $d['pincode'],$d['billingAdd'],$d['shippingAdd'],$d['gst'],
            $d['sapcode'],$d['uType']
        )) return false;

        mysqli_autocommit($this->conn,false);

        $sql = "INSERT INTO distributor_master SET
            distributorname = '{$d['name']}',
            distributorcode = '{$d['sapcode']}',
            sap_hanacode    = '{$d['sapcode']}',
            userid          = '{$d['contact']}',
            password        = '{$d['contact']}',
            type            = '{$d['uType']}',
            email           = '{$d['email']}',
            address1        = '{$d['billingAdd']}',
            address2        = '{$d['shippingAdd']}',
            cityid          = '{$d['city']}',
            stateid         = '{$d['state']}',
            countryid='1', 
            pincode         = '{$d['pincode']}',
            phone           = '{$d['contact']}',
            mobile          = '{$d['contact']}',
            gst_no          = '{$d['gst']}',
            status          = 'active',
            updateby        = '{$_SESSION['userid']}'
        ";

        if(mysqli_query($this->conn,$sql)){
            mysqli_commit($this->conn);
            return true;
        }else{
            mysqli_rollback($this->conn);
            return false;
        }
    }
    public function updateUser(array $d): bool {

        if(empty($d['refid'])) return false;

        if(!$this->validateDetails(
            $d['name'],$d['email'],$d['contact'],$d['state'],$d['city'],
            $d['pincode'],$d['billingAdd'],$d['shippingAdd'],$d['gst'],
            $d['sapcode'],$d['uType']
        )) return false;

        mysqli_autocommit($this->conn,false);

        $sql = "UPDATE distributor_master SET
            distributorname = '{$d['name']}',
            distributorcode = '{$d['sapcode']}',
            sap_hanacode    = '{$d['sapcode']}',
            type            = '{$d['uType']}',
            email           = '{$d['email']}',
            address1        = '{$d['billingAdd']}',
            address2        = '{$d['shippingAdd']}',
            cityid          = '{$d['city']}',
            stateid         = '{$d['state']}',
            pincode         = '{$d['pincode']}',
            phone           = '{$d['contact']}',
            mobile          = '{$d['contact']}',
            gst_no          = '{$d['gst']}',
            updateby        = '{$_SESSION['userid']}',
            status          = 'active',
            countryid='1'
            WHERE distributorid = '{$d['refid']}'
        ";

        if(mysqli_query($this->conn,$sql)){
            mysqli_commit($this->conn);
            return true;
        }else{
            mysqli_rollback($this->conn);
            return false;
        }
    }
    private function validateDetails(
        $name,$email,$contact,$state,$city,$pincode,
        $billingAdd,$shippingAdd,$gst,$sapcode,$uType
    ): bool {

        if(strlen(trim($name)) < 5) return false;
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) return false;
        if(!preg_match('/^[0-9]{10}$/',$contact)) return false;
        if(!$state || !$city) return false;
        if(!preg_match('/^[0-9]{6}$/',$pincode)) return false;
        if(!$billingAdd || !$shippingAdd) return false;
        if(!$gst || !$sapcode || !$uType) return false;

        return true;
    }
}
?>

<?php
require_once("../includes/config.php");

$loader=[];

$op=$_REQUEST['op'];



if(empty($op))$op='Add';

if($op=="Add"||$op=="Edit"){}
else{header("location:distributor_master.php?msg=Some Things Wrong &chkflag=danger");exit();}

$operation=new Operations($link1);

// when uses hit post request
//@extract($_POST);
$data = [
        'name'        => $_POST['name'] ?? '',
        'email'       => $_POST['email'] ?? '',
        'contact'     => $_POST['phone'] ?? '',
        'state'       => $_POST['state'] ?? '',
        'city'        => $_POST['city'] ?? '',
        'pincode'     => $_POST['pincode'] ?? '',
        'billingAdd'  => $_POST['bill_address'] ?? '',
        'shippingAdd' => $_POST['ship_address'] ?? '',
        'gst'         => $_POST['gst_no'] ?? '',
        'sapcode'     => $_POST['distributorcode'] ?? '',
        'uType'       => $_POST['status'] ?? '',
        'refid'       => $_POST['refid'] ?? ''
];

// Get : update ( loading time page)
if($op=="Edit"){
    if(!empty($_REQUEST['id'])){
        $loader = $operation->loadUser($_REQUEST['id']);
    }else{
        header("location:add_distributor.php?msg=User Id is Missing&chkflag=danger");exit();
    }
}
// Post : adding
if(isset($_POST['ADD'])){
    if($operation->addUser($data)){
        header("location:distributor_master.php?msg=Distributor Added&chkflag=success");
    }else{
        header("location:add_distributor.php?msg=Validation Failed&chkflag=danger");
    }
}
// Post : update
if(isset($_POST['Update'])){
    if($operation->updateUser($data)){
        header("location:distributor_master.php?msg=Distributor Updated&chkflag=success");
    }else{
        header("location:add_distributor.php?msg=Validation Failed&chkflag=danger");
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
            $("#form1").validate();
        });
    </script>
    <script src="../js/frmvalidate.js"></script>
    <script src="../js/jquery.validate.js"></script>
</head>

<body>
<div class="container-fluid">
    <div class="row content">
        <?php include("../includes/leftnav2.php"); ?>

        <div id="kk" class="<?=$screenwidth?>">
            <h2 align="center"><i class="fa fa-shopping-basket"></i> <?=$_REQUEST['op']?> Dealer/Distributer</h2><br><br>
            <form  id="dealerForm" method="post" action="">
                <input type="hidden" name="refid" value="<?= $loader['distributorid'] ?? '' ?>">
                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                <div class="form-group">

                    <div class="row">

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Dealer Name <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <input id="name" name="name" class="form-control" placeholder="Enter Name" value="<?=$loader['distributorname']??''?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Contact No <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <input id="phone" maxlength="10" name="phone" class="form-control" placeholder="10 Digit Mobile" value="<?=$loader['phone']??''?>">
                            </div>
                        </div>

                    </div>
                </div>


                <div class="form-group">
                    <div class="row">

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">State <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <select class="form-control" id="state" name="state">
                                    <option>Select State</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">City <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <select class="form-control" id="city" name="city">
                                    <option>Select City</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="form-group">
                    <div class="row">

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Pin Code <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <input id="pincode" name="pincode" maxlength="6" class="form-control" placeholder="6 Digit Pin" value="<?=$loader['pincode']??''?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Email</label>
                            <div class="col-md-5">
                                <input id="email" name="email" class="form-control" placeholder="email@example.com" value="<?=$loader['email']??''?>">
                            </div>
                        </div>

                    </div>
                </div>


                <div class="form-group">
                    <div class="row">

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Billing Address <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <textarea id="billing_add" name="bill_address" class="form-control"><?=$loader['address1']??'Empty'?></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Shipping Address</label>
                            <div class="col-md-5">
                                <textarea id="shipping_add" name="ship_address" class="form-control"><?=$loader['address2']??'Empty'?></textarea>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="form-group">
                    <div class="row">

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">GST No</label>
                            <div class="col-md-5">
                                <input  id="gst" name="gst_no" class="form-control" value="<?=$loader['gst_no']??''?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">SAP Code <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <input id="sap" name="distributorcode" class="form-control" value="<?=$loader['distributorcode']??''?>">
                            </div>
                        </div>

                    </div>
                </div>


                <div class="form-group">
                    <div class="row">

                        <div class="col-md-6">
                            <label class="col-md-5 control-label">Dealer Type <span class="red_small">*</span></label>
                            <div class="col-md-5">
                                <select class="form-control" id="u-type" name="status">
                                    <option>Select Type</option>
                                    <option value="dealer" <?= (isset($loader['type']) && $loader['type']=="dealer") ? "selected" : "" ?>>Dealer</option>
                                    <option value="distributer" <?= (isset($loader['type']) && $loader['type']=="distributer") ? "selected" : "" ?>>Distributer</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="form-group text-center">
                    <?php if($op=='Add'){ ?>
                        <button type="button" onclick="submitForm('ADD')" class="btn btn-primary">Save</button>
                    <?php } else { ?>
                        <button type="button" onclick="submitForm('Update')" class="btn btn-primary">Update</button>
                    <?php } ?>
                    <button type="button" onclick="window.location.href='distributor_master.php'" class="btn btn-danger">Back</button>

                </div>

            </form>
        </div>
    </div>
</div>

<div id="errorBox" class="modal" style="display:none; background:rgba(0,0,0,.65);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">

            <div class="modal-header bg-danger text-white rounded-top-4 py-3">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <span style="font-size:22px">⚠</span> Validation Error
                </h5>
            </div>

            <div class="modal-body text-center py-4">
                <p id="errorText" class="text-secondary fw-semibold fs-6 mb-0"></p>
            </div>

            <div class="modal-footer justify-content-center border-0 pb-4">
                <button class="btn btn-danger rounded-pill px-5 fw-semibold" onclick="hideError()">OK</button>
            </div>

        </div>
    </div>
</div>


<?php if(!empty($_GET['msg'])){ ?>
<!--    <h1>--><?php //= htmlspecialchars($_GET['msg'], ENT_QUOTES, 'UTF-8'); ?><!--</h1>-->
    <div id="autoErrorModal" class="modal" style="display:none;background:rgba(0,0,0,.65);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow border-0 rounded-4">

                <div class="modal-header bg-danger text-white rounded-top-4">
                    <h5 class="modal-title">⚠ Error</h5>
                </div>

                <div class="modal-body text-center">
                    <p id="autoErrorText" class="fw-semibold text-secondary"></p>
                </div>

            </div>
        </div>
    </div>
<script>
    let errorTimer = null;
    <?php if(!empty($_GET['msg'])){ ?>
    showAutoError("<?= htmlspecialchars($_GET['msg'],ENT_QUOTES,'UTF-8') ?>");
    <?php } ?>
    function showAutoError(msg){
        document.getElementById("autoErrorText").innerHTML =`<h3 style="text-transform: uppercase;margin: 50px;">${msg}</h3>`;
        const box = document.getElementById("autoErrorModal");
        box.style.display = "block";

        if(errorTimer) clearTimeout(errorTimer);

        errorTimer = setTimeout(() => {
            box.style.display = "none";
            //hideEverythings(msg);
        }, 5000); // 5 seconds
    }

    function hideEverythings(msg){
        const container = document.getElementById("kk");
        const form = document.getElementById("dealerForm");

        // Completely remove form from DOM
        if(form) form.remove();

        // Replace container content with centered OK button
        container.innerHTML = `<div style="position: fixed;top: 30%;right: 50%;">
<h2 style="text-transform: uppercase;color: red;font-weight: lighter">${msg}</h2>
<button type="button" class="btn btn-primary px-5 py-2 rounded-pill fs-5"
                onclick="window.location.href='distributor_master.php'">
                Go Back
            </button>
</div>`;
    }

</script>
<?php } ?>

<script>

    const stateDD = document.getElementById("state");
    const cityDD  = document.getElementById("city");

    const EDIT_STATE = "<?= $loader['stateid'] ?? '' ?>";
    const EDIT_CITY  = "<?= $loader['cityid'] ?? '' ?>";

    fetch("../pagination/state-grid.php")
        .then(res => res.json())
        .then(data => {
            stateDD.innerHTML = '<option value="">Select State</option>';
            data.forEach(s => {
                let opt = document.createElement("option");
                opt.value = s.id;
                opt.textContent = s.name;
                if(EDIT_STATE && s.id == EDIT_STATE) opt.selected = true;
                stateDD.appendChild(opt);
            });

            // Agar Update mode hai → cities bhi auto load karo
            if(EDIT_STATE){
                loadCities(EDIT_STATE, EDIT_CITY);
            }
        });

    stateDD.addEventListener("change", function(){
        loadCities(this.value, "");
    });

    /* ===== CITY FETCH FUNCTION ===== */
    function loadCities(stateId, selectedCity){
        if(!stateId){
            cityDD.innerHTML = '<option value="">Select City</option>';
            return;
        }

        fetch("../pagination/city-grid.php?state="+stateId)
            .then(res => res.json())
            .then(data => {
                cityDD.innerHTML = '<option value="">Select City</option>';
                data.forEach(c => {
                    let opt = document.createElement("option");
                    opt.value = c.id;
                    opt.textContent = c.name;
                    if(selectedCity && c.id == selectedCity) opt.selected = true;
                    cityDD.appendChild(opt);
                });
            });
    }
    function submitForm(actionType){
        if(!validateForm()) return;
        let f = document.getElementById("dealerForm");
        let act = document.getElementById("formAction");
        if(!act){
            act = document.createElement("input");
            act.type = "hidden";
            act.id = "formAction";
            act.name = actionType;
            act.value = actionType;
            f.appendChild(act);
        }else{
            act.name = actionType;
            act.value = actionType;
        }

        f.submit();
        // f.submit();
    }
    function validateForm(){
        let name  = document.getElementById("name").value.trim();
        let phone = document.getElementById("phone").value.trim();
        let state = document.getElementById("state").value;
        let city  = document.getElementById("city").value;
        let pin   = document.getElementById("pincode").value.trim();
        let email = document.getElementById("email").value.trim();
        let bill  = document.getElementById("billing_add").value.trim();
        let ship  = document.getElementById("shipping_add").value.trim();
        let gst   = document.getElementById("gst").value.trim();
        let sap   = document.getElementById("sap").value.trim();
        let utype = document.getElementById("u-type").value;
        if(name=="" || name.length < 5){
            showError("Dealer name must be at least 5 characters");
            return false;
        }
        if(!/^[0-9]{10}$/.test(phone)){
            showError("Enter valid 10 digit mobile number");
            return false;
        }
        if(state=="" || state=="Select State"){
            showError("Please select State");
            return false;
        }
        if(city=="" || city=="Select City"){
            showError("Please select City");
            return false;
        }
        if(!/^[0-9]{6}$/.test(pin)){
            showError("Enter valid 6 digit pincode");
            return false;
        }
        if(email=="" || !/^\S+@\S+\.\S+$/.test(email)){
            showError("Enter valid email address");
            return false;
        }
        if(bill==""){
            showError("Billing address is required");
            return false;
        }
        if(ship==""){
            showError("Shipping address is required");
            return false;
        }
        if(gst==""){
            showError("GST number is required");
            return false;
        }
        if(sap==""){
            showError("SAP code is required");
            return false;
        }
        if(utype=="" || utype=="Select Type"){
            showError("Please select Dealer Type");
            return false;
        }
        return true;
    }
    function showError(msg){
        document.getElementById("errorText").innerHTML = `<h3>${msg}</h3>`;
        document.getElementById("errorBox").style.display = "block";
        document.body.style.overflow = "hidden"; // background scroll lock
    }
    function hideError(){
        document.getElementById("errorBox").style.display = "none";
        document.body.style.overflow = "auto";
    }

</script>

</body>
</html>
