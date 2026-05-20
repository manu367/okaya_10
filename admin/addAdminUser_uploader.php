<?php
require_once("../includes/config.php");

if(isset($_POST['Submit']) && $_POST['Submit'] == "Upload")
{
	//echo "<pre>";var_dump($_FILES['file']);exit;
	mysqli_autocommit($link1, false);
	$flag = true;
	
	if(isset($_FILES['file']) && $_FILES['file']['size'] != 0)
	{
		#### Excel Processor by Hemant ####
		require_once "../support/simplexlsx.class.php";
		$xlsx = new SimpleXLSX( $_FILES['file']['tmp_name'] );
		list($cols,) = $xlsx->dimension();
		$payload = [];
		foreach( $xlsx->rows() as $k => $r)
		{
			if($k == 0)
			{
				continue; // skip first row
			}
			else
			{
				$rx = [];
				for($i = 0; $i < $xlsx; $i++)
				{
					$rx['row_no'] = strFilter(trim($r[0]));
					$rx['name'] = strFilter(trim($r[1]));
					if(strlen($rx['name']) < 3 || strlen($rx['name']) > 32)
					{
						$flag = false;
						$msg = "Invalid Name! (row #".$rx['row_no']." | name length should be 3 to 32 character long)";
						break;
					}
					$rx['password'] = strFilter(trim($r[2]));
					if(strlen($rx['password']) < 5 || strlen($rx['password']) > 32)
					{
						$flag = false;
						$msg = "Invalid Password! (row #".$rx['row_no']." | password length should be 5 to 32 character long)";
						break;
					}
					$rx['user_type'] = strFilter(trim($r[3]));
					if($rx['user_type'] != "admin")
					{
						$flag = false;
						$msg = "Invalid Usertype! (row #".$rx['row_no']." | type should be 'admin')";
						break;
					}
					$mob = strFilter(trim($r[4]));
					$rx['mobile'] = $mob;
					$mob_len = strlen($mob);
					if($mob_len != '10')
					{
						$flag = false;
						$msg = "Invalid Mobile Number! (row #".$rx['row_no']." | only 10 digit mobile number allowed)";
						break;
					}
					$rx['email'] = strFilter(trim($r[5]));
					if(strlen($rx['email']) < 6 || strlen($rx['email']) > 64)
					{
						$flag = false;
						$msg = "Invalid Email! (row #".$rx['row_no']." | email length should be 6 to 64 character long)";
						break;
					}
					$rx['status'] = strFilter(trim($r[6]));
					if($rx['status'] != '1' || $rx['status'] != '0')
					{
						$flag = false;
						$msg = "Invalid Status! (row #".$rx['row_no']." | status should be '0' or '1')";
						break;
					}
					$payload[] = $rx;
				}
				if(!$flag)
				{
					break;
				}
			}
		}
		//echo "<pre>";var_dump($payload);exit;
		###################################

		if($flag)
		{
			$query_code = "select MAX(uid) as qc from admin_users";
			$result_code = mysqli_query($link1,$query_code)or die("error2".mysqli_error($link1));
			$arr_result2 = mysqli_fetch_array($result_code);
			$current_count = $arr_result2[0];
		}
		
		if($flag)
		{
			if(!empty($payload))
			{
				$count = 0;
				foreach($payload as $key => $datarow)
				{
					/// id generation
					$current_count = $current_count+1;
					$pad=str_pad($current_count,3,"0",STR_PAD_LEFT);
					$admiCode = strtoupper(BRANDNAME)."USR".$pad;
					//echo $admiCode;

					$sql = "INSERT INTO admin_users
					SET
					username = '".$admiCode."',
					password = '".$datarow['name']."',
					name = '".$datarow['password']."',
					utype = '".$datarow['user_type']."',
					phone = '".$datarow['mobile']."',
					emailid = '".$datarow['email']."',
					status = '".$datarow['status']."'";
					$res = mysqli_query($link1, $sql);
					if($res && mysqli_affected_rows($link1) > 0)
					{
						$count += 1;
					}
				}
			}
			else
			{
				$flag = false;
				$msg = "Nothing to upload!";
			}
		}
	}
	else
	{
		$flag = false;
		$msg = "File required!";
	}
	
	if($flag)
	{
        mysqli_commit($link1);
		$c = 'g';
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Uploaded sheet successfully processed! (".$count." records added)";
    }
	else
	{
		mysqli_rollback($link1);
		$c = 'r';
		$cflag = "danger";
		$cmsg = "Failed";
	} 
    mysqli_close($link1);
	
	$_SESSION['resp'] = [ "c" => $c, "msg"=> $msg ];
	exit(header("location:addAdminUser_uploader.php"));	
}


if(isset($_POST['Submit']) && $_POST['Submit'] == "Upload_D")
{
	//ini_set('max_execution_time', 500);
	mysqli_autocommit($link1, false);
	$flag = true;

	if(isset($_FILES['file']))
	{
		// save uploaded file
		/* $should_save = false;
		if($should_save)
		{
			$save_loc = "billing_uploads";
			if(!is_exist($save_loc))
			{
				chmod($save_loc, 0755);
			}
			move_uploaded_file($_FILES["file"]["tmp_name"], $save_loc.$_FILES["file"]["name"]);
		} */
		
		require_once "../support/simplexlsx.class.php";
		$xlsx = new SimpleXLSX( $_FILES['file']['tmp_name'] );
		//var_dump($xlsx);
		
		list($cols,) = $xlsx->dimension();
		
		$payload = [];
		$payload_hi = [];

		foreach( $xlsx->rows() as $k => $r)
		{
			
			if($k == 0)
			{ 
				continue; // skip first row
			}
			else
			{
				$rx = [];
				for($i = 0; $i < $xlsx; $i++)
				{
					$rx['row_no'] = trim($r[0]);
					$rx['shipto_code'] = trim($r[1]);
					$rx['shipto_name'] = trim($r[2]);
					
					$rx['shipto_crmcode'] = trim($r[3]); //*
					
					$rx['date_billing'] = $r[4];			
					
					$rx['gst_invoice'] = trim($r[5]);
					$rx['mrn'] = trim($r[6]);
					$rx['stock_type'] = trim($r[7]);
					$rx['bill_doc_ref'] = trim($r[8]);
					
					$rx['m_price'] = trim($r[9]);
					$rx['m_code'] = trim($r[10]); //*
					$rx['serial_no'] = trim($r[11]);

					$payload_hi[] = $rx;
					
					$iteminfo = [
							"m_code" => $rx['m_code'],
							"m_price" => $rx['m_price'],
							"serial_no" => $rx['serial_no']
								];
					
					if(array_key_exists($rx['gst_invoice'], $payload))
					{
						$payload[$rx['gst_invoice']]['items'][] = $iteminfo;
					}
					else
					{			
						$payload[$rx['gst_invoice']]['shipto_crmcode'] = $rx['shipto_crmcode'];
						$payload[$rx['gst_invoice']]['gst_invoice'] = $rx['gst_invoice'];
						$payload[$rx['gst_invoice']]['bill_date'] = $rx['date_billing'];
						$payload[$rx['gst_invoice']]['mrn'] = $rx['mrn'];
						$payload[$rx['gst_invoice']]['stock_type'] = $rx['stock_type'];
						$payload[$rx['gst_invoice']]['items'][] = $iteminfo;
					}
				}
			}
		}
		echo "<pre>";var_dump($payload);exit;
		//echo "<pre>";var_dump($payload_hi);exit;
			
		/// data history entry
		foreach($payload_hi as $entry)
		{
			//bill_date = '".$entry['date_billing']."',
			
			$sql_ihbd = "INSERT INTO billing_data_history
			SET
			shipto_code = '".$entry['shipto_crmcode']."',
			gst_invoice = '".$entry['gst_invoice']."',
			mrn = '".$entry['mrn']."',
			stock_type = '".$entry['stock_type']."',
			billing_ref = '".$entry['bill_doc_ref']."',
			material_code = '".$entry['m_code']."',
			price = '".$entry['m_price']."',
			serial = '".$entry['serial_no']."',
			create_dt = '".$c_dt."',
			create_by = '".$_SESSION['userid']."',
			create_ip = '".$c_ip."'";
			$res_ihbd = mysqli_query($link1, $sql_ihbd);
			if($res_ihbd)
			{
				if(mysqli_affected_rows($link1) == 0)
				{
					$flag = false;
					$msg = "Error IHBD : Unable to insert!";
					break;
				}
			}
			else
			{
				$flag = false;
				$msg = "Error IHBD : Try again!";
				break;
			}
		}
		
		foreach($payload as $key => $data)
		{
			/// generate bill number
			if($flag)
			{
				$sql_sic = "SELECT fy,inv_series,inv_counter FROM invoice_counter WHERE location_code = '".$_SESSION['userid']."' LIMIT 1";
				$res_sic = mysqli_query($link1, $sql_sic);
				if($res_sic)
				{
					if(mysqli_num_rows($res_sic) > 0)
					{
						$row_sic = mysqli_fetch_assoc($res_sic);
						$next_inv = $row_sic['inv_counter'] + 1;
						$invno = $row_sic['inv_series']."".$row_sic['fy']."".str_pad($next_inv,5,0, STR_PAD_LEFT);
					}
					else
					{
						$flag = false;
						$error_msg = "Error : Unable to generate Invoice Number!";
					}
				}
				else{
					$flag = false;
					$error_msg = "Error SIC : Error occurred!";
				}
			}
			//exit($invno);
			
			/// billling master entry
			if($flag)
			{
				########
				# TO INFO

				########
				# FROM INFO
				$from_loc = $_SESSION['userid'];

				$sql_ibm = "INSERT INTO billing_master
				SET
				from_location = '".$from_loc."',
				to_location = '".$data['shipto_crmcode']."',
				challan_no = '".$invno."',
				sale_date = '',
				entry_date = '".$c_d."',
				entry_time = '".$c_t."',
				dc_date = '".$c_d."',
				dc_time = '".$c_t."',
				logged_by = '".$_SESSION['userid']."',
				status = '3',
				document_type = 'DC',
				stock_type = '".$data['stock_type']."'";
				$res_ibm = mysqli_query($link1, $sql_ibm);
				if($res_ibm)
				{
					if(mysqli_affected_rows($link1) == 0)
					{
						$flag = false;
						$msg = "Error IBM : Unable to insert!";
						break;
					}
				}
				else
				{
					$flag = false;
					$msg = "Error IBM : Try again!"; //.mysqli_error($link1);
					break;
				}
			}
			
			/// invoice count++
			if($flag)
			{
				$res_uic = mysqli_query($link1, "UPDATE invoice_counter SET inv_counter = (inv_counter + 1) WHERE location_code = '".$_SESSION['userid']."' LIMIT 1");
			}
			
			/// billing data entry
			if($flag)
			{
				foreach($data['items'] as $item)
				{
					$sql_ibpi = "INSERT INTO billing_product_items
					SET
					from_location = '".$from_loc."',
					to_location = '".$data['shipto_crmcode']."',
					challan_no = '".$invno."',
					type = 'DC',
					partcode = '".$item['m_code']."',
					qty = '1',
					mrp = '".$item['price']."',
					price = '".$item['price']."',
					entry_datetime = '".$c_dt."'";
					$res_ibpi = mysqli_query($link1, $sql_ibpi);
					if($res_ibpi)
					{
						if(mysqli_affected_rows($link1) == 0)
						{
							$flag = false;
							$msg = "Error IBPI : Unable to insert!";
							break;
						}
					}
					else
					{
						$flag = false;
						$msg = "Error IBPI : Try again!"; //.mysqli_error($link1);
						break;
					}

					/// billing imei entry
					if($flag)
					{
						$sql_iida = "INSERT INTO imei_details_asp
						SET
						imei1 = '".$item['serial_no']."',
						challan_no = '".$invno."',
						partcode = '".$item['m_code']."',
						location_code = '".$data['shipto_crmcode']."',
						entry_datetime = '".$c_dt."'";
						$res_iida = mysqli_query($link1, $sql_iida);
						if($res_iida)
						{
							if(mysqli_affected_rows($link1) == 0)
							{
								$flag = false;
								$msg = "Error IIDA : Unable to insert!";
								break;
							}
						}
						else
						{
							$flag = false;
							$msg = "Error IIDA : Try again!"; //.mysqli_error($link1);
							break;
						}
					}
				}
			}
			/// loop breaker
			if(!$flag)
			{
				break;
			}
		}
	}
	else
	{
		$flag = false;
		$msg = "File required!";
	}
	
	if($flag)
	{
        mysqli_commit($link1);
		$c = 'g';
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Uploaded sheet successfully processed!";
    }
	else
	{
		mysqli_rollback($link1);
		$c = 'r';
		$cflag = "danger";
		$cmsg = "Failed";
	} 
    mysqli_close($link1);
	
	$_SESSION['resp'] = [ "c" => $c, "msg"=> $msg ];
	exit(header("location:billing_data_uploader.php"));	
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?=siteTitle?></title>
		<script src="../js/jquery.js"></script>
		<link href="../css/font-awesome.min.css" rel="stylesheet">
		<link href="../css/abc.css" rel="stylesheet">
		<script src="../js/bootstrap.min.js"></script>
		<link href="../css/abc2.css" rel="stylesheet">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<script src="../js/frmvalidate.js"></script>
		<script type="text/javascript" src="../js/jquery.validate.js"></script>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row content">
				<?php 
				include("../includes/leftnav2.php");
				?>
				<div class="<?=$screenwidth?>">
					<h2 align="center" style="border-bottom:1px solid #aaa8a8;padding:25px 0px;margin:0px;"><i class="fa fa-upload"></i> Admin User Upload
					</h2>
					<?php
					if(isset($_SESSION["resp"]))
					{
						$_tc = ($_SESSION["resp"]["c"]=='g')?'#1ea43f':'#fd4f4f';
						$_bc = ($_SESSION["resp"]["c"]=='g')?'#edfff1':'#fff3f3';
						echo '<div class="py-2 overflow-hidden" style="background:'.$_bc.';padding:15px;line-height:22px;color:'.$_tc.';margin:15px 0px;font-size:14px;border:1px dashed '.$_tc.';"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> '.$_SESSION["resp"]["msg"].'</div>';
						unset($_SESSION["resp"]);
					}
					?>
					<div style="padding:35px 0px;">
						<form name="frm1" id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
						<div class="row" id="page-wrap" style="padding:5px 0px;">
							<div class="col-md-2">
							</div>
							<div class="col-md-8">
								<a href="../templates/template_admin_user_add.xlsx" title="Download Excel Template"><img src="../images/template.png" title="Download Excel Template"></a>
							</div>
							<div class="col-md-2">
							</div>
						</div>
						<div class="row" id="page-wrap" style="padding:5px 0px;">
							<div class="col-md-2">
							</div>
							<div class="col-md-8">
								<input type="file" name="file" required="" class="form-control" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" style="cursor:pointer;" required />
							</div>
							<div class="col-md-2">
							</div>
						</div>
						<div class="row" id="page-wrap" style="padding:5px 0px;">
							<div class="col-md-2">
							</div>
							<div class="col-md-8">
								<input type="submit" class="btn btn-primary" name="Submit" id="save" value="Upload" title="">

								<input title="Back" type="button" class="btn btn-primary" value="Back" onclick="window.location.href='model_master.php?status=&amp;pid=8&amp;hid=Masters'">
							</div>
							<div class="col-md-2">
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
	include("../includes/footer.php");
										   include("../includes/connection_close.php");
		?>
	</body>
</html>