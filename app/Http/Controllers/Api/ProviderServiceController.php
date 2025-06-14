<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProviderServiceRequest;
use App\Http\Requests\UpdateProviderServiceRequest;
use App\Http\Resources\ProviderServiceResource;
use App\Models\ProviderService;
use App\Services\ProviderServiceService;
use App\Services\ApiService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ProviderServiceController extends Controller
{
    protected $providerServiceService;

    public function __construct(ProviderServiceService $providerServiceService)
    {
        $this->providerServiceService = $providerServiceService;
    }

    /**
     * @OA\Get(
     *     path="/provider-services",
     *     tags={"Provider Services"},
     *     summary="Lister tous les services proposés par les providers",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Liste récupérée"),
     *     @OA\Response(response=500, description="Erreur serveur")
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $this->authorize('viewAny', ProviderService::class);
            $items = $this->providerServiceService->getAll();
            return ApiService::response(ProviderServiceResource::collection($items));
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Throwable $e) {
            Log::error('ProviderService index error', ['error' => $e]);
            return ApiService::response(['message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/provider-services",
     *     tags={"Provider Services"},
     *     summary="Créer un service personnalisé par un provider",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"provider_id","service_id"},
     *             @OA\Property(property="provider_id", type="integer"),
     *             @OA\Property(property="service_id", type="integer"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="duration", type="integer"),
     *             @OA\Property(property="available", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Créé avec succès"),
     *     @OA\Response(response=422, description="Validation échouée")
     * )
     */
    public function store(StoreProviderServiceRequest $request): JsonResponse
    {
        try {
            $this->authorize('create', ProviderService::class);
            $item = $this->providerServiceService->create($request->validated());
            return ApiService::response(new ProviderServiceResource($item), 201);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Throwable $e) {
            Log::error('ProviderService store error', ['error' => $e]);
            return ApiService::response(['message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/provider-services/{id}",
     *     tags={"Provider Services"},
     *     summary="Afficher un service provider spécifique",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Détail du service"),
     *     @OA\Response(response=404, description="Non trouvé")
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $providerService = $this->providerServiceService->find($id);
            $this->authorize('view', $providerService);
            return ApiService::response(new ProviderServiceResource($providerService));
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Throwable $e) {
            Log::error('ProviderService show error', ['error' => $e]);
            return ApiService::response(['message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/provider-services/{id}",
     *     tags={"Provider Services"},
     *     summary="Mettre à jour un service provider",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="duration", type="integer"),
     *             @OA\Property(property="available", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Mis à jour"),
     *     @OA\Response(response=404, description="Non trouvé")
     * )
     */
    public function update(UpdateProviderServiceRequest $request, int $id): JsonResponse
    {
        try {
            $providerService = $this->providerServiceService->find($id);
            $this->authorize('update', $providerService);
            $item = $this->providerServiceService->update($providerService, $request->validated());
            return ApiService::response(new ProviderServiceResource($item));
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Throwable $e) {
            Log::error('ProviderService update error', ['error' => $e]);
            return ApiService::response(['message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/provider-services/{id}",
     *     tags={"Provider Services"},
     *     summary="Supprimer un service provider",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Supprimé"),
     *     @OA\Response(response=404, description="Non trouvé")
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $providerService = $this->providerServiceService->find($id);
            $this->authorize('delete', $providerService);
            $this->providerServiceService->delete($providerService);
            return ApiService::response(['message' => 'Supprimé avec succès']);
        } catch (AuthorizationException $e) {
            return ApiService::response(['message' => __('messages.unauthorized')], 403);
        } catch (\Throwable $e) {
            Log::error('ProviderService delete error', ['error' => $e]);
            return ApiService::response(['message' => 'Erreur serveur'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/provider-services/by-provider/{provider_id}",
     *     tags={"Provider Services"},
     *     summary="Lister les services proposés par un provider",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="provider_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Liste des services du provider"),
     *     @OA\Response(response=404, description="Provider non trouvé")
     * )
     */
    public function getByProvider(int $provider_id): JsonResponse
    {
        try {
            $services = $this->providerServiceService->getByProvider($provider_id);
            return ApiService::response(ProviderServiceResource::collection($services));
        } catch (\Throwable $e) {
            return ApiService::response(['message' => 'Erreur serveur'], 500);
        }
    }
    
    /**
     * @OA\Get(
     *     path="/provider-services/by-service/{service_id}",
     *     tags={"Provider Services"},
     *     summary="Lister les providers qui proposent un service donné",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="service_id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Liste des providers pour ce service"),
     *     @OA\Response(response=404, description="Service non trouvé")
     * )
     */
    public function getByService(int $service_id): JsonResponse
    {
        try {
            $providers = $this->providerServiceService->getByService($service_id);
            return ApiService::response(ProviderServiceResource::collection($providers));
        } catch (\Throwable $e) {
            return ApiService::response(['message' => 'Erreur serveur'], 500);
        }
    }

}
