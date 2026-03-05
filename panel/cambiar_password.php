<?php
ob_start();
@session_start();
include("../conf/db.php");
include('functions.php');

// Verificar sesión activa
if (empty($_SESSION['logeado']) || $_SESSION['logeado'] !== 'si') {
    header("Location: login.php");
    exit;
}

$uid = (int)$_SESSION['net_fulltrust_fas_id'];

// ── Procesamiento AJAX ────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task']) && $_POST['task'] === 'CAMBIAR_PASSWORD') {

    ob_clean();
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];

    try {
        $pass_actual    = $_POST['password_actual']  ?? '';
        $pass_nueva     = $_POST['password_nueva']   ?? '';
        $pass_confirmar = $_POST['password_confirmar'] ?? '';

        if (empty($pass_actual) || empty($pass_nueva) || empty($pass_confirmar)) {
            throw new Exception('Todos los campos son obligatorios.');
        }

        if (strlen($pass_nueva) < 6) {
            throw new Exception('La nueva contraseña debe tener al menos 6 caracteres.');
        }

        if ($pass_nueva !== $pass_confirmar) {
            throw new Exception('Las contraseñas nuevas no coinciden.');
        }

        // Verificar contraseña actual
        $hash_actual = md5($pass_actual);
        $stmt = $db->prepare("SELECT usuarios_id FROM usuarios WHERE usuarios_id = ? AND usuarios_password = ?");
        $stmt->bind_param("is", $uid, $hash_actual);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            throw new Exception('La contraseña actual es incorrecta.');
        }

        // Actualizar contraseña y limpiar flag
        $hash_nueva = md5($pass_nueva);
        $stmt = $db->prepare("UPDATE usuarios SET usuarios_password = ?, usuarios_cambiarpassword = 0, usuarios_updated = NOW() WHERE usuarios_id = ?");
        $stmt->bind_param("si", $hash_nueva, $uid);

        if (!$stmt->execute()) {
            throw new Exception('Error al actualizar la contraseña: ' . $db->error);
        }

        // Actualizar password en sesión
        $_SESSION['net_fulltrust_fas_users_password'] = $hash_nueva;

        $response['success'] = true;
        $response['message'] = 'Contraseña actualizada correctamente.';

    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    echo json_encode($response);
    exit;
}
// ── Fin procesamiento ─────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $row_param["parametros_titulo"]; ?></title>

<link rel="icon" type="image/png" href="../favicon.png">
<script src="js/jquery-3.5.1.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<link rel="stylesheet" type="text/css" href="css/styles.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>

<style>
    .pass-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1.5rem;
        max-width: 480px;
        margin: 0 auto;
    }
    .section-title {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #6c757d;
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 0.5rem;
        margin-bottom: 1.25rem;
    }
    .pass-strength {
        height: 4px;
        border-radius: 2px;
        margin-top: 6px;
        transition: all 0.3s;
        background: #e9ecef;
    }
    .pass-strength.weak   { background: #dc3545; width: 33%; }
    .pass-strength.medium { background: #ffc107; width: 66%; }
    .pass-strength.strong { background: #198754; width: 100%; }
</style>
</head>
<body>

<?php include("header.php"); ?>

<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php include("sidebar.php"); ?>
    </div>

    <div id="page-content-wrapper">
        <div class="container-fluid p-4">

            <div class="row top-info-div mb-3">
                <div class="col">
                    <h5 class="mb-0">CAMBIAR CONTRASEÑA</h5>
                </div>
                <div class="col text-end">
                    <a href="index.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Volver al inicio
                    </a>
                </div>
            </div>

            <div class="pass-card">
                <div class="section-title"><i class="fa-solid fa-lock me-1"></i> Nueva contraseña</div>

                <form id="formCambiarPass">

                    <div class="mb-3">
                        <label class="form-label text-info-emphasis">Contraseña actual</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_actual" name="password_actual" placeholder="Ingresa tu contraseña actual">
                            <button class="btn btn-outline-secondary" type="button" id="toggleActual">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-info-emphasis">Nueva contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_nueva" name="password_nueva" placeholder="Mínimo 6 caracteres">
                            <button class="btn btn-outline-secondary" type="button" id="toggleNueva">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        <div class="pass-strength" id="passStrength"></div>
                        <small class="text-muted" id="passStrengthLabel"></small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-info-emphasis">Confirmar nueva contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" placeholder="Repite la nueva contraseña">
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmar">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="button" class="btn btn-primary" id="btnGuardar">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar nueva contraseña
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<?php include("footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>

<script>
$(document).ready(function () {

    // Toggle ver/ocultar contraseñas
    function togglePass(btnId, inputId) {
        $('#' + btnId).click(function () {
            const inp = $('#' + inputId);
            inp.attr('type', inp.attr('type') === 'password' ? 'text' : 'password');
            $(this).find('i').toggleClass('fa-eye fa-eye-slash');
        });
    }
    togglePass('toggleActual',    'password_actual');
    togglePass('toggleNueva',     'password_nueva');
    togglePass('toggleConfirmar', 'password_confirmar');

    // Indicador de fortaleza
    $('#password_nueva').on('input', function () {
        const val = $(this).val();
        const bar = $('#passStrength');
        const lbl = $('#passStrengthLabel');
        bar.removeClass('weak medium strong');
        if (val.length === 0) { lbl.text(''); return; }
        if (val.length < 6) {
            bar.addClass('weak');   lbl.text('Débil').css('color','#dc3545');
        } else if (val.length < 10 || !/[0-9]/.test(val) || !/[^a-zA-Z0-9]/.test(val)) {
            bar.addClass('medium'); lbl.text('Media').css('color','#ffc107');
        } else {
            bar.addClass('strong'); lbl.text('Fuerte').css('color','#198754');
        }
    });

    // Guardar
    $('#btnGuardar').click(function () {
        $.confirm({
            title: 'Confirmar cambio',
            content: '¿Estás seguro de que deseas cambiar tu contraseña?',
            boxWidth: '380px',
            useBootstrap: false,
            buttons: {
                confirmar: {
                    text: 'Sí, cambiar',
                    btnClass: 'btn-primary',
                    action: function () {
                        $.ajax({
                            url: 'cambiar_password.php',
                            type: 'POST',
                            data: {
                                task: 'CAMBIAR_PASSWORD',
                                password_actual:    $('#password_actual').val(),
                                password_nueva:     $('#password_nueva').val(),
                                password_confirmar: $('#password_confirmar').val()
                            },
                            success: function (response) {
                                try {
                                    var res = (typeof response === 'string') ? JSON.parse(response) : response;
                                    if (res.success) {
                                        $.alert({
                                            title: 'Éxito',
                                            content: res.message,
                                            type: 'green',
                                            onClose: function () {
                                                window.location.href = 'index.php';
                                            }
                                        });
                                    } else {
                                        $.alert({ title: 'Error', content: res.message, type: 'red' });
                                    }
                                } catch(e) {
                                    $.alert({ title: 'Error', content: 'Respuesta inválida del servidor.', type: 'red' });
                                }
                            },
                            error: function () {
                                $.alert({ title: 'Error', content: 'Error de conexión con el servidor.', type: 'red' });
                            }
                        });
                    }
                },
                cancelar: function () {}
            }
        });
    });

});
</script>

</body>
</html>
