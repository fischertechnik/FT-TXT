<?php
/*
edit by korki;
deny the footer.php access directly
*/
if(strpos($_SERVER['PHP_SELF'],basename(__FILE__)))
	require "trap.php";

require_once "./config/config.php";
?>


</div><!-- menuright -->
</div><!--content-->
</div><!--wrapperin-->
<div id="footer">
<?php
if (($C_show_console == true && isset($_SESSION['chistory'])) && (!isset($installer_is_on)))
	{
?>
	<div id="console" name="console">
	<pre><?php echo $_SESSION['chistory']?></pre>
	<script type="text/javascript" src="include/scroll.js"></script>
	<script type="text/javascript">
		scrollElementToEndN ("console");
	</script>
	</div>
<?php
	}
//echo "<p class=\"time\">WiFiadmin Version ".$C_VERSION;
echo "<p class=\"time\">";
if($C_count_time)
	{
	$time_stop = getmicrotime();
	$time_diff = $time_stop - $time_start;

	echo $lang['footer']['pagerendered']." ".round($time_diff,3)." ".$lang['dict']['seconds'];
	}
echo "</p>";
?>
</div><!--footer-->

</div><!--wrapperout-->
</body>
</html>

