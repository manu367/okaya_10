<?php
require_once("../includes/config.php");

/// filters value/////
$brandarray = getBrandArray($link1);
$productarray = getProductArray($link1);
## selected  product name
if($_REQUEST['product_name'] != ""){
	$productid = "b.product_id = '".$_REQUEST['product_name']."'";
}else{
	$productid = "1";
}
## selected  product name
if($_REQUEST['brand'] != ""){
	$brandid = "b.brand_id = '".$_REQUEST['brand']."'";
}else{
	$brandid = "1";
}
## selected  location
if($_REQUEST['location_code'] != ""){
	$locationid = "a.location_code = '".$_REQUEST['location_code']."'";
}else{
	$locationid = "1";
}
## selected  model
if($_REQUEST['modelid'] != ""){
	$modelid = "b.model_id like '%".$_REQUEST['modelid']."%'";
}else{
	$modelid = "1";
}
//////End filters value/////
function intransit($location,$part,$type,$link1){
	if($type=="P2C"){ $po_type="'P2C'";} else  {$po_type="'Sale Return','PNA','PO'";}
	
 $intransitd=mysqli_query($link1,"SELECT SUM(b.qty) AS allqty FROM billing_master a, billing_product_items b WHERE ( a.status =  '2' OR a.status =  '3') AND a.to_location =  '".$location."' AND a.challan_no = b.challan_no AND b.partcode =  '".$part."' AND a.po_type IN ($po_type) GROUP BY b.partcode");
				$intransit_data=mysqli_fetch_array($intransitd);

				if($intransit_data['allqty']!=''){  return $intransit_data['allqty'];} else {   return 0;}
	
	}

function engStockDetails($location,$part,$type,$link1){

	//echo "SELECT sum($type) as a  user_inventory where location_code='".$location."' and partcode='".$part."' group by partcode";
 $intransitd=mysqli_query($link1,"SELECT sum($type) as a  from user_inventory where location_code='".$location."' and partcode='".$part."' group by partcode");
				$intransit_data=mysqli_fetch_array($intransitd);
				
				
if($intransit_data['a']!=''){  return $intransit_data['a'];} else {   return 0;}
				
	
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
            ->setCellValue('B1', 'Part code')
            ->setCellValue('C1', 'Description')
         	 ->setCellValue('D1', 'Product')
			->setCellValue('E1', 'Brand')
			->setCellValue('F1', 'Model')
			->setCellValue('G1', 'Customer Price')
			//->setCellValue('H1', 'Mount')
	        ->setCellValue('H1', 'Engineer Fresh')
			->setCellValue('I1', 'Engineer Defective')
			->setCellValue('J1', 'ASP Fresh')
			->setCellValue('K1', 'ASP Defective')
			->setCellValue('L1', 'Missing')
			->setCellValue('M1', 'Fresh In-transit')
			->setCellValue('N1', 'Fresh Replace')
			->setCellValue('O1', 'In Repair')
			->setCellValue('P1', 'Location Name');
		
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
//cellColor('O1', 'F28A8C');

////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
//echo "SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price FROM client_inventory a, partcode_master b where ".$locationid." and a.partcode=b.partcode and ".$productid." and ".$brandid." and ".$modelid." group by a.partcode";
$sql_loc=mysqli_query($link1,"SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price FROM client_inventory a, partcode_master b where a.location_code = '".$_SESSION['asc_code']."' and a.partcode=b.partcode and ".$productid." and ".$brandid." and ".$modelid." group by a.partcode");
while($row_loc = mysqli_fetch_array($sql_loc)){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row_loc['partcode'])
			 ->setCellValue('C'.$i, $row_loc['part_name'])
			->setCellValue('D'.$i,$productarray[$row_loc["product_id"]])
			->setCellValue('E'.$i,$brandarray[$row_loc["brand_id"]])
			->setCellValue('F'.$i, $row_loc['model_id'])
			->setCellValue('G'.$i,$row_loc['customer_price'])
          //	->setCellValue('H'.$i, $row_loc['mount_qty'])
	        ->setCellValue('H'.$i, engStockDetails($row_loc['location_code'],$row_loc['partcode'],"okqty",$link1))
			->setCellValue('I'.$i, engStockDetails($row_loc['location_code'],$row_loc['partcode'],"faulty",$link1))
			->setCellValue('J'.$i, $row_loc['okqty'])
			->setCellValue('K'.$i, $row_loc['faulty'])
			->setCellValue('L'.$i, $row_loc['missing'])
		->setCellValue('M'.$i,intransit($row_loc['location_code'],$row_loc['partcode'],"OK",$link1))
			->setCellValue('N'.$i,$row_loc['repl_qty'])
			->setCellValue('O'.$i,$row_loc['in_repair'])
			->setCellValue('P'.$i, getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1));
		
			
			$i++;					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Stock_details_report.xlsx"');
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