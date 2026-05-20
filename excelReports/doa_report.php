<?php
require_once("../includes/config.php");
/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Calcutta');
$arrstatus = getJobStatus($link1);
//// extract all encoded variables
$status = base64_decode($_REQUEST['status']);
$location_code = base64_decode($_REQUEST['location_code']);
////// filters value/////
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != ""){
	$seldate = explode(" - ",$_REQUEST['daterange']);
	$fromdate = $seldate[0];
	$todate = $seldate[1];
}
else{
	$seldate = $today;
	$fromdate = $today;
	$todate = $today;
}
/////get location///////////////
if($location_code!=""){
	$locationcode=" location_code in ('".$location_code."' )";
}
else {
	$locationcode="1";
}

/////get status///////////////
if($status !=""){
	$st="status='9' and sub_status in (".$status.")";
}
else {
	$st="status='9'";
}


//////End filters value/////

/** Include PHPExcel */
require_once("../ExcelExportAPI/Classes/PHPExcel.php");
//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Candour Software")
							 ->setLastModifiedBy("Candour Software")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("DOA REPORT");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'State')
            ->setCellValue('C1', 'City')
         	 ->setCellValue('D1', 'ASC/L3 Name')
			->setCellValue('E1', 'ASC/L3 Code')
			->setCellValue('F1', 'Job Received From')
			->setCellValue('G1', 'Job For')
			->setCellValue('H1', 'Job Type')
			->setCellValue('I1', 'Warranty Status')
			->setCellValue('J1', 'Job No.')
			->setCellValue('K1', 'IMEI1')
			->setCellValue('L1', 'IMEI2')
			->setCellValue('M1', 'Model')
			->setCellValue('N1', 'Customer Name')
			->setCellValue('O1', 'Contact No.')
			->setCellValue('P1', 'Open Date(DD-MM-YYYY)')
			->setCellValue('Q1', 'Activation Date(DD-MM-YYYY)')
			->setCellValue('R1', 'POP Date(DD-MM-YYYY)')
			->setCellValue('S1', 'Defect Reported')
			->setCellValue('T1', 'Symptom Reported')
			->setCellValue('U1', 'Eng Name (L3)')
			->setCellValue('V1', 'Job Status')
			->setCellValue('W1', ' Remark')
			->setCellValue('X1', ' DOA Remark')
			->setCellValue('Y1', 'Product')
			->setCellValue('Z1', 'Brand')
			->setCellValue('AA1', 'Aging')
			->setCellValue('AB1', 'TAT');
			
////////////////
///////////////////////
cellColor('B1', 'F28A8C');
cellColor('A1', 'F28A8C');
cellColor('C1', 'F28A8C');
cellColor('D1', 'F28A8C');
cellColor('E1', 'F28A8C');
cellColor('F1', 'F28A8C');
cellColor('G1', 'F28A8C');
cellColor('H1', 'F28A8C');
cellColor('I1', 'F28A8C');
cellColor('J1', 'F28A8C');
cellColor('K1', 'F28A8C');
cellColor('L1', 'F28A8C');
cellColor('M1', 'F28A8C');
cellColor('N1', 'F28A8C');
cellColor('O1', 'F28A8C');
cellColor('P1', 'F28A8C');
cellColor('Q1', 'F28A8C');
cellColor('R1', 'F28A8C');
cellColor('S1', 'F28A8C');
cellColor('T1', 'F28A8C');
cellColor('U1', 'F28A8C');
cellColor('V1', 'F28A8C');
cellColor('W1', 'F28A8C');
cellColor('X1', 'F28A8C');
cellColor('Y1', 'F28A8C');
cellColor('Z1', 'F28A8C');
cellColor('AA1', 'F28A8C');
cellColor('AB1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;

$sql_loc = mysqli_query($link1,"Select * from jobsheet_data where (open_date >= '".$fromdate."'  and open_date <='".$todate."') and ".$locationcode."  and ".$st." and call_type = 'DOA'  ");

while($row_loc = mysqli_fetch_array($sql_loc)){
$voc1 = getAnyDetails($row_loc['cust_problem'] ,"voc_desc","voc_code","voc_master" ,$link1);
$voc2 = getAnyDetails($row_loc['cust_problem2'] ,"voc_desc","voc_code","voc_master" ,$link1);
$voc3 = getAnyDetails($row_loc['cust_problem3'] ,"voc_desc","voc_code","voc_master" ,$link1);
if($row_loc['close_date'] =='0000-00-00' ){ $aging = daysDifference($today,$row_loc['open_date']);} else {$aging = "--" ;}
if($row_loc['close_date']  != '0000-00-00'){$tat = daysDifference($row_loc['close_date'],$row_loc['open_date']);}else{ $tat = "--" ;}
		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, getAnyDetails($row_loc['state_id'],"state","stateid","state_master",$link1))
			->setCellValue('C'.$i, getAnyDetails($row_loc['city_id'],"city","cityid","city_master",$link1))
			->setCellValue('D'.$i, getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('E'.$i, $row_loc['location_code'])
			->setCellValue('F'.$i, $row_loc['customer_type'])
			->setCellValue('G'.$i, $row_loc['call_for'])
            ->setCellValue('H'.$i, $row_loc['call_type'])
			->setCellValue('I'.$i, $row_loc['warranty_status'])
			->setCellValue('J'.$i, $row_loc['job_no'])
			->setCellValue('K'.$i, $row_loc['imei'])
			->setCellValue('L'.$i, $row_loc['sec_imei'])
			->setCellValue('M'.$i, $row_loc['model'])
			->setCellValue('N'.$i, $row_loc['customer_name'])
			->setCellValue('O'.$i, $row_loc['contact_no'])
			->setCellValue('P'.$i, dt_format($row_loc['open_date']))
			->setCellValue('Q'.$i, dt_format($row_loc['activation']))
			->setCellValue('R'.$i, dt_format($row_loc['dop']))
			->setCellValue('S'.$i, $voc1."/".$voc2."/".$voc3)
			->setCellValue('T'.$i, getAnyDetails($row_loc['symp_code'],"symp_code","symp_code","symptom_master",$link1))
			->setCellValue('U'.$i, $row_loc['eng_id'])
			->setCellValue('V'.$i, $arrstatus[$row_loc['sub_status']][$row_loc['status']])
			->setCellValue('W'.$i, $row_loc['remark'])
			->setCellValue('X'.$i, $row_loc['doa_remark'])
			->setCellValue('Y'.$i, getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1))
			->setCellValue('Z'.$i, getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1))
			->setCellValue('AA'.$i, $aging)
			->setCellValue('AB'.$i,  $tat);
			
			$i++;	
			$count++;		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="doa_report.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
