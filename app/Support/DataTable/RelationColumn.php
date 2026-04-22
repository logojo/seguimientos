<?php 

namespace App\Support\DataTable;

class RelationColumn extends Column
{
    public string $relation;

    public function relation(string $relation): static
    {
        $this->relation = $relation;
        return $this;
    }
}