<?php
require_once("../includes/config.php");
/** Error reporting */
//// extract all encoded variables
$location_code = base64_decode($_REQUEST['location_code']);
$statusval = base64_decode($_REQUEST['status']);
$typeval = base64_decode($_REQUEST['type']);
$pending = base64_decode($_REQUEST['pending']);
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
/////get location///////////////
if($location_code!=""){
	$locationcode="from_code in ('".$location_code."' )";
}
else {
	$locationcode="1";
}
/////get status(pending/ processed)///////////////
if($statusval!=""){
	$status=" status in ('".$statusval."' )";
}
else {
	$status="1";
}

/////get type(po/pna)///////////////
if($typeval!=""){
	$type=" potype in ('".$typeval."' )";
}
else {
	$type="1";
}



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
							 ->setCategory("PO Report");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'State')
           ->setCellValue('C1', 'Asc Address')
         	->setCellValue('D1', 'ASC/L3 Name')
			->setCellValue('E1', 'ASC/L3 Code')
			->setCellValue('F1', 'Warehouse Name')
			->setCellValue('G1', 'Warehouse Code')
			->setCellValue('H1', 'PO No.')
			->setCellValue('I1', 'Job No.')
			->setCellValue('J1', 'PO Date')
			 ->setCellValue('K1', 'POType')
			->setCellValue('L1', 'Model')
			->setCellValue('M1', 'Partcode')
			->setCellValue('N1', 'Partcode Description')
			->setCellValue('O1', 'Vendor Partcode')
			->setCellValue('P1', 'Product')
			->setCellValue('Q1', 'Brand')
			->setCellValue('R1', 'Pending Qty')
			->setCellValue('S1', 'Dispatch Qty')
		     ->setCellValue('T1', 'Status')
			 ->setCellValue('U1', 'AGING');
			 
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
////////////////////////////////

///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
if($pending == 'checked'){
$sql_loc = mysqli_query($link1,"Select * from po_master where  status = '1'  and potype in ('PO' ,'PNA') ");
}
else{
$sql_loc=mysqli_query($link1,"Select * from po_master where (po_date  >= '".$fromdate."'  and po_date  <='".$todate."') and $locationcode and $status and $type");
}
while($row_loc = mysqli_fetch_array($sql_loc)){
if($row_loc['po_date'] =='0000-00-00' ){ $aging = daysDifference($today,$row_loc['po_date']);} else {$aging = "--" ;}
$sql = mysqli_query($link1,"select * from po_items where po_no = '$row_loc[po_no]' ");
while($row = mysqli_fetch_array($sql )){
  $partinfo= explode("(",getAnyDetails($row['partcode'],"part_desc","partcode","partcode_master",$link1));

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, getAnyDetails($row_loc['from_state'],"state","stateid","state_master",$link1))
			->setCellValue('C'.$i, $row_loc['from_address'])
			->setCellValue('D'.$i, getAnyDetails($row_loc['from_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('E'.$i, $row_loc['from_code'])
			->setCellValue('F'.$i, getAnyDetails($row_loc['to_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('G'.$i, $row_loc['to_location'])
			->setCellValue('H'.$i, $row_loc['po_no'])
			->setCellValue('I'.$i, $row['job_no'])
			->setCellValue('J'.$i, dt_format($row_loc['po_date']))
         	->setCellValue('K'.$i, $row_loc['potype'])
			->setCellValue('L'.$i, getAnyDetails($row['model_id'],"model","model_id","model_master",$link1))		
			->setCellValue('M'.$i, $row['partcode'])
			->setCellValue('N'.$i, $partinfo[0])
			->setCellValue('O'.$i, str_replace(')','' ,$partinfo[1]))
			->setCellValue('P'.$i, getAnyDetails($row['product_id'],"product_name","product_id","product_master",$link1))
			 ->setCellValue('Q'.$i, getAnyDetails($row['brand_id'],"brand","brand_id","brand_master",$link1))
			->setCellValue('R'.$i, $row['qty'])
			->setCellValue('S'.$i, $row['processed_qty'])
			->setCellValue('T'.$i, getdispatchstatus($row['status']))
			->setCellValue('U'.$i, $aging);
			$i++;	
			$count++;
			}		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="adminpendingpo.xlsx"');
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
