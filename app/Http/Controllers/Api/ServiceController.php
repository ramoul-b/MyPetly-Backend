<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Services\ServiceService;
use App\Services\ApiService;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

/**
 * @OA\Get(
 *     path="/services",
 *     tags={"Services"},
 *     summary="Get all services",
 *     description="Retrieve a list of all services",
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List retrieved successfully",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Dog Grooming"),
 *             @OA\Property(property="description", type="string", example="Full grooming service"),
 *             @OA\Property(property="price", type="number", format="float", example=49.99),
 *             @OA\Property(property="active", type="boolean", example=true)
 *         ))
 *     ),
 *     @OA\Response(response=500, description="Internal server error")
 * )
 */
public function index(): JsonResponse
{
    try {
        $services = $this->serviceService->getAll();
        return ApiService::response(ServiceResource::collection($services), 200);
    } catch (\Exception $e) {
        return ApiService::response(['message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
    }
}



    /**
     * @OA\Post(
     *     path="/services",
     *     tags={"Services"},
     *     summary="Create a new service",
     *     description="Add a new service",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="category_id", type="integer", example=1),
     *         @OA\Property(property="provider_id", type="integer", example=2),
     *         @OA\Property(property="name", type="string", example="Dog Grooming"),
     *         @OA\Property(property="description", type="string", example="Full grooming service"),
     *         @OA\Property(property="price", type="number", format="float", example=49.99),
     *         @OA\Property(property="active", type="boolean", example=true)
     *     )),
     *     @OA\Response(response=201, description="Service created successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function store(StoreServiceRequest $request)
    {
        try {
            $service = $this->serviceService->create($request->validated());
            return ApiService::response(new ServiceResource($service), 201);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Get service details",
     *     description="Retrieve details of a specific service",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Service details retrieved successfully"),
     *     @OA\Response(response=404, description="Service not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function show($id)
    {
        try {
            $service = $this->serviceService->find($id);
            return ApiService::response(new ServiceResource($service), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Update service details",
     *     description="Update details of an existing service",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         @OA\Property(property="category_id", type="integer", example=1),
     *         @OA\Property(property="provider_id", type="integer", example=2),
     *         @OA\Property(property="name", type="string", example="Dog Grooming Deluxe"),
     *         @OA\Property(property="description", type="string", example="Enhanced grooming service"),
     *         @OA\Property(property="price", type="number", format="float", example=59.99),
     *         @OA\Property(property="active", type="boolean", example=false)
     *     )),
     *     @OA\Response(response=200, description="Service updated successfully"),
     *     @OA\Response(response=404, description="Service not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function update(UpdateServiceRequest $request, $id)
    {
        try {
            $service = $this->serviceService->find($id);
            $updatedService = $this->serviceService->update($service, $request->validated());
            return ApiService::response(new ServiceResource($updatedService), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Delete a service",
     *     description="Delete a service by ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Service deleted successfully"),
     *     @OA\Response(response=404, description="Service not found"),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function destroy($id)
    {
        try {
            $service = $this->serviceService->find($id);
            $this->serviceService->delete($service);
            return ApiService::response(['message' => 'Service deleted successfully'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'An error occurred.', 'error' => $e->getMessage()], 500);
        }
    }
}
