<?php ?>
<div style="height:25px;"></div>
<footer class="py-4 text-center footer-white">
    <div class="container">
        <span class="footer-title"><?php echo strtoupper($row_param["parametros_titulo"]);?></span>
        <div class="logos">
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
    </div>
</footer>
<input type="hidden" id="sid" value="<?php echo $sid;?>">
<a href="#" class="cd-top" id="backToTop"></a>

<script>
$(document).ready(function() {
    if (localStorage.getItem('sidebarToggled') === 'true') {
        $('#sidebar-wrapper').addClass('toggled');
    }
    
    $('#menu-toggle').on('click', function(e) {
        e.preventDefault();
        $('#sidebar-wrapper').toggleClass('toggled');
        localStorage.setItem('sidebarToggled', $('#sidebar-wrapper').hasClass('toggled'));
    });
    
    $(window).on('scroll', function() {
        if ($(window).scrollTop() > 300) {
            $('#backToTop').addClass('show');
        } else {
            $('#backToTop').removeClass('show');
        }
    });
    
    $('#backToTop').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: 0
        }, 600, 'swing');
    });
});
</script>

<?php
    //echo $_SESSION[ "usuarios_profile"] . "<br>";

    $script = basename($_SERVER['SCRIPT_NAME']);
    $perfil = $_SESSION['usuarios_profile'];

    $paginas_invitado = [
        'index.php',
        'avance.php',
        'avancei.php',
        'cambiar_password.php',
        'usuarios_editar.php',
    ];

    $paginas_area = [
        'index.php',
        'avance.php',
        'avancei.php',
        'proyectos.php',
        'proyectos_editar.php',
        'actividades_editar.php',
        'cambiar_password.php',
        'usuarios_editar.php',
    ];

    if ( ($perfil == "INVITADO") && (!in_array($script, $paginas_invitado))){
        ?>
        <script>
            $('button').prop('disabled', true);
        </script>
        <?php
    }

    if ( ($perfil == "ÁREA") && (!in_array($script, $paginas_area))){
        ?>
        <script>
            $('button').prop('disabled', true);
        </script>
        <?php
    }

    // Verificar si el usuario debe cambiar su contraseña
    $uid = (int)$_SESSION['net_fulltrust_fas_id'];
    $rCambiar = $db->query("SELECT usuarios_cambiarpassword FROM usuarios WHERE usuarios_id = $uid LIMIT 1");
    $rowCambiar = $rCambiar ? $rCambiar->fetch_assoc() : null;
    if ($rowCambiar && $rowCambiar['usuarios_cambiarpassword'] == 1 && $script !== 'cambiar_password.php') {
        ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
        <script>
        $(document).ready(function() {
            $.confirm({
                title: '<i class="fa-solid fa-key me-2"></i> Cambio de contraseña requerido',
                content: 'Por seguridad, debes cambiar tu contraseña antes de continuar.',
                boxWidth: '400px',
                useBootstrap: false,
                type: 'orange',
                closeIcon: false,
                backgroundDismiss: false,
                buttons: {
                    cambiar: {
                        text: 'Cambiar ahora',
                        btnClass: 'btn-warning',
                        action: function() {
                            window.location.href = 'cambiar_password.php';
                        }
                    }
                }
            });
        });
        </script>
        <?php
    }

?>