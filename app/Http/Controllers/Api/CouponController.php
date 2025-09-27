<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use App\Services\ApiService;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Coupons", description="Gestion des coupons")
 */
class CouponController extends Controller
{
    public function __construct(private readonly CouponService $couponService)
    {
    }

    /**
     * @OA\Get(
     *     path="/coupons",
     *     tags={"Coupons"},
     *     summary="Liste des coupons",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="store_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="code", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="is_active", in="query", @OA\Schema(type="boolean")),
     *     @OA\Response(response=200, description="Liste des coupons"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->authorize('view', new Coupon());

            $coupons = $this->couponService->list($request->only(['store_id', 'code', 'is_active', 'per_page']));

            $resource = CouponResource::collection($coupons)->additional([
                'meta' => ['total' => $coupons->total()],
            ]);

            return ApiService::response($resource, 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/coupons",
     *     tags={"Coupons"},
     *     summary="Créer un coupon",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"store_id","code","name","discount_type","discount_value"},
     *         @OA\Property(property="store_id", type="integer"),
     *         @OA\Property(property="product_id", type="integer"),
     *         @OA\Property(property="code", type="string"),
     *         @OA\Property(property="name", type="object"),
     *         @OA\Property(property="description", type="object"),
     *         @OA\Property(property="discount_type", type="string", enum={"percentage","fixed"}),
     *         @OA\Property(property="discount_value", type="number", format="float"),
     *         @OA\Property(property="minimum_order_total", type="number", format="float"),
     *         @OA\Property(property="usage_limit", type="integer"),
     *         @OA\Property(property="starts_at", type="string", format="date-time"),
     *         @OA\Property(property="expires_at", type="string", format="date-time"),
     *         @OA\Property(property="is_active", type="boolean")
     *     )),
     *     @OA\Response(response=201, description="Coupon créé"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function store(StoreCouponRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', new Coupon());

            $data = $request->validated();
            $data['created_by'] = $data['created_by'] ?? $request->user()->id;

            $coupon = $this->couponService->create($data);

            return ApiService::response(new CouponResource($coupon->load(['store', 'product', 'creator'])), 201);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/coupons/{coupon}",
     *     tags={"Coupons"},
     *     summary="Afficher un coupon",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="coupon", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Coupon récupéré"),
     *     @OA\Response(response=404, description="Coupon introuvable")
     * )
     */
    public function show(Coupon $coupon): JsonResponse
    {
        try {
            $this->authorize('view', $coupon);
            $coupon->load(['store', 'product', 'creator']);

            return ApiService::response(new CouponResource($coupon), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Coupon not found', 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/coupons/{coupon}",
     *     tags={"Coupons"},
     *     summary="Mettre à jour un coupon",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="coupon", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Coupon mis à jour"),
     *     @OA\Response(response=404, description="Coupon introuvable"),
     *     @OA\Response(response=422, description="Données invalides")
     * )
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon): JsonResponse
    {
        try {
            $this->authorize('update', $coupon);

            $updated = $this->couponService->update($coupon, $request->validated());

            return ApiService::response(new CouponResource($updated), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Coupon not found', 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/coupons/{coupon}",
     *     tags={"Coupons"},
     *     summary="Supprimer un coupon",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="coupon", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=204, description="Coupon supprimé"),
     *     @OA\Response(response=404, description="Coupon introuvable")
     * )
     */
    public function destroy(Coupon $coupon): JsonResponse
    {
        try {
            $this->authorize('delete', $coupon);

            $this->couponService->delete($coupon);

            return ApiService::response(['message' => 'Coupon deleted'], 200);
        } catch (\Throwable $e) {
            return ApiService::response('Coupon not found', 404);
        }
    }
}
