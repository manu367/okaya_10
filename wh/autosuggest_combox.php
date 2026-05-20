<?php
$db_user = 'root';
$db_pass = '';
$db_host = 'localhost';
$db = "okaya_beta";
$link1=mysqli_connect($db_host, $db_user, $db_pass,$db) or die("Unable to connect to MySQL");
//$selected = mysqli_select_db("hitech",$link1) or die("Could not select DB");
if(isset($_POST['queryString'])) {
	$flag=1;
			$queryString = $_POST['queryString'];
			if(strlen($queryString) >0) {
				
			$query2 = mysqli_query($link1,"select * from partcode_master where (name LIKE '$queryString%' or  partcode LIKE '$queryString%' or model LIKE '$queryString%' ) and status='Active' order by partcode LIMIT 10")or die (mysqli_error($link1));
					if(mysqli_num_rows($query2)>0){
						$flag*=0;		
					while ($rows2=mysqli_fetch_array($query2)) {
			$query3=mysqli_query($link1,"select okqty from warehouse_inventory where w_code='$_SESSION[asc_code]' and partcode='$rows2[partcode]'");
			$row1=mysqli_fetch_array($query3);
			if(mysqli_num_rows($query3)>0){			
	         	$okqty=$row1[0];
			}
			else{
				$okqty=0;
			}
						echo '<li  onClick="fill(\''.addslashes($rows2[partcode].':'.$rows2[name].'~'.$_POST[indx1].'~'.$rows2[purchase_price].'~'.$okqty).'\');">'.$rows2[name].' | '.$rows2[partcode].' | '.$rows2[model].'</li>';
	         		}
					
				echo '</ul>';
					}
					
					if($flag==1){ ?>
						<script language="javascript" type="text/javascript">
						 alert("No Record Found ..!");
						  document.getElementById("item_id[<?=$_POST[indx1]?>]").value='';
			             //document.getElementById("party_name").value='';
						 //document.getElementById("city_state").value='';
                         </script>
						<?php /*?>echo '<ul><li onClick="fill(\''.addslashes(' '.'-'.' ').'\');">No Record Found</li></ul>';<?php */?>
					<?php }
			} else {
				///do nothing
			}
		} else {
			echo 'There should be no direct access to this script!';
		}
?>