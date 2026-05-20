<?php
/* Database connection start */
require_once("../includes/config.php");
$productarray = getProductArray($link1);
$brandarray = getBrandArray($link1);
/// get access product
$access_brand = getAccessBrand($_SESSION['userid'],$link1);
///////get access state
$arrstate = getAccessState($_SESSION['userid'],$link1);
	
/* Database connection end */
// storing  request (ie, get/post) global array to a variable  
$requestData= $_REQUEST;
## selected  state
if($_REQUEST['statename'] != ""){
	$stt_id = " stateid='".$_REQUEST['statename']."'";
}else{
	$stt_id = $arrstate;
}

## selected  state
$date_range = explode(" - ",$_REQUEST['daterange']);
if($_REQUEST['daterange'] != ""){
	$daterange = "create_date  >= '".$date_range[0]."' and create_date  <= '".$date_range[1]."'";
}else{
	$daterange = "1";
}

## selected  location
if($_REQUEST['location_code'] != ""){
	$locationid = " a.location_code='".$_REQUEST['location_code']."'";
}else{
$locationid = " a.location_code='".$_REQUEST['location_code']."'";
	//$locationid = "a.location_code in (select location_code from location_master where stateid in(".$stt_id." ))";
}
/*if(is_array($_REQUEST['location_code'])){
	$locationstr="";
	$post_locationarr = $_REQUEST['location_code'];
	for($i=0; $i<count($post_locationarr); $i++){
		if($locationstr){
			$locationstr .= ",'".$post_locationarr[$i]."'";
		}else{
			$locationstr .= "'".$post_locationarr[$i]."'";
		}
	}
	$locationid=" a.location_code in (".$locationstr.")";
}else{
	$locationid="a.location_code in (select location_code from location_master where stateid in('".$stt_id."' ))";
}*/

## selected  product
/*if(is_array($_REQUEST['product_name'])){
	$productstr="";
	$post_product = $_REQUEST['product_name'];
	for($i=0; $i<count($post_product); $i++){
		if($productstr){
			$productstr .= ",'".$post_product[$i]."'";
		}else{
			$productstr .= "'".$post_product[$i]."'";
		}
	}
	$productid=" b.product_id in (".$productstr.")";
}else{
	$productid="1";
}*/
if($_REQUEST['product_name'] != ""){
	$productid = "b.product_id = '".$_REQUEST['product_name']."'";
}else{
	$productid = "1";
}
## selected  product name
if($_REQUEST['brand'] != ""){
	$brandid = "b.brand_id = '".$_REQUEST['brand']."'";
}else{
	$brandid ="b.brand_id in (".$access_brand.")";
}
## selected  model
if($_REQUEST['modelid'] != ""){
	$modelid = "b.model_id like '%".$_REQUEST['modelid']."%'";
}else{
	$modelid = "1";
}
$columns = array( 
// datatable column index  => database column name
	0 => 'a.id', 
	1 => 'a.partcode',
	2 => 'b.part_name',
	3 => 'b.product_id',
	4 => 'b.brand_id',
	5 => 'b.model',
	6 => 'b.customer_price',
	7 => 'a.mount_qty',
	8 => 'a.okqty',
	9 => 'a.faulty',
	10 => 'a.missing',
	11 => 'a.msl_qty',
	12 => 'a.in_transit',
	13 => 'a.repl_qty'
);
// getting total number records without any search
 $sql = "SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price,b.part_name";
 $sql.=" FROM client_inventory a, partcode_master b where ".$locationid ." and a.partcode=b.partcode and ".$productid." and ".$brandid." and ".$modelid." group by a.partcode";
//echo $sql;
//$sql.=" FROM price_master where ".$loc_state." and ".$product." and ".$loc_type."";
$query=mysqli_query($link1, $sql) or die("stock-grid-data_admin.php: get stock details1");
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.


$sql = "SELECT a.*, b.part_name, b.product_id, b.brand_id, b.customer_price,b.part_name";
$sql.=" FROM client_inventory a, partcode_master b where ".$locationid ." and a.partcode=b.partcode and ".$productid." and ".$brandid." and ".$modelid;
if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
	$sql.=" AND (a.partcode LIKE '".$requestData['search']['value']."%'";
	$sql.=" OR a.location_code LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR b.part_name LIKE '".$requestData['search']['value']."%' ";
	$sql.=" OR b.customer_partcode LIKE '".$requestData['search']['value']."%' )";
}
$sql.=" group by a.partcode";
$query=mysqli_query($link1, $sql) or die("stock-grid-data_admin.php: get stock details");
$totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."  ";
/* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	
//echo $sql;
$query=mysqli_query($link1, $sql) or die("stock-grid-data_admin.php: get stock details2");

$data = array();
$j=1;
while( $row=mysqli_fetch_array($query) ) {  // preparing an array
	$nestedData=array(); 

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
    
	$nestedData[] = $j; 
	$nestedData[] = $row["partcode"];
	$nestedData[] =  $row["part_name"];
	$nestedData[] = $productarray[$row["product_id"]];
	$nestedData[] = $brandarray[$row["brand_id"]];
	$nestedData[] = getAnyDetails($row['location_code'],"locationname","location_code","location_master",$link1);
		$nestedData[] = "<div align='center'><a href='excelexport.php?rname=".base64_encode("partledger_excel")."&location=".$row["location_code"]."&daterange=".$_REQUEST["daterange"]."&partcode=".$row["partcode"]."".$pagenav."' title='Export Part Ledger in excel'><i class='fa fa-file-excel-o fa-2x faicon' title='Export Part Ledger in excel'></i></a></div>";
	$nestedData[] = $opening;
	$nestedData[] = $local_date+$grn_date+$po_rec_date;
	$nestedData[] = $po_dis;
	$nestedData[] = $srn_rec_date;
	$nestedData[] = $srn_out_date;
	$nestedData[] = $adj_in_date;
	$nestedData[] = $adj_out_date;
	$nestedData[] = $eng_iss_date;
	$nestedData[] = $eng_rec_date;
	$nestedData[] = $opening+$in_date-$out_date;
	
	$data[] = $nestedData;
	$j++;
}



$json_data = array(
			"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
			"recordsTotal"    => intval( $totalData ),  // total number of records
			"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
			"data"            => $data   // total data array
			);

echo json_encode($json_data);  // send data as json format
?>
