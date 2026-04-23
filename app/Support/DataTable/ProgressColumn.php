<?php 

namespace App\Support\DataTable;

class ProgressColumn extends Column
{
    public function render($row): string
    {
        $value = $this->resolve($row);

        return "
            <progress class='progress w-full' value='{$value}' max='100'></progress>
        ";
    }
}