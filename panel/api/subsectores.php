<?php 
// ************************************************************
//    __       _ _ _                  _   
//   / _|_   _| | | |_ _ __ _   _ ___| |_ 
//  | |_| | | | | | __| '__| | | / __| __|
//  |  _| |_| | | | |_| |  | |_| \__ \ |_ 
//  |__| \__,_|_|_|\__|_|   \__,_|___/\__|
//                                         
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-10-10
// ************************************************************

@session_start();
include("../../conf/db.php");		
include('functions.php');

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
    $sql = "SELECT * FROM subsectores";
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
    $sql = "UPDATE subsectores SET ";
    $sql .= "subsector_codigo = '" . mysqli_real_escape_string($db, $_POST['subsector_codigo']) . "', ";
    $sql .= "subsector_descripcion = '" . mysqli_real_escape_string($db, $_POST['subsector_descripcion']) . "' ";
    $sql .= "WHERE subsector_id='" . mysqli_real_escape_string($db, $_POST['subsector_id']) . "';";
    
    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }
}

if ($_POST["task"] == "delete") {
    $id = $_POST["subsector_id"];
    if (is_numeric($id)) {
        $sql = "DELETE FROM subsectores WHERE subsector_id = '$id'";
        if(!$result = $db->query($sql)){
            die('Hay un error [' . $db->error . ']');
        }
    }
}

if ($_POST["task"] == "insert") {
    $sql = "INSERT INTO subsectores (subsector_descripcion) VALUES ('Nuevo subsector')";
    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }
    
    $sql = "SELECT * FROM subsectores ORDER BY subsector_id DESC LIMIT 1";
    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }
    $row = $result->fetch_assoc();
    
    $sql = "UPDATE subsectores SET ";
    $sql .= "subsector_codigo = '" . mysqli_real_escape_string($db, $_POST['subsector_codigo']) . "', ";
    $sql .= "subsector_descripcion = '" . mysqli_real_escape_string($db, $_POST['subsector_descripcion']) . "' ";
    $sql .= "WHERE subsector_id='" . mysqli_real_escape_string($db, $row['subsector_id']) . "';";
    
    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    } else {
        echo $row["subsector_id"];
    }
}
?>