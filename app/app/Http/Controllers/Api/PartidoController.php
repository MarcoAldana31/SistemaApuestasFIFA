<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use App\Models\Estadio;
use App\Models\Liga;
use App\Models\Partido;
use App\Models\SeleccionPais;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Throwable;

class PartidoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $datos = Partido::whereIn('estado', [1, 2])
                ->paginate(15);

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
            'tipo_partido' => 'required|numeric',
            'fecha_partido' => 'required|date_format:Y-m-d',
            'hora_inicio' => 'required|date_format:H:i:s',
            'goles_seleccion_a' => 'required|numeric|gte:0',
            'goles_seleccion_b' => 'required|numeric|gte:0',
            'id_liga' => 'required|numeric',
            'id_estadio' => 'required|numeric',
            'id_seleccion_pais_a' => 'required|numeric',
            'id_seleccion_pais_b' => 'required|numeric',
            'estado' => 'required|numeric',
        ]);

        try{
            $partido = new Partido();

            if ( !in_array($request->tipo_partido, array(1, 2, 3, 4, 5, 6))){
                throw new Exception('El tipo de partido indicado no está permitido.');
            }

            if ( $request->id_seleccion_pais_a == $request->id_seleccion_pais_b ){
                throw new Exception('Deben indicarse diferentes selecciones para el partido.');
            }

            if ( !in_array($request->estado, array(1, 2))){
                throw new Exception('El estado del partido indicado no está permitido.');
            }

            $liga = Liga::find($request->id_liga);
            if(!$liga){
                throw new Exception('No se pudo encontrar la liga.');
            }

            if ($liga->estado != 1){
                throw new Exception('La liga ya no se encuentra activa.');
            }

            if ( $liga->es_liga_plantilla == 0  && $liga->id_liga != null ){
                throw new Exception('Esta liga no es la central.');
            }

            $estadio = Estadio::find($request->id_estadio);
            if(!$estadio){
                throw new Exception('No se pudo encontrar el estadio.');
            }

            $seleccionEquipoA = SeleccionPais::find($request->id_seleccion_pais_a);
            if(!$seleccionEquipoA){
                throw new Exception('No se pudo encontrar la selección A.');
            }

            $seleccionEquipoB = SeleccionPais::find($request->id_seleccion_pais_b);
            if(!$seleccionEquipoB){
                throw new Exception('No se pudo encontrar la selección b.');
            }

            $partido->tipo_partido = $request->tipo_partido;
            $partido->fecha_partido = $request->fecha_partido;
            $partido->hora_inicio = $request->hora_inicio;
            $partido->goles_seleccion_a = $request->goles_seleccion_a;
            $partido->goles_seleccion_b = $request->goles_seleccion_b;
            $partido->estado = $request->estado;
            $partido->id_liga = $request->id_liga;
            $partido->id_estadio = $request->id_estadio;
            $partido->id_grupo = $partido->tipo_partido == 1 ? $seleccionEquipoA->id_grupo : null;
            $partido->id_seleccion_pais_a = $request->id_seleccion_pais_a;
            $partido->id_seleccion_pais_b = $request->id_seleccion_pais_b;
            $partido->save();

            return response()->json([
                'message' => 'Partido registrado.',
                'errors' => array(),
                'data' => $partido,
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
            $partido = Partido::whereIn('estado', [1, 2])
                ->where('id', '=', $id)
                ->first();

            if(!$partido){
                throw new Exception('No se pudo encontrar el partido.');
            }

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $partido,
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
        DB::beginTransaction();

        $this->validate($request, [
            'tipo_partido' => 'required|numeric',
            'fecha_partido' => 'required|date_format:Y-m-d',
            'hora_inicio' => 'required|date_format:H:i:s',
            'goles_seleccion_a' => 'required|numeric|gte:0',
            'goles_seleccion_b' => 'required|numeric|gte:0',
            'id_liga' => 'required|numeric',
            'id_estadio' => 'required|numeric',
            'id_seleccion_pais_a' => 'required|numeric',
            'id_seleccion_pais_b' => 'required|numeric',
            'estado' => 'required|numeric',
        ]);

        try{
            $partido = Partido::whereIn('estado', [1, 2])
                ->where('id', '=', $id)
                ->first();

            if(!$partido){
                throw new Exception('No se pudo encontrar el partido.');
            }

            if ( $partido->estado == 2 ){
                throw new Exception('El partido ya ha sido marcado como finalizado, no puede modificarse.');
            }

            if ( !in_array($request->tipo_partido, array(1, 2, 3, 4, 5, 6))){
                throw new Exception('El tipo de partido indicado no está permitido.');
            }

            if ( $request->id_seleccion_pais_a == $request->id_seleccion_pais_b ){
                throw new Exception('Deben indicarse diferentes selecciones para el partido.');
            }

            if ( !in_array($request->estado, array(1, 2))){
                throw new Exception('El estado del partido indicado no está permitido.');
            }

            $liga = Liga::find($request->id_liga);
            if(!$liga){
                throw new Exception('No se pudo encontrar la liga.');
            }

            if ($liga->estado != 1){
                throw new Exception('La liga ya no se encuentra activa.');
            }

            if ( $liga->es_liga_plantilla == 0  && $liga->id_liga != null ){
                throw new Exception('Esta liga no es la central.');
            }

            $estadio = Estadio::find($request->id_estadio);
            if(!$estadio){
                throw new Exception('No se pudo encontrar el estadio.');
            }

            $seleccionEquipoA = SeleccionPais::find($request->id_seleccion_pais_a);
            if(!$seleccionEquipoA){
                throw new Exception('No se pudo encontrar la selección A.');
            }

            $seleccionEquipoB = SeleccionPais::find($request->id_seleccion_pais_b);
            if(!$seleccionEquipoB){
                throw new Exception('No se pudo encontrar la selección b.');
            }

            $partido->tipo_partido = $request->tipo_partido;
            $partido->fecha_partido = $request->fecha_partido;
            $partido->hora_inicio = $request->hora_inicio;
            $partido->goles_seleccion_a = $request->goles_seleccion_a;
            $partido->goles_seleccion_b = $request->goles_seleccion_b;
            $partido->estado = $request->estado;
            $partido->id_liga = $request->id_liga;
            $partido->id_estadio = $request->id_estadio;
            $partido->id_grupo = $partido->tipo_partido == 1 ? $seleccionEquipoA->id_grupo : null;
            $partido->id_seleccion_pais_a = $request->id_seleccion_pais_a;
            $partido->id_seleccion_pais_b = $request->id_seleccion_pais_b;
            $partido->update();

            // --------------------------------------------------------
            // Proceso para distribuir puntos
            if ( $partido->estado == 2 ){
                $configuracion = Configuracion::first();
                if(!$configuracion){
                    throw new Exception('No se pudo obtener la configuración establecida.');
                }

                $vaticinios = $partido->vaticinios()
                    ->where('estado', '=', 1)
                    ->get();

                foreach($vaticinios as $vaticinio){
                    $ganoPuntos = false;
                    // si el vaticinio fue exacto
                    if (
                        $vaticinio->goles_equipo_a == $partido->goles_seleccion_a &&
                        $vaticinio->goles_equipo_b == $partido->goles_seleccion_b
                    ){
                        $vaticinio->puntos_obtenidos = $configuracion->puntos_acierto_exactos_vaticinio;
                        $ganoPuntos = true;
                    }else{
                        // si el vaticinio no fue exacto pero gano el equipo que predico ganar
                        if ( $vaticinio->goles_equipo_a > $vaticinio->goles_equipo_a && $partido->goles_seleccion_a > $partido->goles_seleccion_b){
                            $vaticinio->puntos_obtenidos = $configuracion->puntos_acierto_ganador_vaticinio;
                            $ganoPuntos = true;
                        // o empate
                        }else if ( $partido->goles_seleccion_a == $partido->goles_seleccion_b ){
                            // TODO: No hay seguridad si aplica si se queda empate, ya que no se predico empate, y no hay ganador
                            // $vaticinio->puntos_obtenidos = $configuracion->puntos_acierto_empate_vaticinio;
                        }
                    }

                    // una vez aplicado si gano puntos, se suman al punteo general del usuario en su participacion
                    if ($ganoPuntos){
                        $vaticinio->update();

                        $ligaUsuarioParticipacion = $vaticinio->ligaUsuario;
                        if($ligaUsuarioParticipacion){

                            $ligaUsuarioParticipacion->total_puntos_obtenidos += ($vaticinio->puntos_obtenidos);
                            $ligaUsuarioParticipacion->update();
                        }
                    }
                }
            }

            // --------------------------------------------------------


            DB::commit();
            return response()->json([
                'message' => 'Partido actualizado',
                'errors' => array(),
                'data' => $partido,
            ], Response::HTTP_OK);
        }catch(Exception|Throwable $e){
            DB::rollBack();

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
            $partido = Partido::where('id', '=', $id)
                ->first();

            if(!$partido){
                throw new Exception('No se pudo encontrar el partido.');
            }

            if($partido->estado != 1 ){
                throw new Exception('No se puede eliminar el partido ya que, ya no se encuentra programado.');
            }

            $partido->estado = 0;
            $partido->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $partido,
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
