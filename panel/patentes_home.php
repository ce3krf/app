<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: <?php echo date('Y-m-d H:i:s'); 
// 
?>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
@session_start();
include("../../conf/db.php");		
include('functions.php');



// Consultas para el dashboard
$query_total = "SELECT COUNT(*) as total FROM patentes";
$result_total = $db->query($query_total);
$row_total = $result_total->fetch_assoc();

// Datos por tipo de patente
$query_tipo = "SELECT TIPO_PATENTE, COUNT(*) as cantidad FROM patentes WHERE TIPO_PATENTE IS NOT NULL AND TIPO_PATENTE != '' GROUP BY TIPO_PATENTE ORDER BY cantidad DESC";
$result_tipo = $db->query($query_tipo);

// Datos por actividad (giro)
$query_actividad = "SELECT actividad2, COUNT(*) as cantidad FROM patentes WHERE actividad2 IS NOT NULL AND actividad2 != '' GROUP BY actividad2 ORDER BY cantidad DESC LIMIT 10";
$result_actividad = $db->query($query_actividad);

// Datos por dirección (top 10 calles/sectores)
$query_direccion = "
    SELECT 
        SUBSTRING_INDEX(DIRECCION_COMERCIAL, ' ', 2) as sector,
        COUNT(*) as cantidad 
    FROM patentes 
    WHERE DIRECCION_COMERCIAL IS NOT NULL AND DIRECCION_COMERCIAL != '' 
    GROUP BY sector 
    ORDER BY cantidad DESC 
    LIMIT 10
";
$result_direccion = $db->query($query_direccion);

// Estadísticas adicionales
$query_stats = "
    SELECT 
        COUNT(DISTINCT RUT) as total_contribuyentes,
        COUNT(CASE WHEN LATITUD IS NOT NULL AND LATITUD != '' THEN 1 END) as con_georeferencia,
        COUNT(CASE WHEN ROL_PROP IS NOT NULL AND ROL_PROP != '' THEN 1 END) as con_rol_propiedad
    FROM patentes
";
$result_stats = $db->query($query_stats);
$row_stats = $result_stats->fetch_assoc();

// Preparar datos para gráficos
$tipos = array();
while($row = $result_tipo->fetch_assoc()) {
    $tipos[] = $row;
}

$actividades = array();
while($row = $result_actividad->fetch_assoc()) {
    $actividades[] = $row;
}

$direcciones = array();
while($row = $result_direccion->fetch_assoc()) {
    $direcciones[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?> - Dashboard Patentes</title>

<script src="js/jquery-3.5.1.min.js"></script>	
<link rel="icon" type="image/png" href="../img/gobierno-regional-ohiggins.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<link rel="stylesheet" type="text/css" href="css/styles.css"/>
<link rel="stylesheet" type="text/css" href="css/dashboard.css"/>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.stat-card-patentes {
    background: linear-gradient(135deg, #5a6d8f 0%, #7ca5b8 100%);
    color: white;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    height: 100%;
}

.stat-card-patentes h3 {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
}

.stat-card-patentes p {
    font-size: 0.9rem;
    margin: 5px 0 0 0;
    opacity: 0.95;
}

.stat-card-patentes i {
    font-size: 1.2rem;
    margin-right: 5px;
}

.btn-dashboard-patentes {
    background: linear-gradient(135deg, #5a6d8f 0%, #7ca5b8 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    font-size: 1.1rem;
    border-radius: 6px;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-dashboard-patentes:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(90, 109, 143, 0.3);
    color: white;
    background: linear-gradient(135deg, #7ca5b8 0%, #5a6d8f 100%);
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
        <div class="container-fluid p-4">

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h5><i class="fa-solid fa-store"></i> DASHBOARD - PATENTES COMERCIALES</h5>
                    <button onclick="window.location='patentes.php'" type="button" class="btn btn-dashboard-patentes">
                        <i class="fa-solid fa-list"></i>&nbsp;Ir a la gestión de patentes comerciales
                    </button>
                </div>

                <div class="container">
                    
                    <!-- Tarjetas de estadísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stat-card-patentes">
                                <h3><?php echo number_format($row_total['total']); ?></h3>
                                <p><i class="fa-solid fa-store"></i> Total Patentes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card-patentes">
                                <h3><?php echo number_format($row_stats['total_contribuyentes']); ?></h3>
                                <p><i class="fa-solid fa-users"></i> Contribuyentes</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card-patentes">
                                <h3><?php echo number_format($row_stats['con_georeferencia']); ?></h3>
                                <p><i class="fa-solid fa-map-marker-alt"></i> Georeferenciadas</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card-patentes">
                                <h3><?php echo number_format($row_stats['con_rol_propiedad']); ?></h3>
                                <p><i class="fa-solid fa-home"></i> Con Rol Propiedad</p>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row mb-4">
                        <!-- Por Tipo de Patente -->
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="section-title">
                                    <i class="fa-solid fa-certificate"></i> Patentes por Tipo
                                </div>
                                <div class="chart-container">
                                    <canvas id="chartTipo"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Por Actividad/Giro -->
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="section-title">
                                    <i class="fa-solid fa-briefcase"></i> Top 10 Actividades Comerciales
                                </div>
                                <div class="chart-container">
                                    <canvas id="chartActividad"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Información adicional -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="progress-section">
                                <div class="section-title">
                                    <i class="fa-solid fa-percentage"></i> Cobertura de Datos
                                </div>
                                <div class="progress-item">
                                    <div class="progress-label">
                                        <span>Patentes Georeferenciadas</span>
                                        <span class="text-primary fw-bold">
                                            <?php echo round(($row_stats['con_georeferencia'] / $row_total['total']) * 100, 1); ?>%
                                        </span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                             style="width: <?php echo ($row_stats['con_georeferencia'] / $row_total['total']) * 100; ?>%">
                                        </div>
                                    </div>
                                </div>
                                <div class="progress-item mt-3">
                                    <div class="progress-label">
                                        <span>Con Rol de Propiedad</span>
                                        <span class="text-success fw-bold">
                                            <?php echo round(($row_stats['con_rol_propiedad'] / $row_total['total']) * 100, 1); ?>%
                                        </span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?php echo ($row_stats['con_rol_propiedad'] / $row_total['total']) * 100; ?>%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="section-title">
                                    <i class="fa-solid fa-info-circle"></i> Resumen
                                </div>
                                <div class="p-3">
                                    <p class="mb-2"><strong>Total de patentes comerciales:</strong> <?php echo number_format($row_total['total']); ?></p>
                                    <p class="mb-2"><strong>Contribuyentes únicos:</strong> <?php echo number_format($row_stats['total_contribuyentes']); ?></p>
                                    <p class="mb-2"><strong>Tipos de patentes:</strong> <?php echo count($tipos); ?></p>
                                    <p class="mb-2"><strong>Actividades registradas:</strong> <?php echo $result_actividad->num_rows; ?>+</p>
                                    <p class="mb-0"><strong>Promedio patentes/contribuyente:</strong> 
                                        <?php 
                                        if ($row_stats['total_contribuyentes'] > 0) {
                                            echo number_format($row_total['total'] / $row_stats['total_contribuyentes'], 2);
                                        } else {
                                            echo '0';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
        </div>
    </div>
</div>

<?php include("footer.php");?>
        </div>
    </div>


<script>
// Colores suaves inspirados en index.php
const softColors = [
    '#5a6d8f', '#7ca5b8', '#2e5266', '#6fb98f', 
    '#4a8f5e', '#e8a87c', '#d97777', '#9b8ca8',
    '#8b7fa3', '#a5b8c7'
];

// Configuración común para todos los gráficos
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false
};

// Datos de PHP a JavaScript
const tiposData = <?php echo json_encode($tipos); ?>;
const actividadesData = <?php echo json_encode($actividades); ?>;
const direccionesData = <?php echo json_encode($direcciones); ?>;

// Gráfico de Tipos de Patente (Doughnut)
new Chart(document.getElementById('chartTipo'), {
    type: 'doughnut',
    data: {
        labels: tiposData.map(item => item.TIPO_PATENTE),
        datasets: [{
            data: tiposData.map(item => item.cantidad),
            backgroundColor: softColors.slice(0, tiposData.length),
            borderColor: '#ffffff',
            borderWidth: 2
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// Gráfico de Actividades (Barras horizontales)
new Chart(document.getElementById('chartActividad'), {
    type: 'bar',
    data: {
        labels: actividadesData.map(item => {
            // Acortar textos muy largos
            const label = item.actividad2;
            return label.length > 30 ? label.substring(0, 30) + '...' : label;
        }),
        datasets: [{
            label: 'Cantidad de patentes',
            data: actividadesData.map(item => item.cantidad),
            backgroundColor: 'rgba(124, 165, 184, 0.7)',
            borderColor: 'rgba(124, 165, 184, 1)',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        ...commonOptions,
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                    stepSize: 1
                }
            }
        }
    }
});


</script>

</html
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>
