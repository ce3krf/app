<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-06
// ************************************************************
?>
<?php
include_once("conf/db.php");


//error_reporting(-1);
//ini_set('display_errors', '1');

@session_start();
$session = session_id();


if ( !isset($_GET["token"]) || $session != $_GET["token"] ){
        exit();
}



$sql = "set names utf8mb4";
$result = $db->query($sql);


// Lista de enteros separados por coma (para filtros por código)
function lista_enteros($valor) {
	$ids = array_filter(array_map('intval', explode(',', (string) $valor)), function($n) { return $n > 0; });
	return $ids ? implode(',', $ids) : '';
}


// Nueva estructura de proyectos. Se agregan alias con los nombres
// antiguos (nombre, lat, lng, etapa, proceso) para no romper a quien
// consuma este JSON.
$sql = "SELECT
    p.*,
    p.nombre_iniciativa                       AS nombre,
    p.point_x                                 AS lat,
    p.point_y                                 AS lng,
    p.etapa_actual                            AS etapa,
    p.actividad_actual                        AS proceso,
    s.sector_color,
    COALESCE(s.sector_descripcion, p.sector)  AS sector_descripcion
FROM proyectos p
LEFT JOIN sectores s ON s.sector_id = p.cod_sector
WHERE p.proyectos_status = 1 ";

// Tipo (tipo.id = proyectos.cod_tipo)
$tipo = lista_enteros($_GET["tipo"] ?? '');
if ( $tipo != "" ) {
	$sql .= " AND p.cod_tipo IN ($tipo)";
}

// Sector (sectores.sector_id = proyectos.cod_sector)
$sector = lista_enteros($_GET["sector"] ?? '');
if ( $sector != "" ) {
	$sql .= " AND p.cod_sector IN ($sector)";
}

// Proceso (procesos.procesos_id = proyectos.cod_actividad_actual)
$proceso = lista_enteros($_GET["proceso"] ?? '');
if ( $proceso != "" ) {
	$sql .= " AND p.cod_actividad_actual IN ($proceso)";
}

// Los filtros "area", "origen" y "estado" ya no existen en la tabla proyectos: se ignoran.

$sql .= " ORDER BY p.nombre_iniciativa";

//file_put_contents('debug.txt', $sql);


$result = $db->query($sql);
if (!$result) {
	header('Content-Type: application/json');
	echo json_encode(["error" => true, "message" => "Error de consulta: " . $db->error]);
	exit;
}

$data = array();
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$data[] = $row;
}

$d = ["sEcho" => 1,
        	"iTotalRecords" => count($data),
        	"iTotalDisplayRecords" => count($data),
        	"aaData" => $data ];

header('Content-Type: application/json');
echo json_encode($d);
?>
