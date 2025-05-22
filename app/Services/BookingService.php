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

// Dans BookingService.php
public function getUserBookings($userId)
{
    return Booking::with(['service', 'provider'])
        ->where('user_id', $userId)
        ->orderByDesc('appointment_date')
        ->get();
}

// Dans BookingController.php
public function myBookings(): JsonResponse
{
    try {
        $userId = auth()->id();
        $bookings = $this->bookingService->getUserBookings($userId);
        
        // Retourner directement la collection de ressources
        return response()->json(BookingResource::collection($bookings), 200);
    } catch (\Throwable $e) {
        \Log::error('Erreur bookings/mine', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return response()->json([
            'message' => 'Erreur lors de la récupération des réservations de l\'utilisateur.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


}
