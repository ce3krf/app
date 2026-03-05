<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// ************************************************************

@session_start();
include("../conf/db.php");		
include('functions.php');

// Datos de parametros
$qParam = "SELECT * FROM parametros WHERE parametros_codproyecto='001' LIMIT 1";
$rParam = $db->query($qParam);
$rowSys  = $rParam ? $rParam->fetch_assoc() : [];

// Datos del servidor
$phpVersion   = phpversion();
$mysqlVersion = $db->server_info;
$serverOS     = php_uname('s') . ' ' . php_uname('r');
$serverSW     = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';
$serverName   = $_SERVER['SERVER_NAME']     ?? $_SERVER['HTTP_HOST'] ?? 'N/A';
$maxUpload    = ini_get('upload_max_filesize');
$maxPost      = ini_get('post_max_size');
$memLimit     = ini_get('memory_limit');
$timezone     = date_default_timezone_get();
$fechaServidor = date('d-m-Y H:i:s');

// Último backup
$lastBackup = !empty($rowSys['parametros_lastbackup'])
    ? date('d-m-Y H:i:s', strtotime($rowSys['parametros_lastbackup']))
    : null;
$lastBackupUser = $rowSys['parametros_lastusuariobackup'] ?? null;

// Estadísticas rápidas de BD
$totalTablas = 0;
$rTabs = $db->query("SHOW TABLES");
if ($rTabs) $totalTablas = $rTabs->num_rows;

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

<script src="functions.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.css"/>

<style>

/* ── Layout general ── */
.info-wrapper {
    max-width: 860px;
    margin: 30px auto 60px;
}

/* ── Card base ── */
.info-card {
    background: white;
    border-radius: 16px;
    padding: 32px 36px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    margin-bottom: 24px;
}

/* ── Card header ── */
.info-card-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f0f4f8;
}

.info-card-header .hdr-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    color: white;
    flex-shrink: 0;
}

.hdr-icon.blue   { background: linear-gradient(135deg, #4a8ecd, #1976d2); box-shadow: 0 4px 12px rgba(74,142,205,.3); }
.hdr-icon.green  { background: linear-gradient(135deg, #43a047, #2e7d32); box-shadow: 0 4px 12px rgba(67,160,71,.3); }
.hdr-icon.purple { background: linear-gradient(135deg, #8e44ad, #6c3483); box-shadow: 0 4px 12px rgba(142,68,173,.3); }
.hdr-icon.orange { background: linear-gradient(135deg, #ef6c00, #bf360c); box-shadow: 0 4px 12px rgba(239,108,0,.3); }

.info-card-header .hdr-text h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
}

.info-card-header .hdr-text p {
    margin: 0;
    font-size: 0.82rem;
    color: #9ca3af;
}

/* ── Filas de datos ── */
.info-row {
    display: flex;
    align-items: flex-start;
    padding: 11px 0;
    border-bottom: 1px solid #f5f7fa;
    gap: 12px;
}

.info-row:last-child { border-bottom: none; }

.info-row .ir-label {
    min-width: 210px;
    font-size: 0.84rem;
    color: #6b7280;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.info-row .ir-label i {
    width: 16px;
    text-align: center;
    color: #9ca3af;
}

.info-row .ir-value {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e293b;
    word-break: break-word;
}

/* ── Badges de estado ── */
.badge-ok {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #dcfce7;
    color: #166534;
    border-radius: 20px;
    padding: 3px 12px;
    font-size: 0.82rem;
    font-weight: 600;
}

.badge-warn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #fef9c3;
    color: #854d0e;
    border-radius: 20px;
    padding: 3px 12px;
    font-size: 0.82rem;
    font-weight: 600;
}

/* ── Hero superior ── */
.info-hero {
    background: linear-gradient(135deg, #1976d2 0%, #0d47a1 100%);
    border-radius: 16px;
    padding: 36px 40px;
    color: white;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 24px;
    box-shadow: 0 6px 24px rgba(25,118,210,.35);
}

.info-hero .hero-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    flex-shrink: 0;
}

.info-hero .hero-body h2 {
    margin: 0 0 4px;
    font-size: 1.6rem;
    font-weight: 700;
}

.info-hero .hero-body p {
    margin: 0;
    font-size: 0.92rem;
    opacity: 0.8;
}

.info-hero .hero-body .sys-version {
    display: inline-block;
    margin-top: 10px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    padding: 4px 16px;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.03em;
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
                        <h5 style="margin: 0; color: var(--main); font-weight: 600;">INFORMACIÓN DEL SISTEMA</h5>
                    </div>
                </div>
            </div>

            <div class="container">
            <div class="info-wrapper">

                <!-- ── Hero ── -->
                <div class="info-hero">
                    <div class="hero-icon"><i class="fa-solid fa-server"></i></div>
                    <div class="hero-body">
                        <h2><?php echo htmlspecialchars($rowSys['parametros_titulo'] ?? 'Sistema'); ?></h2>
                        <p>Código de proyecto: <strong><?php echo htmlspecialchars($rowSys['parametros_codproyecto'] ?? '001'); ?></strong>
                           &nbsp;·&nbsp; <?php echo $fechaServidor; ?></p>
                        <?php if (!empty($rowSys['parametros_version'])): ?>
                        <span class="sys-version">v<?php echo htmlspecialchars($rowSys['parametros_version']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ── Tarjeta: Información del sistema ── -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="hdr-icon blue"><i class="fa-solid fa-circle-info"></i></div>
                        <div class="hdr-text">
                            <h5>Información del Sistema</h5>
                            <p>Datos generales registrados en la base de datos</p>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-tag"></i> Nombre del sistema</div>
                        <div class="ir-value"><?php echo htmlspecialchars($rowSys['parametros_titulo'] ?? '—'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-barcode"></i> Código de proyecto</div>
                        <div class="ir-value"><?php echo htmlspecialchars($rowSys['parametros_codproyecto'] ?? '—'); ?></div>
                    </div>
                    <?php if (!empty($rowSys['parametros_version'])): ?>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-code-branch"></i> Versión</div>
                        <div class="ir-value"><?php echo htmlspecialchars($rowSys['parametros_version']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($rowSys['parametros_empresa'])): ?>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-building"></i> Empresa</div>
                        <div class="ir-value"><?php echo htmlspecialchars($rowSys['parametros_empresa']); ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-clock"></i> Zona horaria</div>
                        <div class="ir-value"><?php echo htmlspecialchars($timezone); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-table"></i> Tablas en la BD</div>
                        <div class="ir-value"><?php echo $totalTablas; ?> tablas</div>
                    </div>
                </div>

                <!-- ── Tarjeta: Último respaldo ── -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="hdr-icon green"><i class="fa-solid fa-database"></i></div>
                        <div class="hdr-text">
                            <h5>Último Respaldo</h5>
                            <p>Información del backup más reciente registrado</p>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="ir-label"><i class="fa-regular fa-calendar"></i> Fecha del respaldo</div>
                        <div class="ir-value">
                            <?php if ($lastBackup): ?>
                                <span class="badge-ok"><i class="fa-solid fa-circle-check"></i> <?php echo $lastBackup; ?></span>
                            <?php else: ?>
                                <span class="badge-warn"><i class="fa-solid fa-triangle-exclamation"></i> Sin respaldos registrados</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-user"></i> Realizado por</div>
                        <div class="ir-value"><?php echo $lastBackupUser ? htmlspecialchars($lastBackupUser) : '—'; ?></div>
                    </div>
                </div>

                <!-- ── Tarjeta: Entorno PHP ── -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="hdr-icon purple"><i class="fa-brands fa-php"></i></div>
                        <div class="hdr-text">
                            <h5>Entorno PHP</h5>
                            <p>Configuración y versión del intérprete PHP</p>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-code"></i> Versión PHP</div>
                        <div class="ir-value"><?php echo htmlspecialchars($phpVersion); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-memory"></i> Límite de memoria</div>
                        <div class="ir-value"><?php echo htmlspecialchars($memLimit); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-upload"></i> Tamaño máx. subida</div>
                        <div class="ir-value"><?php echo htmlspecialchars($maxUpload); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-envelope-open"></i> Tamaño máx. POST</div>
                        <div class="ir-value"><?php echo htmlspecialchars($maxPost); ?></div>
                    </div>
                </div>

                <!-- ── Tarjeta: Servidor ── -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="hdr-icon orange"><i class="fa-solid fa-network-wired"></i></div>
                        <div class="hdr-text">
                            <h5>Servidor y Base de Datos</h5>
                            <p>Sistema operativo, software web y motor de BD</p>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-globe"></i> Servidor web</div>
                        <div class="ir-value"><?php echo htmlspecialchars($serverSW); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-laptop"></i> Sistema operativo</div>
                        <div class="ir-value"><?php echo htmlspecialchars($serverOS); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-server"></i> Nombre del servidor</div>
                        <div class="ir-value"><?php echo htmlspecialchars($serverName); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="ir-label"><i class="fa-solid fa-database"></i> Versión MySQL</div>
                        <div class="ir-value"><?php echo htmlspecialchars($mysqlVersion); ?></div>
                    </div>
                </div>

                <div class="text-center mt-2 mb-4">
                    <small style="color: #9ca3af;">
                        <i class="fa-solid fa-shield-halved"></i>
                        Información visible solo para administradores del sistema
                    </small>
                </div>

            </div><!-- /info-wrapper -->
            </div><!-- /container -->

        </div>
    </div>
</div>

<a href="#" class="cd-top"></a>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>

</body>
</html>
