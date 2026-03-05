<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-10-21
// ************************************************************
?>
<?php
@session_start();
include("../conf/db.php");		
include('functions.php');

// Inicializar el array $row para evitar errores
$row = array(
    'indicadores_id' => '',
    'indicadores_nombre' => '',
    'indicadores_descripcion' => '',
    'indicadores_tipo' => '',
    'indicadores_unidad' => '',
    'indicadores_meta' => '0.00',
    'indicadores_valor' => '0.00',
    'indicadores_avance' => '0.00',
    'indicadores_fuente' => '',
    'indicadores_frecuencia' => ''
);

// Si no es un nuevo registro, cargar los datos
if(isset($_GET['indica_id']) && $_GET['indica_id'] != '0') {
    $sql = "SELECT * FROM indicadores WHERE indicadores_id='".$_GET['indica_id']."'";
    if(!$result = $db->query($sql)){
        die('Hay un error [' . $db->error . ']');
    }
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">	
<title><?php echo $row_param["parametros_titulo"];?></title>

<script src="js/jquery-3.5.1.min.js"></script>	
<link rel="icon" type="image/png" href="../favicon.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>	

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<link rel="stylesheet" type="text/css" href="css/styles.css"/>		

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
                      <?php if($_GET["indica_id"]=='0'){ ?>
                        <h5>CREAR NUEVO INDICADOR</h5>
                      <?php } else { ?>
                        <h5>EDITAR INDICADOR</h5>
                      <?php } ?>
                    </div>
                    <div class="col text-end">
                    <button onclick="window.location='proyectos_editar.php?proyectos_id=<?php echo $_GET['proyectos_id'];?>#indicador'" type="button" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Volver</button>                                          
                    </div>                        
                </div>

                <div class="container">

<form id="form1" name="form1">

<!----------------------> 
<div class="row">
<div class="col">
 <div class="form-group">
   <label for="indicadores_nombre">NOMBRE DEL INDICADOR</label>
   <input type="text" class="form-control" id="indicadores_nombre" name="indicadores_nombre" value="<?php echo htmlspecialchars($row["indicadores_nombre"]);?>" required>
 </div>
</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col">
 <div class="form-group">
   <label for="indicadores_descripcion">DESCRIPCIÓN</label>
   <textarea class="form-control" id="indicadores_descripcion" name="indicadores_descripcion" rows="4"><?php echo htmlspecialchars($row["indicadores_descripcion"]);?></textarea>
 </div>
</div>  
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col-md-6">
 <div class="form-group">
   <label for="indicadores_tipo">TIPO DE INDICADOR</label>
   <select class="form-select" name="indicadores_tipo" id="indicadores_tipo" required>
     <option value="">Seleccione...</option>
     <option value="Cuantitativo" <?php if($row["indicadores_tipo"] == "Cuantitativo") echo "selected"; ?>>Cuantitativo</option>
     <option value="Cualitativo" <?php if($row["indicadores_tipo"] == "Cualitativo") echo "selected"; ?>>Cualitativo</option>
   </select>
 </div>
</div>

<div class="col-md-6">
 <div class="form-group">
   <label for="indicadores_unidad">UNIDAD DE MEDIDA</label>
   <input type="text" class="form-control" id="indicadores_unidad" name="indicadores_unidad" value="<?php echo htmlspecialchars($row["indicadores_unidad"]);?>" placeholder="Ej: %, unidades, personas, km">
 </div>
</div>
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col-md-4">
 <div class="form-group">
   <label for="indicadores_meta">META</label>
   <input type="number" step="0.01" class="form-control" id="indicadores_meta" name="indicadores_meta" value="<?php echo $row["indicadores_meta"];?>" required>
 </div>
</div>

<div class="col-md-4">
 <div class="form-group">
   <label for="indicadores_valor">VALOR ACTUAL</label>
   <input type="number" step="0.01" class="form-control" id="indicadores_valor" name="indicadores_valor" value="<?php echo $row["indicadores_valor"];?>">
 </div>
</div>

<div class="col-md-4">
 <div class="form-group">
   <label for="indicadores_avance">AVANCE (%)</label>
   <input type="number" step="0.01" class="form-control" id="indicadores_avance" name="indicadores_avance" value="<?php echo $row["indicadores_avance"];?>" readonly style="background-color: #f8f9fa;">
   <small class="form-text text-muted">Se calcula automáticamente</small>
 </div>
</div>
</div>
<!----------------------> 

<!----------------------> 
<div class="row">
<div class="col-md-6">
 <div class="form-group">
   <label for="indicadores_fuente">FUENTE DE INFORMACIÓN</label>
   <input type="text" class="form-control" id="indicadores_fuente" name="indicadores_fuente" value="<?php echo htmlspecialchars($row["indicadores_fuente"]);?>" placeholder="Ej: Sistema interno, Encuesta anual">
 </div>
</div>

<div class="col-md-6">
 <div class="form-group">
   <label for="indicadores_frecuencia">FRECUENCIA DE MEDICIÓN</label>
   <select class="form-select" name="indicadores_frecuencia" id="indicadores_frecuencia">
     <option value="">Seleccione...</option>
     <option value="Diaria" <?php if($row["indicadores_frecuencia"] == "Diaria") echo "selected"; ?>>Diaria</option>
     <option value="Semanal" <?php if($row["indicadores_frecuencia"] == "Semanal") echo "selected"; ?>>Semanal</option>
     <option value="Quincenal" <?php if($row["indicadores_frecuencia"] == "Quincenal") echo "selected"; ?>>Quincenal</option>
     <option value="Mensual" <?php if($row["indicadores_frecuencia"] == "Mensual") echo "selected"; ?>>Mensual</option>
     <option value="Bimestral" <?php if($row["indicadores_frecuencia"] == "Bimestral") echo "selected"; ?>>Bimestral</option>
     <option value="Trimestral" <?php if($row["indicadores_frecuencia"] == "Trimestral") echo "selected"; ?>>Trimestral</option>
     <option value="Semestral" <?php if($row["indicadores_frecuencia"] == "Semestral") echo "selected"; ?>>Semestral</option>
     <option value="Anual" <?php if($row["indicadores_frecuencia"] == "Anual") echo "selected"; ?>>Anual</option>
   </select>
 </div>
</div>
</div>
<!----------------------> 

<input name="indicadores_id" type="hidden" id="indicadores_id" value="<?php echo $row["indicadores_id"]?>" />
<input name="indicadores_iniciativa" type="hidden" id="indicadores_iniciativa" value="<?php echo $_GET["proyectos_id"]?>" />
<input name="posted" type="hidden" id="posted" value="1" />

<?php if($_GET["indica_id"]=='0'){ ?>
<input name="task" type="hidden" id="task" value="insert" />
<?php } else { ?>
<input name="task" type="hidden" id="task" value="update" />
<?php } ?>
<input name="token" type="hidden" id="token" value="<?php echo $sid;?>" />

<div style="height:30px"></div>

<div class="row top-info-div">
  <div class="col" style="text-align:center">
    <?php if($row_tabla["usuarios_profile"] == 'ADMINISTRADOR'){ ?>
      <?php if($_GET["indica_id"] != '0'){ ?>    
        <button id="btdelete" onclick="eliminar(); return false;" type="button" class="btn btn-danger">
          <i class="fa-regular fa-trash-can"></i>&nbsp;Eliminar indicador
        </button>
      <?php }?>
  </div>

  <div class="col" style="text-align:center">
    <button id="btsend" onclick="guardar(); return false;" type="button" class="btn btn-primary">
      <i class="fa-regular fa-floppy-disk"></i>&nbsp;Guardar cambios
    </button>
  </div>
    <?php }?>
</div>

</form>

<form id="frm1" name="frm1">
<input type="hidden" name="task" id="task_delete" value="delete">    
<input name="indicadores_id" type="hidden" id="indicadores_id_delete" value="<?php echo $row["indicadores_id"]?>" />
<input name="proyectos_id" type="hidden" id="proyectos_id_delete" value="<?php echo $_GET["proyectos_id"]?>" />
<input name="token" type="hidden" id="token_delete" value="<?php echo $sid;?>"> 
</form>

<script>
// Calcular avance automáticamente
function calcularAvance() {
    var meta = parseFloat($('#indicadores_meta').val()) || 0;
    var valor = parseFloat($('#indicadores_valor').val()) || 0;
    
    if(meta > 0) {
        var avance = (valor / meta) * 100;
        avance = Math.min(avance, 100);
        $('#indicadores_avance').val(avance.toFixed(2));
    } else {
        $('#indicadores_avance').val('0.00');
    }
}

$(document).ready(function() {
    $('#indicadores_meta, #indicadores_valor').on('input change', calcularAvance);
    calcularAvance();
});

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
            url: "api/indicadores.php", 
            data: new FormData(document.getElementById("form1")),
            contentType: false,       
            cache: false,             
            processData: false, 
            success: function(response) {
                $.alert({
                    title: '¡Éxito!',
                    content: 'Los cambios han sido guardados correctamente',
                    buttons: {
                      confirm: {
                        text: 'Continuar',
                        action: function(){
                            window.location='proyectos_editar.php?proyectos_id='+$('#indicadores_iniciativa').val()+'#indicador';
                        }
                      }
                    }
                });
            },
            error: function(xhr, status, error) {
                $.alert({
                    title: 'Error',
                    content: 'Ha ocurrido un error al guardar: ' + error,
                    type: 'red'
                });
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
        content: 'Vas a eliminar definitivamente este indicador.<br>¿Deseas continuar?',
        buttons: {
            confirm: {
                text: 'Eliminar',
                btnClass: 'btn-red',
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
            url: "api/indicadores.php", 
            data: new FormData(document.getElementById("frm1")),
            contentType: false,       
            cache: false,             
            processData: false,         
            success: function(response) {
                $.alert({
                    title: '¡Eliminado!',
                    content: 'El indicador ha sido eliminado correctamente',
                    buttons: {
                        ok: {
                            text: 'Continuar',
                            action: function () {
                                window.location='proyectos_editar.php?proyectos_id='+$('#proyectos_id_delete').val()+'#indicador';
                            }
                        }
                    }
                });
            },
            error: function(xhr, status, error) {
                $.alert({
                    title: 'Error',
                    content: 'Ha ocurrido un error al eliminar: ' + error,
                    type: 'red'
                });
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

                </div>
        </div>
    </div>
</div>

<?php include("footer.php");?>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="app.js"></script>
</body>
</html>
