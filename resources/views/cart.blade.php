@extends('layouts.app')
@section('content')
<header class="mb-4">
    <p class="text-uppercase text-success small fw-bold mb-1">Order summary</p>
    <h1 class="h3 mb-1">Your cart and checkout</h1>
    <p class="text-muted mb-0">Review your items, choose a payment method, and submit your order.</p>
</header>
@if(session('cart'))
    @php $total = 0; @endphp
    <section class="card border-0 shadow-sm mb-4" aria-labelledby="cart-items-heading">
        <div class="card-body p-0">
            <h2 id="cart-items-heading" class="visually-hidden">Cart items</h2>
            @foreach(session('cart') as $id => $item)
                @php $total += $item['price'] * $item['quantity']; @endphp
                <article class="p-3 p-lg-4 border-bottom">
                    <div class="row align-items-center g-3">
                        <div class="col-md">
                            <h3 class="h6 mb-1">{{ $item['name'] }}</h3>
                            <p class="text-muted small mb-0">₱{{ number_format($item['price'], 2) }} each</p>
                        </div>
                        <div class="col-md-auto">
                            <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex gap-2 align-items-center">
                                @csrf
                                @method('PUT')
                                <label for="quantity-{{ $id }}" class="small text-muted">Quantity</label>
                                <input id="quantity-{{ $id }}" type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm" style="max-width: 90px;" required>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                        </div>
                        <div class="col-md-auto text-md-end">
                            <strong>₱{{ number_format($item['price'] * $item['quantity'], 2) }}</strong>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="row justify-content-end">
        <div class="col-md-6 col-lg-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Order total</span>
                <strong class="h4 mb-0">₱{{ number_format($total, 2) }}</strong>
            </div>
            <form action="{{ route('checkout') }}" method="POST">
            @csrf
            <label for="payment-method" class="form-label fw-semibold">Payment method</label>
            <select id="payment-method" name="payment_method" class="form-select mb-3" required>
                <option value="">-- Select Payment Method --</option>
                <option value="Cash at Cashier">Cash at PKM Cashier</option>
                <option value="GCash">GCash (Online Payment Demo)</option>
            </select>
            <button class="btn btn-primary btn-lg w-100" type="submit">Submit payment and order</button>
            </form>
        </div>
    </section>
@else
    <div class="alert alert-warning">Cart is empty.</div>
@endif
@endsection