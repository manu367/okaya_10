<?php
require_once("../includes/config.php");

/// filters value/////
$productarray = getProductArray($link1);
$brandarray = getBrandArray($link1);

$location_code=base64_decode($_REQUEST['location_code']);

/// filters value////
if($location_code != ""){
	$locationid = "  user_code = '".$location_code."'";
}else{
	$locationid="1 ";
}
## selected  product

## selected  model

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
            ->setCellValue('B1', 'Training Type')
            ->setCellValue('C1', 'Description')
         	->setCellValue('D1', 'Training Date')
			->setCellValue('E1', 'Score(Out Of 100)')
			->setCellValue('F1', 'Trainer Name')
			->setCellValue('G1', 'Engineer Name');
			
			
		
		
////////////////
///////////////////////
cellColor('A1:G1', 'F28A8C');


////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count =1;
$sql_loc=mysqli_query($link1,"SELECT * from tech_training where location_code ='".$_SESSION['asc_code']."' and '".$locationid ."' ");
while($row_loc = mysqli_fetch_array($sql_loc)){
			

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, $row_loc['type'])
			->setCellValue('C'.$i, $row_loc['tr_desc'])
		   	->setCellValue('D'.$i, dt_format($row_loc["t_date"]))
		    ->setCellValue('E'.$i,$row_loc["score"])
			->setCellValue('F'.$i,$row_loc["trainername"])
			->setCellValue('G'.$i,getAnyDetails($row_loc["user_code"],"locusername","userloginid","locationuser_master",$link1));
			
			$count++; 
			$i++;	
			}	

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ENG_Training Details_report.xlsx"');
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
