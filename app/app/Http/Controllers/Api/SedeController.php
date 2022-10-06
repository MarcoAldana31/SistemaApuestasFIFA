<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class SedeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $datos = Sede::where('estado', '=', 1)
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
            'pais' => 'required|max:100'
        ]);

        try{
            $sede = new Sede();
            $sede->nombre = $request->nombre;
            $sede->pais = $request->pais;
            $sede->estado = 1;
            $sede->save();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $sede,
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
            $sede = Sede::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$sede){
                throw new Exception('No se pudo encontrar la sede.');
            }

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $sede,
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
            'pais' => 'required|max:100'
        ]);

        try{
            $sede = Sede::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$sede){
                throw new Exception('No se pudo encontrar la sede.');
            }

            $sede->nombre = $request->nombre;
            $sede->pais = $request->pais;
            $sede->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $sede,
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
            $sede = Sede::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$sede){
                throw new Exception('No se pudo encontrar la sede.');
            }

            $sede->estado = 0;
            $sede->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $sede,
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
