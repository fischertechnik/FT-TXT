<?php
if(strpos($_SERVER['PHP_SELF'],basename(__FILE__)))
	require "../include/trap.php";

/* Adds a new configured router to the router conf. var. or removes one */
/************
config/routers.ini file format
		[router_name]
			url = router.domain.com
			username = remoteuser
			access_mode = ssh
			system_flavor = linux
			description = optional descriptive text
**/


require_once "./include/funccommon.php";
require_once "./include/ini.class.php";

$routerfile = "./config/routers.ini";
$C_routers = array();
if (is_readable($routerfile)) {
	$C_routers = INI::read ($routerfile);
}


/*****
 * isup - Check if a given ip/fqdn is up
 ******/
function is_up($url)
{
	$isup = wexecp("ping -c 2","$url", $z, true /*force local execution*/);
	return $isup;
}

/**********
* is a host registered? Mind for network or other problems
* return false only when host is **REALY** not registered
* HARD CHECK ***** MUST BE CORRECT ****
*/
function ssh_is_registered($user,$url)
{
//TODO : smart command check
	global $lang;
	if (is_up($url) === false) {
		echo "<p class='error'>".$lang['inst']['rterrdown']."</p>";
		return false;
	}
	$result = wexecp ( "ssh",array("$user@$url","echo T"), $outar, false, "2>&1");
	if ($result)
		return true;
	else {
		$lerr = implode ("\n",$outar);
		echo "<p class='error'>ERROR SSH: $lerr</p>";
		return false;
	}
}
/*****
 * Is a host really unregistered ???
*/
function ssh_is_unregistered($user,$url)
{
    /* unregistered is when host is up and we cannot login.
         if host is down we cannot asume that we are unregistered.
         TODO: clarify this situation
         */
	global $lang;
	if (is_up($url) === false) {
		echo "<p class='error'>".$lang['inst']['rterrdown']." *</p>";
		return false;
	}
	return !ssh_is_registered($user,$url);
}

function local_is_registered()
{
	return true;
}

function local_is_unregistered()
{
	return true;
}

function echo_register()
{
	global $lang;
	echo "<p>".$lang['inst']['rtreginst']."</p>";
}

/***
   Scans the C_routers array for a host with $user@$url registration
	Registration "primary key" is $user@$url combination
***/
function is_in_routers ($routers,$url,$user = null,$access_mode='ssh')
{
	if (strtolower($access_mode) == 'local') {
		foreach ($routers as $name => $router) {
			if (strtolower($router['access_mode']) =='local')
				return $name;
		}
		return false;
	}
	else {
		foreach ($routers as $name => $router) {
			if (strcasecmp($router['url'],$url)==0 && ($user ===null  || ($user !== null && strcasecmp($router['username'],$user)==0) ) )
				return $name;
		}
		return false;
	}
}

/****
   Scans for comlete match at the routers array
****/
function is_same_router ($routers,$name,$url,$user,$sys,$desc = '',$access_mode='ssh')
{
	foreach ($routers as $rname => $router) {
		if ( $rname == $name && strcasecmp($router['url'],$url)==0 && strcasecmp($router['username'],$user)==0 &&
			strcasecmp($router['system_flavor'],$sys)==0 && strcasecmp($router['access_mode'],$access_mode)==0 &&
			$router['description'] == $desc )
				return true;
	}
	return false;
}

function write_ini ($C_routers)
{
	global $routerfile;
	global $lang;

	if (INI::write($routerfile, $C_routers) === false) {
			echo "<p>".$lang['inst']['rterrsave']."</p>";
			$cnf_file = INI::get_ini_string($C_routers);
?>
	<form name="results">
		<textarea name="fullconfig" rows="10" cols="80">
<?php		 echo $cnf_file ?>
		</textarea>
<br \>
		<input type="button" value="<?php echo $lang['inst']['selectall']?>" onClick="javascript:document.results.fullconfig.focus();document.results.fullconfig.select();">
	</form>
<?php
		return false;
	}
	else
		echo "<p>". $lang['inst']['rtsuccwrite'] ."</p>";
	return true;
}

/****
    Adds an entry to the configuration file
    Currently only ssh access mode supported
****/
function register_ini ($C_routers,$name,$url = '',$user = '',$sys,$desc = '',$access_mode='ssh')
{
	global $lang;
	if ($oldname = is_in_routers ($C_routers,$url,$user,$access_mode)) {
		unset ($C_routers[$oldname]);
	}

	if (!empty($C_routers[$name])) {
		echo $lang['inst']['rterrname'];
		return false;
	}

	$C_routers[$name] = array ('url' => $url, 'username' => $user, 'system_flavor' => $sys, 'access_mode' => $access_mode, 'description' => $desc);

	return write_ini ($C_routers);

}

/** Script output **/
echo "<H2>". $lang['inst']['mdrouter'] . "</H2>";

$trail = 'inform' ; //what to display next , inform,succ,recheck
if (!empty ($_GET['delname'])) {
	//delete action requested
	if (array_key_exists ($_GET['delname'],$C_routers)) {
		$access = $C_routers[$_GET['delname']]['access_mode'];
		@$url = $C_routers[$_GET['delname']]['url'];
		@$user = $C_routers[$_GET['delname']]['username'];
		$is_unregistered = strtolower($access) ."_is_unregistered";
		if ($is_unregistered($user,$url)) { // if host is really unregistered
			unset ($C_routers[$_GET['delname']]);
			echo "<p class='error'>".$lang['inst']['rtderegw'] ."</p>";
			write_ini ($C_routers);
			$trail = 'deleted';
		}
		else {
			echo "<p class='error'>".$lang['inst']['rtdereg'] ."</p>";
		}
	}
	else
		echo "<p class='error'>".$lang['inst']['rterrdname'] ."</p>";
}
elseif ( !empty($_POST['rtname']) && (
			empty($_POST['rtaccess']) || empty ($_POST['rtsys']) || (
				$_POST['rtaccess']!=='local' && (
					empty($_POST['rturl']) || empty($_POST['rtuser'])
				)
			)
		)) {
	echo "<p class='error'>".$lang['inst']['rterremptydata']."</p>";
}
elseif (!empty ($_POST['rtname'])) {
//post data proccessor
	$is_registered = $_POST['rtaccess'] . "_is_registered";
	if (array_key_exists ($_POST['rtname'],$C_routers)) {
		//router name already exists
		if (is_same_router($C_routers,$_POST['rtname'],$_POST['rturl'],$_POST['rtuser'],$_POST['rtsys'],$_POST['rtdesc'],$_POST['rtaccess']) ) {
			$trail = 'succ';
		}
		else
			echo "<p class='error'>".$lang['inst']['rterrname']."</p>";
	}
	elseif ($is_registered(@$_POST['rtuser'],@$_POST['rturl'])) {
		if (register_ini ($C_routers,$_POST['rtname'],@$_POST['rturl'],@$_POST['rtuser'],$_POST['rtsys'],@$_POST['rtdesc'],$_POST['rtaccess']))
			$trail = 'succ';
		else
			$trail = 'recheck';
	}
	else {
		//it is not registered. print instractions
		echo_register();
		$trail = 'recheck';
	}
}

if ($trail == 'succ') {
	echo $lang['dict']['succ'];
	echo "<p><a href='".$_SERVER['PHP_SELF']."'>". $lang['inst']['rtanother'] ."</a></p>";
	echo "<p><a href='".dirname($_SERVER['PHP_SELF'])."/'>" . $lang['dict']['goon'] ."</a></p>";
}
elseif ($trail == 'deleted') {
	echo "<p><a href='".$_SERVER['PHP_SELF']."'>". $lang['dict']['back'] ."</a></p>";
	echo "<p><a href='".(dirname($_SERVER['PHP_SELF']))."/'>" . $lang['dict']['goon'] ."</a></p>";
}
elseif ($trail == 'recheck') {
?>

<form name="ssh" method="post" action="<?php echo $_SERVER['PHP_SELF']?>" >

<input type="hidden" name="rtdesc" value="<?php echo $_POST['rtdesc']?>">

<table>
	<tr><td><?php echo $lang['inst']['rtname']?> [*]</td><td><input type="text" name="rtname" value="<?php echo $_POST['rtname']?>" readonly> </td></tr>
	<tr><td><?php echo $lang['inst']['rturl']?> [*]</td><td><input type="text" name="rturl" value="<?php echo @$_POST['rturl']?>" readonly> </td></tr>
	<tr><td><?php echo $lang['inst']['rtuser']?> [*]</td><td><input type="text" name="rtuser" value="<?php echo @$_POST['rtuser']?>" readonly> </td></tr>
	<tr><td><?php echo $lang['inst']['rtsys']?> [*]</td><td><input type="text" name="rtsys" value="<?php echo $_POST['rtsys']?>" readonly> </td></tr>
	<tr><td><?php echo $lang['inst']['rtaccess']?> [*]</td><td><input type="text" name="rtaccess" value="<?php echo $_POST['rtaccess']?>" readonly> </td></tr>
</table>

<input type="submit" value="<?php echo $lang['dict']['recheck']?>" >
</form>
<?php
} //end recheck
else { //inform

	if (@count($C_routers) >0 ){
		echo "<p align='center'>".$lang['inst']['rtregistered']."</p>";
		echo "<table class='t1' align='center'>
			 <th>" .$lang['inst']['rtname'] ."</th><th>" .$lang['inst']['rturl'] ."</th><th>". $lang['inst']['rtuser'] . "</th><th>" . $lang['inst']['rtsys'] ."</th><th>" . $lang['inst']['rtaccess'] ."</th>";
		foreach ($C_routers as $name => $router) {
			echo "<tr><td>". $name ."</td><td>". $router['url'] ."</td><td>".$router['username'] ."</td><td>".$router['system_flavor'] ."</td><td>".$router['access_mode']."</td><td><a href='".$_SERVER['PHP_SELF']."?mode=router&delname=$name'>".$lang['dict']['delete'] ."</a></td></tr>";
		}
		echo "</table><br/>";
	}


	$sysfs = get_sysflavors();
?>

<script type="text/javascript">
function checkaccess()
{
	if (document.ssh.rtaccess.value == 'local')	{
		document.ssh.rtuser.disabled = true;
		document.ssh.rturl.disabled = true;
	}
	else {
		document.ssh.rtuser.disabled = false;
		document.ssh.rturl.disabled = false;
	}
}

</script>
<form name="ssh" method="post" action="<?php echo $_SERVER['PHP_SELF']?>" >
<p align='center'><?php echo $lang['inst']['rtadd']?></p>
<table align='center'>
	<tr><td><?php echo $lang['inst']['rtname']?> [*]</td><td><input type="text" name="rtname"> </td></tr>
	<tr><td><?php echo $lang['inst']['rtaccess']?> [*]</td><td><select name="rtaccess" onClick="checkaccess()">
		<option>local</option>
		<option selected>ssh</option>
	</select></td></tr>
	<tr><td><?php echo $lang['inst']['rturl']?> [*]</td><td><input type="text" name="rturl"> </td></tr>
	<tr><td><?php echo $lang['inst']['rtuser']?> [*]</td><td><input type="text" name="rtuser"> </td></tr>
	<tr><td><?php echo $lang['inst']['rtsys']?> [*]</td><td><select name="rtsys">
<?php
 	foreach ($sysfs as $sysf)
		echo	"<option> $sysf </option>\n";
?>
	</select></td></tr>
	<tr><td><?php echo $lang['inst']['rtdesc']?></td><td><input type="text" name="rtdesc"> </td></tr>
</table>
<p class='error'> <?php echo $lang['general']['mandatory']?> </p>
<p align='center'><input type="submit" value="<?php echo $lang['dict']['submit']?>" ></p>
</form>
<script type="text/javascript"> checkaccess(); </script>
<?php
} //end default else


/***
	Register an ssh remote host with $url with $user $pass creds and $sysf flavor
***/
/*function register($name,$url,$user,$pass,$sysf)
{
	global $C_routers;
	global $lang;
	if (is_registered($user,$url)) {
		register_shh($url,$user,$pass);

		if (! is_in_routers($url) ) {
			$C_routers[$name] = array ($sysf, 'ssh' , $user, $url, $desc);
		}

	}
}*/

?>






