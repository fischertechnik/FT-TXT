<?php
/*+-------------------------------------------------------------------------+
  | Copyright (C) 2004 Panousis Thanos - Dimopoulos Thimios                 |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
  | WifiAdmin: The Free WiFi Web Interface				    |
  +-------------------------------------------------------------------------+
  | Send comments to  							    |
  | - panousis@ceid.upatras.gr						    |
  | - dimopule@ceid.upatras.gr						    |
  +-------------------------------------------------------------------------+*/

/**** DO NOT INCLUDE functions.php here as we don't know the router
	  You can only include funccommon.php ****************************/

if(strpos($_SERVER['PHP_SELF'],basename(__FILE__)))
	require "trap.php";
session_start();
/************** ASUMED that config.php exitst and is valid ************/
require_once("./config/config.php");
require_once("passwd_functions.php");

// Set timezone
date_default_timezone_set( (isset($C_timezone) ? $C_timezone : 'UTC') );

//LOGOUT

if(isset($_POST['logout']))	//destroy session if asked to
{
	session_destroy();
	session_start();
	//include ( "./include/auth.php");		//DONT include once : reinstanciate
}

//header("Cache-control: private"); // IE 6 Fix.

//if there is no user_id, there should be one
if (empty($_SESSION["username"])) {
	$_SESSION["username"] = "guest";

}

//AUTHENTICATION
$auth_results = 0;
if( !empty($_POST["username"]) && !empty($_POST["password"])&& $_SESSION["username"] == "guest" )
{
	// Set variable to be read from other place . Informational messages
	$auth_results = authenticate_user($_POST["username"], $_POST["password"]);
}

/* Assign privileges for the selected usernabe (be it the guest or an authenticated user */
assign_privileges();




?>
