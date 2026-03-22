<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Support\Facades\Storage; // import storage facade for handling file uploads
use App\Models\Wishlist;
use App\Models\RecentView;

use Illuminate\Support\Facades\Auth; 
use Cloudinary\Cloudinary as CloudinaryClient;
use Cloudinary\Configuration\Configuration;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // search and filter products by category (tag)
        $search = $request->input('search');
        $category = $request->input('category');
        // Get the mode from the request, default to 'Online' if not provided
        $mode = $request->input('mode', 'Online');

        $query = Product::query();
        $query = Product::with('tags');

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }
       if ($category) {
    $query->whereHas('tags', function ($q) use ($category) {
        $q->where('name', $category);
    });
}

$secondhandTags = ['Secondhand/2nd hand', 'vintage', '90s'];

if ($mode === 'Secondhand/2nd hand') {
    $query->whereHas('tags', function ($q) use ($secondhandTags) {
        $q->whereIn('name', $secondhandTags);
    });
} else {
    $query->whereDoesntHave('tags', function ($q) use ($secondhandTags) {
        $q->whereIn('name', $secondhandTags);
    });
}

$products = $query->get();

$categories = Tag::whereHas('products', function ($q) use ($mode, $secondhandTags) {
    $q->where('taggable_type', 'App\\Models\\Product');
    if ($mode === 'Secondhand/2nd hand') {
        $q->whereHas('tags', function ($t) use ($secondhandTags) {
            $t->whereIn('name', $secondhandTags);
        });
    } else {
        $q->whereDoesntHave('tags', function ($t) use ($secondhandTags) {
            $t->whereIn('name', $secondhandTags);
        });
    }
})->whereNotIn('name', $mode === 'Secondhand/2nd hand' ? [] : $secondhandTags)
->pluck('name')->unique();

        return view('products.index', compact('products', 'search', 'category', 'categories', 'mode'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //pull all tags from database
        $tags = Tag::all();
        return view('products.create',compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_number' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
       if ($request->hasFile('image')) {
    $cloudinary = new CloudinaryClient(env('CLOUDINARY_URL'));
    $result = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath());
    $validatedData['image'] = $result['secure_url'];
}

       $product = Product::create($validatedData);
if ($request->has('tags')) {
    $product->tags()->sync($request->tags);
}
        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //can not use findOrFail($id) because the primary key is not id
       $product = Product::with('tags')
                ->where('product_id', $id)
                ->firstOrFail();
        
        
        //check if the product is in the user's wishlist
        $inWishlist = Auth::check()? $product->wishlists()->where('user_id', Auth::id())->exists() : false;
    return view('products.show', compact('product', 'inWishlist'));
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id) //adding edit product method
    {
        $product = Product::where('product_id', $id)->firstOrFail();
        $tags = Tag::all();
        return view('products.edit', compact('product','tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) //adding update product method
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_number' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
       if ($request->hasFile('image')) {
    $cloudinary = new CloudinaryClient(env('CLOUDINARY_URL'));
    $result = $cloudinary->uploadApi()->upload($request->file('image')->getRealPath());
    $validatedData['image'] = $result['secure_url'];
}

        $product->update($validatedData);
        if ($request->has('tags')) {
    $product->tags()->sync($request->tags);
} else {
    $product->tags()->detach();
}

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::where('product_id', $id)->firstOrFail();

        // Delete image if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
    //sellers can create product
    public function sellers()
{
    return $this->belongsToMany(User::class, 'seller_products', 'product_id', 'seller_id');
}
}
