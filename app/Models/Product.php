<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; 

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 
        'product_group_id',
        'name', 
        'slug', 
        'description', 
        'price', 
        'shipping_price',
        'stock',          
        'color',
        'colorRGB', 
        'is_main',        
        'image',
        'size'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
    
    public function isFavorited(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->wishlists()->where('user_id', auth()->id())->exists();
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function group()
    {
        return $this->belongsTo(ProductGroup::class, 'product_group_id');
    }

    public function variants()
    {
        return $this->hasMany(Product::class, 'product_group_id', 'product_group_id');
    }

   public function comments()
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }
}