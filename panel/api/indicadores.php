<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-10-21
// ************************************************************
?>
<?php

@session_start();

include("../../conf/db.php");		
include('../functions.php');

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

// **************************************************************************************
// UPDATE
// **************************************************************************************
if ( $_POST["task"] == "update" ){

	$sql = "UPDATE indicadores SET 
		indicadores_nombre = '".mysqli_real_escape_string($db, $_POST['indicadores_nombre'])."',
		indicadores_descripcion = '".mysqli_real_escape_string($db, $_POST['indicadores_descripcion'])."',
		indicadores_tipo = '".mysqli_real_escape_string($db, $_POST['indicadores_tipo'])."',
		indicadores_unidad = '".mysqli_real_escape_string($db, $_POST['indicadores_unidad'])."',
		indicadores_meta = '".mysqli_real_escape_string($db, $_POST['indicadores_meta'])."',
		indicadores_valor = '".mysqli_real_escape_string($db, $_POST['indicadores_valor'])."',
		indicadores_avance = '".mysqli_real_escape_string($db, $_POST['indicadores_avance'])."',
		indicadores_fuente = '".mysqli_real_escape_string($db, $_POST['indicadores_fuente'])."',
		indicadores_frecuencia = '".mysqli_real_escape_string($db, $_POST['indicadores_frecuencia'])."',
		indicadores_user = '".$_SESSION['usuarios_id']."'
		WHERE indicadores_id = '".$_POST['indicadores_id']."'";
	
	//file_put_contents('debug.txt', '\n'.$sql, FILE_APPEND);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
		echo $_POST['indicadores_iniciativa'];
	}	
}

// **************************************************************************************
// DELETE
// **************************************************************************************
if ( $_POST["task"] == "delete" ){

	$id = $_POST["indicadores_id"];
	$proyecto = $_POST["proyectos_id"];
	
	if ( is_numeric($id) ){
		$sql = "DELETE FROM indicadores 
				WHERE indicadores_id = '$id' 
				AND indicadores_iniciativa = '$proyecto'";
        
		//file_put_contents('debug.txt', '\n'.$sql);

		if(!$result = $db->query($sql)){
			die('Hay un error [' . $db->error . ']');
		} else {
			echo "OK";
		}
	}
}

// **************************************************************************************
// INSERT
// **************************************************************************************
if ( $_POST["task"] == "insert" ){

	// Crear el nuevo registro
	$sql = "INSERT INTO indicadores (
		indicadores_iniciativa,
		indicadores_nombre,
		indicadores_descripcion,
		indicadores_tipo,
		indicadores_unidad,
		indicadores_meta,
		indicadores_valor,
		indicadores_avance,
		indicadores_fuente,
		indicadores_frecuencia,
		indicadores_user,
		indicadores_status
	) VALUES (
		'".mysqli_real_escape_string($db, $_POST['indicadores_iniciativa'])."',
		'".mysqli_real_escape_string($db, $_POST['indicadores_nombre'])."',
		'".mysqli_real_escape_string($db, $_POST['indicadores_descripcion'])."',
		'".mysqli_real_escape_string($db, $_POST['indicadores_tipo'])."',
		'".mysqli_real_escape_string($db, $_POST['indicadores_unidad'])."',
		'".mysqli_real_escape_string($db, $_POST['indicadores_meta'])."',
		'".mysqli_real_escape_string($db, $_POST['indicadores_valor'])."',
		'".mysqli_real_escape_string($db, $_POST['indicadores_avance'])."',
		'".mysqli_real_escape_string($db, $_POST['indicadores_fuente'])."',
		'".mysqli_real_escape_string($db, $_POST['indicadores_frecuencia'])."',
		'".$_SESSION['usuarios_id']."',
		1
	)";
	
	//file_put_contents('debug.txt', '\n'.$sql, FILE_APPEND);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
		echo $_POST['indicadores_iniciativa'];
	}	
}

?>