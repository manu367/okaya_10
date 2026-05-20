<?php
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);
$a=array();

if ($_REQUEST["model_sub_under_name"]!='')
	{

        $MODEL_SUB_UNDER_NAME = $_REQUEST['model_sub_under_name'];
        $STATUS = $_REQUEST['status'];

  


    $sql = "select MODEL_NAME,TIME,STATUS from report2 where model_sub_under_name = '".$MODEL_SUB_UNDER_NAME."' and status = '".$STATUS."'";
    
     
    $stmt = mysqli_query( $conn, $sql );
    if( $stmt === false) {
		$json = array('message' => 'Problem with record');
		header('Content-type: application/json');
		echo json_encode($json);
    }
    // $return_arr['himanshu'] = array();
    
    while ($row = mysqli_fetch_array($stmt)){
		// array_push($return_arr['himanshu'],
        $a['MODEL_NAME']=$row['MODEL_NAME'];
        $a['TIME']=$row['TIME'];
        $a['STATUS']=$row['STATUS'];
      
		header('Content-type: application/json');
		echo json_encode($a);
    
    
    


}
    
   mysqli_close($conn);
}

    


?>