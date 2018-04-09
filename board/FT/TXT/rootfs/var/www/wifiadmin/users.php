<?php /*+-------------------------------------------------------------------------+
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

include_once("./include/header.php"); //echo the default Header entries
?>
<script src="include/submitonce.js"></script>
	<h2><?php echo $lang['users']['title'] ?></h2>
<?php function check_users_attributes( $users_attributes ){
	//check if at least one user has privilege to edit users
	global $lang ;
	foreach( $users_attributes as $user_attributes){
		if ($user_attributes["edit_users"] == "true")
			return true;
	}
	echo "<p class = \"error\">".$lang['users']['enoperm']."</p>";
	return false;
} 

//check privilege to edit users

//added by korki - no direct call to file without proper auth should be carried...

if (@$_SESSION["edit_users"] != "true"){
	echo "<p class = \"error\">".$lang['general']['enoperm']."</p>";
	include_once("./include/footer.php");	
	exit();
}

//set default action
if (!isset($_GET["action"]))
	$_GET["action"] = "list_users";

switch ($_GET["action"]){
	case "edit_user_form":
		$users_attributes = read_passwd_file();
		$user_attributes = $users_attributes[$_GET["user_id"]];
		echo "<h3>Edit User</h3>
			<form method = \"POST\" onSubmit=\"submitonce(this)\" action = ".$_SERVER['PHP_SELF']."?category=User_Managment>
			<table align=\"left\" border = 0 >";
			if( $user_attributes["username"] != "guest")
				echo "<tr><td>".$lang['useredit']['uname']."</td><td width=\"100%\"><input type=text name=username value=\"".$user_attributes["username"]."\" class=\"text\"></td></tr>
				<tr><td>".$lang['useredit']['pass']."</td><td><input type=password name=password class=\"text\"></td></tr>
				<tr><td>".$lang['useredit']['repass']."</td><td> <input type=password name=password_retype class=\"text\"></td></tr>";
			else
				echo "<tr><td align=\"left\" nowrap> ".$lang['users']['guestuser']." </td><td width=\"100%\">&nbsp;</td></tr>
					<tr><td><input type=hidden name=username value=guest></td></tr>";

			foreach($user_attributes as $attribute_name => $attribute_value){
				if ($attribute_name == "password" || $attribute_name == "username")
					continue;
				echo "<tr> <td class=\"capitalize\">". str_replace("_"," ",$attribute_name)."</td> <td> <input type=checkbox name= $attribute_name";
				if ($user_attributes[$attribute_name] == "true")
					echo " checked></td>";
				else
					echo " ></td>";
				echo "</tr>";
			}
				echo "<tr><td>
				<input type=hidden name=\"action\" value=\"save_user\">
				<input type=hidden name=\"user_id\" value=".$_GET["user_id"].">
				<tr><td><input type =submit value=\"".$lang['users']['submit']."\"></td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan=2><a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=delete_user_confirm&user_id=".$_GET["user_id"]."&username=".$user_attributes["username"].">[ ".$lang['dict']['delete']." ".$user_attributes["username"]." ]</a></td></tr>
				<tr><td colspan=2 align=\"right\"><a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=list_users>".$lang['users']['backto']."</a></td></tr>
				</table> </form>";
				
		break;
	case "delete_user_confirm":
		echo "<H3>".$lang['useredit']['wdeluser']." ".$_GET["username"]." ".$lang['general']['areyousure']."</H3>
			<div align=\"center\"><big><a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=delete_user&user_id=".$_GET["user_id"].">".$lang['dict']['yes']."</a>
			<a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=edit_user_form&user_id=".$_GET["user_id"].">".$lang['dict']['no']."</a>
			</big></div>";
		break;

	case "list_users" :case "delete_user":
		$users_attributes = read_passwd_file();
		if ($_GET["action"] == "delete_user" ){
			unset ( $users_attributes[ $_GET["user_id"]]);
			if (check_users_attributes( $users_attributes ))
				write_passwd_file($users_attributes);
			else
				$users_attributes = read_passwd_file();
		}
		
		if ( isset($_POST["action"]) && ( $_POST["action"] == "save_user")){
			$user_attributes = $users_attributes[$_POST["user_id"]];
			//check if username and pass are given
			if (!isset($_POST["username"]) || trim($_POST["username"]) == ""){
				echo "<h2>".$lang['users']['nouser']."</h2>";
				break;
			}
				
			//check if given username already exists
			$search_user_index = 0;
			foreach($users_attributes as $user_attributes){
				if  ($user_attributes["username"] == $_POST["username"] && $search_user_index != $_POST["user_id"] ){
					echo "<h2>".$lang['users']['aunameexist']."  ".$_POST["username"]." ".$lang['users']['eexists']."</h2>";
					$break = "true";
					break;
				}
				$search_user_index++;
			}
			
			if ( isset($break))
				break; 
			//check if password retype is correct
			if ( (isset($_POST["password"]) && isset($_POST["password_retype"])) &&  ($_POST["password"]!= $_POST["password_retype"])){
				echo "<h2>".$lang['useredit']['enopassmatch']."</h2>";
				break;
			}
			
			if ( (!isset($_POST["password"]) && isset($_POST["password_retype"])) ||  (isset ($_POST["password"]) && (!isset ($_POST["password_retype"]) ) ) ){
				echo "<h2>".$lang['user']['fillpass']."</h2>";
				break;
			}
			
			//check if username or password contain :
			if ( strpos($_POST["password"], ':')){
				echo "<h2>".$lang['user']['epasschar']."</h2>";
				break;
			}
			
			if ( strpos($_POST["username"], ':')){
				echo "<h2>".$lang['user']['eunamechar']."</h2>";
				break;
			}
			
			//ok, save the user
			foreach($user_attributes as $attribute_name => $attribute_value){
				if($attribute_name == "password"){
					if ( isset( $_POST["password"]) )
						$user_attributes["password"] = md5($_POST["password"]);
				}
				else if($attribute_name == "username")
					$user_attributes["username"] = $_POST["username"];
				else{
					if (isset ($_POST[$attribute_name]) )
						$user_attributes[$attribute_name] = "true";
					else
						$user_attributes[$attribute_name] = "false";
				}
			}
			$users_attributes[$_POST["user_id"]] = $user_attributes;
			if (check_users_attributes( $users_attributes ))
				write_passwd_file($users_attributes);
			else
				$users_attributes = read_passwd_file();
		}

		if ( isset($_POST["action"]) && $_POST["action"] == "add_user"){
			//check if username and pass are given
			if (!isset($_POST["username"]) || trim($_POST["username"]) == ""){
				echo "<h2>".$lang['user']['nouname']."</h2>";
				break;
			}
			if (!isset($_POST["password"]) || trim($_POST["password"]) == ""){
				echo "<h2>".$lang['user']['nopass']."</h2>";
				break;
			}
			if (!isset($_POST["password_retype"])|| trim($_POST["password_retype"]) == ""){
				echo "<h2>".$lang['user']['fillpass']."</h2>";
				break;
			}
				
			//check if given username already exists
			foreach($users_attributes as $user_attributes){
				if  ($user_attributes["username"] == $_POST["username"]){
					echo "<h2>".$lang['users']['aunameexist']."  ".$_POST["username"]." ".$lang['users']['eexists']."</h2>";
					$break = "true";
					break;
				}
			}
			
			if ( isset($break))
				break; 
			//check if password retype is correct
			if ( $_POST["password"]!= $_POST["password_retype"]){
				echo "<h2>".$lang['useredit']['enopassmatch']."</h2>";
				break;
			}
			
			//check if username or password contain :
			if ( strpos($_POST["password"], ':')){
				echo "<h2>".$lang['user']['epasschar']."</h2>";
				break;
			}
			
			if ( strpos($_POST["username"], ':')){
				echo "<h2>".$lang['user']['eunamechar']."</h2>";
				break;
			}
			//ok, add the user
			foreach($users_attributes[0] as $attribute_name => $attribute_value){
				$new_user_attributes[$attribute_name] = "false";
			}
			$new_user_attributes["username"] = $_POST["username"];
			$new_user_attributes["password"] = md5($_POST["password"]);
			foreach( $_POST as $name => $value){
				if ( $name=="username" ||$name == "password_retype" || $name == "action" || $name == "password")
					continue;
				$new_user_attributes[ $name ] = "true";
			}
			$users_attributes[count ($users_attributes)] = $new_user_attributes;
			//save passwd file
			if (check_users_attributes( $users_attributes ))
				write_passwd_file($users_attributes);
			else
				$users_attributes = read_passwd_file();
		}
		
		?><H3><?php echo $lang['users']['seluser']?></H3>
		
		<ul align="left">
		<?php 		$user_index = 0;
		foreach($users_attributes as $user_attributes)
		{
			echo "<li><a href=\"".$_SERVER['PHP_SELF']."?category=User_Managment&action=edit_user_form&user_id=".$user_index."\">".$user_attributes["username"]."</a></li>";
			$user_index++;
		}
		echo "<br><a href=\"".$_SERVER['PHP_SELF']."?category=User_Managment&action=add_user_form\">[ ".$lang['useredit']['addnewuser']." ]</a>";
		break;

	case "add_user_form":
		$users_attributes = read_passwd_file();
		echo "<h3>".$lang['useredit']['addnewuser']."</h3>
			<form method = \"POST\" onSubmit=\"submitonce(this)\" action = ".$_SERVER['PHP_SELF']."?category=User_Managment>
			<table align=\"left\" border = 0 >";
			echo "<tr><td>".$lang['general']['username']."</td><td width=\"100%\"><input type=text name=username class=\"text\"></td></tr>
			<tr><td>".$lang['general']['password']."</td><td><input type=password name=password class=\"text\"></td></tr>
			<tr><td>".$lang['useredit']['repass']."</td><td> <input type=password name=password_retype class=\"text\"></td></tr>";

			foreach($users_attributes[0] as $attribute_name => $attribute_value){
				if ($attribute_name == "password" || $attribute_name == "username")
					continue;
				echo "<tr> <td class=\"capitalize\">". str_replace("_"," ",$attribute_name)."</td> <td> <input type=checkbox name=\"$attribute_name\"></td></tr>";
			}
				echo "<tr><td>
				<input type=hidden name=\"action\" value=\"add_user\">
				<tr><td><input type =submit value = \"".$lang['users']['submit']."\"></td></tr>
				<tr><td>&nbsp;</td></tr>
				<tr><td colspan=2 align=\"right\"><a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=list_users>".$lang['user']['backto']."</a></td></tr>
				</table> </form>";	
		break;
}

include_once("./include/footer.php");
?>
