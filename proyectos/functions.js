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

    // $("#wait").css('display','block');

    var  tipo =  $("input[name='tipo']:checked").map(function() {return this.value;}).get().join(',');    
    var  area =  $("input[name='area']:checked").map(function() {return this.value;}).get().join(',');    
    var  origen =  $("input[name='origen']:checked").map(function() {return this.value;}).get().join(',');    
    var  estado =  $("input[name='estado']:checked").map(function() {return this.value;}).get().join(',');    
    //console.log(tipo);
    //console.log(area);

    url = "datos_detalle.php?tipo="+tipo+'&area='+area+'&origen='+origen+'&estado='+estado+'&ubicacion='+ "&token=" + $('#token').val();
    //console.log( "datos.php?area="+area+'&tipo='+tipo+'&origen='+origen+'&estado='+estado );

    tabla(url);

    $('#tabla').DataTable().ajax.url(url);
    $('#tabla').DataTable().ajax.reload();

    howmany = 0;
    $('#m1').html(  ''+ '0' + ' iniciativas' );

    $.ajax({url:"datos.php?area="+area+'&tipo='+tipo+'&origen='+origen+'&estado='+estado + "&token=" + $('#token').val(), type:'GET', success: function (data){
    
        var data = $.parseJSON(data);
        //console.log(data);

        j=0;

        if (data){
            for (var i = 0; i < data.aaData.length; i++) {

                howmany++;
                $('#m1').html(  ''+ howmany + ' iniciativas' );

                var  id   = data.aaData[i].id;
                var  lat  = data.aaData[i].lat;
                var  lng  = data.aaData[i].lng;
                var  tipo = data.aaData[i].tipo;
                var  area = data.aaData[i].area;
                var  nom  = data.aaData[i].nombre;
                var  ubi  = data.aaData[i].ubicacion;
                var  cod  = data.aaData[i].codigo;
                var  cuantos  = data.aaData[i].cuantos;
                var  primero  = data.aaData[i].primero;
                var  color  = data.aaData[i].color;
                var  stroke  = data.aaData[i].stroke;

                agregar( id, lat, lng, cuantos, tipo, area, nom, ubi, cod, primero, color, stroke ); 
        
            }						
        }}	

    });

}	


function agregar(id, lat, lng, cuantos, tipo, area, nom, ubi, cod, primero, color, stroke){

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





 // inicializa el mapa
 //$( document ).ready(function() {   


var map = L.map('map_canvas', {
    zoomControl: false,
    center: [-33.6405925,-70.3550152], // Specify the center coordinates of your map
    zoom: 13, // Specify the initial zoom level
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
document.querySelector('.leaflet-control-scale').style.bottom = '150px';
document.querySelector('.leaflet-control-scale').style.right = '5px';


// grupos
var layerGroup = L.markerClusterGroup({
	spiderfyOnMaxZoom: true,
	showCoverageOnHover: true,
	zoomToBoundsOnClick: false,
	disableClusteringAtZoom: 18, // Adjust zoom level as needed
	//maxClusterRadius: 150 // Ungroup clusters at 50 meters apart
});
map.addLayer(layerGroup);


osm = new L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> | <a href="https://www.comunidad.city">comunidad.city</a> | contributors'
});
map.addLayer(osm);

// opciones
//RoadOnDemand
//AerialWithLabelsOnDemand


// BING !!!!!!!!!!!!!!!!!!
var bing = new L.BingLayer('AmHIvbpJx9tYb6WRaNVmMthrdvvUbbwd3B5Gczv-zmazPupuNV0yjDJnuMvtl94N', 
{ 'imagerySet' : 'RoadOnDemand', culture: 'es-ES'} );
//map.addLayer(bing);

L.control.zoom({
    position: 'bottomright'
}).addTo(map);


//var layerGroup = L.layerGroup().addTo(map);


muestra();
restaurar();

//});  




function road(){

    map.removeLayer(osm);   
    osm = new L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    map.addLayer(osm);
}

function sat(){
    map.removeLayer(bing);   

    bing = new L.BingLayer('AmHIvbpJx9tYb6WRaNVmMthrdvvUbbwd3B5Gczv-zmazPupuNV0yjDJnuMvtl94N', 
    { 'imagerySet' : 'AerialWithLabelsOnDemand', culture: 'es-ES'} );
    
    map.addLayer(bing);    
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
    var rnd = Math.random();
    var dlg = $.confirm({
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

        content: 'url:verproyecto.php?proyectos_id='+id+'&rnd='+rnd+ "&token=" + $('#token').val()
    });    

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
        //console.log($("#m5").height());

        if ( $("#m5").height() == '20' ){

            $("#m5").animate({
                height: "300px",
                width: "200px"
            }, 200);

        } else {

            $("#m5").animate({
                height: "30px",
                width: "120px"
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
