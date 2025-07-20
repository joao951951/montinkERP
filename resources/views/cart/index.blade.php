@extends('layouts.app')

@section('title', 'Meu Carrinho')

@section('content')
<div class="container py-4">
    <h1 class="mb-4"><i class="bi bi-cart"></i> Carrinho de Compras</h1>

    @if(count($cartItems) > 0)
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-end">Preço Unitário</th>
                                <th class="text-center">Quantidade</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $key => $item)
                            <tr>
                                <td>
                                    <strong>{{ $item['name'] }}</strong>
                                    @if($item['variation_id'])
                                        <div class="text-muted small">Variação ID: {{ $item['variation_id'] }}</div>
                                    @endif
                                </td>
                                <td class="text-end">R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <form action="{{ route('cart.update', $key) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <div class="input-group input-group-sm" style="width: 120px;">
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" 
                                                   min="1" class="form-control">
                                            <button type="submit" class="btn btn-outline-primary" title="Atualizar">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                                <td class="text-end">R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <form action="{{ route('cart.remove', $key) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Remover">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-group-divider">
                            <tr>
                                <th colspan="3" class="text-end">Total do Carrinho:</th>
                                <th class="text-end">R$ {{ number_format($cartTotal, 2, ',', '.') }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Continuar Comprando
            </a>
            <div>
                <form action="{{ route('cart.clear') }}" method="POST" class="d-inline me-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-x-circle"></i> Limpar Carrinho
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center py-4">
            <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
            <h4 class="mt-3">Seu carrinho está vazio</h4>
            <p class="mb-0">Adicione produtos para continuar</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">
                <i class="bi bi-bag"></i> Ver Produtos
            </a>
        </div>
    @endif
</div>
@endsection