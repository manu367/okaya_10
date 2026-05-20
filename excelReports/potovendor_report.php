<?php
require_once("../includes/config.php");

/// filters value/////
$brandarray = getBrandArray($link1);
$productarray = getProductArray($link1);



//////End filters value/////
if ($_REQUEST['daterange'] != ""){
	$date_range = explode(" - ",$_REQUEST['daterange']);
	$daterange = "entry_date  >= '".$date_range[0]."' and entry_date  <= '".$date_range[1]."'";
}
else{
	$daterange = "1";
}
## selected  status

if($_REQUEST['status'] != ""){

	$status = "status = '".$_REQUEST['status']."'";

}else{

	$status = "1";

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
            ->setCellValue('B1', 'Location')
            ->setCellValue('C1', 'Supplier Name')
         	 ->setCellValue('D1', 'PO No.')
			->setCellValue('E1', 'PO Date')
			->setCellValue('F1', 'Part code')
			->setCellValue('G1', 'Description')
			->setCellValue('H1', 'Request Qty')
			->setCellValue('I1', 'Received Qty')
			->setCellValue('J1', 'Price')
			->setCellValue('K1', 'Cost')
		    ->setCellValue('L1', 'Status');
		
////////////////
///////////////////////
cellColor('B1:M1', 'F28A8C');


////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
//echo "SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price FROM client_inventory a, partcode_master b where ".$locationid." and a.partcode=b.partcode and ".$productid." and ".$brandid." and ".$modelid." group by a.partcode";
$sql_loc=mysqli_query($link1,"SELECT * FROM supplier_po_master where location_code='".$_SESSION['asc_code']."' and ".$status." and ".$daterange."  order by id");
while($row_loc = mysqli_fetch_array($sql_loc)){
	
	$sql_model2=mysqli_query($link1,"SELECT * FROM supplier_po_data where system_ref_no='$row_loc[system_ref_no]' order by id") or die(mysqli_error($link1));
	 
//echo "SELECT * FROM supplier_po_data where system_ref_no='$row1[system_ref_no]' order by id";
  while($row = mysqli_fetch_array($sql_model2)){	

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, getAnyDetails($row_loc["location_code"],"locationname","location_code","location_master",$link1))
			 ->setCellValue('C'.$i, getAnyDetails($row_loc["party_name"],"name","id","vendor_master",$link1))
			->setCellValue('D'.$i, $row_loc["system_ref_no"] )
			->setCellValue('E'.$i,$row_loc['entry_date'])
			->setCellValue('F'.$i,$row['partcode'])
			->setCellValue('G'.$i,getAnyDetails($row['partcode'],"part_name","partcode","partcode_master",$link1))
			->setCellValue('H'.$i,$row['req_qty'])
          	->setCellValue('I'.$i,$row['qty'])
			->setCellValue('J'.$i,$row['price'])
			->setCellValue('K'.$i,$row['total_cost'] )
			->setCellValue('L'.$i,getdispatchstatus($row_loc['status']) );
		
			
			$i++;					
}
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PO_Vendor_Details_report.xlsx"');
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
