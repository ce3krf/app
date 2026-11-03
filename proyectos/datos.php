<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-24
// ************************************************************

@session_start();
$session = session_id();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ( $session != ($_GET["token"] ?? '') ){
        //exit();
}



include_once("../conf/db.php");

$sql = "set names utf8mb4";
$result = $db->query($sql);

/*
$sql = "set global sql_mode = (select replace(@@sql_mode,'ONLY_FULL_GROUP_BY', ''));";
$result = $db->query($sql);
*/

$sql = "
SELECT
  p.id,
  p.numero,
  p.nombre_iniciativa                             AS nombre,
  p.cod_sector,
  p.sector,
  p.subsector_de_inversion_sni                    AS subsector,
  p.etapa_actual                                  AS etapa,
  p.actividad_actual                              AS procesos_descripcion,
  p.inicio,
  p.termino,
  p.point_x                                       AS lat,
  p.point_y                                       AS lng,
  p.codigo_idi,
  p.tipo,
  p.instrumento_de_origen,
  p.proyectos_status,
  s.sector_id,
  s.sector_color,
  COALESCE(s.sector_descripcion, p.sector)        AS sector_descripcion
FROM proyectos p
LEFT JOIN sectores s ON s.sector_id = p.cod_sector
WHERE p.proyectos_status = 1
  AND (
        -- con ubicación principal
        (p.point_x IS NOT NULL AND p.point_x <> 0 AND p.point_y IS NOT NULL AND p.point_y <> 0)
        -- o con al menos una ubicación adicional
     OR EXISTS (SELECT 1 FROM ubicaciones u
                WHERE u.numero = p.numero
                  AND u.latitud  IS NOT NULL AND u.latitud  <> '' AND u.latitud  <> '0'
                  AND u.longitud IS NOT NULL AND u.longitud <> '' AND u.longitud <> '0')
  )
";
// point_x = latitud, point_y = longitud (según la planilla de iniciativas)
// Ubicaciones adicionales: tabla ubicaciones, enlazada por proyectos.numero


// Lista de enteros separados por coma (para filtros por código)
function lista_enteros($valor) {
	$ids = array_filter(array_map('intval', explode(',', $valor)), function($n) { return $n > 0; });
	return $ids ? implode(',', $ids) : '';
}

// Filtro de SECTOR (sectores.sector_id = proyectos.cod_sector)
$sector = isset($_GET["sector"]) ? lista_enteros(urldecode($_GET["sector"])) : "";
if ($sector != "") {
	$sql .= " AND p.cod_sector IN ($sector) ";
}

// Filtro de PROCESO (procesos.procesos_id = proyectos.cod_actividad_actual)
$proceso = isset($_GET["proceso"]) ? lista_enteros(urldecode($_GET["proceso"])) : "";
if ($proceso != "") {
	$sql .= " AND p.cod_actividad_actual IN ($proceso) ";
}

// Los filtros "origen" y "estado" ya no existen en la tabla proyectos: se ignoran.

$search = isset($_GET['search']) ? trim(urldecode($_GET['search'])) : '';

if ($search !== '') {
	$search = mysqli_real_escape_string($db, $search);

	// Campos de la tabla proyectos (nueva estructura)
	$fields = [
		'numero', 'codigo_idi', 'nombre_iniciativa', 'descripcion',
		'instrumento_de_origen', 'tipo_de_inversion', 'eje_estrategico', 'objetivo_estrategico',
		'brecha_principal', 'brecha_especifica', 'localizacion', 'localizacion_2',
		'sector', 'subsector_de_inversion_sni', 'tipo', 'utr',
		'etapa_actual', 'actividad_actual', 'inicio', 'termino'
	];

	$likeClauses = [];
	foreach ($fields as $field) {
		$likeClauses[] = "p.`$field` LIKE '%$search%'";
	}
	$likeClauses[] = "s.sector_descripcion LIKE '%$search%'";

	$sql .= " AND (" . implode(' OR ', $likeClauses) . ")";
}

$sql .= " ORDER BY p.nombre_iniciativa";


//file_put_contents('debug.txt', $sql);
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
$por_numero = array();   // numero => posiciones en $data
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$row['ubicaciones'] = array();
	$data[] = $row;
	$por_numero[(int) $row['numero']][] = count($data) - 1;
}


// Ubicaciones adicionales de las iniciativas encontradas
if ($por_numero) {
	$numeros = implode(',', array_keys($por_numero));
	$ru = $db->query("SELECT numero, descripcion, latitud, longitud FROM ubicaciones WHERE numero IN ($numeros) ORDER BY id");
	if ($ru) {
		while ($u = $ru->fetch_assoc()) {
			$lat = str_replace(',', '.', trim((string) $u['latitud']));
			$lng = str_replace(',', '.', trim((string) $u['longitud']));
			if (!is_numeric($lat) || !is_numeric($lng) || (float) $lat == 0 || (float) $lng == 0) continue;
			if (abs((float) $lat) > 90 || abs((float) $lng) > 180) continue;

			foreach ($por_numero[(int) $u['numero']] as $pos) {
				$data[$pos]['ubicaciones'][] = [
					'descripcion' => $u['descripcion'],
					'lat'         => (float) $lat,
					'lng'         => (float) $lng,
				];
			}
		}
	}
}

$d = ["sEcho" => 1,
        	"iTotalRecords" => count($data),
        	"iTotalDisplayRecords" => count($data),
        	"aaData" => $data ];

header('Content-Type: application/json');
echo json_encode($d);
?>