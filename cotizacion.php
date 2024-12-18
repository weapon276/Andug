<?php
include 'modelo/conexion.php';

session_start();

if (!isset($_SESSION['userType'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['userId'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/cotizacion.css">
    <link rel="stylesheet" href="assets/css/rservicios.css">
    <script src="assets/js/cotizacion.js"></script>
</head>   
<style>
     
    </style>
<body>  
    
     <header class="header">
            <h1 class="title">Cotización #1234</h1>
            <button class="edit-button" id="editButton">Editar</button>
        </div>
        
        </header>
        
        <div class="main-content">
            <div class="left-column">

                <div class="quote-details">
                    <div class="section-header">
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
                    <span class="detail-label">Operador:</span>
                    <span class="detail-value" data-field="currency">Alan Vasconcelos</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Camion:</span>
                    <span class="detail-value" data-field="salesChannel">KT213</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Dolly:</span>
                    <span class="detail-value" data-field="salesChannel">SI</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Remolque:</span>
                    <span class="detail-value" data-field="salesChannel">SI</span>
                </div>
            </div>

            <div class="products-section">
                    <div class="section-header">
                        <h2>Servicios</h2>
                    <button class="add-product-button" id="addProductButton">Agregar servicios</button>
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

        <div class="right-column">
        <div class="summary-section">
                    <h3>Resumen
                    <button class="options-button" onclick="toggleDropdown()">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
            </h3>
            
                <div class="dropdown-menu" id="dropdownMenu">
                    <button onclick="handleAction('generate')">Generar venta</button>
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
                    <button class="print-button" id="printButton">Imprimir</button>
                </div>
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
               <!-- Los servicios deben venir de la base de datos -->
                        <label class="required">Producto</label>
                        <select class="form-control" required>
                            <option value="">Seleccione una opción</option>
                            <option value="1">Producto 1</option>
                            <option value="2">Producto 2</option>
                        </select>
                        <div class="validation-message">Por favor, introduzca 2 caracteres</div>
                    </div>
            <!-- Fin -->
              <!-- La cantidad por default debe ser 1 y el precio debe estar autoamticamente al selecionar el tipo de servicio de la base de datos -->
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
            <!-- Fin -->
                    <div class="form-row">
                        <div class="form-group">
                            <label>% de protección</label>
                            <input type="number" class="form-control" step="0.01">
                        </div>
                    </div>

                    <div class="form-group">
   
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
     <!-- New Generate Sale Modal -->
 <div class="modal-overlay" id="generateSaleModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Generar venta</h2>
                <button class="close-button" onclick="closeModal()">&times;</button>
            </div>
            
            <div class="modal-body">
                <!-- Confirmation State -->
                <div class="confirmation-state active" id="confirmationState">
                    <p>¿Está seguro de que desea generar la venta a partir de esta cotización?</p>
                </div>

                <!-- Success State -->
                <div class="success-state" id="successState">
                    <div class="success-message" id="successMessage">
                        <div class="success-icon">✓</div>
                        <div class="success-content">
                            <div class="success-title">Venta generada correctamente</div>
                            <div>Ir a la venta: <a href="#" class="success-link">#587</a></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <!-- Confirmation State Buttons -->
                <div id="confirmationButtons" class="confirmation-state active">
                    <button class="btn btn-secondary" onclick="closeModal()">Cerrar</button>
                    <button class="btn btn-primary" onclick="acceptSale()">Aceptar</button>
                </div>
                
                <!-- Success State Button -->
                <div id="successButtons" class="success-state">
                    <button class="btn btn-secondary" onclick="closeModal()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdownMenu');
            dropdown.classList.toggle('active');
        }

        function handleAction(action) {
            console.log('Action:', action);
            // Add your action handling logic here
            toggleDropdown(); // Close dropdown after action
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdownMenu');
            const optionsButton = document.querySelector('.options-button');
            
            if (!optionsButton.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.key === 'F9') {
                handleAction('send');
            } else if (event.key === 'F10') {
                handleAction('new');
            } else if (event.key === 'F4') {
                handleAction('generate');
            }
        });
    </script>
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdownMenu');
            dropdown.classList.toggle('active');
        }

        function handleAction(action) {
            if (action === 'generate') {
                showModal();
            }
            toggleDropdown(); // Close dropdown after action
        }

        function showModal() {
            const modal = document.getElementById('generateSaleModal');
            modal.classList.add('active');
        }

        function closeModal() {
            const modal = document.getElementById('generateSaleModal');
            modal.classList.remove('active');
            
            // Reset to confirmation state after closing
            setTimeout(() => {
                document.getElementById('confirmationState').classList.add('active');
                document.getElementById('confirmationButtons').classList.add('active');
                document.getElementById('successState').classList.remove('active');
                document.getElementById('successButtons').classList.remove('active');
            }, 300);
        }

        function acceptSale() {
            // Hide confirmation state
            document.getElementById('confirmationState').classList.remove('active');
            document.getElementById('confirmationButtons').classList.remove('active');
            
            // Show success state
            document.getElementById('successState').classList.add('active');
            document.getElementById('successButtons').classList.add('active');
            
            // Animate success message
            setTimeout(() => {
                document.getElementById('successMessage').classList.add('active');
            }, 50);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdownMenu');
            const optionsButton = document.querySelector('.options-button');
            
            if (!optionsButton.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Close modal when clicking outside
        document.getElementById('generateSaleModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('generateSaleModal')) {
                closeModal();
            }
        });

        // Handle keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            if (event.key === 'F9') {
                handleAction('send');
            } else if (event.key === 'F10') {
                handleAction('new');
            } else if (event.key === 'F4') {
                handleAction('generate');
            }
        });
    </script>
</body>
</html>