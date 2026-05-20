<?php
require_once("../includes/config.php");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= siteTitle ?></title>
    <script src="../js/jquery.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>

        .box{
            background: linear-gradient(180deg,#020617,#020617);
            border: 1px solid #1e293b;
            box-shadow: 0 12px 40px rgba(0,0,0,.35);
            padding: 35px 40px;
            max-width: 420px;
            border-radius: 12px;
            text-align: center;
            margin:60px auto;
        }

        .box::before{
            content:"";
            position:absolute;
            inset:0;
            border-radius:12px;
            pointer-events:none;
            box-shadow: inset 0 0 0 1px rgba(56,189,248,.15);
        }
        .btn{
            display:inline-block;
            text-decoration:none;
            padding:11px 30px;
            border-radius:6px;
            border:1px solid #38bdf8;
            color:#38bdf8;
            font-weight:600;
            letter-spacing:1px;
            transition:.2s;
        }
        .box h1,small{
            color: white;
        }
        .box p{
            color:#f87171;
            text-shadow: 0 0 4px rgba(248,113,113,.4);
            animation: errorPulse 2.8s ease-in-out infinite;
        }

        @keyframes errorPulse {
            0% {
                opacity: .85;
                text-shadow: 0 0 4px rgba(248,113,113,.4);
            }
            50% {
                opacity: 1;
                text-shadow: 0 0 12px rgba(248,113,113,.9),
                0 0 20px rgba(248,113,113,.6);
            }
            100% {
                opacity: .85;
                text-shadow: 0 0 4px rgba(248,113,113,.4);
            }
        }


        .btn:hover{
            background:#38bdf8;
            color:#020617;
        }

    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
            <div class="box">
                <h1>Your request could not be completed</h1>

                <p>
                    <?= htmlspecialchars(
                        $message
                        ?? "It looks like something in your request didn’t meet our system rules. For your security, this action has been blocked."
                    ) ?>
                </p>

                <small>Please return to the home page and try again.</small><br><br>
                <a href="/Okaya" class="btn mt-3">OK</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
