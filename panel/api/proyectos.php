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


$user_id = $_SESSION[ "net_fulltrust_fas_id"];
$perfil = $_SESSION['usuarios_profile'];

$sqla = "";

if ($perfil == 'ÁREA'){

    // Obtener los instrumentos asignados al usuario
    $q = "
        SELECT GROUP_CONCAT(instrumentos_id ORDER BY instrumentos_id) AS instrumentos
        FROM usuarios_areas
        WHERE usuarios_id = $user_id
        AND instrumentos_id IS NOT NULL
    ";

    if (!$r = $db->query($q)) {
        die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
    }

    $u = $r->fetch_assoc();
    $instrumentos_lista = $u['instrumentos'];   // ejemplo: "1,3,5"

    if (!empty($instrumentos_lista)) {
        // Filtrar proyectos cuyo campo instrumento esté en la lista asignada al usuario
        $sqla = " and proyectos.instrumento in (" . $instrumentos_lista . ")";
    } else {
        // Sin instrumentos asignados: no ve ningún registro
        $sqla = " and 1 = 0";
    }
}


// **************************************************************************************
if ( $_GET["task"] == "list" ){

	$splitnumber = $_GET["cat"];
	$splittedNumbers = explode(",", $splitnumber);
	$cat = "'" . implode("', '", $splittedNumbers) ."'";



	$sql = "
SELECT 
  `proyectos`.`id`,
  `proyectos`.`nombre`,
  `sectores`.`sector_descripcion`,
  `etapas`.`etapas_descripcion`,
  `procesos`.`procesos_descripcion`,
  DATE_FORMAT(`proyectos`.`finicio`, '%d/%m/%Y') AS finicio,
  DATE_FORMAT(`proyectos`.`ftermino`, '%d/%m/%Y') AS ftermino,
  proyectos.unidad_responsable  
FROM
  `proyectos`
  LEFT OUTER JOIN `sectores` ON (`proyectos`.`sector` = `sectores`.`sector_id`)
  LEFT OUTER JOIN `etapas` ON (`proyectos`.`etapa` = `etapas`.`etapas_id`)
  LEFT OUTER JOIN `procesos` ON (`proyectos`.`proceso` = `procesos`.`procesos_id`)";

  if ($sqla != "") {
  	$sql .= " where true " . $sqla;
  }

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

	// Validar que el usuario ÁREA solo edite proyectos cuyo instrumento le esté asignado
	if ($perfil == 'ÁREA') {
	    $uid_check      = (int) $_SESSION["net_fulltrust_fas_id"];
	    $instrumento_id = (int) $_POST['instrumento'];
	    $q_check = "SELECT COUNT(*) as cnt FROM usuarios_areas 
	                WHERE usuarios_id = $uid_check 
	                AND instrumentos_id = $instrumento_id";
	    $r_check   = $db->query($q_check);
	    $row_check  = $r_check->fetch_assoc();
	    if ((int)$row_check['cnt'] === 0) {
	        http_response_code(403);
	        die('No tiene permisos para editar una iniciativa de ese instrumento.');
	    }
	}


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


	
	// Obtener nombre del usuario que guarda
	$uid_upd = (int) $_SESSION["net_fulltrust_fas_id"];
	$q_usr = "SELECT usuarios_nombre FROM usuarios WHERE usuarios_id = $uid_upd LIMIT 1";
	$r_usr = $db->query($q_usr);
	$nombre_usuario_upd = ($r_usr && $row_usr = $r_usr->fetch_assoc()) ? $row_usr['usuarios_nombre'] : '';
	$proyectos_user_val = mysqli_real_escape_string($db, $uid_upd . ' - ' . $nombre_usuario_upd);

	$sql  = "UPDATE proyectos SET ";
	$sql .= "nombre = '" . mysqli_real_escape_string($db, $_POST['nombre']) . "', ";
	$sql .= "descripcion = '" . mysqli_real_escape_string($db, $_POST['descripcion']) . "', ";
	$sql .= "instrumento = '" . mysqli_real_escape_string($db, $_POST['instrumento']) . "', ";
	$sql .= "sector = '" . mysqli_real_escape_string($db, $_POST['sector']) . "', ";
	$sql .= "subsector = '" . mysqli_real_escape_string($db, $_POST['subsector']) . "', ";
	$sql .= "codigo_bip = '" . mysqli_real_escape_string($db, $_POST['codigo_bip']) . "', ";
	$sql .= "tipo = '" . mysqli_real_escape_string($db, $_POST['tipo']) . "', ";
	$sql .= "p_diseno = '" . mysqli_real_escape_string($db, $_POST['p_diseno']) . "', ";
	$sql .= "p_ejecucion = '" . mysqli_real_escape_string($db, $_POST['p_ejecucion']) . "', ";
	$sql .= "etapa = '" . mysqli_real_escape_string($db, $_POST['etapa']) . "', ";
	$sql .= "proceso = '" . mysqli_real_escape_string($db, $_POST['proceso']) . "', ";
	$sql .= "finicio = '" . mysqli_real_escape_string($db, $_POST['finicio']) . "', ";
	$sql .= "ftermino = '" . mysqli_real_escape_string($db, $_POST['ftermino']) . "', ";
	$sql .= "fuente = '" . mysqli_real_escape_string($db, $_POST['fuente']) . "', ";
	$sql .= "unidad_responsable_id = '" . mysqli_real_escape_string($db, $_POST['unidad_responsable_id']) . "', ";
	$sql .= "instituciones_vinculadas = '" . mysqli_real_escape_string($db, $_POST['instituciones_vinculadas']) . "', ";
	$sql .= "avance_financiero = '" . mysqli_real_escape_string($db, $_POST['avance_financiero']) . "', ";
	$sql .= "avance_actividades = '" . mysqli_real_escape_string($db, $_POST['avance_actividades']) . "', ";
	$sql .= "lat = '" . mysqli_real_escape_string($db, $_POST['lat']) . "', ";
	$sql .= "lng = '" . mysqli_real_escape_string($db, $_POST['lng']) . "', ";
	$sql .= "proyectos_user = '$proyectos_user_val' ";

	$sql .= "WHERE id = '" . mysqli_real_escape_string($db, $_POST['proyectos_id']) . "';";

	
	//file_put_contents('debug.txt', $sql);
	
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

	// Validar que el usuario ÁREA solo inserte en instrumentos que tiene asignados
	if ($perfil == 'ÁREA') {
	    $uid_check2      = (int) $_SESSION["net_fulltrust_fas_id"];
	    $instrumento_id2 = (int) $_POST['instrumento'];
	    $q_check2 = "SELECT COUNT(*) as cnt FROM usuarios_areas 
	                 WHERE usuarios_id = $uid_check2 
	                 AND instrumentos_id = $instrumento_id2";
	    $r_check2   = $db->query($q_check2);
	    $row_check2  = $r_check2->fetch_assoc();
	    if ((int)$row_check2['cnt'] === 0) {
	        http_response_code(403);
	        die('No tiene permisos para crear una iniciativa en ese instrumento.');
	    }
	}


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


	
	// Obtener nombre del usuario que crea el registro
	$uid_ins = (int) $_SESSION["net_fulltrust_fas_id"];
	$q_usr2 = "SELECT usuarios_nombre FROM usuarios WHERE usuarios_id = $uid_ins LIMIT 1";
	$r_usr2 = $db->query($q_usr2);
	$nombre_usuario_ins = ($r_usr2 && $row_usr2 = $r_usr2->fetch_assoc()) ? $row_usr2['usuarios_nombre'] : '';
	$proyectos_user_ins = mysqli_real_escape_string($db, $uid_ins . ' - ' . $nombre_usuario_ins);

	$sql  = "UPDATE proyectos SET ";
	$sql .= "nombre = '" . mysqli_real_escape_string($db, $_POST['nombre']) . "', ";
	$sql .= "descripcion = '" . mysqli_real_escape_string($db, $_POST['descripcion']) . "', ";
	$sql .= "instrumento = '" . mysqli_real_escape_string($db, $_POST['instrumento']) . "', ";
	$sql .= "sector = '" . mysqli_real_escape_string($db, $_POST['sector']) . "', ";
	$sql .= "subsector = '" . mysqli_real_escape_string($db, $_POST['subsector']) . "', ";
	$sql .= "codigo_bip = '" . mysqli_real_escape_string($db, $_POST['codigo_bip']) . "', ";
	$sql .= "tipo = '" . mysqli_real_escape_string($db, $_POST['tipo']) . "', ";
	$sql .= "p_diseno = '" . mysqli_real_escape_string($db, $_POST['p_diseno']) . "', ";
	$sql .= "p_ejecucion = '" . mysqli_real_escape_string($db, $_POST['p_ejecucion']) . "', ";
	$sql .= "etapa = '" . mysqli_real_escape_string($db, $_POST['etapa']) . "', ";
	$sql .= "proceso = '" . mysqli_real_escape_string($db, $_POST['proceso']) . "', ";
	$sql .= "finicio = '" . mysqli_real_escape_string($db, $_POST['finicio']) . "', ";
	$sql .= "ftermino = '" . mysqli_real_escape_string($db, $_POST['ftermino']) . "', ";
	$sql .= "fuente = '" . mysqli_real_escape_string($db, $_POST['fuente']) . "', ";
	$sql .= "unidad_responsable_id = '" . mysqli_real_escape_string($db, $_POST['unidad_responsable_id']) . "', ";
	$sql .= "instituciones_vinculadas = '" . mysqli_real_escape_string($db, $_POST['instituciones_vinculadas']) . "', ";
	$sql .= "avance_financiero = '" . mysqli_real_escape_string($db, $_POST['avance_financiero']) . "', ";
	$sql .= "avance_actividades = '" . mysqli_real_escape_string($db, $_POST['avance_actividades']) . "', ";
	$sql .= "lat = '" . mysqli_real_escape_string($db, $_POST['lat']) . "', ";
	$sql .= "lng = '" . mysqli_real_escape_string($db, $_POST['lng']) . "', ";
	$sql .= "proyectos_user = '$proyectos_user_ins' ";
									
	$sql.="where id='".$row['id']."';";
	
	//file_put_contents('debug.txt', $sql);
	
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	} else {
		echo $row["id"];
	}	


}

// **************************************************************************************

?>
