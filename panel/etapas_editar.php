<?php 
// ************************************************************
//    __       _ _ _                  _   
//   / _|_   _| | | |_ _ __ _   _ ___| |_ 
//  | |_| | | | | | __| '__| | | / __| __|
//  |  _| |_| | | | |_| |  | |_| \__ \ |_ 
//  |__| \__,_|_|_|\__|_|   \__,_|___/\__|
//                                         
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-10-10
// ************************************************************

@session_start();
include("../conf/db.php");		
include('functions.php');

$sql = "SELECT * FROM etapas WHERE etapas_id=" . mysqli_real_escape_string($db, $_GET['etapas_id']);
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
<title><?php echo $_GET["etapas_id"] == '0' ? 'Crear Nueva Etapa' : 'Editar Etapa';?></title>

<link rel="icon" type="image/png" href="../favicon.png">
<script src="js/jquery-3.5.1.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" type="text/css" href="css/styles.css"/>

<script>
function generatePDF() {
    const element = document.getElementById('pprint');
    const opt = {
        margin: 1,
        filename: '<?php echo $row["etapas_descripcion"] ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
    };
    html2pdf().from(element).set(opt).toPdf().get('pdf').then(function (pdf) {
        const totalPages = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(10);
            pdf.text(`Página ${i} de ${totalPages}`, pdf.internal.pageSize.getWidth() / 2, pdf.internal.pageSize.getHeight() - 0.5, { align: 'center' });
        }
    }).save();
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
                    <h5><?php echo $_GET["etapas_id"] == '0' ? 'CREAR NUEVA ETAPA' : 'EDITAR ETAPA';?></h5>
                </div>
                <div class="col text-end">
                    <button onclick="window.location='etapas.php'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>
                </div>
            </div>
            <div id="pprint" class="container">
                <form id="form1" name="form1">
                    <?php if($_GET["etapas_id"] != '0'){ ?>
                    <div class="row">
                        <div class="col">
                            <button onclick="generatePDF();" type="button" class="btn btn-secondary"><i class="fa-regular fa-file"></i>&nbsp;Exportar como PDF</button>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="etapas_descripcion">Descripción</label>
                                <input type="text" class="form-control" id="etapas_descripcion" name="etapas_descripcion" value="<?php echo $row["etapas_descripcion"];?>">
                            </div>
                        </div>
                    </div>
                    <input name="etapas_id" type="hidden" class="fields30" id="etapas_id" value="<?php echo $row["etapas_id"]?>" />
                    <input name="posted" type="hidden" class="fields30" id="posted" value="1" />
                    <input name="task" type="hidden" class="fields30" id="task" value="<?php echo $_GET["etapas_id"] == '0' ? 'insert' : 'update';?>" />
                    <input name="token" type="hidden" class="fields30" id="token" value="<?php echo $sid;?>" />
                    <div class="row top-info-div">
                        <?php if($row_tabla["usuarios_profile"] == 'ADMINISTRADOR' && $_GET["etapas_id"] != '0'){ ?>
                        <div class="col" style="text-align:center">
                            <button id="btdelete" onclick="eliminar();" type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar etapa</button>
                        </div>
                        <?php } ?>
                        <div class="col" style="text-align:center">
                            <button id="btsend" onclick="guardar();" type="button" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar cambios</button>
                        </div>
                    </div>
                </form>
                <form id="frm1" name="frm1">
                    <input type="hidden" name="task" id="task" value="delete">
                    <input type="hidden" class="form-control" id="etapas_id" name="etapas_id" value="<?php echo $row["etapas_id"];?>">
                    <input name="token" type="hidden" class="fields30" id="token" value="<?php echo $sid;?>" />
                </form>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
<script>
function generatePDF() {
    const element = document.getElementById('pprint');
    const opt = {
        margin: 1,
        filename: '<?php echo $row["etapas_descripcion"] ?>.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' },
    };
    html2pdf().from(element).set(opt).toPdf().get('pdf').then(function (pdf) {
        const totalPages = pdf.internal.getNumberOfPages();
        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(10);
            pdf.text(`Página ${i} de ${totalPages}`, pdf.internal.pageSize.getWidth() / 2, pdf.internal.pageSize.getHeight() - 0.5, { align: 'center' });
        }
    }).save();
}
</script>
</head>
<body>
<?php include("header.php");?>

<div class="container-fluid">
    <div class="row">
        <?php include("sidebar.php");?>
        <main role="main" class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="row top-info-div">
                <div class="col">
                    <h5><?php echo $_GET["etapas_id"] == '0' ? 'CREAR NUEVA ETAPA' : 'EDITAR ETAPA';?></h5>
                </div>
                <div class="col text-end">
                    <button onclick="window.location='etapas.php'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>
                </div>
            </div>
            <div id="pprint" class="container">
                <form id="form1" name="form1">
                    <?php if($_GET["etapas_id"] != '0'){ ?>
                    <div class="row">
                        <div class="col">
                            <button onclick="generatePDF();" type="button" class="btn btn-secondary"><i class="fa-regular fa-file"></i>&nbsp;Exportar como PDF</button>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="etapas_descripcion">Descripción</label>
                                <input type="text" class="form-control" id="etapas_descripcion" name="etapas_descripcion" value="<?php echo $row["etapas_descripcion"];?>">
                            </div>
                        </div>
                    </div>
                    <input name="etapas_id" type="hidden" class="fields30" id="etapas_id" value="<?php echo $row["etapas_id"]?>" />
                    <input name="posted" type="hidden" class="fields30" id="posted" value="1" />
                    <input name="task" type="hidden" class="fields30" id="task" value="<?php echo $_GET["etapas_id"] == '0' ? 'insert' : 'update';?>" />
                    <input name="token" type="hidden" class="fields30" id="token" value="<?php echo $sid;?>" />
                    <div class="row top-info-div">
                        <?php if($row_tabla["usuarios_profile"] == 'ADMINISTRADOR' && $_GET["etapas_id"] != '0'){ ?>
                        <div class="col" style="text-align:center">
                            <button id="btdelete" onclick="eliminar();" type="button" class="btn btn-danger"><i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar etapa</button>
                        </div>
                        <?php } ?>
                        <div class="col" style="text-align:center">
                            <button id="btsend" onclick="guardar();" type="button" class="btn btn-primary"><i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar cambios</button>
                        </div>
                    </div>
                </form>
                <form id="frm1" name="frm1">
                    <input type="hidden" name="task" id="task" value="delete">
                    <input type="hidden" class="form-control" id="etapas_id" name="etapas_id" value="<?php echo $row["etapas_id"];?>">
                    <input name="token" type="hidden" class="fields30" id="token" value="<?php echo $sid;?>" />
                </form>
            </div>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>&nbsp;</p>
        </main>
    </div>
</div>

<?php include("footer.php");?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
<script src="app.js"></script>

<script>
function guardar(){    
    $.confirm({
        title: '¡Atención!',
        content: 'Vas a guardar los cambios.<br>¿Deseas continuar?',
        buttons: {
            confirm: {
                text: 'Guardar cambios',
                action: function(){
                    $("#form1").submit();
                }
            },
            cancel: {
                text: 'Cancelar',
                action: function(){}
            }
        }
    });    
}

$(document).ready(function() {
    $('form#form1').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "api/etapas.php",
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
                                if (response != 0){
                                    window.location='etapas_editar.php?etapas_id='+response;
                                }
                            }
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error en la petición AJAX:", error);
            },
            beforeSend: function() {
                $('.fa-floppy-disk').addClass('spin-icon');
                $('#btsend').prop('disabled', true);
            },
            complete: function(xhr, status) {
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
            confirm: {
                text: 'Eliminar',
                action: function(){
                    $("#frm1").submit();
                }
            },
            cancel: {
                text: 'Cancelar',
                action: function(){}
            }
        }
    });    
}

$(document).ready(function() {
    $('form#frm1').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "api/etapas.php",
            data: new FormData(document.getElementById("frm1")),
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                $.confirm({
                    title: '¡Atención!',
                    content: 'El registro ha sido eliminado',
                    buttons: {
                        ok: function () {
                            window.location='etapas.php';
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error("Error en la petición AJAX:", error);
            },
            beforeSend: function() {
                $('.fa-trash-can').addClass('spin-icon');
                $('#btdelete').prop('disabled', true);
            },
            complete: function(xhr, status) {
                $('.fa-trash-can').removeClass('spin-icon');
                $('#btdelete').prop('disabled', false);
            }
        });
    });
});
</script>
-e 
</body>
</html>
