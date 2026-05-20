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
	$brandid =" and brand_id in ($access_brand) ";
}
## selected  model
if($modelid != ""){
	$modelid = "and model_id like '%".$modelid."%'";
}else{
	$modelid = "";
}

function engStockDetails($location,$part,$type,$link1){

	//echo "SELECT sum($type) as a  user_inventory where location_code='".$location."' and partcode='".$part."' group by partcode";
 $intransitd=mysqli_query($link1,"SELECT sum($type) as a  from user_inventory where location_code='".$location."' and partcode='".$part."' group by partcode");
				$intransit_data=mysqli_fetch_array($intransitd);
				
				
if($intransit_data['a']!=''){  return $intransit_data['a'];} else {   return 0;}
				
	
	}

//////End filters value/////


///// in Transit  data for inventory

function intransit($location,$part,$type,$link1){
	if($type=="P2C"){ $po_type="'P2C'";} else  {$po_type="'Sale Return','PNA','PO'";}
	
 $intransitd=mysqli_query($link1,"SELECT SUM(b.qty) AS allqty FROM billing_master a, billing_product_items b WHERE ( a.status =  '2' OR a.status =  '3') AND a.to_location =  '".$location."' AND a.challan_no = b.challan_no AND b.partcode =  '".$part."' AND a.po_type IN ($po_type) GROUP BY b.partcode");
				$intransit_data=mysqli_fetch_array($intransitd);

				if($intransit_data['allqty']!=''){  return $intransit_data['allqty'];} else {   return 0;}
	
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
			->setCellValue('H1', 'Location Price')
			->setCellValue('I1', 'RV Price')
		//	->setCellValue('J1', 'Mount')
	        ->setCellValue('J1', 'Engineer Fresh')
	        ->setCellValue('K1', 'Engineer Defective')
			->setCellValue('L1', 'ASP Fresh')
			->setCellValue('M1', 'ASP Defective')
			->setCellValue('N1', 'Missing')
			->setCellValue('O1', 'Fresh In-transit')
			->setCellValue('P1', 'Fresh Replace')
			->setCellValue('Q1', 'In Repair')
			->setCellValue('R1', 'Location Name')
			->setCellValue('S1', 'Location Code')
			->setCellValue('T1', 'Location State')
			->setCellValue('U1', 'Location City')
			->setCellValue('V1', 'Vandor Partcode');
		
///////////////////////
cellColor('A1:V1', 'F28A8C');
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;
$count =1;
$sql_loc=mysqli_query($link1,"SELECT * FROM client_inventory where ".$locationid);
while($row_loc = mysqli_fetch_array($sql_loc)){
$sql_part=mysqli_query($link1,"SELECT part_name, product_id, brand_id, model_id, customer_price, customer_partcode, vendor_partcode, location_price, l3_price FROM partcode_master where partcode='".$row_loc['partcode']."' ".$productid." ".$brandid." ".$modelid." group by partcode");
while($row_part = mysqli_fetch_array($sql_part)){
$mod_id=$row_part['model_id'];
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
			
			$loc_state_data=explode(",",getAnyDetails($row_loc['location_code'],"stateid","location_code","location_master",$link1));
			$loc_city_data=explode(",",getAnyDetails($row_loc['location_code'],"cityid","location_code","location_master",$link1));
			

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $count)
			->setCellValue('B'.$i, $row_loc['partcode'])
			->setCellValue('C'.$i, $row_part['part_name'])
			->setCellValue('D'.$i,getAnyDetails($row_part["product_id"],"product_name","product_id","product_master",$link1))
			->setCellValue('E'.$i,getAnyDetails($row_part["brand_id"],"brand","brand_id","brand_master",$link1))
			->setCellValue('F'.$i, $rslt_str)
			->setCellValue('G'.$i,$row_part['customer_price'])
			->setCellValue('H'.$i,$row_part['location_price'])
			->setCellValue('I'.$i,$row_part['l3_price'])
          //	->setCellValue('J'.$i, $row_loc['mount_qty'])
	        ->setCellValue('J'.$i, engStockDetails($row_loc['location_code'],$row_loc['partcode'],"okqty",$link1))
	        ->setCellValue('K'.$i, engStockDetails($row_loc['location_code'],$row_loc['partcode'],"faulty",$link1))
			->setCellValue('L'.$i, $row_loc['okqty'])
			->setCellValue('M'.$i, $row_loc['faulty'])
			->setCellValue('N'.$i, $row_loc['missing'])
			->setCellValue('O'.$i, $row_loc['in_transit'])
		/*	->setCellValue('L'.$i,intransit($row_loc['location_code'],$row_loc['partcode'],"OK",$link1))*/
			->setCellValue('P'.$i,$row_loc['repl_qty'])
			->setCellValue('Q'.$i,$row_loc['in_repair'])
			->setCellValue('R'.$i, getAnyDetails($row_loc['location_code'],"locationname","location_code","location_master",$link1))
			->setCellValue('S'.$i, $row_loc['location_code'])
			->setCellValue('T'.$i, getAnyDetails($loc_state_data['0'],"state","stateid","state_master",$link1))
			->setCellValue('U'.$i, getAnyDetails($loc_city_data['0'],"city","cityid","city_master",$link1))
			->setCellValue('V'.$i, $row_part['vendor_partcode']);
		
			
			$count++; 
			$i++;	
			}	
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Stock Details');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client?s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="stock_details_report.xlsx"');
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
