<?php
session_start();
require 'modelo/conexion.php';


// Verificar si el usuario está autenticado
if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit();
}

$user_type_id = $_SESSION['userType'];
$username = $_SESSION['username'];
$usuario_id = $_SESSION['userId'];

// Obtener el nombre y la imagen del empleado
$query = "SELECT Nombre,ApellidoP, Imagen FROM empleado WHERE fk_idUsuario = :usuario_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$empleado = $stmt->fetch(PDO::FETCH_ASSOC);
$nombreEmpleado = $empleado['Nombre'];
$apellidoEmpleado = $empleado['ApellidoP'];
$imagenEmpleado = $empleado['Imagen'];

// Evitar que el usuario vuelva a la página anterior después de cerrar sesión
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Obtener el nombre del tipo de usuario
$query = "SELECT NombreTypeUser FROM typeuser WHERE id_TypeUser = :user_type_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_type_id', $user_type_id, PDO::PARAM_INT);
$stmt->execute();
$user_type = $stmt->fetch(PDO::FETCH_ASSOC)['NombreTypeUser'];

// Función para restringir el acceso basado en el tipo de usuario
function verificarAcceso($user_type, $paginasPermitidas) {
    $paginaActual = basename($_SERVER['PHP_SELF']);
    if (!in_array($paginaActual, $paginasPermitidas)) {
        echo "<script>
            alert('No tienes acceso a esta página.');
            setTimeout(function() {
                window.location.href = '../index.php';
            }, 3000);
        </script>";
        exit();
    }
}

// Definir páginas permitidas por tipo de usuario
switch ($user_type) {
    case 'Admin':
        $paginasPermitidas = ['inicioa.php', 'gestionar_usuarios.php', 'gestionar_empleado.php', 'gestionar_camiones.php'];
        break;
    case 'Administrador':
        $paginasPermitidas = ['inicioa.php', 'gestionar_usuarios.php', 'seguros.php', 'gestionar_empleado.php', 'gestionar_camiones.php'];
        break;
    case 'Contabilidad':
        $paginasPermitidas = ['gestionar_cotizacion.php', 'clientes.php', 'seguros.php','cotizacion.php','rutas.php','alta_cliente.php','facturas.php','gestionar_facturas.php','remolque.php','index.php','viaje.php', 'cliente.php', 'gestion_camiones.php'];
        break;
    case 'Recursos Humanos':
        $paginasPermitidas = ['index.php','inicio.php', 'seguros.php','gestionar_empleados.php', 'registrar_empleado.php'];
        break;
    case 'Operador':
        $paginasPermitidas = ['index.php','viaje.php'];
        break;
    case 'Cliente':
        $paginasPermitidas = ['cliente_viajes.php', 'consultar_facturas.php'];
        break;
    default:
        $paginasPermitidas = ['../index.php']; // Página por defecto
        break;
}
// Fin páginas permitidas por tipo de usuario

// Verificar si el usuario tiene acceso a la página actual
verificarAcceso($user_type, $paginasPermitidas);
// Fin


// Función para generar el menú
function generarMenu($user_type) {
    $menu = "<ul class='nav nav-pills flex-column mb-auto'>";
    switch ($user_type) {
        case 'Admin':
            $menu .= "<li class='nav-item'><a href='inicioa.php' class='nav-link'>🏠 Inicio</a></li>";
            break;
        case 'Administrador':
            $menu .= "<li class='nav-item'><a href='inicioa.php' class='nav-link'>🏠 Inicio</a></li>";
            $menu .= "<li class='nav-item'><a href='../controlador/gestionar_usuarios.php' class='nav-link'>👥 Usuarios</a></li>";
            $menu .= "<li class='nav-item'><a href='../controlador/gestionar_empleado.php' class='nav-link'>👔 Gestionar Empleados</a></li>";
            $menu .= "<li class='nav-item'><a href='../controlador/gestionar_camiones.php' class='nav-link'>🚚 Gestionar camiones</a></li>";
            $menu .= "<li class='nav-item'><a href='../controlador/gestionar_operadores.php' class='nav-link'>🧑‍✈️ Gestionar operadores</a></li>";
            $menu .= "<li class='nav-item'><a href='../controlador/gestionar_clientes.php' class='nav-link'>🤝 Gestionar clientes</a></li>";
            $menu .= "<li class='nav-item'><a href='../controlador/gestionar_viajes.php' class='nav-link'>🗺️ Gestionar viajes</a></li>";
            $menu .= "<li class='nav-item'><a href='../controlador/generar_reportes.php' class='nav-link'>📊 Generar reportes</a></li>";
            break;
            case 'Contabilidad':
                $menu .= "
                <div class='nav-item'>
                    <span class='nav-icon'><i class='fas fa-file-invoice-dollar'></i></span>
                    <span class='nav-text'>Clientes</span>
                    <span class='nav-arrow'>▸</span>
                </div>
                <div class='submenu'>
                    <a href='../modelo/viaje.php' class='submenu-item'><i class='fas fa-file-invoice-dollar'></i> Inicio</a>
                    <a href='cliente.php' class='submenu-item'><i class='fas fa-file-invoice'></i> Clientes</a>
                    <a href='../controlador/alta_cliente.php' class='submenu-item'><i class='fas fa-user-plus'></i> Registrar Cliente</a>
                    <a href='controlador/rutas.php' class='submenu-item'><i class='fas fa-map-marked-alt'></i> Rutas</a>
                </div>";
                $menu .= "
                <div class='nav-item'>
                    <span class='nav-icon'><i class='fas fa-file-invoice-dollar'></i></span>
                    <span class='nav-text'>Cotizaciones</span>
                    <span class='nav-arrow'>▸</span>
                </div>
                <div class='submenu'>
                    <a href='gestionar_cotizacion.php' class='submenu-item'><i class='fas fa-calculator'></i> Cotizaciones</a>
                    <a href='modelo/cotizacion.php' class='submenu-item'><i class='fas fa-file-alt'></i> Nueva Cotización</a>
                </div>";
                $menu .= "
                <div class='nav-item'>
                    <span class='nav-icon'><i class='fas fa-file-invoice-dollar'></i></span>
                    <span class='nav-text'>Facturas</span>
                    <span class='nav-arrow'>▸</span>
                </div>
                <div class='submenu'>
                    <a href='../controlador/gestionar_facturas.php' class='submenu-item'><i class='fas fa-file-invoice'></i> Facturas</a>
                    <a href='vista/facturas.php' class='submenu-item'><i class='fas fa-receipt'></i> Nueva Factura</a>
                </div>";
                
                $menu .= "
                <div class='nav-item'>
                    <span class='nav-icon'><i class='fas fa-file-invoice-dollar'></i></span>
                    <span class='nav-text'>Camiones</span>
                    <span class='nav-arrow'>▸</span>
                </div>
                <div class='submenu'>
                    <a href='../vista/gestion_camiones.php' class='submenu-item'><i class='fas fa-truck'></i> Camiones</a>
                    <a href='../modelo/remolque.php' class='submenu-item'><i class='fas fa-truck-moving'></i> Remolques</a>
                </div>";
                
                break;
        case 'Recursos Humanos':
            $menu .= "<li class='nav-item'><a href='inicio.php' class='nav-link'>🏠 Inicio</a></li>";
            $menu .= "<li class='nav-item'><a href='../controlador/gestionar_empleados.php' class='nav-link'>👥 Gestionar empleados</a></li>";
            $menu .= "<li class='nav-item'><a href='../controlador/registrar_empleado.php' class='nav-link'>➕ Registrar Empleado</a></li>";
            break;
        case 'Operador':
            $menu .= "<li class='nav-item'><a href='viaje.php' class='nav-link'>🚚 Gestionar viajes</a></li>";
            break;
        case 'Cliente':
            $menu .= "<li class='nav-item'><a href='../vista/cliente_viajes.php' class='nav-link'>🔍 Consultar viajes</a></li>";
            $menu .= "<li class='nav-item'><a href='consultar_facturas.php' class='nav-link'>📄 Consultar facturas</a></li>";
            break;
        case 'Prospecto':
            $menu .= "<li class='nav-item'><a href='cliente_viajes.php' class='nav-link'>🔍 Consultar viajes</a></li>";
            $menu .= "<li class='nav-item'><a href='consultar_facturas.php' class='nav-link'>📄 Consultar facturas</a></li>";
            break;
    }
    $menu .= "</ul>";
    return $menu;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/diseño.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">


    <style>
    </style>
</head>
<body>
    <!-- Updated Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <img src="assets/img/1.png" alt="Logo">
            <span class="logo-text"></span>
        </div>

        <div class="welcome-message">
        Bienvenido, <?php echo htmlspecialchars($nombreEmpleado) . ' ' . htmlspecialchars($apellidoEmpleado); ?>
        </div>

        <nav class="nav-section">
            <div class="nav-title"><p>Area: <?php echo htmlspecialchars($user_type); ?></p></div>
            <?php echo generarMenu($user_type); ?>
        </nav>


        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Bar with Notifications and User Menu -->
        <div class="top-bar">
            <div class="top-actions">
                <div class="notifications-dropdown">
                    <button class="btn btn-secondary" onclick="toggleNotifications()">
                        <span>🔔</span>
                    </button>
                    <div class="notifications-panel" id="notificationsPanel">
                        <div class="user-menu-item">No tienes mensajes sin leer</div>
                        <div class="user-menu-item">Ver todas</div>
                    </div>
                </div>

                <div class="user-menu">
                    <button class="btn btn-secondary" onclick="toggleUserMenu()">
                        <span>👤</span>
                        <span><?php echo htmlspecialchars($nombreEmpleado). ' ' . htmlspecialchars($apellidoEmpleado) ; ?></span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-menu-item" onclick="toggleDarkMode()">Dark mode</div>
                        <div class="user-menu-item" onclick="logout()">Cerrar sesión</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="dashboard-content">
            <h1></h1>

            <!-- Add more dashboard content here -->
        </div>
    </main>

    <script>
        function toggleNotifications() {
            const panel = document.getElementById('notificationsPanel');
            panel.classList.toggle('active');
        }

        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        function logout() {
            // Implement logout functionality
            window.location.href = 'cerrar_sesion.php';
        }

        // Sidebar functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const navItems = document.querySelectorAll('.nav-item');

            // Handle click events for nav items
            navItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    // If the item has a submenu
                    if (item.querySelector('.nav-arrow')) {
                        e.stopPropagation();
                        item.classList.toggle('expanded');
                    }
                });
            });

            // Handle pin/unpin
            sidebar.addEventListener('click', (e) => {
                if (e.target === sidebar || e.target.classList.contains('logo')) {
                    sidebar.classList.toggle('pinned');
                }
            });

            // Close expanded items when mouse leaves unpinned sidebar
            sidebar.addEventListener('mouseleave', () => {
                if (!sidebar.classList.contains('pinned')) {
                    navItems.forEach(item => {
                        item.classList.remove('expanded');
                    });
                }
            });

            // Toggle sidebar on mobile
            const toggleSidebar = () => {
                sidebar.classList.toggle('active');
            };

            // Close sidebar when clicking outside
            document.addEventListener('click', (e) => {
                const isClickInsideSidebar = sidebar.contains(e.target);
                const isClickInsideNotifications = document.querySelector('.notifications-dropdown').contains(e.target);
                const isClickInsideUserMenu = document.querySelector('.user-menu').contains(e.target);

                if (!isClickInsideSidebar && !isClickInsideNotifications && !isClickInsideUserMenu) {
                    sidebar.classList.remove('active');
                    document.getElementById('notificationsPanel').classList.remove('active');
                    document.getElementById('userDropdown').classList.remove('active');
                }
            });


        });

        // Inactivity timer
        let inactivityTime = function () {
            let time;
            window.onload = resetTimer;
            document.onmousemove = resetTimer;
            document.onkeypress = resetTimer;

            function logout() {
                alert("Se cerrará la sesión por inactividad.");
                window.location.href = 'cerrar_sesion.php';
            }

            function resetTimer() {
                clearTimeout(time);
                time = setTimeout(logout, 30000000); // 30 minutes
            }
        };

        inactivityTime();
    </script>
</body>
</html>