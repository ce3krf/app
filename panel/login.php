<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>INICIAR SESIÓN</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../favicon.png" />

    <script src="assets/js/jquery-3.5.1.min.js"></script>	
    <link rel="stylesheet" href="assets/js/jquery-confirm/jquery-confirm.min.css">
    <script src="assets/js/jquery-confirm/jquery-confirm.min.js"></script>		    

    <style>
        body {
            background-color: #f0f2f5 !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 1rem;
        }

        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            padding: 2rem 2rem 1.25rem 2rem;
        }

        .login-card .logo-wrap {
            text-align: center;
            margin-bottom: 1rem;
        }

        .login-card .logo-wrap img {
            width: 180px;
        }

        .login-card h6 {
            text-align: center;
            color: #555;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 1.25rem;
            font-weight: 600;
        }

        .login-card .form-control {
            font-size: 0.9rem;
            padding: 0.45rem 0.75rem;
            border-radius: 6px;
        }

        .login-card .mb-3 {
            margin-bottom: 0.65rem !important;
        }

        .login-card .btn-primary {
            font-size: 0.9rem;
            padding: 0.45rem;
            border-radius: 6px;
            margin-top: 0.25rem;
        }

        .login-divider {
            border: none;
            border-top: 1px solid #e8e8e8;
            margin: 1rem 0 0.75rem 0;
        }

        .login-logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 0;
        }

        .login-logos img {
            height: 32px;
            width: auto;
            object-fit: contain;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        .login-logos a:hover img,
        .login-logos img:hover {
            opacity: 1;
        }

        .login-logos a {
            display: inline-block;
        }

        #wait {
            text-align: center;
            margin-top: 0.4rem;
        }

        #wait img {
            height: 20px;
        }
    </style>

    <script>

$(function () {

  $('form#form1').on('submit', function (e) {

    e.preventDefault();

    if ( $('#user').val() == "" ){
        $.alert({
            title: 'Atención!',
            content: 'Ingresa un nombre usuario',
        });
        abort;
    }

    if ( $('#password').val() == "" ){
        $.alert({
            title: 'Atención!',
            content: 'Ingresa una contraseña',
        });
        abort;
    }    

    $.ajax({
      type: 'post',
      url: 'login_process.php',
        
      data: new FormData(document.getElementById("form1")),
      contentType: false,       
      cache: false,             
      processData:false, 
        
      beforeSend: function() {
          $('#bts').prop('disabled', true);
          $('#wait').show();
      },			  
      complete: function() {
          $('#bts').prop('disabled', false);
          $('#wait').hide();				
      },  			  
        
        
      success: function (data) {
        console.log(data);
          if(data == 'BAD LOGIN'){
//******************************
              $.confirm({
                  title: 'Atención',
                  boxWidth: '350px',
                  useBootstrap: false,
                  content: 'Nombre de usuario o contraseña incorrectos!',
                  buttons: {
                      
                      confirm:{
                          text: 'OK',
                          btnClass: 'btn-red',
                          action: function () {
                              //volver();
                      }},							
                  
              
                  }
              });		
//******************************	
          }
          if(data == 'INACTIVE USER'){
//******************************
              $.confirm({
                  title: 'Cuenta inactiva',
                  boxWidth: '350px',
                  useBootstrap: false,
                  content: 'Tu cuenta de usuario se encuentra inactiva. Por favor, contacta al administrador del sistema.',
                  buttons: {
                      confirm:{
                          text: 'OK',
                          btnClass: 'btn-red',
                          action: function () {}
                      }
                  }
              });
//******************************
          }				
          if(data == 'LOGIN OK'){
            window.location='index.php';
          }
      }
        
        
    });

  });

});

</script>	

</head>

<body onLoad="document.getElementById('user').focus();">
    <div class="login-wrapper">
        <div class="login-card">
            <div class="logo-wrap">
                <img src="img/logomuni.png" alt="Logo Municipalidad">
            </div>
            <h6>Iniciar sesión</h6>
            <form id="form1" name="form1">
                <div class="mb-3">
                    <input class="form-control" type="text" id="user" placeholder="Nombre de usuario" name="user">
                </div>
                <div class="mb-3">
                    <input class="form-control" type="password" id="password" placeholder="Contraseña" name="password">
                </div>
                <button id="bts" class="btn btn-primary d-block w-100" type="submit">Iniciar sesión</button>

                <hr class="login-divider">

                <div class="login-logos">
                    <a href="https://www.gobiernosantiago.cl/" target="_blank" rel="noopener">
                        <img src="img/lgore.png" alt="Gobierno Regional Metropolitano de Santiago">
                    </a>
                    <a href="https://www.coresantiago.cl/" target="_blank" rel="noopener">
                        <img src="img/lcore.png" alt="Consejo Regional Metropolitano">
                    </a>
                    <a href="https://www.sanjosedemaipo.cl/" target="_blank" rel="noopener">
                        <img src="img/lsjm.png" alt="Ilustre Municipalidad de San José de Maipo">
                    </a>
                    <img src="img/lpulso-1.png" alt="Pulso Consultores">
                </div>

                <div id="wait" style="display: none;">
                    <img src="img/ajax-loader.gif" alt=""/>
                </div>
            </form>
        </div>
    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>