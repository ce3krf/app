<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-11-16
// ************************************************************
// Ficha de consulta de una iniciativa (se abre desde el mapa).
// Basada en los grupos y etiquetas de proyectos_editar.php, solo lectura.
// ************************************************************

@session_start();
$session = session_id();

include("../conf/db.php");

$sql = "set names utf8mb4";
if (!$result = $db->query($sql)) {
    die('Hay un error [' . $db->error . ']');
}

$proyecto_id = isset($_GET['proyectos_id']) ? (int) $_GET['proyectos_id'] : 0;


// -----------------------------------------------------
// Proyecto
// -----------------------------------------------------
$sql = "
    SELECT p.*,
           COALESCE(s.sector_descripcion, p.sector) AS sector_txt,
           s.sector_color
    FROM proyectos p
    LEFT JOIN sectores s ON s.sector_id = p.cod_sector
    WHERE p.id = $proyecto_id
      AND p.proyectos_status = 1
";
if (!$result = $db->query($sql)) {
    die('Hay un error [' . $db->error . ']');
}
$row = $result->fetch_assoc();

if (!$row) {
    echo '<div style="padding:20px;text-align:center;color:#6c757d;">La iniciativa no existe o no está disponible.</div>';
    exit;
}

$numero = (int) $row['numero'];
$codigo = trim((string) ($row['codigo'] ?? ''));


// -----------------------------------------------------
// Avances (se calculan al vuelo, igual que la ficha de edición)
// -----------------------------------------------------
$costo_pref  = (float) ($row['costos_perfil_prefactibilidad'] ?? 0);
$costo_dis   = (float) ($row['costos_diseno'] ?? 0);
$costo_eje   = (float) ($row['costos_ejecucion'] ?? 0);
$total_monto = $costo_pref + $costo_dis + $costo_eje;

$gasto_total = (float) ($row['gasto_perfil_prefactibilidad'] ?? 0)
             + (float) ($row['gasto_diseno'] ?? 0)
             + (float) ($row['gasto_ejecucion'] ?? 0);

$sql = "
    SELECT
        COUNT(*)                                                             AS total_act,
        SUM(CASE WHEN estado = 'Completa' THEN 1 ELSE 0 END)                 AS act_completas,
        COALESCE(SUM(CASE WHEN estado = 'Completa' THEN monto ELSE 0 END),0) AS monto_completo
    FROM actividades
    WHERE proyecto = $proyecto_id
";
$act = ($r = $db->query($sql)) ? $r->fetch_assoc() : ['total_act' => 0, 'act_completas' => 0, 'monto_completo' => 0];
$total_act          = (int) $act['total_act'];
$avance_actividades = $total_act > 0   ? round((int) $act['act_completas'] * 100 / $total_act, 1) : 0;
$avance_financiero  = $total_monto > 0 ? round((float) $act['monto_completo'] * 100 / $total_monto, 1) : 0;


// -----------------------------------------------------
// Ubicaciones adicionales, fuentes de financiamiento y gasto por etapa (por numero)
// -----------------------------------------------------
$ubicaciones = [];
if ($r = $db->query("SELECT descripcion, latitud, longitud FROM ubicaciones WHERE numero = $numero ORDER BY id")) {
    $ubicaciones = $r->fetch_all(MYSQLI_ASSOC);
}

function vp_por_etapa($db, $tabla, $numero) {
    $sql = "
        SELECT t.etapa, COALESCE(e.etapas_descripcion, CONCAT('Etapa ', t.etapa)) AS etapa_txt, t.monto, t.fuente
        FROM $tabla t
        LEFT JOIN etapas e ON e.etapas_id = t.etapa
        WHERE t.numero = $numero
        ORDER BY t.etapa, t.id
    ";
    $r = $db->query($sql);
    return $r ? $r->fetch_all(MYSQLI_ASSOC) : [];
}
$financiamiento = vp_por_etapa($db, 'financiamiento', $numero);
$gastos         = vp_por_etapa($db, 'gasto', $numero);


// -----------------------------------------------------
// Acciones (acciones.codigo_iniciativa = proyectos.codigo), ordenadas por inicio
// -----------------------------------------------------
$acciones = [];
if ($codigo !== '') {
    $stmt = $db->prepare("
        SELECT nombre_accion, inicio, fin, verificador
        FROM acciones
        WHERE codigo_iniciativa = ?
        ORDER BY (inicio IS NULL), inicio, (fin IS NULL), fin, id
    ");
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    $acciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Gantt semestral: índice = año * 2 + (0 = ene-jun, 1 = jul-dic)
function vp_sem($fecha) {
    if (empty($fecha)) return null;
    $t = strtotime($fecha);
    if ($t === false) return null;
    return (int) date('Y', $t) * 2 + ((int) date('n', $t) > 6 ? 1 : 0);
}
$g_ini = null; $g_fin = null;
foreach ($acciones as $a) {
    $si = vp_sem($a['inicio']); $sf = vp_sem($a['fin']);
    if ($si !== null) $g_ini = ($g_ini === null) ? $si : min($g_ini, $si);
    if ($sf !== null) $g_fin = ($g_fin === null) ? $sf : max($g_fin, $sf);
}
if ($g_ini !== null && $g_fin === null) $g_fin = $g_ini;
if ($g_fin !== null && $g_ini === null) $g_ini = $g_fin;

$g_anos = [];   // año => [semestres]
if ($g_ini !== null) {
    for ($s = $g_ini; $s <= $g_fin; $s++) $g_anos[intdiv($s, 2)][] = $s;
}


// -----------------------------------------------------
// Localidades cubiertas
// -----------------------------------------------------
$LOCALIDADES = [
    'la_obra' => 'La Obra', 'las_vertientes' => 'Las Vertientes', 'el_canelo' => 'El Canelo',
    'el_manzano' => 'El Manzano', 'los_maitenes' => 'Los Maitenes', 'el_alfalfal' => 'El Alfalfal',
    'el_colorado' => 'El Colorado', 'el_guayacan' => 'El Guayacán', 'san_jose_de_maipo' => 'San José de Maipo',
    'el_melocoton' => 'El Melocotón', 'lagunillas' => 'Lagunillas', 'el_toyo' => 'El Toyo',
    'san_alfonso' => 'San Alfonso', 'el_ingenio' => 'El Ingenio', 'bollenar' => 'Bollenar',
    'san_gabriel' => 'San Gabriel', 'el_yeso' => 'El Yeso', 'las_melosas' => 'Las Melosas',
    'los_queltehues' => 'Los Queltehues', 'el_volcan' => 'El Volcán', 'banos_morales' => 'Baños Morales',
    'lo_valdes' => 'Lo Valdés', 'el_romeral' => 'El Romeral', 'los_nogales' => 'Los Nogales',
];
$localidades = [];
foreach ($LOCALIDADES as $col => $label) {
    if (!empty($row[$col])) $localidades[] = $label;
}


// -----------------------------------------------------
// Helpers de presentación
// -----------------------------------------------------
function vp_h($v) {
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
}

function vp_vacio($v) {
    return $v === null || trim((string) $v) === '';
}

// Monto en M$ sin decimales (la etiqueta ya dice M$)
function vp_m($v) {
    return vp_vacio($v) ? '' : number_format((float) $v, 0, ',', '.');
}

function vp_num($v) {
    if (vp_vacio($v)) return '';
    $f = (float) $v;
    return number_format($f, (floor($f) == $f) ? 0 : 2, ',', '.');
}

function vp_fecha($f) {
    return empty($f) ? '-' : date('d/m/Y', strtotime($f));
}

// Título de sección
function vp_seccion($icono, $titulo) {
    echo '<div class="vp-seccion"><i class="' . $icono . '"></i><span>' . $titulo . '</span></div>';
}

// Subtítulo dentro de una sección
function vp_sub($titulo) {
    echo '<div class="vp-sub">' . $titulo . '</div>';
}

// Campo de solo lectura. No se muestra si viene vacío.
// $html = true cuando $valor ya viene escapado / con formato.
function vp_campo($label, $valor, $ancho = '', $html = false) {
    if (vp_vacio(strip_tags((string) $valor))) return;
    $txt = $html ? $valor : nl2br(vp_h($valor));
    echo '<div class="vp-campo ' . $ancho . '"><div class="vp-label">' . $label . '</div><div class="vp-valor">' . $txt . '</div></div>';
}

// ¿Hay algo que mostrar en un grupo de campos?
function vp_hay($row, array $campos) {
    foreach ($campos as $c) if (!vp_vacio($row[$c] ?? null)) return true;
    return false;
}

// Tabla Etapa / Monto M$ / Fuente con subtotales por etapa y total
function vp_tabla_etapas(array $filas) {
    if (!$filas) return;
    $grupos = [];
    foreach ($filas as $f) {
        $k = (string) $f['etapa'];
        if (!isset($grupos[$k])) $grupos[$k] = ['txt' => $f['etapa_txt'], 'filas' => [], 'total' => 0];
        $grupos[$k]['filas'][] = $f;
        $grupos[$k]['total'] += (float) $f['monto'];
    }
    $total = 0;
    echo '<div class="vp-tabla-wrap"><table class="vp-tabla"><thead><tr>'
       . '<th style="text-align:left">Etapa</th><th style="text-align:right">Monto M$</th><th style="text-align:left">Fuente</th>'
       . '</tr></thead><tbody>';
    foreach ($grupos as $g) {
        foreach ($g['filas'] as $i => $f) {
            echo '<tr><td>' . ($i === 0 ? '<strong>' . vp_h($g['txt']) . '</strong>' : '') . '</td>'
               . '<td style="text-align:right; white-space:nowrap">' . vp_m($f['monto']) . '</td>'
               . '<td>' . vp_h($f['fuente']) . '</td></tr>';
        }
        if (count($g['filas']) > 1) {
            echo '<tr class="vp-subtotal"><td style="text-align:right">Subtotal ' . vp_h($g['txt']) . '</td>'
               . '<td style="text-align:right">' . vp_m($g['total']) . '</td><td></td></tr>';
        }
        $total += $g['total'];
    }
    echo '</tbody><tfoot><tr><td style="text-align:right">Total</td><td style="text-align:right">' . vp_m($total) . '</td><td></td></tr></tfoot>';
    echo '</table></div>';
}

$nombre = $row['nombre_iniciativa'] ?: 'Iniciativa';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title><?php echo vp_h($nombre); ?></title>

<style>
#pprint, #pprint * { box-sizing: border-box; }

#pprint {
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
    padding: 6px 4px;
    font-size: 14px;
    color: #212529;
    text-align: left;
}

/* Encabezado */
.vp-head {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.vp-head img { width: 90px; max-width: 90px; height: auto; }
.vp-titulo { flex: 1; font-size: 18px; font-weight: 700; line-height: 1.3; color: #023047; word-wrap: break-word; }
.vp-pdf { text-align: right; margin: 6px 0 10px; }
.vp-pdf a { text-decoration: none; font-size: 13px; color: #1a5276; }
.vp-pdf img { width: 24px; vertical-align: middle; }

/* Etiquetas destacadas bajo el título */
.vp-chips { display: flex; flex-wrap: wrap; gap: 6px; margin: 8px 0 4px; }
.vp-chip {
    display: inline-block;
    font-size: 12px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 12px;
    background: #eaf0fb;
    color: #1a5276;
}
.vp-chip.sector { color: #fff; }
.vp-chip.pm { background: #fff3e0; color: #b35c00; }

/* Tarjetas */
.vp-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 8px; margin: 12px 0 4px; }
.vp-card { padding: 10px 12px; border-radius: 8px; }
.vp-card-t { font-size: 10px; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
.vp-card-v { font-size: 24px; font-weight: 700; color: #023047; line-height: 1.1; }
.vp-card-v.txt { font-size: 14px; font-weight: 600; line-height: 1.3; }
.vp-bar { height: 6px; border-radius: 6px; background: rgba(0,0,0,.08); margin-top: 6px; overflow: hidden; }
.vp-bar div { height: 100%; border-radius: 6px; }

/* Secciones */
.vp-seccion {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 18px 0 8px;
    padding: 7px 12px;
    border-radius: 6px;
    border-left: 5px solid #f0a500;
    background: linear-gradient(135deg, #023047 0%, #1a5276 100%);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
}
.vp-sub {
    margin: 12px 0 4px;
    padding: 4px 10px;
    border-left: 4px solid #e0a800;
    border-radius: 4px;
    background: #fff3c4;
    color: #5c4300;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .5px;
    text-transform: uppercase;
}

/* Campos */
.vp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 2px 14px; }
.vp-campo { padding: 8px 2px; border-bottom: 1px solid #eef0f3; }
.vp-campo.full { grid-column: 1 / -1; }
.vp-label {
    display: inline-block;
    font-size: 10.5px;
    font-weight: 700;
    color: #1a5276;
    background: #eaf0fb;
    border-left: 3px solid #f0a500;
    border-radius: 3px;
    padding: 2px 7px;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 4px;
}
.vp-valor { font-size: 14px; line-height: 1.45; color: #212529; word-wrap: break-word; padding-left: 2px; }
.vp-total { font-size: 18px; font-weight: 700; color: #1a5276; }

.vp-loc { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 3px; }
.vp-loc span { font-size: 12px; padding: 2px 9px; border-radius: 10px; background: #e8f5e9; color: #2e6b3f; }

/* Avance de la meta física */
.vp-meta { display: flex; align-items: center; gap: 8px; }
.vp-meta .vp-bar { flex: 1; height: 8px; margin-top: 0; }
.vp-meta strong { font-size: 15px; color: #023047; }

/* Tablas */
.vp-tabla-wrap { overflow-x: auto; width: 100%; }
.vp-tabla { width: 100%; border-collapse: collapse; font-size: 13px; }
.vp-tabla th { font-size: 11px; font-weight: 700; color: #6c757d; text-transform: uppercase; padding: 6px; border-bottom: 2px solid #dee2e6; }
.vp-tabla td { padding: 6px; border-bottom: 1px solid #eef0f3; vertical-align: top; }
.vp-tabla .vp-subtotal td { font-size: 12px; font-style: italic; color: #495057; background: #f8f9fa; }
.vp-tabla tfoot td { font-weight: 700; color: #023047; background: #eaf0fb; border-top: 2px solid #b8cfe8; }
.vp-nada { text-align: center; color: #6c757d; padding: 12px; font-size: 13px; }

/* Acciones: Gantt semestral horizontal con desplazamiento */
.vp-gantt-wrap { overflow-x: scroll; width: 100%; border: 1px solid #dee2e6; border-radius: 6px; padding-bottom: 2px; }
.vp-gantt-wrap::-webkit-scrollbar { height: 10px; }
.vp-gantt-wrap::-webkit-scrollbar-track { background: #f1f3f5; }
.vp-gantt-wrap::-webkit-scrollbar-thumb { background: #adb5bd; border-radius: 5px; }
.vp-gantt { border-collapse: separate; border-spacing: 0; font-size: 12px; white-space: nowrap; }
.vp-gantt th { background: #17365d; color: #fff; font-size: 10px; font-weight: 700; text-align: center; padding: 4px 3px; border-left: 1px solid #2c4f7c; }
.vp-gantt td { padding: 5px 6px; border-bottom: 1px solid #eef0f3; vertical-align: middle; background: #fff; }
.vp-gantt .col-acc { position: sticky; left: 0; z-index: 2; min-width: 220px; max-width: 220px; white-space: normal; text-align: left; border-right: 2px solid #dee2e6; }
.vp-gantt th.col-acc { background: #17365d; z-index: 3; }
.vp-gantt .col-fec { text-align: center; color: #495057; }
.vp-gantt td.cel { width: 30px; min-width: 30px; padding: 0 1px; border-left: 1px solid #f1f3f5; }
.vp-gantt td.cel.s1 { border-left: 1px solid #ced4da; }
.vp-gantt .barra { height: 14px; background: #4472c4; }
.vp-gantt .barra.ini { border-radius: 7px 0 0 7px; margin-left: 2px; }
.vp-gantt .barra.fin { border-radius: 0 7px 7px 0; margin-right: 2px; }
.vp-gantt .barra.ini.fin { border-radius: 7px; }
.vp-acc-ver { font-size: 11px; color: #6c757d; margin-top: 3px; }
.vp-nota { font-size: 11px; color: #6c757d; margin-top: 4px; }

@media (max-width: 480px) {
    .vp-titulo { font-size: 16px; }
    .vp-card-v { font-size: 20px; }
}
</style>
</head>

<body>
<div id="pprint">

<!-- Encabezado -->
<div class="vp-head">
    <img src="img/logo_website_2021.png" alt="">
    <div class="vp-titulo"><?php echo vp_h($nombre); ?></div>
</div>

<div class="vp-chips">
    <?php if ($codigo !== ''): ?>
    <span class="vp-chip"><?php echo vp_h($codigo); ?></span>
    <?php endif; ?>
    <?php if (!vp_vacio($row['sector_txt'])): ?>
    <span class="vp-chip sector" style="background:<?php echo vp_h(trim($row['sector_color'] ?: '#1a5276')); ?>"><?php echo vp_h($row['sector_txt']); ?></span>
    <?php endif; ?>
    <?php if (!vp_vacio($row['tipo'])): ?>
    <span class="vp-chip"><?php echo vp_h($row['tipo']); ?></span>
    <?php endif; ?>
    <?php if (($row['seleccionada_pm_sjm'] ?? '') === 'Si'): ?>
    <span class="vp-chip pm"><i class="fas fa-star"></i> Preseleccionada Plan Maestro SJM</span>
    <?php endif; ?>
</div>

<div class="vp-pdf" data-html2canvas-ignore="true">
    <a onclick="generatePDF();" href="javascript:;"><img src="img/pdf.png" alt=""> Descargar PDF</a>
</div>


<!-- Tarjetas -->
<div class="vp-cards">
    <div class="vp-card" style="background:#e8f4f8; border-left:4px solid #4a8ecd;">
        <div class="vp-card-t">% Avance Financiero</div>
        <div class="vp-card-v"><?php echo vp_num($avance_financiero); ?>%</div>
        <div class="vp-bar"><div style="width:<?php echo min($avance_financiero, 100); ?>%; background:#4a8ecd;"></div></div>
    </div>
    <div class="vp-card" style="background:#e8f5e9; border-left:4px solid #4a8f5e;">
        <div class="vp-card-t">% Avance Actividades</div>
        <div class="vp-card-v"><?php echo vp_num($avance_actividades); ?>%</div>
        <div class="vp-bar"><div style="width:<?php echo min($avance_actividades, 100); ?>%; background:#4a8f5e;"></div></div>
    </div>
    <div class="vp-card" style="background:#fff3e0; border-left:4px solid #d17a4f;">
        <div class="vp-card-t">Proceso Actual</div>
        <div class="vp-card-v txt"><?php echo vp_h($row['actividad_actual'] ?: 'Sin proceso asignado'); ?></div>
    </div>
</div>


<?php
// ============================================================
// INICIATIVA
// ============================================================
vp_seccion('fas fa-lightbulb', 'Iniciativa');
?>
<div class="vp-grid">
    <?php
    vp_campo('N°', $row['numero']);
    vp_campo('Código', $codigo);
    vp_campo('IDI / BIP', $row['codigo_idi']);
    vp_campo('Preseleccionada Plan Maestro SJM', ($row['seleccionada_pm_sjm'] ?? '') === 'Si' ? 'Sí' : ($row['seleccionada_pm_sjm'] ?? ''));
    vp_campo('Tipo de brecha', $row['tipo_de_inversion']);
    ?>
</div>


<?php
// ============================================================
// DESCRIPCIÓN
// ============================================================
vp_seccion('fas fa-align-left', 'Descripción');
?>
<div class="vp-grid">
    <?php
    vp_campo('Instrumento de origen', $row['instrumento_de_origen'], 'full');
    vp_campo('Tipología de iniciativa', $row['tipo']);
    vp_campo('Descripción', $row['descripcion'], 'full');
    vp_campo('Eje estratégico', $row['eje_estrategico'], 'full');
    vp_campo('Objetivo estratégico', $row['objetivo_estrategico'], 'full');
    vp_campo('Unidad Técnica Responsable (UTR)', $row['utr'], 'full');
    vp_campo('Sector SNI', $row['sector_txt']);
    vp_campo('Subsector SNI', $row['subsector_de_inversion_sni']);
    ?>
</div>


<?php
// ============================================================
// BRECHAS
// ============================================================
if (vp_hay($row, ['brecha_principal', 'brecha_especifica', 'atributo_al_que_responde'])):
    vp_seccion('fas fa-bullseye', 'Brechas');
?>
<div class="vp-grid">
    <?php
    vp_campo('Brecha principal', $row['brecha_principal'], 'full');
    vp_campo('Atributo al que responde', $row['atributo_al_que_responde'], 'full');
    vp_campo('Brecha específica', $row['brecha_especifica'], 'full');
    ?>
</div>
<?php endif; ?>


<?php
// ============================================================
// EMPLAZAMIENTO (sin mapa)
// ============================================================
if ($localidades || $ubicaciones || vp_hay($row, ['localizacion', 'localizacion_2'])):
    vp_seccion('fas fa-map-marker-alt', 'Emplazamiento');
?>
<div class="vp-grid">
    <?php
    vp_campo('Alcance', $row['localizacion']);
    vp_campo('Localización', $row['localizacion_2']);
    if ($localidades) {
        $chips = '<div class="vp-loc">';
        foreach ($localidades as $l) $chips .= '<span>' . vp_h($l) . '</span>';
        $chips .= '</div>';
        vp_campo('Localidades cubiertas', $chips, 'full', true);
    }
    ?>
</div>
<?php if ($ubicaciones): ?>
<?php vp_sub('Ubicaciones adicionales'); ?>
<div class="vp-tabla-wrap">
<table class="vp-tabla">
    <thead>
        <tr>
            <th style="text-align:center; width:30px">N°</th>
            <th style="text-align:left">Descripción</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($ubicaciones as $i => $u): ?>
        <tr>
            <td style="text-align:center"><?php echo $i + 1; ?></td>
            <td><?php echo vp_h($u['descripcion']); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
<?php endif; ?>


<?php
// ============================================================
// FINANCIAMIENTO
// ============================================================
vp_seccion('fas fa-dollar-sign', 'Financiamiento');

// Meta física
if (vp_hay($row, ['indicador_2', 'unidad_de_medida', 'cantidad_proyectada', 'cantidad_realizada'])):
    vp_sub('Meta física');
?>
<div class="vp-grid">
    <?php
    vp_campo('Indicador', $row['indicador_2'], 'full');
    vp_campo('Unidad', $row['unidad_de_medida']);
    vp_campo('Cantidad proyectada', vp_num($row['cantidad_proyectada']));
    vp_campo('Cantidad realizada', vp_num($row['cantidad_realizada']));

    $proy = (float) ($row['cantidad_proyectada'] ?? 0);
    if ($proy > 0) {
        $pct   = (float) ($row['cantidad_realizada'] ?? 0) * 100 / $proy;
        $color = $pct >= 100 ? '#2e9e5b' : ($pct >= 50 ? '#2a81cb' : '#e8743b');
        $barra = '<div class="vp-meta"><div class="vp-bar"><div style="width:' . max(0, min(100, $pct)) . '%; background:' . $color . ';"></div></div>'
               . '<strong>' . number_format($pct, 1, ',', '.') . '%</strong></div>';
        vp_campo('Avance de la meta física', $barra, 'full', true);
    }
    ?>
</div>
<?php endif; ?>

<?php
// Costos (M$)
if ($total_monto > 0 || vp_hay($row, ['costo_unitario_ejecucion', 'origen_del_calculo_de_costos'])):
    vp_sub('Costos (M$)');
?>
<div class="vp-grid">
    <?php
    vp_campo('M$ Prefactibilidad', vp_m($row['costos_perfil_prefactibilidad']));
    vp_campo('M$ Diseño', vp_m($row['costos_diseno']));
    vp_campo('M$ Ejecución', vp_m($row['costos_ejecucion']));
    if ($total_monto > 0) {
        vp_campo('M$ TOTAL', '<span class="vp-total">' . vp_m($total_monto) . '</span>', '', true);
    }
    vp_campo('Valor unidad ejecución M$', vp_m($row['costo_unitario_ejecucion']));
    vp_campo('Origen del cálculo de costos', $row['origen_del_calculo_de_costos'], 'full');
    ?>
</div>
<?php endif; ?>

<?php
// Fuente de financiamiento por etapa (tabla financiamiento)
if ($financiamiento) {
    vp_sub('Fuente de financiamiento por etapa (M$)');
    vp_tabla_etapas($financiamiento);
}

// Gasto efectivo por etapa
if ($gasto_total > 0):
    vp_sub('Gasto efectivo por etapa (M$)');
?>
<div class="vp-grid">
    <?php
    vp_campo('Gasto Prefactibilidad', vp_m($row['gasto_perfil_prefactibilidad']));
    vp_campo('Gasto Diseño', vp_m($row['gasto_diseno']));
    vp_campo('Gasto Ejecución', vp_m($row['gasto_ejecucion']));
    vp_campo('Gasto TOTAL', '<span class="vp-total">' . vp_m($gasto_total) . '</span>', '', true);
    ?>
</div>
<?php endif; ?>

<?php
// Fuente de financiamiento del gasto por etapa (tabla gasto)
if ($gastos) {
    vp_sub('Fuente de financiamiento del gasto por etapa (M$)');
    vp_tabla_etapas($gastos);
}

if ($total_monto <= 0 && !$financiamiento && $gasto_total <= 0 && !$gastos
    && !vp_hay($row, ['indicador_2', 'unidad_de_medida', 'cantidad_proyectada', 'cantidad_realizada', 'costo_unitario_ejecucion', 'origen_del_calculo_de_costos'])) {
    echo '<div class="vp-nada">Sin información de financiamiento.</div>';
}
?>


<?php
// ============================================================
// MANTENCIÓN
// ============================================================
if (vp_hay($row, ['responsable_del_mantenimiento', 'financiamiento_del_mantenimiento'])):
    vp_seccion('fas fa-tools', 'Mantención');
?>
<div class="vp-grid">
    <?php
    vp_campo('Responsable', $row['responsable_del_mantenimiento']);
    vp_campo('Financiamiento', $row['financiamiento_del_mantenimiento']);
    ?>
</div>
<?php endif; ?>


<?php
// ============================================================
// CRONOGRAMA (AÑOS)
// ============================================================
if (vp_hay($row, ['inicio', 'termino', 'plazos_ejecucion_anos'])):
    vp_seccion('fas fa-calendar-alt', 'Cronograma (años)');
?>
<div class="vp-grid">
    <?php
    vp_campo('Inicio', $row['inicio']);
    vp_campo('Término', $row['termino']);
    vp_campo('Plazo de ejecución (años)', $row['plazos_ejecucion_anos']);
    ?>
</div>
<?php endif; ?>


<?php
// ============================================================
// ESTADO
// ============================================================
vp_seccion('fas fa-flag', 'Estado');
?>
<div class="vp-grid">
    <?php
    vp_campo('Etapa actual', $row['etapa_actual'] ?: 'Sin etapa asignada');
    vp_campo('Actividad actual (proceso)', $row['actividad_actual'] ?: 'Sin proceso asignado');
    ?>
</div>


<?php
// ============================================================
// ACCIONES (ruta crítica, ordenadas por inicio)
// ============================================================
vp_seccion('fas fa-list-check', 'Acciones');
?>
<?php if ($acciones): ?>
<div class="vp-gantt-wrap">
<table class="vp-gantt">
    <thead>
        <tr>
            <th class="col-acc" rowspan="2">Acción</th>
            <th rowspan="2">Inicio</th>
            <th rowspan="2">Fin</th>
            <?php foreach ($g_anos as $ano => $sems): ?>
            <th colspan="<?php echo count($sems); ?>"><?php echo (int) $ano; ?></th>
            <?php endforeach; ?>
        </tr>
        <tr>
            <?php foreach ($g_anos as $sems): foreach ($sems as $s): ?>
            <th>S<?php echo ($s % 2) + 1; ?></th>
            <?php endforeach; endforeach; ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($acciones as $a):
        $si = vp_sem($a['inicio']); $sf = vp_sem($a['fin']);
        if ($si === null) $si = $sf;
        if ($sf === null) $sf = $si;
    ?>
        <tr>
            <td class="col-acc">
                <?php echo vp_h($a['nombre_accion']); ?>
                <?php if (!vp_vacio($a['verificador'])): ?>
                <div class="vp-acc-ver"><i class="fas fa-check-double"></i> <?php echo vp_h($a['verificador']); ?></div>
                <?php endif; ?>
            </td>
            <td class="col-fec"><?php echo vp_fecha($a['inicio']); ?></td>
            <td class="col-fec"><?php echo vp_fecha($a['fin']); ?></td>
            <?php foreach ($g_anos as $sems): foreach ($sems as $s):
                $on = ($si !== null && $s >= $si && $s <= $sf);
            ?>
            <td class="cel<?php echo ($s % 2 === 0) ? ' s1' : ''; ?>"><?php if ($on): ?><div class="barra<?php echo $s === $si ? ' ini' : ''; ?><?php echo $s === $sf ? ' fin' : ''; ?>"></div><?php endif; ?></td>
            <?php endforeach; endforeach; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<div class="vp-nota">S1 = enero a junio, S2 = julio a diciembre. Desplace la tabla hacia la derecha para ver todos los semestres.</div>

<?php else: ?>
<div class="vp-nada">No hay acciones registradas.</div>
<?php endif; ?>

<div style="height:10px"></div>

</div><!-- /pprint -->

<script>
function generatePDF() {
    const element = document.getElementById('pprint');
    const nombre  = <?php echo json_encode(function_exists('mb_substr') ? mb_substr($nombre, 0, 80) : substr($nombre, 0, 80), JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE); ?>;

    const opt = {
        margin: 0.6,
        filename: nombre.replace(/[\\/:*?"<>|]+/g, '-') + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
        pagebreak: { mode: ['css', 'legacy'], avoid: ['.vp-campo', '.vp-card', 'tr'] }
    };

    html2pdf().from(element).set(opt).toPdf().get('pdf').then(function (pdf) {
        const totalPages = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(9);
            pdf.text('Página ' + i + ' de ' + totalPages, pdf.internal.pageSize.getWidth() / 2, pdf.internal.pageSize.getHeight() - 0.35, { align: 'center' });
        }
    }).save();
}
</script>

</body>
</html>
