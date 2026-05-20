<?php
require_once("../includes/config.php");
////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Calcutta');
$arrstatus = getJobStatus($link1);
//// extract all encoded variables
$status = base64_decode($_REQUEST['status']);
$location_code = base64_decode($_REQUEST['location_code']);
////// filters value/////
$date_range = explode(" - ",$_REQUEST['daterange']);
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != ""){
		$daterange = "open_date >= '".$date_range[0]."' and open_date <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}
/////get location///////////////
if($location_code!=""){
	$locationcode=" location_code in ('".$location_code."' )";
}
else {
	$locationcode="1";
}

if($_REQUEST['product'] != ""){
	$productid = "product_id = '".$_REQUEST['product']."'";
}else{
	$productid = "1";
}

if($_REQUEST['brand'] != ""){
	$brandid = "brand_id = '".$_REQUEST['brand']."'";
}else{
	$brandid = "brand_id in (".$access_brand.")";
}

if($_REQUEST['model'] != ""){
	$modelid = "model_id = '".$_REQUEST['model']."'";
}else{
	$modelid = "1";
}
///// status
if($status !=""){
	//$statusstr="call_type='Replacement' and sub_status in ('".$status."')";
	$statusstr="status='8' and sub_status in ('".$status."')";
}
else {
	//$statusstr="call_type='Replacement'";
	$statusstr="status='8'";
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
							 ->setCategory("DOA REPORT");


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
			->setCellValue('K1', 'IMEI1')
			->setCellValue('L1', 'IMEI2')
			->setCellValue('M1', 'Model')
			->setCellValue('N1', 'Customer Name')
			->setCellValue('O1', 'Contact No.')
			->setCellValue('P1', 'Open Date(DD-MM-YYYY)')
			->setCellValue('Q1', 'Activation Date(DD-MM-YYYY)')
			->setCellValue('R1', 'POP Date(DD-MM-YYYY)')
			->setCellValue('S1', 'Defect Reported')
			->setCellValue('T1', 'Symptom Reported')
			->setCellValue('U1', 'Eng Name (L3)')
			->setCellValue('V1', 'Job Status')
			->setCellValue('W1', ' Remark')
			->setCellValue('X1', ' Replacement Remark')
			->setCellValue('Y1', 'Product')
			->setCellValue('Z1', 'Brand')
			->setCellValue('AA1', 'Aging')
			->setCellValue('AB1', 'TAT')
			->setCellValue('AC1', 'Handover Date')
			->setCellValue('AD1', 'Approval Status')
			->setCellValue('AE1', 'Approval Date');
		
			
////////////////
///////////////////////
cellColor('A1:AE1', '90EE90');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
//echo "Select * from jobsheet_data where  ".$locationcode." and ".$statusstr." and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid;exit;
$sql_loc = mysqli_query($link1,"Select * from jobsheet_data where  ".$locationcode." and ".$statusstr." and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid);

while($row_loc = mysqli_fetch_array($sql_loc)){
$resst=$arrstatus[$row_loc['sub_status']][$row_loc['status']];
	if($resst!=''){
		$res_st=$resst;
	}else{
	}
	if ($row_loc['doa_approval']=='REPL-Y'){
	
	$app_status= "Approved";
	
	}
	if ($row_loc['doa_approval']=='REPL-N'){
	
	$app_status= "Rejected";
	
	}
	if ($row_loc['doa_approval']==""){
	
	$app_status = "Pending";
	
	}
	
	
$voc1 = getAnyDetails($row_loc['cust_problem'] ,"voc_desc","voc_code","voc_master" ,$link1);
$voc2 = getAnyDetails($row_loc['cust_problem2'] ,"voc_desc","voc_code","voc_master" ,$link1);
$voc3 = getAnyDetails($row_loc['cust_problem3'] ,"voc_desc","voc_code","voc_master" ,$link1);
if($row_loc['close_date'] =='0000-00-00' ){ $aging = daysDifference($today,$row_loc['open_date']);} else {$aging = "--" ;}
if($row_loc['close_date']  != '0000-00-00'){$tat = daysDifference($row_loc['close_date'],$row_loc['open_date']);}else{ $tat = "--" ;}
		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, getAnyDetails($row_loc['state_id'],"state","stateid","state_master",$link1))
			->setCellValue('C'.$i, getAnyDetails($row_loc['city_id'],"city","cityid","city_master",$link1))
			->setCellValue('D'.$i, getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('E'.$i, $row_loc['location_code'])
			->setCellValue('F'.$i, $row_loc['customer_type'])
			->setCellValue('G'.$i, $row_loc['call_for'])
            ->setCellValue('H'.$i, $row_loc['call_type'])
			->setCellValue('I'.$i, $row_loc['warranty_status'])
			->setCellValue('J'.$i, $row_loc['job_no'])
			->setCellValue('K'.$i, " ".$row_loc['imei'])
			->setCellValue('L'.$i, " ".$row_loc['sec_imei'])
			->setCellValue('M'.$i, $row_loc['model'])
			->setCellValue('N'.$i, $row_loc['customer_name'])
			->setCellValue('O'.$i, $row_loc['contact_no'])
			->setCellValue('P'.$i, dt_format($row_loc['open_date']))
			->setCellValue('Q'.$i, dt_format($row_loc['activation']))
			->setCellValue('R'.$i, dt_format($row_loc['dop']))
			->setCellValue('S'.$i, $voc1."/".$voc2."/".$voc3)
			->setCellValue('T'.$i, getAnyDetails($row_loc['symp_code'],"symp_code","symp_code","symptom_master",$link1))
			->setCellValue('U'.$i, $row_loc['eng_id'])
			->setCellValue('V'.$i, $resst)
			->setCellValue('W'.$i, $row_loc['remark'])
			->setCellValue('X'.$i, $row_loc['doa_remark'])
			->setCellValue('Y'.$i, getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1))
			->setCellValue('Z'.$i, getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1))
			->setCellValue('AA'.$i, $aging)
			->setCellValue('AB'.$i,  $tat)
			->setCellValue('AC'.$i,  $row_loc['hand_date'])
			->setCellValue('AD'.$i,  $app_status)
			->setCellValue('AE'.$i,  $row_loc['doa_ar_dt']);
			
			$i++;	
			$count++;		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="replacement_report.xlsx"');
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
