<?php

namespace App\Livewire\Pages\Seguimientos;

use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class Seguimientos extends Component
{

    public $columns = [];

    public function mount() {
        $this->columns = [               
                'status' => [
                    'label'    => 'Estatus del registro',
                    'column'   => 'status',
                    'sortable' => true,
                    'width'    => '110px',
                    'badge'    => [
                        'Validada'  => 'pill-green',
                        'Pendiente' => 'pill-gray',
                        'Observada' => 'pill-yellow',
                    ],
                ],
                'actividad' => [
                    'label'    => 'Actividad',
                    'column'   => 'actividad',
                    'sortable' => true,
                ],
                'programa' => [
                    'label'    => 'Programa',
                    'relation' => 'programa.descripcion',
                    'sortable' => true,
                ],
                'dependencia' => [
                    'label'    => 'Dependencia',
                    'relation' => 'dependencia.descripcion',
                    'sortable' => true,
                ],
                'avance' => [
                    'label'    => 'Avance',
                    'column'   => 'avance',
                    'sortable' => false,
                    'progress' => true,
                ],
                // 'updated' => [
                //     'label'    => 'Actualizado',
                //     'sortable' => false,
                //     'render'   => function($row) {
                //         $created = Carbon::parse( $row->created_at) ;
                //         $updated = Carbon::parse( $row->updated_at );

                //        return $updated > $created
                //        ? "<span class='material-symbols-outlined'>warning</span>" 
                //        : "";
                //     }
                // ]
                // 'status' => [
                //     'label'   => 'estado',
                //     'column'  => 'status',
                //     'boolean' => [
                //         'true'  => ['label' => 'Activo',   'class' => 'badge-success'],
                //         'false' => ['label' => 'Inactivo', 'class' => 'badge-error'],
                //     ],
                // ],
                //*ejemplos de conlunas con relaciones y fechas
                // 'especialidad' => [
                //     'label'    => 'Especialidad',
                //     'relation' => 'especialidad.nombre',
                //     'sortable' => false,
                // ],
                // 'medico' => [
                //     'label'    => 'Médico',
                //     'relation' => 'medico.nombre_completo',
                //     'sortable' => false,
                // ],
                // 'fecha' => [
                //     'label'    => 'Fecha',
                //     'column'   => 'fecha_consulta',
                //     'sortable' => true,
                //     'width'    => '120px',
                //     'render'   => fn($row) => $row->fecha_consulta->format('d/m/Y'),
                // ],
                // 'estado' => [
                //     'label'    => 'Estado',
                //     'column'   => 'estado',
                //     'sortable' => true,
                //     'width'    => '110px',
                //     'badge'    => [
                //         'Completada' => 'badge-success',
                //         'Pendiente'  => 'badge-warning',
                //         'Cancelada'  => 'badge-error',
                //         'En proceso' => 'badge-info',
                //     ],
                // ],   
                // En la definición de columnas con relacion sobre relacion
                // 'especialidad_medico' => [
                //     'label'    => 'Especialidad del médico',
                //     'sortable' => false,                      // no se puede ordenar fácilmente
                //     'render'   => fn($row) => $row->medico?->especialidad?->nombre ?? '—',
                // ],            
        ];
    }    
    
    public function render()
    {
        return view('livewire.pages.seguimientos.seguimientos');
    }
}
