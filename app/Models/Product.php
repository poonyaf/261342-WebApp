<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tag;
use App\Models\CartItem;
class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock_number',
        'image',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_number' => 'integer',
    ];

    // Access cartItem
    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_id', 'product_id');
    }

    // Access carts
    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_items', 'product_id', 'cart_id')
            ->withPivot(['quantity'])
            ->withTimestamps();
    }

    // Access orderItem
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    // Access orders
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items', 'product_id', 'order_id')
            ->withPivot(['quantity', 'price_at_purchase'])
            ->withTimestamps();
    }

    public function isInStock($quantity = 1) //Check is stock is available for the requested quantity   
    {
        if ($this->stock_number < $quantity) {
            throw new \Exception('Product does not have enough stock. Requested: ' . $quantity . ', Available: ' . $this->stock_number);
        }
        return true;
    }

    // Remove from the stock when order is completed
    public function decrementStock($quantity = 1)
    {
        if ($this->stock_number < $quantity) { // check is stock enough or not
            throw new \Exception('Cannot decrement stock. Not enough product available. Requested: ' . $quantity . ', Available: ' . $this->stock_number);
        }
        $this->stock_number -= $quantity; // Decrement stock number if successful
        $this->save();
    }
    
    public function tags()
{
    return $this->morphToMany(Tag::class, 'taggable');
}
}
