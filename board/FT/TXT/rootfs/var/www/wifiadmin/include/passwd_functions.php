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

require_once( "./config/config.php");
require_once( "community_functions.php");
require_once( "lang_init.php"); //if we initialized ok not to include

/*passwd file format:

username:md5(password):boolean edit_settings*/

	function create_default_passwd_file(){
		global $C_passwd_filename;
		$filename = $C_passwd_filename;
		if ( ($fp = fopen($filename,"w")) == false){
			echo "<h3> passwd file could not be created, check filesystem permissions, httpd user, should have permission to write in include directory </h3>";
			return false;
		}
		fwrite($fp,"username:password:view_status:view_status_ext:view_macs:ban_users:access_ifs:edit_users:manage_routers\n
				admin:c178d9bb85c3e412dbc478d76320c2be:true:true:true:true:true:true:true\n
				guest:d41d8cd98f00b204e9800998ecf8427e:true:false:false:false:false:false:false");
		fclose($fp);
	}

	//returns an array of passwd file lines
	function read_passwd_file(){
		global $C_passwd_filename;
		$filename = $C_passwd_filename;
		//if passwd file does not exist or if is just touched, create the default one
		if (!file_exists($filename) || filesize($filename) == 0 ) {
			create_default_passwd_file();
		}
		$fp = fopen($filename,"r");
		$file_contents=fread($fp,filesize($filename));
		fclose($fp);
		$whole_lines=explode("\n",$file_contents);

		$lines_count = 0;
		foreach ($whole_lines as $whole_line){
			$whole_line = trim($whole_line);
			//ignore empty lines
			if ($whole_line == "")
				continue;
			$lines[$lines_count] = explode(":", $whole_line);
			$lines_count++;
		}
		//first line of file is the attribute names
		$attribute_names = $lines[0];

		//for each of the lines
		for ($line_index = 1; $line_index < count($lines); $line_index++){
			//for each attribute
			$attribute_index = 0;
			foreach ($attribute_names as $attribute_name){
				$user_attributes[$attribute_name] = $lines[$line_index][$attribute_index];
				$attribute_index++;
			}
			$return_array[$line_index - 1] = $user_attributes;
		}
		return $return_array;
	}

	function write_passwd_file($users_attributes){
		global $C_passwd_filename;
		$filename = $C_passwd_filename;
		if ( ($fp = fopen($filename,"r+")) == false){
			echo "<h3> could not open passwd file, check permissions </h3>";
			return false;
		}
		$fp = fopen($filename,"r+");
		$attribute_index = 0;
		foreach($users_attributes[0] as $attribute_name => $attribute_value){
			if ($attribute_index == 0)
				$passwd_string = $attribute_name;
			else
				$passwd_string = $passwd_string.":".$attribute_name;
			$attribute_index++;
		}

		foreach ($users_attributes as $user_attributes){
			$passwd_string = $passwd_string."\n";
			$attribute_index = 0;
			foreach($user_attributes as $attribute_name => $attribute_value){
				if ($attribute_index == 0)
					$passwd_string = $passwd_string.$attribute_value;
				else
					$passwd_string = $passwd_string.":".$attribute_value;
				$attribute_index++;
			}
		}
		ftruncate($fp,0);
		fwrite($fp,$passwd_string);
		fclose($fp);
	}

	//assign privileges for the user $_SESSION["username"]
	function assign_privileges()
	{
		global $C_use_mysql;
		if ($C_use_mysql == true){
			$link = connect_to_users_db();
			$sql = 'SELECT * FROM `privileges` WHERE username = "'.mysqli_real_escape_string($link, $_SESSION["username"]).'"';
			$result = mysqli_query($link, $sql)
				or die(" $result Error: $sql<p>" . mysqli_error($link));
			while ($row = mysqli_fetch_assoc($result)) {
				if($row["username"] == $_SESSION["username"] ){
					/* set the php session */
					foreach ( $row as $attribute_name => $attribute_value){
						$_SESSION[$attribute_name] = $attribute_value;
					}
					break;
				}
			}
			/* Free resultset */
			mysqli_free_result($result);
			/* Closing connection */
			mysqli_close($link);
		}
		else{
			$users_attributes = read_passwd_file();
			foreach($users_attributes as $user_attributes){
				if($user_attributes["username"] == $_SESSION["username"] ){
					/* set the php session */
					foreach ( $user_attributes as $attribute_name => $attribute_value){
						$_SESSION[$attribute_name] = $attribute_value;
					}
					break;
				}
			}
		}
	}

	function authenticate_user($given_username, $given_password){
		global $C_use_mysql,$lang;
		$found = 0;
		$return = '';
		if ($C_use_mysql == true){
			$link = connect_to_users_db();
			$sql = 'SELECT *  FROM `user` WHERE username = "'.mysqli_real_escape_string($link, $given_username).'"';
			$result = mysqli_query($link, $sql)
			  or die(" $result Error: $sql<p>" . mysqli_error($link));
			$row = mysqli_fetch_assoc($result);
			if ($row["password"] == md5($given_password)){
				//check if account is confirmed (always because it breaks the logic on users_edit)
				//if ($C_confirm_new_account && $C_send_emails){
					$sql = 'SELECT *  FROM `user_tokens` WHERE username = "'.mysqli_real_escape_string($link, $given_username).'"';
					$query = mysqli_query($link, $sql)
						or die(" $result Error: $sql<p>" . mysqli_error($link));
					if (!$query || (mysqli_num_rows($query) == 0)) {
						return  "<p class=\"error\">".$lang['pf']['enotok']."</p>";
					}
					$row = mysqli_fetch_assoc($query);
					if ($row["status"] == "unconfirmed"){
						return "<p class=\"error\">".$lang['pf']['eunconf']."</p>".
						"<p align='center'><a href='./confirm_account.php?username=$given_username'>".$lang['general']['clickhere']."</a>".$lang['pf']['sendconf']."</p>";
					}
				//}
				$_SESSION["username"] = $given_username;
				//MOVED for clarity assign_privileges();
				$found = 1;
			}

		}
		else{
			$users_attributes = read_passwd_file();
			foreach($users_attributes as $user_attributes)
			{
				if(($user_attributes["username"] == $given_username) && ($user_attributes["password"] == md5($given_password) ))
				{
					/* set the php session */
					$_SESSION["username"] = $_POST["username"];
					//MOVED for clarity assign_privileges();
					$found = 1;
					break;
				}
			}
		}
		if(!$found)
			return "<p class=\"error\">".$lang['pf']['eauth']."</p>";
	}





?>
