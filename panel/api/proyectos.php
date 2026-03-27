<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-03-18
// ************************************************************

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

@session_start();

include("../../conf/db.php");		
include('functions.php');


if ( 
    (!isset($_GET["token"]) || $_GET["token"] != session_id()) && 
    (!isset($_POST["token"]) || $_POST["token"] != session_id()) 
) {
    exit;
}


$q = "set names utf8";
if(!$preg = $db->query($q)){
    die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
}


$user_id = $_SESSION["net_fulltrust_fas_id"];
$perfil  = $_SESSION['usuarios_profile'];

$sqla = "";

if ($perfil == 'ÁREA') {

    $q = "
        SELECT GROUP_CONCAT(instrumentos_id ORDER BY instrumentos_id) AS instrumentos
        FROM usuarios_areas
        WHERE usuarios_id = $user_id
        AND instrumentos_id IS NOT NULL
    ";

    if (!$r = $db->query($q)) {
        die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
    }

    $u = $r->fetch_assoc();
    $instrumentos_lista = $u['instrumentos'];

    if (!empty($instrumentos_lista)) {
        $sqla = " AND p.instrumento IN (" . $instrumentos_lista . ")";
    } else {
        $sqla = " AND 1 = 0";
    }
}


// **************************************************************************************
if ( isset($_GET["task"]) && $_GET["task"] == "list" ) {

    $sql = "
        SELECT
            p.id,
            p.instrumento,
            p.preseleccionado,
            p.nombre,
            p.plan_maestro,
            p.lineamiento_id,
            p.lineamiento,
            p.objetivo_id,
            p.objetivo,
            p.brecha,
            p.descripcion,
            p.localizacion,
            COALESCE(s.sector_descripcion, '') AS sector,
            p.subsector,
            p.tipo,
            p.impacto_territorial,
            p.foco_turismo,
            p.codigo_idi,
            p.p_diseno,
            p.p_ejecucion,
            -- Presupuesto total = p_diseno + p_ejecucion
            COALESCE(p.p_diseno, 0) + COALESCE(p.p_ejecucion, 0) AS total,
            COALESCE(e.etapas_descripcion, '')   AS etapas_descripcion,
            COALESCE(pr.procesos_descripcion, '') AS procesos_descripcion,
            p.finicio,
            p.ftermino,
            p.fuente,
            COALESCE(a.area, '') AS unidad_responsable,
            p.instituciones_vinculadas,
            p.beneficiarios,
            p.informacion,
            p.lat,
            p.lng,
            p.proyectos_user,
            p.proyectos_created,
            p.proyectos_updated,

            -- Total de actividades del proyecto
            COUNT(act.id) AS total_actividades,

            -- Total de actividades completadas
            SUM(CASE WHEN act.estado = 'Completa' THEN 1 ELSE 0 END) AS actividades_completas,

            -- Avance de implementación (valor guardado, actualizado por _recalcular_avances)
            COALESCE(p.avance_actividades, 0) AS avance_actividades,

            -- Suma de montos de actividades completadas
            COALESCE(SUM(CASE WHEN act.estado = 'Completa' THEN act.monto ELSE 0 END), 0) AS monto_actividades_completas,

            -- Avance financiero (valor guardado, actualizado por _recalcular_avances)
            COALESCE(p.avance_financiero, 0) AS avance_financiero

        FROM proyectos p
        LEFT JOIN sectores s   ON p.sector               = s.sector_id
        LEFT JOIN etapas   e   ON p.etapa                = e.etapas_id
        LEFT JOIN procesos pr  ON p.proceso              = pr.procesos_id
        LEFT JOIN areas    a   ON p.unidad_responsable_id = a.id
        LEFT JOIN actividades act ON act.proyecto = p.id
        WHERE p.proyectos_status = 1
        $sqla
        GROUP BY
            p.id,
            p.instrumento,
            p.preseleccionado,
            p.nombre,
            p.plan_maestro,
            p.lineamiento_id,
            p.lineamiento,
            p.objetivo_id,
            p.objetivo,
            p.brecha,
            p.descripcion,
            p.localizacion,
            s.sector_descripcion,
            p.subsector,
            p.tipo,
            p.impacto_territorial,
            p.foco_turismo,
            p.codigo_idi,
            p.p_diseno,
            p.p_ejecucion,
            e.etapas_descripcion,
            pr.procesos_descripcion,
            p.finicio,
            p.ftermino,
            p.fuente,
            a.area,
            p.instituciones_vinculadas,
            p.beneficiarios,
            p.informacion,
            p.lat,
            p.lng,
            p.proyectos_user,
            p.proyectos_created,
            p.proyectos_updated
        ORDER BY p.nombre ASC
    ";

    // file_put_contents('debug.txt', $sql);

    $result = $db->query($sql);

    if (!$result) {
        die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
    }

    $data = [];
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        // Asegurar que los valores numéricos sean del tipo correcto para JSON
        $row['total']                       = (float) $row['total'];
        $row['avance_actividades']          = (float) $row['avance_actividades'];
        $row['avance_financiero']           = (float) $row['avance_financiero'];
        $row['monto_actividades_completas'] = (float) $row['monto_actividades_completas'];
        $row['total_actividades']           = (int)   $row['total_actividades'];
        $row['actividades_completas']       = (int)   $row['actividades_completas'];
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

    if ($perfil == 'ÁREA') {
        $uid_check      = (int) $_SESSION["net_fulltrust_fas_id"];
        $instrumento_id = (int) $_POST['instrumento'];
        $q_check = "SELECT COUNT(*) AS cnt FROM usuarios_areas 
                    WHERE usuarios_id = $uid_check 
                    AND instrumentos_id = $instrumento_id";
        $r_check  = $db->query($q_check);
        $row_check = $r_check->fetch_assoc();
        if ((int)$row_check['cnt'] === 0) {
            http_response_code(403);
            die('No tiene permisos para editar una iniciativa de ese instrumento.');
        }
    }

    // Subida de PDF
    if (!empty($_FILES["pdf"]["name"])) {
        if (pathinfo($_FILES["pdf"]["name"], PATHINFO_EXTENSION) == 'pdf') {
            $path      = "../../pdfs/";
            $filename  = $_POST["proyectos_id"] . "-" . date("YmdHis") . "-";
            $filename .= preg_replace("/[^a-z0-9\_\-\.]/i", '-', basename($_FILES["pdf"]["name"]));
            $target_file = $path . $filename;
            if (file_exists($target_file)) { unlink($target_file); }
            try {
                move_uploaded_file($_FILES['pdf']['tmp_name'], $target_file);
            } catch (Exception $e) { exit; }
        }
    }

    $uid_upd = (int) $_SESSION["net_fulltrust_fas_id"];
    $q_usr   = "SELECT usuarios_nombre FROM usuarios WHERE usuarios_id = $uid_upd LIMIT 1";
    $r_usr   = $db->query($q_usr);
    $nombre_usuario_upd  = ($r_usr && $row_usr = $r_usr->fetch_assoc()) ? $row_usr['usuarios_nombre'] : '';
    $proyectos_user_val  = mysqli_real_escape_string($db, $uid_upd . ' - ' . $nombre_usuario_upd);

    $esc = fn($v) => mysqli_real_escape_string($db, $_POST[$v] ?? '');

    // Calcular total = p_diseno + p_ejecucion
    $p_diseno   = (float) ($_POST['p_diseno']   ?? 0);
    $p_ejecucion = (float) ($_POST['p_ejecucion'] ?? 0);
    $total_calculado = $p_diseno + $p_ejecucion;

    $sql  = "UPDATE proyectos SET ";
    $sql .= "nombre                  = '" . $esc('nombre')                  . "', ";
    $sql .= "plan_maestro            = '" . $esc('plan_maestro')            . "', ";
    $sql .= "descripcion             = '" . $esc('descripcion')             . "', ";
    $sql .= "instrumento             = '" . $esc('instrumento')             . "', ";
    $sql .= "preseleccionado         = '" . $esc('preseleccionado')         . "', ";
    $sql .= "lineamiento_id          = '" . $esc('lineamiento_id')          . "', ";
    $sql .= "lineamiento             = '" . $esc('lineamiento')             . "', ";
    $sql .= "objetivo_id             = '" . $esc('objetivo_id')             . "', ";
    $sql .= "objetivo                = '" . $esc('objetivo')                . "', ";
    $sql .= "brecha                  = '" . $esc('brecha')                  . "', ";
    $sql .= "localizacion            = '" . $esc('localizacion')            . "', ";
    $sql .= "sector                  = '" . $esc('sector')                  . "', ";
    $sql .= "subsector               = '" . $esc('subsector')               . "', ";
    $sql .= "tipo                    = '" . $esc('tipo')                    . "', ";
    $sql .= "impacto_territorial     = '" . $esc('impacto_territorial')     . "', ";
    $sql .= "foco_turismo            = '" . $esc('foco_turismo')            . "', ";
    $sql .= "codigo_idi              = '" . $esc('codigo_idi')              . "', ";
    $sql .= "p_diseno                = '" . $esc('p_diseno')                . "', ";
    $sql .= "p_ejecucion             = '" . $esc('p_ejecucion')             . "', ";
    $sql .= "total                   = '" . $total_calculado                . "', ";
    $sql .= "etapa                   = '" . $esc('etapa')                   . "', ";
    $sql .= "proceso                 = '" . $esc('proceso')                 . "', ";
    $sql .= "informacion             = '" . $esc('informacion')             . "', ";
    $sql .= "finicio                 = '" . $esc('finicio')                 . "', ";
    $sql .= "ftermino                = '" . $esc('ftermino')                . "', ";
    $sql .= "fuente                  = '" . $esc('fuente')                  . "', ";
    $sql .= "unidad_responsable_id   = '" . $esc('unidad_responsable_id')   . "', ";
    $sql .= "instituciones_vinculadas= '" . $esc('instituciones_vinculadas') . "', ";
    $sql .= "beneficiarios           = '" . $esc('beneficiarios')           . "', ";
    $sql .= "lat                     = '" . $esc('lat')                     . "', ";
    $sql .= "lng                     = '" . $esc('lng')                     . "', ";
    $sql .= "proyectos_user          = '$proyectos_user_val' ";
    $sql .= "WHERE id = '" . mysqli_real_escape_string($db, $_POST['proyectos_id']) . "';";

    // file_put_contents('debug.txt', $sql);

    if (!$result = $db->query($sql)) {
        die('Hay un error [' . $db->error . ']');
    }

    // Recalcular avances y actualizarlos en la tabla
    _recalcular_avances($db, (int) $_POST['proyectos_id']);
}
// **************************************************************************************




// **************************************************************************************
if ( isset($_POST["task"]) && $_POST["task"] == "delete" ) {

    $id = $_POST["proyectos_id"];
    if (is_numeric($id)) {
        $sql = "DELETE FROM proyectos WHERE id = '$id'";
        if (!$result = $db->query($sql)) {
            die('Hay un error [' . $db->error . ']');
        }
    }
}
// **************************************************************************************




// **************************************************************************************
if ( isset($_POST["task"]) && $_POST["task"] == "insert" ) {

    if ($perfil == 'ÁREA') {
        $uid_check2      = (int) $_SESSION["net_fulltrust_fas_id"];
        $instrumento_id2 = (int) $_POST['instrumento'];
        $q_check2 = "SELECT COUNT(*) AS cnt FROM usuarios_areas 
                     WHERE usuarios_id = $uid_check2 
                     AND instrumentos_id = $instrumento_id2";
        $r_check2  = $db->query($q_check2);
        $row_check2 = $r_check2->fetch_assoc();
        if ((int)$row_check2['cnt'] === 0) {
            http_response_code(403);
            die('No tiene permisos para crear una iniciativa en ese instrumento.');
        }
    }

    // Subida de PDF
    if (!empty($_FILES["pdf"]["name"])) {
        if (pathinfo($_FILES["pdf"]["name"], PATHINFO_EXTENSION) == 'pdf') {
            $path      = "../../pdfs/";
            $filename  = $_POST["proyectos_id"] . "-" . date("YmdHis") . "-";
            $filename .= preg_replace("/[^a-z0-9\_\-\.]/i", '-', basename($_FILES["pdf"]["name"]));
            $target_file = $path . $filename;
            if (file_exists($target_file)) { unlink($target_file); }
            try {
                move_uploaded_file($_FILES['pdf']['tmp_name'], $target_file);
            } catch (Exception $e) { exit; }
        }
    }

    // Crea el nuevo registro vacío
    $sql = "INSERT INTO proyectos (nombre) VALUES ('Nuevo registro')";
    if (!$result = $db->query($sql)) {
        die('Hay un error [' . $db->error . ']');
    }

    // Recupera el último id creado con INSERT_ID para evitar race conditions
    $nuevo_id = $db->insert_id;

    $uid_ins = (int) $_SESSION["net_fulltrust_fas_id"];
    $q_usr2  = "SELECT usuarios_nombre FROM usuarios WHERE usuarios_id = $uid_ins LIMIT 1";
    $r_usr2  = $db->query($q_usr2);
    $nombre_usuario_ins = ($r_usr2 && $row_usr2 = $r_usr2->fetch_assoc()) ? $row_usr2['usuarios_nombre'] : '';
    $proyectos_user_ins = mysqli_real_escape_string($db, $uid_ins . ' - ' . $nombre_usuario_ins);

    $esc = fn($v) => mysqli_real_escape_string($db, $_POST[$v] ?? '');

    // Calcular total = p_diseno + p_ejecucion
    $p_diseno    = (float) ($_POST['p_diseno']    ?? 0);
    $p_ejecucion = (float) ($_POST['p_ejecucion'] ?? 0);
    $total_calculado = $p_diseno + $p_ejecucion;

    $sql  = "UPDATE proyectos SET ";
    $sql .= "nombre                  = '" . $esc('nombre')                  . "', ";
    $sql .= "plan_maestro            = '" . $esc('plan_maestro')            . "', ";
    $sql .= "descripcion             = '" . $esc('descripcion')             . "', ";
    $sql .= "instrumento             = '" . $esc('instrumento')             . "', ";
    $sql .= "preseleccionado         = '" . $esc('preseleccionado')         . "', ";
    $sql .= "lineamiento_id          = '" . $esc('lineamiento_id')          . "', ";
    $sql .= "lineamiento             = '" . $esc('lineamiento')             . "', ";
    $sql .= "objetivo_id             = '" . $esc('objetivo_id')             . "', ";
    $sql .= "objetivo                = '" . $esc('objetivo')                . "', ";
    $sql .= "brecha                  = '" . $esc('brecha')                  . "', ";
    $sql .= "localizacion            = '" . $esc('localizacion')            . "', ";
    $sql .= "sector                  = '" . $esc('sector')                  . "', ";
    $sql .= "subsector               = '" . $esc('subsector')               . "', ";
    $sql .= "tipo                    = '" . $esc('tipo')                    . "', ";
    $sql .= "impacto_territorial     = '" . $esc('impacto_territorial')     . "', ";
    $sql .= "foco_turismo            = '" . $esc('foco_turismo')            . "', ";
    $sql .= "codigo_idi              = '" . $esc('codigo_idi')              . "', ";
    $sql .= "p_diseno                = '" . $esc('p_diseno')                . "', ";
    $sql .= "p_ejecucion             = '" . $esc('p_ejecucion')             . "', ";
    $sql .= "total                   = '" . $total_calculado                . "', ";
    $sql .= "etapa                   = '" . $esc('etapa')                   . "', ";
    $sql .= "proceso                 = '" . $esc('proceso')                 . "', ";
    $sql .= "informacion             = '" . $esc('informacion')             . "', ";
    $sql .= "finicio                 = '" . $esc('finicio')                 . "', ";
    $sql .= "ftermino                = '" . $esc('ftermino')                . "', ";
    $sql .= "fuente                  = '" . $esc('fuente')                  . "', ";
    $sql .= "unidad_responsable_id   = '" . $esc('unidad_responsable_id')   . "', ";
    $sql .= "instituciones_vinculadas= '" . $esc('instituciones_vinculadas') . "', ";
    $sql .= "beneficiarios           = '" . $esc('beneficiarios')           . "', ";
    $sql .= "lat                     = '" . $esc('lat')                     . "', ";
    $sql .= "lng                     = '" . $esc('lng')                     . "', ";
    $sql .= "proyectos_user          = '$proyectos_user_ins' ";
    $sql .= "WHERE id = '" . $nuevo_id . "';";

    // file_put_contents('debug.txt', $sql);

    if (!$result = $db->query($sql)) {
        die('Hay un error [' . $db->error . ']');
    } else {
        echo $nuevo_id;
    }
}
// **************************************************************************************




// **************************************************************************************
/**
 * Recalcula y persiste avance_actividades, avance_financiero y total para un proyecto dado.
 * Se llama después de INSERT o UPDATE de proyecto, y también puede invocarse desde
 * el módulo de actividades cuando se modifica una actividad.
 *
 * @param mysqli $db        Conexión activa a la base de datos
 * @param int    $proyecto_id  ID del proyecto a recalcular
 */
function _recalcular_avances(mysqli $db, int $proyecto_id): void {

    // 1. Obtener presupuesto del proyecto
    $q = "SELECT COALESCE(p_diseno,0) + COALESCE(p_ejecucion,0) AS total_presupuesto
          FROM proyectos
          WHERE id = $proyecto_id
          LIMIT 1";
    $r = $db->query($q);
    if (!$r) return;
    $proy = $r->fetch_assoc();
    $total_presupuesto = (float) $proy['total_presupuesto'];

    // 2. Estadísticas de actividades
    $q2 = "
        SELECT
            COUNT(*)                                                    AS total_act,
            SUM(CASE WHEN estado = 'Completa' THEN 1 ELSE 0 END)       AS act_completas,
            COALESCE(SUM(CASE WHEN estado = 'Completa' THEN monto ELSE 0 END), 0) AS monto_completo
        FROM actividades
        WHERE proyecto = $proyecto_id
    ";
    $r2 = $db->query($q2);
    if (!$r2) return;
    $act = $r2->fetch_assoc();

    $total_act     = (int)   $act['total_act'];
    $act_completas = (int)   $act['act_completas'];
    $monto_comp    = (float) $act['monto_completo'];

    // 3. Calcular porcentajes
    $avance_actividades = ($total_act > 0)
        ? round($act_completas * 100.0 / $total_act, 2)
        : 0.0;

    $avance_financiero = ($total_presupuesto > 0)
        ? round($monto_comp * 100.0 / $total_presupuesto, 2)
        : 0.0;

    // 4. Persistir en la tabla proyectos
    $upd = "
        UPDATE proyectos SET
            avance_actividades = $avance_actividades,
            avance_financiero  = $avance_financiero,
            total              = $total_presupuesto
        WHERE id = $proyecto_id
    ";
    $db->query($upd);
}
// **************************************************************************************
?>
