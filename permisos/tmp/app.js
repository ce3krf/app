// ************************************************************
// Autor: Marcelo Jiménez
// Fulltrust Software
// https://www.fulltrust.net
// Fecha última actualización: 2025-01-03 15:01:37
// ************************************************************

var max = true;
var min = true;
var url = '';
var howmany = 0;
var usargps = false;
var w_avance = "";
var dlg = '';




function ColorLuminance(hex, lum) {
    // validate hex string
    hex = String(hex).replace(/[^0-9a-f]/gi, '');
    if (hex.length < 6) {
        hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
    }
    lum = lum || 0;
    // convert to decimal and change luminosity
    var rgb = "#", c, i;
    for (i = 0; i < 3; i++) {
        c = parseInt(hex.substr(i*2,2), 16);
        c = Math.round(Math.min(Math.max(0, c + (c * lum)), 255)).toString(16);
        rgb += ("00"+c).substr(c.length);
    }
    return rgb;
}

// muestra los proyectos
function muestra() {
    layerGroup.clearLayers();

    $("#wait").show();

    var busca = $('#searchInput').val();

    var tipo = $("input[name='tipo']:checked").map(function() {return this.value;}).get().join(',');
    var area = $("input[name='area']:checked").map(function() {return this.value;}).get().join(',');
    var origen = $("input[name='origen']:checked").map(function() {return this.value;}).get().join(',');
    var estado = $("input[name='estado']:checked").map(function() {return this.value;}).get().join(',');
    //console.log(tipo);
    //console.log(area);

    url = "datos_detalle.php?tipo=" + tipo + '&search' + busca + '&area=' + area + '&origen=' + origen + '&estado=' + estado + '&ubicacion=' + "&token=" + $('#token').val();
    //console.log( "datos.php?area="+area+'&tipo='+tipo+'&origen='+origen+'&estado='+estado );

    tabla(url);

    $('#tabla').DataTable().ajax.url(url);
    $('#tabla').DataTable().ajax.reload();

    howmany = 0;
    $('#m1').html('<span style="color:gold;">&#9733; </span> 0 permisos');

    $.ajax({
        url: "datos.php?area=" + area + '&search=' + busca + '&tipo=' + tipo + '&origen=' + origen + '&estado=' + estado + "&token=" + $('#token').val(),
        type: 'GET',
        success: function (data) {

            var data = $.parseJSON(data);

            j = 0;

            if (data) {
                for (var i = 0; i < data.aaData.length; i++) {

                    howmany++;
                    $('#m1').html('<span style="color:gold;">&#9733; </span>' + howmany + ' permisos');

                    agregar( data.aaData[i] );

                }
            }
            $("#wait").hide();
        },
        error: function() {
            $("#wait").hide();
        }
    });

}


function agregar(arr) {
    var icono = L.icon({ iconUrl: 'pins/c.png', iconSize: [48, 48] });

    // Definir colores según la categoría de arr['destino2']
    var clr;
    var strk;
    
    switch (arr['destino2']) {
        case 'Comercial':
            clr = '#FF5733'; // Naranja vibrante
            break;
        case 'Energía':
            clr = '#FFD700'; // Amarillo dorado
            break;
        case 'Equipamiento':
            clr = '#8B008B'; // Púrpura oscuro
            break;
        case 'Habitacional':
            clr = '#00CED1'; // Turquesa
            break;
        case 'Industria':
            clr = '#696969'; // Gris oscuro
            break;
        case 'Salud, Educación, Deportes':
            clr = '#32CD32'; // Verde lima
            break;
        case 'Salud, Educación, Deportes, Cultura':
            clr = '#32CD32'; // Verde lima
            break;
        case 'Servicios':
            clr = '#1E90FF'; // Azul brillante
            break;
        case 'Turismo':
            clr = '#FF69B4'; // Rosa intenso
            break;
        default:
            clr = '#FF0000'; // Rojo por defecto si no coincide ninguna categoría
    }

    // Oscurecer el color para el borde
    var strk = darkenColor(clr, 0.4); // Usa la función existente darkenColor

    var marker = L.circleMarker([arr['lat'], arr['lon']], {
        color: strk,
        fillColor: clr,
        weight: 2,
        fillOpacity: 0.8,
        radius: 10,  // Radius in pixels
        meters: 1
    }).addTo(layerGroup).on('click', function(e) {
        var rnd = Math.random();
        var tipo = $("input[name='tipo']:checked").map(function() { return this.value; }).get().join(',');
        var area = $("input[name='area']:checked").map(function() { return this.value; }).get().join(',');
        var origen = $("input[name='origen']:checked").map(function() { return this.value; }).get().join(',');
        var estado = $("input[name='estado']:checked").map(function() { return this.value; }).get().join(',');

        min = true;
    });

    var text2 = L.tooltip({
        direction: 'center',
        className: 'etiqueta',
        permanent: true
    }).setLatLng([arr['lat'], arr['lon']]);
    marker.bindTooltip(text2);

    tooltip = "<b>" + arr['direccion'] + "</b><br>";
    tooltip += "<i>" + arr['destino2'] + "</i><br>";
    tooltip += "Localidad: " + arr['localidad'] + "<br>";
    tooltip += '<span class="mas_info">' + "Click para más información" + '</span>';

    marker.bindPopup(tooltip);
    marker.on('mouseover', function(e) {
        this.openPopup();
    });
    marker.on('mouseout', function(e) {
        this.closePopup();
    });
    marker.on('click', function(e) {
        ver(arr['id']);
    });
}





 // inicializa el mapa
 //$( document ).ready(function() {   


var map = L.map('map_canvas', {
    zoomControl: false,
    center: [-33.6405925,-70.3550152], // Specify the center coordinates of your map
    zoom: 14, // Specify the initial zoom level
    maxZoom: 18, // Specify the maximum zoom level
    minZoom: 5 // Specify the minimum zoom level
});

var maxBounds = L.latLngBounds(L.latLng(-90, -180), L.latLng(90, 180));
map.setMaxBounds(maxBounds);



L.control.scale({
    position: 'bottomright',
    metric: true,
    imperial: false
}).addTo(map);

// Agrega una clase al control
document.querySelector('.leaflet-control-scale').style.bottom = '130px';
document.querySelector('.leaflet-control-scale').style.right = '5px';


// grupos
var layerGroup = L.markerClusterGroup({
	spiderfyOnMaxZoom: true,
	showCoverageOnHover: true,
	zoomToBoundsOnClick: false,
	disableClusteringAtZoom: 14, // Adjust zoom level as needed
	//maxClusterRadius: 150 // Ungroup clusters at 50 meters apart
});
map.addLayer(layerGroup);


osm = new L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://www.comunidad.city">comunidad.city</a> | contributors'
});
map.addLayer(osm);

muestra();
restaurar();

//});  



function clearBaseLayers() {
    if (map.hasLayer(osm)) {
        map.removeLayer(osm);
    }
    if (typeof esri !== 'undefined' && map.hasLayer(esri)) {
        map.removeLayer(esri);
    }
}

function road() {
    clearBaseLayers()

    // Remove any existing attribution
    osm = new L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://www.comunidad.city">comunidad.city</a> | contributors'
    });
    map.addLayer(osm);
}

function sat() {
    clearBaseLayers()

    // Add satellite tile layer (Esri World Imagery)
    esri = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 19,
        attribution: 'Tiles &copy; <a href="https://www.esri.com/">Esri</a> &mdash; Source: Esri, Earthstar Geographics, CNES/Airbus DS, USDA, USGS, AeroGRID, IGN, and the GIS User Community'
    });
    map.addLayer(esri);
}

function opciones(){
    $('#side_menu').toggle(300);
}

function reestablecer(){
    $("#tipo").val($("#tipo option:first").val());    
    $("#estado").val($("#estado option:first").val());        
    $("#estado").val($("#estado option:first").val());   
    layerGroup.clearLayers();
    muestra();
}





function maximizar(){
    if (max) {
        max = false;
        $("#paneltabla").css("height", "50vh");
    } else {
        max = true;
        $("#paneltabla").css("height", "100vh");
    }
}

function restaurar(){
    $("#esperar").css("display", "block");
    if (min) {
        min = false;
        $("#paneltabla").animate({ height: '50vh'}, 0);
        $("#divs").css("display", "block");
        $("#icn").attr("src","img/close_icon.png");
        $("#sep30").css("height", "30px");
        $('div.dataTables_scrollBody').height( $('#paneltabla').height() -70 );
    } else {
        min = true;
        $("#paneltabla").css("display", "block !important");
        $("#divs").css("display", "none");
        $("#icn").attr("src","img/list.png");
        $("#paneltabla").animate({ height: '30px'}, 300);
        $("#sep30").css("height", "0px");
        $('div.dataTables_scrollBody').height( 0 );
    }
    setTimeout(function() {
        $("#esperar").css("display", "none");
    }, 3000);
}




function tabla(url){

    var tb = $('#tabla').dataTable({

        searching: true,

        search: {
            regex: false,
            smart: false
        },

        retrieve: true,
        searchHighlight: true,
        serverSide: false,

		responsive: true,
		processing: true,

        sScrollY:"300px",
        bInfo : false,


        paging: false,

        order: [[1, 'asc']],

        "columnDefs": [
            { className: "celdat05", "targets": 0, width: "5% !important" },
            { className: "celdat20", "targets": 1, width: "65%" },
            { className: "celdat10", "targets": 2, width: "10%" },                        
            { className: "celdat10", "targets": 3, width: "10%" },
            { className: "celdat10", "targets": 4, width: "10%" },
        ],


      "sAjaxSource": url,
      "aoColumns": [
            { mData: mix} ,
            { mData: 'nombre'} ,
            { mData: 'area'} ,
            { mData: 'estado'} ,
            { mData: 'origen'} ,
          ],


          "language":
          {
              "sProcessing":     "<p align='center'><img src='img/ajax-loader.svg' /></p>",
              "sLengthMenu":     "Mostrar _MENU_ registros",
              "sZeroRecords":    "No se encontraron resultados",
              "sEmptyTable":     "Ningún dato disponible en esta tabla",
              "sInfo":           "_START_ al _END_ de _TOTAL_ ",
              "sInfoEmpty":      "0 al 0 ",
              "sInfoFiltered":   "_MAX_ registros)",
              "sInfoPostFix":    "",
              "sSearch":         "",
              "searchPlaceholder": "Buscar",
              "sUrl":            "",
              "sInfoThousands":  ",",
              "sLoadingRecords": "",
              "oPaginate": {
                  "sFirst":    "<<",
                  "sLast":     ">>",
                  "sNext":     ">",
                  "sPrevious": "<"
              },
              "oAria": {
                  "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                  "sSortDescending": ": Activar para ordenar la columna de manera descendente"
              }
          }

    });

    
    $('#tabla').DataTable().ajax.reload(
        function( settings, json ) {
            $('#filas').text( $('#tabla').DataTable().rows( ).count() );
        }
    );

    $('#tabla').DataTable().on( 'search.dt', function () {
        var info = $('#tabla').DataTable().page.info();
        $('#filas').text( info.recordsDisplay );
    } );


    $('div.dataTables_scrollBody').height( $('#paneltabla').height() -70 );

    $('#buscar').keyup(function(){
        //tb.search($(this).val()).draw();
        $('#tabla').dataTable().fnFilter(this.value);
    })    

    

}

$( window ).resize(function() {
    $('div.dataTables_scrollBody').height( $('#paneltabla').height() -70 );
});


function mix(data, type, dataToSet) {
    var r = "";
    var r  = r + '<a onclick="ver('+data.id+')" href="javascript:;"><img style="width:24px" src="img/document.png"></a>';
    return r;
}	



function ver(id) {
  const token = document.getElementById('token').value;
  const url = `verproyecto.php?proyectos_id=${id}&token=${token}&rnd=${Math.random()}`;
  
  const modal = document.createElement('div');
  modal.className = 'modal-overlay';
  modal.innerHTML = `
    <div class="modal-container">
      <div class="modal-close" data-action="close">&times;</div>
      <div class="loading-container">
        <div class="spinner"></div>
        <p>Cargando contenido...</p>
      </div>
      <iframe src="${url}" class="modal-iframe" style="display: none;"></iframe>
    </div>
  `;
  
  // Estilos CSS para la animación de carga
  const style = document.createElement('style');
  style.textContent = `
    
    .loading-container {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100%;
      color: #666;
    }
    
    .spinner {
      width: 40px;
      height: 40px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 20px;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  `;
  
  // Agregar estilos al head si no existen
  if (!document.querySelector('#modal-styles')) {
    style.id = 'modal-styles';
    document.head.appendChild(style);
  }
  
  const iframe = modal.querySelector('.modal-iframe');
  const loadingContainer = modal.querySelector('.loading-container');
  
  // Event listener para cuando el iframe termine de cargar
  iframe.addEventListener('load', function() {
    loadingContainer.style.display = 'none';
    iframe.style.display = 'block';
  });
  
  // Event listener para manejar errores de carga
  iframe.addEventListener('error', function() {
    loadingContainer.innerHTML = `
      <div style="text-align: center; color: #e74c3c;">
        <p>❌ Error al cargar el contenido</p>
        <p style="font-size: 14px;">Por favor, inténtelo de nuevo más tarde</p>
      </div>
    `;
  });
  
  // Close modal events
  modal.addEventListener('click', function(e) {
    if (e.target === modal || e.target.dataset.action === 'close') {
      modal.remove();
    }
  });
  
  // ESC key to close
  const escHandler = function(e) {
    if (e.key === 'Escape') {
      modal.remove();
      document.removeEventListener('keydown', escHandler);
    }
  };
  document.addEventListener('keydown', escHandler);
  
  document.body.appendChild(modal);
}



restaurar();

// muestra las coordenadas al hacer click en el mapa, útil para validar ubicaciones. No es necesario para el funcionamiento del mapa

map.on('click', function(e){
    var coord = e.latlng;
    var lat = coord.lat;
    var lng = coord.lng;
    console.log(lat + ", " + lng);
});	


//map.flyTo([-23.103999, -70.440729], 15)

function checkarea(){
    $("input[name='area']:checkbox").prop('checked',true);
}
function uncheckarea(){
    $("input[name='area']:checkbox").prop('checked',false);
}


function checktipo(){
    $("input[name='tipo']:checkbox").prop('checked',true);
}
function unchecktipo(){
    $("input[name='tipo']:checkbox").prop('checked',false);
}


function checkorigen(){
    $("input[name='origen']:checkbox").prop('checked',true);
}
function uncheckorigen(){
    $("input[name='origen']:checkbox").prop('checked',false);
}


function checkestado(){
    $("input[name='estado']:checkbox").prop('checked',true);
}
function uncheckestado(){
    $("input[name='estado']:checkbox").prop('checked',false);
}





function acerca(){
    var x = Math.random();
    $.confirm({
              title: '',
              type: 'default', // Remove 'blue' to avoid predefined styles
              boxWidth: '80%',
              typeAnimated: true,
              closeIcon: true,
              useBootstrap: false,
              confirmButtonColor: '#d296dd',
              buttons: {
                 info: {
                  text: 'Continuar', 
                  btnClass: 'btn-custom', // Use a custom class
                  action: function(){}
                  }
             },

              content: 'url:acerca.php?x='+x
          }); 		
}











map.on('click', function(e){

    var coord = e.latlng;
    gpslat = coord.lat;
    gpslng = coord.lng;
    //console.log( gpslat + " " + gpslng );
  
    if (!usargps){
      //muestra el punto en la locación en que se hizo click
      //addo(gpslat, gpslng);
    }
  
  
    $.get('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat='+gpslat+'&lon='+gpslng, function(data){
      
      var calle = data.address.road;
      if (typeof calle == 'undefined'){
        calle = '';
      }
      var numero = data.address.house_number;
      if (typeof numero == 'undefined'){
        numero = '';
      }    
      var ciudad = data.address.city;
      if (typeof ciudad == 'undefined'){
        ciudad = '';
      } else {
        ciudad = ', '+data.address.city;
      }   
      
      var x = document.getElementById("snackbar");
      x.innerHTML = calle+' '+numero+ciudad;
      x.className = "show";
      
    
      // After 3 seconds, remove the show class from DIV
      setTimeout(function(){ x.className = x.className.replace("show", ""); }, 3000);
      
    });
  
    
  
    });




    function s(){
        if ( $("#m5").height() == '20' ){

            $("#m5").animate({
                height: "250px",
                width: "250px"
            }, 200);

        } else {

            $("#m5").animate({
                height: "30px",
                width: "130px"
            }, 200);
        }
    };






      function avance(area, tipo) {
        var x = Math.random();
        var w_avance = $.confirm({
            title: '',
            type: 'default', // Remove 'blue' to avoid predefined styles
            boxWidth: '95%',
            typeAnimated: true,
            closeIcon: true,
            useBootstrap: false,
            buttons: {
                info: {
                    text: 'Continuar',
                    btnClass: 'btn-custom', // Use a custom class
                    action: function () {}
                }
            },
            content: 'url:avance.php?area=' + area + '&tipo=' + tipo + "&x=" + x
        });
    }
         





    function darkenColor(hexColor, percent) {
        // Convert hex to RGB
        var r = parseInt(hexColor.slice(1, 3), 16);
        var g = parseInt(hexColor.slice(3, 5), 16);
        var b = parseInt(hexColor.slice(5, 7), 16);
    
        // Calculate HSL values
        var hsl = rgbToHsl(r, g, b);
    
        // Decrease lightness by the specified percent
        hsl[2] *= 1 - percent;
    
        // Convert HSL back to RGB
        var rgb = hslToRgb(hsl[0], hsl[1], hsl[2]);
    
        // Convert RGB to hex
        var darkHexColor = rgbToHex(rgb[0], rgb[1], rgb[2]);
    
        return darkHexColor;
    }
    
    function rgbToHsl(r, g, b) {
        r /= 255;
        g /= 255;
        b /= 255;
        var max = Math.max(r, g, b),
            min = Math.min(r, g, b);
        var h, s, l = (max + min) / 2;
    
        if (max === min) {
            h = s = 0;
        } else {
            var d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
    
        return [h, s, l];
    }
    
    function hslToRgb(h, s, l) {
        var r, g, b;
    
        if (s === 0) {
            r = g = b = l; // achromatic
        } else {
            var hue2rgb = function (p, q, t) {
                if (t < 0) t += 1;
                if (t > 1) t -= 1;
                if (t < 1 / 6) return p + (q - p) * 6 * t;
                if (t < 1 / 2) return q;
                if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
                return p;
            };
    
            var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            var p = 2 * l - q;
            r = hue2rgb(p, q, h + 1 / 3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1 / 3);
        }
    
        return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
    }
    
    function rgbToHex(r, g, b) {
        return "#" + ((1 << 24) | (r << 16) | (g << 8) | b).toString(16).slice(1);
    }
    

    


    function cierra() {
        dlg.close();
    }






    // zoom
    $('#zoom-in').on('click', function () {
        map.zoomIn();
    });

    $('#zoom-out').on('click', function () {
        map.zoomOut();
    });    

    $('#acerca').on('click', function () {
        // acerca();
    });    



/*
    $(document).ready(function() {
        // Show "acerca" if more than one hour has passed since last time
        var lastAcerca = localStorage.getItem('acercaLastShown');
        var now = Date.now();
        if (!lastAcerca || now - parseInt(lastAcerca, 10) > 3600000) { // 1 hour = 3600000 ms
            acerca();
            localStorage.setItem('acercaLastShown', now.toString());
        }
    });
*/














function edifica(){

    var icono = L.icon({ iconUrl: 'pins/c.png', iconSize: [48, 48] });    

    //console.log(cod + '-' + ubi + '-' + color);
        

//    try{

    var clr=''+color;
    var strk=clr;
    
    var originalColor = color;
    var oscurecido = darkenColor( originalColor, 0.4 ); // 
    strk = oscurecido;


    //var r = (cuantos * 2) + 10;
    var r = 20;

    var marker = L.circleMarker([lat, lng], {
        color: strk,
        fillColor: clr,
        weight: 2,
        fillOpacity: 0.8,
        radius: 10,  // Radius in pixels
        meters: 1 
    }).addTo(layerGroup).on('click', function(e) {    

    
//        var marker  = L.marker([lat, lng], {icon: icono}).addTo(layerGroup).on('click', function(e) {
        var rnd  = Math.random();


        var  tipo =  $("input[name='tipo']:checked").map(function() {return this.value;}).get().join(',')    
        var  area =  $("input[name='area']:checked").map(function() {return this.value;}).get().join(',')          
        var  origen =  $("input[name='origen']:checked").map(function() {return this.value;}).get().join(',')          
        var  estado =  $("input[name='estado']:checked").map(function() {return this.value;}).get().join(',')          

        url = "datos_detalle.php?area="+area+'&tipo='+tipo+'&origen='+origen+'&estado='+estado+'&ubicacion='+cod+ "&token=" + $('#token').val();
        //url = "datos_detalle.php?estado=&tipo=&ubicacion="+cod;
        tabla(url);
        $('#tabla').DataTable().ajax.url(url);
        $('#tabla').DataTable().ajax.reload();
        
        min = true;
        //restaurar();


   

        });

        


        var text2 = L.tooltip({
            direction: 'center',
            className: 'etiqueta',
            permanent: true
        })
        .setContent(cuantos)
        .setLatLng([lat, lng]);
        marker.bindTooltip(text2);

        var iniciativa = "iniciativa";
        if (cuantos > 1){
            var iniciativa = "iniciativas";
        }

        tooltip = "<b>" + nom +  "</b><br>";
        tooltip = tooltip + "Área: " + area + "<br>";
        tooltip = tooltip + '<span class="mas_info">'+"Click para más información"+ '</span>';

        marker.bindPopup( tooltip );
        marker.on('mouseover', function (e) {
            this.openPopup();
        });
        marker.on('mouseout', function (e) {
            this.closePopup();
        });
        marker.on('click', function (e) {
            ver(id);
        });

//    } catch {
//    }

}    






document.addEventListener('DOMContentLoaded', function() {
  // Select all li elements
  const listItems = document.querySelectorAll('li');

  // Add click event listener to each li
  listItems.forEach(item => {
    item.addEventListener('click', function() {
      // Get the text content, excluding the div (square)
      const text = this.textContent.trim();
      // Set the input value
      const searchInput = document.getElementById('searchInput');
      if (searchInput) {
        searchInput.value = text;
      }
      // Trigger click on gosearch button
      const goSearchButton = document.getElementById('gosearch');
      if (goSearchButton) {
        goSearchButton.click();
      }
    });
  });
});