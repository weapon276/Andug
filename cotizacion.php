<?php
include 'modelo/conexion.php';

// Iniciar la sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['userType'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario que está creando la factura
$usuario_id = $_SESSION['userId'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/css/cotizacion.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .table thead {
            background-color: #e9ecef;
        }
        .summary-card {
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .summary-card h5 {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header animate__animated animate__fadeIn">
            <h1 class="title">Cotización #1234</h1>
            <button class="edit-button" id="editButton">Editar</button>
        </div>

        <div class="main-content">
            <div class="details-grid animate__animated animate__fadeIn" id="detailsGrid">
                <div class="detail-item">
                    <span class="detail-label">Fecha y hora:</span>
                    <span class="detail-value" data-field="datetime">En la que se esta realizando la cotizacion</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Vendedor:</span>
                    <span class="detail-value" data-field="currency">Alan Vasconcelos</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Cliente:</span>
                    <span class="detail-value" data-field="user">Al que se le realiza la cotizacion</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Camion:</span>
                    <span class="detail-value" data-field="salesChannel">KT213</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Dolly:</span>
                    <span class="detail-value" data-field="salesChannel">SI</span>
                </div>
            </div>

            <div class="table-container animate__animated animate__fadeIn">
                <div class="table-header">
                    <h2 class="table-title">Productos</h2>
                    <button class="add-product-button" id="addProductButton">Agregar producto</button>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>SERVICIO</th>
                            <th>CLAVE DEL SERVICIO</th>
                            <th>CATEGORIA</th>
                            <th>Cantidad #</th>
                            <th>SUBTOTAL</th>
                            <th>ACCIONES</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <tr>
                            <td>Mzo - Mty.</td>
                            <td>12344</td>
                            <td>Ruta</td>
                            <td>23</td>
                            <td>$ 1,184.27 MXN</td>
                            <td>···</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="summary-container animate__animated animate__fadeIn">
            <div class="summary-card">
                <div class="table-header">
                    <h2 class="table-title">Resumen</h2>
                </div>
                <div class="summary-table">
                    <div class="summary-row">
                        <span class="summary-label">Subtotal(=):</span>
                        <span class="summary-value">$ 1,184.27</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Total:</span>
                        <span class="summary-value">$ 1,184.27</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">IVA:</span>
                        <span class="summary-value">$ 19.25</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Cantidad:</span>
                        <span class="summary-value">1.00</span>
                    </div>
                      <hr>
                    <div class="summary-row">
                        <span class="summary-label">Peso contenedor:</span>
                        <span class="summary-value">0.00 kg</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Peso maximo a transportar:</span>
                        <span class="summary-value">0.00 kg</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Contenerizada:</span>
                        <span class="summary-value">40"HC</span>
                        
                    </div>
                    <button class="print-button" id="printButton">Imprimir (F8)</button>
                </div>
            </div>
        </div>
    </div>

    <div id="addProductModal" class="modal">
        <div class="modal-content animate__animated animate__fadeIn">
            <div class="modal-header">
                <h2 class="modal-title">Agregar producto</h2>
                <button class="close-button">&times;</button>
            </div>
            <form id="addProductForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required">Producto</label>
                        <select class="form-control" required>
                            <option value="">Seleccione una opción</option>
                            <option value="1">Producto 1</option>
                            <option value="2">Producto 2</option>
                        </select>
                        <div class="validation-message">Por favor, introduzca 2 caracteres</div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Cantidad</label>
                            <input type="number" class="form-control" min="1">
                        </div>
                        <div class="form-group">
                            <label class="required">Precio</label>
                            <input type="number" class="form-control" step="0.01" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>% de protección</label>
                            <input type="number" class="form-control" step="0.01">
                        </div>
                        <div class="form-group">
                            <label>% de comisionista</label>
                            <input type="number" class="form-control" step="0.01">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Protección neta (por unidad)</label>
                            <input type="number" class="form-control" step="0.01">
                        </div>
                        <div class="form-group">
                            <label>% de descuento</label>
                            <input type="number" class="form-control" step="0.01">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tiempo de entrega</label>
                        <select class="form-control">
                            <option value="">Seleccione una opción</option>
                            <option value="1">Inmediato</option>
                            <option value="2">24 horas</option>
                            <option value="3">48 horas</option>
                            <option value="4">72 horas</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButton = document.getElementById('editButton');
            const detailsGrid = document.getElementById('detailsGrid');
            const addProductButton = document.getElementById('addProductButton');
            const modal = document.getElementById('addProductModal');
            const closeButton = modal.querySelector('.close-button');
            const cancelButton = modal.querySelector('.btn-secondary');
            const addProductForm = document.getElementById('addProductForm');
            const productTableBody = document.getElementById('productTableBody');
            const printButton = document.getElementById('printButton');

            let isEditing = false;

            editButton.addEventListener('click', function() {
                isEditing = !isEditing;
                detailsGrid.classList.toggle('edit-mode');
                editButton.textContent = isEditing ? 'Guardar' : 'Editar';

                const detailValues = detailsGrid.querySelectorAll('.detail-value');
                detailValues.forEach(value => {
                    if (isEditing) {
                        const input = document.createElement('input');
                        input.value = value.textContent;
                        input.dataset.field = value.dataset.field;
                        value.textContent = '';
                        value.appendChild(input);
                    } else {
                        const input = value.querySelector('input');
                        value.textContent = input.value;
                    }
                });
            });

            addProductButton.addEventListener('click', function() {
                modal.style.display = 'block';
            });

            function closeModal() {
                modal.style.display = 'none';
            }

            closeButton.addEventListener('click', closeModal);
            cancelButton.addEventListener('click', closeModal);

            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            addProductForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const product = modal.querySelector('select').options[modal.querySelector('select').selectedIndex].text;
                const quantity = modal.querySelector('input[type="number"]').value;
                const price = modal.querySelector('input[type="number"][required]').value;

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${product}</td>
                    <td>SKU-${Math.floor(Math.random() * 1000)}</td>
                    <td>${quantity}</td>
                    <td>$ ${parseFloat(price).toFixed(2)} MXN</td>
                    <td>···</td>
                `;
                newRow.classList.add('animate__animated', 'animate__fadeIn');
                productTableBody.appendChild(newRow);

                closeModal();
                addProductForm.reset();
            });

            printButton.addEventListener('click', function() {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();
                
                doc.text('Cotización #240', 20, 20);
                doc.text('Fecha: ' + document.querySelector('[data-field="datetime"]').textContent, 20, 30);
                
                let yPos = 40;
                const detailItems = detailsGrid.querySelectorAll('.detail-item');
                detailItems.forEach(item => {
                    const label = item.querySelector('.detail-label').textContent;
                    const value = item.querySelector('.detail-value').textContent;
                    doc.text(`${label} ${value}`, 20, yPos);
                    yPos += 10;
                });

                yPos += 10;
                doc.text('Productos:', 20, yPos);
                yPos += 10;
                const products = productTableBody.querySelectorAll('tr');
                products.forEach(product => {
                    const cells = product.querySelectorAll('td');
                    let productText = '';
                    cells.forEach(cell => {
                        productText += cell.textContent + ' | ';
                    });
                    doc.text(productText, 20, yPos);
                    yPos += 10;
                });

                doc.save('cotizacion.pdf');
            });
        });
    </script>
</body>
</html>