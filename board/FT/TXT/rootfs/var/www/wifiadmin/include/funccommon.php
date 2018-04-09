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
/*** Configuration file HAS to be created e.g. do not include into installation processes ***/

include_once("./config/config.php");
/*************************************
 * ROUTER PLATFORM INDEPENDED FUNCTIONS
**************************************/

// Used in wexec to append console data
function append_consoleln($pay) {
	global $C_show_console;
	if ($C_show_console == true) {
	$time = date("[H:i]");
	if (!isset($_SESSION['chistory']))
		 $_SESSION['chistory'] = $time.$pay;
	else
		$_SESSION['chistory'] .= "\n" .$time. $pay;
	}
}

/* **************************
	Execute System Command Wrapper
	TAKES acount SSH global var to execute remote commands
	**************************/
/* Arg List $cmd[,$outar]
   1 Arg execute command and return all output
   2 Args execute command, fill $outar array with every output LINE and return exit STATUS normalized to bool
     If $outar('forcelocal' = true  then execute all commands on the local machine, when true return output on each line
      in such case you should explicitly set $outar['use_outar'] to select the mode of op
*/
function wexec ($cmd, &$outar = null, $forcelocal = false ) {
	global $SSH;
	$nargs = func_num_args();
	if ($nargs >3)
		return false ;
	if ($nargs>1 && $outar === null)
		$outar = array();

	$use_outar = is_array($outar);

	if (isset($SSH) && !$forcelocal)  $cmd = $SSH . $cmd;
	exec($cmd,$outar,$res);
	append_consoleln($res == 0 ? "[V] ". $cmd : "<font color='red'>[X] ".$cmd."</font>");
	if ( ! $use_outar ) {
		// return all output in a string, no command status
		$cmd_output = implode ("\n",$outar);
		//remove leading, trailing spaces in output string
		return trim($cmd_output);
	}
	else {
		 // output outar, return true on success
		 return ($res == 0 ? true : false);
	}
}
/* **************************
	Execute System Command Wrapper with parameter escaping
	TAKES acount SSH global var to execute remote commands
	ALWAYS execute web input THRU this function
	**************************/
/* Arg List $cmd,$params[,$outar]
   2 Args execute command and return all output
   3 Args execute command, fill $outar array with exery output LINE and return exit STATUS
	$params can be a string for one parameter or an array for multiple parameters that will
	be concatenated with spaces and escaped atomically
*/

function wexecp($cmd, $params, &$outar = false, $forcelocal = false, $redirects = '') {
	$nargs = func_num_args();
	if ($nargs<2 || $nargs>5)
		return false;
	if ($nargs>2 && $outar === false)
		$outar = array();
	// prepare parameters
	if (is_array($params)) {
		$tpar = '';
		foreach ($params as $param) {
			$tpar .= " " . escapeshellarg($param);
		}
		$params = $tpar;
	}
	else {
		$params = " ".escapeshellarg($params);
	}

	return wexec($cmd.$params.($redirects ? ' '.$redirects: ''), $outar, $forcelocal);

}


/***************************
* Returns array of sysflavor names (suffixes of func_*.php files containing
* system abstraction API implementations for supported system flavors
****************************/
function get_sysflavors()
{
	$sys_avail = array();
	$files = scandir ( "./include/");
	foreach ($files as $key => $fname) {
		// clean default directory links
		if ($fname[0] == "." )
			continue;
		if (preg_match("/^func_(.*)"."\.php"."$/",$fname,$match))
			$sys_avail[] = $match[1];
	}
	return $sys_avail;
}

/************************************************
* Bytes to MB, GB, TB function                  *
*************************************************/
function ByteSize($bytes) {
    $size = $bytes / 1024;
    if($size < 1024){
        $size = number_format($size, 2);
        $size .= 'KB';
    } else {
        if($size / 1024 < 1024) {
            $size = number_format($size / 1024, 2);
            $size .= 'MB';
        } elseif($size / 1024 / 1024 < 1024) {
            $size = number_format($size / 1024 / 1024, 2);
            $size .= 'GB';
        } else {
            $size = number_format($size / 1024 / 1024 / 1024, 2);
            $size .= 'TB';
        }
    }
    return $size;
}

function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function error_echo($string)
{
	return "<p class=\"error\">".$string."</p>";
}

/************************************************
* freq2channel($freq)
* returns the channel # of a given frequency of 802.11
************************************************/
//TODO: Check 802.11a
function freq2channel($freq)
{
	$out = array("GHz");
	$freq = str_replace($out,"",$freq);
	//echo "DBG " . 1000*$freq;
	if (!is_numeric($freq))
		return -1;
	$freq*=1000;
	if ($freq>=2412 && $freq <= 2484) //802.11bg
		return ($freq - 2412)/5 + 1;
	elseif ($freq>=5170 && $freq<=5825 ) //802.11a
		return ($freq - 5170)/5 + 34;
	else return -2;
}
/************************************************
* channel2freq($channel)
* returns the frequency of a given channel of 802.11
************************************************/
/*UNSUSED for now function channel2freq($channel)
{
  if(is_int($channel))
    return ($channel+1 * 5) + 2.412;
  else return -1;
}*/


?>
