<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{

    /**
 * @OA\Post(
 *     path="/stripe/webhook",
 *     tags={"Stripe"},
 *     summary="Webhook Stripe - traite les événements comme payment_intent.succeeded",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="string", example="evt_1N1KcdSGgkkP0D1L7LnYpRJD"),
 *             @OA\Property(property="type", type="string", example="payment_intent.succeeded"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="object",
 *                     type="object",
 *                     @OA\Property(property="id", type="string", example="pi_3N1KcdSGgkkP0D1L7LnYpRJD"),
 *                     @OA\Property(property="metadata", type="object", example={"booking_id": "12"})
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Webhook traité avec succès"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête invalide"
 *     )
 * )
 */ 
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            Log::error('⚠️ Webhook invalide : payload non parsable');
            return response('Invalid payload', Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('⚠️ Webhook signature invalide');
            return response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        // Traitement des événements spécifiques
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                Log::info("✅ Paiement réussi : {$paymentIntent->id}");
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                Log::warning("❌ Paiement échoué : {$paymentIntent->id}");
                break;

            default:
                Log::info("🔔 Événement Stripe reçu : {$event->type}");
        }

        return response('Webhook handled', Response::HTTP_OK);
    }
}
