<?php

namespace App\Support\DataTable\Columns;

class ActionsColumn extends Column
{
    protected array $actions = [];

    public function actions(array $actions): static
    {
        $this->actions = $actions;
        return $this;
    }

    public function render($row): string
    {
        $html = '<div class="flex gap-1">';

        foreach ($this->actions as $action) {

            // 🔐 Evaluar visibilidad
            if (isset($action['visible']) && is_callable($action['visible'])) {
                if (! $action['visible']($row)) {
                    continue;
                }
            }

            // ⚡ Generar acción Livewire
            $wireAction = is_callable($action['action'])
                ? $action['action']($row)
                : "{$action['action']}('{$row->id}')";

            $label = $action['label'] ?? '';
            $icon  = $action['icon'] ?? '';
            $class = $action['class'] ?? '';
            $html .= "
                <div  class=\"tooltip tooltip-{$class}\" data-tip='{$label}'>
                    <button type='button' 
                            class='btn btn-circle btn-sm bg-gray-100'     
                            wire:click=\"{$wireAction}\"
                    >
                        <span class=\"material-symbols-outlined text-{$class}\"  style='font-size: 18px'>{$icon}</span>
                    </button>
                </div>
            ";           
        }

        $html .= '</div>';

        return $html;
    }
}