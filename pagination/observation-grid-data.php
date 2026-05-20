<?php
require_once("../includes/config.php");

$requestData = $_REQUEST;

/* STATUS FILTER */
if($_REQUEST['status']=="A"){
    $statusCond = "status='A'";
}else if($_REQUEST['status']=="D"){
    $statusCond = "status='D'";
}else{
    $statusCond = "1"; // ALL
}

$columns = array(
    0 => 'id',
    1 => 'observation',
    2 => 'status'
);

/* TOTAL RECORDS */
$sql = "SELECT id FROM observation_master WHERE $statusCond";
$query = mysqli_query($link1,$sql);
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;

/* SEARCH */
$sql = "SELECT * FROM observation_master WHERE $statusCond";
if(!empty($requestData['search']['value'])){
    $search = $requestData['search']['value'];
    $sql .= " AND (observation LIKE '$search%' OR status LIKE '$search%')";
}

$query = mysqli_query($link1,$sql);
$totalFiltered = mysqli_num_rows($query);

/* ORDER + LIMIT */
$sql .= " ORDER BY ".$columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']."
          LIMIT ".$requestData['start']." ,".$requestData['length']."";

$query = mysqli_query($link1,$sql);

/* DATA */
$data = array();
$i = $requestData['start'] + 1;

while($row = mysqli_fetch_assoc($query)){

    $badge = "<span class='badge badge-".$row['status']."'>".($row['status']=="A"?"Active":"Deactive")."</span>";

    $edit = "<a href='edit_observation.php?op=Edit&id=".$row['id']."'><i class='fa fa-pencil fa-lg faicon'></i></a>";

    $nested = array();
    $nested[] = $i++;
    $nested[] = $row['observation'];
    $nested[] = $badge;
    $nested[] = $edit;

    $data[] = $nested;
}

/* JSON RESPONSE */
echo json_encode(array(
    "draw"            => intval($requestData['draw']),
    "recordsTotal"    => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data"            => $data
));
