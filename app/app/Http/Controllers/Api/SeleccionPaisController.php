<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\Liga;
use App\Models\SeleccionPais;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class SeleccionPaisController extends Controller
{
/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $datos = SeleccionPais::where('estado', '=', 1)
                ->where('nombre_pais', 'LIKE', '%' . $request->nombre . '%')
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
            'nombre_pais' => 'required|max:50',
            'id_liga' => 'required|numeric',
            'id_grupo' => 'required|numeric',
        ]);

        try{
            $liga = Liga::find($request->id_liga);
            if(!$liga){
                throw new Exception('No existe la liga indicada');
            }

            $grupo = Grupo::find($request->id_grupo);
            if(!$grupo){
                throw new Exception('No existe el grupo indicado');
            }

            if ( $grupo->id_liga != $request->id_liga ){
                throw new Exception('El grupo indicada, no pertenece a la liga.');
            }

            $seleccionPais = new SeleccionPais();
            $seleccionPais->nombre_pais = $request->nombre_pais;
            $seleccionPais->estado = 1;
            $seleccionPais->id_liga = $request->id_liga;
            $seleccionPais->id_grupo = $request->id_grupo;
            $seleccionPais->save();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $seleccionPais,
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
            $grupo = SeleccionPais::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$grupo){
                throw new Exception('No se pudo encontrar la selección.');
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
            'nombre_pais' => 'required|max:50',
            'id_liga' => 'required|numeric',
            'id_grupo' => 'required|numeric',
        ]);

        try{
            $seleccionPais = SeleccionPais::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$seleccionPais){
                throw new Exception('No se pudo encontrar el grupo.');
            }

            $liga = Liga::find($request->id_liga);
            if(!$liga){
                throw new Exception('No existe la liga indicada');
            }

            $grupo = Grupo::find($request->id_grupo);
            if(!$grupo){
                throw new Exception('No existe el grupo indicado');
            }

            if ( $grupo->id_liga != $request->id_liga ){
                throw new Exception('El grupo indicada, no pertenece a la liga.');
            }

            $seleccionPais->nombre_pais = $request->nombre_pais;
            $seleccionPais->id_liga = $request->id_liga;
            $seleccionPais->id_grupo = $request->id_grupo;
            $seleccionPais->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $seleccionPais,
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
            $seleccionPais = SeleccionPais::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$seleccionPais){
                throw new Exception('No se pudo encontrar la selección.');
            }

            $seleccionPais->estado = 0;
            $seleccionPais->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $seleccionPais,
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
