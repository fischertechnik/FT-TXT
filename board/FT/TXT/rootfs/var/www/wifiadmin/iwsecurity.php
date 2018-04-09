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
  | WifiAdmin: The Free WiFi Web Interface				                    |
  +-------------------------------------------------------------------------+
  | Send comments to  							                            |
  | - panousis@ceid.upatras.gr						                        |
  | - dimopule@ceid.upatras.gr						                        |
  +-------------------------------------------------------------------------+

~modded by korkakakis Nikos
~modded by basOS

*/


include("./include/header.php");


?>
<script src="include/submitonce.js"></script>
<h2><?php echo _("Wireless Security Settings") ?></h2>
<?php //check privileges
if (@$_SESSION["ban_users"]!="true"){
	echo "<p class = \"error\">"._("You have no permission to access this section of WiFiAdmin")."</p>";
	include("./include/footer.php");
	exit();
}



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
}

echo "\n<ul id=\"tabnav\">\n";					//create tabs
foreach ($iw_names as $device)
{
	if($curname == $device )
		echo "<li id=\"active-tab\"><a href=\"".$_SERVER['PHP_SELF']."?device=".$device."\">".$device."</a></li>\n";      //active-tab for the specific wifi interface
	else
		echo "<li class=\"tab\"><a href=\"".$_SERVER['PHP_SELF']."?device=".$device."\">".$device."</a></li>\n";      //non active tab

}
echo "</ul>";
$current = get_wireless_devstatus($curname);
//END TAB CREATION

//Proccesses Policy, Delete MAC checkboxes, addmac textbox
if(isset($_POST['device']))		//commit security changes for the device
{
	$wif = $_POST['device'];
	echo "<p>"._("Applying changes for device"). " $wif : </p>";
	echo "<table class='frame'>";

	if(isset($_POST['policy']) && $_POST['policy']!= "none")				//change acl policy
	{
		echo "<tr><td>"._("Access list policy . . .")."</td>";
		echo (AP_acl_apply_policy($wif, $_POST['policy']) ? "<td>"._('Ok')."</td></tr>" :"<td class='error'>"._('Error')."</td></tr>" );
			//echo "<p class=\"error\">Access List policy change failed</p>";
	}
	$MAC = array_search("on",$_POST);		//remove MACs from APs Access List
	echo "<tr>";
	if ($MAC) echo "<td>"._("Deleting MACs. . .")."</td>";
	while($MAC)				
	{
		echo (AP_acl_delmac($wif,$MAC) ? "<td>"._('Ok')."</td> ":"<td class='error'>"._('Error')." : ". $MAC."</td>");;
		unset($_POST[$MAC]);
		$MAC = array_search("on",$_POST);
	}
	echo "</tr>";
	if(isset($_POST['addmac']) && $_POST['addmac']!="")				//add more MACs to the ACL
	{
		echo "<tr><td>"._("Adding MACs . . .")."</td>";	
		$maclist = explode(";", $_POST['addmac']);
		foreach($maclist as $var)

echo (AP_acl_addmac($wif,trim($var)) ? "<td>"._('Ok')."</td>" : "<td class='error'>"._('Error')." : ".$var."</td>");

		echo "</tr>";
	}	
	echo"</table>";
}

//TODO: ADd overlib description
if( $current['mode'] == 'Master' )
{
	$security = get_policy($current['name']);
	echo "<form  method=\"POST\" onSubmit=\"submitonce(this)\" action ='".$_SERVER['PHP_SELF']."?device=".$curname."'>
               <input type=\"hidden\" name=\"device\" value=\"".$current['name']."\">";
	echo "<div><h3>"._("Security settings for device")." ".$current['name']."</h3></div>\n
		<table>
		<tr><td>"._("Current Access list policy")." : <i><u>".$security['policy']."</u></i></td></tr>
		<tr><td>New Policy: <select name = \"policy\">
				<option value='none'>"._("choose")."</option>
				<option>open</option>
				<option>allow</option>
				<option>deny</option>
			</select></td></tr>";
	if(isset($security[0]))
	{
		echo "<tr><td>
			"._("Current Access List")."
			<table class=\"sortable\">
			<tr><th>remove</th><th>MAC</th></tr>";
		for($i=0;$i<sizeof($security)-1;$i++)
		{
			echo "<tr><td align=\"center\"><input type=\"checkbox\" name=\"".$security[$i]."\"></td>
			<td>$security[$i]</td></tr>";
		}
		echo "</table><br></td></tr>";
	}
	echo "<tr><td>"._("Add new MACs to ACL here, use ")." <b>;</b> "._(" as a delimiter for multiple MACs")." <br>
		 <textarea rows=\"5\" cols=\"25\" name=\"addmac\"></textarea></td></tr>
		</table>
		<input type=\"submit\" value=\""._("Commit changes")."\">
		</form>";
}
else
echo "<p>"._("The are no security features for non-Master modes yet.")."</p>";


include("./include/footer.php");
?>
