<?php 

namespace App\Support\DataTable;

abstract class Column
{
    public string $key;
    public string $label;
    public bool $sortable = false;
    public string $type = 'string';

    
    public function __construct(string $key)
    {
        $this->key = $key;
        $this->label = ucfirst($key);
    }
    
    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public static function make(string $key): static
    {
        return new static($key);
    }

    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    public function sortable(bool $value = true): static
    {
        $this->sortable = $value;
        return $this;
    }

    // 🔥 clave: render centralizado
    public function render($row): string
    {
        return e($this->resolve($row));
    }

    // 🔥 normalización universal
    public function resolve($row): mixed
    {
        $value = data_get($row, $this->key);

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if ($value instanceof \UnitEnum) {
            return $value->name;
        }

        return $value;
    }
}