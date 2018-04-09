<?php /*+-------------------------------------------------------------------------+
  | Copyright (C) 2008 Korkakakis Nikos (korkakak@ceid.upatras.gr)          |
  |                    basOS (basos@users.sf.net)                                                     |
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
  | Based on previous work of 						    |
  | - panousis@ceid.upatras.gr	- Panousis 8anos			    |
  | - dimopule@ceid.upatras.gr	- Dimopoulos 8umios   			    |
  +-------------------------------------------------------------------------+*/

//TODO : Add download file support when not auto saved

if(strpos($_SERVER['PHP_SELF'],basename(__FILE__)))
	require "../include/trap.php";

require "./include/constants.php";

error_reporting(E_ALL);
/*************
	Option Class to abstract parsing,validations,configfile printing
*************/
class option
{
	var $val_name,$val,$val_type;
	var $val_default,$values,$validate_callback;
	var $val_invalid;
	var $string_ref;

	/* Fix PHP's nasty String casting .Cast from anything to string*/
	function _getstr ($val)
	{
		if (is_bool($val))
			return $val ===false ? "false" : "true" ;
		else
			return (string) $val ;
	}

	/* Return quoted string of val */
	function _get_quoted_str () {
		if ($this->val_type == "string")
			return "\"".$this->val."\"";
		else
			return $this->_getstr($this->val);
	}

	/*** Cast from string to native type **/
	function _getvar ($val)
	{
		if ($this->val_type == 'boolean') {
			return $val == "false" ? false : true;
		}
		else {
			settype ($val,$this->val_type);
			return $val;
		}
	}

	function validate($confs)
	{
		$func = $this->validate_callback;
		if ($func != '')
			if ($func ($confs)) {
				$this->val_invalid =false;
				return true;
			}
			else {
				$this->val_invalid = true;
				return false;
			}
		else
			return true;
	}

	function invalidate()
	{
		$this->val_invalid = true;
	}

	function name()
	{
		return $this->val_name;
	}

	/** sets the option value from its string representation casted back to native **/
	function set_string($nval)
	{
		$this->val = $this->_getvar($nval);
		$this->val_invalid = false;
	}

	function set($nval)
	{
		settype ($nval,$this->val_type);
		$this->val = $nval;
	}

	function set_default()
	{
		$this->val = $this->val_default;
		$this->val_invalid = false;
	}

	function get_config_entry()
	{
		@$entry = "\n/* ".$this->string_ref[$this->val_name]." */\n";
		$entry .= "\$C_".$this->val_name." = ".$this->_get_quoted_str() ." ;\n";
		return $entry;
	}



	function print_html_entry()
	{
		echo "
		<tr>
			<td>".str_replace("\n","<br />",$this->string_ref[$this->val_name])."</td>
			<!-- ". $this->val_name ." -->";
		echo "
			<td>";
		if (sizeof($this->values)>0) { //multiple options
			echo "
				<select name=\"".$this->val_name."\" onClick=\"fields_disable_enable()\" ".($this->val_invalid ? "class='error' >" : " >");
			foreach ($this->values as $opt) {
				echo "
				<option value=\"".$this->_getstr($opt)."\"".($this->val==$opt ? "selected=\"selected\">" : ">").$this->_getstr($opt). "</option>";
			}
			echo "
				</select>";
		} else { //just an input box
			echo "
				<input name=\"".$this->val_name."\" value=\"". $this->val ."\" ".($this->val_invalid ? "class='error' >" : " >");
		}
		echo "
			</td>
		</tr>
		";
	}

	/*  Constructor
		o_name : opt name will be used @ post data and it will be outputed to
		the config as $C_option_name,
		o_default: default value
		o_validate_cback : validator function (optional, leave as empty string for no use)
				should return true or error string
		o_values : if it is a multiple choice option, list posible values, declare an empty array for no use
    */
	function option($o_name,$o_default,$o_validate_cback = '',$o_values = array())
	{
		global $lang;
		$this->string_ref = $lang['inst'];
		$this->val_name = $o_name;
		$this->val_default = $o_default;
		$this->validate_callback = $o_validate_cback;
		$this->val_type = gettype($o_default);
		$this->values = $o_values;
		if ($this->val_type == "boolean" ) $this->values = array(true,false);
		$this->set_default();
	}
}

/* the following javascript snippet sets a poller that runs every 200ms (0.2sec) that checks some variables (usemysql and resolve form fields and disables them or enables them accordingly. */
?>
<script type="text/javascript">

	function fields_disable_enable()
		{
		if (document.all || document.getElementById)
			{
			if (document.install.use_mysql.value=='true')
				{
				document.install.USERS_DBHOST.disabled=false
				document.install.USERS_DBUSER.disabled=false
				document.install.USERS_DBPASS.disabled=false
				document.install.USERS_DB.disabled=false
				document.install.passwd_filename.disabled=true;
				}
			else
				{
				document.install.USERS_DBHOST.disabled=true
				document.install.USERS_DBUSER.disabled=true
				document.install.USERS_DBPASS.disabled=true
				document.install.USERS_DB.disabled=true
				document.install.passwd_filename.disabled=false
				}


		if (document.install.use_mysql.value=='true' && document.install.send_emails.value=='true' && document.install.users_register_themselves.value == 'true')
			document.install.confirm_new_account.disabled=false
		else
			document.install.confirm_new_account.disabled=true


			if (document.install.resolve.value=='true')
				document.install.resolve_timeout.disabled=false
			else
				document.install.resolve_timeout.disabled=true


			}
		}
</script>
<?php


/*************** VALIDATORS *********************/
function validate_mail($tconf)
{
	global $lang;
	static $is_valid = -1; //declared only once
	if (@($tconf['send_emails'] !== "true")) {
		return true;
	}
	if ($is_valid == 1)
		return true; //avoid unessesary execution for all options
	if ($is_valid == 0)
		return false;

	if (empty($tconf['web_master_EMAIL'])) {
		$is_valid = 0;
		echo "<p class='error'>".$lang['inst']['errmissfield']."</p>";
		return false;
	}
	if ($is_valid) return true; //avoid repating the proccess for every affected option
	$mailheaders = 'From: '.$tconf['web_master_EMAIL']."\r\n". 'X-Mailer : PHP';
	if (mail($tconf['web_master_EMAIL'] ,$lang['inst']['mailsubject'],$lang['inst']['mailbody']."\n\n<a href=http://wifiadmin.sf.net/>WifiAdmin</a>")) {
		$is_valid = 1;
		return true;
	}
	else {
		$is_valid = 0;
		echo "<p class='error'>".$lang['inst']['errvalmail']."</p>";
		return false;
	}
}


function validate_db($tconf)
{
	static $is_valid = -1;
	global $lang;

	if (@$tconf['use_mysql'] !== 'true') {
		return true;
	}
	if ($is_valid == 1)
		return true; //avoid unessesary execution for all options
	if ($is_valid == 0)
		return false;

	if (empty($tconf['USERS_DBHOST']) || empty ($tconf['USERS_DBUSER']) || empty ($tconf['USERS_DBPASS']) || empty($tconf['USERS_DB'])) {
		$is_valid = 0;
		echo "<p class='error'>".$lang['inst']['errmissfield']."</p>";
		return false;
	}

	if (@ ($link = mysqli_connect($tconf['USERS_DBHOST'],$tconf['USERS_DBUSER'],$tconf['USERS_DBPASS'])) ) {
		$sql = "show databases like '".$tconf['USERS_DB']."'";
		$result = mysqli_query($link, $sql);
		if (mysqli_num_rows($result) !== 0) {
			$is_valid = 1;
			return true;
		}
		else
			echo "<p class='error'>".$lang['inst']['errvaldb']."<br /> ".mysqli_error($link)."</p>";
	}
	else
		echo "<p class='error'>".$lang['inst']['errvaldbcreds']."<br /> ".@mysqli_error($link)."</p>";
	$is_valid = 0;
	$_SESSION['USERS_DBHOST'] = $tconf['USERS_DBHOST'];
	$_SESSION['USERS_DBUSER'] = $tconf['USERS_DBUSER'];
	$_SESSION['USERS_DBPASS'] = $tconf['USERS_DBPASS'];
	$_SESSION['USERS_DB'] = $tconf['USERS_DB'];
?>
<script type="text/javascript" src="include/popup.js"></script>
<a href="<?php echo $_SERVER['PHP_SELF']?>" onclick="wopen('<?php echo $_SERVER['PHP_SELF']?>?mode=db&modif=create','createdb',500,400);return false;">
<?php
	echo $lang['general']['clickhere'] ."</a>". $lang['inst']['createdb'] ;
	return false;
}

function validate_passwd($tconf)
{
	global $lang;
	if (@$tconf['use_mysql'] !== 'false') {
		return true;
	}

	if (! is_writable ($tconf['passwd_filename']) && !is_writable(dirname($tconf['passwd_filename'])) ) {
		echo "<p class='error'>".$lang['inst']['errvalpasswd']."</p>";
		return false;
	}
	return true;

}

function validate_rrdtool($tconf)
{
	global $lang;

	if (@$tconf['gen_graphs'] !== 'true') {
		return true;
	}
	exec ($tconf['rrdtool_bin'],$outar,$res);
	if ($res != 0) {
		echo "<p class='error'>".$lang['inst']['errrrdtool']."</p>";
		return false;
	}
	return true;
}

function validate_timezone($tconf)
{
	global $lang;

	$tz = @$tconf['timezone'];

	if (! date_default_timezone_set( $tz ) ) {
		echo "<p class='error'>".$lang['inst']['errtzone']."</p>";
		return false;
	}
	return true;
}

/* All options array. ***Order matters** for VALIDATION and presentation */
	/* o_name, o_defailt,o_validate_cback (opt),o_values (opt)	*/
$copts = array (
		new option('status_refresh',120),
		new option('show_console',true),
		new option('resolve',true),
		new option('resolve_timeout',1),
		new option('count_time',true),
		new option('timezone', 'Europe/Athens', 'validate_timezone'),

		new option('gen_graphs',false),
		new option('ses_regenerate_graphs',15*60),
		new option('graphs_path','graphs/'),
		new option('rrd_database_path','rrd_database/'),
		new option('rrdtool_bin','rrdtool','validate_rrdtool'),
		new option('rtgraph_width',300),
		new option('rtgraph_height',150),

		new option('use_mysql',true,'validate_db'),
		new option('USERS_DBHOST','localhost','validate_db'),
		new option('USERS_DB','wifiadmin','validate_db'),
		new option('USERS_DBUSER','dbuser','validate_db'),
		new option('USERS_DBPASS','dbpass','validate_db'),
		new option('passwd_filename','./config/passwd','validate_passwd'),
		new option('web_master','Administrator'),
		new option('send_emails',false,'validate_mail'),
		new option('web_master_EMAIL','root@localhost','validate_mail'),
		new option('users_register_themselves',false),
		new option('confirm_new_account',false),

);



?>

<?php
/*************** SUBMIT ********************************
Function List :
make config file
load config file
load default
set validate
save config file
*******************************************************/
function set_validate($post)
{
	global $lang;
	global $copts;
	$error = false;

	foreach ($copts as $option) {
		if (!isset($post[$option->name()]))  continue;
		$option->set_string($post[$option->name()]);
		if (!$option->validate($post))  {
			$error = true;
		}
		else {
			$option->set_string($post[$option->name()]);
		}
	}

	if ($error) {
		echo "<p class='error'>".$lang['inst']['errvalidate']."</p>";
		return false;
	}

	return true;

}

function make_config_file()
{
	// copts -->conf
	// Keep it silent
	global $lang;
	global $copts;
	global $I_VERSION;
	$cnffile="<?php\n";  //the var that will hold the config file contents and the app should try to write to
 	$cnffile.=$lang['inst']['confighdr_c']."\n" ;

	foreach ($copts as $option)
		$cnffile .=$option->get_config_entry();

	$cnffile .= "\n/*For update sync */\n\$C_VERSION = \"$I_VERSION\" ;";

	$cnffile.= "\n?>";

	return $cnffile;
}

function save_config_file()
{
	global $lang;
	$error = true;
	if (is_writable ("./config/") || is_writable ("./config/config.php")) {
		// OK We can wither write to the config dir or the config.php exists and is writable for us. Go on..
		$cnffile = make_config_file();
		$fp = @fopen ("./config/config.php","wb");
		if (@fwrite ($fp,$cnffile,strlen($cnffile)) !== false) {
			$error = false;
			echo "<p>". $lang['inst']['oksavefil'] . "</p>";
		}
	}
	if ($error) {
		echo "<p class='error'>". $lang['inst']['errsavefil'] . "</p>";
		return false;
	}
	return true;
}

function load_config($silent = false)
{
	global $copts;
	global $lang;

	// conf --> copts
	if (is_readable  ( "./config/config.php"))
	{
		include("./config/config.php"); //dont't include once ok to reinclude, we are inside the function !
	}
	else {
		if (! $silent ) echo "<p>". $lang['inst']['errnofil'] . "</p>";
		return false;
	}

	$error = false;
	foreach ($copts as $option) {
		$varname = "C_".$option->name();
		if (!isset($$varname)) {
			$option->invalidate();
			$error = true;
		}
		else
			$option->set($$varname);
	}
	if ($error) {
		if (!$silent) echo "<p class='error'>". $lang['inst']['errloadfil'] . "</p>";
		return false;
	}
	return true;
}

/* Page output start */
echo "<H2>" . $lang['inst']['mdconfig'] . "</H2>";

if (is_readable("./config/config.php")) {
	include_once ("./config/config.php");
	if (!isset($C_VERSION)) {
		$stall = false;
		//DO AN INSTALL WHEN OLD CONFIG FOUND (OR DAMAGED)
	}
	elseif (@ $C_VERSION != $I_VERSION) {
			$_SESSION['update'] = 1; //When Someone called install.php with no update parameter
			$stall = false;
	}
	elseif (@ $C_config_edit_once === true)
			$stall = false;
	else {
			echo "<P class='error'>".$lang['inst']['erredit_config']."</P>";
			$stall = true;
	}
}
else
	$stall = false;

$goon = false; // should we go on to the next step then ?
if (!$stall) {
if (isset($_POST['submit'])) {
	load_config(true); //silently load prexisting config file for missing POST entries
	//Config variables changed. Validate and generate new config file. POST-->tconf -->file
	if (set_validate($_POST)) {
		echo "<p>".$lang['inst']['okgenconf']."</p>";
 		if(!save_config_file()) {
			echo "<p>".$lang['inst']['errsavemsg']."</p>";
			$cnf_file = make_config_file();
?>
<center>
	<form name="results">
		<textarea name="fullconfig" rows="10" cols="80">
<?php echo $cnf_file ?></textarea>
<br \>
		<input type="button" value="<?php echo $lang['inst']['selectall']?>" onClick="javascript:document.results.fullconfig.focus();document.results.fullconfig.select();">
<?php
/*
Well if its only for m$ better dont do it. It should be written with browser selection infastracture..
<!--
the following code (copy textarea to clipboard) unfortunately works for ie only, out of the box. If anyone has a quick hack for tha please change it!
-->
		input type="button" value="<?php echo $lang['inst']['copy2clip']?>" onClick="window.clipboardData.setData('text',document.results.fullconfig.value);"
*/
?>
	</form>
	<p><a href="<?php echo $_SERVER['PHP_SELF'] ?>?mode=config"> Recheck </a></p>
</center>
<?php
		} // end if not save config
	$goon = true;
	// Show Next Step Form

	} //end if set_validate
}
elseif (isset($_POST['reset'])) {
	//Reload Default Values Asked   DEFAULT -->tconf
	//This is the initial state;
}
else {
	//If reload asked or nothing: Initialize to existing vars   CONFIG -->tconf
	load_config();
}

if (!$goon) {
?>


<table class="t1" align="center">
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>?mode=config" name="install" >
		<tr>
			<th><?php echo $lang['inst']['par_desc'] ?></th>
			<th><?php echo $lang['inst']['par_name'] ?></th>
		</tr>

<?php
	foreach ($copts as $option) {
		$option->print_html_entry();
	}
?>
		<tr>
			<td colspan="2">
				<center>
					<INPUT type="submit" name="submit" value="<?php echo $lang['dict']['submit'] ?>">
					<INPUT type="submit" name="reload" value="<?php echo $lang['inst']['ldcnf'] ?>">
					<INPUT type="submit" name="reset" value="<?php echo $lang['inst']['lddef'] ?>">
				</center>
			</td>
		</tr>

		</form>
		<script language="javascript">
			fields_disable_enable();
		</script>
</table>
<?php
} //end goon
} // end stall
else
	$goon = true;





