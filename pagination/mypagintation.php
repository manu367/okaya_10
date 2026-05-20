<?php
require_once("../includes/config.php");

$draw   = $_POST['draw'] ?? 1;
$start  = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;
$searchValue = $_POST['search']['value'] ?? "";

$columns = [
    0 => 'userloginid',
    1 => 'userloginid',
    2 => 'locusername',
    3 => 'emailid',
    4 => 'contactmo',
    5 => 'statusid',
    6 => 'mapped_bsi',
    7 => 'mapped_rm',
    8 => 'spare_location_code'
];
$orderColumnIndex = $_POST['order'][0]['column'] ?? 0;
$orderColumn = $columns[$orderColumnIndex] ?? 'userloginid';
$orderDir = ($_POST['order'][0]['dir'] ?? 'asc') === 'desc' ? 'DESC' : 'ASC';

$where = "";
if($searchValue != ""){
    $searchValue = mysqli_real_escape_string($link1,$searchValue);
    $where = " WHERE 
        userloginid LIKE '%$searchValue%' OR
        locusername LIKE '%$searchValue%' OR
        emailid LIKE '%$searchValue%' OR
        contactmo LIKE '%$searchValue%'
    ";
}

$totalRes = mysqli_query($link1,"SELECT COUNT(*) c FROM locationuser_master");
$totalData = mysqli_fetch_assoc($totalRes)['c'];

$filteredRes = mysqli_query($link1,"SELECT COUNT(*) c FROM locationuser_master $where");
$totalFiltered = mysqli_fetch_assoc($filteredRes)['c'];


$sql = "
SELECT userloginid, locusername, contactmo, emailid, statusid,
       mapped_bsi, mapped_rm, spare_location_code
FROM locationuser_master
$where
ORDER BY $orderColumn $orderDir
LIMIT $start,$length
";

$res = mysqli_query($link1,$sql);
$data = [];
$serial = $start + 1;

while($row = mysqli_fetch_assoc($res)){
    if($row['statusid']==1){
        $status='<span style="color:green;font-weight:bold;">Active</span>';
    }else{
        $status='<span style="color:#b10b0b;font-weight:bold;">Deactive</span>';
    }
    $data[] = [
        $serial++,
        "<span data-tagName='loginid'>".$row['userloginid']."</span>",
        "<span data-tagName='username'>".$row['locusername']."</span>",
        "<span data-tagName='email'>".$row['emailid']."</span>",
        $row['contactmo'],
        $status,
        $row['mapped_bsi'],
        $row['mapped_rm'],
        $row['spare_location_code'],
        '<a href="enginner_master_op.php?op=edit&id='.$row['userloginid'].'" class="btn btn-sm btn-primary">Edit</a>',
    ];
}

echo json_encode([
    "draw"=>intval($draw),
    "recordsTotal"=>intval($totalData),
    "recordsFiltered"=>intval($totalFiltered),
    "data"=>$data
]);
exit;