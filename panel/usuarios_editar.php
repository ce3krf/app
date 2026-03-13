<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-08-22 16:50:10
// ************************************************************
?>
<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

@session_start();
include("../conf/db.php");		
include('functions.php');

// Verificar si el usuario actual es administrador
$es_admin = isset($_SESSION['usuarios_profile']) && $_SESSION['usuarios_profile'] === 'ADMINISTRADOR';

// Detectar si es perfil restringido (Área o Invitado)
$perfil_sesion = $_SESSION['usuarios_profile'] ?? '';
$es_restringido = in_array($perfil_sesion, ['ÁREA', 'INVITADO']);

// Si es perfil restringido, forzar que solo vea su propio usuario
if ($es_restringido) {
    $id = (int)($_SESSION['net_fulltrust_fas_id'] ?? 0);
} else {
    $id = isset($_GET['usuarios_id']) ? (int)$_GET['usuarios_id'] : 0;
}

$es_nuevo_usuario = ($id === 0 && !$es_restringido);
$title = $es_nuevo_usuario ? "Crear nuevo usuario" : "Editar usuario";

// Configurar codificación UTF-8
$sql = "SET NAMES utf8";
$db->query($sql);

// Obtener datos si es edición
$row = [];
if (!$es_nuevo_usuario) {
    $sql = "SELECT * FROM usuarios WHERE usuarios_id = '$id'";
    $result = $db->query($sql);
    $row = $result->fetch_assoc();
    
    if (!$row) {
        header("Location: usuarios.php?error=Usuario no encontrado");
        exit;
    }
} else {
    // Valores por defecto vacíos para nuevo usuario
    $row = [
        'usuarios_id'                => 0,
        'usuarios_userid'            => '',
        'usuarios_password'          => '',
        'usuarios_nombre'            => '',
        'usuarios_email'             => '',
        'usuarios_profile'           => 'INVITADO',
        'usuarios_status'            => 1,
        'usuarios_updated'           => '',
        'last_login'                 => '',
        'usuarios_foto'              => '',
        'usuarios_cambiarpassword'   => 1
    ];
}

// Obtener todos los instrumentos disponibles
$sql_instrumentos = "SELECT * FROM instrumentos ORDER BY instrumentos_descripcion";
$result_instrumentos = $db->query($sql_instrumentos);
$instrumentos_disponibles = [];
if ($result_instrumentos) {
    while ($instrumento = $result_instrumentos->fetch_assoc()) {
        $instrumentos_disponibles[] = $instrumento;
    }
}

// Obtener los instrumentos asignados al usuario
$instrumentos_usuario = [];
if (!$es_nuevo_usuario) {
    $sql_usuario_instrumentos = "SELECT instrumentos_id FROM usuarios_areas WHERE usuarios_id = '$id'";
    $result_usuario_instrumentos = $db->query($sql_usuario_instrumentos);
    if ($result_usuario_instrumentos) {
        while ($ui = $result_usuario_instrumentos->fetch_assoc()) {
            $instrumentos_usuario[] = $ui['instrumentos_id'];
        }
    }
}

// Ruta donde se guardarán las fotos
$upload_dir  = "profiles/";
$placeholder = "img/profile.png";

// Ruta completa para mostrar la imagen
$imagen_src = !$es_nuevo_usuario && !empty($row['usuarios_foto']) && file_exists($upload_dir . $row['usuarios_foto']) 
    ? $upload_dir . htmlspecialchars($row['usuarios_foto']) 
    : $placeholder;

// Agregar timestamp para evitar caché
$imagen_src .= "?t=" . time();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?></title>

<script src="js/jquery-3.5.1.min.js"></script>	
	
<link rel="icon" type="image/png" href="../favicon.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<link rel="stylesheet" type="text/css" href="css/styles.css"/>		

    <style>
        .profile-pic {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #dee2e6;
        }
        .readonly-field {
            background-color: #f8f9fa;
        }
        .section-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
        }
        .section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #6c757d;
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }
        .profile-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding-top: 0.25rem;
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

            <!-- Encabezado -->
            <div class="row top-info-div mb-3">
                <div class="col">
                    <h5 class="mb-0">USUARIOS <small class="text-muted fw-normal fs-6">/ <?php echo $title; ?></small></h5>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.location.href='usuarios.php'">
                        <i class="fa-solid fa-arrow-left me-1"></i> Volver
                    </button>
                </div>
            </div>

            <!-- Contenido Principal -->
            <form id="formUsuario" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="usuarios_id" value="<?php echo $row['usuarios_id']; ?>">

                <div class="row g-3">

                    <!-- Columna izquierda -->
                    <div class="col-lg-8">

                        <!-- Sección: Datos personales -->
                        <div class="section-card">
                            <div class="section-title"><i class="fa-solid fa-user me-1"></i> Datos del usuario</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="usuarios_userid" class="form-label text-info-emphasis">User ID</label>
                                    <input type="text" class="form-control <?php echo $es_restringido ? 'readonly-field' : ''; ?>"
                                           id="usuarios_userid" name="usuarios_userid"
                                           value="<?php echo htmlspecialchars($row['usuarios_userid']); ?>"
                                           <?php echo $es_restringido ? 'readonly' : 'required'; ?>>
                                    <small class="text-muted">Nombre de usuario único para iniciar sesión en el sistema.</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="usuarios_nombre" class="form-label text-info-emphasis">Nombre</label>
                                    <input type="text" class="form-control <?php echo $es_restringido ? 'readonly-field' : ''; ?>"
                                           id="usuarios_nombre" name="usuarios_nombre"
                                           value="<?php echo htmlspecialchars($row['usuarios_nombre']); ?>"
                                           <?php echo $es_restringido ? 'readonly' : 'required'; ?>>
                                    <small class="text-muted">Nombre completo del usuario tal como aparecerá en el sistema.</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="usuarios_email" class="form-label text-info-emphasis">Email</label>
                                    <input type="email" class="form-control <?php echo $es_restringido ? 'readonly-field' : ''; ?>"
                                           id="usuarios_email" name="usuarios_email"
                                           value="<?php echo htmlspecialchars($row['usuarios_email']); ?>"
                                           <?php echo $es_restringido ? 'readonly' : 'required'; ?>>
                                    <small class="text-muted">Correo electrónico de contacto del usuario.</small>
                                </div>
                                <div class="col-md-3">
                                    <label for="usuarios_profile" class="form-label text-info-emphasis">Perfil</label>
                                    <?php if ($es_restringido): ?>
                                    <input type="text" class="form-control readonly-field"
                                           value="<?php echo htmlspecialchars($row['usuarios_profile']); ?>" readonly>
                                    <?php else: ?>
                                    <select class="form-control" id="usuarios_profile" name="usuarios_profile" required>
                                        <option value="INVITADO" <?php echo $row['usuarios_profile'] === 'INVITADO' ? 'selected' : ''; ?>>Invitado</option>
                                        <option value="ÁREA" <?php echo $row['usuarios_profile'] === 'ÁREA' ? 'selected' : ''; ?>>Área</option>
                                        <option value="ADMINISTRADOR" <?php echo $row['usuarios_profile'] === 'ADMINISTRADOR' ? 'selected' : ''; ?>>Administrador</option>
                                    </select>
                                    <?php endif; ?>
                                    <small class="text-muted">Nivel de acceso del usuario. Área puede ver solo sus iniciativas; Administrador tiene acceso total.</small>
                                </div>
                                <?php if ($es_admin): ?>
                                <div class="col-md-3">
                                    <label for="usuarios_status" class="form-label text-info-emphasis">Estado</label>
                                    <select class="form-control" id="usuarios_status" name="usuarios_status">
                                        <option value="1" <?php echo (isset($row['usuarios_status']) && $row['usuarios_status'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                        <option value="0" <?php echo (isset($row['usuarios_status']) && $row['usuarios_status'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
                                    <small class="text-muted">Los usuarios inactivos no pueden iniciar sesión en el sistema.</small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Sección: Contraseña -->
                        <div class="section-card">
                            <div class="section-title"><i class="fa-solid fa-lock me-1"></i> Seguridad</div>
                            <?php if ($es_nuevo_usuario): ?>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="usuarios_password" class="form-label text-info-emphasis">Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="usuarios_password" name="usuarios_password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordNew"><i class="fa-regular fa-eye"></i></button>
                                    </div>
                                    <small class="text-muted">Mínimo 8 caracteres. Use letras, números y símbolos para mayor seguridad.</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="usuarios_password_confirm" class="form-label text-info-emphasis">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="usuarios_password_confirm" name="usuarios_password_confirm" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm"><i class="fa-regular fa-eye"></i></button>
                                    </div>
                                    <small class="text-muted">Repita la contraseña para confirmar que es correcta.</small>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="cambiar_password" name="cambiar_password">
                                <label class="form-check-label" for="cambiar_password">¿Desea cambiar la contraseña?</label>
                            </div>
                            <div id="password_fields" style="display: none;">
                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label for="usuarios_password" class="form-label text-info-emphasis">Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="usuarios_password" name="usuarios_password">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordNew"><i class="fa-regular fa-eye"></i></button>
                                        </div>
                                        <small class="text-muted">Mínimo 8 caracteres. Use letras, números y símbolos para mayor seguridad.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="usuarios_password_confirm" class="form-label text-info-emphasis">Confirmar Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="usuarios_password_confirm" name="usuarios_password_confirm">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm"><i class="fa-regular fa-eye"></i></button>
                                        </div>
                                        <small class="text-muted">Repita la nueva contraseña para confirmar que es correcta.</small>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($es_admin): ?>
                            <!-- Obligar cambio de contraseña: solo para admin -->
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="usuarios_cambiarpassword" name="usuarios_cambiarpassword" value="1"
                                       <?php echo (!empty($row['usuarios_cambiarpassword']) && $row['usuarios_cambiarpassword'] == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="usuarios_cambiarpassword">
                                    <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                                    Obligar al usuario a cambiar su contraseña en el próximo acceso
                                </label>
                            </div>
                            <?php elseif ($es_restringido && !empty($row['usuarios_cambiarpassword']) && $row['usuarios_cambiarpassword'] == 1): ?>
                            <!-- Aviso al usuario restringido si debe cambiar contraseña -->
                            <div class="alert alert-warning py-2 mt-3 small mb-0">
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                Su administrador ha solicitado que cambie su contraseña.
                            </div>
                            <?php endif; ?>

                        </div>

                        <!-- Sección: Metadatos (solo lectura) -->
                        <div class="section-card">
                            <div class="section-title"><i class="fa-solid fa-clock-rotate-left me-1"></i> Registro</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="usuarios_updated" class="form-label text-info-emphasis">Actualizado</label>
                                    <input type="text" class="form-control readonly-field" id="usuarios_updated" value="<?php echo htmlspecialchars($row['usuarios_updated']); ?>" readonly>
                                    <small class="text-muted">Fecha y hora de la última modificación del perfil. Se actualiza automáticamente.</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_login" class="form-label text-info-emphasis">Último Login</label>
                                    <input type="text" class="form-control readonly-field" id="last_login" value="<?php echo htmlspecialchars($row['last_login']); ?>" readonly>
                                    <small class="text-muted">Fecha y hora del último acceso registrado al sistema.</small>
                                </div>
                            </div>
                        </div>

                    </div><!-- /col-lg-8 -->

                    <!-- Columna derecha -->
                    <div class="col-lg-4">

                        <!-- Sección: Foto de perfil -->
                        <div class="section-card text-center">
                            <div class="section-title"><i class="fa-solid fa-image me-1"></i> Foto de perfil</div>
                            <div class="profile-col">
                                <img id="profilePreview" src="<?php echo $imagen_src; ?>" alt="Foto de perfil" class="profile-pic mb-1">
                                <?php if (!$es_nuevo_usuario && !empty($row['usuarios_foto']) && file_exists($upload_dir . $row['usuarios_foto'])): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="eliminar_foto" id="eliminar_foto">
                                    <label class="form-check-label small" for="eliminar_foto">Eliminar foto actual</label>
                                </div>
                                <?php endif; ?>
                                <input type="file" class="form-control form-control-sm mt-1" id="usuarios_foto" name="usuarios_foto" accept="image/*">
                                <small class="text-muted text-center">JPG, PNG, GIF · Máx. 2MB</small>
                            </div>
                        </div>

                        <!-- Sección: Instrumentos asignados -->
                        <div class="section-card">
                            <div class="section-title"><i class="fa-solid fa-list-check me-1"></i> Editará las iniciativas de los siguientes instrumentos</div>
                            <small class="text-muted d-block mb-2">Aplica solo a usuarios con perfil Área</small>

                            <?php if (!empty($instrumentos_disponibles)): ?>

                                <?php if (!$es_restringido): ?>
                                <!-- Botones Todos / Ninguno: solo para no restringidos -->
                                <div class="mb-2 d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="$('.instrumento-checkbox').prop('checked', true); return false;">
                                        <i class="fa-solid fa-check-double"></i> Todos
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="$('.instrumento-checkbox').prop('checked', false); return false;">
                                        <i class="fa-solid fa-xmark"></i> Ninguno
                                    </button>
                                </div>
                                <?php endif; ?>

                                <div class="border rounded p-2" style="max-height: 220px; overflow-y: auto; background-color: #f8f9fa;">
                                    <?php foreach ($instrumentos_disponibles as $instrumento): ?>
                                    <div class="form-check mb-1">
                                        <input class="form-check-input instrumento-checkbox" type="checkbox"
                                               name="instrumentos[]"
                                               value="<?php echo $instrumento['instrumentos_id']; ?>"
                                               id="instrumento_<?php echo $instrumento['instrumentos_id']; ?>"
                                               <?php echo in_array($instrumento['instrumentos_id'], $instrumentos_usuario) ? 'checked' : ''; ?>
                                               <?php echo $es_restringido ? 'disabled' : ''; ?>>
                                        <label class="form-check-label small" for="instrumento_<?php echo $instrumento['instrumentos_id']; ?>">
                                            <?php echo htmlspecialchars($instrumento['instrumentos_descripcion']); ?>
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($es_restringido): ?>
                                <small class="text-muted mt-1 d-block"><i class="fa-solid fa-lock me-1"></i>Solo lectura</small>
                                <?php endif; ?>

                            <?php else: ?>
                            <div class="alert alert-warning py-2 small">No hay instrumentos disponibles</div>
                            <?php endif; ?>
                        </div>

                    </div><!-- /col-lg-4 -->

                </div><!-- /row -->

                <!-- Botones de acción -->
                <div class="d-flex justify-content-end gap-2 mt-2 mb-4">
                    <?php if ($es_admin && !$es_nuevo_usuario): ?>
                    <button type="button" class="btn btn-danger" id="eliminarUsuario">
                        <i class="fa-solid fa-trash me-1"></i> Eliminar usuario
                    </button>
                    <?php endif; ?>
                    <?php if ($es_admin || $es_restringido): ?>
                    <button type="button" class="btn btn-primary" id="guardarCambios">
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        <?php echo $es_nuevo_usuario ? 'Crear usuario' : 'Guardar cambios'; ?>
                    </button>
                    <?php endif; ?>
                </div>

            </form>

        </div><!-- /container-fluid -->
    </div><!-- /page-content-wrapper -->
</div><!-- /wrapper -->

<?php include("footer.php");?>
    <script>
    $(document).ready(function() {
        // Toggle mostrar/ocultar contraseña
        $('#togglePasswordNew').click(function() {
            const passwordField = $('#usuarios_password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).find('i').toggleClass('fa-eye fa-eye-slash');
        });

        $('#togglePasswordConfirm').click(function() {
            const passwordField = $('#usuarios_password_confirm');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $(this).find('i').toggleClass('fa-eye fa-eye-slash');
        });

        // Mostrar/ocultar campos de contraseña en edición
        $('#cambiar_password').change(function() {
            $('#password_fields').toggle(this.checked);
            if (!this.checked) {
                $('#usuarios_password').val('');
                $('#usuarios_password_confirm').val('');
            }
        });

        // Vista previa de la foto de perfil
        $('#usuarios_foto').change(function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profilePreview').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            } else {
                $('#profilePreview').attr('src', '<?php echo $placeholder; ?>');
            }
        });

        // Validación antes de guardar
        $('#guardarCambios').click(function(e) {
            e.preventDefault();
            const pass = $('#usuarios_password').val();
            const passConfirm = $('#usuarios_password_confirm').val();
            const fileInput = $('#usuarios_foto')[0];
            const maxSize = 2 * 1024 * 1024; // 2MB en bytes

            <?php if (!$es_nuevo_usuario): ?>
            if ($('#cambiar_password').is(':checked') && pass !== passConfirm) {
            <?php else: ?>
            if (pass !== passConfirm) {
            <?php endif; ?>
                $.alert({
                    title: 'Error',
                    content: 'Las contraseñas no coinciden',
                    type: 'red'
                });
                return false;
            }

            if (fileInput.files.length > 0 && fileInput.files[0].size > maxSize) {
                $.alert({
                    title: 'Error',
                    content: 'La foto de perfil no debe exceder los 2MB',
                    type: 'red'
                });
                return false;
            }

            $.confirm({
                title: 'Confirmar acción',
                content: '¿Estás seguro de <?php echo $es_nuevo_usuario ? 'crear este usuario' : 'guardar los cambios'; ?>?',
                buttons: {
                    confirmar: function() {
                        var formData = new FormData($('#formUsuario')[0]);
                        formData.append('task', '<?php echo $es_nuevo_usuario ? 'CREAR' : 'ACTUALIZAR'; ?>');
                        
                        $.ajax({
                            url: 'api/usuarios.php',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                try {
                                    var res = JSON.parse(response);
                                    if (res.success) {
                                        $.alert({
                                            title: 'Éxito',
                                            content: '<?php echo $es_nuevo_usuario ? 'Usuario creado' : 'Cambios guardados'; ?> con éxito',
                                            type: 'green',
                                            onClose: function() {
                                                var redirectUrl = '<?php echo $es_nuevo_usuario ? "" : "usuarios_editar.php?usuarios_id=" . $id; ?>';
                                                if ('<?php echo $es_nuevo_usuario ? "true" : "false"; ?>' === 'true' && res.id) {
                                                    redirectUrl = 'usuarios_editar.php?usuarios_id=' + res.id;
                                                }
                                                window.location.href = redirectUrl || 'usuarios.php';
                                            }
                                        });
                                    } else {
                                        $.alert({
                                            title: 'Error',
                                            content: res.message || 'Error al procesar la solicitud',
                                            type: 'red'
                                        });
                                    }
                                } catch (e) {
                                    $.alert({
                                        title: 'Error',
                                        content: 'Respuesta inválida del servidor: ' + response,
                                        type: 'red'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                $.alert({
                                    title: 'Error',
                                    content: 'Error de conexión con el servidor: ' + error,
                                    type: 'red'
                                });
                            }
                        });
                    },
                    cancelar: function() {}
                }
            });
        });

        <?php if ($es_admin && !$es_nuevo_usuario): ?>
        $('#eliminarUsuario').click(function() {
            $.confirm({
                title: 'Confirmar eliminación',
                content: '¿Estás seguro de eliminar este usuario?',
                buttons: {
                    confirmar: function() {
                        $.ajax({
                            url: 'api/usuarios.php',
                            type: 'POST',
                            data: {
                                usuarios_id: '<?php echo $id; ?>',
                                task: 'ELIMINAR'
                            },
                            success: function(response) {
                                try {
                                    var res = JSON.parse(response);
                                    if (res.success) {
                                        $.alert({
                                            title: 'Éxito',
                                            content: 'Usuario eliminado con éxito',
                                            type: 'green',
                                            onClose: function() {
                                                window.location.href = 'usuarios.php';
                                            }
                                        });
                                    } else {
                                        $.alert({
                                            title: 'Error',
                                            content: res.message || 'Error al eliminar el usuario',
                                            type: 'red'
                                        });
                                    }
                                } catch (e) {
                                    $.alert({
                                        title: 'Error',
                                        content: 'Respuesta inválida del servidor',
                                        type: 'red'
                                    });
                                }
                            },
                            error: function() {
                                $.alert({
                                    title: 'Error',
                                    content: 'Error de conexión con el servidor',
                                    type: 'red'
                                });
                            }
                        });
                    },
                    cancelar: function() {}
                }
            });
        });
        <?php endif; ?>
    });

// Manejar el checkbox de eliminar foto
$('#eliminar_foto').change(function() {
    if (this.checked) {
        $('#profilePreview').attr('src', '<?php echo $placeholder; ?>?t=' + Date.now());
        $('#usuarios_foto').val('');
    } else {
        $('#profilePreview').attr('src', '<?php echo $imagen_src; ?>');
    }
});

// Modificar el evento change del input file para manejar el checkbox
$('#usuarios_foto').change(function(e) {
    const file = e.target.files[0];
    if (file) {
        $('#eliminar_foto').prop('checked', false);
        
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
            $.alert({
                title: 'Error',
                content: 'La foto de perfil no debe exceder los 2MB',
                type: 'red'
            });
            $(this).val('');
            return;
        }
        
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        const fileType = file.type.toLowerCase();
        if (!allowedTypes.includes(fileType)) {
            $.alert({
                title: 'Error',
                content: 'Solo se permiten archivos JPG, PNG y GIF',
                type: 'red'
            });
            $(this).val('');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#profilePreview').attr('src', e.target.result);
        }
        reader.readAsDataURL(file);
    } else {
        if ($('#eliminar_foto').is(':checked')) {
            $('#profilePreview').attr('src', '<?php echo $placeholder; ?>?t=' + Date.now());
        } else {
            $('#profilePreview').attr('src', '<?php echo $imagen_src; ?>');
        }
    }
});

    </script>    

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>
