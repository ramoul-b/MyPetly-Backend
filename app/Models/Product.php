<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'product_category_id',
        'store_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'status',
    ];

    public $translatable = ['name', 'description'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }
}

