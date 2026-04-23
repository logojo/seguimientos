<?php 

namespace App\Support\DataTable\Columns;

class BadgeColumn extends Column
{
    public array $colors = [];

    public function colors(array $map): static
    {
        $this->colors = $map;
        return $this;
    }

    public function options(): array
    {
        return collect($this->getEnumCases())
            ->map(fn($case) => $case->value)
            ->toArray();
    }

    protected function getEnumCases(): array
    {
        $model = app(\App\Models\Usuarios\Actividad::class);

        $casts = $model->getCasts();

        if (!isset($casts[$this->key])) {
            return [];
        }

        $enum = $casts[$this->key];

        return $enum::cases();
    }

    public function render($row): string
    {
        $value = $this->resolve($row);

        $class = $this->colors[$value] ?? 'badge-ghost';

        return "<span class='badge {$class}'>{$value}</span>";
    }
}