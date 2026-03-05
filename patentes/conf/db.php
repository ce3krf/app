<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software - datafunken.de
// https://www.fulltrust.net
// Fecha última actualización: 2025-09-09 10:19:02
// ************************************************************
?>
<?php

//error_reporting(-1);
//ini_set('display_errors', '1');


/*
$hostname="mysql.comunidad.city";
$username="fulltrust";
$password="dsi10349";
$database="fulltrust_proyectossanjosedemaipo";
*/


$hostname="localhost";
$username="root";
$password="";
$database="fulltrust_proyectossanjosedemaipo";


$db = new mysqli($hostname, $username, $password, $database);
if($db->connect_errno > 0){
    die('No se ha podido conectar a la base de datos [' . $db->connect_error . ']');
}

?>