/**
 * products.js - Manages the logic of the product
 * 
 */

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('variations-container');
    const addButton = document.getElementById('add-variation');
    const template = document.getElementById('variation-template');
    const baseInventoryCard = document.getElementById('base-inventory-card');
    const form = document.getElementById('product-form');

    let variationCount = document.querySelectorAll('.variation-item').length;

    /**
     * Adds a new variation to the form
     */
    function addVariation() {
        const clone = template.content.cloneNode(true);
        const inputs = clone.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            const name = input.name.replace('[]', `[${variationCount}]`);
            input.name = name;
        });
        
        container.appendChild(clone);
        variationCount++;
        
        toggleBaseInventoryVisibility();
    }

    /**
     * Remove a variation from the form
     */
    function removeVariation(event) {
        if (event.target.classList.contains('remove-variation')) {
            const variationItem = event.target.closest('.variation-item');
            const hasId = variationItem.querySelector('input[type="hidden"][name*="[id]"]');
            
            if (hasId && !confirm('Tem certeza que deseja remover esta variação?')) {
                return;
            }
            
            variationItem.remove();
            variationCount--;
            
            toggleBaseInventoryVisibility();
        }
    }

    /**
     * Toggles the visibility of the base stock
     */
    function toggleBaseInventoryVisibility() {
        baseInventoryCard.style.display = variationCount > 0 ? 'none' : 'block';
    }

    /**
     * Validate form before submission
     */
    function validateForm(event) {
        if (variationCount === 0) {
            const baseQty = document.getElementById('quantity').value;
            if (!baseQty || baseQty < 0) {
                alert('Por favor, informe a quantidade em estoque ou adicione variações!');
                event.preventDefault();
            }
        }
    }

    addButton.addEventListener('click', addVariation);
    container.addEventListener('click', removeVariation);
    form.addEventListener('submit', validateForm);

    toggleBaseInventoryVisibility();
});

/**
 * Function to confirm product deletion
 */
function confirmDelete(productId) {
    if (confirm('Tem certeza que deseja excluir este produto?')) {
        document.getElementById('delete-form-' + productId).submit();
    }
}