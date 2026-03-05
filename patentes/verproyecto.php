<?php 
// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2024-12-30 11:52:10
// ************************************************************
?>
<?php
@session_start();
$session = session_id();

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

if ( $session != $_GET["token"] ){
        exit();
}
	
include("../conf/db.php");	
	

$sql="set names utf8";
if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}


$sql="SELECT * from patentes where id =".$_GET['proyectos_id'];

//file_put_contents('debug.txt', $sql);


if(!$result = $db->query($sql)){
    die('Hay un error [' . $db->error . ']');
}
$row = $result->fetch_assoc();


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row["nombre"];?></title>

    <!-- Leaflet CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <!-- Leaflet MarkerCluster CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <!-- jQuery and jConfirm (assuming they're used based on the JavaScript) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    
    <!-- Add your jConfirm library here -->

  <link rel="stylesheet" href="css/styles-mapa.css">

    
    <style>
        #map_canvas1 {
            height: 200px;
            width: 100%;
        }
        .sep10 {
            height: 10px;
        }
        .p5p {
            padding: 5px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .row {
            margin-bottom: 15px;
        }
        .p5p {
            background-color: #ffffff !important;
            padding:0px !important;
            border: 0px solid #fff !important;
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
                    boxWidth:'90%',
                    buttons: {
                       info: {
                        text: 'Cerrar', 
                        btnClass: 'btn-blue',
                        action: function(){}
                        }
                   },

                    content: 'url:'+dire
                }); 
    }  
    </script>  
</head>

<body>
    <a name="top" id="top"></a> 
     
    <div id="pprint">
        <div id="mainContainer">
            <div class="container">

                <div class="row">
                    <div class="col">
                        <div style="display: flex; align-items:center;">
                            <img src="img/logo_website_2021.png" style="margin-right:10px; width: 100px" alt="Logo">
                            <span style="font-size:24px"><?php echo $row["direccion"]?></span> 
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <div style="text-align:right; margin-top: -10px !important; margin-bottom:10px">
                            <!-- Content for right-aligned section if needed -->
                        </div>  
                    </div>
                </div>

                <!-- aquí va el mapa -->
                <div class="row">
                    <div class="col">
                        <div id="map_canvas1" data-html2canvas-ignore="true"></div>	  
                    </div>
                </div>
                <div class="sep10"></div>

                <?php if ($row["id"] <> '') { ?>
                <div class="row">
                    <div class="col">
                        <label>ID</label>
                        <div class="p5p"><?php echo $row["id"]?></div>      
                    </div>
                </div>  
                <div class="sep10"></div>
                <?php } ?>

                <?php if ($row["ROL"] <> '') { ?>
                <div class="row">
                    <div class="col">
                        <label>ROL</label>
                        <div class="p5p"><?php echo $row["ROL"]?></div>      
                    </div>
                </div>  
                <div class="sep10"></div>
                <?php } ?>

                <?php if ($row["RUT"] <> '') { ?>
                <div class="row">
                    <div class="col">
                        <label>RUT</label>
                        <div class="p5p"><?php echo $row["RUT"]?></div>      
                    </div>
                </div>  
                <div class="sep10"></div>
                <?php } ?>

                <?php if ($row["NOMBRE"] <> '') { ?>
                <div class="row">
                    <div class="col">
                        <label>NOMBRE</label>
                        <div class="p5p"><?php echo $row["NOMBRE"]?></div>      
                    </div>
                </div>  
                <div class="sep10"></div>
                <?php } ?>

                <?php if ($row["DIRECCION_COMERCIAL"] <> '') { ?>
                <div class="row">
                    <div class="col">
                        <label>DIRECCIÓN COMERCIAL</label>
                        <div class="p5p"><?php echo $row["DIRECCION_COMERCIAL"]?></div>      
                    </div>
                </div>  
                <div class="sep10"></div>
                <?php } ?>

                <?php if ($row["ROL_PROP"] <> '') { ?>
                <div class="row">
                    <div class="col">
                        <label>ROL PROP.</label>
                        <div class="p5p"><?php echo $row["ROL_PROP"]?></div>      
                    </div>
                </div>  
                <div class="sep10"></div>
                <?php } ?>

                <?php if ($row["GIRO"] <> '') { ?>
                <div class="row">
                    <div class="col">
                        <label>GIRO</label>
                        <div class="p5p"><?php echo $row["GIRO"]?></div>      
                    </div>
                </div>  
                <div class="sep10"></div>
                <?php } ?>

                <?php if ($row["actividad2"] <> '') { ?>
                <div class="row">
                    <div class="col">
                        <label>ACTIVIDAD 2</label>
                        <div class="p5p"><?php echo $row["actividad2"]?></div>      
                    </div>
                </div>  
                <div class="sep10"></div>
                <?php } ?>

                <?php if ($row["TIPO_PATENTE"] <> '') { ?>
                <div class="row">
                    <div class="col">
                        <label>TIPO PATENTE</label>
                        <div class="p5p"><?php echo $row["TIPO_PATENTE"]?></div>      
                    </div>
                </div>  
                <div class="sep10"></div>
                <?php } ?>

            </div>
        </div>
    </div>

    <p>&nbsp;</p>

    <script>
    // inicializa el mapa
    var map1 = L.map('map_canvas1', { zoomControl: false }).setView([-33.6405925,-70.3550152], 15);

    //RoadOnDemand
    //AerialWithLabelsOnDemand

    osm1 = new L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attributionControl: false // Esto desactiva la atribución
    });
    map1.addLayer(osm1);

    map1.options.minZoom = 2;
    map1.options.maxZoom = 18;

    var layerGroup1 = L.layerGroup().addTo(map1);

    var icn = L.icon({ iconUrl: 'pins/10.png', iconSize: [32, 32] });  
    var pin=icn;

    marker = L.marker([<?php echo $row["lat"];?>, <?php echo $row["lon"];?>], {icon: pin} ).addTo(layerGroup1)
            .bindTooltip(function (layer) { return '<?php echo $row["direccion"];?>'; }, {opacity: 1}, { offset: L.Point(0,16) });

    map1.flyTo([<?php echo $row["lat"];?>, <?php echo $row["lon"];?>], 16)

    setTimeout(function() {
      map1.invalidateSize();
    }, 5000);
    </script>
</body>
</html>