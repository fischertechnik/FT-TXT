<?php

/**** Installation Script
	Modes/Steps :
	0.language selection
	1.configuration file creation
	2.Database Creation
	3.Routers Registration

						basOS / korki

****/
// We can not include header.php here. Some dups so...session_start();

if (!empty($_POST['lang_id'])) {
	$lang_id_new = $_POST['lang_id'];
}
session_start();
require "./include/lang_init.php";
require "./include/constants.php";

@$mode = $_GET['mode'];
if (empty($mode)) {
	$mode = "lang";
}
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
    <html>
	<head>
		<title>WiFiAdmin</title>
        <link rel = "stylesheet" type = "text/css" href = "./include/global.css">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<script src="include/submitonce.js"></script>
    </head>
    <body>

  	<a name="top"></a>
	<div id="wrapperout">
	<div id="header">
		<table class="brand"><tr><td>
		<h1><i>WiFiAdmin</i> </h1>
		</td><td>
		<?php echo _('Version')." ".$I_VERSION ?>
		</td></tr></table>
		<p class="moto"><i><a href="http://wifiadmin.sourceforge.net/" class="anchor">WiFiAdmin</a></i>, <?php echo _('the <a href="./copying.php" class="anchor">free</a> wifi Web Interface')?> </p>
	</div><!-- header -->
	<div id="content" align="center">
<?php
// Be very carefull when this file will be executed
// config and db are nessasary installation or update steps and will be executed
// by *anyone* but only for one time. Router selection could be made auth aware..
if (!empty($_GET['update']))
	$_SESSION['update'] = 1;
switch ($mode) {
case "config" :

	if (@$_SESSION['update'])
		echo "<H2>". _("Update") ." ". _("Step") ." 1/ 2</H2>";
	else
		echo "<H2>". _("Installation") ." ". _("Step") ." 1 / 3</H2>";

	unset ($_SESSION['cache']); //IMPORTANT clear system info cache
	// It is safe to execute install_config  always as it makes checks about config edit allowance
	require "./install/install_config.php";

	if (@$goon) {
?>
	<p>
	<form name="next" method="POST" action="<?php echo $_SERVER['PHP_SELF'] ?>?mode=db">
		<input type="submit" value="<?php echo _('Go on')?>">
	</form>
	<p />
<?php
	} //end if goon
break;
case "db" :
	if (@$_SESSION['update'])
		echo "<H2>". _("Update") ." ". _("Step") ." 2 / 2</H2>";
	elseif (@ $_GET['modif'] !== 'create')
		echo "<H2>". _("Installation") ." ". _("Step") ." 2 / 3</H2>";
	// It is safe to execute install_db always as it will not do anything when db is up to date
	require "./install/install_db.php";
	if (@$goon && @ !$_SESSION['update']) {
?>
	<p>
	<form name="next" method="POST" action="./manage_routers.php">
		<input type="submit" value="<?php echo _('Go on')?>">
	</form>
	<p />
<?php
	} //end if goon
	elseif (@$goon && @$_SESSION['update']) {
		echo "<p>"._("Succesfull!")."</p>";
?>
	<p>
	<form name="next" method="POST" action="./index.php">
		<input type="submit" value="<?php echo _('Ok')?>">
	</form>
	<p />
<?php
	}
break;
case "lang":
default:

	echo "<H2>". _("WifiAdmin installation wizard"). "</H2>";
	echo "<H2>". _("Installation") ." ". _("Step") ." 0 / 3</H2>";
	echo "<H2>". _("Language Selection") . "</H2>";
?>

	<P>
	 Please Select Your Language :
	<FORM name="lang" method="POST" action="<?php echo $_SERVER['PHP_SELF']?>?mode=config">
	<SELECT name="lang_id">
<?php
	foreach ($lang_avail as $lang_t) {
		echo "<OPTION ".($lang_id == $lang_t ? "selected":"").">$lang_t</OPTION>";
	}
?>
	</SELECT>
	<INPUT type="submit" name="subm" value="<?php echo _('Go on')?>">
	</FORM>
	</P>
<?php
} //end switch mode


?>
	</div><!-- content -->
	</div><!--wrapper out-->
	</body>
	</html>
