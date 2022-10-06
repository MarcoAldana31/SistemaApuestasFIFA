<?php

namespace App\Http\Controllers\Api;

use App\Models\Liga;
use App\Models\LigaUsuarios;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class PremiacionController
{
    public function obtenerPremiacionLigaCentral(Request $request, $id)
    {
        try{
            $ligaCentral = Liga::find($id);

            if(!$ligaCentral){
                throw new Exception('No se pudo encontrar la liga.');
            }

            if ( $ligaCentral->es_liga_plantilla != 1 ){
                throw new Exception('Esta liga no es central');
            }

            $total_recaudado = 0;

            $ligasCopias = $ligaCentral->clonesLigas()
                ->whereIn('estado', [1, 2])
                ->where('tipo_liga', '=', 1) // tipo apuesta
                ->get();
            foreach($ligasCopias as $liga){
                $total_recaudado += doubleval($liga->total_recaudado);
            }

            //---------
            $codigoLigaCentral = $ligaCentral->id;
            //---------
            $ganadores = LigaUsuarios::where('estado', '=', 2) // aprobado
                ->whereHas('liga', function($q) use ($codigoLigaCentral){
                    return $q->whereIn('estado', [1, 2])
                        ->where('tipo_liga', '=', 1)
                        ->where('id_liga', '=', $codigoLigaCentral);
                })
                ->orderBy('total_puntos_obtenidos', 'DESC')
                ->take(4)
                ->get();

            $ganadores_formateados = array();
            $posicion_ganador = 1;
            foreach($ganadores as $ligaUsuario){
                $ganadores_formateados[] = array(
                    'posicion' => $posicion_ganador,
                    'nombre' => $ligaUsuario->usuario->name,
                    'correo' => $ligaUsuario->usuario->email,
                    'total_puntos' => $ligaUsuario->total_puntos_obtenidos
                );

                $posicion_ganador++;
            }


            $data = null;
            $data['liga_nombre'] = $ligaCentral->nombre;
            $data['total_recaudado'] = $total_recaudado;
            $data['ganadores'] = $ganadores_formateados;

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $data,
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
