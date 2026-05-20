<?php
require_once("../includes/config.php");
/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Calcutta');

//// extract all encoded variables
$modelid = base64_decode($_REQUEST['model']);
$productid = base64_decode($_REQUEST['proid']);
$brandid = base64_decode($_REQUEST['brand']);
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

$datetype= $_REQUEST['typedate'];

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
							 ->setCategory("Part Consumption");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'State')
           ->setCellValue('C1', 'City')
         	->setCellValue('D1', 'ASC/L3 Name')
			->setCellValue('E1', 'ASC/L3 Code')
			 ->setCellValue('F1', 'Job Received From')
			->setCellValue('G1', 'Job For')
			->setCellValue('H1', 'Job Type')
			->setCellValue('I1', 'Warranty Status')
			->setCellValue('J1', 'Job No.')
			->setCellValue('K1', 'Replace IMEI1')
			->setCellValue('L1', 'Replace IMEI2')
			->setCellValue('M1', 'Customer Name')
			->setCellValue('N1', 'Contact No.')
			->setCellValue('O1', 'Open Date(MM-DD-YYYY)')
			->setCellValue('P1', 'POP Date(MM-DD-YYYY)')
			->setCellValue('Q1', 'Fault Code')
			->setCellValue('R1', 'Fault Code Description')
			->setCellValue('S1', 'Repair Code')
			 ->setCellValue('T1', 'Repair Code Description')
			->setCellValue('U1', 'Level')
			->setCellValue('V1', 'Part Consume')
			->setCellValue('W1', 'Partcode')
			->setCellValue('X1', 'Partcode Description')
			->setCellValue('Y1', 'Product')
			->setCellValue('Z1', 'Brand')
			->setCellValue('AA1', 'Model')
			->setCellValue('AB1', 'Close Date')
			->setCellValue('AC1', 'Handover Date')
			->setCellValue('AD1', 'Customer Type')
			->setCellValue('AE1', 'TAT')
			->setCellValue('AF1', 'Repair By')
			->setCellValue('AG1', 'Engineer Name');
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
cellColor('V1', 'F28A8C');
cellColor('W1', 'F28A8C');
cellColor('X1', 'F28A8C');
cellColor('Y1', 'F28A8C');
cellColor('Z1', 'F28A8C');
cellColor('AA1', 'F28A8C');cellColor('AB1', 'F28A8C');cellColor('AC1', 'F28A8C');cellColor('AD1', 'F28A8C');cellColor('AE1', 'F28A8C');cellColor('AF1', 'F28A8C');
cellColor('AG1', 'F28A8C');
////////////////////////////////

///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
$sql_loc=mysqli_query($link1,"Select * from repair_detail where ($datetype >= '".$fromdate."'  and $datetype <='".$todate."') and  repair_location ='".$_SESSION['asc_code']."' and ".$model_id."  ");

while($row_loc = mysqli_fetch_array($sql_loc)){
$jobsheet = mysqli_fetch_array(mysqli_query($link1,"select call_for,call_type , warranty_status, customer_type,customer_name,contact_no,open_date,dop,product_id,brand_id from jobsheet_data where job_no ='".$row_loc['job_no']."'"));
$sql = mysqli_fetch_array(mysqli_query($link1,"select cityid , stateid, location_code from location_master where location_code = '".$row_loc['location_code']."' "));
if($row_loc['close_date']  != '0000-00-00'){$tat = daysDifference($row_loc['close_date'],$jobsheet['open_date']);}else{ $tat = "--" ;}
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, getAnyDetails($sql['stateid'],"state","stateid","state_master",$link1))
			->setCellValue('C'.$i, getAnyDetails($sql['cityid'],"city","cityid","city_master",$link1))
			->setCellValue('D'.$i, getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('E'.$i, $row_loc['location_code'])
			->setCellValue('F'.$i, $jobsheet['customer_name'])
			->setCellValue('G'.$i, $jobsheet['call_for'])
            ->setCellValue('H'.$i, $jobsheet['call_type'])
			->setCellValue('I'.$i, $jobsheet['warranty_status'])
			->setCellValue('J'.$i, $row_loc['job_no'])
			->setCellValue('K'.$i, $row_loc['replace_imei1'])
			->setCellValue('L'.$i, $row_loc['replace_imei2'])
			->setCellValue('M'.$i, $jobsheet['customer_name'])
			->setCellValue('N'.$i, $jobsheet['contact_no'])
			->setCellValue('O'.$i, dt_format($jobsheet['open_date']))
			->setCellValue('P'.$i, dt_format($jobsheet['dop']))
			->setCellValue('Q'.$i, $row_loc['fault_code'])
			->setCellValue('R'.$i, getAnyDetails($row_loc['fault_code'],"symp_desc","symp_code","symptom_master",$link1))
			->setCellValue('S'.$i, $row_loc['repair_code'])
			->setCellValue('T'.$i, getAnyDetails($row_loc['repair_code'],"rep_desc","rep_code","repaircode_master",$link1))
			->setCellValue('U'.$i, $row_loc['rep_lvl'])
			->setCellValue('V'.$i, $row_loc['part_qty'])
			->setCellValue('W'.$i, $row_loc['partcode'])
			->setCellValue('X'.$i, getAnyDetails($row_loc['partcode'],"part_desc","partcode","partcode_master",$link1))
			->setCellValue('Y'.$i, getAnyDetails($jobsheet['product_id'],"product_name","product_id","product_master",$link1))
			->setCellValue('Z'.$i, getAnyDetails($jobsheet['brand_id'],"brand","brand_id","brand_master",$link1))
			->setCellValue('AA'.$i, getAnyDetails($row_loc['model_id'],"model","model_id","model_master",$link1))	
			->setCellValue('AB'.$i, dt_format($row_loc['close_date']))
			->setCellValue('AC'.$i, dt_format($row_loc['handover_date']))
			->setCellValue('AD'.$i, $jobsheet['customer_type'])
			->setCellValue('AE'.$i, $tat)
			->setCellValue('AF'.$i, getAnyDetails($row_loc['repair_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('AG'.$i, getAnyDetails($row_loc['eng_id'],"locusername","userloginid","locationuser_master",$link1));
			
			$i++;	
			$count++;				
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="partconsumeasp.xlsx"');
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
