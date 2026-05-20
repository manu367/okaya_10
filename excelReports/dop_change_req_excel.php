<?php
require_once("../includes/config.php");
/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Calcutta');
$arrstatus = getJobStatus($link1);

////get access brand details
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

//// extract all encoded variables
//$status = base64_decode($_REQUEST['status']);
$product = base64_decode($_REQUEST['product']);
$brand = base64_decode($_REQUEST['brand']);
////// filters value/////
$date_range = explode(" - ",$_REQUEST['daterange']);
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != ""){
		$daterange = "entry_date >= '".$date_range[0]."' and entry_date <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}

if($product != ""){
	$productid = "product_id = '".$product."'";
}else{
	$productid = "1";
}

if($brand != ""){
	$brandid = "brand_id in ('".$brand."') ";
}else{
	$brandid = "brand_id in (".$access_brand.")";
}

///// status
if($status !=""){
	//$statusstr=" approve_status in ('".$status."')";
	$statusstr=" approve_status in ('','71','72') ";
}
else {
	$statusstr=" approve_status in ('','71','72') ";
}

//echo "Select * from dop_serial_change_request where ".$daterange." and ".$statusstr." and ".$productid." and ".$brandid." <br><br> ";
//exit;

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
							 ->setCategory("DOP CHANGE REQUEST");


$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
			->setCellValue('B1', 'Requested Serial No')
			->setCellValue('C1', 'Requested Brand')
			->setCellValue('D1', 'Requested Product')
			->setCellValue('E1', 'Requested Model')
			->setCellValue('F1', 'Requested Model ID')
			->setCellValue('G1', 'Requested DOP')
			->setCellValue('H1', 'Requested Warranty End days')
			->setCellValue('I1', 'Requested Warranty Days')
			->setCellValue('J1', 'Requested Ext. Warranty Days')
			->setCellValue('K1', 'Requested Warranty Status')
			->setCellValue('L1', 'Requested Warranty Source')
			->setCellValue('M1', 'Requested AMC Exp Date')
			->setCellValue('N1', 'Requested Warranty Type')
			->setCellValue('O1', 'Requested By name')
			->setCellValue('P1', 'Requested By ID')
			->setCellValue('Q1', 'Entry Date')
			->setCellValue('R1', 'Entry Time')
			->setCellValue('S1', 'Customer Name')
			->setCellValue('T1', 'Customer ID')
			->setCellValue('U1', 'Customer Mobile')
			->setCellValue('V1', 'Approval Status')
			->setCellValue('W1', 'Rejection Reason')
			->setCellValue('X1', 'Approve By Name')
			->setCellValue('Y1', 'Approve By ID')
			->setCellValue('Z1', 'Approval Remark')
			->setCellValue('AA1', 'Approval Date')
			->setCellValue('AB1', 'Approval Time')
			->setCellValue('AC1', 'Before Approval Job No')
	        ->setCellValue('AD1', 'Registered date')
	
	        ->setCellValue('AE1', 'Job status')
	        ->setCellValue('AF1', 'close date')
	
			->setCellValue('AG1', 'Before Approval Serial No')
			->setCellValue('AH1', 'Before Approval DOP')
			->setCellValue('AI1', 'Before Approval Calculation Source')
			->setCellValue('AJ1', 'Rejection Reason')
			->setCellValue('AK1', 'Location Name')
			->setCellValue('AL1', 'Location Code')
			->setCellValue('AM1', 'Location State')
			->setCellValue('AN1', 'Calculation Source')
			->setCellValue('AO1', 'Manufacturing Date')
			->setCellValue('AP1', 'Primary Sales Date')
			->setCellValue('AQ1', 'Secondary Sales Date')
			->setCellValue('AR1', 'Tertiary Sales Date');
		
////////////////
///////////////////////
cellColor('A1:AR1', '90EE90');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;

$sql_loc = mysqli_query($link1, "Select * from dop_serial_change_request where ".$daterange." and ".$statusstr." and ".$productid." and ".$brandid);

while($row_loc = mysqli_fetch_array($sql_loc)){

	$amc_exp = "";
	if($row_loc['amc_expiry_date']=="0000-00-00"){ $amc_exp = ""; }else{ $amc_exp = dt_format($row_loc['amc_expiry_date']); }

	$sts = "";
	if($row_loc['approve_status']==""){
		$sts = "Requested";
	}else if($row_loc['approve_status']=="71"){
		$sts = "Approved";
	}else if($row_loc['approve_status']=="72"){
		$sts = "Rejected";
	}else{
		$sts = "";
	}

	$eng_info = explode("~",getAnyDetails($row_loc['entry_by'],"locusername,location_code","userloginid","locationuser_master",$link1));
	$loc_name = getAnyDetails($eng_info[1],"locationname","location_code","location_master",$link1);
	$job_state = "";
	if($row_loc['old_job_state'] != ""){
		$job_state = getAnyDetails($row_loc['old_job_state'],"state","stateid","state_master",$link1);
	}else{
		$job_state = "";
	}
	$oldjobstatusid = getAnyDetails($row_loc['old_job_no'],"status","job_no","jobsheet_data",$link1);
	$oldjobstatus = getAnyDetails($oldjobstatusid,"display_status","status_id","jobstatus_master",$link1);
	
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, " ".$row_loc['serial_no'])
			->setCellValue('C'.$i, getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1))
			->setCellValue('D'.$i, getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1))
			//->setCellValue('Z'.$i, getAnyDetails($row_loc['model_id'],"model","model_id","model_master",$link1))
			->setCellValue('E'.$i, cleanData($row_loc['model']))
			->setCellValue('F'.$i, $row_loc['model_id'])
			->setCellValue('G'.$i, dt_format($row_loc['dop']))
			->setCellValue('H'.$i, dt_format($row_loc['warranty_end_date']))
			->setCellValue('I'.$i, $row_loc['warranty_days'])
			->setCellValue('J'.$i, $row_loc['ext_days'])
			->setCellValue('K'.$i, $row_loc['warranty_status'])
			->setCellValue('L'.$i, $row_loc['warranty_source'])
			->setCellValue('M'.$i, $amc_exp)
			->setCellValue('N'.$i, $row_loc['newProductProvidedSpinner_txt'])
			->setCellValue('O'.$i, $eng_info[0])
			->setCellValue('P'.$i, $row_loc['entry_by'])
			->setCellValue('Q'.$i, dt_format($row_loc['entry_date']))
			->setCellValue('R'.$i, $row_loc['entry_time'])
			->setCellValue('S'.$i, getAnyDetails($row_loc['customer_id'],"customer_name","customer_id","customer_master",$link1))
			->setCellValue('T'.$i, $row_loc['customer_id'])
			->setCellValue('U'.$i, $row_loc['mobile_no'])
			->setCellValue('V'.$i, $sts)
			->setCellValue('W'.$i, cleanData($row_loc['rejection_reason']))
			->setCellValue('X'.$i, getAnyDetails($row_loc['approve_by'],"name","username","admin_users",$link1))
			->setCellValue('Y'.$i, $row_loc['approve_by'])
			->setCellValue('Z'.$i, cleanData($row_loc['approval_remark']))
			->setCellValue('AA'.$i, $row_loc['approval_date'])
			->setCellValue('AB'.$i, $row_loc['approval_time'])
			->setCellValue('AC'.$i, $row_loc['old_job_no'])
		    ->setCellValue('AD'.$i, dt_format(getAnyDetails($row_loc['old_job_no'],"open_date","job_no","jobsheet_data",$link1)))
		
		    ->setCellValue('AE'.$i, $oldjobstatus)
		    ->setCellValue('AF'.$i, dt_format(getAnyDetails($row_loc['old_job_no'],"close_date","job_no","jobsheet_data",$link1)))
		
			->setCellValue('AG'.$i, $row_loc['old_imei'])
			->setCellValue('AH'.$i, dt_format($row_loc['old_dop']))
			->setCellValue('AI'.$i, $row_loc['old_calulation_source'])
			->setCellValue('AJ'.$i, cleanData($row_loc['rejection_reason']))
			->setCellValue('AK'.$i, $loc_name)
			->setCellValue('AK'.$i, $eng_info[1])
			->setCellValue('AM'.$i, $job_state)
			->setCellValue('AN'.$i, $row_loc['calulation_source'])
			->setCellValue('AO'.$i, dt_format($row_loc['manufacturing_date']))
			->setCellValue('AP'.$i, dt_format($row_loc['primary_sale_date']))
			->setCellValue('AQ'.$i, dt_format($row_loc['secondary_sale_date']))
			->setCellValue('AR'.$i, dt_format($row_loc['tertiary_sale_date']));
			
			$i++;	
			$count++;		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('DOP CHANGE REQUEST');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="dop_change_req.xlsx"');
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
