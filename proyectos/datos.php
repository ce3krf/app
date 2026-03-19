<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-05-31 16:43:52
// ************************************************************

@session_start();
$session = session_id();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ( $session != $_GET["token"] ){
        //exit();
}



include_once("../conf/db.php");

$sql = "set names utf8";
$result = $db->query($sql);

/*
$sql = "set global sql_mode = (select replace(@@sql_mode,'ONLY_FULL_GROUP_BY', ''));";
$result = $db->query($sql);
*/

$sql = "
SELECT 
  `proyectos`.`id`,
  `proyectos`.`nombre`,
  `proyectos`.`sector`,
  `proyectos`.`subsector`,
  `proyectos`.`etapa`,
  `proyectos`.`finicio`,
  `proyectos`.`ftermino`,
  `proyectos`.`unidad_responsable_id`,
  `proyectos`.`lat`,
  `proyectos`.`lng`,
  `proyectos`.`total`,
  `proyectos`.`avance_actividades`,
  `proyectos`.`avance_financiero`,
  `proyectos`.`impacto_territorial`,
  `proyectos`.`foco_turismo`,
  `proyectos`.`codigo_bip`,
  `proyectos`.`codigo_idi`,
  `proyectos`.`lineamiento`,
  `proyectos`.`objetivo`,
  `proyectos`.`proyectos_status`,
  `procesos`.`procesos_descripcion`,
  `sectores`.`sector_id`,
  `sectores`.`sector_color`,
  `sectores`.`sector_descripcion`
FROM
  `proyectos`
  LEFT OUTER JOIN `procesos` ON (`proyectos`.`proceso` = `procesos`.`procesos_id`)
  LEFT OUTER JOIN `sectores` ON (`proyectos`.`sector` = `sectores`.`sector_id`)
WHERE `proyectos`.`proyectos_status` = 1
AND `proyectos`.`lat` IS NOT NULL AND `proyectos`.`lat` != '' AND `proyectos`.`lat` != '0'
AND `proyectos`.`lng` IS NOT NULL AND `proyectos`.`lng` != '' AND `proyectos`.`lng` != '0'
";

// El SELECT ya tiene WHERE proyectos_status = 1, todos los filtros usan AND
$hasWhere = true;

// Filtro de SECTOR
$sector = isset($_GET["sector"]) ? urldecode($_GET["sector"]) : "";
if ($sector != "") {
	$sectores = array_map('trim', explode(',', $sector));
	$sectores_escaped = array_map(function($s) use ($db) {
		return "'" . mysqli_real_escape_string($db, $s) . "'";
	}, $sectores);
	$sql .= ($hasWhere ? " AND" : " WHERE") . " `proyectos`.`sector` IN (" . implode(',', $sectores_escaped) . ") ";
	$hasWhere = true;
}

$proceso = isset($_GET["proceso"]) ? urldecode($_GET["proceso"]) : "";
if ($proceso != "") {
	$procesos = array_map('trim', explode(',', $proceso));
	$procesos_escaped = array_map(function($p) use ($db) {
		return "'" . mysqli_real_escape_string($db, $p) . "'";
	}, $procesos);
	$sql .= ($hasWhere ? " AND" : " WHERE") . " `proyectos`.`proceso` IN (" . implode(',', $procesos_escaped) . ") ";
	$hasWhere = true;
}

$origen = isset($_GET["origen"]) ? urldecode($_GET["origen"]) : "";
if ($origen != "") {
	$origenes = array_map('trim', explode(',', $origen));
	$origenes_escaped = array_map(function($o) use ($db) {
		return "'" . mysqli_real_escape_string($db, $o) . "'";
	}, $origenes);
	$sql .= ($hasWhere ? " AND" : " WHERE") . " `proyectos`.`origen` IN (" . implode(',', $origenes_escaped) . ") ";
	$hasWhere = true;
}

$estado = isset($_GET["estado"]) ? urldecode($_GET["estado"]) : "";
if ($estado != "") {
	$estados = array_map('trim', explode(',', $estado));
	$estados_escaped = array_map(function($e) use ($db) {
		return "'" . mysqli_real_escape_string($db, $e) . "'";
	}, $estados);
	$sql .= ($hasWhere ? " AND" : " WHERE") . " `proyectos`.`estado` IN (" . implode(',', $estados_escaped) . ") ";
	$hasWhere = true;
}

$search = isset($_GET['search']) ? urldecode($_GET['search']) : '';


if ($search !== '') {
	$search = mysqli_real_escape_string($db, $search);
	$sql .= ($hasWhere ? " AND (" : " WHERE (");
	
	// Campos que realmente existen en la tabla proyectos
	$fields = [
		'id', 'codigo_bip', 'codigo_idi', 'nombre', 'descripcion',
		'lineamiento', 'objetivo', 'localizacion', 'sector', 'subsector',
		'finicio', 'ftermino', 'fuente', 'instituciones_vinculadas',
		'etapa', 'informacion', 'proyectos_user'
	];
	
	$likeClauses = [];
	foreach ($fields as $field) {
		$likeClauses[] = "`proyectos`.`$field` LIKE '%$search%'";
	}
	
	// También buscar en las tablas relacionadas (JOIN)
	$likeClauses[] = "`sectores`.`sector_descripcion` LIKE '%$search%'";
	$likeClauses[] = "`procesos`.`procesos_descripcion` LIKE '%$search%'";
	
	$sql .= implode(' OR ', $likeClauses);
	$sql .= ")";
	$hasWhere = true;
}




file_put_contents('debug.txt', $sql);
//echo $sql."<br>";


$result = mysqli_query($db, $sql);
if (!$result) {
    header('Content-Type: application/json');
    echo json_encode([
        "error" => true,
        "message" => "Error de consulta: " . mysqli_error($db),
        "sql" => $sql
    ]);
    exit;
}
$num_rows = $result->num_rows;

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