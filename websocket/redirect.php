<?php
function getRootUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST']; // localhost
    $scriptName = $_SERVER['SCRIPT_NAME']; // e.g., /Okaya Merra/admin/redirect.php

    // Extract first folder after root (application folder)
    $pathParts = explode('/', trim($scriptName, '/'));
    $appFolder = $pathParts[0]; // Okaya Merra

    return $protocol . $host . '/' . $appFolder;
}

if (!isset($_REQUEST['lag']) || empty($_REQUEST['lag'])) {
    $msg="Not found";
    $rootURL=getRootUrl();
    $rootURL=$rootURL."/admin/asc_search_admin.php?pid=171&hid=Reports&msg=Loction paramter is missing";
    //var_dump($rootURL);exit();
    header("location:$rootURL");
    exit();
}


if (!isset($_REQUEST['lap']) || empty($_REQUEST['lap'])) {
    $rootURL=getRootUrl();
    $rootURL=$rootURL."/admin/asc_search_admin.php?pid=171&hid=Reports&msg=Loction paramter is missing";
    //var_dump($rootURL);exit();
    header("location:$rootURL");
    exit();
}

// Decode the Base64 values
$lag_decoded = base64_decode($_REQUEST['lag']);
$lap_decoded = base64_decode($_REQUEST['lap']);

// Print the decoded values/ Create Google Maps URL
$googleMapsUrl = "https://www.google.com/maps/dir/?api=1&origin=Current+Location&destination={$lag_decoded},{$lap_decoded}";
echo "<script>
    window.location.href = '$googleMapsUrl';
</script>";
?>
