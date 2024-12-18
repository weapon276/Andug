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
$query = "SELECT Nombre, ApellidoP, Imagen FROM empleado WHERE fk_idUsuario = :usuario_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$empleado = $stmt->fetch(PDO::FETCH_ASSOC);
$nombreEmpleado = $empleado['Nombre'];
$apellidoEmpleado = $empleado['ApellidoP'];
$imagenEmpleado = $empleado['Imagen'];

// Obtener el nombre del tipo de usuario
$query = "SELECT NombreTypeUser FROM typeuser WHERE id_TypeUser = :user_type_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_type_id', $user_type_id, PDO::PARAM_INT);
$stmt->execute();
$user_type = $stmt->fetch(PDO::FETCH_ASSOC)['NombreTypeUser'];

// Función para verificar acceso
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
$paginasPermitidas = [
    'Admin' => ['inicioa.php', 'gestionar_usuarios.php', 'gestionar_empleado.php', 'gestionar_camiones.php'],
    'Administrador' => ['inicioa.php', 'gestionar_usuarios.php', 'seguros.php', 'camion.php', 'gestionar_empleado.php', 'gestionar_camiones.php'],
    'Contabilidad' => ['gestionar_cotizacion.php', 'clientes.php', 'seguros.php', 'cotizacion.php', 'rutas.php', 'camion.php', 'alta_cliente.php', 'facturas.php', 'gestionar_facturas.php', 'remolque.php', 'index.php', 'viaje.php', 'cliente.php', 'gestion_camiones.php'],
    'Recursos Humanos' => ['index.php', 'inicio.php', 'seguros.php', 'gestionar_empleados.php', 'registrar_empleado.php'],
    'Operador' => ['index.php', 'viaje.php'],
    'Cliente' => ['cliente_viajes.php', 'consultar_facturas.php'],
    'Prospecto' => ['cliente_viajes.php', 'consultar_facturas.php']
];

// Verificar si el usuario tiene acceso a la página actual
verificarAcceso($user_type, $paginasPermitidas[$user_type] ?? ['../index.php']);

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
            $menu .= generarSubmenu('Clientes', [
                ['../modelo/viaje.php', 'fas fa-file-invoice-dollar', 'Inicio'],
                ['cliente.php', 'fas fa-file-invoice', 'Clientes'],
                ['../controlador/alta_cliente.php', 'fas fa-user-plus', 'Registrar Cliente'],
                ['controlador/rutas.php', 'fas fa-map-marked-alt', 'Rutas']
            ]);
            $menu .= generarSubmenu('Cotizaciones', [
                ['gestionar_cotizacion.php', 'fas fa-calculator', 'Cotizaciones'],
                ['modelo/cotizacion.php', 'fas fa-file-alt', 'Nueva Cotización']
            ]);
            $menu .= generarSubmenu('Facturas', [
                ['../controlador/gestionar_facturas.php', 'fas fa-file-invoice', 'Facturas'],
                ['vista/facturas.php', 'fas fa-receipt', 'Nueva Factura']
            ]);
            $menu .= generarSubmenu('Camiones', [
                ['../vista/gestion_camiones.php', 'fas fa-truck', 'Camiones'],
                ['../modelo/remolque.php', 'fas fa-truck-moving', 'Remolques'],
                ['../modelo/seguro.php', 'fas fa-truck-moving', 'Seguro']
            ]);
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
        case 'Prospecto':
            $menu .= "<li class='nav-item'><a href='../vista/cliente_viajes.php' class='nav-link'>🔍 Consultar viajes</a></li>";
            $menu .= "<li class='nav-item'><a href='consultar_facturas.php' class='nav-link'>📄 Consultar facturas</a></li>";
            break;
    }
    $menu .= "</ul>";
    return $menu;
}

function generarSubmenu($titulo, $items) {
    $submenu = "
    <div class='nav-item'>
        <span class='nav-icon'><i class='fas fa-file-invoice-dollar'></i></span>
        <span class='nav-text'>$titulo</span>
        <span class='nav-arrow'>▸</span>
    </div>
    <div class='submenu'>";
    foreach ($items as $item) {
        $submenu .= "<a href='{$item[0]}' class='submenu-item'><i class='{$item[1]}'></i> {$item[2]}</a>";
    }
    $submenu .= "</div>";
    return $submenu;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/diseño.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <img src="assets/img/1.png" alt="Logo">
            <span class="logo-text"></span>
        </div>

        <div class="welcome-message">
            Bienvenido(A), <?php echo htmlspecialchars($nombreEmpleado); ?>
        </div>

        <nav class="nav-section">
            <div class="nav-title"><p>Area: <?php echo htmlspecialchars($user_type); ?></p></div>
            <?php echo generarMenu($user_type); ?>
        </nav>
    </aside>

    <main class="main-content">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
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
                        <span><?php echo htmlspecialchars($nombreEmpleado . ' ' . $apellidoEmpleado); ?></span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-menu-item" onclick="toggleDarkMode()">Dark mode</div>
                        <div class="user-menu-item" onclick="logout()">Cerrar sesión</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- El contenido específico de cada página se insertará aquí -->
            <?php if (isset($pageContent)) echo $pageContent; ?>
        </div>
    </main>

    <script>
        function toggleNotifications() {
            document.getElementById('notificationsPanel').classList.toggle('active');
        }

        function toggleUserMenu() {
            document.getElementById('userDropdown').classList.toggle('active');
        }

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        function logout() {
            window.location.href = 'cerrar_sesion.php';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const navItems = document.querySelectorAll('.nav-item');

            navItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    if (item.querySelector('.nav-arrow')) {
                        e.stopPropagation();
                        item.classList.toggle('expanded');
                    }
                });
            });

            sidebar.addEventListener('click', (e) => {
                if (e.target === sidebar || e.target.classList.contains('logo')) {
                    sidebar.classList.toggle('pinned');
                }
            });

            sidebar.addEventListener('mouseleave', () => {
                if (!sidebar.classList.contains('pinned')) {
                    navItems.forEach(item => item.classList.remove('expanded'));
                }
            });

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
                time = setTimeout(logout, 30000000);
            }
        };

        inactivityTime();
    </script>
</body>
</html>