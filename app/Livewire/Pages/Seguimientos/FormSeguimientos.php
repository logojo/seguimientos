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
use Livewire\Attributes\Validate;
use Livewire\Component;

class FormSeguimientos extends Component
{
    public bool $open = false;
    public $catalogos;

    #[Validate('required')]
    public $actividad;

    #[Validate('required')]
    public $objetivo;

    #[Validate('required', as:'unidad de medida')]
    public $unidad_id;

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
        $this->validate();

        //todo: hasta que se tenga el control de usiarios completo
        // $service = app(UserAssigmentService::class);
        // $assignment = $service->currentAssignment( Auth::user() );

        Actividad::create([
            'actividad' => $this->actividad,
            'objetivo' => $this->objetivo,
            'avance' => 0,
            'status' => StatusActividadType::Pendiente,
            //'user_assignment_id' => $this->assignment->id,
            'programa_id' => $this->programa_id,
            'dependencia_id' => $this->dependencia_id,
            'unidad_medida_id' => $this->unidad_id,
            'estrategia_id' => $this->estrategia_id,
            'linea_accion_id' => $this->linea_accion_id,
        ]);

        $this->clear();
        $this->dispatch('render-actividades');
        toast('Actividad agregada con exito!', 'success');
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

    public function clear() {
        $this->resetValidation();
        $this->resetExcept(['catalogos']);
    }

    public function render()
    {
        return view('livewire.pages.seguimientos.form-seguimientos');
    }
}
