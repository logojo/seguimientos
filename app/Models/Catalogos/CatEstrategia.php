<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CatEstrategia extends Model
{
    protected $connection = 'base';
    protected $table = 'catalogos.cat_estrategias';

    protected $fillable = [
        'descripcion',
        'es_activo',
    ];

    public function scopeCatalogo(Builder $query): Builder
    {
        return $query
            ->where('es_activo', true);
    }
}
