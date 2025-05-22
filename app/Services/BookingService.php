<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingService
{
    /**
     * Crée une nouvelle réservation
     *
     * @param array $data
     * @return Booking
     */
    public function create(array $data): Booking
    {
        return Booking::create([
            'user_id' => Auth::id(),
            'service_id' => $data['service_id'],
            'provider_id' => $data['provider_id'],
            'appointment_date' => $data['appointment_date'],
            'time' => $data['time'],
            'payment_intent' => $data['payment_intent'] ?? null,
            'currency' => $data['currency'] ?? 'eur',
            'status' => 'pending'
        ]);
    }

    public function createConfirmed(array $data): Booking
    {
        // Vérifier si le créneau est déjà réservé
        $exists = Booking::where('provider_id', $data['provider_id'])
            ->whereDate('appointment_date', $data['appointment_date'])
            ->where('time', $data['time'])
            ->exists();

        if ($exists) {
            throw new \Exception('Créneau déjà réservé');
        }

        return Booking::create([
            'user_id' => auth()->id(),
            'service_id' => $data['service_id'],
            'provider_id' => $data['provider_id'],
            'appointment_date' => $data['appointment_date'],
            'time' => $data['time'],
            'payment_intent' => $data['payment_intent'] ?? null,
            'currency' => $data['currency'] ?? 'eur',
            'status' => 'confirmed',
            'notes' => $data['notes'] ?? null,
        ]);
    }

public function getUserBookings(int $userId)
{
    \Log::info('🔁 [BookingService] getUserBookings lancé', ['user_id' => $userId]);

    $bookings = Booking::with(['service', 'provider'])
        ->whereNotNull('id') // sécurité supplémentaire
        ->where('user_id', $userId)
        ->latest('appointment_date')
        ->get();

    \Log::info('✅ [BookingService] Bookings récupérés :', ['count' => $bookings->count(), 'ids' => $bookings->pluck('id')]);

    return $bookings;
}





}
