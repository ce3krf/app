<?php 
@session_start();
include_once("../conf/db.php");
include('functions.php');

// -----------------------------------------------
// FILTROS ACTIVOS
// -----------------------------------------------
$filtro_instrumento = isset($_GET['instrumento']) && $_GET['instrumento'] !== '' ? (int)$_GET['instrumento'] : null;
$filtro_plan        = isset($_GET['plan'])        && $_GET['plan']        !== '' ? $_GET['plan']              : null;

// Validar seleccionada_pm_sjm
if ($filtro_plan !== null && !in_array($filtro_plan, ['Si', 'No'])) {
    $filtro_plan = null;
}

// Construir cláusula WHERE para proyectos según alias
// Instrumento -> proyectos.n_inst | Plan Maestro -> proyectos.seleccionada_pm_sjm
function where_proyectos($alias, $db, $filtro_instrumento, $filtro_plan) {
    $a = $alias !== '' ? $alias . '.' : '';
    $w = "{$a}proyectos_status = 1";
    if ($filtro_instrumento !== null) {
        $w .= " AND {$a}n_inst = " . (int)$filtro_instrumento;
    }
    if ($filtro_plan !== null) {
        $w .= " AND {$a}seleccionada_pm_sjm = '" . $db->real_escape_string($filtro_plan) . "'";
    }
    return $w;
}
$where_extra_np = where_proyectos('', $db, $filtro_instrumento, $filtro_plan);

// Cargar instrumentos para el select
$instrumentos = [];
$res_inst = $db->query("SELECT instrumentos_id, instrumentos_descripcion FROM instrumentos ORDER BY instrumentos_descripcion");
if ($res_inst) {
    while ($r = $res_inst->fetch_assoc()) {
        $instrumentos[] = $r;
    }
}
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
<script src="functions.js"></script>
<link rel="stylesheet" href="css/styles.css"/>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
async function pdf() {
    const btn = document.getElementById('btnPdf');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

    try {
        const element = document.getElementById('pprint');

        // Mostrar filtros solo durante la captura
        const pdfFiltros = document.getElementById('pdf-filtros');
        pdfFiltros.style.display = 'block';

        // Capturar el contenido completo a escala 2x
        const canvas = await html2canvas(element, {
            scale: 2,
            useCORS: true,
            scrollY: 0,
            windowWidth: element.scrollWidth,
            width: element.scrollWidth
        });

        const { jsPDF } = window.jspdf;

        // Carta vertical, márgenes 0.5in
        const marginIn  = 0.5;
        const pageW     = 8.5;
        const pageH     = 11;
        const printW    = pageW - marginIn * 2;   // 7.5in
        const printH    = pageH - marginIn * 2;   // 10in por página

        // Escala: px del canvas → pulgadas del PDF
        const pxPerIn   = canvas.width / printW;
        const sliceH_px = printH * pxPerIn;

        const totalPages = Math.ceil(canvas.height / sliceH_px);

        const doc = new jsPDF({ orientation: 'portrait', unit: 'in', format: 'letter' });

        for (let page = 0; page < totalPages; page++) {
            if (page > 0) doc.addPage();

            const srcY      = page * sliceH_px;
            const srcH      = Math.min(sliceH_px, canvas.height - srcY);
            const sliceH_in = srcH / pxPerIn;

            const slice  = document.createElement('canvas');
            slice.width  = canvas.width;
            slice.height = srcH;
            slice.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcH, 0, 0, canvas.width, srcH);

            const imgData = slice.toDataURL('image/jpeg', 0.92);
            doc.addImage(imgData, 'JPEG', marginIn, marginIn, printW, sliceH_in);

            // Número de página
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text(`Página ${page + 1} de ${totalPages}`, pageW / 2, pageH - 0.25, { align: 'center' });
        }

        doc.save('dashboard-panel-control.pdf');
    } finally {
        // Volver a ocultar los filtros
        const pdfFiltros = document.getElementById('pdf-filtros');
        if (pdfFiltros) pdfFiltros.style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
}

// Función genérica para exportar datos a Excel
function exportarAExcel(datos, nombreArchivo, nombreHoja) {
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(datos);
    XLSX.utils.book_append_sheet(wb, ws, nombreHoja);
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
            
            <!-- Título -->
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <h5 class="mb-0">PANEL DE CONTROL</h5>
                <button id="btnPdf" onclick="pdf();" type="button" class="btn btn-secondary btn-sm">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </button>
            </div>

            <!-- ============================================================
                 PANEL DE FILTROS
                 ============================================================ -->
            <form method="GET" action="" class="mb-4">
                <div class="card card-body py-3 px-4 bg-light border-0 shadow-sm">
                    <div class="row g-3 align-items-end">
                        <!-- Instrumento -->
                        <div class="col-md-4 col-sm-6">
                            <label for="filtro_instrumento" class="form-label fw-semibold mb-1">
                                <i class="fas fa-layer-group me-1 text-secondary"></i> Instrumento
                            </label>
                            <select name="instrumento" id="filtro_instrumento" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <?php foreach ($instrumentos as $inst): ?>
                                <option value="<?php echo $inst['instrumentos_id']; ?>"
                                    <?php echo ($filtro_instrumento == $inst['instrumentos_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($inst['instrumentos_descripcion']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Parte del Plan -->
                        <div class="col-md-3 col-sm-6">
                            <label for="filtro_plan" class="form-label fw-semibold mb-1">
                                <i class="fas fa-clipboard-list me-1 text-secondary"></i> Parte del Plan
                            </label>
                            <select name="plan" id="filtro_plan" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="Si"  <?php echo ($filtro_plan === 'Si')  ? 'selected' : ''; ?>>Sí</option>
                                <option value="No"  <?php echo ($filtro_plan === 'No')  ? 'selected' : ''; ?>>No</option>
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="col-md-3 col-sm-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                <i class="fas fa-filter me-1"></i> Filtrar
                            </button>
                            <?php if ($filtro_instrumento !== null || $filtro_plan !== null): ?>
                            <a href="index.php" class="btn btn-outline-secondary btn-sm px-3">
                                <i class="fas fa-times me-1"></i> Limpiar
                            </a>
                            <?php endif; ?>
                        </div>

                        <!-- Etiquetas de filtros activos -->
                        <?php if ($filtro_instrumento !== null || $filtro_plan !== null): ?>
                        <div class="col-12">
                            <small class="text-muted">Filtrando por:</small>
                            <?php if ($filtro_instrumento !== null):
                                $nombre_inst = '';
                                foreach ($instrumentos as $inst) {
                                    if ($inst['instrumentos_id'] == $filtro_instrumento) {
                                        $nombre_inst = $inst['instrumentos_descripcion'];
                                        break;
                                    }
                                }
                            ?>
                            <span class="badge bg-primary ms-1">
                                <i class="fas fa-layer-group me-1"></i><?php echo htmlspecialchars($nombre_inst); ?>
                            </span>
                            <?php endif; ?>
                            <?php if ($filtro_plan !== null): ?>
                            <span class="badge bg-info ms-1">
                                <i class="fas fa-clipboard-list me-1"></i>Plan Maestro: <?php echo $filtro_plan; ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
            <!-- FIN PANEL DE FILTROS -->

            <div id="pprint">

            <!-- Filtros activos — visible solo en la captura PDF, oculto en pantalla -->
            <div id="pdf-filtros" style="display:none; margin-bottom:1rem; padding:0.6rem 1rem; background:#f8f9fa; border-radius:6px; border:1px solid #dee2e6; font-size:0.85rem;">
                <strong>Filtros aplicados:</strong>
                <?php if ($filtro_instrumento === null && $filtro_plan === null): ?>
                    <span style="color:#6c757d;"> Ninguno (mostrando todos)</span>
                <?php else: ?>
                    <?php if ($filtro_instrumento !== null): ?>
                        <?php
                        $nombre_inst_pdf = '';
                        foreach ($instrumentos as $inst) {
                            if ($inst['instrumentos_id'] == $filtro_instrumento) {
                                $nombre_inst_pdf = $inst['instrumentos_descripcion'];
                                break;
                            }
                        }
                        ?>
                        <span style="display:inline-block;background:#0d6efd;color:#fff;border-radius:4px;padding:2px 8px;margin-left:6px;">
                            Instrumento: <?php echo htmlspecialchars($nombre_inst_pdf); ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($filtro_plan !== null): ?>
                        <span style="display:inline-block;background:#0dcaf0;color:#000;border-radius:4px;padding:2px 8px;margin-left:6px;">
                            Plan Maestro: <?php echo htmlspecialchars($filtro_plan); ?>
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

<?php
// ---------------------------------------
// Métricas calculadas directamente desde la tabla proyectos.
// Presupuesto: suma_costo_total (si viene en 0 o NULL, se suma
//              costos_perfil_prefactibilidad + costos_diseno + costos_ejecucion).
// Monto invertido: gasto_perfil_prefactibilidad + gasto_diseno + gasto_ejecucion.
// DIVISOR_MONTOS: ajustar según la unidad en que se guardan los montos
//   (1000 si están en miles de pesos -> se muestra en M$).
// ---------------------------------------
define('DIVISOR_MONTOS', 1000);

$stats = [
    'total_proyectos'            => 0,
    'presupuesto_total'          => 0,
    'monto_invertido'            => 0,
    'promedio_avance_financiero' => 0
];

$sql_stats = "SELECT
    COUNT(id) AS total_proyectos,
    COALESCE(SUM(
        COALESCE(NULLIF(suma_costo_total, 0),
                 COALESCE(costos_perfil_prefactibilidad,0) + COALESCE(costos_diseno,0) + COALESCE(costos_ejecucion,0))
    ), 0) AS presupuesto_total,
    COALESCE(SUM(
        COALESCE(gasto_perfil_prefactibilidad,0) + COALESCE(gasto_diseno,0) + COALESCE(gasto_ejecucion,0)
    ), 0) AS monto_invertido
FROM proyectos
WHERE {$where_extra_np}";

$r = $db->query($sql_stats);
if ($r && $row = $r->fetch_assoc()) {
    $stats['total_proyectos']   = (int)$row['total_proyectos'];
    $stats['presupuesto_total'] = (float)$row['presupuesto_total'];
    $stats['monto_invertido']   = (float)$row['monto_invertido'];
} elseif (!$r) {
    error_log("Error estadísticas dashboard: " . $db->error);
}

if ($stats['presupuesto_total'] > 0) {
    $stats['promedio_avance_financiero'] = ($stats['monto_invertido'] / $stats['presupuesto_total']) * 100;
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
            <h4>$<?php echo number_format($stats['presupuesto_total'] / DIVISOR_MONTOS, 1, ',', '.'); ?>M</h4>
            <p><i class="fas fa-file-invoice-dollar"></i> Presupuesto Total</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card stat-card-3">
            <h4>$<?php echo number_format($stats['monto_invertido'] / DIVISOR_MONTOS, 1, ',', '.'); ?>M</h4>
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

<!-- Gráfico: Iniciativas por Sector (barras + detalle) -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="section-title d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-bar"></i> Iniciativas por Sector</span>
                <span>
                    <small id="sectorResumen" class="text-muted me-2"></small>
                    <button onclick="exportarPorSector()" class="btn btn-sm btn-success" title="Exportar a Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>
                </span>
            </div>
            <div class="chart-container" id="porSectorContenedor" style="position:relative; height:460px;">
                <canvas id="porSector"></canvas>
            </div>
            <div id="sectorDetalle" class="mt-3"></div>
        </div>
    </div>
</div>

<!-- Gráfico: Iniciativas por Etapa -->
<div class="row mb-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="section-title d-flex justify-content-between align-items-center">
                <span><i class="fas fa-chart-column"></i> Iniciativas por Etapa</span>
                <button onclick="exportarPorEtapa()" class="btn btn-sm btn-success" title="Exportar a Excel">
                    <i class="fas fa-file-excel"></i>
                </button>
            </div>
            <div class="chart-container" style="position:relative; height:380px;">
                <canvas id="porEtapa"></canvas>
            </div>
        </div>
    </div>
</div>

</div>

<script>
    // Paleta amplia para que sectores distintos no repitan color
    const softColors = [
        '#5a6d8f', '#7ca5b8', '#2e5266', '#6fb98f', '#4a8f5e',
        '#e8a87c', '#d97777', '#9b8ca8', '#c9a227', '#3f8f8f',
        '#b5651d', '#8fa86f', '#a05a7a', '#4f6d7a', '#d4a5a5',
        '#6b8e23', '#7a6fb0', '#c97b4a', '#5f9ea0', '#9e7b5b'
    ];

    // Devuelve n colores repitiendo la paleta si hay más categorías que colores
    function coloresPara(n) {
        return Array.from({ length: n }, (_, i) => softColors[i % softColors.length]);
    }

    // Parámetros de filtro para las APIs AJAX
    const apiFilters = <?php echo json_encode(array_filter([
        'instrumento' => $filtro_instrumento !== null ? (string)$filtro_instrumento : null,
        'plan'        => $filtro_plan,
    ], function($v){ return $v !== null; })); ?>;
    const apiParams = new URLSearchParams(apiFilters).toString();

    // Variables globales para almacenar datos de gráficos
    let datosSector = null;
    let datosEtapa = null;

    // Funciones de exportación de gráficos
    function exportarPorSector() {
        if (!datosSector) { alert('No hay datos disponibles para exportar'); return; }
        const datos = [
            ['Sector', 'Cantidad de Iniciativas'],
            ...datosSector.sectores.map((sector, i) => [sector, datosSector.cuantos[i]])
        ];
        exportarAExcel(datos, 'iniciativas-por-sector', 'Por Sector');
    }

    function exportarPorEtapa() {
        if (!datosEtapa) { alert('No hay datos disponibles para exportar'); return; }
        const datos = [
            ['Etapa', 'Cantidad de Iniciativas'],
            ...datosEtapa.map(item => [item.etapa, item.cuantos])
        ];
        exportarAExcel(datos, 'iniciativas-por-etapa', 'Por Etapa');
    }

    // ---------------------------------------------------------
    // Utilidades para etiquetas de datos
    // ---------------------------------------------------------
    const fuenteBase = '"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';

    function sumar(arr) { return arr.reduce((a, b) => a + b, 0); }

    function porcentaje(valor, total) {
        if (!total) return '0%';
        return (valor / total * 100).toFixed(1).replace('.', ',') + '%';
    }

    // Recorta un texto con "…" si no cabe en el ancho disponible
    function recortarTexto(ctx, texto, maxAncho) {
        if (ctx.measureText(texto).width <= maxAncho) return texto;
        let t = texto;
        while (t.length > 1 && ctx.measureText(t + '…').width > maxAncho) t = t.slice(0, -1);
        return t + '…';
    }

    // Divide etiquetas largas en varias líneas (para el eje X)
    function dividirEtiqueta(label, maxLen) {
        if (!label || label.length <= maxLen) return label;
        const lineas = [];
        let actual = '';
        label.split(' ').forEach(p => {
            if ((actual + ' ' + p).trim().length <= maxLen) {
                actual = (actual + ' ' + p).trim();
            } else {
                if (actual) lineas.push(actual);
                actual = p;
            }
        });
        if (actual) lineas.push(actual);
        return lineas;
    }

    // Plugin columnas: valor (%) sobre cada columna
    const pluginValoresColumnas = {
        id: 'valoresColumnas',
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            const meta  = chart.getDatasetMeta(0);
            const datos = chart.data.datasets[0].data;
            const total = sumar(datos);

            ctx.save();
            ctx.font = 'bold 12px ' + fuenteBase;
            ctx.fillStyle = '#212529';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'bottom';
            meta.data.forEach((barra, i) => {
                const { x, y } = barra.getProps(['x', 'y'], true);
                ctx.fillText(datos[i] + ' (' + porcentaje(datos[i], total) + ')', x, y - 4);
            });
            ctx.restore();
        }
    };

    function tooltipConPorcentaje(ctx) {
        const total = sumar(ctx.dataset.data);
        return ' ' + ctx.raw + ' iniciativas (' + porcentaje(ctx.raw, total) + ')';
    }

    // ---------------------------------------------------------
    // Gráfico por sector: barras horizontales, de mayor a menor
    // ---------------------------------------------------------

    // Plugin barras horizontales: valor (%) al final de cada barra
    const pluginValoresBarras = {
        id: 'valoresBarras',
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            const meta  = chart.getDatasetMeta(0);
            const datos = chart.data.datasets[0].data;
            const total = sumar(datos);

            ctx.save();
            ctx.font = 'bold 12px ' + fuenteBase;
            ctx.fillStyle = '#212529';
            ctx.textAlign = 'left';
            ctx.textBaseline = 'middle';
            meta.data.forEach((barra, i) => {
                const { x, y } = barra.getProps(['x', 'y'], true);
                ctx.fillText(datos[i] + ' (' + porcentaje(datos[i], total) + ')', x + 6, y);
            });
            ctx.restore();
        }
    };

    function escaparHtml(t) {
        return String(t).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[ch]));
    }

    // Tabla compacta con todos los sectores, para que ninguno quede oculto
    function renderDetalleSectores(data, colores, total) {
        let filas = data.sectores.map((sec, i) => `
            <tr>
                <td><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:${colores[i]};margin-right:8px;vertical-align:middle;"></span>${escaparHtml(sec)}</td>
                <td class="text-end fw-semibold">${data.cuantos[i]}</td>
                <td class="text-end text-muted">${porcentaje(data.cuantos[i], total)}</td>
            </tr>`).join('');
        document.getElementById('sectorDetalle').innerHTML = `
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0" style="font-size:0.85rem;">
                    <thead><tr><th>Sector</th><th class="text-end">Iniciativas</th><th class="text-end">%</th></tr></thead>
                    <tbody>${filas}</tbody>
                    <tfoot><tr class="fw-bold"><td>Total</td><td class="text-end">${total}</td><td class="text-end">100%</td></tr></tfoot>
                </table>
            </div>`;
    }

    $(document).ready(function() {
        var token = $("#sid").val();
        var url = 'api/grafico_por_sector.php?token=' + encodeURIComponent(token || '') + (apiParams ? '&' + apiParams : '');
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data && data.error) { console.error("Error controlado por el servidor (Sector):", data.error); return; }
                if (!data.sectores || data.sectores.length === 0) {
                    document.getElementById('porSector').parentElement.innerHTML = '<p class="text-center text-muted py-5">No hay datos de sectores disponibles</p>';
                    return;
                }
                // Ordenar de mayor a menor (gráfico, lista y Excel en el mismo orden)
                const orden = data.sectores.map((sec, i) => ({ sec, n: data.cuantos[i] }))
                                           .sort((a, b) => b.n - a.n);
                data.sectores = orden.map(o => o.sec);
                data.cuantos  = orden.map(o => o.n);
                datosSector = data;

                const total   = sumar(data.cuantos);
                const colores = coloresPara(data.sectores.length);

                document.getElementById('sectorResumen').textContent =
                    data.sectores.length + ' sectores · ' + total + ' iniciativas';
                renderDetalleSectores(data, colores, total);

                // Alto según la cantidad de sectores (aprox. 42 px por barra,
                // suficiente para nombres en dos líneas)
                document.getElementById('porSectorContenedor').style.height =
                    Math.max(220, data.sectores.length * 42 + 50) + 'px';

                const ctx = document.getElementById('porSector').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.sectores.map(sec => dividirEtiqueta(sec, 32)),
                        datasets: [{
                            label: 'Iniciativas por Sector',
                            data: data.cuantos,
                            backgroundColor: colores,
                            borderRadius: 4,
                            barPercentage: 0.8,
                            categoryPercentage: 0.9
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { right: 70 } },   // espacio para la etiqueta de valor
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { precision: 0 },
                                grid: { color: '#eef0f3' },
                                title: { display: true, text: 'N° de iniciativas' }
                            },
                            y: {
                                grid: { display: false },
                                ticks: {
                                    autoSkip: false,   // mostrar el nombre de todos los sectores
                                    font: { size: 12 }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    title: (items) => data.sectores[items[0].dataIndex],
                                    label: tooltipConPorcentaje
                                }
                            }
                        }
                    },
                    plugins: [pluginValoresBarras]
                });
            },
            error: function(xhr) {
                console.error("Error obteniendo los datos por sector:", xhr.status, xhr.statusText);
                document.getElementById('porSector').parentElement.innerHTML = '<p class="text-center text-muted py-5">Error al cargar datos de sectores</p>';
            }
        });
    });

    // ---------------------------------------------------------
    // Gráfico por etapa: columnas en orden de etapa
    // ---------------------------------------------------------
    $(document).ready(function() {
        var token = $("#sid").val();
        var url = 'api/grafico_por_etapa.php?token=' + encodeURIComponent(token || '') + (apiParams ? '&' + apiParams : '');
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data && data.error) { console.error("Error controlado por el servidor (Etapa):", data.error); return; }
                if (!data || data.length === 0) {
                    document.getElementById('porEtapa').parentElement.innerHTML = '<p class="text-center text-muted py-5">No hay datos de etapas disponibles</p>';
                    return;
                }
                datosEtapa = data;
                const etapas = data.map(item => item.etapa);
                const counts = data.map(item => item.cuantos);
                const ctx = document.getElementById('porEtapa').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: etapas,
                        datasets: [{
                            label: 'Iniciativas',
                            data: counts,
                            backgroundColor: 'rgba(90, 109, 143, 0.75)',
                            borderColor: 'rgba(90, 109, 143, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                            maxBarThickness: 90
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: { padding: { top: 26 } },
                        plugins: {
                            legend: { display: false },
                            tooltip: { callbacks: { label: tooltipConPorcentaje } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 0,
                                    font: { size: 12, weight: '600' },
                                    color: '#343a40',
                                    callback: function(value, index) { return dividirEtiqueta(etapas[index], 18); }
                                }
                            }
                        }
                    },
                    plugins: [pluginValoresColumnas]
                });
            },
            error: function(xhr) {
                console.error("Error obteniendo los datos por etapa:", xhr.status, xhr.statusText);
                document.getElementById('porEtapa').parentElement.innerHTML = '<p class="text-center text-muted py-5">Error al cargar datos de etapas</p>';
            }
        });
    });
</script>

        </div>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>
