<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Sistema de Produtos</title>
    <link href="{{ asset('css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-box-seam"></i> Sistema de Produtos
            </a>
        </div>
    </nav>
    <!-- Navbar -->

    <div class="container-fluid p-0">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}" href="{{ route('products.index') }}">
                                <i class="bi bi-box"></i> Produtos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link">
                                <i class="bi bi-clipboard-data"></i> Estoque
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link position-relative {{ request()->routeIs('cart.index') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                                <i class="bi bi-cart"></i> Carrinho
                                <span class="cart-counter position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                                    style="{{ count(Session::get('cart', [])) ? '' : 'display: none;' }}">
                                    {{ count(Session::get('cart', [])) }}
                                </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Sidebar -->

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                @include('components.alerts')
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>