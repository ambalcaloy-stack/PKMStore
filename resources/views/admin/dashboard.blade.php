@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Admin Panel</h2>
        <p class="text-muted mb-0">Monitor inventory, update stock, and manage product activity.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="#inventory-monitor" class="btn btn-outline-primary">Inventory Monitor</a>
        <a href="#product-form" class="btn btn-success">+ Add Product</a>
    </div>
</div>

<div class="row mb-4 g-3">
    <div class="col-md-4">
        <div class="card bg-primary text-white shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Orders</h6>
                        <h2 class="mb-0">{{ $totalOrders }}</h2>
                    </div>
                    <i class="bi bi-bag-check fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-2">Sales</h6>
                        <h2 class="mb-0">₱{{ number_format($totalSales, 2) }}</h2>
                    </div>
                    <i class="bi bi-cash-coin fs-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-dark-50 mb-2">Low Stock</h6>
                        <h2 class="mb-0">{{ $lowStock->count() }}</h2>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-1 text-dark-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white fw-bold">Recent Student Orders</div>
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->user?->name ?? 'Unknown' }}</td>
                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @if($order->order_status === 'Paid' && $order->payment?->payment_method === 'GCash')
                                    <span class="badge bg-success">Paid Online</span>
                                @elseif($order->order_status === 'Paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-warning text-dark">{{ $order->order_status }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-danger shadow-sm h-100">
            <div class="card-header bg-danger text-white fw-bold">⚠️ Low Stock Alerts</div>
            <ul class="list-group list-group-flush">
                @forelse($lowStock as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $item->product_name }}
                        <span class="badge bg-danger rounded-pill">{{ $item->stock_quantity }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-success">All stocks are sufficient.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7" id="inventory-monitor">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white fw-bold d-flex justify-content-between align-items-center">
                <span>Inventory Monitoring</span>
                <span class="badge bg-light text-dark">{{ $products->count() }} items</span>
            </div>
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->stock_quantity }}</td>
                            <td>
                                <span class="badge {{ $product->status === 'Out of Stock' ? 'bg-danger' : ($product->status === 'Low Stock' ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ $product->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end align-items-center gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.products.delete', $product) }}" method="POST" onsubmit="return confirm('Delete this product?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </div>
                                <form action="{{ route('admin.products.stock', $product) }}" method="POST" class="mt-2 d-flex gap-2 justify-content-end">
                                    @csrf
                                    <input type="number" name="stock_quantity" class="form-control form-control-sm" value="{{ $product->stock_quantity }}" min="0" style="max-width: 90px;">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No products yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-lg-5" id="product-form">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white fw-bold">Add New Product</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="product_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control"></textarea>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock_quantity" class="form-control" min="0" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mt-3">Save Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection