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
     *     summary="Obtenir la liste des services",
     *     description="Récupère une liste de tous les services avec leurs traductions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="object",
     *                     example={"fr": "Toilettage pour chien", "en": "Dog Grooming", "es": "Aseo para perros"}
     *                 ),
     *                 @OA\Property(property="description", type="object",
     *                     example={"fr": "Service complet de toilettage", "en": "Full grooming service", "es": "Servicio completo de aseo"}
     *                 ),
     *                 @OA\Property(property="price", type="number", format="float", example=49.99),
     *                 @OA\Property(property="active", type="boolean", example=true),
     *                 @OA\Property(property="category", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="object",
     *                         example={"fr": "Toilettage", "en": "Grooming", "es": "Aseo"}
     *                     ),
     *                     @OA\Property(property="icon", type="string", example="icon.png"),
     *                     @OA\Property(property="color", type="string", example="#ffcc00")
     *                 ),
     *                 @OA\Property(property="provider", type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="object",
     *                         example={"fr": "Prestataire Pro", "en": "Pro Provider", "es": "Proveedor Profesional"}
     *                     ),
     *                     @OA\Property(property="photo", type="string", example="provider.png"),
     *                     @OA\Property(property="specialization", type="string", example="Toilettage"),
     *                     @OA\Property(property="rating", type="number", format="float", example=4.8)
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-19 10:30"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-19 11:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('view', new Service());
            $services = $this->serviceService->getAll();
            return ApiService::response(ServiceResource::collection($services), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Une erreur est survenue.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/services",
     *     tags={"Services"},
     *     summary="Créer un nouveau service",
     *     description="Ajoute un nouveau service avec support multilingue",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="provider_id", type="integer", example=2),
     *             @OA\Property(property="name", type="object",
     *                 example={"fr": "Toilettage pour chien", "en": "Dog Grooming", "es": "Aseo para perros"}
     *             ),
     *             @OA\Property(property="description", type="object",
     *                 example={"fr": "Service complet de toilettage", "en": "Full grooming service", "es": "Servicio completo de aseo"}
     *             ),
     *             @OA\Property(property="price", type="number", format="float", example=49.99),
     *             @OA\Property(property="active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="object",
     *                 example={"fr": "Toilettage pour chien", "en": "Dog Grooming", "es": "Aseo para perros"}
     *             ),
     *             @OA\Property(property="description", type="object",
     *                 example={"fr": "Service complet de toilettage", "en": "Full grooming service", "es": "Servicio completo de aseo"}
     *             ),
     *             @OA\Property(property="price", type="number", format="float", example=49.99),
     *             @OA\Property(property="active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function store(StoreServiceRequest $request)
    {
        try {
            $this->authorize('create', new Service());
            $service = $this->serviceService->create($request->validated());
            return ApiService::response(new ServiceResource($service), 201);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Une erreur est survenue.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Obtenir les détails d'un service",
     *     description="Récupère les détails d'un service avec ses traductions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Détails récupérés avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="object",
     *                 example={"fr": "Toilettage pour chien", "en": "Dog Grooming", "es": "Aseo para perros"}
     *             ),
     *             @OA\Property(property="description", type="object",
     *                 example={"fr": "Service complet de toilettage", "en": "Full grooming service", "es": "Servicio completo de aseo"}
     *             ),
     *             @OA\Property(property="price", type="number", format="float", example=49.99),
     *             @OA\Property(property="active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Service non trouvé"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function show($id)
    {
        try {
            $service = $this->serviceService->find($id);
            $this->authorize('view', $service);
            return ApiService::response(new ServiceResource($service), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Une erreur est survenue.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Mettre à jour un service",
     *     description="Met à jour les informations d'un service, y compris les traductions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="category_id", type="integer", example=1),
     *             @OA\Property(property="provider_id", type="integer", example=2),
     *             @OA\Property(property="name", type="object",
     *                 example={"fr": "Toilettage de luxe", "en": "Luxury Dog Grooming"}
     *             ),
     *             @OA\Property(property="description", type="object",
     *                 example={"fr": "Toilettage haut de gamme", "en": "Premium grooming service"}
     *             ),
     *             @OA\Property(property="price", type="number", format="float", example=59.99),
     *             @OA\Property(property="active", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Service mis à jour avec succès"),
     *     @OA\Response(response=404, description="Service non trouvé"),
     *     @OA\Response(response=422, description="Erreur de validation"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function update(UpdateServiceRequest $request, $id)
    {
        try {
            $service = $this->serviceService->find($id);
            $this->authorize('update', $service);
            $updatedService = $this->serviceService->update($service, $request->validated());
            return ApiService::response(new ServiceResource($updatedService), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Une erreur est survenue.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/services/{id}",
     *     tags={"Services"},
     *     summary="Supprimer un service",
     *     description="Supprime un service par ID",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Service supprimé avec succès"),
     *     @OA\Response(response=404, description="Service non trouvé"),
     *     @OA\Response(response=500, description="Erreur interne du serveur")
     * )
     */
    public function destroy($id)
    {
        try {
            $service = $this->serviceService->find($id);
            $this->authorize('delete', $service);
            $this->serviceService->delete($service);
            return ApiService::response(['message' => 'Service supprimé avec succès'], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => 'Une erreur est survenue.', 'error' => $e->getMessage()], 500);
        }
    }
}
