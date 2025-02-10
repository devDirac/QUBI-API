<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JunoProyectos extends Model
{
    use HasFactory;
    protected $table = 'juno_proyectos';
    public $timestamps = false;
    protected $fillable = [
        'imagen',
        'titulo',
        'description',
        'info',
        'ubicacion',
        'lat',
        'lon',
        'activo',
        'id_usuario',
        'fecha_registro'
    ];
}
