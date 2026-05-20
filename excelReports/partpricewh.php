<?php
require_once("../includes/config.php");
date_default_timezone_set('Asia/Calcutta');

/////get location ///////////////
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
            ->setCellValue('B1', 'Partcode')
			 ->setCellValue('C1', 'Model')
            ->setCellValue('D1', 'Description')
         	 ->setCellValue('E1', 'Vendor partcode')
			->setCellValue('F1', 'Location Price')
			->setCellValue('G1', 'Customer Price');

	
			
			
////////////////
///////////////////////
cellColor('A1:G1', '90EE90');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;

$sql_loc = mysqli_query($link1, "SELECT partcode,part_desc,location_price,customer_price,vendor_partcode ,model_id FROM partcode_master where  status = '1' ");

while($row_loc = mysqli_fetch_array($sql_loc)){

		$part= explode(",",$row_loc['model_id']); 
			           $partpresent   = count($part);
					   if($partpresent == '1'){
					   $name = getAnyDetails($part[0],"model","model_id","model_master",$link1 );
					   }
					   else if($partpresent >1){
					     $name ='';
					   for($j=0 ; $j<$partpresent; $j++){					 
			 			$name.=  getAnyDetails($part[$j],"model","model_id","model_master",$link1 ).",";
			 			}}
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, $row_loc["partcode"])
			->setCellValue('C'.$i, $name)
			->setCellValue('D'.$i, $row_loc['part_desc'])
			->setCellValue('E'.$i, $row_loc['vendor_partcode'])
			->setCellValue('F'.$i, $row_loc['location_price'])
			->setCellValue('G'.$i, $row_loc['customer_price']);


			$i++;	
			$count++;		
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="partpricewh.xlsx"');
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
