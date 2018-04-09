<?php
if(strpos($_SERVER['PHP_SELF'],basename(__FILE__)))
	require "../include/trap.php";


/**
 * split_sql
 * splits up a standard SQL dump file into distinct
 * sql queryies
 */
function split_sql($sql) {
        $sql = trim($sql);
        $sql = preg_replace("/\n#[^\n]*\n/", "\n", $sql);
        $buffer = array();
        $ret = array();
        $in_string = false;
        for($i=0; $i<strlen($sql)-1; $i++) {
                if($sql[$i] == ";" && !$in_string) {
						//end sql
                        $ret[] = substr($sql, 0, $i);
                        $sql = substr($sql, $i + 1);
                        $i = 0;
                }
                if($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
						//end quote
                        $in_string = false;
                }
                elseif(!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset($buffer[0]) || $buffer[0] != "\\")) {
						//start quote
                        $in_string = $sql[$i];
                }
                if(isset($buffer[1])) {
                        $buffer[0] = $buffer[1];
                }
                $buffer[1] = $sql[$i];
        }
        if(!empty($sql)) {
                $ret[] = $sql;
        }
        return($ret);
} // split_sql

/*** insert_db
 * Parses an sql file and executes the queries
 * Most code from ampache project
 */
function insert_db($link)
{
//	$data = mysqli_fetch_row($db_results);
//	$mysqli_version = substr(preg_replace("/(\d+)\.(\d+)\.(\d+).*/","$1$2$3",$data[0]),0,3);
//	$sql_file =  ($mysqli_version < '500') ? 'sql/ampache40.sql' : 'sql/ampache.sql';

/* Attempt to insert database */
	$errors = array();
	$sql_file = dirname(__FILE__)."/mysql.sql";
	$query = @fread( fopen($sql_file, "r"), filesize($sql_file));
	if ($query === false) {
		$errors[] = $lang['inst']['errdbsqlr'];
		return $errors;
	}
	$pieces  = split_sql($query);
	for ($i=0; $i<count($pieces); $i++) {
		$pieces[$i] = trim($pieces[$i]);
		if(!empty($pieces[$i]) && $pieces[$i] != "#") {
			if (!$result = mysqli_query ($link, $pieces[$i])) {
				$errors[] =  $pieces[$i] . "<br />". mysqli_error($link) ;
			} // end if
		} // end if
	} // end for
	return $errors;
}

echo "<H2>". $lang['inst']['mddb'] . "</H2>";

@$modifier = $_GET['modif'];
if ($modifier == "create") {
	//litle popup to create user/database

	if (!empty($_POST['dbadmusername']) && !empty($_POST['dbadmpassword'])) {
		$con = @mysqli_connect($_SESSION['USERS_DBHOST'],$_POST['dbadmusername'],$_POST['dbadmpassword']);
		if (!$con) {
			echo "<p class='error'>" . $lang['inst']['errdbadm'] ."</p>";
		}
		else {
			$sql = "create database if not exists ".mysqli_real_escape_string($con, $_SESSION['USERS_DB']);
			$result = mysqli_query($con, $sql);
			if (! $result) {
				echo "<p class='error'>" . $lang['inst']['errdbadminst'] . "<br />". mysqli_error($con) ."</p>";
				die();
			}
			$sql = "grant all on ".mysqli_real_escape_string($con, $_SESSION['USERS_DB']) . ".* TO ". mysqli_real_escape_string($con, $_SESSION['USERS_DBUSER']) . " identified by '". mysqli_real_escape_string($con, $_SESSION['USERS_DBPASS']) ."'";
			$result = mysqli_query($con, $sql);
			if (! $result) {
				echo "<p class='error'>" . $lang['inst']['errdbadminst'] . "<br />". mysqli_error($con) ."</p>";
				die();
			}
			$sql = "FLUSH PRIVILEGES";
			$result = mysqli_query($con, $sql);

			echo "<p class='okp'>" . $lang['inst']['dbadmok'] ."</p>";
		}
	} // !empty adm username
	echo "<p>". $lang['inst']['dbadm']. "</p>";
?>
	<form name="db" method="post" action="<?php echo $_SERVER['PHP_SELF']?>?mode=db&modif=create">
	<table>
	<tr><td>
	Username: <input type="text" name="dbadmusername"><br \>
	</td></tr><tr><td>
	Password: <input type="password" name="dbadmpassword"> <br \>
	<input type="submit" value="submit">
	</td></tr>
	</table>
	</form>
<?php
	die(); //we self destruct
}

if (!is_readable("./config/config.php")) {
	echo "<p class='error'>".$lang['inst']['errdbnoconf']. "</p>";
	die();
}


// Version TRAP
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	echo "<p class='error'>".$lang['general']['errver']."<br />".$land['inst']['errver']."</p>";
	die() ;
}



require_once "./config/config.php";
require "./install/update.class.php";

if ($C_use_mysql != true) {
	echo "<p>".$lang['inst']['nomysql']."</p>";
	$goon = true;
}
else {

// Mysqli extension
if (!extension_loaded('mysqli')) {
	echo "<p class='error'>".$lang['inst']['errmysqli']."</p>";
	die() ;
}

if (@! ($link = mysqli_connect($C_USERS_DBHOST, $C_USERS_DBUSER, $C_USERS_DBPASS)) || !mysqli_select_db($link, $C_USERS_DB)) {
	echo "<p class='error'>" .$lang['inst']['errdb'] ." </p>";
	die();
}


$sql = "show tables";
$result = mysqli_query($link, $sql);

$goon = false ; //are we not clear to procceed
if (mysqli_num_rows($result) > 0 && $modifier !== "overwrite") {
	//assume updating
	Update::display_version($link);

	if (Update::need_update($link)) {
		echo "<p>" . $lang['inst']['dbupdate'] ."</p>";
		Update::display_update($link);
		//echo "<div align='center'><div align='left'>";

		//echo "</div></div>";
		if ($modifier == "apply") { // User Confirmed
			$goon = Update::run_update($link);
			if (!$goon) {
?>
		<form name="overupdate" action="<?php echo $_SERVER['PHP_SELF']?>?mode=db&modif=overwrite" method="post">
		<input type="submit" value="<?php echo $lang['inst']['overwritedb']?>" >
		</form>
<?php
			} //! goon
			else {
				echo "<p>".$lang['dict']['succ'];
			}
		} //modif apply
		else { //confirmation form
			$goon = false ; //no we are not ready yet....
?>
		<form name="update" action="<?php echo $_SERVER['PHP_SELF']?>?mode=db&modif=apply" method="post">
		<input type="submit" value="<?php echo $lang['general']['apply']?>" >
		</form>
<?php
		}
	} //end need update
	else {
		echo "<p>" . $lang['inst']['dbnoupd'] ."</p>";
		$goon = true;
	}
} //end num_rows
else {
	//brand new database selection
	if ($modifier == "overwrite") {
		$sql = "show tables";
		$result = mysqli_query($link, $sql);
		while ($row = mysqli_fetch_row($result)) {
			$sql = "drop table ".$row[0];
			mysqli_query($link, $sql);
		}
		$modifier = "apply";
	}
	if ($modifier == "apply") {
		$errors = insert_db($link);
		if (count($errors)) {
			echo "<p class='error'>". $lang['inst']['errdbinst'] ;
			echo "<ul>";
			foreach ($errors as $error) {
				echo "  <li>$error</li>";
			}
			echo "</ul></p>";
			$goon = false;
		}
		else {
			echo "<p>".$lang['dict']['succ'];
			$goon = true;
		}

	}
	else {
		echo "<p>" . $lang['inst']['dbcreate'] ."</p>";
		$goon = false ; //no we are not ready yet....
?>
		<form name="update" action="<?php echo $_SERVER['PHP_SELF']?>?mode=db&modif=apply" method="post">
		<input type="submit" value="<?php echo $lang['general']['apply']?>" >
		</form>
<?php
	} //end else apply
} //end elsemysqlnumrows
} //end if use mysql


?>
