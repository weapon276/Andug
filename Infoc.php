<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Cliente</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        :root {
            --primary-color: #0ea5e9;
            --border-color: #e5e7eb;
            --text-muted: #6b7280;
            --bg-muted: #f3f4f6;
        }

        body {
            background-color: #f8fafc;
            min-height: 100vh;
        }

        .container {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 24px;
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
            animation: fadeIn 0.5s ease-out;
        }

        /* Sidebar Styles */
        .sidebar {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            animation: slideInLeft 0.5s ease-out;
        }

        .balance-section {
            margin-bottom: 24px;
        }

        .balance-label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .balance-amount {
            font-size: 24px;
            font-weight: 600;
            color: #111827;
        }

        .currency {
            color: var(--text-muted);
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            background-color: #dcfce7;
            color: #16a34a;
        }

        .counters-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin: 20px 0;
        }

        .counter-item {
            text-align: center;
            padding: 12px;
            background: var(--bg-muted);
            border-radius: 6px;
        }

        .counter-value {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
        }

        .counter-label {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Main Content Styles */
        .main-content {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            animation: slideInRight 0.5s ease-out;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            padding: 0 20px;
        }

        .tab {
            padding: 16px 20px;
            color: var(--text-muted);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
        }

        .tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .tab-content {
            padding: 24px;
        }

        .section {
            margin-bottom: 32px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .section-title {
            font-size: 18px;
            color: #111827;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
            border: 1px solid var(--border-color);
        }

        .search-bar {
            margin-bottom: 16px;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 14px;
        }

        .table-container {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: #f9fafb;
            font-weight: 500;
            color: var(--text-muted);
        }

        .empty-state {
            text-align: center;
            padding: 32px;
            color: var(--text-muted);
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            font-size: 14px;
            color: var(--text-muted);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hover-scale {
            transition: transform 0.2s;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="balance-section">
                <div class="balance-label">
                    <span>Saldos</span>
                    <span class="status-badge">vigente</span>
                </div>
                <div class="balance-amount">
                    $ 1,466.25 <span class="currency">MXN</span>
                </div>
                <p class="balance-note" style="font-size: 12px; color: var(--text-muted); margin-top: 8px;">
                    Los saldos son por moneda de cada una de las ventas abiertas sin liquidar.
                </p>
            </div>

            <div class="credit-info">
                <h3 style="margin-bottom: 12px;">Tipo de crédito</h3>
                <div style="font-size: 14px; color: var(--text-muted);">
                    <div>Limitado $ 1,000.00 MXN</div>
                    <div>Disponible $ 266.87 MXN</div>
                    <div>Utilizado $ 733.13 MXN</div>
                </div>
            </div>

            <div class="counters-grid">
                <div class="counter-item hover-scale">
                    <div class="counter-value">3</div>
                    <div class="counter-label">Ventas</div>
                </div>
                <div class="counter-item hover-scale">
                    <div class="counter-value">0</div>
                    <div class="counter-label">CFDIs</div>
                </div>
                <div class="counter-item hover-scale">
                    <div class="counter-value">0</div>
                    <div class="counter-label">Cotizaciones</div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="tabs">
                <div class="tab active">Datos</div>
                <div class="tab">Resumen</div>
                <div class="tab">Soporte técnico</div>
                <div class="tab">Control de acceso</div>
                <div class="tab">Precios especiales</div>
            </div>

            <div class="tab-content">
                <section class="section">
                    <div class="section-header">
                        <h2 class="section-title">RFCs</h2>
                        <div>
                            <button class="btn btn-secondary">Relacionar</button>
                            <button class="btn btn-primary">Nuevo</button>
                        </div>
                    </div>

                    <div class="search-bar">
                        <input type="text" class="search-input" placeholder="Buscar...">
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Razón social</th>
                                    <th>RFC</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state">
                                            Ningún dato disponible en esta tabla
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="pagination">
                            <span>Mostrando registros del 0 al 0 de un total de 0 registros</span>
                            <div>
                                <button class="btn btn-secondary">Recargar</button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Similar sections for Domicilios, Correos electrónicos, Teléfonos, and Contactos -->
                <!-- Each section follows the same structure as the RFCs section -->
            </div>
        </main>
    </div>
</body>
</html>