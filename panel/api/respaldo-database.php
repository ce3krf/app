<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);



include("../../conf/db.php");        


// Configuración inicial
$dump = "-- SQL Dump generado automáticamente\n";
$dump .= "-- Base de datos: `$database`\n";
$dump .= "-- Fecha: " . date("Y-m-d H:i:s") . "\n\n";

$db->set_charset("utf8");

// Obtener lista de tablas
$tablesResult = $db->query("SHOW TABLES");
if (!$tablesResult) {
    die("Error al obtener las tablas: " . $db->error);
}

while ($row = $tablesResult->fetch_row()) {
    $table = $row[0];

    // DROP TABLE
    $dump .= "DROP TABLE IF EXISTS `$table`;\n";

    // CREATE TABLE
    $createTableResult = $db->query("SHOW CREATE TABLE `$table`");
    $createRow = $createTableResult->fetch_assoc();
    $dump .= $createRow["Create Table"] . ";\n\n";

    // INSERT INTO (si tiene datos)
    $dataResult = $db->query("SELECT * FROM `$table`");
    if ($dataResult->num_rows > 0) {
        while ($rowData = $dataResult->fetch_assoc()) {
            $columns = array_map(function($col) use ($db) {
                return "`" . $db->real_escape_string($col) . "`";
            }, array_keys($rowData));

            $values = array_map(function($val) use ($db) {
                if (is_null($val)) return "NULL";
                return "'" . $db->real_escape_string($val) . "'";
            }, array_values($rowData));

            $dump .= "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");\n";
        }
        $dump .= "\n";
    }
}

$db->close();

// Guardar el dump en archivo
$nombreArchivo = "dump_" . date("Ymd_His") . ".sql";
file_put_contents($nombreArchivo, $dump);

// Configurar encabezados para descarga
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
header('Content-Length: ' . filesize($nombreArchivo));  
readfile($nombreArchivo);
// Eliminar el archivo después de la descarga
unlink($nombreArchivo);

?>
