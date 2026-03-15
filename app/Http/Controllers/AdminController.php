<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;   
class AdminController extends Controller
{
    public function index()
    {
        $totalUsers    = User::count();
        $totalProducts = Product::count();
        $totalOrders   = Order::count();
        $totalRevenue  = Payment::where('status', 'paid')->sum('amount');

        return view('admin.index', compact(
            'totalUsers',
            'totalProducts', 
            'totalOrders',
            'totalRevenue'
        ));
    }

    public function users()
    {
        $users = User::latest()->get();
        return view('admin.users', compact('users'));
    }

    public function orders()
    {
        $orders = Order::with('user', 'items.product')->latest('order_date')->get();
        
        return view('admin.orders', compact('orders'));
    }
    public function markAsPacking(string $id)
{
    Order::findOrFail($id)->markAsPacking();
    return redirect()->back()->with('success', 'Order marked as packing.');
}

public function markAsDelivering(string $id)
{
    Order::findOrFail($id)->markAsDelivering();
    return redirect()->back()->with('success', 'Order marked as delivering.');
}

public function markAsComplete(string $id)
{
    Order::findOrFail($id)->markAsComplete();
    return redirect()->back()->with('success', 'Order marked as complete.');
}
// ลบ user
public function destroyUser(string $id)
{
    User::findOrFail($id)->delete();
    return redirect()->back()->with('success', 'User deleted successfully.');
}

// ลบ product
public function destroyProduct(string $id)
{
    Product::where('product_id', $id)->firstOrFail()->delete();
    return redirect()->back()->with('success', 'Product deleted successfully.');
}

// show all products 
public function products()
{
    $products = Product::latest()->get();
    return view('admin.products', compact('products'));
}

public function updateStatus(Request $request, $id)
{
    $order = Order::with('items.product')->findOrFail($id);
    $newStatus = strtolower($request->status);
    $currentStatus = strtolower($order->status);
    //check payment bf confirm
    if (strtolower($order->status) === 'pending' && $newStatus !== 'cancelled') {
        if ($order->payment_status !== 'paid') {
            return back()->with('error', 'cant confirm the order if did not pay');
        }
    }
    // prevent to back to previous status
    $statusOrder = [
        'pending'    => 1,
        'processing' => 2,
        'packing'    => 3,
        'delivering' => 4,
        'complete'   => 5,
        'cancelled'  => 99 
    ];

    $currentRank = $statusOrder[strtolower($order->status)] ?? 0;
    $newRank     = $statusOrder[strtolower($newStatus)] ?? 0;

    // if not Cancel and try to back
    if ($newStatus !== 'cancelled' && $newRank <= $currentRank) {
        return back()->with('error', 'Cannot move status backwards.');
    }
    // 3. use Transaction for security of data
    return \DB::transaction(function () use ($order, $newStatus) {
        if ($newStatus === 'cancelled') {
            // Refund Logic
            if (strtolower($order->payment_status) === 'paid') {
                $order->payment_status = 'refunded';
                $order->payments()->where('status', 'paid')->update(['status' => 'refunded']);
            } else {
                $order->payment_status = 'cancelled';
            }

            // Restore Stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock_number', $item->quantity);
                }
            }
        }

        $order->status = $newStatus;
        $order->save();

        return back()->with('success', "Order updated to {$newStatus}.");
    });
}
public function updateRole($id)
{
    $user = User::findOrFail($id);

    
    if ($user->id === Auth::id()) {
        return back()->with('error', 'You cannot change your own role!');
    }

    
    $user->role = ($user->role === 'admin') ? 'customer' : 'admin';
    $user->save();

    return back()->with('success', "Updated {$user->name}'s role successfully.");
}

public function destroy($id)
{
    $user = User::findOrFail($id);

    // เช็คว่ามี Order ค้างอยู่ไหม ถ้ามีไม่แนะนำให้ลบ (หรือจะใช้ SoftDelete ก็ได้)
    if ($user->orders()->count() > 0) {
        return back()->with('error', 'Cannot delete user with order history.');
    }

    $user->delete();
    return back()->with('success', 'User removed successfully.');
}
//create new admin
public function storeAdmin(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8', 
    ]);

    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => \Hash::make($request->password),
        'role' => 'admin', 
    ]);

    return back()->with('success', 'Create new admin successfully!');
}
}

