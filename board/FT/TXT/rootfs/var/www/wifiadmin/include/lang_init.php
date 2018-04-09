<?php
if(strpos($_SERVER['PHP_SELF'],basename(__FILE__)))
	require "trap.php";
/* Iniatizes correct $lang, $lang_id, $lang_avail variables according to user preference from SQL or $lang_id_new
	Important Notes :
	***** KEEP THIS FILE SILENT or you mess with session when included before session init ****
	-You have to Authenticate a user first (include include/auth.php) for correct usage,
		however if you don't you get the default language and no SESSION var is saved
	-Also it does no harm to reinclude it.
	-If you want lang_id SESSION var to be saved you have to initialize the session *outside*
		this file, but you can use it without SESSION initialization - it exports default
		language and no SESSION var is saved
*/

/* when we dont have mysql support and some other special cases
	** IT SHOULD exist as a lang file or errors will be the default behaivour **
*/
$lang_default = 'en_GB';

/* Language File Extention - why mess with it ?*/
$lang_ext = ".php";

/* Define a fallback gettext function */
if (!function_exists("_")) {
	function _($msg)
	{
		return $msg;
	}
}

/* List of available languages . Cache list to lang_avail Session var for performance issues*/

if (isset($_SESSION['lang_avail']))
	$lang_avail = $_SESSION['lang_avail'];
else {
	$lang_avail = scandir ( "./lang/");
	foreach ($lang_avail as $key => $fname) {
		// clean default directory links
		if ($fname[0] == "." )
			unset ($lang_avail[$key]);
		else if (preg_match("/^.*".$lang_ext."$/",$fname))
			//strip extentions
			$lang_avail[$key] = substr($fname,0,-strlen($lang_ext));
		else
			unset ($lang_avail[$key]);
	}
	$_SESSION['lang_avail'] = $lang_avail;
}


//Think before setting Session lang_id var or a deadlock might occur
/*if (!isset($_SESSION['username'])) {
	//we have not authenticated yet or no session initialized
	if (isset($lang_id_new) && array_search($lang_id_new,$lang_avail)) {
		$lang_id = $lang_id_new;
		unset($lang_id_new);

	}
	else {
		$lang_id = $lang_default;
	};
}
else {*/
	$isset_ses_lang_id = isset($_SESSION['lang_id']); 	//just for performance issue
	if (isset($lang_id_new) && array_search($lang_id_new,$lang_avail) && (($isset_ses_lang_id && $lang_id_new != $_SESSION['lang_id']) || !$isset_ses_lang_id)) {
		//$lang_id_new was changed outside this file and we are authenticated,
		//     new language selected (i.e. 2 or more includes in the same file)
		$lang_id = $lang_id_new;
		unset($lang_id_new);
		@include_once "./config/config.php"; //no require
		if (isset($_SESSION['username']) && @$C_use_mysql === true) {
			require_once( "community_functions.php");
			$link = connect_to_users_db();
			$username = mysqli_real_escape_string($link, $_SESSION['username']);
			$sql = 'SELECT `value` FROM `user_options` WHERE `username` ="'.$username.'" AND `option` = "lang_id"';
			$result = mysqli_query($link, $sql)
				or die(" $result Error: $sql<p>" . mysqli_error($link));
			if (mysqli_num_rows($result) != 0)
				$sql = 'UPDATE `user_options` SET `value` = "'.$lang_id.'" WHERE `option` = "lang_id" AND `username` = "'.$username.'" LIMIT 1';
			else // if user option has not been set add it
				$sql = 'INSERT INTO `user_options` (`username`,`option`,`value`) VALUES ("'.$username.'","lang_id","'.$lang_id.'")';
			//* Free resultset
			mysqli_free_result($result);
			mysqli_query($link, $sql)
				or die(" $result Error: $sql<p>" . mysqli_error($link));
			//* Closing connection
			mysqli_close($link);
		}
		$_SESSION['lang_id'] = $lang_id;
	} elseif ($isset_ses_lang_id) {
		// Lang_id not initialized but we parsed user lang setting before. Restore
		$lang_id = $_SESSION['lang_id'];
	} else {
		//	Lang_id not initialized and no language has been selected yet
		// Assuming session is started elsewhere..
		//   Cache lang_id to land_id Session var for performance issues
		@include_once "./config/config.php"; //no require
		if (isset($_SESSION['username']) && @$C_use_mysql === true) {
			require_once( "community_functions.php");
			$link = connect_to_users_db();
			$username = mysqli_real_escape_string($link, $_SESSION['username']);
			$sql = 'SELECT `value` FROM `user_options` WHERE `username` ="'.$username.'" AND `option` = "lang_id"';
			$result = mysqli_query($link, $sql)
				or die(" $result Error: $sql<p>" . mysqli_error($link));
			if ($row = mysqli_fetch_assoc($result)) {
				if (array_search($row['value'],$lang_avail))
					$lang_id = $row['value'];
				else { // self healing code e.g. when someone removes a language file
					$lang_id = $lang_default;
				}
			}
			else // if user option has not been set revert to default
				$lang_id = $lang_default;
			//* Free resultset
			mysqli_free_result($result);
			//* Closing connection
			mysqli_close($link);
		} else {
			//* no mysql or not auth - we couldnt have saved the user preference. Init with default
			$lang_id = $lang_default;
		}
		$_SESSION['lang_id'] = $lang_id;
	}
//}

/* Include lang file */

$lang_file = "./lang/".$lang_id.$lang_ext;

if (!is_readable($lang_file)) {
	unset($_SESSION['lang_id']);
	unset($_SESSION['lang_avail']);
	die ("<p class='error'> I18n Error: Cannot find language file '".$lang_file."' for language '$lang_id'");
}
include $lang_file;

/* Check if Initalized $lang reference table */
if (!isset($lang)) {
	unset($_SESSION['lang_id']);
	unset($_SESSION['lang_avail']);
	die ("<p class='error'> I18n Error: Cannot find language array \$lang into file '".$lang_file."'");
}

?>
