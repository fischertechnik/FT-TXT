<?php
/*

 Copyright (c) Ampache.org
 Modified to fit wifiadmin 's purposes by basOS (basos@users.sf.net)
 All rights reserved.

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License v2
 as published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

/**
 * Update Class
 * this class handles updating from one version of
 * wifiadmin to the next. Versions are a 6 digit number
 *  220000
 *  ^
 *  Major Revision
 *
 *  220000
 *   ^
 *  Minor Revision
 *
 * The last 4 digits are a build number...
 * If Minor can't go over 9 Major can go as high as we want
 *
 * Exported : need_update(),display_update(),display_version(),run_update()
 */
class Update {

	public static $versions; // array containing version information

	/**
	 * Update
	 * Constructor,
	 */
	function Update ( ) {

		$this->versions = $this->populate_version();

	} // constructor


/**
	 * need_update OK
	 * checks to see if we need to update
	 * ampache at all
	 */
	public static function need_update($link) {

		$current_version = self::_get_version($link);


		if (!is_array(self::$versions)) {
			self::$versions = self::populate_version();
		}

		/*
		   Go through the versions we have and see if
		   we need to apply any updates
		*/
		foreach (self::$versions as $update) {
			if ($update['version'] > $current_version) {
				return true;
			}

		} // end foreach version

		return false;

	} // need_update



	/**
	 * display_update OK
	 * This displays a list of the needed
	 * updates to the database. This will actually
	 * echo out the list...
	 */
	public static function display_update($link) {

		global $lang;
		$ret = "<p><b>". $lang['inst']['dbverchain'] . "</b><br />";
		$current_version = self::_get_version($link);

		if ($current_version === "000000") {
				$ret.= $lang['inst']['dboldv']."<br />";
		}
		if (!is_array(self::$versions)) {
			self::$versions = self::populate_version();
		}

		$ret .= "<ul>\n";

		foreach (self::$versions as $version) {

			if ($version['version'] > $current_version) {
				$updated = true;
				$ret.= "<li><b>".$lang['inst']['dbversion'].self::_format_version($version['version']) . "</b><br />";
				$ret.= $version['description'] . "</li>\n";
			} // if newer

		} // foreach versions

		$ret .= "</ul>\n";

		if (!$updated) { $ret.=$lang['inst']['dbnoupd']; }
		$ret .= "</p>";
		echo $ret;
	} // display_update


	public static function display_version($link) {
		global $lang;
		$current_version = self::_get_version($link);
		if ($current_version === false)
			$ver = "<p>".$lang['inst']['dbnocurv'] ."</p>";
		else
			$ver =  "<p>".$lang['inst']['dbcurv'] . " ".self::_format_version($current_version)."</p>";
		echo $ver;
	}

	/**
	 * run_update OK
	 * This function actually updates the db.
	 * it goes through versions and finds the ones
	 * that need to be run. Checking to make sure
	 * the function exists first.
	 */
	public static function run_update($link) {

		global $lang;
		/* Nuke All Active session before we start the mojo */
		$sql = "TRUNCATE session";
		$db_results = mysqli_query($link, $sql);

		// Prevent the script from timing out, which could be bad
		set_time_limit(0);


		$methods = array();

		$current_version = self::_get_version($link);

		if ($current_version === false) {
			echo "<p class='error'>".$lang['inst']['errdbver'].mysqli_error($link)." </p>";
			return false;
		}


		$methods = get_class_methods('Update');

		if (!is_array((self::$versions))) {
			self::$versions = self::populate_version();
		}

		foreach (self::$versions as $version) {

			// If it's newer than our current version
			// let's see if a function exists and run the
			// bugger
			if ($version['version'] > $current_version) {
				$update_function = "update_" . $version['version'];
				if (in_array($update_function,$methods)) {
					$success = call_user_func(array('Update',$update_function), $link);

					// If the update fails drop out
					if (!$success) {
						echo "<p class='error'>".$lang['inst']['errdbupd1'] . $version['version'].$lang['inst']['errdbupd2'] . mysqli_error($link). "</p>";
						return false;
					}
					else   {
						self::set_version($link, $version['version']);
					}
				} //in array
			} //ver>current
		} // end foreach version
		return true;
	} // run_update

	/**
	 * set_version OK
	 * This updates the 'update_info' which is used by the updater
	 * and plugins
	 */
	private static function set_version($link, $value) {

		$sql = "REPLACE global_options(`option`,`value`) VALUES ('db_version','$value')";
		$db_results = mysqli_query($link, $sql);

	} //set_version

	/**
	 * populate_version OK
	 * just sets an array the current differences
	 * that require an update ORDER MATTERS AND SHOULD BE ASCENTIVE
	 */
	private static function populate_version() {
		//TODO Modify for our needs
		/* Define the array */
		global $lang;
		$version = array();


		$version[] = array('version' => '010001','description' => $lang['inst']['dbupd010001']);
		$version[] = array('version' =>	'010003','description' => $lang['inst']['dbupd010003']);
		return $version;

	} // populate_version

	/**
 	 * update_010001
	 * This update tweaks the preferences a little more and make sure that the
	 * min_object_count has a rational value
	 */
	private function update_010001($link)
	{


	$sql = "
	CREATE TABLE `user_options` (
	`username` varchar(40) NOT NULL,
	`option` varchar(40) NOT NULL ,
	`value` varchar(380) NOT NULL ,
	PRIMARY KEY (`username`,`option`)
) ENGINE=MyISAM ";
		if (! mysqli_query($link, $sql))
			return false;


	$sql = "
	CREATE TABLE `global_options` (
	`option` varchar(40) NOT NULL ,
	`value` varchar(380) NOT NULL,
	PRIMARY KEY (`option`)
) ENGINE=MyISAM ";
		if (! mysqli_query($link, $sql))
			return false;

		$sql = "ALTER TABLE `privileges` ADD COLUMN `view_status_ext` varchar(10) NOT NULL default 'false' AFTER `view_status`";
		if (! mysqli_query($link, $sql))
			return false;
		$sql = "UPDATE `privileges` SET `view_status_ext` = 'false'";
		if (! mysqli_query($link, $sql))
			return false;

		$sql = "UPDATE `privileges` SET `view_status_ext` = 'true' WHERE `username` = 'admin'";
		if (! mysqli_query($link, $sql))
			return false;
		return true;

	} // update_010001

	/**
	 * update _010003
	*/
	private function update_010003($link)
	{


		$sql = "ALTER TABLE `privileges` ADD COLUMN `manage_routers` varchar(10) NOT NULL default 'false'";
		if (! mysqli_query($link, $sql))
			return false;
		$sql = "UPDATE `privileges` SET `manage_routers` = 'false'";
		if (! mysqli_query($link, $sql))
			return false;

		$sql = "UPDATE `privileges` SET `manage_routers` = 'true' WHERE `username` = 'admin'";
		if (! mysqli_query($link, $sql))
			return false;

		$sql = "ALTER TABLE `privileges` DROP COLUMN `add_users`";
		if (! mysqli_query($link, $sql))
			return false;
		return true;
	}

	/**
	 * get_version OK
	 * this checks to see what version you are currently running
	 * because we may not have the update_info table we have to check
	 * for it's existance first. Returns false on erro
	 */
	private static function _get_version($link) {

		/* Make sure that update_info exits */
		$sql = "SHOW TABLES LIKE 'global_options'";
		$db_results = mysqli_query($link, $sql);
		if (!$db_results) {return false; }
		// If no table
		if (!mysqli_num_rows($db_results)) {
			// They can't upgrade, they are too old
			return "000000";
		} // end if table isn't found
		else {
			// If we've found the update_info table, let's get the version from it
			$sql = "SELECT * FROM `global_options` WHERE `option`='db_version'";
			$db_results = mysqli_query($link, $sql);
			$results = mysqli_fetch_assoc($db_results);
			$version = $results['value'];
			if (strlen($version) < 6) return false;
		}
		return $version;
	} // get_version

	/**
	 * format_version OK
	 * make the version number pretty
	 */
	private static function _format_version($data) {

		$new_version = substr($data,0,strlen($data) - 5) . "." . substr($data,strlen($data)-5,1) . " Build:" .
				substr($data,strlen($data)-4,strlen($data));

		return $new_version;

	} // format_version



} // end update class
?>
