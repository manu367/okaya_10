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
   	$db_user = 'rvsolution_user';
	$db_pass = 'zkL4d1!4';
	$db_host = 'localhost';
	$db = "rvsolution_crm";
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