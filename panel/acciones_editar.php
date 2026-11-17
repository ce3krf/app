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


if (!isset($_GET['proyectos_id'], $_GET['acciones_id']) || !is_numeric($_GET['proyectos_id']) || !is_numeric($_GET['acciones_id'])) {
    die('Parámetros no válidos');
}

$proyectos_id = (int) $_GET['proyectos_id'];
$acciones_id  = (int) $_GET['acciones_id'];
$es_nueva     = ($acciones_id === 0);

function h($v) {
    return htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
}


// -----------------------------------------------------
// Iniciativa
// -----------------------------------------------------
$stmt = $db->prepare("SELECT id, codigo, nombre_iniciativa, n_inst FROM proyectos WHERE id = ?");
$stmt->bind_param('i', $proyectos_id);
$stmt->execute();
$pro = $stmt->get_result()->fetch_assoc();
if (!$pro) {
    die('La iniciativa no existe.');
}
$codigo = trim((string) $pro['codigo']);
if ($codigo === '') {
    die('La iniciativa no tiene código. Asígnele uno antes de registrar acciones.');
}


// -----------------------------------------------------
// Acción (debe pertenecer a la iniciativa)
// -----------------------------------------------------
$acc = ['nombre_accion' => '', 'inicio' => '', 'fin' => '', 'verificador' => ''];
if (!$es_nueva) {
    $stmt = $db->prepare("SELECT * FROM acciones WHERE id = ? AND codigo_iniciativa = ?");
    $stmt->bind_param('is', $acciones_id, $codigo);
    $stmt->execute();
    $acc = $stmt->get_result()->fetch_assoc();
    if (!$acc) {
        die('La acción no existe o no pertenece a esta iniciativa.');
    }
}


// -----------------------------------------------------
// Permisos (mismo criterio que la ficha: ADMINISTRADOR, o ÁREA con el instrumento asignado)
// -----------------------------------------------------
$perfil_usuario = $_SESSION['usuarios_profile'] ?? '';
$user_id_areas  = (int) ($_SESSION["net_fulltrust_fas_id"] ?? 0);

$puede_editar = false;
if ($perfil_usuario === 'ADMINISTRADOR') {
    $puede_editar = true;
} elseif ($perfil_usuario === 'ÁREA') {
    $n_inst = (int) $pro['n_inst'];
    $r = $db->query("SELECT COUNT(*) AS c FROM usuarios_areas WHERE usuarios_id = $user_id_areas AND instrumentos_id = $n_inst");
    $puede_editar = $r && (int) $r->fetch_assoc()['c'] > 0;
}
$solo_lectura = !$puede_editar;

if ($es_nueva && $solo_lectura) {
    die('No tiene permisos para crear acciones en esta iniciativa.');
}

$url_volver = 'proyectos_editar.php?proyectos_id=' . (int) $pro['id'] . '#acciones';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $es_nueva ? 'Nueva acción' : 'Editar acción'; ?></title>

<link rel="icon" type="image/png" href="../favicon.png">
<script src="js/jquery-3.5.1.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/styles.css"/>

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
    display: inline-flex; align-items: center; justify-content: center;
    width: 34px; height: 34px; border-radius: 50%;
    background: rgba(255, 255, 255, .18); font-size: 15px; flex-shrink: 0;
}
.ficha-seccion-titulo { font-size: 16px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
.iniciativa-ref {
    background: #f4f8fc; border: 1px solid #d6e3f0; border-radius: 10px;
    padding: 12px 16px; margin-bottom: 24px; font-size: 14px; color: #495057;
}
.iniciativa-ref strong { color: #023047; }
.total-display {
    font-size: 18px; font-weight: 700; color: #1a5276;
    background: #eaf0fb; border: 1px solid #b8cfe8; border-radius: 6px;
    padding: 6px 14px;
}
</style>
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
    <span>Estás viendo esta acción en <strong>modo lectura</strong>.</span>
</div>
<?php endif; ?>

            <div class="row top-info-div">
                <div class="col">
                    <h5><?php echo $es_nueva ? 'CREAR NUEVA ACCIÓN' : 'EDITAR ACCIÓN'; ?></h5>
                </div>
                <div class="col text-end">
                    <button onclick="window.location='<?php echo h($url_volver); ?>'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>
                </div>
            </div>

            <div class="container">
            <form id="form1" name="form1" autocomplete="off">

<div style="height:20px"></div>
<div class="iniciativa-ref">
  <i class="fa-solid fa-lightbulb"></i>&nbsp;Iniciativa <strong><?php echo h($codigo); ?></strong>: <?php echo h($pro['nombre_iniciativa']); ?>
</div>

<div class="ficha-seccion">
  <span class="ficha-seccion-icono"><i class="fa-solid fa-list-check"></i></span>
  <span class="ficha-seccion-titulo">Acción de la ruta crítica</span>
</div>

<div class="row">
  <div class="col-12">
    <div class="form-group">
      <label for="nombre_accion">Acción</label>
      <textarea class="form-control" id="nombre_accion" name="nombre_accion" rows="3" maxlength="255"><?php echo h($acc['nombre_accion']); ?></textarea>
      <small class="text-muted">Acción a realizar. Máximo 255 caracteres (<span id="nombre_cnt">0</span>/255).</small>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-3">
    <div class="form-group">
      <label for="inicio">Inicio</label>
      <input type="date" class="form-control" id="inicio" name="inicio" value="<?php echo h($acc['inicio']); ?>">
      <small class="text-muted">Fecha de inicio. Define el orden en la lista.</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label for="fin">Fin</label>
      <input type="date" class="form-control" id="fin" name="fin" value="<?php echo h($acc['fin']); ?>">
      <small class="text-muted">No puede ser anterior al inicio.</small>
    </div>
  </div>
  <div class="col-md-3">
    <div class="form-group">
      <label>Duración</label>
      <div id="duracion" class="total-display">-</div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="form-group">
      <label for="verificador">Verificador</label>
      <input type="text" class="form-control" id="verificador" name="verificador" maxlength="255" value="<?php echo h($acc['verificador']); ?>">
      <small class="text-muted">Resultado o hito esperado que permite verificar que la acción se cumplió.</small>
    </div>
  </div>
</div>

<input type="hidden" name="proyectos_id" value="<?php echo (int) $pro['id']; ?>">
<input type="hidden" name="acciones_id" id="acciones_id" value="<?php echo (int) $acciones_id; ?>">
<input type="hidden" name="task" value="save">
<input type="hidden" name="token" id="token" value="<?php echo h(session_id()); ?>">

<?php if (!$solo_lectura): ?>
<div class="row top-info-div">
  <div class="col" style="text-align:center">
    <div style="height:50px; clear:both"></div>
    <?php if (!$es_nueva): ?>
    <button id="btdelete" type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar acción</button>
    <?php endif; ?>
  </div>
  <div class="col" style="text-align:center">
    <div style="height:50px; clear:both"></div>
    <button id="btsend" type="button" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar cambios</button>
  </div>
</div>
<?php endif; ?>

            </form>
            <div style="height:60px"></div>
            </div><!-- /container -->

        </div>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>

<script>
var URL_VOLVER = <?php echo json_encode($url_volver); ?>;

function aviso(titulo, texto, tipo) {
    $.alert({ title: titulo, content: texto, type: tipo || 'red', typeAnimated: true });
}

// Duración en meses (aprox.) entre inicio y fin
function actualizarDuracion() {
    var ini = $('#inicio').val(), fin = $('#fin').val();
    if (!ini || !fin) { $('#duracion').text('-'); return; }
    var a = new Date(ini + 'T00:00:00'), b = new Date(fin + 'T00:00:00');
    if (b < a) { $('#duracion').text('Fin anterior al inicio'); return; }
    var meses = (b.getFullYear() - a.getFullYear()) * 12 + (b.getMonth() - a.getMonth()) + (b.getDate() >= a.getDate() - 1 ? 1 : 0);
    $('#duracion').text(meses + (meses === 1 ? ' mes' : ' meses'));
}

function actualizarContador() {
    $('#nombre_cnt').text($('#nombre_accion').val().length);
}

$(document).ready(function () {

    $('#inicio, #fin').on('input change', actualizarDuracion);
    $('#nombre_accion').on('input', actualizarContador);
    actualizarDuracion();
    actualizarContador();

<?php if ($solo_lectura): ?>
    $('#form1 input, #form1 textarea').prop('disabled', true);
<?php else: ?>
    var modificado = false, saliendo = false;
    $('#form1 input, #form1 textarea').on('change input', function () { modificado = true; });
    window.addEventListener('beforeunload', function (e) {
        if (modificado && !saliendo) { e.preventDefault(); e.returnValue = ''; }
    });

    $('#btsend').on('click', function () {
        var nombre = $.trim($('#nombre_accion').val());
        var ini = $('#inicio').val(), fin = $('#fin').val();
        if (nombre === '')             { aviso('Atención', 'Ingrese el nombre de la acción.'); return; }
        if (ini && fin && fin < ini)   { aviso('Atención', 'La fecha de fin no puede ser anterior a la de inicio.'); return; }

        var $b = $(this).prop('disabled', true);
        $('.fa-floppy-disk').addClass('spin-icon');

        $.post('api/api_acciones.php', $('#form1').serialize(), function (r) {
            if (r && r.ok) {
                saliendo = true;
                $.confirm({
                    title: 'Éxito', content: 'La acción se guardó correctamente', type: 'green', typeAnimated: true,
                    buttons: { ok: { text: 'Aceptar', btnClass: 'btn-green', action: function () { window.location = URL_VOLVER; } } }
                });
            } else {
                aviso('Atención', (r && r.mensaje) || 'No se pudo guardar la acción.');
            }
        }, 'json').fail(function (x) {
            aviso('Error', (x.responseJSON && x.responseJSON.mensaje) || 'No se pudo guardar la acción.');
        }).always(function () {
            $b.prop('disabled', false);
            $('.fa-floppy-disk').removeClass('spin-icon');
        });
    });

    $('#btdelete').on('click', function () {
        $.confirm({
            title: 'Confirmar eliminación',
            content: '¿Está seguro que desea eliminar esta acción?<br><strong>Esta acción no se puede deshacer.</strong>',
            type: 'red',
            typeAnimated: true,
            buttons: {
                eliminar: {
                    text: 'Sí, eliminar', btnClass: 'btn-red',
                    action: function () {
                        $.post('api/api_acciones.php', {
                            token: $('#token').val(), task: 'delete',
                            proyectos_id: <?php echo (int) $pro['id']; ?>, acciones_id: $('#acciones_id').val()
                        }, function (r) {
                            if (r && r.ok) { saliendo = true; window.location = URL_VOLVER; }
                            else aviso('Atención', (r && r.mensaje) || 'No se pudo eliminar la acción.');
                        }, 'json').fail(function (x) {
                            aviso('Error', (x.responseJSON && x.responseJSON.mensaje) || 'No se pudo eliminar la acción.');
                        });
                    }
                },
                cancelar: { text: 'Cancelar' }
            }
        });
    });
<?php endif; ?>
});
</script>
</body>
</html>
