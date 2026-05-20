<?php
require_once("../includes/config.php");

$path = "../uploads/job/images/";

// Get the directory listing
$files = scandir($path);

// Remove special entries (".", "..")
$files = array_diff($files, array('.', '..'));

// Loop through and display the files
foreach ($files as $file)
{
  echo $file . "<br>";
}
?>