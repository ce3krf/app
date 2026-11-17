<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-11-16
// ************************************************************
?>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

@session_start();
include("../conf/db.php");		
include('functions.php');


if (!isset($_GET['proyectos_id']) || !is_numeric($_GET['proyectos_id'])) {
    die('ID de proyecto no válido');
}

$codigo_proyecto = (int) $_GET['proyectos_id'];
$es_nuevo        = ($codigo_proyecto === 0);


// -----------------------------------------------------
// Helpers de presentación
// -----------------------------------------------------
function h($v) {
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
}

// Número en formato chileno para inputs (vacío si es NULL)
function fnum($v, $dec = 2) {
    if ($v === null || $v === '') return '';
    return number_format((float) $v, $dec, ',', '.');
}

// Título de grupo de datos
function seccion($icono, $titulo) {
    echo '<div style="height:34px; clear:both"></div>
<div class="row"><div class="col-12">
<div class="ficha-seccion">
  <span class="ficha-seccion-icono"><i class="' . $icono . '"></i></span>
  <span class="ficha-seccion-titulo">' . $titulo . '</span>
</div>
</div></div>';
}

// Input de texto / número. $o: type, datalist, class, placeholder, maxlength, col, attrs
function campo($row, $name, $label, $ayuda = '', $o = []) {
    $type  = $o['type'] ?? 'text';
    $class = 'form-control' . (!empty($o['class']) ? ' ' . $o['class'] : '');
    $valor = $o['value'] ?? ($row[$name] ?? '');
    $col   = $o['col'] ?? 'col-md-6';

    echo '<div class="' . $col . '"><div class="form-group">';
    echo '<label for="' . $name . '">' . $label . '</label>';
    echo '<input type="' . $type . '" class="' . $class . '" id="' . $name . '" name="' . $name . '" value="' . h($valor) . '"';
    if (!empty($o['datalist']))    echo ' list="dl_' . $o['datalist'] . '" autocomplete="off"';
    if (!empty($o['placeholder'])) echo ' placeholder="' . h($o['placeholder']) . '"';
    if (!empty($o['maxlength']))   echo ' maxlength="' . (int) $o['maxlength'] . '"';
    if (!empty($o['attrs']))       echo ' ' . $o['attrs'];
    echo '>';
    if ($ayuda !== '') echo '<small class="text-muted">' . $ayuda . '</small>';
    echo '</div></div>';
}

// Textarea
function campo_area($row, $name, $label, $ayuda = '', $rows = 3, $col = 'col-12') {
    echo '<div class="' . $col . '"><div class="form-group">';
    echo '<label for="' . $name . '">' . $label . '</label>';
    echo '<textarea class="form-control" id="' . $name . '" name="' . $name . '" rows="' . $rows . '">' . h($row[$name] ?? '') . '</textarea>';
    if ($ayuda !== '') echo '<small class="text-muted">' . $ayuda . '</small>';
    echo '</div></div>';
}

// Opciones de un select de catálogo. Si el código guardado ya no existe
// en la tabla, se agrega igual para no perder el dato al guardar.
function opciones(array $cat, $cod_actual, $texto_actual) {
    $cod_actual = (string) ($cod_actual ?? '');
    foreach ($cat as $cod => $txt) {
        $sel = ((string) $cod === $cod_actual) ? ' selected' : '';
        echo '<option value="' . h($cod) . '"' . $sel . '>' . h($txt) . '</option>';
    }
    if ($cod_actual !== '' && !array_key_exists($cod_actual, $cat)) {
        echo '<option value="' . h($cod_actual) . '" selected>' . h($texto_actual ?: 'Código ' . $cod_actual) . '</option>';
    }
}

// Datalist con los valores ya usados en una o más columnas (+ valores fijos)
function datalist($db, $id, array $columnas, array $extra = []) {
    $valores = $extra;
    foreach ($columnas as $c) {
        $r = $db->query("SELECT DISTINCT $c AS v FROM proyectos WHERE $c IS NOT NULL AND $c <> '' ORDER BY $c");
        if ($r) while ($x = $r->fetch_assoc()) $valores[] = $x['v'];
    }
    $valores = array_unique($valores);
    natcasesort($valores);
    echo '<datalist id="dl_' . $id . '">';
    foreach ($valores as $v) echo '<option value="' . h($v) . '">';
    echo '</datalist>';
}


// -----------------------------------------------------
// Catálogos: se leen de las tablas que ya tienen mantenedor
// (tipo, sectores, subsectores, etapas, procesos)
// El id de cada tabla se guarda en el cod_* de proyectos y la
// descripción en el campo de texto correspondiente.
// -----------------------------------------------------
function catalogo($db, $sql, $id, $txt) {
    $out = [];
    if (!$r = $db->query($sql)) {
        die('Hay un error [' . $db->error . ']');
    }
    while ($x = $r->fetch_assoc()) $out[$x[$id]] = $x[$txt];
    return $out;
}
$CAT_TIPOS       = catalogo($db, "SELECT id, tipo FROM tipo ORDER BY id", 'id', 'tipo');
$CAT_SECTORES    = catalogo($db, "SELECT sector_id, sector_descripcion FROM sectores ORDER BY sector_descripcion", 'sector_id', 'sector_descripcion');
$CAT_SUBSECTORES = catalogo($db, "SELECT subsector_id, subsector_descripcion FROM subsectores ORDER BY subsector_descripcion", 'subsector_id', 'subsector_descripcion');
$CAT_ETAPAS      = catalogo($db, "SELECT etapas_id, etapas_descripcion FROM etapas ORDER BY etapas_id", 'etapas_id', 'etapas_descripcion');
$CAT_PROCESOS    = catalogo($db, "SELECT procesos_id, procesos_descripcion FROM procesos ORDER BY procesos_id", 'procesos_id', 'procesos_descripcion');

// Sin tabla en la BD: valores de referencia de la Cartera Consolidada
$CAT_BRECHAS = [
    'B1 Accesibilidad y ordenamiento de flujos',
    'B2 Capacidad de acogida e infraestructura habilitante',
    'B3 Puesta en valor de principales atributos',
    'B4 Resiliencia y habitabilidad',
];
$CAT_ALCANCE = ['Local', 'Varias localidades', 'Comunal'];

// Localidades (columnas tinyint 0/1 de proyectos) => etiqueta
$CAT_LOCALIDADES = [
    'la_obra' => 'La Obra', 'las_vertientes' => 'Las Vertientes', 'el_canelo' => 'El Canelo',
    'el_manzano' => 'El Manzano', 'los_maitenes' => 'Los Maitenes', 'el_alfalfal' => 'El Alfalfal',
    'el_colorado' => 'El Colorado', 'el_guayacan' => 'El Guayacán', 'san_jose_de_maipo' => 'San José de Maipo',
    'el_melocoton' => 'El Melocotón', 'lagunillas' => 'Lagunillas', 'el_toyo' => 'El Toyo',
    'san_alfonso' => 'San Alfonso', 'el_ingenio' => 'El Ingenio', 'bollenar' => 'Bollenar',
    'san_gabriel' => 'San Gabriel', 'el_yeso' => 'El Yeso', 'las_melosas' => 'Las Melosas',
    'los_queltehues' => 'Los Queltehues', 'el_volcan' => 'El Volcán', 'banos_morales' => 'Baños Morales',
    'lo_valdes' => 'Lo Valdés', 'el_romeral' => 'El Romeral', 'los_nogales' => 'Los Nogales',
];


// -----------------------------------------------------
// Datos del proyecto
// -----------------------------------------------------
$row = [];
if (!$es_nuevo) {
    $stmt = $db->prepare("SELECT * FROM proyectos WHERE id = ?");
    $stmt->bind_param('i', $codigo_proyecto);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        die('La iniciativa no existe.');
    }
}

// Costo total (guardado o calculado)
$costo_pref  = (float) ($row['costos_perfil_prefactibilidad'] ?? 0);
$costo_dis   = (float) ($row['costos_diseno'] ?? 0);
$costo_eje   = (float) ($row['costos_ejecucion'] ?? 0);
$total_monto = $costo_pref + $costo_dis + $costo_eje;


// -----------------------------------------------------
// Avances (se calculan al vuelo, ya no se guardan en proyectos)
// -----------------------------------------------------
$avance_actividades = 0;
$avance_financiero  = 0;

if (!$es_nuevo) {
    $q = "
        SELECT
            COUNT(*)                                                             AS total_act,
            SUM(CASE WHEN estado = 'Completa' THEN 1 ELSE 0 END)                 AS act_completas,
            COALESCE(SUM(CASE WHEN estado = 'Completa' THEN monto ELSE 0 END),0) AS monto_completo
        FROM actividades
        WHERE proyecto = ?
    ";
    $stmt = $db->prepare($q);
    $stmt->bind_param('i', $codigo_proyecto);
    $stmt->execute();
    $act = $stmt->get_result()->fetch_assoc();

    $total_act = (int) $act['total_act'];
    if ($total_act > 0) {
        $avance_actividades = round((int) $act['act_completas'] * 100 / $total_act, 2);
    }
    if ($total_monto > 0) {
        $avance_financiero = round((float) $act['monto_completo'] * 100 / $total_monto, 2);
    }
}


// -----------------------------------------------------
// Instrumentos (perfil ÁREA: solo los asignados)
// -----------------------------------------------------
$perfil_usuario = $_SESSION['usuarios_profile'];
$user_id_areas  = (int) $_SESSION["net_fulltrust_fas_id"];

if ($perfil_usuario === 'ÁREA') {
    $sql = "
        SELECT i.instrumentos_id, i.instrumentos_descripcion
        FROM instrumentos i
        INNER JOIN usuarios_areas ua 
            ON ua.instrumentos_id = i.instrumentos_id
            AND ua.usuarios_id = $user_id_areas
        ORDER BY i.instrumentos_descripcion
    ";
} else {
    $sql = "SELECT instrumentos_id, instrumentos_descripcion FROM instrumentos ORDER BY instrumentos_descripcion";
}
if (!$res_inst = $db->query($sql)) {
    die('Hay un error [' . $db->error . ']');
}
$instrumentos = $res_inst->fetch_all(MYSQLI_ASSOC);

// Instrumentos permitidos (perfil ÁREA)
$instrumentos_permitidos = [];
if ($perfil_usuario === 'ÁREA') {
    $r_ap = $db->query("SELECT instrumentos_id FROM usuarios_areas WHERE usuarios_id = $user_id_areas AND instrumentos_id IS NOT NULL");
    if ($r_ap) while ($u = $r_ap->fetch_assoc()) $instrumentos_permitidos[] = (int) $u['instrumentos_id'];
}

// Solo lectura: perfil ÁREA y la iniciativa no es de sus instrumentos
$solo_lectura = ($perfil_usuario === 'ÁREA' && !$es_nuevo && !in_array((int) ($row['n_inst'] ?? 0), $instrumentos_permitidos, true));


// -----------------------------------------------------
// Acciones (tabla acciones, enlazada por acciones.codigo_iniciativa = proyectos.codigo)
// Ordenadas por fecha de inicio para que la Gantt tenga sentido.
// -----------------------------------------------------
$acciones        = [];
$codigo_acciones = trim((string) ($row['codigo'] ?? ''));

if (!$es_nuevo && $codigo_acciones !== '') {
    $stmt = $db->prepare("
        SELECT id, nombre_accion, inicio, fin, verificador
        FROM acciones
        WHERE codigo_iniciativa = ?
        ORDER BY (inicio IS NULL), inicio, (fin IS NULL), fin, id
    ");
    $stmt->bind_param('s', $codigo_acciones);
    $stmt->execute();
    $acciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Gantt semestral: índice de semestre = año * 2 + (0 = ene-jun, 1 = jul-dic)
function semestre_idx($fecha) {
    if (empty($fecha)) return null;
    $t = strtotime($fecha);
    if ($t === false) return null;
    return (int) date('Y', $t) * 2 + ((int) date('n', $t) > 6 ? 1 : 0);
}

$gantt_ini = null;
$gantt_fin = null;
foreach ($acciones as $a) {
    $si = semestre_idx($a['inicio']);
    $sf = semestre_idx($a['fin']);
    if ($si !== null) { $gantt_ini = ($gantt_ini === null) ? $si : min($gantt_ini, $si); }
    if ($sf !== null) { $gantt_fin = ($gantt_fin === null) ? $sf : max($gantt_fin, $sf); }
}
if ($gantt_ini !== null && $gantt_fin === null) $gantt_fin = $gantt_ini;
if ($gantt_fin !== null && $gantt_ini === null) $gantt_ini = $gantt_fin;

// Columnas de la Gantt agrupadas por año: [año => [idx_S1?, idx_S2?]]
$gantt_anos = [];
if ($gantt_ini !== null) {
    for ($s = $gantt_ini; $s <= $gantt_fin; $s++) {
        $gantt_anos[intdiv($s, 2)][] = $s;
    }
}

function fecha_cl($f) {
    return empty($f) ? '-' : date('d/m/Y', strtotime($f));
}


// Localidades marcadas (texto)
$localidades_txt = [];
foreach ($CAT_LOCALIDADES as $col => $label) {
    if (!empty($row[$col])) $localidades_txt[] = $label;
}

// Coordenadas (point_x = latitud, point_y = longitud, según la planilla)
$lat_actual = $row['point_x'] ?? '';
$lng_actual = $row['point_y'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo h($row_param["parametros_titulo"] ?? 'Iniciativa'); ?></title>

<link rel="icon" type="image/png" href="../favicon.png">
<script src="js/jquery-3.5.1.min.js"></script>	
	
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="css/styles.css"/>

<!-- Leaflet OSM -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

<!-- Modal Mapa Geolocalización -->
<style>
#geoModal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(3px);
    align-items: center;
    justify-content: center;
}
#geoModal.active {
    display: flex;
}
#geoModalBox {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 20px 60px rgba(0,0,0,.35);
    width: min(820px, 96vw);
    max-height: 92vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: geoSlideIn .22s ease;
}
@keyframes geoSlideIn {
    from { transform: translateY(-30px); opacity: 0; }
    to   { transform: translateY(0);     opacity: 1; }
}
#geoModalHeader {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px 14px;
    background: linear-gradient(135deg, #023047 0%, #1a5276 100%);
    color: #fff;
    flex-shrink: 0;
}
#geoModalHeader h6 {
    margin: 0;
    font-size: 15px;
    font-weight: 600;
    letter-spacing: .3px;
}
#geoModalHeader p {
    margin: 2px 0 0;
    font-size: 12px;
    opacity: .75;
}
#geoModalClose {
    background: rgba(255,255,255,.18);
    border: none;
    border-radius: 50%;
    width: 32px; height: 32px;
    cursor: pointer;
    color: #fff;
    font-size: 16px;
    line-height: 32px;
    text-align: center;
    transition: background .15s;
    flex-shrink: 0;
}
#geoModalClose:hover { background: rgba(255,255,255,.32); }
#geoMapContainer {
    flex: 1;
    min-height: 420px;
    height: 420px;
    position: relative;
    cursor: crosshair;
}
#geoMap {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    width: 100%;
    height: 100%;
}
#geoToast {
    display: none;
    position: absolute;
    bottom: 18px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    background: #023047;
    color: #fff;
    padding: 10px 20px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 500;
    box-shadow: 0 4px 18px rgba(0,0,0,.3);
    white-space: nowrap;
    pointer-events: none;
    animation: geoToastIn .2s ease;
}
@keyframes geoToastIn {
    from { opacity: 0; transform: translateX(-50%) translateY(8px); }
    to   { opacity: 1; transform: translateX(-50%) translateY(0); }
}
#geoModalFooter {
    padding: 12px 20px;
    background: #f8f9fa;
    border-top: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    flex-shrink: 0;
}
#geoCoordsDisplay {
    font-size: 13px;
    color: #495057;
    flex: 1;
    min-width: 180px;
}
#geoCoordsDisplay span {
    font-weight: 600;
    color: #023047;
}
#geoBtnCerrar {
    background: #023047;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 20px;
    font-size: 13px;
    cursor: pointer;
    font-weight: 600;
    transition: background .15s;
}
#geoBtnCerrar:hover { background: #1a5276; }
</style>


<!-- Ficha: grupos, totales, localidades y mini mapa -->
<style>
.ficha-seccion {
    display: flex;
    align-items: center;
    gap: 12px;
    background: linear-gradient(135deg, #023047 0%, #1a5276 100%);
    color: #fff;
    border-left: 6px solid #f0a500;
    border-radius: 8px;
    padding: 10px 16px;
    margin-bottom: 20px;
    box-shadow: 0 3px 10px rgba(2, 48, 71, .18);
}
.ficha-seccion-icono {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .18);
    font-size: 15px;
    flex-shrink: 0;
}
.ficha-seccion-titulo {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}
/* Subsecciones: barra a todo el ancho, separadas por una línea y espacio */
.subseccion {
    display: block;
    position: relative;
    background: #fff3c4;
    border-left: 5px solid #e0a800;
    border-radius: 6px;
    padding: 8px 14px !important;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: #5c4300;
    margin: 44px 0 14px;
}
.subseccion::before {
    content: "";
    position: absolute;
    left: 0; right: 0; top: -23px;
    border-top: 1px dashed #b8cfe8;
}
.subseccion.primera { margin-top: 10px; }

/* Avance de la meta física */
.meta-avance { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 12px 14px; }
.meta-avance-cab { display: flex; justify-content: space-between; align-items: baseline; gap: 10px; flex-wrap: wrap; margin-bottom: 8px; }
.meta-avance-texto { font-size: 14px; color: #495057; }
.meta-avance-texto strong { color: #212529; }
.meta-avance-pct { font-size: 22px; font-weight: 700; color: #023047; line-height: 1; }
.meta-avance-barra { height: 16px; background: #e9ecef; border-radius: 8px; overflow: hidden; }
.meta-avance-relleno { height: 100%; border-radius: 8px; background: #2a81cb; transition: width .3s ease, background-color .3s ease; }
.meta-avance-relleno.bajo     { background: #e8743b; }
.meta-avance-relleno.medio    { background: #2a81cb; }
.meta-avance-relleno.completo { background: #2e9e5b; }
.subseccion.primera::before { display: none; }
.info-card { padding: 20px; border-radius: 8px; height: 100%; }
.info-card-titulo {
    font-size: 11px; color: #6c757d; font-weight: 600;
    text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;
}
.info-card-valor { font-size: 32px; font-weight: 700; color: #023047; margin-bottom: 5px; }
.total-display {
    font-size: 24px;
    font-weight: 700;
    color: #1a5276;
    background: #eaf0fb;
    border: 1px solid #b8cfe8;
    border-radius: 6px;
    padding: 6px 14px;
    letter-spacing: 0.5px;
}
.total-display.small-total { font-size: 18px; }
.localidades-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
    gap: 6px 14px;
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px 14px;
}
.localidad-item {
    display: flex; align-items: center; gap: 6px;
    font-size: 13px; font-weight: 400; margin: 0; cursor: pointer;
    text-transform: none;
}
.localidad-item .form-check-input { margin: 0; }
#miniMapaWrap {
    position: relative;
    height: 280px;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    overflow: hidden;
    background: #f1f3f5;
}
#miniMapa { width: 100%; height: 100%; }

/* Ubicaciones adicionales */
#ubicEditor .form-control { margin-bottom: 0; }
#ubicEditor .ubic-btn-mapa { margin-bottom: 0; }
#ubicEditor { display: none; background: #f4f8fc; border: 1px solid #d6e3f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; }
/* Fuentes de financiamiento por etapa */
#finEditor { display: none; background: #f4f8fc; border: 1px solid #d6e3f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; }
#finTabla .fin-subtotal td { background: #f8f9fa; font-size: 12px; color: #495057; font-style: italic; }
#finTabla tfoot td { font-weight: 700; color: #023047; background: #eaf0fb; border-top: 2px solid #b8cfe8; }
/* Gasto por etapa */
#gasEditor { display: none; background: #f4f8fc; border: 1px solid #d6e3f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; }
#gasTabla .fin-subtotal td { background: #f8f9fa; font-size: 12px; color: #495057; font-style: italic; }
#gasTabla tfoot td { font-weight: 700; color: #023047; background: #eaf0fb; border-top: 2px solid #b8cfe8; }
#ubicMapa { width: 100%; height: 340px; border-radius: 10px; border: 1px solid #dee2e6; margin-top: 6px; }
#ubicMapaVacio { display: none; padding: 18px; text-align: center; color: #888; border: 1px dashed #ccc; border-radius: 10px; margin-top: 6px; }
.ubic-num { display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%;
    background: #e8743b; color: #fff; font-size: 12px; font-weight: 700; }
.ubic-btn { border: 0; background: none; padding: 0; cursor: pointer; }
.ubic-icono { width: 28px; height: 28px; border-radius: 50%; background: #e8743b; color: #fff; border: 2px solid #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,.4); display: flex; align-items: center; justify-content: center; font: 700 12px/1 sans-serif; }
/* Acciones: Gantt semestral (mismo azul que la planilla) */
.gantt-wrap { overflow-x: auto; }
.gantt-tabla { margin-bottom: 4px; }
.gantt-tabla td { vertical-align: middle; }
.gantt-tabla .gantt-ano { border-left: 2px solid #fff; }
.gantt-tabla .gantt-sem { font-size: 11px; width: 38px; min-width: 38px; padding: 4px 2px; }
.gantt-tabla td.gantt-celda { padding: 0 1px; width: 38px; min-width: 38px; border-left: 1px solid #eef1f5; }
.gantt-tabla td.gantt-celda.gantt-s1 { border-left: 2px solid #dee2e6; }
.gantt-barra { height: 16px; background: #4472c4; }
.gantt-barra.ini { border-top-left-radius: 8px; border-bottom-left-radius: 8px; margin-left: 2px; }
.gantt-barra.fin { border-top-right-radius: 8px; border-bottom-right-radius: 8px; margin-right: 2px; }
.accion-verificador { font-size: 12px; color: #6c757d; margin-top: 3px; }

#miniMapaVacio {
    display: none;
    position: absolute; inset: 0;
    align-items: center; justify-content: center; gap: 8px;
    color: #868e96; font-size: 14px; text-align: center; padding: 20px;
}
#miniMapaVacio i { font-size: 22px; color: #adb5bd; }
.mini-mapa-info {
    display: flex; flex-wrap: wrap; justify-content: space-between; gap: 10px;
    font-size: 12px; color: #495057; margin-top: 6px;
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
async function exportarFichaPDF() {
    var btn = document.getElementById('btnPdfFicha');
    var originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

    try {
        var element = document.getElementById('ficha-print');

        var canvas = await html2canvas(element, {
            scale: 2,
            useCORS: true,
            scrollY: 0,
            windowWidth: element.scrollWidth,
            width: element.scrollWidth
        });

        var { jsPDF } = window.jspdf;

        var marginIn  = 0.5;
        var pageW     = 8.5;
        var pageH     = 11;
        var printW    = pageW - marginIn * 2;
        var printH    = pageH - marginIn * 2;

        var pxPerIn   = canvas.width / printW;
        var sliceH_px = printH * pxPerIn;
        var totalPages = Math.ceil(canvas.height / sliceH_px);

        var doc = new jsPDF({ orientation: 'portrait', unit: 'in', format: 'letter' });

        for (var page = 0; page < totalPages; page++) {
            if (page > 0) doc.addPage();

            var srcY      = page * sliceH_px;
            var srcH      = Math.min(sliceH_px, canvas.height - srcY);
            var sliceH_in = srcH / pxPerIn;

            var slice  = document.createElement('canvas');
            slice.width  = canvas.width;
            slice.height = srcH;
            slice.getContext('2d').drawImage(canvas, 0, srcY, canvas.width, srcH, 0, 0, canvas.width, srcH);

            var imgData = slice.toDataURL('image/jpeg', 0.92);
            doc.addImage(imgData, 'JPEG', marginIn, marginIn, printW, sliceH_in);

            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text('Página ' + (page + 1) + ' de ' + totalPages, pageW / 2, pageH - 0.25, { align: 'center' });
        }

        // Nombre del archivo con el nombre de la iniciativa
        var nombre = <?php echo json_encode(function_exists('mb_substr') ? mb_substr($row['nombre_iniciativa'] ?? 'iniciativa', 0, 50) : substr($row['nombre_iniciativa'] ?? 'iniciativa', 0, 50), JSON_INVALID_UTF8_SUBSTITUTE | JSON_HEX_TAG); ?>;
        doc.save('ficha-' + nombre.replace(/[^a-zA-Z0-9]/g, '-') + '.pdf');

    } finally {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }
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

<?php if ($solo_lectura): ?>
<div class="alert alert-warning d-flex align-items-center mb-3" role="alert" style="border-left: 4px solid #f0ad4e;">
    <i class="fa-solid fa-eye me-2"></i>
    <span>Estás viendo esta iniciativa en <strong>modo lectura</strong>. No pertenece a tus instrumentos asignados.</span>
</div>
<script>
$(function () {
    $('#form1 input, #form1 select, #form1 textarea').prop('disabled', true);
    $('#btsend, #btdelete').prop('disabled', true).css('opacity', '0.5');
    $('button[onclick="abrirMapaGeo()"]').prop('disabled', true).css('opacity', '0.5');
});
</script>
<?php endif; ?>

            <div id="ficha-print">
            <div class="row top-info-div">
                <div class="col">
                  <?php if ($es_nuevo) { ?>
                    <h5>CREAR NUEVA INICIATIVA</h5>
                  <?php } else { ?>
                    <h5>EDITAR INICIATIVA</h5>
                  <?php } ?>
                </div>

                <div class="col text-end d-flex justify-content-end gap-2">
                <?php if (!$es_nuevo) { ?>
                <button id="btnPdfFicha" onclick="exportarFichaPDF();" type="button" class="btn btn-secondary"><i class="fas fa-file-pdf"></i>&nbsp;Exportar PDF</button>
                <?php } ?>
                <button onclick="window.location='proyectos.php'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>
                </div>                        
            </div>    

            <div class="container">
            <form id="form1" name="form1">

<?php if (!$es_nuevo) { ?>
<!-- Tarjetas de Información Clave -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="info-card" style="background-color: #e8f4f8; border-left: 4px solid #4a8ecd;">
            <div class="info-card-titulo">% Avance Financiero</div>
            <div class="info-card-valor"><?php echo round($avance_financiero, 1); ?>%</div>
            <div class="progress" style="height: 8px; border-radius: 10px;">
                <div class="progress-bar" style="width: <?php echo min($avance_financiero, 100); ?>%; background-color: #4a8ecd;" role="progressbar"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card" style="background-color: #e8f5e9; border-left: 4px solid #4a8f5e;">
            <div class="info-card-titulo">% Avance Actividades</div>
            <div class="info-card-valor"><?php echo round($avance_actividades, 1); ?>%</div>
            <div class="progress" style="height: 8px; border-radius: 10px;">
                <div class="progress-bar" style="width: <?php echo min($avance_actividades, 100); ?>%; background-color: #4a8f5e;" role="progressbar"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card" style="background-color: #fff3e0; border-left: 4px solid #d17a4f;">
            <div class="info-card-titulo">Proceso Actual</div>
            <div style="font-size: 18px; font-weight: 600; color: #023047; margin-top: 10px;">
                <?php echo h($row['actividad_actual'] ?: 'Sin proceso asignado'); ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
<div class="col"></div>  
<div class="col" style="text-align:right">
  <i class="fa-solid fa-list-check"></i>&nbsp;<a href="#acciones">Acciones de esta iniciativa</a>
  &nbsp;&nbsp;<i class="fa-solid fa-map-location-dot"></i>&nbsp;<a href="#ubicacion">Ubicación</a>
  &nbsp;&nbsp;<i class="fa-solid fa-map-pin"></i>&nbsp;<a href="#ubicaciones">Ubicaciones adicionales</a>
  &nbsp;&nbsp;<i class="fa-solid fa-sack-dollar"></i>&nbsp;<a href="#financiamiento">Financiamiento</a>
  &nbsp;&nbsp;<i class="fa-solid fa-money-bill-wave"></i>&nbsp;<a href="#gastos_etapa">Gasto por etapa</a>
</div>  
</div>
<?php } ?>


<?php
// ============================================================
// INICIATIVA
// ============================================================
seccion('fas fa-lightbulb', 'INICIATIVA');
?>
<div class="row">
  <div class="col-md-2" style="display:none">
    <div class="form-group">
      <label for="id_iniciativa">Id</label>
      <input type="text" class="form-control" id="id_iniciativa" value="<?php echo $es_nuevo ? '-' : (int) $row['id']; ?>" disabled readonly style="background-color:#f8f9fa; color:#6c757d; font-weight:600;">
      <small class="text-muted">Generado por el sistema.</small>
    </div>
  </div>
  <?php campo($row, 'numero', 'N°', 'Correlativo en la base consolidada. Si se deja vacío al crear, se asigna el siguiente.', ['type' => 'number', 'col' => 'col-md-2', 'attrs' => 'min="1" step="1"']); ?>
  <?php campo($row, 'codigo', 'Código', 'Código de la iniciativa. Ej: P001.', ['col' => 'col-md-2', 'maxlength' => 100, 'placeholder' => 'Ej: P001']); ?>
  <?php campo($row, 'codigo_idi', 'IDI / BIP', 'Código de ficha IDI en el Banco Integrado de Proyectos.', ['col' => 'col-md-4', 'maxlength' => 20, 'placeholder' => 'Ej: 40066700-0']); ?>
  <div class="col-md-4">
    <div class="form-group">
      <label for="seleccionada_pm_sjm">Preseleccionada Plan Maestro SJM</label>
      <select class="form-select" name="seleccionada_pm_sjm" id="seleccionada_pm_sjm">
        <option value="">Seleccione...</option>
        <option value="Si" <?php if (($row['seleccionada_pm_sjm'] ?? '') === 'Si') echo 'selected'; ?>>Sí</option>
        <option value="No" <?php if (($row['seleccionada_pm_sjm'] ?? '') === 'No') echo 'selected'; ?>>No</option>
      </select>
      <small class="text-muted">Indica si forma parte de la cartera preseleccionada.</small>
    </div>
  </div>
</div>

<div class="row">
  <?php campo($row, 'nombre_iniciativa', 'Nombre de la iniciativa', 'Nombre completo y descriptivo de la iniciativa.', ['col' => 'col-md-8', 'maxlength' => 255]); ?>
  <?php campo($row, 'tipo_de_inversion', 'Tipo de brecha', 'Agrupación temática. Ej: Servicios Sanitarios, Acceso a la Montaña.', ['col' => 'col-md-4', 'maxlength' => 100, 'datalist' => 'tipo_de_inversion']); ?>
</div>


<?php
// ============================================================
// DESCRIPCIÓN
// ============================================================
seccion('fas fa-align-left', 'DESCRIPCIÓN');
?>
<div class="row">
  <div class="col-md-8">
    <div class="form-group">
      <label for="n_inst">Instrumento de origen</label>
      <select class="form-select select-con-texto" name="n_inst" id="n_inst" data-texto="instrumento_de_origen">
        <option value="">Seleccione...</option>
        <?php
        $encontrado = false;
        foreach ($instrumentos as $ins) {
            $sel = ((string) $ins['instrumentos_id'] === (string) ($row['n_inst'] ?? ''));
            if ($sel) $encontrado = true;
            echo '<option value="' . (int) $ins['instrumentos_id'] . '"' . ($sel ? ' selected' : '') . '>' . h($ins['instrumentos_descripcion']) . '</option>';
        }
        if (!$encontrado && !empty($row['n_inst'])) {
            echo '<option value="' . (int) $row['n_inst'] . '" selected>' . h($row['instrumento_de_origen'] ?: 'Instrumento ' . $row['n_inst']) . '</option>';
        }
        ?>
      </select>
      <input type="hidden" name="instrumento_de_origen" id="instrumento_de_origen" value="<?php echo h($row['instrumento_de_origen'] ?? ''); ?>">
      <?php if ($perfil_usuario === 'ÁREA'): ?>
      <small class="text-muted"><i class="fas fa-lock" style="color:#d17a4f"></i> Solo se muestran los instrumentos asignados a su perfil.</small>
      <?php else: ?>
      <small class="text-muted">Plan o cartera de la que proviene la iniciativa.</small>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-md-4">
    <div class="form-group">
      <label for="cod_tipo">Tipología de iniciativa</label>
      <select class="form-select select-con-texto" name="cod_tipo" id="cod_tipo" data-texto="tipo">
        <option value="">Seleccione...</option>
        <?php opciones($CAT_TIPOS, $row['cod_tipo'] ?? '', $row['tipo'] ?? ''); ?>
      </select>
      <input type="hidden" name="tipo" id="tipo" value="<?php echo h($row['tipo'] ?? ''); ?>">
      <small class="text-muted">Estudio, Programa, Proyecto o Gestión.</small>
    </div>
  </div>
</div>

<div class="row">
  <?php campo_area($row, 'descripcion', 'Descripción', 'Objetivos, alcance y características principales de la iniciativa.', 5); ?>
</div>

<div class="row">
  <?php campo($row, 'eje_estrategico', 'Eje estratégico', 'Eje estratégico del Plan Maestro al que se vincula.', ['maxlength' => 255, 'datalist' => 'eje_estrategico']); ?>
  <?php campo($row, 'objetivo_estrategico', 'Objetivo estratégico', 'Objetivo estratégico que persigue la iniciativa.', ['maxlength' => 255, 'datalist' => 'objetivo_estrategico']); ?>
</div>

<div class="row">
  <?php campo($row, 'utr', 'Unidad Técnica Responsable (UTR)', 'Institución o unidad a cargo técnicamente de la iniciativa.', ['col' => 'col-12', 'maxlength' => 255, 'datalist' => 'utr']); ?>
</div>

<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="cod_sector">Sector SNI</label>
      <select class="form-select select-con-texto" name="cod_sector" id="cod_sector" data-texto="sector">
        <option value="">Seleccione...</option>
        <?php opciones($CAT_SECTORES, $row['cod_sector'] ?? '', $row['sector'] ?? ''); ?>
      </select>
      <input type="hidden" name="sector" id="sector" value="<?php echo h($row['sector'] ?? ''); ?>">
      <small class="text-muted">Clasificación del Sistema Nacional de Inversiones.</small>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label for="cod_subsector">Subsector SNI</label>
      <select class="form-select select-con-texto" name="cod_subsector" id="cod_subsector" data-texto="subsector_de_inversion_sni">
        <option value="">Seleccione...</option>
        <?php opciones($CAT_SUBSECTORES, $row['cod_subsector'] ?? '', $row['subsector_de_inversion_sni'] ?? ''); ?>
      </select>
      <input type="hidden" name="subsector_de_inversion_sni" id="subsector_de_inversion_sni" value="<?php echo h($row['subsector_de_inversion_sni'] ?? ''); ?>">
      <small class="text-muted">Subsector de inversión del SNI.</small>
    </div>
  </div>
</div>


<?php
// ============================================================
// BRECHAS
// ============================================================
seccion('fas fa-bullseye', 'BRECHAS');
?>
<div class="row">
  <?php campo($row, 'brecha_principal', 'Brecha principal', 'B1 a B4 según el Plan Maestro.', ['maxlength' => 255, 'datalist' => 'brecha_principal']); ?>
  <?php campo($row, 'atributo_al_que_responde', 'Atributo al que responde', 'Atributo del territorio que la iniciativa pone en valor o atiende.', ['maxlength' => 100, 'datalist' => 'atributo_al_que_responde']); ?>
</div>
<div class="row">
  <?php campo($row, 'brecha_especifica', 'Brecha específica', 'Brecha concreta que la iniciativa busca cerrar.', ['col' => 'col-12', 'maxlength' => 255]); ?>
</div>


<?php
// ============================================================
// EMPLAZAMIENTO
// ============================================================
seccion('fas fa-map-marker-alt', 'EMPLAZAMIENTO');
?>
<div class="row">
  <?php campo($row, 'localizacion', 'Alcance', 'Local, Varias localidades o Comunal.', ['col' => 'col-md-4', 'maxlength' => 50, 'datalist' => 'alcance']); ?>
  <?php campo($row, 'localizacion_2', 'Localización', 'Descripción textual del lugar o sector.', ['col' => 'col-md-8', 'maxlength' => 255]); ?>
</div>

<div class="row">
  <div class="col-12">
    <div class="form-group">
      <div class="subseccion">Localidades cubiertas</div>
      <div class="localidades-grid">
        <?php foreach ($CAT_LOCALIDADES as $col => $label): ?>
        <label class="localidad-item">
          <input type="hidden" name="<?php echo $col; ?>" value="0">
          <input type="checkbox" class="form-check-input chk-localidad" name="<?php echo $col; ?>" value="1" <?php if (!empty($row[$col])) echo 'checked'; ?>>
          <span><?php echo h($label); ?></span>
        </label>
        <?php endforeach; ?>
      </div>
      <small class="text-muted">Marque las localidades donde la iniciativa tiene cobertura.</small>
    </div>
  </div>
</div>

<div class="row align-items-end">
  <?php campo($row, 'point_x', 'Latitud', 'Ej: -33.642985', ['col' => 'col-md-5', 'class' => 'coord', 'value' => $lat_actual]); ?>
  <?php campo($row, 'point_y', 'Longitud', 'Ej: -70.356215', ['col' => 'col-md-5', 'class' => 'coord', 'value' => $lng_actual]); ?>
  <div class="col-md-2">
    <div class="form-group">
      <button type="button" onclick="abrirMapaGeo()" class="btn btn-primary w-100"
              style="margin-bottom:22px; background:linear-gradient(135deg,#023047,#1a5276); border:none; border-radius:8px; padding:9px 8px; font-size:13px; font-weight:600;">
        <i class="fa-solid fa-map-location-dot"></i>&nbsp;Seleccionar en mapa
      </button>
    </div>
  </div>
</div>


<!-- Ubicación actual (mini mapa) -->
<a name="ubicacion" id="ubicacion"></a>
<div class="row">
  <div class="col-12">
    <div class="form-group">
      <div class="subseccion">Ubicación actual</div>
      <div id="miniMapaWrap">
        <div id="miniMapa"></div>
        <div id="miniMapaVacio">
          <i class="fa-solid fa-location-dot"></i>
          <span>Sin ubicación registrada. Use "Seleccionar en mapa".</span>
        </div>
      </div>
      <div class="mini-mapa-info">
        <span id="miniMapaCoords"></span>
        <span id="miniMapaLocalidades"><?php if ($localidades_txt) echo '<i class="fa-solid fa-map-pin"></i>&nbsp;' . h(implode(', ', $localidades_txt)); ?></span>
      </div>
    </div>
  </div>
</div>

<!-- ========== UBICACIONES ADICIONALES ========== -->
<a name="ubicaciones" id="ubicaciones"></a>
<div class="row">
  <div class="col-12">
    <div class="form-group">
      <div class="subseccion">Ubicaciones adicionales</div>
<?php if ($es_nuevo) { ?>
      <p class="text-muted mb-0" style="font-size:.9rem;">
        <i class="fa-solid fa-circle-info"></i>&nbsp;Podrá agregar ubicaciones adicionales después de guardar la iniciativa.
      </p>
<?php } else {
    $puede_editar_ubic = in_array($row_tabla["usuarios_profile"], ['ADMINISTRADOR', 'ÁREA'], true) && !$solo_lectura;
?>
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <small class="text-muted" data-html2canvas-ignore="true">
          Otros puntos de la iniciativa, además de la ubicación principal. Se guardan de inmediato, sin presionar "Guardar cambios".
        </small>
        <?php if ($puede_editar_ubic): ?>
        <button type="button" id="btnUbicNueva" class="btn btn-primary btn-sm" data-html2canvas-ignore="true"><i class="fa-solid fa-plus"></i>&nbsp;Nueva ubicación</button>
        <?php endif; ?>
      </div>
<?php if ($puede_editar_ubic): ?>
<div id="ubicEditor" data-html2canvas-ignore="true">
  <input type="hidden" id="ub_id" value="0">
  <div class="row align-items-end g-2">
    <div class="col-md-5">
      <label for="ub_desc">Descripción</label>
      <input type="text" id="ub_desc" class="form-control" maxlength="100" placeholder="Ej: Sede vecinal sector El Canelo">
    </div>
    <div class="col-md-2">
      <label for="ub_lat">Latitud</label>
      <input type="text" id="ub_lat" class="form-control" placeholder="-33.642985">
    </div>
    <div class="col-md-2">
      <label for="ub_lng">Longitud</label>
      <input type="text" id="ub_lng" class="form-control" placeholder="-70.356215">
    </div>
    <div class="col-md-3">
      <button type="button" class="btn btn-outline-primary w-100 ubic-btn-mapa" onclick="abrirMapaGeo('ubic')">
        <i class="fa-solid fa-map-location-dot"></i>&nbsp;Seleccionar en mapa
      </button>
    </div>
  </div>
  <div class="mt-3 text-end">
    <button type="button" id="btnUbicCancelar" class="btn btn-outline-secondary">Cancelar</button>
    <button type="button" id="btnUbicGuardar" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar ubicación</button>
  </div>
</div>
<?php endif; ?>

<table class="table table-hover">
  <thead>
    <tr class="table-primary">
      <th class="labels" scope="col" style="width:6%">N°</th>
      <th class="labels" scope="col">DESCRIPCIÓN</th>
      <th class="labels" scope="col" style="width:16%">LATITUD</th>
      <th class="labels" scope="col" style="width:16%">LONGITUD</th>
      <?php if ($puede_editar_ubic): ?>
      <th class="labels text-center" scope="col" style="width:14%" data-html2canvas-ignore="true">ACCIONES</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody id="ubicBody">
    <tr><td colspan="5" class="text-muted">Cargando...</td></tr>
  </tbody>
</table>

<div id="ubicMapa"></div>
<div id="ubicMapaVacio"><i class="fa-solid fa-location-dot"></i>&nbsp;Sin ubicaciones para mostrar en el mapa</div>
<small class="text-muted d-block mt-1">
  <i class="fa-solid fa-location-dot" style="color:#2a81cb"></i>&nbsp;Ubicación principal
  &nbsp;&nbsp;<span class="ubic-num" style="width:14px;height:14px;font-size:9px;">1</span>&nbsp;Ubicaciones adicionales
</small>
<?php } ?>
    </div>
  </div>
</div>
<!-- ========== /UBICACIONES ADICIONALES ========== -->


<?php
// ============================================================
// FINANCIAMIENTO
// ============================================================
seccion('fas fa-dollar-sign', 'FINANCIAMIENTO');
?>
<div class="subseccion primera">Meta física</div>
<div class="row">
  <?php campo($row, 'indicador_2', 'Indicador', 'Indicador con que se mide el producto de la iniciativa.', ['maxlength' => 255, 'datalist' => 'indicador_2']); ?>
  <?php campo($row, 'unidad_de_medida', 'Unidad', 'Ej: N° soluciones, m2, km.', ['col' => 'col-md-2', 'maxlength' => 50, 'datalist' => 'unidad_de_medida']); ?>
  <?php campo($row, 'cantidad_proyectada', 'Cantidad proyectada', '', ['col' => 'col-md-2', 'class' => 'monto', 'value' => fnum($row['cantidad_proyectada'] ?? null)]); ?>
  <?php campo($row, 'cantidad_realizada', 'Cantidad realizada', '', ['col' => 'col-md-2', 'class' => 'monto', 'value' => fnum($row['cantidad_realizada'] ?? null)]); ?>
</div>
<div class="row">
  <div class="col-12">
    <div class="form-group">
      <label>Avance de la meta física</label>
      <div id="metaAvance" class="meta-avance">
        <div class="meta-avance-cab">
          <span id="metaAvanceTexto" class="meta-avance-texto">-</span>
          <span id="metaAvancePct" class="meta-avance-pct">-</span>
        </div>
        <div class="meta-avance-barra"><div id="metaAvanceRelleno" class="meta-avance-relleno" style="width:0%"></div></div>
      </div>
      <small class="text-muted">Cantidad realizada sobre cantidad proyectada. Se actualiza al modificar las cantidades.</small>
    </div>
  </div>
</div>

<div class="subseccion">COSTO POR ETAPA (M$)</div>
<div class="row">
  <?php campo($row, 'costos_perfil_prefactibilidad', 'M$ Prefactibilidad', '', ['col' => 'col-md-3', 'class' => 'monto costo', 'value' => fnum($row['costos_perfil_prefactibilidad'] ?? null)]); ?>
  <?php campo($row, 'costos_diseno', 'M$ Diseño', '', ['col' => 'col-md-3', 'class' => 'monto costo', 'value' => fnum($row['costos_diseno'] ?? null)]); ?>
  <?php campo($row, 'costos_ejecucion', 'M$ Ejecución', '', ['col' => 'col-md-3', 'class' => 'monto costo', 'value' => fnum($row['costos_ejecucion'] ?? null)]); ?>
  <?php campo($row, 'suma_costo_total', 'M$ TOTAL', 'Suma de Prefactibilidad, Diseño y Ejecución.', ['col' => 'col-md-3', 'class' => 'monto', 'value' => fnum($row['suma_costo_total'] ?? null), 'attrs' => 'readonly tabindex="-1" style="background-color:#f8f9fa; font-weight:600;"']); ?>
</div>
<div class="row">
  <?php campo($row, 'costo_unitario_ejecucion', 'Valor unidad ejecución M$', 'Costo de ejecución por unidad.', ['col' => 'col-md-4', 'class' => 'monto', 'value' => fnum($row['costo_unitario_ejecucion'] ?? null)]); ?>
  <?php campo($row, 'origen_del_calculo_de_costos', 'Origen del cálculo de costos', 'Ej: estimación paramétrica, ficha IDI, presupuesto oficial.', ['col' => 'col-md-8', 'maxlength' => 255, 'datalist' => 'origen_del_calculo_de_costos']); ?>
</div>

<!-- ========== FUENTES DE FINANCIAMIENTO POR ETAPA (tabla financiamiento) ========== -->
<a name="financiamiento" id="financiamiento"></a>
<div class="subseccion">Fuente de financiamiento por etapa (M$)</div>
<div class="row">
  <div class="col-12">
    <div class="form-group">
<?php if ($es_nuevo) { ?>
      <p class="text-muted mb-0" style="font-size:.9rem;">
        <i class="fa-solid fa-circle-info"></i>&nbsp;Podrá agregar fuentes de financiamiento después de guardar la iniciativa.
      </p>
<?php } else {
    $puede_editar_fin = in_array($row_tabla["usuarios_profile"], ['ADMINISTRADOR', 'ÁREA'], true) && !$solo_lectura;
?>
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <small class="text-muted" data-html2canvas-ignore="true">
          Una etapa puede tener varias fuentes. Se guardan de inmediato, sin presionar "Guardar cambios".
        </small>
        <?php if ($puede_editar_fin): ?>
        <button type="button" id="btnFinNueva" class="btn btn-primary btn-sm" data-html2canvas-ignore="true"><i class="fa-solid fa-plus"></i>&nbsp;Nueva fuente</button>
        <?php endif; ?>
      </div>
<?php if ($puede_editar_fin): ?>
<div id="finEditor" data-html2canvas-ignore="true">
  <input type="hidden" id="fin_id" value="0">
  <div class="row align-items-end g-2">
    <div class="col-md-4">
      <label for="fin_etapa">Etapa</label>
      <select id="fin_etapa" class="form-select">
        <option value="">Seleccione...</option>
        <?php foreach ($CAT_ETAPAS as $cod => $txt): ?>
        <option value="<?php echo h($cod); ?>"><?php echo h($txt); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="fin_monto">Monto M$</label>
      <input type="text" id="fin_monto" class="form-control text-end" placeholder="Ej: 1.500">
    </div>
    <div class="col-md-5">
      <label for="fin_fuente">Fuente</label>
      <input type="text" id="fin_fuente" class="form-control" maxlength="255" list="dl_fuentes" autocomplete="off" placeholder="Ej: FNDR, MOP, OTRO">
    </div>
  </div>
  <div class="mt-3 text-end">
    <button type="button" id="btnFinCancelar" class="btn btn-outline-secondary">Cancelar</button>
    <button type="button" id="btnFinGuardar" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar fuente</button>
  </div>
</div>
<?php endif; ?>

<table class="table table-hover" id="finTabla">
  <thead>
    <tr class="table-primary">
      <th class="labels" scope="col" style="width:30%">ETAPA</th>
      <th class="labels text-end" scope="col" style="width:20%">MONTO M$</th>
      <th class="labels" scope="col">FUENTE</th>
      <?php if ($puede_editar_fin): ?>
      <th class="labels text-center" scope="col" style="width:14%" data-html2canvas-ignore="true">ACCIONES</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody id="finBody">
    <tr><td colspan="4" class="text-muted">Cargando...</td></tr>
  </tbody>
  <tfoot id="finFoot"></tfoot>
</table>
<?php } ?>
    </div>
  </div>
</div>
<!-- ========== /FUENTES DE FINANCIAMIENTO POR ETAPA ========== -->


<!--
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label>M$ Prefactibilidad + Diseño</label>
      <div id="total_pref_diseno" class="total-display small-total"><?php echo fnum($costo_pref + $costo_dis); ?></div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label>M$ Total</label>
      <div id="total_costo" class="total-display"><?php echo fnum($total_monto); ?></div>
      <small class="text-muted">Suma de Prefactibilidad, Diseño y Ejecución. Se actualiza al modificar los montos.</small>
    </div>
  </div>
</div>
-->

<!--
<div class="subseccion">Fuente de financiamiento</div>
<div class="row">
  <?php campo($row, 'fuente_financiamiento_prefactibilidad', 'Prefactibilidad', '', ['col' => 'col-md-4', 'maxlength' => 100, 'datalist' => 'fuentes']); ?>
  <?php campo($row, 'fuente_financiamiento_diseno', 'Diseño', '', ['col' => 'col-md-4', 'maxlength' => 100, 'datalist' => 'fuentes']); ?>
  <?php campo($row, 'fuente_financiamiento_ejecucion', 'Ejecución', '', ['col' => 'col-md-4', 'maxlength' => 100, 'datalist' => 'fuentes']); ?>
</div>
-->

<div class="subseccion">GASTO EFECTIVO POR ETAPA (M$)</div>
<div class="row">
  <?php campo($row, 'gasto_perfil_prefactibilidad', 'Gasto Prefactibilidad', '', ['col' => 'col-md-3', 'class' => 'monto gasto', 'value' => fnum($row['gasto_perfil_prefactibilidad'] ?? null)]); ?>
  <?php campo($row, 'gasto_diseno', 'Gasto Diseño', '', ['col' => 'col-md-3', 'class' => 'monto gasto', 'value' => fnum($row['gasto_diseno'] ?? null)]); ?>
  <?php campo($row, 'gasto_ejecucion', 'Gasto Ejecución', '', ['col' => 'col-md-3', 'class' => 'monto gasto', 'value' => fnum($row['gasto_ejecucion'] ?? null)]); ?>
  <?php campo($row, 'gasto_total', 'Gasto TOTAL', 'Suma de Prefactibilidad, Diseño y Ejecución.', ['col' => 'col-md-3', 'class' => 'monto', 'value' => fnum($row['gasto_total'] ?? null), 'attrs' => 'readonly tabindex="-1" style="background-color:#f8f9fa; font-weight:600;"']); ?>
</div>

<!-- ========== GASTO POR ETAPA (tabla gasto) ========== -->
<a name="gastos_etapa" id="gastos_etapa"></a>
<div class="subseccion">FUENTE DE FINANCIAMIENTO DEL GASTO POR ETAPA (M$)</div>
<div class="row">
  <div class="col-12">
    <div class="form-group">
<?php if ($es_nuevo) { ?>
      <p class="text-muted mb-0" style="font-size:.9rem;">
        <i class="fa-solid fa-circle-info"></i>&nbsp;Podrá agregar gastos por etapa después de guardar la iniciativa.
      </p>
<?php } else { ?>
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
        <small class="text-muted" data-html2canvas-ignore="true">
          Una etapa puede tener varios gastos. Se guardan de inmediato, sin presionar "Guardar cambios".
        </small>
        <?php if ($puede_editar_fin): ?>
        <button type="button" id="btnGasNueva" class="btn btn-primary btn-sm" data-html2canvas-ignore="true"><i class="fa-solid fa-plus"></i>&nbsp;Nuevo gasto</button>
        <?php endif; ?>
      </div>
<?php if ($puede_editar_fin): ?>
<div id="gasEditor" data-html2canvas-ignore="true">
  <input type="hidden" id="gas_id" value="0">
  <div class="row align-items-end g-2">
    <div class="col-md-4">
      <label for="gas_etapa">Etapa</label>
      <select id="gas_etapa" class="form-select">
        <option value="">Seleccione...</option>
        <?php foreach ($CAT_ETAPAS as $cod => $txt): ?>
        <option value="<?php echo h($cod); ?>"><?php echo h($txt); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="gas_monto">Monto M$</label>
      <input type="text" id="gas_monto" class="form-control text-end" placeholder="Ej: 1.500">
    </div>
    <div class="col-md-5">
      <label for="gas_fuente">Fuente</label>
      <input type="text" id="gas_fuente" class="form-control" maxlength="255" list="dl_fuentes" autocomplete="off" placeholder="Ej: FNDR, MOP, OTRO">
    </div>
  </div>
  <div class="mt-3 text-end">
    <button type="button" id="btnGasCancelar" class="btn btn-outline-secondary">Cancelar</button>
    <button type="button" id="btnGasGuardar" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar gasto</button>
  </div>
</div>
<?php endif; ?>

<table class="table table-hover" id="gasTabla">
  <thead>
    <tr class="table-primary">
      <th class="labels" scope="col" style="width:30%">ETAPA</th>
      <th class="labels text-end" scope="col" style="width:20%">MONTO M$</th>
      <th class="labels" scope="col">FUENTE</th>
      <?php if ($puede_editar_fin): ?>
      <th class="labels text-center" scope="col" style="width:14%" data-html2canvas-ignore="true">ACCIONES</th>
      <?php endif; ?>
    </tr>
  </thead>
  <tbody id="gasBody">
    <tr><td colspan="4" class="text-muted">Cargando...</td></tr>
  </tbody>
  <tfoot id="gasFoot"></tfoot>
</table>
<?php } ?>
    </div>
  </div>
</div>
<!-- ========== /GASTO POR ETAPA ========== -->



<?php
// ============================================================
// MANTENCIÓN
// ============================================================
seccion('fas fa-tools', 'MANTENCIÓN');
?>
<div class="row">
  <?php campo($row, 'responsable_del_mantenimiento', 'Responsable', 'Institución a cargo de operar y mantener la infraestructura.', ['maxlength' => 255, 'datalist' => 'responsable_del_mantenimiento']); ?>
  <?php campo($row, 'financiamiento_del_mantenimiento', 'Financiamiento', 'Origen de los recursos para la mantención.', ['maxlength' => 255, 'datalist' => 'financiamiento_del_mantenimiento']); ?>
</div>


<?php
// ============================================================
// CRONOGRAMA (AÑOS)
// ============================================================
seccion('fas fa-calendar-alt', 'CRONOGRAMA (AÑOS)');
?>
<div class="row">
  <?php campo($row, 'inicio', 'Inicio', 'Año de inicio.', ['type' => 'number', 'col' => 'col-md-3', 'placeholder' => 'Ej: 2027', 'attrs' => 'min="2000" max="2100"']); ?>
  <?php campo($row, 'termino', 'Término', 'Año de término.', ['type' => 'number', 'col' => 'col-md-3', 'placeholder' => 'Ej: 2035', 'attrs' => 'min="2000" max="2100"']); ?>
  <?php campo($row, 'plazos_ejecucion_anos', 'Plazo de ejecución (años)', 'Si se deja vacío, se sugiere Término menos Inicio.', ['type' => 'number', 'col' => 'col-md-3', 'attrs' => 'min="0" max="100"']); ?>
  <div class="col-md-3">
    <div class="form-group">
      <label>Total (años)</label>
      <div id="total_anos" class="total-display small-total">
        <?php echo (!empty($row['inicio']) && !empty($row['termino'])) ? ((int) $row['termino'] - (int) $row['inicio']) : '-'; ?>
      </div>
    </div>
  </div>
</div>


<?php
// ============================================================
// ESTADO
// ============================================================
seccion('fas fa-flag', 'ESTADO');
?>
<div class="row">
  <div class="col-md-6">
    <div class="form-group">
      <label for="cod_etapa_actual">Etapa actual</label>
      <select class="form-select select-con-texto" name="cod_etapa_actual" id="cod_etapa_actual" data-texto="etapa_actual">
        <option value="">Seleccione...</option>
        <?php opciones($CAT_ETAPAS, $row['cod_etapa_actual'] ?? '', $row['etapa_actual'] ?? ''); ?>
      </select>
      <input type="hidden" name="etapa_actual" id="etapa_actual" value="<?php echo h($row['etapa_actual'] ?? ''); ?>">
      <small class="text-muted">Etapa en desarrollo o concluida.</small>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-group">
      <label for="cod_actividad_actual">Actividad actual (proceso)</label>
      <select class="form-select select-con-texto" name="cod_actividad_actual" id="cod_actividad_actual" data-texto="actividad_actual">
        <option value="">Seleccione...</option>
        <?php opciones($CAT_PROCESOS, $row['cod_actividad_actual'] ?? '', $row['actividad_actual'] ?? ''); ?>
      </select>
      <input type="hidden" name="actividad_actual" id="actividad_actual" value="<?php echo h($row['actividad_actual'] ?? ''); ?>">
      <small class="text-muted">Proceso administrativo o técnico en curso.</small>
    </div>
  </div>
</div>


<?php
// Datalists de sugerencias (valores ya usados en la tabla)
datalist($db, 'tipo_de_inversion',                ['tipo_de_inversion'], ['Servicios Sanitarios', 'Acceso a la Montaña']);
datalist($db, 'eje_estrategico',                  ['eje_estrategico']);
datalist($db, 'objetivo_estrategico',             ['objetivo_estrategico']);
datalist($db, 'utr',                              ['utr']);
datalist($db, 'brecha_principal',                 ['brecha_principal'], $CAT_BRECHAS);
datalist($db, 'atributo_al_que_responde',         ['atributo_al_que_responde']);
datalist($db, 'alcance',                          ['localizacion'], $CAT_ALCANCE);
datalist($db, 'indicador_2',                      ['indicador_2']);
datalist($db, 'unidad_de_medida',                 ['unidad_de_medida']);
datalist($db, 'origen_del_calculo_de_costos',     ['origen_del_calculo_de_costos']);
// Fuentes: valores fijos + los ya usados en la tabla financiamiento
$fuentes_extra = ['FNDR', 'MOP', 'OTRO'];
$r_fu = $db->query("SELECT fuente FROM financiamiento WHERE fuente IS NOT NULL AND fuente <> ''
                    UNION SELECT fuente FROM gasto WHERE fuente IS NOT NULL AND fuente <> ''");
if ($r_fu) while ($x = $r_fu->fetch_assoc()) $fuentes_extra[] = $x['fuente'];
datalist($db, 'fuentes',                          ['fuente_financiamiento_prefactibilidad', 'fuente_financiamiento_diseno', 'fuente_financiamiento_ejecucion'], $fuentes_extra);
datalist($db, 'responsable_del_mantenimiento',    ['responsable_del_mantenimiento']);
datalist($db, 'financiamiento_del_mantenimiento', ['financiamiento_del_mantenimiento']);
?>


<input name="proyectos_id" type="hidden" id="proyectos_id" value="<?php echo $es_nuevo ? 0 : (int) $row['id']; ?>" />
<input name="posted" type="hidden" id="posted" value="1" />
<input name="task" type="hidden" id="task" value="<?php echo $es_nuevo ? 'insert' : 'update'; ?>" />
<input name="token" type="hidden" id="token" value="<?php echo $sid; ?>" />


<?php if ($row_tabla["usuarios_profile"] == 'ADMINISTRADOR' || $row_tabla["usuarios_profile"] == 'ÁREA') { ?>
<div class="row top-info-div" data-html2canvas-ignore="true">
  <div class="col" style="text-align:center">
    <div style="height:50px; clear:both"></div>
    <?php if (!$es_nuevo) { ?>
    <button id="btdelete" type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar iniciativa</button>
    <?php } ?>
  </div>
  <div class="col" style="text-align:center">
    <div style="height:50px; clear:both"></div>
    <button id="btsend" onclick="guardar();" type="button" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar cambios</button>
  </div>
</div>
<?php } ?>

</form>


<form id="frm1" name="frm1">
<input type="hidden" name="task" value="delete">    
<input type="hidden" name="proyectos_id" value="<?php echo $es_nuevo ? 0 : (int) $row['id']; ?>">
<input name="token" type="hidden" value="<?php echo $sid; ?>" />
</form>


<div style="height:60px"></div>  

<?php if (!$es_nuevo) { ?>


<!-- Bloque de auditoría -->
<div style="border-top: 1px solid #dee2e6; padding-top: 14px; margin-bottom: 20px;">
  <small class="text-muted d-flex flex-wrap gap-4">
    <?php if (!empty($row['proyectos_created'])): ?>
    <span>
      <i class="fa-regular fa-calendar-plus" style="color:#4a8ecd"></i>&nbsp;
      <strong>Creado:</strong>
      <?php echo date('d/m/Y H:i', strtotime($row['proyectos_created'])); ?>
    </span>
    <?php endif; ?>
    <?php if (!empty($row['proyectos_updated'])): ?>
    <span>
      <i class="fa-regular fa-calendar-check" style="color:#4a8f5e"></i>&nbsp;
      <strong>Última actualización:</strong>
      <?php echo date('d/m/Y H:i', strtotime($row['proyectos_updated'])); ?>
    </span>
    <?php endif; ?>
    <?php if (!empty($row['proyectos_user'])): ?>
    <span>
      <i class="fa-regular fa-user" style="color:#d17a4f"></i>&nbsp;
      <strong>Modificado por:</strong>
      <?php echo h($row['proyectos_user']); ?>
    </span>
    <?php endif; ?>
  </small>
</div>
<!-- /Bloque de auditoría -->

<?php
// Mismo criterio que ubicaciones y financiamiento (la API valida igual)
$puede_editar_acc = in_array($row_tabla["usuarios_profile"], ['ADMINISTRADOR', 'ÁREA'], true) && !$solo_lectura;
?>
<div class="alert alert-warning" role="alert" data-html2canvas-ignore="true">
Muestra las acciones de la ruta crítica de esta iniciativa, ordenadas por fecha de inicio. Permite editar una acción haciendo clic en la primera columna de cada fila y crear nuevas acciones mediante el botón en la parte superior derecha.
</div>

<div class="row top-info-div">
  <div class="col">
    <a name="acciones" id="acciones"></a><h5>ACCIONES DE LA INICIATIVA</h5>
  </div>
  <div class="col" style="text-align:right">
    <?php if ($puede_editar_acc && $codigo_acciones !== ''): ?>
    <a href="acciones_editar.php?proyectos_id=<?php echo (int) $row['id']; ?>&acciones_id=0" class="btn btn-primary text-white" data-html2canvas-ignore="true">
      <i class="fa-solid fa-plus"></i>&nbsp;Nueva acción
    </a>
    <?php endif; ?>
  </div>
</div>

<?php if ($codigo_acciones === ''): ?>
<p class="text-muted"><i class="fa-solid fa-circle-info"></i>&nbsp;Asigne un código a la iniciativa (ej: P001) y guarde los cambios para poder registrar acciones.</p>
<?php elseif (!$acciones): ?>
<p class="text-muted"><i class="fa-solid fa-circle-info"></i>&nbsp;Esta iniciativa no tiene acciones registradas.</p>
<?php else: ?>
<div class="row">
<div class="col">
<div class="gantt-wrap">
<table class="table table-hover gantt-tabla">
  <thead>
    <tr class="table-primary">
      <th class="labels" scope="col" rowspan="2" style="width:60px">EDITAR</th>
      <th class="labels" scope="col" rowspan="2" style="min-width:280px">ACCIÓN</th>
      <th class="labels text-center" scope="col" rowspan="2" style="width:95px">INICIO</th>
      <th class="labels text-center" scope="col" rowspan="2" style="width:95px">FIN</th>
      <?php foreach ($gantt_anos as $ano => $sems): ?>
      <th class="labels text-center gantt-ano" scope="col" colspan="<?php echo count($sems); ?>"><?php echo (int) $ano; ?></th>
      <?php endforeach; ?>
    </tr>
    <tr class="table-primary">
      <?php foreach ($gantt_anos as $ano => $sems): foreach ($sems as $s): ?>
      <th class="labels text-center gantt-sem" scope="col">S<?php echo ($s % 2) + 1; ?></th>
      <?php endforeach; endforeach; ?>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($acciones as $a):
      $si = semestre_idx($a['inicio']);
      $sf = semestre_idx($a['fin']);
      if ($si === null) $si = $sf;
      if ($sf === null) $sf = $si;
  ?>
    <tr>
      <td>
        <?php if ($puede_editar_acc): ?>
        <a href="acciones_editar.php?acciones_id=<?php echo (int) $a['id']; ?>&proyectos_id=<?php echo (int) $row['id']; ?>"><img src="img/edit.png" width="32"></a>
        <?php else: ?>
        <a href="acciones_editar.php?acciones_id=<?php echo (int) $a['id']; ?>&proyectos_id=<?php echo (int) $row['id']; ?>" title="Ver"><img src="img/document.png" width="32"></a>
        <?php endif; ?>
      </td>
      <td>
        <?php echo h($a['nombre_accion']); ?>
        <?php if (!empty($a['verificador'])): ?>
        <div class="accion-verificador"><i class="fa-solid fa-check-double"></i>&nbsp;<?php echo h($a['verificador']); ?></div>
        <?php endif; ?>
      </td>
      <td class="text-center text-nowrap"><?php echo fecha_cl($a['inicio']); ?></td>
      <td class="text-center text-nowrap"><?php echo fecha_cl($a['fin']); ?></td>
      <?php foreach ($gantt_anos as $ano => $sems): foreach ($sems as $s):
          $activo = ($si !== null && $s >= $si && $s <= $sf);
          $ini_bloque = $activo && $s === $si;
          $fin_bloque = $activo && $s === $sf;
      ?>
      <td class="gantt-celda<?php echo ($s % 2 === 0) ? ' gantt-s1' : ''; ?>"><?php if ($activo): ?><div class="gantt-barra<?php echo $ini_bloque ? ' ini' : ''; ?><?php echo $fin_bloque ? ' fin' : ''; ?>" title="<?php echo h(fecha_cl($a['inicio']) . ' al ' . fecha_cl($a['fin'])); ?>"></div><?php endif; ?></td>
      <?php endforeach; endforeach; ?>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<small class="text-muted">S1 = enero a junio, S2 = julio a diciembre. La barra marca los semestres que toca la acción entre su inicio y su fin.</small>
</div>
</div>
<?php endif; ?>
<?php } ?>


<div style="height:60px"></div>

            </div><!-- /container -->
            </div><!-- /ficha-print -->
            
        </div>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>

<script>
// En esta pantalla todos los perfiles pueden exportar la ficha (también en modo lectura),
// aunque una regla general oculte o deshabilite el botón.
(function () {
    function habilitarExportar() {
        $('#btnPdfFicha').prop('disabled', false).removeClass('d-none disabled').css('opacity', '')
            .each(function () { this.style.setProperty('display', 'inline-block', 'important'); });
    }
    $(habilitarExportar);
    $(window).on('load', habilitarExportar);
})();
</script>

<script>
// ============================================================
// Utilidades numéricas (formato chileno)
// ============================================================
function parseMonto(val) {
    val = String(val || '').replace(/\s/g, '');
    if (val === '') return null;
    if (val.indexOf(',') !== -1) {
        // 1.234,56 => 1234.56
        val = val.replace(/\./g, '').replace(',', '.');
    } else if (/^-?\d{1,3}(\.\d{3})+$/.test(val)) {
        // 1.234.567 (solo miles) => 1234567
        val = val.replace(/\./g, '');
    }
    var n = parseFloat(val);
    return isNaN(n) ? null : n;
}

function fmtCL(n, dec) {
    dec = (dec === undefined) ? 2 : dec;
    return (n || 0).toLocaleString('es-CL', { minimumFractionDigits: dec, maximumFractionDigits: dec });
}

function parseCoord(val) {
    val = String(val || '').trim().replace(',', '.');
    if (val === '') return null;
    var n = parseFloat(val);
    return isNaN(n) ? null : n;
}


// ============================================================
// Totales en tiempo real
// ============================================================
function actualizarTotales() {
    var pref = parseMonto($('#costos_perfil_prefactibilidad').val()) || 0;
    var dis  = parseMonto($('#costos_diseno').val()) || 0;
    var eje  = parseMonto($('#costos_ejecucion').val()) || 0;
    $('#total_pref_diseno').text(fmtCL(pref + dis));
    $('#total_costo').text(fmtCL(pref + dis + eje));
    $('#suma_costo_total').val(fmtCL(pref + dis + eje));

    var gpref = parseMonto($('#gasto_perfil_prefactibilidad').val()) || 0;
    var gdis  = parseMonto($('#gasto_diseno').val()) || 0;
    var geje  = parseMonto($('#gasto_ejecucion').val()) || 0;
    $('#gasto_total').val(fmtCL(gpref + gdis + geje));
}

// Avance de la meta física: realizada / proyectada
function actualizarMetaAvance() {
    var proy = parseMonto($('#cantidad_proyectada').val());
    var real = parseMonto($('#cantidad_realizada').val()) || 0;
    var uni  = $.trim($('#unidad_de_medida').val());
    var $rel = $('#metaAvanceRelleno').removeClass('bajo medio completo');

    if (proy === null || proy <= 0) {
        $('#metaAvanceTexto').text('Ingrese la cantidad proyectada para calcular el avance.');
        $('#metaAvancePct').text('-');
        $rel.css('width', '0%');
        return;
    }

    var pct = real / proy * 100;
    var fmtCant = function (n) { return n.toLocaleString('es-CL', { maximumFractionDigits: 2 }); };
    $('#metaAvanceTexto').empty().append(
        'Realizado: ', $('<strong>').text(fmtCant(real)),
        ' de ', $('<strong>').text(fmtCant(proy)),
        uni ? ' ' + uni : ''
    );
    $('#metaAvancePct').text(fmtCL(pct, 1) + '%');
    $rel.css('width', Math.max(0, Math.min(100, pct)) + '%')
        .addClass(pct >= 100 ? 'completo' : (pct >= 50 ? 'medio' : 'bajo'));
}

function actualizarAnos() {
    var ini = parseInt($('#inicio').val(), 10);
    var ter = parseInt($('#termino').val(), 10);
    var txt = (!isNaN(ini) && !isNaN(ter)) ? (ter - ini) : '-';
    $('#total_anos').text(txt);
    $('#plazos_ejecucion_anos').attr('placeholder', txt === '-' ? '' : 'Sugerido: ' + txt);
}


// ============================================================
// Selects con código + texto (n_inst/instrumento_de_origen, cod_sector/sector, etc.)
// ============================================================
function sincronizarTexto($sel) {
    var destino = $sel.data('texto');
    if (!destino) return;
    var $opt = $sel.find('option:selected');
    var texto = ($sel.val() === '') ? '' : $.trim($opt.text());
    $('#' + destino).val(texto);
}


// ============================================================
// Mini mapa (ubicación actual, al final de la ficha)
// ============================================================
var miniMap    = null;
var miniMarker = null;

function actualizarMiniMapa() {
    var lat = parseCoord($('#point_x').val());
    var lng = parseCoord($('#point_y').val());
    var valido = (lat !== null && lng !== null && Math.abs(lat) <= 90 && Math.abs(lng) <= 180);

    if (!valido) {
        $('#miniMapa').hide();
        $('#miniMapaVacio').css('display', 'flex');
        $('#miniMapaCoords').text('');
        return;
    }

    $('#miniMapaVacio').hide();
    $('#miniMapa').show();
    $('#miniMapaCoords').html('<i class="fa-solid fa-crosshairs"></i>&nbsp;Lat ' + lat.toFixed(6) + ' | Lng ' + lng.toFixed(6));

    if (!miniMap) {
        miniMap = L.map('miniMapa', {
            zoomControl: true,
            scrollWheelZoom: true,
            attributionControl: true
        }).setView([lat, lng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
            crossOrigin: true   // necesario para que salga en el PDF (html2canvas)
        }).addTo(miniMap);

        miniMarker = L.marker([lat, lng]).addTo(miniMap);
    } else {
        miniMarker.setLatLng([lat, lng]);
        miniMap.setView([lat, lng], miniMap.getZoom());
    }
    setTimeout(function () { miniMap.invalidateSize(); }, 100);
}

function actualizarLocalidadesTexto() {
    var nombres = [];
    $('.chk-localidad:checked').each(function () {
        nombres.push($.trim($(this).siblings('span').text()));
    });
    $('#miniMapaLocalidades').html(nombres.length ? '<i class="fa-solid fa-map-pin"></i>&nbsp;' + $('<div>').text(nombres.join(', ')).html() : '');
}


// ============================================================
// MAPA GEOLOCALIZACIÓN (modal) - OpenStreetMap / Leaflet
// ============================================================
var geoMap    = null;
var geoMarker = null;

// Destino de la coordenada elegida en el mapa: ubicación principal o ubicación adicional
var GEO_DESTINOS = {
    principal: { lat: '#point_x', lng: '#point_y', texto: 'Haga clic en el mapa para fijar la coordenada de la iniciativa', alFijar: function () { actualizarMiniMapa(); } },
    ubic:      { lat: '#ub_lat',  lng: '#ub_lng',  texto: 'Haga clic en el mapa para fijar la ubicación adicional', alFijar: function () {} }
};
var geoDestino = GEO_DESTINOS.principal;

var GEO_DEFAULT_LAT  = -33.6442;   // San José de Maipo
var GEO_DEFAULT_LNG  = -70.3567;
var GEO_DEFAULT_ZOOM = 13;

function abrirMapaGeo(destino) {
    geoDestino = GEO_DESTINOS[destino] || GEO_DESTINOS.principal;
    $('#geoModalHeader p').text(geoDestino.texto);

    // Coordenada actual del destino en el pie del modal
    var la0 = parseCoord($(geoDestino.lat).val()), ln0 = parseCoord($(geoDestino.lng).val());
    if (la0 !== null && ln0 !== null) {
        $('#geoLatDisplay').css({ fontWeight: '600', color: '#023047' }).text('Lat: ' + la0);
        $('#geoLngDisplay').text('\u00a0|\u00a0Lng: ' + ln0);
    } else {
        $('#geoLatDisplay').css({ fontWeight: '400', color: '#888' }).text('Sin coordenada seleccionada');
        $('#geoLngDisplay').text('');
    }

    document.getElementById('geoModal').classList.add('active');

    if (geoMap) {
        geoMap.remove();
        geoMap    = null;
        geoMarker = null;
        var div = document.getElementById('geoMap');
        div.innerHTML = '';
        div._leaflet_id = undefined;
    }

    setTimeout(function () {
        var lat = parseCoord($(geoDestino.lat).val());
        var lng = parseCoord($(geoDestino.lng).val());
        var tiene = (lat !== null && lng !== null);

        geoMap = L.map('geoMap', { zoomControl: true })
            .setView([tiene ? lat : GEO_DEFAULT_LAT, tiene ? lng : GEO_DEFAULT_LNG], tiene ? 15 : GEO_DEFAULT_ZOOM);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19
        }).addTo(geoMap);

        function crearMarcador(la, ln) {
            geoMarker = L.marker([la, ln], { draggable: true }).addTo(geoMap);
            geoMarker.on('dragend', function (e) {
                var p = e.target.getLatLng();
                _setCoords(p.lat, p.lng);
            });
        }

        if (tiene) crearMarcador(lat, lng);

        geoMap.on('click', function (e) {
            if (geoMarker) {
                geoMarker.setLatLng(e.latlng);
            } else {
                crearMarcador(e.latlng.lat, e.latlng.lng);
            }
            _setCoords(e.latlng.lat, e.latlng.lng);
        });

        geoMap.invalidateSize();
    }, 300);
}

function _setCoords(lat, lng) {
    var latFmt = lat.toFixed(6);
    var lngFmt = lng.toFixed(6);

    $(geoDestino.lat).val(latFmt).trigger('change');
    $(geoDestino.lng).val(lngFmt).trigger('change');

    var latD = document.getElementById('geoLatDisplay');
    latD.style.fontWeight = '600';
    latD.style.color      = '#023047';
    latD.textContent      = 'Lat: ' + latFmt;
    document.getElementById('geoLngDisplay').textContent = '\u00a0|\u00a0Lng: ' + lngFmt;

    var toast = document.getElementById('geoToast');
    toast.textContent = '\u2713\u00a0Coordenadas traspasadas: ' + latFmt + ', ' + lngFmt;
    toast.style.display = 'block';
    clearTimeout(toast._t);
    toast._t = setTimeout(function () { toast.style.display = 'none'; }, 3000);

    geoDestino.alFijar();
}

function cerrarMapaGeo() {
    document.getElementById('geoModal').classList.remove('active');
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') cerrarMapaGeo();
});


// ============================================================
// Inicialización
// ============================================================
$(document).ready(function () {

    $('#geoModal').on('click', function (e) {
        if (e.target === this) cerrarMapaGeo();
    });

    $('.costo, .gasto').on('input change', actualizarTotales);
    $('#cantidad_proyectada, #cantidad_realizada, #unidad_de_medida').on('input change', actualizarMetaAvance);
    actualizarMetaAvance();
    $('#inicio, #termino').on('input change', actualizarAnos);
    actualizarAnos();

    $('.select-con-texto').on('change', function () { sincronizarTexto($(this)); });

    $('#point_x, #point_y').on('change', actualizarMiniMapa);
    $('.chk-localidad').on('change', actualizarLocalidadesTexto);
    actualizarMiniMapa();
});


// ============================================================
// Ubicaciones adicionales (se guardan de inmediato vía api_proyectos.php)
// ============================================================
<?php if (!$es_nuevo): ?>
var UBIC_PID      = <?php echo (int) $row['id']; ?>;
var UBIC_EDITABLE = <?php echo !empty($puede_editar_ubic) ? 'true' : 'false'; ?>;
var ubicLista     = [];
var ubicMap       = null;
var ubicCapa      = null;

function escH(s) { return $('<div>').text(s == null ? '' : String(s)).html(); }

function ubicCargar() {
    $.getJSON('api/api_proyectos.php', { token: $('#token').val(), task: 'ubic_list', proyectos_id: UBIC_PID }, function (r) {
        ubicLista = (r && r.ok) ? r.data : [];
        ubicPintarTabla();
        ubicPintarMapa();
    }).fail(function () {
        $('#ubicBody').html('<tr><td colspan="5" class="text-danger">No se pudieron cargar las ubicaciones.</td></tr>');
    });
}

function ubicPintarTabla() {
    if (!ubicLista.length) {
        $('#ubicBody').html('<tr><td colspan="5" class="text-muted">Esta iniciativa no tiene ubicaciones adicionales.</td></tr>');
        return;
    }
    var html = '';
    ubicLista.forEach(function (u, i) {
        html += '<tr>' +
            '<td><span class="ubic-num">' + (i + 1) + '</span></td>' +
            '<td>' + escH(u.descripcion) + '</td>' +
            '<td>' + escH(u.latitud) + '</td>' +
            '<td>' + escH(u.longitud) + '</td>';
        if (UBIC_EDITABLE) {
            html += '<td class="text-center" data-html2canvas-ignore="true">' +
                '<button type="button" class="ubic-btn ubic-editar" data-id="' + u.id + '" title="Editar"><img src="img/edit.png" width="28"></button>&nbsp;&nbsp;' +
                '<button type="button" class="ubic-btn ubic-eliminar text-danger" data-id="' + u.id + '" title="Eliminar"><i class="fa-regular fa-trash-can fa-lg"></i></button>' +
                '</td>';
        }
        html += '</tr>';
    });
    $('#ubicBody').html(html);
}

function ubicPintarMapa() {
    var puntos = [];
    var latP = parseCoord($('#point_x').val()), lngP = parseCoord($('#point_y').val());
    var hayPrincipal = (latP !== null && lngP !== null);
    if (hayPrincipal) puntos.push([latP, lngP]);

    var adicionales = [];
    ubicLista.forEach(function (u, i) {
        var la = parseCoord(u.latitud), ln = parseCoord(u.longitud);
        if (la !== null && ln !== null) { adicionales.push({ n: i + 1, u: u, ll: [la, ln] }); puntos.push([la, ln]); }
    });

    if (!puntos.length || typeof L === 'undefined') {
        $('#ubicMapa').hide(); $('#ubicMapaVacio').show();
        return;
    }
    $('#ubicMapaVacio').hide(); $('#ubicMapa').show();

    if (!ubicMap) {
        ubicMap = L.map('ubicMapa', { scrollWheelZoom: false });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
            crossOrigin: true
        }).addTo(ubicMap);
    }
    if (ubicCapa) ubicCapa.remove();
    ubicCapa = L.layerGroup().addTo(ubicMap);

    if (hayPrincipal) {
        L.marker([latP, lngP]).addTo(ubicCapa).bindTooltip('Ubicación principal');
    }
    adicionales.forEach(function (a) {
        L.marker(a.ll, {
            icon: L.divIcon({ className: '', html: '<div class="ubic-icono">' + a.n + '</div>', iconSize: [28, 28], iconAnchor: [14, 14] })
        }).addTo(ubicCapa).bindTooltip(escH(a.n + '. ' + a.u.descripcion));
    });

    setTimeout(function () {
        ubicMap.invalidateSize();
        if (puntos.length === 1) ubicMap.setView(puntos[0], 15);
        else ubicMap.fitBounds(puntos, { padding: [30, 30], maxZoom: 16 });
    }, 100);
}

function ubicAbrirEditor(u) {
    $('#ub_id').val(u ? u.id : 0);
    $('#ub_desc').val(u ? u.descripcion : '');
    $('#ub_lat').val(u ? u.latitud : '');
    $('#ub_lng').val(u ? u.longitud : '');
    $('#ubicEditor').slideDown(150, function () { $('#ub_desc').focus(); });
}

function ubicCerrarEditor() { $('#ubicEditor').slideUp(150); }

function ubicAviso(titulo, texto, tipo) {
    $.alert({ title: titulo, content: texto, type: tipo || 'red', typeAnimated: true });
}

$(document).ready(function () {
    ubicCargar();
    $('#point_x, #point_y').on('change', ubicPintarMapa);

    if (!UBIC_EDITABLE) return;

    $('#btnUbicNueva').on('click', function () { ubicAbrirEditor(null); });

    // Están dentro del formulario de la iniciativa: Enter guarda la ubicación, no la ficha
    $('#ubicEditor').on('keydown', 'input', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#btnUbicGuardar').click(); }
    });
    $('#btnUbicCancelar').on('click', ubicCerrarEditor);

    $('#ubicBody').on('click', '.ubic-editar', function () {
        var id = $(this).data('id');
        ubicAbrirEditor(ubicLista.find(function (u) { return u.id == id; }));
        $('html, body').animate({ scrollTop: $('#ubicaciones').offset().top - 80 }, 200);
    });

    $('#btnUbicGuardar').on('click', function () {
        var desc = $.trim($('#ub_desc').val());
        var lat  = parseCoord($('#ub_lat').val());
        var lng  = parseCoord($('#ub_lng').val());
        if (desc === '') { ubicAviso('Atención', 'Ingrese una descripción.'); return; }
        if (lat === null || lng === null || Math.abs(lat) > 90 || Math.abs(lng) > 180) {
            ubicAviso('Atención', 'Ingrese una latitud y longitud válidas, o selecciónelas en el mapa.'); return;
        }
        var $b = $(this).prop('disabled', true);
        $.post('api/api_proyectos.php', {
            token: $('#token').val(), task: 'ubic_save', proyectos_id: UBIC_PID,
            ubicaciones_id: $('#ub_id').val(), descripcion: desc, latitud: lat, longitud: lng
        }, function (r) {
            if (r && r.ok) { ubicCerrarEditor(); ubicCargar(); }
            else ubicAviso('Atención', (r && r.mensaje) || 'No se pudo guardar la ubicación.');
        }, 'json').fail(function (x) {
            ubicAviso('Error', (x.responseJSON && x.responseJSON.mensaje) || 'No se pudo guardar la ubicación.');
        }).always(function () { $b.prop('disabled', false); });
    });

    $('#ubicBody').on('click', '.ubic-eliminar', function () {
        var id = $(this).data('id');
        var u  = ubicLista.find(function (x) { return x.id == id; });
        $.confirm({
            title: 'Eliminar ubicación',
            content: '¿Eliminar la ubicación <strong>' + escH(u ? u.descripcion : '') + '</strong>?<br>Esta acción no se puede deshacer.',
            type: 'red',
            typeAnimated: true,
            buttons: {
                eliminar: {
                    text: 'Sí, eliminar', btnClass: 'btn-red',
                    action: function () {
                        $.post('api/api_proyectos.php', { token: $('#token').val(), task: 'ubic_delete', proyectos_id: UBIC_PID, ubicaciones_id: id }, function (r) {
                            if (r && r.ok) { if ($('#ub_id').val() == id) ubicCerrarEditor(); ubicCargar(); }
                            else ubicAviso('Atención', (r && r.mensaje) || 'No se pudo eliminar.');
                        }, 'json').fail(function () { ubicAviso('Error', 'No se pudo eliminar la ubicación.'); });
                    }
                },
                cancelar: { text: 'Cancelar' }
            }
        });
    });
});
<?php endif; ?>


// ============================================================
// Fuentes de financiamiento por etapa (tabla financiamiento, guardado inmediato)
// ============================================================
<?php if (!$es_nuevo): ?>
var FIN_PID      = <?php echo (int) $row['id']; ?>;
var FIN_EDITABLE = <?php echo !empty($puede_editar_fin) ? 'true' : 'false'; ?>;
var FIN_ETAPAS   = <?php echo json_encode((object) $CAT_ETAPAS, JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE); ?>;
var finLista     = [];

function finNombreEtapa(cod) {
    if (cod === null || cod === '' || cod === undefined) return '(sin etapa)';
    return FIN_ETAPAS[cod] || ('Etapa ' + cod);
}

function finCargar() {
    $.getJSON('api/api_proyectos.php', { token: $('#token').val(), task: 'fin_list', proyectos_id: FIN_PID }, function (r) {
        finLista = (r && r.ok) ? r.data : [];
        finPintar();
    }).fail(function () {
        $('#finBody').html('<tr><td colspan="4" class="text-danger">No se pudieron cargar las fuentes de financiamiento.</td></tr>');
    });
}

function finPintar() {
    var cols = FIN_EDITABLE ? 4 : 3;
    if (!finLista.length) {
        $('#finBody').html('<tr><td colspan="' + cols + '" class="text-muted">Esta iniciativa no tiene fuentes de financiamiento registradas.</td></tr>');
        $('#finFoot').html('');
        return;
    }

    // Agrupar por etapa (vienen ordenadas por etapa desde la API)
    var grupos = [];
    finLista.forEach(function (f) {
        var g = grupos.length ? grupos[grupos.length - 1] : null;
        if (!g || String(g.etapa) !== String(f.etapa)) {
            g = { etapa: f.etapa, filas: [], total: 0 };
            grupos.push(g);
        }
        g.filas.push(f);
        g.total += parseFloat(f.monto) || 0;
    });

    var html = '', total = 0;
    grupos.forEach(function (g) {
        g.filas.forEach(function (f, i) {
            html += '<tr>' +
                '<td>' + (i === 0 ? '<strong>' + escH(finNombreEtapa(g.etapa)) + '</strong>' : '') + '</td>' +
                '<td class="text-end">' + fmtCL(parseFloat(f.monto) || 0, 0) + '</td>' +
                '<td>' + escH(f.fuente) + '</td>';
            if (FIN_EDITABLE) {
                html += '<td class="text-center" data-html2canvas-ignore="true">' +
                    '<button type="button" class="ubic-btn fin-editar" data-id="' + f.id + '" title="Editar"><img src="img/edit.png" width="28"></button>&nbsp;&nbsp;' +
                    '<button type="button" class="ubic-btn fin-eliminar text-danger" data-id="' + f.id + '" title="Eliminar"><i class="fa-regular fa-trash-can fa-lg"></i></button>' +
                    '</td>';
            }
            html += '</tr>';
        });
        if (g.filas.length > 1) {
            html += '<tr class="fin-subtotal"><td class="text-end">Subtotal ' + escH(finNombreEtapa(g.etapa)) + '</td>' +
                '<td class="text-end">' + fmtCL(g.total, 0) + '</td><td colspan="' + (cols - 2) + '"></td></tr>';
        }
        total += g.total;
    });
    $('#finBody').html(html);
    $('#finFoot').html('<tr><td class="text-end">Total</td><td class="text-end">' + fmtCL(total, 0) + '</td><td colspan="' + (cols - 2) + '"></td></tr>');
}

function finAbrirEditor(f) {
    $('#fin_id').val(f ? f.id : 0);
    $('#fin_etapa').val(f ? String(f.etapa) : '');
    $('#fin_monto').val(f ? fmtCL(parseFloat(f.monto) || 0, 0) : '');
    $('#fin_fuente').val(f ? f.fuente : '');
    $('#finEditor').slideDown(150, function () { $('#fin_etapa').focus(); });
}

function finCerrarEditor() { $('#finEditor').slideUp(150); }

$(document).ready(function () {
    finCargar();

    if (!FIN_EDITABLE) return;

    $('#btnFinNueva').on('click', function () { finAbrirEditor(null); });
    $('#btnFinCancelar').on('click', finCerrarEditor);

    // Dentro del formulario de la iniciativa: Enter guarda la fuente, no la ficha
    $('#finEditor').on('keydown', 'input', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#btnFinGuardar').click(); }
    });

    $('#finBody').on('click', '.fin-editar', function () {
        var id = $(this).data('id');
        finAbrirEditor(finLista.find(function (f) { return f.id == id; }));
        $('html, body').animate({ scrollTop: $('#financiamiento').offset().top - 80 }, 200);
    });

    $('#btnFinGuardar').on('click', function () {
        var etapa  = $('#fin_etapa').val();
        var monto  = parseMonto($('#fin_monto').val());
        var fuente = $.trim($('#fin_fuente').val());
        if (etapa === '')                 { ubicAviso('Atención', 'Seleccione la etapa.'); return; }
        if (monto === null || monto < 0)  { ubicAviso('Atención', 'Ingrese un monto válido en M$.'); return; }
        if (fuente === '')                { ubicAviso('Atención', 'Ingrese la fuente de financiamiento.'); return; }

        var $b = $(this).prop('disabled', true);
        $.post('api/api_proyectos.php', {
            token: $('#token').val(), task: 'fin_save', proyectos_id: FIN_PID,
            financiamiento_id: $('#fin_id').val(), etapa: etapa, monto: Math.round(monto), fuente: fuente
        }, function (r) {
            if (r && r.ok) { finCerrarEditor(); finCargar(); }
            else ubicAviso('Atención', (r && r.mensaje) || 'No se pudo guardar la fuente.');
        }, 'json').fail(function (x) {
            ubicAviso('Error', (x.responseJSON && x.responseJSON.mensaje) || 'No se pudo guardar la fuente.');
        }).always(function () { $b.prop('disabled', false); });
    });

    $('#finBody').on('click', '.fin-eliminar', function () {
        var id = $(this).data('id');
        var f  = finLista.find(function (x) { return x.id == id; });
        $.confirm({
            title: 'Eliminar fuente',
            content: '¿Eliminar <strong>' + escH(f ? f.fuente : '') + '</strong> de la etapa <strong>' + escH(f ? finNombreEtapa(f.etapa) : '') + '</strong>?<br>Esta acción no se puede deshacer.',
            type: 'red',
            typeAnimated: true,
            buttons: {
                eliminar: {
                    text: 'Sí, eliminar', btnClass: 'btn-red',
                    action: function () {
                        $.post('api/api_proyectos.php', { token: $('#token').val(), task: 'fin_delete', proyectos_id: FIN_PID, financiamiento_id: id }, function (r) {
                            if (r && r.ok) { if ($('#fin_id').val() == id) finCerrarEditor(); finCargar(); }
                            else ubicAviso('Atención', (r && r.mensaje) || 'No se pudo eliminar.');
                        }, 'json').fail(function () { ubicAviso('Error', 'No se pudo eliminar la fuente.'); });
                    }
                },
                cancelar: { text: 'Cancelar' }
            }
        });
    });
});
<?php endif; ?>


// ============================================================
// Gasto por etapa (tabla gasto, guardado inmediato)
// Reutiliza FIN_PID, FIN_EDITABLE y finNombreEtapa()
// ============================================================
<?php if (!$es_nuevo): ?>
var gasLista = [];

function gasCargar() {
    $.getJSON('api/api_proyectos.php', { token: $('#token').val(), task: 'gas_list', proyectos_id: FIN_PID }, function (r) {
        gasLista = (r && r.ok) ? r.data : [];
        gasPintar();
    }).fail(function () {
        $('#gasBody').html('<tr><td colspan="4" class="text-danger">No se pudieron cargar los gastos por etapa.</td></tr>');
    });
}

function gasPintar() {
    var cols = FIN_EDITABLE ? 4 : 3;
    if (!gasLista.length) {
        $('#gasBody').html('<tr><td colspan="' + cols + '" class="text-muted">Esta iniciativa no tiene gastos por etapa registrados.</td></tr>');
        $('#gasFoot').html('');
        return;
    }

    // Agrupar por etapa (vienen ordenadas por etapa desde la API)
    var grupos = [];
    gasLista.forEach(function (f) {
        var g = grupos.length ? grupos[grupos.length - 1] : null;
        if (!g || String(g.etapa) !== String(f.etapa)) {
            g = { etapa: f.etapa, filas: [], total: 0 };
            grupos.push(g);
        }
        g.filas.push(f);
        g.total += parseFloat(f.monto) || 0;
    });

    var html = '', total = 0;
    grupos.forEach(function (g) {
        g.filas.forEach(function (f, i) {
            html += '<tr>' +
                '<td>' + (i === 0 ? '<strong>' + escH(finNombreEtapa(g.etapa)) + '</strong>' : '') + '</td>' +
                '<td class="text-end">' + fmtCL(parseFloat(f.monto) || 0, 0) + '</td>' +
                '<td>' + escH(f.fuente) + '</td>';
            if (FIN_EDITABLE) {
                html += '<td class="text-center" data-html2canvas-ignore="true">' +
                    '<button type="button" class="ubic-btn gas-editar" data-id="' + f.id + '" title="Editar"><img src="img/edit.png" width="28"></button>&nbsp;&nbsp;' +
                    '<button type="button" class="ubic-btn gas-eliminar text-danger" data-id="' + f.id + '" title="Eliminar"><i class="fa-regular fa-trash-can fa-lg"></i></button>' +
                    '</td>';
            }
            html += '</tr>';
        });
        if (g.filas.length > 1) {
            html += '<tr class="fin-subtotal"><td class="text-end">Subtotal ' + escH(finNombreEtapa(g.etapa)) + '</td>' +
                '<td class="text-end">' + fmtCL(g.total, 0) + '</td><td colspan="' + (cols - 2) + '"></td></tr>';
        }
        total += g.total;
    });
    $('#gasBody').html(html);
    $('#gasFoot').html('<tr><td class="text-end">Total</td><td class="text-end">' + fmtCL(total, 0) + '</td><td colspan="' + (cols - 2) + '"></td></tr>');
}

function gasAbrirEditor(f) {
    $('#gas_id').val(f ? f.id : 0);
    $('#gas_etapa').val(f ? String(f.etapa) : '');
    $('#gas_monto').val(f ? fmtCL(parseFloat(f.monto) || 0, 0) : '');
    $('#gas_fuente').val(f ? f.fuente : '');
    $('#gasEditor').slideDown(150, function () { $('#gas_etapa').focus(); });
}

function gasCerrarEditor() { $('#gasEditor').slideUp(150); }

$(document).ready(function () {
    gasCargar();

    if (!FIN_EDITABLE) return;

    $('#btnGasNueva').on('click', function () { gasAbrirEditor(null); });
    $('#btnGasCancelar').on('click', gasCerrarEditor);

    // Dentro del formulario de la iniciativa: Enter guarda el gasto, no la ficha
    $('#gasEditor').on('keydown', 'input', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#btnGasGuardar').click(); }
    });

    $('#gasBody').on('click', '.gas-editar', function () {
        var id = $(this).data('id');
        gasAbrirEditor(gasLista.find(function (f) { return f.id == id; }));
        $('html, body').animate({ scrollTop: $('#gastos_etapa').offset().top - 80 }, 200);
    });

    $('#btnGasGuardar').on('click', function () {
        var etapa  = $('#gas_etapa').val();
        var monto  = parseMonto($('#gas_monto').val());
        var fuente = $.trim($('#gas_fuente').val());
        if (etapa === '')                 { ubicAviso('Atención', 'Seleccione la etapa.'); return; }
        if (monto === null || monto < 0)  { ubicAviso('Atención', 'Ingrese un monto válido en M$.'); return; }
        if (fuente === '')                { ubicAviso('Atención', 'Ingrese la fuente del gasto.'); return; }

        var $b = $(this).prop('disabled', true);
        $.post('api/api_proyectos.php', {
            token: $('#token').val(), task: 'gas_save', proyectos_id: FIN_PID,
            gasto_id: $('#gas_id').val(), etapa: etapa, monto: Math.round(monto), fuente: fuente
        }, function (r) {
            if (r && r.ok) { gasCerrarEditor(); gasCargar(); }
            else ubicAviso('Atención', (r && r.mensaje) || 'No se pudo guardar el gasto.');
        }, 'json').fail(function (x) {
            ubicAviso('Error', (x.responseJSON && x.responseJSON.mensaje) || 'No se pudo guardar el gasto.');
        }).always(function () { $b.prop('disabled', false); });
    });

    $('#gasBody').on('click', '.gas-eliminar', function () {
        var id = $(this).data('id');
        var f  = gasLista.find(function (x) { return x.id == id; });
        $.confirm({
            title: 'Eliminar gasto',
            content: '¿Eliminar el gasto de <strong>' + escH(f ? f.fuente : '') + '</strong> en la etapa <strong>' + escH(f ? finNombreEtapa(f.etapa) : '') + '</strong>?<br>Esta acción no se puede deshacer.',
            type: 'red',
            typeAnimated: true,
            buttons: {
                eliminar: {
                    text: 'Sí, eliminar', btnClass: 'btn-red',
                    action: function () {
                        $.post('api/api_proyectos.php', { token: $('#token').val(), task: 'gas_delete', proyectos_id: FIN_PID, gasto_id: id }, function (r) {
                            if (r && r.ok) { if ($('#gas_id').val() == id) gasCerrarEditor(); gasCargar(); }
                            else ubicAviso('Atención', (r && r.mensaje) || 'No se pudo eliminar.');
                        }, 'json').fail(function () { ubicAviso('Error', 'No se pudo eliminar el gasto.'); });
                    }
                },
                cancelar: { text: 'Cancelar' }
            }
        });
    });
});
<?php endif; ?>


// ============================================================
// Guardar
// ============================================================
function guardar() {
    $.confirm({
        title: '¡Atención!',
        content: '¿Está seguro que desea guardar los cambios?',
        type: 'blue',
        typeAnimated: true,
        buttons: {
            confirmar: {
                text: 'Sí, guardar',
                btnClass: 'btn-blue',
                action: function () { $("#form1").submit(); }
            },
            cancelar: { text: 'Cancelar', action: function () {} }
        }
    });    
}

$(document).ready(function () {
    $('form#form1').on('submit', function (e) {
        e.preventDefault();

        var datos = new FormData(document.getElementById("form1"));

        // Montos: enviar como número plano (1234.56)
        $('#form1 .monto').each(function () {
            var n = parseMonto($(this).val());
            datos.set(this.name, n === null ? '' : n);
        });
        // Coordenadas: punto decimal
        $('#form1 .coord').each(function () {
            var n = parseCoord($(this).val());
            datos.set(this.name, n === null ? '' : n);
        });
        // Plazo sugerido si viene vacío
        if ($('#plazos_ejecucion_anos').val() === '') {
            var t = $('#total_anos').text().trim();
            if (/^\d+$/.test(t)) datos.set('plazos_ejecucion_anos', t);
        }

        $.ajax({
            type: "POST",
            url: "api/api_proyectos.php", 
            data: datos,
            contentType: false,       
            cache: false,             
            processData: false,         
            success: function (response) {
                var nuevo_id = parseInt(response, 10);
                $.confirm({
                    title: 'Éxito',
                    content: 'Los datos se guardaron correctamente',
                    type: 'green',
                    typeAnimated: true,
                    buttons: {
                        ok: {
                            text: 'Aceptar',
                            btnClass: 'btn-green',
                            action: function () {
                                if (nuevo_id > 0) {
                                    window.location = 'proyectos_editar.php?proyectos_id=' + nuevo_id;
                                } else {
                                    window.location.reload();
                                }
                            }
                        }
                    }
                });
            },
            error: function (xhr, status, error) {
                var msg = (xhr.responseText || '').substring(0, 300);
                $.confirm({
                    title: 'Error',
                    content: 'No se pudieron guardar los datos.' + (msg ? '<br><small>' + $('<div>').text(msg).html() + '</small>' : ''),
                    type: 'red',
                    typeAnimated: true,
                    buttons: { ok: { text: 'Aceptar', btnClass: 'btn-red' } }
                });
                console.error("Error en la petición AJAX:", error);
            },
            beforeSend: function () {
                $('.fa-floppy-disk').addClass('spin-icon');
                $('#btsend').prop('disabled', true);
            },
            complete: function () {    
                $('.fa-floppy-disk').removeClass('spin-icon');
                $('#btsend').prop('disabled', false);
            }        
        });
    });

    $("#btdelete").click(function () {
        $.confirm({
            title: 'Confirmar eliminación',
            content: '¿Está seguro que desea eliminar esta iniciativa?<br><strong>Esta acción no se puede deshacer.</strong>',
            type: 'red',
            typeAnimated: true,
            buttons: {
                eliminar: { text: 'Sí, eliminar', btnClass: 'btn-red', action: function () { eliminar(); } },
                cancelar: { text: 'Cancelar', action: function () {} }        
            }
        });       
    });
});

function eliminar() {
    $.ajax({
        type: "POST",
        url: "api/api_proyectos.php", 
        data: new FormData(document.getElementById("frm1")),
        contentType: false,       
        cache: false,             
        processData: false,         
        success: function () {
            $.confirm({
                title: 'Eliminado',
                content: 'La iniciativa ha sido eliminada correctamente',
                type: 'green',
                typeAnimated: true,
                buttons: {
                    ok: { text: 'Aceptar', btnClass: 'btn-green', action: function () { window.location = 'proyectos.php'; } }
                }
            });
        },
        error: function (xhr, status, error) {
            $.confirm({
                title: 'Error',
                content: 'No se pudo eliminar la iniciativa. Por favor, intente nuevamente.',
                type: 'red',
                typeAnimated: true,
                buttons: { ok: { text: 'Aceptar', btnClass: 'btn-red' } }
            });
            console.error("Error en la petición AJAX:", error);
        },
        beforeSend: function () {
            $('.fa-trash-can').addClass('spin-icon');
            $('#btdelete').prop('disabled', true);
        },
        complete: function () {    
            $('.fa-trash-can').removeClass('spin-icon');
            $('#btdelete').prop('disabled', false);
        }        
    });
}

<?php if (!$solo_lectura): ?>
// Advertencia de cambios no guardados
(function () {
    var formularioModificado = false;
    var guardando = false;

    $('#form1 input, #form1 select, #form1 textarea').not('#ubicEditor input, #finEditor input, #finEditor select').on('change input', function () {
        formularioModificado = true;
    });
    $(document).on('ajaxSuccess', function () { guardando = true; });
    $('#btsend').on('click', function () { guardando = true; });

    window.addEventListener('beforeunload', function (e) {
        if (formularioModificado && !guardando) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
})();
<?php endif; ?>
</script>

<!-- ========== MODAL MAPA GEOLOCALIZACIÓN (fuera del form) ========== -->
<div id="geoModal">
  <div id="geoModalBox">
    <div id="geoModalHeader">
      <div>
        <h6><i class="fa-solid fa-map-location-dot"></i>&nbsp; Seleccionar ubicación en el mapa</h6>
        <p>Haga clic en el mapa para fijar la coordenada de la iniciativa</p>
      </div>
      <button type="button" id="geoModalClose" onclick="cerrarMapaGeo()" title="Cerrar">&#10005;</button>
    </div>
    <div id="geoMapContainer">
      <div id="geoMap"></div>
      <div id="geoToast"></div>
    </div>
    <div id="geoModalFooter">
      <div id="geoCoordsDisplay">
        <i class="fa-solid fa-crosshairs" style="color:#4a8ecd"></i>&nbsp;
        <?php if ($lat_actual !== '' && $lng_actual !== ''): ?>
          <span id="geoLatDisplay">Lat: <?php echo h($lat_actual); ?></span>
          <span id="geoLngDisplay">&nbsp;|&nbsp;Lng: <?php echo h($lng_actual); ?></span>
        <?php else: ?>
          <span id="geoLatDisplay" style="font-weight:400;color:#888">Sin coordenada seleccionada</span>
          <span id="geoLngDisplay"></span>
        <?php endif; ?>
      </div>
      <button type="button" id="geoBtnCerrar" onclick="cerrarMapaGeo()">
        <i class="fa-solid fa-check"></i>&nbsp; Listo, cerrar mapa
      </button>
    </div>
  </div>
</div>
<!-- ========== /MODAL MAPA ========== -->

</body>
</html>
