<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Liga;
use App\Models\LigaUsuarios;
use App\Models\PagoUsoPlataforma;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mockery\CountValidator\Exact;
use Throwable;

class LigaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try{
            $datos = Liga::whereIn('estado', [1, 2])
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
            'id_usuario' => 'required|numeric',
            'id_sede' => 'required|numeric'
        ]);

        try{
            // CONSIDERACIONES: Cuando se registra una nueva liga desde cero.
            // a) La liga es PLANTILLA, permitiendo que otros usuarios puedan crear su liga a base de esa.
            // b) El tipo de liga sera de TIPO APUESTA
            // c) no tendra referencia otra liga, ya que esta es la central
            // SOLO LA ADMINISTRACION PODRA CREAR LIGAS DE PLANTILLA

            $usuario = User::where('estado', '=', 1)
                ->where('id', '=', $request->id_usuario)
                ->first();

            if(!$usuario){
                throw new Exception('No se pudo reconocer su usuario.');
            }

            if ( $usuario->es_administrador == 0 ){
                throw new Exception('No tienes permiso para registrar la liga.');
            }

            $liga = new Liga();
            $liga->es_liga_plantilla = 1;
            $liga->nombre = $request->nombre;
            $liga->tipo_liga = 1;
            $liga->total_recaudado = 0;
            $liga->estado = 1;
            $liga->id_liga = null;
            $liga->id_usuario_administrador = $request->id_usuario;
            $liga->id_sede = $request->id_sede;
            $liga->save();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $liga,
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
            $liga = Liga::whereIn('estado', [1, 2])
                ->where('id', '=', $id)
                ->first();

            if(!$liga){
                throw new Exception('No se pudo encontrar la liga.');
            }

            // al visualizar, nos encargamos que se vean la liga que es central como plantilla
            // if ( $liga->es_liga_plantilla == 0 ){
            //     $ligaCentral = $liga->ligaCentral;

            //     $liga = $ligaCentral ? $ligaCentral : $liga;
            // }

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $liga,
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
            'tipo_liga' => 'required|numeric',
            'id_usuario' => 'required|numeric',
        ]);

        try{
            $liga = Liga::whereIn('estado', [1, 2])
                ->where('id', '=', $id)
                ->first();

            if(!$liga){
                throw new Exception('No se pudo encontrar la liga.');
            }

            if ( $liga->id_usuario_administrador != $request->id_usuario){
                throw new Exception('Solo el administrador de la liga puede actualizar esta liga.');
            }

            $liga->nombre = $request->nombre;
            $liga->tipo_liga = $request->tipo_liga;

            if ( !in_array($liga->tipo_liga, array(1, 2)) ){
                throw new Exception('El tipo liga indicado es erroneo, permitidos (1) apuesta y (2) diversión.');
            }

            if ( $liga->isDirty('tipo_liga') ){
                if ( $liga->tipo_liga == 2 ){

                    $id_liga = $liga->id;

                    $hayPagos = PagoUsoPlataforma::where('estado', '=', 2)
                        ->whereHas('ligaUsuarios', function($q) use ($id_liga){
                            return $q->where('estado', '=',  2)
                                ->where('id_liga', '=', $id_liga);
                        })
                        ->count();

                    if ( $hayPagos > 0 ){
                        throw new Exception('No puedes pasar esta liga a diversión, ya que hay pagos realizados de los usuarios participantes.');
                    }
                }
            }

            $liga->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $liga,
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
            $liga = Liga::whereIn('estado', [1, 2])
                ->where('id', '=', $id)
                ->first();

            if(!$liga){
                throw new Exception('No se pudo encontrar la liga.');
            }

            if ( $liga->es_liga_plantilla == 1 ){
                throw new Exception('No se puede eliminar una liga de tipo plantilla.');
            }

            $liga->estado = 0;
            $liga->update();

            return response()->json([
                'message' => '',
                'errors' => array(),
                'data' => $liga,
            ], Response::HTTP_NO_CONTENT);
        }catch(Exception|Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function enviarInvitaciones(Request $request, $id)
    {
        $this->validate($request, [
            'usuarios' => 'required|array|min:1',
            'usuarios.*.correo' => 'required|email'
        ]);

        try{
            $liga = Liga::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$liga){
                throw new Exception('La liga no existe o no se encuntra ya activa.');
            }

            if ( $liga->es_liga_plantilla == 1 ){
                throw new Exception('No puedes enviar invitaciones de esta liga.');
            }

            throw new Exception('En desarrollo...');

            return response()->json([
                'message' => 'Correos enviados',
                'errors' => array(),
                'data' => null,
            ], Response::HTTP_OK);
        }catch(Exception|Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function copiarLiga(Request $request, $id)
    {
        $this->validate($request, [
            'id_usuario' => 'required',
            'tipo_liga' => 'required|numeric',
        ]);

        try{
            $liga = Liga::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$liga){
                throw new Exception('La liga no existe o no se encuntra ya activa.');
            }

            if ( $liga->es_liga_plantilla == 0 ){
                throw new Exception('Solo puedes copiar una liga plantilla.');
            }

            if($liga->id_usuario_administrador == $request->id_usuario){
                throw new Exception('No pueds copiar tu propia liga.');
            }

            $ligaCopia = new Liga();
            $ligaCopia->es_liga_plantilla = 0;
            $ligaCopia->nombre = $liga->nombre;
            $ligaCopia->tipo_liga = $request->tipo_liga;
            $ligaCopia->total_recaudado = 0;
            $ligaCopia->estado = 1;
            $ligaCopia->id_liga = $liga->id; // guardamos referencia a la liga central
            $ligaCopia->id_usuario_administrador = $request->id_usuario;
            $ligaCopia->id_sede = $liga->id_sede;

            if ( !in_array($ligaCopia->tipo_liga, array(1, 2)) ){
                throw new Exception('El tipo liga indicado es erroneo, permitidos (1) apuesta y (2) diversión.');
            }

            $ligaCopia->save();

            return response()->json([
                'message' => 'Liga copiada.',
                'errors' => array(),
                'data' => $ligaCopia,
            ], Response::HTTP_CREATED);
        }catch(Exception|Throwable $e){
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => array(),
                'data' => null
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function enviarSolicitud(Request $request, $id)
    {
        $this->validate($request, [
            'id_usuario' => 'required',
        ]);

        try{
            $liga = Liga::where('estado', '=', 1)
                ->where('id', '=', $id)
                ->first();

            if(!$liga){
                throw new Exception('La liga no existe o no se encuntra ya activa.');
            }

            if ( $liga->es_liga_plantilla == 1 ){
                throw new Exception('No puedes enviar invitaciones de esta liga.');
            }

            if ( $liga->id_usuario_administrador == $request->id_usuario ){
                throw new Exception('No puedes enviar solicitud a una liga de tu propiedad.');
            }

            $ligaUsuarioAprobada = LigaUsuarios::where('estado', '=', 2)
                ->where('id_liga', '=', $liga->id)
                ->where('id_usuario', '=', $request->id_usuario)
                ->first();

            if($ligaUsuarioAprobada){
                throw new Exception('Ya te encuentras en esta liga.');
            }

            $ligaUsuario = new LigaUsuarios();
            $ligaUsuario->fecha_aprobado = null;
            $ligaUsuario->total_puntos_obtenidos = 0;
            $ligaUsuario->estado = 1;
            $ligaUsuario->id_liga = $liga->id;
            $ligaUsuario->id_usuario = $request->id_usuario;
            $ligaUsuario->save();

            return response()->json([
                'message' => 'Solicitud enviada.',
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
}
