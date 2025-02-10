<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JunoProyectosReporte extends Model
{
    use HasFactory;
    protected $table = 'juno_proyectos_reporte';
    public $timestamps = false;
    protected $fillable = [
        'titulo',
        'activo',
        'fecha_ejecucion',
        'ubicacion_file',
        'id_proyecto',
        'id_usuario',
        'fecha_registro'
    ];
}
