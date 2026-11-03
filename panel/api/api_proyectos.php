<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-05
// ************************************************************

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

@session_start();

include("../../conf/db.php");		
require __DIR__ . '/inc_seguridad.php';
include('functions.php');
require_once __DIR__ . '/../inc_auditoria.php';


if ( 
    (!isset($_GET["token"]) || $_GET["token"] != session_id()) && 
    (!isset($_POST["token"]) || $_POST["token"] != session_id()) 
) {
    exit;
}


$q = "set names utf8mb4";
if(!$preg = $db->query($q)){
    die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}


$user_id = (int) $_SESSION["net_fulltrust_fas_id"];
$perfil  = $_SESSION['usuarios_profile'];


// **************************************************************************************
// Definiciones de la tabla proyectos
// **************************************************************************************

// Localidades (columnas tinyint 0/1) => etiqueta
$LOCALIDADES = [
    'la_obra'           => 'La Obra',
    'las_vertientes'    => 'Las Vertientes',
    'el_canelo'         => 'El Canelo',
    'el_manzano'        => 'El Manzano',
    'los_maitenes'      => 'Los Maitenes',
    'el_alfalfal'       => 'El Alfalfal',
    'el_colorado'       => 'El Colorado',
    'el_guayacan'       => 'El Guayacán',
    'san_jose_de_maipo' => 'San José de Maipo',
    'el_melocoton'      => 'El Melocotón',
    'lagunillas'        => 'Lagunillas',
    'el_toyo'           => 'El Toyo',
    'san_alfonso'       => 'San Alfonso',
    'el_ingenio'        => 'El Ingenio',
    'bollenar'          => 'Bollenar',
    'san_gabriel'       => 'San Gabriel',
    'el_yeso'           => 'El Yeso',
    'las_melosas'       => 'Las Melosas',
    'los_queltehues'    => 'Los Queltehues',
    'el_volcan'         => 'El Volcán',
    'banos_morales'     => 'Baños Morales',
    'lo_valdes'         => 'Lo Valdés',
    'el_romeral'        => 'El Romeral',
    'los_nogales'       => 'Los Nogales',
];

// Columnas editables desde el formulario (el nombre del campo POST = nombre de la columna)
$CAMPOS_EDITABLES = array_merge([
    'numero', 'codigo', 'n_inst', 'instrumento_de_origen', 'seleccionada_pm_sjm', 'tipo_de_inversion',
    'nombre_iniciativa', 'eje_estrategico', 'objetivo_estrategico', 'brecha_principal',
    'brecha_especifica', 'atributo_al_que_responde', 'descripcion', 'localizacion', 'localizacion_2',
    'cod_sector', 'sector', 'cod_subsector', 'subsector_de_inversion_sni', 'cod_tipo', 'tipo',
    'utr', 'codigo_idi', 'point_x', 'point_y', 'indicador_2', 'unidad_de_medida',
    'cantidad_proyectada', 'cantidad_realizada', 'origen_del_calculo_de_costos',
    'costos_perfil_prefactibilidad', 'costos_diseno', 'costos_ejecucion', 'costo_unitario_ejecucion',
    'gasto_perfil_prefactibilidad', 'gasto_diseno', 'gasto_ejecucion',
    'fuente_financiamiento_prefactibilidad', 'fuente_financiamiento_diseno', 'fuente_financiamiento_ejecucion',
    'plazos_ejecucion_anos', 'inicio', 'termino',
    'cod_etapa_actual', 'etapa_actual', 'cod_actividad_actual', 'actividad_actual',
    'responsable_del_mantenimiento', 'financiamiento_del_mantenimiento', 'pm',
], array_keys($LOCALIDADES));


// **************************************************************************************
// Instrumentos asignados al usuario ÁREA
// **************************************************************************************
$mis_instrumentos = [];

if ($perfil == 'ÁREA') {

    $q = "
        SELECT instrumentos_id
        FROM usuarios_areas
        WHERE usuarios_id = $user_id
        AND instrumentos_id IS NOT NULL
    ";

    if (!$r = $db->query($q)) {
        die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
    }

    while ($u = $r->fetch_assoc()) {
        $mis_instrumentos[] = (int) $u['instrumentos_id'];
    }
}


// **************************************************************************************
if ( isset($_GET["task"]) && $_GET["task"] == "list" ) {

    // Texto con las localidades marcadas
    $partes = [];
    foreach ($LOCALIDADES as $col => $label) {
        $partes[] = "CASE WHEN p.$col = 1 THEN '" . $db->real_escape_string($label) . "' END";
    }
    $sql_localidades = "CONCAT_WS(', ', " . implode(', ', $partes) . ")";

    $sql = "
        SELECT
            p.*,
            $sql_localidades AS localidades,

            -- Costo total (si no está guardado, se calcula)
            COALESCE(
                NULLIF(p.suma_costo_total, 0),
                COALESCE(p.costos_perfil_prefactibilidad, 0) + COALESCE(p.costos_diseno, 0) + COALESCE(p.costos_ejecucion, 0)
            ) AS total,

            COALESCE(act.total_act, 0)      AS total_actividades,
            COALESCE(act.act_completas, 0)  AS actividades_completas,
            COALESCE(act.monto_completo, 0) AS monto_actividades_completas,

            -- Ubicaciones adicionales (tabla ubicaciones, por numero)
            COALESCE(ub.n_ubic, 0)          AS n_ubicaciones,
            ub.txt_ubic                     AS ubicaciones_adicionales

        FROM proyectos p
        LEFT JOIN (
            SELECT
                proyecto,
                COUNT(*)                                                 AS total_act,
                SUM(CASE WHEN estado = 'Completa' THEN 1 ELSE 0 END)     AS act_completas,
                SUM(CASE WHEN estado = 'Completa' THEN monto ELSE 0 END) AS monto_completo
            FROM actividades
            GROUP BY proyecto
        ) act ON act.proyecto = p.id
        LEFT JOIN (
            SELECT
                numero,
                COUNT(*) AS n_ubic,
                GROUP_CONCAT(CONCAT(COALESCE(descripcion, ''), ' (', COALESCE(latitud, ''), ', ', COALESCE(longitud, ''), ')')
                             ORDER BY id SEPARATOR '; ') AS txt_ubic
            FROM ubicaciones
            GROUP BY numero
        ) ub ON ub.numero = p.numero
        WHERE p.proyectos_status = 1
        ORDER BY p.nombre_iniciativa ASC
    ";

    // file_put_contents('debug.txt', $sql);

    $db->query("SET SESSION group_concat_max_len = 100000");   // lista de ubicaciones sin cortar
    $result = $db->query($sql);

    if (!$result) {
        die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
    }

    $data = [];
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {

        $total      = (float) $row['total'];
        $total_act  = (int)   $row['total_actividades'];
        $act_comp   = (int)   $row['actividades_completas'];
        $monto_comp = (float) $row['monto_actividades_completas'];

        $row['total']                       = $total;
        $row['total_actividades']           = $total_act;
        $row['actividades_completas']       = $act_comp;
        $row['monto_actividades_completas'] = $monto_comp;

        // Avances calculados al vuelo (ya no existen como columnas)
        $row['avance_actividades'] = ($total_act > 0) ? round($act_comp * 100.0 / $total_act, 2) : 0.0;
        $row['avance_financiero']  = ($total > 0)     ? round($monto_comp * 100.0 / $total, 2)   : 0.0;

        // Marcar si este proyecto pertenece a los instrumentos del usuario ÁREA
        $row['es_mio'] = ($perfil == 'ÁREA' && in_array((int) $row['n_inst'], $mis_instrumentos)) ? 1 : 0;

        // Evitar null en DataTables
        foreach ($row as $k => $v) {
            if ($v === null) $row[$k] = '';
        }

        $data[] = $row;
    }

    $results = [
        "sEcho"                => 1,
        "iTotalRecords"        => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData"               => $data
    ];

    echo json_encode($results);
}
// **************************************************************************************




// **************************************************************************************
if ( isset($_POST["task"]) && $_POST["task"] == "update" ) {

    _validar_permiso_area($db, $perfil, $user_id, 'editar');

    $id = (int) ($_POST['proyectos_id'] ?? 0);
    if ($id <= 0) {
        die('ID de proyecto inválido.');
    }

    $antes = audit_snapshot($db, 'proyectos', $id);

    $pdf = _subir_pdf($id);
    _guardar_campos($db, $id, $CAMPOS_EDITABLES, $LOCALIDADES, $user_id);
    _recalcular_totales($db, $id);

    // Si cambió el número de la iniciativa, sus ubicaciones adicionales y fuentes de financiamiento lo siguen
    $despues = audit_snapshot($db, 'proyectos', $id);
    if ($antes && $despues && (string) $antes['numero'] !== (string) $despues['numero']) {
        $n_nuevo = (int) $despues['numero']; $n_viejo = (int) $antes['numero'];
        foreach (['ubicaciones', 'financiamiento', 'gasto'] as $tabla_hija) {
            $stmt = $db->prepare("UPDATE $tabla_hija SET numero = ? WHERE numero = ?");
            $stmt->bind_param('ii', $n_nuevo, $n_viejo);
            $stmt->execute();
        }
    }

    // Auditoría: campos modificados (+ PDF adjunto, que no es columna)
    audit_cambios($db, 'proyectos', $id, $antes, $pdf ? ['pdf_adjunto' => [null, $pdf]] : []);
}
// **************************************************************************************




// **************************************************************************************
if ( isset($_POST["task"]) && $_POST["task"] == "delete" ) {

    $id = $_POST["proyectos_id"];
    if (is_numeric($id)) {
        $antes = audit_snapshot($db, 'proyectos', (int) $id);

        $sql = "DELETE FROM proyectos WHERE id = " . (int) $id;
        if (!$result = $db->query($sql)) {
            die('Hay un error [' . $db->error . ']');
        }

        audit_eliminar($db, 'proyectos', (int) $id, $antes, 'Iniciativa eliminada');

        // Ubicaciones adicionales de la iniciativa eliminada
        if ($antes && !audit_snapshot($db, 'proyectos', (int) $id)) {
            $num = (int) $antes['numero'];
            $r = $db->query("SELECT id FROM ubicaciones WHERE numero = $num");
            while ($u = $r->fetch_assoc()) {
                $u_antes = audit_snapshot($db, 'ubicaciones', $u['id']);
                $db->query("DELETE FROM ubicaciones WHERE id = " . (int) $u['id']);
                audit_eliminar($db, 'ubicaciones', $u['id'], $u_antes, 'Ubicación eliminada junto con la iniciativa');
            }

            // Fuentes de financiamiento de la iniciativa eliminada
            $r = $db->query("SELECT id FROM financiamiento WHERE numero = $num");
            while ($r && $f = $r->fetch_assoc()) {
                $f_antes = audit_snapshot($db, 'financiamiento', $f['id']);
                $db->query("DELETE FROM financiamiento WHERE id = " . (int) $f['id']);
                audit_eliminar($db, 'financiamiento', $f['id'], $f_antes, 'Fuente de financiamiento eliminada junto con la iniciativa');
            }

            // Gastos por etapa de la iniciativa eliminada
            $r = $db->query("SELECT id FROM gasto WHERE numero = $num");
            while ($r && $g = $r->fetch_assoc()) {
                $g_antes = audit_snapshot($db, 'gasto', $g['id']);
                $db->query("DELETE FROM gasto WHERE id = " . (int) $g['id']);
                audit_eliminar($db, 'gasto', $g['id'], $g_antes, 'Gasto por etapa eliminado junto con la iniciativa');
            }
        }
    }
}
// **************************************************************************************




// **************************************************************************************
if ( isset($_POST["task"]) && $_POST["task"] == "insert" ) {

    _validar_permiso_area($db, $perfil, $user_id, 'crear');

    // numero es NOT NULL y UNIQUE: si no viene, se asigna el siguiente disponible
    $numero = (isset($_POST['numero']) && $_POST['numero'] !== '') ? (int) $_POST['numero'] : 0;
    if ($numero <= 0) {
        $r = $db->query("SELECT COALESCE(MAX(numero), 0) + 1 AS siguiente FROM proyectos");
        $numero = (int) $r->fetch_assoc()['siguiente'];
        $_POST['numero'] = $numero;
    }

    // Crea el nuevo registro
    $sql = "INSERT INTO proyectos (numero, nombre_iniciativa) VALUES ($numero, 'Nuevo registro')";
    if (!$result = $db->query($sql)) {
        die('Hay un error [' . $db->error . ']');
    }

    // INSERT_ID para evitar race conditions
    $nuevo_id = $db->insert_id;

    $pdf = _subir_pdf($nuevo_id);
    _guardar_campos($db, $nuevo_id, $CAMPOS_EDITABLES, $LOCALIDADES, $user_id);
    _recalcular_totales($db, $nuevo_id);

    audit_crear($db, 'proyectos', $nuevo_id, 'Iniciativa creada' . ($pdf ? " (PDF: $pdf)" : ''));

    echo $nuevo_id;
}
// **************************************************************************************




// **************************************************************************************
// UBICACIONES ADICIONALES (tabla ubicaciones, enlazada por proyectos.numero)
//   GET  task=ubic_list    proyectos_id
//   POST task=ubic_save    proyectos_id, ubicaciones_id (0 = nueva), descripcion, latitud, longitud
//   POST task=ubic_delete  proyectos_id, ubicaciones_id
// **************************************************************************************
$task_ubic = $_GET['task'] ?? $_POST['task'] ?? '';

if (in_array($task_ubic, ['ubic_list', 'ubic_save', 'ubic_delete'], true)) {

    header('Content-Type: application/json; charset=utf-8');

    $pid = (int) ($_GET['proyectos_id'] ?? $_POST['proyectos_id'] ?? 0);
    $r   = $db->query("SELECT id, numero, n_inst FROM proyectos WHERE id = $pid LIMIT 1");
    $pro = $r ? $r->fetch_assoc() : null;
    if (!$pro) {
        echo json_encode(['ok' => false, 'mensaje' => 'Iniciativa no encontrada.']);
        exit;
    }
    $numero = (int) $pro['numero'];

    // ---------- Listar
    if ($task_ubic === 'ubic_list') {
        $stmt = $db->prepare("SELECT id, descripcion, latitud, longitud FROM ubicaciones WHERE numero = ? ORDER BY id");
        $stmt->bind_param('i', $numero);
        $stmt->execute();
        echo json_encode(['ok' => true, 'data' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
        exit;
    }

    // ---------- Permisos para modificar (mismo criterio que la ficha)
    if (!in_array($perfil, ['ADMINISTRADOR', 'ÁREA'], true)
        || ($perfil === 'ÁREA' && !in_array((int) $pro['n_inst'], $mis_instrumentos, true))) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'mensaje' => 'No tiene permisos para modificar esta iniciativa.']);
        exit;
    }

    $uid = (int) ($_POST['ubicaciones_id'] ?? 0);

    // La ubicación debe pertenecer a esta iniciativa
    if ($uid > 0) {
        $u = audit_snapshot($db, 'ubicaciones', $uid);
        if (!$u || (int) $u['numero'] !== $numero) {
            echo json_encode(['ok' => false, 'mensaje' => 'Ubicación no encontrada.']);
            exit;
        }
    }

    // ---------- Eliminar
    if ($task_ubic === 'ubic_delete') {
        $antes = audit_snapshot($db, 'ubicaciones', $uid);
        $stmt = $db->prepare("DELETE FROM ubicaciones WHERE id = ? AND numero = ?");
        $stmt->bind_param('ii', $uid, $numero);
        $stmt->execute();
        audit_eliminar($db, 'ubicaciones', $uid, $antes, 'Ubicación eliminada');
        echo json_encode(['ok' => true]);
        exit;
    }

    // ---------- Crear / editar
    $desc = mb_substr(trim((string) ($_POST['descripcion'] ?? '')), 0, 100);
    $lat  = str_replace(',', '.', trim((string) ($_POST['latitud'] ?? '')));
    $lng  = str_replace(',', '.', trim((string) ($_POST['longitud'] ?? '')));

    if ($desc === '') {
        echo json_encode(['ok' => false, 'mensaje' => 'Ingrese una descripción.']);
        exit;
    }
    if (!is_numeric($lat) || !is_numeric($lng) || abs((float) $lat) > 90 || abs((float) $lng) > 180) {
        echo json_encode(['ok' => false, 'mensaje' => 'Latitud o longitud no válida.']);
        exit;
    }
    $lat = (string) round((float) $lat, 6);
    $lng = (string) round((float) $lng, 6);

    if ($uid > 0) {
        $antes = audit_snapshot($db, 'ubicaciones', $uid);
        $stmt = $db->prepare("UPDATE ubicaciones SET descripcion = ?, latitud = ?, longitud = ? WHERE id = ? AND numero = ?");
        $stmt->bind_param('sssii', $desc, $lat, $lng, $uid, $numero);
        $stmt->execute();
        audit_cambios($db, 'ubicaciones', $uid, $antes);
    } else {
        $stmt = $db->prepare("INSERT INTO ubicaciones (numero, descripcion, latitud, longitud) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $numero, $desc, $lat, $lng);
        $stmt->execute();
        $uid = (int) $db->insert_id;
        audit_crear($db, 'ubicaciones', $uid, 'Ubicación creada');
    }

    echo json_encode(['ok' => true, 'id' => $uid]);
    exit;
}
// **************************************************************************************




// **************************************************************************************
// FUENTES DE FINANCIAMIENTO Y GASTO POR ETAPA (tablas financiamiento y gasto, enlazadas por proyectos.numero)
//   GET  task=fin_list    proyectos_id
//   POST task=fin_save    proyectos_id, financiamiento_id (0 = nueva), etapa, monto, fuente
//   POST task=fin_delete  proyectos_id, financiamiento_id
//   GET  task=gas_list    proyectos_id
//   POST task=gas_save    proyectos_id, gasto_id (0 = nuevo), etapa, monto, fuente
//   POST task=gas_delete  proyectos_id, gasto_id
// Una etapa puede tener varias filas (una por fuente).
// **************************************************************************************
$TABLAS_POR_ETAPA = [
    'fin' => [
        'tabla'       => 'financiamiento',
        'param_id'    => 'financiamiento_id',
        'no_existe'   => 'Fuente de financiamiento no encontrada.',
        'sin_fuente'  => 'Ingrese la fuente de financiamiento.',
        'msg_crear'   => 'Fuente de financiamiento creada',
        'msg_borrar'  => 'Fuente de financiamiento eliminada',
    ],
    'gas' => [
        'tabla'       => 'gasto',
        'param_id'    => 'gasto_id',
        'no_existe'   => 'Gasto no encontrado.',
        'sin_fuente'  => 'Ingrese la fuente del gasto.',
        'msg_crear'   => 'Gasto por etapa creado',
        'msg_borrar'  => 'Gasto por etapa eliminado',
    ],
];

$task_fin = $_GET['task'] ?? $_POST['task'] ?? '';

if (preg_match('/^(fin|gas)_(list|save|delete)$/', (string) $task_fin, $m_task)) {

    header('Content-Type: application/json; charset=utf-8');

    $cfg    = $TABLAS_POR_ETAPA[$m_task[1]];
    $accion = $m_task[2];
    $tabla  = $cfg['tabla'];   // nombre fijo desde $TABLAS_POR_ETAPA, nunca desde la petición

    $pid = (int) ($_GET['proyectos_id'] ?? $_POST['proyectos_id'] ?? 0);
    $r   = $db->query("SELECT id, numero, n_inst FROM proyectos WHERE id = $pid LIMIT 1");
    $pro = $r ? $r->fetch_assoc() : null;
    if (!$pro) {
        echo json_encode(['ok' => false, 'mensaje' => 'Iniciativa no encontrada.']);
        exit;
    }
    $numero = (int) $pro['numero'];

    // ---------- Listar (ordenado por etapa y luego por orden de ingreso)
    if ($accion === 'list') {
        $stmt = $db->prepare("SELECT id, etapa, monto, fuente FROM $tabla WHERE numero = ? ORDER BY etapa, id");
        $stmt->bind_param('i', $numero);
        $stmt->execute();
        echo json_encode(['ok' => true, 'data' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
        exit;
    }

    // ---------- Permisos para modificar (mismo criterio que la ficha)
    if (!in_array($perfil, ['ADMINISTRADOR', 'ÁREA'], true)
        || ($perfil === 'ÁREA' && !in_array((int) $pro['n_inst'], $mis_instrumentos, true))) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'mensaje' => 'No tiene permisos para modificar esta iniciativa.']);
        exit;
    }

    $fid = (int) ($_POST[$cfg['param_id']] ?? 0);

    // La fila debe pertenecer a esta iniciativa
    // (consulta directa: no depende de que inc_auditoria conozca la tabla)
    if ($fid > 0) {
        $stmt = $db->prepare("SELECT id FROM $tabla WHERE id = ? AND numero = ? LIMIT 1");
        $stmt->bind_param('ii', $fid, $numero);
        $stmt->execute();
        if (!$stmt->get_result()->fetch_assoc()) {
            echo json_encode(['ok' => false, 'mensaje' => $cfg['no_existe']]);
            exit;
        }
    }

    // ---------- Eliminar
    if ($accion === 'delete') {
        $antes = audit_snapshot($db, $tabla, $fid);
        $stmt = $db->prepare("DELETE FROM $tabla WHERE id = ? AND numero = ?");
        $stmt->bind_param('ii', $fid, $numero);
        $stmt->execute();
        audit_eliminar($db, $tabla, $fid, $antes, $cfg['msg_borrar']);
        echo json_encode(['ok' => true]);
        exit;
    }

    // ---------- Crear / editar
    $etapa  = (int) ($_POST['etapa'] ?? 0);
    $monto  = str_replace(',', '.', trim((string) ($_POST['monto'] ?? '')));
    $fuente = mb_substr(trim((string) ($_POST['fuente'] ?? '')), 0, 255);

    if ($etapa <= 0) {
        echo json_encode(['ok' => false, 'mensaje' => 'Seleccione la etapa.']);
        exit;
    }
    $r = $db->query("SELECT COUNT(*) AS c FROM etapas WHERE etapas_id = $etapa");
    if (!$r || (int) $r->fetch_assoc()['c'] === 0) {
        echo json_encode(['ok' => false, 'mensaje' => 'La etapa no existe.']);
        exit;
    }
    if (!is_numeric($monto) || (float) $monto < 0) {
        echo json_encode(['ok' => false, 'mensaje' => 'Monto no válido.']);
        exit;
    }
    $monto = (int) round((float) $monto);
    if ($fuente === '') {
        echo json_encode(['ok' => false, 'mensaje' => $cfg['sin_fuente']]);
        exit;
    }

    if ($fid > 0) {
        $antes = audit_snapshot($db, $tabla, $fid);
        $stmt = $db->prepare("UPDATE $tabla SET etapa = ?, monto = ?, fuente = ? WHERE id = ? AND numero = ?");
        $stmt->bind_param('iisii', $etapa, $monto, $fuente, $fid, $numero);
        $stmt->execute();
        audit_cambios($db, $tabla, $fid, $antes);
    } else {
        $stmt = $db->prepare("INSERT INTO $tabla (numero, etapa, monto, fuente) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiis', $numero, $etapa, $monto, $fuente);
        $stmt->execute();
        $fid = (int) $db->insert_id;
        audit_crear($db, $tabla, $fid, $cfg['msg_crear']);
    }

    echo json_encode(['ok' => true, 'id' => $fid]);
    exit;
}
// **************************************************************************************




// **************************************************************************************
// Funciones
// **************************************************************************************

/**
 * Perfil ÁREA: solo puede crear/editar iniciativas de sus instrumentos (n_inst).
 */
function _validar_permiso_area(mysqli $db, $perfil, int $user_id, string $accion): void {
    if ($perfil != 'ÁREA') return;

    $instrumento_id = (int) ($_POST['n_inst'] ?? 0);
    $q = "SELECT COUNT(*) AS cnt FROM usuarios_areas 
          WHERE usuarios_id = $user_id 
          AND instrumentos_id = $instrumento_id";
    $r   = $db->query($q);
    $row = $r ? $r->fetch_assoc() : ['cnt' => 0];

    if ((int) $row['cnt'] === 0) {
        http_response_code(403);
        die("No tiene permisos para $accion una iniciativa de ese instrumento.");
    }
}


/**
 * Sube el PDF (si viene) usando el id del proyecto en el nombre del archivo.
 */
function _subir_pdf(int $id): ?string {
    if (empty($_FILES["pdf"]["name"])) return null;
    if (strtolower(pathinfo($_FILES["pdf"]["name"], PATHINFO_EXTENSION)) != 'pdf') return null;

    $path      = "../../pdfs/";
    $filename  = $id . "-" . date("YmdHis") . "-";
    $filename .= preg_replace("/[^a-z0-9\_\-\.]/i", '-', basename($_FILES["pdf"]["name"]));
    $target_file = $path . $filename;

    if (file_exists($target_file)) { unlink($target_file); }
    try {
        if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $target_file)) return null;
    } catch (Exception $e) { exit; }
    return $filename;
}


/**
 * Actualiza solo las columnas que vienen en $_POST.
 * Vacío => NULL, excepto localidades (tinyint NOT NULL) que se guardan como 0/1.
 */
function _guardar_campos(mysqli $db, int $id, array $campos, array $localidades, int $user_id): void {

    $q_usr = "SELECT usuarios_nombre FROM usuarios WHERE usuarios_id = $user_id LIMIT 1";
    $r_usr = $db->query($q_usr);
    $nombre_usuario = ($r_usr && $row_usr = $r_usr->fetch_assoc()) ? $row_usr['usuarios_nombre'] : '';
    $proyectos_user = $db->real_escape_string($user_id . ' - ' . $nombre_usuario);

    $sets = [];
    foreach ($campos as $c) {
        if (!array_key_exists($c, $_POST)) continue;

        $v = is_string($_POST[$c]) ? trim($_POST[$c]) : $_POST[$c];

        if (isset($localidades[$c])) {
            $sets[] = "$c = " . ((int) !empty($v));
        } elseif ($v === '' || $v === null) {
            $sets[] = "$c = NULL";
        } else {
            $sets[] = "$c = '" . $db->real_escape_string($v) . "'";
        }
    }
    $sets[] = "proyectos_user = '$proyectos_user'";

    $sql = "UPDATE proyectos SET " . implode(",\n", $sets) . " WHERE id = $id";

    // file_put_contents('debug.txt', $sql);

    if (!$db->query($sql)) {
        die('Hay un error [' . $db->error . ']');
    }
}


/**
 * Recalcula las sumas de costos del proyecto.
 */
function _recalcular_totales(mysqli $db, int $id): void {
    $sql = "
        UPDATE proyectos SET
            suma_costo_prefactibilidad_y_diseno =
                COALESCE(costos_perfil_prefactibilidad, 0) + COALESCE(costos_diseno, 0),
            suma_costo_total =
                COALESCE(costos_perfil_prefactibilidad, 0) + COALESCE(costos_diseno, 0) + COALESCE(costos_ejecucion, 0),
            gasto_total =
                COALESCE(gasto_perfil_prefactibilidad, 0) + COALESCE(gasto_diseno, 0) + COALESCE(gasto_ejecucion, 0)
        WHERE id = $id
    ";
    $db->query($sql);
}
// **************************************************************************************
?>
