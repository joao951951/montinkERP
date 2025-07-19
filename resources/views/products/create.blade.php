@extends('layouts.app')

@section('title', 'Cadastrar Produto')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-box-seam"></i> Cadastrar Novo Produto
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
        <form method="POST" action="{{ route('products.store') }}" id="product-form">
            @csrf

            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nome do Produto" required>
                        <label for="name">Nome do Produto*</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="hidden" name="active" value="0">
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                        <label class="form-check-label" for="active">Produto Ativo</label>
                    </div>
                </div>
            </div>

            <div class="form-floating mb-4">
                <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Preço" required>
                <label for="price">Preço (R$)*</label>
            </div>

            <hr class="my-4">

            <h5 class="mb-3">
                <i class="bi bi-list-nested"></i> Variações do Produto
            </h5>
            <p class="text-muted mb-4">Adicione variações como tamanhos, cores, etc. Se não houver variações, preencha o estoque abaixo.</p>

            <div id="variations-container">
                <!-- Variações serão adicionadas aqui -->
            </div>

            <button type="button" class="btn btn-outline-primary mb-4" id="add-variation">
                <i class="bi bi-plus-circle"></i> Adicionar Variação
            </button>

            <div class="card mb-4" id="base-inventory-card">
                <div class="card-header bg-light text-dark">
                    <i class="bi bi-box-seam"></i> Controle de Estoque
                </div>
                <div class="card-body">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Quantidade" min="0" value="0" required>
                        <label for="quantity">Quantidade em Estoque*</label>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <button type="reset" class="btn btn-outline-secondary me-md-2">
                    <i class="bi bi-x-circle"></i> Limpar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Cadastrar Produto
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