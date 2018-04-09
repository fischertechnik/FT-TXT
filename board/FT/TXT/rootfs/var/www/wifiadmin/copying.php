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
  | WifiAdmin: The Free WiFi Web Interface                                  |
  +-------------------------------------------------------------------------+
  | Send comments to                                                        |
  | - panousis@ceid.upatras.gr                                              |
  | - dimopule@ceid.upatras.gr                                              |
  +-------------------------------------------------------------------------+*/
include("./include/header.php");
$gpl = "./COPYING";
if (!file_exists($gpl))
	echo '<p class="error">'._('Cannot find GPL licence file, download it from  <a href="http://www.gnu.org">www.gnu.org</a>').'</p>';
else
	{
	$fp = fopen($gpl,"r");
	echo "<pre>".fread($fp,filesize($gpl))."</pre>";
	fclose($fp);
	}
include("./include/footer.php");
?>

