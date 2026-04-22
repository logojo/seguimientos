<?php 

namespace App\Support\DataTable;

class EnumColumn extends Column
{
    public function render($row): string
    {
        $raw = data_get($row, $this->key);

        if ($raw instanceof \BackedEnum || $raw instanceof \UnitEnum) {
            $value = $raw instanceof \BackedEnum ? $raw->value : $raw->name;

            $class = method_exists($raw, 'badge')
                ? $raw->badge()
                : 'badge-ghost';

            return "<span class=\"badge {$class}\">{$value}</span>";
        }

        // fallback
        return parent::render($row);
    }
}