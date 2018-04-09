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

include_once("./include/header.php");	//echo the default Header entries
include_once("./include/community_functions.php");


/*
korki: intl version minor bug fixes (1)
*/
if (( $C_users_register_themselves ) && ($C_use_mysql))
	{	
	if(empty($_POST))
		echo_user_add_form();
	
	else{
		add_user( $_POST, $C_confirm_new_account && $C_send_emails);
	}
}
else
	echo "<h3>".$lang['general']['enoperm']."</h3>";
include("./include/footer.php");
?>
