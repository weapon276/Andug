<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Ventas</title>
    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #8b5cf6;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --success-color: #22c55e;
            --danger-color: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-primary);
            line-height: 1.5;
            padding: 1rem;
        }

        .dashboard {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(4, 1fr);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            background: var(--card-background);
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .main-chart {
            grid-column: span 4;
            height: 300px;
        }

        .metric-card {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .metric-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .metric-change {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
        }

        .metric-change.positive {
            color: var(--success-color);
        }

        .metric-change.negative {
            color: var(--danger-color);
        }

        .chart-container {
            grid-column: span 2;
            height: 250px;
        }

        .donut-chart {
            grid-column: span 2;
            height: 200px;
        }

        /* Placeholder for charts */
        .chart-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(110deg, #f5f5f5 8%, #ffffff 18%, #f5f5f5 33%);
            background-size: 200% 100%;
            animation: shine 1.5s linear infinite;
        }

        @keyframes shine {
            to {
                background-position-x: -200%;
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .chart-container,
            .donut-chart {
                grid-column: span 4;
            }
        }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }

            .main-chart,
            .metric-card,
            .chart-container,
            .donut-chart {
                grid-column: span 1;
            }

            .card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Main Chart -->
        <div class="card main-chart">
            <h2>Ventas Totales</h2>
            <div class="chart-placeholder"></div>
        </div>

        <!-- Metric Cards -->
        <div class="card metric-card">
            <span class="metric-value">$21,370</span>
            <span class="metric-label">Ventas totales</span>
            <div class="metric-change positive">
                <span>↑ 12.5%</span>
            </div>
        </div>

        <div class="card metric-card">
            <span class="metric-value">20</span>
            <span class="metric-label">Nuevos clientes</span>
            <div class="metric-change positive">
                <span>↑ 8.3%</span>
            </div>
        </div>

        <div class="card metric-card">
            <span class="metric-value">1,069</span>
            <span class="metric-label">Productos vendidos</span>
            <div class="metric-change positive">
                <span>↑ 5.2%</span>
            </div>
        </div>

        <div class="card metric-card">
            <span class="metric-value">5</span>
            <span class="metric-label">Devoluciones</span>
            <div class="metric-change negative">
                <span>↓ 2.1%</span>
            </div>
        </div>

        <!-- Bar Charts -->
        <div class="card chart-container">
            <h2>Ventas por Marca</h2>
            <div class="chart-placeholder"></div>
        </div>

        <div class="card chart-container">
            <h2>Ventas por Producto</h2>
            <div class="chart-placeholder"></div>
        </div>

        <!-- Donut Charts -->
        <div class="card donut-chart">
            <h2>Ventas por Almacén</h2>
            <div class="chart-placeholder"></div>
        </div>

        <div class="card donut-chart">
            <h2>Ventas por Canal</h2>
            <div class="chart-placeholder"></div>
        </div>
    </div>

    <script>
        // Add animation class to cards sequentially
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                }, index * 100);
            });
        });
    </script>
</body>
</html>