<?php 
@session_start();
include_once("../conf/db.php");
include('functions.php');
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
<link rel="stylesheet" href="css/styles.css"/>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
async function pdf() {
    const element = document.getElementById('pprint');
    const canvas = await html2canvas(element, {scale: window.devicePixelRatio, useCORS: true, scrollY: 0});
    const imgData = canvas.toDataURL('image/jpeg', 0.98);
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({orientation: 'landscape', unit: 'in', format: 'letter'});
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = pdf.internal.pageSize.getHeight();
    const imgWidth = canvas.width / 96;
    const imgHeight = canvas.height / 96;
    const aspectRatio = imgWidth / imgHeight;
    let finalWidth = pdfWidth;
    let finalHeight = pdfWidth / aspectRatio;
    if (finalHeight > pdfHeight) {
        finalHeight = pdfHeight;
        finalWidth = pdfHeight * aspectRatio;
    }
    pdf.addImage(imgData, 'JPEG', 0, 0, finalWidth, finalHeight);
    const totalPages = pdf.internal.getNumberOfPages();
    for (let i = 1; i <= totalPages; i++) {
        pdf.setPage(i);
        pdf.setFontSize(10);
        pdf.text(`Página ${i} de ${totalPages}`, pdfWidth / 2, pdfHeight - 0.5, { align: 'center' });
    }
    pdf.save('dashboard-iniciativas.pdf');
}

// Función genérica para exportar datos a Excel
function exportarAExcel(datos, nombreArchivo, nombreHoja) {
    // Crear workbook y worksheet
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(datos);
    
    // Agregar la hoja al workbook
    XLSX.utils.book_append_sheet(wb, ws, nombreHoja);
    
    // Descargar el archivo
    XLSX.writeFile(wb, nombreArchivo + '.xlsx');
}
</script>
</head>
<body>

<?php include("header.php");?>

<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php include("sidebar.php");?>
    </div>
    
    <div id="page-content-wrapper">
        <div class="container-fluid p-4">
            
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <h5 class="mb-0">PANEL DE CONTROL</h5>
                <!--
                <button onclick="pdf();" type="button" class="btn btn-secondary btn-sm">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </button>
                -->
            </div>

            <div id="pprint">
<?php
// Obtener estadísticas generales usando la misma lógica de avance financiero
$stats = [
    'total_proyectos' => 0,
    'presupuesto_total' => 0,
    'monto_invertido' => 0,
    'promedio_avance_actividades' => 0,
    'promedio_avance_financiero' => 0
];

// Estadísticas consolidadas (presupuesto e inversión real)
$query_stats = "SELECT 
    COUNT(DISTINCT p.id) as total_proyectos,
    COALESCE(SUM(p.p_diseno + p.p_ejecucion), 0) as presupuesto_total,
    COALESCE(SUM(CASE WHEN a.estado = 'Completa' THEN a.monto ELSE 0 END), 0) as monto_invertido
FROM proyectos p
LEFT JOIN actividades a ON p.id = a.proyecto
WHERE p.proyectos_status = 1";

$result_stats = $db->query($query_stats);
if ($result_stats && $row_stats = $result_stats->fetch_assoc()) {
    $stats['total_proyectos'] = $row_stats['total_proyectos'];
    $stats['presupuesto_total'] = $row_stats['presupuesto_total'];
    $stats['monto_invertido'] = $row_stats['monto_invertido'];
    
    // Calcular porcentaje de avance financiero real
    if ($stats['presupuesto_total'] > 0) {
        $stats['promedio_avance_financiero'] = ($stats['monto_invertido'] / $stats['presupuesto_total']) * 100;
    }
}

// Promedio de avance de actividades (porcentaje de actividades completas)
$query_avance_act = "SELECT 
    COALESCE(
        (SUM(CASE WHEN a.estado = 'Completa' THEN 1 ELSE 0 END) / NULLIF(COUNT(a.id), 0)) * 100, 
        0
    ) as promedio_avance_actividades
FROM proyectos p
LEFT JOIN actividades a ON p.id = a.proyecto
WHERE p.proyectos_status = 1";

$result_avance_act = $db->query($query_avance_act);
if ($result_avance_act && $row_avance = $result_avance_act->fetch_assoc()) {
    $stats['promedio_avance_actividades'] = $row_avance['promedio_avance_actividades'];
}

// Obtener avances por sector
$query_sectores_avance = "SELECT 
    s.sector_id,
    s.sector_descripcion,
    s.sector_color,
    COALESCE(
        (SUM(CASE WHEN a.estado = 'Completa' THEN 1 ELSE 0 END) / NULLIF(COUNT(a.id), 0)) * 100, 
        0
    ) as avance_actividades,
    COALESCE(SUM(p.p_diseno + p.p_ejecucion), 0) as presupuesto_sector,
    COALESCE(SUM(CASE WHEN a.estado = 'Completa' THEN a.monto ELSE 0 END), 0) as monto_invertido_sector
FROM sectores s
LEFT JOIN proyectos p ON s.sector_id = p.sector AND p.proyectos_status = 1
LEFT JOIN actividades a ON p.id = a.proyecto
GROUP BY s.sector_id, s.sector_descripcion, s.sector_color
ORDER BY s.sector_descripcion";

$sectores_avance = [];
$result_sectores = $db->query($query_sectores_avance);
if ($result_sectores) {
    while ($row_sector_avance = $result_sectores->fetch_assoc()) {
        $avance_financiero = 0;
        if ($row_sector_avance['presupuesto_sector'] > 0) {
            $avance_financiero = ($row_sector_avance['monto_invertido_sector'] / $row_sector_avance['presupuesto_sector']) * 100;
        }
        $row_sector_avance['avance_financiero'] = $avance_financiero;
        $sectores_avance[] = $row_sector_avance;
    }
}
?>

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-card-1">
            <h3><?php echo $stats['total_proyectos']; ?></h3>
            <p><i class="fas fa-project-diagram"></i> Total Iniciativas</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card-2">
            <h3>$<?php echo number_format($stats['presupuesto_total'] / 1000000, 1); ?>M</h3>
            <p><i class="fas fa-file-invoice-dollar"></i> Presupuesto Total</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card-3">
            <h3>$<?php echo number_format($stats['monto_invertido'] / 1000000, 1); ?>M</h3>
            <p><i class="fas fa-dollar-sign"></i> Monto Invertido</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card-4">
            <h3><?php echo round($stats['promedio_avance_actividades'], 1); ?>%</h3>
            <p><i class="fas fa-tasks"></i> Avance Promedio</p>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="dashboard-card">
            <div class="section-title d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-pie"></i> Iniciativas por Sector</span>
                <button onclick="exportarPorSector()" class="btn btn-sm btn-success" title="Exportar a Excel">
                    <i class="fas fa-file-excel"></i>
                </button>
            </div>
            <div class="chart-container">
                <canvas id="porSector"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="dashboard-card">
            <div class="section-title d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-bar"></i> Iniciativas por Etapa</span>
                <button onclick="exportarPorEtapa()" class="btn btn-sm btn-success" title="Exportar a Excel">
                    <i class="fas fa-file-excel"></i>
                </button>
            </div>
            <div class="chart-container">
                <canvas id="porEtapa"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Iniciativas por Proceso (ancho completo) -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="dashboard-card">
            <div class="section-title d-flex justify-content-between align-items-center">
                <span><i class="fas fa-project-diagram"></i> Iniciativas por Proceso</span>
                <button onclick="exportarPorProceso()" class="btn btn-sm btn-success" title="Exportar a Excel">
                    <i class="fas fa-file-excel"></i>
                </button>
            </div>
            <div class="chart-container">
                <canvas id="porProceso"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- Avances por Sector -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="progress-section">
            <div class="section-title d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clipboard-check"></i> Avance General de Actividades por Sector</span>
                <button onclick="exportarAvanceActividades()" class="btn btn-sm btn-success" title="Exportar a Excel">
                    <i class="fas fa-file-excel"></i>
                </button>
            </div>
            <?php foreach ($sectores_avance as $sector): ?>
            <div class="progress-item mb-3">
                <div class="progress-label">
                    <span style="display: flex; align-items: center;">
                        <span class="badge" style="background-color: <?php echo $sector['sector_color']; ?>; width: 15px; height: 15px; border-radius: 3px; margin-right: 8px;"></span>
                        <?php echo htmlspecialchars($sector['sector_descripcion']); ?>
                    </span>
                    <span class="text-primary fw-bold"><?php echo round($sector['avance_actividades'], 1); ?>%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" 
                         style="width: <?php echo $sector['avance_actividades']; ?>%; background-color: <?php echo $sector['sector_color']; ?>" 
                         aria-valuenow="<?php echo $sector['avance_actividades']; ?>" 
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="progress-section">
            <div class="section-title d-flex justify-content-between align-items-center">
                <span><i class="fas fa-money-bill-wave"></i> Avance Financiero General por Sector</span>
                <button onclick="exportarAvanceFinanciero()" class="btn btn-sm btn-success" title="Exportar a Excel">
                    <i class="fas fa-file-excel"></i>
                </button>
            </div>
            <?php foreach ($sectores_avance as $sector): ?>
            <div class="progress-item mb-3">
                <div class="progress-label">
                    <span style="display: flex; align-items: center;">
                        <span class="badge" style="background-color: <?php echo $sector['sector_color']; ?>; width: 15px; height: 15px; border-radius: 3px; margin-right: 8px;"></span>
                        <?php echo htmlspecialchars($sector['sector_descripcion']); ?>
                    </span>
                    <span class="text-success fw-bold"><?php echo round($sector['avance_financiero'], 1); ?>%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" 
                         style="width: <?php echo $sector['avance_financiero']; ?>%; background-color: <?php echo $sector['sector_color']; ?>" 
                         aria-valuenow="<?php echo $sector['avance_financiero']; ?>" 
                         aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Top 5 Proyectos por Avance -->
<?php
$query_top = "SELECT 
    p.id,
    p.nombre,
    COALESCE(p.p_diseno + p.p_ejecucion, 0) as presupuesto,
    COALESCE(SUM(CASE WHEN a.estado = 'Completa' THEN a.monto ELSE 0 END), 0) as monto_invertido,
    COUNT(a.id) as total_actividades,
    SUM(CASE WHEN a.estado = 'Completa' THEN 1 ELSE 0 END) as actividades_completas,
    CASE 
        WHEN COUNT(a.id) > 0 THEN (SUM(CASE WHEN a.estado = 'Completa' THEN 1 ELSE 0 END) / COUNT(a.id)) * 100
        ELSE 0 
    END as avance_actividades,
    CASE 
        WHEN (p.p_diseno + p.p_ejecucion) > 0 
        THEN (SUM(CASE WHEN a.estado = 'Completa' THEN a.monto ELSE 0 END) / (p.p_diseno + p.p_ejecucion)) * 100
        ELSE 0 
    END as avance_financiero
FROM proyectos p
LEFT JOIN actividades a ON p.id = a.proyecto
WHERE p.proyectos_status = 1
GROUP BY p.id, p.nombre, p.p_diseno, p.p_ejecucion
HAVING presupuesto > 0
ORDER BY avance_financiero DESC
LIMIT 5";
$result_top = $db->query($query_top);
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="section-title">
                <i class="fas fa-star"></i> Top 5 Iniciativas con Mayor Avance
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Iniciativa</th>
                            <th>Monto Invertido</th>
                            <th>Avance Actividades</th>
                            <th>Avance Financiero</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result_top && $result_top->num_rows > 0) {
                            while ($row = $result_top->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars(substr($row['nombre'], 0, 60)); ?></td>
                            <td>$<?php echo number_format($row['monto_invertido'], 0, ',', '.'); ?></td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: <?php echo $row['avance_actividades']; ?>%">
                                        <?php echo round($row['avance_actividades'], 1); ?>%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?php echo $row['avance_financiero']; ?>%">
                                        <?php echo round($row['avance_financiero'], 1); ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            endwhile;
                        } else {
                            echo '<tr><td colspan="4" class="text-center text-muted">No hay Iniciativas con presupuesto disponibles</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
















</div>

<script>
    const softColors = [
        '#5a6d8f', '#7ca5b8', '#2e5266', '#6fb98f', 
        '#4a8f5e', '#e8a87c', '#d97777', '#9b8ca8',
        '#8b7fa3', '#a5b8c7'
    ];

    // Datos de avances por sector desde PHP
    const sectoresAvance = <?php echo json_encode($sectores_avance); ?>;

    // Variables globales para almacenar datos de gráficos
    let datosSector = null;
    let datosEtapa = null;
    let datosProceso = null;

    // Funciones de exportación de gráficos
    function exportarPorSector() {
        if (!datosSector) {
            alert('No hay datos disponibles para exportar');
            return;
        }
        const datos = [
            ['Sector', 'Cantidad de Iniciativas'],
            ...datosSector.sectores.map((sector, i) => [sector, datosSector.cuantos[i]])
        ];
        exportarAExcel(datos, 'iniciativas-por-sector', 'Por Sector');
    }

    function exportarPorEtapa() {
        if (!datosEtapa) {
            alert('No hay datos disponibles para exportar');
            return;
        }
        const datos = [
            ['Etapa', 'Cantidad de Iniciativas'],
            ...datosEtapa.map(item => [item.etapa, item.cuantos])
        ];
        exportarAExcel(datos, 'iniciativas-por-etapa', 'Por Etapa');
    }

    function exportarPorProceso() {
        if (!datosProceso) {
            alert('No hay datos disponibles para exportar');
            return;
        }
        const datos = [
            ['Proceso', 'Cantidad de Iniciativas'],
            ...datosProceso.map(item => [item.proceso, item.cuantos])
        ];
        exportarAExcel(datos, 'iniciativas-por-proceso', 'Por Proceso');
    }

    // Funciones de exportación de avances
    function exportarAvanceActividades() {
        if (!sectoresAvance || sectoresAvance.length === 0) {
            alert('No hay datos disponibles para exportar');
            return;
        }
        const datos = [
            ['Sector', 'Avance de Actividades (%)'],
            ...sectoresAvance.map(sector => [
                sector.sector_descripcion, 
                Math.round(sector.avance_actividades * 10) / 10
            ])
        ];
        exportarAExcel(datos, 'avance-actividades-por-sector', 'Avance Actividades');
    }

    function exportarAvanceFinanciero() {
        if (!sectoresAvance || sectoresAvance.length === 0) {
            alert('No hay datos disponibles para exportar');
            return;
        }
        const datos = [
            ['Sector', 'Avance Financiero (%)'],
            ...sectoresAvance.map(sector => [
                sector.sector_descripcion, 
                Math.round(sector.avance_financiero * 10) / 10
            ])
        ];
        exportarAExcel(datos, 'avance-financiero-por-sector', 'Avance Financiero');
    }

    // Gráfico por sector
    $(document).ready(function() {
        var token = $("#sid").val();
        $.ajax({
            url: 'api/grafico_por_sector.php?token=' + token,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data && data.error) {
                    console.error("Error controlado por el servidor (Sector):", data.error);
                    return;
                }
                
                // Verificar si hay datos
                if (!data.sectores || data.sectores.length === 0) {
                    document.getElementById('porSector').parentElement.innerHTML = 
                        '<p class="text-center text-muted py-5">No hay datos de sectores disponibles</p>';
                    return;
                }
                
                // Guardar datos para exportación
                datosSector = data;
                
                const ctx = document.getElementById('porSector').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.sectores,
                        datasets: [{
                            label: '# de Iniciativas',
                            data: data.cuantos,
                            backgroundColor: softColors.slice(0, data.sectores.length),
                            borderColor: '#ffffff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
            },
            error: function(xhr, status, error) {
                console.error("Error obteniendo los datos por sector:", xhr.status, xhr.statusText);
                document.getElementById('porSector').parentElement.innerHTML = 
                    '<p class="text-center text-muted py-5">Error al cargar datos de sectores</p>';
            }
        });
    });

    // Gráfico por etapa
    $(document).ready(function() {
        var token = $("#sid").val();
        $.ajax({
            url: 'api/grafico_por_etapa.php?token=' + token,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data && data.error) {
                    console.error("Error controlado por el servidor (Etapa):", data.error);
                    return;
                }

                // Verificar si hay datos
                if (!data || data.length === 0) {
                    document.getElementById('porEtapa').parentElement.innerHTML = 
                        '<p class="text-center text-muted py-5">No hay datos de etapas disponibles</p>';
                    return;
                }

                // Guardar datos para exportación
                datosEtapa = data;

                const etapas = data.map(item => item.etapa);
                const counts = data.map(item => item.cuantos);

                const ctx = document.getElementById('porEtapa').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: etapas,
                        datasets: [{
                            label: 'Cantidad de Iniciativas',
                            data: counts,
                            backgroundColor: 'rgba(90, 109, 143, 0.7)',
                            borderColor: 'rgba(90, 109, 143, 1)',
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error obteniendo los datos por etapa:", xhr.status, xhr.statusText);
                document.getElementById('porEtapa').parentElement.innerHTML = 
                    '<p class="text-center text-muted py-5">Error al cargar datos de etapas</p>';
            }
        });
    });

    // Gráfico por proceso
    $(document).ready(function() {
        var token = $("#sid").val();
        $.ajax({
            url: 'api/grafico_por_proceso.php?token=' + token,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data && data.error) {
                    console.error("Error controlado por el servidor (Proceso):", data.error);
                    return;
                }

                // Verificar si hay datos
                if (!data || data.length === 0) {
                    document.getElementById('porProceso').parentElement.innerHTML = 
                        '<p class="text-center text-muted py-5">No hay datos de procesos disponibles</p>';
                    return;
                }

                // Guardar datos para exportación
                datosProceso = data;

                const procesos = data.map(item => item.proceso);
                const counts = data.map(item => item.cuantos);

                const ctx = document.getElementById('porProceso').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: procesos,
                        datasets: [{
                            label: 'Cantidad de Iniciativas',
                            data: counts,
                            backgroundColor: 'rgba(76, 175, 80, 0.7)',
                            borderColor: 'rgba(76, 175, 80, 1)',
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error obteniendo los datos por proceso:", xhr.status, xhr.statusText);
                document.getElementById('porProceso').parentElement.innerHTML = 
                    '<p class="text-center text-muted py-5">Error al cargar datos de procesos</p>';
            }
        });
    });
</script>

<?php
// ---------------------------------------
// ACTUALIZAR AVANCES DE TODOS LOS PROYECTOS
// ---------------------------------------

try {
    // Obtener todos los proyectos
    $sql_proyectos = "SELECT id FROM proyectos WHERE proyectos_status = 1";
    $result_proyectos = $db->query($sql_proyectos);

    if ($result_proyectos && $result_proyectos->num_rows > 0) {
        while ($proyecto = $result_proyectos->fetch_assoc()) {

            $codigo_proyecto = (int) $proyecto['id'];

            // 1️⃣ Totales del proyecto
            $query_total = "
                SELECT 
                    COUNT(*) AS total_actividades,
                    COALESCE(SUM(monto), 0) AS total_monto
                FROM actividades
                WHERE proyecto = ?
            ";
            $stmt_total = $db->prepare($query_total);
            $stmt_total->bind_param('i', $codigo_proyecto);
            $stmt_total->execute();
            $result_total = $stmt_total->get_result();
            $row_total = $result_total->fetch_assoc();

            $total_actividades = (int) $row_total['total_actividades'];
            $total_monto = (float) $row_total['total_monto'];
            $stmt_total->close();

            // 2️⃣ Actividades completas del proyecto
            $query_completas = "
                SELECT 
                    COUNT(*) AS completadas,
                    COALESCE(SUM(monto), 0) AS monto_completo
                FROM actividades
                WHERE proyecto = ? AND estado = 'Completa'
            ";
            $stmt_completas = $db->prepare($query_completas);
            $stmt_completas->bind_param('i', $codigo_proyecto);
            $stmt_completas->execute();
            $result_completas = $stmt_completas->get_result();
            $row_completas = $result_completas->fetch_assoc();

            $actividades_completas = (int) $row_completas['completadas'];
            $monto_completo = (float) $row_completas['monto_completo'];
            $stmt_completas->close();

            // 3️⃣ Cálculos
            $avance_actividades = ($total_actividades > 0)
                ? ($actividades_completas / $total_actividades) * 100
                : 0;

            $avance_financiero = ($total_monto > 0)
                ? ($monto_completo / $total_monto) * 100
                : 0;

            $avance_actividades = round($avance_actividades, 2);
            $avance_financiero  = round($avance_financiero, 2);

            // 4️⃣ Actualizar proyecto
            $update = "
                UPDATE proyectos 
                SET avance_financiero = ?, avance_actividades = ?
                WHERE id = ?
            ";
            $stmt_update = $db->prepare($update);
            $stmt_update->bind_param('ddi', $avance_financiero, $avance_actividades, $codigo_proyecto);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    // Cerrar resultados
    if ($result_proyectos) $result_proyectos->close();

} catch (Exception $e) {
    // Modo silencioso: no mostrar errores al usuario,
    // pero puedes registrar el problema en un log.
    error_log("Error actualizando avances: " . $e->getMessage());
}
?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>