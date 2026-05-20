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
	$warr_month_used = $_GET['warr_month_used'];
	$Time = $_GET['Time'];
	$phy_condition = $_GET['phy_condition'];
    $jobno = $_GET['job_no'];

	if ($conn)
		{
		
		if($phy_condition != '1') {
		
				if( $phy_condition != '17' && $phy_condition != '18' && $phy_condition != '19')
					{
					  $json = array('result' => 'Refer TO HO','final_decision' => 'Refer TO HO');
					   header('Content-type: application/json');
					   echo json_encode($json);

					}
					else if($phy_condition == '17' || $phy_condition == '18' || $phy_condition == '19'){
					 $json = array('result' => 'OK','final_decision' => 'Test Pass');
						header('Content-type: application/json');
					   echo json_encode($json);

					}	
	       }		
		else {
			
			if($_GET['csat'] != "" && $_GET['test_interrupt']=="Y")
			{
			   $json = array('result' => 'Test interrupted','final_decision' => 'Retest Required');
				/* output in necessary format */
				header('Content-type: application/json');
				//echo json_encode($sql);
				echo json_encode($json);
			}
						
		   else if($_GET['csat'] != "" ){
		
			$json = array('result' => 'Customer Satisfied battery test PASS','final_decision' => 'TEST PASS');
			/* output in necessary format */
			header('Content-type: application/json');
			//echo json_encode($sql);
			echo json_encode($json);	
			
		}
		else if($_GET['test_interrupt']=="Y"){
		
			$json = array('result' => 'Test interrupted','final_decision' => 'RETEST REQUIRED');
			/* output in necessary format */
			header('Content-type: application/json');
			//echo json_encode($sql);
			echo json_encode($json);	
			
		}
		else{
		//$sql = "SELECT concat(result,'-',srno) as result from report2 where model_name = '$ModelName' and status = '$status' and warr_month_used = '$warr_month_used' and time <='$Time'";
		
		
		#### Fetch Decision as per Physical Condition
		$sql_phy= mysqli_query($conn,"select final_decision from discharger_physical_condition where id='".$phy_condition."' ");
		$row_phy=mysqli_fetch_array($sql_phy);
		
		if($status=='OUT')
		{
		    $sql = "SELECT concat(result) as result from report2 where model_code = '$ModelName' and status in ('OUT','OW')   and time <='$Time' group by result";
			
		}else {
		   $sql = "SELECT concat(result) as result from report2 where model_code = '$ModelName' and status = '$status' and warr_month_used = '$warr_month_used' and time <='$Time' group by result";
		}
	//	echo ($sql);
		$result = mysqli_query($conn, $sql);
		
		if (mysqli_num_rows($result) > 0)

		{

		while (($row = mysqli_fetch_assoc($result)) != false)
			{

			//$json = $row;
			 if($status=='OUT' && $Time < '010000'){
				$json = array('result' => 'FAIL','final_decision' => 'O/W Refer to HO');
			}
			else if($status=='OUT' && $Time > '010000'){
				$json = array('result' => 'PASS','final_decision' => 'O/W PASS');
			}			
			else{
				$json = array('result' => $row['result'],'final_decision' => $row_phy['final_decision']);
			}
		    header('Content-type: application/json');
		    echo json_encode($json);
				
			}   #### END While

		}   #### END IF for Numrows
		else{
	
			 if($status=='OUT' || $status=='IN GP') {
					//$json = array('result' => 'FAIL','final_decision' => 'Refer to HO');
				     $json = array('result' => 'Refer to HO','final_decision' => 'Refer to HO');

					/* output in necessary format */
					header('Content-type: application/json');
					//echo json_encode($sql);
					echo json_encode($json);
			 }else {
			         $json = array('result' => 'FAIL','final_decision' => 'FAIL');
				 
				 //  $json = array('result' => 'Refer to HO','final_decision' => 'Refer to HO');

					/* output in necessary format */
					header('Content-type: application/json');
					//echo json_encode($sql);
					echo json_encode($json);
			 
			    }
			
		}  #### END IF
		}  #### END IF CSAT Condition
	
	}	
		
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

