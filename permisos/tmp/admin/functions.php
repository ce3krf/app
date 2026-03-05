<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 25 de Agosto de 2023
// ************************************************************
?>
<?php
@session_start();
$sid = session_id();
	
	
include("../conf/db.php");		

/************************** */
$proyecto = '001';

// Clave y método de cifrado
define('SECRET_KEY', 'PATATA');  // Clave secreta
define('CIPHER_METHOD', 'AES-256-CBC');  // Método de cifrado
/************************** */


$sql="set names utf8";
if(!$result = $db->query($sql)){
	die('Hay un error [' . $db->error . ']');
}	

$u = mysqli_real_escape_string($db, $_SESSION["net_fulltrust_fas_users_id"] );
$p = mysqli_real_escape_string($db, $_SESSION["net_fulltrust_fas_users_password"] );


$sql="select * from usuarios where usuarios_userid='".$u."' and usuarios_password='".md5($p)."'"; 

if(!$restabla = $db->query($sql)){
	die('Hay un error [' . $db->error . ']');
}
$row_tabla = $restabla->fetch_assoc();  	
$count = $restabla->num_rows;

if($count < 1){
	session_destroy();
	?>
	<script>
	window.location='index.php';
	</script>		
	<?php
}

$usuarios_foto = $row_tabla["usuarios_foto"];

// get the parameters
$sql="select * from parametros where parametros_codproyecto='".$proyecto."' limit 1"; 
if(!$resparam = $db->query($sql)){
	die('Hay un error [' . $db->error . ']');
}
$row_param = $resparam->fetch_assoc();      









// Función para encriptar usando XOR y base64 con al menos 16 caracteres de salida
function encrypt($id) {
    $key = SECRET_KEY;
    $id = (string) $id;
    $key = str_pad($key, strlen($id), $key);  // Repetir la clave si es más corta que la ID
    $encrypted = '';
    
    // Aplicar XOR entre el ID y la clave
    for ($i = 0; $i < strlen($id); $i++) {
        $encrypted .= chr(ord($id[$i]) ^ ord($key[$i]));
    }
    
    // Codificar en base64
    $encoded = base64_encode($encrypted);
    
    // Asegurarse de que tenga al menos 16 caracteres, agregando relleno si es necesario
    while (strlen($encoded) < 16) {
        $encoded .= "=";  // Usamos `=` como relleno, común en codificación base64
    }

    return substr($encoded, 0, 16);  // Devolver los primeros 16 caracteres
}

// Función para desencriptar
function decrypt($encrypted) {
    $key = SECRET_KEY;
    
    // Eliminar cualquier `=` añadido durante el encriptado
    $encrypted = str_replace("=", "", $encrypted);
    
    // Decodificar desde base64
    $encrypted = base64_decode($encrypted);
    $key = str_pad($key, strlen($encrypted), $key);  // Repetir la clave si es más corta que el texto cifrado
    $decrypted = '';
    
    // Aplicar XOR nuevamente para recuperar la ID original
    for ($i = 0; $i < strlen($encrypted); $i++) {
        $decrypted .= chr(ord($encrypted[$i]) ^ ord($key[$i]));
    }
    
    return $decrypted;
}



?>