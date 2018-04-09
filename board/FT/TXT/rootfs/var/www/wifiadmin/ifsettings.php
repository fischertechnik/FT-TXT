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

/**********************************************
* User can choose which device to
* configure. Points to ifsettings.php
***********************************************/

include("./include/header.php");	//echo the default Header entries

//returns a 32char string
function dectobin($dectobin) {
	$cadtemp="";
	$dectobin = decbin($dectobin);
	$numins = 8 - strlen($dectobin);

	for ($i = 0; $i < $numins; $i++) {
		$cadtemp = $cadtemp."0";
	}
return $cadtemp.$dectobin;
}

function broadcast_address($decimal_netmask_octet,$decimal_ip_octet) {
	$tempchar="";
	$bin_bcast="";
	$cadres="";
	$bin_netmask=dectobin($decimal_netmask_octet);
	$bin_ip=dectobin($decimal_ip_octet);

	for ($i=0;$i<8;$i++) {
		$tempchar = substr($bin_netmask,$i,1);
		if ($tempchar=="1") {
			$cadres=$cadres.substr($bin_ip,$i,1);
		} else {
			$cadres=$cadres."1";
		}
	}
	return bindec($cadres);
}

function ipmask($in_ip,$in_mask){
	/* Check netmask */
	if (4 != sscanf($in_mask,"%d.%d.%d.%d", $mask_octets[0], $mask_octets[1], $mask_octets[2], $mask_octets[3])) {
		echo "<p class = \"error\">" ._('Invalid netmask')." \"$in_mask\".</p>";
        	return false;
	}
	foreach ($mask_octets as $mask_octet) {
		if ($mask_octet < 0 || $mask_octet > 255) {
			echo "<p class = \"error\">"._("Invalid octet")." $mask_octet @ \"$in_mask\".</p>";
        		return false;
		}
	}

	/* Check IP address */
	if (4 != sscanf($in_ip,"%d.%d.%d.%d", $ip_octets[0],$ip_octets[1],$ip_octets[2],$ip_octets[3])) {
		echo "<p class = \"error\">"._("Invalid ip address")." ".$in_ip.".</p>";
        	return false;
	}
	foreach ($ip_octets as $ip_octet) {
		if ($ip_octet < 0 || $ip_octet > 255) {
			echo "<p class = \"error\">"._("Invalid octet")." $ip_octet in \"$in_ip\".</p>";
        		return false;
		}
	}

	for($n_octet = 0; $n_octet < 4; $n_octet++)
	{
		$bcast_octets[$n_octet] = broadcast_address($mask_octets[$n_octet], $ip_octets[$n_octet]);
		$netaddr_octets[$n_octet] = $ip_octets[$n_octet] & $mask_octets[$n_octet];
	}


	$string_netaddr = sprintf ("%d.%d.%d.%d",$netaddr_octets[0], $netaddr_octets[1], $netaddr_octets[2], $netaddr_octets[3]);
	$string_bcast = sprintf ("%d.%d.%d.%d",$bcast_octets[0], $bcast_octets[1], $bcast_octets[2], $bcast_octets[3]);

	$array["bcast"]= $string_bcast;
	$array["netaddr"]= $string_netaddr;

	return $array;
}
?>
<script src="include/submitonce.js"></script>
<h2><?php echo _("Ethernet Settings")?></h2>

<?php
//check privileges
//extra check added
if (!isset($_SESSION["access_ifs"]) || $_SESSION["access_ifs"]!="true"){
	echo "<p class = \"error\">"._("You have no permission to access this section of WiFiAdmin")."</p>";
	include("./include/footer.php");
	exit();
}

//set default action
if(!isset($_GET['action']))
	$_GET['action'] = "show_device_status";


//START TAB
if (isset($_SESSION['cache']['ifs'])) {
	//Lighten system commands
	$i_names = $_SESSION['cache']['ifs'];
}
else {
	$i_names = get_ifs();		//get_all names
	$_SESSION['cache']['ifs'] = $i_names;
}
if (count($i_names) == 0) {
	echo "<p class ='error'>"._("No network interfaces found")."</p>";
	include("./include/footer.php");
	exit();
}
if(!isset($_GET['device']) || array_search ($_GET['device'],$i_names)=== false ) {		//we need an interface to show
	$curdevname = $i_names[0];
	if (isset($_SESSION['curdevname']) && array_search ($_SESSION['curdevname'],$i_names)!== false) $curdevname = $_SESSION['curdevname'];
}
else{
	$curdevname = $_GET['device'];
	$_SESSION['curdevname'] = $curdevname;
}

echo "\n<ul id=\"tabnav\">\n";					//create tabs
foreach ($i_names as $device)
{
	if($curdevname == $device && ($_GET['action'] == "show_device_status" || $_GET['action'] == "save_device_changes"))
		echo "<li id=\"active-tab\"><a href=\"".$_SERVER['PHP_SELF']."?device=".$device."\">".$device."</a></li>\n";      //active-tab for the specific wifi interface
	else
		echo "<li class=\"tab\"><a href=\"".$_SERVER['PHP_SELF']."?device=".$device."\">".$device."</a></li>\n";      //non active tab
}
if ($_GET['action'] == "show_routing_table")
	echo "<li id=\"active-tab\"><a href=\"".$_SERVER['PHP_SELF']."?action=show_routing_table\">Router</a></li>\n";
else
	echo "<li class=\"tab\"><a href=\"".$_SERVER['PHP_SELF']."?action=show_routing_table\">Router</a></li>\n";
echo "</ul>";
$curdevstatus = get_ethernet_devstatus($curdevname);
//END TAB CREATION	if (isset($_SESSION['curdevname'])) $curname = $_SESSION['curdevname'];


switch ($_GET["action"]){
	case "edit_new_route":
?>
		<form onSubmit="submitonce(this)" action="<?php echo $_SERVER['PHP_SELF']?>" method = "GET">
		<table>
		<tr><td>Target *</td><td><input type="text" name="target"></td></tr>
		<tr><td>Netmask *</td><td><input type="text" name="netmask" value="255.255.255.0"></td></tr>
		<tr><td>Gateway</td><td><input type="text" name="gateway"></td></tr>
		<tr><td>Device</td><td colspan="7"  class="noborder">
        		<select name = "device">
			<option>Determined by Gateway</option>
		<?php foreach($i_names as $device){
				echo "<option>".$device."</option>";
			}?>
		        </select>
		</td></tr>
		</table>
		<input type ="hidden" name="action" value = "save_new_route">
		<input type="submit" value="Commit Changes">
		</form><?php 		break;
	case "save_new_route":
		echo "<h5>Saving new route</h5>";

		$route_res = save_new_route($_GET['target'],$_GET['netmask'],$_GET['gateway'],
						($_GET['device']=="Determined by Gateway" ? "" : $_GET['device']));

		echo ($route_res === true ? "" :"<p class='error'>FAILED : $route_res");
		echo "<a href=\"".$_SERVER['PHP_SELF']."?action=show_routing_table\">view new routing table</a>";
		break;
	case "delete_route":
		echo "<h5>deleting route</h5>";
		$routes = get_routing_table();

		$route = $routes[ $_GET['route_id']];
		$route_res = delete_route($route['destination'],$route['netmask'],$route['iface']);

		echo ($route_res === true ? "" :"<p class='error'>FAILED: $route_res");
		//don't delte another route in case of refresh
		$_GET["action"] = "show_routing_table";
		//don't break, go on and show the new routing table
	case "show_routing_table":
		$routes = get_routing_table();
		echo "<h4>Current routing table</h4>";?>
		<form method = "GET" onSubmit="submitonce(this)" action = "<?php echo $_SERVER['PHP_SELF'];?>">
		<table class="t1" align="center" cellspacing="0">
		<tr>
			<th>Destination</th>
			<th>Netmask</th>
			<th>Gateway</th>
			<th>Interface</th>
		</tr>
		<?php 		foreach($routes as $route_index => $route)
		{
			echo "<tr>";
			echo "<td>". $route["destination"]."</td>";
			echo "<td>". $route["netmask"]."</td>";
			echo "<td>". $route["gateway"]."</td>";
			echo "<td>". $route["iface"]."</td>";
			echo "<td><a href=\"".$_SERVER['PHP_SELF']."?action=delete_route&route_id=".$route_index."\">delete</a></td>";
			echo "</tr>";
		}
		echo "</table><div align=\"center\"><a href=\"".$_SERVER['PHP_SELF']."?action=edit_new_route\">add a new route</a></div></form>";
		break;
    case "ask_for_ip":
        echo "<h5>Sending DHCP request for $curdevname </h4>" ;
        $dhcp_res = ask_dhcp_ip( $curdevname ) ;
        echo ($dhcp_res === true ? "<p>OK</p>" :"<p class='error'>FAILED: $dhcp_res</p>");
		//$_GET["action"] = "show_device_status";
        echo "<br />";
		echo "<a href=\"".$_SERVER['PHP_SELF']."?action=show_device_status\">view device status</a>";
    break;
	case "save_device_changes":
		if($_GET["mtu"] != $curdevstatus["mtu"]){
			$new_mtu = $_GET["mtu"];
			echo "<p>Changing mtu to " .$new_mtu. "<p>";

			$ifpar = array('mtu' => $new_mtu);
			$res = update_ifdev_settings($curdevname,$ifpar);
			echo ($res === true ? "" :"<p class='error'>FAILED: $res </p> ");
		}
		else
			echo "<p>Leaving mtu unchanged <p>";

		if ($_GET["ipaddr"]!=$curdevstatus["ipaddr"] || $_GET["mask"] != $curdevstatus["mask"]){
			//check if supplied ip and mask are valid
			$array = ipmask($_GET["ipaddr"],$_GET["mask"]);
			if ($array == false)
				break;
			$bcast = $array["bcast"];
			$ipaddr = $_GET["ipaddr"];
			$mask = $_GET["mask"];
			echo "<p>Changing $curdevname to ip: ". $ipaddr." netmask: ".$mask. "<p>";

			$ifpar = array('ipaddr' => $ipaddr, 'bcast' => $bcast, 'mask' => $mask);
			$res = update_ifdev_settings($curdevname,$ifpar);
			echo ($res === true ? "" :"<p class='error'>FAILED: $res </p> ");
		}
		else
			echo "<p>Leaving ip and netmask unchanged <p>";
		//get possibly updated ethernet_status to show
		$curdevstatus = get_ethernet_devstatus($curdevname);
		//don't break here, go on and show the new status
	case "show_device_status":
		// Print Device information

		//ethernet ifs might have no ip assigned yet
		if ($curdevstatus["ipaddr"] != ""){
			$array= ipmask($curdevstatus["ipaddr"], $curdevstatus["mask"]);
			if ($array == false)
				exit();
			$curdevstatus["bcast"] = $array["bcast"];
			$curdevstatus["netaddr"] = $array["netaddr"];
		}
		else
		{
			$curdevstatus["bcast"] = "255.255.255.0";
			$curdevstatus["netaddr"] = "";
		}
		echo "<h4>Current setting for device $curdevname</h4>";
/***********************************************************************************************
**  Interface UP/DOWN CONTROL/VIEW
***********************************************************************************************/
?>
	<table class="invisible" align="center">
<?php 	if ($_SESSION["access_ifs"] == "true" && isset($_POST['ifaction'])) {
		if ($_POST['ifaction'] == "down")
			$upc = false;
		else
			$upc = true;
		if (control_device ($curdevname, $upc) === false)
			echo "<p class=\"error\">Error Controling interface</p>";
	}
?>
	<form method="POST" onSubmit="submitonce(this)" action="<?php echo $_SERVER['PHP_SELF'];?>?device=<?php echo $curdevname;?>">
<?php
	echo "<tr><td><h3>"._("Device Status").": </h3>" ;
	if (is_device_up ($curdevname)) {
?>
		<td><h4 class='lblb'><?php echo _("UP")?></h4><td>
		<input type=submit value="Bring Down" <?php echo ($_SESSION["access_ifs"] != "true" ? "style=\"visibility:hidden\"" : "class='btns'") ?>>
		<input type=hidden value="down" name="ifaction">
<?php
	}
	else
	{
?>
		<td><h4 class='lbl'><?php echo _("DOWN")?></h4><td>
		<input type=submit value="<?php echo _('Bring Up')?>" <?php echo ($_SESSION["access_ifs"] != "true" ? "style=\"visibility:hidden\"" : "class='btns'") ?>>
		<input type=hidden value="up" name="ifaction">
<?php
	}
	echo "</form> </table>";
/************************************************************************************************/


		?>
		<form name="settings" onSubmit="submitonce(this)" action="<?php echo $_SERVER['PHP_SELF']?>" method = "GET">
		<table>
		<tr><td></td><td><input  type="hidden"  name="device" value="<?php echo $curdevname; ?>"></td></tr>
		<tr><td>IP address</td><td><input type="text" name="ipaddr" value= "<?php echo $curdevstatus["ipaddr"]?>"></td></tr>
		<tr><td>Netmask</td><td><input type="text" name="mask" value ="<?php echo $curdevstatus["mask"]?>"></td></tr>
		<tr><td>Broadcast</td><td><input type="text" name="bcast" value = "<?php echo $curdevstatus["bcast"]?>"disabled></td></tr>
		<tr><td>Network address</td><td><input type="text" name="netaddr" value = "<?php echo $curdevstatus["netaddr"]?>"disabled></td></tr>
		<tr><td>MTU</td><td><input type="text" name="mtu" value = "<?php echo $curdevstatus["mtu"]?>"></td></tr>
		</table>
		<input type ="hidden" name="action" value = "save_device_changes">
		<input type="submit" value="Commit Changes">
		</form>
              <p align="center"><a href="<?php echo $_SERVER['PHP_SELF']?>?action=ask_for_ip">Send a DHCP request</a></p>
<?php
/************************************************
  Realtime Graphs
*************************************************/
if (@$_SESSION['view_status_ext']=='true') {
?>
<script type="text/javascript" src="include/popup.js"></script>
<p align="center"><a href="rt_graph.php" onclick="wopen('rt_graph.php?device=<?php echo $curdevname?>&mode=t','graph_<?php echo $curdevname?>',<?php echo ($C_rtgraph_width+100).",".($C_rtgraph_height+100)?>);return false;" > <?php echo "[ "._("Show Realtime Traffic Graph"). " ]"?></a>

<?php
}
}


include("./include/footer.php");
?>
