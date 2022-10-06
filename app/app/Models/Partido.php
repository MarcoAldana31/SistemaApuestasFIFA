<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Partido extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'partido';

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
        'tipo_partido', // 1=clasificacion, 2=octavos, 3=cuartos, 4=semifinal, 5=tercer y cuarto puesto, 6=final
        'fecha_partido',
        'hora_inicio',
        'goles_seleccion_a',
        'goles_seleccion_b',
        'estado', // 0=eliminado, 1=programado, 2=finalizado
        'id_liga',
        'id_estadio',
        'id_grupo',
        'id_seleccion_pais_a',
        'id_seleccion_pais_b',
    ];

    public function liga(): HasOne
    {
        return $this->hasOne('App\Models\Liga', 'id', 'id_liga');
    }

    public function vaticinios(): HasMany
    {
        return $this->hasMany('App\Models\Vaticinio', 'id_partido', 'id');
    }
}
