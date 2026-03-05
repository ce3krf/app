<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-02-10 17:10:51
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
if ( $_GET["task"] == "list" ){

	$splitnumber = $_GET["cat"];
	$splittedNumbers = explode(",", $splitnumber);
	$cat = "'" . implode("', '", $splittedNumbers) ."'";



	$sql = "SELECT 
	proyectos.*,
	areas.area
	FROM
	`proyectos`
	LEFT OUTER JOIN `areas` ON (`proyectos`.`cod_area` = `areas`.`codigo`)
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
			$filename = $_POST["proyectos_id"] . "-" . date("YmdHis") . "-";
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


	
	$sql ="update proyectos set ";
	$sql.="nombre = '".mysqli_real_escape_string($db, $_POST['nombre'])."', ";	
	$sql.="descripcion = '".mysqli_real_escape_string($db, $_POST['descripcion'])."', ";	
	$sql.="cod_tipo = '".mysqli_real_escape_string($db, $_POST['tipo'])."', ";		
	$sql.="cod_area = '".mysqli_real_escape_string($db, $_POST['cod_area'])."', ";			
	$sql.="subarea = '".mysqli_real_escape_string($db, $_POST['subarea'])."', ";			
	$sql.="origen = '".mysqli_real_escape_string($db, $_POST['origen'])."', ";			
	$sql.="lineamiento = '".mysqli_real_escape_string($db, $_POST['lineamiento'])."', ";			
	$sql.="objetivos = '".mysqli_real_escape_string($db, $_POST['objetivos'])."', ";			
	$sql.="etapa = '".mysqli_real_escape_string($db, $_POST['etapa'])."', ";			
	$sql.="meta_del_periodo = '".mysqli_real_escape_string($db, $_POST['meta_del_periodo'])."', ";			
	$sql.="p_diseno = '".mysqli_real_escape_string($db, $_POST['p_diseno'])."', ";					
	$sql.="p_prefactibilidad = '".mysqli_real_escape_string($db, $_POST['p_prefactibilidad'])."', ";					
	$sql.="p_ejecucion = '".mysqli_real_escape_string($db, $_POST['p_ejecucion'])."', ";					
	$sql.="total = '".mysqli_real_escape_string($db, $_POST['total'])."', ";					
	$sql.="financiamiento = '".mysqli_real_escape_string($db, $_POST['financiamiento'])."', ";					
	$sql.="unidad = '".mysqli_real_escape_string($db, $_POST['unidad'])."', ";					
	$sql.="responsable = '".mysqli_real_escape_string($db, $_POST['responsable'])."', ";					
	$sql.="entidades_relacionadas = '".mysqli_real_escape_string($db, $_POST['entidades_relacionadas'])."', ";					
	$sql.="prioridad = '".mysqli_real_escape_string($db, $_POST['prioridad'])."', ";					
	$sql.="estado = '".mysqli_real_escape_string($db, $_POST['estado'])."', ";				
	$sql.="avance_financiero = '".mysqli_real_escape_string($db, $_POST['avance_financiero'])."', ";					
	$sql.="avance_actividades = '".mysqli_real_escape_string($db, $_POST['avance_actividades'])."', ";					
	$sql.="lat = '".mysqli_real_escape_string($db, $_POST['lat'])."', ";					
	$sql.="lng = '".mysqli_real_escape_string($db, $_POST['lng'])."' ";					

	$sql.="where id='".$_POST['proyectos_id']."';";
	
	//file_put_contents('debug.txt', '\n'.$sql, FILE_APPEND);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	


}

// **************************************************************************************








// **************************************************************************************
if ( $_POST["task"] == "delete" ){

	$id = $_POST["proyectos_id"];
	if ( is_numeric($id) ){

		$sql = "delete from proyectos where id = '$id' ";

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

			$path = "../../pdfs/";
			$filename = $_POST["proyectos_id"] . "-" . date("YmdHis") . "-";
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
	$sql ="insert into proyectos (nombre) values ('Nuevo registro') ";
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
	}	

	// recurepa la última id creada
	$sql="select * from proyectos order by id desc limit 1";
	if(!$result = $db->query($sql)){
		die('Hay un error [' . $db->error . ']');
	}
	$row = $result->fetch_assoc();


	
	$sql ="update proyectos set ";
	$sql.="nombre = '".mysqli_real_escape_string($db, $_POST['nombre'])."', ";	
	$sql.="descripcion = '".mysqli_real_escape_string($db, $_POST['descripcion'])."', ";	
	$sql.="cod_tipo = '".mysqli_real_escape_string($db, $_POST['tipo'])."', ";		
	$sql.="cod_area = '".mysqli_real_escape_string($db, $_POST['cod_area'])."', ";			
	$sql.="subarea = '".mysqli_real_escape_string($db, $_POST['subarea'])."', ";			
	$sql.="origen = '".mysqli_real_escape_string($db, $_POST['origen'])."', ";			
	$sql.="lineamiento = '".mysqli_real_escape_string($db, $_POST['lineamiento'])."', ";			
	$sql.="objetivos = '".mysqli_real_escape_string($db, $_POST['objetivos'])."', ";			
	$sql.="etapa = '".mysqli_real_escape_string($db, $_POST['etapa'])."', ";			
	$sql.="meta_del_periodo = '".mysqli_real_escape_string($db, $_POST['meta_del_periodo'])."', ";			
	$sql.="p_diseno = '".mysqli_real_escape_string($db, $_POST['p_diseno'])."', ";					
	$sql.="p_prefactibilidad = '".mysqli_real_escape_string($db, $_POST['p_prefactibilidad'])."', ";					
	$sql.="p_ejecucion = '".mysqli_real_escape_string($db, $_POST['p_ejecucion'])."', ";					
	$sql.="total = '".mysqli_real_escape_string($db, $_POST['total'])."', ";					
	$sql.="financiamiento = '".mysqli_real_escape_string($db, $_POST['financiamiento'])."', ";					
	$sql.="unidad = '".mysqli_real_escape_string($db, $_POST['unidad'])."', ";					
	$sql.="responsable = '".mysqli_real_escape_string($db, $_POST['responsable'])."', ";					
	$sql.="entidades_relacionadas = '".mysqli_real_escape_string($db, $_POST['entidades_relacionadas'])."', ";					
	$sql.="prioridad = '".mysqli_real_escape_string($db, $_POST['prioridad'])."', ";					
	$sql.="estado = '".mysqli_real_escape_string($db, $_POST['estado'])."', ";				
	$sql.="avance_financiero = '".mysqli_real_escape_string($db, $_POST['avance_financiero'])."', ";					
	$sql.="avance_actividades = '".mysqli_real_escape_string($db, $_POST['avance_actividades'])."', ";					
	$sql.="lat = '".mysqli_real_escape_string($db, $_POST['lat'])."', ";					
	$sql.="lng = '".mysqli_real_escape_string($db, $_POST['lng'])."' ";												
	$sql.="where id='".$row['id']."';";
	
	//file_put_contents('debug.txt', '\n'.$sql, FILE_APPEND);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
		echo $row["id"];
	}	


}

// **************************************************************************************

?>


