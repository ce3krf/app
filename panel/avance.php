<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Versión Dinámica v3 - Pivot directo sin tabla intermedia
// ************************************************************

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../conf/db.php");
include('functions.php');

$db->set_charset("utf8mb4");

// ----------------------------------------------------------
// 1. ETAPAS — fuente de verdad para columnas y orden
//    etapas_id => etapas_descripcion
// ----------------------------------------------------------
$etapas = [];
$res = $db->query("SELECT etapas_id, etapas_descripcion FROM etapas ORDER BY etapas_id");
if (!$res) die('Error etapas [' . $db->error . ']');
while ($row = $res->fetch_assoc()) {
    $etapas[(int)$row['etapas_id']] = $row['etapas_descripcion'];
}
// Columna extra para proyectos sin etapa asignada (al final)
$etapas[0] = 'Sin etapa';

// ----------------------------------------------------------
// 2. LISTA DE SECTORES para el filtro
//    JOIN con sectores (p.sector = sectores.sector_id)
// ----------------------------------------------------------
$sectores = [];
$res = $db->query("
    SELECT DISTINCT s.sector_descripcion
    FROM proyectos p
    INNER JOIN sectores s ON p.sector = s.sector_id
    WHERE p.proyectos_status = 1
      AND s.sector_descripcion IS NOT NULL
      AND s.sector_descripcion <> ''
    ORDER BY s.sector_descripcion
");
if (!$res) die('Error sectores [' . $db->error . ']');
while ($row = $res->fetch_assoc()) {
    $sectores[] = $row['sector_descripcion'];
}

// ----------------------------------------------------------
// 3. LISTA DE TIPOS para el filtro
//    JOIN con tabla tipo (p.tipo = tipo.id)
// ----------------------------------------------------------
$tipos = [];
$res = $db->query("
    SELECT DISTINCT t.tipo
    FROM proyectos p
    INNER JOIN tipo t ON p.tipo = t.id
    WHERE p.proyectos_status = 1
      AND t.tipo IS NOT NULL
      AND t.tipo <> ''
    ORDER BY t.tipo
");
if (!$res) die('Error tipos [' . $db->error . ']');
while ($row = $res->fetch_assoc()) {
    $tipos[] = $row['tipo'];
}

// ----------------------------------------------------------
// 4. FILTROS desde GET (saneados)
// ----------------------------------------------------------
$filtro_sector = isset($_GET['sector']) ? trim($_GET['sector']) : '';
$filtro_tipo   = isset($_GET['tipo'])   ? trim($_GET['tipo'])   : '';

// ----------------------------------------------------------
// 5. CONSULTA PRINCIPAL
//    LEFT JOIN sectores y tipo para no perder proyectos
//    LEFT JOIN etapas para resolver el id a descripcion
//    etapa vacía o nula → etapa_id = 0 → "Sin etapa"
// ----------------------------------------------------------
$where = ["p.proyectos_status = 1"];

if ($filtro_sector !== '') {
    $where[] = "s.sector_descripcion = '" . $db->real_escape_string($filtro_sector) . "'";
}
if ($filtro_tipo !== '') {
    $where[] = "t.tipo = '" . $db->real_escape_string($filtro_tipo) . "'";
}

$where_sql = implode(' AND ', $where);

$sql = "
    SELECT
        COALESCE(s.sector_descripcion, 'Sin sector') AS area,
        COALESCE(t.tipo, 'Sin tipo')                 AS tipo,
        CASE
            WHEN p.etapa IS NULL OR TRIM(p.etapa) = '' THEN 0
            ELSE CAST(p.etapa AS UNSIGNED)
        END                                           AS etapa_id,
        COUNT(p.id)                                   AS cantidad
    FROM proyectos p
    LEFT JOIN sectores s ON p.sector = s.sector_id
    LEFT JOIN tipo     t ON p.tipo   = t.id
    WHERE {$where_sql}
    GROUP BY area, tipo, etapa_id
    ORDER BY area, tipo, etapa_id
";

$res_datos = $db->query($sql);
if (!$res_datos) die('Error consulta principal [' . $db->error . ']');

// ----------------------------------------------------------
// 6. PIVOT EN PHP
//    $pivot[ "area||tipo" ][ etapa_id ] = cantidad
// ----------------------------------------------------------
$pivot       = [];
$filas_orden = [];

while ($row = $res_datos->fetch_assoc()) {
    $clave    = $row['area'] . '||' . $row['tipo'];
    $etapa_id = (int)$row['etapa_id'];
    $cant     = (int)$row['cantidad'];

    if (!isset($pivot[$clave])) {
        $pivot[$clave]       = [];
        $filas_orden[$clave] = ['area' => $row['area'], 'tipo' => $row['tipo']];
    }
    $pivot[$clave][$etapa_id] = $cant;
}

// ----------------------------------------------------------
// 7. TOTALES
// ----------------------------------------------------------
$totales_etapa = array_fill_keys(array_keys($etapas), 0);
$total_general = 0;

foreach ($pivot as $datos_fila) {
    foreach ($etapas as $etapa_id => $etapa_desc) {
        $val = isset($datos_fila[$etapa_id]) ? (int)$datos_fila[$etapa_id] : 0;
        $totales_etapa[$etapa_id] += $val;
        $total_general            += $val;
    }
}

// ----------------------------------------------------------
// 8. ARRAY DE EXPORTACIÓN
// ----------------------------------------------------------
$datos_export = [];
foreach ($filas_orden as $clave => $info) {
    $fila = ['area' => $info['area'], 'tipo' => $info['tipo'], 'etapas' => []];
    $tt   = 0;
    foreach ($etapas as $etapa_id => $etapa_desc) {
        $val  = isset($pivot[$clave][$etapa_id]) ? (int)$pivot[$clave][$etapa_id] : 0;
        $pct  = ($total_general > 0) ? round(($val / $total_general) * 100, 1) : 0;
        $fila['etapas'][$etapa_id] = ['v' => $val, 'p' => $pct];
        $tt  += $val;
    }
    $fila['tt'] = $tt;
    $fila['tp'] = ($total_general > 0) ? round(($tt / $total_general) * 100, 1) : 0;
    $datos_export[] = $fila;
}

// Fallback por si $row_param no viene de functions.php
if (!isset($row_param)) $row_param = ['parametros_titulo' => 'Estado de Avance'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo htmlspecialchars($row_param["parametros_titulo"]); ?></title>

<link rel="icon" type="image/png" href="../favicon.png">
<script src="js/jquery-3.5.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/styles.css"/>

<style>
:root {
  --main:      #018837;
  --primary:   #4e7dbb;
  --secondary: #40a629;
  --accent:    #fe5311;
  --light:     #f1faee;
  --border:    #e0e0e0;
}

body { background-color: #f8f9fa; }

.table-wrapper {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 20px;
}

#data-table {
    font-size: 13px;
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

#data-table thead th {
    background: var(--primary);
    color: white;
    font-weight: 600;
    padding: 14px 8px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    position: sticky;
    top: 0;
    z-index: 10;
    text-align: center;
    vertical-align: middle;
}

#data-table thead th.sector-col { text-align: left; min-width: 160px; }
#data-table thead th.tipo-col   { text-align: left; min-width: 130px; }

#data-table tbody td {
    padding: 10px 8px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}

#data-table tbody tr { transition: background-color 0.15s ease; }
#data-table tbody tr:hover { background-color: #f0f4f8; }
#data-table tbody tr:last-child td { border-bottom: none; }

.sector-cell {
    font-weight: 600;
    color: var(--main);
    border-right: 2px solid var(--secondary);
}

.tipo-cell {
    color: #555;
    font-size: 12px;
    border-right: 2px solid var(--secondary);
}

.data-cell { text-align: center; padding: 8px 6px !important; }

.data-cell-value {
    font-weight: 700;
    color: var(--main);
    font-size: 14px;
    display: block;
    margin-bottom: 2px;
}

.data-cell-percent { font-size: 11px; color: #888; display: block; }

.progress-mini {
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
    margin-top: 4px;
}

.progress-mini-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    border-radius: 2px;
}

.etapa-col-odd  { background-color: #ffffff; }
.etapa-col-even { background-color: #fafbfc; }
.etapa-separator { border-right: 1px solid var(--border); }

/* Columna "Sin etapa" diferenciada visualmente */
.col-sin-etapa { background-color: #fff8f0 !important; }
#data-table thead th.col-sin-etapa { background: #8d99a6 !important; }

#data-table tfoot td {
    background: var(--primary);
    color: white;
    font-weight: 700;
    padding: 14px 8px;
    border: none;
    text-align: center;
    font-size: 13px;
}

#data-table tfoot td:first-child { text-align: left; }
.total-general { background: var(--main) !important; font-size: 15px !important; }

/* Filtros */
.filters-bar {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.filter-group { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
.filter-item  { flex: 0 1 220px; }

.filter-item label {
    all: unset;
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--main);
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-item select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: 6px;
    font-size: 13px;
}

.filter-item select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(78,125,187,0.15);
}

.btn-custom {
    padding: 8px 20px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-primary-custom { background: var(--primary); color: white; }
.btn-primary-custom:hover { background: var(--main); color: white; transform: translateY(-1px); }
.btn-secondary-custom { background: white; color: var(--main); border: 2px solid var(--border); }
.btn-secondary-custom:hover { border-color: var(--primary); color: var(--primary); }

.empty-state { text-align: center; padding: 60px 20px; color: #6c757d; }
.empty-state i { font-size: 3rem; color: var(--border); margin-bottom: 15px; display: block; }

.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.summary-card {
    background: white;
    border-radius: 8px;
    padding: 14px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    border-left: 4px solid var(--primary);
}

.summary-card.total-card     { border-left-color: var(--main); background: rgba(1,136,55,0.05); }
.summary-card.sin-etapa-card { border-left-color: #8d99a6; }

.summary-value {
    font-size: 26px;
    font-weight: 700;
    color: var(--main);
    display: block;
    line-height: 1;
}

.summary-label {
    font-size: 11px;
    color: #666;
    text-transform: uppercase;
    margin-top: 5px;
    display: block;
    letter-spacing: 0.4px;
}

@media (max-width: 768px) {
    #data-table { font-size: 11px; }
    #data-table thead th { padding: 8px 4px; font-size: 10px; }
    #data-table tbody td { padding: 6px 4px; }
    .filter-group { flex-direction: column; }
    .filter-item  { flex: 1 1 100%; }
    .summary-cards { grid-template-columns: repeat(2, 1fr); }
}

@media print {
    .filters-bar, .btn-custom, .top-info-div .text-end { display: none !important; }
    .table-wrapper { box-shadow: none; border: 1px solid var(--border); }
    #data-table thead th { position: static; }
}

#pprint { margin: 0; }
</style>
</head>
<body>

<?php include("header.php"); ?>

<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php include("sidebar.php"); ?>
    </div>

    <div id="page-content-wrapper">
        <div class="container-fluid p-4">

            <div class="row top-info-div mb-3">
                <div class="col">
                    <h5><i class="fas fa-chart-line"></i> ESTADO DE AVANCE DE INICIATIVAS</h5>
                </div>
                <div class="col text-end">
                    <button onclick="exportToExcel();" type="button" class="btn-custom btn-secondary-custom">
                        <i class="fas fa-file-excel"></i> Exportar a Excel
                    </button>
                </div>
            </div>

            <div class="container-fluid">

                <!-- ===== FILTROS ===== -->
                <div class="filters-bar">
                    <form method="get" target="_self">
                        <div class="filter-group">

                            <div class="filter-item">
                                <label for="sector">Sector</label>
                                <select class="form-select" name="sector" id="sector">
                                    <option value="">Todos los sectores</option>
                                    <?php foreach ($sectores as $sdesc) { ?>
                                    <option value="<?php echo htmlspecialchars($sdesc); ?>"
                                        <?php if ($filtro_sector === $sdesc) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($sdesc); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="filter-item">
                                <label for="tipo">Tipo</label>
                                <select class="form-select" name="tipo" id="tipo">
                                    <option value="">Todos los tipos</option>
                                    <?php foreach ($tipos as $tdesc) { ?>
                                    <option value="<?php echo htmlspecialchars($tdesc); ?>"
                                        <?php if ($filtro_tipo === $tdesc) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($tdesc); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div style="flex:0 1 auto; display:flex; gap:6px; align-items:flex-end;">
                                <button type="submit" class="btn-custom btn-primary-custom">
                                    <i class="fas fa-filter"></i> Aplicar
                                </button>
                                <?php if ($filtro_sector !== '' || $filtro_tipo !== '') { ?>
                                <a href="?" class="btn-custom btn-secondary-custom">
                                    <i class="fas fa-times"></i> Limpiar
                                </a>
                                <?php } ?>
                            </div>

                        </div>
                    </form>
                </div>

                <?php if (!empty($datos_export)) { ?>

                <!-- ===== SUMMARY CARDS ===== -->
                <div class="summary-cards">
                    <div class="summary-card total-card">
                        <span class="summary-value"><?php echo number_format($total_general, 0); ?></span>
                        <span class="summary-label">Total Iniciativas</span>
                    </div>
                    <?php foreach ($etapas as $etapa_id => $etapa_desc) {
                        $tv = $totales_etapa[$etapa_id];
                        if ($tv > 0) {
                            $card_class = ($etapa_id === 0) ? 'summary-card sin-etapa-card' : 'summary-card';
                    ?>
                    <div class="<?php echo $card_class; ?>">
                        <span class="summary-value"><?php echo number_format($tv, 0); ?></span>
                        <span class="summary-label"><?php echo htmlspecialchars($etapa_desc); ?></span>
                    </div>
                    <?php } } ?>
                </div>

                <!-- ===== TABLA ===== -->
                <div id="pprint">
                <div class="table-wrapper">
                <table id="data-table">
                    <thead>
                        <tr>
                            <th class="sector-col">Sector</th>
                            <th class="tipo-col">Tipo</th>
                            <?php foreach ($etapas as $etapa_id => $etapa_desc) {
                                $th_class = ($etapa_id === 0) ? 'col-sin-etapa' : '';
                            ?>
                            <th class="<?php echo $th_class; ?>"><?php echo htmlspecialchars($etapa_desc); ?></th>
                            <?php } ?>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $num_cols = count($etapas);
                    foreach ($datos_export as $fila) {
                        $col_num = 0;
                    ?>
                        <tr>
                            <td class="sector-cell"><?php echo htmlspecialchars($fila['area']); ?></td>
                            <td class="tipo-cell"><?php echo htmlspecialchars($fila['tipo']); ?></td>

                            <?php foreach ($etapas as $etapa_id => $etapa_desc) {
                                $col_num++;
                                $col_class = ($etapa_id === 0)
                                    ? 'col-sin-etapa'
                                    : (($col_num % 2 === 0) ? 'etapa-col-even' : 'etapa-col-odd');
                                $sep_class = ($col_num < $num_cols) ? 'etapa-separator' : '';
                                $val = $fila['etapas'][$etapa_id]['v'];
                                $pct = $fila['etapas'][$etapa_id]['p'];
                            ?>
                                <td class="data-cell <?php echo $col_class; ?> <?php echo $sep_class; ?>">
                                    <?php if ($val > 0) { ?>
                                        <span class="data-cell-value"><?php echo number_format($val, 0); ?></span>
                                        <span class="data-cell-percent"><?php echo number_format($pct, 1); ?>%</span>
                                        <div class="progress-mini">
                                            <div class="progress-mini-bar" style="width:<?php echo min($pct, 100); ?>%"></div>
                                        </div>
                                    <?php } else { ?>
                                        <span style="color:#ccc;">—</span>
                                    <?php } ?>
                                </td>
                            <?php } ?>

                            <td class="data-cell">
                                <span class="data-cell-value"><?php echo number_format($fila['tt'], 0); ?></span>
                                <span class="data-cell-percent"><?php echo number_format($fila['tp'], 1); ?>%</span>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="2">TOTALES</td>
                            <?php foreach ($etapas as $etapa_id => $etapa_desc) {
                                $tv  = $totales_etapa[$etapa_id];
                                $pct = ($total_general > 0) ? ($tv / $total_general) * 100 : 0;
                            ?>
                                <td>
                                    <?php echo number_format($tv, 0); ?><br>
                                    <span style="font-size:11px;"><?php echo number_format($pct, 1); ?>%</span>
                                </td>
                            <?php } ?>
                            <td class="total-general">
                                <?php echo number_format($total_general, 0); ?><br>
                                <span style="font-size:11px;">100%</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                </div>
                </div>

                <?php } else { ?>
                <div class="table-wrapper">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No hay datos para mostrar</h5>
                        <p>No se encontraron iniciativas con los filtros seleccionados.</p>
                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>

<!-- ===== EXPORT EXCEL ===== -->
<script>
function exportToExcel() {
    try {
        const wb   = XLSX.utils.book_new();
        const data = [];

        const headers = ['Sector', 'Tipo'];
        <?php foreach ($etapas as $etapa_id => $etapa_desc) {
            echo "headers.push('" . addslashes($etapa_desc) . "');\n";
        } ?>
        headers.push('Total');
        data.push(headers);

        <?php foreach ($datos_export as $fila) {
            echo "data.push([\n";
            echo "  '" . addslashes($fila['area']) . "',\n";
            echo "  '" . addslashes($fila['tipo']) . "',\n";
            foreach ($etapas as $etapa_id => $etapa_desc) {
                $v = $fila['etapas'][$etapa_id]['v'];
                $p = number_format($fila['etapas'][$etapa_id]['p'], 1, '.', '');
                echo "  '" . $v . " (" . $p . "%)',\n";
            }
            $tt = $fila['tt'];
            $tp = number_format($fila['tp'], 1, '.', '');
            echo "  '" . $tt . " (" . $tp . "%)',\n";
            echo "]);\n";
        } ?>

        const totalsRow = ['TOTALES', ''];
        <?php foreach ($etapas as $etapa_id => $etapa_desc) {
            $tv  = $totales_etapa[$etapa_id];
            $pct = ($total_general > 0)
                ? number_format(($tv / $total_general) * 100, 1, '.', '')
                : '0.0';
            echo "totalsRow.push('" . $tv . " (" . $pct . "%)');\n";
        }
        echo "totalsRow.push('" . $total_general . " (100%)');\n"; ?>
        data.push(totalsRow);

        const ws = XLSX.utils.aoa_to_sheet(data);
        const colWidths = [{ wch: 25 }, { wch: 20 }];
        <?php foreach ($etapas as $etapa_id => $etapa_desc) { ?>
        colWidths.push({ wch: 15 });
        <?php } ?>
        colWidths.push({ wch: 18 });
        ws['!cols'] = colWidths;

        XLSX.utils.book_append_sheet(wb, ws, 'Estado de Avance');
        const fecha = new Date().toISOString().split('T')[0];
        XLSX.writeFile(wb, 'estado_avance_' + fecha + '.xlsx');

    } catch (error) {
        console.error('Error al generar Excel:', error);
        alert('Error al generar el archivo Excel. Por favor intente nuevamente.');
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V"
        crossorigin="anonymous"></script>
<script src="app.js"></script>
</body>
</html>
