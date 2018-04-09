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
/*
edit by korki;
deny the header.php access directly
*/
if(strpos($_SERVER['PHP_SELF'],basename(__FILE__)))
	require "trap.php";

require_once ("constants.php");


//If we don't have config or config version does not match program version initiate installer
if (!is_readable("./config/config.php")) {
	header("Location: install.php?mode=lang");
}
require_once ( "./config/config.php");
if (@ $C_VERSION !== $I_VERSION) {
	//session_start();
	header("Location: install.php?mode=config&update=1");
}

require_once ( "./include/auth.php");

if(isset($_POST['lang_id'])) {
	// change language request
	$lang_id_new = $_POST['lang_id'];
}

if(isset($_POST['router_name'])) {
	// change router requested
	$router_name_new = $_POST['router_name'];
	unset ($_SESSION['cache']); //IMPORTANT clear system info cache
}

require ("./include/lang_init.php"); // Don't use include_once. We Want $lang to be reinitialized AFTER include/auth.php
require ("./include/router_init.php"); //DONT use include once.
if (isset($router_name)) {
	require ("./include/functions.php"); // Don't use include_once. We Want SYSFLAVOR includes and $SSH to be reinitialized AFTER include/auth.php and include/router_init.php. functions.php IS reinclude safe
}
elseif (basename($_SERVER['PHP_SELF']) != "manage_routers.php") { //NO ROUTERS CONFIGURED
	header("Location: manage_routers.php");
	die(); //just in case
}

if(isset($C_count_time)) {
		if ($C_count_time)
			$time_start = getmicrotime();
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
	<head>
		<title>WiFiAdmin</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTf-8">
        <link rel = "stylesheet" type = "text/css" href = "./include/global.css">
    </head>
    <body>

  	<a name="top"></a>
	<div id="wrapperout">
	<div id="wrapperin">
	<div id="header">
	<div id="login">
<?php

if($_SESSION['username'] == 'guest'){
	echo $lang['header']['loggedin']." <b>".$_SESSION['username'].'</b>, '.$lang['header']['login']."<a href=\"./index.php\">".$lang['dict']['here']."</a>" ;
	if (@($C_users_register_themselves) && @($C_use_mysql))
		echo $lang['header']['register'] ."<a href=\"./register.php\">".$lang['dict']['here']."</a>";
}
else
{
        echo "Welcome <b>".$_SESSION['username']."</b>,
        <form name = \"logout\" method = \"POST\" action = \"".$_SERVER['PHP_SELF']."\">
        <input type = \"hidden\" name = \"logout\" value = \"1\">
        <a href=\"javascript:document.logout.submit();\">[logout]</a>
	</form>";
	/* When not in mysql we need users.php but it takes numerical UIDS. We cannot have this moment... */
	if (@ $C_use_mysql === true )
		echo "<a href=users_edit.php?id=".$_SESSION["username"]."&action=edit>".$lang['header']['edprofile']."</a>" ;
}
	echo '</div><!--login-->';
?>
	<script type="text/javascript">
		function cng_lang(nl) {
			document.lang_idf.lang_id.value = nl;
			document.lang_idf.submit();
			return false;
		}
		function show_lang_nav() {
			document.getElementById("lang_list").style.visibility = "visible";
		}
		function hide_lang_nav() {
			document.getElementById("lang_list").style.visibility = "collapse";
		}

		function cng_router(nl) {
			document.routerf.router_name.value = nl;
			document.routerf.submit();
			return false;
		}
		function show_router_nav() {
			document.getElementById("router_list").style.visibility = "visible";
		}
		function hide_router_nav() {
			document.getElementById("router_list").style.visibility = "collapse";
		}
	 </script>
	<form name = "lang_idf" method = "POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
    <input type = "hidden" name = "lang_id" value = "">
	</form>
	<div id="lang" onmouseover="show_lang_nav()" onmouseout="hide_lang_nav()"> Locale: <b><?php echo $lang_id?></b>
	<div id="lang_list" STYLE="visibility:collapse"><table class="list">
<?php
	foreach ($lang_avail as $tl)
		echo '<tr><td><a href="javascript:cng_lang(\''.$tl.'\')">'. $tl .' </a></td></tr>'."\n";
	echo '</table></div>  </div><!-- lang -->';

	if (isset($router_name)) {
?>
	<form name = "routerf" method = "POST" action = "<?php echo $_SERVER['PHP_SELF'] ?>">
	<input type = "hidden" name = "router_name" value = "">
	</form>
	<div id="router" onmouseover="show_router_nav()" onmouseout="hide_router_nav()"> Router: <b><?php echo $router_name?></b>
	<div id="router_list" STYLE="visibility:collapse"><table class="list">
<?php
	foreach ($C_routers as $rt_n => $rt)
		echo '<tr><td><a href="javascript:cng_router(\''.$rt_n.'\')">'. $rt_n .' </a></td></tr>'."\n";
	echo '</table></div>  </div><!-- router -->';
	} //end isset router name ?>
	<div id="brand">
	<h1>WiFiAdmin</h1>
	<p class="vers"><?php echo $lang['dict']['version']." ".$I_VERSION ?></p>
	<p class="moto"><i><a href="http://wifiadmin.sourceforge.net/" class="anchor" target="_blank">WiFiAdmin</a></i>,
	<?php echo $lang['general']['moto']?>
	</p>
	</div><!-- brand -->
	</div> <!--header-->

	<div id="content">
  <?php
		$curfile=basename($_SERVER['PHP_SELF']);
	?>
	<div id="menuleft">

	<div id="menu">
	<ul class="menu">
	<?php echo '<li><a href="./index.php" class="menulink" >Main Page</a></li>
	<li><a href="./iwstatus.php" class="'.($curfile=="iwstatus.php"?"menulinksel":"menulink").'">'.$lang['header']['wstatus'].'</a></li>';
	if(@($_SESSION["access_ifs"] == "true")){
		echo '
			<li><a href="./iwsettings.php" class="'.($curfile=="iwsettings.php"?"menulinksel":"menulink").'">'.$lang['header']['wsettings'].'</a></li>
			<li><a href="./ifsettings.php" class="'.($curfile=="ifsettings.php"?"menulinksel":"menulink").'">'.$lang['header']['esettings'].'</a></li>';
	}
	if(@($_SESSION["ban_users"] == "true")){
		echo '<li><a href="./iwsecurity.php" class="'.($curfile=="iwsecurity.php"?"menulinksel":"menulink").'">'.$lang['header']['wsecurity'].'</a></li>';
	}
	if (@$C_use_mysql){
		echo '<li><a href="./users_edit.php" class="'.($curfile=="users_edit.php"?"menulinksel":"menulink").'">'.$lang['header']['wcommunity'].'</a></li>';
	}
	else{
		if(@($_SESSION["edit_users"]=="true"))
			echo '<li><a href="./users.php" class="'.($curfile=="users.php"?"menulinksel":"menulink").'">'.$lang['header']['edusers'].'</a></li>';
	}
	if (@($_SESSION['manage_routers'] == 'true')) {
		echo '<li><a href="./manage_routers.php?mode=add" class="'.($curfile=="manage_routers.php"?"menulinksel":"menulink").'">'.$lang['header']['managerout'].'</a></li>';
	}
	echo '
		</ul>
		</div><!-- menu -->'; ?>
		</div><!-- menuleft -->
		<div id="menuright">
		<!-- main window -->
