<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Game;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
* @OA\Info(title="API Bulls and Cows", version="1.0")
*
* @OA\Server(url="http://127.0.0.1:8000/")
*
* @OA\SecurityScheme(
*     type="apiKey",
*     in="header",
*     securityScheme="api_key",
*     name="api_key"
* )
*/

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    

    protected function authorizationCheck(Request $request)
    {
        $json = json_decode($request->getContent(), true); 
        $id = $json['id'];
        $user = $json['user'];

        if (isset($id) && isset($user)) { 
            $game = Game::find($id);             
            if ($game && $game->user != $user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthorized'
                ], 401);
            }
            return '';
        } 
        return '';      
    }
}

