<?php

require_once("../includes/config.php");
$loc=base64_decode($_REQUEST['location']);
$type=base64_decode($_REQUEST['type']);

/** Error reporting */

////// filters value/////


/////get status(pending/ processed)///////////////

if($type!=""){

	$ty=" and potype ='".$type."' ";

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

							 ->setCategory("PO Report");





// Add some data

$objPHPExcel->setActiveSheetIndex(0)

            ->setCellValue('A1', 'S.No.')
			
			 ->setCellValue('B1', 'From Loctaion')

           ->setCellValue('C1', 'PO No.')

			->setCellValue('D1', 'Job No.')

			->setCellValue('E1', 'PO Type')

			->setCellValue('F1', 'Partcode')
			
			->setCellValue('G1', 'Vendor Partcode')
			
			->setCellValue('H1', 'Requested Qty')

			->setCellValue('I1', 'Pending Qty')

			->setCellValue('J1', 'Dispatch Qty')
			
			->setCellValue('K1', 'Part Description');

			 

////////////////

///////////////////////
cellColor('A1:K1', 'F28A8C');
///////////////////////////////////////////////

// Miscellaneous glyphs, UTF-8

$i=2;

$count=1;

$sql_loc=mysqli_query($link1,"Select * from po_master where to_code= '".$_SESSION['asc_code']."' ".$ty." and status in ('1','6') ");

while($row_loc = mysqli_fetch_array($sql_loc)){

$sql = mysqli_query($link1,"select * from po_items where po_no = '".$row_loc['po_no']."' and status in ('1','6')");

while($row = mysqli_fetch_array($sql )){

//$partinfo= getAnyDetails($row['partcode'],"vendor_partcode","partcode","partcode_master",$link1);
$partinfo= explode("~",getAnyDetails($row['partcode'],"part_desc,vendor_partcode","partcode","partcode_master",$link1));
$ped_qty=$row['qty']-$row['processed_qty'];

$objPHPExcel->setActiveSheetIndex(0)

            ->setCellValue('A'.$i, $count)

			->setCellValue('B'.$i, $row_loc['from_code'])
			
			->setCellValue('C'.$i, $row_loc['po_no'])

			->setCellValue('D'.$i, $row['job_no'])

			->setCellValue('E'.$i, $row_loc['potype'])

         	->setCellValue('F'.$i, $row['partcode'])
			
			->setCellValue('G'.$i, $partinfo[1])
			
			->setCellValue('H'.$i, $row['qty'])

			->setCellValue('I'.$i, $ped_qty)

			->setCellValue('J'.$i, "")
			->setCellValue('K'.$i, $partinfo[0]);
			$i++;	
			$count++;

			}		
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$filename=$type."_".po_pna_pending;
// Redirect output to a client’s web browser (Excel2007)

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header("Content-Disposition: attachment;filename=$filename.xlsx");

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

