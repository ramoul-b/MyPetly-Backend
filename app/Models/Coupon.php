<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Coupon extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'store_id',
        'product_id',
        'created_by',
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_order_total',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'is_active'            => 'boolean',
        'starts_at'            => 'datetime',
        'expires_at'           => 'datetime',
        'discount_value'       => 'decimal:2',
        'minimum_order_total'  => 'decimal:2',
    ];

    public array $translatable = ['name', 'description'];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
