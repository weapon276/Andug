<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Seguros</title>
    <link rel="stylesheet" href="assets/css/camion.css">
    <link rel="stylesheet" href="assets/css/servicios.css">
    <link rel="stylesheet" href="assets/css/rservicios.css">
    <link rel="stylesheet" href="assets/css/dolly.css">
    <link rel="stylesheet" href="assets/css/modal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Listado de Seguros</h1>
            <div class="actions">
                <button id="btnNuevoSeguro" class="btn btn-primary">Agregar Seguro</button>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" class="search-input" placeholder="Buscar por empresa aseguradora o póliza..." id="searchInput">
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th data-sort="fk_id_camion"><i class="fas fa-truck"></i> ID Camión</th>
                        <th data-sort="empresa_aseguradora">Empresa Aseguradora</th>
                        <th data-sort="vigencia">Vigencia</th>
                        <th data-sort="status_seguro">Estado</th>
                        <th data-sort="tipo_pago">Tipo de Pago</th>
                        <th data-sort="polizaAr">Póliza</th>
                        <th data-sort="fecha_creacion">Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="segurosTableBody">
                    <!-- Los datos de seguros se insertarán aquí dinámicamente -->
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <div id="paginationInfo"></div>
            <div class="pagination-controls" id="paginationControls"></div>
        </div>
    </div>

    <!-- Modal para Nuevo/Editar Seguro -->
    <div id="modalSeguro" class="modal">
        <div class="modal-content animate-fade-in">
            <span class="close">&times;</span>
            <h2 id="modalTitle">Agregar Nuevo Seguro</h2>
            <form id="formSeguro">
                <input type="hidden" id="seguroId" name="id_seguro">
                <div class="form-group">
                    <label for="fk_id_camion">ID Camión:</label>
                    <input type="number" id="fk_id_camion" name="fk_id_camion" required>
                </div>
                <div class="form-group">
                    <label for="empresa_aseguradora">Empresa Aseguradora:</label>
                    <input type="text" id="empresa_aseguradora" name="empresa_aseguradora" required>
                </div>
                <div class="form-group">
                    <label for="vigencia">Vigencia:</label>
                    <input type="date" id="vigencia" name="vigencia" required>
                </div>
                <div class="form-group">
                    <label for="status_seguro">Estado del Seguro:</label>
                    <select id="status_seguro" name="status_seguro" required>
                        <option value="Activo">Activo</option>
                        <option value="Vencido">Vencido</option>
                        <option value="Falta de pago">Falta de pago</option>
                        <option value="Cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tipo_pago">Tipo de Pago:</label>
                    <select id="tipo_pago" name="tipo_pago" required>
                        <option value="Mensual">Mensual</option>
                        <option value="Trimestral">Trimestral</option>
                        <option value="Anual">Anual</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="polizaAr">Póliza:</label>
                    <input type="text" id="polizaAr" name="polizaAr">
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
    </div>

    <script>
        // Datos de ejemplo (reemplazar con datos reales de la base de datos)
        const segurosData = [
            { id_seguro: 1, fk_id_camion: 101, empresa_aseguradora: "Seguros XYZ", vigencia: "2023-12-31", status_seguro: "Activo", tipo_pago: "Mensual", polizaAr: "POL-001", fecha_creacion: "2023-01-01" },
            { id_seguro: 2, fk_id_camion: 102, empresa_aseguradora: "Aseguradora ABC", vigencia: "2023-11-30", status_seguro: "Vencido", tipo_pago: "Anual", polizaAr: "POL-002", fecha_creacion: "2023-02-15" },
            // Agregar más datos de ejemplo aquí
        ];

        let currentPage = 1;
        const itemsPerPage = 10;
        let sortColumn = 'id_seguro';
        let sortOrder = 'ASC';

        const modalSeguro = document.getElementById('modalSeguro');
        const btnNuevoSeguro = document.getElementById('btnNuevoSeguro');
        const closeBtn = document.getElementsByClassName('close')[0];
        const formSeguro = document.getElementById('formSeguro');
        const searchInput = document.getElementById('searchInput');

        function renderTable(data) {
            const tableBody = document.getElementById('segurosTableBody');
            tableBody.innerHTML = '';

            data.forEach(seguro => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${seguro.fk_id_camion}</td>
                    <td>${seguro.empresa_aseguradora}</td>
                    <td>${seguro.vigencia}</td>
                    <td><span class="status-badge ${seguro.status_seguro.toLowerCase().replace(' ', '-')}">${seguro.status_seguro}</span></td>
                    <td>${seguro.tipo_pago}</td>
                    <td>${seguro.polizaAr || 'N/A'}</td>
                    <td>${seguro.fecha_creacion}</td>
                    <td>
                        <button class="action-btn edit-btn" data-id="${seguro.id_seguro}">✏️</button>
                        <button class="action-btn delete-btn" data-id="${seguro.id_seguro}">🗑️</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            setupEditButtons();
            setupDeleteButtons();
        }

        function setupPagination(filteredData) {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            const paginationControls = document.getElementById('paginationControls');
            const paginationInfo = document.getElementById('paginationInfo');

            paginationControls.innerHTML = '';
            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.innerText = i;
                btn.classList.add('btn', i === currentPage ? 'btn-primary' : 'btn-secondary');
                btn.addEventListener('click', () => {
                    currentPage = i;
                    renderTableWithPagination(filteredData);
                });
                paginationControls.appendChild(btn);
            }

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
            paginationInfo.innerText = `Mostrando ${startIndex + 1} a ${endIndex} de ${filteredData.length} registros`;
        }

        function renderTableWithPagination(data) {
            const startIndex = (currentPage - 1) * itemsPerPage;
            const paginatedData = data.slice(startIndex, startIndex + itemsPerPage);
            renderTable(paginatedData);
            setupPagination(data);
        }

        function sortData(data, column, order) {
            return data.sort((a, b) => {
                if (a[column] < b[column]) return order === 'ASC' ? -1 : 1;
                if (a[column] > b[column]) return order === 'ASC' ? 1 : -1;
                return 0;
            });
        }

        function setupSorting() {
            const headers = document.querySelectorAll('th[data-sort]');
            headers.forEach(header => {
                header.addEventListener('click', () => {
                    const column = header.dataset.sort;
                    if (sortColumn === column) {
                        sortOrder = sortOrder === 'ASC' ? 'DESC' : 'ASC';
                    } else {
                        sortColumn = column;
                        sortOrder = 'ASC';
                    }
                    const sortedData = sortData(filterData(segurosData), sortColumn, sortOrder);
                    renderTableWithPagination(sortedData);
                });
            });
        }

        function filterData(data) {
            const searchTerm = searchInput.value.toLowerCase();
            return data.filter(seguro => 
                seguro.empresa_aseguradora.toLowerCase().includes(searchTerm) ||
                seguro.polizaAr.toLowerCase().includes(searchTerm)
            );
        }

        function setupSearch() {
            searchInput.addEventListener('input', () => {
                currentPage = 1;
                const filteredData = filterData(segurosData);
                renderTableWithPagination(filteredData);
            });
        }

        function setupEditButtons() {
            const editButtons = document.querySelectorAll('.edit-btn');
            editButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const seguroId = button.getAttribute('data-id');
                    const seguro = segurosData.find(s => s.id_seguro == seguroId);
                    if (seguro) {
                        document.getElementById('modalTitle').innerText = 'Editar Seguro';
                        document.getElementById('seguroId').value = seguro.id_seguro;
                        document.getElementById('fk_id_camion').value = seguro.fk_id_camion;
                        document.getElementById('empresa_aseguradora').value = seguro.empresa_aseguradora;
                        document.getElementById('vigencia').value = seguro.vigencia;
                        document.getElementById('status_seguro').value = seguro.status_seguro;
                        document.getElementById('tipo_pago').value = seguro.tipo_pago;
                        document.getElementById('polizaAr').value = seguro.polizaAr || '';
                        modalSeguro.style.display = 'block';
                    }
                });
            });
        }

        function setupDeleteButtons() {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const seguroId = button.getAttribute('data-id');
                    if (confirm('¿Está seguro de que desea eliminar este seguro?')) {
                        // Aquí iría la lógica para eliminar el seguro de la base de datos
                        segurosData = segurosData.filter(s => s.id_seguro != seguroId);
                        renderTableWithPagination(filterData(segurosData));
                    }
                });
            });
        }

        btnNuevoSeguro.onclick = function() {
            document.getElementById('modalTitle').innerText = 'Agregar Nuevo Seguro';
            formSeguro.reset();
            document.getElementById('seguroId').value = '';
            modalSeguro.style.display = 'block';
        }

        closeBtn.onclick = function() {
            modalSeguro.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modalSeguro) {
                modalSeguro.style.display = 'none';
            }
        }

        formSeguro.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(formSeguro);
            const seguroData = Object.fromEntries(formData.entries());
            
            if (seguroData.id_seguro) {
                // Actualizar seguro existente
                const index = segurosData.findIndex(s => s.id_seguro == seguroData.id_seguro);
                if (index !== -1) {
                    segurosData[index] = { ...segurosData[index], ...seguroData };
                }
            } else {
                // Agregar nuevo seguro
                seguroData.id_seguro = segurosData.length + 1;
                seguroData.fecha_creacion = new Date().toISOString().split('T')[0];
                segurosData.push(seguroData);
            }

            renderTableWithPagination(filterData(segurosData));
            modalSeguro.style.display = 'none';
        }

        // Inicialización
        renderTableWithPagination(segurosData);
        setupSorting();
        setupSearch();
    </script>
</body>
</html>