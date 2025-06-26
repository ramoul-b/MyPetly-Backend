<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Shipping", description="Mise à jour du statut d'expédition")
 */
class ShippingStatusController extends Controller
{
    /**
     * @OA\Patch(
     *     path="/orders/{order}/shipping-status",
     *     tags={"Shipping"},
     *     summary="Mettre à jour le statut d'expédition",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="order", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(@OA\JsonContent(
     *         required={"shipping_status"},
     *         @OA\Property(property="shipping_status", type="string")
     *     )),
     *     @OA\Response(response=200, description="Statut mis à jour")
     * )
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $this->authorize('update', $order);
        $data = $request->validate([
            'shipping_status' => 'required|string'
        ]);
        $order->shipping_status = $data['shipping_status'];
        $order->save();

        return ApiService::response(new OrderResource($order), 200);
    }
}
