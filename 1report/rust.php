<?php
require_once("../includes/config.php");
if(isset($_REQUEST['manu'])){
    var_dump("SDcsc");exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CRM:: Support System</title>
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
</head>
<body>
<div id="customLoader">
    <div class="spinner"></div>
</div>
<div class="container-fluid">
    <div class="row content"cddccssoo7>
        <?php
        include("../includes/leftnav2.php");
        ?>
        <div class="col-sm-9">
            <h2 align="center"><i class="fa fa-users"></i> Add Enginner</h2><br/><br/>
            <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
                <form  name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                    <input type="hidden" name="csrf_token"
                           value="48c4bd4ec7a0ebd454c8e5b886ecff1047e09b389e1a90825cfe5765ce1bb4c6">

                    <!--                    userid and username -> read only -->
                    <div class="form-group">
                        <div class="col-md-6">
                            <label for="user_id" class="col-md-6 control-label">
                                User ID<span class="red_small">*</span>
                            </label>
                            <div class="col-md-6">
                                <input name="user_id" type="text" id="user_id"
                                       value=""
                                       class="form-control"
                                >

                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="col-md-6 control-label">User Name<span class="red_small">*</span></label>
                            <div class="col-md-6">
                                <input name="username" id="username" type="text" autocomplete="off"
                                       value=""
                                       class="form-control"
                                >
                            </div>
                        </div>
                    </div>
                    <!--                    password id and mobile no-->
                    <div class="form-group">
                        <div class="col-md-6"><label class="col-md-6 control-label">Password</label>
                            <div class="col-md-6">
                                <input name="password" type="text"
                                       value=""
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
                                       value=""
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
                                       value=""
                                       type="email" class="form-control"  id="email" placeholder="demo@gmail.com">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="textarea" class="col-md-6 control-label">Address<span class="red_small">*</span></label>
                            <div class="col-md-6">
                                <textarea name="address" autocomplete="off" id="textarea" class="form-control"></textarea>
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
                                       value=""
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
                                    <option value="EMP000709" >Ajay Kumar Shaw</option><option value="EMP001742" >Vinit Kumar</option><option value="EMP000265" >Ankush Jyoti Sharma</option><option value="EMP000328" >Mukesh Kumar</option><option value="EMP000574" >Sunil Kumar</option><option value="EMP000576" >Utkarsh Rai</option><option value="EMP000580" >Bagish Kumar Tripathi</option><option value="EMP000629" >Amit Kumar Sinha</option><option value="EMP000714" >Sanjay Rautela</option><option value="EMP000766" >Sheo Kumar Singh</option><option value="EMP000767" >Sayyed Hasan</option><option value="EMP000827" >Saumya Ranjan Swain</option><option value="EMP001064" >Mahesh A Surve</option><option value="EMP001103" >Atul Sneh Pandey</option><option value="EMP001226" >Banshidhar Biswal</option><option value="EMP001278" >Debjyoti Chakraborty</option><option value="EMP001282" >Lokesh Kumar</option><option value="EMP001287" >Jitendra Kumar Singh</option><option value="EMP001736" >Ditu Pal</option><option value="EMP001743" >Shailesh Choudhary</option><option value="EMP001772" >Suresh Prajapati</option><option value="EMP000946" >R Logesh</option><option value="EMP000547" >Avadhesh Kumar Pal</option><option value="EMP001521" >Chandrakant Joshi</option><option value="EMP000629" >Kaushal Kumar</option><option value="EMP001736" >Surajit Bachar</option><option value="EMP000767" >Vikash Singh</option>                                </select>
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
                                    <option value="1" >Active</option>
                                    <option value="0" selected>Deactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="col-md-6 control-label">Mapped RSI</label>
                            <div class="col-md-6">
                                <select name="mapped_rm" id="mapped_rm" class="form-control select-multiple" required>
                                    <option value="">--Select BSI--</option>
                                    <option value="EMP000528" >Amar Singh</option><option value="EMP0001156" >Munesh Kumar</option><option value="EMP001372" >Indranil Maji</option><option value="EMP000992" >P Saravanan</option><option value="EMP001374" >Umesh Prajapati</option><option value="EMP001653" >Abhishek Kumar Tripathi</option><option value="EMP001775" >Pardeep Pahwaal</option>                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="is_edit" value="0">

                    <div class="text-center mt-5">
                        <button class="btn btn-primary">
                            <span id="operation_name">Add</span>
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

<footer class="container-fluid" style="width:100%;">
    <p align="center">Copyright © Okaya 2025. All Rights Reserved. Powered By : <a href="http://www.candoursoft.com/" target="_blank">CANDOUR SOFTWARE</a></p>
</footer>
</body>
</html>
