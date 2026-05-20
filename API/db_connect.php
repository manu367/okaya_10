<?php
   class DB_Connect {
   // constructor
   function __construct() {
       }
   // destructor
   function __destruct() {
   // $this->close();
       }
   // Connecting to database
   public function connect() {
   //require_once 'config.php';
   // connecting to mysql
   //$con = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
  // selecting database
   //mysql_select_db(DB_DATABASE);

	  #### add by priya on 2025-04-21 ################# 
/*$db_user = 'ev_btr_usr';
$db_pass = '55X@zsh95';
$db_host = '10.1.20.242';
$db = "ev_batterydb";*/
	   
$db_user = 'csdb_betacrm';
$db_pass = 'W4tTujXs$nze~2u5';
$db_host = 'localhost';
$db = "okaya_betadb";	   
	   
	   
	  /* $db_user = 'csusr_v3e';
$db_pass = 'CS@#$123';
$db_host = 'localhost';
$db = "crmdemo_v3_e";*/
	$con = mysqli_connect($db_host, $db_user, $db_pass,$db);
   // return database handler
    return $con;
        }
  // Closing database connection
  public function close() {
    mysqli_close();
	     }
    }
	  ?>