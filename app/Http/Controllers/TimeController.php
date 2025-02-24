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
     *     path="/api/time",
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
     *         response=201,
     *         description="Resultado del registro del registro del tiempo",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="error", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Time has been recorded"),
     *          )
     *     ),
     *     @OA\Response(
     *         response=400,
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
            return response()->json(['success'=>false, 'error'=>true, 'message'=>'Missing required parameter time'], 400);
        }
        $nt = new Time();
        $nt->user_id = 3;
        $nt->time = $time;
        $nt->save();

        return response()->json(['success'=>true, 'error'=>false, 'message'=>'Time has been save'], 201);
    }
}
