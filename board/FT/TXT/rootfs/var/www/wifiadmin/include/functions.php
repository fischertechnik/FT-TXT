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

/* API generator. System abstraction infastracture 
 NOTE: If you want to use only a function in funccommon.php you SHOULD include
       that file for simplycity. 
	SAFE to include many times
*/

/***** ASUMED valid config.php and router_init.php inclusion *************/
require_once("./config/config.php");

require_once("funccommon.php");
if (!isset($router_name)) {
	// THIS should never happen i.e. think before including functions.php
	die ("<h3>BUG : No router Configured. Please Report.".$_SERVER['PHP_SELF']."</h3>");
}

require_once("func_${SYSFLAVOR}.php");







