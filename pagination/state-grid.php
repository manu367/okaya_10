<?php
require_once("../includes/config.php");
header("Content-Type: application/json");


$q = mysqli_query($link1,"SELECT stateid, state FROM state_master ORDER BY state");

$rows = [];
while($r = mysqli_fetch_assoc($q)){
    $rows[] = [
        "id"   => $r['stateid'],
        "name" => $r['state']
    ];
}

echo json_encode($rows);
