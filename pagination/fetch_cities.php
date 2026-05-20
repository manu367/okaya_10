<?php
require_once("../includes/config.php");

if(isset($_POST['state_id'])){
    $state_id = intval($_POST['state_id']); // sanitize input
    $query = "SELECT cityid, city FROM city_master WHERE stateid='$state_id' ORDER BY city ASC";
    $res = mysqli_query($link1, $query);

    echo '<option value="">--Select City--</option>';
    while($row = mysqli_fetch_assoc($res)){
        echo "<option value='".htmlspecialchars($row['cityid'])."'>".htmlspecialchars($row['city'])."</option>";
    }
}

if(isset($_GET['state_id'])){
    $state_id = intval($_POST['state_id']); // sanitize input
    $query = "SELECT cityid, city FROM city_master WHERE stateid='$state_id' ORDER BY city ASC";
    $res = mysqli_query($link1, $query);

    echo '<option value="">--Select City--</option>';
    while($row = mysqli_fetch_assoc($res)){
        echo "<option value='".htmlspecialchars($row['cityid'])."'>".htmlspecialchars($row['city'])."</option>";
    }
}
?>
