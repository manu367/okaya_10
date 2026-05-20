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
	$status=" status in ('1','6')";
}

if($_REQUEST['location_code']!=""){
	$locationcode=" from_code='".$_REQUEST['location_code']."'";
}
else {
	$locationcode="1";
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
            ->setCellValue('B1', 'PO No.')
           ->setCellValue('C1', 'PO Date')
         	->setCellValue('D1', 'PO Type')
			->setCellValue('E1', 'Location Code')
			->setCellValue('F1', 'Location Name')
			->setCellValue('G1', 'City/State')
			->setCellValue('H1', 'Status');	
			 
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


////////////////////////////////

///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$sql_loc=mysqli_query($link1,"Select * from po_master where (po_date  >= '".$fromdate."'  and po_date  <='".$todate."')  and $status and to_code='".$_SESSION['asc_code']."' and $locationcode");
while($row_loc = mysqli_fetch_array($sql_loc)){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row_loc['po_no'])
			->setCellValue('C'.$i, dt_format($row_loc['po_date']))
			->setCellValue('D'.$i, $row_loc['potype'])
			->setCellValue('E'.$i, $row_loc['from_code'])
			->setCellValue('F'.$i, getAnyDetails($row_loc['from_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('G'.$i, $row_loc['from_address'])
			->setCellValue('H'.$i, getdispatchstatus($row_loc['status']));
			$i++;	
			
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Dispatchpopna_whReport.xlsx"');
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
