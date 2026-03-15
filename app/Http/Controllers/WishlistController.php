<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())->with('product')->get();    
        return view('wishlists.index', compact('wishlists'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
        ]);

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $request->input('product_id'),
        ]);

        return redirect()->back()->with('success', 'Product added to wishlist!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Wishlist $wishlist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wishlist $wishlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        //
    }

    //when user click the wishlist button, it will toggle the wishlist status of the product
    public function toggle(Request $request)
{
    $request->validate(['product_id' => 'required|exists:products,product_id']);

    $wishlist = Wishlist::where('user_id', Auth::id())
        ->where('product_id', $request->product_id)
        ->first();

    if ($wishlist) {
        $wishlist->delete();
        return response()->json(['wishlisted' => false]);
    } else {
        Wishlist::create([
            'user_id'    => Auth::id(),
            'product_id' => $request->product_id,
        ]);
        return response()->json(['wishlisted' => true]);
    }
}
    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy(Wishlist $wishlist)
    {
        $wishlist = Wishlist::where('wishlist_id', $wishlist->wishlist_id)
         ->where('user_id', Auth::id())
         ->firstOrFail(); // Will throw a ModelNotFoundException if the entry doesn't exist or doesn't belong to the user

        $wishlist->delete();
        return redirect()->back()->with('success', 'Product removed from wishlist!');
    }
}
