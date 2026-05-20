<?php
include_once 'db_functions.php';     
$db = new DB_Functions();
$reflection_class = new ReflectionClass($db);
$private_variable = $reflection_class->getProperty('link');
$private_variable->setAccessible(true);
$conn=$private_variable->getValue($db);


// Try Post Method
/*if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
	$json = file_get_contents('php://input'); // Catching input
	$value = json_decode($json, true);


	$ModelName =$value['model_name'];
	$status = $value['status'];
	$warr_month_used = $value['warr_month_used'];
	$Time = $value['Time'];*/



//Get Method
if ($_SERVER["REQUEST_METHOD"] == "GET")
	{
	//$json = file_get_contents('php://input'); // Catching input
	//$value = json_decode($json, true);


	$ModelName =$_GET['model_name'];
	$status = $_GET['status'];
        //  $status = "IN";
	$warr_month_used = $_GET['warr_month_used'];
	$Time = $_GET['Time'];


	if ($conn)
		{
			
		if($_GET['csat']!=''){
		
			$json = array('result' => 'Customer Satisfied battery test PASS');
			/* output in necessary format */
			header('Content-type: application/json');
			//echo json_encode($sql);
			echo json_encode($json);	
			
		}
		else{
		//$sql = "SELECT concat(result,'-',srno) as result from report2 where model_name = '$ModelName' and status = '$status' and warr_month_used = '$warr_month_used' and time <='$Time'";
		$sql = "SELECT concat(result) as result from report2 where model_code = '$ModelName' and status = '$status' and warr_month_used = '$warr_month_used' and time <='$Time'";
	//	echo ($sql);
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0)

		{

		while (($row = mysqli_fetch_assoc($result)) != false)
			{

			$json = $row;
		    header('Content-type: application/json');
		    echo json_encode($json);
				
			}   #### END While

		}   #### END IF for Numrows
		else{
			
			$json = array('result' => 'FAIL');
			/* output in necessary format */
			header('Content-type: application/json');
			//echo json_encode($sql);
			echo json_encode($json);
			
		}  #### END IF
		}  #### END IF CSAT Condition
		}  #### END IF Connection
		
	}
  else
	{
	$json = array('success' => 0,'message' => 'Error');
	/* output in necessary format */
	header('Content-type: application/json');
	//echo json_encode($sql);
	echo json_encode($json);
	}

mysqli_close($conn);
?>

