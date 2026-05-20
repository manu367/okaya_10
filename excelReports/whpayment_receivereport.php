<?php
require_once("../includes/config.php");
////// filters value/////
## selected  location
if($_REQUEST['location']!=""){
	$location_code="to_location='".$_REQUEST['location']."'";
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
							 ->setCategory("Party Account");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'To Location Name')
            ->setCellValue('C1', 'From Location Name')
         	 ->setCellValue('D1', 'Challan No.')
			->setCellValue('E1', 'Payment Mode')
			->setCellValue('F1', 'Bank Name')
			->setCellValue('G1', 'Account No.')
			->setCellValue('H1', 'DD/Cheque No.')
			->setCellValue('I1', 'Amount')
			->setCellValue('J1', 'Receive Amount')
			->setCellValue('K1', 'Status')
			->setCellValue('L1', 'Remark')
			->setCellValue('M1', 'Receive Date');
			

///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$sql_loc=mysqli_query($link1,"Select * from payment_details where ".$location_code."");
while($row_loc = mysqli_fetch_array($sql_loc)){

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, getAnyDetails($row_loc['to_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('C'.$i, getAnyDetails($row_loc['from_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('D'.$i, $row_loc['challan_no'])
			->setCellValue('E'.$i, $row_loc['pay_mode'])
			->setCellValue('F'.$i, getAnyDetails($row_loc['bankname'],"name","bank_id","bank_master",$link1))
			->setCellValue('G'.$i, $row_loc['account_no'])
			->setCellValue('H'.$i, $row_loc['dd_chequeno'])
			->setCellValue('I'.$i, $row_loc['amount'])
			->setCellValue('J'.$i, $row_loc['receive_amt'])
			->setCellValue('K'.$i, getdispatchstatus($row_loc['status']))
			->setCellValue('L'.$i, $row_loc['remark'])
			->setCellValue('M'.$i, dt_format($row_loc['receiveddate']));
			$i++;	
					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PaymentReceivereport.xlsx"');
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
