<?php   
require_once("../includes/dbconnect.php");
//////////////////////////// Activation date updatation///////////////////////////////////////

 $sql = "SELECT * FROM imei_data_auto where sale_date !='0000-00-00'";
$query=mysqli_query($link1, $sql) ;

$j=1;
while( $row=mysqli_fetch_array($query) ) {

/////////////  query to fetch  modelid  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$modelid = mysqli_fetch_array(mysqli_query($link1,"select model_id from model_master where model = '".$row['model']."' "));

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	//////////////////////////checking import///////////////////////////
 	$import = "SELECT * FROM imei_data_import where (imei1='".$row['imei1']."' or  imei2='".$row['imei1']."')";
	$import_row=mysqli_query($link1, $import) ;
	if(mysqli_num_rows($import_row)==0){

	if($modelid['model_id'] != ""){

	mysqli_query($link1,"insert into  imei_data_import set imei1='".$row['imei1']."' ,imei2='".$row['imei1']."', activation_date ='".$row['sale_date']."' , model_id = '".$modelid['model_id']."' ");

	mysqli_query($link1,"update imei_data_auto  set imp_in='Y' where (imei1='".$row['imei1']."' or  imei2='".$row['imei1']."')");	

		}
	
}else{

	mysqli_query($link1,"update imei_data_import set activation_date ='".$row['sale_date']."' where (imei1='".$row['imei1']."' or  imei2='".$row['imei1']."')");
	mysqli_query($link1,"update imei_data_auto  set imp_in='Y' where (imei1='".$row['imei1']."' or  imei2='".$row['imei1']."')");	
	}
	
	
	
   
}


?>
