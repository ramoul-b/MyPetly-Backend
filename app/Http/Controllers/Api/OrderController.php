<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Services\ApiService;
use App\Services\OrderService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index(): JsonResponse
    {
        try {
            $this->authorize('view', new Order());
            $orders = $this->orderService->list();
            return ApiService::response(OrderResource::collection($orders), 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    public function my(Request $request): JsonResponse
    {
        try {
            $this->authorize('view', new Order());
            $filters = $request->only([
                'status',
                'date_from',
                'date_to',
                'sort',
                'limit',
                'page',
                'items_count',
            ]);

            $result = $this->orderService->listForProvider($request->user(), $filters);

            if ($result instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) {
                return ApiService::response([
                    'data' => OrderResource::collection($result->items()),
                    'meta' => [
                        'page' => $result->currentPage(),
                        'per_page' => $result->perPage(),
                        'total' => $result->total(),
                        'total_pages' => $result->lastPage(),
                    ],
                ], 200);
            }

            return ApiService::response(OrderResource::collection($result), 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->find($id);
            $this->authorize('view', $order);
            return ApiService::response(new OrderResource($order), 200);
        } catch (\Throwable $e) {
            return ApiService::response('Order not found', 404);
        }
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', new Order());
            $order = $this->orderService->checkout($request->validated());
            return ApiService::response(new OrderResource($order), 201);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }

    public function stats(Request $request): JsonResponse
    {
        try {
            $this->authorize('view', new Order());
            $stats = $this->orderService->statsForProvider(
                $request->user(),
                $request->query('date_from'),
                $request->query('date_to')
            );
            return ApiService::response($stats, 200);
        } catch (\Throwable $e) {
            return ApiService::response($e->getMessage(), 500);
        }
    }
}
