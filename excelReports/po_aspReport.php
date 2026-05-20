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
							 ->setCategory("PO Report");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'State')
           ->setCellValue('C1', 'City')
         	->setCellValue('D1', 'ASC/L3 Name')
			->setCellValue('E1', 'ASC/L3 Code')
			->setCellValue('F1', 'Warehouse Name')
			->setCellValue('G1', 'Warehouse Code')
			->setCellValue('H1', 'PO No.')
			->setCellValue('I1', 'PO Date')
			 ->setCellValue('J1', 'POType')
			->setCellValue('K1', 'Model')
			->setCellValue('L1', 'Partcode')
			->setCellValue('M1', 'Partcode Description')
			->setCellValue('N1', 'Product')
			->setCellValue('O1', 'Brand')
			->setCellValue('P1', 'Pending Qty')
			->setCellValue('Q1', 'Dispatch Qty')
		      ->setCellValue('R1', 'Status');
			 
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
cellColor('R1', 'F28A8C');
cellColor('S1', 'F28A8C');
cellColor('T1', 'F28A8C');
cellColor('U1', 'F28A8C');
cellColor('V1', 'F28A8C');cellColor('W1', 'F28A8C');cellColor('X1', 'F28A8C');cellColor('Y1', 'F28A8C');cellColor('Z1', 'F28A8C');
////////////////////////////////

///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$sql_loc=mysqli_query($link1,"Select * from po_master where (po_date  >= '".$fromdate."'  and po_date  <='".$todate."')  and $status and from_code='".$_SESSION['asc_code']."'");
while($row_loc = mysqli_fetch_array($sql_loc)){
$sql = mysqli_query($link1,"select * from po_items where po_no = '$row_loc[po_no]' ");
while($row = mysqli_fetch_array($sql )){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, getAnyDetails($row_loc['from_state'],"state","stateid","state_master",$link1))
			->setCellValue('C'.$i, getAnyDetails($_REQUEST['city'],"city","cityid","city_master",$link1))
			->setCellValue('D'.$i, getAnyDetails($row_loc['from_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('E'.$i, $row_loc['from_code'])
			->setCellValue('F'.$i, getAnyDetails($row_loc['to_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('G'.$i, $row_loc['to_location'])
			->setCellValue('H'.$i, $row_loc['po_no'])
			->setCellValue('I'.$i, dt_format($row_loc['po_date']))
         	->setCellValue('J'.$i, $row_loc['potype'])
			->setCellValue('K'.$i, getAnyDetails($row['model_id'],"model","model_id","model_master",$link1)."/".$row['model_id'])		
			->setCellValue('L'.$i, $row['partcode'])
			->setCellValue('M'.$i, getAnyDetails($row['partcode'],"part_desc","partcode","partcode_master",$link1))
			->setCellValue('N'.$i, getAnyDetails($row['product_id'],"product_name","product_id","product_master",$link1)."/".$row['product_id'])
			  ->setCellValue('O'.$i, getAnyDetails($row['brand_id'],"brand","brand_id","brand_master",$link1)."/".$row['brand_id'])
			->setCellValue('P'.$i, $row['qty'])
			->setCellValue('Q'.$i, $row['processed_qty'])
			->setCellValue('R'.$i, getdispatchstatus($row['status']));
			$i++;	
			}		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="po_aspReport.xlsx"');
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
