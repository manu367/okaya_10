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
$access_state = getAccessState($_SESSION['userid'],$link1);
//// extract all encoded variables
$status = base64_decode($_REQUEST['status']);
$location_code = base64_decode($_REQUEST['location_code']);
$product = base64_decode($_REQUEST['product']);
$brand = base64_decode($_REQUEST['brand']);
$model = base64_decode($_REQUEST['model']);
$pending = base64_decode($_REQUEST['pending']);
$apr_rej_status = base64_decode($_REQUEST['apr_rej_status']);
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

if($model != ""){
	$modelid = "model_id = '".$model."'";
}else{
	$modelid = "1";
}
///// status
## selected  Status
if(is_array($_REQUEST['info'])){
	$statusstr="";
	$post_statusarr = $_REQUEST['info'];
	for($i=0; $i<count($post_statusarr); $i++){
		if($statusstr){
			$statusstr .= ",'".$post_statusarr[$i]."'";
		}else{
			$statusstr .= "'".$post_statusarr[$i]."'";
		}
	}
	$status="status in('81') and sub_status in ('81')";
}else if($apr_rej_status=='83' || $apr_rej_status=='85'){
	$status="status in('10')";
}else{
	$status="status in('81')";
}

## selected  apr rej status
if($apr_rej_status != ""){
	$apr_rej_st = "l3_status = '".$apr_rej_status."'";
}else{
	$apr_rej_st = "1";
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
	        ->setCellValue('B1', 'ASC Code')
         	->setCellValue('C1', 'ASC Name')
			->setCellValue('D1', 'Job No.')
	        ->setCellValue('E1', 'Serial Number')
	        ->setCellValue('F1', 'Product')
	        ->setCellValue('G1', 'Model')
	        ->setCellValue('H1', 'DOP Date')
	        ->setCellValue('I1', 'Warranty End Date')
	        ->setCellValue('J1', 'Warranty Status')
	        ->setCellValue('K1', 'Job Open Date')
	        ->setCellValue('L1', 'Manufacturing Date')
	        ->setCellValue('M1', 'Primary Sale Date')
	        ->setCellValue('N1', 'Aging')
			->setCellValue('O1', 'TAT')
	        ->setCellValue('P1', 'Customer Category')
	        ->setCellValue('Q1', 'Job Attend Point')
	        ->setCellValue('R1', 'Product Status')
	        ->setCellValue('S1', 'Physical Condition')
	        ->setCellValue('T1', 'Charager Installed')
	        ->setCellValue('U1', 'VOC')
	        ->setCellValue('V1', 'Problem Observed')
	        ->setCellValue('W1', 'Solution Given')
	        ->setCellValue('X1', 'Job Type')
	        ->setCellValue('Y1', 'Customer Name')
	        ->setCellValue('Z1', 'Contact No.')
	        ->setCellValue('AA1', 'State')
            ->setCellValue('AB1', 'City')
	        ->setCellValue('AC1', 'Pincode')
	        ->setCellValue('AD1', 'Customer Address')
	        ->setCellValue('AE1', 'Partner SAP Code')
	        ->setCellValue('AF1', 'Partner Name')
	        ->setCellValue('AG1', 'Partner Type')
	        ->setCellValue('AH1', 'Partner State')
			->setCellValue('AI1', 'Partner Distric')
	        ->setCellValue('AJ1', 'Partner Pincode')
	        ->setCellValue('AK1', 'Partner Mobile No')
	        ->setCellValue('AL1', 'Eng Name')
	        ->setCellValue('AM1', 'Eng ID')
	        ->setCellValue('AN1', 'Eng Mobile No')
	        ->setCellValue('AO1', 'Job Status')
	        ->setCellValue('AP1', 'Eng Remark')
	        //->setCellValue('AP1', ' Replacement Remark')
	        ->setCellValue('AQ1', 'Brand')
	       // ->setCellValue('AQ1', 'Handover Date')
	        ->setCellValue('AR1', 'Approval Status')
			->setCellValue('AS1', 'Approval Date & Time')
			->setCellValue('AT1', 'Request For')
			->setCellValue('AU1', 'L1/L2 Approval/Rejection Remarks');
			//->setCellValue('AW1', 'Job History Remark');
	
	
		
			
////////////////
///////////////////////
cellColor('A1:AU1', '90EE90');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;

//echo "Select * from jobsheet_data where app_reason != '' and status='50' ";exit;

//echo "Select * from jobsheet_data where  app_reason != '' and ".$locationcode." and ".$statusstr." and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid;exit;
//echo "Select * from jobsheet_data where ext1 = 'Replacement Request' and ".$locationcode." and ".$status." and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid;exit;
//print_r($apr_rej_st);
//echo "Select * from jobsheet_data where ext1 = 'Replacement Request' and (ext7='' or ext7 <= '90') and ".$locationcode." and ".$status." and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid." and ".$apr_rej_st;exit;

	$sql_loc = mysqli_query($link1,"Select * from jobsheet_data where ext1 = 'Replacement Request' and (ext7='' or ext7 <= '90') and state_id in ($access_state) and ".$locationcode." and ".$status." and ".$daterange." and ".$productid." and ".$brandid." and ".$modelid." and ".$apr_rej_st);


//$sql_loc = mysqli_query($link1,"Select * from jobsheet_data where app_reason != '' and status='50' ");

while($row_loc = mysqli_fetch_array($sql_loc)){
$resst=$arrstatus[$row_loc['sub_status']][$row_loc['status']];
	if($resst!=''){
		$res_st=$resst;
	}else{
	}
	/*if ($row_loc['doa_approval']=='REPL-Y'){
	
	$app_status= "Approved";
	
	}
	if($row_loc['doa_approval']=='Y'){
		
		$app_status= "Approved";
		}
	if ($row_loc['doa_approval']=='REPL-N'){
	
	$app_status= "Rejected";
	
	}
	if ($row_loc['doa_approval']==""){
	
	$app_status = "Pending";
	
	}*/
	
	if($row_loc['l3_status']=='83'){$app_status = "Rejected";}else if($row_loc['l3_status']=='85'){$app_status = "Same Back-Battery found okay on back up test";}else{$app_status = "Pending";}
	
	
	
$voc1 = getAnyDetails($row_loc['cust_problem'] ,"voc_desc","voc_code","voc_master" ,$link1);
$voc2 = getAnyDetails($row_loc['cust_problem2'] ,"voc_desc","voc_code","voc_master" ,$link1);
$voc3 = getAnyDetails($row_loc['cust_problem3'] ,"voc_desc","voc_code","voc_master" ,$link1);
if($row_loc['close_date'] =='0000-00-00' ){ $aging = daysDifference($today,$row_loc['open_date']);} else {$aging = "--" ;}
if($row_loc['close_date']  != '0000-00-00'){$tat = daysDifference($row_loc['close_date'],$row_loc['open_date']);}else{ $tat = "--" ;}

//////////////////////////////
$job_hist_rmk = "";
$jhr = mysqli_query($link1, "SELECT remark FROM call_history WHERE job_no = '".$row_loc['job_no']."' AND activity = 'Pending For Approval' ");
if(mysqli_num_rows($jhr)>0){
	$jhr_data = mysqli_fetch_array($jhr);
	$job_hist_rmk = $jhr_data['remark'];
}else{
	$job_hist_rmk = "";
}
	
	    $rdm_sql = "SELECT * FROM retailer_distibuter_master where sap_id='".$row_loc['partner_id']."'";
        $rdm_res = mysqli_query($link1, $rdm_sql);
        $rdm_row = mysqli_fetch_assoc($rdm_res);
	
	
	    if($row_loc['partner_type']=="1"){
			$partner_typ = "Distributor";
		} else if($row_loc['partner_type']=="2"){
			$partner_typ = "Direct dealer";
		}else if($row_loc['partner_type']=="3"){
			$partner_typ = "Retailer";
		}else{
			$partner_typ = "";
		}
	$repair_history = mysqli_query($link1, "SELECT * FROM repair_detail where job_no='" . $row_loc['job_no'] . "'");
	$solution_given="";
	$problem_observed="";
	//while ($repair_info = mysqli_fetch_assoc($repair_history)) {
	if($solution_given == ""){
		$solution_given =getAnyDetails($row_loc['problem_detect'], "rep_desc", "rep_code", "repaircode_master", $link1);
	} else {
		$solution_given.=",".getAnyDetails($row_loc['problem_detect'], "rep_desc", "rep_code", "repaircode_master", $link1);	
	}
	if($problem_observed == ""){
		$problem_observed = getAnyDetails($row_loc['ext6'], "defect_desc", "defect_code", "defect_master", $link1); 
	}else{
		$problem_observed.=",".getAnyDetails($row_loc['ext6'], "defect_desc", "defect_code", "defect_master", $link1); 
	}
		$appro_datetime = $row_loc['doa_ar_dt']." ".$row_loc['doa_ar_time'];
		
	//}
		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
		    ->setCellValue('B'.$i, $row_loc['current_location'])
			->setCellValue('C'.$i, getAnyDetails($row_loc['current_location'],"locationname","location_code","location_master",$link1))
			->setCellValue('D'.$i, $row_loc['job_no'])
		    ->setCellValue('E'.$i, " ".$row_loc['imei'])
		    ->setCellValue('F'.$i, getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1))
		    ->setCellValue('G'.$i, $row_loc['model'])
		    ->setCellValue('H'.$i, dt_format($row_loc['dop']))
		    ->setCellValue('I'.$i, dt_format($row_loc['warranty_end_date']))
		    ->setCellValue('J'.$i, $row_loc['warranty_status'])
		    ->setCellValue('K'.$i, dt_format($row_loc['open_date']))
		    ->setCellValue('L'.$i, dt_format($row_loc['manufacturing_date']))
		    ->setCellValue('M'.$i, dt_format($row_loc['primary_sale_date']))
		    ->setCellValue('N'.$i, $aging)
		    ->setCellValue('O'.$i,  $tat)
		    ->setCellValue('P'.$i, $row_loc['customer_type'])
		    ->setCellValue('Q'.$i, $row_loc['comp_attend'])
		    ->setCellValue('R'.$i, $row_loc['sold_unsold'])
		    ->setCellValue('S'.$i, getAnyDetails($row_loc["phy_cond"], "name", "id", "physical_condition", $link1))
		    ->setCellValue('T'.$i, $row_loc['charger_installed'])
		    ->setCellValue('U'.$i, $voc1."/".$voc2."/".$voc3)
		    //->setCellValue('V'.$i, getAnyDetails($row_loc['symp_code'],"symp_code","symp_code","symptom_master",$link1))
		    ->setCellValue('V'.$i, $problem_observed)
		    ->setCellValue('W'.$i, $solution_given)
		    ->setCellValue('X'.$i, $row_loc['call_for'])
		    ->setCellValue('Y'.$i, $row_loc['customer_name'])
		    ->setCellValue('Z'.$i, $row_loc['contact_no'])
			->setCellValue('AA'.$i, getAnyDetails($row_loc['state_id'],"state","stateid","state_master",$link1))
			->setCellValue('AB'.$i, getAnyDetails($row_loc['city_id'],"city","cityid","city_master",$link1))
			->setCellValue('AC'.$i, $row_loc['pincode'])
			->setCellValue('AD'.$i, $row_loc['address'])
			->setCellValue('AE'.$i, $row_loc['partner_id'])
		    ->setCellValue('AF'.$i, cleanData($rdm_row['name']))
			->setCellValue('AG'.$i, $partner_typ)
			->setCellValue('AH'.$i, cleanData($rdm_row['state']))
			->setCellValue('AI'.$i, cleanData($rdm_row['district']))
			->setCellValue('AJ'.$i, $rdm_row['pincode'])
			->setCellValue('AK'.$i, $rdm_row['mobile'])
			->setCellValue('AL'.$i, getAnyDetails($row_loc['eng_id'],"locusername","userloginid","locationuser_master",$link1))
			->setCellValue('AM'.$i, $row_loc['eng_id'])
		    ->setCellValue('AN'.$i, getAnyDetails($row_loc['eng_id'],"contactmo","userloginid","locationuser_master",$link1))
			->setCellValue('AO'.$i,  $resst)
			->setCellValue('AP'.$i,  $row_loc['remark'])
			//->setCellValue('AP'.$i,  $row_loc['doa_remark'])
			->setCellValue('AQ'.$i,  getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1))
			//->setCellValue('AQ'.$i,  $row_loc['hand_date'])
			->setCellValue('AR'.$i,  $app_status)
			->setCellValue('AS'.$i,  $appro_datetime)
			->setCellValue('AT'.$i,  $resst)
			//->setCellValue('AU'.$i,  $row_loc['app_reason']);
	        ->setCellValue('AU'.$i,  $row_loc['doa_remark']);
			//->setCellValue('AW'.$i,  $job_hist_rmk);
			
			
			$i++;	
			$count++;		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Replacement_req_appr_l1');
//print_r('dddddddddd');exit;

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="replacement_request_approval_l1.xlsx"');
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
