#!/usr/bin/php 
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

//TODO : Check for multiple HOSTS......
/*This is supposed to run periodically and update the 
rrd databases, with signal and noise level, of all wifs*/

if (isset($_SERVER['REMOTE_ADDR'])) {
?>
	<H1> You should not be here </H1>
	<H2> Your IP/Port: <?php echo $_SERVER['REMOTE_ADDR']."/".$_SERVER['REMOTE_PORT'];?></H2>
	<H2> Your Browser Fingerprint: <?php echo $_SERVER['HTTP_USER_AGENT'];?></H2>
	<H2> Request Time: <?php echo date("d/M/Y H:i",$_SERVER['REQUEST_TIME']);?></H2>

<?php
	die();
}

//change current working directory to the directory where the script is located
$script_path=dirname($_SERVER["PHP_SELF"]);
echo "Changing to Script Path: $script_path\n";
chdir($script_path);


require("config/config.php");
require("include/lang_init.php");

require "include/router_init.php";

if ($C_gen_graphs !== true) {
	echo "No rrds save. Graph support is deactivated\n";
	die();
}

//echo "DBG "; print_r($_SERVER);
//echo `pwd`."\n";
//exec("env > $script_path/$C_rrd_database_path/env");

//check if rrd database path exists, if not, create
if (!is_dir( $C_rrd_database_path))
	mkdir( $C_rrd_database_path, 0777);


foreach ($C_routers as $router => $rt){
$router_name_new = $router;
echo " * Fetching data for router $router\n";
require "./include/router_init.php"; //no include once
require "./include/functions.php"; // no include once
//get status of wireless ifs
$status = get_wireless_status();
//echo "DBG "; print_r($status);
// For each wireless if get wif name, signal and noise, quality, etc into rrds
foreach ( $status as $device => $device_status)
{
	//if device in master mode, create-update number of connected users rrd
	if ( $device_status["mode"] == "Master"){
		$nusers_rrd_filename = $C_rrd_database_path.$router."-".$device."-nusers.rrd";
		//check whether the coresponding rrd exists, if not, create
		if (!file_exists($nusers_rrd_filename)) {
			print "creating nusers database for $device\n";
			$rrd_create_cmd = $C_rrdtool_bin." create ".$nusers_rrd_filename."\
				--step 300\
				DS:nusers:GAUGE:600:0:30\
				RRA:AVERAGE:0.5:1:600\
				RRA:AVERAGE:0.5:6:700\
				RRA:AVERAGE:0.5:24:775\
				RRA:AVERAGE:0.5:288:797\
				RRA:MAX:0.5:1:600\
				RRA:MAX:0.5:6:700\
				RRA:MAX:0.5:24:775\
				RRA:MAX:0.5:288:797";
			echo "create_command ".$rrd_create_cmd."\n";
			$out = `$rrd_create_cmd`;
			echo $out."\n";
		}
		//insert nusers into rrd
		$nusers = get_connected_users_num( $device );
		$rrd_update_cmd = $C_rrdtool_bin." update ".$nusers_rrd_filename."\
		--template nusers N:".$nusers;
		echo "Updating nusers rrd for ".$device."\n".$rrd_update_cmd."\n";
		$out = `$rrd_update_cmd`;
		echo $out;
		continue;
	}

	//if device not in master mode, create signal-noise graphs
	$rrd_filename = $C_rrd_database_path.$router."-".$device.".rrd";
	//check whether the coresponding rrd exists, if not, create
	if (!file_exists($rrd_filename)) {
		print "creating signal-noise database for $device\n";
		$rrd_create_cmd = $C_rrdtool_bin." create ".$rrd_filename."\
			--step 300\
			DS:signal:GAUGE:600:-150:0\
			DS:noise:GAUGE:600:-150:0\
			DS:quality:GAUGE:600:0:100\
			DS:rate:GAUGE:600:0:100\
			RRA:AVERAGE:0.5:1:600\
			RRA:AVERAGE:0.5:6:700\
			RRA:AVERAGE:0.5:24:775\
			RRA:AVERAGE:0.5:288:797\
			RRA:MAX:0.5:1:600\
			RRA:MAX:0.5:6:700\
			RRA:MAX:0.5:24:775\
			RRA:MAX:0.5:288:797";
		echo "create_command ".$rrd_create_cmd."\n";
		$out = `$rrd_create_cmd`;
		echo $out."\n";
	}
	
	//strip trailing /92 out of quality string
	$pos = strpos($device_status["quality"], '/');
	if ($pos)
		$device_status["quality"] = substr($device_status["quality"],0,$pos);
	
	//insert signal, noise into rrd
	$rrd_update_cmd = $C_rrdtool_bin." update ".$rrd_filename."\
	 --template signal:noise:quality:rate N:".$device_status["signal"].":".$device_status["noise"].":".$device_status["quality"].":".$device_status["rate"];
	echo "Updating rrd for ".$device."\n".$rrd_update_cmd."\n";
	$out = `$rrd_update_cmd`;
	echo $out;
}

//for all ethernet interfaces (except loopback), create-update, traffic rrds

//get ethernet ifs
$devices_status = get_ethernet_status();

foreach ($devices_status as $device => $device_status){
	//if tx, rx are empty (because of a fake device for example)
	if ( $device_status["tx"]=="")
		continue;
	$traffic_rrd_filename = $C_rrd_database_path.$router."-".$device."-traffic.rrd";
	if (!file_exists($traffic_rrd_filename)) {
		print "creating traffic database for $device\n";
		$rrd_create_cmd = $C_rrdtool_bin." create ".$traffic_rrd_filename."\
			--step 300  \
			DS:in:COUNTER:600:0:100000000 \
			DS:out:COUNTER:600:0:100000000 \
			RRA:AVERAGE:0.5:1:600 \
			RRA:AVERAGE:0.5:6:700 \
			RRA:AVERAGE:0.5:24:775 \
			RRA:AVERAGE:0.5:288:797 \
			RRA:MAX:0.5:1:600 \
			RRA:MAX:0.5:6:700 \
			RRA:MAX:0.5:24:775 \
			RRA:MAX:0.5:288:797";
		echo "create_command ".$rrd_create_cmd."\n";
		$out = `$rrd_create_cmd`;
		echo $out."\n";
	}
	
	//insert tx,rx into rrd
	$rrd_update_cmd = $C_rrdtool_bin." update ".$traffic_rrd_filename."\
	 --template in:out N:".$device_status["rx"].":".$device_status["tx"];
	echo "Updating rrd for ".$device."\n".$rrd_update_cmd."\n";
	$out = `$rrd_update_cmd`;
	echo $out;
}
} //end foreach routers







?>
