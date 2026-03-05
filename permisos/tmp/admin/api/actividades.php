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



$q = "set names utf8";
if(!$preg = $db->query($q)){
	die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}




// **************************************************************************************
if ( $_POST["task"] == "update" ){


	// upload the file if there is any 
	if ($_FILES["pdf"]["name"] <> ''){

		if ( pathinfo($_FILES["pdf"]["name"], PATHINFO_EXTENSION) == 'pdf'){

			$path = "../../adjuntos/";
			$filename = $_POST["proyectos_id"] . '-' . $_POST["actividades_id"] . "-" . date("YmdHis") . "-";
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


	
	$sql ="update actividades set ";
	$sql.="proyecto = '".mysqli_real_escape_string($db, $_POST['proyectos_id'])."', ";	
	$sql.="actividad = '".mysqli_real_escape_string($db, $_POST['actividad'])."', ";		
	$sql.="ano = '".mysqli_real_escape_string($db, $_POST['ano'])."', ";			
	$sql.="monto = '".mysqli_real_escape_string($db, $_POST['monto'])."', ";			
	$sql.="finaciamiento = '".mysqli_real_escape_string($db, $_POST['finaciamiento'])."', ";			
	$sql.="estado = '".mysqli_real_escape_string($db, $_POST['estado'])."', ";					
	$sql.="monto_final = '".mysqli_real_escape_string($db, $_POST['monto_final'])."', ";					
	$sql.="responsable = '".mysqli_real_escape_string($db, $_POST['responsable'])."', ";					
	$sql.="observaciones = '".mysqli_real_escape_string($db, $_POST['observaciones'])."', ";					

	if ($_FILES["pdf"]["name"] <> ''){
		$sql.="adjunto = '".mysqli_real_escape_string($db, $filename )."', ";					
	}	

	$sql.="actualizado = '".date("d-m-Y H:i:s")."' ";													
	$sql.="where id='".$_POST['actividades_id']."';";
	
	//file_put_contents('debug.txt', '\n'.$sql, FILE_APPEND);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	


}

// **************************************************************************************








// **************************************************************************************
if ( $_POST["task"] == "delete" ){

	$id = $_POST["actividades_id"];
	$proyecto = $_POST["proyectos_id"];
	if ( is_numeric($id) ){

		$sql = "delete from actividades where id = '$id' and proyecto = '$proyecto' ";

		if(!$result = $db->query($sql)){
			die('Hay un error [' . $db->error . ']');
		}

	}
	
}
// **************************************************************************************









// **************************************************************************************
if ( $_POST["task"] == "insert" ){


	// upload the file if there is any 
	if ($_FILES["pdf"]["name"] <> ''){

		if ( pathinfo($_FILES["pdf"]["name"], PATHINFO_EXTENSION) == 'pdf'){

			$path = "../../adjuntos/";
			$filename = $_POST["proyectos_id"] . '-' . $_POST["actividades_id"] . "-" . date("YmdHis") . "-";
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


	// crea el nuevo registro vacio
	$sql ="insert into actividades (actividad) values ('Nuevo registro') ";
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	

	// recurepa la última id creada
	$sql="select * from actividades order by id desc limit 1";
	if(!$result = $db->query($sql)){
		die('Hay un error [' . $db->error . ']');
	}
	$row = $result->fetch_assoc();


	
	$sql ="update actividades set ";
	$sql.="proyecto = '".mysqli_real_escape_string($db, $_POST['proyectos_id'])."', ";	
	$sql.="actividad = '".mysqli_real_escape_string($db, $_POST['actividad'])."', ";		
	$sql.="ano = '".mysqli_real_escape_string($db, $_POST['ano'])."', ";			
	$sql.="monto = '".mysqli_real_escape_string($db, $_POST['monto'])."', ";			
	$sql.="finaciamiento = '".mysqli_real_escape_string($db, $_POST['finaciamiento'])."', ";			
	$sql.="estado = '".mysqli_real_escape_string($db, $_POST['estado'])."', ";					
	$sql.="monto_final = '".mysqli_real_escape_string($db, $_POST['monto_final'])."', ";					
	$sql.="responsable = '".mysqli_real_escape_string($db, $_POST['responsable'])."', ";					
	$sql.="observaciones = '".mysqli_real_escape_string($db, $_POST['observaciones'])."', ";					

	if ($_FILES["pdf"]["name"] <> ''){
		$sql.="adjunto = '".mysqli_real_escape_string($db, $filename )."', ";					
	}	

	$sql.="creado = '".date("d-m-Y H:i:s")."' ";													
	$sql.="where id='".$row['id']."';";
	
	//file_put_contents('debug.txt', '\n'.$sql);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
		echo $_POST['proyectos_id'];
	}	


}

// **************************************************************************************

?>


