<?php

namespace App\Services;

use App\Models\Animal;
use Illuminate\Support\Facades\Storage;

class AnimalService
{
    /**
     * Récupérer tous les animaux d'un utilisateur authentifié.
     */
    public function getAllAnimals()
    {
        return auth()->user()->animals;
    }

    /**
     * Créer un nouvel animal.
     */
    public function createAnimal(array $data)
    {
        if (isset($data['photo'])) {
            $data['photo'] = $data['photo']->store('animals', 'public');
        }
        $data['user_id'] = auth()->id();

        return Animal::create($data);
    }

    /**
     * Récupérer un animal par son ID pour l'utilisateur authentifié.
     */
    public function getAnimalById($id)
    {
        return auth()->user()->animals()->find($id);
    }

    /**
     * Mettre à jour un animal existant.
     */
    public function updateAnimal($id, array $data)
    {
        $animal = $this->getAnimalById($id);

        if (!$animal) {
            return null;
        }

        if (isset($data['photo'])) {
            if ($animal->photo) {
                Storage::disk('public')->delete($animal->photo);
            }
            $data['photo'] = $data['photo']->store('animals', 'public');
        }

        $animal->update($data);

        return $animal;
    }

    /**
     * Supprimer un animal.
     */
    public function deleteAnimal($id)
    {
        $animal = $this->getAnimalById($id);

        if (!$animal) {
            return false;
        }

        if ($animal->photo) {
            Storage::disk('public')->delete($animal->photo);
        }

        $animal->delete();

        return true;
    }

    /**
     * Associer un collier à un animal.
     */
    public function attachCollar($id, $collarId)
    {
        $animal = $this->getAnimalById($id);

        if (!$animal) {
            return null;
        }

        $animal->collar_id = $collarId;
        $animal->save();

        return $animal;
    }

    /**
     * Marquer un animal comme "Perdu".
     */
    public function markAsLost($id)
    {
        $animal = $this->getAnimalById($id);

        if (!$animal) {
            return null;
        }

        $animal->status = 'lost';
        $animal->save();

        return $animal;
    }

    /**
     * Marquer un animal comme "Retrouvé".
     */
    public function markAsFound($id)
    {
        $animal = $this->getAnimalById($id);

        if (!$animal) {
            return null;
        }

        $animal->status = 'active';
        $animal->save();

        return $animal;
    }

    /**
     * Scanner un collier et récupérer l'animal associé.
     */
    public function scanCollar($collarId)
    {
        return Animal::where('collar_id', $collarId)->first();
    }
}
