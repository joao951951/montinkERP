class CartHandler {
    constructor() {
        this.variationSelect = document.getElementById('cart-variation');
        this.quantityInput = document.getElementById('cart-quantity');
        this.addToCartBtn = document.getElementById('add-to-cart-btn');
        this.feedbackDiv = document.getElementById('cart-feedback');
        this.buySection = document.getElementById('buy-section');
        
        if (this.addToCartBtn) {
            this.cartAddUrl = this.buySection.dataset.cartAddUrl;
            this.productId = this.buySection.dataset.productId;
            this.initEvents();
        }
    }
    
    initEvents() {
        if (this.variationSelect) {
            this.variationSelect.addEventListener('change', () => this.handleVariationChange());
        }
        
        this.addToCartBtn.addEventListener('click', () => this.addToCart());
    }
    
    handleVariationChange() {
        const selectedOption = this.variationSelect.options[this.variationSelect.selectedIndex];
        const maxQuantity = selectedOption.getAttribute('data-stock');
        
        this.quantityInput.max = maxQuantity;
        this.quantityInput.value = Math.min(1, maxQuantity);
    }
    
    async addToCart() {
        const formData = {
            product_id: this.productId,
            quantity: this.quantityInput.value,
            _token: document.querySelector('#_token').value
        };

        if(this.variationSelect){
            formData.variation_id = this.variationSelect.value;
        }

        try {
            const response = await fetch(this.cartAddUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': formData._token
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Erro ao adicionar ao carrinho');
            }
            
            this.showFeedback('success', data.message);

            console.log(data);

            this.updateCartCounter(Object.keys(data));
            
        } catch (error) {
            this.showFeedback('danger', error.message);
            console.error('Error:', error);
        }
    }
    
    showFeedback(type, message) {
        this.feedbackDiv.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }
    
    updateCartCounter(count) {
        const cartCounter = document.querySelector('.cart-counter');
        if (cartCounter) {
            cartCounter.textContent = 1;
            cartCounter.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new CartHandler();
});