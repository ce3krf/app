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
// ---------------------------------------
// Todas las métricas del dashboard se calculan en tiempo real desde la tabla actividades.
// No se usa proyectos.avance_actividades ni proyectos.avance_financiero para el dashboard,
// ya que esos campos solo se actualizan al cargar esta página y no reflejan cambios
// hechos desde otras páginas (edición de actividades).
// Unidades: actividades.monto e proyectos.p_diseno/p_ejecucion están en miles de pesos.
// Para mostrar en M$ se divide por 1.000.
// ---------------------------------------

// Actualizar proyectos.avance_actividades y avance_financiero en segundo plano
// (para que las fichas individuales de proyecto también muestren valores actualizados)
try {
    $result_proy = $db->query("SELECT id FROM proyectos WHERE proyectos_status = 1");
    if ($result_proy) {
        while ($proy = $result_proy->fetch_assoc()) {
            $pid = (int) $proy['id'];
            $s1 = $db->prepare("SELECT COUNT(*) as tot, COALESCE(SUM(monto),0) as monto_tot FROM actividades WHERE proyecto = ?");
            $s1->bind_param('i', $pid); $s1->execute();
            $r1 = $s1->get_result()->fetch_assoc(); $s1->close();

            $s2 = $db->prepare("SELECT COUNT(*) as comp, COALESCE(SUM(monto),0) as monto_comp FROM actividades WHERE proyecto = ? AND estado = 'Completa'");
            $s2->bind_param('i', $pid); $s2->execute();
            $r2 = $s2->get_result()->fetch_assoc(); $s2->close();

            $av_act = ($r1['tot']       > 0) ? round($r2['comp']       / $r1['tot']       * 100, 2) : 0;
            $av_fin = ($r1['monto_tot'] > 0) ? round($r2['monto_comp'] / $r1['monto_tot'] * 100, 2) : 0;

            $su = $db->prepare("UPDATE proyectos SET avance_actividades = ?, avance_financiero = ? WHERE id = ?");
            $su->bind_param('ddi', $av_act, $av_fin, $pid); $su->execute(); $su->close();
        }
        $result_proy->close();
    }
} catch (Exception $e) {
    error_log("Error actualizando avances: " . $e->getMessage());
}

// ESTADÍSTICAS GENERALES
$stats = [
    'total_proyectos'           => 0,
    'presupuesto_total'         => 0,
    'monto_invertido'           => 0,
    'promedio_avance_financiero'=> 0
];

// Total proyectos y presupuesto
$r = $db->query("SELECT COUNT(id) as total_proyectos,
    COALESCE(SUM(COALESCE(p_diseno,0) + COALESCE(p_ejecucion,0)), 0) as presupuesto_total
FROM proyectos WHERE proyectos_status = 1");
if ($r && $row = $r->fetch_assoc()) {
    $stats['total_proyectos']   = $row['total_proyectos'];
    $stats['presupuesto_total'] = $row['presupuesto_total'];
}

// Monto invertido: suma de actividades Completa en tiempo real
$r = $db->query("SELECT COALESCE(SUM(a.monto), 0) as monto_invertido
FROM actividades a
INNER JOIN proyectos p ON p.id = a.proyecto
WHERE p.proyectos_status = 1 AND a.estado = 'Completa'");
if ($r && $row = $r->fetch_assoc()) {
    $stats['monto_invertido'] = $row['monto_invertido'];
}

// Avance financiero global = monto invertido / presupuesto total
if ($stats['presupuesto_total'] > 0) {
    $stats['promedio_avance_financiero'] = ($stats['monto_invertido'] / $stats['presupuesto_total']) * 100;
}

// AVANCES POR SECTOR — todo calculado en tiempo real desde actividades
// proyectos.sector es INT (FK a sectores.sector_id)
$query_sectores_avance = "SELECT
    s.sector_id,
    s.sector_descripcion,
    s.sector_color,
    COALESCE(act.avance_actividades_sector, 0) as avance_actividades,
    COALESCE(fin.avance_financiero_sector, 0)  as avance_financiero_sector
FROM sectores s
-- Avance actividades: promedio por proyecto (completas/total)
LEFT JOIN (
    SELECT p.sector,
           AVG(sub.avance_act) as avance_actividades_sector
    FROM (
        SELECT a.proyecto,
               SUM(CASE WHEN a.estado = 'Completa' THEN 1 ELSE 0 END) / COUNT(*) * 100 as avance_act
        FROM actividades a
        GROUP BY a.proyecto
    ) sub
    INNER JOIN proyectos p ON p.id = sub.proyecto
    WHERE p.proyectos_status = 1
    GROUP BY p.sector
) act ON act.sector = s.sector_id
-- Avance financiero: promedio por proyecto (monto_completo/presupuesto_proyecto)
-- Cada proyecto pesa igual, evita que un proyecto grande distorsione el sector
LEFT JOIN (
    SELECT p.sector,
           AVG(sub.avance_fin) as avance_financiero_sector
    FROM (
        SELECT a.proyecto,
               CASE
                   WHEN (COALESCE(p2.p_diseno,0) + COALESCE(p2.p_ejecucion,0)) > 0
                   THEN SUM(CASE WHEN a.estado = 'Completa' THEN a.monto ELSE 0 END)
                        / (COALESCE(p2.p_diseno,0) + COALESCE(p2.p_ejecucion,0)) * 100
                   ELSE 0
               END as avance_fin
        FROM actividades a
        INNER JOIN proyectos p2 ON p2.id = a.proyecto
        WHERE p2.proyectos_status = 1
        GROUP BY a.proyecto, p2.p_diseno, p2.p_ejecucion
    ) sub
    INNER JOIN proyectos p ON p.id = sub.proyecto
    WHERE p.proyectos_status = 1
    GROUP BY p.sector
) fin ON fin.sector = s.sector_id
ORDER BY s.sector_descripcion";

$sectores_avance = [];
$result_sectores = $db->query($query_sectores_avance);
if ($result_sectores) {
    while ($row_sector_avance = $result_sectores->fetch_assoc()) {
        $row_sector_avance['avance_financiero'] = $row_sector_avance['avance_financiero_sector'];
        $sectores_avance[] = $row_sector_avance;
    }
}


?>

<!-- Tarjetas de Estadísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card stat-card-1">
            <h4><?php echo $stats['total_proyectos']; ?></h4>
            <p><i class="fas fa-project-diagram"></i> Total Iniciativas</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card-2">
            <h4>$<?php echo number_format($stats['presupuesto_total'] / 1000, 1); ?>M</h4>
            <p><i class="fas fa-file-invoice-dollar"></i> Presupuesto Total</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card-3">
            <h4>$<?php echo number_format($stats['monto_invertido'] / 1000, 1); ?>M</h4>
            <p><i class="fas fa-dollar-sign"></i> Monto Invertido</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card-4">
            <h4><?php echo round($stats["promedio_avance_financiero"], 1); ?>%</h4>
            <p><i class="fas fa-percentage"></i> Avance Financiero</p>
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
    COALESCE(p.p_diseno,0) + COALESCE(p.p_ejecucion,0) as presupuesto,
    COALESCE(SUM(CASE WHEN a.estado = 'Completa' THEN a.monto ELSE 0 END), 0) as monto_invertido,
    COUNT(a.id) as total_actividades,
    SUM(CASE WHEN a.estado = 'Completa' THEN 1 ELSE 0 END) as actividades_completas,
    CASE 
        WHEN COUNT(a.id) > 0 THEN (SUM(CASE WHEN a.estado = 'Completa' THEN 1 ELSE 0 END) / COUNT(a.id)) * 100
        ELSE 0 
    END as avance_actividades,
    CASE 
        WHEN (COALESCE(p.p_diseno,0) + COALESCE(p.p_ejecucion,0)) > 0
        THEN (SUM(CASE WHEN a.estado = 'Completa' THEN a.monto ELSE 0 END) / (COALESCE(p.p_diseno,0) + COALESCE(p.p_ejecucion,0))) * 100
        ELSE 0 
    END as avance_financiero
FROM proyectos p
INNER JOIN actividades a ON p.id = a.proyecto
WHERE p.proyectos_status = 1
GROUP BY p.id, p.nombre, p.p_diseno, p.p_ejecucion
ORDER BY avance_actividades DESC, avance_financiero DESC
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
                // Ajustar altura del contenedor según cantidad de procesos
                const alturaMinPorBarra = 50;
                const alturaMinima = 300;
                const alturaNecesaria = Math.max(alturaMinima, procesos.length * alturaMinPorBarra);
                document.getElementById('porProceso').parentElement.style.height = alturaNecesaria + 'px';
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
                            },
                            y: {
                                ticks: {
                                    autoSkip: false,
                                    crossAlign: 'far',
                                    font: {
                                        size: 12
                                    },
                                    callback: function(value, index) {
                                        const label = procesos[index];
                                        if (!label) return '';
                                        // Dividir etiquetas largas en múltiples líneas
                                        const maxLen = 35;
                                        if (label.length <= maxLen) return label;
                                        const words = label.split(' ');
                                        const lines = [];
                                        let current = '';
                                        words.forEach(word => {
                                            if ((current + ' ' + word).trim().length <= maxLen) {
                                                current = (current + ' ' + word).trim();
                                            } else {
                                                if (current) lines.push(current);
                                                current = word;
                                            }
                                        });
                                        if (current) lines.push(current);
                                        return lines;
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                left: 10
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