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
  `peredifica`

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


	// upload the file if there is any 
	if ($_FILES["pdf"]["name"] <> ''){

		if ( pathinfo($_FILES["pdf"]["name"], PATHINFO_EXTENSION) == 'pdf'){

			$path = "../../pdfs/";
			$filename = $_POST["permisos_id"] . "-" . date("YmdHis") . "-";
			$filename = $filename . preg_replace("/[^a-z0-9\_\-\.]/i", '-', basename($_FILES["pdf"]["name"]) );
			
			$target_file =  $path . $filename;

			if(file_exists( $target_file )){
				unlink( $target_file );
			}

			try {

				//file_put_contents('debug.txt', $_FILES['pdf']['tmp_name']."\n".$target_file );
				move_uploaded_file( $_FILES['pdf']['tmp_name'], $target_file );	
		
			} catch (Exception $e) {
				//file_put_contents('debug.txt', "\n >".$e->getMessage(), FILE_APPEND );
				exit;
			}
		 


		}

	}
	// upload the file if there is any 


	
    $sql = "UPDATE peredifica SET ";
    $sql .= "nombre = '" . mysqli_real_escape_string($db, $_POST['nombre']) . "', ";
    $sql .= "direccion = '" . mysqli_real_escape_string($db, $_POST['direccion']) . "', ";
    $sql .= "localidad = '" . mysqli_real_escape_string($db, $_POST['localidad']) . "', ";
    $sql .= "rol = '" . mysqli_real_escape_string($db, $_POST['rol']) . "', ";
    $sql .= "per_edif = '" . mysqli_real_escape_string($db, $_POST['per_edif']) . "', ";
    $sql .= "rec_def = '" . mysqli_real_escape_string($db, $_POST['rec_def']) . "', ";
    $sql .= "m2 = '" . mysqli_real_escape_string($db, $_POST['m2']) . "', ";
    $sql .= "n = '" . mysqli_real_escape_string($db, $_POST['n']) . "', ";
    $sql .= "m2_avaluo = '" . mysqli_real_escape_string($db, $_POST['m2_avaluo']) . "', ";
    $sql .= "destino2 = '" . mysqli_real_escape_string($db, $_POST['destino2']) . "', ";
    $sql .= "tipo = '" . mysqli_real_escape_string($db, $_POST['tipo']) . "', ";
    $sql .= "expediente = '" . mysqli_real_escape_string($db, $_POST['expediente']) . "', ";
    $sql .= "ano = '" . mysqli_real_escape_string($db, $_POST['ano']) . "', ";
    $sql .= "lat = '" . mysqli_real_escape_string($db, $_POST['lat']) . "', ";
    $sql .= "lon = '" . mysqli_real_escape_string($db, $_POST['lon']) . "' ";
    $sql .= "WHERE id='" . mysqli_real_escape_string($db, $_POST['permisos_id']) . "';";
	
	//file_put_contents('debug.txt', '\n'.$sql, FILE_APPEND);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	


}

// **************************************************************************************








// **************************************************************************************
if ( $_POST["task"] == "delete" ){

	$id = $_POST["permisos_id"];
	if ( is_numeric($id) ){

		$sql = "delete from peredifica where id = '$id' ";

		if(!$result = $db->query($sql)){
			die('Hay un error [' . $db->error . ']');
		}

	}
	
}
// **************************************************************************************









// **************************************************************************************
if ( $_POST["task"] == "insert" ){




	// crea el nuevo registro vacio
	$sql ="insert into peredifica (nombre) values ('Nuevo registro') ";
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	

	// recurepa la última id creada
	$sql="select * from peredifica order by id desc limit 1";
	if(!$result = $db->query($sql)){
		die('Hay un error [' . $db->error . ']');
	}
	$row = $result->fetch_assoc();


	
	$sql ="update peredifica set ";
    $sql .= "nombre = '" . mysqli_real_escape_string($db, $_POST['nombre']) . "', ";
    $sql .= "direccion = '" . mysqli_real_escape_string($db, $_POST['direccion']) . "', ";
    $sql .= "localidad = '" . mysqli_real_escape_string($db, $_POST['localidad']) . "', ";
    $sql .= "rol = '" . mysqli_real_escape_string($db, $_POST['rol']) . "', ";
    $sql .= "per_edif = '" . mysqli_real_escape_string($db, $_POST['per_edif']) . "', ";
    $sql .= "rec_def = '" . mysqli_real_escape_string($db, $_POST['rec_def']) . "', ";
    $sql .= "m2 = '" . mysqli_real_escape_string($db, $_POST['m2']) . "', ";
    $sql .= "n = '" . mysqli_real_escape_string($db, $_POST['n']) . "', ";
    $sql .= "m2_avaluo = '" . mysqli_real_escape_string($db, $_POST['m2_avaluo']) . "', ";
    $sql .= "destino2 = '" . mysqli_real_escape_string($db, $_POST['destino2']) . "', ";
    $sql .= "tipo = '" . mysqli_real_escape_string($db, $_POST['tipo']) . "', ";
    $sql .= "expediente = '" . mysqli_real_escape_string($db, $_POST['expediente']) . "', ";
    $sql .= "ano = '" . mysqli_real_escape_string($db, $_POST['ano']) . "', ";
    $sql .= "lat = '" . mysqli_real_escape_string($db, $_POST['lat']) . "', ";
    $sql .= "lon = '" . mysqli_real_escape_string($db, $_POST['lon']) . "' ";
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


