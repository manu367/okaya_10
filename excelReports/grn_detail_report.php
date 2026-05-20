<?php
require_once("../includes/config.php");

/// filters value/////
$brandarray = getBrandArray($link1);
$productarray = getProductArray($link1);


## selected  location


//////End filters value/////
if ($_REQUEST['daterange'] != ""){
	$date_range = explode(" - ",$_REQUEST['daterange']);
	$daterange = "receive_date  >= '".$date_range[0]."' and receive_date  <= '".$date_range[1]."'";
}
else{
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
							 ->setCategory("Summarize Sale");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Locationweqqw')
            ->setCellValue('C1', 'Supplier Name')
         	 ->setCellValue('D1', 'PO No.')
			->setCellValue('E1', 'PO Date')
			->setCellValue('F1', 'Part code')
			->setCellValue('G1', 'Description')
			->setCellValue('H1', 'Gate Entry No')
			->setCellValue('I1', 'Invoice Date')
			->setCellValue('J1', 'GRN No')
			->setCellValue('K1', 'GRN Date')
			->setCellValue('L1', 'Received Qty')
			->setCellValue('M1', 'OK')
			->setCellValue('N1', 'Missing')
			->setCellValue('O1', 'Damage')
			->setCellValue('P1', 'Excess')
		    ->setCellValue('Q1', 'Price')
			 ->setCellValue('R1', 'Cost')
			  ->setCellValue('S1', 'Serial No');
		
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


////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;
//echo "SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price FROM client_inventory a, partcode_master b where ".$locationid." and a.partcode=b.partcode and ".$productid." and ".$brandid." and ".$modelid." group by a.partcode";
$ch= "SELECT * FROM grn_master where location_code='".$_SESSION['asc_code']."' and ".$daterange." order by sno";
$sql_loc=mysqli_query($link1,"SELECT * FROM grn_master where location_code='".$_SESSION['asc_code']."' and ".$daterange." order by sno");
while($row_loc = mysqli_fetch_array($sql_loc)){
	
	$sql_model2=mysqli_query($link1,"SELECT * FROM grn_data where grn_no='".$row_loc['grn_no']."' order by sno") or die(mysqli_error($link1));
	  $supplier_detail=mysqli_fetch_array(mysqli_query($link1,"select tax_type,bill_date from supplier_po_master where system_ref_no='".$row_loc['po_no']."'"));
//echo "SELECT * FROM supplier_po_data where system_ref_no='$row1[system_ref_no]' order by id";
  while($row = mysqli_fetch_array($sql_model2)){	
  if($row['type']=='GRN'){
	  $ship_qty=1;
	  $amt=$row['price'];
	  }
	  else{
		  $ship_qty=$row['shipped_qty'];
	  $amt=$row['okqty']*$row['price'];
		  
		  }
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, getAnyDetails($row_loc["location_code"],"locationname","location_code","location_master",$link1))
			 ->setCellValue('C'.$i, getAnyDetails($row_loc["party_code"],"name","id","vendor_master",$link1))
			->setCellValue('D'.$i,$row_loc["po_no"] )
			->setCellValue('E'.$i,$supplier_detail['bill_date'])
			->setCellValue('F'.$i,$row['partcode'])
			->setCellValue('G'.$i,getAnyDetails($row['partcode'],"part_name","partcode","partcode_master",$link1))
          	->setCellValue('H'.$i,$row_loc['gate_entry_no'])
			->setCellValue('I'.$i,$row_loc['inv_no_date'])
			->setCellValue('J'.$i,$row_loc['grn_no'] )
			->setCellValue('K'.$i,$row_loc['receive_date'] )
			->setCellValue('L'.$i,$ship_qty)
			->setCellValue('M'.$i,$row['okqty'])
			->setCellValue('N'.$i,$row['missing'])
			->setCellValue('O'.$i, $row['damage'])
			->setCellValue('P'.$i, $row['excess'])
	       ->setCellValue('Q'.$i, $row['price'])
			->setCellValue('R'.$i,$amt)
			->setCellValue('S'.$i, $row['imei1']);
		
			
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
header('Content-Disposition: attachment;filename="GRN_Details_report.xlsx"');
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
