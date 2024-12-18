<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Servicios</title>
    <link rel="stylesheet" href="assets/css/servicios.css">
    <link rel="stylesheet" href="assets/css/rservicios.css">
    <link rel="stylesheet" href="assets/css/modal.css">

    <style>
        .rutaDetails {
            display: none;
        }
    </style>
</head>
<body>
     <div class="container">
        <header class="header">
            <h1 class="title">Servicios</h1>
            <div class="header-actions">
            <button id="btnNuevo" class="btn btn-primary">Nuevo</button>
            </div>
        </header>

        <div class="search-container">
            <input type="text" class="search-input" placeholder="Buscar...">
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>CLAVE</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="sku-cell">RTMZO-MYT</td>
                        <td>Flete manzanillo a monterrey</td>
                        <td class="price-cell">$ 299.53 MXN</td>
                        <td>
                            <div class="actions-cell">
                                <button class="action-btn">📋</button>
                                <button class="action-btn">✏️</button>
                                <button class="action-btn">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                    <td class="sku-cell">RTMZO-MYT</td>
                        <td>Flete manzanillo a monterrey</td>
                        <td class="price-cell">$ 299.53 MXN</td>
                        <td>
                            <div class="actions-cell">
                                <button class="action-btn">📋</button>
                                <button class="action-btn">✏️</button>
                                <button class="action-btn">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                    <td class="sku-cell">RTMZO-MYT</td>
                        <td>Flete manzanillo a monterrey</td>
                        <td class="price-cell">$ 5.00 MXN</td>
                        <td>
                            <div class="actions-cell">
                                <button class="action-btn">📋</button>
                                <button class="action-btn">✏️</button>
                                <button class="action-btn">🗑️</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

    <!-- Modal -->
    <div id="modalNuevo" class="modal">
        <div class="modal-content">
            <button class="btn-close" id="btnCerrar">&times;</button>
            <header class="header">
                <h1>Nuevo Servicio</h1>
            </header>
            <form class="product-form" id="productForm">
                <div class="form-sections">
                    <section class="form-section" id="generales">
                        <div class="form-group">
                            <label for="servicio">Nombre de Servicio <span class="required">*</span></label>
                            <input type="text" id="servicio" name="servicio" class="form-control" placeholder="Nombre del servicio" required>
                        </div>
                        <div class="form-group">
                            <label for="dservicio">Descripción del Servicio <span class="required">*</span></label>
                            <input type="text" id="dservicio" name="dservicio" class="form-control" placeholder="Descripción del servicio" required>
                        </div>
                    </section>

                    <section class="form-section" id="detalles">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="id">Clave del servicio</label>
                                <input type="text" id="id" class="form-control" placeholder="Ejemplo: #F1234">
                            </div>
                            <div class="form-group">
                                <label for="categoria">Categoría</label>
                                <select id="categoria" class="form-control">
                                    <option value="flete">Fletes</option>
                                    <option value="ruta">Ruta</option>
                                    <option value="otros">Otros</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="precio">Precio</label>
                                <input type="number" id="precio" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="moneda">Moneda</label>
                                <select id="moneda" class="form-control">
                                    <option value="MXN">MXN - Peso mexicano</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Configuracion Rutas -->
                    <section class="form-section hidden" id="rutaDetails">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="estadoOrigen">Estado de Origen</label>
                                <input type="text" id="estadoOrigen" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="municipioOrigen">Municipio de Origen</label>
                                <input type="text" id="municipioOrigen" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="estadoDestino">Estado de Destino</label>
                                <input type="text" id="estadoDestino" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="municipioDestino">Municipio de Destino</label>
                                <input type="text" id="municipioDestino" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="kilometros">Kilómetros</label>
                                <input type="number" id="kilometros" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="costoMantenimiento">Costo Mantenimiento</label>
                                <input type="number" id="costoMantenimiento" class="form-control">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="costoCasetas">Costo Casetas</label>
                                <input type="number" id="costoCasetas" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="costoGasolina">Costo Gasolina</label>
                                <input type="number" id="costoGasolina" class="form-control">
                            </div>
                        </div>
                    </section>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="reset" class="btn btn-secondary">Limpiar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const btnNuevo = document.getElementById('btnNuevo');
        const modalNuevo = document.getElementById('modalNuevo');
        const btnCerrar = document.getElementById('btnCerrar');
        const categoriaSelect = document.getElementById('categoria');
        const rutaDetails = document.getElementById('rutaDetails');

        // Mostrar el modal
        btnNuevo.addEventListener('click', () => {
            modalNuevo.style.display = 'flex';
        });

        // Cerrar el modal
        btnCerrar.addEventListener('click', () => {
            modalNuevo.style.display = 'none';
        });

        // Cerrar modal al hacer clic fuera de él
        window.addEventListener('click', (event) => {
            if (event.target === modalNuevo) {
                modalNuevo.style.display = 'none';
            }
        });

        // Show/hide Ruta-specific fields based on category selection
        categoriaSelect.addEventListener('change', (event) => {
            if (event.target.value === 'ruta') {
                rutaDetails.classList.remove('hidden');
            } else {
                rutaDetails.classList.add('hidden');
            }
        });
    </script>
</body>
</html>