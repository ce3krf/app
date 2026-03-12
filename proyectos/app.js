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
function muestra(sector, proceso, origen, estado) {
    layerGroup.clearLayers();

    $("#wait").show();

    var busca = $('#searchInput').val();

    var procesoArray = proceso ? proceso.split(',') : $("input[name='proceso[]']:checked").map(function() { return this.value; }).get();
    var sectorArray = sector ? sector.split(',') : $("input[name='sector[]']:checked").map(function() { return this.value; }).get();
    var origenArray = origen ? origen.split(',') : $("input[name='origen[]']:checked").map(function() { return this.value; }).get();
    var estadoArray = estado ? estado.split(',') : $("input[name='estado[]']:checked").map(function() { return this.value; }).get();

    var proceso = procesoArray.join(',');
    var sector = sectorArray.join(',');
    var origen = origenArray.join(',');
    var estado = estadoArray.join(',');

    howmany = 0;
    $('#m1').html('<span style="color:gold;">&#9733;</span> 0 iniciativas');

    $.ajax({
        url: "datos.php?sector=" + sector + '&search=' + busca + '&proceso=' + proceso + '&origen=' + origen + '&estado=' + estado + "&token=" + $('#token').val(),
        type: 'GET',
        dataType: 'json',
        success: function (data) {

            j = 0;

            if (data && data.aaData) {
                for (var i = 0; i < data.aaData.length; i++) {

                    howmany++;
                    $('#m1').html('<span style="color:gold;">&#9733;</span>' + howmany + ' iniciativas');

                    var id = data.aaData[i].id;
                    var lat = data.aaData[i].lng;
                    var lng = data.aaData[i].lat;
                    var nom = data.aaData[i].nombre;
                    var sector_id = data.aaData[i].sector_id;
                    var sector_descripcion = data.aaData[i].sector_descripcion;
                    var color = data.aaData[i].sector_color;
                    var cuantos = data.aaData[i].cuantos;

                    if (lat != '' && lng != '' && lat != '0' && lng != '0') 
                        agregar(id, lat, lng, nom, sector_id, color, cuantos, sector_descripcion);

                }
            }
            $("#wait").hide();
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición AJAX:");
            console.error("Status:", status);
            console.error("Error:", error);
            console.error("Response:", xhr.responseText);
            alert("Error al cargar los datos. Revisa la consola para más detalles.");
            $("#wait").hide();
        }
    });

}


function agregar(id, lat, lng, nom, sector_id, color, cuantos, sector_descripcion){

    var icono = L.icon({ iconUrl: 'pins/c.png', iconSize: [48, 48] });    

    //console.log(cod + '-' + ubi + '-' + color);
        

//    try{

    var clr=''+color;
    var strk=clr;
    
    var originalColor = color;
    var oscurecido ='#000000'; // 
    strk = oscurecido;


    //var r = (cuantos * 2) + 10;
    var r = 20;

var marker = L.circleMarker([lat, lng], {
    color: strk,
    fillColor: clr,
    weight: 2,
    fillOpacity: 0.8,
    radius: 10,
    meters: 1 
}).addTo(layerGroup);

        



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
        tooltip = tooltip + "Sector: " + sector_descripcion + "<br>";
        tooltip = tooltip + '<span class="mas_info">'+"Click para más información"+ '</span>';

        marker.bindPopup(tooltip);
        marker.on('mouseover', function (e) {
            this.openPopup();
        });
        marker.on('mouseout', function (e) {
            this.closePopup();
        });
        marker.on('click', function (e) {
            L.DomEvent.stopPropagation(e);
            ver(id);
        });

//    } catch {
//    }

}





 // inicializa el mapa
 //$( document ).ready(function() {   


var map = L.map('map_canvas', {
    zoomControl: false,
    center: [-33.6405925,-70.3550152], // Specify the center coordinates of your map
    zoom: 12, // Specify the initial zoom level
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
	zoomToBoundsOnClick: true,
	disableClusteringAtZoom: 20, // Adjust zoom level as needed
	//maxClusterRadius: 150 // Ungroup clusters at 50 meters apart
    spiderLegPolylineOptions: { weight: 1.5, color: '#222', opacity: 0.5 },
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
    esri = new L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri | <a href="https://www.comunidad.city">comunidad.city</a> &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
    });
    map.addLayer(esri);
}


function opciones(){
	$('#box').toggle();
};







function tabla(url) {


	console.log(url);

$('#tabla').DataTable({
			
	destroy: true,
	pagingType: 'full_numbers',
	processing: true,
	serverSide: false,
	ajax: {
			url: url,
	},
	
	paging: true,
	searching: false,
	autoWidth: true,
	select: false, 
	lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],				

	language: {
			//"url": "js/DataTables/es-ES.json",
			infoFiltered: "",
			processing: '<div style="text-align:center">Cargando el listado. Espere un momento...<br><br><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Cargando...</span></div>',							
		"sProcessing":     "Procesando...",
		"sLengthMenu":     "Mostrar _MENU_ registros",
		"sZeroRecords":    "No se encontraron resultados",
		"sEmptyTable":     "NingÃºn dato disponible en esta tabla",
		"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
		"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
		"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		"sInfoPostFix":    "",
		"sSearch":         "Buscar:",
		"sUrl":            "",
		"sInfoThousands":  ",",
		"sLoadingRecords": "Cargando...",
		"oPaginate": {
			"sFirst":    "Primero",
			"sLast":     "Ãšltimo",
			"sNext":     "Siguiente",
			"sPrevious": "Anterior"
		},
		"oAria": {
			"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			"sSortDescending": ": Activar para ordenar la columna de manera descendente"
		}					
				
				
	},
	order: [[3, 'asc']],
	initComplete: function() {
		console.log('init');
	},
	
	searchHighlight: true,				
	
	columnDefs: [

	{ data: 'codigo_bip', 
		render: function ( data, type, row ) { return '<center><a style="color:white" href="javascript:;" onclick="ver('+row.id+');"><i class="fas fa-info-circle fa-2x"></i></a></center>';   },
	
	targets: 0   }, 	

	{ data: 'codigo_bip', 
		render: function ( data, type, row ) {  
		var cadena = '<b>'+row.nombre+'</b> ';
		cadena = cadena + '</div>'; 
		return cadena;
		},
		
		targets: 1   },  

	{ data: 'sector_descripcion', 
		render: function ( data, type, row ) {  
		return data;
		},
		
		targets: 2   },  

	{ data: 'etapas_descripcion', 
		render: function ( data, type, row ) { return data;   },
		
		targets: 3   },  

	{ data: 'procesos_descripcion', 
		render: function ( data, type, row ) { return data;   },
		
		targets: 4   },  



	],
	
	});

}


function ver(id){
    var x = Math.random();
    dlg = $.confirm({
        title: '',
        type: 'default',
        useBootstrap: false,
        typeAnimated: true,
        closeIcon: true,
        buttons: {
            info: {
                text: 'Continuar',
                btnClass: 'btn-custom',
                action: function () {}
            }
        },
        content: 'url:verproyecto.php?proyectos_id='+id+"&x="+x,
        onOpen: function(){
            var self = this;
            [0, 50, 150, 400].forEach(function(t){
                setTimeout(function(){
                    var box = self.$body.closest('.jconfirm-box')[0];
                    if (box) {
                        box.style.cssText += '; width: 90% !important; max-width: 500px !important; margin-left: auto !important; margin-right: auto !important;';
                    }
                }, t);
            });
        }
    });
}

function acerca(){

    var x = Math.random();
	$.confirm({
		title: '',
		type: 'default', // Remove 'blue' to avoid predefined styles
		boxWidth: '1200px',
		typeAnimated: true,
		closeIcon: true,
		useBootstrap: false,
		buttons: {
			info: {
				text: 'Continuar',
				btnClass: 'btn-custom', // Use a custom class
				action: function () {
				}
			}
		},
		content: 'url:acerca.php?x='+x
	});


}




function checkarea(){
	$("#form1 input[name='sector[]']").prop( "checked" , true );	
}		



function restaurar(){

    if(min){

        $("#paneltabla").animate({
            height: "50px",
            width: "250px"
        }, 200);

        $('#paneltabla').css('pointer-events', 'none');
        $('#paneltabla').css('opacity', '0.7');
        $('#paneltabla').css('background-color', 'rgba(0, 0, 0, 0.5)');


        min = false;

    } else {

        $("#paneltabla").animate({
            height: "70vh",
            width: "450px"
        }, 200);

        $('#paneltabla').css('pointer-events', 'all');
        $('#paneltabla').css('opacity', '1');
        $('#paneltabla').css('background-color', 'rgba(0, 0, 0, 0.8)');

        min = true;

    }


}









    var marker;
    var gpslat;
    var gpslng;



    map.on('click', function(e) {

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
                height: "430px",
                width: "250px"
            }, 200);

        } else {

            $("#m5").animate({
                height: "30px",
                width: "130px"
            }, 200);
        }
    };






      function avance(sector, proceso) {
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
            content: 'url:avance.php?sector=' + sector + '&proceso=' + proceso + "&x=" + x
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
        acerca();
    });    




    $(document).ready(function() {
        // Show "acerca" if more than one hour has passed since last time
        var lastAcerca = localStorage.getItem('acercaLastShown');
        var now = Date.now();
        if (!lastAcerca || now - parseInt(lastAcerca, 10) > 3600000) { // 1 hour = 3600000 ms
            acerca();
            localStorage.setItem('acercaLastShown', now.toString());
        }
    });



$('#m4').on('click', function () {
   $('#box').toggle(300);
});


$("#selectall").click(function(){
    $("#form1 input[type='checkbox']").prop( "checked" , true );
});			

$("#unselectall").click(function(){
    $("#form1 input[type='checkbox']").prop( "checked" , false );
});	 



function procesar() {

    $(".box").slideToggle();

    var sectorArray = [];
    var procesoArray = [];
    var origenArray = [];
    var estadoArray = [];

    // Recorre los checkboxes de sector
    $("#form1").find("input[name='sector[]']:checked").each(function() {
        var valor = $(this).val();
        if (valor) {
            sectorArray.push(valor);
        }
    });

    // Recorre los checkboxes de proceso
    $("#form1").find("input[name='proceso[]']:checked").each(function() {
        var valor = $(this).val();
        if (valor) {
            procesoArray.push(valor);
        }
    });

    // Recorre los checkboxes de origen
    $("#form1").find("input[name='origen[]']:checked").each(function() {
        var valor = $(this).val();
        if (valor) {
            origenArray.push(valor);
        }
    });

    // Recorre los checkboxes de estado
    $("#form1").find("input[name='estado[]']:checked").each(function() {
        var valor = $(this).val();
        if (valor) {
            estadoArray.push(valor);
        }
    });

    // Unir los arrays en strings separados por comas
    var sectorSelected = sectorArray.join(',');
    var procesoSelected = procesoArray.join(',');
    var origenSelected = origenArray.join(',');
    var estadoSelected = estadoArray.join(',');

    // Asignar a variables globales si es necesario
    sector = sectorSelected;
    proceso = procesoSelected;
    origen = origenSelected;
    estado = estadoSelected;

    // Llamar a la función muestra con los valores procesados
    muestra(sector, proceso, origen, estado);

}