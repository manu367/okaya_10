<?php
require_once("../includes/config.php");

/// filters value/////
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != ""){
	$date_range = explode(" - ",$_REQUEST['daterange']);
	$daterange = "sale_date  >= '".$date_range[0]."' and sale_date  <= '".$date_range[1]."'";
}
else{
	$daterange = "1";
}

/////get model///////////////
if($modelid!=""){
	$model_id=" model_id in ('".$modelid."' )";
}
else {
	$model_id="1";
}
/////get product///////////////
if($productid !=""){
	$product_id=" product_id in ('".$productid."' )";
}
else {
	$product_id="1";
}
/////get brand///////////////
if($brandid !=""){
	$brand_id="brand_id in ('".$brandid."' )";
}
else {
	$brand_id="1";
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
							 ->setCategory("Summarize Sale");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Invoice Type')
            ->setCellValue('C1', 'Courier')
         	 ->setCellValue('D1', 'Docket no')
			->setCellValue('E1', 'Docket date')
			->setCellValue('F1', 'Location Code')
			->setCellValue('G1', 'Name of Receipient')
			->setCellValue('H1', 'Nature of Supply')
			->setCellValue('I1', 'Invoice number')
			->setCellValue('J1', 'Invoice date')
			->setCellValue('K1', 'GSTIN / UIN of recipient')
			->setCellValue('L1', 'State of receipient of Invoice')
			->setCellValue('M1', 'State of supply of services')
			->setCellValue('N1', 'Total Invoice Value')
			->setCellValue('O1', 'Original Invoice Number')
			->setCellValue('P1', 'Original Invoice Date')
			->setCellValue('Q1', 'Sr No for Item Details')
			->setCellValue('R1', 'Taxable value')
			->setCellValue('S1', 'IGST Tax Amount')
			->setCellValue('T1', 'CGST Tax Amount')
			->setCellValue('U1', 'SGST Tax Amount')
			->setCellValue('V1', 'Status')
			->setCellValue('W1', 'CN Amount');
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
cellColor('P1', 'F28A8C');
cellColor('Q1', 'F28A8C');
cellColor('R1', 'F28A8C');cellColor('S1', 'F28A8C');cellColor('T1', 'F28A8C');cellColor('U1', 'F28A8C');cellColor('V1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$sql_loc=mysqli_query($link1,"Select * from billing_master where (po_type='Sale Return' or po_type='P2C'  )and  $daterange and  to_location = '".$_SESSION['asc_code']."' ");
while($row_loc = mysqli_fetch_array($sql_loc)){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row_loc['document_type'])
			 ->setCellValue('C'.$i, $row_loc['courier'])
			->setCellValue('D'.$i, $row_loc['docket_no'])
			->setCellValue('E'.$i, dt_format2($row_loc['dc_date']))
			->setCellValue('F'.$i, $row_loc['from_location'])
			->setCellValue('G'.$i, getAnyDetails($row_loc['from_location'],"locationname","location_code","location_master",$link1))
          	->setCellValue('H'.$i, $row_loc['total_cost'])
			->setCellValue('I'.$i, $row_loc['challan_no'])
			->setCellValue('J'.$i, dt_format2($row_loc['sale_date']))
			->setCellValue('K'.$i, $row_loc['from_gst_no'])
			->setCellValue('L'.$i, getAnyDetails($row_loc['from_stateid'],"state","stateid","state_master",$link1))
			->setCellValue('M'.$i, getAnyDetails($row_loc['to_stateid'],"state","stateid","state_master",$link1))
			->setCellValue('N'.$i, $row_loc['total_cost'])
			->setCellValue('O'.$i, $row_loc['total_cost'])
			->setCellValue('P'.$i, $row_loc['total_cost'])
			->setCellValue('Q'.$i, $row_loc['total_cost'])
			->setCellValue('R'.$i, $row_loc['tax_cost'])
			->setCellValue('S'.$i, $row_loc['igst_amt'])
			->setCellValue('T'.$i, $row_loc['cgst_amt'])
			->setCellValue('U'.$i, $row_loc['sgst_amt'])
			->setCellValue('V'.$i, getDispatchStatus($row_loc['status']))
			->setCellValue('W'.$i,  getAnyDetails($row_loc['challan_no'],"amount","transaction_no","location_account_ledger",$link1));
			
			$i++;					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="saledetail_report.xlsx"');
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
