<?php	
/* Manages the add removal of routers from the configuration files..
	Due to misuse that can be performed by anauthorized access we HAVE to
	authorize first. We use the manage_router privilege to enable router 
	file modifications...
*/
	
	require "./include/header.php";

	if (@ $_GET['mode'] != "add" && empty($_SESSION['update'])) 
		echo "<H2>". $lang['inst']['install'] ." ". $lang['inst']['step'] ." 3 / 3</H2>";
	else
		$_SESSION['update'] = 1; 

	if (@$_SESSION['manage_routers'] === 'true') {
		require "./install/install_router.php";
	}
	else {
		echo "<p class = \"error\">".$lang['general']['enoperm']."</p>";
		// Display the auth .prompt box
		require "./include/auth_prompt.php";

	}
	require "./include/footer.php";
?>
