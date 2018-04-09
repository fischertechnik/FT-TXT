<?php /*+-------------------------------------------------------------------------+
  | Copyright (C) 2008 Korkakakis Nikos (korkakak@ceid.upatras.gr)          |
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
  | Based on previous work of 						    |
  | - panousis@ceid.upatras.gr	- Panousis 8anos			    |
  | - dimopule@ceid.upatras.gr	- Dimopoulos 8umios   			    |
  +-------------------------------------------------------------------------+*/

/*
 * Please USE ONLY CONSTANT-STATIC STRINGS
 * *** Avoid hrefs in here as they can increase the propability a typo error to break the functionality***
 */


/*
english dict germandict etc...
*/
$englishdict = array (
	'hello' 	=> 'Hello',
	'wb'  		=> 'Welcome',
	'version' 	=> 'Version',
	'error'		=> 'Error',
	'edit'		=> 'Edit',
	'delete'	=> 'Delete',
	'enable'	=> 'Enable',
	'full'		=> 'Full',
	'yes'		=> 'Yes',
	'no'		=> 'No',
	'user'		=> 'User',
	'ok'		=> 'Ok',
	'info'		=> 'Info',
	'essid'		=> "ESSID",
	'nname'		=> "Nickname",
	'mode'		=> "Mode",
	'channel'	=> "channel",
	'br'		=> "Bit Rate",
	'ap'		=> "AP",
	'sensitivity'	=> "Sensitivity",
	'rl'		=> "Retry limit",
	'rts'		=> "RTS threshold",
	'fts'		=> "Fragmentation thresold",
	'to'		=> "to",
	'eillchar'	=> "contains illegal characters, ingored",
	'pm'		=> "Power Management",
	'type'		=> "Type",
	'mac'		=> "MAC",
	'uploaded'	=> "Uploaded",
	'downloaded'	=> "Downloaded",
	'udratio'	=> "Upld/dwnld ratio",
	'signal'	=> "Signal",
	'noise'		=> "Noise",
	'reset'		=> "Reset",
	'submit'	=> "Submit",
	'last'		=> "Last",
	'txpower'	=>	"Tx power",
	'channel'	=>	"Channel",
	'succ'		=> "Succesfull!",
	'failed'	=> "Failed...",
	'name'		=> "Name",
	'goon'		=> "Go on",
	'here'		=> "Here",
	'seconds'		=> "seconds",
	'recheck'	=>	"Recheck",
	'back'		=>	"Back",
	'checking'	=>	"Checking",
	'missing'	=>	"Missing",
	);

/*
The main selectable object provided by this file
*/

$lang = array (
	'lang' 		=> 'english',
/*
Strings from confirm_account.php
*/
	'confirm_acc' 	=> array (
		'success' 	=> "You have successfuly confirmed your email address.Your account is now activated.You may login ",
		'error' 	=> "There is no token to confirm, you probably got here by mistake"
		),
/*
Strings from index.php
*/
	'index'		=> array (
		'running' 	=> "Running on",
		'nodename' 	=> "Node name",
		'nodehostn'	=> "Node hostname",
		'nodedescr'	=> "Node Decription",
		'nownic' 	=> "# wireless NICS",
		'uptime' 	=> "Uptime",
		'loginhere' 	=> "Login to WiFiAdmin",
		'uname' 	=> "Username",
		'upass' 	=> "Password",
		'loginbutton' 	=> "Login",
		'forgotpass' 	=> "Forgot your password? ",
		'checksys'	=> "Check System Bindings ",
		'bincheckmsg'	=> "System bindings Status. Note that you will need to install missing items for wifiadmin to be functional",
		'bincheck'	=> "System Binaries Checking.. ",
		'sudcheck'	=> "Sudo (privileged) execution checking..",
		'errbinfat'	=> "This *will* break things",
		'errbinopt'	=> "This *may* break things",
		'errbininf'	=> "This *will* render some information messages useless",
		'warnjs'	=> "Note: Javascript has to be enabled for wifiadmin to be fully functional",

		),
/*
Strings from header.php
*/
	'header' 	=> array (
		'loggedin' 	=> "You are logged in as",
		'login'	 	=> "login ",
		'register' 	=> ", or register to our community ",
		'edprofile' 	=> "[Edit profile]",
		'wstatus' 	=> "Wireless Status",
		'wsettings'	=> "Wireless Settings",
		'esettings'	=> "Ethernet Settings",
		'wsecurity'	=> "Wireless Security",
		'wcommunity'	=> "User Community",
		'edusers'	=> "Edit users",
		'managerout'	=> "Manage Routers",

		),
/*
Strings from copying.php
*/
	'copying' 		=> 'Cannot find GPL licence file, download it from  <a href="http://www.gnu.org">www.gnu.org</a>',

/*
Strings from forgot password
*/
	'fpass' 	=> array (
		'usemysql'	=> "This feature is only available when using mysql.<br \>Ask your sysadmin to change your password.",
		'eemails' 	=> "This system is not configured to send emails. Check config.php" ,
		'forgot'	=> "If you've forgotten your username or password, just enter the email address that you registered with, and your password will be emailed to you immediately.",
		'emailaddr'	=> "Your Email Address:",
		'welpass'	=> "Your WiFiadmin Password",
		'emailbody1'	=> "Below is the username and password information you requested.\n\n",
		'unknownemail'	=> "We could not find the email address you entered. Perhaps you registered with a different email address.",
		'error'		=> "An error occured during email sending. Please notify an admin to address the issue.",
		'passsent'	=> "Your username and password have been emailed to you"
		),

/*
ifsettings.php NOT FINISHED
*/

	'nicsetup'	=> array (
		'enetmask'	=> "Invalid netmask",
		'eip'		=> "Invalid ip address",
		'eoctet'	=> "Invalid octet",
		'title'		=> "Ethernet Settings",
		'eperm'		=> "You have no permission to access this section of WiFiAdmin"
		),

/*
footer.php
*/
	'footer'	=> array (
		'pagerendered'	=> "Page rendered in"
		),

/*
iwsecurity.php
*/
	'iwsec'		=> array (
		'eperm'		=> "You have no permission to access this section of WiFiAdmin",
		'applychng'	=> "Applying changes for device",
		'aclpolicy'	=> "Access list policy . . .",
		'addmacs'	=> "Adding MACs . . .",
		'delmacs'	=> "Deleting MACs. . .",
		'currpol'	=> "Current Access list policy",
		'secsett'	=> "Security settings for device",
		'nonmastermode' => "The are no security features for non-Master modes yet.",
		'addmacsacl'	=> "Add new MACs to ACL here, use ",
		'adddelimiter'	=> " as a delimiter for multiple MACs",
		'choose'	=> "choose",
		'selopen'	=> "open",
		'selallow'	=> "allow",
		'seldeny'	=> "deny",
		'selsubmit'	=> "Commit changes",
		'title'		=> "Wireless Security Settings",
		'curracl'	=> "Current Access List"
		),

/*
register.php
*/
	'register'	=> array (
		'eperm'		=> "Users are not allowed to register themselves (contact your cell administrator) "
		),

/*
users_edit.php
*/
	'useredit'	=> array (
		'userlist' 	=> "Users list",
		'addnewuser'	=> "Add a new user",
		'action'	=> "action",
		'regnewusr'	=> "New User Registration",
		'enousers'	=> "No registered users found.",
		'ulist'		=> "Users list",
		'adduser'	=> "Add a new user",
		'edituser'	=> "Edit User",
		'uname'		=> "Username *",
		'pass'		=> "Password *",
		'enopass'	=> "You did not supply a password.",
		'repass'	=> "Retype password *",
		'mail'		=> "Your email *",
		'macaddr'	=> "MAC address *",
		'ipaddr'	=> "IP address *",
		'subnet'	=> "Name your subnet",
		'fname'		=> "First Name",
		'lname'		=> "Last Name",
		'phone1'	=> "Landline Number",
		'phone2'	=> "Cellular Number",
		'ant'		=> "Antenna",
		'windid'	=> "Wind ID number",
		'services'	=> "Services",
		'comment'	=> "Your Comment",
		'submituser'	=> "Save User",
		'enouser'	=> "You did not supply a username",
		'enopassmatch'	=> "Password fields do not match.",
		'enoemail'	=> "You did not supply an email.",
		'enomac'	=> "You did not supply a MAC address and/or IP address.",
		'enoperm'	=> "You do not have permission to edit this user",
		'enopermadd'	=> "You don't have permission to add a user",
		'elockout'	=> "You do not have permission to delete this user or you may be locked out",
		'wdeluser'	=> "You are about to delete user",
		'ruser'		=> "enabled, just a moment please",
		'rdel'		=> "User deleted successfuly, just a moment please",
		'rsucc'		=> "User information updated successfuly, just a moment please"
		),

/*
dict array Used for single word entries in different context throughout the application
*/


		'dict' => $englishdict,
/*
iwsettings.php array...

IF YOU WANT TO TRANSLATE THE FOLLOWING STATEMENTS PLEASE CHANGE $englishdict with the local lang definition eg $deutchdict as stated @ line 17 in this file:

for instance if the declaration is stated

$deutchdict = array (
	'hello' 	=> 'Hallo',
	'wb'  		=> 'Wilkommen',
	'version' 	=> 'Version',

	... blah blah blah ...

	)

you have to use the variable $deutchdict

*/

	'iwset'		=> array (
		'chngset'	=> "Changing settings for device",
		'chngESSID'	=> "Changing ".$englishdict['essid']." ". $englishdict['to'],
		'chngnick'	=> "Changing ".$englishdict['nname']." ". $englishdict['to'],
		'chngmode'	=> "Changing ".$englishdict['mode']." ". $englishdict['to'],
		'chngchannel'	=> "Changing ".$englishdict['channel']." ". $englishdict['to'],
		'changeAP'	=> "Changing ".$englishdict['ap']." ". $englishdict['to'],
		'changebr'	=> "Changing ".$englishdict['br']." ". $englishdict['to'],
		'changesen'	=> "Changing ".$englishdict['sensitivity']." ". $englishdict['to'],
		'changeretlim'	=> "Changing ".$englishdict['rl']." ". $englishdict['to'],
		'changerts'	=> "Changing ".$englishdict['rts']." ". $englishdict['to'],
		'changefts'	=> "Changing ".$englishdict['fts']." ". $englishdict['to'],
		'changepm'	=> "Changing ".$englishdict['pm']." ". $englishdict['to'],
		'changetxp' => "Changing transmit power to ",
		'errcharfts'	=> $englishdict['fts']." ".$englishdict['eillchar'],
		'errcharrts'	=> $englishdict['rts']." ".$englishdict['eillchar'],
		'errchardev' 	=> "Illegal characters found in device name string:",
		'errcharAP'	=> $englishdict['ap']." ".$englishdict['eillchar'],
		'errcharbr'	=> $englishdict['br']." ".$englishdict['eillchar'],
		'errcharretlim'	=> $englishdict['rl']." ".$englishdict['eillchar'],
		'errcharcha'	=> $englishdict['channel']." ".$englishdict['eillchar'],
		'errchartxp'	=> "Tx-power ".$englishdict['eillchar'],
		'enoscan'	=> "Device does not support scanning",
		'noscanres'	=> "No scan results",
		'scanres'	=> "Scan results for device",
		'curdevset'	=> "Current settings for device",
		'notall'	=> "Some values may not have been applied.",
		'AP'		=> "Access Point:",
		'sitesurvey'	=> "Site Survey",
		'title'		=> "Wireless Settings",
		'nochanges'	=> "No changes made"
		),
/*
iwstatus.php
	STRINGS
*/



	'iwstat'	=> array (
		'enocontrol'	=> "Error Controling interface",
		'ws'		=> "Wireless Status",
		'devstat'	=> "Device Status",
		'assocmacs'	=> "Assosiated MACs list on Master-mode device",
		'noassocs'	=> "There are currently no associations on",
		'unresolved'	=> "unresolved",
		'notreg'	=> "not registered",
		'eusernum'	=> "number of users graph unavailable",
		'etraff'	=> "traffic graph unavailable",
		'esnoise'	=> "singal-noise graph unavailable",
		'erate'		=> "rate graph unavailable",
		'daily'		=> "daily",
		'weekly' 	=> "weekly",
		'monthly'	=> "monthly",
		'yearly' 	=> "yearly",
		'sgraph'	=> "select graph",
		'rmac'		=> "Remote MAC",
		'lstatus'	=> "link status",
		'devmode'	=> "This device's mode",
		'notsupp'	=> "is not supported",
		'showrtg'	=> "[ Show Realtime Traffic Graph ]",
		'kernmsg'	=> "lines of kernel wireless messages",
		'vwl'		=> "View Wireless Logs",
		'enoperm'	=> "You have no permission to perform this action",
		'errcgraph'	=> "Graphs dir could not be created",
		'hostapnote' => "Note: Per association traffic stats only supported under hostap",
		),



/*
users.php
*/
	'users' 	=> array (
		'title'		=> "User Management",
		'guestuser'	=> "This is the guest user account",
		'submit'	=> "Save changes",
 		'nouname'	=> "No username",
		'aunameexist'	=> "A user with username",
		'backto'	=> "Back to user list",
		'eexists'	=> "already exists",
		'nopass'	=> "No Password",
		'seluser'	=> "Select a user to edit or delete",
		'fillpass'	=> "Please fill both password fields",
		'epasschar'	=> "Password contains illegal character :",
		'eunamechar'	=> "Username contains illegal character :",
		'enoperm'	=> "This operation is not allowed. You will have no user with permission to edit your users"
		),
/*
include/community_functions.php
*/
	'cf'		=> array (
		'msg2' 		=> "Thank you for signing up. This email has been sent to you automatically. Please click the link below in order to confirm your account.\n\n",
		'tx'		=> "\n\nThanks,\n",
		'eemailexist'	=> "Email address already exists. Please use another one.",
		'eusernameexist'=> "Username already exists. Please use another one.",
		'eusernamenexist' => "Username does not exist. ",
		'eusrreg'	=> "User registration operation failed:",
		'noconnect'	=> "could not connect",
		'noselect'	=> "could not select",
		'eusrreg2'	=> "User priv registration operation failed:",
		'confirm'	=> "In order to confirm that the email address you entered is valid,
		 you have been automatically sent an email . Please follow its instructions.",
		'success'	=> "User registration was succesful! Note that user data can be viewed by everyone.",
		'msg'		=> "Welcome to Wifiadmin, please confirm your email address"
		),

/*
include/passwd_functions.php
*/
	'pf'		=> array (
		'enotok' => 'No token exists for such a user.',
		'eunconf' => 'This account has not yet been confirmed.Check your email.',
		'eauth'	=> 'Wrong username and/or password. Authentication failed.',
		'sendconf' => ' to resend a confirmation email.',
	),

/*
install.php
*/
	'inst'		=> array(
		'title'		=> "WifiAdmin installation wizard",
		'install'	=>	"Installation",
		'update'	=>	"Update",
		'step'	=> "Step",
		'mdlang'	=> "Language Selection",
		'mdconfig'	=> "Configuration File Generation",
		'mddb'		=> "Database Update/Creation",
		'mdrouter'	=> "Router(s) Registration",
		'confighdr_c'	=> "
/*
This is an automatically created file.
For your convenience please use install.php to edit this file
*/",
		'erredit_config'	=> 'Configuration file ("config/config.php") is ok. If you deliberately want to reconfigure it you need to delete it or manualy open it, add the line "$C_config_edit_once = true;" somewhere in the file and reload this page. Please notice that there are serious security risks when someone unauthorized can edit the config file. We can not base on the authentication schema used currently in wifiadmin because this is based on the configuration file. Edit at your own risk.',
/** Option Descriptors ***/
		'status_refresh'	=> "How often should interfaces status refresh (seconds)\nSet 0 (zero) to refresh only ondemand",
		'show_console'	=> "Should the console be visible at the bottom of the\npage? Usefull to check about the calls done to the\nsystem",
		'resolve'	=> "Should iwstatus resolve the assosiated clients IPs to\nDNS names? Might be a little slower, but its nicer",
		'resolve_timeout'	=> "The timeout for reverse DNS lookups (in secs)",
		'count_time'	=> "Should the application calculate the running time?\n(php render time: the time that takes the web server\nto create the page)",
		'timezone' => "Specify the timezone to use for date & time php functions",
		'ses_regenerate_graphs'	=> "Minimum time between regeneration of graphs for a\nsession (seconds)",
		'gen_graphs'	=> "Do you want the application to create rrd graphs?",
		'rrd_database_path'	=> "Select the directory where to store the rrd data",
		'rrdtool_bin'	=> "Select the rrdtool binary (running on server)",
		'graphs_path'	=> "Select the directory where to store the graphs",
		'rtgraph_width'	=> "What should be the real time graph width\n(in pixels) ?",
		'rtgraph_height'	=> "What should be the real time graph height\n(in pixels) ?",
		'use_mysql'		=> "Are you intrested in using mysql db as a data storage\nmechanism? Much better/faster/safer than simple file storage.",
		'USERS_DBHOST'	=> "Where is the db located? (IP or FQDN)\nIf you selected  no at the use mysql question then you\ncan ignore this",
		'USERS_DBUSER'	=> "Under which user should we connect to the db?\nIf you selected  no at the use mysql question then you\ncan ignore this",
		'USERS_DBPASS'	=> "Please type the users password\nIf you selected  no at the use mysql question then you\ncan ignore this",
		'USERS_DB'	=> "Which db should we use to store data?\nIf you selected  no at the use mysql question then you\ncan ignore this",
		'passwd_filename'	=> "Select where to store the password file.\n***SECRITY WARNING*** Place this file outside the webroot\nIf you selected  no at the use mysql question then you\ncan ignore this",
		'send_emails'	=> "Should the WifiAdmin be allowed to send emails?\n(to remind passwords confirm accounts etc...)",
		'confirm_new_account'	=> "Should the wifiadmin ask for a valid email and check\nthe validity of that email address for that before\naccepting the client?",
		'users_register_themselves'	=> "Should the users be able to self register?\n Note that the default permissions for these users are \nrather restrictive!",
		'web_master'	=> "Webmasters's Name",
		'web_master_EMAIL' => "Webmasters's Email",

/** Other **/
		'par_name'		=>	"Setting",
		'par_desc'	=> "Description",
		'mailsubject' 	=> "This is a test mail",
		'mailbody'	=> "This is a test mail, used to check whether or not your mail subsystem works.\nIf you received this then everything works great!\nHorray!!!",
		'ldcnf'	=> "(Re)load Existing Config file",
		'lddef'	=>	"Load Defaults",
		'selectall'	=> "Highlight all of the above text",
		'copy2clip'	=> "Copy contents to clipboard",
		'oksavefil'	=> "Config file saved",
		'okgenconf'	=>	"Config file successfully generated",
		'dbadm'		=> 	"You can give us a database admin account (root?). He will create the selected username/password/database for you. Be sure for the data you suplied at the configuration parameters for the database before submitting. Also note that these credentials will be holded temporarily and not saved anywhere on the disk",
		'createdb'	=>	" to create username/database",
		'dbadmok'	=>	"Database/username/password created succefully. You can now close this window and procceed to the installation",
		'dboldv'	=>	"Notice : You are propably updating from an old database with no version assigned<br /> We will try to elevate you to the latest version starting at the db schema found from wifiadmin version 0.0.2",
		'dbversion'	=> "DB Version:",
		'dbnoupd'	=> "You are already to the latest database version. No updates Needed",
		'dbcurv'	=>  "Current DB Version",
		'dbnocurv'	=>	"Current DB is not version tagged. Make sure you have selected the correct db and procceed",
		'dbupdate'	=>	"Your database version is deprecated. You need to update",
		'dbupd010001'	=> 	' - Starting Version Count from now and on</br />'.
				' - Added table global_options<br />'.
				' - Added table user_options <br />'.
				' - Altered tables privileges. Added view_status_ext priv<br />',
		'dbupd010003'	=>	' - Altered table privileges. Added manage_routers priv. Deleted add_users priv<br />',
		'dbcreate'	=>	' Selected Database is empty. Tables will now be created',
		'dbverchain'	=> "We estimate that the following updates need to be done",
		'overwritedb'	=>	"Overwrite Database",
		'nomysql'	=> "There is no need to update the database since you have disabled mysql. <br /> Nevertheless NOTE that you will need to delete your passwd file for new privileges to take place. If you want fully automated updated and many more security feautures you are encouraged to use mysql.",
		'rtreginst'		=> " Selected Router is not registered. The registration proccess requires some things that MIGHT need root access in the local machine (apache user should have a home directory). Also it WILL require root access on the router machine (either local or remoter)(we need to register a user with sudo priviliges for specific binaries).<br/> For these reasons the registration proccess should be made from a user shell on the local machine with the knowledge of the passwords. You can use the script <b>install/register_host.sh</b> on local machine (if it fails with permission denied try chmod u+x register_host.sh).You have to cd to the install dir for this (e.g. cd install) You can also consult README, SSH_NOPASSWD supplied files.<br/> When you are done please recheck here..",
		'rtsuccwrite'	=>	" Router configuration file succesfully written ",
		'rtdereg'		=> " Selected router is registered with local system. Please deregister it. You will need a shell and run <b>install/unregister_host.sh</b> or consult README, SSH_NOPASSWD and perform the reverse steps.",
		'rtderegw'	=> "NOTE: You *should* run <b>install/unregister_host.sh</b> (if not already) to be 100% sure that ssh and sudo settings were removed from router. Alternatively you can consult README, SSH_NOPASSWD and perform the reverse steps",
		'rtanother'		=> "Register another router?",
		'rtregistered'	=>	"Registered Routers",
		'rtname'		=>	"Router name",
		'rturl'			=>	"IP or FQDN",
		'rtsys'			=>	"System Flavor",
		'rtuser'		=>	"Username",
		'rtdesc'		=> 	"Description",
		'rtaccess'		=>  "Access Mode",
		'rtadd'			=>  "Add new Router",

/** Error **/
		'rterrdown'		=>	"ERROR: Host is unreachable. It can not be registered",
		'rterrdname'		=> "ERROR: Selected router name does not exist",
		'rterremptydata'	=> "ERROR: Required fields should be specified",
		'rterrname'		=> "ERROR: Specified router name is already in use. Please use another",
		'rterrsave'		=>	"ERROR: Couldn't write routers configuration file. <br /> Please copy the following file contents under config/routers.ini and recheck",
		'errvalidate'	=> "Some Fields have failed. Check them out.",
		'errvalmail'	=> "Could not sent email. Check the PHP's mail subsustem",
		'errvaldb'		=> "Selected database must exist.",
		'errvaldbcreds'	=> "Could not connect to the databse.Selected username/password must exist.",
		'errvalpasswd'	=>	"Could not write to passwd dir or passwd file. Please grant webserver user write access for the directory containing passwd or create an empty passwd file and grant write access to this file only",
		'errsavefil'	=> "Could not save config file",
		'errsavemsg'	=>  'You can copy the contents bellow and paste them to ./config/config.php under wifiadmin\'s main dir.<br/> ** NOTE that *NO* Characters should be placed before "&lt?php" and after "?&gt" (Not even a new line). You have been warned..<br/> Alternatively you need to give write access to the apache user for the ./config/ directory or the config/config.php file and configuration will be saved automatically if you resubmit the changes',
		'errloadfil'	=> "Config File found but some settings are not present. Please resubmit your changes (Are you updating?)",
		'errnofil'		=> "No config file found. Defaults Loaded..",
		'errmissfield'	=> "A required field is empty.",
		'errdbnoconf'	=>	"Configuration File not found. Please follow the instructions of the previous step and place the config file in the appropriate directory. You can refresh this page to procceed when ready",
		'errdbadm'		=> "The credentials you supplied are not valid on this machine. Verify their and the host name's validity",
		'errdbadminst'	=>	"The was an error modifying the sql server. Do you have grant,and create privileges ?",
		'errdb'			=> " Cannot connect to the database. Please go back to the previous step and set the database parameters correctly",
		'errdbver'		=> " Fatal Error During update. Database Problem. Cannot *Check* for global_options table existance..Update chain halted. Fix Manually. SQL Error:",
		'errdbupd1'		=> "Error During update proccess chain. Stuck at version: ",
		'errdbupd2'		=> ". Please make sure the selected database is released version wifiadmin one. If this is the case it is probably an updater error. Please repport and try to manually fix the db (phpmyadmin??). You can view the current database schema at install/mysql.sql. You can also overwrite the database with a clean schema. SQL Error: ",
		'errdbsqlr'		=> "FATAL: Cannot read sql file. Please report a BUG",
		'errdbinst'		=> "FATAL: There was error(s) while inserting tables in the database. Is mysql server up n running? Are your privs modified by anyone else?",
		'errrrdtool'	=> "ERROR: Specified RRD tool binary not found",
		'errver'		=> "You should consult README and procceed to manual installation",
		'errtzone' => "Invalid timezone. Click <a href=\"http://www.php.net/manual/en/timezones.php\" target=\"_blank\">here</a> for a list of supported timezones",
		'errmysqli' => "Mysqli php extension is should be enabled",
		),

/*
general array Used for larger strings that may be reused
*/
	'general' 	=> array (
		'moto' 		=> 'the <a href="./copying.php" class="anchor">free</a> wifi Web Interface',
		'tryagain'	=> 'Try again',
		'username'	=> "username",
		'password'	=> "password",
		'areyousure'	=> "are you sure?",
		'enoperm'	=> "You have no permission to access this section of WiFiAdmin",
		'commitchng'	=> "Commit changes",
		'invacc'	=> "You cannot access this page directly",
		'usemysql'	=> "This feature is only available when using mysql. Check config.php",
		'enowifs'	=> "No wireless Interfaces found",
		'enoifs'	=> "No network interfaces found",
		'clickhere' 	=> "Click Here",
		'apply'		=> "Apply changes",
		'errrouter'	=> "ROUTER SELECTION Error: Please verify the contents of config/routers.ini. Read the README for more information. Current router name:",
		'errsysfl'	=> "ROUTER SELECTION Error: Selected System Flavor is not supported. Check config/routers.php and read the README for more info. Current router name:",
		'mandatory'	=> "* starred entries are mandatory.",
		'errver'		=> "This feauture requires PHP5.3+. You are strongly encouraged to update (<a href='www.php.net/download.php'>Download</a>)",
		)
);
