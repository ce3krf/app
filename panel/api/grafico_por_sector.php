<?php
// grafico_por_sector.php
// Iniciativas por sector. Se agrupa por el NOMBRE del sector (proyectos.sector),
// no por cod_sector, para que no se fusionen sectores distintos que tengan
// cod_sector vacío o repetido.

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

include_once("../../conf/db.php");
require __DIR__ . '/inc_seguridad.php';

if (!isset($db) || !($db instanceof mysqli) || $db->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Fallo de conexión a la Base de Datos. Revise "../../conf/db.php".']);
    exit;
}

$db->set_charset('utf8mb4');

// Filtros (mismos que index.php)
$where = "p.proyectos_status = 1";
if (isset($_GET['instrumento']) && $_GET['instrumento'] !== '') {
    $where .= " AND p.n_inst = " . (int)$_GET['instrumento'];
}
if (isset($_GET['plan']) && in_array($_GET['plan'], ['Si', 'No'], true)) {
    $where .= " AND p.seleccionada_pm_sjm = '" . $_GET['plan'] . "'";
}

$query = "
    SELECT
        COALESCE(NULLIF(TRIM(p.sector), ''), 'Sin sector') AS sector_nombre,
        COUNT(p.id) AS cuantos
    FROM proyectos p
    WHERE {$where}
    GROUP BY COALESCE(NULLIF(TRIM(p.sector), ''), 'Sin sector')
    ORDER BY cuantos DESC, sector_nombre ASC
";

$result = $db->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta SQL (sectores): ' . $db->error]);
    exit;
}

$data = ['sectores' => [], 'cuantos' => []];

while ($row = $result->fetch_assoc()) {
    $data['sectores'][] = $row['sector_nombre'];
    $data['cuantos'][]  = (int)$row['cuantos'];
}

$result->free();
$db->close();

echo json_encode($data, JSON_UNESCAPED_UNICODE);
