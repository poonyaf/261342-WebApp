<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SellerForm;
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
        return view('admin.index', compact('totalUsers', 'totalProducts', 'totalOrders', 'totalRevenue'));
    }

    public function users(Request $request)
    {
        $users = User::when($request->search, fn($q) => 
            $q->where('name', 'like', "%{$request->search}%")
              ->orWhere('email', 'like', "%{$request->search}%")
        )->latest()->get();

        $pendingRequests = \App\Models\SellerForm::with('user')->where('status', 'pending')->latest()->get();

        return view('admin.users', compact('users', 'pendingRequests'));
    }

    public function orders(Request $request)
    {
        $orders = Order::with('user', 'items.product')
            ->when($request->search, fn($q) => 
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$request->search}%"))
                  ->orWhere('order_id', 'like', "%{$request->search}%")
            )->latest('order_date')->get();
        
        return view('admin.orders', compact('orders'));
    }

    public function products(Request $request)
    {
        $products = Product::when($request->search, fn($q) => 
            $q->where('name', 'like', "%{$request->search}%")
        )->latest()->get();
        return view('admin.products', compact('products'));
    }

    public function markAsPacking(string $id)
    {
        $order = Order::with('items.product.sellers')->findOrFail($id);
        
        $hasSeller = $order->items->some(fn($item) => $item->product->sellers->isNotEmpty());
        
        if ($hasSeller) {
            return back()->with('error', 'This order contains seller products. Seller must handle packing.');
        }
        
        $order->markAsPacking();
        return redirect()->back()->with('success', 'Order marked as packing.');
    }

    public function markAsDelivering(string $id)
    {
        $order = Order::with('items.product.sellers')->findOrFail($id);
        
        $hasSeller = $order->items->some(fn($item) => $item->product->sellers->isNotEmpty());
        
        if ($hasSeller) {
            return back()->with('error', 'This order contains seller products. Seller must handle delivery.');
        }
        
        $order->markAsDelivering();
        return redirect()->back()->with('success', 'Order marked as delivering.');
    }

    public function markAsComplete(string $id)
    {
        Order::findOrFail($id)->markAsComplete();
        return redirect()->back()->with('success', 'Order marked as complete.');
    }

    public function destroyUser(string $id)
    {
        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function destroyProduct(string $id)
    {
        Product::where('product_id', $id)->firstOrFail()->delete();
        return redirect()->back()->with('success', 'Product deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('items.product.sellers')->findOrFail($id);
        $newStatus = strtolower($request->status);
        $currentStatus = strtolower($order->status);

        if (strtolower($order->status) === 'pending' && $newStatus !== 'cancelled') {
            if ($order->payment_status !== 'paid') {
                return back()->with('error', 'cant confirm the order if did not pay');
            }
        }

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

        if ($newStatus !== 'cancelled' && $newRank <= $currentRank) {
            return back()->with('error', 'Cannot move status backwards.');
        }

        $hasSeller = $order->items->some(fn($item) => $item->product->sellers->isNotEmpty());

        if ($hasSeller && in_array($newStatus, ['packing', 'delivering'])) {
            return back()->with('error', 'This order contains seller products. Seller must handle packing and delivery.');
        }

        return \DB::transaction(function () use ($order, $newStatus) {
            if ($newStatus === 'cancelled') {
                if (strtolower($order->payment_status) === 'paid') {
                    $order->payment_status = 'refunded';
                    $order->payments()->where('status', 'paid')->update(['status' => 'refunded']);
                } else {
                    $order->payment_status = 'cancelled';
                }

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

        $roles = ['customer', 'seller', 'admin'];
        $currentIndex = array_search($user->role, $roles);
        $user->role = $roles[($currentIndex + 1) % count($roles)];
        $user->save();

        return back()->with('success', "Updated {$user->name}'s role to {$user->role} successfully.");
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->orders()->count() > 0) {
            return back()->with('error', 'Cannot delete user with order history.');
        }

        $user->delete();
        return back()->with('success', 'User removed successfully.');
    }

    public function approveSeller($id)
    {
        return DB::transaction(function () use ($id) {
            $request = SellerForm::findOrFail($id);
            
            $request->status = 'approved';
            $request->save();

            $user = $request->user;
            $user->role = 'seller';
            $user->save(); 

            return back()->with('success', "Approved seller request for {$user->name}!");
        });
    }

    public function rejectSeller($id)
    {
        $request = SellerForm::findOrFail($id);
        $request->update(['status' => 'rejected']);

        return back()->with('sorry', "Rejected request for {$request->user->name}! please try again later or contact support for more info.");
    }

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