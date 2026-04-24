<?php

namespace App\Livewire\Pages\Seguimientos;

use App\Enums\StatusActividadType;
use App\Models\Catalogos\CatDependencia;
use App\Models\Catalogos\CatEstrategia;
use App\Models\Catalogos\CatLineaAccion;
use App\Models\Catalogos\CatPrograma;
use App\Models\Catalogos\CatUnidadMedida;
use App\Models\Usuarios\Actividad;
use App\Services\UserAssigmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Seguimientos extends Component
{
    public bool $open = false;
    public $catalogos;

    public int $actividadId = 0;

    #[Validate('required')]
    public $actividad;

    #[Validate('required')]
    public $objetivo;

    #[Validate('required')]
    public $year;

    #[Validate('required', as:'unidad de medida')]
    public $unidad_medida_id;

    #[Validate('required')]
    public $programa_id;

    #[Validate('required')]
    public $dependencia_id;

    #[Validate('required')]
    public $estrategia_id;

    #[Validate('required', as:'línea de accion')]
    public $linea_accion_id;

    public function mount() {
        $this->loadCatalogos();
    }

    public function save() {
        $this->actividadId > 0
        ? $this->update()
        : $this->store();
    }

    public function store() {
        $this->validate();

        //todo: hasta que se tenga el actividad de usiarios completo
        // $service = app(UserAssigmentService::class);
        // $assignment = $service->currentAssignment( Auth::user() );

        Actividad::create([
            'actividad' => $this->actividad,
            'objetivo' => $this->objetivo,
            'year' => $this->year,
            'avance' => 0,
            'status' => StatusActividadType::Pendiente,
            //'user_assignment_id' => $this->assignment->id,
            'programa_id' => $this->programa_id,
            'dependencia_id' => $this->dependencia_id,
            'unidad_medida_id' => $this->unidad_medida_id,
            'estrategia_id' => $this->estrategia_id,
            'linea_accion_id' => $this->linea_accion_id,
        ]);

        $this->clear();
        $this->dispatch('render-table');
        toast('Actividad agregada con exito!', 'success');
    }

    #[On('edit-actividad')]
    public function edit(Actividad $actividad) {      
        $this->actividadId = $actividad->id;
        $this->actividad = $actividad->actividad;      
        $this->objetivo = $actividad->objetivo;      
        $this->year = $actividad->year;
        $this->unidad_medida_id = $actividad->unidad_medida_id;       
        $this->programa_id = $actividad->programa_id;       
        $this->dependencia_id = $actividad->dependencia_id;       
        $this->estrategia_id = $actividad->estrategia_id;       
        $this->linea_accion_id = $actividad->linea_accion_id;       
        $this->open = true;
    }

    public function update() {
        $actividad = Actividad::find($this->actividadId);
        $actividad->fill($this->only([
            'actividad',
            'objetivo',
            'year',
            'programa_id',
            'dependencia_id',
            'unidad_medida_id',
            'estrategia_id',
            'linea_accion_id',
        ]));

        $actividad->save();

        $this->clear();
        $this->dispatch('render-table');
        toast('Actividad modificada con exito!', 'success');
    }

    private function loadCatalogos(): void
    {
        $this->catalogos = Cache::remember('catalogos.seguimientos', 3600, function () {
            return [
                'unidades_medida' => CatUnidadMedida::select('id','descripcion')->get(),
                'programas' => CatPrograma::catalogo()->get(['id','descripcion']),
                'dependencias' => CatDependencia::catalogo()->get(['id','descripcion']),
                'estrategias' => CatEstrategia::catalogo()->get(['id','descripcion']),
                'lineas_accion' => CatLineaAccion::catalogo()->get(['id','descripcion']), 
            ];
        });

        foreach ($this->catalogos as $key => $value) {
            $this->{$key} = $value;
        }
    }

    #[On('delete-actividad')]
    public function onConfirm(Actividad $actividad ) {
       confirm( $actividad );
    }

    public function delete(Actividad $actividad)
    {
        //todo: revisar validacion para eliminar
        $actividad->delete();

        $this->clear();
        $this->dispatch('render-table');
        toast('Actividad eliminada con exito!', 'success');
        
    }


    public function clear() {
        $this->resetValidation();
        $this->resetExcept(['catalogos']);
    }

    #[On('clear-form')]
    public function clearForm() {
        $this->resetValidation();
        $this->resetExcept(['catalogos','open']);
    }

    public function render()
    {
        return view('livewire.pages.seguimientos.seguimientos');
    }
}
