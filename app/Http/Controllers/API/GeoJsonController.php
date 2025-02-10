<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\ApmCatIsumos;
use App\Models\JunoProyectosReporte;
use Illuminate\Support\Facades\Storage;
use App\Models\ApmEntradasSalidasRelacion;
use App\Utils\MailSend;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\File;
use ZipArchive;
date_default_timezone_set('America/Mexico_City');
set_time_limit(0);
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '0');
ini_set('max_execution_time', '0');
ini_set('max_input_time', '0');
ini_set('memory_limit', '-1');


class GeoJsonController extends BaseController
{

    public function deleteReporte(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('El id del reporte es requerido', $validator->errors());
            }
            $reporte = JunoProyectosReporte::where('id', $request->id)->get()->first();
            if(!$reporte){
                return $this->sendError('El reporte especificado no existe', [], 404);
            }
            $reporte->activo = 0;
            $reporte->save();
            return $this->sendResponse('Exito al desactivar el reporte');
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


    public function setReporte(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required',
                'fecha_ejecucion' => 'required',
                'id_proyecto' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los parametros son requeridos', $validator->errors());
            }
            $user = Auth::user();
            $reporteInsert['titulo'] = $request->titulo;
            $reporteInsert['ubicacion_file'] = '';
            $reporteInsert['id_proyecto'] = $request->id_proyecto;
            $reporteInsert['id_usuario'] = $user->id;
            $reporteInsert['activo'] = 1;
            $reporteInsert['fecha_ejecucion'] = $request->fecha_ejecucion;
            $reporteInsert['fecha_registro'] = now();
            $reporte = JunoProyectosReporte::create($reporteInsert);
            /* agrega documento */
            $file = $request->file('zip_file');
            $fileName = $reporte->id.'.zip';
            $filePath = storage_path("app/documentos/juno/".$request->id_proyecto."/".$reporte->id."/zip/" . $fileName);
            $file->move(storage_path("app/documentos/juno/".$request->id_proyecto."/".$reporte->id."/zip"), $fileName);
            $zip = new ZipArchive;
            if ($zip->open($filePath) === TRUE) {
                $extractPath = storage_path("app/documentos/juno/" . $request->id_proyecto . "/" . $reporte->id . "/unzipped");
                $zip->extractTo($extractPath);
                $zip->close();
                $subfolders = File::directories($extractPath);
                if (!empty($subfolders)) {
                    $subfolderName = basename($subfolders[0]);
                    $gdbPath = storage_path("app/documentos/juno/" . $request->id_proyecto . "/" . $reporte->id."/unzipped/". $subfolderName );
                    $command = "ogrinfo -ro -so \"$gdbPath\"";
                    $output = shell_exec($command);
                    preg_match_all('/Layer:\s+(.+?)\s+\(/', $output, $matches);
                    $layers = $matches[1];
                    $ata = [];
                    foreach ($layers as $layer) {
                        $outputPath = storage_path("app/documentos/juno/{$request->id_proyecto}/{$reporte->id}/{$layer}.json");
                        $command = "ogr2ogr -f GeoJSON \"$outputPath\" \"$gdbPath\" \"$layer\"";
                        $result = shell_exec($command);
                    }
                }
            }
            $reporte->ubicacion_file = "app/documentos/juno/" . $request->id_proyecto . "/" . $reporte->id;
            $reporte->save();
            return $this->sendResponse($reporte);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


    public function getReporteCategoria(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'id_proyecto' => 'required',
                'layer' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los parametros son requeridos', $validator->errors());
            }
            $outputPath = storage_path("app/documentos/juno/{$request->id_proyecto}/{$request->id}/{$request->layer}.json");
            $data['path'] = $outputPath;
            $data['nombre_capa'] = $layer;
            $geoJson = file_get_contents($outputPath);
            $data['json'] = json_decode($geoJson);
            $ata[] = $data;
            return $this->sendResponse($ata);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
    }


    public function listarArchivos()
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'id_proyecto' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Todos los parametros son requeridos', $validator->errors());
            }
            $ruta = storage_path("app/documentos/juno/{$request->id_proyecto}/{$request->id}");
            $archivos = File::files($ruta); // Obtener lista de archivos
            $nombres = array_map(function ($archivo) {
                return $archivo->getFilename(); // Extraer solo el nombre
            }, $archivos);
            return response()->json($nombres);
        } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        }
        
}


    public function convierteGeoJson(Request $request)
    {
        /* try { */
            $gdbPath = storage_path('app/documentos/data/Bses_PpB.gdb');
            // Obtener la lista de capas
            $command = "ogrinfo -ro -so \"$gdbPath\"";  // Solo lectura, solo capas
            $output = shell_exec($command);
            // Procesar la salida para extraer las capas
            preg_match_all('/Layer:\s+(.+?)\s+\(/', $output, $matches);
            $layers = $matches[1];  // Contiene los nombres de las capas
            // return $this->sendResponse($layers);
            // Iterar sobre cada capa y convertirla a GeoJSON
            $ata = [];
            foreach ($layers as $layer) {
                $outputPath = storage_path("app/documentos/data/{$layer}.json");
                $command = "ogr2ogr -f GeoJSON \"$outputPath\" \"$gdbPath\" \"$layer\"";
                // Ejecutar el comando
                $result = shell_exec($command);
                // Verificar si la capa fue exportada correctamente
                if($layer === 'Municipios'){
                    $data['path'] = $outputPath;
                    $data['nombre_capa'] = $layer;
                    $geoJson = file_get_contents($outputPath);
                    $data['json'] = json_decode($geoJson);
                    $ata[] = $data;
                }
            }
            return $this->sendResponse($ata);
        /* } catch (\Throwable $th) {
            return $this->sendError('Error', $th, 500);
        } */

    }


}