<div class="topdiv"></div>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #4a7dbb 0%, #5a8dc9 100%); padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div class="container-fluid" style="padding: 8px 15px;">
        
        <!-- Botón colapso sidebar (izquierda, más sutil) -->
        <button id="menu-toggle" class="btn btn-link text-white d-md-block" 
                style="font-size: 1.2rem; opacity: 0.7; transition: opacity 0.2s ease; padding: 8px 12px; margin-right: 15px;"
                onmouseover="this.style.opacity='1'" 
                onmouseout="this.style.opacity='0.7'">
            <i class="fa-solid fa-bars"></i>
        </button>
        
        <!-- Logo (ahora a la izquierda del todo) -->
        <a class="navbar-brand d-flex align-items-center" href="index.php" style="margin: 0;">
            <img src="img/logomuni2.png" style="width:120px; height: auto; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">
        </a>
        
        <!-- Botón móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                style="border: none; padding: 8px;">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú usuario -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" 
                       href="#" 
                       id="userDropdown" 
                       role="button" 
                       data-bs-toggle="dropdown"
                       style="padding: 8px 15px; border-radius: 25px; transition: background-color 0.2s ease;"
                       onmouseover="this.style.backgroundColor='rgba(255,255,255,0.1)'"
                       onmouseout="this.style.backgroundColor='transparent'">
                        <img src="<?php echo $usuarios_foto;?>" 
                             style="width:35px; height:35px; object-fit:cover; border-radius:50%; margin-right:10px; border: 2px solid rgba(255,255,255,0.3); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                        <span style="font-weight: 500;"><?php echo $_SESSION["net_fulltrust_fas_users_name"];?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border: none; margin-top: 8px;">
                        <li><a class="dropdown-item" href="cambiar_password.php" style="padding: 10px 20px; transition: background-color 0.2s ease;">
                            <i class="fa-solid fa-user" style="margin-right: 8px; color: var(--top);"></i>Cambiar contraseña
                        </a></li>
                        <li><hr class="dropdown-divider" style="margin: 5px 0;"></li>
                        <li><a class="dropdown-item" href="logout.php" style="padding: 10px 20px; transition: background-color 0.2s ease;">
                            <i class="fa-solid fa-right-from-bracket" style="margin-right: 8px; color: #dc3545;"></i>Cerrar sesión
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>