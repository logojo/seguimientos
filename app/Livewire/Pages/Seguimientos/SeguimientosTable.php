<?php

namespace App\Livewire\Pages\Seguimientos;

use App\Livewire\Shared\DataTable;
use App\Support\DataTable\Columns\ActionsColumn;
use App\Support\DataTable\Columns\BadgeColumn;
use App\Support\DataTable\Columns\IconColumn;
use App\Support\DataTable\Columns\ProgressColumn;
use App\Support\DataTable\Columns\RelationColumn;
use App\Support\DataTable\Columns\TextColumn;
use Carbon\Carbon;

class SeguimientosTable extends DataTable
{
    public string $title   = 'Actividades';
    public int    $perPage = 10;

    protected function model(): string
    {
       return \App\Models\Usuarios\Actividad::class;
    }

    protected function with(): array
    {
        return ['programa', 'dependencia', 'unidadMedida', 'estrategia', 'lineaAccion'];
    }

    protected function searchable(): array
    {
        return ['updated_at','status','actividad', 'programa', 'dependencia'];
    }

    protected function columns(): array
    {
        return [

            BadgeColumn::make('status')
                ->label('Estatus de registro')
                ->type('enum')
                ->colors([
                    'Validada'  => 'pill-green text-xs',
                    'Pendiente' => 'pill-gray text-xs',
                    'Observada' => 'pill-yellow text-xs',
                ]),
            
            IconColumn::make('')
                 ->type('icon'),

            TextColumn::make('actividad')
                ->label('Actividad')
                ->type('string')
                ->sortable(),

            RelationColumn::make('programa')
                ->relation('programa.descripcion')
                ->sortable(),
                
                
            RelationColumn::make('dependencia')
                ->relation('dependencia.descripcion')
                ->sortable(),
                
            ProgressColumn::make('avance')
                ->label('Avance %')
               ->type('numeric'),

            ActionsColumn::make('actions')
                ->label('Acciones')
                ->actions([
                    [
                        'label' => 'Editar',
                        'icon' => 'edit',
                        'action' => 'edit',
                        'class' => 'primary',
                    ],
                    [
                        'label' => 'Registrar avance',
                        'icon' => 'trending_up',
                        'action' => 'reportar',
                        'class' => 'accent',
                    ],
                    [
                        'label' => 'Eliminar',
                        'icon' => 'delete',
                        'action' => 'delete',
                        'class' => 'error',
                        'visible' => fn($row) => $row->status !== 'Validada',
                    ],
                ]),
        ];
    }

    public function edit($id)
    {
        $this->dispatch('edit-actividad', actividad: $id );
    }

    public function delete($id)
    {
        $this->dispatch('delete-actividad', actividad: $id );
    }

    public function reportar($id)
    {
        // ejecutar  / redireccionar
    }

    // protected function columns(): array
    // {
    //     return [
    //          'updated_at' => [
    //             'label'    => '',
    //             'column'   => 'updated_at',
    //             'width'    => '20px',
    //             'sortable' => false,
    //             'render'   => function($row) {
    //                 $created = Carbon::parse($row->created_at);
    //                 $updated = Carbon::parse($row->updated_at);

    //                 if ($updated->eq($created)) {
    //                     return '';
    //                 }

    //                 return "<span class='material-symbols-outlined text-warning' style='font-size:15px'>warning</span>";
    //             },
    //         ],
    //         'status' => [
    //             'label'    => 'Estado',
    //             'column'   => 'status',
    //             'sortable' => true,
    //             'width'    => '110px',
    //             'badge'    => [
    //                 'Validada'  => 'pill-green',
    //                 'Pendiente' => 'pill-gray',
    //                 'Observada' => 'pill-yellow',
    //             ],
    //         ],
    //         'actividad' => [
    //             'label'    => 'Actividad',
    //             'column'   => 'actividad',
    //             'sortable' => true,
    //         ],
    //         'programa' => [
    //             'label'    => 'Programa',
    //             'relation' => 'programa.descripcion',
    //             'sortable' => true,
    //         ],
    //         'dependencia' => [
    //             'label'    => 'Dependencia',
    //             'relation' => 'dependencia.descripcion',
    //             'sortable' => true,
    //         ],
    //         'avance' => [
    //             'label'    => 'Avance',
    //             'column'   => 'avance',
    //             'class'    => 'text-left',
    //             'sortable' => false,
    //             'progress' => true,
    //         ],
            
    //     ];
    // }

}
