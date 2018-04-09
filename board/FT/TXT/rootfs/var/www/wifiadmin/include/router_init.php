<?php
if(strpos($_SERVER['PHP_SELF'],basename(__FILE__)))
	require "trap.php";

/* Initializes $SSH and $SYSFLAVOR and $router_name variables acording to user preference from SQL or $router_name_new

	Important Notes :
	***** KEEP THIS FILE SILENT or you mess with session when included before session init ****
	-You have to Authenticate a user first (include include/auth.php) for correct usage,
		however if you don't you get the default router and no SESSION var is saved
	-Also it does no harm to reinclude it.
	-If you want router_name SESSION var to be saved you have to initialize the session *outside*
		this file, but you can use it without SESSION initialization - it exports default
		router and no SESSION var is saved

	** You have to initialize lang (include lang_init.php outside this file
*/

require_once "./include/funccommon.php";
require_once "./config/config.php";
require_once "./include/ini.class.php";
//@include_once "./config/routers.php";
$routerfile = "./config/routers.ini";

if (is_readable($routerfile)) {
	$C_routers = INI::read ($routerfile);
}

if (@sizeof($C_routers) == 0 ) {
	unset ($router_name);
}
else {

//initialize to the first configured router when no SESSION has been init or when
// we dont use mysql or when an error occurs whiles reading user preference
reset($C_routers);
$router_name_default = key($C_routers);


/*if (!isset($_SESSION['username'])) {
	//we have not authenticated yet or no session initialized
	$router_name = $router_name_default;
}
else {
*/
	//just for performance issue
	$isset_ses_router_name = isset($_SESSION['router_name']);
	if ($isset_ses_router_name && !array_key_exists($_SESSION['router_name'], $C_routers))
		$isset_ses_router_name = false;
	if (isset($router_name_new) && array_key_exists($router_name_new, $C_routers) &&
		(($isset_ses_router_name && $router_name_new != $_SESSION['router_name']) || !$isset_ses_router_name)) {
		//$router_name_new was changed outside this file and we are authenticated,
		//     new router selected (i.e. 2 or more includes in the same file)
		$router_name = $router_name_new;
		unset($router_name_new);
		require_once( "community_functions.php");
		if (isset($_SESSION['username']) && $C_use_mysql == true) {
			$link = connect_to_users_db();
			$username = mysqli_real_escape_string($link, $_SESSION['username']);
			$router_name = mysqli_real_escape_string($link, $router_name);
			$sql = 'SELECT `value` FROM `user_options` WHERE `username` ="'.$username.'" AND `option` = "router_name"';
			$result = mysqli_query($link, $sql)
				or die(" $result Error: $sql<p>" . mysqli_error($link));

			if (mysqli_num_rows($result) != 0)
				$sql = 'UPDATE `user_options` SET `value` = "'.$router_name.'" WHERE `option` = "router_name" AND `username` = "'.$username.'" LIMIT 1';
			else // if user option has not been set add it
				$sql = 'INSERT INTO `user_options` (`username`,`option`,`value`) VALUES ("'.$username.'","router_name","'.$router_name.'")';
			//* Free resultset
			mysqli_free_result($result);
			mysqli_query($link, $sql)
				or die(" $result Error: $sql<p>" . mysqli_error($link));
			//* Closing connection
			mysqli_close($link);
		}
		$_SESSION['router_name'] = $router_name;
	} elseif ($isset_ses_router_name) {
		// router_name_new not initialized but we parsed user router setting before. Restore
		$router_name = $_SESSION['router_name'];
	} else {
		//	router_name_new not initialized and no router has been selected yet
		// Assuming session is started elsewhere..
		//   Cache router_name to router_name Session var for performance issues
		require_once( "community_functions.php");
		if (isset($_SESSION['username']) && $C_use_mysql == true) {
			$link = connect_to_users_db();
			$username = mysqli_real_escape_string($link, $_SESSION['username']);
			$sql = 'SELECT `value` FROM `user_options` WHERE `username` ="'.$username.'" AND `option` = "router_name"';
			$result = mysqli_query($link, $sql)
				or die(" $result " .$lang['dict']['error']. ": $sql<p>" . mysqli_error($link));
			if ($row = mysqli_fetch_assoc($result)) {
				if (array_search($row['value'],$C_routers))
					$router_name = $row['value'];
				else { // self healing code e.g. when someone removes a language file
					$router_name = $router_name_default;
				}
			}
			else // if user option has not been set revert to default
				$router_name = $router_name_default;
			//* Free resultset
			mysqli_free_result($result);
			//* Closing connection
			mysqli_close($link);
		} else{
			//* no mysql - we couldnt have saved the user preference. Init with default
			$router_name = $router_name_default;
		}
		$_SESSION['router_name'] = $router_name;
	}
//}


/* Now we have router_name from somewhere - go initialize vars */
@$SYSFLAVOR = strtolower($C_routers[$router_name]['system_flavor']);
@$_access = $C_routers[$router_name]['access_mode'];
@$_user = $C_routers[$router_name]['username'];
@$_host = $C_routers[$router_name]['url'];
@$DESCRIPTION =  $C_routers[$router_name]['description'];

if (!isset($_user) || !isset($SYSFLAVOR) || !isset($_access) || !isset($_host)) {
	die ("<p class='error'> ".$lang['general']['errrouter']. " $router_name</p>");
}
if (array_search ($SYSFLAVOR, get_sysflavors()) === false )
	// MIsconfigured router
	die ("<p class='error'> ".$lang['general']['errsysfl']. " $router_name</p>");

switch ($_access) {
case 'local':
	$SSH = "";
	break;
case 'ssh':
default:
	$SSH = "ssh ".$_user ."@". $_host . " ";
}
unset ($_user);
unset ($_access);
unset ($_host);
} //end isset routers

?>
