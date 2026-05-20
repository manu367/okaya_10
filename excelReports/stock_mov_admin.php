<?php
require_once("../includes/config.php");
//// get access brand /////
$access_brand = getAccessBrand($_SESSION['userid'],$link1);

/// filters value/////
$productarray = getProductArray($link1);
$brandarray = getBrandArray($link1);

$location_code=$_REQUEST['location_code'];
$product_name=$_REQUEST['product_name'];
$brand=$_REQUEST['brand'];
$modelid=$_REQUEST['modelid'];
$date_range = explode(" - ",$_REQUEST['daterange']);

/// filters value////
if($location_code != ""){
	$locationid = " location_code='".$location_code."'";
}else{
	$locationid=" 1";
}
## selected  product
if($product_name != ""){
	$productid = " and  product_id='".$product_name."'";
}else{
	$productid = "";
}
## selected  brand name
if($brand != ""){
	$brandid = "and brand_id = '".$brand."'";
}else{
	$brandid ="and brand_id in (".$access_brand.")";
}
## selected  model
if($modelid != ""){
	$modelid = "and model_id like '%".$modelid."%'";
}else{
	$modelid = "";
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
			->setCellValue('F1', 'Model')
			->setCellValue('G1', 'Opening Stock')
			->setCellValue('H1', 'Stock In')
			->setCellValue('I1', 'PO Dispatch')
			->setCellValue('J1', 'SRN IN')
			->setCellValue('K1', 'SRN OUT')
			->setCellValue('L1', 'Adjustment Plus')
			->setCellValue('M1', 'Adjustment Minus')
			->setCellValue('N1', 'Stock Issue To ENG')
			->setCellValue('O1', 'Receive From ENG')
			->setCellValue('P1', 'Closing Stock')
			->setCellValue('Q1', 'Location Name')
			->setCellValue('R1', 'Location State')
			->setCellValue('S1', 'Location City');
		
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
$count =1;
$sql_loc=mysqli_query($link1,"SELECT a.*, b.part_name, b.product_id, b.brand_id,b.model_id, b.customer_price,b.part_name FROM client_inventory a, partcode_master b where ".$locationid ." and a.partcode=b.partcode  ".$productid."  ".$brandid."  ".$modelid." group by a.partcode" );
while($row = mysqli_fetch_array($sql_loc)){



$local=stock_movement($row["partcode"],$row['location_code'],"IN","Local Purchase",$date_range[0],$date_range[1],"opening",$link1);
$po_receive=stock_movement($row["partcode"],$row['location_code'],"IN","Recieve Stock From Location",$date_range[0],$date_range[1],"opening",$link1);
$grn_receive=stock_movement($row["partcode"],$row['location_code'],"IN","Stock IN",$date_range[0],$date_range[1],"opening",$link1);
$srn_receive=stock_movement($row["partcode"],$row['location_code'],"IN","Sale Return Receive",$date_range[0],$date_range[1],"opening",$link1);
$adjt_in=stock_movement($row["partcode"],$row['location_code'],"IN","Admin Stock Adjustment",$date_range[0],$date_range[1],"opening",$link1);
$eng_in=stock_movement($row["partcode"],$row['location_code'],"IN","Recieve Stock From Eng",$date_range[0],$date_range[1],"opening",$link1);

$opening_stock_in=$local+$po_receive+$grn_receive+$srn_receive+$adjt_in+$eng_in;

$po_out=stock_movement($row["partcode"],$row['location_code'],"OUT","PO Dispatch",$date_range[0],$date_range[1],"opening",$link1);
$srn_out=stock_movement($row["partcode"],$row['location_code'],"OUT","Sale Return",$date_range[0],$date_range[1],"opening",$link1);
$eng_out=stock_movement($row["partcode"],$row['location_code'],"OUT","Issue To Eng",$date_range[0],$date_range[1],"opening",$link1);
$adjt_out=stock_movement($row["partcode"],$row['location_code'],"OUT","Admin Stock Adjustment",$date_range[0],$date_range[1],"opening",$link1);
//$consume=stock_movement($row["partcode"],$row['location_code'],"IN","CONSUME",$date_range[0],$date_range[1],"opening",$link1);
$opening_stock_out=$po_out+$srn_out+$eng_out+$adjt_out;

$opening=$opening_stock_in-$opening_stock_out;
$local_date=stock_movement($row["partcode"],$row['location_code'],"IN","Local Purchase",$date_range[0],$date_range[1],"DATE",$link1);
$grn_date=stock_movement($row["partcode"],$row['location_code'],"IN","Stock IN",$date_range[0],$date_range[1],"DATE",$link1);
$po_rec_date=stock_movement($row["partcode"],$row['location_code'],"IN","Recieve Stock From Location",$date_range[0],$date_range[1],"DATE",$link1);
$srn_rec_date=stock_movement($row["partcode"],$row['location_code'],"IN","Sale Return Receive",$date_range[0],$date_range[1],"DATE",$link1);
$adj_in_date=stock_movement($row["partcode"],$row['location_code'],"IN","Admin Stock Adjustment",$date_range[0],$date_range[1],"DATE",$link1);
$eng_rec_date= stock_movement($row["partcode"],$row['location_code'],"IN","Recieve Stock From Eng",$date_range[0],$date_range[1],"DATE",$link1);
$in_date= $local_date+$grn_date+$po_rec_date+$srn_rec_date+$adj_in_date+$eng_rec_date;
$po_dis=stock_movement($row["partcode"],$row['location_code'],"OUT","PO Dispatch",$date_range[0],$date_range[1],"DATE",$link1);
$srn_out_date= stock_movement($row["partcode"],$row['location_code'],"OUT","Sale Return",$date_range[0],$date_range[1],"DATE",$link1);
$adj_out_date=stock_movement($row["partcode"],$row['location_code'],"OUT","Admin Stock Adjustment",$date_range[0],$date_range[1],"DATE",$link1);
$eng_iss_date= stock_movement($row["partcode"],$row['location_code'],"OUT","Issue To Eng",$date_range[0],$date_range[1],"DATE",$link1);

$out_date=$po_dis+$srn_out_date+$adj_out_date+$eng_iss_date;



$mod_id=$row['model_id'];
	$explodee = explode(",",$mod_id);
	$rslt_str="";
	for($k=0;$k < count($explodee);$k++){
		$model_name = getAnyDetails($explodee[$k],"model","model_id","model_master",$link1);
		if($rslt_str==""){
          		$rslt_str= $model_name;
	   		}else{
          		$rslt_str.= ",".$model_name;
			}
	}
			
			$loc_state_data=explode(",",getAnyDetails($row['location_code'],"stateid","location_code","location_master",$link1));
			$loc_city_data=explode(",",getAnyDetails($row['location_code'],"cityid","location_code","location_master",$link1));
			

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row['partcode'])
			 ->setCellValue('C'.$i, $row['part_name'])
			->setCellValue('D'.$i,getAnyDetails($row["product_id"],"product_name","product_id","product_master",$link1))
			->setCellValue('E'.$i,getAnyDetails($row["brand_id"],"brand","brand_id","brand_master",$link1))
			->setCellValue('F'.$i, $rslt_str)
			->setCellValue('G'.$i,$opening)
          	->setCellValue('H'.$i,  $local_date+$grn_date+$po_rec_date)
			->setCellValue('I'.$i,$po_dis)
			->setCellValue('J'.$i, $srn_rec_date)
			->setCellValue('K'.$i, $srn_out_date)
			->setCellValue('L'.$i, $adj_in_date)
			
			->setCellValue('M'.$i,$adj_out_date)
			->setCellValue('N'.$i,$eng_iss_date)
			->setCellValue('O'.$i, $eng_rec_date)
			->setCellValue('P'.$i,$opening+$in_date-$out_date)
			->setCellValue('Q'.$i, getAnyDetails($row['location_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('R'.$i, getAnyDetails($loc_state_data['0'],"state","stateid","state_master",$link1))
			->setCellValue('S'.$i, getAnyDetails($loc_city_data['0'],"city","cityid","city_master",$link1));
		
			
			$count++; 
			$i++;	
	
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Stock_Movement.xlsx"');
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
