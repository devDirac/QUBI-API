<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\JunoProyectos;
use App\Models\JunoProyectosUsuarios;
use App\Models\JunoProyectosReporte;

date_default_timezone_set('America/Mexico_City');
set_time_limit(0);
ini_set('upload_max_filesize', '0');
ini_set('post_max_size', '0');
ini_set('max_execution_time', '0');
ini_set('max_input_time', '0');
ini_set('memory_limit', '-1');

class ProyectosController extends BaseController
{

    /* Obtiene todos los proyectos asociados al id del usuario de los parametros */
    public function getAllProyectos(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('El id de usuario es requerido', $validator->errors());
            }
            $proyectos = JunoProyectos::join('juno_proyectos_usuarios', 'juno_proyectos.id', '=', 'juno_proyectos_usuarios.id_proyecto')
            ->select('juno_proyectos.*')
            ->where('juno_proyectos_usuarios.id_usuario', $request->id_usuario)
            ->where('juno_proyectos.activo', 1)
            ->orderBy('id', 'asc')
            ->get();
            foreach ($proyectos as $key => $value) {
                $usuarios = JunoProyectosUsuarios::where('id_proyecto', $value->id)->get();
                $proyectos[$key]->usuarios = $usuarios;
                $reportes = JunoProyectosReporte::where('id_proyecto', $value->id)->where('activo', 1)->get();
                $proyectos[$key]->reportes = $reportes;
            }
            return $this->sendResponse($proyectos);
        } catch (\Throwable $th) {
            return $this->sendError('Error al registrar el usuario', $th, 500);
        }
    }

    /* Guarda un nuevo documento */
    public function storeProyecto(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required',
                'description' => 'required',
                'info' => 'required',
                'ubicacion' => 'required',
                'lat' => 'required',
                'lon' => 'required',
                'file' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los parametros son requeridos', $validator->errors());
            }
            $user = Auth::user();
            $file = $request->file('file');
            $proyectoInsert['imagen'] = '';
            $proyectoInsert['titulo'] = $request->titulo;
            $proyectoInsert['description'] = $request->description;
            $proyectoInsert['info'] = $request->info;
            $proyectoInsert['ubicacion'] = $request->ubicacion;
            $proyectoInsert['lat'] = $request->lat;
            $proyectoInsert['lon'] = $request->lon;
            $proyectoInsert['id_usuario'] = $user->id;
            $proyectoInsert['fecha_registro'] = now();
            $proyectoInsert['activo'] = 1;
            $proyecto = JunoProyectos::create($proyectoInsert);
            /* agrega documento */
            $extension = $file->extension();
            $nombre_archivo = preg_replace('/\s+/', '', $proyecto->id) . '.' . $extension;
            $file->storeAs("documentos/juno", $nombre_archivo);
            asset("documentos/juno/{$nombre_archivo}");
            $ruta = "documentos/juno/{$nombre_archivo}";
            $proyecto->imagen = $ruta;
            $proyecto->save();
            /* Asocia el proyecto al usuario que lo crea */
            $proyectoUsuario['id_usuario'] = $user->id;
            $proyectoUsuario['id_proyecto'] = $proyecto->id;
            JunoProyectosUsuarios::create($proyectoUsuario);
            return $this->sendResponse($proyecto);
        } catch (\Throwable $th) {
            return $this->sendError('Error al registrar el usuario', $th, 500);
        }
    }

    /* Actualiza la informacion de un proyecto */
    public function updateProyecto(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required',
                'description' => 'required',
                'info' => 'required',
                'ubicacion' => 'required',
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los parametros son requeridos', $validator->errors());
            }
            $proyecto = JunoProyectos::where('id', $request->id)->get()->first();
            if(!$proyecto){
                return $this->sendError('El proyecto que desea actualizar no existe', [], 404);
            }
            if($request->has('file')){
                if (Storage::exists("{$proyecto->imagen}")) {
                    Storage::delete("{$proyecto->imagen}");
                }
                $file = $request->file('file');
                $extension = $file->extension();
                $nombre_archivo = preg_replace('/\s+/', '', $proyecto->id) . '.' . $extension;
                $file->storeAs("documentos/juno", $nombre_archivo);
                asset("documentos/juno/{$nombre_archivo}");
                $ruta = "documentos/juno/{$nombre_archivo}";
            }else{
                $ruta = $proyecto->imagen;
            }
            $user = Auth::user();
            $proyecto->imagen = $ruta;
            $proyecto->titulo = $request->titulo;
            $proyecto->description = $request->description;
            $proyecto->info = $request->info;
            $proyecto->ubicacion = $request->ubicacion;
            $proyecto->id_usuario = $user->id;
            $proyecto->lat = $request->has('lat') ? $request->lat : $proyecto->lat;
            $proyecto->lon = $request->has('lon') ? $request->lon : $proyecto->lon;
            $proyecto->save();
            return $this->sendResponse($proyecto);
        } catch (\Throwable $th) {
            return $this->sendError('Error al actualizar el proyecto', $th, 500);
        }
    }

    /* Elimina un proyecto */
    public function deleteProyecto(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los parametros son requeridos', $validator->errors());
            }
            $proyecto = JunoProyectos::where('id', $request->id)->get()->first();
            if(!$proyecto){
                return $this->sendError('El proyecto que desea actualizar no existe', [], 404);
            }
            $proyecto->activo = 0;
            $proyecto->save();
            return $this->sendResponse('Exito al eliminar el proyecto');
        } catch (\Throwable $th) {
            return $this->sendError('Error al eliminar el proyecto', $th, 500);
        }
    }

    /* asocia un usuario a un proyecto */
    public function asociaUsuarioProyecto(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required',
                'id_proyecto' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los parametros son requeridos', $validator->errors());
            }
            $proyectoUsuario['id_usuario'] = $request->id_usuario;
            $proyectoUsuario['id_proyecto'] = $request->id_proyecto;
            JunoProyectosUsuarios::create($proyectoUsuario);
            return $this->sendResponse('Exito al asociar el usuario al proyecto');
        } catch (\Throwable $th) {
            return $this->sendError('Error al asociar el usuario al proyecto', $th, 500);
        }
    }

    /* Elimina la asociación de un usuario a un proyecto */
    public function eliminaAsociacionUsuarioProyecto(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id_proyecto' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los parametros son requeridos', $validator->errors());
            }
            $proyectoUsuario = JunoProyectosUsuarios::where('id_proyecto',$request->id_proyecto)->delete();
            return $this->sendResponse('Exito al eliminar la asociacion del usuario al proyecto');
        } catch (\Throwable $th) {
            return $this->sendError('Error al asociar el usuario al proyecto', $th, 500);
        }
    }

}