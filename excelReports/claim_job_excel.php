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
$ref_id = base64_decode($_REQUEST['id']);

$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "hand_date  >= '".$date_range[0]."' and  hand_date  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
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
							 ->setCategory("Claim Report");


$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
			->setCellValue('B1', 'Loction Code')
			->setCellValue('C1', 'Location Name')
            ->setCellValue('D1', 'Job No')
            ->setCellValue('E1', SERIIALNO)
         	 ->setCellValue('F1', 'Product Name')
			->setCellValue('G1', 'Brand Name')
			->setCellValue('H1', 'Model')
			->setCellValue('I1', 'Open Date(DD-MM-YYYY)')
			->setCellValue('J1', 'Close Date(DD-MM-YYYY)')
			->setCellValue('K1', 'Vist By')
			->setCellValue('L1', 'KMs.')
			->setCellValue('M1', 'Apporved / Reject')
			->setCellValue('N1', 'Apporved By')
			->setCellValue('O1', 'Approve Date')
			->setCellValue('P1', 'Remark')
				->setCellValue('Q1', 'Close TAT')
				->setCellValue('R1', 'Call Count')
					->setCellValue('S1', 'Service Charge')
			;
		
			
////////////////
///////////////////////
cellColor('A1:AE1', '90EE90');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;

$sql_loc = mysqli_query($link1,"Select * from job_claim_appr where 	".$daterange." and brand_id in (".$access_brand.")");

while($row_loc = mysqli_fetch_array($sql_loc)){
$resst=$arrstatus[$row_loc['sub_status']][$row_loc['status']];

		if($row_loc['app_status'] == 'Y'){ $app='Approved';}
		else if($row_loc['app_status'] == 'N'){ $app='Reject';}else{ $app= 'Pending For approval';}
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, $row_loc['action_by'])
			->setCellValue('C'.$i, getAnyDetails($row_loc['action_by'],"locationname","location_code","location_master",$link1))
			->setCellValue('D'.$i, $row_loc['job_no'])
			->setCellValue('E'.$i, getAnyDetails($row_loc['job_no'],"imei","job_no","jobsheet_data",$link1))
			->setCellValue('F'.$i, getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1))
			->setCellValue('G'.$i, getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1))
            ->setCellValue('H'.$i, getAnyDetails($row_loc['job_no'],"model","job_no","jobsheet_data",$link1))
			->setCellValue('I'.$i, dt_format(getAnyDetails($row_loc['job_no'],"open_date","job_no","jobsheet_data",$link1)))
			->setCellValue('J'.$i, dt_format($row_loc['hand_date']))
			->setCellValue('K'.$i, getAnyDetails($row_loc['eng_name'],"locusername","userloginid","locationuser_master",$link1))
			->setCellValue('L'.$i, $row_loc['travel_km'])
			->setCellValue('M'.$i, $app)
			->setCellValue('N'.$i, $row_loc['app_by'])
			->setCellValue('O'.$i, dt_format($row_loc['app_date']))
			->setCellValue('P'.$i, $row_loc['remark'])
			->setCellValue('Q'.$i, $row_loc['claim_tat'])
			->setCellValue('R'.$i, $row_loc['job_count'])
			->setCellValue('S'.$i, $row_loc['price']);
			
			$i++;	
			$count++;		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Claim_details.xlsx"');
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
