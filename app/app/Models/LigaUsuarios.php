<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LigaUsuarios extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'liga_usuarios';

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
        'fecha_aprobado',
        'total_puntos_obtenidos',
        'estado', // 0=eliminado, 1=en solicitud, 2=aprobado, 3=rechazado
        'id_liga',
        'id_usuario',
    ];

    public function liga(): HasOne
    {
        return $this->hasOne('App\Models\Liga', 'id', 'id_liga');
    }

    public function usuario(): HasOne
    {
        return $this->hasOne('App\Models\User', 'id', 'id_usuario');
    }

    public function pagoUsoPlataforma(): HasOne
    {
        return $this->hasOne('App\Models\PagoUsoPlataforma', 'id_liga_usuarios', 'id');
    }
}
