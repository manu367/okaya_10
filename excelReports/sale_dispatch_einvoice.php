<?php
require_once("../includes/config.php");

ini_set('max_execution_time', 300);
ini_set('memory_limit', '2048M');

/////// get Access brand////////////////////////
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
/////// get Access product category////////////////////////
$access_product = getAccessProduct($_SESSION['userid'],$link1);

/////// get Access state////////////////////////
$arrstate = getAccessState($_SESSION['userid'],$link1);

//// extract all encoded variables
$tostate = base64_decode($_REQUEST['to_state']);
$toloc = base64_decode($_REQUEST['to_loc']);
$brandid = base64_decode($_REQUEST['brand']);
$productid = base64_decode($_REQUEST['prod_code']);
$statusid = base64_decode($_REQUEST['status_id']);

////// filters value/////
//////// get date /////////////////////////

if ($_REQUEST['daterange'] != ""){
	$date_range = explode(" - ",$_REQUEST['daterange']);
	$daterange = " and (sale_date  >= '".$date_range[0]."' and sale_date  <= '".$date_range[1]."')";
}
else{
	$daterange = "and 1";
}

## selected  to_loc
if($toloc != ""){
	$to_location = "and to_location in ('".$toloc."')";
}else if($tostate != ""){
	$to_location = "and to_stateid in ('".$tostate."')";
}else{
	//$to_location = "and to_stateid in (".$arrstate.") ";
	$to_location = "and 1 ";
}

## selected  brand
/*if($brandid != ""){
	$brand_id = " and partcode  in (select partcode from partcode_master where brand_id  = '".$brandid."' ) ";
}else {
	$brand_id=" and 1 ";
}*/
/*
if($brandid != ""){
	$brand_id = "and partcode  in (select partcode from partcode_master where brand_id  = '".$brandid."' ) ";
}else if($brandid == "" && $toloc != ""){
	$ab = getAccessBrand($toloc,$link1);
	$brand_id = "and partcode  in (select partcode from partcode_master where brand_id  in (".$ab.") ) ";	
}else {
	$brand_id="and partcode  in (select partcode from partcode_master where brand_id  in (".$access_brand.") )";
}*/

## selected  brand
/*if($productid != ""){
	$product_id = " and partcode  in (select partcode from partcode_master where product_id = '".$productid."' ) ";
}else {
	$product_id=" and 1 ";
}*/
/*
if($productid != ""){
	$product_id = "and partcode  in (select partcode from partcode_master where product_id = '".$productid."' ) ";
}else if($productid == "" && $toloc != ""){
	$ap = getAccessProduct($toloc,$link1);
	$product_id = "and partcode  in (select partcode from partcode_master where product_id in (".$ap.") ) ";
}else {
	$product_id="and partcode  in (select partcode from partcode_master where product_id in (".$access_product.") ) ";
}*/

## selected  status
/*if($statusid != ""){
	$status_str = " and status = '".$statusid."' ";
}else {
	$status_str = " and 1 ";
}
*/
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
							 ->setCategory("E-Invoice Report");

// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
			->setCellValue('B1', 'SUPP_TYPE')
			->setCellValue('C1', 'REVERSE_CHARGE')
			->setCellValue('D1', 'E_COMM_GSTIN')
			->setCellValue('E1', 'IGST_ON_INTRA')	
            ->setCellValue('F1', 'DOC_TYPE')
			->setCellValue('G1', 'DOC_NO')
			->setCellValue('H1', 'DOC_DT')
			->setCellValue('I1', 'BUYER_GSTIN')
			->setCellValue('J1', 'BUYER_LEGAL_NAME')
			->setCellValue('K1', 'BUYER_TRADE_NAME')
			->setCellValue('L1', 'BUYER_POS')
			->setCellValue('M1', 'BUYER_ADD1')
			->setCellValue('N1', 'BUYER_ADD2')
			->setCellValue('O1', 'BUYER_LOCATION')
			->setCellValue('P1', 'BUYER_PIN')
			->setCellValue('Q1', 'BUYER_STATE')
			->setCellValue('R1', 'BUYER_PHONE')
			->setCellValue('S1', 'BUYER_EMAIL')
			->setCellValue('T1', 'DISPATCH_NAME')
			->setCellValue('U1', 'DISPATCH_ADDR1')
			->setCellValue('V1', 'DISPATCH_ADDR2')
			->setCellValue('W1', 'DISPATCH_LOCATION')
			->setCellValue('X1', 'DISPATCH_PINCODE')
			->setCellValue('Y1', 'DISPATCH_STATE')
			->setCellValue('Z1', 'SHIPPING_GSTIN')
			
			->setCellValue('AA1', 'SHIPPING_LEGANNAME')
			->setCellValue('AB1', 'SHIPPING_TRADENAME')
			->setCellValue('AC1', 'SHIPPING_ADDR1')
			->setCellValue('AD1', 'SHIPPING_ADDR2')
			->setCellValue('AE1', 'SHIPPING_LOCATION')
			->setCellValue('AF1', 'SHIPPING_PINCODE')
			->setCellValue('AG1', 'SHIPPING_STATE')
			->setCellValue('AH1', 'SL_NO')
			->setCellValue('AI1', 'PROD_DESC')
			->setCellValue('AJ1', 'IS_SERVICE')
			->setCellValue('AK1', 'HSN_CD')
			->setCellValue('AL1', 'BARCODE')
			->setCellValue('AM1', 'QTY')
			->setCellValue('AN1', 'FREE_QTY')
			->setCellValue('AO1', 'UNIT')
			->setCellValue('AP1', 'UNIT_PRICE')
			->setCellValue('AQ1', 'GROSS_AMT')
			->setCellValue('AR1', 'DISCOUNT')
			->setCellValue('AS1', 'PRE_TAX_VAL')
			->setCellValue('AT1', 'TAXABLE_VAL')
			->setCellValue('AU1', 'GST_RATE')
			->setCellValue('AV1', 'SGST_AMT')
			->setCellValue('AW1', 'CGST_AMT')
			->setCellValue('AX1', 'IGST_AMT')
			->setCellValue('AY1', 'CESS_RATE')
			->setCellValue('AZ1', 'CESS_AMT_ADVAL')
			
			->setCellValue('BA1', 'CESS_NON_ADVAL_AMT')
			->setCellValue('BB1', 'STATE_CESS_RATE')
			->setCellValue('BC1', 'STATE_CESS_ADVAL_AMT')
			->setCellValue('BD1', 'STATE_CESS_NON_ADVAL_AMT')
			->setCellValue('BE1', 'OTHER_CHARGES')
			->setCellValue('BF1', 'ITEM_TOTAL')
			->setCellValue('BG1', 'BATCH_NAME')
			->setCellValue('BH1', 'BATCH_EXP_DT')
			->setCellValue('BI1', 'WARRANTY_DT')
			->setCellValue('BJ1', 'TOTAL_TAXABLE_VAL')
			->setCellValue('BK1', 'TOT_SGST_AMT')
			->setCellValue('BL1', 'TOT_CGST_AMT')
			->setCellValue('BM1', 'TOT_IGST_AMT')
			->setCellValue('BN1', 'CESS_AMT')
			->setCellValue('BO1', 'STATE_CESS_AMT')
			->setCellValue('BP1', 'TOT_DISCOUNT')
			->setCellValue('BQ1', 'TOT_OTHER_CHARGES')
			->setCellValue('BR1', 'ROUND_OFF')
			->setCellValue('BS1', 'TOT_INV_VAL')
			->setCellValue('BT1', 'SHIPPING_BILLNO')
			->setCellValue('BU1', 'SHIPPING_BILL_DT')
			->setCellValue('BV1', 'PORT')
			->setCellValue('BW1', 'REFUND_CLAIM')
			->setCellValue('BX1', 'FOREIGN_CURRENCY')
			->setCellValue('BY1', 'COUNTRY_CODE')
			->setCellValue('BZ1', 'EXP_DUTY_AMT')
			
			->setCellValue('CA1', 'TRANS_ID')
			->setCellValue('CB1', 'TRANS_NAME')
			->setCellValue('CC1', 'TRANS_MODE')
			->setCellValue('CD1', 'DISTANCE')
			->setCellValue('CE1', 'TRANS_DOCNO')
			->setCellValue('CF1', 'TRANS_DOCDT')
			->setCellValue('CG1', 'VEHICLE_NO')
			->setCellValue('CG1', 'VEHICLE_TYPE');
		
			
           
///////////////////////
cellColor('A1:CG1', 'F28A8C');
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
//echo "Select * from billing_master where document_type='INV'  and from_gst_no!='' and  to_gst_no!='' ".$daterange."  ".$to_location." ";
$sql_loc=mysqli_query($link1, "Select * from billing_master where document_type='INV'  and from_gst_no!='' and  to_gst_no!='' ".$daterange."  ".$to_location." ");
//$sql_loc=mysqli_query($link1, "Select * from billing_master where (po_type='PO' or po_type='PNA' ) ".$status_str." ".$daterange."  ".$to_location." ");
while($row_loc = mysqli_fetch_array($sql_loc)){
	
$sql = mysqli_query($link1, "Select * from billing_product_items where challan_no= '".$row_loc['challan_no']."'");
while($row = mysqli_fetch_array($sql)){
$tax_per=$row['cgst_per']+$row['sgst_per']+$row['igst_per'];

/*$partdis="";
if($row['partcode']!="SERVICE"){
	$partdis=getAnyDetails($row['partcode'],"part_desc","partcode","partcode_master",$link1); 
}else{
	$partdis="SERVICE";
}
*/
/*$tolocation="";
if($row['to_location']!=""){
	$toloc=getAnyDetails($row['to_location'],"locationname","location_code","location_master",$link1);
	if($toloc){
		$tolocation=$toloc;
	}else{
		$tolocation=$row['to_location'];
	}
}else{
	$tolocation=$row['to_location'];
}	
if($row_loc['status']==4){
$recqty=$row['okqty'];
$broqty=$row['broken'];
$missqty=$row['missing'];
}else{
$recqty=0;
$broqty=0;
$missqty=0;

}
*/
//$ctyf=getAnyDetails($row_loc['from_location'],"cityid","location_code","location_master",$link1);
//$ctyt=getAnyDetails($row_loc['to_location'],"cityid","location_code","location_master",$link1);

//$part_info = mysqli_fetch_array(mysqli_query($link1, "select part_name, brand_id, product_id from partcode_master where partcode = '".$row['partcode']."' "));

//$po_dt = getAnyDetails($row_loc['po_no'],"po_date","po_no","po_master",$link1);

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			//->setCellValue('B'.$i, $row_loc['document_type'])
			->setCellValue('B'.$i, 'B2B')
			->setCellValue('C'.$i, 'No')
			->setCellValue('D'.$i, '')
			->setCellValue('E'.$i, 'No')	
            ->setCellValue('F'.$i, 'TAX INVOICE')
			->setCellValue('G'.$i, $row_loc['challan_no'])
			->setCellValue('H'.$i, str_replace("-","/",dt_format($row_loc['sale_date'])))
			->setCellValue('I'.$i, $row_loc['to_gst_no'])
			->setCellValue('J'.$i, $row_loc['party_name'])
			->setCellValue('K'.$i, $row_loc['party_name'])
			->setCellValue('L'.$i, $row_loc['to_state'])
			->setCellValue('M'.$i, $row_loc['to_addrs'])
			->setCellValue('N'.$i, '')
			->setCellValue('O'.$i, $row_loc['to_city'])
			->setCellValue('P'.$i, $row_loc['to_pincode'])
			->setCellValue('Q'.$i, $row_loc['to_state'])
			->setCellValue('R'.$i, $row_loc['to_phone'])
			->setCellValue('S'.$i, $row_loc['to_email'])
			
		/*	->setCellValue('T'.$i, 'DISPATCH_NAME')
			->setCellValue('U'.$i, 'DISPATCH_ADDR1')
			->setCellValue('V'.$i, 'DISPATCH_ADDR2')
			->setCellValue('W'.$i, 'DISPATCH_LOCATION')
			->setCellValue('X'.$i, 'DISPATCH_PINCODE')
			->setCellValue('Y'.$i, 'DISPATCH_STATE')
			->setCellValue('Z'.$i, 'SHIPPING_GSTIN')
			
			->setCellValue('AA'.$i, 'SHIPPING_LEGANNAME')
			->setCellValue('AB'.$i, 'SHIPPING_TRADENAME')
			->setCellValue('AC'.$i, 'SHIPPING_ADDR1')
			->setCellValue('AD'.$i, 'SHIPPING_ADDR2')
			->setCellValue('AE'.$i, 'SHIPPING_LOCATION')
			->setCellValue('AF'.$i, 'SHIPPING_PINCODE')
			->setCellValue('AG'.$i, 'SHIPPING_STATE')
			*/
			->setCellValue('T'.$i, '')
			->setCellValue('U'.$i, '')
			->setCellValue('V'.$i, '')
			->setCellValue('W'.$i, '')
			->setCellValue('X'.$i, '')
			->setCellValue('Y'.$i, '')
			->setCellValue('Z'.$i, '')
			
			->setCellValue('AA'.$i, '')
			->setCellValue('AB'.$i, '')
			->setCellValue('AC'.$i, '')
			->setCellValue('AD'.$i, '')
			->setCellValue('AE'.$i, '')
			->setCellValue('AF'.$i, '')
			->setCellValue('AG'.$i, '' )
			->setCellValue('AH'.$i, $count)  /* SL_NO */
			->setCellValue('AI'.$i, $row['part_name'])
			->setCellValue('AJ'.$i, 'No')
			->setCellValue('AK'.$i, $row['hsn_code'])
			->setCellValue('AL'.$i, '')
			->setCellValue('AM'.$i, $row['qty'])
			->setCellValue('AN'.$i, '')
			->setCellValue('AO'.$i, 'PIECES')
			->setCellValue('AP'.$i, $row['price'])
			->setCellValue('AQ'.$i, $row['value'])
			->setCellValue('AR'.$i, '0')
			->setCellValue('AS'.$i, $row['value'])
			->setCellValue('AT'.$i, $row['value'])
			->setCellValue('AU'.$i, $tax_per)
			->setCellValue('AV'.$i, $row['cgst_amt'])
			->setCellValue('AW'.$i, $row['sgst_amt'])
			->setCellValue('AX'.$i, $row['igst_amt'])
			->setCellValue('AY'.$i, $row['cess_rate'])
			->setCellValue('AZ'.$i, $row['cess_adval_amt'])
			
			->setCellValue('BA'.$i, $row['cess_non_adval_amt'])
			->setCellValue('BB'.$i, $row['state_cess_rate'])
			->setCellValue('BC'.$i, $row['state_cess_adval_amt'])
			->setCellValue('BD'.$i, $row['state_cess_non_adval_amt'])
			->setCellValue('BE'.$i, '')
			->setCellValue('BF'.$i, $row['item_total'])
			->setCellValue('BG'.$i, '')
			->setCellValue('BH'.$i, '')
			->setCellValue('BI'.$i, '')
			->setCellValue('BJ'.$i, $row_loc['basic_cost'])
			->setCellValue('BK'.$i, $row_loc['sgst_amt'])
			->setCellValue('BL'.$i, $row_loc['cgst_amt'])
			->setCellValue('BM'.$i, $row_loc['igst_amt'])
			->setCellValue('BN'.$i, '')
			->setCellValue('BO'.$i, '')
			->setCellValue('BP'.$i, '')
			->setCellValue('BQ'.$i, '0')
			->setCellValue('BR'.$i, $row_loc['round_off'])
			->setCellValue('BS'.$i, $row_loc['total_cost'])
			->setCellValue('BT'.$i, '')
			->setCellValue('BU'.$i, '')
			->setCellValue('BV'.$i, '')
			->setCellValue('BW'.$i, '')
			->setCellValue('BX'.$i, '')
			->setCellValue('BY'.$i, '')
			->setCellValue('BZ'.$i, '')
			
			->setCellValue('CA'.$i, '')
			->setCellValue('CB'.$i, '')
			->setCellValue('CC'.$i, '')
			->setCellValue('CD'.$i, '')
			->setCellValue('CE'.$i, '')
			->setCellValue('CF'.$i, '')
			->setCellValue('CG'.$i, '')
			->setCellValue('CG'.$i, '');
			
			$i++;
			$count++;
			}
					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('B2B Sale Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="e_invoice_report.xlsx"');
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
