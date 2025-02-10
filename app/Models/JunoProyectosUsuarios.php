<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JunoProyectosUsuarios extends Model
{
    use HasFactory;
    protected $table = 'juno_proyectos_usuarios';
    public $timestamps = false;
    protected $fillable = [
        'id_usuario',
        'id_proyecto'
    ];
}
