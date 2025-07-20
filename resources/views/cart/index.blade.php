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
                                        <div class="text-muted small">Variação ID: {{ $item['variation_id'] . ' - ' . $item['variation_name'] }}</div>
                                    @endif
                                </td>
                                <td class="text-end">R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                                <td class="text-center">
                                    <form action="{{ route('cart.updateCartItem', $key) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <div class="input-group input-group-sm" style="width: 120px;">
                                            <input type="number" 
                                                name="quantity" 
                                                value="{{ old('quantity', $item['quantity']) }}" 
                                                min="1" 
                                                max="{{ $item['max_available'] ?? config('cart.max_quantity', 1000) }}"
                                                class="form-control @error('quantity') is-invalid @enderror"
                                                aria-label="Quantidade">
                                            <button type="submit" class="btn btn-outline-primary" title="Atualizar">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            @error('quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                <th colspan="3" class="text-end">Subtotal:</th>
                                <th class="text-end">R$ {{ number_format($cartTotal, 2, ',', '.') }}</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Frete:</th>
                                <th class="text-end">
                                    @if($shippingCost > 0)
                                        R$ {{ number_format($shippingCost, 2, ',', '.') }}
                                    @else
                                        <span class="text-success">Grátis</span>
                                    @endif
                                </th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Total:</th>
                                <th class="text-end">R$ {{ number_format($cartTotal + $shippingCost, 2, ',', '.') }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0 text-dark"><i class="bi bi-truck"></i> Calcular Frete</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('cart.calculateShipping') }}" method="POST">
                    @csrf
                    <div class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <label for="cep" class="form-label">Digite seu CEP</label>
                            <div class="input-group">
                                <input type="text" 
                                    class="form-control @error('cep') is-invalid @enderror" 
                                    id="cep" 
                                    name="cep" 
                                    value="{{ old('cep', session('shipping_cep')) }}"
                                    placeholder="00000-000"
                                    required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-calculator"></i> Calcular Frete
                                </button>
                                @error('cep')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if(session('shipping_address'))
                                <div class="alert alert-success mb-0 py-2">
                                    <i class="bi bi-check-circle"></i> 
                                    Frete calculado para: {{ session('shipping_address') }}
                                    <div class="small">CEP: {{ session('shipping_cep') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Incentivo para frete grátis --}}
        @if($shippingCost > 0 && $missingForFreeShipping > 0)
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i> Adicione mais R$ {{ number_format($missingForFreeShipping, 2, ',', '.') }} em produtos para ganhar frete grátis!
            </div>
        @endif

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Continuar Comprando
            </a>
            <div>
                <form action="{{ route('cart.clearCart') }}" method="POST" class="d-inline me-2">
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