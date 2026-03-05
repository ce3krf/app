<?php
// grafico_por_etapa.php

// ************************************************
// DEBUG: Descomentar estas líneas si la conexión sigue fallando 
// para ver el error exacto de PHP, ya sea de ruta o de conexión.
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// ************************************************

header('Content-Type: application/json');

// IMPORTANTE: Si la ruta es incorrecta, el script muere aquí.
include_once("../../conf/db.php"); 

// 🚨 CONTROL DE FALLO DE CONEXIÓN: Captura si $db no fue definido o si la conexión falló
if (!isset($db) || !($db instanceof mysqli) || (isset($db->connect_error) && $db->connect_error)) {
    http_response_code(500);
    echo json_encode(['error' => 'Fallo al conectar a la Base de Datos. Revise "../../conf/db.php" y asegúrese de que la variable de conexión se llama $db.']);
    exit;
}

// 1. Establecer el charset (necesario para UTF-8)
$db->query("SET NAMES 'utf8'");

// Validar token (Lógica existente)
$token = isset($_GET['token']) ? $_GET['token'] : '';
if (empty($token)) {
    http_response_code(401);
    echo json_encode(['error' => 'Token no proporcionado. Asegúrate de que $("#sid").val() tenga el valor correcto.']);
    exit;
}

// 2. Consulta para obtener conteos por etapa
$query = "
    SELECT 
        e.etapas_descripcion AS etapa,
        COUNT(p.id) AS cuantos
    FROM etapas e
    INNER JOIN proyectos p ON e.etapas_id = p.etapa
    GROUP BY e.etapas_id, e.etapas_descripcion
    ORDER BY e.etapas_id ASC
";

$result = $db->query($query);

if (!$result) {
    // CAPTURA DE ERRORES SQL/DB
    http_response_code(500);
    echo json_encode(['error' => 'Error en la consulta SQL (etapas): ' . $db->error . '. Revise su tabla y columnas.']);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'etapa' => $row['etapa'],
        'cuantos' => (int)$row['cuantos']
    ];
}

// 3. Cierre
$result->free();
$db->close();

echo json_encode($data);
?>