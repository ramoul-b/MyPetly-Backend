<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Animal;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sex', 'weight', 'height', 'identification_number', 'color', 'species', 'breed', 'birthdate', 'photo', 'status', 'user_id'];

    // Relation avec User (Propriétaire)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec Collar
    public function collar()
    {
        return $this->hasOne(Collar::class);
    }

    public function animals()
{
    return $this->hasMany(Animal::class);
}

}

