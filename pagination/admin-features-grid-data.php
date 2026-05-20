<?php
require_once("../includes/config.php");

$draw = $_GET['draw'];
$start = $_GET['start'];
$length = $_GET['length'];
$searchValue = $_GET['search']['value'];

$columns = [
    0 => 'id',
    2 => 'username',
    3 => 'name',
    4 => 'email',
    5 => 'contact_no',
    6 => 'status',
];

$orderColumn = $columns[$_GET['order'][0]['column']];
$orderDir = $_GET['order'][0]['dir'];
$where = "";

if(!empty($searchValue)){
    $where = " WHERE 
        username LIKE '%$searchValue%' OR
        name LIKE '%$searchValue%' OR
        phone LIKE '%$searchValue%' OR
        emailid LIKE '%$searchValue%'
    ";
}

$totalQuery = mysqli_query($link1, "SELECT COUNT(*) as total FROM admin_users");
$totalData = mysqli_fetch_assoc($totalQuery)['total'];
$filteredQuery = mysqli_query($link1, "SELECT COUNT(*) as total FROM admin_users $where");
$totalFiltered = mysqli_fetch_assoc($filteredQuery)['total'];

$query = "SELECT au.*,au.name as admin_name,dm.dname FROM admin_users as au  LEFT JOIN designation_master AS dm ON au.designation_id = dm.id"
   ."$where ORDER BY $orderColumn $orderDir LIMIT $start, $length";

$result = mysqli_query($link1, $query);

$data = [];
$serial = $start + 1;

while($row = mysqli_fetch_assoc($result)) {
    $nestedData = [];
    $nestedData['id'] = $serial++;
    $nestedData['loginid'] = $row['sapid'];
    $nestedData['username'] = $row['username'];
    $nestedData['name'] = $row['admin_name'];
    $nestedData['email'] = $row['emailid'];
    $nestedData['contact_no'] = $row['phone'];
    $nestedData['status'] = $row['dname'];
    $nestedData['action'] = '<a href="admin_featres_add_init.php?op=' . base64_encode('edit') . '&uname=' . base64_encode($row['username']) . '" class="btn btn-sm btn-primary">Edit</a>';
    $data[] = $nestedData;
}

$response = [
    "draw" => intval($draw),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
];

echo json_encode($response);
exit;