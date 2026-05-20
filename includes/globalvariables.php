<?php
//set_magic_quotes_runtime(0);
define("TIME_OUT_ONLINE", 1200);
define("TIME_OUT_IDLE", 120);
define("DISPLAY_ERRORS",TRUE);
// Admin
define("siteTitle", "CRM:: Support System");
define("BRANDNAME","Okaya",true);
define("COMPANYNAME","Okaya ",true);
define("SERIALNO","Serial No",true);
/////
$today=date("Y-m-d");
$todayt=date("Ymd");
$datetime=date("Y-m-d H:i:s");
$currtime=date("H:i:s");
$now=date("His");
$ip=$_SERVER['REMOTE_ADDR'];
$menutab = "V";//// (V/H) this setting will change your application navigation like if you set this V then nav will display left vertical otherwise top horizontal.
$screenwidth = "col-sm-9";//// if you set nav bar as V then it should be 9 otherwise it should 12
$btncolor = " btn-primary";/// From here you can change yorr application all button css
$tableheadcolor = " bg-primary";/// From here you can change yorr application all table header color which are showing in listing
$locationstr = "Location"; ///From here you can give name to operational users like location/ASC/Service location etc.
///// page nav variable ///////////////////
$pagenav = "&pid=".$_REQUEST['pid']."&hid=".$_REQUEST['hid'];
?>