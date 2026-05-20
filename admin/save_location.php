<?php
require_once("../includes/config.php");

if(isset($_REQUEST['lat']) && isset($_REQUEST['lon'])){
    $lat = $_REQUEST['lat'];
    $lon = $_REQUEST['lon'];
    $googleMapsUrl = "https://www.google.com/maps/dir/?api=1&origin=Current+Location&destination={$lag_decoded},{$lap_decoded}";
}
?>
