<?php
// grafico_por_etapa.php
// Iniciativas por etapa, usando proyectos.cod_etapa_actual / proyectos.etapa_actual

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

include_once("../../conf/db.php");
require __DIR__ . '/inc_seguridad.php';

if (!isset($db) || !($db instanceof mysqli) || $db->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Fallo de conexión a la Base de Datos. Revise "../../conf/db.php".']);
    exit;
}

$db->set_charset('utf8mb4');

// Validar token
$token = isset($_GET['token']) ? $_GET['token'] : '';
if ($token === '') {
    http_response_code(401);
    echo json_encode(['error' => 'Token no proporcionado.']);
    exit;
}

// Filtros (mismos que index.php)
$where = "p.proyectos_status = 1";
if (isset($_GET['instrumento']) && $_GET['instrumento'] !== '') {
    $where .= " AND p.n_inst = " . (int)$_GET['instrumento'];
}
if (isset($_GET['plan']) && in_array($_GET['plan'], ['Si', 'No'], true)) {
    $where .= " AND p.seleccionada_pm_sjm = '" . $_GET['plan'] . "'";
}

// Sin etapa se muestra al final
$query = "
    SELECT
        COALESCE(NULLIF(TRIM(MAX(p.etapa_actual)), ''), 'Sin etapa') AS etapa,
        COUNT(p.id) AS cuantos
    FROM proyectos p
    WHERE {$where}
    GROUP BY p.cod_etapa_actual
    ORDER BY (p.cod_etapa_actual IS NULL), p.cod_etapa_actual ASC
";

$result = $db->query($query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta SQL (etapas): ' . $db->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'etapa'   => $row['etapa'],
        'cuantos' => (int)$row['cuantos']
    ];
}

$result->free();
$db->close();

echo json_encode($data, JSON_UNESCAPED_UNICODE);
