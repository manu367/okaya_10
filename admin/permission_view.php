<?php
require_once("../includes/config.php");


if(isset($_GET['i']) && $_GET['i'] != '')
{
	$i = base64_decode($_GET['i']);
	$payload = [];
	$sql = "SELECT * FROM permission_log WHERE id='".$i."'";
	$res = mysqli_query($link1, $sql);
	if($res)
	{
		if(mysqli_num_rows($res) > 0)
		{
			$row = mysqli_fetch_assoc($res);
			$type = $row['type'];
			$permissions = json_decode($row['permissions']);

			if($type=="Masters/Reports")
			{
				foreach($permissions as $item)
				{
					$sql_a = "SELECT subtabname, subtabicon, maintabname FROM tab_master WHERE tabid='".$item."'";
					$res_a = mysqli_query($link1, $sql_a);
					if($res_a)
					{
						if(mysqli_num_rows($res_a) > 0)
						{
							$row_a = mysqli_fetch_assoc($res_a);
							$payload[] = [ "icon" => $row_a['subtabicon'], "name" => $row_a['subtabname'] ];
						}
					}
				}
			}
			elseif($type=="Region")
			{
				foreach($permissions as $item)
				{
					
					$sql_a = "SELECT state FROM state_master WHERE stateid='".$item."'";
					$res_a = mysqli_query($link1, $sql_a);
					if($res_a)
					{
						if(mysqli_num_rows($res_a) > 0)
						{
							$row_a = mysqli_fetch_assoc($res_a);
							$payload[] = [ "icon" => "fa-map-marker", "name" => $row_a['state'] ];
						}
					}
				}
			}
			elseif($type=="Brand")
			{
				foreach($permissions as $item)
				{
					$sql_a = "SELECT brand FROM brand_master WHERE brand_id='".$item."'";
					$res_a = mysqli_query($link1, $sql_a);
					if($res_a)
					{
						if(mysqli_num_rows($res_a) > 0)
						{
							$row_a = mysqli_fetch_assoc($res_a);
							$payload[] = [ "icon" => "fa-superpowers", "name" => $row_a['brand'] ];
						}
					}
				}
			}
			elseif($type=="Product")
			{
				foreach($permissions as $item)
				{
					$sql_a = "SELECT product_name FROM product_master WHERE product_id='".$item."'";
					$res_a = mysqli_query($link1, $sql_a);
					if($res_a)
					{
						if(mysqli_num_rows($res_a) > 0)
						{
							$row_a = mysqli_fetch_assoc($res_a);
							$payload[] = [ "icon" => "fa-cube", "name" => $row_a['product_name'] ];
						}
					}
				}
			}
			else
			{
				// do nothing
			}
		}
	}
?>
<div style="padding:15px 0px;background:#f5faff;">
	<ul style='list-style:none;font-size:14px;line-height:34px;'>
	<?php
	foreach($payload as $key => $load)
	{
		echo '<li><span style="width:30px;display:inline-block;text-align:center;"><i class="fa '.$load['icon'].'" aria-hidden="true"></i></span>'.$load['name'].'</li>';
	}
	?>
	</ul>
</div>
<?php
}
?>