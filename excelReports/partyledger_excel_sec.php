<?php 
require_once("../includes/config.php");
////// filters value/////
$locationcode = $_REQUEST['location'];
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
## selected  location
if($locationcode !=""){
	 $location_code="location_code = '".$_REQUEST['location']."' ";
}else{
	 $location_code="1";
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
							 ->setCategory("Party Ledger");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Party Name')
            ->setCellValue('C1', 'Party Code')
         	 ->setCellValue('D1', 'Trasaction Details')
			->setCellValue('E1', 'Trsacation Type')
			->setCellValue('F1', 'Trasaction Date')
			->setCellValue('G1', 'Amount Cr')
			->setCellValue('H1', 'Amount Dr');
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
$count=1;
$sql_loc=mysqli_query($link1,"Select * from location_account_ledger_security where   $location_code");
while($row_loc = mysqli_fetch_array($sql_loc)){
//////////////////// get amount on basis on cr/dr ////////////////////////////`
if ($row_loc['crdr'] == 'CR' ) { 
 $cr_amt = $row_loc['amount'];  $dr_amt = '0' ;}
else { $dr_amt = $row_loc['amount'];  $cr_amt = '0';  }

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('C'.$i, $row_loc['location_code'])
			->setCellValue('D'.$i, $row_loc['remark'])
			->setCellValue('E'.$i, $row_loc['transaction_type'])
			->setCellValue('F'.$i, dt_format2($row_loc['entry_date']))
			->setCellValue('G'.$i, $cr_amt)
            ->setCellValue('H'.$i, $dr_amt);
			$count++;
			$i++;			
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="partyledger_report.xlsx"');
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
