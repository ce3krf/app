<?php
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-10-23
//
// Log de usuarios: actividad de cada usuario (solo ADMINISTRADOR)
// ************************************************************

@session_start();
include("../conf/db.php");
include('functions.php');

if (($row_tabla['usuarios_profile'] ?? '') !== 'ADMINISTRADOR') {
    ?><script>window.location='index.php';</script><?php
    exit;
}

// Por defecto: últimos 30 días
$hoy   = date('Y-m-d');
$desde = date('Y-m-d', strtotime('-30 days'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log de usuarios</title>

<link rel="icon" type="image/png" href="../favicon.png">
<script src="js/jquery-3.5.1.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/styles.css"/>

<style>
.dataTables_filter { display: block !important; margin: 0; background-color: white !important; }
.dataTables_wrapper .dataTables_paginate { margin-top: 5px; }
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 5px 10px; margin: 0 2px; border: 1px solid #ddd;
    background-color: #f9f9f9; color: #333; cursor: pointer;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background-color: #007bff; color: white; border: 1px solid #007bff;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background-color: #e9ecef; color: #6c757d; cursor: not-allowed;
}
.dt-buttons { display: none; }   /* se usa el botón propio "Exportar a Excel" */

.filtros { background: #fff; border: 1px solid #e3e8ef; border-radius: 8px; padding: 12px 14px; }
.filtros label { font-size: .78rem; color: #5b6b7f; margin-bottom: 2px; }
.filtros .form-control, .filtros .form-select { font-size: .88rem; }
#tabla { font-size: .85rem; }
#tabla td { vertical-align: top; }
.badge-accion { font-size: .72rem; font-weight: 600; padding: 3px 7px; border-radius: 4px; color: #fff; white-space: nowrap; }
.a-CREAR { background: #2e8b57; } .a-EDITAR { background: #d48a00; } .a-ELIMINAR { background: #c0392b; }
.a-LOGIN { background: #3c78b5; } .a-LOGOUT { background: #6c7a89; } .a-LOGIN_FALLIDO { background: #8e44ad; }
.a-VER { background: #95a5a6; } .a-CAMBIO_PASSWORD { background: #16a085; } .a-RESPALDO { background: #34495e; }
#aviso { display: none; }
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

            <div class="row top-info-div">
                <div class="col">
                    <h5>LOG DE USUARIOS</h5>
                </div>
                <div class="col text-end">
                    <button id="bexcel" type="button" class="btn btn-primary"><i class="fa-solid fa-file-excel"></i>&nbsp;Exportar a Excel</button>
                </div>
            </div>

            <div class="filtros mt-3">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-4">
                        <label for="f_usuario">Usuario</label>
                        <select id="f_usuario" class="form-select"><option value="">Todos los usuarios</option></select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label for="f_desde">Desde</label>
                        <input type="date" id="f_desde" class="form-control" value="<?php echo $desde;?>">
                    </div>
                    <div class="col-6 col-md-2">
                        <label for="f_hasta">Hasta</label>
                        <input type="date" id="f_hasta" class="form-control" value="<?php echo $hoy;?>">
                    </div>
                    <div class="col-8 col-md-2">
                        <label for="f_accion">Acción</label>
                        <select id="f_accion" class="form-select">
                            <option value="">Todas</option>
                            <option value="LOGIN">Ingreso</option>
                            <option value="LOGIN_FALLIDO">Ingreso fallido</option>
                            <option value="LOGOUT">Salida</option>
                            <option value="CREAR">Creación</option>
                            <option value="EDITAR">Edición</option>
                            <option value="ELIMINAR">Eliminación</option>
                            <option value="VER">Pantallas visitadas</option>
                            <option value="CAMBIO_PASSWORD">Cambio de contraseña</option>
                            <option value="RESPALDO">Respaldo</option>
                        </select>
                    </div>
                    <div class="col-4 col-md-2">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="f_incluir_ver">
                            <label class="form-check-label" for="f_incluir_ver" style="font-size:.82rem;">Incluir pantallas visitadas</label>
                        </div>
                    </div>
                </div>
            </div>

            <div id="aviso" class="alert alert-warning mt-2 py-2" style="font-size:.85rem;"></div>

            <div class="mt-3">
                <table id="tabla" class="table display hoverable" style="width: 100%">
                    <thead>
                        <tr>
                            <th>Fecha y hora</th>
                            <th>Usuario</th>
                            <th>Perfil</th>
                            <th>Acción</th>
                            <th>Opción</th>
                            <th>Tabla</th>
                            <th>Registro</th>
                            <th>Detalle</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>

<script>
var OPCIONES = {
    'index.php': 'Inicio', 'proyectos.php': 'Iniciativas', 'proyectos_editar.php': 'Editar iniciativa',
    'avance.php': 'Avance implementación', 'avancei.php': 'Avance financiero',
    'actividades_editar.php': 'Editar actividad', 'indicadores_editar.php': 'Editar indicador',
    'areas.php': 'Áreas', 'areas_editar.php': 'Editar área', 'instrumentos.php': 'Instrumentos',
    'instrumentos_editar.php': 'Editar instrumento', 'sectores.php': 'Sectores', 'sectores_editar.php': 'Editar sector',
    'subsectores.php': 'Subsectores', 'subsectores_editar.php': 'Editar subsector', 'tipo.php': 'Tipo',
    'tipo_editar.php': 'Editar tipo', 'etapas.php': 'Etapas', 'etapas_editar.php': 'Editar etapa',
    'procesos.php': 'Procesos', 'procesos_editar.php': 'Editar proceso', 'usuarios.php': 'Usuarios',
    'usuarios_editar.php': 'Editar usuario', 'backup.php': 'Respaldo', 'guia_uso.php': 'Guía de uso',
    'info_sistema.php': 'Información del sistema', 'cambiar_password.php': 'Cambiar contraseña',
    'login.php': 'Login', 'logout.php': 'Salir', 'auditoria.php': 'Auditoría', 'log_usuarios.php': 'Log de usuarios',
    'patentes.php': 'Patentes', 'patentes_editar.php': 'Editar patente',
    'permisos.php': 'Permisos', 'permisos_editar.php': 'Editar permiso'
};
var TABLAS = {
    'proyectos': 'Iniciativas', 'actividades': 'Actividades', 'indicadores': 'Indicadores', 'areas': 'Áreas',
    'etapas': 'Etapas', 'instrumentos': 'Instrumentos', 'procesos': 'Procesos', 'tipo': 'Tipo',
    'sectores': 'Sectores', 'subsectores': 'Subsectores', 'usuarios': 'Usuarios', 'patentes': 'Patentes',
    'peredifica': 'Permisos de edificación'
};
var ACCIONES = {
    'LOGIN': 'Ingreso', 'LOGIN_FALLIDO': 'Ingreso fallido', 'LOGOUT': 'Salida', 'CREAR': 'Creación',
    'EDITAR': 'Edición', 'ELIMINAR': 'Eliminación', 'VER': 'Visita', 'CAMBIO_PASSWORD': 'Cambio de contraseña',
    'RESPALDO': 'Respaldo'
};

function esc(s) {
    if (s === null || s === undefined) return '';
    return String(s).replace(/[&<>"']/g, function (c) {
        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
}

// Para Excel se exporta texto plano; en pantalla, HTML
function txt(type, plano, html) { return (type === 'display') ? html : plano; }

$(document).ready(function () {

    var token = $("#sid").val();

    var tabla = $('#tabla').DataTable({
        searching: true,
        ordering: true,
        order: [[0, 'desc']],
        bPaginate: true,
        iDisplayLength: 50,
        dom: 'B<"top"ifp<"clear">>rt<"bottom"ip<"clear">>',
        buttons: [{
            extend: 'excelHtml5',
            title: 'Log de usuarios',
            filename: function () {
                var u = $('#f_usuario option:selected').val() ? $('#f_usuario option:selected').text() : 'todos';
                return 'log_usuarios_' + u.replace(/[^a-z0-9]+/gi, '_') + '_' + $('#f_desde').val() + '_' + $('#f_hasta').val();
            },
            exportOptions: { orthogonal: 'export' }
        }],
        data: [],
        columns: [
            { data: 'fecha_hora', render: function (v, t) { return txt(t, v, '<span style="white-space:nowrap">' + esc(v) + '</span>'); } },
            { data: null, render: function (r, t) {
                var n = r.usuario_nombre || r.usuario_login || '-';
                var plano = n + (r.usuario_login ? ' (' + r.usuario_login + ')' : '');
                return txt(t, plano, esc(n) + (r.usuario_login ? '<br><small class="text-muted">' + esc(r.usuario_login) + '</small>' : ''));
            } },
            { data: 'perfil', render: function (v) { return esc(v); } },
            { data: 'accion', render: function (v, t) {
                return txt(t, ACCIONES[v] || v, '<span class="badge-accion a-' + esc(v) + '">' + esc(ACCIONES[v] || v) + '</span>');
            } },
            { data: 'opcion', render: function (v) { return esc(OPCIONES[v] || v); } },
            { data: 'tabla',  render: function (v) { return esc(TABLAS[v] || v); } },
            { data: null, render: function (r, t) {
                if (!r.registro_id) return '';
                var plano = '#' + r.registro_id + (r.registro_desc ? ' ' + r.registro_desc : '');
                return txt(t, plano, '#' + esc(r.registro_id) + (r.registro_desc ? '<br><small>' + esc(r.registro_desc) + '</small>' : ''));
            } },
            { data: 'detalle', render: function (v) { return esc(v); } },
            { data: 'ip', render: function (v, t) { return txt(t, v || '', '<small>' + esc(v) + '</small>'); } }
        ],
        language: {
            sProcessing: "<p align='center'><img src='img/ajax-loader.svg' /></p>",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Sin actividad para los filtros seleccionados",
            sInfo: "_START_ al _END_ de _TOTAL_",
            sInfoEmpty: "0 registros",
            sInfoFiltered: "(de _MAX_)",
            sSearch: "",
            searchPlaceholder: "Buscar",
            oPaginate: { sFirst: "<<", sLast: ">>", sNext: ">", sPrevious: "<" }
        }
    });

    function cargar() {
        $('#aviso').hide();
        $.getJSON('api/log_usuarios.php', {
            token:       token,
            task:        'list',
            usuario_id:  $('#f_usuario').val(),
            desde:       $('#f_desde').val(),
            hasta:       $('#f_hasta').val(),
            accion:      $('#f_accion').val(),
            incluir_ver: $('#f_incluir_ver').is(':checked') ? 1 : ''
        }, function (r) {
            tabla.clear().rows.add(r.data).draw();
            if (r.truncado) {
                $('#aviso').text('Se muestran los ' + r.limite.toLocaleString('es-CL') +
                    ' registros más recientes. Acote el rango de fechas para ver el resto.').show();
            }
        }).fail(function () {
            $('#aviso').text('No se pudo cargar el log. Recargue la página o inicie sesión nuevamente.').show();
        });
    }

    // Usuarios para el filtro
    $.getJSON('api/log_usuarios.php', { token: token, task: 'usuarios' }, function (lista) {
        lista.forEach(function (u) {
            var t = (u.nombre || u.login) + ' (' + u.login + ')' + (u.eliminado == 1 ? ' - eliminado' : '');
            $('#f_usuario').append($('<option>').val(u.id).text(t));
        });
    });

    $('.filtros').on('change', 'input, select', cargar);

    $('#bexcel').on('click', function () {
        $('.buttons-excel').trigger('click');
        // Dejar constancia en la auditoría
        $.post('api/log_usuarios.php', {
            token: token, task: 'exportado',
            filtro: 'usuario: ' + $('#f_usuario option:selected').text() + ', ' + $('#f_desde').val() + ' a ' + $('#f_hasta').val()
        });
    });

    cargar();
});
</script>
</body>
</html>
