<?php
/**
* Description:	The main class for Database.
* Author:		Joken Villanueva
* Date Created:	october 27, 2013
* Revised By:		
*/

//Database Constants
defined('DB_SERVER') ? null : define('DB_SERVER','127.0.0.1');//define our database server
defined('DB_USER') ? null : define('DB_USER','u510162695_hmsystemd');		  //define our database user	
defined('DB_PASS') ? null : define('DB_PASS','z$J0n;c&=^gR');			  //define our database Password	
defined('DB_NAME') ? null : define('DB_NAME','u510162695_hmsystemd'); //define our database Name
defined('DB_PORT') ? null : define('DB_PORT', '3306'); // define our database port

$thisFile = str_replace('\\', '/', __FILE__);
$docRoot =$_SERVER['HTTP_HOST'];

$webRoot  = str_replace(array($docRoot, 'includes/config.php'), '', $thisFile);
$srvRoot  = str_replace('config/config.php','', $thisFile);
$connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME, DB_PORT);


define('WEB_ROOT', $webRoot);
define('SRV_ROOT', $srvRoot);
?>