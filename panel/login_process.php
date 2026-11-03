<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');   // no mostrar errores en producción
@session_start();

date_default_timezone_set('America/Santiago');

$_SESSION[ "ahora"] =	date('D M d Y H:i:s O');	

include("../conf/db.php");

$q = "set names utf8";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}

$user = trim((string) ($_POST["user"] ?? ''));
$pass = md5( (string) ($_POST["password"] ?? '') );


// Consulta preparada: evita inyección SQL
$stmt = $db->prepare("SELECT * FROM usuarios WHERE usuarios_userid = ? AND usuarios_password = ? LIMIT 1");
$stmt->bind_param("ss", $user, $pass);
if (!$stmt->execute()) {
	die('Ha ocurrido un error ejecutando la consulta');
}
$r = $stmt->get_result();
$u = $r->fetch_assoc();
$cuantos = $r->num_rows;
$stmt->close();

require_once __DIR__ . '/inc_auditoria.php';


if ( $cuantos == 1 && $u["usuarios_status"] != 1 ){
	audit_registrar($db, 'LOGIN_FALLIDO', [
		'usuario_id'     => $u["usuarios_id"],
		'usuario_login'  => $u["usuarios_userid"],
		'usuario_nombre' => $u["usuarios_nombre"],
		'perfil'         => $u["usuarios_profile"],
		'opcion'         => 'login.php',
		'detalle'        => 'Usuario inactivo',
	]);
	echo "INACTIVE USER";
	exit;
}

if ( $cuantos == 1 ){

	// Nuevo id de sesión al ingresar (evita fijación de sesión)
	session_regenerate_id(true);

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
	$ahora = date("Y-m-d H:i:s");
	$uid   = (int) $u["usuarios_id"];
	$stmt  = $db->prepare("UPDATE usuarios SET last_login = ? WHERE usuarios_id = ?");
	$stmt->bind_param("si", $ahora, $uid);
	if (!$stmt->execute()) {
		die('Ha ocurrido un error ejecutando la consulta');
	}
	$stmt->close();

	audit_registrar($db, 'LOGIN', ['opcion' => 'login.php']);

	echo "LOGIN OK";


} else {

	audit_registrar($db, 'LOGIN_FALLIDO', [
		'usuario_id'     => null,
		'usuario_login'  => mb_substr($user, 0, 100),
		'usuario_nombre' => null,
		'perfil'         => null,
		'opcion'         => 'login.php',
		'detalle'        => 'Usuario o contraseña incorrectos',
	]);

	echo "BAD LOGIN";
}
?>