<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 25 de Agosto de 2023
// ************************************************************


//error_reporting(E_ALL);
//ini_set('display_errors', 1);

	@session_start();
	
	
	include("../db.php");		
	
	
	$sql="set names utf8";
	if(!$result = $db->query($sql)){
    	die('Hay un error [' . $db->error . ']');
	}	
	
	// Solo un ADMINISTRADOR con sesión válida puede descargar el respaldo
	$count = 0;
	if (($_SESSION['logeado'] ?? '') === 'si' && !empty($_SESSION['net_fulltrust_fas_id'])) {
		$stmt = $db->prepare("SELECT usuarios_id FROM usuarios WHERE usuarios_id = ? AND usuarios_password = ?
		                      AND usuarios_status = 1 AND usuarios_profile = 'ADMINISTRADOR' LIMIT 1");
		$uid  = (int) $_SESSION['net_fulltrust_fas_id'];
		$pass = (string) ($_SESSION['net_fulltrust_fas_users_password'] ?? '');
		$stmt->bind_param('is', $uid, $pass);
		$stmt->execute();
		$count = $stmt->get_result()->num_rows;
	}
	
	if($count < 1){
		?>
        <script>
		window.location='index.php';
		</script>		
        <?php
		exit;
	}

	require_once __DIR__ . '/inc_auditoria.php';
	audit_registrar($db, 'RESPALDO', ['detalle' => 'Descarga de respaldo (respaldo_db.php)']);
	
	

    
    // Nombre del archivo de respaldo
    $backupFileName = 'respaldo.sql';
    
    // Establecer la conexión a la base de datos
    $conn = new mysqli($hostname, $username, $password, $database);
    
    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    // Configurar la codificación de caracteres
    $conn->set_charset("utf8");
    
    // Crear el archivo de respaldo
    $backupFile = fopen($backupFileName, 'w');
    
    // Incluir cláusula IF NOT EXISTS en la creación de tablas
    fwrite($backupFile, "-- Respaldando la base de datos $database --\n\n");
    fwrite($backupFile, "SET NAMES 'utf8';\n\n");
    fwrite($backupFile, "SET foreign_key_checks = 0;\n\n");
    
    // Obtener todas las tablas de la base de datos
    $tables = array();
    $result = $conn->query("SHOW TABLES");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_row()) {
            $tables[] = $row[0];
        }
    }
    
    // Iterar a través de las tablas y generar el SQL de respaldo
    foreach ($tables as $table) {
        $tableStructure = "";
        $tableData = "";
    
        // Obtener la estructura de la tabla
        $result = $conn->query("SHOW CREATE TABLE $table");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $tableStructure = $row['Create Table'] . ";";
        }
    
        // Obtener los datos de la tabla
        $result = $conn->query("SELECT * FROM $table");
        if ($result->num_rows > 0) {
            $tableData .= "INSERT INTO $table (";
            $fields = array();
            while ($fieldInfo = $result->fetch_field()) {
                $fields[] = $fieldInfo->name;
            }
            $tableData .= implode(", ", $fields) . ") VALUES\n";
    
            while ($row = $result->fetch_assoc()) {
                $rowValues = array();
                foreach ($fields as $field) {
                    $rowValues[] = "'" . $conn->real_escape_string($row[$field]) . "'";
                }
                $tableData .= "(" . implode(", ", $rowValues) . "),\n";
            }
            // Eliminar la coma y el salto de línea finales
            $tableData = rtrim($tableData, ",\n");
            $tableData .= ";\n";
        }
    
        // Escribir la estructura y los datos de la tabla en el archivo de respaldo
        fwrite($backupFile, "-- Estructura de la tabla $table --\n");
        fwrite($backupFile, "DROP TABLE IF EXISTS $table;\n");
        fwrite($backupFile, $tableStructure . "\n\n");
        fwrite($backupFile, "-- Datos de la tabla $table --\n");
        fwrite($backupFile, $tableData . "\n\n");
    }
    
    // Cerrar la conexión a la base de datos
    $conn->close();
    
    // Cerrar el archivo de respaldo
    fclose($backupFile);
    
    // Comprimir el archivo de respaldo
    $zipFileName = 'respaldo.zip';
    $zip = new ZipArchive();
    if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($backupFileName);
        $zip->close();
        unlink($backupFileName); // Eliminar el archivo SQL original
    } else {
        die("Error al comprimir el archivo de respaldo.");
    }
    
    // Ofrecer el archivo comprimido para su descarga
    header("Content-type: application/zip");
    header("Content-Disposition: attachment; filename=$zipFileName");
    readfile($zipFileName);
    
    // Eliminar el archivo comprimido después de la descarga
    unlink($zipFileName);
    ?>
    