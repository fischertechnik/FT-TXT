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

/**********************************************
* Script produces a form for the
* user to apply new settings to a specific
* wireless device
***********************************************/

/*********************************************************
* get_iwconfig_params($post):
* Function that parses the output of
* the user's POST command. Makes the nessecary
* checks to make sure he doesn't rm -rf's the system
* and returns the rigth iwconfig command as a string
* DOESNOT execute the command, justs outputs the string.
**********************************************************/
//TODO: Move this shit to the abstraction API (when it will be created..)
function get_iwconfig_params($post)
{
	if(!ctype_alnum($post['device']))
		exit(_("Illegal characters found in device name string:")." ".$post['device'].", "._("Exiting")."...");

	echo "<h4>"._("Changing settings for device")." ".$post["device"]."</h4>\n<ul>";
	$cmd = array();
	if(isset($post['essid_c'])) {     //do we have an essid to change?
		$post['essid'] = $post['essid'];
		echo "<li>ESSID: " .$post['essid']. "</li>";
		$cmd["essid"] = $post['essid'];
	}
	if(isset($post['nick_c']) ) {     //do we have an essid to change?
		$post['nick'] = $post['nick'];
		$post['nick'] = $post['nick'] ? $post['nick']:"''";
		echo "<li>Nickname: " .$post['nick']. "</li>";
		$cmd["nick"]=$post['nick'];
  }
	if(isset($post['mode_c']) ) {
		$post['mode'] = $post['mode'];
		echo "<li>Mode: " .$post['mode']. "</li>";
		$cmd["mode"]=$post['mode'];
	}//else echo "<p class=error>Channel contains illegal characters, ingored</p>";
	if(isset($post['channel_c'])) {
	 	if (ctype_alnum($post['channel'])) {
			//$post['channel'] = ($post['channel']);
			echo "<li>Channel: " .$post['channel']. "</li>";
			$cmd ["channel"]=$post['channel'];
		} else echo "<li class=error>channel "._("contains illegal characters, ingored")."</li>";
	}
	if(isset($post['ap_c']) && $post['mode'] != "Ad-Hoc") {
		if(@(strlen($post['ap']) && ctype_alnum($post['ap'])) ) {
			$post['ap'] = ($post['ap']);
			echo "<li>AP: " .$post['ap']. "</li>";
			$cmd ["ap"]=$post['ap'];
		} else echo "<li class='error'>AP "._("contains illegal characters, ingored")."</li>";
	}
	if(isset($post['rate_c'])) {
		if (is_numeric($post['rate'])) {
			 $post['rate'] = ($post['rate'] . "M");
			 echo "<li>Bitrate: " .$post['rate']. "</li>";
			 $cmd["rate"]=$post['rate'];
		} else echo "<li class='error'>Bitrate "._("contains illegal characters, ingored")."</li>";
	}
	if(isset($post['sens_c'])) {
		$post['sens'] = ($post['sens']);
		echo "<li>Sensitivity: " .$post['sens']. "</li>";
		$cmd ["sens"]=$post['sens'];
	} //else echo "<li class=error>Sensitivity contains illegal characters, ingored</li>";
	if(isset($post['retry_c']) ) {
		if (ctype_alnum($post['retry']) ) {
			$post['retry'] = ($post['retry']);
			echo "<li>Retry Limit: " .$post['retry']. "</li>";
			$cmd["retry"]=$post['retry'];
		} else echo "<li class='error'>Retry Limit "._("contains illegal characters, ingored")."</li>";
	}
	if(isset($post['rts_c']) ) {
		if (ctype_alnum($post['rts'])) {
			$post['rts'] = ($post['rts']);
			echo "<li>RTS Threshold: " .$post['rts']. "</li>";
			$cmd["rts"]=$post['rts'];
		}else echo "<li class='error'>RTS Threshold "._("contains illegal characters, ingored")."</li>";
	}
	if(isset($post['frag_c']) ) {
		if (ctype_alnum($post['frag'])) {
			$post['frag'] = ($post['frag']);
			echo "<li>Fragmentation Threshold: ".$post['frag']. "</li>";
			$cmd["frag"]=$post['frag'];
		}else echo "<li class='error'>Fragmentation Threshold "._("contains illegal characters, ingored")."</li>";
	}
	if(isset($post['power_c']) ) {
		$post['power'] = ($post['power']);
		echo "<li>Power Management: " .$post['power']. "</li>";
		$cmd["power"]=$post['power'];
  }
	if (isset($post['txpower_c'])) {
		if (ctype_alnum($post['txpower'])) {
			 $post['txpower'] = ($post['txpower']. "dbm");
			 echo "<li>TX Power: ".$post['txpower']."</li>";
			 $cmd["txpower"]=$post['txpower'];
		} else echo "<li class='error'>TX Power "._("contains illegal characters, ingored")."</li>";
	}
	echo "</ul>";
	if(sizeof($cmd)>0)
		return $cmd;
	else
		return 0;
}

include("./include/header.php");

if (@$_SESSION["access_ifs"]!= "true" )
{
	echo "<p class = \"error\">"._("You have no permission to access this section of WiFiAdmin")." - ("._("Wireless Settings").")</p>";
	include("./include/footer.php");
	exit();
}


?>
<script src="include/submitonce.js"></script>
<script src="include/sorttable.js"></script>
<h2><?php echo _("Wireless Settings")?></h2>
<?php


//START TAB
if (isset($_SESSION['cache']['wifs'])) {
	//Lighten system commands
	$iw_names = $_SESSION['cache']['wifs'];
}
else {
	$iw_names = get_wifs();		//get_all wireless names
	$_SESSION['cache']['wifs'] = $iw_names;
}
if (count($iw_names) == 0) {
	echo "<p class ='error'>"._("No wireless Interfaces found")."</p>";
	include("./include/footer.php");
	exit();
}
if(!isset($_GET['device']) || array_search ($_GET['device'],$iw_names)===false ) {		//we need an interface to show
	$curname = $iw_names[0];
	if (isset($_SESSION['curdevname']) && array_search ($_SESSION['curdevname'],$iw_names)!== false) $curname = $_SESSION['curdevname'];
}
else{
	$curname = $_GET['device'];
	$_SESSION['curdevname'] = $curname;
} ?>

<ul id="tabnav">
<?php // CREATE TABS
foreach ($iw_names as $device)
{
	if($curname == $device )
		echo "<li id=\"active-tab\"><a href=\"".$_SERVER['PHP_SELF']."?device=".$device."\">".$device."</a></li>\n";      //active-tab for the specific wifi interface
	else
		echo "<li class=\"tab\"><a href=\"".$_SERVER['PHP_SELF']."?device=".$device."\">".$device."</a></li>\n";      //non active tab

} //END TAB CREATION ?>
</ul>


<?php //check whether the user has asked to change setting
if(isset($_POST['device']))
{
	$command = get_iwconfig_params($_POST);
	if($command){
		$res = update_iwdev_settings($_POST['device'], $command);
		if($res !== true )
			echo "</pre><p class='error prex'>$res"._("Some values may not have been applied.")."</p>";
	}
	else {
		echo "<p class='error'>"._("No changes made")."</p>";
	}
		//echo "<a href=\"".$_SERVER['PHP_SELF']."?device=".$curname."\">Back to settings</a>";
}
//print site survey
if(isset($_GET['survey']) )
{
?>
<form name="assoc" action="<?php echo $_SERVER['PHP_SELF'].'?device='.$curname ?>" method="POST">
	<input type="hidden" name="device" value="<?php echo $curname?>">
	<input type="hidden" name="essid" value="">
	<input type="hidden" name="essid_c" value="on">
	<input type="hidden" name="mode" value="Managed">
	<input type="hidden" name="mode_c" value="on">
</form>
<script type="text/javascript">
	function associate(essidv)
	{
		document.forms["assoc"].essid.value=essidv;
		document.forms["assoc"].submit();
		return false;
	}
</script>
<?php
	$scan_results = get_scan_results($curname);
	if ($scan_results === false)
		echo "<p class=\"error\"> "._("Device does not support scanning")." </p>";
	if (count($scan_results) == 0)
		echo "<h4>"._("No scan results")."</h4>";
	else{
		echo '<h4>'._("Scan results for device").' '.$curname.'</h4>
		<table class="sortable" id="sort" align="center" cellspacing="0">
		<tr> <th>#</th>';
		foreach(array_keys($scan_results[0]) as $header)
			echo "<th>".$header."</th>";
		echo "</tr>";
		foreach ($scan_results as $num => $scan_result){
			echo "<tr>";
			echo "<td>$num</td>";
			foreach ($scan_result as $index => $value) {
				// Make Clickable site survey
				if ($index == "essid") {
					echo '<td><a href="#" onclick=\'associate("'.$value.'");\'>'.$value.'</a></td>';
				}
				else
				echo "<td>".$value."</td>";
			}
			echo "</tr>";

		}
	echo "</table>";
	}
	/*?>
	<h4>List of users registered in our community</h4>
	<table class="t1" align="center" cellspacing="0">
	<tr bgcolor="#BEC8D1">
	<?php*/
}
else
{
	$data = get_wireless_devstatus($curname);
	echo "<h4>"._("Current settings for device")." ".$curname."</h4>";

?>
<script type="text/javascript">
function has_changed(textb) {
	if (textb) {
		var destc = textb.name;
		//DBG	//document.open();  //document.writeln("textb=" + destc);
		document.getElementsByName(destc+"_c")[0].checked=true;
	}
}
</script>
<table>
<tr><td>
<form name="settings" onSubmit="submitonce(this)" action="<?php echo $_SERVER['PHP_SELF']."?device=".$curname?>" method="POST">
<table>
	<tr><td></td><td><input type="hidden"  name="device" value="<?php echo $curname; ?>"></td><td></td></tr>
	<tr><td>ESSID: </td><td><input type="text" name="essid" onfocus="has_changed(this)" value='<?php echo $data['essid']?>'></td><td><input type="checkbox" name="essid_c"></td></tr>
	<tr><td>Nickname: </td><td><input type="text" name="nick" onfocus="has_changed(this)" value="<?php echo $data['nick']?>"></td><td><input type="checkbox" name="nick_c"></td></tr>

<?php
/*
mode strings (eg ad-hoc etc...) are not changed. I am thinking they are universal
korkakak
*/
?>

	<tr><td><?php echo _("Mode")?>: </td><td><select name="mode" onfocus="has_changed(this)"><option></option><option <?php if ($data['mode']=="Master") echo "selected";?>>Master</option><option <?php if ($data['mode']=="Managed") echo "selected";?>>Managed</option><option <?php if ($data['mode']=="Ad-hoc") echo "selected";?>>Ad-Hoc</option><option <?php if ($data['mode']=="Monitor") echo "selected";?>>Monitor</option></select></td><td><input type="checkbox" name="mode_c"></td></tr>
	<tr><td>Channel: </td><td><input type="text" name="channel" onfocus="has_changed(this)" value="<?php echo $data['channel']?>"></td><td><input type="checkbox" name="channel_c"></td></tr>
	<?php if( $data['mode'] != "Ad-Hoc"){
	echo "<tr><td>AP </td><td><input type=\"text\" name=\"ap\" onfocus=\"has_changed(this)\" value=\"".$data["ap"]."\"></td><td><input type=\"checkbox\" name=\"ap_c\"></td></tr>"; }?>
	<tr><td>Bitrate (Mbps): </td><td><input type="text" name="rate" onfocus="has_changed(this)" value="<?php echo $data['rate']?>"></td><td><input type="checkbox" name="rate_c"></td></tr>
	<tr><td>TX Power (dBm): </td><td><input type="text" name="txpower" onfocus="has_changed(this)" value="<?php echo $data['txpower']?>"></td><td><input type="checkbox" name="txpower_c"></td></tr>
	<tr><td>Sensitivity: </td><td><input type="text" name="sens" onfocus="has_changed(this)" value="<?php echo $data['sens']?>"></td><td><input type="checkbox" name="sens_c"></td></tr>
	<tr><td>Retry Limit: </td><td><input type="text" name="retry" onfocus="has_changed(this)" value="<?php echo $data['retry']?>"></td><td><input type="checkbox" name="retry_c"></td></tr>
	<tr><td>RTS Threshold: </td><td><input type="text" name="rts" onfocus="has_changed(this)" value="<?php echo $data['rts']?>"></td><td><input type="checkbox" name="rts_c"></td></tr>
	<tr><td>Fragmentation Threshold: </td><td><input type="text" name="frag" onfocus="has_changed(this)" value="<?php echo $data['frag']?>"></td><td><input type="checkbox" name="frag_c"></td></tr>
	<tr><td>Power Management: </td><td><input type="text" name="power" onfocus="has_changed(this)" value="<?php echo $data['power']?>"></td><td><input type="checkbox" name="power_c"></td></tr>
</table>
<input type="submit" value="<?php echo _("Commit Changes")?>">
</form>
</td>
<td valign = "top" align = "left">
<?php
if($data['mode'] == "Managed" || $data['mode'] == "Ad-Hoc" || $data['mode'] == "Monitor")
        echo "<a href=\"".$_SERVER['PHP_SELF']."?device=".$curname."&survey=1\">[ "._("Site Survey")." ]</a>";
?>
</td></tr>
</table>
<?php
}
include("./include/footer.php");
?>
