<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PagoUsoPlataforma extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pago_uso_plataforma';

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
        'fecha_pagado',
        'total_pagado',
        'estado', // 0=eliminado, 1=pendiente de pago, 2=pagado
        'id_liga_usuarios',
    ];

    public function ligaUsuarios(): HasOne
    {
        return $this->hasOne('App\Models\LigaUsuarios', 'id', 'id_liga_usuarios');
    }
}
