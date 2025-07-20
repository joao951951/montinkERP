@extends('layouts.app')

@section('title', 'Carrinho de Compras')

@section('content')
<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-cart"></i> Seu Carrinho</h2>
    
    @if(empty($cart))
        <div class="alert alert-info">Seu carrinho está vazio</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço Unitário</th>
                        <th>Quantidade</th>
                        <th>Subtotal</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $id => $item)
                    <tr>
                        <td>
                            {{ $item['name'] }}
                            @if($item['variation_name'])
                                <br><small class="text-muted">Variação: {{ $item['variation_name'] }}</small>
                            @endif
                        </td>
                        <td>R$ {{ number_format($item['price'], 2, ',', '.') }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>R$ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Remover
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td colspan="2">R$ {{ number_format($total, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="d-flex justify-content-between">
            <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Continuar Comprando
            </a>
            <a href="#" class="btn btn-success">
                <i class="bi bi-credit-card"></i> Finalizar Compra
            </a>
        </div>
    @endif
</div>
@endsection