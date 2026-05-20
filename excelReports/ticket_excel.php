<?php
require_once("../includes/config.php");
/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Calcutta');
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
///////////////// get location/////////////////////////
if($_REQUEST['location'] != ""){
	$location = "location_code  = '".$_REQUEST['location']."' ";
}else{
	$location = "1";
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
							 ->setCategory("Part Consumption");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Ticket No.')
           ->setCellValue('C1', 'Location Name')
         	->setCellValue('D1', 'State')
			->setCellValue('E1', 'City')
			 ->setCellValue('F1', 'Product')
			->setCellValue('G1', 'Brand')
			->setCellValue('H1', 'Model')
			->setCellValue('I1', 'Customer Type')
			->setCellValue('J1', 'Customer Name')
			->setCellValue('K1', 'Contact No.')
			->setCellValue('L1', 'Address')
			->setCellValue('M1', 'Customer Problem')
			->setCellValue('N1', 'Open Date')
			->setCellValue('O1', 'Create Remark')
			->setCellValue('P1', 'priority')
			->setCellValue('Q1', 'Status')
			->setCellValue('R1', 'Followup Date')
			->setCellValue('S1', 'update Date')
			->setCellValue('T1', 'Remark');
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
cellColor('P1', 'F28A8C');cellColor('R1', 'F28A8C');
cellColor('Q1', 'F28A8C');
cellColor('S1', 'F28A8C');cellColor('T1', 'F28A8C');
////////////////////////////////

///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$sql_loc=mysqli_query($link1,"Select * from ticket_master where (open_date >= '".$fromdate."'  and open_date <='".$todate."') and ".$location." ");
while($row_loc = mysqli_fetch_array($sql_loc)){

if ($row_loc['proiority'] == '1'){$st= "Low" ;} elseif ($row_loc['proiority'] == '2') {$st= "Normal";} else {$st= "High";}


$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row_loc['ticket_no'])
			->setCellValue('C'.$i, getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('D'.$i, getAnyDetails($row_loc['state_id'],"state","stateid","state_master",$link1))
			->setCellValue('E'.$i, getAnyDetails($row_loc['city_id'],"city","cityid","city_master",$link1))
			->setCellValue('F'.$i, getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1)."/".$row_loc['product_id'])
			->setCellValue('G'.$i, getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1)."/".$row_loc['brand_id'])
            ->setCellValue('H'.$i, $row_loc['model'])
			->setCellValue('I'.$i, $row_loc['customer_type'])
			->setCellValue('J'.$i, $row_loc['customer_name'])
			->setCellValue('K'.$i, $row_loc['contact_no'])
			->setCellValue('L'.$i, $row_loc['address'])
			->setCellValue('M'.$i, $row_loc['cust_problem'])
			->setCellValue('N'.$i, dt_format($row_loc['open_date']))
			->setCellValue('O'.$i, $row_loc['remark'])
			->setCellValue('P'.$i, $st)
			->setCellValue('Q'.$i, $row_loc['status'])
			->setCellValue('R'.$i, $row_loc['follow_date'])
			->setCellValue('S'.$i, $row_loc['updatedt'])
			->setCellValue('T'.$i, $row_loc['tic_rmk']);
		

			
			$i++;			
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ticketreport.xlsx"');
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
