<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-06
// ************************************************************
// Ficha de consulta de una iniciativa (se abre desde el mapa).
// Basada en los grupos de proyectos_editar.php, solo lectura.
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


// -----------------------------------------------------
// Actividades y avances (se calculan al vuelo)
// -----------------------------------------------------
$sql = "SELECT * FROM actividades WHERE proyecto = $proyecto_id ORDER BY ano";
if (!$actividades = $db->query($sql)) {
    die('Hay un error [' . $db->error . ']');
}

$costo_pref  = (float) ($row['costos_perfil_prefactibilidad'] ?? 0);
$costo_dis   = (float) ($row['costos_diseno'] ?? 0);
$costo_eje   = (float) ($row['costos_ejecucion'] ?? 0);
$total_monto = $costo_pref + $costo_dis + $costo_eje;

$total_act = 0; $act_completas = 0; $monto_completo = 0;
$lista_actividades = [];
while ($a = $actividades->fetch_assoc()) {
    $lista_actividades[] = $a;
    $total_act++;
    if ($a['estado'] === 'Completa') {
        $act_completas++;
        $monto_completo += (float) $a['monto'];
    }
}
$avance_actividades = $total_act > 0   ? round($act_completas * 100 / $total_act, 1) : 0;
$avance_financiero  = $total_monto > 0 ? round($monto_completo * 100 / $total_monto, 1) : 0;


// -----------------------------------------------------
// Localidades marcadas
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

// Monto en M$ (sin decimales, formato chileno)
function vp_mm($v) {
    return vp_vacio($v) ? '' : 'M$ ' . number_format((float) $v, 0, ',', '.');
}

function vp_num($v) {
    if (vp_vacio($v)) return '';
    $f = (float) $v;
    return number_format($f, (floor($f) == $f) ? 0 : 2, ',', '.');
}

// Título de sección
function vp_seccion($icono, $titulo) {
    echo '<div class="vp-seccion"><i class="' . $icono . '"></i><span>' . $titulo . '</span></div>';
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

/* Campos */
.vp-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 2px 14px; }
.vp-campo { padding: 6px 2px; border-bottom: 1px solid #eef0f3; }
.vp-campo.full { grid-column: 1 / -1; }
.vp-label { font-size: 11px; font-weight: 700; color: #6c757d; text-transform: uppercase; letter-spacing: .4px; margin-bottom: 2px; }
.vp-valor { font-size: 14px; line-height: 1.45; word-wrap: break-word; }
.vp-total { font-size: 18px; font-weight: 700; color: #1a5276; }

.vp-loc { display: flex; flex-wrap: wrap; gap: 5px; margin-top: 3px; }
.vp-loc span { font-size: 12px; padding: 2px 9px; border-radius: 10px; background: #e8f5e9; color: #2e6b3f; }

/* Actividades */
.vp-tabla-wrap { overflow-x: auto; width: 100%; }
.vp-tabla { width: 100%; border-collapse: collapse; font-size: 13px; }
.vp-tabla th { font-size: 11px; font-weight: 700; color: #6c757d; text-transform: uppercase; padding: 6px; border-bottom: 2px solid #dee2e6; }
.vp-tabla td { padding: 6px; border-bottom: 1px solid #eef0f3; vertical-align: top; }
.vp-estado { font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 10px; background: #f1f3f5; white-space: nowrap; }
.vp-estado.ok { background: #e8f5e9; color: #2e6b3f; }
.vp-nada { text-align: center; color: #6c757d; padding: 12px; font-size: 13px; }

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
    <?php if (!vp_vacio($row['sector_txt'])): ?>
    <span class="vp-chip sector" style="background:<?php echo vp_h(trim($row['sector_color'] ?: '#1a5276')); ?>"><?php echo vp_h($row['sector_txt']); ?></span>
    <?php endif; ?>
    <?php if (!vp_vacio($row['tipo'])): ?>
    <span class="vp-chip"><?php echo vp_h($row['tipo']); ?></span>
    <?php endif; ?>
    <?php if (($row['seleccionada_pm_sjm'] ?? '') === 'Si'): ?>
    <span class="vp-chip pm"><i class="fas fa-star"></i> Plan Maestro SJM</span>
    <?php endif; ?>
</div>

<div class="vp-pdf" data-html2canvas-ignore="true">
    <a onclick="generatePDF();" href="javascript:;"><img src="img/pdf.png" alt=""> Descargar PDF</a>
</div>


<!-- Tarjetas -->
<div class="vp-cards">
    <div class="vp-card" style="background:#e8f4f8; border-left:4px solid #4a8ecd;">
        <div class="vp-card-t">Avance financiero</div>
        <div class="vp-card-v"><?php echo vp_num($avance_financiero); ?>%</div>
        <div class="vp-bar"><div style="width:<?php echo min($avance_financiero, 100); ?>%; background:#4a8ecd;"></div></div>
    </div>
    <div class="vp-card" style="background:#e8f5e9; border-left:4px solid #4a8f5e;">
        <div class="vp-card-t">Avance actividades</div>
        <div class="vp-card-v"><?php echo vp_num($avance_actividades); ?>%</div>
        <div class="vp-bar"><div style="width:<?php echo min($avance_actividades, 100); ?>%; background:#4a8f5e;"></div></div>
    </div>
    <div class="vp-card" style="background:#fff3e0; border-left:4px solid #d17a4f;">
        <div class="vp-card-t">Proceso actual</div>
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
    vp_campo('Código IDI / BIP', $row['codigo_idi']);
    vp_campo('Tipo de inversión', $row['tipo_de_inversion']);
    vp_campo('Instrumento de origen', $row['instrumento_de_origen'], 'full');
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
    vp_campo('Descripción', $row['descripcion'], 'full');
    vp_campo('Eje estratégico', $row['eje_estrategico'], 'full');
    vp_campo('Objetivo estratégico', $row['objetivo_estrategico'], 'full');
    vp_campo('Sector SNI', $row['sector_txt']);
    vp_campo('Subsector SNI', $row['subsector_de_inversion_sni']);
    vp_campo('Tipología', $row['tipo']);
    vp_campo('Unidad Técnica Responsable', $row['utr'], 'full');
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
    vp_campo('Brecha específica', $row['brecha_especifica'], 'full');
    vp_campo('Atributo al que responde', $row['atributo_al_que_responde'], 'full');
    ?>
</div>
<?php endif; ?>


<?php
// ============================================================
// EMPLAZAMIENTO (sin mapa)
// ============================================================
if ($localidades || vp_hay($row, ['localizacion', 'localizacion_2'])):
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
        vp_campo('Localidades', $chips, 'full', true);
    }
    ?>
</div>
<?php endif; ?>


<?php
// ============================================================
// FINANCIAMIENTO
// ============================================================
vp_seccion('fas fa-dollar-sign', 'Financiamiento');
?>
<div class="vp-grid">
    <?php
    $meta = '';
    if (!vp_vacio($row['cantidad_proyectada'])) {
        $meta = vp_num($row['cantidad_proyectada']) . ' ' . vp_h($row['unidad_de_medida']);
    }
    vp_campo('Indicador', $row['indicador_2'], 'full');
    vp_campo('Meta proyectada', $meta, '', true);
    if (!vp_vacio($row['cantidad_realizada'])) {
        vp_campo('Meta realizada', vp_num($row['cantidad_realizada']) . ' ' . vp_h($row['unidad_de_medida']), '', true);
    }

    vp_campo('Costo prefactibilidad', vp_mm($row['costos_perfil_prefactibilidad']));
    vp_campo('Costo diseño', vp_mm($row['costos_diseno']));
    vp_campo('Costo ejecución', vp_mm($row['costos_ejecucion']));
    if ($total_monto > 0) {
        vp_campo('Costo total', '<span class="vp-total">' . vp_mm($total_monto) . '</span>', '', true);
    }

    vp_campo('Financiamiento prefactibilidad', $row['fuente_financiamiento_prefactibilidad']);
    vp_campo('Financiamiento diseño', $row['fuente_financiamiento_diseno']);
    vp_campo('Financiamiento ejecución', $row['fuente_financiamiento_ejecucion']);

    vp_campo('Gasto prefactibilidad', vp_mm($row['gasto_perfil_prefactibilidad']));
    vp_campo('Gasto diseño', vp_mm($row['gasto_diseno']));
    vp_campo('Gasto ejecución', vp_mm($row['gasto_ejecucion']));
    ?>
</div>


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
    if (!vp_vacio($row['plazos_ejecucion_anos'])) {
        vp_campo('Plazo de ejecución', $row['plazos_ejecucion_anos'] . ' ' . ((int) $row['plazos_ejecucion_anos'] === 1 ? 'año' : 'años'));
    }
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
    vp_campo('Proceso actual', $row['actividad_actual'] ?: 'Sin proceso asignado');
    ?>
</div>


<?php
// ============================================================
// ACTIVIDADES
// ============================================================
vp_seccion('fas fa-list-check', 'Actividades');
?>
<div class="vp-tabla-wrap">
<?php if ($lista_actividades): ?>
<table class="vp-tabla">
    <thead>
        <tr>
            <th style="text-align:left">Actividad</th>
            <th style="text-align:center">Año</th>
            <th style="text-align:right">Monto M$</th>
            <th style="text-align:center">Estado</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($lista_actividades as $a): ?>
        <tr>
            <td>
                <?php echo vp_h($a['actividad']); ?>
                <?php if (!vp_vacio($a['finaciamiento']) || !vp_vacio($a['responsable'])): ?>
                <div style="font-size:11px;color:#6c757d;margin-top:2px;">
                    <?php echo vp_h(implode(' · ', array_filter([$a['finaciamiento'], $a['responsable']], function($x) { return !vp_vacio($x); }))); ?>
                </div>
                <?php endif; ?>
            </td>
            <td style="text-align:center"><?php echo vp_h($a['ano']); ?></td>
            <td style="text-align:right; white-space:nowrap"><?php echo number_format((float) $a['monto'], 0, ',', '.'); ?></td>
            <td style="text-align:center"><span class="vp-estado <?php echo $a['estado'] === 'Completa' ? 'ok' : ''; ?>"><?php echo vp_h($a['estado']); ?></span></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="vp-nada">No hay actividades registradas.</div>
<?php endif; ?>
</div>

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
