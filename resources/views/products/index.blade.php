@extends('layouts.app')

@section('title', 'Lista de Produtos')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">
        <i class="bi bi-box-seam"></i> Lista de Produtos
    </h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Novo Produto
    </a>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0">Produtos Cadastrados</h4>
            </div>
            <div class="col-md-6">
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Pesquisar..." 
                               value="{{ request('search') }}">
                        <button class="btn btn-light" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($products->isEmpty())
            <div class="alert alert-info">Nenhum produto cadastrado ainda.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th class="text-end">Preço</th>
                            <th class="text-center">Estoque</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->variations->count() > 0)
                                    <span class="badge bg-info ms-2">
                                        {{ $product->variations->count() }} variações
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">R$ {{ number_format($product->price, 2, ',', '.') }}</td>
                            <td class="text-center">
                                @if($product->variations->count() > 0)
                                    <span class="text-muted">Por variação</span>
                                @else
                                    {{ $product->inventory->quantity }}
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $product->active ? 'success' : 'secondary' }}">
                                    {{ $product->active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('products.edit', $product->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete({{ $product->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $product->id }}" 
                                          action="{{ route('products.destroy', $product->id) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/products.js') }}"></script>
@endpush