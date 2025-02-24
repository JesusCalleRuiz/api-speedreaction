<?php

namespace App\Http\Controllers;

use App\Models\Time;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="SPEEDREACTION API",
 *     version="1.0",
 *     description="L5 Swagger OpenApi para la interface de recepcion de tiempos generados por SpeedReaction",
 *     @OA\Contact(
 *          email="jesus.calle.ruiz8@gmail.com"
 *     ),
 * )
 */
class TimeController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/times",
     *     tags={"Time"},
     *     summary="Almacenar el tiempo de un usuario",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={
     *                          "user_id", "time"
     *                      },
     *            @OA\Property(property="user_id", type="integer", example="1"),
     *            @OA\Property(property="time", type="float", example="0.189"),
     *         ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="Resultado del registro del registro del tiempo",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Time has been recorded"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *          description="Error asociado al registro del tiempo",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Missing required parameter time"),
     *           )
     *     )
     * )
     */
    public function store(Request $r):JsonResponse {
        $statusCode = 201;
        $time = $r->get('time');
        if($time == null){
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter time'], 404);
        }
        $nt = new Time();
        $nt->user_id = 3;
        $nt->time = $time;
        $nt->save();

        return response()->json(['success'=>true, 'error'=>false, 'message'=>'Time has been save'], 200);
    }
    /**
     * @OA\Get(
     *     path="/api/times",
     *     tags={"Time"},
     *     summary="Obtener todos los tiempos registrados",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tiempos registrados",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=3),
     *                 @OA\Property(property="time", type="float", example=0.189),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *             )),
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse{
        $times = Time::all();
        return response()->json(['success' => true, 'error'=>false, 'data' => $times], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/times/{id}",
     *     tags={"Time"},
     *     summary="Obtener un tiempo específico por ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle del tiempo registrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=3),
     *                 @OA\Property(property="time", type="float", example=0.189),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Tiempo no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Time not found"),
     *         )
     *     )
     * )
     */
    public function show($id): JsonResponse{
        $time = Time::find($id);
        if (!$time) {
            return response()->json(['success' => false, 'error' => true, 'message' => 'Time not found'], 404);
        }
        return response()->json(['success' => true, 'error'=>false, 'data' => $time], 200);
    }
}
