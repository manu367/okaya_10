<?php
require_once("../includes/config.php");

$arrstatus = ["A"=>"Active","D"=>"Deactive"];

$id = isset($_GET['id']) ? $_GET['id'] : '';
$editData = null;

/* LOAD EDIT DATA */
if($id!=""){
    $q = mysqli_query($link1,"SELECT * FROM observation_master WHERE id='$id'");
    $editData = mysqli_fetch_assoc($q);
}

/* SAVE */
if($_SERVER['REQUEST_METHOD']=="POST"){

    $name   = mysqli_real_escape_string($link1,$_POST['observername']);
    $status = mysqli_real_escape_string($link1,$_POST['status']);

    $prdstr="";
    if(!empty($_POST['product'])){
        $prdstr = implode(",",$_POST['product']);
    }

    if($_POST['mode']=="ADD"){
        mysqli_query($link1,"INSERT INTO observation_master SET
            observation='$name',
            status='$status',
            mapped_product='$prdstr',
            update_date=NOW()
        ");
    }

    if($_POST['mode']=="EDIT"){
        $uid = $_POST['uid'];
        mysqli_query($link1,"UPDATE observation_master SET
            observation='$name',
            status='$status',
            mapped_product='$prdstr',
            update_date=NOW()
            WHERE id='$uid'
        ");
    }

    header("location:observer_master.php");
    exit;
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
    <script src="../js/jquery.validate.js"></script>
</head>

<body>
<div class="container-fluid">
    <div class="row content">
        <?php include("../includes/leftnav2.php"); ?>
        <div class="<?=$screenwidth?>">

            <h2 class="text-center"><?= $id!="" ? "Edit" : "Add" ?> Primary Observer</h2>

            <form method="post">

                <input type="hidden" name="mode" value="<?= $id!="" ? "EDIT":"ADD" ?>">
                <?php if($id!=""){ ?>
                    <input type="hidden" name="uid" value="<?=$id?>">
                <?php } ?>

                <div class="row">
                    <div class="col-md-6">
                        <label>Observer Name</label>
                        <input type="text" name="observername" value="<?=@$editData['observation']?>" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <?php foreach($arrstatus as $k=>$v){ ?>
                                <option value="<?=$k?>" <?=(@$editData['status']==$k)?"selected":""?>><?=$v?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <br>

                <label>Map Product</label>
                <table class="table table-bordered">
                    <?php
                    $mapped = ($editData) ? explode(",",$editData['mapped_product']) : [];
                    $map = array_flip($mapped);

                    $rs = mysqli_query($link1,"SELECT product_id,product_name FROM product_master WHERE status='1'");
                    $i=1;
                    while($r=mysqli_fetch_assoc($rs)){
                        if($i%2==1) echo "<tr>";
                        $chk = isset($map[$r['product_id']]) ? "checked" : "";
                        echo "<td><input type='checkbox' name='product[]' value='{$r['product_id']}' $chk> {$r['product_name']}</td>";
                        if($i%2==0) echo "</tr>";
                        $i++;
                    }
                    ?>
                </table>

                <div class="text-center">
                    <button class="btn btn-primary"><?= $id!="" ? "Update":"Save" ?></button>
                    <button class="btn btn-primary" onclick="window.location.href='observer_master.php'">back</button>
                </div>

            </form>
        </div>
    </div>
</div>
</body>
</html>
