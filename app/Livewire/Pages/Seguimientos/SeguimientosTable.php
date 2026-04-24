<?php

namespace App\Livewire\Pages\Seguimientos;

use App\Livewire\Shared\DataTable;
use App\Models\Usuarios\Actividad;
use App\Support\DataTable\Columns\ActionsColumn;
use App\Support\DataTable\Columns\BadgeColumn;
use App\Support\DataTable\Columns\IconColumn;
use App\Support\DataTable\Columns\ProgressColumn;
use App\Support\DataTable\Columns\RelationColumn;
use App\Support\DataTable\Columns\TextColumn;

class SeguimientosTable extends DataTable
{

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

    protected function externalFiltersConfig(): array
    {
        return [

            // Filtro por ejercicio
            'year' => [
                'label' => 'Ejercicio',
                'type'  => 'select',
                'options' => function () {
                    return Actividad::query()
                        ->select('year')
                        ->distinct()
                        ->orderBy('year', 'desc')
                        ->pluck('year', 'year')
                        ->toArray();
                },

                'query' => function ($query, $value) {
                    $query->where('year', $value);
                },
            ],

        ];
    }

    protected function columns(): array
    {
        return [

            BadgeColumn::make('status')
                ->label('Estatus de registro')
                ->type('enum')
                ->sortable()
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
                ->type('numeric')
                ->sortable(),

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

}
