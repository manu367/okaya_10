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

if($_REQUEST['doc_type']!=""){
	$doc_type=" document_type='".$_REQUEST['doc_type']."'";
}
else {
	$doc_type="1";
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
            ->setCellValue('B1', 'From Location')
           ->setCellValue('C1', 'From Gst No.')
         	->setCellValue('D1', 'To Location')
			->setCellValue('E1', 'To Gst No.')
			->setCellValue('F1', 'Party Name')
			->setCellValue('G1', 'Document No.')
			->setCellValue('H1', 'Doc Date')
			->setCellValue('I1', 'Status')
			 ->setCellValue('J1', 'Document Type');
			 
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

////////////////////////////////

///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$sql_loc=mysqli_query($link1,"Select * from billing_master where (sale_date  >= '".$fromdate."'  and sale_date  <='".$todate."')  and $status and from_location='".$_SESSION['asc_code']."' and $doc_type");
while($row_loc = mysqli_fetch_array($sql_loc)){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, getAnyDetails($row_loc['from_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('C'.$i, $row_loc['from_gst_no'])
			->setCellValue('D'.$i, getAnyDetails($row_loc['to_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('E'.$i, $row_loc['to_gst_no'])
			->setCellValue('F'.$i, $row_loc['party_name'])
			->setCellValue('G'.$i, $row_loc['challan_no'])
			->setCellValue('H'.$i, dt_format($row_loc['sale_date']))
			->setCellValue('I'.$i, getdispatchstatus($row_loc['status']))
         	->setCellValue('J'.$i, $row_loc['document_type']);			
			$i++;	
			
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="stockin_aspReport.xlsx"');
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
