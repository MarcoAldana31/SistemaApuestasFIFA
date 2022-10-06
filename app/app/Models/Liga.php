<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Liga extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'liga';

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
        'es_liga_plantilla', // 0=no, 1=si
        'nombre',
        'tipo_liga', // 1=tipo apuesta, 2=diversion
        'total_recaudado',
        'estado', // 0=eliminado, 1=activo, 2=finalizada
        'id_liga',
        'id_usuario_administrador',
        'id_sede',
    ];

    public function ligaCentral(): HasOne
    {
        return $this->hasOne('App\Models\Liga', 'id', 'id_liga');
    }

    public function clonesLigas(): HasMany
    {
        return $this->hasMany('App\Models\Liga', 'id_liga', 'id');
    }
}
