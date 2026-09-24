<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKM System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .pkm-bg { background-color: #18558d; }
        .product-card { transition: transform 0.18s ease, box-shadow 0.18s ease; }
        .product-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .login-page { min-height: 100vh; background: #0b1f3a; font-family: 'Manrope', sans-serif; }
        .login-page .login-main { min-height: 100vh; }
        .login-page .login-content { min-height: 100vh; }
        .login-page .login-visual { position: relative; overflow: hidden; background: linear-gradient(90deg, rgba(7, 27, 58, .88), rgba(20, 71, 125, .2)), url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSmIR_M9gfTdXklMvf2K664Vy6LZu2caM75PR5A1FPuqg&s=10') center / cover; }
        .login-page .login-visual::after { content: ''; position: absolute; inset: 0; background: linear-gradient(180deg, rgba(17, 61, 112, .08), rgba(5, 24, 54, .62)); pointer-events: none; }
        .login-page .login-visual > div { position: relative; z-index: 1; max-width: 560px; }
        .login-page .login-visual h1,
        .login-page .login-panel h2 { font-family: 'DM Serif Display', Georgia, serif; letter-spacing: 0; }
        .login-page .login-visual h1 { line-height: 1.05; }
        .login-page .login-panel { background: #edf5ff; color: #102b4e; }
        .login-page .login-panel .form-control { border: 1px solid #a9bdd5; background: #fafdff; color: #102b4e; border-radius: 4px; }
        .login-page .login-panel .form-control:focus { border-color: #2f6fae; box-shadow: 0 0 0 .2rem rgba(47, 111, 174, .18); }
        .login-page .login-panel .btn-success { background: #18558d; border-color: #18558d; border-radius: 4px; font-weight: 700; }
        .login-page .login-panel .btn-success:hover { background: #0f3d6b; border-color: #0f3d6b; }
        .login-page .login-panel a { color: #18558d !important; }
        .ordering-page { --order-blue: #18558d; --order-blue-dark: #0f3d6b; --order-blue-soft: #dcecff; }
        .ordering-page .pkm-bg { background-color: var(--order-blue-dark); }
        .ordering-page .bg-success { background-color: var(--order-blue) !important; }
        .ordering-page .btn-success { background-color: var(--order-blue); border-color: var(--order-blue); }
        .ordering-page .btn-success:hover { background-color: var(--order-blue-dark); border-color: var(--order-blue-dark); }
        .ordering-page .text-success { color: var(--order-blue) !important; }
        .ordering-page .table-success { --bs-table-bg: var(--order-blue-soft); --bs-table-color: #102b4e; }
        .ordering-page .navbar { position: sticky; top: 0; z-index: 1000; min-height: 82px; }
        .ordering-page .navbar-brand { font-size: 1.25rem; letter-spacing: .01em; }
        .ordering-page .navbar-nav .nav-link { padding-top: .8rem; padding-bottom: .8rem; }
        @media (max-width: 767.98px) {
            .ordering-page .navbar { min-height: 70px; }
            .ordering-page .navbar-brand { font-size: 1.05rem; }
        }
        @media (max-width: 767.98px) {
            .login-page .login-visual { min-height: 34vh; }
            .login-page .login-panel { min-height: 66vh; }
        }
    </style>
</head>
<body class="{{ request()->routeIs('login') ? 'login-page' : (request()->routeIs('shop', 'cart', 'cart.add', 'checkout') ? 'ordering-page bg-light' : 'bg-light') }}">

@if(!request()->routeIs('login'))
<nav class="navbar navbar-expand-lg navbar-dark pkm-bg shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('shop') }}">Pambayang Kolehiyo ng Mauban</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                @if(!request()->routeIs('register'))
                    <li class="nav-item"><a class="nav-link" href="{{ route('shop') }}">Shop Products</a></li>
                @endif
                @auth
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item"><a class="nav-link text-warning fw-bold" href="/admin/dashboard">Admin Panel</a></li>
                    @endif
                    <li class="nav-item"><a class="nav-link" href="/cart">Cart ({{ count((array) session('cart')) }})</a></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf <button class="btn btn-link nav-link">Logout</button>
                        </form>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="/register">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
@endif

<div class="{{ request()->routeIs('login') ? 'login-main' : 'container mt-4 mb-5' }}">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @yield('content')
</div>

@if(!request()->routeIs('login'))
<footer class="text-center p-3 bg-white border-top mt-5">
    <small class="text-muted">© 2026 PKM Research Project - Product Ordering & Inventory System.</small>
</footer>
@endif
</body>
</html>