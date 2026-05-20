<?php
require_once("../includes/config.php");
header("Content-Type: application/json");

$state = $_GET['state'];

$q = mysqli_query($link1,"SELECT cityid, city FROM city_master WHERE stateid='$state' ORDER BY city");

$rows = [];
while($r = mysqli_fetch_assoc($q)){
    $rows[] = [
        "id"   => $r['cityid'],
        "name" => $r['city']
    ];
}

echo json_encode($rows);
