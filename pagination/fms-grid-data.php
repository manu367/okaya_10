<?php
require_once("../includes/config.php");
$draw   = $_POST['draw'] ?? 1;
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$searchValue = $_POST['search']['value'] ?? "";

$columns = [
    0 => 'fmsname',
    1 => 'details',
];

$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderColumn = $columns[$orderColumnIndex] ?? 'userloginid';
$orderDir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

$where = "";
if($searchValue != ""){
    $searchValue = mysqli_real_escape_string($link1,$searchValue);
    $where = " WHERE 
        fmsname LIKE '%$searchValue%' OR
        details LIKE '%$searchValue%' OR
      ";
}

$totalRes = mysqli_query($link1,"SELECT COUNT(*) c FROM fms_master");
$totalData = mysqli_fetch_assoc($totalRes)['c'];

$filteredRes = mysqli_query($link1,"SELECT COUNT(*) c FROM fms_master $where");
$totalFiltered = mysqli_fetch_assoc($filteredRes)['c'];


$sql = " SELECT * FROM fms_master $where ORDER BY $orderColumn $orderDir LIMIT $start,$length";

$res = mysqli_query($link1,$sql);
$data = [];
$serial = $start + 1;
//current_timestamp()
while($row = mysqli_fetch_assoc($res)){

    if($row['status']==1){
        $status='<span style="color:green;font-weight:bold;">Active</span>';
    }else{
        $status='<span style="color:#b10b0b;font-weight:bold;">Deactive</span>';
    }
    $data[] = [
        $serial++,
        $row['fmsname'],
        $row['details'],
        $row['created_at'],
        $row['updated_at'],
        $status,
        '<a href="add_fms_master.php?op=edit&id='.base64_encode($row['id']).'" class="btn btn-sm btn-primary">View</a>',
    ];
}

echo json_encode([
    "draw"=>intval($draw),
    "recordsTotal"=>intval($totalData),
    "recordsFiltered"=>intval($totalFiltered),
    "data"=>$data
]);
exit;