<?php
require_once("../includes/config.php");
//// extract all encoded variables
$modelid = base64_decode($_REQUEST['model']);
$productid = base64_decode($_REQUEST['prod_code']);
$brandid = base64_decode($_REQUEST['brand']);
/// filters value/////
$brandarray = getBrandArray($link1);
$productarray = getProductArray($link1);

/// get access product
$get_accproduct = getAccessProduct($_SESSION['asc_code'],$link1);
/// get access brand
$get_accbrand = getAccessBrand($_SESSION['asc_code'],$link1);

 if($productid != ''){
	$product =" b.product_id in ('".$productid."')" ;}else{
	$product = "b.product_id in (".$get_accproduct.")";
	}
## selected  brand name
 if($brandid != ''){
	$brand= "b.brand_id in ('".$brandid."')" ;}else{
	$brand = "b.brand_id in (".$get_accbrand.")";
}

## selected  model
 if($modelid != ''){
	$model=" b.model_id in ('".$modelid."')"; }else{
	$model = "1";
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
							 ->setCategory("Summarize Sale");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Part code')
            ->setCellValue('C1', 'Description')
         	 ->setCellValue('D1', 'Product')
			->setCellValue('E1', 'Brand')
			->setCellValue('F1', 'Model')
			->setCellValue('G1', 'Offer Price')
			->setCellValue('H1', 'Stock List')
	        ->setCellValue('I1', 'Location Name');
		
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


////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$sql_loc=mysqli_query($link1,"SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price FROM client_inventory a, partcode_master b where  a.list_qty > 0  and a.location_code = '".$_SESSION['asc_code']."'  and a.partcode=b.partcode and ".$product." and ".$brand." and ".$model." group by a.partcode");
while($row_loc = mysqli_fetch_array($sql_loc)){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row_loc['partcode'])
			 ->setCellValue('C'.$i, $row_loc['part_name'])
			->setCellValue('D'.$i, getAnyDetails($row_loc["product_id"],"product_name","product_id" ,"product_master",$link1))
			->setCellValue('E'.$i,getAnyDetails($row_loc["brand_id"],"brand","brand_id" ,"brand_master",$link1))
			->setCellValue('F'.$i, $row_loc['model_id'])
			->setCellValue('G'.$i,$row_loc['list_price'])
          	->setCellValue('H'.$i, $row_loc['list_qty'])
	       
			->setCellValue('I'.$i, getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1));
		
			
			$i++;					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Stock_list_aspreport.xlsx"');
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
