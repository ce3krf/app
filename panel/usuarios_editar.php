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

// Determinar si es creación o edición
$id = isset($_GET['usuarios_id']) ? (int)$_GET['usuarios_id'] : 0;
$es_nuevo_usuario = ($id === 0);
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
        'usuarios_id' => 0,
        'usuarios_userid' => '',
        'usuarios_password' => '',
        'usuarios_nombre' => '',
        'usuarios_email' => '',
        'usuarios_profile' => 'INVITADO',
        'usuarios_status' => 1,
        'usuarios_updated' => '',
        'last_login' => '',
        'usuarios_foto' => ''
    ];
}


// Obtener todas las áreas disponibles
$sql_areas = "SELECT * FROM areas ORDER BY area";
$result_areas = $db->query($sql_areas);
$areas_disponibles = [];
if ($result_areas) {
    while ($area = $result_areas->fetch_assoc()) {
        $areas_disponibles[] = $area;
    }
}

// Obtener las áreas asignadas al usuario
$areas_usuario = [];
if (!$es_nuevo_usuario) {
    $sql_usuario_areas = "SELECT areas_id FROM usuarios_areas WHERE usuarios_id = '$id'";
    $result_usuario_areas = $db->query($sql_usuario_areas);
    if ($result_usuario_areas) {
        while ($ua = $result_usuario_areas->fetch_assoc()) {
            $areas_usuario[] = $ua['areas_id'];
        }
    }
}

// Ruta donde se guardarán las fotos
$upload_dir = "profiles/";
$placeholder = "img/profile.png"; // Placeholder de 128x128

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
                                    <input type="text" class="form-control" id="usuarios_userid" name="usuarios_userid" value="<?php echo htmlspecialchars($row['usuarios_userid']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="usuarios_nombre" class="form-label text-info-emphasis">Nombre</label>
                                    <input type="text" class="form-control" id="usuarios_nombre" name="usuarios_nombre" value="<?php echo htmlspecialchars($row['usuarios_nombre']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="usuarios_email" class="form-label text-info-emphasis">Email</label>
                                    <input type="email" class="form-control" id="usuarios_email" name="usuarios_email" value="<?php echo htmlspecialchars($row['usuarios_email']); ?>" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="usuarios_profile" class="form-label text-info-emphasis">Perfil</label>
                                    <select class="form-control" id="usuarios_profile" name="usuarios_profile" required>
                                        <option value="INVITADO" <?php echo $row['usuarios_profile'] === 'INVITADO' ? 'selected' : ''; ?>>Invitado</option>
                                        <option value="ÁREA" <?php echo $row['usuarios_profile'] === 'ÁREA' ? 'selected' : ''; ?>>Área</option>
                                        <option value="ADMINISTRADOR" <?php echo $row['usuarios_profile'] === 'ADMINISTRADOR' ? 'selected' : ''; ?>>Administrador</option>
                                    </select>
                                </div>
                                <?php if ($es_admin): ?>
                                <div class="col-md-3">
                                    <label for="usuarios_status" class="form-label text-info-emphasis">Estado</label>
                                    <select class="form-control" id="usuarios_status" name="usuarios_status">
                                        <option value="1" <?php echo (isset($row['usuarios_status']) && $row['usuarios_status'] == 1) ? 'selected' : ''; ?>>Activo</option>
                                        <option value="0" <?php echo (isset($row['usuarios_status']) && $row['usuarios_status'] == 0) ? 'selected' : ''; ?>>Inactivo</option>
                                    </select>
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
                                </div>
                                <div class="col-md-6">
                                    <label for="usuarios_password_confirm" class="form-label text-info-emphasis">Confirmar Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="usuarios_password_confirm" name="usuarios_password_confirm" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm"><i class="fa-regular fa-eye"></i></button>
                                    </div>
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
                                    </div>
                                    <div class="col-md-6">
                                        <label for="usuarios_password_confirm" class="form-label text-info-emphasis">Confirmar Nueva Contraseña</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="usuarios_password_confirm" name="usuarios_password_confirm">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm"><i class="fa-regular fa-eye"></i></button>
                                        </div>
                                    </div>
                                </div>
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
                                </div>
                                <div class="col-md-6">
                                    <label for="last_login" class="form-label text-info-emphasis">Último Login</label>
                                    <input type="text" class="form-control readonly-field" id="last_login" value="<?php echo htmlspecialchars($row['last_login']); ?>" readonly>
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

                        <!-- Sección: Áreas -->
                        <div class="section-card">
                            <div class="section-title"><i class="fa-solid fa-sitemap me-1"></i> Áreas asignadas</div>
                            <small class="text-muted d-block mb-2">Aplica solo a usuarios con perfil Área</small>
                            <?php if (!empty($areas_disponibles)): ?>
                            <div class="mb-2 d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="$('.area-checkbox').prop('checked', true); return false;">
                                    <i class="fa-solid fa-check-double"></i> Todas
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="$('.area-checkbox').prop('checked', false); return false;">
                                    <i class="fa-solid fa-xmark"></i> Ninguna
                                </button>
                            </div>
                            <div class="border rounded p-2" style="max-height: 220px; overflow-y: auto; background-color: #f8f9fa;">
                                <?php foreach ($areas_disponibles as $area): ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input area-checkbox" type="checkbox"
                                           name="areas[]"
                                           value="<?php echo $area['id']; ?>"
                                           id="area_<?php echo $area['id']; ?>"
                                           <?php echo in_array($area['id'], $areas_usuario) ? 'checked' : ''; ?>>
                                    <label class="form-check-label small" for="area_<?php echo $area['id']; ?>">
                                        <?php echo htmlspecialchars($area['area']); ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning py-2 small">No hay áreas disponibles</div>
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
                    <?php if ($es_admin): ?>
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
                                                // Redirigir según si es nuevo usuario o edición
                                                var redirectUrl = '<?php echo $es_nuevo_usuario ? "" : "usuarios_editar.php?usuarios_id=" . $id; ?>';
                                                if ('<?php echo $es_nuevo_usuario ? "true" : "false"; ?>' === 'true' && res.id) {
                                                    redirectUrl = 'usuarios_editar.php?usuarios_id=' + res.id;
                                                }
                                                window.location.href = redirectUrl || 'usuarios.php'; // Fallback a lista si no hay ID
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




// Agregar este código al JavaScript del formulario en usuarios_editar.php

// Manejar el checkbox de eliminar foto
$('#eliminar_foto').change(function() {
    if (this.checked) {
        // Mostrar placeholder cuando se marca eliminar
        $('#profilePreview').attr('src', '<?php echo $placeholder; ?>?t=' + Date.now());
        // Limpiar el input de archivo
        $('#usuarios_foto').val('');
    } else {
        // Restaurar imagen original si se desmarca
        $('#profilePreview').attr('src', '<?php echo $imagen_src; ?>');
    }
});

// Modificar el evento change del input file para manejar el checkbox
$('#usuarios_foto').change(function(e) {
    const file = e.target.files[0];
    if (file) {
        // Desmarcar el checkbox de eliminar si se selecciona nueva foto
        $('#eliminar_foto').prop('checked', false);
        
        // Validar tamaño de archivo
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            $.alert({
                title: 'Error',
                content: 'La foto de perfil no debe exceder los 2MB',
                type: 'red'
            });
            $(this).val(''); // Limpiar el input
            return;
        }
        
        // Validar tipo de archivo
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        const fileType = file.type.toLowerCase();
        if (!allowedTypes.includes(fileType)) {
            $.alert({
                title: 'Error',
                content: 'Solo se permiten archivos JPG, PNG y GIF',
                type: 'red'
            });
            $(this).val(''); // Limpiar el input
            return;
        }
        
        // Mostrar vista previa
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#profilePreview').attr('src', e.target.result);
        }
        reader.readAsDataURL(file);
    } else {
        // Si no hay archivo, verificar el estado del checkbox
        if ($('#eliminar_foto').is(':checked')) {
            $('#profilePreview').attr('src', '<?php echo $placeholder; ?>?t=' + Date.now());
        } else {
            $('#profilePreview').attr('src', '<?php echo $imagen_src; ?>');
        }
    }
});



    </script>    



        </div>
    </div>



<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>