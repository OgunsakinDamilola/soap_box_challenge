<?php
/**
 * Created by PhpStorm.
 * User: umarfarouq
 * Date: 6/10/20
 * Time: 9:59 PM
 */

namespace App\Handlers;

use Illuminate\Support\Facades\Validator;

/**
 * Trait RequestHandler
 * @package App\Libraries
 */
trait ResponseHandler
{
    public function successResponse($message, $data)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    public function errorResponse($message, $errors)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], 422);
    }
}
