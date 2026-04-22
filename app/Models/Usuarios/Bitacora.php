<?php

namespace App\Models\Usuarios;

use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    protected $connection = 'base';
    protected $table = 'usuarios.bitacoras';
    protected $fillable = ['accion','campo','valor_anterior','valor_actual','user_assignment_id'];

    public function storeable()
    {
        return $this->morphedTo();
    }

    public function user()
    {
        return $this->belongsTo(UserAssignment::class, 'user_assignment_id');
    }
}
