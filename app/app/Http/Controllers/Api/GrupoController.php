<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $datos = Grupo::where('estado', '=', 1)
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
            'id_liga' => 'required|numeric',
        ]);

        try{
            $grupo = new Grupo();
            $grupo->nombre = $request->nombre;
            $grupo->estado = 1;
            $grupo->id_liga = $request->id_liga;
            $grupo->save();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $grupo,
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
            $grupo = Grupo::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$grupo){
                throw new Exception('No se pudo encontrar el grupo.');
            }

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $grupo,
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
            $grupo = Grupo::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$grupo){
                throw new Exception('No se pudo encontrar el grupo.');
            }

            $grupo->nombre = $request->nombre;
            $grupo->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $grupo,
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
            $grupo = Grupo::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$grupo){
                throw new Exception('No se pudo encontrar el grupo.');
            }

            $grupo->estado = 0;
            $grupo->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $grupo,
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
