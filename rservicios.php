<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo producto</title>
    <link rel="stylesheet" href="assets/css/rservicios.css">

</style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Nuevo Servicio</h1>
            <div class="header-actions">

            </div>
        </header>

        <nav class="tabs">
            <a href="#" class="tab-link active">Servicios</a>
        </nav>
        <form class="product-form" id="productForm">
            <div class="form-sections">
                <section class="form-section active" id="generales">
                    <div class="form-group">
                        <label for="servicio">Nombre de Servicio <span class="required">*</span></label>
                        <input type="text" id="servicio" name="servicio" class="form-control" placeholder="Nombre del servicio" required>
                    </div>
                    <div class="form-group">
                        <label for="dservicio">Descripción del Servicio <span class="required">*</span></label>
                        <input type="text" id="dservicio" name="dservicio" class="form-control" placeholder="Descripción del servicio" required>
                    </div>
                </section>

                <section class="form-section active" id="generales">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="id">Clave del servicio</label>
                            <input type="text" id="id" class="form-control" placeholder="Ejemplo: #F1234">
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoría</label>
                            <select id="categoria" class="form-control">
                                <option value="ruta">Ruta</option>
                                <option value="flete">Fletes</option>
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

                <section class="shipping-section" id="shippingSection">
                    <label class="switch">
                        <input type="checkbox" id="toggleShipping">
                        <span class="slider round"></span>
                        <span class="text">Peso y dimensiones</span>
                    </label>

                    <div id="shippingDetails" class="hidden">
                        <div class="info-box">
                            <span class="info-icon">ℹ️</span>
                            <p>Favor de insertar la información solicitada, de no contar con toda la información deje en blanco</p>
                        </div>

                        <div class="dimensions-grid">
                            <div class="form-group">
                                <label for="alto">Alto</label>
                                <div class="input-group">
                                    <input type="number" id="alto" class="form-control" placeholder="0">
                                    <span class="unit">En CM</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="largo">Largo</label>
                                <div class="input-group">
                                    <input type="number" id="largo" class="form-control" placeholder="0">
                                    <span class="unit">En CM</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="ancho">Ancho</label>
                                <div class="input-group">
                                    <input type="number" id="ancho" class="form-control" placeholder="0">
                                    <span class="unit">En CM</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="peso">Peso</label>
                                <div class="input-group">
                                    <input type="number" id="peso" class="form-control" placeholder="0">
                                    <span class="unit">En KG</span>
                                </div>
                            </div>
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

    <script>
        const toggleButton = document.getElementById('toggleShipping');
        const shippingDetails = document.getElementById('shippingDetails');

        toggleButton.addEventListener('click', () => {
            const isHidden = shippingDetails.classList.toggle('hidden');
            toggleButton.textContent = isHidden 
                ? 'Activar peso y dimensiones' 
                : 'Desactivar peso y dimensiones';
        });

    </script>
</body>
</html>
