<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LigaUsuarios;
use App\Models\PagoUsoPlataforma;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class LigaUsuarioController extends Controller
{
    public function rechazar(Request $request, $id)
    {
        try{
            $ligaUsuario = LigaUsuarios::where('id', '=', $id)
                ->first();

            if (!$ligaUsuario){
                throw new Exception('No se pudo encontrar la solicitud.');
            }

            if ($ligaUsuario->estado != 1){
                throw new Exception('Ya no se puede rechazar la solicitud.');
            }

            $ligaUsuario->estado = 3;
            $ligaUsuario->update();

            return response()->json([
                'message' => 'Solicitud rechazada.',
                'errors' => array(),
                'data' => $ligaUsuario,
            ], Response::HTTP_CREATED);
        }catch(Exception|Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function aprobar(Request $request, $id)
    {
        DB::beginTransaction();

        try{
            $ligaUsuario = LigaUsuarios::where('id', '=', $id)
                ->first();

            if (!$ligaUsuario){
                throw new Exception('No se pudo encontrar la solicitud.');
            }

            if ($ligaUsuario->estado != 1){
                throw new Exception('Ya no se puede aprobar la solicitud.');
            }

            $ligaUsuario->fecha_aprobado = date('Y-m-d');
            $ligaUsuario->estado = 2;
            $ligaUsuario->update();

            $pagoUsoPlataforma = new PagoUsoPlataforma();
            $pagoUsoPlataforma->fecha_pagado = null;
            $pagoUsoPlataforma->total_pagado = 0;
            $pagoUsoPlataforma->estado = 1;
            $pagoUsoPlataforma->id_liga_usuarios = $ligaUsuario->id;
            $pagoUsoPlataforma->save();

            DB::commit();

            return response()->json([
                'message' => 'Solicitud aprobada.',
                'errors' => array(),
                'data' => $ligaUsuario,
            ], Response::HTTP_CREATED);
        }catch(Exception|Throwable $e){
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function pagar(Request $request, $id)
    {
        $this->validate($request, [
            'total_pagado' => 'required|numeric|gt:0'
        ]);

        DB::beginTransaction();

        try{
            $ligaUsuario = LigaUsuarios::where('id', '=', $id)
                ->first();

            if (!$ligaUsuario){
                throw new Exception('No se pudo encontrar la solicitud.');
            }

            if ($ligaUsuario->estado != 2){
                throw new Exception('Solo se puede cobrar si la solicitud fue aprobada.');
            }

            $liga = $ligaUsuario->liga;
            if(!$liga){
                throw new Exception('No se pudo encontrar datos de la liga.');
            }

            if ( $liga->estado != 1 ){
                throw new Exception('La liga ya no se encuentra activa.');
            }

            if ( $liga->tipo_liga != 1 ){
                throw new Exception('Esta liga no es de tipo apuesta, no se puede cobrar la participación del usuario.');
            }

            $pagoUsoPlataforma = $ligaUsuario->pagoUsoPlataforma;
            if(!$pagoUsoPlataforma){
                throw new Exception('No se pudo encontrar la deuda.');
            }

            if ( $pagoUsoPlataforma->estado != 1 ){
                throw new Exception('Se ha detectado que el pago ya no se encuentra pendiente.');
            }

            $pagoUsoPlataforma->fecha_pagado = date('Y-m-d');
            $pagoUsoPlataforma->total_pagado = $request->total_pagado;
            $pagoUsoPlataforma->estado = 2;
            $pagoUsoPlataforma->update();

            $liga->total_recaudado += $pagoUsoPlataforma->total_pagado;
            $liga->update();

            DB::commit();

            return response()->json([
                'message' => 'Cobro realizado.',
                'errors' => array(),
                'data' => $ligaUsuario,
            ], Response::HTTP_CREATED);
        }catch(Exception|Throwable $e){
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
