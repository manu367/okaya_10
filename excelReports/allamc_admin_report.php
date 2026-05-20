<?php
require_once("../includes/config.php");
/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Calcutta');
$arrstatus = getJobStatus($link1);
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

//// extract all encoded variables
$modelid = base64_decode($_REQUEST['modelid']);
$productid = base64_decode($_REQUEST['proid']);
$brandid = base64_decode($_REQUEST['brand']);
$state = base64_decode($_REQUEST['state']);
$loc_code = base64_decode($_REQUEST['location_code']);
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
if($loc_code!=""){
	$locationcode="location_code in ('".$loc_code."')";
}
else {
	$locationcode="1";
}
/////get model///////////////
if($modelid!=""){
	$model_id="and model_id in ('".$modelid."' )";
}
else {
	$model_id="";
}
/////get product///////////////
if($productid !=""){
	$product_id="and product_id in ('".$productid."' )";
}
else {
	$product_id="";
}
/////get brand///////////////
if($brandid !=""){
	$brand_id="and brand_id in ('".$brandid."')";
}
else {
	$brand_id="";
}

/////get state///////////////
if($state !=""){
	$stateid="and state_id in('".$state."' )";
}
else {
	$stateid="";
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
							 ->setCategory("All AMC Jobs");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'State')
            ->setCellValue('C1', 'City')
         	->setCellValue('D1', 'ASC/L3 Name')
			->setCellValue('E1', 'ASC/L3 Code')
			->setCellValue('F1', 'Product')
			->setCellValue('G1', 'Brand')
			->setCellValue('H1', 'Customer Category ')
			->setCellValue('I1', 'Customer Name')
			->setCellValue('J1', 'Address ')
			->setCellValue('K1', 'Landmark')
			->setCellValue('L1', 'Pincode')
			->setCellValue('M1', 'Contact No.')
			->setCellValue('N1', 'Alternate Contact No')
			->setCellValue('O1', 'Customer State')
			->setCellValue('P1', 'Customer City')
			->setCellValue('Q1', 'Customer Email')
			->setCellValue('R1', 'Residence No.')
			->setCellValue('S1', 'Model')
			->setCellValue('T1', 'IMEI/Serial No')
			->setCellValue('U1', 'Open Date')
			->setCellValue('V1', 'AMC Duration.(In Days)')
			->setCellValue('W1', 'AMC Start Date')
			->setCellValue('X1', 'AMC Expiry Date')
			->setCellValue('Y1', 'AMC Type')
			->setCellValue('Z1', 'AMC Amount')
			->setCellValue('AA1', 'Bill Purchase Date')
			->setCellValue('AB1', 'Payment Mode')
			->setCellValue('AC1', 'Entity Name')
			->setCellValue('AD1', 'CR/Transaction Number')
			->setCellValue('AE1', 'Cheque Number')
			->setCellValue('AF1', 'Cheque Date')
			->setCellValue('AG1', 'Bank Name')
			->setCellValue('AH1', 'Payee Name');
			
			
////////////////
///////////////////////
//cellColor('B1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;

$sql_loc = mysqli_query($link1,"Select * from amc where ".$locationcode." and (update_date >= '".$fromdate."' and update_date <='".$todate."') ".$model_id." ".$stateid." ".$product_id." ".$brand_id."");

while($sql_amc = mysqli_fetch_array($sql_loc)){
$sql_cust= mysqli_query($link1,"Select * from customer_master where customer_id='".$sql_amc['customer_id']."' " );	
$row_loc = mysqli_fetch_array($sql_cust);


		///////////////////////////////////////////////////PNA Part Details//////////////////////////////

	
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, getAnyDetails($sql_amc['state_id'],"state","stateid","state_master",$link1))
			->setCellValue('C'.$i, getAnyDetails($sql_amc['city_id'],"city","cityid","city_master",$link1))
			->setCellValue('D'.$i, getAnyDetails($sql_amc['location_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('E'.$i, $sql_amc['location_code'])
			->setCellValue('F'.$i, getAnyDetails($sql_amc['product_id'],"product_name","product_id","product_master",$link1))
			->setCellValue('G'.$i, getAnyDetails($sql_amc['brand_id'],"brand","brand_id","brand_master",$link1))
            ->setCellValue('H'.$i, $row_loc['customer_type'])
			->setCellValue('I'.$i, $row_loc['customer_name'])
			->setCellValue('J'.$i, $row_loc['address1'])
			->setCellValue('K'.$i, $row_loc['landmark'])
			->setCellValue('L'.$i, $row_loc['pincode'])
			->setCellValue('M'.$i, $row_loc['mobile'])
			->setCellValue('N'.$i, $row_loc['alt_mobile'])
			->setCellValue('O'.$i, getAnyDetails($row_loc['stateid'],"state","stateid","state_master",$link1))
			->setCellValue('P'.$i, getAnyDetails($row_loc['cityid'],"city","cityid","city_master",$link1))
			->setCellValue('Q'.$i, $row_loc['email'])
			->setCellValue('R'.$i, $row_loc['phone'])
			->setCellValue('S'.$i, getAnyDetails($sql_amc['model_id'],"model","model_id","model_master",$link1))
			->setCellValue('T'.$i, $sql_amc['serial_no'])
			->setCellValue('U'.$i, dt_format($row_loc['update_date']))
			->setCellValue('V'.$i, $sql_amc['amc_duration'])
			->setCellValue('W'.$i, $sql_amc['amc_start_date'])
			->setCellValue('X'.$i, $sql_amc['amc_end_date'])
			->setCellValue('Y'.$i, $sql_amc['amc_type'])
			->setCellValue('Z'.$i, $sql_amc['amc_amount'])
			->setCellValue('AA'.$i, dt_format($sql_amc['purchase_date'])) 
			->setCellValue('AB'.$i, $sql_amc['mode_of_payment'])
			->setCellValue('AC'.$i, $sql_amc['entity_type'])
			->setCellValue('AD'.$i, $sql_amc['cr_no'])
			->setCellValue('AE'.$i, $sql_amc['cheque_no'])
			->setCellValue('AF'.$i, $sql_amc['cheque_date'])
			->setCellValue('AG'.$i, $sql_amc['bank_name'])
			->setCellValue('AH'.$i, $sql_amc['payee_name']);
			
			$i++;	
			$count++;		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="allamc_admin_report.xlsx"');
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
