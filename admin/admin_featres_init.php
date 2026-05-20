<?php
require_once("../includes/config.php");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="../images/titleimg.png" type="image/png">
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/jquery.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
    <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
    <title><?=siteTitle?></title>
    <style>
        #customLoader {
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.11);
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
    <script>
        $(document).ready(function() {
            var table = $('#myacc-users-grid').DataTable({
                "processing": false,
                "serverSide": true,
                "ajax": {
                    "url": "../pagination/admin-features-grid-data.php",
                    "type": "GET",
                    "beforeSend": function() {
                        $("#customLoader").fadeIn(200);
                    },
                    "complete": function() {
                        $("#customLoader").fadeOut(200);
                    },
                    "error": function() {
                        $("#customLoader").hide();
                        //alert("Something went wrong while loading data.");
                    }
                },
                "columns": [
                    { "data": "id" },
                    { "data": "loginid" },
                    { "data": "username" },
                    { "data": "name" },
                    { "data": "email" },
                    { "data": "contact_no" },
                    { "data": "status" },
                    { "data": "action", "orderable": false }
                ]
            });
        });

    </script>

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
        <div class="<?=$screenwidth?> tab-pane fade in active" id="home">
            <h2 align="center"><i class="fa fa-users"></i> Users Master</h2>

            <!--            when message is availbale then showing the message-->
            <?php if($_REQUEST['msg']){?><br>
                <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
                </div>
            <?php }?>

            <!--            pid and hid hidden form-->
            <form class="form-horizontal" role="form" name="form1" action="" method="get">
                <div class="form-group">
                    <div class="col-md-6"><label class="col-md-5 control-label"></label>
                        <div class="col-md-5">
                            <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                            <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="col-md-5 control-label"></label>
                        <div class="col-md-5" align="left">

                        </div>
                    </div>
                </div><!--close form group-->
            </form>

            <div style="text-align: end">
                <button
                    class="btn btn-primary"
                    onclick="window.location.href='admin_featres_add_init.php?op=<?= base64_encode('add') ?>&pid=<?= $_REQUEST['pid'] ?? '' ?>&hid=<?= $_REQUEST['hid'] ?? '' ?>'">
                    Add Admin User
                </button>

            </div>
            <!--            main form for showing data-->
            <form class="form-horizontal" role="form">
                &nbsp;&nbsp;
                <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>

                    <table  width="100%" id="myacc-users-grid" class="display" align="center" cellpadding="4" cellspacing="0" border="1">
                        <thead>
                        <tr class="<?=$tableheadcolor?>">
                            <th>S.No</th>
                            <th>Login ID</th>
                            <th>UserName</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>View/Edit</th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <!--</div>-->
            </form>
        </div>

    </div>
</div>


<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>