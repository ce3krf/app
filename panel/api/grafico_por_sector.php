<?php
// grafico_por_sector.php
// ************************************************
// DEBUG: Asegúrate de que los errores se muestren si hay un problema en la conexión
// ************************************************
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
// ************************************************

header('Content-Type: application/json');

// IMPORTANTE: Si esta ruta es incorrecta, el script muere aquí.
include("../../conf/db.php"); 

// 🚨 CONTROL DE FALLO DE CONEXIÓN: Verifica que $db sea un objeto válido antes de usarlo
if (!isset($db) || !($db instanceof mysqli) || $db->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Fallo fatal de conexión a la Base de Datos. Revise "../../conf/db.php".']);
    exit;
}

// 1. Establecer el charset
$db->query("SET NAMES 'utf8'");

// 2. Consulta de datos
$query = "
    SELECT 
        s.sector_descripcion AS sector,
        COUNT(p.id) AS cuantos
    FROM sectores s
    INNER JOIN proyectos p ON s.sector_id = p.sector
    GROUP BY s.sector_id, s.sector_descripcion
    ORDER BY cuantos DESC
";

$result = $db->query($query);

if (!$result) {
    // CAPTURA DE ERRORES SQL/DB
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta SQL (sectores): ' . $db->error]);
    exit;
}

$data = [
    'sectores' => [],
    'cuantos' => []
];

while ($row = $result->fetch_assoc()) {
    $data['sectores'][] = $row['sector'];
    $data['cuantos'][] = (int)$row['cuantos'];
}

$result->free();
$db->close();

// Devolver el JSON final
echo json_encode($data);
?>