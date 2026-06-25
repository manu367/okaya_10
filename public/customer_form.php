<?php
require_once("../security/dbh.php");

global $link1;

$response = [
    'status' => false,
];

/* ===========================
   FETCH CUSTOMER FEEDBACK
========================= */
if (isset($_REQUEST['customer_form']) && !empty($_REQUEST['customer_form'])) {

    $customer_id = mysqli_real_escape_string($link1, $_REQUEST['customer_form']);

    $query = "SELECT job_no, customer_id, is_submitted 
              FROM csat_feedback 
              WHERE customer_id='$customer_id' 
              LIMIT 1";

    $result = mysqli_query($link1, $query);

    if (!$result) {
        $response['status'] = false;
        $response['msg'] = mysqli_error($link1);

        echo json_encode($response);
        exit();
    }

    if (mysqli_num_rows($result) == 0) {
        $response['status'] = false;
        $response['msg'] = "No Feedback found";

        echo json_encode($response);
        exit();
    }

    $response['status'] = true;
    $response['customer_id'] = $customer_id;
    $response['data'] = mysqli_fetch_assoc($result);

    echo json_encode($response);
    exit();
}


if(isset($_REQUEST['submit'])){

    $jobno                = $_REQUEST['job_no'] ?? '';
    $customer_id          = $_REQUEST['customer_id'] ?? '';
    $customer_name        = $_REQUEST['customer_name'] ?? '';
    $job_card_closed_date = $_REQUEST['job_card_closed_date'] ?? '';
    $dealer_code          = $_REQUEST['dealer_code'] ?? '';
    $rating1              = $_REQUEST['r1'] ?? '';
    $rating2              = $_REQUEST['r2'] ?? '';
    $rating3              = $_REQUEST['r3'] ?? '';
    $comment              = $_REQUEST['comments'] ?? '';

    $insertQuery = "INSERT INTO csat_feedback (
        job_no,
        customer_id,
        customer_name,
        job_card_closed_date,
        dealer_code,
        rating_overall,
        rating_staff_behaviour,
        rating_service_ontime,
        comments,
        is_submitted,
        submitted_at
    ) VALUES (
        '$jobno',
        '$customer_id',
        '$customer_name',
        '$job_card_closed_date',
        '$dealer_code',
        '$rating1',
        '$rating2',
        '$rating3',
        '$comment',
        '1',
        NOW()
    )";

    $insertResult = mysqli_query($link1, $insertQuery);

    if(!$insertResult){

        $response['status'] = false;
        $response['msg'] = mysqli_error($link1);

        echo json_encode($response);
        exit();
    }

    $response['status'] = true;
    $response['msg'] = "Feedback submitted successfully";

    echo json_encode($response);
    exit();
}
?>