<?php
require_once("../includes/config.php");
/** Error reporting */
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Asia/Calcutta');
$arrstatus = getJobStatus($link1);
//// extract all encoded variables
$modelid = base64_decode($_REQUEST['modelid']);
$productid = base64_decode($_REQUEST['proid']);
$brandid = base64_decode($_REQUEST['brand']);
$status = base64_decode($_REQUEST['status']);
$substatus = base64_decode($_REQUEST['substatus']);
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
/////get status///////////////
if($status !=""){
	$st=" status in ('".$status."' )";
}
else {
	$st="1";
}
/////get substatus///////////////
if($substatus !=""){
	$subst=" sub_status in ('".$substatus."' )";
}
else {
	$subst="1";
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
							 ->setCategory("All Jobs");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'State')
            ->setCellValue('C1', 'City')
         	 ->setCellValue('D1', 'Call Center Name')
			->setCellValue('E1', 'Call Center Code')
			->setCellValue('F1', 'ASC Name')
			->setCellValue('G1', 'Job For')
			->setCellValue('H1', 'Escalations From')
			->setCellValue('I1', 'Warranty Status')
			->setCellValue('J1', 'Job No.')
			->setCellValue('K1', SERIALNO)
			->setCellValue('L1', 'Product')
			->setCellValue('M1', 'Brand')
			->setCellValue('N1', 'Model')
			->setCellValue('O1', 'Customer Category')
			->setCellValue('P1', 'Customer Name')
			->setCellValue('Q1', 'Contact No.')
			->setCellValue('R1', 'Alternate Contact No.')
			->setCellValue('S1', 'Customer State')
			->setCellValue('T1', 'Region')
			 ->setCellValue('U1', 'Address')
			->setCellValue('V1', 'Email id')
			->setCellValue('W1', 'Pincode')
			->setCellValue('X1', 'Residence No')
			->setCellValue('Y1', 'Landmarks')
			->setCellValue('Z1', 'Open Date(DD-MM-YYYY)')
			->setCellValue('AA1', 'Purchase Date (DD-MM-YYYY)')
			->setCellValue('AB1', 'Installation Date(DD-MM-YYYY)')
			->setCellValue('AC1', 'AMC End Date(DD-MM-YYYY)')
			->setCellValue('AD1', 'AMC No.')
			->setCellValue('AE1', 'Entity Name')
			->setCellValue('AF1', 'Dealer Name')
			->setCellValue('AG1', 'Invoice No.')
			->setCellValue('AH1', 'Ageing')
			->setCellValue('AI1', 'Defect Reported')
			->setCellValue('AJ1', 'PNA/PO No.')
			->setCellValue('AK1', 'PNA 1')
			->setCellValue('AL1', 'PNA 1 Description')
			->setCellValue('AM1', 'Close Date')
			->setCellValue('AN1', 'Confirm Date')
			->setCellValue('AO1', 'CWH Challan No.')
			->setCellValue('AP1', 'CWH Challan Date')
			->setCellValue('AQ1', 'CWH Courier Name')
			->setCellValue('AR1', 'CWH Docket No.')
			->setCellValue('AS1', 'Escalate to L3 (SFR) Challan')
			->setCellValue('AT1', 'ASC To L3 Courier')
			->setCellValue('AU1',  'ASC To L3 Docket')
			->setCellValue('AV1', 'ASC To L3 Dispatch Date')
			->setCellValue('AW1', 'L3 SFR Receive Status')
		    ->setCellValue('AX1', 'L3 SFR Receive Date')
			->setCellValue('AY1', 'L3 DIspatch Challan')
			->setCellValue('AZ1', 'L3 Courier Name')
			->setCellValue('BA1', 'L3 Docket')
			->setCellValue('BB1', 'L3 Dispatch Date')
			->setCellValue('BC1', 'ASC Receive Status')
			->setCellValue('BD1', 'ASC Receive Date')
			->setCellValue('BE1', 'Pending at (ASC/L3)')
			->setCellValue('BF1', 'Eng Name')
			->setCellValue('BG1', 'Job Status')
			->setCellValue('BH1', ' Remark')
			->setCellValue('BI1', ' Customer Feed Back')
			->setCellValue('BJ1', ' Customer Remarks')
			->setCellValue('BK1', ' Area Type')
			->setCellValue('BL1', 'Pending Reason')
			->setCellValue('BM1', 'Closed Reason')
			->setCellValue('BN1', 'Cello Job Date')
			->setCellValue('BO1', 'Cello Job Time')
			->setCellValue('BP1', 'Brand Call Id')
			->setCellValue('BQ1', 'Brand Customer Id');
		
		   
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
\cellColor('W1', 'F28A8C');
cellColor('X1', 'F28A8C');
cellColor('Y1', 'F28A8C');
cellColor('Z1', 'F28A8C');
cellColor('AA1', 'F28A8C');
cellColor('AB1', 'F28A8C');
cellColor('AC1', 'F28A8C');
cellColor('AD1', 'F28A8C');
cellColor('AE1', 'F28A8C');
cellColor('AF1', 'F28A8C');
cellColor('AG1', 'F28A8C');
cellColor('AH1', 'F28A8C');
cellColor('AI1', 'F28A8C');
cellColor('AJ1', 'F28A8C');
cellColor('AK1', 'F28A8C');
cellColor('AL1', 'F28A8C');
cellColor('AM1', 'F28A8C');cellColor('AN1', 'F28A8C');cellColor('AO1', 'F28A8C');cellColor('AP1', 'F28A8C');cellColor('AQ1', 'F28A8C');cellColor('AR1', 'F28A8C');
cellColor('AS1', 'F28A8C');cellColor('AT1', 'F28A8C');cellColor('AU1', 'F28A8C');cellColor('AV1', 'F28A8C');cellColor('AW1', 'F28A8C');cellColor('AX1', 'F28A8C');cellColor('AY1', 'F28A8C');cellColor('AZ1', 'F28A8C');cellColor('BA1', 'F28A8C');cellColor('BB1', 'F28A8C');cellColor('BC1', 'F28A8C');cellColor('BD1', 'F28A8C');cellColor('BE1', 'F28A8C');cellColor('BF1', 'F28A8C');cellColor('BG1', 'F28A8C');cellColor('BH1', 'F28A8C');
cellColor('BI1', 'F28A8C');cellColor('BJ1', 'F28A8C');cellColor('BK1', 'F28A8C');cellColor('BL1', 'F28A8C');cellColor('BM1', 'F28A8C');cellColor('BN1', 'F28A8C');cellColor('BO1', 'F28A8C');cellColor('BQ1', 'F28A8C');cellColor('BP1', 'F28A8C');
////////////////////////////////

///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
if($pending == 'checked'){
	$sql_loc = mysqli_query($link1,"Select customer_id,sub_status,status,hc_feedback,imei,cust_problem,cust_problem2,cust_problem3,close_date,open_date,job_no,location_code,entity_type,state_id,city_id,current_location,call_for,call_type,warranty_status,product_id,brand_id,model,customer_type,customer_name,contact_no,area,dop,dname,inv_no,hand_date,eng_id,remark,hc_remark,area_type,reason,close_rmk,m_job_date,m_time,ticket_no,b_cust_id from jobsheet_data where current_location= '".$_SESSION['asc_code']."' and ".$model_id."   and ".$product_id." and ".$brand_id." and status not in('10' ,'12','6','11') and sub_status not in('92','411')");
}
else{
	//echo "Select * from jobsheet_data where (open_date >= '".$fromdate."'  and open_date <='".$todate."') and current_location = '".$_SESSION['asc_code']."' and ".$model_id." and ".$st."  and ".$subst." and ".$product_id." and ".$brand_id."";exit;
	$sql_loc = mysqli_query($link1,"Select customer_id,sub_status,status,hc_feedback,imei,cust_problem,cust_problem2,cust_problem3,close_date,open_date,job_no,location_code,entity_type,state_id,city_id,current_location,call_for,call_type,warranty_status,product_id,brand_id,model,customer_type,customer_name,contact_no,area,dop,dname,inv_no,hand_date,eng_id,remark,hc_remark,area_type,reason,close_rmk,m_job_date,m_time,ticket_no,b_cust_id from jobsheet_data where (open_date >= '".$fromdate."'  and open_date <='".$todate."') and current_location = '".$_SESSION['asc_code']."' and ".$model_id." and ".$st."  and ".$subst." and ".$product_id." and ".$brand_id."");
}

while($row_loc = mysqli_fetch_array($sql_loc)){
	
	$cust_det = explode("~",getAnyDetails($row_loc['customer_id'],"pincode,address1,stateid,landmark,email,phone,alt_mobile,cityid","customer_id","customer_master",$link1));
		$resst=$arrstatus[$row_loc['sub_status']][$row_loc['status']];
	if($resst!=''){
		$res_st=$resst;
	}else{
		$row_set=mysqli_fetch_array(mysqli_query($link1,"select  display_status from jobstatus_master where status_id='".$row_loc['sub_status']."' and main_status_id='".$row_loc['status']."'"));
		$res_st=$row_set['display_status'];
	}
	
	if( $row_loc['hc_feedback']==1){
	$feedback='Poor';
	}else if($row_loc['hc_feedback']==2){
	$feedback='Average';
	
	}else if($row_loc['hc_feedback']==3){
	$feedback='Good';
	
	}else if($row_loc['hc_feedback']==4){
	$feedback='Very Good';
	
	}else if($row_loc['hc_feedback']==5){
	$feedback='Excellent';
	
	}else{
	$feedback='';
	}
	$product_det = mysqli_fetch_assoc(mysqli_query($link1,"SELECT * FROM product_registered  where serial_no='".$row_loc['imei']."'"));
$voc1 = getAnyDetails($row_loc['cust_problem'] ,"voc_desc","voc_code","voc_master" ,$link1);
$voc2 = getAnyDetails($row_loc['cust_problem2'] ,"voc_desc","voc_code","voc_master" ,$link1);
$voc3 = getAnyDetails($row_loc['cust_problem3'] ,"voc_desc","voc_code","voc_master" ,$link1);
if($row_loc['close_date'] =='0000-00-00' ){ $aging = daysDifference($today,$row_loc['open_date']);} else {$aging = "--" ;}
if($row_loc['close_date']  != '0000-00-00'){$tat = daysDifference($row_loc['close_date'],$row_loc['open_date']);}else{ $tat = "--" ;}

		$pono_det =mysqli_query($link1,"Select * from auto_part_request where job_no= '".$row_loc['job_no']."' ");
		if(mysqli_num_rows($pono_det)>0){
	while($pono = mysqli_fetch_array($pono_det)){
		if($partdesc==""){
			 $partdesc=getAnyDetails($pono['partcode'],"part_desc","partcode","partcode_master",$link1);
		}else{
			 $partdesc.=",".getAnyDetails($pono['partcode'],"part_desc","partcode","partcode_master",$link1);
		}
		if($statestr==""){
			 $statestr=$pono['partcode'];
		}else{
			 $statestr.=",".$pono['partcode'];
		}
		
	}}else {
	$partdesc="";
	$statestr="";
	}	
		
		
		
	$pono = mysqli_fetch_array(mysqli_query($link1,"Select po_no from po_items where job_no= '".$row_loc['job_no']."' "));
	$challan_no=mysqli_fetch_array(mysqli_query($link1,"Select challan_no from billing_product_items where job_no= '".$row_loc['job_no']."' "));
	$info=mysqli_fetch_array(mysqli_query($link1,"Select dc_date,courier ,docket_no from billing_master where challan_no = '".$challan_no['challan_no']."' ")); 	
	$asc_part=mysqli_fetch_array(mysqli_query($link1,"Select * from sfr_transaction where job_no = '".$row_loc['job_no']."' "));
	$sfr_part=mysqli_fetch_array(mysqli_query($link1,"Select * from sfr_challan where challan_no = '".$asc_part['challan_no']."' "));
	if($row_loc['location_code'] == $asc_part['from_location']) {$status = "Pending at Asc to L3";} elseif ($row_loc['location_code'] == $asc_part['to_location'] ){$status = "Pending at L3 to Asc";}
	
	if($row_loc['entity_type']=='Others'){
	$entity="Others";
	
	}else{
	$entity= getAnyDetails($row_loc['entity_type'],"name","id","entity_type",$link1);
	}
	//print_r('ddddddd');exit;
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, cleanData(getAnyDetails($row_loc['state_id'],"state","stateid","state_master",$link1)))
			->setCellValue('C'.$i, cleanData(getAnyDetails($row_loc['city_id'],"city","cityid","city_master",$link1)))
			->setCellValue('D'.$i, cleanData(getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1)))
			->setCellValue('E'.$i, $row_loc['location_code'])
			->setCellValue('F'.$i, cleanData(getAnyDetails($row_loc['current_location'],"locationname","location_code","location_master",$link1)))
			->setCellValue('G'.$i, $row_loc['call_for'])
            ->setCellValue('H'.$i, $row_loc['call_type'])
			->setCellValue('I'.$i, $row_loc['warranty_status'])
			->setCellValue('J'.$i, $row_loc['job_no'])
			->setCellValue('K'.$i, $row_loc['imei'])
		    ->setCellValue('L'.$i, cleanData(getAnyDetails($row_loc['product_id'],"product_name","product_id","product_master",$link1)))
			->setCellValue('M'.$i, cleanData(getAnyDetails($row_loc['brand_id'],"brand","brand_id","brand_master",$link1)))    
			->setCellValue('N'.$i, $row_loc['model'])
			->setCellValue('O'.$i, $row_loc['customer_type'])
			->setCellValue('P'.$i, cleanData($row_loc['customer_name']))
		    ->setCellValue('Q'.$i," ". $row_loc['contact_no'])
		     ->setCellValue('R'.$i, cleanData($cust_det[6]))
			->setCellValue('S'.$i, getAnyDetails($cust_det[3],"state","stateid","state_master",$link1))
			->setCellValue('T'.$i,  cleanData($row_loc['area']))
			 ->setCellValue('U'.$i, cleanData($cust_det[1]))
			  ->setCellValue('V'.$i, cleanData($cust_det[4]))
			   ->setCellValue('W'.$i, " ".cleanData($cust_det[0]))
			    ->setCellValue('X'.$i, " ".cleanData($cust_det[5]))
				->setCellValue('Y'.$i, cleanData($cust_det[3]))
				->setCellValue('Z'.$i, dt_format($row_loc['open_date']))
				->setCellValue('AA'.$i, dt_format($row_loc['dop']))
			->setCellValue('AB'.$i, dt_format($product_det['installation_date']))
			->setCellValue('AC'.$i, dt_format($product_det['amc_end_date']))
			->setCellValue('AD'.$i, $product_det['amc_no'])
			->setCellValue('AE'.$i,cleanData($entity))
			->setCellValue('AF'.$i,cleanData($row_loc['dname']))
			->setCellValue('AG'.$i,cleanData($row_loc['inv_no']))
			->setCellValue('AH'.$i,$aging )
			->setCellValue('AI'.$i, cleanData($voc1."/".$voc2."/".$voc3))
			->setCellValue('AJ'.$i, $pono['po_no'])
			->setCellValue('AK'.$i, cleanData($statestr))
			->setCellValue('AL'.$i, cleanData($partdesc))
			->setCellValue('AM'.$i, dt_format($row_loc['close_date']))
			->setCellValue('AN'.$i, dt_format($row_loc['hand_date']))
				->setCellValue('A0'.$i, $challan_no['challan_no'])
			->setCellValue('AP'.$i, dt_format($info['dc_date']))
			->setCellValue('AQ'.$i, cleanData($info['courier']))
			->setCellValue('AR'.$i, $info['docket_no'])
			->setCellValue('AS'.$i, $asc_ch_details['challan_no'])
			->setCellValue('AT'.$i, cleanData($sfr_part['courier']))
			->setCellValue('AU'.$i, cleanData($sfr_part['docket_no']))
			->setCellValue('AV'.$i, dt_format($sfr_part['challan_date']))
			->setCellValue('AW'.$i, getdispatchstatus($sfr_part['status']))
			->setCellValue('AX'.$i, dt_format($sfr_part['receive_date']))
			->setCellValue('AY'.$i, $l4_challan_details['challan_no'])
			->setCellValue('AZ'.$i, cleanData($sfr_part_l4['courier']))
			->setCellValue('BA'.$i, cleanData($sfr_part_l4['docket_no']))
			->setCellValue('BB'.$i, dt_format($sfr_part_l4['challan_date']))
			->setCellValue('BC'.$i, getdispatchstatus($sfr_part_l4['status']))
			->setCellValue('BD'.$i, dt_format($sfr_part_l4['receive_date']))
			->setCellValue('BE'.$i, $sf_status)
			->setCellValue('BF'.$i, cleanData(getAnyDetails($row_loc['eng_id'],"locusername","userloginid","locationuser_master",$link1)))
			->setCellValue('BG'.$i, $res_st)
			->setCellValue('BH'.$i, cleanData($row_loc['remark']))
			->setCellValue('BI'.$i, cleanData($feedback))
			->setCellValue('BJ'.$i, cleanData($row_loc['hc_remark']))
			->setCellValue('BK'.$i, cleanData($row_loc['area_type']))
			->setCellValue('BL'.$i, cleanData($row_loc['reason']))
			->setCellValue('BM'.$i, cleanData($row_loc['close_rmk']))
			->setCellValue('BN'.$i, cleanData($row_loc['m_job_date']))
			->setCellValue('BO'.$i, cleanData($row_loc['m_time']))
			->setCellValue('BP'.$i, cleanData($row_loc['ticket_no']))
			->setCellValue('BQ'.$i, $row_loc['b_cust_id']);
			$i++;	
			$count++;		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="allcall_aspreport.xlsx"');
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
