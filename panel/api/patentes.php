<?php 
// ************************************************************
//    __       _ _ _                  _   
//   / _|_   _| | | |_ _ __ _   _ ___| |_ 
//  | |_| | | | | | __| '__| | | / __| __|
//  |  _| |_| | | | |_| |  | |_| \__ \ |_ 
//  |_|  \__,_|_|_|\__|_|   \__,_|___/\__|
//                                         
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-09-18 18:35:12
// ************************************************************

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

@session_start();

include("../../conf/db.php");		
include('functions.php');


if ( 
    (!isset($_GET["token"]) || $_GET["token"] != session_id()) && 
    (!isset($_POST["token"]) || $_POST["token"] != session_id()) 
) {
    exit;
}



$q = "set names utf8";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}


// **************************************************************************************
if ( $_GET["task"] == "list" ){

	$splitnumber = $_GET["cat"];
	$splittedNumbers = explode(",", $splitnumber);
	$cat = "'" . implode("', '", $splittedNumbers) ."'";



	$sql = "SELECT * 
FROM
  `patentes`

	";

	/////////////////////////////////////////////////////////////
	//file_put_contents('debug.txt', $sql);

	$result = $db->query($sql);
	$row_cnt = $result->num_rows;

	$data = array();
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		$data[] = $row;
	}


	$results = ["sEcho" => 1,
				"iTotalRecords" => count($data),
				"iTotalDisplayRecords" => count($data),
				"aaData" => $data ];


	echo json_encode($results);
	
}
// **************************************************************************************




// **************************************************************************************
if ( $_POST["task"] == "update" ){





	
    $sql = "UPDATE patentes SET ";
    $sql .= "ROL = '" . mysqli_real_escape_string($db, $_POST['ROL']) . "', ";
    $sql .= "RUT = '" . mysqli_real_escape_string($db, $_POST['RUT']) . "', ";
    $sql .= "NOMBRE = '" . mysqli_real_escape_string($db, $_POST['NOMBRE']) . "', ";
    $sql .= "DIRECCION_COMERCIAL = '" . mysqli_real_escape_string($db, $_POST['DIRECCION_COMERCIAL']) . "', ";
    $sql .= "ROL_PROP = '" . mysqli_real_escape_string($db, $_POST['ROL_PROP']) . "', ";
    $sql .= "GIRO = '" . mysqli_real_escape_string($db, $_POST['GIRO']) . "', ";
    $sql .= "TIPO_PATENTE = '" . mysqli_real_escape_string($db, $_POST['TIPO_PATENTE']) . "', ";
    $sql .= "LATITUD = '" . mysqli_real_escape_string($db, $_POST['LATITUD']) . "', ";
    $sql .= "LONGITUD = '" . mysqli_real_escape_string($db, $_POST['LONGITUD']) . "' ";
    $sql .= "WHERE id='" . mysqli_real_escape_string($db, $_POST['patentes_id']) . "';";
	//file_put_contents('debug.txt', '\n'.$sql, FILE_APPEND);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	


}

// **************************************************************************************








// **************************************************************************************
if ( $_POST["task"] == "delete" ){

	$id = $_POST["patentes_id"];
	if ( is_numeric($id) ){

		$sql = "delete from patentes where id = '$id' ";

		if(!$result = $db->query($sql)){
			die('Hay un error [' . $db->error . ']');
		}

	}
	
}
// **************************************************************************************









// **************************************************************************************
if ( $_POST["task"] == "insert" ){




	// crea el nuevo registro vacio
	$sql ="insert into patentes (nombre) values ('Nuevo registro') ";
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	

	// recurepa la última id creada
	$sql="select * from patentes order by id desc limit 1";
	if(!$result = $db->query($sql)){
		die('Hay un error [' . $db->error . ']');
	}
	$row = $result->fetch_assoc();


	
    $sql = "UPDATE patentes SET ";
    $sql .= "ROL = '" . mysqli_real_escape_string($db, $_POST['ROL']) . "', ";
    $sql .= "RUT = '" . mysqli_real_escape_string($db, $_POST['RUT']) . "', ";
    $sql .= "NOMBRE = '" . mysqli_real_escape_string($db, $_POST['NOMBRE']) . "', ";
    $sql .= "DIRECCION_COMERCIAL = '" . mysqli_real_escape_string($db, $_POST['DIRECCION_COMERCIAL']) . "', ";
    $sql .= "ROL_PROP = '" . mysqli_real_escape_string($db, $_POST['ROL_PROP']) . "', ";
    $sql .= "GIRO = '" . mysqli_real_escape_string($db, $_POST['GIRO']) . "', ";
    $sql .= "TIPO_PATENTE = '" . mysqli_real_escape_string($db, $_POST['TIPO_PATENTE']) . "', ";
    $sql .= "LATITUD = '" . mysqli_real_escape_string($db, $_POST['LATITUD']) . "', ";
    $sql .= "LONGITUD = '" . mysqli_real_escape_string($db, $_POST['LONGITUD']) . "' ";
    $sql .= "WHERE id='" . mysqli_real_escape_string($db, $row['id']) . "';";								
	
	//file_put_contents('debug.txt', '\n'.$sql, FILE_APPEND);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
		echo $row["id"];
	}	


}

// **************************************************************************************

?>


