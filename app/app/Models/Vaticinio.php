<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vaticinio extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vaticinio';

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
        'goles_equipo_a',
        'goles_equipo_b',
        'puntos_obtenidos',
        'estado', // 0=eliminado, 1=activo
        'id_liga_usuario',
        'id_partido',
    ];

    public function ligaUsuario(): HasOne
    {
        return $this->hasOne('App\Models\LigaUsuarios', 'id', 'id_liga_usuario');
    }

    public function partido(): HasOne
    {
        return $this->hasOne('App\Models\Partido', 'id', 'id_partido');
    }
}
