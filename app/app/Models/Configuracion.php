<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'configuracion';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'estado',
        'tiempo_limite_vaticinio',
        'puntos_acierto_exactos_vaticinio',
        'puntos_acierto_ganador_vaticinio',
        'puntos_acierto_empate_vaticinio',
    ];
}
