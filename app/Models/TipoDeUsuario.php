<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDeUsuario extends Model
{
    protected $table = 'tipos_de_usuarios';
    public $timestamps = false;

    public function usuarios()
    {
        return $this->hasMany(User::class, 'tipo_id');
    }
}
