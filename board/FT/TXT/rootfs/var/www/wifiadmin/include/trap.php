<?php
/** Include it at the very begining of files that are to be included and
	prevent unwanted code execution
	Prefix with this condition : if(strpos($_SERVER['PHP_SELF'],basename(__FILE__)))
***/
	if(isset($_SESSION))
		session_destroy();
?>
	<H1> You should not be here </H1>
	<H3> Your IP/Port: <?php echo $_SERVER['REMOTE_ADDR']."/".$_SERVER['REMOTE_PORT'];?></H3>
	<H3> Your Browser Fingerprint: <?php echo $_SERVER['HTTP_USER_AGENT'];?></H3>
	<H3> Request Time: <?php echo date("d/M/Y H:i",$_SERVER['REQUEST_TIME']);?></H3>
<?php
	exit();	
?>
