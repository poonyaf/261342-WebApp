<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
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
    ]);
    $orderItems = collect($validated['products'])->map(
            fn($item) => [
                'product_id'        => $item['product_id'],
                'quantity'          => $item['quantity'],
                'price_at_purchase' => $products[$item['product_id']]->price,
            ]
        )->toArray();

        $order->items()->createMany($orderItems);
// Clear user's cart after order creation
        Auth::user()->cart()->first()?->items()->delete();
    return redirect()->route('orders.show', $order->order_id)
                     ->with('success', 'Order created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order =Auth::user()->orders()->with('items.product')->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /** 
     * Mark order as packing
     */
    public function markAsPacking(string $id)
    {
        $order = Order::where('order_id', $id)->firstOrFail();
        
        if ($order->status !== 'processing') {
            return redirect()->route('orders.index')
                           ->with('warning', 'Order must be in processing status to pack.');
        }

        $order->markAsPacking();
        return redirect()->route('orders.index')->with('success', 'Order marked as packing.');
    }

    /**
     * Update the specified resource in storage.
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