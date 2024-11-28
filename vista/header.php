<?php

include 'conexion.php';

// Función para generar el menú
function generarMenu($user_type) {
    $menu = "<ul>";
    switch ($user_type) {
        case 'Administrador':
            $menu .= "<li><a href='gestionar_usuarios.php'>Gestionar usuarios</a></li>";
            $menu .= "<li><a href='gestionar_camiones.php'>Gestionar camiones</a></li>";
            $menu .= "<li><a href='gestionar_operadores.php'>Gestionar operadores</a></li>";
            $menu .= "<li><a href='gestionar_clientes.php'>Gestionar clientes</a></li>";
            $menu .= "<li><a href='gestionar_viajes.php'>Gestionar viajes</a></li>";
            $menu .= "<li><a href='generar_reportes.php'>Generar reportes</a></li>";
            break;
        case 'Contabilidad':
            $menu .= "<li><a href='gestionar_facturas.php'>Gestionar facturas</a></li>";
            $menu .= "<li><a href='gestionar_liquidaciones.php'>Gestionar liquidaciones</a></li>";
            break;
        case 'Recursos Humanos':
            $menu .= "<li><a href='gestionar_empleados.php'>Gestionar empleados</a></li>";
            break;
        case 'Operador':
            $menu .= "<li><a href='registrar_ubicacion.php'>Registrar ubicación</a></li>";
            $menu .= "<li><a href='gestionar_viajes.php'>Gestionar viajes</a></li>";
            break;
        case 'Cliente':
            $menu .= "<li><a href='consultar_viajes.php'>Consultar viajes</a></li>";
            $menu .= "<li><a href='consultar_facturas.php'>Consultar facturas</a></li>";
            break;
    }
    $menu .= "<li><a href='cerrar_sesion.php'>Cerrar sesión</a></li>";
    $menu .= "</ul>";
    return $menu;
}

// Verificar si el usuario está autenticado
if (isset($_SESSION['user_type'])) {
    $user_type = $_SESSION['user_type'];
    echo generarMenu($user_type);
} else {
    header("Location: login.php");
    exit();
}

// Prevenir que el usuario vuelva a la página anterior después de cerrar sesión
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
