<?php 

namespace App\Support\DataTable\Columns;

class IconColumn extends Column
{
    public function render($row): string
    {
        if ( $row->updated_at->eq($row->created_at) ) {
             return '';
        }

        return "
            <span class='material-symbols-outlined text-warning' style='font-size:15px'>warning</span>
        ";
    }
}