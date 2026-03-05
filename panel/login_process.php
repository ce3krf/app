<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
@session_start();

date_default_timezone_set('America/Santiago');

$_SESSION[ "ahora"] =	date('D M d Y H:i:s O');	

include("../conf/db.php");

$q = "set names utf8";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}

$user = $_POST["user"];
$pass = md5( $_POST["password"] );


$q = "select * from usuarios where usuarios_userid='".$user."' and usuarios_password='".$pass."' limit 1";
if(!$r = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}
$u = $r->fetch_assoc();
$cuantos = $r->num_rows;


if ( $cuantos == 1 && $u["usuarios_status"] != 1 ){
	echo "INACTIVE USER";
	exit;
}

if ( $cuantos == 1 ){

	$_SESSION[ "logeado"]= 							"si";	
	$_SESSION[ "net_fulltrust_fas_id"]= 			$u["usuarios_id"];
	$_SESSION[ "net_fulltrust_fas_users_id"]= 		$u["usuarios_userid"];
	$_SESSION[ "net_fulltrust_fas_users_name"]=  	$u["usuarios_nombre"];	
	$_SESSION[ "net_fulltrust_fas_users_email"]= 	$u["usuarios_email"];	
	$_SESSION[ "net_fulltrust_fas_users_password"]= $u["usuarios_password"];	
	$_SESSION[ "lastlogin"]= 						$u["last_login"];	
	$_SESSION[ "usuarios_profile"]= 				$u["usuarios_profile"];	
	$_SESSION[ "foto"]= 							$u["usuarios_foto"];	



	// último acceso
	$sql = "update usuarios set last_login = '".date("Y-m-d H:i:s")."' where usuarios_userid='".$user."' and usuarios_password='".$pass."'";
	//file_put_contents('debug.txt', $sql);
	
	if(!$r = $db->query($sql)){
		die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
	}	

	echo "LOGIN OK";	
	

} else {

	echo "BAD LOGIN";
}
?>