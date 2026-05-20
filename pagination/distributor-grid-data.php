<?php
require_once("../includes/config.php");

$requestData = $_REQUEST;

$columns = array(
    0 => 'distributorid',
    1 => 'distributorname',
    2 => 'distributorcode',
    3 => 'sap_hanacode',
    4 => 'userid',
    5 => 'type',
    6 => 'brand',
    7 => 'email',
    8 => 'address1',
    9 => 'cityid',
    10 => 'stateid',
    11 => 'countryid',
    12 => 'pincode',
    13 => 'companyid',
    14 => 'phone',
    15 => 'mobile',
    16 => 'gst_no',
    17 => 'status',
    18 => 'updateby',
    19 => 'update_date',
    20 => 'sale_segment'
);

/* TOTAL COUNT */
$sql = "SELECT distributorid FROM distributor_master";
$q = mysqli_query($link1,$sql);
$totalData = mysqli_num_rows($q);
$totalFiltered = $totalData;

/* SEARCH */
$sql = "SELECT * FROM distributor_master WHERE 1";
if(!empty($requestData['search']['value'])){
    $s = $requestData['search']['value'];
    $sql .= " AND (
        distributorname LIKE '$s%' OR distributorcode LIKE '$s%' OR
        email LIKE '$s%' OR phone LIKE '$s%' OR mobile LIKE '$s%'
    )";
}

$q = mysqli_query($link1,$sql);
$totalFiltered = mysqli_num_rows($q);

/* ORDER + LIMIT */
$sql .= " ORDER BY ".$columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']."
          LIMIT ".$requestData['start']." ,".$requestData['length'];

$q = mysqli_query($link1,$sql);

/* DATA */
$data = [];
$i = $requestData['start'] + 1;

while($r=mysqli_fetch_assoc($q)){
    $status = $r['status']=="active"
        ? "<span style='background:#28a745;color:#fff;padding:4px 10px;border-radius:12px;font-size:12px;'>Active</span>"
        : "<span style='background:#dc3545;color:#fff;padding:4px 10px;border-radius:12px;font-size:12px;'>Deactive</span>";


    $edit="<a href='add_distributor.php?op=Edit&id=".$r['distributorid']."' class='btn btn-xs btn-info'>
          <i class='fa fa-pencil'></i> Edit</a>";

    $row=[
        $i++,
        $r['distributorname'],$r['distributorcode'],$r['sap_hanacode'],$r['userid'],
        $r['type'],$r['brand'],$r['email'],
        $r['address1']." ".$r['address2']." ".$r['landmark'],
        $r['cityid'],$r['stateid'],$r['countryid'],$r['pincode'],$r['companyid'],
        $r['phone'],$r['mobile'],$r['gst_no'],$status,
        $r['updateby'],$r['update_date'],$r['sale_segment'],
        $edit
    ];
    $data[]=$row;
}

/* JSON */
echo json_encode([
    "draw" => intval($requestData['draw']),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
]);
