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
$query_total = "SELECT COUNT(*) as total FROM peredifica";
$result_total = $db->query($query_total);
$row_total = $result_total->fetch_assoc();

// Datos por localidad
$query_localidad = "SELECT localidad, COUNT(*) as cantidad FROM peredifica WHERE localidad IS NOT NULL AND localidad != '' GROUP BY localidad ORDER BY cantidad DESC LIMIT 10";
$result_localidad = $db->query($query_localidad);

// Datos por destino1
$query_destino = "SELECT destino1, COUNT(*) as cantidad FROM peredifica WHERE destino1 IS NOT NULL AND destino1 != '' GROUP BY destino1 ORDER BY cantidad DESC LIMIT 10";
$result_destino = $db->query($query_destino);

// Datos por tipo
$query_tipo = "SELECT tipo, COUNT(*) as cantidad FROM peredifica WHERE tipo IS NOT NULL AND tipo != '' GROUP BY tipo ORDER BY cantidad DESC LIMIT 10";
$result_tipo = $db->query($query_tipo);

// Datos por año
$query_ano = "SELECT ano, COUNT(*) as cantidad FROM peredifica WHERE ano IS NOT NULL AND ano != '' GROUP BY ano ORDER BY ano";
$result_ano = $db->query($query_ano);

// Preparar datos para gráficos
$localidades = array();
while($row = $result_localidad->fetch_assoc()) {
    $localidades[] = $row;
}

$destinos = array();
while($row = $result_destino->fetch_assoc()) {
    $destinos[] = $row;
}

$tipos = array();
while($row = $result_tipo->fetch_assoc()) {
    $tipos[] = $row;
}

$anos = array();
while($row = $result_ano->fetch_assoc()) {
    $anos[] = $row;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?> - Dashboard Permisos</title>

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
.stat-card-permisos {
    background: linear-gradient(135deg, #5a6d8f 0%, #7ca5b8 100%);
    color: white;
    border-radius: 8px;
    padding: 25px;
    text-align: center;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.stat-card-permisos h3 {
    font-size: 2.5rem;
    font-weight: bold;
    margin: 0;
}

.stat-card-permisos p {
    font-size: 1rem;
    margin: 5px 0 0 0;
    opacity: 0.95;
}

.btn-dashboard-permisos {
    background: linear-gradient(135deg, #5a6d8f 0%, #7ca5b8 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    font-size: 1.1rem;
    border-radius: 6px;
    transition: all 0.3s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.btn-dashboard-permisos:hover {
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
                    <h5><i class="fa-solid fa-chart-line"></i> DASHBOARD - PERMISOS DE EDIFICACIÓN</h5>
                    <button onclick="window.location='permisos.php'" type="button" class="btn btn-dashboard-permisos">
                        <i class="fa-solid fa-list"></i>&nbsp;Ir a la gestión de permisos de edificación
                    </button>
                </div>

                <div class="container">
                    
                    <!-- Tarjeta de total -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="stat-card-permisos">
                                <h3><?php echo number_format($row_total['total']); ?></h3>
                                <p><i class="fa-solid fa-building"></i> Total de Permisos de Edificación</p>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row mb-4">
                        <!-- Por Localidad -->
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="section-title">
                                    <i class="fa-solid fa-location-dot"></i> Permisos por Localidad
                                </div>
                                <div class="chart-container">
                                    <canvas id="chartLocalidad"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Por Destino -->
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="section-title">
                                    <i class="fa-solid fa-building"></i> Permisos por Destino
                                </div>
                                <div class="chart-container">
                                    <canvas id="chartDestino"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <!-- Por Tipo -->
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="section-title">
                                    <i class="fa-solid fa-tags"></i> Permisos por Tipo
                                </div>
                                <div class="chart-container">
                                    <canvas id="chartTipo"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Por Año -->
                        <div class="col-md-6">
                            <div class="dashboard-card">
                                <div class="section-title">
                                    <i class="fa-solid fa-calendar"></i> Permisos por Año
                                </div>
                                <div class="chart-container">
                                    <canvas id="chartAno"></canvas>
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
const localidadesData = <?php echo json_encode($localidades); ?>;
const destinosData = <?php echo json_encode($destinos); ?>;
const tiposData = <?php echo json_encode($tipos); ?>;
const anosData = <?php echo json_encode($anos); ?>;

// Gráfico de Localidades (Barras)
new Chart(document.getElementById('chartLocalidad'), {
    type: 'bar',
    data: {
        labels: localidadesData.map(item => item.localidad),
        datasets: [{
            label: 'Cantidad de permisos',
            data: localidadesData.map(item => item.cantidad),
            backgroundColor: 'rgba(90, 109, 143, 0.7)',
            borderColor: 'rgba(90, 109, 143, 1)',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0,
                    stepSize: 1
                }
            }
        }
    }
});

// Gráfico de Destinos (Doughnut)
new Chart(document.getElementById('chartDestino'), {
    type: 'doughnut',
    data: {
        labels: destinosData.map(item => item.destino1),
        datasets: [{
            data: destinosData.map(item => item.cantidad),
            backgroundColor: softColors.slice(0, destinosData.length),
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

// Gráfico de Tipos (Barras horizontales)
new Chart(document.getElementById('chartTipo'), {
    type: 'bar',
    data: {
        labels: tiposData.map(item => item.tipo),
        datasets: [{
            label: 'Cantidad de permisos',
            data: tiposData.map(item => item.cantidad),
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

// Gráfico de Años (Línea)
new Chart(document.getElementById('chartAno'), {
    type: 'line',
    data: {
        labels: anosData.map(item => item.ano),
        datasets: [{
            label: 'Permisos por año',
            data: anosData.map(item => item.cantidad),
            backgroundColor: 'rgba(90, 109, 143, 0.2)',
            borderColor: 'rgba(90, 109, 143, 1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgba(90, 109, 143, 1)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7
        }]
    },
    options: {
        ...commonOptions,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
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


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>
