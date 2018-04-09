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

include_once("./include/header.php");
include_once("./include/community_functions.php");


if (!$C_use_mysql){
	echo error_echo(_('This feature is only available when using mysql.<br \>Ask your sysadmin to change your password.'));
	include("./include/footer.php");
	exit();
}


if (!$C_send_emails){
	echo error_echo(_('This system is not configured to send emails. Check config.php'));
	include("./include/footer.php");
	exit();
}

//set default action
if (!isset($_GET["action"]))
	$_GET["action"] = "get_email_address";

switch ($_GET["action"]){
	case "get_email_address":
	echo ('<p>'._('If you\'ve forgotten your username or password, just enter the email address that you registered with, and your password will be emailed to you immediately.').'</p>
		<form method="get" action='. $_SERVER['PHP_SELF'].'>
			<b>'._('Your Email Address:').'</b><br />
			<input type="text" name="email" />&nbsp;<input type=submit value="Submit" />
			<input type=hidden name="action" value="send_email">
		</form>');
		break;
	case "send_email":
		$link = connect_to_users_db();
		$sql = "SELECT username,email, password_string FROM `user` WHERE email='".mysqli_real_escape_string($link, $_GET['email'])."'";
		$query = mysqli_query($link, $sql);
		$row = mysqli_fetch_assoc($query);
		if ($query && (mysqli_num_rows($query) > 0)) {
			$res = mail($_GET['email'],
				_('Your WiFiadmin Password'),
				_('Below is the username and password information you requested.\n\n').
				_('Username')." : ".$row["username"]."\n"._('Password')." : ".$row["password_string"]."\n\n",
				"From: $C_web_master <$C_web_master_EMAIL>\n");
			if($res)
				echo "<p>"._('Your username and password have been emailed to you')."</p>";
			else{
				echo error_echo(_('An error occured during email sending. Please notify an admin to address the issue.'));
				break;
			}
		}
		else{
			echo "<p>"._('We could not find the email address you entered. Perhaps you registered with a different email address.')."</p>
			<a href=".$_SERVER['PHP_SELF']."?action=get_email_address>"._('Try again')."</a>";
			break;
		}
}
include("./include/footer.php");
?>
