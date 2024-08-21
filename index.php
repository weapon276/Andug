<?php
session_start();
include 'conexion.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_type'])) {
    header("Location: login.php");
    exit();
}

// Verificar si la clave 'username' está definida en la sesión
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {

}

$user_type = $_SESSION['user_type'];

// Prevenir que el usuario vuelva a la página anterior después de cerrar sesión
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Función para generar el menú
function generarMenu($user_type) {
    $menu = "<ul class='nav nav-pills flex-column mb-auto'>";
    switch ($user_type) {
        case 'Admin':
            $menu .= "<li class='nav-item'><a href='inicioa.php' class='nav-link'><i class='fas fa-users'></i> Inicio</a></li>";
            break;
        case 'Administrador':
            $menu .= "<li class='nav-item'><a href='inicioa.php' class='nav-link'><i class='fas fa-users'></i> Inicio</a></li>";
            $menu .= "<li class='nav-item'><a href='gestionar_usuarios.php' class='nav-link'><i class='fas fa-users'></i> Usuarios</a></li>";
            $menu .= "<li class='nav-item'><a href='gestionar_empleado.php' class='nav-link'><i class='fas fa-users'></i> Gestionar Empleados</a></li>";
            $menu .= "<li class='nav-item'><a href='gestionar_camiones.php' class='nav-link'><i class='fas fa-truck'></i> Gestionar camiones</a></li>";
            $menu .= "<li class='nav-item'><a href='gestionar_operadores.php' class='nav-link'><i class='fas fa-user'></i> Gestionar operadores</a></li>";
            $menu .= "<li class='nav-item'><a href='gestionar_clientes.php' class='nav-link'><i class='fas fa-user-friends'></i> Gestionar clientes</a></li>";
            $menu .= "<li class='nav-item'><a href='gestionar_viajes.php' class='nav-link'><i class='fas fa-route'></i> Gestionar viajes</a></li>";
            $menu .= "<li class='nav-item'><a href='generar_reportes.php' class='nav-link'><i class='fas fa-chart-line'></i> Generar reportes</a></li>";
            break;
        case 'Contabilidad':
            $menu .= "<li class='nav-item'><a href='cliente.php' class='nav-link'><i class='fas fa-file-invoice'></i> Clientes</a></li>";
            $menu .= "<li class='nav-item'><a href='gestionar_facturas.php' class='nav-link'><i class='fas fa-file-invoice'></i> Gestionar facturas</a></li>";
            $menu .= "<li class='nav-item'><a href='gestionar_cotizacion.php' class='nav-link'><i class='fas fa-file-invoice'></i> Gestionar Cotizacion</a></li>";
            $menu .= "<li class='nav-item'><a href='cotizacion.php' class='nav-link'><i class='fas fa-file-invoice'></i> Realizar Cotizacion</a></li>";
            $menu .= "<li class='nav-item'><a href='facturas.php' class='nav-link'><i class='fas fa-file-invoice'></i> Realizar Factura</a></li>";
            $menu .= "<li class='nav-item'><a href='alta_cliente.php' class='nav-link'><i class='fas fa-file-invoice-dollar'></i> Registrar Cliente</a></li>";
            break;
        case 'Recursos Humanos':
            $menu .= "<li class='nav-item'><a href='inicio.php' class='nav-link'><i class='fas fa-user-tie'></i> Inicio</a></li>";
            $menu .= "<li class='nav-item'><a href='gestionar_empleados.php' class='nav-link'><i class='fas fa-user-tie'></i> Gestionar empleados</a></li>";
            $menu .= "<li class='nav-item'><a href='registrar_empleado.php' class='nav-link'><i class='fas fa-user-tie'></i> Registrar Empleado</a></li>";
            break;
        case 'Operador':
            $menu .= "<li class='nav-item'><a href='viaje.php' class='nav-link'><i class='fas fa-route'></i> Gestionar viajes</a></li>";
            break;
        case 'Cliente':
            $menu .= "<li class='nav-item'><a href='cliente_viajes.php' class='nav-link'><i class='fas fa-search-location'></i> Consultar viajes</a></li>";
            $menu .= "<li class='nav-item'><a href='consultar_facturas.php' class='nav-link'><i class='fas fa-file-invoice'></i> Consultar facturas</a></li>";
            break;
    }
    $menu .= "</ul>";
    return $menu;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <style>
        :root {
            --navbar-bg-color: #e9322c;
        }
        .navbar-custom {
            background-color: var(--navbar-bg-color);
        }
        .navbar-custom .nav-link,
        .navbar-custom .navbar-brand,
        .navbar-custom .navbar-toggler-icon {
            color: #fff;
        }
        .nav-link i {
            color: black;
        }
        html, body {
            font-family: "Raleway", sans-serif;
        }
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            top: 56px;
            left: 0;
            background-color: #fff;
            padding: 1rem;
        }
        .w3-main {
            margin-left: 320px;
            margin-top: 60px;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Top container -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">ADMIN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="notificaciones.php"><i class="fa fa-envelope"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-user"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-cog"></i></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cerrar_sesion.php"><i class="fa fa-sign-out"></i> Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script>
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
                time = setTimeout(logout, 300000);
            }
        };

        window.onload = function() {
            inactivityTime();
        }
    </script>

    <div class="sidebar">
        <div class="d-flex flex-column p-3">
            <a href="#" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
                <img src="/w3images/avatar2.png" class="rounded-circle me-2" alt="Avatar" width="48" height="48">
                <span class="fs-4">Bienvenido, <strong><?php echo $username; ?></strong></span>
            </a>
            <p>Usuario: <strong><?php echo $user_type; ?></strong></p>
            <hr>
            <?php echo generarMenu($user_type); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
