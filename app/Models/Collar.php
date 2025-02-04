<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collar extends Model
{
    use HasFactory;

    protected $fillable = ['nfc_id', 'qr_code_url', 'animal_id'];

    // Relation avec Animal
    public function animal()
    {
        return $this->belongsTo(Animal::class);
    }
}
