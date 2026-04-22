<?php 

namespace App\Support\DataTable;

class BadgeColumn extends Column
{
    public array $colors = [];

    public function colors(array $map): static
    {
        $this->colors = $map;
        return $this;
    }
}