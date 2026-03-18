<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Versión Dinámica - Avance Financiero sin tabla intermedia
// ************************************************************

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include("../conf/db.php");
include('functions.php');

$db->set_charset("utf8mb4");

// ----------------------------------------------------------
// 1. PARÁMETROS del sistema
// ----------------------------------------------------------
if (!isset($row_param)) {
    $res = $db->query("SELECT * FROM parametros LIMIT 1");
    $row_param = $res ? $res->fetch_assoc() : ['parametros_titulo' => 'Avance Financiero'];
}

// ----------------------------------------------------------
// 2. ETAPAS — fuente de verdad para columnas y orden
//    etapas_id => etapas_descripcion
//    id=0 reservado para proyectos sin etapa asignada
// ----------------------------------------------------------
$etapas = [];
$res = $db->query("SELECT etapas_id, etapas_descripcion FROM etapas ORDER BY etapas_id");
if (!$res) die('Error etapas [' . $db->error . ']');
while ($row = $res->fetch_assoc()) {
    $etapas[(int)$row['etapas_id']] = $row['etapas_descripcion'];
}
$etapas[0] = 'Sin etapa'; // al final

// ----------------------------------------------------------
// 3. SECTORES para el filtro
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
// 4. TIPOS para el filtro
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
// 5. FILTROS desde GET (saneados)
// ----------------------------------------------------------
$filtro_sector = isset($_GET['sector']) ? trim($_GET['sector']) : '';
$filtro_tipo   = isset($_GET['tipo'])   ? trim($_GET['tipo'])   : '';

// ----------------------------------------------------------
// 6. CONSULTA PRINCIPAL
//
//    Presupuesto: p_diseno + p_ejecucion por proyecto
//    Inversión:   SUM(actividades.monto) donde estado = 'Completa'
//                 JOIN correcto: actividades.proyecto = proyectos.id
//
//    Agrupa por sector / tipo / etapa
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
        COALESCE(s.sector_descripcion, 'Sin sector')  AS area,
        COALESCE(t.tipo, 'Sin tipo')                  AS tipo,
        CASE
            WHEN p.etapa IS NULL OR TRIM(p.etapa) = '' THEN 0
            ELSE CAST(p.etapa AS UNSIGNED)
        END                                            AS etapa_id,
        COALESCE(e.etapas_descripcion, 'Sin etapa')   AS etapa_desc,
        COUNT(DISTINCT p.id)                           AS cantidad,
        SUM(COALESCE(p.p_diseno, 0) + COALESCE(p.p_ejecucion, 0))
                                                       AS presupuesto,
        -- JOIN correcto: actividades.proyecto = proyectos.id
        -- Solo actividades con estado 'Completa'
        COALESCE(SUM(
            CASE WHEN a.estado = 'Completa'
                 THEN COALESCE(a.monto_final, a.monto, 0)
                 ELSE 0
            END
        ), 0)                                          AS inversion
    FROM proyectos p
    LEFT JOIN sectores   s ON p.sector = s.sector_id
    LEFT JOIN tipo       t ON p.tipo   = t.id
    LEFT JOIN etapas     e ON e.etapas_id = CASE
                                WHEN p.etapa IS NULL OR TRIM(p.etapa) = '' THEN NULL
                                ELSE CAST(p.etapa AS UNSIGNED)
                              END
    LEFT JOIN actividades a ON a.proyecto = p.id
    WHERE {$where_sql}
    GROUP BY area, tipo, etapa_id, etapa_desc
    ORDER BY area, tipo, etapa_id
";

$res_datos = $db->query($sql);
if (!$res_datos) die('Error consulta principal [' . $db->error . ']');

// ----------------------------------------------------------
// 7. PIVOT EN PHP
//    $pivot[ "area||tipo" ][ etapa_id ] = [
//        'cantidad', 'presupuesto', 'inversion', 'porcentaje'
//    ]
// ----------------------------------------------------------
$pivot       = [];
$filas_orden = [];

while ($row = $res_datos->fetch_assoc()) {
    $clave    = $row['area'] . '||' . $row['tipo'];
    $etapa_id = (int)$row['etapa_id'];
    $pres     = (float)$row['presupuesto'];
    $inv      = (float)$row['inversion'];
    $pct      = ($pres > 0) ? round(($inv / $pres) * 100, 1) : 0;

    if (!isset($pivot[$clave])) {
        $pivot[$clave]       = [];
        $filas_orden[$clave] = ['area' => $row['area'], 'tipo' => $row['tipo']];
    }

    $pivot[$clave][$etapa_id] = [
        'cantidad'    => (int)$row['cantidad'],
        'presupuesto' => $pres,
        'inversion'   => $inv,
        'porcentaje'  => $pct,
    ];
}

// ----------------------------------------------------------
// 8. TOTALES GENERALES
// ----------------------------------------------------------
$total_presupuesto = 0;
$total_inversion   = 0;

// Totales por etapa [ etapa_id => ['presupuesto'=>..., 'inversion'=>...] ]
$totales_etapa = [];
foreach ($etapas as $etapa_id => $etapa_desc) {
    $totales_etapa[$etapa_id] = ['presupuesto' => 0, 'inversion' => 0];
}

foreach ($pivot as $datos_fila) {
    foreach ($etapas as $etapa_id => $etapa_desc) {
        if (isset($datos_fila[$etapa_id])) {
            $totales_etapa[$etapa_id]['presupuesto'] += $datos_fila[$etapa_id]['presupuesto'];
            $totales_etapa[$etapa_id]['inversion']   += $datos_fila[$etapa_id]['inversion'];
        }
    }
}

foreach ($totales_etapa as $t) {
    $total_presupuesto += $t['presupuesto'];
    $total_inversion   += $t['inversion'];
}

$porcentaje_general = ($total_presupuesto > 0)
    ? round(($total_inversion / $total_presupuesto) * 100, 1)
    : 0;

// ----------------------------------------------------------
// 9. ARRAY DE EXPORTACIÓN
// ----------------------------------------------------------
$datos_export = [];
foreach ($filas_orden as $clave => $info) {
    $fila = ['area' => $info['area'], 'tipo' => $info['tipo'], 'etapas' => []];
    foreach ($etapas as $etapa_id => $etapa_desc) {
        $fila['etapas'][$etapa_id] = isset($pivot[$clave][$etapa_id])
            ? $pivot[$clave][$etapa_id]
            : ['cantidad' => 0, 'presupuesto' => 0, 'inversion' => 0, 'porcentaje' => 0];
    }
    $datos_export[] = $fila;
}
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
  --primary:   #2d6a4f;
  --secondary: #40a629;
  --accent:    #74c69d;
  --light:     #f1faee;
  --border:    #e0e0e0;
}

body { background-color: #f8f9fa; }

.table-wrapper {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    overflow-x: auto;
    margin-bottom: 20px;
}

#data-table {
    font-size: 13px;
    margin-bottom: 0;
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

/* Grupo de cabecera por etapa */
#data-table thead tr.header-etapas th {
    background: var(--primary);
    color: white;
    font-weight: 700;
    padding: 10px 8px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.15);
    position: sticky;
    top: 0;
    z-index: 10;
}

#data-table thead tr.header-etapas th.left-align { text-align: left; }
#data-table thead tr.header-etapas th.sin-etapa-header { background: #8d99a6 !important; }

/* Sub-cabeceras (Pres / Inv / %) */
#data-table thead tr.header-sub th {
    background: #3a7d5e;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 6px 4px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.1);
    position: sticky;
    top: 37px;
    z-index: 9;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

#data-table tbody td {
    padding: 9px 8px;
    border-bottom: 1px solid var(--border);
    border-right: 1px solid #f0f0f0;
    vertical-align: middle;
}

#data-table tbody tr:hover { background-color: #f0f7f4; }
#data-table tbody tr:last-child td { border-bottom: none; }

.sector-cell {
    font-weight: 600;
    color: var(--main);
    border-right: 2px solid var(--secondary) !important;
    min-width: 150px;
}

.tipo-cell {
    color: #555;
    font-size: 12px;
    border-right: 2px solid var(--secondary) !important;
    min-width: 120px;
}

/* Separador visual entre grupos de etapa */
.etapa-group-sep { border-right: 2px solid var(--border) !important; }

/* Celda de dinero */
.money-cell {
    text-align: right;
    font-variant-numeric: tabular-nums;
    white-space: nowrap;
    font-size: 12px;
    min-width: 90px;
}

/* Barra de avance porcentual */
.pct-cell { min-width: 90px; text-align: center; }

.pct-bar-wrap {
    display: flex;
    align-items: center;
    gap: 5px;
}

.pct-bar-bg {
    flex: 1;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.pct-bar-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s ease;
}

.pct-high   { background: #2d9e5f; }
.pct-medium { background: #f4a261; }
.pct-low    { background: #e76f51; }
.pct-zero   { background: #ced4da; }

.pct-label {
    font-size: 11px;
    font-weight: 700;
    min-width: 36px;
    text-align: right;
}

.pct-label.high   { color: #2d9e5f; }
.pct-label.medium { color: #e07a2a; }
.pct-label.low    { color: #e76f51; }
.pct-label.zero   { color: #aaa; }

/* Celda vacía */
.empty-cell { text-align: center; color: #ccc; font-size: 16px; }

/* Sin etapa — fondo diferenciado */
.sin-etapa-col { background-color: #fff8f0 !important; }

/* Tfoot */
#data-table tfoot td {
    background: var(--primary);
    color: white;
    font-weight: 700;
    padding: 12px 8px;
    border: none;
    text-align: right;
    font-size: 13px;
}

#data-table tfoot td.total-label  { text-align: left; }
#data-table tfoot td.total-pct    { text-align: center; }

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
    box-shadow: 0 0 0 3px rgba(45,106,79,0.15);
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
.btn-primary-custom:hover { background: var(--main); color: white; }
.btn-secondary-custom { background: white; color: var(--main); border: 2px solid var(--border); }
.btn-secondary-custom:hover { border-color: var(--primary); color: var(--primary); }

.empty-state { text-align: center; padding: 60px 20px; color: #6c757d; }
.empty-state i { font-size: 3rem; color: var(--border); margin-bottom: 15px; display: block; }

/* Summary cards */
.summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.summary-card {
    background: white;
    border-radius: 8px;
    padding: 16px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    border-left: 4px solid var(--primary);
}

.summary-card.total-card { border-left-color: var(--main); background: rgba(1,136,55,0.04); }

.summary-value {
    font-size: 22px;
    font-weight: 700;
    color: var(--main);
    display: block;
    line-height: 1.1;
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
                    <h5><i class="fas fa-dollar-sign"></i> AVANCE FINANCIERO DE INICIATIVAS</h5>
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
                        <span class="summary-value">$<?php echo number_format($total_presupuesto, 0); ?></span>
                        <span class="summary-label">Presupuesto Total</span>
                    </div>
                    <div class="summary-card total-card">
                        <span class="summary-value">$<?php echo number_format($total_inversion, 0); ?></span>
                        <span class="summary-label">Inversión Total</span>
                    </div>
                    <div class="summary-card total-card">
                        <span class="summary-value"><?php echo number_format($porcentaje_general, 1); ?>%</span>
                        <span class="summary-label">Avance General</span>
                    </div>
                </div>

                <!-- ===== TABLA ===== -->
                <div id="pprint">
                <div class="table-wrapper">
                <table id="data-table" class="table table-sm">
                    <thead>
                        <!-- Fila 1: Sector | Tipo | [Etapa 1] | [Etapa 2] | ... | [Sin etapa] | TOTAL -->
                        <tr class="header-etapas">
                            <th class="left-align" rowspan="2">Sector</th>
                            <th class="left-align" rowspan="2">Tipo</th>
                            <?php
                            $num_etapas = count($etapas);
                            $col_idx = 0;
                            foreach ($etapas as $etapa_id => $etapa_desc) {
                                $col_idx++;
                                $h_class = ($etapa_id === 0) ? 'sin-etapa-header' : '';
                            ?>
                            <th colspan="3" class="<?php echo $h_class; ?>">
                                <?php echo htmlspecialchars($etapa_desc); ?>
                            </th>
                            <?php } ?>
                            <th colspan="3">TOTAL</th>
                        </tr>
                        <!-- Fila 2: sub-cabeceras Pres / Inv / % por cada etapa -->
                        <tr class="header-sub">
                            <?php foreach ($etapas as $etapa_id => $etapa_desc) { ?>
                            <th>Pres.</th>
                            <th>Inv.</th>
                            <th>%</th>
                            <?php } ?>
                            <th>Pres.</th>
                            <th>Inv.</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($datos_export as $fila) {
                        // Totales de fila
                        $fila_pres = 0;
                        $fila_inv  = 0;
                        foreach ($etapas as $etapa_id => $etapa_desc) {
                            $fila_pres += $fila['etapas'][$etapa_id]['presupuesto'];
                            $fila_inv  += $fila['etapas'][$etapa_id]['inversion'];
                        }
                        $fila_pct = ($fila_pres > 0) ? round(($fila_inv / $fila_pres) * 100, 1) : 0;

                        // Helper para clase de barra
                        $pct_class = function($p) {
                            if ($p >= 75) return 'high';
                            if ($p >= 40) return 'medium';
                            if ($p > 0)  return 'low';
                            return 'zero';
                        };
                        $col_idx = 0;
                    ?>
                        <tr>
                            <td class="sector-cell"><?php echo htmlspecialchars($fila['area']); ?></td>
                            <td class="tipo-cell"><?php echo htmlspecialchars($fila['tipo']); ?></td>

                            <?php foreach ($etapas as $etapa_id => $etapa_desc) {
                                $col_idx++;
                                $d    = $fila['etapas'][$etapa_id];
                                $pres = $d['presupuesto'];
                                $inv  = $d['inversion'];
                                $pct  = $d['porcentaje'];
                                $sep  = ($col_idx < $num_etapas) ? 'etapa-group-sep' : '';
                                $bg   = ($etapa_id === 0) ? 'sin-etapa-col' : '';
                                $cls  = $pct_class($pct);
                            ?>
                                <?php if ($pres > 0 || $inv > 0) { ?>
                                <td class="money-cell <?php echo $bg; ?>">
                                    $<?php echo number_format($pres, 0); ?>
                                </td>
                                <td class="money-cell <?php echo $bg; ?>">
                                    $<?php echo number_format($inv, 0); ?>
                                </td>
                                <td class="pct-cell <?php echo $bg; ?> <?php echo $sep; ?>">
                                    <div class="pct-bar-wrap">
                                        <div class="pct-bar-bg">
                                            <div class="pct-bar-fill pct-<?php echo $cls; ?>"
                                                 style="width:<?php echo min($pct, 100); ?>%"></div>
                                        </div>
                                        <span class="pct-label <?php echo $cls; ?>">
                                            <?php echo number_format($pct, 1); ?>%
                                        </span>
                                    </div>
                                </td>
                                <?php } else { ?>
                                <td class="empty-cell <?php echo $bg; ?>">—</td>
                                <td class="empty-cell <?php echo $bg; ?>">—</td>
                                <td class="empty-cell <?php echo $bg; ?> <?php echo $sep; ?>">—</td>
                                <?php } ?>
                            <?php } ?>

                            <!-- Total fila -->
                            <td class="money-cell">$<?php echo number_format($fila_pres, 0); ?></td>
                            <td class="money-cell">$<?php echo number_format($fila_inv, 0); ?></td>
                            <td class="pct-cell">
                                <?php $cls = $pct_class($fila_pct); ?>
                                <div class="pct-bar-wrap">
                                    <div class="pct-bar-bg">
                                        <div class="pct-bar-fill pct-<?php echo $cls; ?>"
                                             style="width:<?php echo min($fila_pct, 100); ?>%"></div>
                                    </div>
                                    <span class="pct-label <?php echo $cls; ?>">
                                        <?php echo number_format($fila_pct, 1); ?>%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="2" class="total-label">TOTALES</td>
                            <?php foreach ($etapas as $etapa_id => $etapa_desc) {
                                $tp = $totales_etapa[$etapa_id]['presupuesto'];
                                $ti = $totales_etapa[$etapa_id]['inversion'];
                                $tc = ($tp > 0) ? round(($ti / $tp) * 100, 1) : 0;
                            ?>
                                <td>$<?php echo number_format($tp, 0); ?></td>
                                <td>$<?php echo number_format($ti, 0); ?></td>
                                <td class="total-pct"><?php echo number_format($tc, 1); ?>%</td>
                            <?php } ?>
                            <td>$<?php echo number_format($total_presupuesto, 0); ?></td>
                            <td>$<?php echo number_format($total_inversion, 0); ?></td>
                            <td class="total-pct"><?php echo number_format($porcentaje_general, 1); ?>%</td>
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

        // Fila 1: encabezados de etapas
        const row1 = ['Sector', 'Tipo'];
        <?php foreach ($etapas as $etapa_id => $etapa_desc) {
            echo "row1.push('" . addslashes($etapa_desc) . "', '', '');\n";
        } ?>
        row1.push('TOTAL', '', '');
        data.push(row1);

        // Fila 2: sub-encabezados
        const row2 = ['', ''];
        <?php foreach ($etapas as $etapa_id => $etapa_desc) { ?>
        row2.push('Presupuesto', 'Inversión', '% Avance');
        <?php } ?>
        row2.push('Presupuesto', 'Inversión', '% Avance');
        data.push(row2);

        // Filas de datos
        <?php foreach ($datos_export as $fila) {
            $fila_pres = 0;
            $fila_inv  = 0;
            foreach ($etapas as $etapa_id => $etapa_desc) {
                $fila_pres += $fila['etapas'][$etapa_id]['presupuesto'];
                $fila_inv  += $fila['etapas'][$etapa_id]['inversion'];
            }
            $fila_pct = ($fila_pres > 0) ? round(($fila_inv / $fila_pres) * 100, 1) : 0;

            echo "data.push([\n";
            echo "  '" . addslashes($fila['area']) . "',\n";
            echo "  '" . addslashes($fila['tipo']) . "',\n";
            foreach ($etapas as $etapa_id => $etapa_desc) {
                $d   = $fila['etapas'][$etapa_id];
                $p   = $d['presupuesto'];
                $i   = $d['inversion'];
                $pct = number_format($d['porcentaje'], 1, '.', '');
                echo "  $p, $i, $pct,\n";
            }
            echo "  $fila_pres, $fila_inv, " . number_format($fila_pct, 1, '.', '') . "\n";
            echo "]);\n";
        } ?>

        // Fila totales
        data.push([
            'TOTALES', '',
            <?php foreach ($etapas as $etapa_id => $etapa_desc) {
                $tp = $totales_etapa[$etapa_id]['presupuesto'];
                $ti = $totales_etapa[$etapa_id]['inversion'];
                $tc = ($tp > 0) ? number_format(($ti / $tp) * 100, 1, '.', '') : '0.0';
                echo "$tp, $ti, $tc,\n";
            } ?>
            <?php echo $total_presupuesto . ", " . $total_inversion . ", " . number_format($porcentaje_general, 1, '.', ''); ?>
        ]);
        data.push([]);

        const ws = XLSX.utils.aoa_to_sheet(data);

        // Anchos de columna
        const colWidths = [{ wch: 25 }, { wch: 20 }];
        <?php foreach ($etapas as $etapa_id => $etapa_desc) { ?>
        colWidths.push({ wch: 14 }, { wch: 14 }, { wch: 10 });
        <?php } ?>
        colWidths.push({ wch: 14 }, { wch: 14 }, { wch: 10 });
        ws['!cols'] = colWidths;

        XLSX.utils.book_append_sheet(wb, ws, 'Avance Financiero');
        const fecha = new Date().toISOString().split('T')[0];
        XLSX.writeFile(wb, 'avance_financiero_' + fecha + '.xlsx');

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
