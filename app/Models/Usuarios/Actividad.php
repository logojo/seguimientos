<?php

namespace App\Models\Usuarios;

use App\Enums\StatusActividadType;
use App\Models\Catalogos\CatDependencia;
use App\Models\Catalogos\CatEstrategia;
use App\Models\Catalogos\CatLineaAccion;
use App\Models\Catalogos\CatPrograma;
use App\Models\Catalogos\CatUnidadMedida;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    protected $connection = 'base';
    protected $table = 'usuarios.actividades';

    protected $fillable = [
        'actividad',
        'objetivo',
        'avance',
        'status',
        'user_assignment_id',
        'programa_id',
        'dependencia_id',
        'unidad_medida_id',
        'estrategia_id',
        'linea_accion_id',
    ];

    protected $casts = [
        'status' => StatusActividadType::class,
    ];

    public function user()
    {
        return $this->belongsTo(UserAssignment::class, 'user_assignment_id');
    } 

    public function programa()
    {
        return $this->belongsTo(CatPrograma::class, 'programa_id');
    } 

    public function dependencia()
    {
        return $this->belongsTo(CatDependencia::class, 'dependencia_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(CatUnidadMedida::class, 'unidad_medida_id');
    } 

    public function estrategia()
    {
        return $this->belongsTo(CatEstrategia::class, 'estrategia_id');
    } 

    public function lineaAccion()
    {
        return $this->belongsTo(CatLineaAccion::class, 'linea_accion_id');
    }  

}
