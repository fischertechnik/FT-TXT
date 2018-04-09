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

if(strpos($_SERVER['PHP_SELF'],basename(__FILE__))) 
	require "trap.php";
/* create signal-noise and link graphs. Supposed to run periodically by crond*/
include_once ("./config/config.php");


function create_graphs($dev){
	global $lang,$C_ses_regenerate_graphs;
	if (!isset($_SESSION['cache']["graphed-$dev"]) ) {
			echo "<p align='center'>Generating Graphs . . .</p>";
			if (!generate_graphs($dev))
				echo "<p class='error'>".$lang['iwstat']['errcgraph']."</p>";
			else
				$_SESSION['cache']["graphed-$dev"]=time();
	} else if (time()-$_SESSION['cache']["graphed-$dev"] > $C_ses_regenerate_graphs) {
			echo "<p align='center'>Re Generating Graphs (after $C_ses_regenerate_graphs secs). . .</p>";
			if (!generate_graphs($dev))
				echo "<p class='error'>".$lang['iwstat']['errcgraph']."</p>";
			else
				$_SESSION['cache']["graphed-$dev"]=time();
	}
}

function echo_graph_signal($router,$dev,$time)
{
	global $lang;
	echo "<img src=\"./graphs/$router-$dev-signal-$time.png\" alt=\"".$lang['iwstat']['esnoise']."\">";
}

function echo_graph_rate($router,$dev,$time)
{
	global $lang;
	echo "<img src=\"./graphs/$router-$dev-rate-$time.png\" alt=\"".$lang['iwstat']['erate']."\">";
}

function echo_graph_traffic($router,$dev,$time)
{
	global $lang;
	echo "<img src=\"./graphs/$router-$dev-traffic-$time.png\" alt=\"".$lang['iwstat']['etraff']."\">";
}

function echo_graph_nusers($router,$dev,$time)
{
	global $lang;
	echo "<img src=\"./graphs/$router-$dev-nusers-$time.png\" alt=\"".$lang['iwstat']['eusernum']."\">";
}



function generate_graphs($device) {
	global $C_graphs_path,$C_rrd_database_path,$router_name;

	//starting dir is main script dir	
	//check if graphs path exists, if not, create
	if (!is_dir( $C_graphs_path))
		if (!@mkdir( $C_graphs_path)) {
			return false;
		}

	//$status = get_wireless_status();
	$device_status = get_wireless_devstatus($device);
//for wireless devices
	//foreach ( $status as $device => $device_status)
	//{
		//echo print_r($device_status);
		//if device in master mode create only nusers graphs
		if ( $device_status["mode"] == "Master"){
			//check whether the coresponding rrd exists, if not, complain
			$nusers_rrd_filename = $C_rrd_database_path.$router_name."-".$device."-nusers.rrd";
			if (!file_exists($nusers_rrd_filename)) {
				echo "<p align='center'>nusers rrd file for $device does not exist</p>";
				//continue;
			}
			else
				create_nusers_graphs($device);
			//continue;
		}
		else {
			
			//if device not in master mode, create signal-noise graphs
			$rrd_filename = $C_rrd_database_path.$router_name."-".$device.".rrd";
			
			//check whether the coresponding rrd exists, if not, complain
			if (!file_exists($rrd_filename)) {
				echo "<p align='center'>rrd file for $device does not exist</p>";
				//continue;
			}
			else
				create_signal_noise_graphs($device);
		} //end else device mode
	//}

	//for all ethernet devices
	//$devices_status = get_ethernet_status();
	$device_status = get_ethernet_devstatus($device);
	//foreach ($devices_status as $device => $device_status){
		//if tx, rx are empty (because of a fake device for example)
		if ( $device_status["tx"]!=="") {
			$rrd_filename = $C_rrd_database_path.$router_name."-".$device."-traffic.rrd";
			if (!file_exists($rrd_filename)) {
				echo "<p align='center'>rrd file for $device does not exist</p>";
				//continue;
			}
			else
				create_traffic_graphs($device);
		}
	//}

	return true;
}

function create_signal_noise_graphs($device){
// creates signal - noise and link graphs
// inputs: $device: interface name (ie, eth0 wlan1)
	global $C_rrd_database_path, $C_rrdtool_bin, $C_graphs_path,$router_name;
	$intervals = array("daily" => -86400, "weekly" =>-604800, "monthly" =>-2678400, "yearly" => -33053184);
	
	foreach ( $intervals as $interval => $magic_number)
	{
		# generate signal/noise graph
		$graph_create_cmd = $C_rrdtool_bin." graph ".
		$C_graphs_path."$router_name-$device-signal-$interval.png\
		--imgformat=PNG \
		--start=$magic_number\
		--title=\"$interval $device signal-noise\"\
		--rigid \
		--base=1000\
		--height=120\
		--width=500\
		--upper-limit=-50\
		--lower-limit=-110 \
		--vertical-label=dBm\
		DEF:signal=\"".$C_rrd_database_path.$router_name."-".$device.".rrd\":signal:AVERAGE \
		AREA:signal#74C366:\"signal\" \
		GPRINT:signal:MIN:\"Min\:%4.0lf\" \
		GPRINT:signal:MAX:\"Max\:%4.0lf\" \
		GPRINT:signal:AVERAGE:\"Avg\:%4.0lf\" \
		GPRINT:signal:LAST:\"Current\:%4.0lf\\n\" \
		DEF:noise=\"".$C_rrd_database_path.$router_name."-".$device.".rrd\":noise:AVERAGE  \
		LINE2:noise#FF0000:\"noise \" \
		GPRINT:noise:MIN:\"Min\:%4.0lf\" \
		GPRINT:noise:MAX:\"Max\:%4.0lf\" \
		GPRINT:noise:AVERAGE:\"Avg\:%4.0lf\"\
		GPRINT:noise:LAST:\"Current\:%4.0lf\"";
		//echo $graph_create_cmd ."\n";
		$out = `$graph_create_cmd`;
		//echo $out."\n";
		
		//generate rate graph
		$graph_create_cmd = $C_rrdtool_bin." graph ".
		$C_graphs_path."$router_name-$device-rate-$interval.png\
		--imgformat=PNG\
		--start=$magic_number\
		--title=\" $device rate $interval graph\"\
		--rigid \
		--base=1000\
		--height=120\
		--width=500\
		--alt-autoscale-max\
		--lower-limit=0 \
		--vertical-label=\"Mbps\" \
		DEF:rate=\"".$C_rrd_database_path.$router_name."-".$device.".rrd\":rate:AVERAGE \
		LINE1:rate#0000FF:\"Link Rate\" \
		GPRINT:rate:MIN:\"Min\:%3.0lf\" \
		GPRINT:rate:MAX:\"Max\:%3.0lf\" \
		GPRINT:rate:AVERAGE:\"Avg\:%3.0lf\" \
		GPRINT:rate:LAST:\"Current\:%3.0lf\"";
		
		/*GPRINT:rate:LAST:\"Current\:%8.2lf %s\" \*/
		
		/*
		GPRINT:out:LAST:\"Current\:%8.2lf %s\"  \
		GPRINT:out:AVERAGE:\"Average\:%8.2lf %s\"  \
		GPRINT:out:MAX:\"Maximum\:%8.2lf %s\"";*/
		//echo $graph_create_cmd ."\n";
		$out = `$graph_create_cmd`;
		//echo $out."\n";
	}
}

function create_nusers_graphs($device){
// creates nusers graphs
// inputs: $device: interface name (ie, eth0 wlan1)
	global $C_rrd_database_path, $C_rrdtool_bin, $C_graphs_path,$router_name,$router_name;
	$intervals = array("daily" => -86400, "weekly" =>-604800, "monthly" =>-2678400, "yearly" => -33053184);
	
	foreach ( $intervals as $interval => $magic_number)
	{
		# generate nusers graph
		$graph_create_cmd = $C_rrdtool_bin." graph ".
		$C_graphs_path."$router_name-$device-nusers-$interval.png\
		--imgformat=PNG\
		--start=$magic_number\
		--title=\"$interval associations on device $device\"\
		--rigid \
		--base=1000\
		--height=120\
		--width=500\
		--alt-autoscale-max \
		--lower-limit=0 \
		--vertical-label=users\
		DEF:nusers=\"".$C_rrd_database_path.$router_name."-".$device."-nusers.rrd\":nusers:AVERAGE \
		AREA:nusers#74C366:\"number of users\" \
		GPRINT:nusers:MIN:\"Min\:%4.0lf\" \
		GPRINT:nusers:MAX:\"Max\:%4.0lf\" \
		GPRINT:nusers:AVERAGE:\"Avg\:%4.0lf\" \
		GPRINT:nusers:LAST:\"Current\:%4.0lf\"";
		//echo $graph_create_cmd ."\n";
		$out = `$graph_create_cmd`;
		//echo $out."\n";
		}
}

function create_traffic_graphs($device){
	global $C_rrd_database_path, $C_rrdtool_bin, $C_graphs_path,$router_name;
	$intervals = array("daily" => -86400, "weekly" =>-604800, "monthly" =>-2678400, "yearly" => -33053184);
	foreach ( $intervals as $interval => $magic_number)
	{
		$graph_create_cmd = $C_rrdtool_bin." graph ".
		$C_graphs_path."$router_name-$device-traffic-$interval.png\
		--imgformat=PNG \
		--start=$magic_number\
		--title=\"$device $interval traffic\" \
		--rigid \
		--base=1000 \
		--height=120 \
		--width=500 \
		--alt-autoscale-max \
		--lower-limit=0 \
		--vertical-label=\"bytes per second\" \
		DEF:in=\"".$C_rrd_database_path.$router_name."-".$device."-traffic.rrd\":in:AVERAGE \
		AREA:in#00CF00:\"Inbound \"  \
		GPRINT:in:MIN:\"Min\:%8.2lf %s\"  \
		GPRINT:in:MAX:\"Max\:%8.2lf %s\"  \
		GPRINT:in:AVERAGE:\"Avg\:%8.2lf %s\"  \
		GPRINT:in:LAST:\"Current\:%8.2lf %s\\n\"  \
		DEF:out=\"".$C_rrd_database_path.$router_name."-".$device."-traffic.rrd\":out:AVERAGE \
		LINE1:out#002A97:\"Outbound\"  \
		GPRINT:out:MIN:\"Min\:%8.2lf %s\"  \
		GPRINT:out:MAX:\"Max\:%8.2lf %s\" \
		GPRINT:out:AVERAGE:\"Avg\:%8.2lf %s\"  \
		GPRINT:out:LAST:\"Current\:%8.2lf %s\"";
		//echo $graph_create_cmd ."\n";
		$out = `$graph_create_cmd`;
		//echo $out."\n"; 
	}
}

?>


