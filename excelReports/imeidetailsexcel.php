<?php
require_once("../includes/config.php");

/// filters value/////
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
							 ->setCategory("Summarize Sale");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'IMEI 1')
            ->setCellValue('C1', 'IMEI 2')
         	 ->setCellValue('D1', 'Message')
			->setCellValue('E1', 'Contact No.')
			->setCellValue('F1', 'Model')
			->setCellValue('G1', 'Location')
			->setCellValue('H1', 'State')
			->setCellValue('I1', 'Status');
		
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
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$sql_loc=mysqli_query($link1, "SELECT * FROM imei_data_auto where sale_date  >= '".$_REQUEST[start_date]."' and sale_date  <= '".$_REQUEST[end_date]."' ");
while($row_loc = mysqli_fetch_array($sql_loc)){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row_loc['imei1'])
			 ->setCellValue('C'.$i, $row_loc['imei2'])
			->setCellValue('D'.$i, $row_loc['msg'])
			->setCellValue('E'.$i, $row_loc['contact_no'])
			->setCellValue('F'.$i, $row_loc['model'])
			->setCellValue('G'.$i, $row_loc['sale_location'])
          	->setCellValue('H'.$i, $row_loc['state'])
			->setCellValue('I'.$i, $row_loc['status']);
			
			$i++;					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="imeidetail_report.xlsx"');
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
