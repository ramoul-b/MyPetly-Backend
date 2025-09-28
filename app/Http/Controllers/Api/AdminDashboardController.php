<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AdminDashboard;
use App\Services\AdminDashboardService;
use App\Services\ApiService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Admin Dashboard", description="Statistiques globales pour l'administration")
 */
class AdminDashboardController extends Controller
{
    public function __construct(private readonly AdminDashboardService $dashboardService)
    {
    }

    /**
     * @OA\Get(
     *     path="/admin/dashboard/stats",
     *     tags={"Admin Dashboard"},
     *     summary="Récupérer les statistiques du tableau de bord administrateur",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         required=false,
     *         description="Filtrer à partir de cette date (format YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         required=false,
     *         description="Filtrer jusqu'à cette date (format YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques calculées",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_revenue", type="number", format="float", example=1250.50),
     *             @OA\Property(property="orders_volume", type="integer", example=45),
     *             @OA\Property(property="average_order_value", type="number", format="float", example=27.78),
     *             @OA\Property(property="pending_orders", type="integer", example=5),
     *             @OA\Property(property="completed_orders", type="integer", example=30),
     *             @OA\Property(property="bookings_volume", type="integer", example=12),
     *             @OA\Property(property="pending_bookings", type="integer", example=3),
     *             @OA\Property(property="pending_users", type="integer", example=4),
     *             @OA\Property(property="pending_providers", type="integer", example=2),
     *             @OA\Property(property="total_users", type="integer", example=150),
     *             @OA\Property(property="total_providers", type="integer", example=40)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Non authentifié"),
     *     @OA\Response(response=403, description="Accès interdit"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $this->authorize('viewAny', AdminDashboard::class);

            $filters = $request->only(['date_from', 'date_to']);
            $stats = $this->dashboardService->getStats($filters);

            return ApiService::response($stats, 200);
        } catch (AuthorizationException $exception) {
            return ApiService::response($exception->getMessage(), 403);
        } catch (\Throwable $exception) {
            return ApiService::response($exception->getMessage(), 500);
        }
    }
}
