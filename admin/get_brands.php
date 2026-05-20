<?php
require_once("../includes/config.php");

if(isset($_GET['model_ids'])){
    $model_ids = $_GET['model_ids']; // array of model_ids
//    var_dump($model_ids);exit();
    $model_ids = array_map(function($id){ return mysqli_real_escape_string($link1,$id); }, $model_ids);
    $ids_str = "'" . implode("','",$model_ids) . "'";

    $query = "SELECT mm.model_id,bm.brand_id,bm.brand FROM `model_master` mm 
    LEFT JOIN brand_master bm ON mm.brand_id=bm.brand_id WHERE mm.model_id=$model_ids";

//    var_dump($query); exit();
    $res = mysqli_query($link1, $query);

    while($row = mysqli_fetch_assoc($res)){
        echo '<option value="'.$row['brand_id'].'">'.$row['brand'].'</option>';
    }
}

if(isset($_GET['brand_id'])){
    $brandid=$_GET['brand_id'];
    if($brandid==""){
        $brandid=1;
    }
    $query="SELECT * FROM model_master WHERE brand_id=1";
    $model_m = mysqli_query($link1, $query);
    while ($row = mysqli_fetch_assoc($model_m)){
        echo '<option value="'.$row['model_id'].'">'.$row['model'].' | '.$row['modelcode'].'</option>';
    }
}
?>
