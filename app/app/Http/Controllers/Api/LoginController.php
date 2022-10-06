<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Throwable;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        try{
            if ( !Auth::attempt(['name' => $request->username, 'password' => $request->password], false)){
                throw new Exception('Credenciales incorrectas.');
            }

            $usuario = Auth::user();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $usuario,
            ], Response::HTTP_OK);
        }catch(Exception|Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
