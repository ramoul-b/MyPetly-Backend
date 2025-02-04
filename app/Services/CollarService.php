<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CollarService
{
    public function generateQrCode($collarId)
    {
        $url = route('collar.scan', ['collarId' => $collarId]);

        // Générer le QR Code
        $qrCodePath = 'qrcodes/' . $collarId . '.png';
        QrCode::format('png')
            ->size(300)
            ->generate($url, storage_path('app/public/' . $qrCodePath));

        return asset('storage/' . $qrCodePath);
    }
}
