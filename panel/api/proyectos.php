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
            p.total,
            COALESCE(e.etapas_descripcion, '')   AS etapas_descripcion,
            COALESCE(pr.procesos_descripcion, '') AS procesos_descripcion,
            p.finicio,
            p.ftermino,
            p.fuente,
            COALESCE(a.area, '') AS unidad_responsable,
            p.instituciones_vinculadas,
            p.beneficiarios,
            p.informacion,
            p.avance_financiero,
            p.avance_actividades,
            p.lat,
            p.lng,
            p.proyectos_user,
            p.proyectos_created,
            p.proyectos_updated
        FROM proyectos p
        LEFT JOIN sectores s  ON p.sector               = s.sector_id
        LEFT JOIN etapas   e  ON p.etapa                = e.etapas_id
        LEFT JOIN procesos pr ON p.proceso               = pr.procesos_id
        LEFT JOIN areas    a  ON p.unidad_responsable_id = a.id
        WHERE p.proyectos_status = 1
        $sqla
        ORDER BY p.nombre ASC
    ";

    // file_put_contents('debug.txt', $sql);

    $result = $db->query($sql);

    if (!$result) {
        die('Ha ocurrido un error ejecutando la consulta [' . $db->error . ']');
    }

    $data = [];
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
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
    $sql .= "etapa                   = '" . $esc('etapa')                   . "', ";
    $sql .= "proceso                 = '" . $esc('proceso')                 . "', ";
    $sql .= "informacion             = '" . $esc('informacion')             . "', ";
    $sql .= "finicio                 = '" . $esc('finicio')                 . "', ";
    $sql .= "ftermino                = '" . $esc('ftermino')                . "', ";
    $sql .= "fuente                  = '" . $esc('fuente')                  . "', ";
    $sql .= "unidad_responsable_id   = '" . $esc('unidad_responsable_id')   . "', ";
    $sql .= "instituciones_vinculadas= '" . $esc('instituciones_vinculadas') . "', ";
    $sql .= "beneficiarios           = '" . $esc('beneficiarios')           . "', ";
    $sql .= "avance_financiero       = '" . $esc('avance_financiero')       . "', ";
    $sql .= "avance_actividades      = '" . $esc('avance_actividades')      . "', ";
    $sql .= "lat                     = '" . $esc('lat')                     . "', ";
    $sql .= "lng                     = '" . $esc('lng')                     . "', ";
    $sql .= "proyectos_user          = '$proyectos_user_val' ";
    $sql .= "WHERE id = '" . mysqli_real_escape_string($db, $_POST['proyectos_id']) . "';";

    // file_put_contents('debug.txt', $sql);

    if (!$result = $db->query($sql)) {
        die('Hay un error [' . $db->error . ']');
    }
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
    $sql .= "etapa                   = '" . $esc('etapa')                   . "', ";
    $sql .= "proceso                 = '" . $esc('proceso')                 . "', ";
    $sql .= "informacion             = '" . $esc('informacion')             . "', ";
    $sql .= "finicio                 = '" . $esc('finicio')                 . "', ";
    $sql .= "ftermino                = '" . $esc('ftermino')                . "', ";
    $sql .= "fuente                  = '" . $esc('fuente')                  . "', ";
    $sql .= "unidad_responsable_id   = '" . $esc('unidad_responsable_id')   . "', ";
    $sql .= "instituciones_vinculadas= '" . $esc('instituciones_vinculadas') . "', ";
    $sql .= "beneficiarios           = '" . $esc('beneficiarios')           . "', ";
    $sql .= "avance_financiero       = '" . $esc('avance_financiero')       . "', ";
    $sql .= "avance_actividades      = '" . $esc('avance_actividades')      . "', ";
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
?>
