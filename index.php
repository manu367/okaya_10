<?php
$page_type = "insecure";
require_once("security/backend.php");
$arr_browsers = ["Firefox", "Chrome", "Safari", "Opera", "MSIE", "Trident", "Edge"];

$agent = $_SERVER['HTTP_USER_AGENT'];

$user_browser = '';
foreach ($arr_browsers as $browser) {
    if (strpos($agent, $browser) !== false) {
        $user_browser = $browser;
        break;
    }
}
switch ($user_browser) {
    case 'MSIE':
        $user_browser = 'Internet Explorer';
        break;

    case 'Trident':
        $user_browser = 'Internet Explorer';
        break;

    case 'Edge':
        $user_browser = 'Internet Explorer';
        break;
}


require_once("includes/common_function.php");
session_start();
/// check if session is already there then same account should be open
if($_SESSION['userid']){
    if($_SESSION['id_type']=="admin"){
        header("Location:admin/home2.php");
        exit;
    }else if($_SESSION['id_type']=="WH"){
        header("Location:wh/home2.php");
        exit;
    }else{
        header("Location:asp/home2.php");
        exit;
    }
}
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>CRM :: Support System</title>
        <link rel="shortcut icon" href="images/titleimg.png" type="image/png">

        <!-- Tailwind -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="./js/jquery.min.js"></script>
        <script>
            function chk_data() {
                const user = document.getElementById("userid");
                const pwd = document.getElementById("pwd");

                if (user.value.trim() === "") {
                    alert("Please enter your User Id.");
                    user.focus();
                    return false;
                }
                if (pwd.value.trim() === "") {
                    alert("Please enter your Password.");
                    pwd.focus();
                    return false;
                }
                return true;
            }

            function validateEmail(el) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const error = document.getElementById("emailError");

                if (!emailRegex.test(el.value)) {
                    error.classList.remove("hidden");
                    el.classList.add("border-red-500");
                } else {
                    error.classList.add("hidden");
                    el.classList.remove("border-red-500");
                }
            }
        </script>
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

    <?php
    if (isset($_REQUEST['msg']) ){
        $msg = htmlspecialchars($_REQUEST['msg']);
        ?>
        <div id="errorPopup" class="toast" style="background-color:green">
            <span class="icon">⚠️</span>
            <span class="message"><?= htmlspecialchars($msg) ?></span>
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

    <body class="min-h-screen bg-white flex items-center justify-center font-sans text-gray-800"
          style="background-image: url('https://care.okaya.in/images/okaya_bg.jpg');background-repeat: no-repeat;background-size: cover;"
    >

    <!-- Login Card -->
    <div
            class="w-full max-w-md bg-white border border-gray-200 rounded-xl
           shadow-lg hover:shadow-2xl
           transition-all duration-300 ease-out
           transform hover:-translate-y-1 xl:ml-[700px] p-8" >

        <!-- Logo -->
        <div class="text-center mb-6">
            <img src="images/canent.png" class="mx-auto w-48">
        </div>

        <!-- PHP MESSAGE (UNCHANGED) -->
        <?php
        if(isset($_SESSION["logres"]["msg"])) {
            $t_color = (isset($_SESSION["logres"]["status"]) && $_SESSION["logres"]["status"] == "success")
                    ? 'text-green-700' : 'text-red-600';

            echo '<div class="mb-4 p-3 rounded-lg bg-gray-100 '.$t_color.' text-sm text-center">'
                    .$_SESSION["logres"]["msg"].'</div>';

            unset($_SESSION["logres"]["msg"]);
        }
        unset($_SESSION["logres"], $_SESSION["otp"]);
        ?>

        <!-- Form -->
        <form id="login_form" name="login_form" method="post"
              action="verify.php" onsubmit="return chk_data()" class="space-y-5">

            <!-- Email -->
            <div>
                <input type="text"
                       name="userid"
                       id="userid"
                       placeholder="Email Address"
                       onchange=""
                       class="w-full px-4 py-3 rounded-md border border-gray-300
                      focus:outline-none focus:ring-1 focus:ring-gray-400
                      transition">
                <p id="emailError" class="hidden text-red-500 text-xs mt-1">
                    Invalid email format
                </p>
            </div>

            <!-- Password -->
            <div>
                <input type="password"
                       name="pwd"
                       id="pwd"
                       placeholder="Password"
                       class="w-full px-4 py-3 rounded-md border border-gray-300
                      focus:outline-none focus:ring-1 focus:ring-gray-400
                      transition">
            </div>
            <div class="text-right">
                <a href="forget-password.php" style="color: blue;">Forget Password</a>
            </div>

            <!-- Button -->
            <button type="submit"
                    class="w-full bg-gray-900 text-white py-3 rounded-md
                     hover:bg-gray-800
                     transition duration-200">
                Sign In
            </button>



            <!-- Error -->
            <div class="text-center text-red-600 text-sm">
                <?php echo errorMsg($_REQUEST['msg']); ?>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <div class="absolute bottom-4 text-gray-500 text-xs text-center w-full">
        © Okaya 2025 · Powered by
        <a href="http://www.candoursoft.com/" target="_blank"
           class="text-gray-700 hover:underline">
            Candour Software
        </a>
    </div>
    </body>
    </html>

<?php //}?>