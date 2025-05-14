<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\CreatePaymentIntentRequest;
use App\Http\Resources\PaymentIntentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @OA\Post(
     *     path="/payment-intent",
     *     tags={"Paiement"},
     *     summary="Créer un PaymentIntent Stripe",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount"},
     *             @OA\Property(property="amount", type="integer", example=1000),
     *             @OA\Property(property="currency", type="string", example="eur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Client secret créé",
     *         @OA\JsonContent(
     *             @OA\Property(property="client_secret", type="string", example="pi_123456_secret_abcdef")
     *         )
     *     )
     * )
     */
    public function createIntent(CreatePaymentIntentRequest $request): JsonResponse
    {
        try {
            $intent = $this->paymentService->createIntent(
                $request->amount,
                $request->currency ?? 'eur'
            );

            return response()->json(
                new PaymentIntentResource($intent),
                201
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la création du PaymentIntent',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
