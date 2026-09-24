@extends('layouts.app')
@section('content')
<header class="p-4 p-lg-5 mb-5 rounded-3 text-white" style="background: linear-gradient(135deg, #0f3d6b, #2f6fae);">
    <p class="text-uppercase small fw-bold mb-2 opacity-75">PKM campus store</p>
    <h1 class="h2 fw-bold mb-2">Hello, {{ auth()->user()->name }}.</h1>
    <p class="mb-0 opacity-75">Find the supplies you need and send your order to the PKM office in a few clicks.</p>
</header>

<main>
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <p class="text-uppercase text-success small fw-bold mb-1">Shop</p>
            <h2 class="h3 mb-1">Available school products</h2>
            <p class="text-muted mb-0">Browse essentials currently in stock.</p>
        </div>
        <span class="text-muted small">{{ $products->count() }} item(s)</span>
    </div>

    <div class="row g-4">
    @foreach($products as $product)
        <div class="col-md-4 col-xl-3">
            <div class="card shadow-sm border-0 h-100 product-card">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge {{ $product->status === 'Low Stock' ? 'bg-warning text-dark' : 'bg-success' }} rounded-pill">
                            {{ $product->status }}
                        </span>
                        <span class="text-muted small">Stock: {{ $product->stock_quantity }}</span>
                    </div>

                    <h5 class="fw-bold mb-2">{{ $product->product_name }}</h5>
                    <p class="text-muted small mb-3">{{ $product->description ?: 'School supply item for everyday use.' }}</p>
                    <div class="mt-auto">
                        <h4 class="text-success mb-3">₱{{ number_format($product->price, 2) }}</h4>

                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                                <button class="btn btn-success" type="submit">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    </div>
</main>
@endsection