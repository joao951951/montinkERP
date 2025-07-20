@extends('layouts.app')

@section('title', isset($product) ? 'Editar Produto' : 'Cadastrar Produto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-box-seam"></i> {{ isset($product) ? 'Editar Produto' : 'Cadastrar Novo Produto' }}
    </h2>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Voltar
    </a>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Informações do Produto</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($product) ? route('products.update', $product->id) : route('products.store') }}" id="product-form">
            @csrf
            @if(isset($product))
                @method('PUT')
            @endif

            <!-- Dados Básicos -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Nome do Produto" required
                               value="{{ old('name', $product->name ?? '') }}">
                        <label for="name">Nome do Produto*</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="hidden" name="active" value="0">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="active" name="active" 
                               value="1" {{ (old('active', $product->active ?? true) ? 'checked' : '') }}>
                        <label class="form-check-label" for="active">Produto Ativo</label>
                    </div>
                </div>
            </div>

            <!-- Preço -->
            <div class="form-floating mb-4">
                <input type="number" step="0.01" class="form-control" id="price" name="price" 
                       placeholder="Preço" required
                       value="{{ old('price', $product->price ?? '') }}">
                <label for="price">Preço (R$)*</label>
            </div>

            <hr class="my-4">

            <!-- Variações -->
            <h5 class="mb-3">
                <i class="bi bi-list-nested"></i> Variações do Produto
            </h5>
            <p class="text-muted mb-4">Adicione variações como tamanhos, cores, etc. Se não houver variações, preencha o estoque abaixo.</p>

            <div id="variations-container">
                <!-- Variações existentes (em modo edição) -->
                @if(isset($product) && $product->variations->count() > 0)
                    @foreach($product->variations as $index => $variation)
                    <div class="card mb-3 variation-item">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center text-dark">
                            <span><i class="bi bi-tag"></i> Variação</span>
                            <button type="button" class="btn btn-sm btn-danger remove-variation">
                                <i class="bi bi-trash"></i> Remover
                            </button>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="variations[{{ $index }}][id]" value="{{ $variation->id }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" 
                                               name="variations[{{ $index }}][name]" 
                                               placeholder="Nome da Variação" required
                                               value="{{ old("variations.$index.name", $variation->name) }}">
                                        <label>Nome da Variação*</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" step="0.01" class="form-control" 
                                               name="variations[{{ $index }}][price_adjustment]" 
                                               placeholder="Acréscimo no Preço"
                                               value="{{ old("variations.$index.price_adjustment", $variation->price_adjustment) }}">
                                        <label>Acréscimo no Preço (R$)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="number" class="form-control" 
                                       name="variations[{{ $index }}][quantity]" 
                                       placeholder="Quantidade" min="0" required
                                       value="{{ old("variations.$index.quantity", $variation->inventory->quantity ?? 0) }}">
                                <label>Quantidade em Estoque*</label>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            <button type="button" class="btn btn-outline-primary mb-4" id="add-variation">
                <i class="bi bi-plus-circle"></i> Adicionar Variação
            </button>

            <!-- Estoque Base (apenas para produtos sem variações) -->
            <div class="card mb-4" id="base-inventory-card" style="{{ isset($product) && $product->variations->count() > 0 ? 'display: none;' : '' }}">
                <div class="card-header bg-light text-dark">
                    <i class="bi bi-box-seam"></i> Controle de Estoque
                </div>
                <div class="card-body">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="quantity" name="quantity" 
                               placeholder="Quantidade" min="0" required
                               value="{{ old('quantity', isset($product) && $product->variations->count() == 0 ? $product->inventory->quantity ?? 0 : '0') }}">
                        <label for="quantity">Quantidade em Estoque*</label>
                    </div>
                </div>
            </div>
            <!-- Adicionar produto ao carrinho -->
            @if(isset($product) && $product->active)
                <hr class="my-4">
                <h5 class="mb-3"><i class="bi bi-cart-plus"></i> Adicionar ao Carrinho</h5>
                
                <form method="POST" action="{{ route('cart.add', $product) }}" class="row g-3">
                    @csrf
                    
                    @if($product->variations->count() > 0)
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="variation_id" name="variation_id" required>
                                    <option value="">Selecione uma variação</option>
                                    @foreach($product->variations as $variation)
                                        <option value="{{ $variation->id }}" 
                                            data-quantity="{{ $variation->inventory->quantity ?? 0 }}">
                                            {{ $variation->name }} 
                                            (Estoque: {{ $variation->inventory->quantity ?? 0 }})
                                            @if($variation->price_adjustment)
                                                ({{ $variation->price_adjustment > 0 ? '+' : '' }}{{ number_format($variation->price_adjustment, 2, ',', '.') }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <label for="variation_id">Variação*</label>
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="variation_id" value="">
                        <div class="col-12">
                            <p>Estoque disponível: {{ $product->inventory->quantity ?? 0 }}</p>
                        </div>
                    @endif
                    
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="quantity" name="quantity" 
                                value="1" min="1" required
                                max="{{ $product->variations->count() ? '' : ($product->inventory->quantity ?? 1) }}">
                            <label for="quantity">Quantidade*</label>
                        </div>
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-cart-plus"></i> Comprar
                        </button>
                    </div>
                </form>
            @endif
            <!-- Adicionar produto ao carrinho -->

            <!-- Botões de Ação -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="reset" class="btn btn-outline-secondary me-md-2">
                    <i class="bi bi-x-circle"></i> Limpar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ isset($product) ? 'Atualizar' : 'Cadastrar' }} Produto
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template para novas variações -->
<template id="variation-template">
    <div class="card mb-3 variation-item">
        <div class="card-header bg-light d-flex justify-content-between align-items-center text-dark">
            <span><i class="bi bi-tag"></i> Variação</span>
            <button type="button" class="btn btn-sm btn-danger remove-variation">
                <i class="bi bi-trash"></i> Remover
            </button>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="variations[][name]" placeholder="Nome da Variação" required>
                        <label>Nome da Variação*</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="number" step="0.01" class="form-control" name="variations[][price_adjustment]" placeholder="Acréscimo no Preço" value="0">
                        <label>Acréscimo no Preço (R$)</label>
                    </div>
                </div>
            </div>
            <div class="form-floating mb-3">
                <input type="number" class="form-control" name="variations[][quantity]" placeholder="Quantidade" min="0" value="0" required>
                <label>Quantidade em Estoque*</label>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script src="{{ asset('js/products.js') }}"></script>
@endpush