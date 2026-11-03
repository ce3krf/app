<?php 
// ************************************************************
//    __       _ _ _                  _   
//   / _|_   _| | | |_ _ __ _   _ ___| |_ 
//  | |_| | | | | | __| '__| | | / __| __|
//  |__| \__,_|_|_|\__|_|   \__,_|___/\__|
//                                         
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-10-10
// ************************************************************

@session_start();
include("../../conf/db.php");		
require __DIR__ . '/inc_seguridad.php';
include('functions.php');
require_once __DIR__ . '/../inc_auditoria.php';

if ( 
    (!isset($_GET["token"]) || $_GET["token"] != session_id()) && 
    (!isset($_POST["token"]) || $_POST["token"] != session_id()) 
) {
    exit;
}

$q = "SET NAMES utf8";
if(!$preg = $db->query($q)){
    die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}

if ($_GET["task"] == "list") {
    $sql = "SELECT * FROM sectores";
    $result = $db->query($sql);
    $row_cnt = $result->num_rows;
    $data = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC)){
        $data[] = $row;
    }
    $results = [
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    ];
    echo json_encode($results);
}

if ($_POST["task"] == "update") {
    $antes = audit_snapshot($db, 'sectores', $_POST['sector_id']);

    $sql = "UPDATE sectores SET ";
    $sql .= "sector_descripcion = '" . mysqli_real_escape_string($db, $_POST['sector_descripcion']) . "', ";
    $sql .= "sector_color = '" . mysqli_real_escape_string($db, $_POST['sector_color']) . "' ";
    $sql .= "WHERE sector_id='" . mysqli_real_escape_string($db, $_POST['sector_id']) . "';";
    
    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }

    audit_cambios($db, 'sectores', $_POST['sector_id'], $antes);
}

if ($_POST["task"] == "delete") {
    $id = $_POST["sector_id"];
    if (is_numeric($id)) {
        $antes = audit_snapshot($db, 'sectores', $id);
        $sql = "DELETE FROM sectores WHERE sector_id = '$id'";
        if(!$result = $db->query($sql)){
            die('Hay un error [' . $db->error . ']');
        }
        audit_eliminar($db, 'sectores', $id, $antes);
    }
}

if ($_POST["task"] == "insert") {
    $sql = "INSERT INTO sectores (sector_descripcion) VALUES ('Nuevo sector')";
    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }
    
    $sql = "SELECT * FROM sectores ORDER BY sector_id DESC LIMIT 1";
    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }
    $row = $result->fetch_assoc();
    
    $sql = "UPDATE sectores SET ";
    $sql .= "sector_descripcion = '" . mysqli_real_escape_string($db, $_POST['sector_descripcion']) . "', ";
    $sql .= "sector_color = '" . mysqli_real_escape_string($db, $_POST['sector_color']) . "' ";
    $sql .= "WHERE sector_id='" . mysqli_real_escape_string($db, $row['sector_id']) . "';";
    
    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    } else {
        audit_crear($db, 'sectores', $row["sector_id"]);
        echo $row["sector_id"];
    }
}
?>