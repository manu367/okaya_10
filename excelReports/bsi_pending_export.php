<?php
/**
 * BSI Pendency Dashboard — Excel Export
 */

require_once("../includes/config.php");
require_once("../ExcelExportAPI/Classes/PHPExcel.php");

global $link1;

// 1. Condition collect karo
$condition = [];
$condition['date_range']  = $_REQUEST['date_range']  ?? '';
$condition['zone']        = $_REQUEST['zone']        ?? '';
$condition['state']       = $_REQUEST['state']       ?? '';
$condition['bsi']         = $_REQUEST['bsi']         ?? '';
$condition['enginner']    = $_REQUEST['enginner']    ?? '';
$condition['segment']     = $_REQUEST['segment']     ?? '';
$condition['sub_segment'] = $_REQUEST['sub_segment'] ?? '';


class DashoardLaoder_Export
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function bsiLoader($condition = [])
    {
        $data = [];
        $bsiCondition = "";

        if (!empty($condition['bsi'])) {
            $bsi = mysqli_real_escape_string($this->connection, $condition['bsi']);
            $bsiCondition .= " AND au.sapid = '{$bsi}' ";
        }
        if (!empty($condition['zone'])) {
            $zone = (int)$condition['zone'];
            $bsiCondition .= " AND sm.zoneid= '{$zone}' ";
        }
        if (!empty($condition['state'])) {
            $state = (int)$condition['state'];
            $bsiCondition .= " AND sm.stateid= '{$state}' ";
        }

        $allBSI = $this->getAllBSI($bsiCondition);
        if (empty($allBSI)) return [];

        foreach ($allBSI as $bsi) {
            $bsiId   = $bsi['sapid'];
            $bsiName = $bsi['name'];
            $bsiUser = $bsi['username'];
            $zone    = $this->getZone($bsiUser, $condition);

            // FIX: Zone/State se engineer filter nahi — sirf engineer filter pass karo
            $engineers    = $this->fetchEngineers($bsiId, $condition);
            $engineerRows = [];
            $bsiTotals    = $this->emptyBucket();

            foreach ($engineers as $eng) {
                $engId   = $eng['userloginid'];
                $engName = $eng['locusername'];

                // FIX: Job fetch ke liye sirf ye conditions pass karo, zone/state nahi
                $jobCondition = [
                    'date_range'  => $condition['date_range']  ?? '',
                    'segment'     => $condition['segment']     ?? '',
                    'sub_segment' => $condition['sub_segment'] ?? '',
                    'enginner'    => $condition['enginner']    ?? '',
                ];

                $jobs = $this->fetchJobs($engId, $jobCondition);
                if (empty($jobs)) continue;

                $engData = $this->emptyBucket();

                foreach ($jobs as $job) {
                    $status = $job['status'];
                    $aging  = (int)$job['aging_days'];
                    $bucket = $this->getBucket($aging);
                    if ($bucket === false) continue;

                    if ($status == '2')      { $engData['assigned'][$bucket]++;    $engData['assigned_total']++; }
                    elseif ($status == '3')  { $engData['pna'][$bucket]++;         $engData['pna_total']++; }
                    elseif ($status == '7')  { $engData['wip'][$bucket]++;         $engData['wip_total']++; }
                    elseif ($status == '81') { $engData['replacement'][$bucket]++; $engData['replacement_total']++; }

                    $engData['grand_total']++;
                }

                $this->merge($bsiTotals, $engData);
                $engineerRows[] = ['id' => $engId, 'name' => $engName, 'data' => $engData];
            }

            $data[] = [
                'bsi_id'    => $bsiId,
                'bsi_name'  => $bsiName,
                'bsi_zone'  => $zone ?? '',
                'engineers' => $engineerRows,
                'totals'    => $bsiTotals,
            ];
        }

        return $data;
    }

    private function getAllBSI($condition = "")
    {
        $sql = "
            SELECT au.sapid, au.username, au.name, au.status 
            FROM access_region ar 
            LEFT JOIN admin_users au ON ar.userid = au.username 
            LEFT JOIN state_master sm ON ar.stateid = sm.stateid 
            WHERE au.designation_id='45' 
              AND au.status='1' 
              AND ar.status='Y' 
              {$condition}
            GROUP BY au.sapid
        ";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return [];
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
        return $rows;
    }

    private function getZone($username, $condition = [])
    {
        $where = " WHERE ar.userid = '" . mysqli_real_escape_string($this->connection, $username) . "' AND ar.status='Y'";
        if (!empty($condition['zone']))  $where .= " AND zm.zoneid  = '" . (int)$condition['zone']  . "' ";
        if (!empty($condition['state'])) $where .= " AND sm.stateid = '" . (int)$condition['state'] . "' ";

        $sql    = "SELECT zm.zonename FROM access_region ar
                   LEFT JOIN state_master sm ON ar.stateid = sm.stateid
                   LEFT JOIN zone_master  zm ON sm.zoneid  = zm.zoneid
                   {$where} LIMIT 1";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return '';
        $row = mysqli_fetch_assoc($result);
        return $row['zonename'] ?? '';
    }

    private function fetchEngineers($bsiId, $condition = [])
    {
        // FIX: Zone/State filter hata diya — DashoardLaoder_1 ki tarah sirf bsiId + engineer filter
        $bsiId = mysqli_real_escape_string($this->connection, $bsiId);
        $where = " WHERE lum.mapped_bsi = '{$bsiId}' ";

        if (!empty($condition['enginner'])) {
            $e     = mysqli_real_escape_string($this->connection, $condition['enginner']);
            $where .= " AND lum.userloginid = '{$e}' ";
        }

        $sql    = "SELECT lum.locusername, lum.userloginid FROM locationuser_master lum {$where}";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return [];
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
        return $rows;
    }

    private function fetchJobs($engId, $condition = [])
    {
        // FIX: Zone/State filter hata diya — BSI/Engineer already filter ho chuke hain upar se
        $engId = mysqli_real_escape_string($this->connection, $engId);
        $where = "";

        if (!empty($condition['date_range'])) {
            $where .= " AND jd.open_date >= NOW() - INTERVAL " . (int)$condition['date_range'] . " DAY ";
        }
        if (!empty($condition['enginner'])) {
            $e     = mysqli_real_escape_string($this->connection, $condition['enginner']);
            $where .= " AND jd.eng_id = '{$e}' ";
        }
        if (!empty($condition['segment'])) {
            $seg = (string)$condition['segment'];
            if ($seg === '1')     $where .= " AND jd.product_id IN ('1') ";
            elseif ($seg === '2') $where .= " AND jd.product_id NOT IN ('1','6','10','11','12','14') ";
            elseif ($seg === '3') $where .= " AND jd.product_id IN ('6','10','11','12') ";
        }
        if (!empty($condition['sub_segment'])) {
            $where .= " AND jd.product_id = '" . (int)$condition['sub_segment'] . "' ";
        }

        // FIX: Status '0' add kiya — DashoardLaoder_1 ki tarah
        $sql    = "SELECT jd.*, DATEDIFF(NOW(), jd.open_date) as aging_days
                   FROM jobsheet_data jd
                   WHERE jd.eng_id = '{$engId}' 
                     AND jd.status IN ('0','1','2','3','7','81') 
                     {$where}";
        $result = mysqli_query($this->connection, $sql);
        if (!$result) return [];
        $rows = [];
        while ($row = mysqli_fetch_assoc($result)) $rows[] = $row;
        return $rows;
    }

    public function emptyBucket()
    {
        $b = ['b1' => 0, 'b2' => 0, 'b3' => 0, 'b4' => 0, 'b5' => 0, 'b6' => 0];
        return [
            'assigned'          => $b, 'replacement'       => $b,
            'wip'               => $b, 'pna'               => $b,
            'assigned_total'    => 0,  'replacement_total' => 0,
            'wip_total'         => 0,  'pna_total'         => 0,
            'grand_total'       => 0,
        ];
    }

    public function getBucket($aging)
    {
        if ($aging < 0)                   return false;
        if ($aging <= 1)                  return 'b1';
        if ($aging == 2)                  return 'b2';
        if ($aging >= 3  && $aging <= 5)  return 'b3';
        if ($aging >= 6  && $aging <= 10) return 'b4';
        if ($aging >= 11 && $aging <= 15) return 'b5';
        if ($aging >= 16)                 return 'b6';
        return false;
    }

    public function merge(&$target, $source)
    {
        foreach (['assigned', 'replacement', 'wip', 'pna'] as $type) {
            foreach (['b1','b2','b3','b4','b5','b6'] as $b) {
                $target[$type][$b] += ($source[$type][$b] ?? 0);
            }
        }
        $target['assigned_total']    += $source['assigned_total'];
        $target['replacement_total'] += $source['replacement_total'];
        $target['wip_total']         += $source['wip_total'];
        $target['pna_total']         += $source['pna_total'];
        $target['grand_total']       += $source['grand_total'];
    }
}

// ── 3. Data fetch karo 
$loader = new DashoardLaoder_Export($link1);
$data   = $loader->bsiLoader($condition);

$excel = new PHPExcel();
$excel->getProperties()
    ->setCreator("Candour Software")
    ->setTitle("BSI Pendency Report")
    ->setSubject("Zone & BSI Wise Pendency Summary");

$sheet = $excel->getActiveSheet();
$sheet->setTitle('BSI Pendency');


function cellBg($sheet, $range, $hexColor)
{
    $sheet->getStyle($range)
        ->getFill()
        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
        ->getStartColor()->setRGB($hexColor);
}

function applyStyle($sheet, $range, $bgHex = null, $bold = false, $fontRgb = '000000',
                    $hAlign = PHPExcel_Style_Alignment::HORIZONTAL_CENTER, $fontSize = 10)
{
    $style = $sheet->getStyle($range);

    $style->getFont()
        ->setBold($bold)
        ->setName('Arial')
        ->setSize($fontSize)
        ->getColor()->setRGB($fontRgb);

    $style->getAlignment()
        ->setHorizontal($hAlign)
        ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
        ->setWrapText(true);

    $style->getBorders()->getAllBorders()
        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
        ->getColor()->setRGB('AAAAAA');

    if ($bgHex) {
        $style->getFill()
            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
            ->getStartColor()->setRGB($bgHex);
    }
}

// ── 6. Row 1 — Main Headers
$sheet->mergeCells('A1:A2');
$sheet->mergeCells('B1:B2');
$sheet->mergeCells('C1:I1');
$sheet->mergeCells('J1:P1');
$sheet->mergeCells('Q1:W1');
$sheet->mergeCells('X1:AD1');
$sheet->mergeCells('AE1:AE2');

$sheet->setCellValue('A1',  'Zone');
$sheet->setCellValue('B1',  'BSI Name / Engineer');
$sheet->setCellValue('C1',  'Assigned (Ageing in Days)');
$sheet->setCellValue('J1',  'Replacement Request (Ageing in Days)');
$sheet->setCellValue('Q1',  'Work in Progress (Ageing in Days)');
$sheet->setCellValue('X1',  'PNA (Ageing in Days)');
$sheet->setCellValue('AE1', 'Grand Total');

// Base style Row 1
applyStyle($sheet, 'A1:AE1', '1F3864', true, 'FFFFFF', PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 10);
$sheet->getStyle('A1')->getFont()->getColor()->setRGB('000000');
$sheet->getStyle('B1')->getFont()->getColor()->setRGB('000000');


// Section color — Row 1 section headers
cellBg($sheet, 'C1',  'D6E4F0');
cellBg($sheet, 'J1',  'FCE4D6');
cellBg($sheet, 'Q1',  'E2EFDA');
cellBg($sheet, 'X1',  'FFF2CC');

// Font color fix for section headers (dark on light bg)
foreach (['C1','J1','Q1','X1'] as $c) {
    $sheet->getStyle($c)->getFont()->setBold(true)->getColor()->setRGB('1F3864');
}

// ── 7. Row 2 — Bucket Sub-Headers 
// PHPExcel getCellByColumnAndRow is 0-indexed for columns
// C=2, J=9, Q=16, X=23
$bucketLabels = ['0-1 Days', '2 Days', '3-5 Days', '6-10 Days', '11-15 Days', 'Above 15', 'Total'];
$startCols    = [2, 9, 16, 23]; // 0-based: C=2, J=9, Q=16, X=23

foreach ($startCols as $startCol) {
    foreach ($bucketLabels as $i => $label) {
        $sheet->getCellByColumnAndRow($startCol + $i, 2)->setValue($label);
    }
}

applyStyle($sheet, 'A2:AE2', 'D9D9D9', true, '1F3864', PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 9);

// Section tints Row 2
cellBg($sheet, 'C2:I2',   'D6E4F0');
cellBg($sheet, 'J2:P2',   'FCE4D6');
cellBg($sheet, 'Q2:W2',   'E2EFDA');
cellBg($sheet, 'X2:AD2',  'FFF2CC');

// ── 8. Row heights & freeze pane ─
$sheet->getRowDimension(1)->setRowHeight(30);
$sheet->getRowDimension(2)->setRowHeight(22);
$sheet->freezePane('C3');

// ── 9. Column widths ──
$sheet->getColumnDimension('A')->setWidth(14);
$sheet->getColumnDimension('B')->setWidth(32);
foreach (['C','D','E','F','G','H','I',
             'J','K','L','M','N','O','P',
             'Q','R','S','T','U','V','W',
             'X','Y','Z'] as $col) {
    $sheet->getColumnDimension($col)->setWidth(10);
}
foreach (['AA','AB','AC','AD','AE'] as $col) {
    $sheet->getColumnDimension($col)->setWidth(11);
}

// ── 10. Helper: ek row ke liye bucket values likhna ──
// PHPExcel getCellByColumnAndRow column is 0-indexed
// C=2, J=9, Q=16, X=23, AE=30
function writeDataRow($sheet, $rowNum, $d)
{
    $buckets  = ['b1','b2','b3','b4','b5','b6'];
    $sections = [
        ['key' => 'assigned',    'totalKey' => 'assigned_total',    'startCol' => 2],
        ['key' => 'replacement', 'totalKey' => 'replacement_total', 'startCol' => 9],
        ['key' => 'wip',         'totalKey' => 'wip_total',         'startCol' => 16],
        ['key' => 'pna',         'totalKey' => 'pna_total',         'startCol' => 23],
    ];

    foreach ($sections as $s) {
        $col = $s['startCol'];
        foreach ($buckets as $b) {
            $sheet->getCellByColumnAndRow($col, $rowNum)->setValue($d[$s['key']][$b] ?? 0);
            $col++;
        }
        // Total column (7th column in section)
        $sheet->getCellByColumnAndRow($col, $rowNum)->setValue($d[$s['totalKey']] ?? 0);
    }

    // Grand Total — AE = col 30 (0-based)
    $sheet->getCellByColumnAndRow(30, $rowNum)->setValue($d['grand_total'] ?? 0);
}

// ── 11. Data rows 
$rowNum     = 3;
$grandTotal = $loader->emptyBucket();

foreach ($data as $bsiRow) {
    $t = $bsiRow['totals'];

    // BSI summary row
    $sheet->getCellByColumnAndRow(0, $rowNum)->setValue($bsiRow['bsi_zone'] ?? '');
    $sheet->getCellByColumnAndRow(1, $rowNum)->setValue($bsiRow['bsi_name'] ?? '');
    writeDataRow($sheet, $rowNum, $t);

    applyStyle($sheet, "A{$rowNum}:AE{$rowNum}", 'F2F2F2', true, '1F3864',
        PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 10);

    $sheet->getStyle("A{$rowNum}:B{$rowNum}")
        ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    $sheet->getRowDimension($rowNum)->setRowHeight(18);

    $loader->merge($grandTotal, $t);
    $rowNum++;

    // Engineer rows
//    foreach ($bsiRow['engineers'] as $eng) {
//        $d = $eng['data'];
//
//        $sheet->getCellByColumnAndRow(0, $rowNum)->setValue('');
//        $sheet->getCellByColumnAndRow(1, $rowNum)->setValue($eng['id'] . ' - ' . $eng['name']);
//        writeDataRow($sheet, $rowNum, $d);
//
//        applyStyle($sheet, "A{$rowNum}:AE{$rowNum}", 'FFFFFF', false, '555555',
//            PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 9);
//        $sheet->getStyle("B{$rowNum}")
//            ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//        $sheet->getStyle("B{$rowNum}")->getFont()->setItalic(true);
//        $sheet->getRowDimension($rowNum)->setRowHeight(16);
//
//        $rowNum++;
//    }
}

// ── 12. Grand Total row ────
$sheet->mergeCells("A{$rowNum}:B{$rowNum}");
$sheet->getCellByColumnAndRow(0, $rowNum)->setValue('Grand Total');
writeDataRow($sheet, $rowNum, $grandTotal);

applyStyle($sheet, "A{$rowNum}:AE{$rowNum}", 'BDD7EE', true, '1F3864',
    PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 10);
$sheet->getStyle("A{$rowNum}")
    ->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$sheet->getStyle("A{$rowNum}:AE{$rowNum}")->getBorders()->getAllBorders()
    ->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM)
    ->getColor()->setRGB('888888');
$sheet->getRowDimension($rowNum)->setRowHeight(20);

// ── 13. Output as download ─
$excel->setActiveSheetIndex(0);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="BSI_Pendency_' . date('d-m-Y_His') . '.xlsx"');
header('Cache-Control: max-age=0');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: cache, must-revalidate');
header('Pragma: public');

$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$objWriter->save('php://output');
exit;