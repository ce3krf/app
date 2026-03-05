<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-09-06 10:19:44
// ************************************************************
?>
<?php

@session_start();

include("../../conf/db.php");		
include('functions.php');


if ( 
    (!isset($_GET["token"]) || $_GET["token"] != session_id()) && 
    (!isset($_POST["token"]) || $_POST["token"] != session_id()) 
) {
    exit;
}




$sql = "set names utf8";
$result = $db->query($sql);

$sql = "SELECT 
            COUNT(`id`) AS `cuantos`, 
            `proyectos`.`estado`
        FROM 
            `proyectos`
        GROUP BY 
            `proyectos`.`estado`
        ORDER BY 
            `cuantos` DESC";

$result = $db->query($sql);

$estados = [];
$cuantos = [];

if($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $estados[] = $row['estado'];
        $cuantos[] = $row['cuantos'];
    }
}

// Devuelve los datos como JSON
echo json_encode([
    'estados' => $estados,
    'cuantos' => $cuantos
]);
?>