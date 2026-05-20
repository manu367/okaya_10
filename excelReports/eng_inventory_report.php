<?php
require_once("../includes/config.php");

/// filters value/////
$productarray = getProductArray($link1);
$brandarray = getBrandArray($link1);

$location_code=base64_decode($_REQUEST['location_code']);
$product_name=base64_decode($_REQUEST['product_name']);
$brand=base64_decode($_REQUEST['brand']);
$modelid=base64_decode($_REQUEST['modelid']);
/// filters value////
if($location_code != ""){
	$locationid = " and a.locationuser_code = '".$location_code."'";
}else{
	$locationid=" ";
}
## selected  product
if($product_name != ""){
	$productid = " and  b.product_id='".$product_name."'";
}else{
	$productid = " ";
}
## selected  brand name
if($brand != ""){
	$brandid = "and b.brand_id = '".$brand."'";
}else{
	$brandid =" ";
}
## selected  model
if($modelid != ""){
	$modelid = "and b.model_id like '%".$modelid."%'";
}else{
	$modelid = " ";
}
//////End filters value/////


///// in Transit  data for inventory


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
			->setCellValue('F1', 'Customer Price')
			->setCellValue('G1', 'Mount')
			->setCellValue('H1', 'Fresh')
			->setCellValue('I1', 'Defective')
			->setCellValue('J1', 'Missing')
			->setCellValue('K1', 'Fresh In-transit')
			->setCellValue('L1', 'MSL QTY')
			->setCellValue('M1', 'Fresh Replace')
			->setCellValue('N1', 'In Repair')
			->setCellValue('O1', 'Eng Name')
			->setCellValue('P1', 'Eng Code');
		
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

////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count =1;

$sql_loc=mysqli_query($link1,"SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price FROM user_inventory a, partcode_master b where a.location_code='".$_SESSION['asc_code']."' ".$locationid." and a.partcode=b.partcode ".$productid." ".$brandid."  ".$modelid." ");
while($row_loc = mysqli_fetch_array($sql_loc)){
			

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, $row_loc['partcode'])
			 ->setCellValue('C'.$i, getAnyDetails($row_loc["partcode"],"part_name","partcode","partcode_master",$link1))
			->setCellValue('D'.$i,getAnyDetails($row_loc["product_id"],"product_name","product_id","product_master",$link1))
			->setCellValue('E'.$i,getAnyDetails($row_loc["brand_id"],"brand","brand_id","brand_master",$link1))
			->setCellValue('F'.$i, $row_loc['customer_price'])
			->setCellValue('G'.$i, $row_loc['mount_qty'])
          	->setCellValue('H'.$i, $row_loc['okqty'])
			->setCellValue('I'.$i, $row_loc['faulty'])
			->setCellValue('J'.$i, $row_loc['missing'])
			->setCellValue('K'.$i, $row_loc['in_transit'] )
			->setCellValue('L'.$i, $row_loc['msl_qty'])
			->setCellValue('M'.$i,$row_loc['repl_qty'])
			->setCellValue('N'.$i,$row_loc['in_repair'])
			->setCellValue('O'.$i, getAnyDetails($row_loc['locationuser_code'],"locusername","userloginid","locationuser_master",$link1))
			->setCellValue('P'.$i, $row_loc['locationuser_code']);
			
			$count++; 
			$i++;	
			}	

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ENG_Stock_details_report.xlsx"');
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
