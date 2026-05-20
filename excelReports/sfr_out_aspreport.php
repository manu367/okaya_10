<?php
require_once("../includes/config.php");
/** Error reporting */
//// extract all encoded variables

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

/////get status(pending/ processed)///////////////
if($_REQUEST['status']!=""){
	$status=" status='".$_REQUEST['status']."'";
}
else {
	$status="1";
}



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
            ->setCellValue('B1', 'Document No.')
           ->setCellValue('C1', 'Generate Date')
         	->setCellValue('D1', 'Generate By')
			->setCellValue('E1', 'Courier')
			->setCellValue('F1', 'Docket No.')
			->setCellValue('G1', 'Status');

			 
////////////////
cellColor('B1', 'F28A8C');
cellColor('A1', 'F28A8C');
cellColor('C1', 'F28A8C');
cellColor('D1', 'F28A8C');
cellColor('E1', 'F28A8C');
cellColor('F1', 'F28A8C');
cellColor('G1', 'F28A8C');

///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$sql_loc=mysqli_query($link1,"Select * from sfr_challan where (challan_date  >= '".$fromdate."'  and challan_date  <='".$todate."')  and $status and $location_code");
while($row_loc = mysqli_fetch_array($sql_loc)){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row_loc['challan_no'])
			->setCellValue('C'.$i, dt_format($row_loc['challan_date']))
			->setCellValue('D'.$i, getAnyDetails($row_loc['to_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('E'.$i, $row_loc['courier'])
			->setCellValue('F'.$i, $row_loc['docket_no'])
			->setCellValue('G'.$i, getdispatchstatus($row_loc['status']));
			
			$i++;	
			
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="SFRout_aspReport.xlsx"');
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
