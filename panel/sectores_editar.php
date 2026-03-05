<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-12-31
// ************************************************************

@session_start();
include("../conf/db.php");		
include('functions.php');

$sql = "SELECT * FROM sectores WHERE sector_id=" . mysqli_real_escape_string($db, $_GET['sector_id']);
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $_GET["sector_id"] == '0' ? 'Crear Nuevo Sector' : 'Editar Sector';?></title>

<link rel="icon" type="image/png" href="../favicon.png">
<script src="js/jquery-3.5.1.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<link rel="stylesheet" href="css/styles.css"/>

<script>
function generatePDF() {
    const element = document.getElementById('pprint');
    const opt = {
        margin: 1,
        filename: '<?php echo $row["sector_descripcion"] ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    html2pdf().from(element).set(opt).save();
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
                    <h5><?php echo $_GET["sector_id"] == '0' ? 'CREAR NUEVO SECTOR' : 'EDITAR SECTOR';?></h5>
                </div>
                <div class="col text-end">
                    <button onclick="window.location='sectores.php'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>
                </div>
            </div>

            <div id="pprint" class="container">
                <form id="form1" name="form1">
                    <?php if($_GET["sector_id"] != '0'){ ?>
                    <div class="row">
                        <div class="col">
                            <button onclick="generatePDF();" type="button" class="btn btn-secondary"><i class="fa-regular fa-file"></i>&nbsp;Exportar como PDF</button>
                        </div>
                    </div>
                    <?php } ?>
                    
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="sector_descripcion">Descripción</label>
                                <input type="text" class="form-control" id="sector_descripcion" name="sector_descripcion" value="<?php echo $row["sector_descripcion"];?>">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="sector_color">Color</label>
                                <input type="text" class="form-control" id="sector_color" name="sector_color" value="<?php echo $row["sector_color"];?>">
                            </div>
                        </div>
                    </div>
                    
                    <input name="sector_id" type="hidden" id="sector_id" value="<?php echo $row["sector_id"]?>" />
                    <input name="posted" type="hidden" id="posted" value="1" />
                    <input name="task" type="hidden" id="task" value="<?php echo $_GET["sector_id"] == '0' ? 'insert' : 'update';?>" />
                    <input name="token" type="hidden" id="token" value="<?php echo $sid;?>" />
                    
                    <div class="row mt-4">
                        <?php if($row_tabla["usuarios_profile"] == 'ADMINISTRADOR' && $_GET["sector_id"] != '0'){ ?>
                        <div class="col text-center">
                            <button id="btdelete" onclick="eliminar();" type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar sector</button>
                        </div>
                        <?php } ?>
                        <div class="col text-center">
                            <button id="btsend" onclick="guardar();" type="button" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar cambios</button>
                        </div>
                    </div>
                </form>
                
                <form id="frm1" name="frm1">
                    <input type="hidden" name="task" value="delete">
                    <input type="hidden" name="sector_id" value="<?php echo $row["sector_id"];?>">
                    <input type="hidden" name="token" value="<?php echo $sid;?>" />
                </form>
            </div>

        </div>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>

<script>
function guardar(){    
    $.confirm({
        title: '¡Atención!',
        content: 'Vas a guardar los cambios.<br>¿Deseas continuar?',
        buttons: {
            confirm: { text: 'Guardar cambios', action: function(){ $("#form1").submit(); } },
            cancel: { text: 'Cancelar', action: function(){} }
        }
    });    
}

$(document).ready(function() {
    $('form#form1').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "api/sectores.php",
            data: new FormData(document.getElementById("form1")),
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                $.alert({
                    title: '¡Atención!',
                    content: 'Los cambios han sido guardados exitosamente',
                    buttons: {
                        confirm: {
                            text: 'Continuar',
                            action: function(){
                                if (response != 0) window.location='sectores_editar.php?sector_id='+response;
                            }
                        }
                    }
                });
            },
            error: function(xhr, status, error) { console.error("Error:", error); },
            beforeSend: function() {
                $('.fa-floppy-disk').addClass('spin-icon');
                $('#btsend').prop('disabled', true);
            },
            complete: function() {
                $('.fa-floppy-disk').removeClass('spin-icon');
                $('#btsend').prop('disabled', false);
            }
        });
    });
});

function eliminar(){    
    $.confirm({
        title: '¡Atención!',
        content: 'Vas a eliminar definitivamente este registro.<br>¿Deseas continuar?',
        buttons: {
            confirm: { text: 'Eliminar', action: function(){ $("#frm1").submit(); } },
            cancel: { text: 'Cancelar', action: function(){} }
        }
    });    
}

$(document).ready(function() {
    $('form#frm1').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "api/sectores.php",
            data: new FormData(document.getElementById("frm1")),
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                $.confirm({
                    title: '¡Atención!',
                    content: 'El registro ha sido eliminado',
                    buttons: { ok: function () { window.location='sectores.php'; } }
                });
            },
            error: function(xhr, status, error) { console.error("Error:", error); },
            beforeSend: function() {
                $('.fa-trash-can').addClass('spin-icon');
                $('#btdelete').prop('disabled', true);
            },
            complete: function() {
                $('.fa-trash-can').removeClass('spin-icon');
                $('#btdelete').prop('disabled', false);
            }
        });
    });
});
</script>
</body>
</html>
