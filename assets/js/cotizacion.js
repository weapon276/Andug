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
