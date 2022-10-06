<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\LigaUsuarios;
use App\Models\Partido;
use App\Models\Vaticinio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Throwable;

class VaticinioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
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
            'goles_equipo_a' => 'required|numeric|gte:0',
            'goles_equipo_b' => 'required|numeric|gte:0',
            'id_liga_usuario' => 'required|numeric',
            'id_partido' => 'required|numeric',
        ]);

        try{
            $ligaUsuario = LigaUsuarios::find($request->id_liga_usuario);
            if(!$ligaUsuario){
                throw new Exception('No se encontró la solicitud de participación de esta liga.');
            }

            $partido = Partido::find($request->id_partido);
            if(!$partido){
                throw new Exception('No se encontró el partido.');
            }

            if ( $partido->estado != 1){
                throw new Exception('El partido ya no se encuentra programado.');
            }

            $liga = $ligaUsuario->liga;
            if(!$liga){
                throw new Exception('No se pudo encontrar la liga origen a la que estara asociada el vaticinio.');
            }

            if ( $liga->id_liga != $partido->id_liga ){
                throw new Exception('El partido al que esta apuntando realizar apuesta, no corresponde a la liga asignada en que está participando.');
            }

            $configuracion = Configuracion::first();
            if(!$configuracion){
                throw new Exception('No se pudo obtener la configuración establecida.');
            }

            $fecha_actual = date('Y-m-d');

            if ( $fecha_actual < $partido->fecha_partido ){
                // no hay problema
            }else if ( $fecha_actual == $partido->fecha_partido ){
                $hora_actual = strtotime(date('H:i:s'));
                $hora_inicio = strtotime($partido->hora_inicio);

                $hora_diferencia = $hora_inicio - $hora_actual;

                if ( $hora_diferencia >= $configuracion->tiempo_limite_vaticinio){
                    throw new Exception('Ya no puede registrar el vaticinio, fuera de límite permitido.');
                }
            }else{
                throw new Exception('Ya no puede registrar el vaticinio, ya que el partido ya ha transcurrido el día ' . $partido->fecha_partido . ' a las ' . $partido->hora_inicio);
            }

            $vaticinio = new Vaticinio();
            $vaticinio->goles_equipo_a = $request->goles_equipo_a;
            $vaticinio->goles_equipo_b = $request->goles_equipo_b;
            $vaticinio->puntos_obtenidos = 0;
            $vaticinio->estado = 1;
            $vaticinio->id_liga_usuario = $request->id_liga_usuario;
            $vaticinio->id_partido = $request->id_partido;
            $vaticinio->save();

            return response()->json([
                'message' => 'Vaticinio registrado.',
                'errors' => array(),
                'data' => $vaticinio,
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
            $vaticinio = Vaticinio::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$vaticinio){
                throw new Exception('No se pudo encontrar el vaticinio.');
            }

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $vaticinio,
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
            'goles_equipo_a' => 'required|numeric|gte:0',
            'goles_equipo_b' => 'required|numeric|gte:0',
        ]);

        try{
            $vaticinio = Vaticinio::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$vaticinio){
                throw new Exception('No se pudo encontrar el vaticinio.');
            }

            $partido = $vaticinio->partido;
            if(!$partido){
                throw new Exception('No se encontró el partido.');
            }

            if ( $partido->estado != 1){
                throw new Exception('El partido ya no se encuentra programado.');
            }

            $configuracion = Configuracion::first();
            if(!$configuracion){
                throw new Exception('No se pudo obtener la configuración establecida.');
            }

            $fecha_actual = date('Y-m-d');

            if ( $fecha_actual < $partido->fecha_partido ){
                // no hay problema
            }else if ( $fecha_actual == $partido->fecha_partido ){
                $hora_actual = strtotime(date('H:i:s'));
                $hora_inicio = strtotime($partido->hora_inicio);

                $hora_diferencia = $hora_inicio - $hora_actual;

                if ( $hora_diferencia >= $configuracion->tiempo_limite_vaticinio){
                    throw new Exception('Ya no puede registrar el vaticinio, fuera de límite permitido.');
                }
            }else{
                throw new Exception('Ya no puede registrar el vaticinio, ya que el partido ya ha transcurrido el día ' . $partido->fecha_partido . ' a las ' . $partido->hora_inicio);
            }

            $vaticinio->goles_equipo_a = $request->goles_equipo_a;
            $vaticinio->goles_equipo_b = $request->goles_equipo_b;
            $vaticinio->update();

            return response()->json([
                'message' => 'Vaticinio actualizado',
                'errors' => array(),
                'data' => $vaticinio,
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
            $vaticinio = Vaticinio::where('id', '=', $id)
                ->first();

            if ( $vaticinio->estado != 1 ){
                throw new Exception('El vaticinio ya no se encuentra activo.');
            }

            $partido = $vaticinio->partido;

            if(!$partido){
                throw new Exception('No se pudo encontrar la referencia del partido.');
            }

            if ( $partido->estado == 2){
                throw new Exception('No se puede eliminar el vaticinio ya que el partido ya se finalizó.');
            }

            $vaticinio->estado = 0;
            $vaticinio->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $vaticinio,
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
