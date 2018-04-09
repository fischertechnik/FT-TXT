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
  | Send comments to  						            |
  | - panousis@ceid.upatras.gr						    |
  | - dimopule@ceid.upatras.gr						    |
  +-------------------------------------------------------------------------+

*/


include ("./include/header.php");
?>
<script src="include/submitonce.js"></script>
<script src="include/sorttable.js"></script>

<h2><?php echo $lang['iwstat']['ws'] ?></h2>
<?php
//check priviledges
if (@$_SESSION["view_status"]!="true"){
	echo "<p class = \"error\">".$lang['general']['enoperm']."</p>";
	include("./include/footer.php");
	exit();
}
echo '<meta http-equiv="refresh" content="'.$C_status_refresh.'"/>';

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
	echo "<p class ='error'>".$lang['general']['enowifs']."</p>";
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

/*************************************************************************************************
	* proccess request to kick users from the AP device that the user asked
	* if there are for exaple 2 AP devices, the kicking proccess takes place only on the right one
	***************************************************************************************************/
	if(isset($_POST['ban'])){
		if($_SESSION["ban_users"] == 'true'){          			//CAN THE USER BAN OTHER USERS?
			$MAC = array_search("on",$_POST);
			while($MAC){
				ban_user($_POST['ban'],$MAC);
		        unset($_POST[$MAC]);
		        $MAC = array_search("on",$_POST);
	        }
	    }
		else{
		echo "<p class=\"error\">".$lang['iwstat']['enoperm']."</p>";
		exit();
        }
	}//end of if-kick clients


/***********************************************************************************************
**  Interface UP/DOWN CONTROL/VIEW
***********************************************************************************************/
?>
	<table class="invisible" align="center">
<?php
	if ($_SESSION["access_ifs"] == "true" && isset($_POST['ifaction'])) {
		if ($_POST['ifaction'] == "down")
			$upc = false;
		else
			$upc = true;
		if (control_device ($current['name'], $upc) === false)
			echo "<p class=\"error\">".$lang['iwstat']['enocontrol']."</p>";
	}
?>
	<form method="POST" onSubmit="submitonce(this)" action="<?php echo $_SERVER['PHP_SELF'];?>?device=<?php echo $current['name'];?>">
<?php
	echo "<tr><td><h3>".$lang['iwstat']['devstat']." : </h3>" ;
	if (is_device_up ($current['name'])) {
?>
		<td><h4 class='lblb'>UP</h4><td>
		<input type=submit value="Bring Down" <?php echo ($_SESSION["access_ifs"] != "true" ? "style=\"visibility:hidden\"" : "class='btns'") ?>>
		<input type=hidden value="down" name="ifaction">
<?php
	}
	else
	{
?>
		<td><h4 class='lbl'>DOWN</h4><td>
		<input type=submit value="Bring Up" <?php echo ($_SESSION["access_ifs"] != "true" ? "style=\"visibility:hidden\"" : "class='btns'") ?>>
		<input type=hidden value="up" name="ifaction">
<?php
	}
	echo "</form> </table>";
/************************************************************************************************/

/************************************************
* Main case for printing the device chosen
*************************************************/
switch ($current['mode']){
	case 'Master':													//do a Master device

	echo "<h4>".$lang['iwstat']['assocmacs']." ".$current['name']."</h4>\n";
	$asses = get_assocs($current['name']);   //Call function to get the data that we want for associations
	if($asses === false)
		echo "<h4>".$lang['iwstat']['noassocs']." ".$current['name']."</h4>\n";
	else
	{

	$data = $asses;
	$tx_max = max($asses['tx_bytes']);									//calculate maxes for later notification
	$rx_max = max($asses['rx_bytes']);

	$link = null;
	if($C_use_mysql == true)
		$link = connect_to_users_db();												//connect once if nessecary FOR USER

	?>
<table class = "invisible" align="center">
	<tr><td>
	<form method = "POST" onSubmit="submitonce(this)" action = "<?php echo $_SERVER['PHP_SELF'];?>?device=<?php echo $current['name'];?>">
	<table class="sortable" id="sort" align="center" cellspacing="0">
	<tr>
<?php
	if($_SESSION['ban_users'] == "true")
			echo "<th class=\"invisible\">&nbsp;</th>";
?>
	<th>#</th>
<?php if($_SESSION['view_macs'] == 'true'){
	echo "<th>".$lang['dict']['mac']."</th>";
}
if($C_use_mysql == true){
	   echo "<th>".$lang['dict']['user']."</th>";
}					?>
		<th><?php echo $lang['dict']['name'] ?></th>
 		<th><?php echo $lang['dict']['uploaded'] ?></th>
        <th><?php echo $lang['dict']['downloaded'] ?></th>
        <th><?php echo $lang['dict']['udratio'] ?></th>
	    <th><?php echo $lang['dict']['signal'] ?></th>
        <th><?php echo $lang['dict']['noise'] ?></th>
	</tr>
	<?php
	for($j=0;$j < count($asses['macs']);$j++)							//every loop creates one line of the assoc table
	{
		echo "<tr>\n";
		if($_SESSION['ban_users'] == "true")
			echo "<td><input type=checkbox name=\"".$asses['macs'][$j]."\"></td>\n";
		echo "<td>".($j+1)."</td>";		//echo number
		if($_SESSION['view_macs'] == 'true')            				//check users priviledge to see MAC addresses
			echo "<td>".$asses['macs'][$j]."</td>\n";

		//***show the respective MYSQL user***//
		if($C_use_mysql == true)
		{
			$output['username']=$lang['iwstat']['notreg'];
			$sql = "SELECT DISTINCT username FROM `user` WHERE mac = '".mysqli_real_escape_string($link, $asses['macs'][$j])."'";
			$result = mysqli_query($link, $sql);
			if (mysqli_num_rows($result) > 0)
			{
				$output = mysqli_fetch_assoc($result);
				echo "<td><a href=\"./users_edit.php?action=full_user_info&id=".$output['username']."\">".$output['username']."</a></td>\n";
			}else
			echo "<td>".$output['username']."</td>\n";
		}

		if($IP = resolve_mac($asses['macs'][$j]))						//get IP or name from a MAC address
		{
			if($C_resolve)    //resolve the IPs found assosiated?
				$name = ar_gethostbyaddr($IP);
			else
			$name = $IP;
			$name = "<a href=\"http://".$name."\">".$name."</a>";		//make $name a valid URL that points to "name"
		}
		else
		$name = "<i>".$lang['iwstat']['unresolved']."</i>";
		echo "<td>".$name."</td>\n";
		if($rx_max == $asses['rx_bytes'][$j])							//RX bytes
			echo "<td><div class=\"error\">".ByteSize($asses['rx_bytes'][$j])."</div></td>";
		else
			echo "<td>".ByteSize($asses['rx_bytes'][$j])."</td>";

        if($tx_max == $asses['tx_bytes'][$j])							//TX bytes
        	echo "<td><div class=\"error\">".ByteSize($asses['tx_bytes'][$j])."</div></td>";
		else
             echo "<td>".ByteSize($asses['tx_bytes'][$j])."</td>";

		echo "<td>".$asses['rates'][$j]."%</td>\n";						//RX-TX rates
		echo "<td>".$asses['signal'][$j]."</td>\n";
        echo "<td>".$asses['noise'][$j]."</td>\n";
		echo "</tr>\n";
	}
	echo "</table>
	</td></tr>";
	if($_SESSION['ban_users'] == "true")
		echo "<tr><td><input type=hidden name=\"ban\" value=\"".$current['name']."\">
				<input type = \"submit\" value = \"Ban user\"></td></tr>";
	echo "
	</form></table>";
	if ($asses['iftype'] != "hostap")
		echo "<p align='center'> * ".$lang['iwstat']['hostapnote'] ."</p>";

	if ($C_gen_graphs == true) {
		include "include/create_graphs.php";
		create_graphs($current['name']);
		echo "
		<table class=\"invisible\" cellspacing=\"0\" align=\"center\"> ";

		if(!isset($_POST['time']))
			$_POST["time"] = $lang['iwstat']['daily'];

	    echo "<tr><td>";
		echo_graph_nusers($router_name,$current['name'],$_POST['time']);
		echo"</td></tr>";
		echo "<tr><td>";
		echo_graph_traffic($router_name,$current['name'],$_POST['time']);
		echo "</td></tr>";
		?>
		<tr><td>
			<form method = "POST" onSubmit="submitonce(this)" action ="<?php echo $_SERVER['PHP_SELF']."?device=".$current['name'];?>">
				<select name = "time">
		            <option <?php if ($_POST["time"]== "daily") echo "selected"?>><?php echo $lang['iwstat']['daily'] ?></option>
		            <option <?php if ($_POST["time"]== "weekly") echo "selected"?>><?php echo $lang['iwstat']['weekly'] ?></option>
		            <option <?php if ($_POST["time"]== "monthly") echo "selected"?>><?php echo $lang['iwstat']['monthly'] ?></option>
		            <option <?php if ($_POST["time"]== "yearly") echo "selected"?>><?php echo $lang['iwstat']['yearly'] ?></option>
				</select>
				<input type="hidden" name ="nusers" value="<?php echo $current['name'];?>">
	            <input type = "submit" value = "<?php echo $lang['iwstat']['sgraph']?>">
			</form>
		</td></tr>
		</table>
<?php
	} // graphs
	}//end if no clients
break;//end printing Master device status

case 'Managed' :

	echo "<h4>".$current['name']." ".$lang['iwstat']['lstatus']."</h4>\n";
	echo "<table align=\"center\" class=\"t1\">
		<tr>
			<th>".$lang['dict']['type']."</th>
			<th>".$lang['dict']['mode']."</th>
			<th>".$lang['dict']['essid']."</th>
			<th>".$lang['dict']['channel']."</th>";
	if($_SESSION['view_macs'] == 'true'){								//check users priviledge to see MAC addresses
		echo "<th>".$lang['iwstat']['rmac']."</th>";
	}
	echo "	<th>Quality</th>
			<th>".$lang['dict']['signal']."</th>
			<th>".$lang['dict']['noise']."</th>
		</tr>";


	echo "<tr>
		<td>".$current['type']."</td><td>".$current['mode']."</td><td>".$current['essid']."</td><td>".$current['channel']."</td>";
		if($_SESSION['view_macs'] == 'true'){            //check users priviledge to see MAC addresses
			echo "<td>".$current['ap']."</td>";
		}
		echo "<td>".$current['quality']."</td><td>".$current['signal']."</td><td>".$current['noise']."</td>
	</tr>";
	if ($C_gen_graphs == true) {
		include "include/create_graphs.php";
		create_graphs($current['name']);
		if (!isset($_POST["time"]))
			$_POST["time"] = "daily";
		echo "<tr><td colspan=\"8\" align =\"right\" class=\"noborder\">";
		echo_graph_signal($router_name,$current['name'],$_POST['time']);
		echo "</td></tr>" ;
		echo "<tr><td colspan=\"8\" align =\"right\" class=\"noborder\">";
		echo_graph_rate($router_name,$current['name'],$_POST['time']);
		echo "</td></tr>" ;
		echo "<tr><td colspan=\"8\" align =\"right\" class=\"noborder\">";
		echo_graph_traffic($router_name,$current['name'],$_POST['time']);
		echo "</td></tr>" ;
	?>
	<tr><td colspan="8"  class="noborder">
	<form method = "POST" onSubmit="submitonce(this)" action = "<?php echo $_SERVER['PHP_SELF']."?device=".$current['name'];?>">
		<select name = "time">
			<option <?php if ($_POST["time"]== "daily") echo "selected"?>><?php echo $lang['iwstat']['daily'] ?></option>
			<option <?php if ($_POST["time"]== "weekly") echo "selected"?>><?php echo $lang['iwstat']['weekly'] ?></option>
			<option <?php if ($_POST["time"]== "monthly") echo "selected"?>><?php echo $lang['iwstat']['monthly'] ?></option>
			<option <?php if ($_POST["time"]== "yearly") echo "selected"?>><?php echo $lang['iwstat']['yearly'] ?></option>

		</select>
		<input type="hidden" name ="graph" value="<?php echo $current['name'];?>">
		<input type = "submit" value = "<?php echo $lang['iwstat']['sgraph']?>">
	</form>
	</td></tr>
	<?php
	} // graphs
	echo "</table>\n";
	break;

case 'Ad-Hoc':
	echo "<h4>".$current['name']." ".$lang['iwstat']['lstat']."</h4>\n";
	echo "<table align=\"center\" class=\"t1\">
		<tr>
			<th>".$lang['dict']['type']."</th>
			<th>".$lang['dict']['mode']."</th>
			<th>".$lang['dict']['essid']."</th>
			<th>".$lang['dict']['channel']."</th>";
	if($_SESSION['view_macs'] == 'true'){								//check users priviledge to see MAC addresses
		echo "<th>".$lang['iwstat']['rmac']."</th>";
	}
	echo "	<th>Quality</th>
			<th>".$lang['dict']['signal']."</th>
			<th>".$lang['dict']['noise']."</th>
		</tr>";

	echo "<tr>
		<td>".$current['type']."</td><td>".$current['mode']."</td><td>".$current['essid']."</td><td>".$current['channel']."</td>";
		if($_SESSION['view_macs'] == 'true'){            //check users priviledge to see MAC addresses
			echo "<td>".$current['cell']."</td>";
		}
		echo "<td>".$current['quality']."</td><td>".$current['signal']."</td><td>".$current['noise']."</td>
	</tr>";
	if ($C_gen_graphs == true) {
		include "include/create_graphs.php";
		create_graphs($current['name']);
		if (!isset($_POST["time"]))
			$_POST["time"] = "daily";
		echo "<tr><td colspan=\"8\" align =\"right\" class=\"noborder\">";
		echo_graph_signal($router_name,$current['name'],$_POST['time']);
		echo "</td></tr>" ;
		echo "<tr><td colspan=\"8\" align =\"right\" class=\"noborder\">";
		echo_graph_rate($router_name,$current['name'],$_POST['time']);
		echo "</td></tr>" ;
		echo "<tr><td colspan=\"8\" align =\"right\" class=\"noborder\">";
		echo_graph_traffic($router_name,$current['name'],$_POST['time']);
		echo "</td></tr>" ;
	?>
	<tr><td colspan="8"  class="noborder">
	<form method = "POST" onSubmit="submitonce(this)" action = "<?php echo $_SERVER['PHP_SELF']."?device=".$current['name'];?>">
		<select name = "time">
			<option <?php if ($_POST["time"]== "daily") echo "selected"?>><?php echo $lang['iwstat']['daily'] ?></option>
			<option <?php if ($_POST["time"]== "weekly") echo "selected"?>><?php echo $lang['iwstat']['weekly'] ?></option>
			<option <?php if ($_POST["time"]== "monthly") echo "selected"?>><?php echo $lang['iwstat']['monthly'] ?></option>
			<option <?php if ($_POST["time"]== "yearly") echo "selected"?>><?php echo $lang['iwstat']['yearly'] ?></option>
		</select>
		<input type="hidden" name ="graph" value="<?php echo $current['name'];?>">
		<input type = "submit" value = "<?php echo $lang['iwstat']['sgraph'] ?>">
	</form>
	</td></tr>
	<?php
    } // graphs
	echo "</table>\n";
	break;

default:
	echo "<h4>".$current['name']." status</h4>
	<p class =\"error\">".$lang['iwstat']['devmode']." ".$current['mode']. " ".$lang['iwstat']['notsupp']. ".</p>";
	break;
}//end case!


/************************************************
  Realtime Graphs
*************************************************/
if (@$_SESSION['view_status_ext']=='true') {
?>
<script type="text/javascript" src="include/popup.js"></script>
<p align="center"><a href="rt_graph.php" onclick="wopen('rt_graph.php?device=<?php echo $curname?>&mode=t','graph_<?php echo $curname?>',<?php echo ($C_rtgraph_width+100).",".($C_rtgraph_height+100)?>);return false;" > <?php echo $lang['iwstat']['showrtg']?></a>

<?php
}


/************************************************
* Log output
*************************************************/
if(isset($_GET['logs']) &&(@$_SESSION['view_macs'] == 'true') && $curname != ''){
	$num_of_lines = 35;
	echo "<h4>".$lang['dict']['last']." $num_of_lines ".$lang['iwstat']['kernmsg']."</h4>";
	echo "<p class=\"pre\">".str_replace("\n","<br>",taildevlog($curname,$num_of_lines)."</p>");
}
else
//show logs only if user has permission to see macs
if($_SESSION['view_macs'] == 'true'){            //check users priviledge to see MAC addresses
    echo "<div align=\"center\"><a href=\"".$_SERVER['PHP_SELF']."?device=".$current['name']."&logs=1\">[ ".$lang['iwstat']['vwl']." ]</a></div>";
}
include("./include/footer.php");
?>

