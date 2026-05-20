<?php
/**
 * Application Workflow: Forgot Password Process
 * This process is divided into four main steps:
 *
 * Step 1: User Identification
 * - The user provides their user_id.
 * - The system validates this user_id against admin_user, location_master, and locationuser_master tables.
 * - If the user is found, return the user model.
 * - If not, throw an exception.
 *
 * Step 2: Phone Number Verification
 * - Display the phone number associated with the user_id.
 * - If the phone number is invalid or unavailable, display a masked/default value (e.g., "0000000000").
 * - If the user requests an OTP with an invalid phone number, return an error indicating an invalid phone number.
 *
 * Step 3: OTP Validation
 * - If the phone number is valid, display the OTP input page.
 * - The user enters the OTP, which is compared with the OTP stored in the session.
 * - If the OTP matches, proceed to the password reset page.
 * - If not, throw an exception indicating an invalid OTP.
 *
 * Step 4: Password Reset
 * - Upon successful OTP verification, allow the user to reset their password.
 * - After the password is successfully changed, redirect the user to the login page.
 *
 * Note: Key Classes and Their Purpose
 * 1. PasswordException
 * - A custom exception class used to handle password-related errors specifically.
 * - Useful when multiple exception types exist on the same page, allowing targeted handling
 * of password-specific issues.
 *
 * 2. ForgetUserPasswordModel
 * - A wrapper (data transfer) class used to encapsulate related data into a single object.
 * - Helps in passing user password reset data cleanly between layers.
 *
 * 3. ForgetPasswordUtils
 * - A utility class that contains multiple helper methods.
 * - Responsible for handling various operations related to the "Forgot Password" process.
 *
 *  Note: Key Variables and Their Purpose
 *
 * $link1
 * - Instance of the MySQLi connection used for database operations.
 *
 * $forgetPassword
 * - Object of the ForgetUserPasswordModel class.
 * - Used to store and transfer user-related data during the forgot password process.
 *
 * $isUserIdFormEnabled
 * $isPhoneNumberFormEnabled
 * $isOTPFormEnabled
 * $isPasswordFormEnabled
 * - Boolean flags used to control which step/form is currently active in the workflow.
 *
 * $res
 * - Stores response metadata.
 * - Metadata is added dynamically based on specific conditions during execution.
 *
 * $step
 * - Tracks the current step of the forgot password process.
 * - Updated after each form submission to control the flow of the application.
 *
 * $user
 * - Stores the model object representing the user.
 * - Holds null if the user is not found or not yet initialized.
 */
include_once("security/dbh.php");
global $link1;
class PasswordException extends Exception {
    public function __construct($message, $code = 0, Exception $previous = null){
        parent::__construct($message, $code, $previous);
    }
}
class ForgetUserPasswordModal{
    private $userid,$username,$userphone,$status,$tablename,$passwordKey,$idKey;
    public function __construct($userid, $username, $userphone,$status,$tablename,$passwordKey,$idKey){
        $this->userid = $userid;
        $this->username = $username;
        $this->userphone = $userphone;
        $this->status = $status;
        $this->tablename = $tablename;
        $this->passwordKey = $passwordKey;
        $this->idKey = $idKey;
    }
    public function getIdKey(){
        return $this->idKey;
    }

    public function getPasswordKey(){
        return $this->passwordKey;
    }
    public function getTableName(){
        return $this->tablename;
    }

    public function getUserid()
    {
        return $this->userid;
    }
    public function getStatus(){
        return $this->status;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function getUserphone()
    {
        return $this->userphone;
    }

}

set_exception_handler(function($e){
    if($e instanceof PasswordException){
        $msg=$e->getMessage();
        header("location:forget-password.php?$msg");
        exit();
    }
    $msg=$e->getMessage();
    header("location:forget-password.php?$msg");
    exit();
});
class ForgetPasswordUnits{
    private $connection;
    public function __construct($connection){
        $this->connection = $connection;
    }
    public function getUSerId($userid){
        $user=null;
        if($userid===null || $userid===""){
            throw new Exception("user id is not Empty");
        }
        if($user=$this->checkInAdminUser($userid)){
            return $user;
        }
        if($user=$this->checkInAdminUser($userid)){
            return $user;
        }
        if($user=$this->checkLocationMaster($userid)){
            return $user;
        }
        if($user=$this->checkLocationUserMaster($userid)){
            return $user;
        }

        throw new Exception("User does not exist");
    }
    private function checkInAdminUser($userid){
        $conn = $this->connection;
        $userid = mysqli_real_escape_string($conn, $userid);
        $sql = "SELECT * FROM admin_users WHERE username = '$userid'";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            throw new Exception(mysqli_error($conn));
        }
        if (mysqli_num_rows($result) > 0) {
            $userfound=mysqli_fetch_assoc($result);
            $modal=new ForgetUserPasswordModal($userfound['id'],$userfound['username'],$userfound['phone'],$userfound['status'],'admin_users','password','id');
            return $modal;
        } else {
            return false;
        }
    }
    private function checkLocationMaster($userid){
        $conn = $this->connection;
        $userid = mysqli_real_escape_string($conn, $userid);
        $sql = "SELECT * FROM location_master WHERE location_code = '$userid'";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            throw new Exception(mysqli_error($conn));
        }
        if (mysqli_num_rows($result) > 0) {
            $userfound=mysqli_fetch_assoc($result);
            $modal=new ForgetUserPasswordModal($userfound['locationid'],$userfound['location_code'],$userfound['contactno1'],$userfound['statusid'],'location_master','pwd','locationid');
            return $modal;
        } else {
            return false;
        }
    }
    private function checkLocationUserMaster($userid){
        $conn = $this->connection;
        $userid = mysqli_real_escape_string($conn, $userid);
        $sql = "SELECT * FROM locationuser_master WHERE userloginid = '$userid'";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            throw new Exception(mysqli_error($conn));
        }
        if (mysqli_num_rows($result) > 0) {
            $userfound=mysqli_fetch_assoc($result);
            $modal=new ForgetUserPasswordModal($userfound['id'],$userfound['userloginid'],$userfound['contactmo'],$userfound['statusid'],'locationuser_master','pwd','id');
            return $modal;
        } else {
            return false;
        }
    }
    public static function phoneNumberMasking($phonenumber){
        if(strlen($phonenumber)<10){
            $phonenumber='0000000000';
            return $phonenumber;
        }
        if($phonenumber==='' || $phonenumber===null){
            $phonenumber='0000000000';
            return $phonenumber;
        }

        $phonenumber = preg_replace('/\D/', '', $phonenumber);
        $length = strlen($phonenumber);
        if ($length <= 4) {
            return $phonenumber;
        }
        $last4 = substr($phonenumber, -4);
        $masked = str_repeat('X', $length - 4);
        return $masked . $last4;
    }
    public static function encodeData($data){
        $KEY = "OKAYA_SECRET_KEY_28_04_2026";
        $encoded = openssl_encrypt($data, "AES-128-ECB", $KEY, 0);
        return base64_encode($encoded);
    }
    public static function decodeData($data){
        $KEY = "OKAYA_SECRET_KEY_28_04_2026";
        $decoded = base64_decode($data);
        return openssl_decrypt($decoded, "AES-128-ECB", $KEY, 0);
    }
    public static function generateOTP($phoneNumber){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
        $otp = rand(100000, 999999);

        $_SESSION['otp'] = $otp;
        $_SESSION['otp_phone'] = $phoneNumber;
        $_SESSION['otp_expiry'] = time() + 300;
        return $otp;
    }
    public static function validateOTP($otp){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }

        if (!isset($_SESSION['otp'])) {
            return false;
        }

        if (time() > $_SESSION['otp_expiry']) {
            unset($_SESSION['otp'], $_SESSION['otp_phone'], $_SESSION['otp_expiry']);
            return false;
        }

        if ($_SESSION['otp'] == $otp) {
            unset($_SESSION['otp'], $_SESSION['otp_phone'], $_SESSION['otp_expiry']);
            return true;
        }
        return false;
    }
    public function updateUserPassword($userid, $table, $password, $passwordColumnName,$id){
        $conn = $this->connection;
        $userid  = mysqli_real_escape_string($conn, $userid);
        $password = mysqli_real_escape_string($conn, $password);

        $sql = "UPDATE `$table` SET `$passwordColumnName` = '$password' WHERE $id = '$userid' LIMIT 1";

        $result = mysqli_query($conn, $sql);
        if (!$result) {
            throw new Exception(mysqli_error($conn));
        }
        return mysqli_affected_rows($conn) > 0;
    }
}

$forgetpassword=new ForgetPasswordUnits($link1);

$isUseridFormEnabled=true;
$isPhoneNumberFormEnabled=false;
$isOTPFormEnabled=false;
$isPasswordFormEnabled=false;

$res=[]; // send to response to the user;
$step = 1;
$user=null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try{
        // user login id form handling
        if (isset($_POST['userid_check'])) {
            $userid=$_POST['userid'];
            $user=$forgetpassword->getUSerId($userid);
            $isUseridFormEnabled=false;
            $isPhoneNumberFormEnabled=true;
            $step=2;
            if($user->getStatus()===0){
                throw new Exception("Deactivated users cannot change passwords.");
            }
        }

        // user phonenumber handling
        elseif (isset($_POST['phonenumber_submit'])) {
            $decodeData=ForgetPasswordUnits::decodeData($_POST['userid']);
            $decodeData=json_decode($decodeData,true);
            if($decodeData['phone']===null || $decodeData['phone']==='0000000000'){
                throw new Exception("Invalid phone number");
            }
            $otp=ForgetPasswordUnits::generateOTP($decodeData['phone']);
            $isUseridFormEnabled=false;
            $isPhoneNumberFormEnabled=false;
            $isOTPFormEnabled=true;
            $isPasswordFormEnabled=false;
            $step=3;
        }

        // user otp sent handling
        elseif (isset($_POST['otp_sent'])) {
            $otp=$_POST['otp'];
            if(ForgetPasswordUnits::validateOTP($otp)){
                $isUseridFormEnabled=false;
                $isPhoneNumberFormEnabled=false;
                $isOTPFormEnabled=false;
                $isPasswordFormEnabled=true;
            }else{
                throw new Exception("Invalid OTP");
            }
            $step=4;
        }

        elseif (isset($_POST['update_password_final'])) {
            $decodeData=ForgetPasswordUnits::decodeData($_POST['user_basic_pass']);
            $decodeData=json_decode($decodeData,true);
            $password=($_POST['password']);
            $ook=$forgetpassword->updateUserPassword($decodeData['userid'],$decodeData['table'],$password,$decodeData['password_key'],$decodeData['id']);
            if($ook){
                $us=$decodeData['user'];
                header("location:index.php?msg=Successfully User($us) Password Updated");
                exit();
            }
        }
    }catch (Exception $e){
        $res['type']='error';
        $res['msg']=$e->getMessage();
        $param=http_build_query($res);
        throw new PasswordException($param);
    }
}

$totalSteps = 4;
$progress = ($step / $totalSteps) * 100; // 1/4=0.25 * 100= 25.00%
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <script src="js/tailwindcss.js"></script>
    <script src="./js/jquery.min.js"></script>
    <style>
        .toast {
            position: fixed;
            top: 20px;
            right: -350px;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(8px);
            z-index: 9999;
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
            animation: progress 60s linear;
        }

        @keyframes progress {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center md:justify-end px-4 md:px-16"
      style="background-image: url('https://care.okaya.in/images/okaya_bg.jpg');background-repeat: no-repeat;background-size: cover;">

<?php
if (isset($_REQUEST['type'], $_REQUEST['msg']) && !empty($_REQUEST['type']) && !empty($_REQUEST['msg'])){
    $type = htmlspecialchars($_REQUEST['type']);
    $msg = htmlspecialchars($_REQUEST['msg']);
    ?>
    <div id="errorPopup" class="toast" style="background-color: <?= $type === 'error' ? 'darkred' : 'green' ?>">
        <span class="icon">⚠️</span>
        <span class="message" style="text-transform: uppercase"><?= htmlspecialchars($msg) ?></span>
    </div>

    <script>
        $(document).ready(function(){
            let toast = $("#errorPopup");

            if(toast.length){
                setTimeout(() => {
                    toast.addClass("show");
                }, 500);

                setTimeout(() => {
                    toast.removeClass("show");
                }, 60000);
            }
        });
    </script>
<?php } ?>

<div class="w-full max-w-md">

    <div class="bg-white border border-gray-200 shadow-xl rounded-2xl p-8">

        <!-- Title -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-800">Forget Password <?=$otp??''?></h1>
            <p class="text-gray-500 text-sm mt-1">Step <?php echo $step ?> of 4</p>
        </div>

        <!-- Progress -->
        <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
            <div class="bg-[green] h-2 rounded-full transition-all duration-300"
                 style="width: <?php echo $progress ?>%"></div>
        </div>

        <!-- STEP 1 -->
        <?php if($isUseridFormEnabled): ?>
            <form method="POST">
                <label class="text-sm text-gray-600">User ID</label>
                <input type="text" name="userid" required
                       class="w-full mt-2 mb-4 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500"/>

                <button type="submit" name="userid_check"
                        class="w-full bg-gray-900 hover:bg-gray-700 text-white py-2 rounded-lg">
                    Continue
                </button>
            </form>
        <?php endif; ?>

        <!-- STEP 2 -->
        <?php if($isPhoneNumberFormEnabled): ?>
            <form method="POST">

                <input type="hidden" name="userid"
                       value="<?= htmlspecialchars(ForgetPasswordUnits::encodeData(json_encode([
                               'userid' => $user->getUserid(),
                               "user"=>$user->getUsername(),
                               'table'  => $user->getTableName(),
                               'phone'=>$user->getUserphone(),
                               "id"=>$user->getIdKey(),
                               'password_key'=>$user->getPasswordKey()
                       ]))) ?>">

                <label class="text-sm text-gray-600">Phone Number</label>
                <input type="tel" name="phone" required value="<?=ForgetPasswordUnits::phoneNumberMasking($user->getUserphone())?>" disabled
                       class="w-full mt-2 mb-4 px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500"/>

                <div class="flex gap-2">
                    <a href="?back=1" class="w-1/2 bg-gray-200 text-center py-2 rounded-lg">Back</a>
                    <button type="submit" name="phonenumber_submit"
                            class="w-1/2 bg-gray-600 text-white py-2 rounded-lg">
                        Send OTP
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <!-- STEP 3 -->
        <?php if($isOTPFormEnabled): ?>
            <form method="POST">

                <input type="hidden" name="user_basic" value="<?=$_REQUEST['userid']?>">

                <label class="text-sm text-gray-600">OTP Verification</label>
                <input type="text" name="otp" maxlength="6" required
                       class="w-full mt-2 mb-2 px-4 py-2 text-center tracking-widest rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500"/>

                <p class="text-xs text-gray-400 mb-4">Resend OTP in 30s</p>

                <div class="flex gap-2">
                    <a href="?back=1" class="w-1/2 bg-gray-200 text-center py-2 rounded-lg">Back</a>
                    <button type="submit" name="otp_sent"
                            class="w-1/2 bg-gray-600 text-white py-2 rounded-lg">
                        Verify
                    </button>
                </div>
            </form>
        <?php endif; ?>

        <!-- STEP 4 -->
        <?php if($isPasswordFormEnabled): ?>
            <form method="POST">
                <input type="hidden" name="user_basic_pass" value="<?=$_REQUEST['user_basic']?>">
                <label class="text-sm text-gray-600">Create Password</label>
                <!-- Password -->
                <div class="relative">
                    <input type="password" id="password" name="password" required placeholder="New Password"
                           class="w-full mt-2 mb-3 px-4 py-2 pr-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"/>

                    <span onclick="togglePassword('password', this)"
                          class="absolute right-3 top-4 cursor-pointer text-gray-500">
            👁️
        </span>
                </div>

                <!-- Confirm Password -->
                <div class="relative">
                    <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm Password"
                           class="w-full mb-3 px-4 py-2 pr-10 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500"/>

                    <span onclick="togglePassword('confirm_password', this)"
                          class="absolute right-3 top-3 cursor-pointer text-gray-500">
            👁️
        </span>
                </div>

                <div class="h-2 bg-gray-200 rounded-full mb-2">
                    <div id="strengthBar" class="h-2 rounded-full w-0 transition-all duration-300"></div>
                </div>

                <p id="passwordMsg" class="text-xs mb-3"></p>
                <p id="matchMsg" class="text-xs mb-4"></p>

                <button type="submit" name="update_password_final"
                        class="w-full bg-gray-600 hover:bg-green-700 text-white py-2 rounded-lg">
                    Finish
                </button>
            </form>

            <script>
                function togglePassword(id, el){
                    const input = document.getElementById(id);

                    if(input.type === "password"){
                        input.type = "text";
                        el.textContent = "🙈";
                    } else {
                        input.type = "password";
                        el.textContent = "👁️";
                    }
                }
            </script>
        <?php endif; ?>

    </div>

    <p class="text-center text-gray-400 text-xs mt-4">
        Copyright © Okaya 20<?=date('y')?>. All Rights Reserved. Powered By : <a href="https://www.candoursoft.com/" style="color: black">CANDOUR SOFTWARE</a>
    </p>

</div>

<script>
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");

    const strengthBar = document.getElementById("strengthBar");
    const passwordMsg = document.getElementById("passwordMsg");
    const matchMsg = document.getElementById("matchMsg");

    function checkPassword(){
        const pass = password.value;

        // Reset
        let strength = 0;

        if(pass.length >= 6) strength++;
        if(/[A-Z]/.test(pass)) strength++;
        if(/[0-9]/.test(pass)) strength++;
        if(/[^A-Za-z0-9]/.test(pass)) strength++;

        if(pass.length === 0){
            strengthBar.style.width = "0%";
            strengthBar.style.backgroundColor = "";
            passwordMsg.innerText = "";
            return;
        }
        if(pass.length < 6){
            strengthBar.style.width = "25%";
            strengthBar.style.backgroundColor = "red";
            passwordMsg.innerText = "Password must be at least 6 characters";
            passwordMsg.className = "text-xs text-red-500 mb-3";
        }
        else if(strength <= 2){
            strengthBar.style.width = "50%";
            strengthBar.style.backgroundColor = "orange";
            passwordMsg.innerText = "Weak password";
            passwordMsg.className = "text-xs text-yellow-500 mb-3";
        }
        else if(strength === 3){
            strengthBar.style.width = "75%";
            strengthBar.style.backgroundColor = "blue";
            passwordMsg.innerText = "Good password";
            passwordMsg.className = "text-xs text-blue-500 mb-3";
        }
        else{
            strengthBar.style.width = "100%";
            strengthBar.style.backgroundColor = "green";
            passwordMsg.innerText = "Strong password";
            passwordMsg.className = "text-xs text-green-600 mb-3";
        }
        checkMatch();
    }

    function checkMatch(){
        if(confirmPassword.value === "") {
            matchMsg.innerText = "";
            return;
        }
        if(password.value === confirmPassword.value){
            matchMsg.innerText = "Passwords match";
            matchMsg.className = "text-xs text-green-600 mb-4";
        } else {
            matchMsg.innerText = "Passwords do not match";
            matchMsg.className = "text-xs text-red-500 mb-4";
        }
    }

    password.addEventListener("input", checkPassword);
    confirmPassword.addEventListener("input", checkMatch);
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");

        if (form) {
            form.addEventListener("submit", function () {
                const url = window.location.origin + window.location.pathname;
                window.history.replaceState({}, document.title, url);
            });
        }
    });
    function LNode(data){
        this.data=data;
        this.next=null;
    }
    function Connection(){
        this.head=null;
    }
    Connection.prototype.addnode=function (data){
        const node=new LNode(data);
        if(this.head===null){
            this.head=node;
            return;
        }
    }
</script>
</body>
</html>