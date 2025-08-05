<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Translatable\HasTranslations;

class ProductCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = ['name', 'description', 'icon', 'color'];

    public $translatable = ['name', 'description'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

