<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-12-31
// ************************************************************

@session_start();
include("../conf/db.php");		
include('functions.php');

$perfil = $_SESSION["usuarios_profile"] ?? '';
$perfiles_restringidos = ['Area', 'Invitado'];
$puede_crear = !in_array($perfil, $perfiles_restringidos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?></title>

<link rel="icon" type="image/png" href="../favicon.png">

<script src="js/jquery-3.5.1.min.js"></script>	

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<link rel="stylesheet" type="text/css" href="css/styles.css"/>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    $('#usuariosTable').DataTable({
        "ajax": {
            "url": "api/usuarios.php?task=list",
            "dataSrc": ""
        },
        "columns": [
            { "data": mix },
            { "data": "usuarios_userid" },
            { "data": "usuarios_nombre" },
            { "data": "usuarios_email" },
            { "data": "usuarios_profile" },
            { "data": "last_login" },
            { "data": "usuarios_id", "visible": false },
            { "data": "usuarios_password", "visible": false },
            { "data": "usuarios_updated", "visible": false }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/2.1.7/i18n/es-ES.json"
        },
        "pageLength": 100,
        "responsive": true,
        "dom": 'Bfrtip',
        "buttons": [
            {
                extend: 'excelHtml5',
                text: 'Exportar a Excel',
                title: 'Lista de Usuarios',
                className: 'btn btn-primary'
            }
        ]
    });
});

function mix(data, type, dataToSet) {
    var link = '<a href="usuarios_editar.php?usuarios_id=' + data.usuarios_id + '"><img src="img/document.png" width="32"></a>';
    return link;
}
</script>

</head>
<body>

<?php include("header.php");?>

<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php include("sidebar.php");?>
    </div>
    
    <div id="page-content-wrapper">
        <div class="container-fluid p-4">

            <div class="row top-info-div">
                <div class="col">
                    <h5>USUARIOS</h5>
                </div>
                <div class="col text-end">
                    <?php if ($puede_crear): ?>
                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="window.location.href='usuarios_editar.php?usuarios_id=0'">
                        <i class="fa-solid fa-plus"></i>&nbsp;Nuevo usuario
                    </button>
                    <?php endif; ?>
                </div>                        
            </div>

            <div class="container mt-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Lista de Usuarios</h5>
                                <table id="usuariosTable" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>User ID</th>
                                            <th>Nombre</th>
                                            <th>Email</th>
                                            <th>Perfil</th>
                                            <th>Último Login</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Los datos se cargarán dinámicamente -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>
