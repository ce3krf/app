<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2026-02-04 09:43:39
// ************************************************************

@session_start();
$session = session_id();

include("../conf/db.php");	
	
$sql="set names utf8";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}

// Consulta modificada para obtener todos los campos con JOINs
$sql="SELECT 
    p.*,
    i.instrumentos_descripcion,
    s.sector_descripcion,
    ss.subsector_descripcion,
    e.etapas_descripcion,
    pr.procesos_descripcion,
    a.area as unidad_responsable_area
FROM proyectos p
LEFT JOIN instrumentos i ON p.instrumento = i.instrumentos_id
LEFT JOIN sectores s ON p.sector = s.sector_id
LEFT JOIN subsectores ss ON p.subsector = ss.subsector_id
LEFT JOIN etapas e ON p.etapa = e.etapas_id
LEFT JOIN procesos pr ON p.proceso = pr.procesos_id
LEFT JOIN areas a ON p.unidad_responsable_id = a.id
WHERE p.id=".$_GET['proyectos_id'];

if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row = $result->fetch_assoc();

$sql="SELECT * from actividades where proyecto=".$_GET['proyectos_id']." order by ano";
if(!$actividades = $db->query($sql)){
  die('Hay un error [' . $db->error . ']');
}
// Debug: Número de actividades encontradas
$num_actividades = $actividades->num_rows;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title><?php echo $row["nombre"];?></title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0 !important;
    padding: 0 !important;
    overflow-x: hidden !important;
    width: 100% !important;
    max-width: 100vw !important;
    font-size: 14px;
}

/* Estilos para el diálogo jconfirm */
.jconfirm-box {
    max-width: 100vw !important;
    width: 100% !important;
    margin: 0 !important;
}

.jconfirm-content-pane {
    overflow-x: hidden !important;
    max-width: 100vw !important;
}

.jconfirm-content {
    overflow-x: hidden !important;
    max-width: 100vw !important;
}

.jconfirm-content > div {
    overflow-x: hidden !important;
    max-width: 100vw !important;
}

#pprint {
    width: 100% !important;
    max-width: 100vw !important;
    overflow-x: hidden !important;
    padding: 10px !important;
    box-sizing: border-box !important;
}

#mainContainer {
    width: 100% !important;
    max-width: 100vw !important;
    padding: 10px !important;
    overflow-x: hidden !important;
    box-sizing: border-box !important;
}

.container {
    width: 100% !important;
    max-width: 100vw !important;
    padding: 0 10px !important;
    margin: 0 !important;
    box-sizing: border-box !important;
}

.row {
    width: 100% !important;
    max-width: 100vw !important;
    margin: 0 !important;
    box-sizing: border-box !important;
}

.col, .col-md-4, .col-6 {
    width: 100% !important;
    padding: 8px !important;
    box-sizing: border-box !important;
}

.info-card {
    width: 100% !important;
    margin-bottom: 15px !important;
    box-sizing: border-box !important;
}

#map_canvas1 {
    width: 100% !important;
    height: 250px !important;
    max-width: 100vw !important;
}

table {
    width: 100% !important;
    max-width: 100% !important;
    font-size: 14px !important;
    display: block !important;
    overflow-x: auto !important;
}

img {
    max-width: 100% !important;
    height: auto !important;
}

label {
    font-size: 13px !important;
    padding: 8px !important;
    font-weight: 600 !important;
}

.p5p {
    font-size: 14px !important;
    word-wrap: break-word !important;
    line-height: 1.5 !important;
}

h5 {
    font-size: 16px !important;
}

/* Estilos específicos para móvil */
@media (max-width: 768px) {
    body {
        font-size: 13px !important;
    }
    
    #pprint {
        padding: 5px !important;
    }
    
    #mainContainer {
        padding: 5px !important;
    }
    
    .container {
        padding: 0 5px !important;
    }
    
    .col, .col-md-4, .col-6 {
        padding: 3px !important;
    }
    
    .info-card {
        padding: 10px !important;
        margin-bottom: 8px !important;
    }
    
    .info-card > div:first-child {
        font-size: 11px !important;
    }
    
    .info-card > div:nth-child(2) {
        font-size: 28px !important;
    }
    
    #map_canvas1 {
        height: 200px !important;
    }
    
    table {
        font-size: 12px !important;
    }
    
    th, td {
        padding: 5px !important;
    }
    
    label {
        font-size: 12px !important;
        padding: 5px !important;
    }
    
    .p5p {
        font-size: 13px !important;
    }
    
    h5 {
        font-size: 14px !important;
    }
}
</style>

<script>
function url(dire){
  jconfirm.instances[0].close();
  var dlg = $.confirm({
                title: '',
                type: 'blue',
                typeAnimated: true,
                closeIcon: true,
                useBootstrap: false,
                boxWidth:'95%',
                buttons: {
                   info: {
                    text: 'Cerrar', 
                    btnClass: 'btn-blue', 'btn-blue',
                    action: function(){}
                    }
               },
                content: 'url:'+dire
            }); 
}  
</script>

</head>

<body style="margin:0!important;padding:0!important;overflow-x:hidden!important;max-width:100vw!important;">
<a name="top" id="top"></a> 
 
<div id="pprint" style="width:100%!important;max-width:100%!important;overflow-x:hidden!important;padding:5px!important;">
<div id="mainContainer" style="width:100%!important;max-width:100%!important;padding:5px!important;overflow-x:hidden!important;">
<div class="container" style="width:100%!important;max-width:100%!important;padding:0 5px!important;margin:0!important;">

<div style="display: flex; align-items:center; width:100%; flex-wrap:wrap;">
  <img src="img/logo_website_2021.png" style="margin-right:10px; width: 100px; max-width:100px;">
    <span style="font-size:20px; flex:1; word-wrap:break-word;"><?php echo $row["nombre"]?></span> 
</div>

  <div style="text-align:right; margin-top: 5px; margin-bottom:10px; width:100%;">
    <a style="text-decoration:none; font-size:14px;" onclick="generatePDF();" href="javascript:;"><img src="img/pdf.png" width="28px">Descargar PDF</a> 
  </div>  

<!-- aquí va el mapa -->
  <div class="row" style="width:100%!important;max-width:100%!important;margin:0!important;">
    <div class="col" style="width:100%!important;padding:0!important;">
      <div id="map_canvas1" data-html2canvas-ignore="true" style="width:100%!important;max-width:100%!important;height:150px!important;"></div>	  
    </div>
  </div>
  <div class="sep10" style="height:5px;"></div>

<!-- Tarjetas de Información Clave -->
<div class="row mb-4" style="width:100%!important;max-width:100%!important;margin:0!important;">
    <div class="col-md-4" style="width:100%!important;padding:8px!important;">
        <div class="info-card" style="background-color: #e8f4f8; border-left: 4px solid #4a8ecd; padding: 15px; border-radius: 8px; margin-bottom:10px;">
            <div style="font-size: 11px; color: #6c757d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                % Avance Financiero
            </div>
            <div style="font-size: 32px; font-weight: 700; color: #023047; margin-bottom: 5px;">
                <?php echo round($row['avance_financiero'], 1); ?>%
            </div>
            <div class="progress" style="height: 8px; border-radius: 10px; background-color: #e9ecef;">
                <div class="progress-bar" style="width: <?php echo $row['avance_financiero']; ?>%; background-color: #4a8ecd;" role="progressbar"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4" style="width:100%!important;padding:8px!important;">
        <div class="info-card" style="background-color: #e8f5e9; border-left: 4px solid #4a8f5e; padding: 15px; border-radius: 8px; margin-bottom:10px;">
            <div style="font-size: 11px; color: #6c757d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                % Avance Actividades
            </div>
            <div style="font-size: 32px; font-weight: 700; color: #023047; margin-bottom: 5px;">
                <?php echo round($row['avance_actividades'], 1); ?>%
            </div>
            <div class="progress" style="height: 8px; border-radius: 10px; background-color: #e9ecef;">
                <div class="progress-bar" style="width: <?php echo $row['avance_actividades']; ?>%; background-color: #4a8f5e;" role="progressbar"></div>
            </div>
        </div>
    </div>
    <div class="col-md-4" style="width:100%!important;padding:8px!important;">
        <div class="info-card" style="background-color: #fff3e0; border-left: 4px solid #d17a4f; padding: 15px; border-radius: 8px; margin-bottom:10px;">
            <div style="font-size: 11px; color: #6c757d; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                Proceso Actual
            </div>
            <div style="font-size: 16px; font-weight: 600; color: #023047; margin-top: 5px;">
                <?php echo $row["procesos_descripcion"] ? $row["procesos_descripcion"] : "Sin proceso asignado"; ?>
            </div>
        </div>
    </div>
</div>

<div class="sep10"></div>
<div class="sep10"></div>

<!-- ID -->
<?php if ($row["nombre"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
    <div class="col" style="width:100%!important;padding:5px!important;">
      <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">ID</label>
      <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["id"]?></div>      
    </div>
  </div>  
<?php }?>    

<!-- NOMBRE DE LA INICIATIVA -->
<?php if ($row["nombre"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
    <div class="col" style="width:100%!important;padding:5px!important;">
      <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">NOMBRE DE LA INICIATIVA</label>
      <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["nombre"]?></div>      
    </div>
  </div>  
<?php }?>    

<!-- DESCRIPCIÓN -->
<?php if ($row["descripcion"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
    <div class="col" style="width:100%!important;padding:5px!important;">
      <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">DESCRIPCIÓN DE LA INICIATIVA</label>
      <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo nl2br($row["descripcion"])?></div>      
    </div>
  </div>  
<?php }?>

<!-- INSTRUMENTO -->
<?php if ($row["instrumentos_descripcion"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">INSTRUMENTO</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["instrumentos_descripcion"]?></div>      
  </div>
</div>  
<?php }?>

<!-- SECTOR -->
<?php if ($row["sector_descripcion"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">SECTOR</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["sector_descripcion"]?></div>      
  </div>
</div>  
<?php }?>

<!-- SUBSECTOR -->
<?php if ($row["subsector_descripcion"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">SUBSECTOR</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["subsector_descripcion"]?></div>      
  </div>
</div>  
<?php }?>

<!-- CODIGO BIP -->
<?php if ($row["codigo_bip"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">CÓDIGO BIP</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["codigo_bip"]?></div>      
  </div>
</div>  
<?php }?>

<!-- DISEÑO M$ -->
<?php if ($row["p_diseno"] > 0){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">DISEÑO M$</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo number_format($row["p_diseno"], 2, ',', '.')?></div>      
  </div>
</div>  
<?php }?>

<!-- EJECUCIÓN M$ -->
<?php if ($row["p_ejecucion"] > 0){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">EJECUCIÓN M$</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo number_format($row["p_ejecucion"], 2, ',', '.')?></div>      
  </div>
</div>  
<?php }?>

<!-- ETAPA -->
<?php if ($row["etapas_descripcion"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">ETAPA</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["etapas_descripcion"]?></div>      
  </div>
</div>  
<?php }?>

<!-- PROCESO -->
<?php if ($row["procesos_descripcion"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">PROCESO</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["procesos_descripcion"]?></div>      
  </div>
</div>  
<?php }?>

<!-- INICIO -->
<?php if ($row["finicio"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">INICIO</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["finicio"]?></div>      
  </div>
</div>  
<?php }?>

<!-- TÉRMINO -->
<?php if ($row["ftermino"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">TÉRMINO</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["ftermino"]?></div>      
  </div>
</div>  
<?php }?>

<!-- FUENTE -->
<?php if ($row["fuente"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">FUENTE</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["fuente"]?></div>      
  </div>
</div>  
<?php }?>

<!-- UNIDAD RESPONSABLE -->
<?php if ($row["unidad_responsable_area"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">UNIDAD RESPONSABLE</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["unidad_responsable_area"]?></div>      
  </div>
</div>  
<?php }?>

<!-- INSTITUCIONES VINCULADAS -->
<?php if ($row["instituciones_vinculadas"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">INSTITUCIONES VINCULADAS</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["instituciones_vinculadas"]?></div>      
  </div>
</div>  
<?php }?>

<!-- AVANCE FINANCIERO -->
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">% AVANCE FINANCIERO</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo number_format($row["avance_financiero"], 2, ',', '.')?>%</div>     
  </div>
</div>  

<!-- AVANCE ACTIVIDADES -->
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col" style="width:100%!important;padding:5px!important;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">% AVANCE ACTIVIDADES</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo number_format($row["avance_actividades"], 2, ',', '.')?>%</div>     
  </div>
</div>  

<!-- COORDENADAS -->
<?php if ($row["lat"] <> '' && $row["lng"] <> ''){?>  
<div class="row" style="width:100%!important;margin:0!important;">
  <div class="col-6" style="width:50%!important;padding:5px!important;float:left;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">LATITUD</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["lat"]?></div>      
  </div>
  <div class="col-6" style="width:50%!important;padding:5px!important;float:left;">
    <label style="font-size:13px!important;padding:8px!important;margin:5px 0!important;font-weight:600!important;">LONGITUD</label>
    <div class="p5p" style="font-size:14px!important;padding:8px!important;line-height:1.5!important;"><?php echo $row["lng"]?></div>      
  </div>
</div>
<div style="clear:both;"></div>
<?php }?>

<!-- ACTIVIDADES DE LA INICIATIVA -->
<div class="row" style="width:100%!important;margin:0!important;">
<div class="col" style="width:100%!important;padding:5px!important;">
<h5 style="font-size:11px!important;font-weight:bold;margin:5px 0!important;">ACTIVIDADES</h5>
</div>  
</div>

<div class="row" style="width:100%!important;max-width:100%!important;margin:0!important;">
<div class="col" style="width:100%!important;padding:0!important;">

<div style="overflow-x:auto;width:100%;max-width:100%;">
<table class="table table-hover" style="width:100%!important;font-size:14px!important;">
  <thead>
    <tr>
      <th class="label" style="padding:8px;font-size:13px;font-weight:600;" scope="col">ACTIVIDAD</th>
      <th class="label" style="padding:8px;font-size:13px;font-weight:600;text-align:center" scope="col">AÑO</th>
      <th class="label" style="padding:8px;font-size:13px;font-weight:600;text-align:right" scope="col">MONTO</th>
      <th class="label" style="padding:8px;font-size:13px;font-weight:600;text-align:center" scope="col">FINANC.</th>
      <th class="label" style="padding:8px;font-size:13px;font-weight:600;text-align:center" scope="col">ESTADO</th>
      <th class="label" style="padding:8px;font-size:13px;font-weight:600;text-align:center" scope="col">RESP.</th>
    </tr>
  </thead>
  <tbody>

  <?php 
  // Debug
  $tiene_actividades = ($actividades && $actividades->num_rows > 0);
  
  if($tiene_actividades) { 
    while($row_actividades = $actividades->fetch_assoc()) { 
  ?>
    <tr>
      <td style="padding:8px;font-size:14px;"><?php echo htmlspecialchars($row_actividades["actividad"]);?></td>
      <td style="padding:8px;font-size:14px;text-align:center"><?php echo htmlspecialchars($row_actividades["ano"]);?></td>
      <td style="padding:8px;font-size:14px;text-align:right"><?php echo number_format($row_actividades["monto"], 0, ',', '.');?></td>
      <td style="padding:8px;font-size:14px;text-align:center"><?php echo htmlspecialchars($row_actividades["finaciamiento"]);?></td>
      <td style="padding:8px;font-size:14px;text-align:center"><?php echo htmlspecialchars($row_actividades["estado"]);?></td>
      <td style="padding:8px;font-size:14px;text-align:center"><?php echo htmlspecialchars($row_actividades["responsable"]);?></td>
    </tr>
  <?php 
    } 
  } else { 
  ?>
    <tr>
      <td colspan="6" style="text-align:center;padding:10px;font-size:14px;">
        No hay actividades registradas
        <?php if(isset($actividades)) { echo " (Total: " . $actividades->num_rows . ")"; } ?>
      </td>
    </tr>
  <?php } ?>

  </tbody>
</table>
</div>

</div>  
</div>

<div class="sep10"></div>

</div>
</div>
</div>
</div>

<script>
function generatePDF() {
    const element = document.getElementById('pprint');

    const opt = {
        margin: 1,
        filename: '<?php echo $row["nombre"]?>.pdf',
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
       
<p>&nbsp;</p>

<script>
// inicializa el mapa
var map1 = L.map('map_canvas1', { zoomControl: false }).setView([-33.6405925,-70.3550152], 15);

osm1 = new L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attributionControl: false
});
map1.addLayer(osm1);

map1.options.minZoom = 2;
map1.options.maxZoom = 18;

var layerGroup1 = L.layerGroup().addTo(map1);

var icn = L.icon({ iconUrl: 'pins/10.png', iconSize: [32, 32] });  
var pin=icn;

<?php if($row["lat"] != '' && $row["lng"] != '') { ?>
marker = L.marker([<?php echo $row["lng"];?>, <?php echo $row["lat"];?>], {icon: pin} ).addTo(layerGroup1)
		.bindTooltip(function (layer) { return '<?php echo $row["nombre"];?>'; }, {opacity: 1}, { offset: L.Point(0,16) });

map1.flyTo([<?php echo $row["lng"];?>, <?php echo $row["lat"];?>], 16)
<?php } ?>

</script>

</body>
</html>