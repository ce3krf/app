<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-01-03 16:56:35
// ************************************************************
?>
<?php

@session_start();

include("../../conf/db.php");		
include('functions.php');

//error_reporting(E_ALL);
//ini_set('display_errors', 1);



if ( 
    (!isset($_GET["token"]) || $_GET["token"] != session_id()) && 
    (!isset($_POST["token"]) || $_POST["token"] != session_id()) 
) {
    exit;
}


    $sql = "set names utf8";
    $result = $db->query($sql);

    $sql = "SELECT 
                count(`id`) AS `cuantos`,
                area
            FROM
                proyectos
            WHERE 
                area <> ''  
            GROUP BY
                area
            ORDER BY
                area";

    $result = $db->query($sql);
    
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }


    $json = json_encode($data);

    if ($json === false) {
        echo "Error al codificar JSON: " . json_last_error_msg();
    } else {
        echo $json;
    }
?>