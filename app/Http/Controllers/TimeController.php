<?php

namespace App\Http\Controllers;

use App\Models\Time;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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
        $formattedTime = number_format($time, 6, '.', '');
        $nt = new Time();
        $nt->user_id = auth()->id();
        $nt->time = $formattedTime;
        if($formattedTime >= 0.10000){
            $nt->valid = true;
        }else{
            $nt->valid = false;
        }
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
        $sql = "SELECT t.user_id, u.name, ROUND(t.time, 3) AS times, t.created_at
                FROM times t
                JOIN users u ON u.id = t.user_id
                JOIN (
                    SELECT user_id, MIN(time) AS min_time
                    FROM times
                    WHERE valid = 1
                    GROUP BY user_id
                ) sub ON t.user_id = sub.user_id AND t.time = sub.min_time
                ORDER BY t.time ASC
                LIMIT 100";
        $times = DB::select($sql);
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
    /**
     * @OA\Get(
     *     path="/api/user/times",
     *     tags={"Time"},
     *     summary="Obtener los tiempos del usuario autenticado",
     *     description="Recupera todos los tiempos registrados del usuario actualmente autenticado.",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tiempos del usuario autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=10),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="time", type="float", example=0.189),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-02-24T12:34:56Z"),
     *                 )
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Unauthenticated"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron tiempos para el usuario autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="error", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="No times found for this user"),
     *         )
     *     )
     * )
     */
    public function showUserTimes(Request $request): JsonResponse{
        $userId = auth()->id();
        $perPage = $request->input('per_page', 50);
        $times = Time::where('user_id', $userId)->orderBy('created_at', 'desc')->selectRaw('ROUND(time, 3) as time, created_at, valid')->paginate($perPage);;
        if ($times->isEmpty()) {
            return response()->json(['success' => false, 'error' => true, 'message' => 'No times found for this user'], 404);
        }
        return response()->json(['success' => true, 'data' => $times->items(), 'current_page' => $times->currentPage(),'last_page' => $times->lastPage()], 200);
    }
}
