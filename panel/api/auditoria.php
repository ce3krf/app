<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-23
//
// API de consulta del log de auditoría (solo lectura, solo ADMINISTRADOR)
//   task=list     -> DataTables server-side
//   task=filtros  -> valores para los selectores
//   task=export   -> Excel (.xlsx) con los filtros aplicados
//   task=vaciar   -> borra todo el log (POST, con respuesta mes * año)
// ************************************************************

@session_start();
include("../../conf/db.php");
$API_SOLO_ADMIN = true;
require __DIR__ . '/inc_seguridad.php';

if (
    (!isset($_GET["token"]) || $_GET["token"] != session_id()) &&
    (!isset($_POST["token"]) || $_POST["token"] != session_id())
) {
    exit;
}

if (($_SESSION['usuarios_profile'] ?? '') !== 'ADMINISTRADOR') {
    http_response_code(403);
    exit('Sin permisos');
}

$db->set_charset('utf8mb4');

$task = $_GET['task'] ?? $_POST['task'] ?? '';


// **************************************************************************************
// Construye el WHERE a partir de los filtros (siempre con parámetros)
// **************************************************************************************
function _filtros(mysqli $db, array $in): array {
    $w = []; $tipos = ''; $vals = [];
    $add = function ($sql, $t, $v) use (&$w, &$tipos, &$vals) { $w[] = $sql; $tipos .= $t; $vals[] = $v; };

    if (!empty($in['desde']))       $add('fecha_hora >= ?', 's', $in['desde'] . ' 00:00:00');
    if (!empty($in['hasta']))       $add('fecha_hora <= ?', 's', $in['hasta'] . ' 23:59:59');
    if (!empty($in['usuario_id']))  $add('usuario_id = ?', 'i', (int) $in['usuario_id']);
    if (!empty($in['accion']))      $add('accion = ?', 's', $in['accion']);
    if (!empty($in['tabla']))       $add('tabla = ?', 's', $in['tabla']);
    if (!empty($in['opcion']))      $add('opcion = ?', 's', $in['opcion']);
    if (isset($in['registro_id']) && $in['registro_id'] !== '') $add('registro_id = ?', 's', $in['registro_id']);
    if (!empty($in['proyecto_id'])) $add('proyecto_id = ?', 'i', (int) $in['proyecto_id']);
    if (empty($in['incluir_ver']) && empty($in['accion'])) $w[] = "accion <> 'VER'";

    // Vista "eventos": una fila por acción. Vista "detalle": campo por campo.
    if (($in['vista'] ?? 'detalle') === 'eventos') {
        $w[] = 'campo IS NULL';
    } else {
        $w[] = "(campo IS NOT NULL OR accion NOT IN ('CREAR','EDITAR','ELIMINAR'))";
    }

    $q = trim($in['buscar'] ?? '');
    if ($q !== '') {
        $like = '%' . $q . '%';
        $w[] = '(usuario_nombre LIKE ? OR usuario_login LIKE ? OR registro_desc LIKE ? OR campo LIKE ?
                 OR valor_anterior LIKE ? OR valor_nuevo LIKE ? OR detalle LIKE ? OR opcion LIKE ?)';
        $tipos .= str_repeat('s', 8);
        array_push($vals, $like, $like, $like, $like, $like, $like, $like, $like);
    }

    return [$w ? 'WHERE ' . implode(' AND ', $w) : '', $tipos, $vals];
}

function _query(mysqli $db, string $sql, string $tipos, array $vals): mysqli_result {
    $stmt = $db->prepare($sql);
    if ($tipos !== '') { $stmt->bind_param($tipos, ...$vals); }
    $stmt->execute();
    return $stmt->get_result();
}

$COLUMNAS = 'id, fecha_hora, operacion_id, usuario_id, usuario_login, usuario_nombre, perfil, ip,
             accion, opcion, script, tabla, registro_id, registro_desc, proyecto_id,
             campo, valor_anterior, valor_nuevo, detalle';


// **************************************************************************************
if ($task === 'list') {

    $in = $_GET;
    $in['buscar'] = $_GET['search']['value'] ?? '';
    [$where, $tipos, $vals] = _filtros($db, $in);

    $start  = max(0, (int) ($_GET['start'] ?? 0));
    $length = (int) ($_GET['length'] ?? 50);
    if ($length <= 0 || $length > 500) $length = 50;

    $total = (int) $db->query("SELECT COUNT(*) c FROM auditoria_log")->fetch_assoc()['c'];
    $filtrados = (int) _query($db, "SELECT COUNT(*) c FROM auditoria_log $where", $tipos, $vals)->fetch_assoc()['c'];

    $r = _query($db, "SELECT $COLUMNAS FROM auditoria_log $where ORDER BY id DESC LIMIT $start, $length", $tipos, $vals);
    $data = [];
    while ($row = $r->fetch_assoc()) {
        foreach (['valor_anterior', 'valor_nuevo'] as $c) {
            if ($row[$c] !== null && mb_strlen($row[$c]) > 2000) {
                $row[$c] = mb_substr($row[$c], 0, 2000) . '…';
            }
        }
        $data[] = $row;
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'draw'            => (int) ($_GET['draw'] ?? 1),
        'recordsTotal'    => $total,
        'recordsFiltered' => $filtrados,
        'data'            => $data,
    ]);
    exit;
}


// **************************************************************************************
if ($task === 'filtros') {
    $usuarios = [];
    $r = $db->query("SELECT DISTINCT usuario_id, usuario_nombre, usuario_login FROM auditoria_log
                     WHERE usuario_id IS NOT NULL ORDER BY usuario_nombre");
    while ($row = $r->fetch_assoc()) { $usuarios[$row['usuario_id']] = $row; }

    // Tablas y opciones (pantallas): las que existan en el log en este momento
    $tablas = [];
    $r = $db->query("SELECT DISTINCT tabla FROM auditoria_log WHERE tabla IS NOT NULL AND tabla <> '' ORDER BY tabla");
    while ($row = $r->fetch_assoc()) { $tablas[] = $row['tabla']; }

    $opciones = [];
    $r = $db->query("SELECT DISTINCT opcion FROM auditoria_log WHERE opcion IS NOT NULL AND opcion <> '' ORDER BY opcion");
    while ($row = $r->fetch_assoc()) { $opciones[] = $row['opcion']; }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['usuarios' => array_values($usuarios), 'tablas' => $tablas, 'opciones' => $opciones]);
    exit;
}


// **************************************************************************************
if ($task === 'export') {
    [$where, $tipos, $vals] = _filtros($db, $_GET);

    // Registrar que alguien exportó la auditoría
    require_once __DIR__ . '/../inc_auditoria.php';
    audit_registrar($db, 'VER', ['opcion' => 'auditoria.php', 'detalle' => 'Exportó auditoría a Excel']);

    require_once __DIR__ . '/inc_xlsx.php';

    $r = _query($db, "SELECT $COLUMNAS FROM auditoria_log $where ORDER BY id DESC LIMIT 200000", $tipos, $vals);

    $xlsx = new XlsxSimple('Auditoría', [
        'ID', 'Fecha y hora', 'Operación', 'ID usuario', 'Login', 'Usuario', 'Perfil', 'IP',
        'Acción', 'Opción', 'Script', 'Tabla', 'ID registro', 'Registro', 'ID iniciativa',
        'Campo', 'Valor anterior', 'Valor nuevo', 'Detalle',
    ], [9, 19, 18, 10, 14, 24, 15, 15, 16, 24, 24, 14, 11, 34, 12, 22, 40, 40, 45]);

    $enteros = ['id', 'usuario_id', 'proyecto_id'];
    while ($row = $r->fetch_assoc()) {
        foreach ($enteros as $c) {
            if ($row[$c] !== null && $row[$c] !== '') { $row[$c] = (int) $row[$c]; }
        }
        $xlsx->fila($row);
    }

    $xlsx->descargar('auditoria_' . date('Ymd_His') . '.xlsx');
    exit;
}


// **************************************************************************************
// Vaciar todo el log. Confirmación: respuesta = mes actual * año actual (hora de Chile)
// **************************************************************************************
if ($task === 'vaciar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $ahora    = new DateTime('now', new DateTimeZone('America/Santiago'));
    $esperado = (int) $ahora->format('n') * (int) $ahora->format('Y');
    $resp     = trim((string) ($_POST['respuesta'] ?? ''));

    if ($resp === '' || !ctype_digit($resp) || (int) $resp !== $esperado) {
        echo json_encode(['ok' => false, 'mensaje' => 'Respuesta incorrecta. El log no se vació.']);
        exit;
    }

    try {
        $total = (int) $db->query("SELECT COUNT(*) c FROM auditoria_log")->fetch_assoc()['c'];
        $db->query("DELETE FROM auditoria_log");
    } catch (\Throwable $e) {
        echo json_encode(['ok' => false, 'mensaje' => 'No se pudo vaciar el log (¿la tabla está protegida con triggers?): ' . $e->getMessage()]);
        exit;
    }

    // Queda constancia de quién vació el log y cuántos registros se borraron
    require_once __DIR__ . '/../inc_auditoria.php';
    audit_registrar($db, 'VACIAR_LOG', [
        'opcion'  => 'auditoria.php',
        'tabla'   => 'auditoria_log',
        'detalle' => "Log vaciado: $total registro(s) eliminado(s)",
    ]);

    echo json_encode(['ok' => true, 'mensaje' => "Se eliminaron $total registro(s) del log."]);
    exit;
}

