<hr class="my-4">
<h5 class="mb-3"><i class="bi bi-cart-plus"></i> Comprar Produto</h5>
<div id="buy-section" class="row g-3" data-cart-add-url="{{ route('cart.store') }}" data-product-id="{{ $product->id }}">
    @if($product->variations->count() > 0)
        <div class="col-md-6">
            <div class="form-floating">
                <select class="form-select" id="cart-variation" name="variation_id" required>
                    <option value="">Selecione uma variação</option>
                    @foreach($product->variations as $variation)
                        <option value="{{ $variation->id }}" 
                            data-price="{{ $product->price + $variation->price_adjustment }}"
                            data-stock="{{ $variation->inventory->quantity ?? 0 }}">
                            {{ $variation->name }} 
                            (R$ {{ number_format($product->price + $variation->price_adjustment, 2, ',', '.') }})
                            - {{ $variation->inventory->quantity ?? 0 }} disponíveis
                        </option>
                    @endforeach
                </select>
                <label for="cart-variation">Variação*</label>
            </div>
        </div>
    @else
        <input type="hidden" name="variation_id" value="">
        <div class="col-12">
            <p class="mb-2">Estoque disponível: <span id="available-stock">{{ $product->inventory->quantity ?? 0 }}</span></p>
        </div>
    @endif
    
    <div class="col-md-4">
        <div class="form-floating">
            <input type="number" class="form-control" id="cart-quantity" 
                    name="quantity" value="1" min="1" 
                    max="{{ $product->variations->count() ? '' : ($product->inventory->quantity ?? 1) }}"
                    required>
            <label for="cart-quantity">Quantidade*</label>
        </div>
    </div>
    
    <div class="col-md-2 d-flex align-items-end">
        <button type="button" id="add-to-cart-btn" class="btn btn-primary w-100">
            <i class="bi bi-cart-plus"></i> Comprar
        </button>
    </div>
</div>
<div id="cart-feedback" class="mt-3"></div>