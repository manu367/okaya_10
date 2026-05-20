<?php
/* Database connection start */
require_once("../includes/config.php");


## selected  location
if($_REQUEST['location_code'] != ""){
	$locationid = "location_code='".$_REQUEST['location_code']."'";
}else{
	$locationid = "1";
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
							 ->setCategory("Engineer Pincode Mapping");
// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
			->setCellValue('B1', 'Engineer Name')
			->setCellValue('C1', 'Engineer Code')
            ->setCellValue('D1', 'Pincode')
			->setCellValue('E1', 'Area Type')
            ->setCellValue('F1', 'Status')
			->setCellValue('G1', 'System ID');
		
////////////////

cellColor('A1:G1', 'F28A8C');

// Miscellaneous glyphs, UTF-8
$i=2;
$count=1;

$sql_loc=mysqli_query($link1,"SELECT * FROM location_pincode_access WHERE  location_code LIKE '%U%' order by id");
while($row1 = mysqli_fetch_array($sql_loc)){

	if($row1['statusid']=='1'){
	  $rcv='Active';
	  }
	  else{
		  $rcv='Deactive';		  
		  }
	
  
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, getAnyDetails($row1["location_code"],"locusername","userloginid","locationuser_master",$link1))
			->setCellValue('C'.$i, $row1["location_code"])
			->setCellValue('D'.$i, $row1["pincode"])
			->setCellValue('E'.$i, $row1['area_type'])
			->setCellValue('F'.$i, $rcv)
			->setCellValue('G'.$i, $row1['id']);

			$i++;	
			$count++;								

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Engineer Pincode Mapping');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="engineer_pincode_access_report.xlsx"');
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
