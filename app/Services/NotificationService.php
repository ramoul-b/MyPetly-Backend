<?php

namespace App\Services;

use App\Models\Animal;

class NotificationService
{
    public static function notifyOwnerOfScan(Animal $animal)
    {
        $owner = $animal->user;

        // Exemple d'envoi d'e-mail (ajustez selon votre système)
        Mail::to($owner->email)->send(new AnimalLostScanNotification($animal));

        // Vous pouvez ajouter ici des notifications push ou SMS
    }
}
