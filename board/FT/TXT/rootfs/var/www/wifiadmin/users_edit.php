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
include_once("./include/header.php");
include_once("./config/config.php");

/*
added by korki
	if mysql isn't used for user storage the no access to this file is required...
*/

if(!$C_use_mysql)
	{
	echo "<p class =\"error\">".$lang['general']['usemysql']."</p>";
	include("./include/footer.php");
	exit();
	}
/*
	eof korki additions
*/


//just siplifies the following code
if(isset($_POST["action"]))
	$_GET["action"] = $_POST["action"];

if(isset($_POST["id"]))
	$_GET["id"] = $_POST["id"];



function check_permission_to_edit(){
// Some notes on security expressions :
//when expression truthness means halt NOT THAT EXPRESSION should compare with the NOT of the NORMAL case (and not compare with the ABNORMAL case)
//e.g. exit if privilege != 'true'    NOT  exit if privilege == 'false'. The first leaves less doubts . Note that if we misspell and write flase the second form will be fooled.....
	if ( @($_SESSION["edit_users"] != "true" && $_SESSION["username"]!= $_GET["id"]) || $_SESSION["username"] =="guest" )
		return false;
	return true;
}

function check_permission_to_delete($link, $user){
	$sql = "SELECT * FROM privileges WHERE edit_privileges='true' AND username!='".mysqli_real_escape_string($link, $user)."'";
	$query = mysqli_query($link, $sql)
		or die(error_echo("Error: $sql<p>" . mysqli_error($link)));
	if ($user == "guest" || ($_SESSION["edit_users"] != "true" && $_SESSION["username"] != $_GET["id"]) ||
		$_SESSION["username"] == "guest" || mysqli_num_rows($query) == 0)
			return false;
	return true;
}



//set default action
if (!isset ($_GET["action"]))
	$_GET["action"] = "list_users";

$link = connect_to_users_db();
$_SESSION['add_users'] = $_SESSION['edit_users'] ; // Add users has no meaning... Deprecation
switch ($_GET["action"]){
	case "list_users":
		echo "\n<ul id=\"tabnav\">\n";
		echo "<li id=\"active-tab\"><a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=list_users>".$lang['useredit']['userlist']."</a></li>\n";
		if (@$_SESSION["add_users"] == "true"){
			echo "<li class=\"tab\"><a href=users_edit.php?action=add_user_form >".$lang['useredit']['addnewuser']."</a></li>\n";
		}
		echo "</ul>";

		if (isset($_GET['order']))
			$order = mysqli_real_escape_string($link, $_GET['order']);
		else
			$order = "email";
		//if you want to see more fields in the user list, just name them here

		//if user can edit users, show unconfirmed ones too
		if (@$_SESSION["edit_users"]== "true"){
			$sql = "SELECT user.username,ip,firstname,lastname,status
				FROM `user`,`user_tokens`
				WHERE
				user.username=user_tokens.username
				ORDER BY $order";
		}
		else{
			$sql = "SELECT user.username,ip,firstname,lastname FROM `user`,`user_tokens`
			WHERE
			user.username=user_tokens.username AND status=\"enabled\" ORDER BY $order";
		}
		$query = mysqli_query($link, $sql)
			or die($lang['dict']['error'].": $sql<p>" . mysqli_error($link));

		//if there are any users
		if (mysqli_num_rows($query) > 0) {
			?>
			<h4>List of users registered in our community</h4>
			<table class="t1" align="center" cellspacing="0">
			<tr bgcolor="#BEC8D1">
			<?php
			//table title
			for ($i = 0; $i < mysqli_num_fields($query); $i++) {
				$field_name = mysqli_fetch_field_direct ($query, $i)->name;
				echo "<th><a href=".$_SERVER['SCRIPT_NAME']."?order=$field_name>$field_name</a></th>\n";
			}

			if (@$_SESSION["edit_users"]== "true")
				echo "<th>".$lang['useredit']['action']."</th></tr>";
			if ($query && (mysqli_num_rows($query) > 0)) {
				$i = 0;
				while ($row = mysqli_fetch_assoc($query)) {
					echo "<tr".(++$i % 2 == 0 ? " class=\"dark\"" : "").">\n";
					foreach ($row as $attribute_name => $attribute_value){
						if($attribute_name=='password' || $attribute_name == 'password_string' )
							continue;
						if($attribute_name =='username')
							echo "<td><a href=".$_SERVER['SCRIPT_NAME']."?action=full_user_info&id=$attribute_value>$attribute_value</a></td>";
						else
							echo "<td>".stripslashes($attribute_value)."</td>";
					}
					if (@$_SESSION["edit_users"]== "true"){
						echo "<td><a href=users_edit.php?id=".$row["username"]."&action=edit>".$lang['dict']['edit']."</a>";
						if ($row["username"]!='guest')
							echo "<a href=users_edit.php?id=".$row["username"]."&action=delete_user_confirm>   ".$lang['dict']['delete']."</a>";
						if ($row["status"]=="unconfirmed")
							echo "<a href=users_edit.php?id=".$row["username"]."&action=enable_user>   ".$lang['dict']['enable']."</a>";
						echo "</td>";
					}
					echo "  </tr>\n";
				}
			}
			echo "</table>\n";
		}
		else
			echo "<p>".$lang['useredit']['enousers']."</p>\n";
		break;
	case "full_user_info":
		$sql = "SELECT * FROM `user` WHERE username=\"".mysqli_real_escape_string($link, $_GET["id"])."\"";
		$query = mysqli_query($link, $sql)
			or die("Error: $sql<p>" . mysqli_error($link));
		$row = mysqli_fetch_assoc($query);
		echo "\n<ul id=\"tabnav\">\n";
		echo "<li id=\"tab\"><a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=list_users>".$lang['useredit']['ulist']."</a></li>\n";
		if (@$_SESSION["add_users"] == "true"){
			echo "<li class=\"tab\"><a href=users_edit.php?action=add_user_form >".$lang['useredit']['adduser']."</a></li>\n";
		}
		//if such a user exists
		if (mysqli_num_rows($query) > 0) {
			echo "<li class=\"tab\" id=\"active-tab\"><a href=".$_SERVER['SCRIPT_NAME']."?action=full_user_info&id=".$row["username"].">".$row["username"]." info</a></li>";
			if (check_permission_to_edit() == true) {
					echo "<li class=\"tab\"><a href=users_edit.php?id=".$row["username"]."&action=edit>".$lang['dict']['edit'] . " ".$row["username"]."</a></li>";
					echo "<li class=\"tab\"><a href=".$_SERVER['PHP_SELF']."?action=delete_user_confirm&id=".$row["username"].">".$lang['dict']['delete']. " ".$row["username"]."</a></p>";
				}
		}
		echo "</ul>";

		//if there are any users
		if (mysqli_num_rows($query) > 0) {
			echo "<h4>".$lang['dict']['full']." ".$row["username"]." ".$lang['dict']['info']."</h4>"?>
			<table class='t1' align="center">
			<?php

			foreach ($row as $attribute_name => $attribute_value){
				if ($attribute_name == "password" || $attribute_name == "password_string")
					continue;
				if ($attribute_name == "mac" && @$_SESSION["view_macs"] != "true")
					continue;
				echo "<tr><th>$attribute_name</th><td>$attribute_value</td></tr>\n";
			}
			echo "</table><div align=\"center\">";
		}
		else {
			echo "<p>".$lang['useredit']['enousers']."</p>\n";
		}
		break;
	case "edit":
		if (check_permission_to_edit() == false)
			die(error_echo($lang['useredit']['enoperm']));
		$sql = 'SELECT * FROM `user` WHERE username = "'.mysqli_real_escape_string($link, $_GET["id"]).'"';
		$result = mysqli_query($link, $sql)
			or die($lang['dict']['error'].": $sql<p>" . mysqli_error($link));
		$row = mysqli_fetch_assoc($result);

		echo "\n<ul id=\"tabnav\">\n";
		echo "<li id=\"tab\"><a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=list_users>".$lang['useredit']['userlist']."</a></li>\n";
		if ($_SESSION["add_users"] == "true"){
			echo "<li class=\"tab\"><a href=users_edit.php?action=add_user_form >".$lang['useredit']['addnewuser']."</a></li>\n";
		}
		//if such a user exists
		if (mysqli_num_rows($result) > 0) {
			echo "<li class=\"tab\" ><a href=".$_SERVER['SCRIPT_NAME']."?action=full_user_info&id=".$row["username"].">".$row["username"]." ".$lang['dict']['info']."</a></li>";
			if ( check_permission_to_edit() == true) {
					echo "<li class=\"tab\" id=\"active-tab\"><a href=users_edit.php?id=".$row["username"]."&action=edit>".$lang['dict']['edit']." ".$row["username"]."</a></li>";
					echo "<li class=\"tab\"><a href=".$_SERVER['PHP_SELF']."?action=delete_user_confirm&id=".$row["username"].">".$lang['dict']['delete']." ".$row["username"]."</a></p>";
				}
		}
		echo "</ul>";

		//if there are any users
		if (!mysqli_num_rows($result) > 0)
			echo mydie($lang['useredit']['enouser']);

		?>
		<form name="edit" onSubmit="submitonce(this)" action="<?php echo $_SERVER['PHP_SELF']?>" method="POST">
		<?php echo("
		<h4>".$lang['useredit']['edituser']. " ".$row["username"]."</h4>
		<table class=\"t1\" align=\"center\">

			<tr><td>".$lang['useredit']['uname']."</td><td><input type=\"text\" name=\"username\" value=\"".$row["username"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['pass']."</td><td><input type=\"password\" name=\"password\" value=\"".$row["password_string"]."\" class=\"userattribute\" ></td></tr>
			<tr><td>".$lang['useredit']['repass']."</td><td><input type=\"password\" name=\"retype\" value=\"".$row["password_string"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['mail']."</td><td><input type=\"text\" name=\"email\" value=\"".$row["email"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['macaddr']."</td><td><input type=\"text\" name=\"mac\" value=\"".$row["mac"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['ipaddr']."</td><td><input type=\"text\" name=\"ip\" value=\"".$row["ip"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['subnet']."</td><td><input type=\"text\" name=\"owns_subnet\" value=\"".$row["owns_subnet"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['fname']."</td><td><input type=\"text\" name=\"firstname\" value=\"".$row["firstname"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['lname']."</td><td><input type=\"text\" name=\"lastname\" value=\"".$row["lastname"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['phone1']."</td><td><input type=\"text\" name=\"phone1\" value=\"".$row["phone1"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['phone2']."</td><td><input type=\"text\" name=\"phone2\" value=\"".$row["phone2"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['ant']."</td><td><input type=\"text\" name=\"antenna\" value=\"".$row["antenna"]."\" class=\"userattribute\"></td></tr>
			<tr><td>".$lang['useredit']['windid']."</td><td><input type=\"text\" name=\"nodedb_id\" value=\"".$row["nodedb_id"]."\" class=\"userattribute\"></td></tr>
			<tr><td valign=\"top\">".$lang['useredit']['services']."</td><td><textarea rows=\"5\" cols=\"25\" name=\"services\"  class=\"userattribute\">".$row["services"]."</textarea></td></tr>
			<tr><td>".$lang['useredit']['comment']."</td><td><input type=\"text\" name=\"comment\" value=\"".$row["comment"]."\" class=\"userattribute\"></td></tr>");
			?><tr><td colspan="2" class="error"><?php echo $lang['general']['mandatory'] ?></td><?php  //added by korki (...starred items are madatory...)

		$sql = 'SELECT * FROM `privileges` WHERE username = "'.mysqli_real_escape_string($link, $_GET["id"]).'"';
		$result = mysqli_query($link, $sql)
			or die("Error: $sql<p>" . mysqli_error($link));
		//if there are any users
		if (!mysqli_num_rows($result) > 0)
			echo mydie("No privileges for such user (possible bug)");
		if($_SESSION["edit_privileges"]=="true")
		{
			$row = mysqli_fetch_assoc($result);
				foreach ($row as $privilege_name => $privilege_value){
					if ($privilege_name == "username")
						continue;
					echo "<tr> <td class=\"capitalize\">". str_replace("_"," ",$privilege_name)."</td> <td> <input type=checkbox name=\"$privilege_name\"";
					if ($row[$privilege_name] == "true")
						echo " checked></td>";
					else
						echo " ></td>";
					echo "</tr>";
				}
		}?>
		</table><div align="center">
		<input type=hidden name="action" value="save_user">
		<input type=hidden name="id"<?php echo "value=".$_GET["id"]?>>
		<input type="submit" value="<?php echo $lang['useredit']['submituser']?>">
		</form></div>
<?php
		break;
	case "save_user":
		if (check_permission_to_edit() == false)
			die(error_echo($lang['edituser']['enoperm']));;
		//form completion errors
		$errors = "";
		if(empty($_POST['username']))
			$errors = error_echo($lang['useredit']['enouser']);
		if($_POST['password']!=$_POST['retype'])
			$errors .= error_echo($lang['useredit']['enopassmatch']);
		if(empty($_POST['email']))
			$errors .= error_echo($lang['useredit']['enomail']);
		if(empty($_POST['mac']) || !isset($_POST['ip']))
			$errors .= error_echo($lang['useredit']['enomac']);
		if(strlen($errors))		//if there are user errors, print and die
			mydie($errors);
		$_POST['password_string'] = $_POST['password'];
		$_POST['password'] = md5($_POST['password']);

		$sql = "SHOW COLUMNS FROM `user`";
		$user_fields = mysqli_query($link, $sql)
			or die("Error: $sql<p>" . mysqli_error($link));
		$columns = mysqli_num_rows($user_fields);

		//update user table
		$sql = "UPDATE `user` SET ";
		for ($i = 0; $i < $columns; $i++) {
			$row = mysqli_fetch_assoc($user_fields);
			$_field_name = $row['Field'];
			if ($i == 0)
				$sql .= $_field_name."=\"".mysqli_real_escape_string($link, $_POST[$_field_name])."\" ";
			else
				$sql .= ",".$_field_name."=\"".mysqli_real_escape_string($link, $_POST[$_field_name])."\" ";
		}
		$sql .= "WHERE username=\"".mysqli_real_escape_string($link, $_POST["id"])."\"";
		mysqli_query($link, $sql)
			or die("Error: $sql<p>" . mysqli_error($link));

		$sql = "SHOW COLUMNS FROM `privileges`";
		$privileges_fields = mysqli_query($link, $sql)
			or die("Error: $sql<p>" . mysqli_error($link));
		$columns = mysqli_num_rows($privileges_fields);

		//update privileges table if current user can edit privileges
		if($_SESSION["edit_privileges"]){
			$sql = "UPDATE `privileges` SET ";
			for ($i = 0; $i < $columns; $i++) {
				$row = mysqli_fetch_assoc($privileges_fields);
				$_field_name = $row['Field'];
				if ($_field_name == "username")
					continue;
				if ($i != 1)
					$sql .=", ";
				$sql .= $_field_name."=";
				if (isset ($_POST[$_field_name]) )
					$sql .= "\"true\" ";
				else
					$sql .= "\"false\" ";
			}

			$sql .= "WHERE username=\"".mysqli_real_escape_string($link, $_POST["id"])."\"";
			mysqli_query($link, $sql)
				or die($lang['dict']['error'].": $sql<p>" . mysqli_error($link));
		}
		echo "<p>".$lang['useredit']['rsucc']."</p><meta http-equiv=\"refresh\" content=\"1;URL=".$_SERVER['PHP_SELF']."?action=full_user_info&id=".$_GET["id"]."\"/>";
		break;
	case "delete_user_confirm":
		echo "\n<ul id=\"tabnav\">\n";
		echo "<li id=\"tab\"><a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=list_users>".$lang['useredit']['ulist']."</a></li>\n";
		if ($_SESSION["add_users"] == "true"){
			echo "<li class=\"tab\"><a href=users_edit.php?action=add_user_form >".$lang['useredit']['addnewuser']."</a></li>\n";
		}
		//if such a user exists
		echo "<li class=\"tab\" ><a href=".$_SERVER['PHP_SELF']."?action=full_user_info&id=".$_GET["id"].">".$_GET["id"]." ".$lang['dict']['info']."</a></li>";
		if ( check_permission_to_edit() == true){
				echo "<li class=\"tab\" ><a href=users_edit.php?id=".$_GET["id"]."&action=edit>".$lang['dict']['edit']." ".$_GET["id"]."</a></li>";
				echo "<li class=\"tab\" id=\"active-tab\"><a href=".$_SERVER['PHP_SELF']."?action=delete_user_confirm&id=".$_GET["id"].">".$lang['dict']['delete']." ".$_GET["id"]."</a></p>";
			}

		echo "</ul>";
		if (check_permission_to_delete($link, $_GET["id"]) == false)
			die(error_echo($lang['useredit']['elockout']));
		echo "<H3>".$lang['useredit']['wdeluser']." ".$_GET["id"].". ".$lang['general']['areyousure']."</H3>
			<div align=\"center\"><big><a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=delete_user&id=".$_GET["id"].">".$lang['dict']['yes']."</a>
			<a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=edit&id=".$_GET["id"].">".$lang['dict']['no']."</a>
			</big></div>";
		break;
	case "delete_user":
		if (check_permission_to_delete($link, $_GET["id"]) == false)
				die(error_echo($lang['useredit']['elockout']));
		$sql = "DELETE FROM `user` WHERE `username` = '".mysqli_real_escape_string($link, $_GET["id"])."' LIMIT 1";
		mysqli_query($link, $sql)
			or die("Error: $sql<p>" . mysqli_error($link));
		$sql = "DELETE FROM `privileges` WHERE `username` = '".mysqli_real_escape_string($link, $_GET["id"])."' LIMIT 1";
		mysqli_query($link, $sql)
			or die("Error: $sql<p>" . mysqli_error($link));
		$sql = "DELETE FROM `user_tokens` WHERE `username` = '".mysqli_real_escape_string($link, $_GET["id"])."' LIMIT 1";
		mysqli_query($link, $sql)
			or die("Error: $sql<p>" . mysqli_error($link));
		echo "<p><h3>".$lang['useredit']['rdel']."</h3></p><meta http-equiv=\"refresh\" content=\"2;URL=".$_SERVER['PHP_SELF']."?action=list_users\"/>";
		break;
	case "add_user_form":
		echo "\n<ul id=\"tabnav\">\n";
		echo "<li class=\"tab\"><a href=".$_SERVER['PHP_SELF']."?category=User_Managment&action=list_users>".$lang['useredit']['userlist']."</a></li>\n";
		if ($_SESSION["add_users"] == "true"){
			echo "<li class=\"tab\" id=\"active-tab\"><a href=users_edit.php?action=add_user_form >".$lang['useredit']['addnewuser']."</a></li>\n";
		}
		echo "</ul>";
		if ($_SESSION["add_users"] != "true"){
			echo error_echo( $lang['useredit']['enopermadd'] );
			break;
		}
		echo_user_add_form();
		break;
	case "add_user":
		if ($_SESSION["add_users"] != "true"){
			echo error_echo($lang['useredit']['enopermadd']);
			break;
		}
		//add the user without sending email confirmation.
		add_user( $_POST, false);
		echo "<p><h3>".$lang['useredit']['rsucc']."</h3></p><meta http-equiv=\"refresh\" content=\"0;URL=".$_SERVER['PHP_SELF']."?action=list_users\"/>";
		break;
	case "enable_user":
		$sql = "UPDATE user_tokens SET status='enabled' WHERE username='".mysqli_real_escape_string($link, $_GET["id"])."'";
		mysqli_query($link, $sql)
			or die( $lang['dict']['error'].": $sql - ".mysqli_error($link));
		echo "<p><h3>".$lang['dict']['user']." ".$_GET["id"]." ".$lang['useredit']['ruser']. " </h3></p><meta http-equiv=\"refresh\" content=\"0;URL=".$_SERVER['PHP_SELF']."?action=list_users\"/>";
		break;
}
include("./include/footer.php");
?>
