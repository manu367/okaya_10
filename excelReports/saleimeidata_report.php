<?php
require_once("../includes/config.php");

/// filters value/////
if ($_REQUEST['daterange'] != ""){
	$date_range = explode(" - ",$_REQUEST['daterange']);
	$daterange = "  (import_date  >= '".$date_range[0]."' and import_date  <= '".$date_range[1]."')";
}
else{
	$daterange = " 1";
}
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
							 ->setCategory("Sale Imei Details");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'IMEI-1/Serial')          
         	 ->setCellValue('C1', 'IMEI-2/Serial')
			->setCellValue('D1', 'Model Description')
	        ->setCellValue('E1', 'Model Code')
			->setCellValue('F1', 'Th Serial no')	
			->setCellValue('G1', 'Import Date')
			->setCellValue('H1', 'Activation Date')
	        ->setCellValue('I1', 'Party Name')
	        ->setCellValue('J1', 'City Name');
		
////////////////
///////////////////////
cellColor('A1', 'F28A8C');
cellColor('B1', 'F28A8C');
cellColor('C1', 'F28A8C');
cellColor('D1', 'F28A8C');
cellColor('E1', 'F28A8C');
cellColor('F1', 'F28A8C');
cellColor('G1', 'F28A8C');
cellColor('H1', 'F28A8C');
cellColor('I1', 'F28A8C');
cellColor('J1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count =1;
$sql_loc=mysqli_query($link1, "SELECT * FROM imei_data_import where $daterange  ");
while($row_loc = mysqli_fetch_array($sql_loc)){

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, $row_loc['imei1'])			
			->setCellValue('C'.$i, $row_loc['imei2'])
			->setCellValue('D'.$i, getAnyDetails($row_loc['model_id'],"model","model_id","model_master",$link1))
	        ->setCellValue('E'.$i, $row_loc['model_id'])	
	        ->setCellValue('F'.$i, $row_loc['sn'])
          	->setCellValue('G'.$i, dt_format($row_loc['import_date']))
	        ->setCellValue('H'.$i, dt_format($row_loc['activation_date']))
	        ->setCellValue('I'.$i, $row_loc['party_name'])
	        ->setCellValue('J'.$i, $row_loc['city']);
			$count++;
			$i++;					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="saleimeidetail_report.xlsx"');
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
