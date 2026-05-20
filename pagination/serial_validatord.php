<?php
require_once("../includes/config.php");
header("Content-type: application/json; charset=utf-8");
$serial_no = isset($_GET['serial_no']) ? trim($_GET['serial_no']) : '';
if ($serial_no === '') {
    echo json_encode([
        'status' => false,
        'message' => 'Serial number missing'
    ]);
    exit;
}
$sql = "SELECT dop, repl_dealer_serial FROM jobsheet_data WHERE repl_dealer_serial = '$serial_no'";
$result = mysqli_query($link1, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode([
        'status' => true,
        'message' => $row
    ]);
}
else {
    echo json_encode([
        'status' => false,
        'message' => 'No data found'
    ]);
}

mail("","","","","");