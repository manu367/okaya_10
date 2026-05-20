<?php
require_once("../includes/config.php");

/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);

//// extract all encoded variables
$tostate = base64_decode($_REQUEST['to_state']);
$toloc = base64_decode($_REQUEST['to_loc']);
$doctypid = base64_decode($_REQUEST['doc_typ']);
/// filters value/////
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != ""){
	$date_range = explode(" - ",$_REQUEST['daterange']);
	$daterange = "and sale_date  >= '".$date_range[0]."' and sale_date  <= '".$date_range[1]."'";
}
else{
	$daterange = "and 1";
}
## selected  to_loc
if($toloc != ""){
	$to_location = " from_location in ('".$toloc."')";
}else if($tostate != ""){
	$to_location = " from_stateid in ('".$tostate."')";
}else{
	$to_location = " from_stateid in (".$arrstate.") ";
}
## selected  doc_typ
if($doctypid == "P2C"){
	$doc_type_id = " po_type='P2C' ";
}else if($doctypid == "Sale Return"){
	$doc_type_id = " po_type='Sale Return' ";
}else{
	$doc_type_id = " po_type in ('P2C','Sale Return') ";
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
							 ->setCategory("Detail Sales Return Report");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
			->setCellValue('B1', 'Courier')
         	->setCellValue('C1', 'Docket No')
			->setCellValue('D1', 'Docket Date')
			
			->setCellValue('E1', 'From Location Code')
			->setCellValue('F1', 'From Location Name')
			->setCellValue('G1', 'State of From Location')
			->setCellValue('H1', 'GSTIN / UIN of  From Location')
			
			->setCellValue('I1', 'To Location Code')
			->setCellValue('J1', 'To Location Name')
			->setCellValue('K1', 'State of To Location')
			->setCellValue('L1', 'GSTIN / UIN of  From Location')
			
			->setCellValue('M1', 'Invoice Type')
			->setCellValue('N1', 'Invoice Number')
			->setCellValue('O1', 'Invoice Date')
			
			->setCellValue('P1', 'Total Invoice Value')
			->setCellValue('Q1', 'IGST Tax Amount')
			->setCellValue('R1', 'CGST Tax Amount')
			->setCellValue('S1', 'SGST Tax Amount')
			->setCellValue('T1', 'Total Invoice with GST')
			
			->setCellValue('U1', 'Status')
			->setCellValue('V1', 'Type')
			->setCellValue('W1', 'Asc Receive Date')
			
			->setCellValue('X1', 'Ship From Code')
			->setCellValue('Y1', 'Ship From Name')
			->setCellValue('Z1', 'Ship From State')
			->setCellValue('AA1', 'Ship From Address')
			->setCellValue('AB1', 'Ship From GST')
	
			->setCellValue('AC1', 'Grand Total')
			->setCellValue('AD1', 'Send Total Qty');			
			
////////////////
///////////////////////
cellColor('A1:AD1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
$sql_loc=mysqli_query($link1,"Select * from billing_master where ".$doc_type_id." ".$daterange." and ".$to_location." ");
while($row_loc = mysqli_fetch_array($sql_loc)){
	
$tot = mysqli_fetch_array(mysqli_query($link1,"Select sum(qty) as tot from billing_product_items where challan_no= '".$row_loc['challan_no']."' group by challan_no "));	
	
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, $row_loc['courier'])
			->setCellValue('C'.$i, $row_loc['docket_no'])
			->setCellValue('D'.$i, dt_format2($row_loc['dc_date']))
			
			->setCellValue('E'.$i, $row_loc['from_location'])
			->setCellValue('F'.$i, getAnyDetails($row_loc['from_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('G'.$i, getAnyDetails($row_loc['from_stateid'],"state","stateid","state_master",$link1))
			->setCellValue('H'.$i, $row_loc['from_gst_no'])
			
			->setCellValue('I'.$i, $row_loc['to_location'])
			->setCellValue('J'.$i, getAnyDetails($row_loc['to_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('K'.$i, getAnyDetails($row_loc['to_stateid'],"state","stateid","state_master",$link1))
			->setCellValue('L'.$i, $row_loc['to_gst_no'])
			
			->setCellValue('M'.$i, $row_loc['document_type'])
			->setCellValue('N'.$i, $row_loc['challan_no'])
			->setCellValue('O'.$i, dt_format2($row_loc['sale_date']))		
			
			->setCellValue('P'.$i, $row_loc['basic_cost'])	
			->setCellValue('Q'.$i, $row_loc['igst_amt'])
			->setCellValue('R'.$i, $row_loc['cgst_amt'])
			->setCellValue('S'.$i, $row_loc['sgst_amt'])
			->setCellValue('T'.$i, $row_loc['total_cost'])
			
			->setCellValue('U'.$i, getDispatchStatus($row_loc['status']))
			->setCellValue('V'.$i, $row_loc['po_type'])
			->setCellValue('W'.$i, dt_format($row_loc['receive_date']))
			
			->setCellValue('X'.$i, $row_loc['ship_from_code'])
			->setCellValue('Y'.$i, getAnyDetails($row_loc['ship_from_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('Z'.$i, getAnyDetails($row_loc['ship_from_state'],"state","stateid","state_master",$link1))
			->setCellValue('AA'.$i, $row_loc['ship_from_addr'])
			->setCellValue('AB'.$i, $row_loc['ship_from_gst'])
	
			->setCellValue('AC'.$i, $row_loc['total_cost'])
			->setCellValue('AD'.$i, $tot['tot']);
						
			$count++;
			$i++;					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Detail Sales Return Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="detail_sales_return_report.xlsx"');
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
