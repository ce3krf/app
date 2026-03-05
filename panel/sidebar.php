<style>
    .nav-link {
        padding-bottom:10px !important;
        padding-top:10px !important;
    }
</style>    


<div class="sidebar-content p-0">
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link sidebar-link" href="index.php">
                <span class="link-text">Inicio</span>
                <span class="link-indicator"></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" href="proyectos.php">
                <span class="link-text">Iniciativas</span>
                <span class="link-indicator"></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" href="../proyectos" target="_blank">
                <span class="link-text">Mapa</span>
                <span class="link-indicator"></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" href="avance.php">
                <span class="link-text">Avance implementación</span>
                <span class="link-indicator"></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" href="avancei.php">
                <span class="link-text">Avance financiero</span>
                <span class="link-indicator"></span>
            </a>
        </li>
        
        <li class="nav-item">
            <div class="sidebar-divider"></div>
        </li>
        
        <li class="nav-item">
            <a class="nav-link sidebar-link" href="#" id="parametrosLink">
                <span class="link-text">Parámetros</span>
                <span class="dropdown-arrow">▼</span>
                <span class="link-indicator"></span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link sidebar-link" href="backup.php">
                <span class="link-text">Hacer respaldo</span>
                <span class="link-indicator"></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" href="usuarios.php">
                <span class="link-text">Usuarios</span>
                <span class="link-indicator"></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" href="guia_uso.php">
                <span class="link-text">Guía de uso</span>
                <span class="link-indicator"></span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link sidebar-link" href="info_sistema.php">
                <span class="link-text">Información del sistema</span>
                <span class="link-indicator"></span>
            </a>
        </li>
    </ul>
</div>

<!-- Submenú flotante FUERA del sidebar -->
<div id="parametrosSubmenu" class="submenu-floating">
    <div class="submenu-header">Parámetros del Sistema</div>
    <a href="areas.php" class="submenu-item">Áreas municipales</a>
    <a href="instrumentos.php" class="submenu-item">Instrumentos</a>
    <a href="sectores.php" class="submenu-item">Sectores</a>
    <a href="subsectores.php" class="submenu-item">Subsectores</a>
    <a href="tipo.php" class="submenu-item">Tipo</a>
    <a href="etapas.php" class="submenu-item">Etapas</a>
    <a href="procesos.php" class="submenu-item">Procesos</a>
</div>

<style>
/* Sidebar con fondo celeste */
#sidebar-wrapper {
    background: linear-gradient(180deg, #e3f2fd 0%, #bbdefb 100%) !important;
}

.sidebar-content {
    padding-top: 20px;
    background: transparent;
}

.nav-item {
    position: relative;
    margin-bottom: 4px;
}

.sidebar-link {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 20px;
    color: #1565c0;
    font-weight: 500;
    font-size: 0.95rem;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-left: 3px solid transparent;
    background: linear-gradient(90deg, transparent 0%, transparent 100%);
}

.sidebar-link:hover {
    color: #0d47a1;
    background: linear-gradient(90deg, rgba(25, 118, 210, 0.15) 0%, transparent 100%);
    border-left-color: #1976d2;
    padding-left: 25px;
}

.sidebar-link.active {
    color: #0d47a1;
    background: linear-gradient(90deg, rgba(25, 118, 210, 0.2) 0%, transparent 100%);
    border-left-color: #1976d2;
    font-weight: 600;
}

.link-text {
    flex: 1;
    letter-spacing: 0.3px;
}

.link-indicator {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: transparent;
    transition: all 0.3s ease;
}

.sidebar-link:hover .link-indicator {
    background-color: #1976d2;
    box-shadow: 0 0 8px rgba(25, 118, 210, 0.6);
}

.sidebar-link.active .link-indicator {
    background-color: #1976d2;
    box-shadow: 0 0 12px rgba(25, 118, 210, 0.8);
}

/* Divisor elegante */
.sidebar-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent 0%, rgba(25, 118, 210, 0.3) 50%, transparent 100%);
    margin: 15px 20px;
}

/* Dropdown arrow */
.dropdown-arrow {
    font-size: 0.7rem;
    color: #1976d2;
    transition: transform 0.3s ease;
    margin-right: 8px;
}

.sidebar-link:hover .dropdown-arrow {
    color: #0d47a1;
}

#parametrosLink.active .dropdown-arrow {
    transform: rotate(180deg);
}

/* Submenú flotante - FUERA del contenedor sidebar */
.submenu-floating {
    position: fixed;
    left: 270px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(0, 0, 0, 0.08);
    padding: 8px 0;
    min-width: 240px;
    max-height: 400px;
    overflow-y: auto;
    opacity: 0;
    visibility: hidden;
    transform: translateX(-10px) scale(0.95);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 99999;
    pointer-events: none;
}

.submenu-floating.show {
    opacity: 1;
    visibility: visible;
    transform: translateX(0) scale(1);
    pointer-events: all;
}

.submenu-header {
    padding: 12px 20px 8px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #1976d2;
    border-bottom: 2px solid #e3f2fd;
    margin-bottom: 4px;
    background: linear-gradient(90deg, #e3f2fd 0%, transparent 100%);
}

.submenu-item {
    display: block;
    padding: 12px 20px;
    color: #424242;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.submenu-item:hover {
    background: linear-gradient(90deg, rgba(25, 118, 210, 0.1) 0%, transparent 100%);
    color: #1976d2;
    border-left-color: #1976d2;
    padding-left: 24px;
}

/* Responsive: en móviles el submenú se muestra dentro */
@media (max-width: 768px) {
    .submenu-floating {
        position: relative;
        left: 0;
        margin: 8px 20px;
        transform: none;
        max-height: none;
    }
    
    .submenu-floating.show {
        transform: none;
    }
}

/* Animación sutil al cargar */
.nav-item {
    animation: slideIn 0.4s ease forwards;
    opacity: 0;
}

@keyframes slideIn {
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.nav-item:nth-child(1) { animation-delay: 0.05s; }
.nav-item:nth-child(2) { animation-delay: 0.1s; }
.nav-item:nth-child(3) { animation-delay: 0.15s; }
.nav-item:nth-child(4) { animation-delay: 0.2s; }
.nav-item:nth-child(5) { animation-delay: 0.25s; }
.nav-item:nth-child(6) { animation-delay: 0.3s; }
.nav-item:nth-child(7) { animation-delay: 0.35s; }
.nav-item:nth-child(8) { animation-delay: 0.4s; }
.nav-item:nth-child(9) { animation-delay: 0.45s; }

/* Scrollbar personalizada para el submenú */
.submenu-floating::-webkit-scrollbar {
    width: 6px;
}

.submenu-floating::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.submenu-floating::-webkit-scrollbar-thumb {
    background: #1976d2;
    border-radius: 10px;
}

.submenu-floating::-webkit-scrollbar-thumb:hover {
    background: #0d47a1;
}
</style>

<script>
const parametrosLink = document.getElementById('parametrosLink');
const parametrosSubmenu = document.getElementById('parametrosSubmenu');
let isSubmenuOpen = false;

// Toggle submenú con posicionamiento dinámico
parametrosLink.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    isSubmenuOpen = !isSubmenuOpen;
    
    if (isSubmenuOpen) {
        // Posicionar el submenú junto al elemento
        const rect = parametrosLink.getBoundingClientRect();
        parametrosSubmenu.style.top = rect.top + 'px';
        
        // Mostrar con un pequeño delay para asegurar que se renderice
        setTimeout(() => {
            parametrosSubmenu.classList.add('show');
            parametrosLink.classList.add('active');
        }, 10);
    } else {
        parametrosSubmenu.classList.remove('show');
        parametrosLink.classList.remove('active');
    }
});

// Cerrar al hacer clic fuera
document.addEventListener('click', function(e) {
    if (!parametrosLink.contains(e.target) && !parametrosSubmenu.contains(e.target)) {
        parametrosSubmenu.classList.remove('show');
        parametrosLink.classList.remove('active');
        isSubmenuOpen = false;
    }
});

// Actualizar posición al hacer scroll
window.addEventListener('scroll', function() {
    if (isSubmenuOpen) {
        const rect = parametrosLink.getBoundingClientRect();
        parametrosSubmenu.style.top = rect.top + 'px';
    }
}, { passive: true });

// Actualizar posición al cambiar tamaño de ventana
window.addEventListener('resize', function() {
    if (isSubmenuOpen) {
        const rect = parametrosLink.getBoundingClientRect();
        parametrosSubmenu.style.top = rect.top + 'px';
    }
});

// Marcar página activa
const currentPage = window.location.pathname.split('/').pop();
document.querySelectorAll('.sidebar-link, .submenu-item').forEach(link => {
    const href = link.getAttribute('href');
    if (href && href === currentPage) {
        link.classList.add('active');
    }
});
</script>