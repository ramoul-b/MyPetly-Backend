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
}
