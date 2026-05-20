<?php
require_once("../includes/config.php");

//// extract all encoded variables
$modelid = base64_decode($_REQUEST['model']);
$productid = base64_decode($_REQUEST['prod_code']);
$brandid = base64_decode($_REQUEST['brand']);
$tolocid = base64_decode($_REQUEST['to_loc']);
////// filters value/////
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
/////get to loc///////////////
if($tolocid !=""){
	$to_loc_id="to_location in ('".$tolocid."' )";
}
else {
	$to_loc_id="1";
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
							 ->setCategory("Partwise Sale");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Invoice Type')
            ->setCellValue('C1', 'Courier')
         	 ->setCellValue('D1', 'Docket no')
			->setCellValue('E1', 'ASC Code')
			->setCellValue('F1', 'Job No')
			->setCellValue('G1', 'PO No')
			->setCellValue('H1', 'PO Date')
			->setCellValue('I1', 'Partcode')
			->setCellValue('J1', 'Name of Receipient')
			->setCellValue('K1', 'Nature of Supply')
			->setCellValue('L1', 'Invoice number')
			->setCellValue('M1', 'Invoice date')
			->setCellValue('N1', 'GSTIN / UIN of recipient')
			->setCellValue('O1', 'State of receipient of Invoice')
			->setCellValue('P1', 'State of supply of services')
			->setCellValue('Q1', 'Total Invoice Value')
			->setCellValue('R1', 'Original Invoice Number')
			->setCellValue('S1', 'Taxable value')
			->setCellValue('T1', 'Rate')
			->setCellValue('U1', 'IGST Rate')
			->setCellValue('V1', 'IGST Tax Amount')
			->setCellValue('W1', 'SGST Rate')
			->setCellValue('X1', 'SGST Tax Amount')	
			->setCellValue('Y1', 'CGST Rate')
			->setCellValue('Z1', 'CGST Tax Amount')
			->setCellValue('AA1', 'HSN')
			->setCellValue('AB1', 'Description of goods sold')	
			->setCellValue('AC1', 'UQC of goods sold')	
			->setCellValue('AD1', 'Quantity of goods sold')	
			->setCellValue('AE1', 'Shipping Bill No.')	
			->setCellValue('AF1', 'Shipping Bill Date.')	
			->setCellValue('AG1', 'Status')
			->setCellValue('AH1', 'Type')
			->setCellValue('AI1', 'Asc Receive Date')
			->setCellValue('AJ1', 'To Location Code')
			->setCellValue('AK1', 'To Location');
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
cellColor('R1', 'F28A8C');cellColor('S1', 'F28A8C');cellColor('T1', 'F28A8C');cellColor('U1', 'F28A8C');cellColor('V1', 'F28A8C');cellColor('W1', 'F28A8C');cellColor('X1', 'F28A8C');cellColor('Y1', 'F28A8C');cellColor('Z1', 'F28A8C');cellColor('AA1', 'F28A8C');cellColor('AB1', 'F28A8C');cellColor('AC1', 'F28A8C');cellColor('AD1', 'F28A8C');cellColor('AE1', 'F28A8C');cellColor('AF1', 'F28A8C');cellColor('AG1', 'F28A8C');cellColor('AH1', 'F28A8C');
cellColor('AI1', 'F28A8C');cellColor('AJ1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
$sql_loc=mysqli_query($link1,"Select * from billing_master where ".$daterange." and  from_location = '".$_SESSION['asc_code']."' and ".$to_loc_id." ");
while($row_loc = mysqli_fetch_array($sql_loc)){
$sql = mysqli_query($link1,"Select * from billing_product_items where challan_no= '$row_loc[challan_no]'  and ".$product_id." and ".$brand_id." and ".$model_id."  and from_location = '".$_SESSION['asc_code']."' and ".$to_loc_id." ");
while($row = mysqli_fetch_array($sql )){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, $row_loc['document_type'])
			 ->setCellValue('C'.$i, $row_loc['courier'])
			->setCellValue('D'.$i, $row_loc['docket_no'])
			->setCellValue('E'.$i, $row_loc['from_location'])
			->setCellValue('F'.$i, $row['job_no'])
			->setCellValue('G'.$i, $row_loc['po_no'])
			->setCellValue('H'.$i, getAnyDetails($row_loc['po_no'],"po_date","po_no","po_master",$link1))
          	->setCellValue('I'.$i, $row['partcode'])
			->setCellValue('J'.$i, getAnyDetails($row_loc['from_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('K'.$i, $row_loc['po_no'])
			->setCellValue('L'.$i, $row_loc['challan_no'])
			->setCellValue('M'.$i, dt_format2($row_loc['sale_date']))
			->setCellValue('N'.$i, $row_loc['from_gst_no'])
			->setCellValue('O'.$i, getAnyDetails($row_loc['from_stateid'],"state","stateid","state_master",$link1))
			->setCellValue('P'.$i, getAnyDetails($row_loc['to_stateid'],"state","stateid","state_master",$link1))
			->setCellValue('Q'.$i, $row_loc['total_cost'])
			->setCellValue('R'.$i, $row_loc['po_no'])
			->setCellValue('S'.$i, $row['discount_amt'])
			->setCellValue('T'.$i, $row['price'])
			->setCellValue('U'.$i, $row['igst_per'])
			->setCellValue('V'.$i, $row['igst_amt'])
			->setCellValue('W'.$i, $row['sgst_per'])
			->setCellValue('X'.$i, $row['sgst_amt'])
			->setCellValue('Y'.$i, $row['cgst_per'])
			->setCellValue('Z'.$i, $row['cgst_amt'])
			->setCellValue('AA'.$i, $row['hsn_code'])
			->setCellValue('AB'.$i, $row['part_name'])
			->setCellValue('AC'.$i, $row['uom'])
			->setCellValue('AD'.$i, $row['qty'])
			->setCellValue('AE'.$i, $row['tally_challan_no'])
			->setCellValue('AF'.$i, $row['dc_date'])
			->setCellValue('AG'.$i, getDispatchStatus($row_loc['status']))
			->setCellValue('AH'.$i, $row_loc['po_type'])
			->setCellValue('AI'.$i, dt_format($row_loc['receive_date']))
			->setCellValue('AJ'.$i, $row_loc['to_location'])
			->setCellValue('AK'.$i, getAnyDetails($row_loc['to_location'],"locationname","location_code","location_master",$link1));
			$i++;
			$count++;
			}
					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="partwisesalewh_report.xlsx"');
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
