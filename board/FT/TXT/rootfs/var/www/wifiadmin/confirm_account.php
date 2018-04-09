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

include_once("./include/community_functions.php");
include("./include/header.php");

$link = connect_to_users_db();

/* basos mod: If username is set it means a request for confirmation email resend */
if (isset($_GET['username'])) {
	send_confirmation($link, $_GET['username']);
}

if (isset($_GET['token'])) {
	$sql = sprintf("
		SELECT *
		FROM user_tokens
		WHERE token='%s'",
		mysqli_real_escape_string($link, base64_decode($_GET['token']) ));
	$query = mysqli_query($link, $sql)
		or die("error in query: $sql - ".mysqli_error($link));
	if (!$query || (mysqli_num_rows($query) == 0)) {
		echo "Incorrect! This token is incorrect";
		exit;
	}
	else {
		$sql = "UPDATE user_tokens SET status='enabled' WHERE token='".mysqli_real_escape_string($link, base64_decode($_GET['token']))."'";
		mysqli_query($link, $sql)
			or die("error in query: $sql - ".mysqli_error($link));
		echo "<p>"._('You have successfuly confirmed your email address.Your account is now activated.You may login ').
			"<a href=\"./index.php\">"._('here')."</a></p>";
		exit;
	}
}

else
	mydie(_('There is no token to confirm, you probably got here by mistake'));
include("./include/footer.php");
?>
