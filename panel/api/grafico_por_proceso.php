<?php
// ************************************************************
// API: Gráfico de Iniciativas por Proceso
// ************************************************************

// Capturar TODO el output incluyendo errores del include
ob_start();

@session_start();
include_once("../../conf/db.php");

// Descartar TODO el output capturado
ob_end_clean();

// Ahora sí, enviar header limpio
header('Content-Type: application/json; charset=utf-8');

// Verificar conexión
if (!isset($db) || !$db) {
    echo json_encode(['error' => true, 'message' => 'No hay conexión a la base de datos']);
    exit;
}

$db->query("SET NAMES 'utf8'");

// Consulta
$query = "
    SELECT 
        COALESCE(proc.procesos_descripcion, 'Sin proceso') as proceso,
        COUNT(p.id) as cuantos
    FROM proyectos p
    LEFT JOIN procesos proc ON p.proceso = proc.procesos_id
    WHERE p.proyectos_status = 1
    GROUP BY p.proceso, proc.procesos_descripcion
    ORDER BY cuantos DESC
";

$result = $db->query($query);

if (!$result) {
    echo json_encode([
        'error' => true, 
        'message' => 'Error en la consulta',
        'sql_error' => $db->error
    ]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'proceso' => $row['proceso'],
        'cuantos' => (int)$row['cuantos']
    ];
}

echo json_encode($data);
$db->close();
?>