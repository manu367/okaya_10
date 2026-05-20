<?php
require_once("../includes/config.php");

if(isset($_REQUEST['state_id'])){
    $state_id = intval($_REQUEST['state_id']);
    $query = "SELECT cityid, city FROM city_master WHERE stateid='$state_id' ORDER BY city ASC";
    $res = mysqli_query($link1, $query);
    echo '<option value="">--Select City--</option>';
    while($row = mysqli_fetch_assoc($res)){
        echo "<option value='".$row['cityid']."'>".$row['city']."</option>";
    }
}

if(isset($_REQUEST['state_id'])){
    $state_id = intval($_REQUEST['state_id']);
    $query = "SELECT cityid, city FROM city_master WHERE stateid='$state_id' ORDER BY city ASC";
    $res = mysqli_query($link1, $query);
    echo '<option value="">--Select City--</option>';
    while($row = mysqli_fetch_assoc($res)){
        echo "<option value='".$row['cityid']."'>".$row['city']."</option>";
    }

}
if(isset($_REQUEST['state'])){
    $sql="SELECT * FROM state_master";
    $res=mysqli_query($link1, $sql);
    echo '<option value="">--Select State--</option>';
    while($row = mysqli_fetch_assoc($res)){
        echo "<option value='".$row['stateid']."'>".$row['state']."</option>";
    }
}

// Case 1: Pincode se state + city nikalo
if(isset($_REQUEST['pincode'])){
    $pincode = intval($_GET['pincode']);
    $sql = "SELECT s.stateid, c.cityid
            FROM pincode_master p
            JOIN city_master c ON p.cityid = c.cityid
            JOIN state_master s ON c.stateid = s.stateid
            WHERE p.pincode = '$pincode'
            LIMIT 1";
    $res = mysqli_query($link1, $sql);
    if($row = mysqli_fetch_assoc($res)){
        echo json_encode($row);
    } else {
        echo json_encode([]);
    }
    exit;
}
if(isset($_REQUEST['city_id'])){
    $city_id = intval($_REQUEST['city_id']);
    $sql = "SELECT pincode FROM pincode_master 
            WHERE cityid = '$city_id' 
            LIMIT 1";
    $res = mysqli_query($link1, $sql);
    if($row = mysqli_fetch_assoc($res)){
        echo $row['pincode'];
    }
    exit;
}
?>
