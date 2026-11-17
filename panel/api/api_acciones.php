<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-11-16
// ************************************************************
//
// ACCIONES DE LA RUTA CRÍTICA (tabla acciones, enlazada por acciones.codigo_iniciativa = proyectos.codigo)
//   GET  task=list    proyectos_id
//   GET  task=get     proyectos_id, acciones_id
//   POST task=save    proyectos_id, acciones_id (0 = nueva), nombre_accion, inicio, fin, verificador
//   POST task=delete  proyectos_id, acciones_id
// Siempre responde JSON: { ok: true|false, mensaje?, id?, data? }
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

header('Content-Type: application/json; charset=utf-8');

$db->query("set names utf8mb4");

$user_id = (int) $_SESSION["net_fulltrust_fas_id"];
$perfil  = $_SESSION['usuarios_profile'];

function responder(array $r, int $http = 200): void {
    if ($http !== 200) http_response_code($http);
    echo json_encode($r, JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

// Fecha YYYY-MM-DD válida, o null si viene vacía. false si viene con formato inválido.
function fecha_valida($v) {
    $v = trim((string) $v);
    if ($v === '') return null;
    $d = DateTime::createFromFormat('Y-m-d', $v);
    return ($d && $d->format('Y-m-d') === $v) ? $v : false;
}


$task = $_GET['task'] ?? $_POST['task'] ?? '';
if (!in_array($task, ['list', 'get', 'save', 'delete'], true)) {
    responder(['ok' => false, 'mensaje' => 'Tarea no válida.'], 400);
}


// ---------- Iniciativa (el código se toma siempre de la BD, nunca de la petición)
$pid  = (int) ($_GET['proyectos_id'] ?? $_POST['proyectos_id'] ?? 0);
$stmt = $db->prepare("SELECT id, codigo, n_inst FROM proyectos WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $pid);
$stmt->execute();
$pro = $stmt->get_result()->fetch_assoc();

if (!$pro) {
    responder(['ok' => false, 'mensaje' => 'Iniciativa no encontrada.']);
}
$codigo = trim((string) $pro['codigo']);
if ($codigo === '') {
    responder(['ok' => false, 'mensaje' => 'La iniciativa no tiene código. Asígnele uno antes de registrar acciones.']);
}


// ---------- Listar (ordenado por inicio, igual que la ficha)
if ($task === 'list') {
    $stmt = $db->prepare("
        SELECT id, nombre_accion, inicio, fin, verificador
        FROM acciones
        WHERE codigo_iniciativa = ?
        ORDER BY (inicio IS NULL), inicio, (fin IS NULL), fin, id
    ");
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    responder(['ok' => true, 'data' => $stmt->get_result()->fetch_all(MYSQLI_ASSOC)]);
}


$aid = (int) ($_GET['acciones_id'] ?? $_POST['acciones_id'] ?? 0);

// La acción debe pertenecer a esta iniciativa
if ($aid > 0) {
    $stmt = $db->prepare("SELECT id, codigo_iniciativa, nombre_accion, inicio, fin, verificador FROM acciones WHERE id = ? AND codigo_iniciativa = ? LIMIT 1");
    $stmt->bind_param('is', $aid, $codigo);
    $stmt->execute();
    $accion = $stmt->get_result()->fetch_assoc();
    if (!$accion) {
        responder(['ok' => false, 'mensaje' => 'Acción no encontrada.']);
    }
}


// ---------- Obtener una
if ($task === 'get') {
    if ($aid <= 0) responder(['ok' => false, 'mensaje' => 'Acción no encontrada.']);
    responder(['ok' => true, 'data' => $accion]);
}


// ---------- Permisos para modificar (mismo criterio que la ficha, ubicaciones y financiamiento)
$mis_instrumentos = [];
if ($perfil === 'ÁREA') {
    $r = $db->query("SELECT instrumentos_id FROM usuarios_areas WHERE usuarios_id = $user_id AND instrumentos_id IS NOT NULL");
    if ($r) while ($u = $r->fetch_assoc()) $mis_instrumentos[] = (int) $u['instrumentos_id'];
}
if (!in_array($perfil, ['ADMINISTRADOR', 'ÁREA'], true)
    || ($perfil === 'ÁREA' && !in_array((int) $pro['n_inst'], $mis_instrumentos, true))) {
    responder(['ok' => false, 'mensaje' => 'No tiene permisos para modificar las acciones de esta iniciativa.'], 403);
}


// ---------- Eliminar
if ($task === 'delete') {
    if ($aid <= 0) responder(['ok' => false, 'mensaje' => 'Acción no encontrada.']);

    $antes = audit_snapshot($db, 'acciones', $aid);
    $stmt  = $db->prepare("DELETE FROM acciones WHERE id = ? AND codigo_iniciativa = ?");
    $stmt->bind_param('is', $aid, $codigo);
    $stmt->execute();
    audit_eliminar($db, 'acciones', $aid, $antes, 'Acción eliminada');

    responder(['ok' => true]);
}


// ---------- Crear / editar
$nombre      = mb_substr(trim((string) ($_POST['nombre_accion'] ?? '')), 0, 255);
$verificador = mb_substr(trim((string) ($_POST['verificador'] ?? '')), 0, 255);
$inicio      = fecha_valida($_POST['inicio'] ?? '');
$fin         = fecha_valida($_POST['fin'] ?? '');

if ($nombre === '') {
    responder(['ok' => false, 'mensaje' => 'Ingrese el nombre de la acción.']);
}
if ($inicio === false || $fin === false) {
    responder(['ok' => false, 'mensaje' => 'Las fechas deben tener formato válido (AAAA-MM-DD).']);
}
if ($inicio !== null && $fin !== null && $fin < $inicio) {
    responder(['ok' => false, 'mensaje' => 'La fecha de fin no puede ser anterior a la de inicio.']);
}
if ($verificador === '') $verificador = null;

if ($aid > 0) {
    $antes = audit_snapshot($db, 'acciones', $aid);
    $stmt  = $db->prepare("UPDATE acciones SET nombre_accion = ?, inicio = ?, fin = ?, verificador = ? WHERE id = ? AND codigo_iniciativa = ?");
    $stmt->bind_param('ssssis', $nombre, $inicio, $fin, $verificador, $aid, $codigo);
    if (!$stmt->execute()) responder(['ok' => false, 'mensaje' => 'No se pudo guardar la acción.'], 500);
    audit_cambios($db, 'acciones', $aid, $antes);
} else {
    $stmt = $db->prepare("INSERT INTO acciones (codigo_iniciativa, nombre_accion, inicio, fin, verificador) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('sssss', $codigo, $nombre, $inicio, $fin, $verificador);
    if (!$stmt->execute()) responder(['ok' => false, 'mensaje' => 'No se pudo guardar la acción.'], 500);
    $aid = (int) $db->insert_id;
    audit_crear($db, 'acciones', $aid, 'Acción creada');
}

responder(['ok' => true, 'id' => $aid]);
?>
