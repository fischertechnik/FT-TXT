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

include_once("./config/config.php");


function connect_to_users_db() {
	global $C_USERS_DBHOST,$C_USERS_DB, $C_USERS_DBUSER, $C_USERS_DBPASS,$lang;
	$link = mysqli_connect($C_USERS_DBHOST, $C_USERS_DBUSER, $C_USERS_DBPASS)
    or
		die($lang['cf']['noconnect']." $C_USERS_DBHOST, $C_USERS_DBUSER , $C_USERS_DBPASS ". mysqli_error($link));

	if (!mysqli_select_db($link, $C_USERS_DB)) {
		// couldn't connect
		echo $lang['cf']['noselect']." ($C_USERS_DB)";
	}
	return $link;
}

function mydie($string)
{
	echo "<p class='error'> $string </p>";
	exit();
}

function send_confirmation_email($email, $token) {
	global $C_web_master,$C_web_master_EMAIL,$lang;
	$base = dirname($_SERVER["PHP_SELF"]);
	$WiFiAdmin_BASE_URL =  "http://".$_SERVER["SERVER_NAME"].$base."/";
	return mail($email,
	$lang['cf']['msg'],
	$lang['cf']['msg2']. $WiFiAdmin_BASE_URL."confirm_account.php?token=".base64_encode($token).$lang['cf']['tx'].$C_web_master,
	 "From: $C_web_master <$C_web_master_EMAIL>\n");
}

function echo_user_add_form(){
	global $lang;
	?>
	<h2><?php echo $lang['useredit']['regnewusr']?></h2>
	<form name="register" onSubmit="submitonce(this)" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
	<table class="t1" align="center">
		<tr><td><?php echo $lang['useredit']['uname'] ?></td><td><input type="text" name="username"></td></tr>
		<tr><td><?php echo $lang['useredit']['pass'] ?></td><td><input type="password" name="password"></td></tr>
		<tr><td><?php echo $lang['useredit']['repass'] ?></td><td><input type="password" name="retype"></td></tr>
		<tr><td><?php echo $lang['useredit']['mail'] ?></td><td><input type="text" name="email"></td></tr>
		<tr><td><?php echo $lang['useredit']['macaddr'] ?></td><td><input type="text" name="mac"></td></tr>
		<tr><td><?php echo $lang['useredit']['ipaddr'] ?></td><td><input type="text" name="ip"></td></tr>
		<tr><td><?php echo $lang['useredit']['subnet'] ?></td><td><input type="text" name="subnet"></td></tr>
		<tr><td><?php echo $lang['useredit']['fname'] ?></td><td><input type="text" name="firstname"></td></tr>
		<tr><td><?php echo $lang['useredit']['lname'] ?></td><td><input type="text" name="lastname"></td></tr>
		<tr><td><?php echo $lang['useredit']['phone1'] ?></td><td><input type="text" name="phone1"></td></tr>
		<tr><td><?php echo $lang['useredit']['phone2'] ?></td><td><input type="text" name="phone2"></td></tr>
		<tr><td><?php echo $lang['useredit']['ant'] ?></td><td><input type="text" name="antenna"></td></tr>
		<tr><td><?php echo $lang['useredit']['windid'] ?></td><td><input type="text" name="nodedb_id"></td></tr>
		<tr><td valign="top"><?php echo $lang['useredit']['services'] ?></td><td><textarea rows="5" cols="25" name="services"></textarea></td></tr>
		<tr><td><?php echo $lang['useredit']['comment'] ?></td><td><input type="text" name="comment"></td></tr>
		<tr><td colspan="2" class="error"><?php echo $lang['general']['mandatory'] ?></td>
	</table>
	<input type="hidden" name="action" value="add_user">
	<div align="center"><input type="submit" value="<?php echo $lang['useredit']['submituser']?>"></div>
	</form>
<br \>
<br \>

	<?php
}

function add_user( $user_info, $send_confirmation_email){
	global $C_USERS_DB,$lang;
	//form completion errors
	$errors = "";
	if(empty($user_info['username']))
		$errors = error_echo($lang['useredit']['enouser']);
	if(empty($user_info['password']))
		$errors .= error_echo($lang['useredit']['enopass']);
	if($user_info['password']!=$user_info['retype'])
		$errors .= error_echo($lang['useredit']['enopassmatch']);
	if(empty($user_info['email']))
		$errors .= error_echo($lang['useredit']['enoemail']);
	if(empty($user_info['mac']) || !isset($user_info['ip']))
		$errors .= error_echo($lang['useredit']['enomac']);

	if(strlen($errors))		//if there are user errors, print and die
		mydie($errors);

	//database errors
	$link = connect_to_users_db();

	foreach($user_info as $key=>$var)
	{
		$user_info[$key]=mysqli_real_escape_string($link, $var);
		if(!isset($user_info[$key]))
			$user_info[$key]="";
	}

	$sql = "SELECT username FROM $C_USERS_DB.user WHERE username = '".$user_info['username']."'";
	$user = mysqli_query($link, $sql) or
		mydie($lang['dict']['error']." ". mysqli_error($link) );
	if(mysqli_fetch_array($user))
		mydie($lang['cf']['eusernameexist']);

	$sql = "SELECT email FROM $C_USERS_DB.user WHERE email = '".$user_info['email']."'";
	$email = mysqli_query($link, $sql) or
		mydie($lang['dict']['error']." ". mysqli_error($link));
	if(mysqli_fetch_array($email))
		mydie($lang['cf']['eemailexist']);

////Insert DATA into user database!
	 $insert =	"INSERT INTO $C_USERS_DB.user
	(username,password,email,mac,ip,owns_subnet,services,phone1,phone2,antenna,nodedb_id,comment,firstname,lastname,password_string)
     VALUES ('" .$user_info['username']. "', '" .md5($user_info['password']). "', '" .$user_info['email']. "',
     '" .$user_info['mac']. "', '" .$user_info['ip']. "','" .$user_info['subnet']. "', '" .$user_info['services'].
     "', '" .$user_info['phone1']. "', '" .$user_info['phone2']. "', '" .$user_info['antenna']."', '" .$user_info['nodedb_id']."', '"
     .$user_info['comment']. "', '" .$user_info['firstname']."', '" .$user_info['lastname']."', '".$user_info['password']."')";

	$privilege = "INSERT INTO $C_USERS_DB.privileges
	(username,view_status,view_status_ext,view_macs,ban_users,access_ifs,edit_users, edit_privileges  )
	VALUES ('".$user_info['username']."','true','false','false','false','false','false','false')";

 	mysqli_query($link, $insert)
    or mydie($lang['cf']['eusrreg']." ". mysqli_error($link));

  mysqli_query($link, $privilege)
  	or mydie($lang['cf']['eusrreg2']." ". mysqli_error($link));


	if ($send_confirmation_email){
		send_confirmation($link, $user_info['username']);
	}
	else{
		$token = time() . "::".$user_info["username"];
		$sql = "INSERT INTO user_tokens (username,status,token) VALUES ('".$user_info['username']."','enabled','".mysqli_real_escape_string($link, $token)."')";
		mysqli_query($link, $sql)
      or mydie("Error in query: $sql - ".mysqli_error($link));
		echo "<p>".$lang['cf']['success']."</p>";
	}
}

function send_confirmation($link, $username) {
	global $lang;

  $username = mysqli_real_escape_string($link, $username);
	$sql = "SELECT email FROM user WHERE username = '".$username."'";
	$user = mysqli_query($link, $sql)
    or mydie($lang['dict']['error']." ". mysqli_error($link) );
	if(! $row = mysqli_fetch_array($user))
		mydie($lang['cf']['eusernamebexist']);
	$email = $row[0];

	$token = time() . "::".$username;
	$sql = "SELECT * FROM user_tokens WHERE username='".$username."'";
	$result = mysqli_query($link, $sql)
    or mydie("Error in query: $sql - ".mysqli_error($link));
	if(mysqli_fetch_array($result))
		$sql = "UPDATE user_tokens SET token='".mysqli_real_escape_string($link, $token)."',status='unconfirmed' WHERE username='".$username."'";
	else
		$sql = "INSERT INTO user_tokens (username,status,token) VALUES ('".$username."','unconfirmed','".mysqli_real_escape_string($link, $token)."')";
	mysqli_query($link, $sql)
    or mydie("Error in query: $sql - ".mysqli_error($link));

	if (!send_confirmation_email($email, $token))
		mydie($lang['fpass']['error']);
	else
		echo ("<p>".$lang['cf']['confirm']."</p>");
}


?>
