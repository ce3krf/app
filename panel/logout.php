<?php

@session_start();
$_SESSION["logeado"] = ''; 

	setcookie( "net_fulltrust_fas_logged", 			"", time() - ( 3600 * 3 ) );	
	setcookie( "net_fulltrust_fas_users_id", 		"", time() - ( 3600 * 3 ) );	
	setcookie( "net_fulltrust_fas_users_name",  	"", time() - ( 3600 * 3 ) );	
	setcookie( "net_fulltrust_fas_users_email", 	"",	time() - ( 3600 * 3 ) );	
	setcookie( "net_fulltrust_fas_users_profile",	"",	time() - ( 3600 * 3 ) );	
	setcookie( "net_fulltrust_fas_users_picture",	"",	time() - ( 3600 * 3 ) );	
	setcookie( "net_fulltrust_fas_users_password",  "",	time() - ( 3600 * 3 ) );	
	setcookie( "logeado",  							"",	time() - ( 3600 * 3 ) );	
	
	if (isset($_SERVER['HTTP_COOKIE'])) {
		$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
		foreach($cookies as $cookie) {
			$parts = explode('=', $cookie);
			$name = trim($parts[0]);
			setcookie($name, '', time()-1000);
			setcookie($name, '', time()-1000, '/');
		}
	}	

?>

<script>
	
window.location='login.php';	
	
</script>