<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weapon</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/dashboard.css">
    <script src="assets/dashboard.js"></script>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="logo">
            <span class="logo-text">Weapon</span>
        </div>

        <nav class="nav-section">
            <div class="nav-title">Principal</div>
            
            <div class="nav-item">
                <span class="nav-icon">👥</span>
                <span class="nav-text">Clientes</span>
            </div>

            <div class="nav-item">
                <span class="nav-icon">📦</span>
                <span class="nav-text">Productos</span>
                <span class="nav-arrow">▸</span>
            </div>

            <div class="nav-item">
                <span class="nav-icon">📊</span>
                <span class="nav-text">Inventario</span>
                <span class="nav-arrow">▸</span>
            </div>

            <div class="nav-item">
                <span class="nav-icon">🛒</span>
                <span class="nav-text">Compras</span>
                <span class="nav-arrow">▸</span>
            </div>

            <div class="nav-item">
                <span class="nav-icon">⚙️</span>
                <span class="nav-text">Administración</span>
                <span class="nav-arrow">▸</span>
            </div>
            <div class="submenu">
                <div class="submenu-item">Notas de crédito</div>
                <div class="submenu-item">Recibos de pago ventas</div>
                <div class="submenu-item">Recibos de pago compras</div>
                <div class="submenu-item">Movimientos de caja</div>
                <div class="submenu-item">Movimientos de banco</div>

            </div>

            <div class="nav-item">
                <span class="nav-icon">🔧</span>
                <span class="nav-text">Soporte técnico</span>
                <span class="nav-arrow">▸</span>
            </div>
        </nav>

  

        </nav>
    </aside>

    <main class="main-content">
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
                        <span>Weapon</span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-menu-item">Mis datos</div>
                        <div class="user-menu-item">Dark mode</div>
                        <div class="user-menu-item">
                        <a href="cerrar.php" style="text-decoration: none; color: inherit;">Cerrar sesión</a>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sales-list-header">
            <h1 class="page-title">Ventas</h1>
            <div class="sales-actions">
                <button class="btn btn-primary" onclick="showLoading(this)">
                    <span>Nueva venta</span>
                </button>
                <button class="btn btn-secondary" onclick="showLoading(this)">
                    <span>Filtros</span>
                </button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Fecha y hora</th>
                        <th>Total</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>563</td>
                        <td>Sin cliente asignado [Principal]</td>
                        <td><span class="status-badge status-open">Abierta</span></td>
                        <td>2024-12-09 16:33:54</td>
                        <td>$ 0.00 MXN</td>
                        <td>
                            <button class="btn btn-secondary" onclick="showLoading(this)">
                                <span>Ver</span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>562</td>
                        <td>Alan Vasconcelos</td>
                        <td><span class="status-badge status-paid">Pagada</span></td>
                        <td>2024-12-09 16:16:12</td>
                        <td>$ 5,500.00 MXN</td>
                        <td>
                            <button class="btn btn-secondary" onclick="showLoading(this)">
                                <span>Ver</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>


</body>
</html>