<?php
require_once("../includes/config.php");
//// extract all encoded variables
$modelid = base64_decode($_REQUEST['model']);
$productid = base64_decode($_REQUEST['prod_code']);
$brandid = base64_decode($_REQUEST['brand']);
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
			->setCellValue('G1', 'Po No')
			->setCellValue('H1', 'Partcode')
			->setCellValue('I1', 'Name of Receipient')
			->setCellValue('J1', 'Nature of Supply')
			->setCellValue('K1', 'Invoice number')
			->setCellValue('L1', 'Invoice date')
			->setCellValue('M1', 'GSTIN / UIN of recipient')
			->setCellValue('N1', 'State of receipient of Invoice')
			->setCellValue('O1', 'State of supply of services')
			->setCellValue('P1', 'Total Invoice Value')
			->setCellValue('Q1', 'Original Invoice Number')
			->setCellValue('R1', 'Original Invoice Date')
			->setCellValue('S1', 'Sr No for Item Details')
			->setCellValue('T1', 'Taxable value')
			->setCellValue('U1', 'Rate')
			->setCellValue('V1', 'IGST Rate')
			->setCellValue('W1', 'IGST Tax Amount')
			->setCellValue('X1', 'SGST Rate')
			->setCellValue('Y1', 'SGST Tax Amount')	
			->setCellValue('Z1', 'CGST Rate')
			->setCellValue('AA1', 'CGST Tax Amount')
			->setCellValue('AB1', 'HSN')
			->setCellValue('AC1', 'Description of goods sold')	
			->setCellValue('AD1', 'UQC of goods sold')	
			->setCellValue('AE1', 'Quantity of goods sold')	
			->setCellValue('AF1', 'Shipping Bill No.')	
			->setCellValue('AG1', 'Shipping Bill Date.')	
			->setCellValue('AH1', 'Status')
			->setCellValue('AI1', 'Return Type');
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
cellColor('R1', 'F28A8C');cellColor('S1', 'F28A8C');cellColor('T1', 'F28A8C');cellColor('U1', 'F28A8C');cellColor('V1', 'F28A8C');cellColor('W1', 'F28A8C');cellColor('X1', 'F28A8C');cellColor('Y1', 'F28A8C');cellColor('Z1', 'F28A8C');cellColor('AA1', 'F28A8C');cellColor('AB1', 'F28A8C');cellColor('AC1', 'F28A8C');cellColor('AD1', 'F28A8C');cellColor('AE1', 'F28A8C');cellColor('AF1', 'F28A8C');cellColor('AG1', 'F28A8C');cellColor('AH1', 'F28A8C');cellColor('AI1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
//echo "Select * from billing_master where $daterange and po_type='Sale Return' and from_location = '".$_SESSION['asc_code']."'  ";

$i=2;
$sql_loc=mysqli_query($link1,"Select * from billing_master where $daterange and (po_type='Sale Return' or  po_type='P2C') and to_location = '".$_SESSION['asc_code']."'  ");
while($row_loc = mysqli_fetch_array($sql_loc)){
$sql = mysqli_query($link1,"Select * from billing_product_items where challan_no= '$row_loc[challan_no]' and $product_id and $brand_id and $model_id");
while($row = mysqli_fetch_array($sql )){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row_loc['document_type'])
			 ->setCellValue('C'.$i, $row_loc['courier'])
			->setCellValue('D'.$i, $row_loc['docket_no'])
			->setCellValue('E'.$i, $row_loc['from_location'])
			->setCellValue('F'.$i, $row['job_no'])
			->setCellValue('G'.$i, $row_loc['po_no'])
          	->setCellValue('H'.$i, $row['partcode'])
			->setCellValue('I'.$i, getAnyDetails($row_loc['from_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('J'.$i, $row_loc['po_no'])
			->setCellValue('K'.$i, $row_loc['challan_no'])
			->setCellValue('L'.$i, dt_format2($row_loc['sale_date']))
			->setCellValue('M'.$i, $row_loc['from_gst_no'])
			->setCellValue('N'.$i, getAnyDetails($row_loc['from_stateid'],"state","stateid","state_master",$link1))
			->setCellValue('O'.$i, getAnyDetails($row_loc['to_stateid'],"state","stateid","state_master",$link1))
			->setCellValue('P'.$i, $row_loc['total_cost'])
			->setCellValue('Q'.$i, $row_loc['po_no'])
			->setCellValue('R'.$i, $row_loc['po_no'])
			->setCellValue('S'.$i, $row_loc['po_no'])
			->setCellValue('T'.$i, $row['discount_amt'])
			->setCellValue('U'.$i, $row['price'])
			->setCellValue('V'.$i, $row['igst_per'])
			->setCellValue('W'.$i, $row['igst_amt'])
			->setCellValue('X'.$i, $row['sgst_per'])
			->setCellValue('Y'.$i, $row['sgst_amt'])
			->setCellValue('Z'.$i, $row['cgst_per'])
			->setCellValue('AA'.$i, $row['cgst_amt'])
			->setCellValue('AB'.$i, $row['hsn_code'])
			->setCellValue('AC'.$i, getAnyDetails($row['partcode'],"part_name","partcode","partcode_master",$link1))
			->setCellValue('AD'.$i, $row['uom'])
			->setCellValue('AE'.$i, $row['okqty'])
			->setCellValue('AF'.$i, 'N/A')
			->setCellValue('AG'.$i,'N/A')
			->setCellValue('AH'.$i,getDispatchStatus($row_loc['status']))
			->setCellValue('AI'.$i, $row_loc['po_type']);
			$i++;
			}
					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="SRN _report.xlsx"');
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
