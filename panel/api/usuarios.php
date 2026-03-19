<?php
// Enable error reporting for debugging
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

@session_start();

include("../../conf/db.php");		
include('../functions.php');

$sql = "set names utf8";
$result = $db->query($sql);    

// Configuración para manejo de archivos
$upload_dir = "../profiles/";
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_file_size = 2 * 1024 * 1024; // 2MB

// Crear directorio si no existe
if (!file_exists($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'No se pudo crear el directorio de perfiles']);
        exit;
    }
}

// **************************************************************************************
if (isset($_GET["task"]) && $_GET["task"] == "list") {

    $perfil = $_SESSION["usuarios_profile"] ?? '';
    $perfiles_restringidos = ['ÁREA', 'INVITADO'];

    if (in_array($perfil, $perfiles_restringidos)) {
        // Solo ve su propio registro
        $id_propio = (int)($_SESSION["net_fulltrust_fas_id"] ?? 0);
        $sql = "SELECT * FROM usuarios WHERE usuarios_id = ? ORDER BY usuarios_nombre";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $id_propio);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "SELECT * FROM usuarios ORDER BY usuarios_nombre";
        $result = $db->query($sql);
    }

    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = [
            'usuarios_id'               => $row['usuarios_id'],
            'usuarios_userid'           => $row['usuarios_userid'],
            'usuarios_nombre'           => $row['usuarios_nombre'],
            'usuarios_email'            => $row['usuarios_email'],
            'usuarios_profile'          => $row['usuarios_profile'],
            'last_login'                => $row['last_login'],
            'usuarios_password'         => $row['usuarios_password'],
            'usuarios_updated'          => $row['usuarios_updated'],
            'usuarios_cambiarpassword'  => $row['usuarios_cambiarpassword']
        ];
    }
    
    echo json_encode($usuarios);
    exit;
}
// **************************************************************************************

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    $task = $_POST['task'];

    try {
        switch ($task) {
            case 'ELIMINAR':
                $id = (int)$_POST['usuarios_id'];

                // Perfiles restringidos no pueden eliminar usuarios
                $perfil_sesion = $_SESSION['usuarios_profile'] ?? '';
                if (in_array($perfil_sesion, ['ÁREA', 'INVITADO'])) {
                    throw new Exception('No tiene permisos para eliminar usuarios');
                }
                
                // Obtener foto actual antes de eliminar
                $sql = "SELECT usuarios_foto FROM usuarios WHERE usuarios_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $foto_data = $result->fetch_assoc();
                
                // Eliminar instrumentos del usuario primero
                $sql_areas = "DELETE FROM usuarios_areas WHERE usuarios_id = ?";
                $stmt_areas = $db->prepare($sql_areas);
                $stmt_areas->bind_param("i", $id);
                $stmt_areas->execute();
                
                // Eliminar usuario
                $sql = "DELETE FROM usuarios WHERE usuarios_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    // Eliminar foto si existe
                    if ($foto_data && !empty($foto_data['usuarios_foto']) && file_exists($upload_dir . $foto_data['usuarios_foto'])) {
                        unlink($upload_dir . $foto_data['usuarios_foto']);
                    }
                    $response['success'] = true;
                    $response['message'] = 'Usuario eliminado correctamente';
                } else {
                    throw new Exception('Error al eliminar el usuario: ' . $db->error);
                }
                break;

            case 'CREAR':
            case 'ACTUALIZAR':
                $id = isset($_POST['usuarios_id']) ? (int)$_POST['usuarios_id'] : 0;
                $es_nuevo_usuario = ($task === 'CREAR' || $id === 0);

                // Perfiles restringidos: solo pueden actualizar su propio registro, nunca crear
                $perfil_sesion = $_SESSION['usuarios_profile'] ?? '';
                $es_restringido_api = in_array($perfil_sesion, ['ÁREA', 'INVITADO']);
                if ($es_restringido_api) {
                    if ($es_nuevo_usuario) {
                        throw new Exception('No tiene permisos para crear usuarios');
                    }
                    $id_propio = (int)($_SESSION['net_fulltrust_fas_id'] ?? 0);
                    if ($id !== $id_propio) {
                        throw new Exception('No tiene permisos para modificar este usuario');
                    }
                }
                
                // Validar campos requeridos
                // Para perfiles restringidos, usuarios_userid y usuarios_profile vienen de BD, no del POST
                $required_fields = $es_restringido_api
                    ? ['usuarios_nombre', 'usuarios_email']
                    : ['usuarios_userid', 'usuarios_nombre', 'usuarios_email', 'usuarios_profile'];
                foreach ($required_fields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("El campo $field es requerido");
                    }
                }
                
                // Preparar datos básicos
                $usuarios_userid  = trim($_POST['usuarios_userid']);
                $usuarios_nombre  = trim($_POST['usuarios_nombre']);
                $usuarios_email   = trim($_POST['usuarios_email']);
                $usuarios_profile = $_POST['usuarios_profile'];
                $usuarios_status  = isset($_POST['usuarios_status']) ? (int)$_POST['usuarios_status'] : 1;

                // Perfiles restringidos no pueden cambiar su propio perfil, estado ni userid
                if ($es_restringido_api) {
                    $sql_orig = "SELECT usuarios_userid, usuarios_profile, usuarios_status, usuarios_cambiarpassword FROM usuarios WHERE usuarios_id = ?";
                    $stmt_orig = $db->prepare($sql_orig);
                    $stmt_orig->bind_param("i", $id);
                    $stmt_orig->execute();
                    $row_orig = $stmt_orig->get_result()->fetch_assoc();
                    if ($row_orig) {
                        $usuarios_userid  = $row_orig['usuarios_userid'];
                        $usuarios_profile = $row_orig['usuarios_profile'];
                        $usuarios_status  = (int)$row_orig['usuarios_status'];
                    }
                }
                
                // Validar email
                if (!filter_var($usuarios_email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Formato de email inválido');
                }
                
                // Verificar si el userid ya existe (excluyendo el usuario actual en edición)
                $sql = $es_nuevo_usuario ? 
                    "SELECT usuarios_id FROM usuarios WHERE usuarios_userid = ?" : 
                    "SELECT usuarios_id FROM usuarios WHERE usuarios_userid = ? AND usuarios_id != ?";
                $stmt = $db->prepare($sql);
                if ($es_nuevo_usuario) {
                    $stmt->bind_param("s", $usuarios_userid);
                } else {
                    $stmt->bind_param("si", $usuarios_userid, $id);
                }
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    throw new Exception('El User ID ya existe');
                }
                
                // Manejo de contraseña con MD5
                $password_hash   = null;
                $cambiar_password = false;
                
                if ($es_nuevo_usuario) {
                    if (empty($_POST['usuarios_password'])) {
                        throw new Exception('La contraseña es requerida para usuarios nuevos');
                    }
                    if ($_POST['usuarios_password'] !== $_POST['usuarios_password_confirm']) {
                        throw new Exception('Las contraseñas no coinciden');
                    }
                    $password_hash   = md5($_POST['usuarios_password']);
                    $cambiar_password = true;
                } else {
                    // Usuario existente - solo cambiar contraseña si se solicita
                    if (isset($_POST['cambiar_password']) && !empty($_POST['usuarios_password'])) {
                        if ($_POST['usuarios_password'] !== $_POST['usuarios_password_confirm']) {
                            throw new Exception('Las contraseñas no coinciden');
                        }
                        $password_hash   = md5($_POST['usuarios_password']);
                        $cambiar_password = true;
                    }
                }
                
                // Obtener foto actual si es edición
                $foto_actual = '';
                if (!$es_nuevo_usuario) {
                    $sql = "SELECT usuarios_foto FROM usuarios WHERE usuarios_id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $foto_actual = $row['usuarios_foto'];
                    }
                }
                
                $nueva_foto = $foto_actual; // Por defecto mantener la foto actual
                
                // Verificar si se debe eliminar la foto actual
                if (isset($_POST['eliminar_foto']) && !empty($foto_actual)) {
                    $archivo_foto = $upload_dir . $foto_actual;
                    if (file_exists($archivo_foto)) {
                        unlink($archivo_foto);
                    }
                    $nueva_foto = ''; // Limpiar el nombre de la foto
                }
                
                // Procesar nueva foto si se subió
                if (isset($_FILES['usuarios_foto']) && $_FILES['usuarios_foto']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['usuarios_foto'];
                    
                    // Validar tipo de archivo usando finfo
                    $finfo     = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);
                    
                    if (!in_array($mime_type, $allowed_types)) {
                        throw new Exception('Tipo de archivo no permitido. Solo se aceptan JPG, PNG y GIF.');
                    }
                    
                    // Validar tamaño
                    if ($file['size'] > $max_file_size) {
                        throw new Exception('El archivo excede el tamaño máximo de 2MB.');
                    }
                    
                    // Generar nombre único para el archivo
                    $extension      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $nombre_archivo = 'profile_' . ($es_nuevo_usuario ? 'new_' . time() : $id) . '_' . uniqid() . '.' . $extension;
                    $ruta_archivo   = $upload_dir . $nombre_archivo;
                    
                    // Eliminar foto anterior si existe y es diferente
                    if (!empty($foto_actual) && $foto_actual !== $nombre_archivo) {
                        $archivo_anterior = $upload_dir . $foto_actual;
                        if (file_exists($archivo_anterior)) {
                            unlink($archivo_anterior);
                        }
                    }
                    
                    // Mover archivo subido
                    if (!move_uploaded_file($file['tmp_name'], $ruta_archivo)) {
                        throw new Exception('Error al subir la foto de perfil.');
                    }
                    
                    $nueva_foto = $nombre_archivo;
                }
                
                // Leer el flag de cambio de contraseña obligatorio (solo admin lo envía)
                // Los perfiles restringidos conservan el valor actual de la BD
                if ($es_restringido_api && isset($row_orig)) {
                    $forzar_cambio_password = (int)$row_orig['usuarios_cambiarpassword'];
                } else {
                    $forzar_cambio_password = isset($_POST['usuarios_cambiarpassword']) ? 1 : 0;
                }

                // Ejecutar INSERT o UPDATE
                if ($es_nuevo_usuario) {
                    $sql  = "INSERT INTO usuarios (usuarios_userid, usuarios_password, usuarios_nombre, usuarios_email, usuarios_profile, usuarios_status, usuarios_foto, usuarios_cambiarpassword, usuarios_updated) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param("sssssisi", $usuarios_userid, $password_hash, $usuarios_nombre, $usuarios_email, $usuarios_profile, $usuarios_status, $nueva_foto, $forzar_cambio_password);
                } else {
                    if ($cambiar_password) {
                        $sql  = "UPDATE usuarios SET usuarios_userid=?, usuarios_password=?, usuarios_nombre=?, usuarios_email=?, usuarios_profile=?, usuarios_status=?, usuarios_foto=?, usuarios_cambiarpassword=?, usuarios_updated=NOW() WHERE usuarios_id=?";
                        $stmt = $db->prepare($sql);
                        $stmt->bind_param("sssssisii", $usuarios_userid, $password_hash, $usuarios_nombre, $usuarios_email, $usuarios_profile, $usuarios_status, $nueva_foto, $forzar_cambio_password, $id);
                    } else {
                        $sql  = "UPDATE usuarios SET usuarios_userid=?, usuarios_nombre=?, usuarios_email=?, usuarios_profile=?, usuarios_status=?, usuarios_foto=?, usuarios_cambiarpassword=?, usuarios_updated=NOW() WHERE usuarios_id=?";
                        $stmt = $db->prepare($sql);
                        $stmt->bind_param("ssssisii", $usuarios_userid, $usuarios_nombre, $usuarios_email, $usuarios_profile, $usuarios_status, $nueva_foto, $forzar_cambio_password, $id);
                    }
                }
                
                if ($stmt->execute()) {
                    if ($es_nuevo_usuario) {
                        $id = $db->insert_id;
                        
                        // Renombrar archivo con el ID real si es usuario nuevo
                        if (!empty($nueva_foto) && strpos($nueva_foto, 'new_') !== false) {
                            $extension      = pathinfo($nueva_foto, PATHINFO_EXTENSION);
                            $nuevo_nombre   = 'profile_' . $id . '_' . time() . '.' . $extension;
                            $archivo_temporal = $upload_dir . $nueva_foto;
                            $archivo_final    = $upload_dir . $nuevo_nombre;
                            
                            if (rename($archivo_temporal, $archivo_final)) {
                                // Actualizar la base de datos con el nuevo nombre
                                $sql  = "UPDATE usuarios SET usuarios_foto = ? WHERE usuarios_id = ?";
                                $stmt = $db->prepare($sql);
                                $stmt->bind_param("si", $nuevo_nombre, $id);
                                $stmt->execute();
                                $nueva_foto = $nuevo_nombre;
                            }
                        }
                    }
                    
                    // Manejar los instrumentos del usuario
                    // Los perfiles restringidos NO pueden modificar sus instrumentos asignados
                    if (!$es_restringido_api) {
                        // Primero eliminar todos los instrumentos actuales del usuario
                        $sql_delete  = "DELETE FROM usuarios_areas WHERE usuarios_id = ?";
                        $stmt_delete = $db->prepare($sql_delete);
                        $stmt_delete->bind_param("i", $id);
                        $stmt_delete->execute();
                        
                        // Insertar los nuevos instrumentos seleccionados
                        if (isset($_POST['instrumentos']) && is_array($_POST['instrumentos'])) {
                            $sql_insert  = "INSERT INTO usuarios_areas (usuarios_id, instrumentos_id) VALUES (?, ?)";
                            $stmt_insert = $db->prepare($sql_insert);
                            
                            foreach ($_POST['instrumentos'] as $instrumento_id) {
                                $instrumento_id = (int)$instrumento_id;
                                if ($instrumento_id > 0) {
                                    $stmt_insert->bind_param("ii", $id, $instrumento_id);
                                    $stmt_insert->execute();
                                }
                            }
                        }
                    }
                    
                    $response['success'] = true;
                    $response['message'] = $es_nuevo_usuario ? 'Usuario creado correctamente' : 'Usuario actualizado correctamente';
                    $response['id']      = $id;
                } else {
                    throw new Exception('Error al ejecutar la consulta: ' . $db->error);
                }
                break;

            default:
                throw new Exception('Tarea no válida');
        }
        
    } catch (Exception $e) {
        // Limpiar archivo subido en caso de error
        if (isset($nombre_archivo) && isset($ruta_archivo) && file_exists($ruta_archivo)) {
            unlink($ruta_archivo);
        }
        $response['success'] = false;
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
?>
