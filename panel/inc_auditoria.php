<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-23
//
// Log de auditoría. Registra en la tabla auditoria_log:
//   - ingresos, salidas e intentos fallidos de login
//   - pantallas visitadas
//   - registros creados, editados (campo por campo) y eliminados
//
// Uso típico en una API:
//   $antes = audit_snapshot($db, 'areas', $id);   // antes del UPDATE / DELETE
//   ... UPDATE ...
//   audit_cambios($db, 'areas', $id, $antes);     // registra solo lo que cambió
//
//   ... DELETE ...
//   audit_eliminar($db, 'areas', $id, $antes);
//
//   ... INSERT ...
//   audit_crear($db, 'areas', $nuevo_id);
//
// El log nunca interrumpe la aplicación: si falla, se ignora en silencio.
// ************************************************************

if (!defined('AUDITORIA_CARGADA')) {
define('AUDITORIA_CARGADA', 1);

if (session_status() === PHP_SESSION_NONE) { @session_start(); }


// ------------------------------------------------------------
// Configuración por tabla
//   pk     = columna clave primaria
//   desc   = columnas que describen el registro de forma legible
//   padre  = columna que apunta a la iniciativa (proyectos.id)
//   padre_numero = columna que apunta a la iniciativa por su número (proyectos.numero)
// ------------------------------------------------------------
$GLOBALS['AUDIT_TABLAS'] = [
    'proyectos'      => ['pk' => 'id',              'desc' => ['numero', 'nombre_iniciativa'], 'padre' => 'id'],
    'actividades'    => ['pk' => 'id',              'desc' => ['actividad'],                   'padre' => 'proyecto'],
    'indicadores'    => ['pk' => 'indicadores_id',  'desc' => ['indicadores_nombre'],          'padre' => 'indicadores_iniciativa'],
    'areas'          => ['pk' => 'id',              'desc' => ['area']],
    'etapas'         => ['pk' => 'etapas_id',       'desc' => ['etapas_descripcion']],
    'instrumentos'   => ['pk' => 'instrumentos_id', 'desc' => ['instrumentos_descripcion']],
    'procesos'       => ['pk' => 'procesos_id',     'desc' => ['procesos_descripcion']],
    'tipo'           => ['pk' => 'id',              'desc' => ['tipo']],
    'sectores'       => ['pk' => 'sector_id',       'desc' => ['sector_descripcion']],
    'subsectores'    => ['pk' => 'subsector_id',    'desc' => ['subsector_codigo', 'subsector_descripcion']],
    'patentes'       => ['pk' => 'id',              'desc' => ['ROL', 'NOMBRE']],
    'peredifica'     => ['pk' => 'id',              'desc' => ['rol', 'nombre']],
    'usuarios'       => ['pk' => 'usuarios_id',     'desc' => ['usuarios_userid', 'usuarios_nombre']],
    'ubicaciones'    => ['pk' => 'id',              'desc' => ['descripcion'],                 'padre_numero' => 'numero'],
];

// Campos cuyo valor nunca se guarda (solo se registra que cambió)
$GLOBALS['AUDIT_OCULTOS'] = ['usuarios_password'];

// Campos técnicos que se actualizan solos y no aportan a la auditoría
$GLOBALS['AUDIT_IGNORAR'] = [
    'proyectos_user', 'suma_costo_prefactibilidad_y_diseno', 'suma_costo_total',
    'usuarios_updated', 'last_login', 'actualizado', 'creado', 'indicadores_user',
];


// ------------------------------------------------------------
// Utilidades internas
// ------------------------------------------------------------

/** Mismo id para todas las filas generadas en una misma petición. */
function audit_operacion_id(): string {
    static $id = null;
    if ($id === null) { $id = bin2hex(random_bytes(8)); }
    return $id;
}

function audit_ip(): string {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    $ip = trim(explode(',', $ip)[0]);
    return substr($ip, 0, 45);
}

/** Archivo que se está ejecutando, ej. "api/api_proyectos.php" o "proyectos.php". */
function audit_script(): string {
    $s = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = basename(dirname($s));
    return ($dir === 'api' ? 'api/' : '') . basename($s);
}

/** Pantalla desde donde actuó el usuario. En las APIs se toma del referer. */
function audit_opcion(): string {
    if (strpos(audit_script(), 'api/') !== 0) {
        return basename($_SERVER['SCRIPT_NAME'] ?? '');
    }
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $path = $ref ? parse_url($ref, PHP_URL_PATH) : '';
    return $path ? basename($path) : '';
}

function audit_cfg(string $tabla): array {
    return $GLOBALS['AUDIT_TABLAS'][$tabla] ?? ['pk' => 'id', 'desc' => []];
}

function audit_desc(string $tabla, ?array $fila): ?string {
    if (!$fila) return null;
    $partes = [];
    foreach (audit_cfg($tabla)['desc'] as $c) {
        if (isset($fila[$c]) && $fila[$c] !== '') { $partes[] = $fila[$c]; }
    }
    return $partes ? mb_substr(implode(' - ', $partes), 0, 255) : null;
}

function audit_padre(string $tabla, ?array $fila, ?mysqli $db = null): ?int {
    $cn = audit_cfg($tabla)['padre_numero'] ?? null;
    if ($cn && $db && $fila && isset($fila[$cn]) && is_numeric($fila[$cn])) {
        try {
            $r = $db->query("SELECT id FROM proyectos WHERE numero = " . (int) $fila[$cn] . " LIMIT 1");
            $p = $r ? $r->fetch_assoc() : null;
            return $p ? (int) $p['id'] : null;
        } catch (\Throwable $e) { return null; }
    }
    $c = audit_cfg($tabla)['padre'] ?? null;
    if (!$c || !$fila || !isset($fila[$c]) || !is_numeric($fila[$c])) return null;
    return (int) $fila[$c];
}

function audit_valor(string $campo, $v) {
    if ($v === null) return null;
    if (in_array($campo, $GLOBALS['AUDIT_OCULTOS'], true)) return '[oculto]';
    return (string) $v;
}


// ------------------------------------------------------------
// Función base: inserta una fila en el log
// ------------------------------------------------------------
function audit_registrar(mysqli $db, string $accion, array $d = []): void {
    try {
        // Datos del usuario: los de la sesión, salvo que se indiquen explícitamente (incluso como null)
        $usr = function (string $k, string $ses) use ($d) {
            return array_key_exists($k, $d) ? $d[$k] : ($_SESSION[$ses] ?? null);
        };

        $f = [
            'operacion_id'   => audit_operacion_id(),
            'usuario_id'     => $usr('usuario_id',     'net_fulltrust_fas_id'),
            'usuario_login'  => $usr('usuario_login',  'net_fulltrust_fas_users_id'),
            'usuario_nombre' => $usr('usuario_nombre', 'net_fulltrust_fas_users_name'),
            'perfil'         => $usr('perfil',         'usuarios_profile'),
            'ip'             => audit_ip(),
            'user_agent'     => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'accion'         => $accion,
            'opcion'         => mb_substr($d['opcion'] ?? audit_opcion(), 0, 100),
            'script'         => audit_script(),
            'tabla'          => $d['tabla']          ?? null,
            'registro_id'    => isset($d['registro_id']) ? (string) $d['registro_id'] : null,
            'registro_desc'  => $d['registro_desc']  ?? null,
            'proyecto_id'    => $d['proyecto_id']    ?? null,
            'campo'          => $d['campo']          ?? null,
            'valor_anterior' => $d['valor_anterior'] ?? null,
            'valor_nuevo'    => $d['valor_nuevo']    ?? null,
            'detalle'        => isset($d['detalle']) ? mb_substr($d['detalle'], 0, 500) : null,
        ];

        // Hora de Chile, independiente de la zona horaria del servidor MySQL
        $f['fecha_hora'] = (new DateTime('now', new DateTimeZone('America/Santiago')))->format('Y-m-d H:i:s');

        $cols = implode(', ', array_keys($f));
        $ph   = implode(', ', array_fill(0, count($f), '?'));
        $stmt = $db->prepare("INSERT INTO auditoria_log ($cols) VALUES ($ph)");
        if (!$stmt) return;

        $vals  = array_values($f);
        $tipos = str_repeat('s', count($vals));
        $stmt->bind_param($tipos, ...$vals);
        $stmt->execute();
        $stmt->close();
    } catch (\Throwable $e) {
        // El log nunca debe romper la aplicación
    }
}


// ------------------------------------------------------------
// Lectura del registro (foto del "antes")
// ------------------------------------------------------------
function audit_snapshot(mysqli $db, string $tabla, $id): ?array {
    if (!isset($GLOBALS['AUDIT_TABLAS'][$tabla]) || $id === null || $id === '') return null;
    try {
        $pk   = audit_cfg($tabla)['pk'];
        $stmt = $db->prepare("SELECT * FROM `$tabla` WHERE `$pk` = ? LIMIT 1");
        if (!$stmt) return null;
        $id = (string) $id;
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $fila = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $fila ?: null;
    } catch (\Throwable $e) {
        return null;
    }
}


// ------------------------------------------------------------
// CREAR: un evento + una fila por cada campo con valor
// ------------------------------------------------------------
function audit_crear(mysqli $db, string $tabla, $id, string $detalle = 'Registro creado'): void {
    $fila = audit_snapshot($db, $tabla, $id);
    $base = [
        'tabla'         => $tabla,
        'registro_id'   => $id,
        'registro_desc' => audit_desc($tabla, $fila),
        'proyecto_id'   => audit_padre($tabla, $fila, $db),
    ];
    audit_registrar($db, 'CREAR', $base + ['detalle' => $detalle]);

    if (!$fila) return;
    $pk = audit_cfg($tabla)['pk'];
    foreach ($fila as $campo => $v) {
        if ($campo === $pk || in_array($campo, $GLOBALS['AUDIT_IGNORAR'], true)) continue;
        if ($v === null || $v === '' || $v === '0') continue;   // valores por defecto: no aportan
        audit_registrar($db, 'CREAR', $base + [
            'campo'       => $campo,
            'valor_nuevo' => audit_valor($campo, $v),
        ]);
    }
}


// ------------------------------------------------------------
// EDITAR: compara antes vs después y registra solo lo que cambió
// Devuelve la cantidad de campos modificados.
// ------------------------------------------------------------
function audit_cambios(mysqli $db, string $tabla, $id, ?array $antes, array $extra = []): int {
    $despues = audit_snapshot($db, $tabla, $id);
    if (!$despues) return 0;
    $antes = $antes ?: [];

    $cambios = [];
    foreach ($despues as $campo => $nuevo) {
        if (in_array($campo, $GLOBALS['AUDIT_IGNORAR'], true)) continue;
        $viejo = array_key_exists($campo, $antes) ? $antes[$campo] : null;
        $v1 = $viejo === null ? null : (string) $viejo;
        $v2 = $nuevo === null ? null : (string) $nuevo;
        if ($v1 !== $v2) { $cambios[$campo] = [$v1, $v2]; }
    }

    // Cambios adicionales que no son columnas de la tabla (ej. instrumentos asignados)
    foreach ($extra as $campo => [$v1, $v2]) {
        if ($v1 !== $v2) { $cambios[$campo] = [$v1, $v2]; }
    }

    if (!$cambios) return 0;

    $base = [
        'tabla'         => $tabla,
        'registro_id'   => $id,
        'registro_desc' => audit_desc($tabla, $despues),
        'proyecto_id'   => audit_padre($tabla, $despues, $db),
    ];
    audit_registrar($db, 'EDITAR', $base + [
        'detalle' => count($cambios) . ' campo(s) modificado(s): ' . implode(', ', array_keys($cambios)),
    ]);
    foreach ($cambios as $campo => [$v1, $v2]) {
        audit_registrar($db, 'EDITAR', $base + [
            'campo'          => $campo,
            'valor_anterior' => audit_valor($campo, $v1),
            'valor_nuevo'    => audit_valor($campo, $v2),
        ]);
    }
    return count($cambios);
}


// ------------------------------------------------------------
// ELIMINAR: guarda una copia completa de lo que se borró
// ------------------------------------------------------------
function audit_eliminar(mysqli $db, string $tabla, $id, ?array $antes, string $detalle = 'Registro eliminado'): void {
    if (!$antes) return;                               // no existía
    if (audit_snapshot($db, $tabla, $id)) return;       // no se borró (ej. condición no cumplida)

    $base = [
        'tabla'         => $tabla,
        'registro_id'   => $id,
        'registro_desc' => audit_desc($tabla, $antes),
        'proyecto_id'   => audit_padre($tabla, $antes, $db),
    ];
    audit_registrar($db, 'ELIMINAR', $base + ['detalle' => $detalle]);

    $pk = audit_cfg($tabla)['pk'];
    foreach ($antes as $campo => $v) {
        if ($campo === $pk || $v === null || $v === '') continue;
        audit_registrar($db, 'ELIMINAR', $base + [
            'campo'          => $campo,
            'valor_anterior' => audit_valor($campo, $v),
        ]);
    }
}


// ------------------------------------------------------------
// VER: pantalla visitada (se llama desde functions.php)
// ------------------------------------------------------------
function audit_ver_pagina(mysqli $db): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') return;
    if (strpos(audit_script(), 'api/') === 0) return;
    if (empty($_SESSION['net_fulltrust_fas_id'])) return;

    $qs = $_SERVER['QUERY_STRING'] ?? '';
    parse_str($qs, $params);
    unset($params['token']);

    $proyecto_id = (isset($params['proyectos_id']) && is_numeric($params['proyectos_id']) && (int) $params['proyectos_id'] > 0)
        ? (int) $params['proyectos_id'] : null;

    audit_registrar($db, 'VER', [
        'proyecto_id' => $proyecto_id,
        'detalle'     => $params ? http_build_query($params) : null,
    ]);
}

} // AUDITORIA_CARGADA
