<?php 

namespace App\Support\DataTable\Columns;

class RelationColumn extends Column
{
    public string $relation;

    public function relation(string $relation): static
    {
        $this->relation = $relation;
        return $this;
    }

    public function resolve($row): mixed
    {
        if (!$this->relation) {
            return parent::resolve($row);
        }

        return data_get($row, $this->relation) ?? '—';
    }
}