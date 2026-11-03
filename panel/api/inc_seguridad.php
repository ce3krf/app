<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-23
//
// Seguridad común para todas las APIs.
// Se incluye justo después de conectar a la base de datos ($db).
//
// Verifica que:
//   1. Exista una sesión iniciada con login
//   2. El usuario siga existiendo, esté activo y no haya cambiado su contraseña
//
// Opcional, antes de incluir este archivo:
//   $API_SOLO_ADMIN = true;   -> solo perfil ADMINISTRADOR
// ************************************************************

if (session_status() === PHP_SESSION_NONE) { @session_start(); }

function _api_rechazar(int $code, string $msg): void {
    http_response_code($code);
    header('Content-Type: text/plain; charset=utf-8');
    exit($msg);
}

// 1. Sesión con login
if (($_SESSION['logeado'] ?? '') !== 'si' || empty($_SESSION['net_fulltrust_fas_id'])) {
    _api_rechazar(401, 'Sesión no válida. Inicie sesión nuevamente.');
}

// 2. Usuario vigente
if (!isset($db) || !($db instanceof mysqli)) {
    _api_rechazar(500, 'Sin conexión a la base de datos.');
}

$_api_uid  = (int) $_SESSION['net_fulltrust_fas_id'];
$_api_pass = (string) ($_SESSION['net_fulltrust_fas_users_password'] ?? '');

$_api_stmt = $db->prepare("SELECT usuarios_status, usuarios_profile FROM usuarios
                           WHERE usuarios_id = ? AND usuarios_password = ? LIMIT 1");
$_api_stmt->bind_param('is', $_api_uid, $_api_pass);
$_api_stmt->execute();
$_api_usr = $_api_stmt->get_result()->fetch_assoc();
$_api_stmt->close();

if (!$_api_usr || (int) $_api_usr['usuarios_status'] !== 1) {
    _api_rechazar(401, 'Usuario no válido o inactivo.');
}

// El perfil se toma siempre de la base de datos (por si fue cambiado)
$_SESSION['usuarios_profile'] = $_api_usr['usuarios_profile'];

// 3. Solo administradores (si la API lo pide)
if (!empty($API_SOLO_ADMIN) && $_api_usr['usuarios_profile'] !== 'ADMINISTRADOR') {
    _api_rechazar(403, 'Sin permisos.');
}

unset($_api_uid, $_api_pass, $_api_stmt, $_api_usr);
