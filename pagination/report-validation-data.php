<?php
require_once("../includes/config.php");
require_once ("../ExcelExportAPI/Classes/PHPExcel.php");
require_once ("../ExcelExportAPI/Classes/PHPExcel/IOFactory.php");

if (isset($_GET['q']) && $_GET['q'] === 'changes') {
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "success",
        "message" => [1,2,3,4,5],
    ]);
    exit;
}

if (!isset($_FILES['file'])) {
    exit("<span style='color:red'>No file received</span>");
}

//function processData(Validation $valida,$data)
//{
//    return $valida->processData($data);
//}

$fileTmp = $_FILES['file']['tmp_name'];

$objPHPExcel = PHPExcel_IOFactory::load($fileTmp);
$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

echo "<table class='table table-bordered' id='editableTable'>";
echo "<thead>
        <tr>
            <th>#</th>
            <th>Model No</th>
            <th>Battery Type</th>
            <th>Status</th>
        </tr>
      </thead>
      <tbody>";

$i = 1;
$k=0;
foreach ($sheetData as $row) {

    if ($i == 1) { $i++; continue; } // header skip

    $model  = trim($row['A']);
    $type   = trim($row['B']);
//    mysqli_query()

    if ($model == "") continue;
    $style = ($model === 'M02951')
        ? "style='background-color:red;color:white;border-radius:20px;padding:5px;'"
        : "";
    $failReason = ($model === 'M02951')
        ? "Model already exists"
        : "Validation failed";

    $k++;
    $editable=($model==='M02951')?false:true;
    echo "<tr class='error-row' data-error-index='{$i}'>
    <td>{$k}</td>
    <td contenteditable='{$editable}' class='editable model'><span {$style}>{$model}</span></td>
    <td class='editable type'>{$type}</td>
    <td class='status text-danger'>
        failed
        <span class='info-icon' data-tooltip='{$failReason}'>ⓘ</span>
    </td>
    </tr>";


    $i++;
}

echo "</tbody></table>";

echo "<div style='text-align:right;margin-top:10px'>
        <button class='btn btn-success' onclick='collectTableData()'>
            Save Changes
        </button>
      </div>";


