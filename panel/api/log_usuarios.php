<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-23
//
// API "Log de usuarios" (solo lectura, solo ADMINISTRADOR)
//   task=usuarios  -> lista de usuarios para el filtro
//   task=list      -> actividad (una fila por acción) según filtros
//   task=exportado -> deja constancia en la auditoría de que se exportó a Excel
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

$db->set_charset('utf8mb4');

$task   = $_GET['task'] ?? $_POST['task'] ?? '';
$LIMITE = 20000;   // máximo de filas por consulta

header('Content-Type: application/json; charset=utf-8');


// **************************************************************************************
if ($task === 'usuarios') {
    // Usuarios actuales + usuarios que aparecen en el log (aunque hayan sido eliminados)
    // (dos consultas separadas: las tablas pueden tener collations distintas)
    $data = [];

    $r = $db->query("SELECT usuarios_id, usuarios_nombre, usuarios_userid FROM usuarios");
    while ($row = $r->fetch_assoc()) {
        $data[(int) $row['usuarios_id']] = [
            'id' => (int) $row['usuarios_id'], 'nombre' => $row['usuarios_nombre'],
            'login' => $row['usuarios_userid'], 'eliminado' => 0,
        ];
    }

    $r = $db->query("SELECT usuario_id, MAX(usuario_nombre) AS nombre, MAX(usuario_login) AS login
                     FROM auditoria_log WHERE usuario_id IS NOT NULL GROUP BY usuario_id");
    while ($row = $r->fetch_assoc()) {
        $id = (int) $row['usuario_id'];
        if (!isset($data[$id])) {
            $data[$id] = ['id' => $id, 'nombre' => $row['nombre'], 'login' => $row['login'], 'eliminado' => 1];
        }
    }

    $data = array_values($data);
    usort($data, function ($a, $b) {
        return strcasecmp((string) ($a['nombre'] ?: $a['login']), (string) ($b['nombre'] ?: $b['login']));
    });
    echo json_encode($data);
    exit;
}


// **************************************************************************************
if ($task === 'list') {

    $w = ['a.campo IS NULL'];   // una fila por acción (sin el detalle campo por campo)
    $tipos = ''; $vals = [];

    if (!empty($_GET['usuario_id'])) { $w[] = 'a.usuario_id = ?'; $tipos .= 'i'; $vals[] = (int) $_GET['usuario_id']; }
    if (!empty($_GET['desde']))      { $w[] = 'a.fecha_hora >= ?'; $tipos .= 's'; $vals[] = $_GET['desde'] . ' 00:00:00'; }
    if (!empty($_GET['hasta']))      { $w[] = 'a.fecha_hora <= ?'; $tipos .= 's'; $vals[] = $_GET['hasta'] . ' 23:59:59'; }
    if (!empty($_GET['accion']))     { $w[] = 'a.accion = ?';      $tipos .= 's'; $vals[] = $_GET['accion']; }
    if (empty($_GET['incluir_ver']) && empty($_GET['accion'])) { $w[] = "a.accion <> 'VER'"; }

    // En EDITAR, el detalle ya trae la lista de campos modificados
    $sql = "SELECT a.fecha_hora, a.usuario_nombre, a.usuario_login, a.perfil, a.accion, a.opcion,
                   a.tabla, a.registro_id, a.registro_desc, a.detalle, a.ip
            FROM auditoria_log a
            WHERE " . implode(' AND ', $w) . "
            ORDER BY a.id DESC
            LIMIT " . ($LIMITE + 1);

    $stmt = $db->prepare($sql);
    if ($tipos !== '') { $stmt->bind_param($tipos, ...$vals); }
    $stmt->execute();
    $r = $stmt->get_result();

    $data = [];
    while ($row = $r->fetch_assoc()) { $data[] = $row; }

    $truncado = count($data) > $LIMITE;
    if ($truncado) { array_pop($data); }

    echo json_encode(['data' => $data, 'truncado' => $truncado, 'limite' => $LIMITE]);
    exit;
}


// **************************************************************************************
if ($task === 'exportado') {
    require_once __DIR__ . '/../inc_auditoria.php';
    $detalle = 'Exportó log de usuarios a Excel';
    if (!empty($_POST['filtro'])) { $detalle .= ' (' . mb_substr((string) $_POST['filtro'], 0, 300) . ')'; }
    audit_registrar($db, 'VER', ['opcion' => 'log_usuarios.php', 'detalle' => $detalle]);
    echo json_encode(['ok' => true]);
    exit;
}

echo json_encode(['error' => 'Tarea no válida']);
