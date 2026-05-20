<?php

require_once("../includes/config.php");
/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Calcutta');
$arrstatus = getJobStatus($link1);
//// extract all encoded variables
$ref_id = base64_decode($_REQUEST['id']);
$refdate = base64_decode($_REQUEST['claim_month']);
$fromloc = base64_decode($_REQUEST['from_location']);

$frondate=$refdate."-"."06";

$tomonth=date("m",strtotime("+1 months",strtotime($frondate)));
 $fyear=date("Y",strtotime("+1 months",strtotime($frondate)));

$to_date=$fyear."-".$tomonth."-"."05";
/*echo "Select * from job_claim_appr where app_date between '".$frondate."' and '".$to_date."' and action_by='".$_SESSION['asc_code']."' and app_status='Y' and  	lb_claim_no";
exit;*/

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
							 ->setCategory("Claim Report");


$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
			->setCellValue('B1', 'Loction Code')
			->setCellValue('C1', 'Loction Name')
			->setCellValue('D1', 'Location City')
            ->setCellValue('E1', 'Job No')
            ->setCellValue('F1', 'Customer Name')
            ->setCellValue('G1', 'Customer Address')
            
            ->setCellValue('H1', SERIALNO)
         	 ->setCellValue('I1', 'Product Name')
			->setCellValue('J1', 'Brand Name')
			->setCellValue('K1', 'Model')
			->setCellValue('L1', 'Open Date(DD-MM-YYYY)')
			->setCellValue('M1', 'Close Date(DD-MM-YYYY)')
			->setCellValue('N1', 'Vist By')
			//->setCellValue('O1', 'Claim Tat')
			//->setCellValue('P1', 'status')
			//->setCellValue('Q1', 'Apporved / Reject')
			//->setCellValue('R1', 'Apporved By')
			//->setCellValue('S1', 'Approve Date')
			//->setCellValue('T1', 'Remark')
			->setCellValue('O1', 'status')
			->setCellValue('P1', 'Area Type')
			->setCellValue('Q1', 'Labour Rate');
		
			
////////////////
///////////////////////
cellColor('A1:Q1', '90EE90');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;

$sql_loc = mysqli_query($link1,"Select * from job_claim_appr where app_date between '".$frondate."' and '".$to_date."' and action_by='".$fromloc."' and app_status='Y'  and 	lb_claim_no!=''");

while($row_loc = mysqli_fetch_array($sql_loc)){
	$cust_id=explode("~",getAnyDetails($row_loc['job_no'],"customer_id,status","job_no","jobsheet_data",$link1));
	$cust_det = explode("~",getAnyDetails($cust_id['0'],"address1,customer_name","customer_id","customer_master",$link1));
	$resst=$arrstatus[$row_loc['sub_status']][$row_loc['status']];

	if($row_loc['app_status'] == 'Y'){ $app='Approved';}else{ $app= 'Reject';}
	
	///// amount details //////
	$lev_pric = "";
	$cp_qr = mysqli_fetch_array(mysqli_query($link1, "SELECT level_price FROM claim_price WHERE product_id = '".$row_loc['product_id']."' AND area_type = '".$row_loc['area_type']."' AND job_status = '".$row_loc['status']."' "));
	if($cp_qr['level_price']==""){
		$lev_pric = "";
	}else{
		$lev_pric = $cp_qr['level_price'];
	}
	
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, $row_loc['action_by'])
			->setCellValue('C'.$i, getAnyDetails($row_loc['action_by'],"locationname","location_code","location_master",$link1))
			->setCellValue('D'.$i, getAnyDetails(getAnyDetails($row_loc['action_by'],"cityid","location_code","location_master",$link1),"city","cityid","city_master",$link1))
			->setCellValue('E'.$i, $row_loc['job_no'])
			->setCellValue('F'.$i, $cust_det['1'])
			->setCellValue('G'.$i, $cust_det['0'])
			->setCellValue('H'.$i, getAnyDetails($row_loc['job_no'],"imei","job_no","jobsheet_data",$link1))
			->setCellValue('I'.$i, getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1))
			->setCellValue('J'.$i, getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1))
            ->setCellValue('K'.$i, getAnyDetails($row_loc['job_no'],"model","job_no","jobsheet_data",$link1))
			->setCellValue('L'.$i, dt_format(getAnyDetails($row_loc['job_no'],"open_date","job_no","jobsheet_data",$link1)))
			->setCellValue('M'.$i, dt_format($row_loc['hand_date']))
			->setCellValue('N'.$i, getAnyDetails($row_loc['eng_name'],"locusername","userloginid","locationuser_master",$link1))
			//->setCellValue('O'.$i, $row_loc['claim_tat'])
			//->setCellValue('P'.$i, getAnyDetails($cust_id['1'],"display_status","status_id","jobstatus_master",$link1))
			//->setCellValue('Q'.$i, $app)
			//->setCellValue('R'.$i, $row_loc['app_by'])
			//->setCellValue('S'.$i, dt_format($row_loc['app_date']))
			//->setCellValue('T'.$i, $row_loc['remark'])
			->setCellValue('O'.$i, getAnyDetails($cust_id['1'],"display_status","status_id","jobstatus_master",$link1))
			->setCellValue('P'.$i, $row_loc['area_type'])
			->setCellValue('Q'.$i, $lev_pric);
			
			$i++;	
			$count++;		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="claim_inv_excel_asp.xlsx"');
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
