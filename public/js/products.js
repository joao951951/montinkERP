document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('variations-container');
    const addButton = document.getElementById('add-variation');
    const template = document.getElementById('variation-template');
    const baseInventoryCard = document.getElementById('base-inventory-card');
    let variationCount = 0;
    
    addButton.addEventListener('click', function() {
        const clone = template.content.cloneNode(true);
        const inputs = clone.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            const name = input.name.replace('[]', `[${variationCount}]`);
            input.name = name;
        });
        container.appendChild(clone);
        variationCount++;
        baseInventoryCard.style.display = variationCount > 0 ? 'none' : 'block';
    });

    container.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variation')) {
            e.target.closest('.variation-item').remove();
            variationCount--;
            baseInventoryCard.style.display = variationCount > 0 ? 'none' : 'block';
        }
    });

    document.getElementById('product-form').addEventListener('submit', function(e) {
        if (variationCount === 0) {
            const baseQty = document.getElementById('quantity').value;
            if (!baseQty || baseQty < 0) {
                alert('Por favor, informe a quantidade em estoque ou adicione variações!');
                e.preventDefault();
            }
        }
    });
});
function confirmDelete(productId) {
    if (confirm('Tem certeza que deseja excluir este produto?')) {
        document.getElementById('delete-form-' + productId).submit();
    }
}