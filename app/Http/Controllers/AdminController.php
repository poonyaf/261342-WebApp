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

    public function users()
    {
        $users = User::latest()->get();

        $pendingRequests = \App\Models\SellerForm::with('user')->where('status', 'pending')->latest()->get();

        return view('admin.users', compact('users', 'pendingRequests'));
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

// update user role (admin <-> customer)
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

    
//create new admin from admin dashboard (optional)

//using Seller
public function approveSeller($id)
    {
        // use transaction for security of data (update 2 table at once -- seller_requests + users)
        return DB::transaction(function () use ($id) {
            $request = SellerForm::findOrFail($id);
            
            // 1. อัปเดตสถานะฟอร์มว่าผ่านการอนุมัติแล้ว
            $request->status = 'approved';
            $request->save();

            // 🌟 2. อัปเดต Role ของ User (แก้มาใช้แบบกำหนดค่าตรงๆ แล้วค่อย save)
            $user = $request->user;
            $user->role = 'seller';
            $user->save(); 

            return back()->with('success', "Approved seller request for {$user->name}!");
        });
    }

    // in case admin want to reject the request (optional)
    public function rejectSeller($id)
    {
        $request = SellerForm::findOrFail($id);
        $request->update(['status' => 'rejected']);

        return back()->with('sorry', "Rejected request for {$request->user->name}! please try again later or contact support for more info.");
    }

    // in case admin want to create new admin by himself (optional)
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