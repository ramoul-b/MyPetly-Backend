<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnimalRequest;
use App\Http\Requests\UpdateAnimalRequest;
use App\Http\Resources\AnimalResource;
use App\Services\AnimalService;
use App\Services\ApiService;
use Illuminate\Http\Request;

class AnimalController extends Controller
{
    protected $animalService;

    public function __construct(AnimalService $animalService)
    {
        $this->animalService = $animalService;
    }

    /**
     * @OA\Get(
     *     path="/animals",
     *     tags={"Animals"},
     *     summary="Get all animals",
     *     description="Retrieve a list of all animals for the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of animals",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Buddy"),
     *                 @OA\Property(property="species", type="string", example="Dog"),
     *                 @OA\Property(property="breed", type="string", example="Labrador"),
     *                 @OA\Property(property="birthdate", type="string", format="date", example="2020-05-10"),
     *                 @OA\Property(property="photo_url", type="string", example="http://example.com/storage/animals/1.jpg"),
     *                 @OA\Property(property="status", type="string", example="active")
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
            // Vérification du rôle
            if (auth()->user()->hasRole('admin')) {
                $animals = Animal::all();
            } else {
                $animals = auth()->user()->animals ?? collect();
            }
    
            return ApiService::response(AnimalResource::collection($animals), 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }
    


    /**
     * @OA\Post(
     *     path="/animals",
     *     tags={"Animals"},
     *     summary="Create a new animal",
     *     description="Add a new animal profile for the authenticated user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Buddy"),
     *             @OA\Property(property="species", type="string", example="Dog"),
     *             @OA\Property(property="breed", type="string", example="Labrador"),
     *             @OA\Property(property="birthdate", type="string", format="date", example="2020-05-10"),
     *             @OA\Property(property="photo", type="string", format="binary", description="Animal photo file")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Animal created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Buddy"),
     *             @OA\Property(property="sex", type="string", enum={"male", "female"}, example="male"),
     *             @OA\Property(property="color", type="string", example="Brown"),
     *             @OA\Property(property="weight", type="number", format="float", example=12.5),
     *             @OA\Property(property="height", type="number", format="float", example=45.3),
     *             @OA\Property(property="identification_number", type="string", example="1234567890AB"),
     *             @OA\Property(property="species", type="string", example="Dog"),
     *             @OA\Property(property="breed", type="string", example="Labrador"),
     *             @OA\Property(property="birthdate", type="string", format="date", example="2020-05-10"),
     *             @OA\Property(property="photo_url", type="string", example="http://example.com/storage/animals/1.jpg"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     )
     * )
     */
    public function store(StoreAnimalRequest $request)
{
    try {
        $animal = $this->animalService->createAnimal($request->validated());
        return ApiService::response(new AnimalResource($animal), 201);
    } catch (\Exception $e) {
        return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
    }
}


    /**
     * @OA\Get(
     *     path="/animals/{id}",
     *     tags={"Animals"},
     *     summary="Get animal details",
     *     description="Retrieve the details of a specific animal owned by the authenticated user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Animal ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Animal details",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Buddy"),
     *             @OA\Property(property="species", type="string", example="Dog"),
     *             @OA\Property(property="breed", type="string", example="Labrador"),
     *             @OA\Property(property="birthdate", type="string", format="date", example="2020-05-10"),
     *             @OA\Property(property="photo_url", type="string", example="http://example.com/storage/animals/1.jpg"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Animal not found.")
     *         )
     *     )
     * )
     */
    public function show($id)
{
    try {
        $animal = $this->animalService->getAnimalById($id);
        if (!$animal) {
            return ApiService::response(['message' => __('messages.animal_not_found')], 404);
        }
        return ApiService::response(new AnimalResource($animal), 200);
    } catch (\Exception $e) {
        return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
    }
}


    /**
     * @OA\Put(
     *     path="/animals/{id}",
     *     tags={"Animals"},
     *     summary="Update animal details",
     *     description="Update the details of an existing animal",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Animal ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Buddy"),
     *             @OA\Property(property="species", type="string", example="Dog"),
     *             @OA\Property(property="breed", type="string", example="Labrador"),
     *             @OA\Property(property="birthdate", type="string", format="date", example="2020-05-10"),
     *             @OA\Property(property="photo", type="string", format="binary", description="Animal photo file")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Animal updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Buddy"),
     *             @OA\Property(property="species", type="string", example="Dog"),
     *             @OA\Property(property="breed", type="string", example="Labrador"),
     *             @OA\Property(property="birthdate", type="string", format="date", example="2020-05-10"),
     *             @OA\Property(property="photo_url", type="string", example="http://example.com/storage/animals/1.jpg"),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Animal not found.")
     *         )
     *     )
     * )
     */
    public function update(UpdateAnimalRequest $request, $id)
{
    try {
        $animal = $this->animalService->updateAnimal($id, $request->validated());
        if (!$animal) {
            return ApiService::response(['message' => __('messages.animal_not_found')], 404);
        }
        return ApiService::response(new AnimalResource($animal), 200);
    } catch (\Exception $e) {
        return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
    }
}


    /**
     * @OA\Delete(
     *     path="/animals/{id}",
     *     tags={"Animals"},
     *     summary="Delete an animal",
     *     description="Delete an animal profile owned by the authenticated user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Animal ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Animal deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Animal deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Animal not found.")
     *         )
     *     )
     * )
     */
    public function destroy($id)
{
    try {
        $deleted = $this->animalService->deleteAnimal($id);
        if (!$deleted) {
            return ApiService::response(['message' => __('messages.animal_not_found')], 404);
        }
        return ApiService::response(['message' => __('messages.animal_deleted')], 200);
    } catch (\Exception $e) {
        return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
    }
}


    /**
     * @OA\Post(
     *     path="/animals/{id}/collar",
     *     tags={"Animals"},
     *     summary="Attach a collar to an animal",
     *     description="Attach a unique NFC/QR collar to the specified animal",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the animal",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"collar_id"},
     *             @OA\Property(property="collar_id", type="string", example="12345ABC")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collar attached successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Collar attached successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Animal not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error."),
     *             @OA\Property(property="errors", type="object", example={"collar_id": {"The collar ID must be unique."}})
     *         )
     *     )
     * )
     */

     public function attachCollar(Request $request, $id)
     {
         try {
             $validated = $request->validate([
                 'collar_id' => 'required|unique:collars,id',
             ]);
     
             $animal = $this->animalService->attachCollar($id, $validated['collar_id']);
             if (!$animal) {
                 return ApiService::response(['message' => __('messages.animal_not_found')], 404);
             }
     
             return ApiService::response(['message' => __('messages.collar_attached')], 200);
         } catch (\Exception $e) {
             return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
         }
     }
     

    /**
     * @OA\Put(
     *     path="/animals/{id}/lost",
     *     tags={"Animals"},
     *     summary="Mark an animal as lost",
     *     description="Update the status of an animal to 'lost'",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the animal",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Animal marked as lost",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Animal marked as lost.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Animal not found.")
     *         )
     *     )
     * )
     */
    public function markAsLost($id)
{
    try {
        $animal = $this->animalService->markAsLost($id);
        if (!$animal) {
            return ApiService::response(['message' => __('messages.animal_not_found')], 404);
        }
        return ApiService::response(['message' => __('messages.animal_marked_lost')], 200);
    } catch (\Exception $e) {
        return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
    }
}



    /**
     * @OA\Put(
     *     path="/animals/{id}/found",
     *     tags={"Animals"},
     *     summary="Mark an animal as found",
     *     description="Update the status of an animal to 'active' after being found",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the animal",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Animal marked as found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Animal marked as found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Animal not found.")
     *         )
     *     )
     * )
     */

     public function markAsFound($id)
     {
         try {
             $animal = $this->animalService->markAsFound($id);
             if (!$animal) {
                 return ApiService::response(['message' => __('messages.animal_not_found')], 404);
             }
             return ApiService::response(['message' => __('messages.animal_marked_found')], 200);
         } catch (\Exception $e) {
             return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
         }
     }
     

    /**
     * @OA\Get(
     *     path="/scan/{collarId}",
     *     tags={"Animals"},
     *     summary="Scan an animal's collar",
     *     description="Retrieve public details of an animal by scanning its NFC/QR collar",
     *     @OA\Parameter(
     *         name="collarId",
     *         in="path",
     *         required=true,
     *         description="Unique ID of the collar",
     *         @OA\Schema(type="string", example="12345ABC")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Animal details",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Buddy"),
     *             @OA\Property(property="owner_contact", type="string", example="+123456789"),
     *             @OA\Property(property="message", type="string", example="Scan successful.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Animal not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Animal not found.")
     *         )
     *     )
     * )
     */
    public function scanCollar($collarId)
    {
        try {
            $animal = $this->animalService->scanCollar($collarId);
            if (!$animal) {
                return ApiService::response(['message' => __('messages.animal_not_found')], 404);
            }

            return ApiService::response([
                'name' => $animal->name,
                'owner_contact' => $animal->user->contact_info,
                'message' => __('messages.scan_success'),
            ], 200);
        } catch (\Exception $e) {
            return ApiService::response(['message' => __('messages.operation_failed'), 'error' => $e->getMessage()], 500);
        }
    }

}
