<?php
/* Used where you want athentication prompt as well corresponding error messages to be placed */

if(strpos($_SERVER['PHP_SELF'],basename(__FILE__)))
	require "trap.php";


if($auth_results)
	echo $auth_results;

if($_SESSION["username"] == "guest")
{ ?>
<div id="loginf">
		<fieldset title="login here">
		<legend><?php echo $lang['index']['loginhere']; ?></legend>
		<form name="login" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<table>
			<tr><td><?php echo $lang['index']['uname']; ?></td><td><input type="text" name="username" class="text"></td></tr>
			<tr><td><?php echo $lang['index']['upass']; ?></td><td><input type="password" name="password" class="text"></td></tr>
			<tr><td colspan="2" align="right"><input type="submit" value="<?php  echo $lang['index']['loginbutton']; ?>"></td></tr>
		</table>
		<input type="hidden" name="action" value="login">
		</form>
		</fieldset>
<?php if ($C_send_emails) { ?>
		<p align="right"><?php echo $lang['index']['forgotpass'];?><a href="./forgotpassword.php"><?php echo $lang['general']['clickhere']?></a></p>
<?php } ?>
</div>
<?php } ?>
