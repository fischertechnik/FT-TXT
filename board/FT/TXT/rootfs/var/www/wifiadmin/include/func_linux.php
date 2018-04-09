<?php
/*+-------------------------------------------------------------------------+
  | Copyright (C) 2004 Panousis Thanos - Dimopoulos Thimios
  |                2008 basOS (basos@users.sf.net)                          |
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
  | WifiAdmin: The Free WiFi Web Interface                                  |
  +-------------------------------------------------------------------------+
  | Send comments to                                                        |
  | - panousis@ceid.upatras.gr                                              |
  | - dimopule@ceid.upatras.gr                                              |
  +-------------------------------------------------------------------------+*/
require_once("./config/config.php");

/************************************************
*   LINUX SPECIFIC ABSTRACTION FUNCS            *
*************************************************/

/* Underscore (_) prefixed functions are HELP functions used localy */
/* System Paths. The P prefixed vars are to used only into this file and represent
   linux specific paths to binaries or other files */

$BIN_LINUX = array(
	'hostap_device_path' => '/proc/net/hostap/',
	'net_dev_path' => '/proc/net/dev',
	// DEPRECATED 'wlan_dev_path' => '/proc/net/wireless',
	'uptime_path' => '/proc/uptime',
	'hostname_path' => '/proc/sys/kernel/hostname',
	/*Paths to binaries*/
	'cat_bin' => "cat",
	'ls_bin' => "ls",
	'dmesg_bin' => "dmesg",
	'madwifi_wlanconfig_bin' => '/sbin/wlanconfig',
	/*Sudo'd programs are prefixed with s
		Allow for a missconfigured sudod system to *see* things*/
	's_iwconfig_bin' => 'sudo iwconfig', // *
	'iwconfig_bin' => '/sbin/iwconfig',

	's_iwlist_bin' => 'sudo iwlist', // *

	's_iwpriv_bin' => 'sudo iwpriv', //*

	's_ifconfig_bin' => 'sudo ifconfig', // *
	'ifconfig_bin' => '/sbin/ifconfig',

	's_route_bin' => 'sudo route', // *
	'route_bin' => '/sbin/route',

	'host_bin' => 'host',
	'arp_bin' => '/usr/sbin/arp',
	'uname_bin' => 'uname',

	's_dhclient_bin' => "sudo dhclient", //*
);


/********************************************
 ***** GENERIC API **************************
 *******************************************/

/******* 7 *************************************
 * Check for the existance of nessesary binaries
 * as well as for sudo access
 ************************************************/
function echo_bin_check()
{
	global $lang, $BIN_LINUX ;

	$P_cat_bin = $BIN_LINUX['cat_bin'];
	$P_ls_bin = $BIN_LINUX['ls_bin'];
	$P_dmesg_bin = $BIN_LINUX['dmesg_bin'];
  $P_madwifi_wlanconfig_bin = $BIN_LINUX['madwifi_wlanconfig_bin'];
  $P_iwconfig_bin = $BIN_LINUX['iwconfig_bin'];
  $P_ifconfig_bin = $BIN_LINUX['ifconfig_bin'];
  $P_route_bin = $BIN_LINUX['route_bin'];
  $P_host_bin = $BIN_LINUX['host_bin'];
  $P_arp_bin = $BIN_LINUX['arp_bin'];
  $P_uname_bin = $BIN_LINUX['uname_bin'];
  $P_s_iwconfig_bin = $BIN_LINUX['s_iwconfig_bin'];
  $P_s_iwpriv_bin = $BIN_LINUX['s_iwpriv_bin'];
  $P_s_iwlist_bin = $BIN_LINUX['s_iwlist_bin'];
  $P_s_iwpriv_bin = $BIN_LINUX['s_iwpriv_bin'];
  $P_s_ifconfig_bin = $BIN_LINUX['s_ifconfig_bin'];
  $P_s_route_bin = $BIN_LINUX['s_route_bin'];
  $P_s_dhclient_bin = $BIN_LINUX['s_dhclient_bin'];

	$desc = array ( 0 => $lang['index']['errbininf'],
			1 => $lang['index']['errbinopt'],
			2 => $lang['index']['errbinfat'] );

	// bin check array of severity; 2: a must, 1: might brek, 0: miss information
	$bincheck = array ($P_cat_bin => 2,
			 $P_ls_bin => 2,
			 $P_dmesg_bin => 0,
			 $P_madwifi_wlanconfig_bin => array(1,"--version"),
			 $P_iwconfig_bin => array(1,"--version"),
			 $P_ifconfig_bin =>2,
			 $P_route_bin => 1,
			 $P_host_bin => array(1,"localhost"),
			 $P_arp_bin =>0,
			 $P_uname_bin =>0);
	echo "<table><caption>".$lang['index']['bincheck']."</caption>"; //retrun string
    /* do not use flash(), there is at least one case where is stops execution (appweb webserver) */
	ob_start();
	foreach ($bincheck as $bin => $sev) {
		$par = "";
		if (is_array($sev)) {
			$par = $sev[1];
			$sev = $sev[0];
		}
    $outar = array();
		$res = wexec ($bin. " ".$par,$outar) ;
		$err = implode ("\n",$outar);
		echo "<tr><td>".$lang['dict']['checking']." $bin :</td>";
		//flush();
        ob_flush();
		if (!$res)
			echo "<td class='error'>".$lang['dict']['missing'].". " . $desc[$sev] ."</td>";
		else
			echo "<td>".$lang['dict']['ok']."</td>";
		echo "<tr>";
		//flush();
        ob_flush();
	}

	$sudcheck = array($P_s_iwconfig_bin => array(1,"--version"),
			 $P_s_iwpriv_bin => array(1,"--version"),
			 $P_s_iwlist_bin => array(1,"--version"),
			 $P_s_ifconfig_bin => 1,
			 $P_s_route_bin => 1,
             $P_s_dhclient_bin => array(1,"--version"));
	echo "</table><table><caption>".$lang['index']['sudcheck'] ."</caption>";
	foreach ($sudcheck as $bin => $sev) {
		$par = "";
		if (is_array($sev)) {
			$par = $sev[1];
			$sev = $sev[0];
		}
		$res = wexec ($bin. " ".$par, $outar) ;
		$err = implode ("\n",$outar);
		echo "<tr><td>".$lang['dict']['checking']." $bin :</td>";
		//flush();
        ob_flush();
		if (!$res)
			echo "<td class='error'>".$lang['dict']['error'].". ". $desc[$sev] ."</td>";
		else
			echo "<td>".$lang['dict']['ok']."</td>";
		echo "<tr>";
		//flush();
        ob_flush();
	}
	echo "</table>";
}


/**** 2 ********************************
*	 Return System Info
*    For informational purposes
***************************************/
function system_nfo () {
	global $BIN_LINUX;
	$result = wexec($BIN_LINUX['uname_bin'].' -srmo');
  return $result;
}

/**** 3 *********************************
*	Return system uptime. Informational only
****************************************/
function uptime () {
	global $BIN_LINUX;
	if (! $buf = wexec($BIN_LINUX['cat_bin'] . ' '. $BIN_LINUX['uptime_path'])) {
		return "N/A";
	}
	$ar_buf = preg_split('/ /', $buf);
	$sys_ticks = trim($ar_buf[0]);
	$min = $sys_ticks / 60;
	$hours = $min / 60;
	$days = floor($hours / 24);
	$hours = floor($hours - ($days * 24));
	$min = floor($min - ($days * 60 * 24) - ($hours * 60));
	$result = "";
	if ($days != 0) {
		if ($days > 1)
			$result = "$days " ." days ";
		else
			$result = "$days " ." day ";
	}
	if ($hours != 0) {
	if ($hours > 1)
		$result .= "$hours " ." hours ";
	else
		$result .= "$hours " ." hour ";
	}
	if ($min > 1 || $min == 0)
		$result .= "$min " ." minutes ";
	elseif ($min == 1)
		$result .= "$min " ." minute ";

	return $result;
}

/*** 4 *********************************
*	 Return Router Hostname
***************************************/
function chostname () {
	global $BIN_LINUX;
  if ($buf = trim(wexec($BIN_LINUX['cat_bin'] . ' '. $BIN_LINUX['hostname_path']))) {
		@$resulta = gethostbyaddr(gethostbyname($result));
    if ($resulta !== "") $result = $resulta;
	} else {
		$result = 'N/A';
	}
	return $result;
}

/******** 5 ***************************************
* Resolve a mac into ip
* Transported from iwstatus.php
***************************************************/
function resolve_mac($mac_addr)
{
	global $BIN_LINUX;
	$ip_addr = wexec( $BIN_LINUX['arp_bin']." -n | grep -i ".$mac_addr." | cut -d' ' -f1");	//get IP from mac
	if($ip_addr == "")
		$ip_addr = false;
	return $ip_addr;
}

/******** 6 ***************************************
* function that safely resolves hosts to names using a timeout
* It uses the linux command host, for it provides a timeout mechanism
* Transported from iwstatus.php
***************************************************/
function ar_gethostbyaddr($ip)
{
	global $BIN_LINUX;
	global $C_resolve_timeout;
	$output = wexecp($BIN_LINUX['host_bin'] ." -W ". $C_resolve_timeout, $ip);
	if (preg_match('/.*pointer ([A-Za-z0-9.-]+)\..*/',$output,$regs)) {
		return $regs[1];
	}
	return $ip;
}

/****** 7 *************************************
* Tail the last $nlines of dmesg containing $dev string
* Transported from iwstatus.php
***********************************************/
function taildevlog ($dev,$nlines)
{
	global $BIN_LINUX;
	wexec($BIN_LINUX['dmesg_bin']." | grep $dev | tail -n $nlines");
}




/************************************************
 ***** WIRELESS DEVICES API ********************
 ************************************************/

/**** 1 *****************************************
* returns an array in the following format
* ans[i] = ith interface's name
************************************************/
function get_wifs() 
{
	$ifs = get_ifs();
	$and = array();
	foreach ($ifs as $dev) {
		if (false !== get_wireless_devstatus($dev))
			$ans[] = $dev;
	}
	return $ans;
}

/** DEPRECATED, does not always show existing interfaces. Use iwconfig instead
function get_wifs() {
	global $BIN_LINUX;
	//wifs contains the names of the wifi devices
	//MODA
	$ans = array();
	wexec($BIN_LINUX['cat_bin'] . ' '. $BIN_LINUX['wlan_dev_path'],$wifs);
	//echo "DBG getwif "; print_r($wifs);

	unset ($wifs[0]);
	unset ($wifs[1]);

	foreach($wifs as $var)	// find names of wireless ifs
	{
		$var=trim($var); // *** VERY IMPORTANT ***
		if ($var == "") continue;
		preg_match ('/(^.*):/',$var,$fields);
		$ans[]=$fields[1];
	}
	return $ans;
}
*/

   // For use in get_assocs
	function _parse_assoc_hostap($associations)
	{
		$list = preg_split("/[\s]+/", $associations);
		for($i=0;$i<count($list)-1;$i++)
		{
				if(!(strpos($list[$i],"STA=" )=== FALSE))
					$macs[] = str_replace("STA=","",$list[$i]);

				elseif (!(strpos($list[$i],"rx_bytes=")=== FALSE))
					$rx_bytes[] = str_replace("rx_bytes=","",$list[$i]);

				elseif (!(strpos($list[$i],"tx_bytes=")=== FALSE))
					$tx_bytes[] =  str_replace("tx_bytes=","",$list[$i]);

				// new hostap exports correct signal levels
				elseif (!(strpos($list[$i],"silence=")=== FALSE))
					$noise[] = str_replace("silence=","",$list[$i]);//-256;

				elseif (!(strpos($list[$i],"signal=")=== FALSE))
					$signal[] = str_replace("signal=","",$list[$i]);//-256;
		}

		for($i=0;$i<count($tx_bytes);$i++)
		{
			if ($rx_bytes[$i] == 0){
				$rates[$i] =c0;
				continue;
			}
			if($tx_bytes[$i]==0)
				$rates[$i] = 0;
			else
				$rates[$i] = round($rx_bytes[$i]/$tx_bytes[$i],4);
			$rates[$i] = $rates[$i]*100;
		}
		return $ans = array('macs'=>$macs,'rx_bytes'=>$rx_bytes,'tx_bytes'=>$tx_bytes,'rates'=>$rates,'signal'=>$signal,'noise'=>$noise);
	}

   // For use in get_assocs
  function _parse_assoc_madwifi ($ass) {
 	 /*
		ADDR               AID CHAN RATE RSSI  DBM IDLE  TXSEQ  RXSEQ CAPS ACAPS ERP    STATE     MODE
		00:0b:85:1e:03:d0    1  112  36M   46  -49  135  53229  24992 ESs          0       21   Normal
	  */
	$lines = explode("\n", $ass);
	for ($c=1; $c<count($lines); $c++) {
	//TODO : Check RXSEQ TXSEQ field meaning
		$fields = preg_split("/[\t ]+/",$lines[$c]);
		//DBG	//echo $lines[$c];	//print_r($fields);
		$res['macs'][] = $fields[0];
		$res['rx_bytes'][] = "-1"; //$fields[8];
		$res['tx_bytes'][] = "-1"; //$fields[7];
		$res['rate'][] = $fields[3];
		$res['signal'][] = $fields[5];
		$res['noise'][] = $fields[5] - $fields[4];
    	/*if ($fields[7] > 0)
			// rx/tx  => 8/7
			$res['rates'][] = round($fields[8]/$fields[7]*100,2);
		else
    	  	$res['rates'][] = 0;
		*/
		$res['rates'][] = "0";
	}
	return $res;
  };

/***** 2 *************************************************
* Return Array with the following info about a Master mode wireless
* interface for each associated station :
* Array['macs','rx_bytes',tx_bytes','rates','signal','noise'][station index];
* NOTE: WIRELESS TOOLS NOT USED FOR THIS (SUPPORTED :HOSTAP, MADWIFI)
*********************************************************/
function get_assocs ($iwname) {
	global $BIN_LINUX;
	//TRY HOSTAP
	$command = $BIN_LINUX['cat_bin']. " ". $BIN_LINUX['hostap_device_path'].$iwname."/0*";		//command that accumulates association data
	$associations = wexec ($command);
	if ($associations != "") {
		$res = _parse_assoc_hostap($associations);
		$res['iftype']="hostap";
	}
	else if ($associations == "") {
	//TRY MADWIFI
		$command = $BIN_LINUX['madwifi_wlanconfig_bin'] . " " . $iwname . " list";
		$associations = wexec ($command);
		//echo "DBG " .$command . " ASS= ". $associations;
		if ($associations != "" ) {
			$res = _parse_assoc_madwifi($associations);
			$res['iftype']="madwifi";
		}
		else
			return false;
	}
	return $res;
}


/******* 3 *************************************************
* **HOSTAP SPECIFIC**
* Returns the security policy of the specified device
* Device must be in master mode or else garbage is returned
*************************************************************/
function get_policy($wif)
{
	//MODA
	global $BIN_LINUX;
	$ap_control_file = $BIN_LINUX['cat_bin']." ".$BIN_LINUX['hostap_device_path'].$wif."/ap_control";
	if (wexec($ap_control_file,$data)) {
		sscanf($data[0],"MAC policy: %s", $security['policy']);
		if(sizeof($data) > 3)
		{
			for($i=3;$i<sizeof($data);$i++)
						 $security[$i-3] = $data[$i];
		}
	}
	else {
		$security['policy']="Unavailable";
	}
	return $security;
}

/****** 4 ************************************************
* Bans a MAC address from the master mode device given.
* operation varies depending on current Access control list
* policy.
************************************************************/
function ban_user($wif,$MAC)
{
	global $BIN_LINUX;
	$security = get_policy($wif);

	if( $security['policy'] == "allow" ) {
		$cmd = $BIN_LINUX['s_iwpriv_bin']." ".$wif ." delmac ".$MAC;
		echo wexec($cmd);     	 //delete the MAC from the allow list
		echo wexec($BIN_LINUX['s_iwpriv_bin'] ." $wif kickmac $MAC");     //and finally kick the bastard!!
  }
	elseif ( $security['policy'] == "deny" ) {
		$cmd = $BIN_LINUX['s_iwpriv_bin']." ".$wif ." addmac ". $MAC;
		echo wexec($cmd);      //delete the MAC from the allow list
		echo wexec($BIN_LINUX['s_iwpriv_bin'] ." $wif kickmac $MAC");     //and finally kick the bastard!!
  }
	else { //default assume open policy ( $security['policy'] == "open" )
		$cmd = $BIN_LINUX['s_iwpriv_bin']." ".$wif." maccmd 2";
		echo wexec($cmd);	//change to deny policy
		echo wexecp($BIN_LINUX['s_iwpriv_bin'],array("$wif", "addmac", "$MAC"));	//add the MAC to the deny list
		echo wexecp($BIN_LINUX['s_iwpriv_bin'],array("$wif", "kickmac", "$MAC"));	//and finally kick the bastard!!
	}
}

/**** 5 *****************************************************
* Returns # of connected users in a AP
* this works for hostap only. it is used in create-update-rrds
* NOTE: WIRELESS TOOLS NOT USED FOR THIS (SUPPORTED :HOSTAP, MADWIFI)
************************************************************/
function get_connected_users_num( $device ){
	//MODA
	global $BIN_LINUX;

	//wexec($P_ls_bin." ".$P_hostap_device_path,$device_dirs);
	if ( wexec($BIN_LINUX['ls_bin']." ".$BIN_LINUX['hostap_device_path'] . $device, $files) == false) {
		// try madiwifi (here $files are the lines of associations)
		wexec($BIN_LINUX['madwifi_wlanconfig_bin']." ". $device." list", $files);
	}
	$users_num = 0;
	foreach( $files as $file){
		if (strstr( $file , ':') != false)
			$users_num++;
	}
	return $users_num;
}

  /*an1     IEEE 802.11b  ESSID:"PWN_aroi"
          Mode:Managed  Frequency:2.432GHz  Access Point: 00:40:96:38:9:0
          Bit Rate:2Mb/s   Tx-Power:128 dBm   Sensitivity=1/3
          Retry limit:8   RTS thr:off   Fragment thr:off
          Encryption key:off
          Power Management:off
          Link Quality:24/92  Signal level:-68 dBm  Noise level:-95 dBm
          Rx invalid nwid:0  Rx invalid crypt:42  Rx invalid frag:0
          Tx excessive retries:416352  Invalid misc:70361034   Missed beacon:0 */
  //use for use within parse_iwconfig
 /* function _getnext($haystack,$needle)
  {
    $pos = strpos($haystack,$needle);
	if($pos !== true)
	{
		//$pos=end of label (start delim) $epos end of data (end delim)
		$pos+= strlen($needle);
		echo "DBG ".strpos($haystack,'"',$pos+1);
		if (strpos($haystack,'"',$pos+1) == $pos+1) {
			$epos = strpos($haystack,'"',$pos+2);
			$pos++;
		}
		else
			$epos = strpos($haystack," ",$pos+1);
		$str = substr($haystack, $pos+1,$epos-($pos+1));
		echo " DBG pos=$pos epos=$epos str=$str <br>";
		return trim($str);
	}
	else
		return "";
  };*/

/* Returns part of a string starting at the end of $needle and ending etiher at
   the next " (if next char of $needle is ") or the next space */
//help func
function _getnext ($haystack,$needle) {
	preg_match('/'.$needle.'"([^"]*)"/',$haystack,$parts);
	if (isset($parts[1]))
		return trim($parts[1]);
	preg_match('/'.$needle.'([^\ ]*)/',$haystack,$parts);
	//echo "DBG $needle @ $haystack <br>";
	if (isset($parts[1]))
		return trim($parts[1]);
	else
		return "";
}

  //returns the following attributes of a wifi device
  //given its "iwconfig wlanX" output
  // use within get_wireless_dev_status
  function _parse_iwconfig($output)
  {
	//IWCONFIG OUTPUT PARSING!!
	$data['type'] = _getnext($output,"IEEE\ ");
	$data['nick'] = _getnext($output,"Nickname:");
	$data['essid'] = _getnext($output,"ESSID.");
	$data['mode'] =  _getnext($output,"Mode:");
	$data['channel'] =  freq2channel(_getnext($output,"Frequency:"));
	$data['ap'] =  _getnext($output,"Access Point:\ ");
	$data['cell'] =  _getnext($output,"Cell:");
	$data['quality'] =  _getnext($output,"Link Quality=");
	$data['signal'] =  _getnext($output,"Signal level.");
	$data['noise'] =  _getnext($output,"Noise level.");
	$data['rate'] =  _getnext($output,"Rate.");
	$data['sens'] =  _getnext($output,"Sensitivity.");
	$data['retry'] =  _getnext($output,"limit.");//fix "retry min limit" and "retry limit" that exist in different versions of hostap/witools
	$data['rts'] =  _getnext($output,"RTS thr.");
	$data['frag'] =  _getnext($output,"Fragment thr.");
	$data['power'] =  _getnext($output,"Power Management.");
	$data['key'] = _getnext($output,"Encryption Key:");
	$data['secmod'] = _getnext($output,"Security mode:");
	$data['txpower'] = _getnext($output,"Tx-Power.");
	return $data;
  };

//basos ADD
/****** 6 ***************************************************
* iwconfig output parsed
* TODO: Wipe it out. Use API or write new tool
*************************************************************/
function get_wireless_devstatus($devname) {
	global $BIN_LINUX;
	$res = wexecp($BIN_LINUX['iwconfig_bin'], $devname, $device_status_ar);
	if ($res === false)
		return false;
	$device_status_string = implode(" ", $device_status_ar);
	$device_data = _parse_iwconfig($device_status_string);
	$device_data["name"] = $devname;
	//DBG //echo $devname . $device_status_string;//print_r($device_data);
	return $device_data;
}

/**** 7 *******************************************
* iwconfig output in an array with device names as keys\
* CHECK : Possibly remove
***************************************************/

function get_wireless_status(){
	$devs = get_wifs();
	$devices_data = array();
	foreach($devs as $device){
		$devices_data[$device] = get_wireless_devstatus($device);
	}
	return $devices_data;
}

//help func
function _get_value($input_string, $attribute){
	sscanf(strstr($input_string, $attribute) ,$attribute."%s",$out);
	return trim($out);
}

//help func
function _get_all_values($input_string, $attribute){
	$values = array();
	$count = 0;
	$next_pos = strpos($input_string, $attribute);
	while(!($next_pos === false)){
		//sscanf(strstr($input_string, $attribute), $attribute."%s",$value);
		preg_match ("/".$attribute."(.*)/",$input_string,$parts);
		$values[$count] = trim($parts[1]);
		$count++;
		$input_string = substr($input_string, $next_pos + strlen($attribute));
		$next_pos = strpos($input_string, $attribute);
	}
	return $values;
}

/***** 8 ***************************************************************
* Get scan results
* returns false if device doesn't support scanning, empty array if there are no scan results
* A[index]['address','essid','mode','freq','chan','signal','noise','SNR','enc','rates','extras']
**************************************************************************/
function get_scan_results($device){
	global $BIN_LINUX;
	$iwlist_cmd = $BIN_LINUX['s_iwlist_bin']." ".$device." scan";
	$iwlist_output = wexec($iwlist_cmd);
	//echo "DBG scan: '$iwlist_output'<br>";
	if (!(stristr( $iwlist_output, "Operation not supported") === false))
		return false;
	//remove first line
	$iwlist_output = ltrim (substr($iwlist_output,strcspn($iwlist_output, "\n")));
	//shift the first empty element
	$cell_strings = array_slice(explode("Cell", $iwlist_output),1);

	$cell_infos = array();
	foreach( $cell_strings as $cell_index => $cell_string){
		$cell_info["address"] = _getnext($cell_string,"Address:\ ");
		$cell_info["essid"] = _getnext($cell_string,"ESSID:");
		$cell_info["mode"] = _getnext($cell_string,"Mode:");
		$cell_info["freq"] = _getnext($cell_string,"Frequency:");
		$cell_info["chan"] = freq2channel($cell_info["freq"]);
		/* Changed for wireless tools v29
		Maybe compatibility issues but we 'll support the new ones */
		$cell_info["signal"] = _getnext($cell_string,"Signal level.");
		$cell_info["noise"] = _getnext($cell_string,"Noise level.");
		//OLD semantics
		/*if ($cell_info["signal"] =="") $cell_info["signal"] = _get_value($cell_string,"Signal level:");
		if ($cell_info["noise"] =="") $cell_info["noise"] = _get_value($cell_string,"Noise level:");*/

		//$cell_info["quality"] = _get_value($cell_string,"Quality:");
		if ($cell_info['noise']) @$cell_info['SNR'] = $cell_info['signal'] - $cell_info['noise'];
		$cell_info["enc"] = _getnext($cell_string,"Encryption key:");

		preg_match('/Bit Rates:(.*)\n(.*:|(.*)\n(.*:|(.*)\n))/',$cell_string,$rates);
		@$cell_info["rates"] =trim($rates[1]).trim("<br>".$rates[3]).trim("<br>".$rates[5]);
		$treps = array("Mb/s"," ");
		$cell_info["rates"] = str_ireplace($treps,"",$cell_info["rates"]);

		//OLD Semantics
		if ($cell_info["rates"] =="") {
			$rates = _get_all_values($cell_string,"Bit Rate:");
			$cell_info["rates"] = array_shift($rates);
			foreach ($rates as $rate)
				$cell_info["rates"] = $cell_info["rates"].", ".$rate;
		}

		$extras = _get_all_values($cell_string,"Extra:");
		$cell_info["extras"] = array_shift($extras);
		foreach ($extras as $extra)
			$cell_info["extras"] = $cell_info["extras"].", ".$extra;
		$cell_infos[$cell_index] = $cell_info;
	}
	return($cell_infos);
}

/***** 9 ************************************************
* Deletes a MAC from the ACL list
* Transported from iwsecurity.php
*********************************************************/
function AP_acl_delmac($wif,$mac) {
	global $BIN_LINUX;
	return wexecp ($BIN_LINUX['s_iwpriv_bin'],array("$wif", "delmac", "$mac"),$out);
}

/***** 10 ************************************************
* Adds a MAC to the ACL list
* Transported from iwsecurity.php
*********************************************************/
function AP_acl_addmac($wif,$mac) {
	global $BIN_LINUX;
	return wexecp($BIN_LINUX['s_iwpriv_bin'],array("$wif", "addmac", "$mac"),$out);
}

/***** 11 ************************************************
* Sets a new ACL policy
* Transported from iwsecurity.php
*********************************************************/
function AP_acl_apply_policy($wif, $policy)
{
	global $BIN_LINUX,$lang;
	if($policy == $lang['iwsec']['selopen'])
		return wexec($BIN_LINUX['s_iwpriv_bin'] ." $wif maccmd 0",$res);
	elseif($policy == $lang['iwsec']['selallow'])
		return wexec($BIN_LINUX['s_iwpriv_bin']. " $wif maccmd 1",$res);
	elseif($policy == $lang['iwsec']['seldeny'])
		return wexec($BIN_LINUX['s_iwpriv_bin']. " $wif maccmd 2",$res);
	else
		return false;
}

/******* 12 ****************************************
* A wrapper for iwconfig basic setup, Config vars are optional
*	and are elements of $sets array. Possible values :
*	$sets['essid','nick','mode','channel','ap','rate','sens','retry','rts','frag','power','txpower']
*	Return Error string on error or true on success
* Transported from iwsettings.php
**************************************************/
function update_iwdev_settings($dev,$sets) {
	global $BIN_LINUX;
	$if_par[] = $dev;
	foreach ($sets as $opt => $val) {
		//$if_par .= " ".$opt . " " .$val;
		$if_par[] =  $opt;
		$if_par[] =  $val;
	}

	$route_res = wexecp($BIN_LINUX['s_iwconfig_bin'], $if_par, $out, false, "2>&1");
	if (!$route_res)
			$route_res = implode ("\n",$out);
	return $route_res;
}








/***************************************
 ****** GENERIC INTERFACES API *********
 **************************************/

/****** 1 **************************************
* get_ifs() - All Interfces
* returns an array in the following format
* ans[i] = ith interface's name
************************************************/
function get_ifs()
{
	global $BIN_LINUX;

	$ans = array();
	wexec($BIN_LINUX['cat_bin'] . ' '. $BIN_LINUX['net_dev_path'],$nifs);

	unset ($nifs[0]);
	unset ($nifs[1]);

	foreach($nifs as $var)	// find names of ifs
	{
		$var=trim($var); // *** VERY IMPORTANT ***
		if ($var == "") continue;
		// Skip wifiX dummy devices for madwifi and hostap
		if (strpos($var,"wifi") !== false) continue;
		// Skip loopback interface
		if (strpos($var,"lo") !== false) continue;
		preg_match ('/(^.*):/',$var,$fields);
		$ans[]=$fields[1];
		//echo "DBG: /". $fields[1]."/";
	}
	return $ans;
}

/***** 2 *************************************
* Ifconfig output parsed
***********************************************/
function get_ethernet_devstatus($devname) {
	global $BIN_LINUX;
	$res = wexecp($BIN_LINUX['ifconfig_bin'], $devname, $device_status_ar);
	if (false === $res)
		return false;

	$device_status_string = implode(" ", $device_status_ar);
	$device_status["Link encap"] = _get_value($device_status_string,"Link encap:");
	$device_status["hwaddr"] = _get_value($device_status_string,"HWaddr");
	$device_status["ipaddr"] = _get_value($device_status_string,"inet addr:");
	$device_status["bcast"] = _get_value($device_status_string,"Bcast:");
	$device_status["mask"] = _get_value($device_status_string,"Mask:");
	$device_status["mtu"] = _get_value($device_status_string,"MTU:");
	$device_status["Metric"] = _get_value($device_status_string,"Metric:");
	$device_status["tx"] = _get_value($device_status_string,"TX bytes:");
	$device_status["rx"] = _get_value($device_status_string,"RX bytes:");

	return $device_status;
}

/****** 3 ************************************
* device, inet addr, Bcast, mask,  in an associative array with device name as key
* CHECK: Posibly remove
**********************************************/
function get_ethernet_status(){

	$devices_data = array();
	$devs = get_ifs();
	foreach($devs as $device){
		$devices_data[$device] = get_ethernet_devstatus($device);
	}

	return $devices_data;
}

//basos ADD
/**** 4 *****************************************
*	Device UP/ DOWN VIEW
***********************************************/
function is_device_up($netdev) {
	global $BIN_LINUX;
	$ifconfig_cmd = $BIN_LINUX['ifconfig_bin'] . " -s";
	$ifconfig_output = wexec($ifconfig_cmd) ;
	//exec($ifconfig_cmd,$ifconfig_out,$res);
	//echo "DBG n=". $ifconfig_output ."//s=". $netdev . "//r=". $res;
	if (strpos($ifconfig_output, $netdev) === false ) {
	//if (array_search($netdev,$ifconfig_out) == 0) {
		return false;
	}
	else {
		return true;
	}
}

/**** 5 ***************************************
* Bring interfaces up or down
***********************************************/
function control_device($netdev,$up) {
	global $BIN_LINUX;
	$ifconfig_cmd = $BIN_LINUX['s_ifconfig_bin'] . " " . $netdev . ($up == true ? " up" : " down");
	return wexec ($ifconfig_cmd, $out);
}

/**** 6 *******************************************
* A wrapper for ifconfig basic setup, Config vars are optional
*	and are elements of $sets array. Possible values
*	$sets['mtu','ipaddr','bcast','mask']
*	Return Error string on error or true on success
* Transported from ifsettings.php
****************************************************/
function update_ifdev_settings($dev,$sets) {
	global $BIN_LINUX;
	$if_par = array($dev);

	if (isset($sets['ipaddr'])) {
			$if_par[] = $sets['ipaddr'];
	}
	if (isset($sets['mask'])) {
			$if_par[] = "netmask";
			$if_par[] = $sets['mask'];
  }
	if (isset($sets['bcast'])) {
			$if_par[] = "broadcast";
			$if_par[] = $sets['bcast'];
	}
	if (isset($sets['mtu'])) {
			$if_par[] =  "mtu";
			$if_par[] = $sets['mtu'];
	}

	$route_res = wexecp($BIN_LINUX['s_ifconfig_bin'], $if_par, $out, false, "2>&1");
	if (!$route_res)
			$route_res = implode ("\n",$out);
	return $route_res;
}


/***************************************
 ******* ROUTES API ********************
 **************************************/

/**** 1 *************************************
* Get Routing Table :each element of routes is an associative array with key the attribute name
* A[index]['destination','gateway','netmask','flags','metric','ref','use','iface']
* Transported from ifsettings.php
*********************************************/
function get_routing_table(){
	global $BIN_LINUX;
	$route_cmd = $BIN_LINUX['route_bin']." -n";
	wexec($route_cmd,$route_strings);
	$index=0;
	foreach($route_strings as $route_string_index => $route_string){
		if($route_string_index == 0 || $route_string_index ==1)
			continue;
		list($routes[$index]["destination"],
			$routes[$index]["gateway"],
			$routes[$index]["netmask"],
			$routes[$index]["flags"],
			$routes[$index]["metric"],
			$routes[$index]["ref"],
			$routes[$index]["use"],
			$routes[$index]["iface"]) = sscanf($route_string, "%s %s %s %s %s %s %s %s");
		$index++;
	}
	return $routes;
}

/***** 2 **************************************
* Add a new route for target/netmask with optional gw and/or device
* Return Error string on error or true on success
* Transported from ifsettings.php
**********************************************/
function save_new_route($target,$netmask,$gw,$dev) {
		global $BIN_LINUX;
		$route_par = array("add", "-net", "$target", "netmask", "$netmask");
		if ($gw != "") {
			$route_par[] = "gw";
			$route_par[] = $gw;
		}
		if ($dev != "") {
			$route_par[] = "dev";
			$route_par[] = $dev;
		}
		$route_res = wexecp($BIN_LINUX['s_route_bin'],$route_par,$out, false,  "2>&1");
		if (!$route_res)
			$route_res = implode ("\n",$out);
		return $route_res;
}

/*** 3 *******************************************
*   Delete a route to target/netmask/dev
*   Return Error string on error or true on success
* Transported from ifsettings.php
**************************************************/
function delete_route($target,$netmask,$dev) {
		global $BIN_LINUX;
		$route_par = array("del", "-net", $target, "netmask", $netmask, "dev", $dev);
		$route_res = wexecp($BIN_LINUX['s_route_bin'], $route_par, $out, false, "2>&1");
		if (!$route_res)
			$route_res = implode ("\n",$out);
		return $route_res;
}

/*** 4 *******************************************
*   Send a DHCP request on device $devname
*   Return Error string on error or true on success
**************************************************/
function ask_dhcp_ip( $devname ) {
    global $BIN_LINUX ;

    $pars = array("-1", $devname);
    $res = wexecp($BIN_LINUX['s_dhclient_bin'], $pars, $out, false, '2>&1');
    $out = array_slice($out, -4);
    array_unshift($out, "Last 4 lines of output:");
    if (!$res)
        return implode ("<br />\n",$out);
    return true ;
}




?>
