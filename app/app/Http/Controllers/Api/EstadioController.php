<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estadio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class EstadioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $datos = Estadio::where('estado', '=', 1)
                ->where('nombre', 'LIKE', '%' . $request->nombre . '%')
                ->take(15)
                ->get();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $datos,
            ], Response::HTTP_OK);
        }catch(Exception|Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'nombre' => 'required|max:50',
        ]);

        try{
            $estadio = new Estadio();
            $estadio->nombre = $request->nombre;
            $estadio->estado = 1;
            $estadio->save();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $estadio,
            ], Response::HTTP_CREATED);
        }catch(Exception|Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $estadio = Estadio::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$estadio){
                throw new Exception('No se pudo encontrar el estadio.');
            }

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $estadio,
            ], Response::HTTP_OK);
        }catch(Exception|Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nombre' => 'required|max:50',
        ]);

        try{
            $estadio = Estadio::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$estadio){
                throw new Exception('No se pudo encontrar el estadio.');
            }

            $estadio->nombre = $request->nombre;
            $estadio->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $estadio,
            ], Response::HTTP_OK);
        }catch(Exception|Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $estadio = Estadio::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$estadio){
                throw new Exception('No se pudo encontrar el estadio.');
            }

            $estadio->estado = 0;
            $estadio->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $estadio,
            ], Response::HTTP_NO_CONTENT);
        }catch(Exception|Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
