<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-02-11 16:01:35
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


	
	$sql ="update indica set ";
	$sql.="indica_indicador = '".mysqli_real_escape_string($db, $_POST['indica_indicador'])."', ";	
	$sql.="indica_meta = '".mysqli_real_escape_string($db, $_POST['indica_meta'])."' ";		


	$sql.="where indica_id='".$_POST['indica_id']."';";
	
	//file_put_contents('debug.txt', '\n'.$sql, FILE_APPEND);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	


}

// **************************************************************************************








// **************************************************************************************
if ( $_POST["task"] == "delete" ){

	$id = $_POST["indica_id"];
	$proyecto = $_POST["proyectos_id"];
	if ( is_numeric($id) ){

		$sql = "delete from indica where indica_id = '$id' and indica_iniciativa = '$proyecto' ";
        //file_put_contents('debug.txt', '\n'.$sql);

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
	$sql ="insert into indica (indica_indicador) values ('Nuevo registro') ";
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	

	// recurepa la última id creada
	$sql="select * from indica order by indica_id desc limit 1";
	if(!$result = $db->query($sql)){
		die('Hay un error [' . $db->error . ']');
	}
	$row = $result->fetch_assoc();


	
	$sql ="update indica set ";
	$sql.="indica_iniciativa = '".mysqli_real_escape_string($db, $_POST['proyectos_id'])."', ";	
	$sql.="indica_indicador = '".mysqli_real_escape_string($db, $_POST['indica_indicador'])."', ";	
	$sql.="indica_meta = '".mysqli_real_escape_string($db, $_POST['indica_meta'])."' ";						


	$sql.="where indica_id='".$row['indica_id']."';";
	
	//file_put_contents('debug.txt', '\n'.$sql);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
		echo $_POST['proyectos_id'];
	}	


}

// **************************************************************************************

?>


