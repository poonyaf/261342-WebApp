<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $orders = Auth::user()
        ->orders()
        ->with('items.product')
        ->latest('order_date') 
        ->get();

    return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('orders.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,product_id',
        'products.*.quantity'  => 'required|integer|min:1',
        //collect data for order details from user input, such as name, address, phone number, payment method, etc. (for order confirmation page)
        'name'             => 'required|string',
        'address'          => 'required|string',
        'phone'            => 'required|string|max:10',
        'payment_method'   => 'required|in:promptpay,credit_card,bank_transfer,cash_on_delivery',
        ]);
        // Fetch products and calculate total price
    $productIds = collect($validated['products'])->pluck('product_id');
    $products   = Product::findMany($productIds)->keyBy('product_id');
     $total = collect($validated['products'])->sum(
        fn($item) => $products[$item['product_id']]->price * $item['quantity']
    );
    // Create order and attach products
    $order = Auth::user()->orders()->create([
        'status'      => 'pending',
        'total_amount' => $total,
        'order_date'   => now(),
        'address'      => $validated['address'],
    ]);
    $orderItems = collect($validated['products'])->map(
            fn($item) => [
                'product_id'        => $item['product_id'],
                'quantity'          => $item['quantity'],
                'price_at_purchase' => $products[$item['product_id']]->price,
            ]
        )->toArray();

        $order->items()->createMany($orderItems);
        $order->payments()->create([
            'status'       => 'unpaid',
            'method'       => $validated['payment_method'],
            'amount'       => $total,
            'payment_date' => now(), // Payment date will be set when payment is completed
        ]);
        // Clear user's cart after order creation
        Auth::user()->cart()->first()?->items()->delete();
    return redirect()->route('payments.create', $order->order_id)
                 ->with('success', 'Order created successfully');
    }

        public function show(string $id)
{
    $order = Auth::user()
        ->orders()
        ->with('items.product')
        ->findOrFail($id);

    return view('orders.show', compact('order'));
}
// For "Buy Now" functionality, we can create a separate method that creates an order directly from a single product without going through the cart.
public function edit(string $id)
{
    $order = Auth::user()
        ->orders()
        ->with('items.product')
        ->findOrFail($id);

    $products = Product::all();

    return view('orders.edit', compact('order', 'products'));
}
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
{
    $order = Auth::user()->orders()->findOrFail($id);
    
    $validated = $request->validate([
        'products' => 'required|array|min:1',
        'products.*.product_id' => 'required|exists:products,product_id',
        'products.*.quantity'  => 'required|integer|min:1',
    ]);

    return DB::transaction(function () use ($validated, $order) {
        $productIds = collect($validated['products'])->pluck('product_id');
        $products = Product::findMany($productIds)->keyBy('product_id');

        $syncData = collect($validated['products'])->mapWithKeys(fn($item) => [
            $item['product_id'] => [
                'quantity' => $item['quantity'],
                'price_at_purchase' => $products[$item['product_id']]->price,
            ]
        ])->toArray();

        $order->products()->sync($syncData);

        // update total amount
        $total = collect($validated['products'])->sum(
            fn($item) => $products[$item['product_id']]->price * $item['quantity']
        );
        $order->update(['total_amount' => $total]);
        // Prevent updates to paid orders
        if ($order->isPaid()) {
        return redirect()->back()
                       ->with('error', 'Cannot update order that has already been paid.');
    }

        return redirect()->route('orders.show', $order->order_id)
                         ->with('success', 'Order updated successfully');
    });
}
// For "Buy Now" functionality, we can create a separate method that creates an order directly from a single product without going through the cart.
public function storeNow(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,product_id',
        'quantity'   => 'required|integer|min:1',
    ]);

    
    return redirect()->route('orders.confirm', [ 'product_id' => $request->product_id,
        'quantity'   => $request->quantity,]);
}
//need confirm order page before store order, to show order summary and confirm before place order
public function confirm(Request $request) 
{
    if ($request->has('product_id')) {
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        return view('orders.confirm', [
            'items'      => collect([['product' => $product, 'quantity' => $quantity, 'product_id' => $product->product_id]]),
            'is_buy_now' => true,
            'product_id' => $product->product_id,
            'quantity'   => $quantity,
        ]);
    }

    $cart = Auth::user()->cart()->with('items.product')->first();

    if (!$cart || $cart->items->isEmpty()) {
        return redirect()->route('carts.index')->with('error', 'Cart is empty.');
    }

    return view('orders.confirm', compact('cart'));
}

    //status pending(default) -> processing -> packing -> delivering -> complete
     /**
     * Mark order as processing (payment initiated)
     */
    public function markAsProcessing(string $id)
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        if ($order->status !== 'pending') {
            return redirect()->route('orders.index')
                           ->with('warning', 'Only pending orders can be marked as processing.');
        }
        $order->markAsProcessing();
        return redirect()->route('orders.show', $order->order_id)
                         ->with('success', 'Order marked as processing successfully.');
    }
    /** 
     * Mark order as packing
     */
    public function markAsDelivering(string $id) //according to the order flow (order status), only orders in packing status can be marked as delivering
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        if ($order->status !== 'packing') {
            return redirect()->back()->with('error', 'Only orders in packing status can be marked as delivering.');
        }
        $order->markAsDelivering();
        return redirect()->route('orders.show', $order->order_id)
                         ->with('success', 'Order marked as delivering successfully.');
    }
     /**
     * Update the specified resource in storage.
     */
    
    /**
     * Mark order as complete
     */
    public function markAsComplete(string $id)
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        
        if ($order->status !== 'delivering') {
            return redirect()->route('orders.index')
                           ->with('warning', 'Order must be in delivering status to complete.');
        }

        $order->markAsComplete();
        return redirect()->route('orders.index')->with('success', 'Order marked as complete.');
    }
    // Mark order as paid
    public function markAsPaid(string $id)
{
    $order = Auth::user()->orders()->findOrFail($id);

     // update old  payment record instead of creating new one.
    $payment = $order->payments()->where('status', 'unpaid')->first();
    
    if ($payment) {
        $payment->update([
            'status'       => 'paid',
            'method'       => 'manual',
            'payment_date' => now(),
        ]);
    } else {
        $order->payments()->create([
            'status'       => 'paid',
            'method'       => 'manual',
            'amount'       => $order->total_amount,
            'payment_date' => now(),
        ]);
    }

    $order->markAsProcessing();

    return redirect()->route('orders.show', $order->order_id)
                     ->with('success', 'Payment confirmed!');
}

public function cancel(string $id)
{
    $order = Auth::user()->orders()->findOrFail($id);

    if ($order->status !== 'pending') {
        return redirect()->back()
                       ->with('error', 'Only pending orders can be cancelled.');
    }

    $order->markAsCancelled();
    //update payment status to cancelled if order is cancelled
    $order->payments()->where('status', 'unpaid')->update(['status' => 'cancelled']);
    //update order's payment status to cancelled as well
     Order::where('order_id', $order->order_id)->update(['payment_status' => 'cancelled']);
    return redirect()->route('orders.index')
                     ->with('success', 'Order cancelled successfully.');
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }


}
