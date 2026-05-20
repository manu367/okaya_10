<?php
//require_once("../includes/config_mis.php");
require_once("../includes/config.php");

$data_arr=base64_decode($_REQUEST['data_arr']);

$arr = array();
$arr = explode(",",$data_arr);

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
							 ->setCategory("Unloaded Report");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'ID')
			->setCellValue('B1', 'Serial No');
			
			

//////////////////////////
cellColor('A1:B1', 'F28A8C');
//////////////////////////

// Miscellaneous glyphs, UTF-8
$i=2;
$count =1;

foreach($arr as $row => $item) {
	
$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A'.$i, $count)
	->setCellValue('B'.$i, $item);
	
	$count++; 
	$i++;	
}	

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Unloaded Report');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="unloaded_excel.xlsx"');
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
