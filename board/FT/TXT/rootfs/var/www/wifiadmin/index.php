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


require("./include/header.php");	//echo the default Header entries
error_reporting(E_ALL); // Betray all worms...

/* Just cache some data to avoid unecessary system command execution */
if (!isset($_SESSION['cache']['wifs']))
	$_SESSION['cache']['wifs']=get_wifs();
if (!isset($_SESSION['cache']['system_nfo']))
	$_SESSION['cache']['system_nfo']=system_nfo();
if (!isset($_SESSION['cache']['hostname']))
	$_SESSION['cache']['hostname']=chostname();


/*	Korki Edit
	//fix "s" in wireless NIC(s) :) a size of 1 means one NIC
	$s = "s";
	if(sizeof($_SESSION['cache']['wifs']) == 1)  $s = "";
Who cares about s we have bigger problems...
*/
?>

<?php require "./include/auth_prompt.php"; ?>

<table>
	<tr>
		<th >
		<?php echo _('Node name'); ?>
		</th>
		<td>
		<?php echo $router_name ?>
		</td>
	</tr>
	<tr>
		<th>
		<?php echo _('Node hostname'); ?>
		</th>
		<td>
		<?php echo $_SESSION['cache']['hostname']; ?>
		</td>
	</tr>
	<tr>
		<th>
		<?php echo _('Node Decription') ?>
		</th>
		<td>
		<?php echo $DESCRIPTION; ?>
		</td>
	</tr>
	<tr>
		<th>
		<?php echo _('Running on') ?>
		</th>
		<td>
		<?php echo $_SESSION['cache']['system_nfo']; ?>
		</td>
	</tr>
	<tr>
		<th>
		<?php echo _('# wireless NICS')?>
		</th>
		<td>
		<?php echo sizeof($_SESSION['cache']['wifs']); ?>
		</td>
	</tr>
	<tr>
		<th>
		<?php echo _('Uptime'); ?>
		</th>
		<td>
		<?php echo uptime(); ?>
		</td>
	</tr>
</table>

<br />
<br />
<?php //DBG
//echo "DBG wifs=";
// print_r($wifs);

// Binary checks (existance, sudo, ...)

if (!empty($_GET['bincheck'])) {
	echo "<div align='center'> <h3>"._('System bindings Status. Note that you will need to install missing items for wifiadmin to be functional'). " </h3> ";
	echo_bin_check();
	echo "</div>";

}
else {
	echo "<p><a href='".$_SERVER['PHP_SELF']."?bincheck=1'>"._('Check System Bindings') ."</a></p>";
}

echo "<noscript><p align='right'>"._('Note: Javascript has to be enabled for wifiadmin to be fully functional') ."</p></noscript>";


require("./include/footer.php");
?>
