<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-12-31
// ************************************************************

@session_start();
include("../conf/db.php");		
include('functions.php');

// *** NUEVO *** Leer último backup desde parametros
$qParam = "SELECT parametros_lastbackup, parametros_lastusuariobackup FROM parametros WHERE parametros_codproyecto='001' LIMIT 1";
$rParam = $db->query($qParam);
$rowBackupInfo = $rParam ? $rParam->fetch_assoc() : null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?></title>

<link rel="icon" type="image/png" href="../favicon.png">

<script src="js/jquery-3.5.1.min.js"></script>	

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="functions.js"></script>

<link rel="stylesheet" type="text/css" href="css/styles.css"/>

<style>
.backup-card {
    background: white;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    max-width: 700px;
    margin: 40px auto;
}

.backup-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #4a8ecd 0%, #1976d2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    box-shadow: 0 4px 15px rgba(74, 142, 205, 0.3);
}

.backup-icon i {
    font-size: 2.5rem;
    color: white;
}

.backup-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #2c3e50;
    text-align: center;
    margin-bottom: 20px;
}

.backup-description {
    color: #6c757d;
    font-size: 1rem;
    line-height: 1.7;
    text-align: center;
    margin-bottom: 30px;
}

.backup-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border-left: 4px solid #1976d2;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.backup-info h6 {
    color: #1565c0;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 0.95rem;
}

.backup-info ul {
    margin: 0;
    padding-left: 20px;
    color: #424242;
}

.backup-info li {
    margin-bottom: 8px;
    font-size: 0.9rem;
}

/* *** NUEVO *** Estilo panel último backup */
.last-backup-panel {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    border-left: 4px solid #2e7d32;
    border-radius: 8px;
    padding: 18px 20px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.last-backup-panel .lbp-icon {
    font-size: 1.8rem;
    color: #2e7d32;
    flex-shrink: 0;
}

.last-backup-panel .lbp-body {
    line-height: 1.4;
}

.last-backup-panel .lbp-label {
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #388e3c;
    margin-bottom: 4px;
}

.last-backup-panel .lbp-date {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1b5e20;
}

.last-backup-panel .lbp-user {
    font-size: 0.88rem;
    color: #33691e;
    margin-top: 2px;
}

.last-backup-panel.no-backup {
    background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
    border-left-color: #f57f17;
}

.last-backup-panel.no-backup .lbp-icon { color: #f57f17; }
.last-backup-panel.no-backup .lbp-label { color: #e65100; }
.last-backup-panel.no-backup .lbp-date { color: #bf360c; font-size: 0.95rem; font-weight: 600; }
/* fin *** NUEVO *** */

.btn-backup {
    background: linear-gradient(135deg, #4a8ecd 0%, #1976d2 100%);
    border: none;
    padding: 14px 40px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 50px;
    color: white;
    box-shadow: 0 4px 15px rgba(74, 142, 205, 0.3);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.btn-backup:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(74, 142, 205, 0.4);
    background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%);
    color: white;
}

.btn-backup i {
    font-size: 1.2rem;
}

.access-denied {
    text-align: center;
    padding: 60px 20px;
}

.access-denied i {
    font-size: 4rem;
    color: #dc3545;
    margin-bottom: 20px;
}

.access-denied h3 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 15px;
}

.access-denied p {
    color: #6c757d;
    font-size: 1.1rem;
}

.loading-spinner {
    display: none;
    margin-left: 10px;
}

.btn-backup.loading .loading-spinner {
    display: inline-block;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

</head>
<body>

<?php include("header.php");?>

<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php include("sidebar.php");?>
    </div>
    
    <div id="page-content-wrapper">
        <div class="container-fluid">

            <div class="top-info-div">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 style="margin: 0; color: var(--main); font-weight: 600;">RESPALDO DE BASE DE DATOS</h5>
                    </div>
                </div>
            </div>

            <div class="container">
                
                <?php if($row_tabla["usuarios_profile"] == 'ADMINISTRADOR'){ ?>
                
                <div class="backup-card">
                    <div class="backup-icon">
                        <i class="fa-solid fa-database"></i>
                    </div>
                    
                    <h2 class="backup-title">Generar Respaldo del Sistema</h2>
                    
                    <p class="backup-description">
                        Crea una copia de seguridad completa de toda la información del sistema. 
                        Este proceso guardará todos los datos en un archivo comprimido que podrás 
                        descargar y almacenar de forma segura.
                    </p>

                    <!-- *** NUEVO *** Panel último backup -->
                    <?php if (!empty($rowBackupInfo['parametros_lastbackup'])): ?>
                        <?php
                            // Formatear fecha a dd-mm-yyyy HH:MM:SS
                            $fechaBackup = date('d-m-Y H:i:s', strtotime($rowBackupInfo['parametros_lastbackup']));
                            $usuarioBackup = htmlspecialchars($rowBackupInfo['parametros_lastusuariobackup']);
                        ?>
                        <div class="last-backup-panel">
                            <div class="lbp-icon"><i class="fa-solid fa-circle-check"></i></div>
                            <div class="lbp-body">
                                <div class="lbp-label">✅ Último respaldo realizado</div>
                                <div class="lbp-date"><i class="fa-regular fa-calendar"></i> <?php echo $fechaBackup; ?></div>
                                <div class="lbp-user"><i class="fa-solid fa-user"></i> <?php echo $usuarioBackup; ?></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="last-backup-panel no-backup">
                            <div class="lbp-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <div class="lbp-body">
                                <div class="lbp-label">⚠️ Sin respaldos registrados</div>
                                <div class="lbp-date">Aún no se ha generado ningún respaldo del sistema.</div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!-- fin *** NUEVO *** -->
                    
                    <div class="backup-info">
                        <h6>📋 ¿Qué incluye el respaldo?</h6>
                        <ul>
                            <li>Todas las tablas de la base de datos</li>
                            <li>Estructura completa (definiciones y relaciones)</li>
                            <li>Todos los registros y datos almacenados</li>
                            <li>Archivo comprimido en formato ZIP</li>
                        </ul>
                    </div>
                    
                    <div class="backup-info">
                        <h6>⚡ Recomendaciones</h6>
                        <ul>
                            <li>Realiza respaldos de forma periódica (semanal o mensual)</li>
                            <li>Guarda los archivos en un lugar seguro</li>
                            <li>Verifica que la descarga se complete correctamente</li>
                            <li>El proceso puede tomar unos segundos</li>
                        </ul>
                    </div>
                    
                    <form id="form1" name="form1" method="post" action="backup.php">
                        <input type="hidden" name="accion" value="backup">
                        <div class="text-center">
                            <button type="submit" class="btn-backup" id="backupBtn">
                                <i class="fa-solid fa-download"></i>
                                Generar y Descargar Respaldo
                                <span class="loading-spinner">
                                    <i class="fa-solid fa-spinner"></i>
                                </span>
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <small style="color: #9ca3af;">
                            <i class="fa-solid fa-shield-halved"></i> 
                            Solo usuarios administradores pueden generar respaldos
                        </small>
                    </div>
                </div>
                
                <?php } else { ?>
                
                <div class="backup-card">
                    <div class="access-denied">
                        <i class="fa-solid fa-lock"></i>
                        <h3>Acceso Denegado</h3>
                        <p>No tienes permisos para realizar respaldos de la base de datos.</p>
                        <p style="font-size: 0.9rem; margin-top: 20px;">
                            Esta función está disponible únicamente para usuarios con rol de administrador.
                        </p>
                    </div>
                </div>
                
                <?php } ?>

            </div>

        </div>
    </div>
</div>

<!-- Botón Back to Top -->
<a href="#" class="cd-top"></a>

<?php include("footer.php");?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'backup') {
    $backupFile = "backups/backup_" . date("Y-m-d_H-i-s") . ".sql";
    $zipFile = $backupFile . ".zip";
    
    $sqlScript = "SET FOREIGN_KEY_CHECKS=0;\n";
    $tablesResult = $db->query("SHOW TABLES");
    
    while ($row = $tablesResult->fetch_array()) {
        $table = $row[0];
        $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";
        
        $createTableResult = $db->query("SHOW CREATE TABLE `$table`");
        $createTableRow = $createTableResult->fetch_array();
        $sqlScript .= $createTableRow[1] . ";\n\n";
        
        $dataResult = $db->query("SELECT * FROM `$table`");
        while ($dataRow = $dataResult->fetch_assoc()) {
            $values = array_map(function($value) use ($db) {
                return isset($value) ? "'" . $db->real_escape_string($value) . "'" : "NULL";
            }, array_values($dataRow));
            
            $sqlScript .= "INSERT INTO `$table` VALUES(" . implode(", ", $values) . ");\n";
        }
        $sqlScript .= "\n";
    }
    
    $sqlScript .= "SET FOREIGN_KEY_CHECKS=1;\n";
    file_put_contents($backupFile, $sqlScript);
    
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($backupFile);
        $zip->close();
    }
    
    unlink($backupFile);

    // *** NUEVO *** Registrar el backup en parametros
    // Usuario: userid - nombre  (tomados de la sesión)
    $backupUserId   = $db->real_escape_string($_SESSION['net_fulltrust_fas_users_id']);
    $backupUserName = $db->real_escape_string($_SESSION['net_fulltrust_fas_users_name']);
    $lastusuario    = $backupUserId . ' - ' . $backupUserName;
    $lastfecha      = date('Y-m-d H:i:s');

    $sqlReg = "UPDATE parametros 
               SET parametros_lastbackup = '$lastfecha',
                   parametros_lastusuariobackup = '$lastusuario'
               WHERE parametros_codproyecto = '001'";
    $db->query($sqlReg);
    // fin *** NUEVO ***

    ?><script>window.location='<?php echo $zipFile;?>';</script><?php
}
?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>

<script>
$(document).ready(function() {
    $('#form1').on('submit', function() {
        const btn = $('#backupBtn');
        btn.addClass('loading');
        btn.prop('disabled', true);
        btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Generando respaldo...');
    });
});
</script>

</body>
</html>
