<?php

// app/Models/StoreCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class StoreCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'store_id',
        'name',
        'slug',
        'parent_id',
    ];

    public $translatable = ['name'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
