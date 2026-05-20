<?php
require_once("../includes/config.php");

//error_reporting(E_ALL);
ini_set('max_execution_time', 300);
ini_set('memory_limit', '2048M');

////// filters value/////
//////// get date /////////////////////////
if ($_REQUEST['daterange'] != ""){
	$seldate = explode(" - ",$_REQUEST['daterange']);
	$fromdate = $seldate[0];
	$todate = $seldate[1];
}else{
	$seldate = $today;
	$fromdate = $today;
	$todate = $today;
}

/////get status(pending/ processed)///////////////
if($_REQUEST['status']==""){
	$status=" 1 ";
}else {
	$status="status='".$_REQUEST['status']."' ";
}

if($_REQUEST['po_typ']==""){
	$potyp=" 1 ";
}else {
	$potyp="potype='".$_REQUEST['po_typ']."' ";
}

//echo "Select po_date, status, po_no, from_state, from_address, from_code, to_code, potype from po_master where (po_date  >= '".$fromdate."'  and po_date  <='".$todate."') and to_code= '".$_SESSION['asc_code']."' and $status and $potyp ";

//exit;


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
			->setCellValue('J1', 'PO Entry Date')
			->setCellValue('K1', 'POType')
			->setCellValue('L1', 'Model')
			->setCellValue('M1', 'Partcode')
			->setCellValue('N1', 'Partcode Description')
			->setCellValue('O1', 'Vendor Partcode')
			->setCellValue('P1', 'Product')
			->setCellValue('Q1', 'Brand')
			->setCellValue('R1', 'PO Qty')
			->setCellValue('S1', 'Dispatch Qty')
			->setCellValue('T1', 'Pending Qty')
		  	->setCellValue('U1', 'Status')
			->setCellValue('V1', 'AGING')
			->setCellValue('W1', 'Job Open Date')
			->setCellValue('X1', 'Unit Price')
			->setCellValue('Y1', 'Courier Name')
			->setCellValue('Z1', 'Docket No.')
			->setCellValue('AA1', 'Docket Date')
			->setCellValue('AB1', 'Courier Amount')
			->setCellValue('AC1', 'Dispatch Remark')
			->setCellValue('AD1', 'Document No')
			->setCellValue('AE1', 'Document Date')
			->setCellValue('AF1', 'WH Stock for this Part (OKQTY)');

////////////////
///////////////////////
cellColor('A1:AF1', 'F28A8C');
///////////////////////////////////////////////

// Miscellaneous glyphs, UTF-8

$i=2;
$count=1;

$sql_loc=mysqli_query($link1,"Select po_date, status, po_no, from_state, from_address, from_code, to_code, potype from po_master where (po_date  >= '".$fromdate."'  and po_date  <='".$todate."') and to_code= '".$_SESSION['asc_code']."' and $status and $potyp ");

while($row_loc = mysqli_fetch_array($sql_loc)){
	
	if($row_loc['status'] == '1' ){ $aging = daysDifference($today,$row_loc['po_date']);} else {$aging = "--" ;}
		
$sql = mysqli_query($link1,"select partcode, job_no, model_id, partcode, product_id, brand_id, qty, processed_qty, status, process_challan from po_items where po_no = '".$row_loc['po_no']."' ");
while($row = mysqli_fetch_array($sql)){
	
	if($row['process_challan'] != ""){
		$sql_c = mysqli_query($link1,"select courier, docket_no, doc_price, disp_rmk, challan_no, sale_date, dc_date from billing_master where challan_no = '".$row['process_challan']."' ");
		$row_c = mysqli_fetch_array($sql_c);
		
		$courier_dt = $row_c['courier'];
		$docket_no_dt = $row_c['docket_no'];
		$docket_date_dt = $row_c['dc_date'];
		$doc_price_dt = $row_c['doc_price'];
		$disp_rmk_dt = $row_c['disp_rmk'];
		$challan_no_dt = $row_c['challan_no'];
		$sale_date_dt = $row_c['sale_date'];
		
	}else{
		$courier_dt = "";
		$docket_no_dt = "";
		$docket_date_dt = "";
		$doc_price_dt = "";
		$disp_rmk_dt = "";
		$challan_no_dt = "";
		$sale_date_dt = "";
	}
	
	$pending_qty=0;
	$pending_qty=($row['qty']-$row['processed_qty']);
  
  $partinfo= explode("(",getAnyDetails($row['partcode'],"part_desc","partcode","partcode_master",$link1));
  $ven_code = explode("~",getAnyDetails($row['partcode'],"vendor_partcode,customer_price","partcode","partcode_master",$link1));
  
  $ok_wh_qty = "0";
  if($row['partcode'] != ""){
		$sql_wh_stk = mysqli_query($link1,"select okqty, consqty from client_inventory where location_code = '".$_SESSION['asc_code']."' and partcode = '".$row['partcode']."' ");
		$row_wh_stk = mysqli_fetch_array($sql_wh_stk);
		$ok_wh_qty = $row_wh_stk['okqty'];
  }else{
	  $ok_wh_qty = "0";
  }
    
  if($row['job_no'] != ""){
	  $job_info1 = getAnyDetails($row['job_no'],"open_date","job_no","jobsheet_data",$link1);
	  $job_info = dt_format($job_info1);
  }else{
	  $job_info = "";
  }
  

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, getAnyDetails($row_loc['from_state'],"state","stateid","state_master",$link1))
			->setCellValue('C'.$i, $row_loc['from_address'])
			->setCellValue('D'.$i, getAnyDetails($row_loc['from_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('E'.$i, $row_loc['from_code'])
			->setCellValue('F'.$i, getAnyDetails($row_loc['to_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('G'.$i, $row_loc['to_code'])
			->setCellValue('H'.$i, $row_loc['po_no'])
			->setCellValue('I'.$i, $row['job_no'])
			->setCellValue('J'.$i, dt_format($row_loc['po_date']))
			->setCellValue('K'.$i, $row_loc['potype'])
			->setCellValue('L'.$i, getAnyDetails($row['model_id'],"model","model_id","model_master",$link1))		
			->setCellValue('M'.$i, $row['partcode'])
			->setCellValue('N'.$i, $partinfo[0])
			->setCellValue('O'.$i, str_replace(')','' ,$ven_code[0]))
			->setCellValue('P'.$i, getAnyDetails($row['product_id'],"product_name","product_id","product_master",$link1))
			->setCellValue('Q'.$i, getAnyDetails($row['brand_id'],"brand","brand_id","brand_master",$link1))
			->setCellValue('R'.$i, $row['qty'])
			->setCellValue('S'.$i, $row['processed_qty'])
			->setCellValue('T'.$i, $pending_qty)
			->setCellValue('U'.$i, getdispatchstatus($row['status']))
			->setCellValue('V'.$i, $aging)
			->setCellValue('W'.$i, $job_info)
			->setCellValue('X'.$i, $ven_code[1])
			->setCellValue('Y'.$i, $courier_dt)
			->setCellValue('Z'.$i, $docket_no_dt)
			->setCellValue('AA'.$i, $docket_date_dt)
			->setCellValue('AB'.$i, $doc_price_dt)
			->setCellValue('AC'.$i, $disp_rmk_dt)
			->setCellValue('AD'.$i, $challan_no_dt)
			->setCellValue('AE'.$i, $sale_date_dt)
			->setCellValue('AF'.$i, $ok_wh_qty);
			
			$i++;	
			$count++;
				
}
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="po_report.xlsx"');
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

