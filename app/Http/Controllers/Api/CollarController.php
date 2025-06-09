<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCollarRequest;
use App\Http\Requests\UpdateCollarRequest;
use App\Http\Resources\CollarResource;
use App\Models\Collar;
use App\Services\ApiService;

class CollarController extends Controller
{


    
    /**
     * @OA\Get(
     *     path="/collars",
     *     tags={"Collars"},
     *     summary="Get all collars",
     *     description="Retrieve a list of all collars",
     *     @OA\Response(
     *         response=200,
     *         description="List of collars",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nfc_id", type="string", example="NFC12345"),
     *                 @OA\Property(property="qr_code_url", type="string", example="http://example.com/qrcode/1.png"),
     *                 @OA\Property(property="animal_id", type="integer", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-25T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-25T12:34:56Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="An error occurred."),
     *             @OA\Property(property="error", type="string", example="Detailed error message")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $this->authorize('view', new Collar());
            $collars = Collar::all();
            return ApiService::response(CollarResource::collection($collars), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }


/**
 * @OA\Post(
 *     path="/collars",
 *     tags={"Collars"},
 *     summary="Create a new collar",
 *     description="Add a new collar with optional NFC ID to the system",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"animal_id"},
 *             @OA\Property(property="nfc_id", type="string", example="NFC12345", description="Optional NFC ID of the collar"),
 *             @OA\Property(property="animal_id", type="integer", example=1, description="ID of the associated animal")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Collar created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="nfc_id", type="string", example="NFC12345", description="NFC ID of the collar"),
 *             @OA\Property(property="qr_code_url", type="string", example="http://example.com/storage/qrcodes/1.png", description="URL of the generated QR Code"),
 *             @OA\Property(property="animal_id", type="integer", example=1, description="ID of the associated animal"),
 *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-25T12:34:56Z"),
 *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-25T12:34:56Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Validation error."),
 *             @OA\Property(property="errors", type="object", example={"nfc_id": {"The nfc_id field must be unique."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="An error occurred."),
 *             @OA\Property(property="error", type="string", example="Detailed error message")
 *         )
 *     )
 * )
 */
    public function store(StoreCollarRequest $request)
    {
        try {
            $this->authorize('create', new Collar());
            $collar = Collar::create($request->validated());
            return ApiService::response(new CollarResource($collar), 201);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/collars/{id}",
     *     tags={"Collars"},
     *     summary="Get collar details",
     *     description="Retrieve the details of a specific collar by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the collar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collar details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nfc_id", type="string", example="NFC12345"),
     *             @OA\Property(property="qr_code_url", type="string", example="http://example.com/qrcode/1.png"),
     *             @OA\Property(property="animal_id", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-25T12:34:56Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-25T12:34:56Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Collar not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Collar not found.")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $collar = Collar::find($id);
            if (!$collar) {
                return ApiService::response(['message' => __('messages.collar_not_found')], 404);
            }
            $this->authorize('view', $collar);
            return ApiService::response(new CollarResource($collar), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/collars/{id}",
     *     tags={"Collars"},
     *     summary="Update a collar",
     *     description="Update the details of an existing collar",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the collar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nfc_id", type="string", example="NFC12345"),
     *             @OA\Property(property="qr_code_url", type="string", example="http://example.com/qrcode/1.png")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collar updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nfc_id", type="string", example="NFC12345"),
     *             @OA\Property(property="qr_code_url", type="string", example="http://example.com/qrcode/1.png"),
     *             @OA\Property(property="animal_id", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-25T12:34:56Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-25T12:34:56Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Collar not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Collar not found.")
     *         )
     *     )
     * )
     */
    public function update(UpdateCollarRequest $request, $id)
    {
        try {
            $collar = Collar::find($id);
            if (!$collar) {
                return ApiService::response(['message' => __('messages.collar_not_found')], 404);
            }

            $this->authorize('update', $collar);
            $collar->update($request->validated());
            return ApiService::response(new CollarResource($collar), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/collars/{id}",
     *     tags={"Collars"},
     *     summary="Delete a collar",
     *     description="Remove a collar from the system",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the collar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collar deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Collar deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Collar not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Collar not found.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $collar = Collar::find($id);
            if (!$collar) {
                return ApiService::response(['message' => __('messages.collar_not_found')], 404);
            }

            $this->authorize('delete', $collar);
            $collar->delete();
            return ApiService::response(['message' => __('messages.collar_deleted')], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/collars/{collarId}/assign/{animalId}",
     *     tags={"Collars"},
     *     summary="Assign a collar to an animal",
     *     description="Link a collar to a specific animal",
     *     @OA\Parameter(
     *         name="collarId",
     *         in="path",
     *         required=true,
     *         description="ID of the collar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="animalId",
     *         in="path",
     *         required=true,
     *         description="ID of the animal",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collar assigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Collar assigned successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Collar or animal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Collar or animal not found.")
     *         )
     *     )
     * )
     */
    public function assignToAnimal($collarId, $animalId)
    {
        try {
            $collar = Collar::find($collarId);
            if (!$collar) {
                return ApiService::response(['message' => __('messages.collar_not_found')], 404);
            }

            // Vérifiez si le collier est déjà assigné
            if ($collar->animal_id && $collar->animal_id !== $animalId) {
                return ApiService::response(['message' => __('messages.collar_already_assigned')], 409);
            }

            $collar->animal_id = $animalId;
            $collar->save();

            return ApiService::response(['message' => __('messages.collar_assigned')], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

}
