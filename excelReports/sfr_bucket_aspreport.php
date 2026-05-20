<?php
require_once("../includes/config.php");
/** Error reporting */
//// extract all encoded variables

////// filters value/////

$location_code=base64_decode($_REQUEST['location_code']);
if($location_code!=""){
	$location_code=" to_location='".$location_code."'";
}
else {
	$location_code="1";
}

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
							 ->setCategory("Stock In Report");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Location Name')
           ->setCellValue('C1', 'To Location Name')
         	->setCellValue('D1', 'Job No.')
			->setCellValue('E1', 'Imei')
			->setCellValue('F1', 'Partcode')
			->setCellValue('G1', 'Entry Date')
			->setCellValue('H1', 'Status')
			->setCellValue('I1', 'Qty');

			 
////////////////
cellColor('B1', 'F28A8C');
cellColor('A1', 'F28A8C');
cellColor('C1', 'F28A8C');
cellColor('D1', 'F28A8C');
cellColor('E1', 'F28A8C');
cellColor('F1', 'F28A8C');
cellColor('G1', 'F28A8C');
cellColor('H1', 'F28A8C');
cellColor('I1', 'F28A8C');
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;

$sql_loc=mysqli_query($link1,"Select * from sfr_bin  where status='4' and  $location_code group by to_location");
while($row_loc = mysqli_fetch_array($sql_loc)){
$objPHPExcel->setActiveSheetIndex(0)
           	->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('C'.$i, getAnyDetails($row_loc['to_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('D'.$i, $row_loc['job_no'])
			->setCellValue('E'.$i, $row_loc['imei'])
			->setCellValue('F'.$i, getAnyDetails($row_loc['partcode'],"part_name","id","partcode_master",$link1))
			->setCellValue('G'.$i,dt_format($row_loc['entry_date']))
			->setCellValue('H'.$i, getdispatchstatus($row_loc['status']))
			->setCellValue('I'.$i, $row_loc['qty']);
			$i++;	
			
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="SFRbucket_aspReport.xlsx"');
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
