<?php
namespace App\Http\Controllers;

use App\Models\{Product, Order, OrderItem, Payment, InventoryTransaction};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};

class SystemController extends Controller
{
    protected function syncProductStatus(Product $product): void
    {
        $product->status = $product->stock_quantity <= 0
            ? 'Out of Stock'
            : ($product->stock_quantity <= 10 ? 'Low Stock' : 'Available');

        $product->save();
    }

    // --- 1. STUDENT: SHOP & HOME ---
    public function index()
    {
        $products = Product::where('stock_quantity', '>', 0)
            ->orderBy('product_name')
            ->get();

        return view('home', compact('products'));
    }

    // --- 2. STUDENT: CART LOGIC ---
    public function viewCart()
    {
        $cart = session()->get('cart', []);
        return view('cart', compact('cart'));
    }

    public function addToCart(Request $request, $id)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($id);
        $quantity = (int) $request->quantity;
        $cart = session()->get('cart', []);

        if ($product->stock_quantity < $quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        if (isset($cart[$id])) {
            $newTotal = $cart[$id]['quantity'] + $quantity;

            if ($newTotal > $product->stock_quantity) {
                return back()->with('error', 'That exceeds the available stock.');
            }

            $cart[$id]['quantity'] = $newTotal;
        } else {
            $cart[$id] = [
                'name' => $product->product_name,
                'quantity' => $quantity,
                'price' => $product->price,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Added to cart successfully.');
    }

    public function updateCart(Request $request, $id)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = session()->get('cart', []);
        $product = Product::findOrFail($id);
        $quantity = (int) $request->quantity;

        if (!isset($cart[$id])) {
            return back()->with('error', 'That product is not in your cart.');
        }

        if ($quantity > $product->stock_quantity) {
            return back()->with('error', 'Only ' . $product->stock_quantity . ' items are available.');
        }

        $cart[$id]['quantity'] = $quantity;
        session()->put('cart', $cart);

        return back()->with('success', 'Cart quantity updated.');
    }

    // --- 3. STUDENT: CHECKOUT & INVENTORY DEDUCTION ---
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => ['required', 'string', 'in:Cash at PKM Cashier,GCash'],
        ]);

        $cart = session()->get('cart');
        if (empty($cart)) {
            return back()->with('error', 'Cart is empty.');
        }

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => 0,
                'order_status' => 'Pending',
            ]);

            $totalAmount = 0;

            foreach ($cart as $id => $details) {
                if (!isset($details['quantity']) || !is_numeric($details['quantity']) || (int) $details['quantity'] <= 0) {
                    throw new \Exception('One of the cart items has an invalid quantity.');
                }

                $product = Product::lockForUpdate()->find($id);
                if (!$product) {
                    throw new \Exception('One of the products is no longer available.');
                }

                $quantity = (int) $details['quantity'];
                if ($quantity > $product->stock_quantity) {
                    throw new \Exception('Not enough stock for ' . $product->product_name . '.');
                }

                $subtotal = $product->price * $quantity;
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $newStock = $product->stock_quantity - $quantity;
                InventoryTransaction::create([
                    'product_id' => $id,
                    'transaction_type' => 'Order Deduction',
                    'quantity' => $quantity,
                    'previous_stock' => $product->stock_quantity,
                    'new_stock' => $newStock,
                    'reference' => 'Order #' . $order->id,
                ]);

                $product->stock_quantity = $newStock;
                $this->syncProductStatus($product);
            }

            $order->update(['total_amount' => $totalAmount, 'order_status' => 'Paid']);

            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'amount' => $totalAmount,
                'reference_number' => 'PKM-' . strtoupper(uniqid()),
                'payment_status' => 'Paid',
                'payment_date' => now(),
            ]);

            DB::commit();
            session()->forget('cart');

            return redirect('/')->with('success', 'Order successful! Please claim your order at the PKM office.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // --- 4. ADMIN: DASHBOARD ---
    public function adminDashboard()
    {
        $totalOrders = Order::count();
        $totalSales = Payment::where('payment_status', 'Paid')->sum('amount');
        $lowStock = Product::where('stock_quantity', '<=', 10)->orderBy('stock_quantity', 'asc')->get();
        $recentOrders = Order::with(['user', 'payment'])->latest()->take(5)->get();
        $products = Product::orderBy('product_name')->get();

        return view('admin.dashboard', compact('totalOrders', 'totalSales', 'lowStock', 'recentOrders', 'products'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ]);

        $product = Product::create($validated);
        $this->syncProductStatus($product);

        return redirect('/admin/dashboard')->with('success', 'Product created successfully.');
    }

    public function editProduct(Product $product)
    {
        return view('admin.edit-product', compact('product'));
    }

    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ]);

        $product->fill($validated);
        $this->syncProductStatus($product);

        return redirect('/admin/dashboard')->with('success', 'Product updated successfully.');
    }

    public function destroyProduct(Product $product)
    {
        $product->delete();

        return redirect('/admin/dashboard')->with('success', 'Product deleted successfully.');
    }

    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ]);

        $product->stock_quantity = (int) $validated['stock_quantity'];
        $this->syncProductStatus($product);

        return redirect('/admin/dashboard')->with('success', 'Stock updated successfully.');
    }
}