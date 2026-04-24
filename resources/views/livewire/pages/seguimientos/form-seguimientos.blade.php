<div>
    <form wire:submit.prevent="save">
    <x-modal 
        id="actividad"
        button="Registrar nueva actividad" 
        title=" Adminitrar actividad" 
        icon="bubble"
    >
       
        <x-slot name="content">          

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mt-4">

                    <div class="w-full">
                        <legend class="fieldset-legend text-sm font-bold text-neutral">Ejercicio</legend>
                        <select class="select" wire:model="year">
                            <option>--- Seleccione ---</option>
                           @foreach(getYears() as $year)
                                <option value="{{ $year}}">
                                        {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for='unidad_medida_id' /> 
                    </div>

                    <div class="w-full">
                        <legend class="fieldset-legend text-sm font-bold text-neutral">Nombre de la actividad</legend>
                        <label class="input w-full validator">
                            <span class="material-symbols-outlined text-primary">bubble</span>
                            <input
                                class="bg-white"
                                type="text"
                                placeholder="Nombre de la actividad" 
                                wire:model="actividad"     
                                required                
                            />
                        </label>
                        <x-input-error for='actividad' /> 
                    </div>

                    <div class="w-full">
                        <legend class="fieldset-legend text-sm font-bold text-neutral">Unidad de medida</legend>
                        <select class="select" wire:model="unidad_medida_id">
                            <option>--- Seleccione ---</option>
                            @foreach ($catalogos['unidades_medida'] as $unidad_medida)
                            <option value="{{ $unidad_medida->id }}">{{ $unidad_medida->descripcion }}</option>
                            @endforeach
                        </select>
                        <x-input-error for='unidad_medida_id' /> 
                    </div>
            
                    <div class="w-full" wire:key="programa-select-{{ $programa_id }}">
                        <legend class="fieldset-legend text-sm font-bold text-neutral">Programa al que pertenece</legend>
                        <x-select2 wire:model="programa_id">
                            <option value="">--- seleccione ---</option> 
                            @foreach ($catalogos['programas'] as $programas)
                            <option value="{{ $programas->id }}">{{ $programas->descripcion }}</option>
                            @endforeach
                        </x-select2>
                        <x-input-error for='programa_id' /> 
                    </div>

                    <div class="w-full" wire:key="depedencia-select-{{ $dependencia_id }}">
                        <legend class="fieldset-legend text-sm font-bold text-neutral">Dependencia</legend>
                        <x-select2 wire:model="dependencia_id">
                            <option value="">--- seleccione ---</option> 
                            @foreach ($catalogos['dependencias'] as $dependencia)
                            <option value="{{ $dependencia->id }}">{{ $dependencia->descripcion }}</option>
                            @endforeach
                        </x-select2>
                        <x-input-error for='dependencia_id' /> 
                    </div>

                    <div class="w-full">
                        <legend class="fieldset-legend text-sm font-bold text-neutral">Objetivo</legend>
                        <label class="input w-full validator">
                            <span class="material-symbols-outlined text-primary">bubble</span>
                            <input
                                class="bg-white"
                                type="text"
                                placeholder="Objetivo" 
                                wire:model="objetivo"     
                                required                
                            />
                        </label>
                        <x-input-error for='objetivo' /> 
                    </div>

                    <div class="w-full">
                        <legend class="fieldset-legend text-sm font-bold text-neutral">Estrategia</legend>
                        <select class="select" wire:model="estrategia_id">
                            <option>--- Seleccione ---</option>
                            @foreach ($catalogos['estrategias'] as $estrategia)
                            <option value="{{ $estrategia->id }}">{{ $estrategia->descripcion }}</option>
                            @endforeach
                        </select>
                        <x-input-error for='estrategia_id' /> 
                    </div>

                    <div class="w-full">
                        <legend class="fieldset-legend text-sm font-bold text-neutral">Línea de acción</legend>
                        <select class="select" wire:model="linea_accion_id">
                            <option>--- Seleccione ---</option>
                            @foreach ($catalogos['lineas_accion'] as $linea_accion)
                            <option value="{{ $linea_accion->id }}">{{ $linea_accion->descripcion }}</option>
                            @endforeach
                        </select>
                        <x-input-error for='linea_accion_id' /> 
                    </div>                      
                    
            </div>

        </x-slot>
        
        <x-slot name="footer">
            <button class="btn btn-primary" type="submit">
                <span class="loading loading-spinner loading-xs" wire:loading wire:target='save'></span>
                Guardar
            </button>
            <button class="btn" type="button" x-on:click="open = false" wire:click="clear()">Cancelar</button>
        </x-slot>
    </x-modal>
    </form>
</div>
